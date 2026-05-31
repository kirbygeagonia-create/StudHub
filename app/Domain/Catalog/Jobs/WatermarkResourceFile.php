<?php

namespace App\Domain\Catalog\Jobs;

use App\Models\LearningResource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;

class WatermarkResourceFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly int $resourceId) {}

    public function handle(): void
    {
        $resource = LearningResource::find($this->resourceId);

        if ($resource === null) {
            return;
        }

        if ($resource->file_url === null) {
            return;
        }

        $storagePath = $resource->file_url;

        if (! Storage::disk('public')->exists($storagePath)) {
            Log::warning('WatermarkResourceFile: file not found', [
                'resource_id' => $resource->id,
                'file_url' => $storagePath,
            ]);

            return;
        }

        $isPdf = $resource->file_mime === 'application/pdf'
            || strtolower(pathinfo($resource->file_url, PATHINFO_EXTENSION)) === 'pdf';

        if ($isPdf) {
            $this->generatePdfThumbnail($resource, $storagePath);
        } else {
            $this->generateImageThumbnail($resource, $storagePath);
        }

        $thumbFile = $this->resolveThumbnailPath($resource, $isPdf);
        $resource->forceFill([
            'is_watermarked' => true,
            'thumbnail_url' => $thumbFile,
        ])->save();

        Log::info('WatermarkResourceFile completed', [
            'resource_id' => $resource->id,
            'is_watermarked' => true,
            'file_mime' => $resource->file_mime,
        ]);
    }

    private function ensureThumbDir(): string
    {
        $thumbDir = Storage::disk('public')->path('thumbs');

        if (! is_dir($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }

        return $thumbDir;
    }

    private function resolveThumbnailPath(LearningResource $resource, bool $isPdf): string
    {
        $thumbDir = Storage::disk('public')->path('thumbs');

        $pngPath = $thumbDir . '/' . $resource->id . '.png';
        if (file_exists($pngPath)) {
            return 'thumbs/' . $resource->id . '.png';
        }

        $svgPath = $thumbDir . '/' . $resource->id . '.svg';
        if (file_exists($svgPath)) {
            return 'thumbs/' . $resource->id . '.svg';
        }

        return $isPdf
            ? 'thumbs/' . $resource->id . '.svg'
            : 'thumbs/' . $resource->id . '.png';
    }

    private function generatePdfThumbnail(LearningResource $resource, string $storagePath): void
    {
        $originalPath = Storage::disk('public')->path($storagePath);

        if (! file_exists($originalPath)) {
            return;
        }

        $thumbDir = $this->ensureThumbDir();

        $gsPath = $this->ghostscriptBinary();
        if ($gsPath === null) {
            $this->generateSvgFallback($resource, $storagePath, $thumbDir);

            return;
        }

        $thumbPath = $thumbDir . '/' . $resource->id . '.png';
        $escapedInput = escapeshellarg($originalPath);
        $escapedOutput = escapeshellarg($thumbPath);

        $command = sprintf(
            '%s -dSAFER -dBATCH -dNOPAUSE -dFirstPage=1 -dLastPage=1 '
            . '-sDEVICE=png16m -r150 -dTextAlphaBits=4 -dGraphicsAlphaBits=4 '
            . '-dUseCropBox -dFIXEDMEDIA -dPDFFitPage '
            . '-sOutputFile=%s %s 2>&1',
            escapeshellcmd($gsPath),
            $escapedOutput,
            $escapedInput
        );

        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);

        if ($exitCode !== 0 || ! file_exists($thumbPath)) {
            Log::warning('Ghostscript PDF thumbnail failed', [
                'resource_id' => $resource->id,
                'exit_code' => $exitCode,
                'output' => implode("\n", array_slice($output, 0, 5)),
            ]);

            $this->generateSvgFallback($resource, $storagePath, $thumbDir);

            return;
        }

        Log::info('PDF thumbnail generated via Ghostscript', [
            'resource_id' => $resource->id,
            'thumb_path' => 'thumbs/' . $resource->id . '.png',
        ]);
    }

    private function generateImageThumbnail(LearningResource $resource, string $storagePath): void
    {
        $originalPath = Storage::disk('public')->path($storagePath);

        if (! file_exists($originalPath)) {
            return;
        }

        $thumbDir = $this->ensureThumbDir();
        $thumbPath = $thumbDir . '/' . $resource->id . '.png';

try {
            $driver = extension_loaded('imagick')
                ? new ImagickDriver
                : new GdDriver;
            $manager = new ImageManager($driver);

            $image = $manager->read($originalPath);

            $image->scaleDown(width: 300, height: 400);

            $image->toPng()->save($thumbPath);

            Log::info('Image thumbnail generated', [
                'resource_id' => $resource->id,
                'thumb_path' => 'thumbs/' . $resource->id . '.png',
            ]);
        } catch (\Exception $e) {
            Log::warning('Image thumbnail generation failed, using GD driver fallback', [
                'resource_id' => $resource->id,
                'error' => $e->getMessage(),
            ]);

            $this->generateImageThumbnailFallback($resource, $originalPath, $thumbPath);
        }
    }

    private function generateImageThumbnailFallback(LearningResource $resource, string $originalPath, string $thumbPath): void
    {
        try {
            $manager = new ImageManager(
                new \Intervention\Image\Drivers\Gd\Driver
            );

            $image = $manager->read($originalPath);
            $image->scaleDown(width: 300, height: 400);
            $image->toPng()->save($thumbPath);

            Log::info('Image thumbnail generated via GD fallback', [
                'resource_id' => $resource->id,
            ]);
        } catch (\Exception $e) {
            Log::warning('Could not generate image thumbnail', [
                'resource_id' => $resource->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function generateSvgFallback(LearningResource $resource, string $storagePath, string $thumbDir): void
    {
        $thumbPath = $thumbDir . '/' . $resource->id . '.svg';

        $originalPath = Storage::disk('public')->path($storagePath);
        $pageCount = 0;
        $pdfContent = @file_get_contents($originalPath);

        if ($pdfContent !== false && strlen($pdfContent) > 20) {
            preg_match_all('/\/Type\s*\/Page[^s]/i', $pdfContent, $pageMatches);
            $pageCount = max(1, count($pageMatches[0]));
        }

        $thumbnailSvg = $this->generatePdfThumbnailSvg($resource->title, $pageCount);
        file_put_contents($thumbPath, $thumbnailSvg);

        Log::info('PDF thumbnail generated (SVG fallback)', [
            'resource_id' => $resource->id,
            'pages' => $pageCount,
            'thumb_path' => 'thumbs/' . $resource->id . '.svg',
        ]);
    }

    private function ghostscriptBinary(): ?string
    {
        static $binary = null;

        if ($binary !== null) {
            return $binary;
        }

        $configured = config('services.ghostscript.path', 'gs');

        if ($configured && $configured !== 'gs') {
            return $binary = $configured;
        }

        $candidates = [
            'gs',
            'gswin64c',
            'gswin32c',
            '/usr/bin/gs',
        ];

        foreach ($candidates as $candidate) {
            $output = [];
            $exitCode = 0;
            exec(escapeshellcmd($candidate) . ' --version 2>&1', $output, $exitCode);
            if ($exitCode === 0) {
                return $binary = $candidate;
            }
        }

        return $binary = null;
    }

    private function generatePdfThumbnailSvg(string $title, int $pages): string
    {
        $safeTitle = htmlspecialchars(mb_substr($title, 0, 60));
        $displayTitle = wordwrap($safeTitle, 18, "\n", true);
        $titleLines = explode("\n", $displayTitle);
        $titleSvg = '';
        $y = 218;
        foreach (array_slice($titleLines, 0, 2) as $line) {
            $titleSvg .= '<text x="100" y="' . $y . '" font-family="sans-serif" font-size="8" fill="#495057" text-anchor="middle">' . htmlspecialchars($line) . '</text>';
            $y += 10;
        }

        return '<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="260" viewBox="0 0 200 260">
  <defs>
    <linearGradient id="headerGrad" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" style="stop-color:#e63946;stop-opacity:1"/>
      <stop offset="100%" style="stop-color:#c1121f;stop-opacity:1"/>
    </linearGradient>
  </defs>
  <rect width="200" height="260" rx="6" fill="#ffffff" stroke="#dee2e6" stroke-width="1"/>
  <rect width="200" height="36" rx="6" fill="url(#headerGrad)"/>
  <rect y="30" width="200" height="6" fill="url(#headerGrad)"/>
  <text x="100" y="24" font-family="sans-serif" font-size="10" fill="#ffffff" text-anchor="middle" font-weight="bold">PDF Document</text>
  <rect x="30" y="52" width="140" height="148" rx="4" fill="#f8f9fa" stroke="#e9ecef" stroke-width="1"/>
  <text x="44" y="74" font-family="sans-serif" font-size="24" fill="#adb5bd">&#x1F4C4;</text>
  <line x1="44" y1="100" x2="156" y2="100" stroke="#dee2e6" stroke-width="3"/>
  <line x1="44" y1="112" x2="130" y2="112" stroke="#dee2e6" stroke-width="2"/>
  <line x1="44" y1="122" x2="140" y2="122" stroke="#dee2e6" stroke-width="2"/>
  <line x1="44" y1="132" x2="120" y2="132" stroke="#dee2e6" stroke-width="2"/>
  <line x1="44" y1="142" x2="135" y2="142" stroke="#dee2e6" stroke-width="2"/>
  <line x1="44" y1="152" x2="110" y2="152" stroke="#dee2e6" stroke-width="2"/>
  <line x1="44" y1="162" x2="145" y2="162" stroke="#dee2e6" stroke-width="2"/>
  <line x1="44" y1="172" x2="125" y2="172" stroke="#dee2e6" stroke-width="2"/>
  ' . $titleSvg . '
  <text x="100" y="' . ($y + 2) . '" font-family="sans-serif" font-size="7" fill="#adb5bd" text-anchor="middle">' . $pages . ' page' . ($pages > 1 ? 's' : '') . '</text>
</svg>';
    }
}

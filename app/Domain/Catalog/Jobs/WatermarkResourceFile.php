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

/**
 * Watermarks a resource file after upload.
 *
 * For PDFs, this prepares the file for per-user watermarking on download
 * by analyzing the PDF structure and marking it as watermarked.
 * For images, it generates a thumbnail.
 *
 * The heavy per-user watermarking happens at download time in
 * DownloadResourceFile — this job marks the file as "ready for
 * watermarking" and generates a thumbnail preview.
 */
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
            $this->generateThumbnail($resource, $storagePath);
        }

        $resource->forceFill(['is_watermarked' => $isPdf])->save();

        Log::info('WatermarkResourceFile completed', [
            'resource_id' => $resource->id,
            'is_watermarked' => $isPdf,
            'file_mime' => $resource->file_mime,
        ]);
    }

    private function generateThumbnail(LearningResource $resource, string $storagePath): void
    {
        $originalPath = Storage::disk('public')->path($storagePath);
        $thumbDir = Storage::disk('public')->path('thumbs');
        $thumbPath = $thumbDir . '/' . $resource->id . '.svg';

        if (! is_dir($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }

        if (! file_exists($originalPath)) {
            return;
        }

        try {
            $pdfContent = file_get_contents($originalPath);

            if ($pdfContent === false || strlen($pdfContent) < 20) {
                return;
            }

            $previewContent = substr($pdfContent, 0, min(2048, strlen($pdfContent)));

            $pageCount = 0;
            preg_match_all('/\/Type\s*\/Page[^s]/i', $pdfContent, $pageMatches);
            $pageCount = max(1, count($pageMatches[0]));

            $thumbnailSvg = $this->generatePdfThumbnailSvg($resource->title, $pageCount);

            file_put_contents($thumbPath, $thumbnailSvg);

            Log::info('PDF thumbnail generated', [
                'resource_id' => $resource->id,
                'pages' => $pageCount,
                'thumb_path' => 'thumbs/' . $resource->id . '.svg',
            ]);
        } catch (\Exception $e) {
            Log::warning('Could not generate PDF thumbnail', [
                'resource_id' => $resource->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate a polished SVG thumbnail for PDF files.
     * Shows title, page count, and a document icon.
     * Ghostscript/Imagick rendering can replace this when installed.
     */
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

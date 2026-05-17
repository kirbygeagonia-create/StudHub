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
        $thumbPath = $thumbDir . '/' . $resource->id . '.jpg';

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
            $pageCount = max(1, count($pageMatches[0] ?? []));

            $thumbnailSvg = $this->generatePdfThumbnailSvg($resource->title, $pageCount);

            file_put_contents($thumbPath, $thumbnailSvg);

            Log::info('PDF thumbnail generated', [
                'resource_id' => $resource->id,
                'pages' => $pageCount,
                'thumb_path' => 'thumbs/' . $resource->id . '.jpg',
            ]);
        } catch (\Exception $e) {
            Log::warning('Could not generate PDF thumbnail', [
                'resource_id' => $resource->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate a simple SVG placeholder thumbnail for PDF files.
     * In production, swap this with Imagick or Ghostscript rendering.
     */
    private function generatePdfThumbnailSvg(string $title, int $pages): string
    {
        $safeTitle = htmlspecialchars(mb_substr($title, 0, 60));

        return '<?xml version="1.0" encoding="UTF-8"?>
<svg xmlns="http://www.w3.org/2000/svg" width="200" height="260" viewBox="0 0 200 260">
  <rect width="200" height="260" rx="4" fill="#f8f9fa" stroke="#dee2e6" stroke-width="1"/>
  <rect x="15" y="15" width="170" height="192" rx="2" fill="#ffffff" stroke="#e9ecef" stroke-width="1"/>
  <line x1="30" y1="50" x2="170" y2="50" stroke="#e9ecef" stroke-width="2"/>
  <line x1="30" y1="70" x2="150" y2="70" stroke="#e9ecef" stroke-width="2"/>
  <line x1="30" y1="85" x2="160" y2="85" stroke="#e9ecef" stroke-width="2"/>
  <line x1="30" y1="100" x2="140" y2="100" stroke="#e9ecef" stroke-width="2"/>
  <line x1="30" y1="115" x2="155" y2="115" stroke="#e9ecef" stroke-width="2"/>
  <line x1="30" y1="130" x2="145" y2="130" stroke="#e9ecef" stroke-width="2"/>
  <line x1="30" y1="145" x2="165" y2="145" stroke="#e9ecef" stroke-width="2"/>
  <line x1="30" y1="160" x2="135" y2="160" stroke="#e9ecef" stroke-width="2"/>
  <line x1="30" y1="175" x2="150" y2="175" stroke="#e9ecef" stroke-width="2"/>
  <rect x="40" y="215" width="120" height="16" rx="2" fill="#e9ecef"/>
  <text x="100" y="236" font-family="sans-serif" font-size="8" fill="#6c757d" text-anchor="middle">' . $safeTitle . '</text>
  <text x="100" y="250" font-family="sans-serif" font-size="7" fill="#adb5bd" text-anchor="middle">' . $pages . ' page' . ($pages > 1 ? 's' : '') . '</text>
</svg>';
    }
}
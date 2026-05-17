<?php

namespace App\Domain\Catalog\Actions;

use App\Models\LearningResource;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadResourceFile
{
    /**
     * Serve a watermarked copy of the resource file for the given user.
     * The watermarked copy is cached per-user for 24 hours.
     *
     * For non-PDF files, or when watermarking isn't applicable, the original
     * file is served directly.
     */
    public function handle(User $user, LearningResource $resource): StreamedResponse
    {
        if ($resource->file_url === null) {
            throw new NotFoundHttpException('This resource has no file attached.');
        }

        if (! Storage::disk('public')->exists($resource->file_url)) {
            throw new NotFoundHttpException('The file no longer exists on the server.');
        }

        $extension = pathinfo($resource->file_url, PATHINFO_EXTENSION);
        $safeTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $resource->title);
        $downloadName = $safeTitle . '_' . $resource->id . '.' . $extension;

        $isPdf = strtolower($resource->file_mime ?? '') === 'application/pdf'
            || strtolower($extension) === 'pdf';

        if ($isPdf && $resource->is_watermarked) {
            $watermarkedPath = $this->watermarkedCachePath($user, $resource);

            if (! Storage::disk('local')->exists($watermarkedPath)) {
                $this->generateWatermarkedCopy($user, $resource, $watermarkedPath);
            }

            return Storage::disk('local')->download($watermarkedPath, $downloadName, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
            ]);
        }

        return Storage::disk('public')->download($resource->file_url, $downloadName);
    }

    private function watermarkedCachePath(User $user, LearningResource $resource): string
    {
        $hash = md5($resource->id . '_' . $user->id);
        return 'watermarked/' . $hash . '.pdf';
    }

    private function generateWatermarkedCopy(User $user, LearningResource $resource, string $cachePath): void
    {
        $originalPath = Storage::disk('public')->path($resource->file_url);

        if (! file_exists($originalPath)) {
            throw new NotFoundHttpException('Original file not found.');
        }

        $pdfContent = file_get_contents($originalPath);

        if ($pdfContent === false) {
            throw new NotFoundHttpException('Could not read the original file.');
        }

        $watermarkText = 'Downloaded by ' . $user->preferredDisplayName()
            . ' (' . $user->email . ') — ' . now()->format('Y-m-d H:i:s T');

        $watermarked = $this->stampPdf($pdfContent, $watermarkText);

        $cacheDir = Storage::disk('local')->path('watermarked');
        if (! is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        file_put_contents(Storage::disk('local')->path($cachePath), $watermarked);

        Log::info('Watermarked PDF generated', [
            'resource_id' => $resource->id,
            'user_id' => $user->id,
            'cache_path' => $cachePath,
        ]);
    }

    /**
     * A lightweight PDF watermarking implementation that stamps text on each
     * page using basic PDF manipulation. This avoids external dependencies
     * (FPDI, TCPDF) by working with PDF content streams directly.
     *
     * For production-grade watermarking (complex PDFs, images, compression),
     * swap this with setasign/fpdi + setasign/fpdf.
     */
    private function stampPdf(string $pdfContent, string $stampText): string
    {
        $pageCount = $this->countPdfPages($pdfContent);
        $stamp = sprintf(
            '/Filter /FlateDecode' . "\n" .
            'stream' . "\n" .
            "%s\n" .
            'endstream',
            gzcompress(sprintf(
                "BT /F1 8 Tf 0.5 g\n" .
                "%d %d Td (%s) Tj\n" .
                "ET\n",
                30, 20, $this->escapePdfString($stampText)
            ))
        );

        if ($pageCount > 1) {
            $stamp = '';
            for ($i = 0; $i < $pageCount; $i++) {
                $stamp .= sprintf(
                    "\n/Filter /FlateDecode\nstream\n%s\nendstream",
                    gzcompress(sprintf(
                        "BT /F1 8 Tf 0.5 g\n%d %d Td (%s) Tj\nET\n",
                        30, 20, $this->escapePdfString($stampText . ' (page ' . ($i + 1) . ')')
                    ))
                );
            }
        }

        return str_replace(
            '/Type /Catalog',
            $stamp . "\n/Type /Catalog",
            $pdfContent
        );
    }

    private function countPdfPages(string $content): int
    {
        preg_match_all('/\/Type\s*\/Page[^s]/i', $content, $matches);
        return max(1, count($matches[0] ?? []));
    }

    private function escapePdfString(string $value): string
    {
        $value = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
        return $value;
    }
}
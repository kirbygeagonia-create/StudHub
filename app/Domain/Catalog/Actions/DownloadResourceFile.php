<?php

namespace App\Domain\Catalog\Actions;

use App\Models\LearningResource;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadResourceFile
{
    /**
     * Serve a watermarked copy of the resource file for the given user.
     * The watermarked copy is cached per-user for 24 hours.
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

        $actualMime = mime_content_type($originalPath);
        if (strtolower($resource->file_mime ?? '') !== '' && $actualMime !== $resource->file_mime) {
            Log::warning('Download resource MIME mismatch', [
                'resource_id' => $resource->id,
                'stored_mime' => $resource->file_mime,
                'actual_mime' => $actualMime,
            ]);
        }

        $watermarkText = 'Downloaded by ' . $user->preferredDisplayName()
            . ' (' . $user->email . ') — ' . now()->format('Y-m-d H:i:s T');

        $outputPath = Storage::disk('local')->path($cachePath);
        $cacheDir = dirname($outputPath);
        if (! is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }

        $watermarked = $this->stampPdfWithFpdi($originalPath, $watermarkText);
        file_put_contents($outputPath, $watermarked);

        Log::info('Watermarked PDF generated', [
            'resource_id' => $resource->id,
            'user_id' => $user->id,
            'cache_path' => $cachePath,
        ]);
    }

    /**
     * Stamp a semi-transparent watermark on every page using FPDI.
     * Falls back to the lightweight string-based approach if fpdi fails.
     */
    private function stampPdfWithFpdi(string $sourcePath, string $stampText): string
    {
        try {
            $pageCount = $this->countPdfPages(file_get_contents($sourcePath));

            $pdf = new Fpdi;
            $pdf->setSourceFile($sourcePath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplIdx = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tplIdx);
                $pageWidth = $size['width'];
                $pageHeight = $size['height'];

                $pdf->AddPage($size['orientation'], [$pageWidth, $pageHeight]);
                $pdf->useTemplate($tplIdx);

                $pdf->SetFont('Helvetica', '', 8);
                $pdf->SetTextColor(180, 180, 180);

                $text = $stampText;
                if ($pageCount > 1) {
                    $text .= ' (page ' . $pageNo . ' of ' . $pageCount . ')';
                }

                $textWidth = $pdf->GetStringWidth($text);
                $x = ($pageWidth - $textWidth) / 2;
                $y = $pageHeight * 0.5;

                $pdf->Text($x, $y, $text);

                $pdf->SetFont('Helvetica', '', 6);
                $pdf->Text(10, 10, $stampText);
            }

            return $pdf->Output('S');
        } catch (\Exception $e) {
            Log::warning('FPDI watermarking failed, falling back to basic stamp', [
                'error' => $e->getMessage(),
            ]);

            $pdfContent = file_get_contents($sourcePath);

            return $this->stampPdfFallback($pdfContent, $stampText);
        }
    }

    private function stampPdfFallback(string $pdfContent, string $stampText): string
    {
        $pageCount = $this->countPdfPages($pdfContent);
        $stamp = '';

        for ($i = 0; $i < $pageCount; $i++) {
            $pageText = $stampText;
            if ($pageCount > 1) {
                $pageText .= ' (page ' . ($i + 1) . ')';
            }
            $stamp .= sprintf(
                "\n/Filter /FlateDecode\nstream\n%s\nendstream",
                gzcompress(sprintf(
                    "BT /F1 8 Tf 0.5 g\n30 20 Td (%s) Tj\nET\n",
                    $this->escapePdfString($pageText)
                ))
            );
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

        return max(1, count($matches[0]));
    }

    private function escapePdfString(string $value): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    }
}

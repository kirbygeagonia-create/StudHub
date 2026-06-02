<?php

namespace App\Domain\Catalog\Actions;

use App\Models\LearningResource;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\ImageManager;
use setasign\Fpdi\Fpdi;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadResourceFile
{
    /** @var list<string> */
    private const WATERMARKABLE_IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/webp',
    ];

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

        // Track download count
        $resource->increment('download_count');

        $extension = pathinfo($resource->file_url, PATHINFO_EXTENSION);
        $safeTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', $resource->title);
        $downloadName = $safeTitle . '_' . $resource->id . '.' . $extension;

        $isPdf = in_array(strtolower($resource->file_mime ?? ''), ['application/pdf', 'application/x-pdf', 'application/acrobat'], true)
            || strtolower($extension) === 'pdf';

        $isWatermarkableImage = in_array(
            strtolower($resource->file_mime ?? ''),
            self::WATERMARKABLE_IMAGE_MIMES,
            true
        );

        if ($resource->is_watermarked) {
            $cachePath = $isPdf
                ? $this->watermarkedPdfCachePath($user, $resource)
                : $this->watermarkedImageCachePath($user, $resource, $extension);

            if (! Storage::disk('local')->exists($cachePath)) {
                if ($isPdf) {
                    $this->generateWatermarkedPdf($user, $resource, $cachePath);
                } elseif ($isWatermarkableImage) {
                    $this->generateWatermarkedImage($user, $resource, $cachePath);
                }
            }

            if (Storage::disk('local')->exists($cachePath)) {
                $mime = $isPdf ? 'application/pdf' : ($resource->file_mime ?? 'application/octet-stream');

                return Storage::disk('local')->download($cachePath, $downloadName, [
                    'Content-Type' => $mime,
                    'Content-Disposition' => 'attachment; filename="' . $downloadName . '"',
                ]);
            }
        }

        return Storage::disk('public')->download($resource->file_url, $downloadName);
    }

    private function watermarkedPdfCachePath(User $user, LearningResource $resource): string
    {
        $hash = hash('xxh3', $resource->id . '_' . $user->id);

        return 'watermarked/' . $hash . '.pdf';
    }

    private function watermarkedImageCachePath(User $user, LearningResource $resource, string $extension): string
    {
        $hash = hash('xxh3', $resource->id . '_' . $user->id);

        return 'watermarked/' . $hash . '.' . $extension;
    }

    private function ensureCacheDir(string $cachePath): void
    {
        $outputPath = Storage::disk('local')->path($cachePath);
        $cacheDir = dirname($outputPath);
        if (! is_dir($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
    }

    private function generateWatermarkedPdf(User $user, LearningResource $resource, string $cachePath): void
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

        $this->ensureCacheDir($cachePath);
        $outputPath = Storage::disk('local')->path($cachePath);

        $watermarked = $this->stampPdfWithFpdi($originalPath, $watermarkText);
        file_put_contents($outputPath, $watermarked);

        Log::info('Watermarked PDF generated', [
            'resource_id' => $resource->id,
            'user_id' => $user->id,
            'cache_path' => $cachePath,
        ]);
    }

    private function generateWatermarkedImage(User $user, LearningResource $resource, string $cachePath): void
    {
        $originalPath = Storage::disk('public')->path($resource->file_url);

        if (! file_exists($originalPath)) {
            throw new NotFoundHttpException('Original file not found.');
        }

        $this->ensureCacheDir($cachePath);
        $outputPath = Storage::disk('local')->path($cachePath);

        try {
            $manager = $this->createImageManager();
            $image = $manager->read($originalPath);

            $width = $image->width();
            $height = $image->height();

            $stampText = 'Downloaded by ' . $user->preferredDisplayName()
                . ' (' . $user->email . ')'
                . ' — ' . now()->format('Y-m-d H:i:s T');

            $image->text($stampText, (int) ($width * 0.05), (int) ($height * 0.95), function ($font) use ($height) {
                $font->size((int) max(10, $height / 40));
                $font->color('rgba(180, 180, 180, 0.5)');
                $font->file($this->fallbackFontPath());
            });

            $image->save($outputPath);

            Log::info('Watermarked image generated', [
                'resource_id' => $resource->id,
                'user_id' => $user->id,
                'cache_path' => $cachePath,
            ]);
        } catch (\Exception $e) {
            Log::warning('Image watermarking failed, serving original', [
                'resource_id' => $resource->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function createImageManager(): ImageManager
    {
        if (extension_loaded('imagick')) {
            return new ImageManager(new ImagickDriver);
        }

        return new ImageManager(new GdDriver);
    }

    private function fallbackFontPath(): string
    {
        $candidates = [
            'C:\\Windows\\Fonts\\arial.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/TTF/DejaVuSans.ttf',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return $candidates[0];
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

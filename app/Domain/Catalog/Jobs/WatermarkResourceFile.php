<?php

namespace App\Domain\Catalog\Jobs;

use App\Models\LearningResource;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Stub for the actual PDF/image watermarking work that lands in Week 5.
 *
 * For now this just flips the `is_watermarked` flag and logs — the heavy
 * lifting (re-rendering PDFs with the downloader's name + timestamp, and
 * generating a per-user cached copy) is intentionally deferred so Week 4
 * can ship the catalog without the FPDI/Imagick dependency.
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

        Log::info('WatermarkResourceFile (stub): scheduled for resource ' . $resource->id, [
            'file_url' => $resource->file_url,
            'file_mime' => $resource->file_mime,
        ]);

        $resource->forceFill(['is_watermarked' => true])->save();
    }
}

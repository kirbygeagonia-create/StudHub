<?php

namespace App\Domain\Lends\Jobs;

use App\Domain\Lends\Notifications\ReturnReminder;
use App\Models\Lend;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendReturnReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $dueWindow = (int) config('lends.reminder_days_before', 2);

        Lend::with(['fromUser', 'toUser', 'resource'])
            ->dueSoon($dueWindow)
            ->cursor()
            ->each(function (Lend $lend): void {
                if ($lend->toUser === null) {
                    Log::warning('Return reminder skipped: lend has no borrower.', ['lend_id' => $lend->id]);

                    return;
                }

                try {
                    $lend->toUser->notify(new ReturnReminder($lend));
                } catch (\Throwable $e) {
                    Log::error('Failed to send return reminder notification.', [
                        'lend_id' => $lend->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            });
    }
}
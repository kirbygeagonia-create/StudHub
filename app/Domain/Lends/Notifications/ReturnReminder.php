<?php

namespace App\Domain\Lends\Notifications;

use App\Models\Lend;
use Carbon\Carbon;
use Illuminate\Notifications\Notification;

class ReturnReminder extends Notification
{
    public function __construct(public Lend $lend) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        /** @var Carbon|null $returnByDate */
        $returnByDate = $this->lend->return_by;
        $returnBy = $returnByDate?->format('M d, Y') ?? 'soon';

        return [
            'type' => 'return_reminder',
            'lend_id' => $this->lend->id,
            'resource_title' => $this->lend->resource?->title ?? 'Unknown resource',
            'lender_name' => $this->lend->fromUser?->preferredDisplayName() ?? 'the lender',
            'return_by' => $returnBy,
            'message' => sprintf(
                'Reminder: "%s" borrowed from %s is due back by %s.',
                $this->lend->resource?->title ?? 'Unknown resource',
                $this->lend->fromUser?->preferredDisplayName() ?? 'the lender',
                $returnBy,
            ),
        ];
    }
}

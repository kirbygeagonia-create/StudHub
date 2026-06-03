<?php

namespace App\Domain\Lends\Notifications;

use App\Models\Lend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LendEscalated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Lend $lend) {}

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
        return [
            'lend_id' => $this->lend->id,
            'resource_title' => $this->lend->resource?->title ?? 'Unknown resource',
            'message' => 'A lend has been escalated for "' . ($this->lend->resource?->title ?? 'Unknown resource') . '". Please return the item as soon as possible.',
        ];
    }
}

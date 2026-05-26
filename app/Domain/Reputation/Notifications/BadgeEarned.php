<?php

namespace App\Domain\Reputation\Notifications;

use App\Domain\Reputation\Enums\Badge;
use App\Models\UserBadge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BadgeEarned extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly UserBadge $userBadge) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        /** @var Badge $badge */
        $badge = $this->userBadge->badge;

        return [
            'type' => 'badge_earned',
            'badge' => $badge->value,
            'badge_label' => $badge->label(),
            'badge_icon' => $badge->icon(),
            'badge_desc' => $badge->description(),
            'rarity' => $badge->rarity()->value,
            'earned_at' => $this->userBadge->earned_at?->toIso8601String(), // @phpstan-ignore-line — cast to datetime in UserBadge model
        ];
    }
}

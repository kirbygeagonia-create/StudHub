<?php

namespace App\Domain\Moderation\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SuspendUser
{
    public function handle(User $moderator, User $target, int $days = 7, ?string $reason = null): void
    {
        if ($target->id === $moderator->id) {
            throw new RuntimeException('You cannot suspend yourself.');
        }

        if ($target->isAdmin()) {
            throw new RuntimeException('You cannot suspend an admin.');
        }

        $suspendedUntil = now()->addDays($days);

        DB::transaction(function () use ($moderator, $target, $days, $reason, $suspendedUntil): void {
            $target->update([
                'suspended_until' => $suspendedUntil,
            ]);

            (new LogAudit)->handle(
                $moderator,
                'user.suspend',
                'User',
                $target->id,
                [
                    'days' => $days,
                    'reason' => $reason,
                    'suspended_until' => $suspendedUntil->toIso8601String(),
                ]
            );
        });
    }

    public function unsuspend(User $moderator, User $target): void
    {
        DB::transaction(function () use ($moderator, $target): void {
            $target->update(['suspended_until' => null]);

            (new LogAudit)->handle(
                $moderator,
                'user.unsuspend',
                'User',
                $target->id,
                []
            );
        });
    }
}

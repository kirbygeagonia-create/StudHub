<?php

namespace App\Domain\Reputation\Actions;

use App\Domain\Reputation\Enums\KarmaEventReason;
use App\Models\KarmaEvent;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AwardKarma
{
    public function handle(User $user, KarmaEventReason $reason, ?int $relatedId = null, ?string $relatedType = null): void
    {
        DB::transaction(function () use ($user, $reason, $relatedId, $relatedType): void {
            KarmaEvent::create([
                'user_id' => $user->id,
                'delta' => $reason->delta(),
                'reason' => $reason->value,
                'related_type' => $relatedType,
                'related_id' => $relatedId,
            ]);

            $user->karma = (int) KarmaEvent::where('user_id', $user->id)->sum('delta');
            $user->save();
        });
    }
}
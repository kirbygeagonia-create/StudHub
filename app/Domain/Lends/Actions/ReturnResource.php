<?php

namespace App\Domain\Lends\Actions;

use App\Domain\Catalog\Enums\ResourceAvailability;
use App\Domain\Lends\Enums\LendCondition;
use App\Models\Lend;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ReturnResource
{
    public function handle(User $user, Lend $lend, ?LendCondition $condition = null): void
    {
        if ($lend->to_user_id !== $user->id) {
            throw new RuntimeException('Only the borrower can return the resource.');
        }

        if ($lend->isReturned()) {
            throw new RuntimeException('This resource has already been returned.');
        }

        DB::transaction(function () use ($lend, $condition): void {
            $lend->update([
                'returned_at' => now(),
                'condition_on_return' => $condition,
            ]);

            $resource = $lend->resource;
            if ($resource !== null && $resource->availability === ResourceAvailability::LentOut) {
                $resource->update(['availability' => ResourceAvailability::Available]);
            }
        });
    }
}
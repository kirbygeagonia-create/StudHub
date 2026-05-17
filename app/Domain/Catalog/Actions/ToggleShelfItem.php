<?php

namespace App\Domain\Catalog\Actions;

use App\Domain\Reputation\Actions\AwardKarma;
use App\Domain\Reputation\Enums\KarmaEventReason;
use App\Models\LearningResource;
use App\Models\Shelf;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ToggleShelfItem
{
    /**
     * Toggle a resource on/off the user's default shelf.
     * Returns true if the resource is now saved, false if removed.
     */
    public function handle(User $user, LearningResource $resource): bool
    {
        return DB::transaction(function () use ($user, $resource): bool {
            $shelf = $this->ensureDefaultShelf($user);

            $existing = $shelf->resources()
                ->where('resource_id', $resource->id)
                ->exists();

            if ($existing) {
                $shelf->resources()->detach($resource->id);
                $resource->decrement('save_count');

                return false;
            }

            $shelf->resources()->attach($resource->id);
            $resource->increment('save_count');
            $this->awardResourceSavedKarma($resource);

            return true;
        });
    }

    /**
     * Check if a resource is saved on the user's default shelf.
     */
    public function isSaved(User $user, LearningResource $resource): bool
    {
        $shelf = $user->shelves()->first();

        if ($shelf === null) {
            return false;
        }

        return $shelf->resources()
            ->where('resource_id', $resource->id)
            ->exists();
    }

    private function awardResourceSavedKarma(LearningResource $resource): void
    {
        if ($resource->save_count >= 1 && $resource->owner_user_id !== null) {
            $owner = User::find($resource->owner_user_id);
            if ($owner !== null) {
                (new AwardKarma)->handle($owner, KarmaEventReason::ResourceSaved, $resource->id, 'LearningResource');
            }
        }
    }

    private function ensureDefaultShelf(User $user): Shelf
    {
        $shelf = $user->shelves()->first();

        if ($shelf === null) {
            $shelf = Shelf::create([
                'user_id' => $user->id,
                'name' => 'My Shelf',
            ]);
        }

        return $shelf;
    }
}
<?php

namespace App\Observers;

use App\Domain\Catalog\Enums\ResourceAvailability;
use App\Models\User;

class UserObserver
{
    /**
     * Before a user is deleted, archive their resources so they don't
     * cascade-delete and leave broken references for other users.
     */
    public function deleting(User $user): void
    {
        $user->resources()->update([
            'availability' => ResourceAvailability::Archived->value,
            'owner_user_id' => null,
        ]);
    }
}

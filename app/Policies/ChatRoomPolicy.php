<?php

namespace App\Policies;

use App\Models\ChatRoom;
use App\Models\User;

class ChatRoomPolicy
{
    /**
     * Determine if the user can view/access the chat room.
     */
    public function view(User $user, ChatRoom $room): bool
    {
        if ($user->isSuspended()) {
            return false;
        }

        if (! $user->hasCompletedOnboarding()) {
            return false;
        }

        if ($room->school_id !== $user->school_id) {
            return false;
        }

        if ($room->program_id !== null && $room->program_id !== $user->program_id) {
            return false;
        }

        if ($room->year_level !== null && $room->year_level !== $user->year_level) {
            return false;
        }

        return true;
    }
}

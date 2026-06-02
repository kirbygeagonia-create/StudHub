<?php

namespace App\Domain\Chat\Support;

use App\Models\User;

class StudHubBot
{
    /**
     * Get the StudHub Bot system user.
     * Created by the 2026_06_01_000005 migration.
     */
    public static function user(): User
    {
        return User::where('email', 'bot@studhub.local')->firstOrFail();
    }

    /**
     * The display name shown in chat for system messages.
     */
    public static function displayName(): string
    {
        return 'StudHub Bot';
    }
}

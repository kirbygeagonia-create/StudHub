<?php

namespace App\Domain\Identity\Support;

use App\Domain\Identity\Enums\UserRole;
use App\Models\User;

class ScopeGuard
{
    /**
     * Returns WHERE clause additions for scoping queries to the user's authority.
     * Returns null for unrestricted access (SAO, SuperAdmin).
     *
     * @return array<string, mixed>|null
     */
    public static function programScope(User $user): ?array
    {
        return match ($user->role) {
            UserRole::SuperAdmin, UserRole::Sao => null,
            UserRole::Dean => null,  // Dean sees all programs in college; use collegeScope
            UserRole::ProgramHead, UserRole::Moderator => ['program_id' => $user->program_id],
            default => ['program_id' => $user->program_id, 'year_level' => $user->year_level],
        };
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function collegeScope(User $user): ?array
    {
        return match ($user->role) {
            UserRole::SuperAdmin, UserRole::Sao => null,
            UserRole::Dean => ['college_id' => $user->college_id],
            default => ['college_id' => $user->college_id],
        };
    }

    /**
     * Checks if $user can administer $target (based on role hierarchy and scope).
     */
    public static function canAdminister(User $user, User $target): bool
    {
        if ($user->isSuperAdmin() || $user->isSao()) {
            return true;
        }

        $targetRoleValue = $target->role instanceof UserRole
            ? $target->role->value
            : (string) $target->role;

        if ($user->isDean()) {
            return $target->college_id === $user->college_id
                && in_array($targetRoleValue, ['student', 'moderator', 'program_head'], true);
        }
        if ($user->isProgramHead()) {
            return $target->program_id === $user->program_id
                && in_array($targetRoleValue, ['student', 'moderator'], true);
        }

        return false;
    }
}

<?php

namespace App\Domain\Identity\Enums;

enum UserRole: string
{
    case Student = 'student';
    case Moderator = 'moderator';
    case Admin = 'admin';
    case SuperAdmin = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::Moderator => 'Program Moderator',
            self::Admin => 'Program Head / Dean',
            self::SuperAdmin => 'Super Admin',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }

    /**
     * Returns all roles that have at least the given role's permissions.
     * SuperAdmin inherits Admin, Admin inherits Moderator, etc.
     *
     * @return array<int, string>
     */
    public function inheritedRoles(): array
    {
        return match ($this) {
            self::SuperAdmin => ['super_admin', 'admin', 'moderator'],
            self::Admin => ['admin', 'moderator'],
            self::Moderator => ['moderator'],
            self::Student => ['student'],
        };
    }
}

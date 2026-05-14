<?php

namespace App\Domain\Identity\Enums;

enum UserRole: string
{
    case Student = 'student';
    case Moderator = 'moderator';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::Moderator => 'Program Moderator',
            self::Admin => 'School Admin',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }
}

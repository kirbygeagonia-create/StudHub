<?php

namespace App\Domain\Identity\Enums;

enum UserRole: string
{
    case Student = 'student';
    case Moderator = 'moderator';
    case ProgramHead = 'program_head';
    case Dean = 'dean';
    case Sao = 'sao';
    case SuperAdmin = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::Student => 'Student',
            self::Moderator => 'Program Moderator',
            self::ProgramHead => 'Program Head',
            self::Dean => 'College Dean',
            self::Sao => 'Administrator',
            self::SuperAdmin => 'System Administrator',
        };
    }

    /** @return array<int, string> */
    public static function values(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }

    /**
     * Returns all roles that have at least the given role's permissions.
     * SAO is the highest school-side authority.
     * SuperAdmin is developer-only, hidden from school UI.
     *
     * @return array<int, string>
     */
    public function inheritedRoles(): array
    {
        return match ($this) {
            self::SuperAdmin => ['super_admin', 'sao', 'dean', 'program_head', 'moderator'],
            self::Sao => ['sao', 'dean', 'program_head', 'moderator'],
            self::Dean => ['dean', 'program_head', 'moderator'],
            self::ProgramHead => ['program_head', 'moderator'],
            self::Moderator => ['moderator'],
            self::Student => ['student'],
        };
    }

    public function panelClass(): string
    {
        return match ($this) {
            self::Student, self::Moderator => '',
            self::ProgramHead => 'panel-program-head',
            self::Dean => 'panel-dean',
            self::Sao => 'panel-sao',
            self::SuperAdmin => 'panel-super',
        };
    }

    /**
     * Whether this role should be visible/assignable inside the school's admin panels.
     * super_admin is excluded — only assignable directly in the database or via artisan.
     */
    public function isSchoolRole(): bool
    {
        return $this !== self::SuperAdmin;
    }
}

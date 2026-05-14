<?php

namespace App\Domain\Catalog\Enums;

enum ResourceVisibility: string
{
    case School = 'school';
    case ProgramOnly = 'program_only';
    case PrivateLink = 'private_link';

    public function label(): string
    {
        return match ($this) {
            self::School => 'Anyone in the school',
            self::ProgramOnly => 'My program only',
            self::PrivateLink => 'Private link',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $c) => $c->value, self::cases());
    }
}

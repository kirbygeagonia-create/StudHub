<?php

namespace App\Domain\Catalog\Enums;

enum ResourceAvailability: string
{
    case Available = 'available';
    case LentOut = 'lent_out';
    case DigitalOnly = 'digital_only';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Available => 'Available',
            self::LentOut => 'Lent out',
            self::DigitalOnly => 'Digital only',
            self::Archived => 'Archived',
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

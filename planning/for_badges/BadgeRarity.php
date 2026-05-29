<?php

namespace App\Domain\Reputation\Enums;

enum BadgeRarity: string
{
    case Common = 'common';
    case Uncommon = 'uncommon';
    case Rare = 'rare';
    case Legendary = 'legendary';
    case Hidden = 'hidden';

    public function label(): string
    {
        return match ($this) {
            self::Common => 'Common',
            self::Uncommon => 'Uncommon',
            self::Rare => 'Rare',
            self::Legendary => 'Legendary',
            self::Hidden => 'Hidden',
        };
    }

    /** Tailwind colour classes for the badge pill in blade views. */
    public function colorClasses(): string
    {
        return match ($this) {
            self::Common => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::Uncommon => 'bg-blue-50 text-blue-700 border-blue-200',
            self::Rare => 'bg-purple-50 text-purple-700 border-purple-200',
            self::Legendary => 'bg-amber-50 text-amber-700 border-amber-200',
            self::Hidden => 'bg-pink-50 text-pink-700 border-pink-200',
        };
    }
}

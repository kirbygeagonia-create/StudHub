<?php

namespace App\Domain\Reputation\Enums;

enum BadgeRarity: string
{
    case Common    = 'common';
    case Uncommon  = 'uncommon';
    case Rare      = 'rare';
    case Legendary = 'legendary';
    case Hidden    = 'hidden';

    public function label(): string
    {
        return match ($this) {
            self::Common    => 'Common',
            self::Uncommon  => 'Uncommon',
            self::Rare      => 'Rare',
            self::Legendary => 'Legendary',
            self::Hidden    => 'Hidden',
        };
    }

    /** Tailwind colour classes for the badge pill in blade views. */
    public function colorClasses(): string
    {
        return match ($this) {
            self::Common    => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 dark:border-emerald-800/40',
            self::Uncommon  => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800/40',
            self::Rare      => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-900/30 dark:text-purple-300 dark:border-purple-800/40',
            self::Legendary => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800/40',
            self::Hidden    => 'bg-pink-50 text-pink-700 border-pink-200 dark:bg-pink-900/30 dark:text-pink-300 dark:border-pink-800/40',
        };
    }
}
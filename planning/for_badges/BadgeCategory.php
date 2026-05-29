<?php

namespace App\Domain\Reputation\Enums;

enum BadgeCategory: string
{
    case Upload = 'upload';
    case Fulfill = 'fulfill';
    case Lend = 'lend';
    case Activity = 'activity';
    case Community = 'community';
    case Special = 'special';

    public function label(): string
    {
        return match ($this) {
            self::Upload => 'Uploads',
            self::Fulfill => 'Request Fulfillment',
            self::Lend => 'Lending',
            self::Activity => 'Activity & Streaks',
            self::Community => 'Community & Chat',
            self::Special => 'Special / Hidden',
        };
    }

    /** Tailwind icon colour classes (matches the widget colours). */
    public function iconClasses(): string
    {
        return match ($this) {
            self::Upload => 'bg-blue-50 text-blue-600',
            self::Fulfill => 'bg-teal-50 text-teal-600',
            self::Lend => 'bg-purple-50 text-purple-600',
            self::Activity => 'bg-emerald-50 text-emerald-600',
            self::Community => 'bg-orange-50 text-orange-600',
            self::Special => 'bg-pink-50 text-pink-600',
        };
    }
}

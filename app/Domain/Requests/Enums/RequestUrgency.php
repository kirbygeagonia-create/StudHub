<?php

namespace App\Domain\Requests\Enums;

enum RequestUrgency: string
{
    case Low = 'low';
    case Normal = 'normal';
    case Urgent = 'urgent';

    public function label(): string
    {
        return match ($this) {
            self::Low => 'Low',
            self::Normal => 'Normal',
            self::Urgent => 'Urgent',
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
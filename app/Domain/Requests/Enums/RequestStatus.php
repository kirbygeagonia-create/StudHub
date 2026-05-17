<?php

namespace App\Domain\Requests\Enums;

enum RequestStatus: string
{
    case Open = 'open';
    case Matched = 'matched';
    case Fulfilled = 'fulfilled';
    case Expired = 'expired';
    case Withdrawn = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::Open => 'Open',
            self::Matched => 'Matched',
            self::Fulfilled => 'Fulfilled',
            self::Expired => 'Expired',
            self::Withdrawn => 'Withdrawn',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_map(fn (self $c) => $c->value, self::cases());
    }

    /**
     * @return array<int, string>
     */
    public static function openValues(): array
    {
        return [self::Open->value, self::Matched->value];
    }
}
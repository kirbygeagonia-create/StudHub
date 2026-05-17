<?php

namespace App\Domain\Lends\Enums;

enum LendCondition: string
{
    case LikeNew = 'like_new';
    case Good = 'good';
    case Worn = 'worn';
    case Damaged = 'damaged';

    public function label(): string
    {
        return match ($this) {
            self::LikeNew => 'Like new',
            self::Good => 'Good',
            self::Worn => 'Worn',
            self::Damaged => 'Damaged',
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
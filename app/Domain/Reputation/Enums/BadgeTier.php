<?php

namespace App\Domain\Reputation\Enums;

enum BadgeTier: string
{
    case Bronze = 'bronze';
    case Silver = 'silver';
    case Gold = 'gold';

    public function label(): string
    {
        return match ($this) {
            self::Bronze => 'Bronze',
            self::Silver => 'Silver',
            self::Gold => 'Gold',
        };
    }

    public function threshold(): int
    {
        return match ($this) {
            self::Bronze => 25,
            self::Silver => 75,
            self::Gold => 150,
        };
    }

    public static function fromKarma(int $karma): self
    {
        if ($karma >= self::Gold->threshold()) {
            return self::Gold;
        }

        if ($karma >= self::Silver->threshold()) {
            return self::Silver;
        }

        return self::Bronze;
    }

    public static function fromKarmaOrNull(int $karma): ?self
    {
        if ($karma < self::Bronze->threshold()) {
            return null;
        }

        return self::fromKarma($karma);
    }
}
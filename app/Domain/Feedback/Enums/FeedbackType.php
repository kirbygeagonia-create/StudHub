<?php

namespace App\Domain\Feedback\Enums;

enum FeedbackType: string
{
    case Bug = 'bug';
    case Feature = 'feature';
    case Praise = 'praise';
    case Other = 'other';
    case General = 'feedback';

    public function label(): string
    {
        return match ($this) {
            self::Bug => 'Bug Report',
            self::Feature => 'Feature Request',
            self::Praise => 'Praise',
            self::Other => 'Other',
            self::General => 'General Feedback',
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(fn (self $case) => $case->value, self::cases());
    }
}

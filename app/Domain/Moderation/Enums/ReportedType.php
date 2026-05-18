<?php

namespace App\Domain\Moderation\Enums;

enum ReportedType: string
{
    case Message = 'message';
    case Resource = 'resource';
    case User = 'user';

    public function label(): string
    {
        return match ($this) {
            self::Message => 'Chat Message',
            self::Resource => 'Resource',
            self::User => 'User',
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

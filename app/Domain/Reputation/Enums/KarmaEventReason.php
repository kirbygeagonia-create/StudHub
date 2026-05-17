<?php

namespace App\Domain\Reputation\Enums;

enum KarmaEventReason: string
{
    case ResourceUploaded = 'resource_uploaded';
    case ResourceSaved = 'resource_saved';
    case RequestFulfilled = 'request_fulfilled';
    case ChatMarkedHelpful = 'chat_marked_helpful';
    case ReportConfirmed = 'report_confirmed';

    public function label(): string
    {
        return match ($this) {
            self::ResourceUploaded => 'Resource uploaded',
            self::ResourceSaved => 'Resource saved by others',
            self::RequestFulfilled => 'Request fulfilled',
            self::ChatMarkedHelpful => 'Chat marked helpful',
            self::ReportConfirmed => 'Report confirmed',
        };
    }

    public function delta(): int
    {
        return match ($this) {
            self::ResourceUploaded => +5,
            self::ResourceSaved => +5,
            self::RequestFulfilled => +10,
            self::ChatMarkedHelpful => +2,
            self::ReportConfirmed => -5,
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
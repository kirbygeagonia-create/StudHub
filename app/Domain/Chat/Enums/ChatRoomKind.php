<?php

namespace App\Domain\Chat\Enums;

enum ChatRoomKind: string
{
    case Program = 'program';
    case ProgramYear = 'program_year';
    case Request = 'request';
    case Announcement = 'announcement';

    public function label(): string
    {
        return match ($this) {
            self::Program => 'Program chat',
            self::ProgramYear => 'Program year chat',
            self::Request => 'Request thread',
            self::Announcement => 'Announcements',
        };
    }
}

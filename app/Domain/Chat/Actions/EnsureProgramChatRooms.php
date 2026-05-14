<?php

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Enums\ChatRoomKind;
use App\Models\ChatRoom;
use App\Models\Program;
use Illuminate\Support\Str;

/**
 * Auto-creates one #general chat room per program and one room per
 * (program × year_level) sub-channel. Idempotent — safe to run on every
 * deploy or seeder pass.
 */
class EnsureProgramChatRooms
{
    /**
     * @return array{rooms_created: int, rooms_total: int}
     */
    public function run(?Program $only = null): array
    {
        $query = Program::query()->where('is_active', true);

        if ($only !== null) {
            $query->whereKey($only->id);
        }

        $created = 0;

        foreach ($query->get() as $program) {
            $created += $this->ensureProgramRoom($program);
            $created += $this->ensureYearRooms($program);
        }

        return [
            'rooms_created' => $created,
            'rooms_total' => ChatRoom::count(),
        ];
    }

    private function ensureProgramRoom(Program $program): int
    {
        $slug = Str::slug($program->code) . '-general';

        $room = ChatRoom::firstOrCreate(
            ['school_id' => $program->school_id, 'slug' => $slug],
            [
                'kind' => ChatRoomKind::Program->value,
                'program_id' => $program->id,
                'year_level' => null,
                'title' => $program->code . ' — General',
                'description' => 'General discussion for ' . $program->name . ' students.',
            ],
        );

        return $room->wasRecentlyCreated ? 1 : 0;
    }

    private function ensureYearRooms(Program $program): int
    {
        $created = 0;
        $max = max(1, (int) $program->default_year_levels);

        for ($year = 1; $year <= $max; $year++) {
            $slug = Str::slug($program->code) . '-y' . $year;

            $room = ChatRoom::firstOrCreate(
                ['school_id' => $program->school_id, 'slug' => $slug],
                [
                    'kind' => ChatRoomKind::ProgramYear->value,
                    'program_id' => $program->id,
                    'year_level' => $year,
                    'title' => $program->code . ' — Year ' . $year,
                    'description' => 'Year-' . $year . ' discussion for ' . $program->code . '.',
                ],
            );

            if ($room->wasRecentlyCreated) {
                $created++;
            }
        }

        return $created;
    }
}

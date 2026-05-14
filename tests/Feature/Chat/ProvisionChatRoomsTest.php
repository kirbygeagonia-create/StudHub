<?php

use App\Domain\Chat\Actions\EnsureProgramChatRooms;
use App\Domain\Chat\Enums\ChatRoomKind;
use App\Models\ChatRoom;
use App\Models\Program;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
});

it('creates one program room plus year sub-rooms for every active program', function () {
    (new EnsureProgramChatRooms)->run();

    $bsit = Program::where('code', 'BSIT')->firstOrFail();

    expect(ChatRoom::where('program_id', $bsit->id)->where('kind', ChatRoomKind::Program->value)->count())->toBe(1);
    expect(ChatRoom::where('program_id', $bsit->id)->where('kind', ChatRoomKind::ProgramYear->value)->count())
        ->toBe($bsit->default_year_levels);

    $bsce = Program::where('code', 'BSCE')->firstOrFail();
    expect(ChatRoom::where('program_id', $bsce->id)->where('kind', ChatRoomKind::ProgramYear->value)->count())
        ->toBe(5);
});

it('is idempotent — running twice does not duplicate rooms', function () {
    $first = (new EnsureProgramChatRooms)->run();
    $second = (new EnsureProgramChatRooms)->run();

    expect($second['rooms_created'])->toBe(0);
    expect($second['rooms_total'])->toBe($first['rooms_total']);
});

it('uses program slugs that include the program code', function () {
    (new EnsureProgramChatRooms)->run();
    $bsit = Program::where('code', 'BSIT')->firstOrFail();

    $programRoom = ChatRoom::where('program_id', $bsit->id)
        ->where('kind', ChatRoomKind::Program->value)
        ->firstOrFail();

    expect($programRoom->slug)->toContain('bsit');
    expect($programRoom->title)->toContain('BSIT');
});

<?php

use App\Domain\Chat\Actions\EnsureProgramChatRooms;
use App\Domain\Chat\Enums\ChatRoomKind;
use App\Models\ChatRoom;
use App\Models\College;
use App\Models\Program;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    (new EnsureProgramChatRooms)->run();
});

it('lists only rooms scoped to the user\'s program and year', function () {
    $bsit = Program::where('code', 'BSIT')->firstOrFail();
    $user = User::factory()->onboarded()->create([
        'school_id' => $bsit->school_id,
        'college_id' => $bsit->college_id,
        'program_id' => $bsit->id,
        'year_level' => 2,
    ]);

    $response = $this->actingAs($user)->get('/chat');

    $response->assertOk();
    $response->assertSee('BSIT — General');
    $response->assertSee('BSIT — Year 2');
    $response->assertDontSee('BSCE — Year 2');
});

it('lets a student open their own program room', function () {
    $bsit = Program::where('code', 'BSIT')->firstOrFail();
    $room = ChatRoom::where('program_id', $bsit->id)
        ->where('kind', ChatRoomKind::Program->value)
        ->firstOrFail();

    $user = User::factory()->onboarded()->create([
        'school_id' => $bsit->school_id,
        'college_id' => $bsit->college_id,
        'program_id' => $bsit->id,
        'year_level' => 2,
    ]);

    $this->actingAs($user)
        ->get('/chat/' . $room->id)
        ->assertOk();
});

it('forbids a student from opening another program\'s room', function () {
    $bsit = Program::where('code', 'BSIT')->firstOrFail();
    $bsce = Program::where('code', 'BSCE')->firstOrFail();

    $room = ChatRoom::where('program_id', $bsce->id)
        ->where('kind', ChatRoomKind::Program->value)
        ->firstOrFail();

    $user = User::factory()->onboarded()->create([
        'school_id' => $bsit->school_id,
        'college_id' => $bsit->college_id,
        'program_id' => $bsit->id,
        'year_level' => 2,
    ]);

    $this->actingAs($user)
        ->get('/chat/' . $room->id)
        ->assertForbidden();
});

it('forbids a student from opening a wrong-year sub-channel', function () {
    $bsit = Program::where('code', 'BSIT')->firstOrFail();
    $year4Room = ChatRoom::where('program_id', $bsit->id)
        ->where('kind', ChatRoomKind::ProgramYear->value)
        ->where('year_level', 4)
        ->firstOrFail();

    $user = User::factory()->onboarded()->create([
        'school_id' => $bsit->school_id,
        'college_id' => $bsit->college_id,
        'program_id' => $bsit->id,
        'year_level' => 2,
    ]);

    $this->actingAs($user)
        ->get('/chat/' . $year4Room->id)
        ->assertForbidden();
});

it('redirects unauthenticated users to login', function () {
    $this->get('/chat')->assertRedirect(route('login'));
});

it('does not pollute college reference', function () {
    expect(College::count())->toBeGreaterThan(0);
});

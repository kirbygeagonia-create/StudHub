<?php

use App\Domain\Chat\Actions\EnsureProgramChatRooms;
use App\Domain\Chat\Enums\ChatRoomKind;
use App\Livewire\Chat\RoomConversation;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Program;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    (new EnsureProgramChatRooms)->run();

    $bsit = Program::where('code', 'BSIT')->firstOrFail();

    $this->room = ChatRoom::where('program_id', $bsit->id)
        ->where('kind', ChatRoomKind::Program->value)
        ->firstOrFail();

    $this->user = User::factory()->onboarded()->create([
        'school_id' => $bsit->school_id,
        'college_id' => $bsit->college_id,
        'program_id' => $bsit->id,
        'year_level' => 2,
    ]);
});

it('renders the composer with the empty-state message', function () {
    Livewire::actingAs($this->user)
        ->test(RoomConversation::class, ['room' => $this->room])
        ->assertSee('No messages yet')
        ->assertStatus(200);
});

it('persists a message via the Livewire component', function () {
    Livewire::actingAs($this->user)
        ->test(RoomConversation::class, ['room' => $this->room])
        ->set('body', 'first message')
        ->call('send')
        ->assertHasNoErrors()
        ->assertSet('body', '');

    expect(ChatMessage::where('chat_room_id', $this->room->id)->count())->toBe(1);
});

it('rejects empty submissions', function () {
    Livewire::actingAs($this->user)
        ->test(RoomConversation::class, ['room' => $this->room])
        ->set('body', '   ')
        ->call('send')
        ->assertHasErrors('body');

    expect(ChatMessage::where('chat_room_id', $this->room->id)->count())->toBe(0);
});

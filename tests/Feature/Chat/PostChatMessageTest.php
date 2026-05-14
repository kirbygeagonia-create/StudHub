<?php

use App\Domain\Chat\Actions\EnsureProgramChatRooms;
use App\Domain\Chat\Actions\PostChatMessage;
use App\Domain\Chat\Events\ChatMessagePosted;
use App\Domain\Chat\Notifications\ChatMentionNotification;
use App\Models\ChatRoom;
use App\Models\Program;
use App\Models\User;
use Database\Seeders\SeaitCollegesSeeder;
use Database\Seeders\SeaitProgramsSeeder;
use Database\Seeders\SeaitSchoolSeeder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    $this->seed(SeaitSchoolSeeder::class);
    $this->seed(SeaitCollegesSeeder::class);
    $this->seed(SeaitProgramsSeeder::class);
    (new EnsureProgramChatRooms)->run();

    $this->bsit = Program::where('code', 'BSIT')->firstOrFail();
    $this->room = ChatRoom::where('program_id', $this->bsit->id)
        ->whereNull('year_level')
        ->firstOrFail();
});

it('persists the message and dispatches the broadcast event', function () {
    Event::fake([ChatMessagePosted::class]);

    $sender = User::factory()->onboarded()->create();

    $message = (new PostChatMessage)->run($this->room, $sender, 'Hello world');

    expect($message->id)->not->toBeNull();
    expect($message->body)->toBe('Hello world');
    expect($message->chat_room_id)->toBe($this->room->id);
    expect($message->sender_id)->toBe($sender->id);

    Event::assertDispatched(ChatMessagePosted::class, function (ChatMessagePosted $event) use ($message): bool {
        return $event->message->id === $message->id;
    });
});

it('refuses to post an empty message without an attachment', function () {
    $sender = User::factory()->onboarded()->create();

    expect(fn () => (new PostChatMessage)->run($this->room, $sender, '   '))
        ->toThrow(InvalidArgumentException::class);
});

it('resolves @display_name mentions and sends a database notification', function () {
    Notification::fake();

    $sender = User::factory()->onboarded()->create(['display_name' => 'Alice']);
    $target = User::factory()->onboarded()->create(['display_name' => 'BobReviewer']);
    $other = User::factory()->onboarded()->create(['display_name' => 'Charlie']);

    $message = (new PostChatMessage)->run($this->room, $sender, 'Hey @BobReviewer can you share the module?');

    expect($message->mentions->pluck('id')->all())->toBe([$target->id]);

    Notification::assertSentTo($target, ChatMentionNotification::class);
    Notification::assertNotSentTo($other, ChatMentionNotification::class);
});

it('does not mention the sender themselves', function () {
    Notification::fake();

    $sender = User::factory()->onboarded()->create(['display_name' => 'Selfie']);

    $message = (new PostChatMessage)->run($this->room, $sender, 'note to self @Selfie remember the exam');

    expect($message->mentions)->toHaveCount(0);
    Notification::assertNothingSent();
});

it('broadcasts on the chat-room scoped private channel', function () {
    $sender = User::factory()->onboarded()->create();

    $message = (new PostChatMessage)->run($this->room, $sender, 'channel check');

    $event = new ChatMessagePosted($message);
    $channels = collect($event->broadcastOn())->map(fn ($c) => $c->name)->all();

    expect($channels)->toContain('private-chat-room.' . $this->room->id);
    expect($event->broadcastAs())->toBe('chat.message.posted');
});

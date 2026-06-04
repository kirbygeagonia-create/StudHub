<?php

use App\Domain\Chat\Events\ChatMessagePosted;
use App\Models\ChatMessage;
use App\Models\User;

it('includes is_system in the broadcast payload', function () {
    $message = ChatMessage::factory()->make([
        'id' => 1,
        'chat_room_id' => 1,
        'body' => 'Hello',
        'is_system' => true,
    ]);

    $event = new ChatMessagePosted($message);
    $payload = $event->broadcastWith();

    expect($payload)->toHaveKey('is_system');
    expect($payload['is_system'])->toBeTrue();
});

it('includes all required keys in the broadcast payload', function () {
    $sender = User::factory()->onboarded()->create(['display_name' => 'TestUser']);
    $message = ChatMessage::factory()->make([
        'id' => 1,
        'chat_room_id' => 1,
        'body' => 'Hello world',
        'is_system' => false,
        'sender_id' => $sender->id,
    ]);
    $message->setRelation('sender', $sender);

    $event = new ChatMessagePosted($message);
    $payload = $event->broadcastWith();

    expect($payload)->toHaveKeys(['id', 'chat_room_id', 'body', 'is_system', 'created_at', 'sender']);
    expect($payload['sender'])->toHaveKeys(['id', 'display_name']);
});

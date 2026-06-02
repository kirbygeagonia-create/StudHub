<?php

use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function (User $user, int $id) {
    return $user->id === $id;
});

Broadcast::channel('chat-room.{roomId}', function (User $user, int $roomId): array|bool {
    $room = ChatRoom::find($roomId);

    if (! $room) {
        return false;
    }

    if (! $user->can('view', $room)) {
        return false;
    }

    return [
        'id' => $user->id,
        'display_name' => $user->preferredDisplayName(),
        'program_code' => $user->program?->code,
        'year_level' => $user->year_level,
    ];
});

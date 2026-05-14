<?php

namespace App\Domain\Chat\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessagePosted implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public readonly ChatMessage $message) {}

    /**
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat-room.' . $this->message->chat_room_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.message.posted';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        $sender = $this->message->sender;

        return [
            'id' => $this->message->id,
            'chat_room_id' => $this->message->chat_room_id,
            'body' => $this->message->body,
            'attachment_url' => $this->message->attachment_url,
            'attachment_mime' => $this->message->attachment_mime,
            'created_at' => $this->message->created_at?->toIso8601String(),
            'sender' => [
                'id' => $sender?->id,
                'display_name' => $sender?->preferredDisplayName(),
                'program_code' => $sender?->program?->code,
                'year_level' => $sender?->year_level,
            ],
        ];
    }
}

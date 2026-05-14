<?php

namespace App\Domain\Chat\Notifications;

use App\Models\ChatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ChatMentionNotification extends Notification
{
    use Queueable;

    public function __construct(public readonly ChatMessage $message) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $sender = $this->message->sender;

        return [
            'type' => 'chat.mention',
            'chat_room_id' => $this->message->chat_room_id,
            'chat_message_id' => $this->message->id,
            'preview' => mb_substr($this->message->body, 0, 160),
            'sender' => [
                'id' => $sender?->id,
                'display_name' => $sender?->preferredDisplayName(),
            ],
        ];
    }
}

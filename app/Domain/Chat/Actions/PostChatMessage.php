<?php

namespace App\Domain\Chat\Actions;

use App\Domain\Chat\Events\ChatMessagePosted;
use App\Domain\Chat\Notifications\ChatMentionNotification;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use InvalidArgumentException;

/**
 * Persists a chat message, resolves @-mentions against the school roster,
 * dispatches the broadcast event, and fans out database notifications to
 * mentioned users.
 *
 * @phpstan-type AttachmentPayload array{url: string, mime?: string|null, size?: int|null}
 */
class PostChatMessage
{
    /**
     * @param  AttachmentPayload|null  $attachment
     */
    public function run(ChatRoom $room, User $sender, string $body, ?array $attachment = null, ?int $replyToMessageId = null): ChatMessage
    {
        $body = trim($body);

        if ($body === '' && $attachment === null) {
            throw new InvalidArgumentException('Chat message must have a body or an attachment.');
        }

        /** @var ChatMessage $message */
        $message = DB::transaction(function () use ($room, $sender, $body, $attachment, $replyToMessageId) {
            $message = ChatMessage::create([
                'chat_room_id' => $room->id,
                'sender_id' => $sender->id,
                'body' => $body,
                'attachment_url' => $attachment['url'] ?? null,
                'attachment_mime' => $attachment['mime'] ?? null,
                'attachment_size' => $attachment['size'] ?? null,
                'reply_to_message_id' => $replyToMessageId,
            ]);

            $mentionedUsers = $this->resolveMentions($body, $sender);

            if ($mentionedUsers->isNotEmpty()) {
                $message->mentions()->sync($mentionedUsers->pluck('id')->all());
            }

            return $message->load(['sender.program', 'mentions']);
        });

        broadcast(new ChatMessagePosted($message));

        if ($message->mentions->isNotEmpty()) {
            Notification::send($message->mentions, new ChatMentionNotification($message));
        }

        return $message;
    }

    /**
     * Resolve `@display_name` (and `@first.last` style) mentions against
     * users in the sender's school. The sender themselves is excluded.
     *
     * @return Collection<int, User>
     */
    private function resolveMentions(string $body, User $sender): Collection
    {
        preg_match_all('/(?<![\w@])@([A-Za-z0-9_.\-]{2,40})/u', $body, $matches);

        $handles = collect($matches[1])
            ->map(fn (string $h) => mb_strtolower($h))
            ->unique()
            ->values();

        if ($handles->isEmpty()) {
            return User::query()->whereRaw('1 = 0')->get();
        }

        return User::query()
            ->when($sender->school_id, fn ($q) => $q->where('school_id', $sender->school_id))
            ->whereKeyNot($sender->id)
            ->where(function ($q) use ($handles) {
                foreach ($handles as $handle) {
                    $q->orWhereRaw('LOWER(display_name) = ?', [$handle])
                        ->orWhereRaw('LOWER(REPLACE(name, " ", ".")) = ?', [$handle]);
                }
            })
            ->get();
    }
}

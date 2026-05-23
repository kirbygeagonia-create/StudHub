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
    /** @var list<string> */
    private const ALLOWED_ATTACHMENT_MIMES = [
        'application/pdf',
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
    ];

    /**
     * @param  AttachmentPayload|null  $attachment
     */
    public function handle(ChatRoom $room, User $sender, string $body, ?array $attachment = null, ?int $replyToMessageId = null): ChatMessage
    {
        $body = trim($body);

        if ($body === '' && $attachment === null) {
            throw new InvalidArgumentException('Chat message must have a body or an attachment.');
        }

        if ($attachment !== null && isset($attachment['mime'])) {
            if (! in_array($attachment['mime'], self::ALLOWED_ATTACHMENT_MIMES, true)) {
                throw new InvalidArgumentException('Attachment file type is not allowed.');
            }
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
        preg_match_all('/@([\w.\-]+)/u', $body, $matches);

        $mentionedNames = collect($matches[1])->unique()->all();

        if (empty($mentionedNames)) {
            return User::whereRaw('0 = 1')->get();
        }

        return User::where('school_id', $sender->school_id)
            ->where('id', '!=', $sender->id)
            ->where(function ($query) use ($mentionedNames) {
                $query->whereIn('display_name', $mentionedNames);
                foreach ($mentionedNames as $name) {
                    $escaped = str_replace(['%', '_'], ['\%', '\_'], $name);
                    $query->orWhere(DB::raw("REPLACE(display_name, ' ', '.')"), 'like', "%{$escaped}%");
                }
            })
            ->get();
    }
}

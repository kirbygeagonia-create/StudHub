<?php

namespace App\Livewire\Chat;

use App\Domain\Chat\Actions\PostChatMessage;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RoomConversation extends Component
{
    use WithFileUploads;

    public ChatRoom $room;

    /** @var Collection<int, ChatMessage> */
    public Collection $broadcastMessages;

    public ?int $oldestMessageId = null;

    public int $messagePage = 0;

    #[Validate('required_without:attachment|string|max:4000')]
    public string $body = '';

    /** @var TemporaryUploadedFile|null */
    public $attachment = null;

    public function mount(ChatRoom $room): void
    {
        $user = auth()->user();

        if ($user === null) {
            throw new AccessDeniedHttpException('You must be logged in to view this chat room.');
        }

        if (! $user->can('view', $room)) {
            throw new AccessDeniedHttpException('You cannot view this chat room.');
        }

        $this->room = $room;
        $this->broadcastMessages = collect();

        // Track membership — upsert so it's safe on repeat visits
        $user = auth()->user();
        $user->chatRooms()->syncWithoutDetaching([
            $room->id => [
                'joined_at' => now(),
                'last_read_at' => now(),
                'unread_count' => 0,
            ],
        ]);
    }

    /**
     * @return Collection<int, ChatMessage>
     */
    #[Computed]
    public function roomMessages(): Collection
    {
        $persisted = $this->room->messages()
            ->with('sender.program')
            ->orderBy('created_at', 'desc')
            ->take($this->messagePage * 50)
            ->get()
            ->reverse();

        // Merge broadcast messages that arrived after the last fetch
        $allMessages = $persisted->merge($this->broadcastMessages)
            ->unique('id')
            ->sortBy('created_at')
            ->values();

        return $allMessages;
    }

    #[Computed]
    public function hasMoreMessages(): bool
    {
        $totalMessages = $this->room->messages()->count();

        return $totalMessages > ($this->messagePage * 50);
    }

    public function loadMore(): void
    {
        $this->messagePage++;
        unset($this->roomMessages);
        // Clear broadcast messages when loading more to avoid duplicates
        $this->broadcastMessages = collect();
    }

    public function send(PostChatMessage $action): void
    {
        $this->body = trim($this->body);

        $this->validate([
            'body' => ['required_without:attachment', 'nullable', 'string', 'max:4000'],
            'attachment' => ['nullable', 'file', 'max:25600', 'mimetypes:image/jpeg,image/png,image/webp,image/gif,application/pdf'],
        ]);

        $user = auth()->user();
        abort_unless($user !== null, 403);

        $attachmentPayload = null;

        if ($this->attachment !== null) {
            $path = $this->attachment->store('chat-attachments', 'public');
            $attachmentPayload = [
                'url' => '/storage/' . $path,
                'mime' => $this->attachment->getMimeType(),
                'size' => $this->attachment->getSize(),
                'name' => $this->attachment->getClientOriginalName(),
            ];
        }

        try {
            $action->handle($this->room, $user, $this->body, $attachmentPayload);
        } catch (InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());

            return;
        }

        $this->body = '';
        $this->attachment = null;

        // Clear broadcast messages since we'll get the broadcast event
        $this->broadcastMessages = collect();

        $this->dispatch('message-sent');
    }

    /** @param array<string, mixed> $payload */
    #[On('echo-private:chat-room.{room.id},chat.message.posted')]
    public function onMessageBroadcast(array $payload): void
    {
        // Only append if we're at the bottom (latest messages)
        // Create a lightweight message-like object from the payload
        $message = new ChatMessage;
        $message->id = $payload['id'];
        $message->chat_room_id = $payload['chat_room_id'];
        $message->body = $payload['body'] ?? '';
        $message->attachment_url = $payload['attachment_url'] ?? null;
        $message->attachment_mime = $payload['attachment_mime'] ?? null;
        $message->created_at = isset($payload['created_at']) ? Carbon::parse($payload['created_at']) : now();
        $message->sender_id = $payload['sender']['id'] ?? null;
        $message->is_system = $payload['is_system'] ?? false;

        // Set up sender relationship manually
        if (isset($payload['sender'])) {
            $sender = new User;
            $sender->id = $payload['sender']['id'];
            $sender->display_name = $payload['sender']['display_name'];
            $message->setRelation('sender', $sender);
        }

        $this->broadcastMessages->push($message);
    }

    public function render(): View
    {
        return view('livewire.chat.room-conversation');
    }
}

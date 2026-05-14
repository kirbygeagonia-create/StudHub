<?php

namespace App\Livewire\Chat;

use App\Domain\Chat\Actions\PostChatMessage;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
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

    #[Validate('required_without:attachment|string|max:4000')]
    public string $body = '';

    /** @var TemporaryUploadedFile|null */
    public $attachment = null;

    public function mount(ChatRoom $room): void
    {
        $user = auth()->user();

        if ($user === null || $room->school_id !== $user->school_id) {
            throw new AccessDeniedHttpException('You cannot view this chat room.');
        }

        if ($room->program_id !== null && $room->program_id !== $user->program_id) {
            throw new AccessDeniedHttpException('You cannot view this chat room.');
        }

        if ($room->year_level !== null && $room->year_level !== $user->year_level) {
            throw new AccessDeniedHttpException('You cannot view this chat room.');
        }

        $this->room = $room;
    }

    /**
     * @return Collection<int, ChatMessage>
     */
    #[Computed]
    public function roomMessages(): Collection
    {
        return $this->room->messages()
            ->with('sender.program')
            ->latest()
            ->limit(50)
            ->get()
            ->reverse()
            ->values();
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
            ];
        }

        $action->run($this->room, $user, $this->body, $attachmentPayload);

        $this->body = '';
        $this->attachment = null;

        unset($this->roomMessages);
    }

    #[On('echo-private:chat-room.{room.id},chat.message.posted')]
    public function onMessageBroadcast(): void
    {
        unset($this->roomMessages);
    }

    public function render(): View
    {
        return view('livewire.chat.room-conversation');
    }
}

<div class="space-y-4" wire:poll.10s>
    <div data-testid="chat-message-list" class="h-96 overflow-y-auto border border-gray-100 rounded-lg p-4 bg-gray-50 space-y-3">
        @forelse ($this->roomMessages as $message)
            <article class="text-sm" data-testid="chat-message">
                <header class="flex items-baseline gap-2 mb-0.5">
                    <span class="font-semibold text-gray-900">{{ $message->sender?->preferredDisplayName() ?? 'Unknown' }}</span>
                    @if ($message->sender?->program?->code)
                        <span class="text-xs text-gray-400">{{ $message->sender->program->code }}{{ $message->sender->year_level ? '·Y' . $message->sender->year_level : '' }}</span>
                    @endif
                    <time class="text-xs text-gray-400">{{ $message->created_at?->diffForHumans() }}</time>
                </header>
                <p class="text-gray-700 whitespace-pre-wrap">{{ $message->body }}</p>
                @if ($message->hasAttachment())
                    <p class="mt-1 text-xs">
                        <a href="{{ $message->attachment_url }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                            Attachment ({{ $message->attachment_mime ?? 'file' }})
                        </a>
                    </p>
                @endif
            </article>
        @empty
            <p class="text-sm text-gray-500" data-testid="chat-empty-state">No messages yet — be the first to say hi.</p>
        @endforelse
    </div>

    <form wire:submit="send" class="space-y-2">
        <div>
            <label for="chat-body" class="sr-only">Message</label>
            <textarea
                id="chat-body"
                wire:model="body"
                rows="2"
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                placeholder="Type a message. Use @display_name to mention someone."
            ></textarea>
            @error('body') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="text-xs text-gray-500 inline-flex items-center gap-2">
                <input type="file" wire:model="attachment" class="text-xs" accept="image/*,application/pdf" />
                <span>≤ 25 MB · images / PDF</span>
            </label>
            <x-primary-button type="submit">Send</x-primary-button>
        </div>
        @error('attachment') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
    </form>
</div>

<div class="space-y-4" wire:poll.10s.visible>
    <div data-testid="chat-message-list" class="h-96 overflow-y-auto border border-gray-100 rounded-lg p-4 bg-gray-50 space-y-3">
        @forelse ($this->roomMessages as $message)
            <article class="text-sm" data-testid="chat-message" wire:key="message-{{ $message->id }}">
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
                        <a href="{{ $message->attachment_url }}" target="_blank" class="text-seait-500 hover:text-seait-800">
                            Attachment ({{ $message->attachment_mime ?? 'file' }})
                        </a>
                    </p>
                @endif
                <div class="mt-1">
                    <button type="button" onclick="document.getElementById('report-form-msg-{{ $message->id }}').classList.toggle('hidden')"
                            class="text-xs text-red-400 hover:text-red-600">
                        Report
                    </button>
                    <form id="report-form-msg-{{ $message->id }}" method="POST" action="{{ route('reports.store') }}" class="hidden mt-1 space-y-1">
                        @csrf
                        <input type="hidden" name="reported_type" value="message">
                        <input type="hidden" name="reported_id" value="{{ $message->id }}">
                        <select name="reason" required class="text-xs border-gray-300 rounded-md shadow-sm">
                            <option value="">Select reason</option>
                            @foreach (\App\Domain\Moderation\Enums\ReportReason::cases() as $reason)
                                <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                            @endforeach
                        </select>
                        <div class="flex items-center gap-1">
                            <input type="text" name="notes" maxlength="1000" placeholder="Optional note"
                                   class="text-xs border-gray-300 rounded-md shadow-sm flex-1">
<button type="submit" onclick="this.disabled=true; this.form.submit();"
                                            class="px-2 py-1 bg-red-500 border border-transparent rounded text-xs font-semibold text-white uppercase tracking-widest hover:bg-red-600 disabled:opacity-50">
                                        Submit
                                    </button>
                        </div>
                    </form>
                </div>
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
                class="w-full rounded-md border-gray-300 shadow-sm focus:border-seait-100 focus:ring focus:ring-seait-100 focus:ring-opacity-50"
                placeholder="Type a message. Use @display_name to mention someone."
            ></textarea>
            @error('body') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between">
            <label class="text-xs text-gray-500 inline-flex items-center gap-2">
                <input type="file" wire:model="attachment" class="text-xs" accept="image/*,application/pdf" />
                <span>≤ 25 MB · images / PDF</span>
            </label>
            <button type="submit" wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 bg-seait-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-seait-600 disabled:opacity-50">
                <span wire:loading.remove.delay>Send</span>
                <span wire:loading>Sending...</span>
            </button>
        </div>
        @error('attachment') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
    </form>
</div>

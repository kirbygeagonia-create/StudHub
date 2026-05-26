<div class="space-y-4" wire:poll.10s.visible>
    <div role="log" aria-live="polite" data-testid="chat-message-list"
         class="h-[28rem] overflow-y-auto rounded-2xl p-4 bg-gradient-to-b from-gray-50 to-white space-y-4 scroll-smooth dark:from-navy-850 dark:to-navy-800">
        @forelse ($this->roomMessages as $message)
            <article data-testid="chat-message" wire:key="message-{{ $message->id }}"
                     class="animate-fade-in group">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br from-gray-300 to-gray-400 dark:from-navy-500 dark:to-navy-700 flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr($message->sender?->preferredDisplayName() ?? '?', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-baseline gap-2 flex-wrap">
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                {{ $message->sender?->preferredDisplayName() ?? 'Unknown' }}
                            </span>
                            @if ($message->sender?->program?->code)
                                <span class="badge-seait text-[10px]">{{ $message->sender->program->code }}{{ $message->sender->year_level ? ' Y' . $message->sender->year_level : '' }}</span>
                            @endif
                            <span class="text-[11px] text-gray-400 dark:text-gray-500">{{ $message->created_at?->diffForHumans() }}</span>
                        </div>
                        <div class="mt-1 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed dark:text-gray-300">{{ $message->body }}</div>
                        @if ($message->hasAttachment())
                            <a href="{{ $message->attachment_url }}" target="_blank"
                               class="mt-2 inline-flex items-center gap-1.5 text-xs text-seait-600 hover:text-seait-700 bg-seait-50 px-2.5 py-1 rounded-lg dark:text-seait-400 dark:bg-seait-900/20 dark:hover:text-seait-300 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                Attachment
                            </a>
                        @endif
                        <div class="mt-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button type="button" onclick="document.getElementById('report-form-msg-{{ $message->id }}').classList.toggle('hidden')"
                                    class="text-[11px] text-red-400 hover:text-red-600 dark:hover:text-red-300 transition-colors">Report</button>
                        </div>
                    </div>
                </div>
                <form id="report-form-msg-{{ $message->id }}" method="POST" action="{{ route('reports.store') }}"
                      class="hidden mt-2 ml-11 p-3 rounded-xl bg-red-50/50 dark:bg-red-900/10 space-y-2">
                    @csrf
                    <input type="hidden" name="reported_type" value="message">
                    <input type="hidden" name="reported_id" value="{{ $message->id }}">
                    <select name="reason" required class="text-xs input-field">
                        <option value="">Select reason</option>
                        @foreach (\App\Domain\Moderation\Enums\ReportReason::cases() as $reason)
                            <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                        @endforeach
                    </select>
                    <div class="flex items-center gap-2">
                        <input type="text" name="notes" maxlength="1000" placeholder="Optional note" class="text-xs input-field flex-1">
                        <button type="submit" onclick="this.disabled=true; this.form.submit();"
                                class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition-colors disabled:opacity-50">Submit</button>
                    </div>
                </form>
            </article>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-center py-12">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-100 to-emerald-200 dark:from-emerald-800/20 dark:to-emerald-700/20 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium" data-testid="chat-empty-state">No messages yet</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Be the first to say hi!</p>
            </div>
        @endforelse
    </div>

    <form wire:submit="send" class="space-y-3">
        <div class="relative">
            <label for="chat-body" class="sr-only">Message</label>
            <textarea id="chat-body" wire:model="body" rows="2"
                      class="input-field resize-none pr-24"
                      placeholder="Type a message… @mention someone"
                      @keydown.enter.prevent="$wire.send()"
            ></textarea>
            <div class="absolute bottom-2 right-2 flex items-center gap-2">
                <label class="cursor-pointer p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" title="Attach file">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    <input type="file" wire:model="attachment" accept="image/*,application/pdf" class="hidden">
                </label>
                <button type="submit" wire:loading.attr="disabled"
                        class="p-2 rounded-lg bg-seait-500 hover:bg-seait-600 text-white transition-colors disabled:opacity-50"
                        title="Send message">
                    <svg wire:loading.remove.delay class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    <svg wire:loading class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>
            </div>
        </div>
        @error('body') <span class="text-xs text-red-600 block">{{ $message }}</span> @enderror
        @error('attachment') <span class="text-xs text-red-600 block">{{ $message }}</span> @enderror
    </form>
</div>
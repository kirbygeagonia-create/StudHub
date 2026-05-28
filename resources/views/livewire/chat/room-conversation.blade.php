<div class="flex flex-col h-full" wire:poll.10s.visible>

    {{-- Messages --}}
    <div id="chat-log"
         role="log"
         aria-live="polite"
         data-testid="chat-message-list"
         x-data="chatLog()"
         x-init="init()"
         class="flex-1 overflow-y-auto px-4 py-4 space-y-0.5 bg-gray-50/40 dark:bg-navy-900/20 scroll-smooth">

        @php
            $colors = ['from-violet-400 to-violet-600','from-sky-400 to-sky-600','from-emerald-400 to-emerald-600','from-rose-400 to-rose-600','from-amber-400 to-amber-600','from-fuchsia-400 to-fuchsia-600','from-cyan-400 to-cyan-600','from-teal-400 to-teal-600'];
            $messages = $this->roomMessages;
        @endphp

        @forelse ($messages as $index => $message)
            @php
                $isOwn      = $message->sender_id === Auth::id();
                $isAdmin    = $message->sender?->isAdmin();
                $isMod      = $message->sender?->isModerator();
                $prev       = $messages[$index - 1] ?? null;
                $showHeader = !$prev || $prev->sender_id !== $message->sender_id
                    || $message->created_at->diffInMinutes($prev->created_at) > 3;
                $colorClass = $colors[$message->sender_id % count($colors)];
                $initials   = strtoupper(substr($message->sender?->preferredDisplayName() ?? '?', 0, 2));
            @endphp

            <article data-testid="chat-message"
                     wire:key="message-{{ $message->id }}"
                     class="{{ $showHeader ? 'mt-4 first:mt-0' : 'mt-0.5' }} group">

                @if ($isOwn)
                    {{-- ===== OWN MESSAGE (right-aligned) ===== --}}
                    @if ($showHeader)
                        <div class="flex flex-col items-end mb-1">
                            <span class="text-[11px] text-gray-400 dark:text-gray-500 mr-1">You · {{ $message->created_at?->diffForHumans() }}</span>
                        </div>
                    @endif
                    <div class="flex justify-end">
                        <div class="max-w-[75%]">
                            <div class="bg-seait-500 dark:bg-seait-600 text-white text-sm px-4 py-2.5 rounded-2xl rounded-tr-md shadow-sm">
                                <p class="whitespace-pre-wrap leading-relaxed break-words">{{ $message->body }}</p>
                            </div>
                            @if ($message->hasAttachment())
                                @php
                                    $ext = strtolower(pathinfo($message->attachment_name ?? $message->attachment_url, PATHINFO_EXTENSION));
                                    $iconColor = match($ext) {
                                        'pdf' => 'text-red-400',
                                        'doc', 'docx' => 'text-blue-400',
                                        'xls', 'xlsx' => 'text-green-400',
                                        'ppt', 'pptx' => 'text-orange-400',
                                        'jpg', 'jpeg', 'png', 'gif', 'webp' => 'text-purple-400',
                                        'zip', 'rar', '7z' => 'text-yellow-400',
                                        default => 'text-gray-400',
                                    };
                                @endphp
                                <a href="{{ route('chat.attachments.download', $message) }}" target="_blank" rel="noopener"
                                   class="mt-1.5 inline-flex items-center gap-1.5 text-xs text-seait-300 hover:text-seait-200 transition-colors float-right bg-seait-500/20 hover:bg-seait-500/30 px-2 py-1 rounded-lg">
                                    <svg class="w-3.5 h-3.5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                    <span>{{ $message->attachment_name ?? 'Attachment' }}</span>
                                    @if ($message->attachment_size)
                                        <span class="opacity-60">· {{ round($message->attachment_size / 1024) }} KB</span>
                                    @endif
                                </a>
                            @endif
                        </div>
                    </div>

                @else
                    {{-- ===== OTHER'S MESSAGE (left-aligned) ===== --}}
                    @if ($showHeader)
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br {{ $colorClass }} flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                {{ $initials }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        {{ $message->sender?->preferredDisplayName() ?? 'Unknown' }}
                                    </span>
                                    @if ($isAdmin)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 uppercase tracking-wide">Admin</span>
                                    @elseif ($isMod)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 uppercase tracking-wide">Mod</span>
                                    @elseif ($message->sender?->program?->code)
                                        <span class="badge-seait text-[10px]">{{ $message->sender->program->code }}{{ $message->sender->year_level ? ' Y'.$message->sender->year_level : '' }}</span>
                                    @endif
                                    <time class="text-[11px] text-gray-400 dark:text-gray-500"
                                          title="{{ $message->created_at?->format('M d, Y g:i A') }}">
                                        {{ $message->created_at?->diffForHumans() }}
                                    </time>
                                </div>
                                <div class="max-w-[75%] bg-white dark:bg-navy-800 border border-gray-100 dark:border-navy-700/50 rounded-2xl rounded-tl-md px-4 py-2.5 shadow-sm text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed break-words">
                                    {{ $message->body }}
                                </div>
                                @if ($message->hasAttachment())
                                    @php
                                        $ext = strtolower(pathinfo($message->attachment_name ?? $message->attachment_url, PATHINFO_EXTENSION));
                                        $iconColor = match($ext) {
                                            'pdf' => 'text-red-500',
                                            'doc', 'docx' => 'text-blue-500',
                                            'xls', 'xlsx' => 'text-green-500',
                                            'ppt', 'pptx' => 'text-orange-500',
                                            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'text-purple-500',
                                            'zip', 'rar', '7z' => 'text-yellow-500',
                                            default => 'text-gray-500',
                                        };
                                    @endphp
                                    <a href="{{ route('chat.attachments.download', $message) }}" target="_blank" rel="noopener"
                                       class="mt-1.5 inline-flex items-center gap-1.5 text-xs text-seait-600 bg-seait-50 hover:bg-seait-100 px-2.5 py-1 rounded-lg dark:text-seait-400 dark:bg-seait-900/20 transition-colors">
                                        <svg class="w-3.5 h-3.5 {{ $iconColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                                        <span>{{ $message->attachment_name ?? 'Attachment' }}</span>
                                        @if ($message->attachment_size)
                                            <span class="opacity-60">· {{ round($message->attachment_size / 1024) }} KB</span>
                                        @endif
                                    </a>
                                @endif
                            </div>
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 mt-0.5">
                                <button type="button" onclick="document.getElementById('rf-{{ $message->id }}').classList.toggle('hidden')"
                                        class="p-1 rounded text-gray-300 hover:text-red-400 dark:text-gray-600 dark:hover:text-red-400 transition-colors" title="Report">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                                </button>
                            </div>
                        </div>
                    @else
                        {{-- Compact continuation --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 flex-shrink-0 flex items-center justify-center">
                                <time class="text-[10px] text-gray-300 dark:text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity">{{ $message->created_at?->format('g:i') }}</time>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="max-w-[75%] bg-white dark:bg-navy-800 border border-gray-100 dark:border-navy-700/50 rounded-2xl px-4 py-2.5 shadow-sm text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed break-words">
                                    {{ $message->body }}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Report form --}}
                    <form id="rf-{{ $message->id }}" method="POST" action="{{ route('reports.store') }}"
                          class="hidden mt-2 ml-11 p-3 rounded-xl bg-red-50/70 dark:bg-red-900/10 border border-red-100 dark:border-red-800/20 space-y-2">
                        @csrf
                        <input type="hidden" name="reported_type" value="message">
                        <input type="hidden" name="reported_id" value="{{ $message->id }}">
                        <p class="text-xs font-semibold text-red-700 dark:text-red-400">Report message</p>
                        <select name="reason" required class="text-xs input-field w-full">
                            <option value="">Select reason…</option>
                            @foreach (\App\Domain\Moderation\Enums\ReportReason::cases() as $reason)
                                <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                            @endforeach
                        </select>
                        <div class="flex gap-2">
                            <input type="text" name="notes" maxlength="1000" placeholder="Optional note" class="text-xs input-field flex-1">
                            <button type="submit" onclick="this.disabled=true;this.form.submit();"
                                    class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition-colors disabled:opacity-50">Submit</button>
                            <button type="button" onclick="this.closest('form').classList.add('hidden')"
                                    class="px-3 py-1.5 border border-gray-200 dark:border-navy-600 text-xs text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors">Cancel</button>
                        </div>
                    </form>
                @endif

            </article>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-center py-16">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-seait-100 to-seait-200 dark:from-seait-900/30 dark:to-seait-800/20 flex items-center justify-center mb-4">
                    <x-icon name="chat" class="w-10 h-10 text-seait-400 dark:text-seait-500" />
                </div>
                <p class="text-base font-semibold text-gray-700 dark:text-gray-300" data-testid="chat-empty-state">No messages yet</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Be the first to say something!</p>
            </div>
        @endforelse

        {{-- Scroll sentinel --}}
        <div id="chat-bottom"></div>
    </div>

    {{-- Jump to bottom button --}}
    <div class="relative flex-shrink-0 pointer-events-none">
        <div id="scroll-btn-wrap" class="absolute -top-12 right-4 z-10 pointer-events-auto hidden">
            <button onclick="document.getElementById('chat-bottom').scrollIntoView({ behavior: 'smooth' })"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-seait-500 text-white text-xs font-medium rounded-full shadow-lg hover:bg-seait-600 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                Jump to latest
            </button>
        </div>
    </div>

    {{-- Input bar --}}
    <div class="flex-shrink-0 border-t border-gray-200/60 dark:border-navy-700/40 bg-white/80 dark:bg-navy-800/80 backdrop-blur-sm px-4 py-3">
        {{-- File upload progress + preview --}}
        <div x-data="{ uploading: false, progress: 0 }"
             x-on:livewire-upload-start="uploading = true; progress = 0"
             x-on:livewire-upload-finish="uploading = false"
             x-on:livewire-upload-error="uploading = false"
             x-on:livewire-upload-progress="progress = $event.detail.progress">
            {{-- Upload progress bar --}}
            <div x-show="uploading" class="mb-2 p-3 rounded-xl bg-gray-50 dark:bg-navy-700/50 border border-gray-200 dark:border-navy-700">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-seait-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300">Uploading attachment…</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400" x-text="progress + '%'"></span>
                        </div>
                        <div class="h-1.5 w-full bg-gray-200 dark:bg-navy-600 rounded-full overflow-hidden">
                            <div class="h-full bg-seait-500 rounded-full transition-all duration-300" :style="'width: ' + progress + '%'"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- File preview card --}}
            <template x-if="$wire.attachment">
                <div class="mb-2 p-2.5 rounded-xl bg-gray-50 dark:bg-navy-700/50 border border-gray-200 dark:border-navy-700 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-seait-100 dark:bg-seait-900/30 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-gray-700 dark:text-gray-300 truncate" x-text="$wire.attachment ? $wire.attachment.name : ''"></p>
                        <p class="text-[10px] text-gray-400 dark:text-gray-500" x-text="$wire.attachment ? Math.round($wire.attachment.size / 1024) + ' KB' : ''"></p>
                    </div>
                    <button type="button" x-on:click="$wire.set('attachment', null)"
                            class="p-1 rounded text-gray-300 hover:text-red-500 hover:bg-red-50 dark:text-gray-600 dark:hover:text-red-400 dark:hover:bg-red-900/10 transition-colors flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </template>

            <form wire:submit="send" class="flex items-end gap-2">
                <label class="flex-shrink-0 cursor-pointer p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors text-gray-400 hover:text-gray-600 border border-gray-200 dark:border-navy-700" title="Attach file">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                    <input type="file" wire:model="attachment" accept="image/*,application/pdf" class="hidden">
                </label>
                <div class="flex-1 relative">
                    <label for="chat-body" class="sr-only">Message</label>
                    <textarea id="chat-body" wire:model="body" rows="1"
                              class="input-field resize-none w-full max-h-32 overflow-y-auto !py-2.5"
                              placeholder="Send a message… @name to mention"
                              @keydown.enter.prevent="if(!$event.shiftKey){$wire.send()}else{$event.target.value+='\n'}"
                              oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,128)+'px'"></textarea>
                </div>
                <button type="submit" wire:loading.attr="disabled"
                        class="flex-shrink-0 p-2.5 rounded-xl bg-seait-500 hover:bg-seait-600 text-white transition-colors disabled:opacity-50 shadow-sm">
                    <x-icon wire:loading.remove.delay name="send" class="w-5 h-5" />
                    <x-icon wire:loading name="spinner" class="w-5 h-5 animate-spin" />
                </button>
            </form>
        </div>
        @error('body') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        @error('attachment') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        <p class="mt-1 text-[10px] text-gray-400 dark:text-gray-600">
            <kbd class="px-1 rounded bg-gray-100 dark:bg-navy-700 font-mono text-[9px]">Enter</kbd> send &nbsp;·&nbsp;
            <kbd class="px-1 rounded bg-gray-100 dark:bg-navy-700 font-mono text-[9px]">Shift+Enter</kbd> new line
        </p>
    </div>
</div>

@push('scripts')
<script>
function chatLog() {
    return {
        atBottom: true,
        init() {
            const log = this.$el;
            // Scroll to bottom on mount
            log.scrollTop = log.scrollHeight;

            // Track scroll position
            log.addEventListener('scroll', () => {
                this.atBottom = (log.scrollHeight - log.scrollTop - log.clientHeight) < 100;
                const btn = document.getElementById('scroll-btn-wrap');
                if (btn) btn.classList.toggle('hidden', this.atBottom);
            });

            // Re-scroll after every Livewire update (poll re-render)
            Livewire.hook('morph.updated', ({ el }) => {
                if (this.atBottom) {
                    this.$nextTick(() => {
                        log.scrollTop = log.scrollHeight;
                    });
                }
            });
        }
    }
}
</script>
@endpush
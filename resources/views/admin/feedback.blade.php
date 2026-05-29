<x-app-layout>
    <x-page-header title="Feedback Inbox" />

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card overflow-hidden">
                @if ($feedbacks->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">No feedback yet</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-navy-700/50">
                        @foreach ($feedbacks as $fb)
                            @php
                                $typeStyle = match($fb->type) {
                                    'bug'     => 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300',
                                    'feature' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300',
                                    'praise'  => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300',
                                    default   => 'bg-gray-100 text-gray-700 dark:bg-navy-700 dark:text-gray-300',
                                };
                                $typeIconName = match($fb->type) {
                                    'bug'     => 'warning',
                                    'feature' => 'sparkle',
                                    'praise'  => 'thumbs-up',
                                    default   => 'chat',
                                };
                            @endphp
                            <div class="px-6 py-5">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $fb->user?->display_name ?: $fb->user?->name ?: 'Anonymous' }}
                                        </span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $fb->user?->email }}</span>
                                        @if ($fb->user?->program)
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-navy-700 text-gray-500 dark:text-gray-400">
                                                {{ $fb->user->program->code }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $typeStyle }}">
                                            <x-icon name="{{ $typeIconName }}" class="w-3.5 h-3.5" /> {{ ucfirst($fb->type ?? 'feedback') }}
                                        </span>
                                        <time class="text-xs text-gray-400 dark:text-gray-500">{{ $fb->created_at->diffForHumans() }}</time>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $fb->body }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-navy-700/50">
                        {{ $feedbacks->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
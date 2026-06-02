<x-app-layout>
    <x-page-header title="{{ __('Chat') }}" />

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-2">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-seait-500 dark:hover:text-seait-400 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Dashboard
        </a>
    </div>
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if ($rooms->isEmpty())
                <div class="card p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mx-auto mb-4">
                        <x-icon name="chat" class="w-8 h-8 text-gray-400 dark:text-gray-500" />
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">No chat rooms yet</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Chat rooms are automatically created for your program.</p>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 px-1">
                    Rooms for
                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ auth()->user()->program?->code ?? 'your program' }}</span>
                </p>

                <div class="space-y-2">
                    @foreach ($rooms as $room)
                        <a href="{{ route('chat.show', $room) }}"
                           class="group flex items-center gap-4 p-4 card hover:shadow-md transition-all duration-200 hover:-translate-y-0.5">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-seait-400 to-seait-600 flex items-center justify-center flex-shrink-0 shadow-sm group-hover:scale-105 transition-transform">
                                <x-icon name="chat" class="w-5 h-5 text-white" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">#{{ $room->slug }}</span>
                                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 dark:bg-navy-700 dark:text-gray-400 uppercase tracking-wide">
                                        {{ $room->kind->label() }}
                                    </span>
                                    @if ($room->members->isNotEmpty() && $room->members->first()->pivot->unread_count > 0)
                                        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-[10px] font-bold text-white bg-red-500 rounded-full">
                                            {{ $room->members->first()->pivot->unread_count }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $room->title }}</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 group-hover:text-seait-400 group-hover:translate-x-1 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>

                {{-- School chat etiquette notice --}}
                <div class="card p-4 border-l-4 border-amber-400 dark:border-amber-500 bg-amber-50/40 dark:bg-amber-900/10">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.502-1.275.722-1.845l-6.928-5.013c-.752-.545-1.792-.545-2.544 0L5.094 17.155c-.78.57-.332 1.845.722 1.845z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300">School Chat — Keep it respectful</p>
                            <p class="text-xs text-amber-700 dark:text-amber-400 mt-0.5">
                                Be kind, stay on topic, no spam. Inside any room tap the <strong>Rules</strong> button for the full etiquette guide.
                                All activity is subject to the <a href="{{ route('aup') }}" class="underline hover:text-amber-900 dark:hover:text-amber-200">Acceptable Use Policy</a>.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
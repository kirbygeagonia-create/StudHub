<x-app-layout>
    <div class="flex flex-col" style="height: calc(100vh - 4rem); overflow: hidden;">

        {{-- Chat header --}}
        <div class="flex items-center gap-3 px-4 sm:px-6 py-3 bg-white/90 dark:bg-navy-800/90 backdrop-blur-sm border-b border-gray-200/60 dark:border-navy-700/40 flex-shrink-0">
            <a href="{{ route('chat.index') }}"
               class="p-1.5 rounded-lg hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors text-gray-500 dark:text-gray-400"
               title="All rooms">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-seait-400 to-seait-600 flex items-center justify-center flex-shrink-0">
                <x-icon name="chat" class="w-5 h-5 text-white" />
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-base font-bold text-gray-900 dark:text-gray-100 leading-tight">#{{ $room->slug }}</h1>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $room->title }} · {{ $room->kind->label() }}</p>
            </div>

            {{-- Rules panel --}}
            <div x-data="{ rulesOpen: false }" class="relative">
                <button @click="rulesOpen = !rulesOpen"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 dark:bg-amber-900/20 dark:text-amber-300 dark:hover:bg-amber-900/30 transition-colors border border-amber-200 dark:border-amber-800/40">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    Rules
                </button>
                <div x-show="rulesOpen"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     @click.outside="rulesOpen = false"
                     class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-navy-800 rounded-2xl shadow-xl border border-gray-100 dark:border-navy-700/50 z-50 p-4"
                     x-cloak>
                    <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-3 flex items-center gap-2">
                        <span class="text-base">📋</span> School Chat Etiquette
                    </h4>
                    <ul class="space-y-2.5">
                        @foreach ([
                            ['🎓', 'Be respectful', 'Treat everyone as you would in class. No bullying or hate speech.'],
                            ['📚', 'Stay on topic', 'Keep discussions relevant to academics and school life.'],
                            ['🔇', 'No spamming', 'Avoid repeated messages, all-caps, or emoji floods.'],
                            ['🔒', 'Protect privacy', 'Never share personal info or confidential documents.'],
                            ['⚠️', 'Report, don\'t retaliate', 'Use the Report button — don\'t escalate conflicts.'],
                            ['✅', 'Use @mentions wisely', 'Only @mention someone when your message is directly for them.'],
                        ] as [$emoji, $title, $desc])
                        <li class="flex items-start gap-2">
                            <span class="text-sm leading-none mt-0.5 flex-shrink-0">{{ $emoji }}</span>
                            <div>
                                <span class="text-xs font-semibold text-gray-900 dark:text-gray-100">{{ $title }}: </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $desc }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    <p class="mt-3 pt-3 border-t border-gray-100 dark:border-navy-700/50 text-[10px] text-gray-400 dark:text-gray-500">
                        Full policy: <a href="{{ route('aup') }}" class="underline hover:text-seait-500">Acceptable Use Policy</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Livewire chat component fills remaining height --}}
        <div class="flex-1 overflow-hidden">
            <livewire:chat.room-conversation :room="$room" />
        </div>
    </div>
</x-app-layout>
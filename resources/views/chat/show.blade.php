<x-app-layout>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-2">
        <a href="{{ route('chat.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-seait-500 dark:hover:text-seait-400 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Chat Rooms
        </a>
    </div>

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
                        <svg class="w-4 h-4 text-seait-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg> School Chat Etiquette
                    </h4>
                    <ul class="space-y-2.5">
                        @foreach ([
                            ['<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>', 'Be respectful', 'Treat everyone as you would in class. No bullying or hate speech.'],
                            ['<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25"/></svg>', 'Stay on topic', 'Keep discussions relevant to academics and school life.'],
                            ['<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/></svg>', 'No spamming', 'Avoid repeated messages, all-caps, or emoji floods.'],
                            ['<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>', 'Protect privacy', 'Never share personal info or confidential documents.'],
                            ['<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z"/></svg>', 'Report, don\'t retaliate', 'Use the Report button — don\'t escalate conflicts.'],
                            ['<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>', 'Use @mentions wisely', 'Only @mention someone when your message is directly for them.'],
                        ] as [$svg, $title, $desc])
                        <li class="flex items-start gap-2">
                            <span class="text-seait-500 dark:text-seait-400 flex-shrink-0 mt-0.5">{!! $svg !!}</span>
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
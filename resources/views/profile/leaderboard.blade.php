<x-app-layout>
    <x-page-header title="{{ __('Leaderboard') }}" />

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Program filter --}}
            <div class="card p-4">
                <form method="GET" action="{{ route('leaderboard') }}" class="flex items-end gap-3">
                    <div class="flex-1">
                        <label for="program_id" class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 block">Filter by Program</label>
                        <select id="program_id" name="program_id" class="w-full input-field text-sm" onchange="this.form.submit()">
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}" @selected($selectedProgramId == $program->id)>
                                    {{ $program->code }} — {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <div class="card overflow-hidden">
                @if ($topSharers->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100" data-testid="leaderboard-empty">No top sharers yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Be the first to earn karma by sharing resources!</p>
                    </div>
                @else
                    {{-- Header --}}
                    <div class="px-6 py-4 bg-gradient-to-r from-seait-50 to-transparent dark:from-seait-900/20 dark:to-transparent border-b border-gray-100 dark:border-navy-700/50">
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Rankings show total karma earned. Your
                            <span class="font-semibold text-gray-700 dark:text-gray-300">Badge Tier</span>
                            (e.g. Scholar, Luminary) is earned separately based on karma milestones — it does not reset.
                        </p>
                    </div>

                    <ol class="divide-y divide-gray-100 dark:divide-navy-700/50" data-testid="leaderboard-list">
                        @foreach ($topSharers as $index => $sharer)
                            @php
                                $isMe = auth()->id() === $sharer->id;
                                $badge = \App\Domain\Reputation\Enums\BadgeTier::fromKarma($sharer->karma ?? 0);
                                $rankColors = [
                                    0 => 'bg-amber-400 text-white',
                                    1 => 'bg-gray-400 text-white',
                                    2 => 'bg-amber-700 text-white',
                                ];
                                $rankColor = $rankColors[$index] ?? 'bg-gray-100 text-gray-500 dark:bg-navy-700 dark:text-gray-400';
                            @endphp
                            <li data-testid="leaderboard-item"
                                class="flex items-center gap-4 px-5 py-4 {{ $isMe ? 'bg-seait-50/60 dark:bg-seait-900/10' : 'hover:bg-gray-50/80 dark:hover:bg-navy-700/30' }} transition-colors">

                                {{-- Rank badge --}}
                                <div class="w-9 h-9 rounded-xl {{ $rankColor }} flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm">
                                    {{ $index + 1 }}
                                </div>

                                {{-- Avatar --}}
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-seait-400 to-seait-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0 shadow-sm">
                                    {{ strtoupper(substr($sharer->display_name ?: $sharer->name, 0, 2)) }}
                                </div>

                                {{-- Name + badge tier --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                            {{ $sharer->display_name ?: $sharer->name }}
                                        </p>
                                        @if ($isMe)
                                            <span class="text-[10px] font-medium text-seait-500">(you)</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        {{-- Actual badge tier — different from leaderboard rank --}}
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold px-1.5 py-0.5 rounded-md
                                            {{ in_array($badge->rarity()->value, ['legendary', 'rare']) ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300' : 'bg-gray-100 text-gray-600 dark:bg-navy-700 dark:text-gray-400' }}">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                {!! $badge->icon() !!}
                                            </svg>
                                            {{ $badge->label() }}
                                        </span>
                                        @if ($sharer->year_level)
                                            <span class="text-[10px] text-gray-400 dark:text-gray-500">Year {{ $sharer->year_level }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Karma score --}}
                                <div class="text-right flex-shrink-0">
                                    <p class="text-base font-bold text-gray-900 dark:text-gray-100" data-testid="leaderboard-karma">
                                        {{ number_format($sharer->karma) }}
                                    </p>
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500">karma</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>

            {{-- What is karma? --}}
            {{-- Info icon + modal --}}
            <div x-data="{ showInfo: false }">
                <div class="flex justify-end -mt-2 mb-2">
                    <button @click="showInfo = true"
                            class="inline-flex items-center gap-1.5 text-xs text-gray-400 hover:text-blue-500 dark:hover:text-blue-400 transition-colors">
                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full border border-current text-current text-[10px] font-semibold">?</span>
                        How ranking works
                    </button>
                </div>

                {{-- Modal --}}
                <div x-show="showInfo"
                     x-cloak
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-navy-950/50 backdrop-blur-sm" @click="showInfo = false"></div>
                    <div x-transition:enter="ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                         class="relative w-full max-w-md bg-white dark:bg-navy-800 rounded-2xl shadow-card-lg border border-gray-100 dark:border-navy-700/50 overflow-hidden p-6">
                        <button @click="showInfo = false" class="absolute top-3 right-3 p-1.5 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition rounded-lg hover:bg-gray-100 dark:hover:bg-navy-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 rounded-xl bg-seait-100 dark:bg-seait-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">How rankings and badges work</h3>
                        </div>
                        <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <p>
                                <strong class="text-gray-800 dark:text-gray-200">Leaderboard rank</strong> (the numbered position) shows who has earned the most karma in your program.
                                It changes as others earn or spend karma.
                            </p>
                            <p>
                                <strong class="text-gray-800 dark:text-gray-200">Badge Tiers</strong> (Seedling → StudHub Legend) are permanent milestones you unlock by reaching karma thresholds — they never go down.
                            </p>
                            <p>Earn karma by:</p>
                            <ul class="list-disc list-inside space-y-1">
                                <li>Uploading resources (+5)</li>
                                <li>Your resource gets saved by someone (+5)</li>
                                <li>Fulfilling a request (+10)</li>
                                <li>Your chat message marked helpful (+2)</li>
                            </ul>
                        </div>
                        <button @click="showInfo = false" class="btn-primary w-full mt-5 !py-2">Got it</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
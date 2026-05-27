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
            <div class="card p-5 border-l-4 border-seait-400 bg-seait-50/30 dark:bg-seait-900/10">
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-1">How rankings and badges work</p>
                <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                    <strong class="text-gray-800 dark:text-gray-200">Leaderboard rank</strong> (the numbered position) shows who has earned the most karma in your program.
                    It changes as others earn or spend karma.<br>
                    <strong class="text-gray-800 dark:text-gray-200">Badge Tiers</strong> (Seedling → StudHub Legend) are permanent milestones you unlock by reaching karma thresholds — they never go down.
                    Earn karma by uploading resources, fulfilling requests, and lending materials.
                </p>
            </div>

        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-page-header title="{{ __('Top Sharers') }}" />

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="card p-6">
                <form method="GET" action="{{ route('leaderboard') }}" class="mb-6">
                    <label for="program_id" class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1 block">Program</label>
                    <select id="program_id" name="program_id"
                            class="w-full input-field text-sm"
                            onchange="this.form.submit()">
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}" @selected($selectedProgramId == $program->id)>
                                {{ $program->code }} — {{ $program->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                @if ($topSharers->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100" data-testid="leaderboard-empty">No top sharers yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Be the first to earn karma by sharing resources!</p>
                    </div>
                @else
                    <ol class="divide-y divide-gray-100 dark:divide-navy-700" data-testid="leaderboard-list">
                        @foreach ($topSharers as $index => $sharer)
                            <li class="py-3 flex items-center justify-between" data-testid="leaderboard-item">
                                <div class="flex items-center gap-3">
                                    <span class="text-lg font-bold text-gray-400 dark:text-gray-500 w-8 text-right">
                                        #{{ $index + 1 }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $sharer->display_name ?: $sharer->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            @if ($sharer->year_level)
                                                Year {{ $sharer->year_level }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100" data-testid="leaderboard-karma">
                                        {{ $sharer->karma }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 block">karma</span>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
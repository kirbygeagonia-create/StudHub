<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Top Sharers') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('leaderboard') }}" class="mb-6">
                    <label for="program_id" class="block text-xs font-medium text-gray-700 mb-1">Program</label>
                    <select id="program_id" name="program_id"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            onchange="this.form.submit()">
                        @foreach ($programs as $program)
                            <option value="{{ $program->id }}" @selected($selectedProgramId == $program->id)>
                                {{ $program->code }} — {{ $program->name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                @if ($topSharers->isEmpty())
                    <p class="text-sm text-gray-500" data-testid="leaderboard-empty">No top sharers yet.</p>
                @else
                    <ol class="divide-y divide-gray-100" data-testid="leaderboard-list">
                        @foreach ($topSharers as $index => $sharer)
                            <li class="py-3 flex items-center justify-between" data-testid="leaderboard-item">
                                <div class="flex items-center gap-3">
                                    <span class="text-lg font-bold text-gray-400 w-8 text-right">
                                        #{{ $index + 1 }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ $sharer->display_name ?: $sharer->name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            @if ($sharer->year_level)
                                                Year {{ $sharer->year_level }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-sm font-semibold text-gray-900" data-testid="leaderboard-karma">
                                        {{ $sharer->karma }}
                                    </span>
                                    <span class="text-xs text-gray-500 block">karma</span>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100">
                    Moderation Dashboard
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ auth()->user()->program?->code }}
                    — {{ auth()->user()->program?->name }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Stat Row --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="stat-card text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium
                               uppercase tracking-wide">
                        Open Reports
                    </p>
                    <p class="text-3xl font-bold mt-1
                       {{ $reports->total() > 0
                          ? 'text-amber-600 dark:text-amber-400'
                          : 'text-gray-900 dark:text-gray-100' }}">
                        {{ $reports->total() }}
                    </p>
                </div>
                <div class="stat-card text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium
                               uppercase tracking-wide">
                        Resolved Today
                    </p>
                    <p class="text-3xl font-bold text-emerald-600
                               dark:text-emerald-400 mt-1">
                        {{ $resolvedToday }}
                    </p>
                </div>
                <div class="stat-card text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium
                               uppercase tracking-wide">
                        Total Actioned
                    </p>
                    <p class="text-3xl font-bold text-gray-900
                               dark:text-gray-100 mt-1">
                        {{ $totalActioned }}
                    </p>
                </div>
            </div>

            {{-- Reports List --}}
            <div class="card">
                <div class="p-5 border-b border-gray-100 dark:border-navy-700/50
                            flex items-center justify-between">
                    <h3 class="section-title">Open Reports</h3>

                    {{-- Type filter tabs --}}
                    <div class="flex gap-1 bg-gray-100 dark:bg-navy-800 p-1 rounded-lg">
                        @foreach (['all', 'message', 'resource', 'user'] as $filter)
                            <a href="{{ route('moderation.dashboard',
                                            $filter !== 'all' ? ['type' => $filter] : []) }}"
                               class="text-xs px-3 py-1.5 rounded-md font-medium
                                      transition-colors
                                      {{ request('type', 'all') === $filter
                                         ? 'bg-white dark:bg-navy-700 text-gray-900
                                            dark:text-gray-100 shadow-sm'
                                         : 'text-gray-500 dark:text-gray-400
                                            hover:text-gray-700' }}">
                                {{ ucfirst($filter) }}
                            </a>
                        @endforeach
                    </div>
                </div>

                @if ($reports->isEmpty())
                    {{-- Empty state --}}
                    <div class="p-12 text-center">
                        <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-900/20
                                    rounded-full flex items-center justify-center
                                    mx-auto mb-4">
                            <svg class="w-7 h-7 text-emerald-500" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0
                                         0118 0z"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-900 dark:text-gray-100">
                            All clear!
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            No open reports right now.
                        </p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50 dark:divide-navy-700/30">
                        @foreach ($reports as $report)
                            <div class="p-5 hover:bg-gray-50/50
                                        dark:hover:bg-navy-800/30
                                        transition-colors duration-150">
                                <div class="flex items-start gap-4">

                                    {{-- Type icon --}}
                                    <div class="w-9 h-9 rounded-lg flex items-center
                                                justify-center flex-shrink-0
                                                bg-amber-50 dark:bg-amber-900/20">
                                        @if ($report->reported_type === 'message')
                                            <svg class="w-4 h-4 text-amber-500"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M8 12h.01M12 12h.01M16
                                                         12h.01M21 12c0 4.418-4.03
                                                         8-9 8a9.863 9.863 0
                                                         01-4.255-.949L3 20l1.395
                                                         -3.72C3.512 15.042 3
                                                         13.574 3 12c0-4.418 4.03
                                                         -8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                        @elseif ($report->reported_type === 'resource')
                                            <svg class="w-4 h-4 text-amber-500"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2
                                                         2 0 01-2-2V5a2 2 0 012
                                                         -2h5.586a1 1 0 01.707.293
                                                         l5.414 5.414a1 1 0
                                                         01.293.707V19a2 2 0
                                                         01-2 2z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-amber-500"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0
                                                         018 0zM12 14a7 7 0 00-7
                                                         7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        @endif
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2
                                                    flex-wrap">
                                            <span class="text-sm font-semibold
                                                         text-gray-900
                                                         dark:text-gray-100">
                                                {{ ucfirst($report->reported_type) }}
                                                reported
                                            </span>
                                            @if ($report->reason)
                                                <span class="badge badge-amber text-xs">
                                                    {{ str_replace('_', ' ',
                                                       $report->reason) }}
                                                </span>
                                            @endif
                                            <span class="text-xs text-gray-400
                                                         ml-auto flex-shrink-0">
                                                {{ $report->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500
                                                   dark:text-gray-400 mt-0.5">
                                            Reported by
                                            <span class="font-medium text-gray-700
                                                         dark:text-gray-300">
                                                {{ $report->reporter
                                                   ?->preferredDisplayName()
                                                   ?? 'Unknown' }}
                                            </span>
                                        </p>
                                        @if ($report->notes)
                                            <p class="text-xs text-gray-600
                                                       dark:text-gray-400
                                                       bg-gray-50 dark:bg-navy-800
                                                       rounded-lg p-2.5 mt-2
                                                       border border-gray-100
                                                       dark:border-navy-700">
                                                {{ $report->notes }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex gap-2 mt-3 ml-13">
                                    <form method="POST"
                                          action="{{ route('moderation.resolve',
                                                          $report) }}"
                                          class="inline-flex items-center gap-1.5">
                                        @csrf
                                        <input type="hidden"
                                               name="resolution"
                                               value="actioned">
                                        <input type="text"
                                               name="resolution_note"
                                               maxlength="1000"
                                               class="text-xs input-field !py-1 w-44"
                                               placeholder="Add a note (optional)">
                                        <button type="submit"
                                                class="btn-primary !text-xs
                                                       !px-3 !py-1.5">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Action
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('moderation.resolve',
                                                          $report) }}">
                                        @csrf
                                        <input type="hidden"
                                               name="resolution"
                                               value="dismissed">
                                        <button type="submit"
                                                class="btn-secondary !text-xs
                                                       !px-3 !py-1.5">
                                            Dismiss
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-5 py-3 border-t border-gray-100
                                dark:border-navy-700/50">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>

            {{-- Suspend User — FIXED: user search replaces raw User ID --}}
            <div class="card p-6">
                <h3 class="section-title mb-1">Suspend a User</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Search by name or email. Only students in your
                    program can be suspended.
                </p>

                <form method="POST" action="{{ route('moderation.suspend') }}"
                      class="space-y-4"
                      x-data="userSearch()">
                    @csrf

                    {{-- User search input --}}
                    <div class="relative">
                        <label class="label-text">Search Student</label>
                        <input type="text"
                               x-model="query"
                               @input.debounce.300ms="search"
                               @keydown.escape="results = []"
                               class="input-field"
                               placeholder="Type a name or email…"
                               autocomplete="off">

                        {{-- Dropdown --}}
                        <div x-show="results.length > 0"
                             x-cloak
                             class="absolute z-20 mt-1 w-full bg-white
                                    dark:bg-navy-800 rounded-xl shadow-lg
                                    border border-gray-100 dark:border-navy-700
                                    divide-y divide-gray-50
                                    dark:divide-navy-700/50
                                    max-h-48 overflow-y-auto">
                            <template x-for="u in results" :key="u.id">
                                <button type="button"
                                        @click="select(u)"
                                        class="w-full text-left px-4 py-2.5
                                               hover:bg-gray-50
                                               dark:hover:bg-navy-700
                                               transition-colors">
                                    <p class="text-sm font-medium text-gray-900
                                               dark:text-gray-100"
                                       x-text="u.display_name || u.name"></p>
                                    <p class="text-xs text-gray-500
                                               dark:text-gray-400"
                                       x-text="u.email"></p>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Hidden user_id --}}
                    <input type="hidden" name="user_id" x-model="selectedId">

                    {{-- Selected user chip --}}
                    <div x-show="selectedName" x-cloak
                         class="flex items-center gap-2 bg-emerald-50
                                dark:bg-emerald-900/20 border border-emerald-200
                                dark:border-emerald-800/50 rounded-lg
                                px-3 py-2 text-sm">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12
                                     14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="font-medium text-emerald-700
                                     dark:text-emerald-300"
                              x-text="selectedName"></span>
                        <button type="button" @click="clear"
                                class="ml-auto text-emerald-400
                                       hover:text-emerald-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="label-text">Duration</label>
                            <select name="days" class="input-field">
                                <option value="1">1 day</option>
                                <option value="3">3 days</option>
                                <option value="7" selected>7 days</option>
                                <option value="14">14 days</option>
                                <option value="30">30 days</option>
                                <option value="90">90 days</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="label-text">Reason (optional)</label>
                            <input type="text" name="reason" maxlength="500"
                                   class="input-field"
                                   placeholder="e.g. Spam, harassment">
                        </div>
                    </div>

                    <button type="submit"
                            class="btn-primary !bg-red-500 hover:!bg-red-600"
                            :disabled="!selectedId"
                            :class="!selectedId
                                    ? 'opacity-40 cursor-not-allowed' : ''">
                        Suspend User
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
function userSearch() {
    return {
        query:        '',
        results:      [],
        selectedId:   '',
        selectedName: '',

        async search() {
            if (this.query.length < 2) { this.results = []; return; }
            const res = await fetch(
                `/moderation/users/search?q=${encodeURIComponent(this.query)}`,
                { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
            );
            this.results = await res.json();
        },

        select(user) {
            this.selectedId   = user.id;
            this.selectedName = user.display_name || user.name;
            this.results      = [];
            this.query        = '';
        },

        clear() {
            this.selectedId   = '';
            this.selectedName = '';
        }
    };
}
</script>
@endpush
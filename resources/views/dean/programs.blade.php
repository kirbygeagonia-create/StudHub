@extends('layouts.admin')

@section('sidebar')
    @include('dean._sidebar')
@endsection

@section('pageHeader')
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                Programs & Program Heads
            </h1>
        </div>
    </div>
@endsection

@section('content')
    <div class="card p-6 mb-6">
        <h3 class="section-title mb-4">Programs</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-navy-700/50">
                        <th class="pb-2 font-medium">Code</th>
                        <th class="pb-2 font-medium">Name</th>
                        <th class="pb-2 font-medium">Students</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($programs as $program)
                        <tr class="border-b border-gray-50 dark:border-navy-700/30">
                            <td class="py-2.5 font-medium">{{ $program->code }}</td>
                            <td class="py-2.5">{{ $program->name }}</td>
                            <td class="py-2.5">{{ $program->student_count ?? 0 }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-4 text-center text-gray-400">No programs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card p-6">
        <h3 class="section-title mb-4">Assign Program Head</h3>
        <form method="POST" action="{{ route('dean.program_heads.assign') }}" class="space-y-4" x-data="userSearch()">
            @csrf

            {{-- User search input --}}
            <div class="relative">
                <label class="label-text">Search User</label>
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
                 class="flex items-center gap-2 bg-indigo-50
                        dark:bg-indigo-900/20 border border-indigo-200
                        dark:border-indigo-800/50 rounded-lg
                        px-3 py-2 text-sm">
                <svg class="w-4 h-4 text-indigo-500 flex-shrink-0"
                     fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12
                             14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="font-medium text-indigo-700
                             dark:text-indigo-300"
                      x-text="selectedName"></span>
                <button type="button" @click="clear"
                        class="ml-auto text-indigo-400
                               hover:text-indigo-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Program selection --}}
            <div>
                <label class="label-text">Program</label>
                <select name="program_id" class="input-field" required>
                    <option value="">Select a program</option>
                    @foreach ($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->code }} - {{ $program->name }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit"
                    class="btn-primary"
                    :disabled="!selectedId"
                    :class="!selectedId
                            ? 'opacity-40 cursor-not-allowed' : ''">
                Assign Program Head
            </button>
        </form>
        @if ($programHeads->isNotEmpty())
            <div class="mt-6">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Current Program Heads</h4>
                <div class="space-y-2">
                    @foreach ($programHeads as $ph)
                        <div class="flex items-center justify-between bg-gray-50 dark:bg-navy-800/60 rounded-lg px-4 py-2">
                            <span class="text-sm">{{ $ph->preferredDisplayName() }}</span>
                            <span class="text-xs text-gray-500">{{ $ph->college?->code ?? 'N/A' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection

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
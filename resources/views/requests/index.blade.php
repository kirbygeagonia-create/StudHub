<x-app-layout>
    <x-page-header title="{{ __('Request Board') }}">
        <x-slot name="actions">
            <a href="{{ route('requests.create') }}" class="btn-primary text-xs flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New request
            </a>
        </x-slot>
    </x-page-header>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Filters --}}
            <div class="card p-4">
                <form method="GET" action="{{ route('requests.index') }}" class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
                    <div class="col-span-2 md:col-span-1">
                        <label for="subject_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                        <select id="subject_id" name="subject_id" class="w-full input-field text-sm">
                            <option value="">All subjects</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(($filters['subject_id'] ?? '') == $subject->id)>{{ $subject->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="type_wanted" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Type wanted</label>
                        <select id="type_wanted" name="type_wanted" class="w-full input-field text-sm">
                            <option value="">All types</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected(($filters['type_wanted'] ?? '') == $type->value)>{{ $type->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="urgency" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Urgency</label>
                        <select id="urgency" name="urgency" class="w-full input-field text-sm">
                            <option value="">All</option>
                            @foreach ($urgencies as $urgency)
                                <option value="{{ $urgency->value }}" @selected(($filters['urgency'] ?? '') == $urgency->value)>{{ $urgency->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2 md:col-span-1 flex gap-2 items-end">
                        <button type="submit" class="flex-1 btn-secondary text-xs">Filter</button>
                        <a href="{{ route('requests.index') }}" class="flex-1 btn-ghost text-xs text-center">Clear</a>
                    </div>
                </form>
            </div>

            {{-- List --}}
            <div class="space-y-3">
                @if ($requests->isEmpty())
                    <div class="card p-12 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 10 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">No open requests</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Need a reviewer or textbook? Post a request — it'll be routed to programs that teach the same subject.</p>
                        <a href="{{ route('requests.create') }}" class="mt-4 inline-block btn-primary text-xs">Post a request</a>
                    </div>
                @else
                    <div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 300)">
                        {{-- Skeletons --}}
                        <div x-show="!loaded" class="space-y-3">
                            @for ($i = 0; $i < 4; $i++)
                                <div class="card p-5 flex items-start gap-4">
                                    <div class="skeleton w-10 h-10 rounded-xl flex-shrink-0"></div>
                                    <div class="flex-1 space-y-2">
                                        <div class="skeleton h-4 w-2/3"></div>
                                        <div class="skeleton h-3 w-1/2"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        {{-- Actual cards --}}
                        <div x-show="loaded" class="space-y-3" data-testid="requests-list">
                            @foreach ($requests as $request)
                                @php
                                    $urgencyStyle = match ($request->urgency?->value) {
                                        'urgent' => 'border-l-red-400 bg-red-50/30 dark:bg-red-900/5',
                                        'low'    => 'border-l-gray-300 dark:border-l-navy-600',
                                        default  => 'border-l-amber-400',
                                    };
                                    $badgeStyle = match ($request->urgency?->value) {
                                        'urgent' => 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300',
                                        'low'    => 'bg-gray-100 text-gray-600 dark:bg-navy-700 dark:text-gray-400',
                                        default  => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300',
                                    };
                                @endphp
                                <a href="{{ route('requests.show', $request) }}"
                                   class="card card-hover p-5 flex items-start gap-4 border-l-4 {{ $urgencyStyle }} block"
                                   data-testid="request-item">
                                    {{-- Icon --}}
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/30 dark:to-blue-800/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-3">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                                {{ $request->subject->code }} — {{ $request->type_wanted }}
                                            </p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium flex-shrink-0 {{ $badgeStyle }}">
                                                {{ $request->urgency?->label() ?: 'Normal' }}
                                            </span>
                                        </div>
                                        @if ($request->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $request->description }}</p>
                                        @endif
                                        <div class="flex items-center gap-3 mt-2 text-[11px] text-gray-400 dark:text-gray-500">
                                            <span>by {{ $request->requester?->display_name ?: $request->requester?->name ?: 'Unknown' }}</span>
                                            @if ($request->needed_by)
                                                <span>· Needed by {{ $request->needed_by->format('M d') }}</span>
                                            @endif
                                            <span>· {{ $request->created_at->diffForHumans() }}</span>
                                            <span>· {{ $request->offers->count() }} {{ Str::plural('offer', $request->offers->count()) }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-4">{{ $requests->links() }}</div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
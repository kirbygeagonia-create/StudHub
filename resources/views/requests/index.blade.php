<x-app-layout>
    <x-page-header title="{{ __('Request Board') }}">
        <x-slot name="actions">
            <a href="{{ route('requests.create') }}" class="btn-primary text-xs">+ New request</a>
        </x-slot>
    </x-page-header>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="card p-6">
                <form method="GET" action="{{ route('requests.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    <div>
                        <label for="subject_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                        <select id="subject_id" name="subject_id"
                                class="w-full input-field text-sm">
                            <option value="">All subjects</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(($filters['subject_id'] ?? '') == $subject->id)>
                                    {{ $subject->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="type_wanted" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Type wanted</label>
                        <select id="type_wanted" name="type_wanted"
                                class="w-full input-field text-sm">
                            <option value="">All types</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected(($filters['type_wanted'] ?? '') == $type->value)>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="urgency" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Urgency</label>
                        <select id="urgency" name="urgency"
                                class="w-full input-field text-sm">
                            <option value="">All</option>
                            @foreach ($urgencies as $urgency)
                                <option value="{{ $urgency->value }}" @selected(($filters['urgency'] ?? '') == $urgency->value)>
                                    {{ $urgency->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div></div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                                class="w-full btn-primary text-xs !bg-gray-700 hover:!bg-gray-800">
                            Filter
                        </button>
                        <a href="{{ route('requests.index') }}"
                           class="w-full btn-secondary text-xs">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($requests->isEmpty())
                        <div class="text-center py-16">
                            <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No open requests</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Need a reviewer or textbook? Your request will be auto-routed to programs that teach the same subject.</p>
                            <a href="{{ route('requests.create') }}"
                               class="mt-4 btn-primary text-xs">
                                Post a request
                            </a>
                        </div>
                    @else
                        <div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 300)">
                        <div x-show="!loaded" class="divide-y divide-gray-100 dark:divide-navy-700">
                            @for ($i = 0; $i < 5; $i++)
                                <div class="py-4 flex items-start justify-between gap-4">
                                    <div class="min-w-0 flex-1 space-y-2">
                                        <div class="skeleton h-4 w-3/4"></div>
                                        <div class="skeleton h-3 w-1/2"></div>
                                        <div class="skeleton h-3 w-1/3"></div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                        <ul x-show="loaded" class="divide-y divide-gray-100 dark:divide-navy-700" data-testid="requests-list">
                            @foreach ($requests as $request)
                                <li class="py-4 flex items-start justify-between gap-4" data-testid="request-item">
                                    <div class="min-w-0">
                                        <a href="{{ route('requests.show', $request) }}"
                                           class="font-medium text-seait-500 hover:text-seait-800 truncate block">
                                            {{ $request->subject->code }} — {{ $request->type_wanted }}
                                        </a>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $request->requester?->display_name ?: $request->requester?->name ?: 'Unknown' }}
                                            · {{ ucfirst($request->urgency?->value ?? 'normal') }}
                                            @if ($request->needed_by)
                                                · Needed by {{ $request->needed_by->format('M d, Y') }}
                                            @endif
                                            · {{ $request->created_at->diffForHumans() }}
                                        </p>
                                        @if ($request->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">{{ $request->description }}</p>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $request->urgency?->value === 'urgent' ? 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300' : ($request->urgency?->value === 'low' ? 'bg-gray-100 text-gray-700 dark:bg-navy-700 dark:text-gray-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300') }}">
                                        {{ $request->urgency?->label() ?: 'Normal' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        </div>
                        <div class="mt-4">
                            {{ $requests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
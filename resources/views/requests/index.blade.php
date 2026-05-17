<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Request Board') }}
            </h2>
            <a href="{{ route('requests.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                + New request
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('requests.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    <div>
                        <label for="subject_id" class="block text-xs font-medium text-gray-700 mb-1">Subject</label>
                        <select id="subject_id" name="subject_id"
                                class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All subjects</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(($filters['subject_id'] ?? '') == $subject->id)>
                                    {{ $subject->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="type_wanted" class="block text-xs font-medium text-gray-700 mb-1">Type wanted</label>
                        <select id="type_wanted" name="type_wanted"
                                class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All types</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected(($filters['type_wanted'] ?? '') == $type->value)>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="urgency" class="block text-xs font-medium text-gray-700 mb-1">Urgency</label>
                        <select id="urgency" name="urgency"
                                class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
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
                                class="w-full px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Filter
                        </button>
                        <a href="{{ route('requests.index') }}"
                           class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest text-center shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($requests->isEmpty())
                        <p class="text-sm text-gray-500" data-testid="requests-empty">No open requests found.</p>
                    @else
                        <ul class="divide-y divide-gray-100" data-testid="requests-list">
                            @foreach ($requests as $request)
                                <li class="py-4 flex items-start justify-between gap-4" data-testid="request-item">
                                    <div class="min-w-0">
                                        <a href="{{ route('requests.show', $request) }}"
                                           class="font-medium text-indigo-600 hover:text-indigo-900 truncate block">
                                            {{ $request->subject->code }} — {{ $request->type_wanted }}
                                        </a>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $request->requester?->display_name ?: $request->requester?->name ?: 'Unknown' }}
                                            · {{ ucfirst($request->urgency?->value ?? 'normal') }}
                                            @if ($request->needed_by)
                                                · Needed by {{ $request->needed_by->format('M d, Y') }}
                                            @endif
                                            · {{ $request->created_at->diffForHumans() }}
                                        </p>
                                        @if ($request->description)
                                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ $request->description }}</p>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $request->urgency?->value === 'urgent' ? 'bg-red-100 text-red-800' : ($request->urgency?->value === 'low' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $request->urgency?->label() ?: 'Normal' }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4">
                            {{ $requests->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Resources') }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('resources.shelf') }}"
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-xs font-semibold uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    My shelf
                </a>
                <a href="{{ route('resources.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    + Post resource
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('resources.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                    <div>
                        <label for="q" class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" id="q" name="q" value="{{ $filters['q'] ?? '' }}"
                               class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Title, description…" />
                    </div>
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
                        <label for="type" class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                        <select id="type" name="type"
                                class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All types</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected(($filters['type'] ?? '') == $type->value)>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="program_id" class="block text-xs font-medium text-gray-700 mb-1">Program</label>
                        <select id="program_id" name="program_id"
                                class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All programs</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}" @selected(($filters['program_id'] ?? '') == $program->id)>
                                    {{ $program->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="year_level" class="block text-xs font-medium text-gray-700 mb-1">Year</label>
                        <select id="year_level" name="year_level"
                                class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All years</option>
                            @for ($y = 1; $y <= 5; $y++)
                                <option value="{{ $y }}" @selected(($filters['year_level'] ?? '') == $y)>
                                    Year {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="submit"
                                class="w-full px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Filter
                        </button>
                        <a href="{{ route('resources.index') }}"
                           class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest text-center shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($resources->isEmpty())
                        <p class="text-sm text-gray-500" data-testid="resources-empty">No resources found.</p>
                    @else
                        <ul class="divide-y divide-gray-100" data-testid="resources-list">
                            @foreach ($resources as $resource)
                                <li class="py-4 flex items-start justify-between gap-4" data-testid="resource-item">
                                    <div class="min-w-0">
                                        <a href="{{ route('resources.show', $resource) }}"
                                           class="font-medium text-indigo-600 hover:text-indigo-900 truncate block">
                                            {{ $resource->title }}
                                        </a>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $resource->subject->code }} · {{ $resource->type->label() }}
                                            @if ($resource->program)
                                                · {{ $resource->program->code }}
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            {{ $resource->owner?->display_name ?: $resource->owner?->name ?: 'Unknown' }}
                                            · {{ $resource->save_count }} saves
                                            · {{ $resource->published_at?->diffForHumans() }}
                                        </p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4">
                            {{ $resources->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
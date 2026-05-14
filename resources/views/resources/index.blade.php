<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Resources') }}
            </h2>
            <a href="{{ route('resources.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                + Post resource
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('resources.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
                        <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                               placeholder="title or description"
                               class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" />
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Subject</label>
                        <select name="subject_id" class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All</option>
                            @foreach ($subjects as $subject)
                                <option value="{{ $subject->id }}" @selected(($filters['subject_id'] ?? null) == $subject->id)>
                                    {{ $subject->code }} — {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Type</label>
                        <select name="type" class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected(($filters['type'] ?? null) === $type->value)>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Program</label>
                        <select name="program_id" class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}" @selected(($filters['program_id'] ?? null) == $program->id)>
                                    {{ $program->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Year level</label>
                        <select name="year_level" class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All</option>
                            @for ($y = 1; $y <= 5; $y++)
                                <option value="{{ $y }}" @selected(($filters['year_level'] ?? null) == $y)>Year {{ $y }}</option>
                            @endfor
                        </select>
                    </div>

                    <div class="md:col-span-6 flex justify-end gap-2">
                        <a href="{{ route('resources.index') }}" class="text-sm text-gray-500 hover:text-gray-700 px-3 py-2">Clear</a>
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($resources->isEmpty())
                        <p class="text-sm text-gray-500" data-testid="resources-empty">
                            No resources match your filters yet. Try clearing them or
                            <a href="{{ route('resources.create') }}" class="text-indigo-600 hover:text-indigo-700">post one</a>.
                        </p>
                    @else
                        <ul class="divide-y divide-gray-100" data-testid="resources-list">
                            @foreach ($resources as $resource)
                                <li class="py-4 flex items-start justify-between gap-4" data-testid="resource-item">
                                    <div class="space-y-1">
                                        <a href="{{ route('resources.show', $resource) }}" class="text-base font-semibold text-indigo-700 hover:text-indigo-900">
                                            {{ $resource->title }}
                                        </a>
                                        <p class="text-xs text-gray-500">
                                            {{ $resource->type->label() }}
                                            · {{ $resource->subject->code }} {{ $resource->subject->name }}
                                            @if ($resource->program)
                                                · {{ $resource->program->code }}
                                            @endif
                                            @if ($resource->year_level)
                                                · Y{{ $resource->year_level }}
                                            @endif
                                        </p>
                                        @if ($resource->description)
                                            <p class="text-sm text-gray-700 line-clamp-2 max-w-2xl">
                                                {{ \Illuminate\Support\Str::limit($resource->description, 180) }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs text-gray-500">
                                            by {{ $resource->owner?->display_name ?: $resource->owner?->name ?: 'Unknown' }}
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ $resource->published_at?->diffForHumans() ?? $resource->created_at->diffForHumans() }}
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

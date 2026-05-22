<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Resources') }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('resources.shelf') }}"
                   class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-xs font-semibold uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-seait-400 focus:ring-offset-2">
                    My shelf
                </a>
                <a href="{{ route('resources.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-seait-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-seait-600">
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
                               class="w-full border-gray-300 rounded-md text-sm focus:ring-seait-400 focus:border-seait-400"
                               placeholder="Title, description…" />
                    </div>
                    <div>
                        <label for="subject_id" class="block text-xs font-medium text-gray-700 mb-1">Subject</label>
                        <select id="subject_id" name="subject_id"
                                class="w-full border-gray-300 rounded-md text-sm focus:ring-seait-400 focus:border-seait-400">
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
                                class="w-full border-gray-300 rounded-md text-sm focus:ring-seait-400 focus:border-seait-400">
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
                                class="w-full border-gray-300 rounded-md text-sm focus:ring-seait-400 focus:border-seait-400">
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
                                class="w-full border-gray-300 rounded-md text-sm focus:ring-seait-400 focus:border-seait-400">
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
                                class="w-full px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-seait-400 focus:ring-offset-2">
                            Filter
                        </button>
                        <a href="{{ route('resources.index') }}"
                           class="w-full px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest text-center shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-seait-400 focus:ring-offset-2">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($resources->isEmpty())
                        <div class="text-center py-16">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No resources found</h3>
                            <p class="mt-1 text-sm text-gray-500">Be the first to share a reviewer, e-module, or textbook for your subject.</p>
                            <a href="{{ route('resources.create') }}"
                               class="mt-4 inline-flex items-center px-4 py-2 bg-seait-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-seait-600">
                                Upload a resource
                            </a>
                        </div>
                    @else
                        <ul class="divide-y divide-gray-100" data-testid="resources-list">
                            @foreach ($resources as $resource)
                                <li class="py-4 flex items-start justify-between gap-4" data-testid="resource-item">
                                    <div class="min-w-0">
                                        <a href="{{ route('resources.show', $resource) }}"
                                           class="font-medium text-seait-500 hover:text-seait-800 truncate block">
                                            {{ $resource->title }}
                                        </a>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $resource->subject->code }} · {{ $resource->type->label() }}
                                            @if ($resource->program)
                                                · <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-seait-50 text-seait-600">{{ $resource->program->code }}</span>
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
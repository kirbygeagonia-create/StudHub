<x-app-layout>
    <x-page-header title="{{ __('Resources') }}">
        <x-slot name="actions">
            <a href="{{ route('resources.shelf') }}" class="btn-secondary text-xs">My shelf</a>
            <a href="{{ route('resources.create') }}" class="btn-primary text-xs">+ Post resource</a>
        </x-slot>
    </x-page-header>

    <div class="py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="card p-6">
                <form method="GET" action="{{ route('resources.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                    <div>
                        <label for="q" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                        <input type="text" id="q" name="q" value="{{ $filters['q'] ?? '' }}"
                               class="w-full input-field text-sm"
                               placeholder="Title, description…" />
                    </div>
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
                        <label for="type" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                        <select id="type" name="type"
                                class="w-full input-field text-sm">
                            <option value="">All types</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected(($filters['type'] ?? '') == $type->value)>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="program_id" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Program</label>
                        <select id="program_id" name="program_id"
                                class="w-full input-field text-sm">
                            <option value="">All programs</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}" @selected(($filters['program_id'] ?? '') == $program->id)>
                                    {{ $program->code }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="year_level" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Year</label>
                        <select id="year_level" name="year_level"
                                class="w-full input-field text-sm">
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
                                class="w-full btn-primary text-xs !bg-gray-700 hover:!bg-gray-800">
                            Filter
                        </button>
                        <a href="{{ route('resources.index') }}"
                           class="w-full btn-secondary text-xs">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if ($resources->isEmpty())
                        <div class="text-center py-16">
                            <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No resources found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Be the first to share a reviewer, e-module, or textbook for your subject.</p>
                            <a href="{{ route('resources.create') }}"
                               class="mt-4 btn-primary text-xs">
                                Upload a resource
                            </a>
                        </div>
                    @else
                        <div x-data="{ loaded: false }" x-init="setTimeout(() => loaded = true, 300)">
                        <!-- Loading Skeleton -->
                        <div x-show="!loaded" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @for ($i = 0; $i < 5; $i++)
                                <div class="card p-5 animate-pulse">
                                    <div class="flex items-start gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-gray-200 dark:bg-navy-700 flex-shrink-0"></div>
                                        <div class="flex-1 space-y-2">
                                            <div class="skeleton h-4 w-3/4"></div>
                                            <div class="skeleton h-3 w-1/2"></div>
                                            <div class="skeleton h-3 w-1/3"></div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                        <!-- Resource Cards -->
                        <div x-show="loaded" class="grid grid-cols-1 md:grid-cols-2 gap-4" data-testid="resources-list">
                            @foreach ($resources as $resource)
                                @php
                                    $typeIcon = match($resource->type->value) {
                                        'reviewer' => '<svg class="w-6 h-6 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
                                        'textbook' => '<svg class="w-6 h-6 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                                        'e_module' => '<svg class="w-6 h-6 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
                                        'past_exam' => '<svg class="w-6 h-6 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>',
                                        'lab_manual' => '<svg class="w-6 h-6 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>',
                                        'thesis_sample' => '<svg class="w-6 h-6 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9Z"/></svg>',
                                        'lecture_notes' => '<svg class="w-6 h-6 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>',
                                        default => '<svg class="w-6 h-6 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9Z"/></svg>',
                                    };
                                @endphp
                                <div class="card card-hover p-5 flex items-start gap-4" data-testid="resource-item">
                                    <!-- Type Icon -->
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-seait-100 to-seait-200 dark:from-seait-900/30 dark:to-seait-800/20 flex items-center justify-center flex-shrink-0">
                                        {!! $typeIcon !!}
                                    </div>
                                    <!-- Content -->
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('resources.show', $resource) }}"
                                           class="font-semibold text-gray-900 dark:text-gray-100 hover:text-seait-600 dark:hover:text-seait-400 transition-colors block truncate">
                                            {{ $resource->title }}
                                        </a>
                                        <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-navy-700 dark:text-gray-300">
                                                {{ $resource->subject->code }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-seait-50 text-seait-700 dark:bg-seait-900/30 dark:text-seait-300">
                                                {{ $resource->type->label() }}
                                            </span>
                                            @if ($resource->program)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-navy-700 dark:text-gray-300">
                                                    {{ $resource->program->code }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                {{ $resource->owner?->display_name ?: $resource->owner?->name ?: 'Unknown' }}
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                                {{ $resource->save_count }} saves
                                            </span>
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                {{ $resource->published_at?->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        </div>
                        <div class="mt-6">
                            {{ $resources->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

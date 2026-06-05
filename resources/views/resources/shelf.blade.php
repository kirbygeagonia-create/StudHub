<x-app-layout>
    <x-page-header title="{{ __('My Shelf') }}">
        <x-slot name="actions">
            <a href="{{ route('resources.index') }}" class="btn-secondary text-xs">Browse resources</a>
        </x-slot>
    </x-page-header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-2">
        <a href="{{ route('resources.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-seait-500 dark:hover:text-seait-400 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Resources
        </a>
    </div>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($shelf && $resources->isNotEmpty())
                {{-- Search & Filter --}}
                <div class="card p-4 mb-4">
                    <form method="GET" action="{{ route('resources.shelf') }}" class="flex flex-wrap items-end gap-3">
                        <div class="flex-1 min-w-[200px]">
                            <label for="shelf-q" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                            <input id="shelf-q" type="text" name="q" value="{{ $filters['q'] ?? '' }}"
                                   placeholder="Search by title, description, or uploader…"
                                   class="input-field w-full text-sm">
                        </div>
                        <div class="w-40">
                            <label for="shelf-type" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                            <select id="shelf-type" name="type" class="input-field w-full text-sm">
                                <option value="">All types</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->value }}" @selected(($filters['type'] ?? '') === $type->value)>
                                        {{ $type->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn-secondary text-xs">Filter</button>
                        @if (($filters['q'] ?? '') !== '' || ($filters['type'] ?? '') !== '')
                            <a href="{{ route('resources.shelf') }}" class="btn-ghost text-xs">Clear</a>
                        @endif
                    </form>
                </div>

                <div class="space-y-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 px-1">{{ $resources->total() }} saved {{ Str::plural('resource', $resources->total()) }}</p>

                    @foreach ($resources as $resource)
                        @php
                            $typeIcon = match($resource->type->value) {
                                'reviewer' => '<svg class="w-5 h-5 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>',
                                'textbook' => '<svg class="w-5 h-5 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
                                'e_module' => '<svg class="w-5 h-5 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>',
                                'past_exam' => '<svg class="w-5 h-5 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>',
                                'lab_manual' => '<svg class="w-5 h-5 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>',
                                'thesis_sample' => '<svg class="w-5 h-5 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9Z"/></svg>',
                                'lecture_notes' => '<svg class="w-5 h-5 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>',
                                default => '<svg class="w-5 h-5 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9Z"/></svg>',
                            };
                        @endphp
                        <div class="card card-hover p-5 flex items-start gap-4">
                            {{-- Type icon --}}
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-seait-100 to-seait-200 dark:from-seait-900/30 dark:to-seait-800/20 flex items-center justify-center flex-shrink-0">
                                {!! $typeIcon !!}
                            </div>
                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('resources.show', $resource) }}"
                                   class="font-semibold text-gray-900 dark:text-gray-100 hover:text-seait-600 dark:hover:text-seait-400 transition-colors block truncate">
                                    {{ $resource->title }}
                                </a>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-navy-700 dark:text-gray-300">{{ $resource->subject->code }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-seait-50 text-seait-700 dark:bg-seait-900/30 dark:text-seait-300">{{ $resource->type->label() }}</span>
                                    @if ($resource->program)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 dark:bg-navy-700 dark:text-gray-300">{{ $resource->program->code }}</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Saved {{ $resource->pivot->created_at?->diffForHumans() }}</p>
                            </div>
                            {{-- Remove button --}}
                            <form method="POST" action="{{ route('resources.toggle-save', $resource) }}" class="flex-shrink-0">
                                @csrf
                                <button type="submit"
                                        class="p-2 rounded-xl text-gray-300 hover:text-red-500 hover:bg-red-50 dark:text-gray-600 dark:hover:text-red-400 dark:hover:bg-red-900/10 transition-colors"
                                        title="Remove from shelf" aria-label="Remove from shelf">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                                </button>
                            </form>
                        </div>
                    @endforeach

                    <div class="mt-4">{{ $resources->links() }}</div>
                </div>
            @else
                <div class="card p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Your shelf is empty</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Save resources to build your personal study collection.</p>
                    <a href="{{ route('resources.index') }}" class="mt-5 inline-block btn-primary text-xs">Browse resources</a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
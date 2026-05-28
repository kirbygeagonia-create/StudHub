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
                        <div class="card card-hover p-5 flex items-start gap-4">
                            {{-- Type icon --}}
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-seait-100 to-seait-200 dark:from-seait-900/30 dark:to-seait-800/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
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
                                        title="Remove from shelf">
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
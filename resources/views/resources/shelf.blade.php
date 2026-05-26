<x-app-layout>
    <x-page-header title="{{ __('My Shelf') }}">
        <x-slot name="actions">
            <a href="{{ route('resources.index') }}" class="btn-primary text-xs">Browse resources</a>
        </x-slot>
    </x-page-header>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            @if ($shelf && $resources->isNotEmpty())
                <div class="card">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">{{ $shelf->name }}</h3>
                        <ul class="divide-y divide-gray-100 dark:divide-navy-700">
                            @foreach ($resources as $resource)
                                <li class="py-4 flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <a href="{{ route('resources.show', $resource) }}" class="font-medium text-seait-500 hover:text-seait-800 truncate block">
                                            {{ $resource->title }}
                                        </a>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            {{ $resource->subject->code }} · {{ $resource->type->label() }}
                                            @if ($resource->program)
                                                · {{ $resource->program->code }}
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                            Saved {{ $resource->pivot->created_at?->diffForHumans() }}
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('resources.toggle-save', $resource) }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 hover:underline">
                                            Remove
                                        </button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4">
                            {{ $resources->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="card p-6 text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">Your shelf is empty</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Save resources to build your personal collection.</p>
                    <a href="{{ route('resources.index') }}" class="mt-4 btn-primary text-xs">Browse resources</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
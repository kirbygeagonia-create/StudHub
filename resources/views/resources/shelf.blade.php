<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Shelf') }}
            </h2>
            <a href="{{ route('resources.index') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Browse resources
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($shelf && $resources->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">{{ $shelf->name }}</h3>
                        <ul class="divide-y divide-gray-100">
                            @foreach ($resources as $resource)
                                <li class="py-4 flex items-start justify-between gap-4">
                                    <div class="min-w-0">
                                        <a href="{{ route('resources.show', $resource) }}" class="font-medium text-indigo-600 hover:text-indigo-900 truncate block">
                                            {{ $resource->title }}
                                        </a>
                                        <p class="text-xs text-gray-500 mt-0.5">
                                            {{ $resource->subject->code }} · {{ $resource->type->label() }}
                                            @if ($resource->program)
                                                · {{ $resource->program->code }}
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-400 mt-0.5">
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
                <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-sm text-gray-500">Your shelf is empty.</p>
                    <a href="{{ route('resources.index') }}" class="mt-2 inline-block text-sm text-indigo-600 hover:underline">Browse resources to save some →</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
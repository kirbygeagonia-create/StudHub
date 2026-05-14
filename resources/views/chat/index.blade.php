<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    @if ($rooms->isEmpty())
                        <p class="text-sm text-gray-500">
                            No chat rooms yet. Your program's rooms are created during seeding —
                            run <code class="text-gray-700">php artisan db:seed</code> and refresh.
                        </p>
                    @else
                        <p class="text-sm text-gray-500">
                            Rooms scoped to your program{{ auth()->user()->program?->code ? ' (' . auth()->user()->program->code . ')' : '' }}.
                        </p>
                        <ul class="divide-y divide-gray-100 border border-gray-100 rounded-lg">
                            @foreach ($rooms as $room)
                                <li class="px-4 py-3 flex items-center justify-between">
                                    <div>
                                        <a href="{{ route('chat.show', $room) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                                            #{{ $room->slug }}
                                        </a>
                                        <p class="text-xs text-gray-500">{{ $room->title }}</p>
                                    </div>
                                    <span class="text-xs uppercase tracking-wide text-gray-400">{{ $room->kind->label() }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

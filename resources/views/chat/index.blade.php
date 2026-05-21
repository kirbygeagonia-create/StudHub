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
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No chat rooms yet</h3>
                            <p class="mt-1 text-sm text-gray-500">Chat rooms are automatically created for your program.</p>
                        </div>
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

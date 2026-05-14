<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                #{{ $room->slug }} <span class="text-gray-400 text-sm">— {{ $room->title }}</span>
            </h2>
            <a href="{{ route('chat.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">All rooms</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <livewire:chat.room-conversation :room="$room" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

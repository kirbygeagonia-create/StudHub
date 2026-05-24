<x-app-layout>
    <x-page-header title="#{{ $room->slug }}">
        <x-slot name="actions">
            <a href="{{ route('chat.index') }}" class="text-sm text-seait-500 hover:text-seait-800">All rooms</a>
        </x-slot>
    </x-page-header>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <livewire:chat.room-conversation :room="$room" />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@props(['title'])

<x-slot name="header">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight dark:text-gray-100">
            {{ $title }}
        </h2>
        @if (isset($actions))
            <div class="flex items-center gap-3">
                {{ $actions }}
            </div>
        @endif
    </div>
</x-slot>
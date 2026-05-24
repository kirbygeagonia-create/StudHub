@props(['icon' => 'document', 'title' => 'Nothing here yet', 'description' => null, 'actionLabel' => null, 'actionUrl' => null])

<div class="text-center py-16">
    <x-icon :name="$icon" class="mx-auto h-12 w-12 text-gray-300 dark:text-gray-600" />
    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $description }}</p>
    @endif
    @if ($actionLabel && $actionUrl)
        <a href="{{ $actionUrl }}" class="mt-4 btn-primary text-xs">{{ $actionLabel }}</a>
    @endif
</div>
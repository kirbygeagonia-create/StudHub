@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-semibold text-seait-600 bg-seait-50 transition-colors dark:bg-seait-900/30 dark:text-seait-300'
            : 'flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-navy-800/60';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
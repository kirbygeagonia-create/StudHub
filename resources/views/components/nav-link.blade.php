@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-semibold text-seait-600 bg-seait-50 rounded-lg border border-seait-100 transition-all duration-150 dark:bg-seait-900/30 dark:text-seait-300 dark:border-seait-800/50'
            : 'inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-500 hover:text-gray-900 hover:bg-gray-100/80 rounded-lg transition-all duration-150 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-navy-800/60';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

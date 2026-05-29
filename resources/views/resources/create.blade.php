<x-app-layout>
    <x-page-header title="{{ __('Post a resource') }}" />

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 mt-2">
        <a href="{{ route('resources.index') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-seait-500 dark:hover:text-seait-400 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Resources
        </a>
    </div>
    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6">
                <livewire:resources.resource-form />
            </div>
        </div>
    </div>
</x-app-layout>
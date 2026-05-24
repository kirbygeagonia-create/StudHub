<x-app-layout>
    <x-page-header title="{{ __('Post a resource') }}" />

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6">
                <livewire:resources.resource-form />
            </div>
        </div>
    </div>
</x-app-layout>
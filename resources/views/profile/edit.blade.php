<x-app-layout>
    <x-page-header title="{{ __('Account Settings') }}" />

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="card p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Notification Preferences shortcut --}}
            <div class="card p-6">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                            <x-icon name="notifications" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notification Preferences</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Control which notifications you receive and how often.</p>
                        </div>
                    </div>
                    <a href="{{ route('profile.notification-preferences') }}" class="btn-secondary text-xs flex-shrink-0">
                        Manage
                    </a>
                </div>
            </div>

            <div class="card p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
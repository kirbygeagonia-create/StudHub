<x-app-layout>
    <x-page-header title="{{ __('Account Settings') }}" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-2">
        <a href="{{ route('profile.show') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-seait-500 dark:hover:text-seait-400 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Profile
        </a>
    </div>
    <div class="py-8" x-data="{ section: 'profile' }" x-init="
        const hash = window.location.hash.replace('#', '');
        if (['profile', 'password', 'notifications', 'delete'].includes(hash)) {
            section = hash;
        }
    ">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                {{-- Sidebar Navigation --}}
                <aside class="lg:w-56 flex-shrink-0">
                    <div class="lg:sticky lg:top-8">
                        {{-- Mobile: Dropdown --}}
                        <div class="lg:hidden mb-4">
                            <label for="mobile-nav" class="sr-only">{{ __('Select a section') }}</label>
                            <select id="mobile-nav"
                                    class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-seait-400 focus:ring-seait-400"
                                    x-model="section">
                                <option value="profile">{{ __('Profile Information') }}</option>
                                <option value="password">{{ __('Update Password') }}</option>
                                <option value="notifications">{{ __('Notification Preferences') }}</option>
                                <option value="delete">{{ __('Delete Account') }}</option>
                            </select>
                        </div>

                        {{-- Desktop: Vertical Nav --}}
                        <nav class="hidden lg:flex flex-col gap-1" aria-label="{{ __('Account Settings Sections') }}">
                            <button
                                type="button"
                                @click="section = 'profile'"
                                :class="section === 'profile'
                                    ? 'bg-seait-50 dark:bg-seait-900/20 text-seait-600 dark:text-seait-400 font-medium'
                                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-200'"
                                class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm transition-colors text-left"
                            >
                                <x-icon name="user" class="w-4 h-4 flex-shrink-0" />
                                {{ __('Profile Information') }}
                            </button>

                            <button
                                type="button"
                                @click="section = 'password'"
                                :class="section === 'password'
                                    ? 'bg-seait-50 dark:bg-seait-900/20 text-seait-600 dark:text-seait-400 font-medium'
                                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-200'"
                                class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm transition-colors text-left"
                            >
                                <x-icon name="lock-closed" class="w-4 h-4 flex-shrink-0" />
                                {{ __('Update Password') }}
                            </button>

                            <button
                                type="button"
                                @click="section = 'notifications'"
                                :class="section === 'notifications'
                                    ? 'bg-seait-50 dark:bg-seait-900/20 text-seait-600 dark:text-seait-400 font-medium'
                                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-200'"
                                class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm transition-colors text-left"
                            >
                                <x-icon name="bell" class="w-4 h-4 flex-shrink-0" />
                                {{ __('Notification Preferences') }}
                            </button>

                            <button
                                type="button"
                                @click="section = 'delete'"
                                :class="section === 'delete'
                                    ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 font-medium'
                                    : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-200'"
                                class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg text-sm transition-colors text-left"
                            >
                                <x-icon name="trash" class="w-4 h-4 flex-shrink-0" />
                                {{ __('Delete Account') }}
                            </button>
                        </nav>
                    </div>
                </aside>

                {{-- Content Panel --}}
                <main class="flex-1 min-w-0">
                    {{-- Profile Information --}}
                    <div
                        x-show="section === 'profile'"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-cloak
                    >
                        <div class="card p-4 sm:p-8">
                            <div class="max-w-xl">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>
                    </div>

                    {{-- Update Password --}}
                    <div
                        x-show="section === 'password'"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-cloak
                    >
                        <div class="card p-4 sm:p-8">
                            <div class="max-w-xl">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>
                    </div>

                    {{-- Notification Preferences --}}
                    <div
                        x-show="section === 'notifications'"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-cloak
                    >
                        <div class="card p-4 sm:p-8">
                            <div class="max-w-xl">
                                <section>
                                    <header>
                                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                            {{ __('Notification Preferences') }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ __('Control which notifications you receive and how often.') }}
                                        </p>
                                    </header>

                                    <div class="mt-6 flex items-center gap-3 p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
                                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-800/40 flex items-center justify-center flex-shrink-0">
                                            <x-icon name="notifications" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ __('Manage your notification settings') }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ __('Choose which programs to hear from and how often.') }}
                                            </p>
                                        </div>
                                        <a href="{{ route('profile.notification-preferences') }}" class="btn-secondary text-xs flex-shrink-0">
                                            {{ __('Manage') }}
                                        </a>
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>

                    {{-- Delete Account --}}
                    <div
                        x-show="section === 'delete'"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-cloak
                    >
                        <div class="card p-4 sm:p-8 border-red-200 dark:border-red-800/50">
                            <div class="max-w-xl">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>
</x-app-layout>

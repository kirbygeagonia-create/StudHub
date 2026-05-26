@php
// Check if this is being loaded inside a modal context
$isModal = request()->header('X-Requested-With') === 'XMLHttpRequest' || request()->query('modal') === '1';
@endphp

@if (!$isModal)
<x-guest-layout :heading="__('Log in to StudHub')">
@endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" @if($isModal) @submit.prevent="submitModalForm($event)" @endif>
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-seait-500 shadow-sm focus:ring-seait-400 dark:bg-navy-700 dark:checked:bg-seait-500" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-6">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-seait-400" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Don\'t have an account?') }}
            <a href="{{ route('register') }}" class="font-semibold text-seait-500 hover:text-seait-600 dark:hover:text-seait-400 underline underline-offset-2 transition">
                {{ __('Register') }}
            </a>
        </p>
    </div>

@if (!$isModal)
</x-guest-layout>
@endif

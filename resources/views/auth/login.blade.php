@php
// Check if this is being loaded inside a modal context
$isModal = request()->header('X-Requested-With') === 'XMLHttpRequest' || request()->query('modal') === '1';
@endphp

@if (!$isModal)
<x-guest-layout>
@endif

    {{-- Error banner — above heading --}}
    @if ($errors->any())
        <div class="w-full bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/30 text-red-700 dark:text-red-300 rounded-xl px-4 py-3 mb-4 flex items-center gap-2.5">
            <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-medium">{{ $errors->first('login') }}</span>
        </div>
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-6">{{ __('Log in to StudHub') }}</h1>

    <form method="POST" action="{{ route('login') }}" @if($isModal) @submit.prevent="submitModalForm($event)" @endif>
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

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

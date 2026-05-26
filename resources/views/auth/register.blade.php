@php
// Check if this is being loaded inside a modal context
$isModal = request()->header('X-Requested-With') === 'XMLHttpRequest' || request()->query('modal') === '1';
@endphp

@if (!$isModal)
<x-guest-layout :heading="__('Create your account')">
@endif

    <form method="POST" action="{{ route('register') }}" @if($isModal) @submit.prevent="submitModalForm($event)" @endif>
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Student Number -->
        <div class="mt-4">
            <x-input-label for="student_number" :value="__('Student Number (optional)')" />
            <x-text-input id="student_number" class="block mt-1 w-full" type="text" name="student_number" :value="old('student_number')" maxlength="20" placeholder="e.g. SEAIT-2024-001" />
            <x-input-error :messages="$errors->get('student_number')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-seait-400" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" class="font-semibold text-seait-500 hover:text-seait-600 dark:hover:text-seait-400 underline underline-offset-2 transition">
                {{ __('Log in') }}
            </a>
        </p>
    </div>

@if (!$isModal)
</x-guest-layout>
@endif

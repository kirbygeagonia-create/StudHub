<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ dark: localStorage.getItem('dark') === 'true' }" x-init="$watch('dark', v => { localStorage.setItem('dark', v); document.documentElement.classList.toggle('dark', v) }); document.documentElement.classList.toggle('dark', dark)" :class="{ 'dark': dark }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="manifest" href="/manifest.json">
        <meta name="theme-color" content="#FF6B35">
        <meta name="color-scheme" content="light dark">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow dark:bg-gray-800">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- PWA Install Banner -->
            <div id="pwa-install-banner"
                 class="hidden fixed bottom-0 inset-x-0 z-50 bg-seait-500 text-white px-4 py-3 flex items-center justify-between shadow-lg">
                <p class="text-sm font-medium">Install StudHub for quick access</p>
                <button onclick="installPwa()"
                        class="ml-4 px-4 py-1.5 bg-white text-seait-500 rounded-md text-sm font-semibold hover:bg-seait-50">
                    Install
                </button>
            </div>
        </div>
    </body>
</html>

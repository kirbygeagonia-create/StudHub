<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ dark: localStorage.getItem('dark') === 'true' }" x-init="if (dark) { document.documentElement.classList.add('dark') }; $watch('dark', v => { localStorage.setItem('dark', v); document.documentElement.classList.toggle('dark', v) })" :class="{ 'dark': dark }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2" sizes="any">
        <link rel="icon" href="/favicon.svg?v=2" type="image/svg+xml">
        <meta name="theme-color" content="#FF6B35">
        <meta name="color-scheme" content="light dark">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100">

        {{-- Guest Nav --}}
        <header class="sticky top-0 z-40 bg-white/80 dark:bg-navy-900/80 backdrop-blur-xl border-b border-gray-200/50 dark:border-navy-700/30 shadow-sm">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2 group">
                    <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" fill="none" class="w-8 h-8">
                            <defs>
                                <linearGradient id="ghG" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#FF6B35"/>
                                    <stop offset="100%" stop-color="#C94B15"/>
                                </linearGradient>
                                <linearGradient id="ghA" x1="0" y1="1" x2="1" y2="0">
                                    <stop offset="0%" stop-color="#FFB347"/>
                                    <stop offset="100%" stop-color="#FF8C5A"/>
                                </linearGradient>
                            </defs>
                            <path d="M100 18 C68 18 38 30 24 52 C10 74 10 100 10 100 C10 132 12 152 30 166 C48 180 72 182 100 182 C128 182 152 180 170 166 C188 152 190 132 190 100 C190 100 190 74 176 52 C162 30 132 18 100 18Z" fill="url(#ghG)"/>
                            <path d="M100 52 C88 54 66 62 54 74 L54 148 C66 138 88 132 100 132 Z" fill="white" opacity="0.16"/>
                            <path d="M100 52 C112 54 134 62 146 74 L146 148 C134 138 112 132 100 132 Z" fill="white" opacity="0.10"/>
                            <line x1="100" y1="52" x2="100" y2="148" stroke="white" stroke-width="2" opacity="0.28" stroke-linecap="round"/>
                            <rect x="72" y="94" width="56" height="8" rx="4" fill="white" opacity="0.95"/>
                            <path d="M100 70 L118 91 L100 99 L82 91 Z" fill="white" opacity="0.95"/>
                            <line x1="116" y1="97" x2="116" y2="122" stroke="url(#ghA)" stroke-width="3" stroke-linecap="round"/>
                            <circle cx="116" cy="126" r="4.5" fill="url(#ghA)"/>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900 tracking-tight dark:text-gray-100 group-hover:text-seait-500 dark:group-hover:text-seait-400 transition-colors duration-200">StudHub</span>
                </a>
                <div class="flex items-center gap-3">
                    <a href="/?open=help" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">Help</a>
                    <a href="/?open=aup" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">Policy</a>
                    <a href="/?open=login" class="btn-primary text-xs !px-4 !py-2">Log in</a>
                    <a href="/?open=register" class="btn-secondary text-xs !px-4 !py-2">Register</a>
                    <button @click="dark = !dark" type="button" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition rounded-xl hover:bg-gray-100/80 dark:hover:bg-navy-800/60" aria-label="Toggle dark mode">
                        <template x-if="!dark">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        </template>
                        <template x-if="dark">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </template>
                    </button>
                </div>
            </div>
        </header>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative">

            {{-- Subtle background pattern for auth pages --}}
            <div class="absolute inset-0 pointer-events-none opacity-30 dark:opacity-20">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="auth-dots" x="0" y="0" width="32" height="32" patternUnits="userSpaceOnUse">
                            <circle cx="1.5" cy="1.5" r="1" class="fill-gray-300 dark:fill-navy-500" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#auth-dots)" />
                </svg>
            </div>

            <!-- Flash Messages -->
            @if (session('status') || session('success') || session('error') || session('warning') || session('info'))
                <div class="w-full max-w-md px-4 mb-4 relative z-10">
                    @if (session('status') || session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="flash-success">
                            <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('status') ?? session('success') }}</span>
                            <button @click="show = false" class="ml-auto text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="flash-error">
                            <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('error') }}</span>
                            <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700 dark:hover:text-red-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @endif
                    @if (session('warning'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="flash-warning">
                            <svg class="w-5 h-5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.502-1.275.722-1.845l-6.928-5.013c-.752-.545-1.792-.545-2.544 0L5.094 17.155c-.78.57-.332 1.845.722 1.845z"/></svg>
                            <span>{{ session('warning') }}</span>
                            <button @click="show = false" class="ml-auto text-amber-500 hover:text-amber-700 dark:hover:text-amber-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @endif
                    @if (session('info'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                             x-transition:leave="transition ease-in duration-300"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-2"
                             class="flash-info">
                            <svg class="w-5 h-5 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('info') }}</span>
                            <button @click="show = false" class="ml-auto text-blue-500 hover:text-blue-700 dark:hover:text-blue-300">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <div class="shrink-0 flex items-center flex-col relative z-10">
                <a href="/">
                    <x-application-logo class="w-16 h-16" />
                </a>
                <p class="mt-2 text-xs font-semibold text-seait-500 dark:text-seait-400 tracking-widest uppercase">StudHub</p>
                <p class="text-[10px] text-gray-400 dark:text-gray-500">SEAIT Academic Resource Exchange</p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-8 card overflow-hidden relative z-10">
                @if (isset($heading))
                    <h1 class="text-2xl font-bold text-center text-gray-900 dark:text-white mb-2">
                        {{ $heading }}
                    </h1>
                @endif

                {{ $slot }}
            </div>
        </div>
        @stack('scripts')
        @livewireScripts
    </body>
</html>

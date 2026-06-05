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

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <script>if (localStorage.getItem('dark') === 'true') document.documentElement.classList.add('dark')</script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased text-gray-900 dark:text-gray-100 {{ ($authUser ?? Auth::user())?->panelClass() }}">
        <div class="min-h-screen">
            @auth
                <x-role-context-banner />
                @include('layouts.navigation')
            @else
                {{-- Guest nav — minimal, no protected links --}}
                <nav class="sticky top-0 z-40 bg-white/95 dark:bg-navy-900/95 backdrop-blur-xl border-b border-gray-200/50 dark:border-navy-700/30 shadow-sm">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16 items-center">
                            <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" fill="none" class="w-8 h-8">
                                        <defs>
                                            <linearGradient id="gstG" x1="0" y1="0" x2="1" y2="1">
                                                <stop offset="0%" stop-color="#FF6B35"/>
                                                <stop offset="100%" stop-color="#C94B15"/>
                                            </linearGradient>
                                            <linearGradient id="gstA" x1="0" y1="1" x2="1" y2="0">
                                                <stop offset="0%" stop-color="#FFB347"/>
                                                <stop offset="100%" stop-color="#FF8C5A"/>
                                            </linearGradient>
                                        </defs>
                                        <path d="M100 18 C68 18 38 30 24 52 C10 74 10 100 10 100 C10 132 12 152 30 166 C48 180 72 182 100 182 C128 182 152 180 170 166 C188 152 190 132 190 100 C190 100 190 74 176 52 C162 30 132 18 100 18Z" fill="url(#gstG)"/>
                                        <path d="M100 52 C88 54 66 62 54 74 L54 148 C66 138 88 132 100 132 Z" fill="white" opacity="0.16"/>
                                        <path d="M100 52 C112 54 134 62 146 74 L146 148 C134 138 112 132 100 132 Z" fill="white" opacity="0.10"/>
                                        <line x1="100" y1="52" x2="100" y2="148" stroke="white" stroke-width="2" opacity="0.28" stroke-linecap="round"/>
                                        <rect x="72" y="94" width="56" height="8" rx="4" fill="white" opacity="0.95"/>
                                        <path d="M100 70 L118 91 L100 99 L82 91 Z" fill="white" opacity="0.95"/>
                                        <line x1="116" y1="97" x2="116" y2="122" stroke="url(#gstA)" stroke-width="3" stroke-linecap="round"/>
                                        <circle cx="116" cy="126" r="4.5" fill="url(#gstA)"/>
                                    </svg>
                                </div>
                                <span class="text-lg font-bold text-gray-900 tracking-tight dark:text-gray-100 group-hover:text-seait-500 dark:group-hover:text-seait-400 transition-colors duration-200">StudHub</span>
                            </a>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('help') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition {{ request()->routeIs('help') ? 'font-semibold text-seait-500' : '' }}">Help</a>
                                <a href="{{ route('aup') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition {{ request()->routeIs('aup') ? 'font-semibold text-seait-500' : '' }}">Policy</a>
                                <a href="{{ route('login') }}" class="btn-primary text-xs !px-4 !py-2">Log in</a>
                                <a href="{{ route('register') }}" class="btn-secondary text-xs !px-4 !py-2">Register</a>
                            </div>
                        </div>
                    </div>
                </nav>
            @endauth

            @isset($header)
                <header class="bg-white/60 backdrop-blur-sm border-b border-gray-100 dark:bg-navy-800/40 dark:border-navy-700/30">
                    <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- ======= FLOATING FLASH MESSAGES (fixed, top-center, no layout shift) ======= --}}
            <div class="fixed top-20 inset-x-0 z-[200] flex flex-col items-center gap-2 px-4 pointer-events-none">

                @if (session('status') || session('success'))
                    <div x-data="{ show: true }" x-show="show"
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="flash-success pointer-events-auto w-full max-w-md">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="flex-1">{{ session('status') ?? session('success') }}</span>
                        <button @click="show = false" class="ml-auto flex-shrink-0 text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300" aria-label="Dismiss notification">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show"
                         x-init="setTimeout(() => show = false, 6000)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="flash-error pointer-events-auto w-full max-w-md">
                        <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="flex-1">{{ session('error') }}</span>
                        <button @click="show = false" class="ml-auto flex-shrink-0 text-red-500 hover:text-red-700 dark:hover:text-red-300" aria-label="Dismiss notification">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if (session('warning'))
                    <div x-data="{ show: true }" x-show="show"
                         x-init="setTimeout(() => show = false, 7000)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="flash-warning pointer-events-auto w-full max-w-md">
                        <svg class="w-5 h-5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.502-1.275.722-1.845l-6.928-5.013c-.752-.545-1.792-.545-2.544 0L5.094 17.155c-.78.57-.332 1.845.722 1.845z"/></svg>
                        <span class="flex-1">{{ session('warning') }}</span>
                        <button @click="show = false" class="ml-auto flex-shrink-0 text-amber-500 hover:text-amber-700 dark:hover:text-amber-300" aria-label="Dismiss notification">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if (session('info'))
                    <div x-data="{ show: true }" x-show="show"
                         x-init="setTimeout(() => show = false, 5000)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="flash-info pointer-events-auto w-full max-w-md">
                        <svg class="w-5 h-5 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="flex-1">{{ session('info') }}</span>
                        <button @click="show = false" class="ml-auto flex-shrink-0 text-blue-500 hover:text-blue-700 dark:hover:text-blue-300" aria-label="Dismiss notification">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div x-data="{ show: true }" x-show="show"
                         x-init="setTimeout(() => show = false, 10000)"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-300"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="flash-error pointer-events-auto w-full max-w-md">
                        <svg class="w-5 h-5 flex-shrink-0 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <ul class="flex-1 list-disc list-inside space-y-0.5 text-xs">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button @click="show = false" class="ml-auto flex-shrink-0 text-red-500 hover:text-red-700 dark:hover:text-red-300" aria-label="Dismiss notification">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

            </div>
            {{-- ======= END FLASH MESSAGES ======= --}}

            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- PWA Install Banner — visibility controlled by app.js via classList, NOT Alpine x-show -->
        <div id="pwa-install-banner"
             class="hidden fixed bottom-4 inset-x-4 z-50 card p-4 flex items-center justify-between gap-4 max-w-lg mx-auto shadow-card-lg">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-seait-100 dark:bg-seait-900/30 flex items-center justify-center flex-shrink-0">
                    <x-application-logo class="w-7 h-7" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Install StudHub</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Get faster access on your device</p>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <button onclick="installPwa()" class="btn-primary text-xs !px-4 !py-2">Install</button>
                <button onclick="document.getElementById('pwa-install-banner').classList.add('hidden')"
                        class="p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200" aria-label="Dismiss install banner">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        @stack('scripts')
        @livewireScripts
    </body>
</html>
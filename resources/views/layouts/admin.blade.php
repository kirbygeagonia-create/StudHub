<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ dark: localStorage.getItem('dark') === 'true' }"
      x-init="if (dark) { document.documentElement.classList.add('dark') };
               $watch('dark', v => {
                   localStorage.setItem('dark', v);
                   document.documentElement.classList.toggle('dark', v)
               })"
      :class="{ 'dark': dark }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2" sizes="any">
        <meta name="theme-color" content="#FF6B35">
        <meta name="color-scheme" content="light dark">
        <title>@yield('title', config('app.name', 'StudHub'))</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap"
              rel="stylesheet"/>
        <script>
            if (localStorage.getItem('dark') === 'true')
                document.documentElement.classList.add('dark')
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>

    <body class="font-sans antialiased text-gray-900 dark:text-gray-100
                 {{ auth()->user()?->panelClass() }}">

        {{-- Role banner + top navigation --}}
        <x-role-context-banner />
        @include('layouts.navigation')

        {{-- Flash messages --}}
        <div class="fixed top-20 inset-x-0 z-[200] flex flex-col items-center
                    gap-2 px-4 pointer-events-none">

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
                    <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="flex-1">
                        {{ session('status') ?? session('success') }}
                    </span>
                    <button @click="show = false"
                            class="ml-auto text-emerald-500 hover:text-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
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
                    <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="flex-1">{{ session('error') }}</span>
                    <button @click="show = false"
                            class="ml-auto text-red-500 hover:text-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
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
                    <svg class="w-5 h-5 flex-shrink-0 text-red-500 mt-0.5"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <ul class="flex-1 list-disc list-inside space-y-0.5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button @click="show = false"
                            class="ml-auto text-red-500 hover:text-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

        </div>

        {{-- Two-column admin layout --}}
        <div class="admin-layout">

            {{-- Sidebar — hidden on mobile, visible md+ --}}
            <aside class="admin-sidebar hidden md:flex flex-col">
                @yield('sidebar')
            </aside>

            {{-- Main content --}}
            <main class="admin-content">
                @hasSection('pageHeader')
                    <div class="mb-5">@yield('pageHeader')</div>
                @endif
                @yield('content')
            </main>

        </div>

        @stack('scripts')
        @livewireScripts
    </body>
</html>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth" x-data="{ dark: localStorage.getItem('dark') === 'true', showLoginModal: false, showRegisterModal: false }" x-init="$watch('dark', v => { localStorage.setItem('dark', v); document.documentElement.classList.toggle('dark', v) }); document.documentElement.classList.toggle('dark', dark)" :class="{ 'dark': dark }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudHub — SEAIT Academic Resource Exchange</title>

    <meta name="color-scheme" content="light dark">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased font-sans">
    <div class="min-h-screen relative">
        {{-- Background pattern --}}
        <div class="absolute inset-0 pointer-events-none overflow-hidden">
            <div class="absolute -top-1/2 -left-1/2 w-full h-full bg-gradient-to-br from-seait-500/[0.03] via-transparent to-transparent dark:from-seait-500/[0.05]"></div>
            <div class="absolute -bottom-1/2 -right-1/2 w-full h-full bg-gradient-to-tl from-navy-800/[0.3] via-transparent to-transparent dark:from-navy-800/[0.2]"></div>
        </div>

        {{-- Flash Messages --}}
        @if (session('status') || session('success') || session('error'))
            <div class="fixed top-4 left-1/2 -translate-x-1/2 z-50 w-full max-w-md px-4">
                @if (session('status') || session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                         x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                         class="flash-success">
                        <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('status') ?? session('success') }}</span>
                        <button @click="show = false" class="ml-auto text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                @endif
                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                         x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                         class="flash-error">
                        <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ session('error') }}</span>
                        <button @click="show = false" class="ml-auto text-red-500 hover:text-red-700 dark:hover:text-red-300"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                @endif
            </div>
        @endif

        {{-- Nav --}}
        <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-gray-200/50 dark:bg-navy-900/80 dark:border-navy-700/30 shadow-sm dark:shadow-navy-950/30">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2 group">
                    <div class="w-8 h-8 bg-gradient-to-br from-seait-400 to-seait-600 rounded-xl flex items-center justify-center shadow-sm group-hover:shadow-seait-500/30 group-hover:shadow-md transition-all duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="font-bold text-lg text-gray-900 dark:text-gray-100 group-hover:text-seait-500 dark:group-hover:text-seait-400 transition-colors duration-200">StudHub</span>
                </a>
                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-primary text-xs !px-4 !py-2">Dashboard</a>
                        @else
                            <button @click="showLoginModal = true" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-300 dark:hover:text-white transition font-medium">Log in</button>
                            @if (Route::has('register'))
                                <button @click="showRegisterModal = true" class="btn-primary text-xs !px-4 !py-2">Register</button>
                            @endif
                        @endauth
                    @endif
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

        {{-- Hero with gradient background --}}
        <section class="relative overflow-hidden">
            {{-- Hero background gradient --}}
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute inset-0 bg-gradient-to-br from-seait-50/80 via-transparent to-blue-50/40 dark:from-seait-900/10 dark:via-transparent dark:to-blue-900/5"></div>
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[600px] bg-gradient-to-b from-seait-500/10 to-transparent rounded-full blur-3xl dark:from-seait-500/5"></div>
                <div class="absolute bottom-0 right-0 w-[400px] h-[400px] bg-gradient-to-tl from-navy-300/10 to-transparent rounded-full blur-3xl dark:from-navy-500/5"></div>
            </div>

            <div class="max-w-4xl mx-auto px-6 pt-16 pb-12 text-center relative">
                <div class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-seait-50 dark:bg-seait-900/20 border border-seait-100 dark:border-seait-800/40 rounded-full text-xs font-semibold text-seait-600 dark:text-seait-300 mb-6 shadow-sm">
                    <span class="w-2 h-2 bg-seait-500 rounded-full animate-pulse"></span>
                    South East Asian Institute of Technology
                </div>
                <h1 class="text-4xl sm:text-6xl font-extrabold text-gray-900 dark:text-white leading-[1.1] mb-5 tracking-tight">
                    Your school's resource<br class="hidden sm:block">
                    <span class="text-gradient-seait">exchange</span>, not your inbox.
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-xl mx-auto mb-10 leading-relaxed">
                    Share textbooks, e-modules, reviewers, and past exams across 6 colleges and 26 programs at SEAIT. Find what you need from someone who already took the subject.
                </p>
                <div class="flex justify-center gap-3">
                    @if (Route::has('register'))
                        <button @click="showRegisterModal = true" class="btn-gradient !px-8 !py-3 !text-base">Get started</button>
                    @endif
                    <a href="#features" class="btn-secondary !px-8 !py-3 !text-base">Learn more</a>
                </div>
            </div>
        </section>

        {{-- Features --}}
        <section id="features" class="max-w-6xl mx-auto px-6 py-16 relative">
            <div class="grid sm:grid-cols-3 gap-8">
                <div class="card-hover p-8 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-seait-50 to-seait-100 dark:from-seait-900/30 dark:to-seait-800/20 rounded-2xl flex items-center justify-center mb-5 shadow-sm group-hover:shadow-seait-500/10 transition-shadow duration-300">
                        <svg class="w-6 h-6 text-seait-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-2 text-lg">Smart Search</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Search across resources, requests, and chat messages to find exactly what you need.</p>
                </div>
                <div class="card-hover p-8 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-800/10 rounded-2xl flex items-center justify-center mb-5 shadow-sm group-hover:shadow-green-500/10 transition-shadow duration-300">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-2 text-lg">Cross-Program</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Resources are matched across programs. A BSIT reviewer might be exactly what a BSCE student needs.</p>
                </div>
                <div class="card-hover p-8 group">
                    <div class="w-12 h-12 bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/10 rounded-2xl flex items-center justify-center mb-5 shadow-sm group-hover:shadow-amber-500/10 transition-shadow duration-300">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-bold text-gray-900 dark:text-gray-100 mb-2 text-lg">Real-Time</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Per-program chat rooms with @mentions and file sharing. Get help from seniors instantly.</p>
                </div>
            </div>
        </section>

        {{-- How it works --}}
        <section class="relative py-16 overflow-hidden">
            <div class="absolute inset-0 bg-white/60 dark:bg-navy-800/40 border-y border-gray-100 dark:border-navy-700/30 backdrop-blur-sm"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-seait-50/30 via-transparent to-blue-50/20 dark:from-seait-900/5 dark:via-transparent dark:to-blue-900/5"></div>
            <div class="max-w-4xl mx-auto px-6 text-center relative">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 dark:text-gray-100 mb-4">How StudHub works</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-12 text-lg">Three simple steps. No Facebook groups required.</p>
                <div class="grid sm:grid-cols-3 gap-8 text-left">
                    <div class="relative group">
                        <div class="w-10 h-10 bg-gradient-to-br from-seait-500 to-seait-600 text-white rounded-xl flex items-center justify-center text-sm font-bold mb-4 shadow-lg shadow-seait-500/20 group-hover:shadow-seait-500/30 transition-shadow">1</div>
                        <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-1 text-lg">Post what you have</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Upload reviewers, textbooks, e-modules, or past exams. They're available to your whole school.</p>
                    </div>
                    <div class="relative group">
                        <div class="w-10 h-10 bg-gradient-to-br from-seait-500 to-seait-600 text-white rounded-xl flex items-center justify-center text-sm font-bold mb-4 shadow-lg shadow-seait-500/20 group-hover:shadow-seait-500/30 transition-shadow">2</div>
                        <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-1 text-lg">Request what you need</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Post a request for any subject. StudHub routes it to programs that teach that subject.</p>
                    </div>
                    <div class="relative group">
                        <div class="w-10 h-10 bg-gradient-to-br from-seait-500 to-seait-600 text-white rounded-xl flex items-center justify-center text-sm font-bold mb-4 shadow-lg shadow-seait-500/20 group-hover:shadow-seait-500/30 transition-shadow">3</div>
                        <h4 class="font-bold text-gray-900 dark:text-gray-100 mb-1 text-lg">Lend & earn karma</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">Lend your resources, get karma points, and climb the leaderboard. The community grows together.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Stats --}}
        <section class="max-w-4xl mx-auto px-6 py-16 text-center">
            <div class="grid sm:grid-cols-3 gap-8">
                <div class="card p-6 group hover:shadow-card-hover transition-all duration-300">
                    <p class="text-4xl font-extrabold text-gradient-seait mb-1">6</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Colleges</p>
                </div>
                <div class="card p-6 group hover:shadow-card-hover transition-all duration-300">
                    <p class="text-4xl font-extrabold text-gradient-seait mb-1">26</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Programs</p>
                </div>
                <div class="card p-6 group hover:shadow-card-hover transition-all duration-300">
                    <p class="text-4xl font-extrabold text-gradient-seait mb-1">24/7</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Always available</p>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="border-t border-gray-100 dark:border-navy-700/30 py-8 relative">
            <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">StudHub &copy; {{ date('Y') }} &mdash; South East Asian Institute of Technology, Inc.</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('help') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">Help</a>
                    <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                    @auth
                        <a href="{{ route('feedback.create') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">Feedback</a>
                        <span class="text-gray-300 dark:text-gray-600">&middot;</span>
                    @endauth
                    <a href="{{ route('aup') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">Acceptable Use Policy</a>
                </div>
            </div>
        </footer>
    </div>

    {{-- Login Modal --}}
    <div x-show="showLoginModal" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-navy-950/50 backdrop-blur-sm" @click="showLoginModal = false"></div>

        {{-- Modal Content --}}
        <div x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="relative w-full max-w-sm bg-white dark:bg-navy-800 rounded-2xl shadow-card-lg border border-gray-100 dark:border-navy-700/50 overflow-hidden">
            {{-- Close button --}}
            <button @click="showLoginModal = false" class="absolute top-3 right-3 p-1.5 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition rounded-lg hover:bg-gray-100 dark:hover:bg-navy-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="p-6">
                <div class="text-center mb-5">
                    <div class="w-10 h-10 bg-gradient-to-br from-seait-400 to-seait-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Welcome back</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Log in to your StudHub account</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div>
                        <label for="login-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input id="login-email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               class="input-field" placeholder="you@seait.edu.ph">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-3">
                        <label for="login-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                        <input id="login-password" type="password" name="password" required autocomplete="current-password"
                               class="input-field" placeholder="••••••••">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between mt-3">
                        <label for="remember_me" class="inline-flex items-center cursor-pointer">
                            <input id="remember_me" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-seait-500 shadow-sm focus:ring-seait-400 dark:bg-navy-700 dark:checked:bg-seait-500" name="remember">
                            <span class="ms-2 text-xs text-gray-600 dark:text-gray-400">Remember me</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-seait-500 hover:text-seait-600 dark:hover:text-seait-400 transition">Forgot password?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn-primary w-full mt-5 !py-2.5">Log in</button>
                </form>

                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        Don't have an account?
                        <button @click="showLoginModal = false; showRegisterModal = true" class="font-semibold text-seait-500 hover:text-seait-600 dark:hover:text-seait-400 transition">Register</button>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Register Modal --}}
    <div x-show="showRegisterModal" x-cloak
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-navy-950/50 backdrop-blur-sm" @click="showRegisterModal = false"></div>

        {{-- Modal Content --}}
        <div x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="relative w-full max-w-sm bg-white dark:bg-navy-800 rounded-2xl shadow-card-lg border border-gray-100 dark:border-navy-700/50 overflow-hidden max-h-[85vh] overflow-y-auto">
            {{-- Close button --}}
            <button @click="showRegisterModal = false" class="absolute top-3 right-3 p-1.5 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition rounded-lg hover:bg-gray-100 dark:hover:bg-navy-700 z-10">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <div class="p-6">
                <div class="text-center mb-5">
                    <div class="w-10 h-10 bg-gradient-to-br from-seait-400 to-seait-600 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Create your account</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Join the SEAIT resource exchange</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div>
                        <label for="register-name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                        <input id="register-name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                               class="input-field">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-3">
                        <label for="register-student-number" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Student Number <span class="text-gray-400 font-normal text-xs">(optional)</span></label>
                        <input id="register-student-number" type="text" name="student_number" value="{{ old('student_number') }}" maxlength="20"
                               class="input-field" placeholder="e.g. SEAIT-2024-001">
                        @error('student_number')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-3">
                        <label for="register-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input id="register-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                               class="input-field" placeholder="you@seait.edu.ph">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-3">
                        <label for="register-password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                        <input id="register-password" type="password" name="password" required autocomplete="new-password"
                               class="input-field" placeholder="••••••••">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-3">
                        <label for="register-password-confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Confirm Password</label>
                        <input id="register-password-confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                               class="input-field" placeholder="••••••••">
                    </div>

                    <button type="submit" class="btn-primary w-full mt-5 !py-2.5">Register</button>
                </form>

                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        Already have an account?
                        <button @click="showRegisterModal = false; showLoginModal = true" class="font-semibold text-seait-500 hover:text-seait-600 dark:hover:text-seait-400 transition">Log in</button>
                    </p>
                </div>
            </div>
        </div>
    </div>

    </body>
</html>

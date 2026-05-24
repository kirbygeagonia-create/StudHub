<!DOCTYPE html>
<html lang="en" class="scroll-smooth" x-data="{ dark: localStorage.getItem('dark') === 'true' }" x-init="$watch('dark', v => { localStorage.setItem('dark', v); document.documentElement.classList.toggle('dark', v) }); document.documentElement.classList.toggle('dark', dark)" :class="{ 'dark': dark }">
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
    <div class="min-h-screen">
        {{-- Nav --}}
        <header class="bg-navy-900/95 backdrop-blur-sm border-b border-navy-700/50 sticky top-0 z-40">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2">
                    <span class="w-8 h-8 bg-seait-500 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    <span class="font-semibold text-lg text-white">StudHub</span>
                </a>
                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-seait-500 text-white text-sm font-medium rounded-xl hover:bg-seait-600 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-white/70 hover:text-white transition">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4 py-2 bg-seait-500 text-white text-sm font-medium rounded-xl hover:bg-seait-600 transition">Register</a>
                            @endif
                        @endauth
                    @endif
                    <button @click="dark = !dark" type="button" class="p-2 text-white/70 hover:text-white transition" aria-label="Toggle dark mode">
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

        {{-- Hero --}}
        <section class="max-w-4xl mx-auto px-6 pt-24 pb-16 text-center">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-seait-50 dark:bg-seait-900/30 border border-seait-50 dark:border-seait-800 rounded-full text-xs font-medium text-seait-600 dark:text-seait-300 mb-6">
                South East Asian Institute of Technology
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold text-gray-900 dark:text-gray-100 leading-tight mb-4">
                Your school's resource<br class="hidden sm:block"> exchange, not your inbox.
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-xl mx-auto mb-8">
                Share textbooks, e-modules, reviewers, and past exams across 6 colleges and 26 programs at SEAIT. Find what you need from someone who already took the subject.
            </p>
            <div class="flex justify-center gap-3">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="px-6 py-3 bg-seait-500 text-white font-medium rounded-xl hover:bg-seait-600 transition shadow-sm">Get started</a>
                @endif
                <a href="#features" class="px-6 py-3 border border-navy-500 text-gray-900 dark:text-gray-100 font-medium rounded-xl hover:bg-navy-50 dark:hover:bg-navy-500/20 transition">Learn more</a>
            </div>
        </section>

        {{-- Features --}}
        <section id="features" class="max-w-6xl mx-auto px-6 py-20">
            <div class="grid sm:grid-cols-3 gap-8">
                <div class="card p-8">
                    <div class="w-10 h-10 bg-seait-50 dark:bg-seait-900/30 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-seait-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Smart Search</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Search across resources, requests, and chat messages to find exactly what you need.</p>
                </div>
                <div class="card p-8">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Cross-Program</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Resources are matched across programs. A BSIT reviewer might be exactly what a BSCE student needs.</p>
                </div>
                <div class="card p-8">
                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">Real-Time</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Per-program chat rooms with @mentions and file sharing. Get help from seniors instantly.</p>
                </div>
            </div>
        </section>

        {{-- How it works --}}
        <section class="bg-white dark:bg-navy-800 border-t border-gray-100 dark:border-navy-700 py-20">
            <div class="max-w-4xl mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">How StudHub works</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-12">Three simple steps. No Facebook groups required.</p>
                <div class="grid sm:grid-cols-3 gap-8 text-left">
                    <div class="relative">
                        <div class="w-8 h-8 bg-seait-500 text-white rounded-full flex items-center justify-center text-sm font-bold mb-4">1</div>
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Post what you have</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Upload reviewers, textbooks, e-modules, or past exams. They're available to your whole school.</p>
                    </div>
                    <div class="relative">
                        <div class="w-8 h-8 bg-seait-500 text-white rounded-full flex items-center justify-center text-sm font-bold mb-4">2</div>
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Request what you need</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Post a request for any subject. StudHub routes it to programs that teach that subject.</p>
                    </div>
                    <div class="relative">
                        <div class="w-8 h-8 bg-seait-500 text-white rounded-full flex items-center justify-center text-sm font-bold mb-4">3</div>
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-1">Lend & earn karma</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Lend your resources, get karma points, and climb the leaderboard. The community grows together.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Stats --}}
        <section class="max-w-4xl mx-auto px-6 py-16 text-center">
            <div class="grid sm:grid-cols-3 gap-8">
                <div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">6</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Colleges</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">26</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Programs</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">24/7</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Always available</p>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="border-t border-gray-100 dark:border-navy-700 py-8">
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
</body>
</html>
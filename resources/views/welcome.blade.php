<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudHub — SEAIT Academic Resource Exchange</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=lexend:400,500,600,700|dm-sans:400,500" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-slate-50 font-sans">
    <div class="min-h-screen">
        {{-- Nav --}}
        <header class="bg-white border-b border-slate-200">
            <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2">
                    <span class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    <span class="font-semibold text-lg text-slate-900" style="font-family: 'Lexend', sans-serif;">StudHub</span>
                </a>
                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-slate-600 hover:text-slate-900 transition">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">Register</a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </header>

        {{-- Hero --}}
        <section class="max-w-4xl mx-auto px-6 pt-24 pb-16 text-center">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 border border-indigo-100 rounded-full text-xs font-medium text-indigo-700 mb-6">
                South East Asian Institute of Technology
            </div>
            <h1 class="text-4xl sm:text-5xl font-bold text-slate-900 leading-tight mb-4" style="font-family: 'Lexend', sans-serif;">
                Your school's resource<br class="hidden sm:block"> exchange, not your inbox.
            </h1>
            <p class="text-lg text-slate-500 max-w-xl mx-auto mb-8">
                Share textbooks, e-modules, reviewers, and past exams across 6 colleges and 26 programs at SEAIT. Find what you need from someone who already took the subject.
            </p>
            <div class="flex justify-center gap-3">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition shadow-sm">Get started</a>
                @endif
                <a href="#features" class="px-6 py-3 border border-slate-200 text-slate-700 font-medium rounded-lg hover:bg-white transition">Learn more</a>
            </div>
        </section>

        {{-- Features --}}
        <section id="features" class="max-w-6xl mx-auto px-6 py-20">
            <div class="grid sm:grid-cols-3 gap-8">
                <div class="bg-white rounded-xl p-8 border border-slate-100 shadow-sm">
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-2" style="font-family: 'Lexend', sans-serif;">Smart Search</h3>
                    <p class="text-sm text-slate-500">Search across resources, requests, and chat messages to find exactly what you need.</p>
                </div>
                <div class="bg-white rounded-xl p-8 border border-slate-100 shadow-sm">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-2" style="font-family: 'Lexend', sans-serif;">Cross-Program</h3>
                    <p class="text-sm text-slate-500">Resources are matched across programs. A BSIT reviewer might be exactly what a BSCE student needs.</p>
                </div>
                <div class="bg-white rounded-xl p-8 border border-slate-100 shadow-sm">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-2" style="font-family: 'Lexend', sans-serif;">Real-Time</h3>
                    <p class="text-sm text-slate-500">Per-program chat rooms with @mentions and file sharing. Get help from seniors instantly.</p>
                </div>
            </div>
        </section>

        {{-- How it works --}}
        <section class="bg-white border-t border-slate-100 py-20">
            <div class="max-w-4xl mx-auto px-6 text-center">
                <h2 class="text-3xl font-bold text-slate-900 mb-4" style="font-family: 'Lexend', sans-serif;">How StudHub works</h2>
                <p class="text-slate-500 mb-12">Three simple steps. No Facebook groups required.</p>
                <div class="grid sm:grid-cols-3 gap-8 text-left">
                    <div class="relative">
                        <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold mb-4">1</div>
                        <h4 class="font-semibold text-slate-900 mb-1">Post what you have</h4>
                        <p class="text-sm text-slate-500">Upload reviewers, textbooks, e-modules, or past exams. They're available to your whole school.</p>
                    </div>
                    <div class="relative">
                        <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold mb-4">2</div>
                        <h4 class="font-semibold text-slate-900 mb-1">Request what you need</h4>
                        <p class="text-sm text-slate-500">Post a request for any subject. StudHub routes it to programs that teach that subject.</p>
                    </div>
                    <div class="relative">
                        <div class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold mb-4">3</div>
                        <h4 class="font-semibold text-slate-900 mb-1">Lend & earn karma</h4>
                        <p class="text-sm text-slate-500">Lend your resources, get karma points, and climb the leaderboard. The community grows together.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Stats --}}
        <section class="max-w-4xl mx-auto px-6 py-16 text-center">
            <div class="grid sm:grid-cols-3 gap-8">
                <div>
                    <p class="text-3xl font-bold text-slate-900" style="font-family: 'Lexend', sans-serif;">6</p>
                    <p class="text-sm text-slate-500">Colleges</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-slate-900" style="font-family: 'Lexend', sans-serif;">26</p>
                    <p class="text-sm text-slate-500">Programs</p>
                </div>
                <div>
                    <p class="text-3xl font-bold text-slate-900" style="font-family: 'Lexend', sans-serif;">24/7</p>
                    <p class="text-sm text-slate-500">Always available</p>
                </div>
            </div>
        </section>

        {{-- Footer --}}
        <footer class="border-t border-slate-100 py-8">
            <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-sm text-slate-400">StudHub &copy; {{ date('Y') }} &mdash; South East Asian Institute of Technology, Inc.</p>
                <div class="flex items-center gap-4">
                    <a href="{{ route('help') }}" class="text-sm text-slate-400 hover:text-slate-600 transition">Help</a>
                    <span class="text-slate-300">&middot;</span>
                    @auth
                        <a href="{{ route('feedback.create') }}" class="text-sm text-slate-400 hover:text-slate-600 transition">Feedback</a>
                        <span class="text-slate-300">&middot;</span>
                    @endauth
                    <a href="{{ route('aup') }}" class="text-sm text-slate-400 hover:text-slate-600 transition">Acceptable Use Policy</a>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
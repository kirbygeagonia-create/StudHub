<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 tracking-tight dark:text-gray-100">
                    Welcome back, {{ explode(' ', $user->preferredDisplayName() ?? '')[0] }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ $user->program?->code ?? '' }}{{ $user->year_level ? ' · Year ' . $user->year_level : '' }}
                </p>
            </div>
        </div>
    </x-slot>

    @include('partials.onboarding-modal')

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <!-- Stats row -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="stat-card animate-fade-in" style="animation-delay: 0ms">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-seait-400 to-seait-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Karma</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ number_format($karma) }}</p>
                    <p class="text-xs text-gray-400 mt-1 dark:text-gray-500">points earned</p>
                </div>

                <div class="stat-card animate-fade-in" style="animation-delay: 100ms">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Badge</span>
                    </div>
                    @php $badge = \App\Domain\Reputation\Enums\BadgeTier::fromKarmaOrNull($karma); @endphp
                    <p class="text-xl font-bold {{ $badge ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400' }}">
                        {{ $badge?->label() ?? '—' }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1 dark:text-gray-500">{{ $badge ? $badge->name : 'No badge yet' }}</p>
                </div>

                <div class="stat-card animate-fade-in" style="animation-delay: 200ms">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-400 to-blue-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Program</span>
                    </div>
                    <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $user->program?->code ?? '—' }}</p>
                    <p class="text-xs text-gray-400 mt-1 dark:text-gray-500">{{ $user->program?->college?->code ?? '' }}</p>
                </div>

                <div class="stat-card animate-fade-in" style="animation-delay: 300ms">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider dark:text-gray-400">Year Level</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $user->year_level ?? '—' }}</p>
                    <p class="text-xs text-gray-400 mt-1 dark:text-gray-500">
                        {{ $user->year_level ? (match($user->year_level) { 1 => '1st', 2 => '2nd', 3 => '3rd', default => $user->year_level . 'th' }) . ' Year' : '' }}
                    </p>
                </div>
            </div>

            <!-- Quick actions -->
            <div>
                <h3 class="section-title mb-4">Quick Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <a href="{{ route('resources.create') }}" class="card-hover group p-6 animate-fade-in" style="animation-delay: 400ms">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-seait-400 to-seait-500 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-seait-600 dark:group-hover:text-seait-400 transition-colors">Upload Resource</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Share reviewers, e-modules, textbooks, and past exams with your peers.</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('requests.create') }}" class="card-hover group p-6 animate-fade-in" style="animation-delay: 500ms">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-400 to-blue-500 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Make a Request</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Need material? Post a request — auto-routed to the right programs.</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('chat.index') }}" class="card-hover group p-6 animate-fade-in" style="animation-delay: 600ms">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-400 to-emerald-500 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">Chat</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Join your program's group chat and connect with classmates.</p>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('requests.index') }}" class="card-hover group p-6 animate-fade-in" style="animation-delay: 700ms">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-500 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">Need a resource? Post a request</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Describe what you need — it gets routed to programs that teach that subject.</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Getting started -->
            <div class="card p-6 animate-fade-in" style="animation-delay: 700ms">
                <h3 class="section-title mb-4">How StudHub Works</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 mt-0.5 rounded-lg bg-seait-50 text-seait-600 dark:bg-seait-800/30 dark:text-seait-400 flex items-center justify-center font-bold text-sm">1</div>
                        <div>
                            <p class="font-semibold text-sm text-gray-800 dark:text-gray-200">Share resources</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Upload reviewers, e-modules, textbooks, and past exams tagged by subject. Build karma as your resources get saved and lent.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 mt-0.5 rounded-lg bg-blue-50 text-blue-600 dark:bg-blue-800/30 dark:text-blue-400 flex items-center justify-center font-bold text-sm">2</div>
                        <div>
                            <p class="font-semibold text-sm text-gray-800 dark:text-gray-200">Request & get notified</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Post what you need. StudHub auto-routes your request to programs that teach the subject you need help with.</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 mt-0.5 rounded-lg bg-amber-50 text-amber-600 dark:bg-amber-800/30 dark:text-amber-400 flex items-center justify-center font-bold text-sm">3</div>
                        <div>
                            <p class="font-semibold text-sm text-gray-800 dark:text-gray-200">Earn karma & badges</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Fulfill requests, lend materials, and earn points. Unlock badges that show your reputation across the platform.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
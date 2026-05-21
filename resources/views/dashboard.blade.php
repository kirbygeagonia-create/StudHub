<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <p class="text-sm font-medium text-gray-500">Karma</p>
                        <p class="text-2xl font-bold text-indigo-600">{{ number_format(Auth::user()?->karma ?? 0) }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <p class="text-sm font-medium text-gray-500">Badge</p>
                        <p class="text-2xl font-bold text-yellow-600">
                            @php
                                $badge = \App\Domain\Reputation\Enums\BadgeTier::fromKarmaOrNull(Auth::user()?->karma ?? 0);
                            @endphp
                            {{ $badge?->label() ?? 'None' }}
                        </p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <p class="text-sm font-medium text-gray-500">Program</p>
                        <p class="text-lg font-semibold text-gray-800">{{ Auth::user()?->program?->code ?? '—' }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <p class="text-sm font-medium text-gray-500">Year Level</p>
                        <p class="text-2xl font-bold text-gray-800">{{ Auth::user()?->year_level ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="{{ route('resources.create') }}"
                   class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <svg class="h-10 w-10 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 10.5v6m3-3H9m4.06-7.19l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/>
                            </svg>
                            <div class="ms-4">
                                <h3 class="text-lg font-semibold text-gray-800">Upload Resource</h3>
                                <p class="text-sm text-gray-500 mt-1">Share reviewers, e-modules, textbooks</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('requests.create') }}"
                   class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <svg class="h-10 w-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                            <div class="ms-4">
                                <h3 class="text-lg font-semibold text-gray-800">Make a Request</h3>
                                <p class="text-sm text-gray-500 mt-1">Ask for materials across programs</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('chat.index') }}"
                   class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex items-center">
                            <svg class="h-10 w-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M8.625 9.75a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 0 1 .778-.332 48.294 48.294 0 0 0 5.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
                            </svg>
                            <div class="ms-4">
                                <h3 class="text-lg font-semibold text-gray-800">Chat</h3>
                                <p class="text-sm text-gray-500 mt-1">Join your program's group chat</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Getting started card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Getting Started</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                        <div class="flex items-start">
                            <span class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs me-3">1</span>
                            <span><strong>Upload</strong> your reviewers and e-modules tagged by subject so others can find them.</span>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs me-3">2</span>
                            <span><strong>Post a request</strong> for the material you need — it gets auto-routed to programs that teach the subject.</span>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs me-3">3</span>
                            <span><strong>Lend and earn karma</strong> — fulfill a request, get karma points, unlock badges.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
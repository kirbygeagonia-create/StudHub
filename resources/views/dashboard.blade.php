<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight dark:text-gray-100">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Onboarding modal -->
    <div x-data="{ showOnboarding: {{ Auth::user()?->hasCompletedOnboarding() ? 'false' : 'true' }} }"
         x-show="showOnboarding"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div @click.away="showOnboarding = false" class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl max-w-lg w-full mx-4 p-8">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-2">Welcome to StudHub!</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Here's how to get started in 3 simple steps:</p>

            <div class="space-y-4">
                <div class="flex items-start">
                    <span class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-seait-50 dark:bg-seait-800 text-seait-600 dark:text-seait-100 font-bold text-sm me-3">1</span>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">Upload a resource</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Share your reviewers, e-modules, and textbooks tagged by subject so your batchmates can find them.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <span class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-seait-50 dark:bg-seait-800 text-seait-600 dark:text-seait-100 font-bold text-sm me-3">2</span>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">Join the chat</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Connect with your program's group chat. Use @mentions to get someone's attention across programs.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <span class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-seait-50 dark:bg-seait-800 text-seait-600 dark:text-seait-100 font-bold text-sm me-3">3</span>
                    <div>
                        <p class="font-semibold text-gray-800 dark:text-gray-200">Make a request</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Need something specific? Post a request — it gets auto-routed to programs that teach that subject.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button @click="showOnboarding = false"
                        class="px-6 py-2 bg-seait-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-seait-600">
                    Got it!
                </button>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Karma</p>
                        <p class="text-2xl font-bold text-seait-500">{{ number_format(Auth::user()?->karma ?? 0) }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Badge</p>
                        <p class="text-2xl font-bold text-yellow-600">
                            @php
                                $badge = \App\Domain\Reputation\Enums\BadgeTier::fromKarmaOrNull(Auth::user()?->karma ?? 0);
                            @endphp
                            {{ $badge?->label() ?? 'None' }}
                        </p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Program</p>
                        <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ Auth::user()?->program?->code ?? '—' }}</p>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-4">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Year Level</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ Auth::user()?->year_level ?? '—' }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <a href="{{ route('resources.create') }}"
                   class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow dark:bg-gray-800">
                    <div class="p-6">
                        <div class="flex items-center">
                            <x-heroicon-o-plus-circle class="h-10 w-10 text-seait-400"/>
                            <div class="ms-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Upload Resource</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Share reviewers, e-modules, textbooks</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('requests.create') }}"
                   class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow dark:bg-gray-800">
                    <div class="p-6">
                        <div class="flex items-center">
                            <x-heroicon-o-magnifying-glass class="h-10 w-10 text-emerald-500"/>
                            <div class="ms-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Make a Request</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Ask for materials across programs</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a href="{{ route('chat.index') }}"
                   class="block bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow dark:bg-gray-800">
                    <div class="p-6">
                        <div class="flex items-center">
                            <x-heroicon-o-chat-bubble-left-right class="h-10 w-10 text-blue-500"/>
                            <div class="ms-4">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Chat</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Join your program's group chat</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Getting started card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Getting Started</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 dark:text-gray-400">
                        <div class="flex items-start">
                            <span class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-seait-50 dark:bg-seait-800 text-seait-600 dark:text-seait-100 font-bold text-xs me-3">1</span>
                            <span><strong>Upload</strong> your reviewers and e-modules tagged by subject so others can find them.</span>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-seait-50 dark:bg-seait-800 text-seait-600 dark:text-seait-100 font-bold text-xs me-3">2</span>
                            <span><strong>Post a request</strong> for the material you need — it gets auto-routed to programs that teach the subject.</span>
                        </div>
                        <div class="flex items-start">
                            <span class="flex-shrink-0 flex items-center justify-center w-6 h-6 rounded-full bg-seait-50 dark:bg-seait-800 text-seait-600 dark:text-seait-100 font-bold text-xs me-3">3</span>
                            <span><strong>Lend and earn karma</strong> — fulfill a request, get karma points, unlock badges.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
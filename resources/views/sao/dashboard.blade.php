<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('SAO Dashboard — SEAIT Campus Administration') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
                <div class="stat-card">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Users</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $totalUsers }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Students</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $totalStudents }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Moderators</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $totalModerators }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Program Heads</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $totalProgramHeads }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Deans</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $totalDeans }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Open Reports</p>
                    <p class="text-xl font-bold text-red-600 dark:text-red-400">{{ $openReports }}</p>
                </div>
            </div>

            <!-- Alerts -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @if ($unreadFeedback > 0)
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-xl p-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.502-1.275.722-1.845l-6.928-5.013c-.752-.545-1.792-.545-2.544 0L5.094 17.155c-.78.57-.332 1.845.722 1.845z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-amber-700 dark:text-amber-300">{{ $unreadFeedback }} unread escalated feedback</p>
                                <a href="{{ route('sao.feedback') }}" class="text-xs text-amber-600 underline">View feedback</a>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($openReports > 0)
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-xl p-4">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-red-700 dark:text-red-300">{{ $openReports }} open reports across campus</p>
                                <a href="{{ route('moderation.dashboard') }}" class="text-xs text-red-600 underline">View reports</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Colleges Overview -->
            <div class="card p-6">
                <h3 class="section-title mb-4">Colleges Overview</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($colleges as $college)
                        <div class="bg-gray-50 dark:bg-navy-800/60 rounded-xl p-4 border border-gray-100 dark:border-navy-700/30">
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $college->code }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $college->name }}</p>
                            <div class="flex gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span>{{ $college->program_count ?? 0 }} programs</span>
                                <span>{{ $college->active_user_count ?? 0 }} active users</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-400 col-span-full text-center py-4">No colleges found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
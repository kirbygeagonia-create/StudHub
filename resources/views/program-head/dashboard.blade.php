<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Program Head Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="stat-card">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Open Reports <span class="text-xs opacity-60">(campus-wide)</span></p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $openReports }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Moderators</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalModerators }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Active Users</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $activeUsers }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Resources</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalResources }}</p>
                </div>
            </div>

            <!-- Unread Feedback Alert -->
            @if ($unreadFeedback > 0)
                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/50 rounded-xl p-4 mb-6">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.502-1.275.722-1.845l-6.928-5.013c-.752-.545-1.792-.545-2.544 0L5.094 17.155c-.78.57-.332 1.845.722 1.845z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-amber-700 dark:text-amber-300">{{ $unreadFeedback }} unread feedback items</p>
                            <a href="{{ route('program_head.feedback') }}" class="text-xs text-amber-600 hover:text-amber-700 dark:text-amber-400 underline">View feedback</a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Moderators List -->
            <div class="card p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="section-title">Program Moderators</h3>
                    <a href="{{ route('moderation.dashboard') }}" class="text-sm text-seait-600 hover:text-seait-700 dark:text-seait-400 font-medium">Manage Reports →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-navy-700/50">
                                <th class="pb-2 font-medium">User</th>
                                <th class="pb-2 font-medium">Program</th>
                                <th class="pb-2 font-medium">Assigned</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($moderators as $mod)
                                <tr class="border-b border-gray-50 dark:border-navy-700/30">
                                    <td class="py-2.5">{{ $mod->user?->preferredDisplayName() ?? 'Unknown' }}</td>
                                    <td class="py-2.5">{{ $mod->program?->code ?? 'N/A' }}</td>
                                    <td class="py-2.5 text-gray-500">{{ $mod->created_at->diffForHumans() }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-gray-400">No moderators assigned yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $moderators->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
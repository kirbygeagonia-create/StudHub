<x-app-layout>
    <x-page-header title="Super Admin Dashboard">
        <x-slot name="actions">
            <a href="{{ route('admin.dashboard') }}" class="btn-secondary text-xs">Admin Dashboard</a>
        </x-slot>
    </x-page-header>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="stat-card">
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalUsers }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Users</p>
                </div>
                <div class="stat-card">
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalAdmins }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Program Heads / Deans</p>
                </div>
                <div class="stat-card">
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalModerators }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Moderators</p>
                </div>
                <div class="stat-card">
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $allFeedback->total() }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Feedback</p>
                </div>
            </div>

            {{-- Open Reports --}}
            <div class="card p-6">
                <h3 class="section-title mb-4">Open Reports</h3>
                @if ($openReports->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">No open reports.</p>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-navy-700/50">
                        @foreach ($openReports as $report)
                            <div class="py-3 flex items-start justify-between">
                                <div class="text-sm">
                                    <span class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $report->reporter?->display_name ?: $report->reporter?->name ?: 'Unknown' }}
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400"> reported </span>
                                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ class_basename($report->reported_type ?? '') }}</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-500 ml-2">{{ $report->created_at->diffForHumans() }}</span>
                                </div>
                                <a href="{{ route('moderation.dashboard') }}" class="text-xs text-seait-500 hover:text-seait-600">View</a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- All Feedback --}}
            <div class="card overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 dark:border-navy-700/50">
                    <h3 class="section-title mb-0">All Feedback</h3>
                </div>
                @if ($allFeedback->isEmpty())
                    <div class="p-12 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No feedback submitted yet.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-navy-700/50">
                        @foreach ($allFeedback as $fb)
                            @php
                                $typeStyle = match($fb->type) {
                                    'bug'     => 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300',
                                    'feature' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300',
                                    'praise'  => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300',
                                    default   => 'bg-gray-100 text-gray-700 dark:bg-navy-700 dark:text-gray-300',
                                };
                            @endphp
                            <div class="px-6 py-4">
                                <div class="flex items-start justify-between gap-4 mb-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $fb->user?->display_name ?: $fb->user?->name ?: 'Anonymous' }}
                                        </span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $fb->user?->email }}</span>
                                        @if ($fb->user?->program)
                                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-gray-100 dark:bg-navy-700 text-gray-500 dark:text-gray-400">
                                                {{ $fb->user->program->code }}
                                            </span>
                                        @endif
                                    </div>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $typeStyle }}">
                                        {{ ucfirst($fb->type ?? 'feedback') }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $fb->body }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $fb->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-navy-700/50">
                        {{ $allFeedback->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
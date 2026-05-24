<x-app-layout>
    <x-page-header title="Moderation Dashboard" />

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="card p-6">
                <h3 class="section-title mb-4">
                    Open Reports ({{ $reports->total() }})
                </h3>

                @if ($reports->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">No open reports.</p>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-navy-700">
                        @foreach ($reports as $report)
                            <div class="py-3 space-y-2">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ ucfirst($report->reported_type) }} reported
                                            @if ($report->reason)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">for {{ str_replace('_', ' ', $report->reason) }}</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Reported by {{ $report->reporter?->preferredDisplayName() ?? 'Unknown' }}
                                            on {{ $report->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300">
                                        Open
                                    </span>
                                </div>

                                @if ($report->notes)
                                    <p class="text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-navy-800 rounded p-2">{{ $report->notes }}</p>
                                @endif

                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('moderation.resolve', $report) }}" class="inline-flex items-center gap-1">
                                        @csrf
                                        <input type="hidden" name="resolution" value="actioned">
                                        <input type="text" name="resolution_note" maxlength="1000"
                                               class="text-xs input-field w-40" placeholder="Note (optional)">
                                        <button type="submit"
                                                class="btn-primary text-xs !px-2 !py-1 !bg-emerald-500 hover:!bg-emerald-600">
                                            Action
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('moderation.resolve', $report) }}" class="inline-flex">
                                        @csrf
                                        <input type="hidden" name="resolution" value="dismissed">
                                        <button type="submit"
                                                class="btn-secondary text-xs !px-2 !py-1">
                                            Dismiss
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>

            <div class="card p-6">
                <h3 class="section-title mb-4">Suspend User</h3>
                <form method="POST" action="{{ route('moderation.suspend') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label for="suspend_user_id" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">User ID</label>
                        <input type="number" id="suspend_user_id" name="user_id" required
                               class="text-sm input-field w-full">
                    </div>
                    <div>
                        <label for="suspend_days" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Days (1-90)</label>
                        <input type="number" id="suspend_days" name="days" value="7" required min="1" max="90"
                               class="text-sm input-field w-32">
                    </div>
                    <div>
                        <label for="suspend_reason" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Reason (optional)</label>
                        <input type="text" id="suspend_reason" name="reason" maxlength="500"
                               class="text-sm input-field w-full">
                    </div>
                    <button type="submit"
                            class="btn-primary text-xs !px-3 !py-1.5 !bg-red-500 hover:!bg-red-600">
                        Suspend
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
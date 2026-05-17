<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Moderation Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">
                    Open Reports ({{ $reports->total() }})
                </h3>

                @if ($reports->isEmpty())
                    <p class="text-sm text-gray-500">No open reports.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($reports as $report)
                            <div class="py-3 space-y-2">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ ucfirst($report->reported_type) }} reported
                                            @if ($report->reason)
                                                <span class="text-xs text-gray-500">for {{ str_replace('_', ' ', $report->reason) }}</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Reported by {{ $report->reporter?->preferredDisplayName() ?? 'Unknown' }}
                                            on {{ $report->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                        Open
                                    </span>
                                </div>

                                @if ($report->notes)
                                    <p class="text-xs text-gray-600 bg-gray-50 rounded p-2">{{ $report->notes }}</p>
                                @endif

                                <div class="flex gap-2">
                                    <form method="POST" action="{{ route('moderation.resolve', $report) }}" class="inline-flex items-center gap-1">
                                        @csrf
                                        <input type="hidden" name="resolution" value="actioned">
                                        <input type="text" name="resolution_note" maxlength="1000"
                                               class="text-xs border-gray-300 rounded-md shadow-sm w-40" placeholder="Note (optional)">
                                        <button type="submit"
                                                class="px-2 py-1 bg-green-600 border border-transparent rounded text-xs font-semibold text-white uppercase tracking-widest hover:bg-green-700">
                                            Action
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('moderation.resolve', $report) }}" class="inline-flex">
                                        @csrf
                                        <input type="hidden" name="resolution" value="dismissed">
                                        <button type="submit"
                                                class="px-2 py-1 bg-gray-400 border border-transparent rounded text-xs font-semibold text-white uppercase tracking-widest hover:bg-gray-500">
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

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Suspend User</h3>
                <form method="POST" action="{{ route('moderation.suspend') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label for="suspend_user_id" class="block text-xs text-gray-600 mb-1">User ID</label>
                        <input type="number" id="suspend_user_id" name="user_id" required
                               class="text-sm border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <div>
                        <label for="suspend_days" class="block text-xs text-gray-600 mb-1">Days (1-90)</label>
                        <input type="number" id="suspend_days" name="days" value="7" required min="1" max="90"
                               class="text-sm border-gray-300 rounded-md shadow-sm w-32">
                    </div>
                    <div>
                        <label for="suspend_reason" class="block text-xs text-gray-600 mb-1">Reason (optional)</label>
                        <input type="text" id="suspend_reason" name="reason" maxlength="500"
                               class="text-sm border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <button type="submit"
                            class="px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Suspend
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
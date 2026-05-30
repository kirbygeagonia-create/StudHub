<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('SAO — Campus Feedback') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6">
                @forelse ($feedbacks as $feedback)
                    <div class="border-b border-gray-100 dark:border-navy-700/30 last:border-b-0 py-4">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $feedback->user?->preferredDisplayName() ?? 'Unknown' }}</span>
                                    <span class="text-xs text-gray-400">{{ $feedback->created_at->diffForHumans() }}</span>
                                    <span class="badge {{ $feedback->status === 'open' ? 'badge-amber' : ($feedback->status === 'resolved' ? 'badge-emerald' : 'badge-seait') }}">
                                        {{ $feedback->status }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $feedback->body }}</p>
                                @if ($feedback->status === 'open')
                                    <form method="POST" action="{{ route('sao.feedback.resolve', $feedback) }}" class="mt-2">
                                        @csrf
                                        <button type="submit" class="text-xs btn-primary !px-3 !py-1">Resolve</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 py-8">No escalated feedback yet.</p>
                @endforelse
                <div class="mt-4">
                    {{ $feedbacks->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@extends('layouts.admin')

@section('sidebar')
    @include('program-head._sidebar')
@endsection

@section('pageHeader')
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                Program Feedback
            </h1>
        </div>
    </div>
@endsection

@section('content')
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
                            <div class="flex gap-2 mt-2">
                                <form method="POST" action="{{ route('program_head.feedback.resolve', $feedback) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs btn-primary !px-3 !py-1">Resolve</button>
                                </form>
                                <form method="POST" action="{{ route('program_head.feedback.escalate', $feedback) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs btn-secondary !px-3 !py-1">Escalate to Dean</button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-400 py-8">No feedback for your program yet.</p>
        @endforelse
        <div class="mt-4">
            {{ $feedbacks->links() }}
        </div>
    </div>
@endsection
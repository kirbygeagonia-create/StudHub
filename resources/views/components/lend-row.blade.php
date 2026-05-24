@props(['lend', 'variant' => 'lent'])

<div class="py-3 flex items-start justify-between">
    <div>
        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
            {{ $lend->resource?->title ?? 'Unknown resource' }}
        </p>
        <p class="text-xs text-gray-500 dark:text-gray-400">
            @if ($variant === 'lent')
                Lent to {{ $lend->toUser?->preferredDisplayName() ?? 'Unknown' }}
            @else
                Borrowed from {{ $lend->fromUser?->preferredDisplayName() ?? 'Unknown' }}
            @endif
            on {{ $lend->lent_at->format('M d, Y') }}
        </p>
        @if ($lend->return_by)
            <p class="text-xs {{ $lend->isOverdue() ? 'text-red-600 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">
                Return by {{ $lend->return_by->format('M d, Y') }}
                @if ($lend->isOverdue())
                    <span class="font-semibold">(Overdue)</span>
                @endif
            </p>
        @endif
        @if ($lend->isEscalated())
            <p class="text-xs text-red-600 dark:text-red-400 font-semibold mt-1 flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                Escalated — moderator has been notified
            </p>
        @endif
    </div>
    <div class="flex items-center gap-2">
        @if ($lend->isReturned())
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                Returned {{ $lend->returned_at->format('M d') }}
            </span>
        @elseif ($lend->isEscalated())
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                Escalated
            </span>
        @elseif ($variant === 'borrowed')
            <form method="POST" action="{{ route('lends.return', $lend) }}" class="inline-flex items-center gap-1">
                @csrf
                <select name="condition" class="text-xs input-field">
                    <option value="">No condition</option>
                    @foreach (\App\Domain\Lends\Enums\LendCondition::cases() as $cond)
                        <option value="{{ $cond->value }}">{{ $cond->label() }}</option>
                    @endforeach
                </select>
                <button type="submit"
                        class="px-2 py-1 bg-green-600 border border-transparent rounded text-xs font-semibold text-white uppercase tracking-widest hover:bg-green-700">
                    Return
                </button>
            </form>
        @else
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                Active
            </span>
        @endif
    </div>
</div>
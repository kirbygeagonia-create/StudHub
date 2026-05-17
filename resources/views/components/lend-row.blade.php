@props(['lend', 'variant' => 'lent'])

<div class="py-3 flex items-start justify-between">
    <div>
        <p class="text-sm font-medium text-gray-900">
            {{ $lend->resource?->title ?? 'Unknown resource' }}
        </p>
        <p class="text-xs text-gray-500">
            @if ($variant === 'lent')
                Lent to {{ $lend->toUser?->preferredDisplayName() ?? 'Unknown' }}
            @else
                Borrowed from {{ $lend->fromUser?->preferredDisplayName() ?? 'Unknown' }}
            @endif
            on {{ $lend->lent_at->format('M d, Y') }}
        </p>
        @if ($lend->return_by)
            <p class="text-xs {{ $lend->isOverdue() ? 'text-red-600' : 'text-gray-500' }}">
                Return by {{ $lend->return_by->format('M d, Y') }}
                @if ($lend->isOverdue())
                    <span class="font-semibold">(Overdue)</span>
                @endif
            </p>
        @endif
    </div>
    <div class="flex items-center gap-2">
        @if ($lend->isReturned())
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                Returned {{ $lend->returned_at->format('M d') }}
            </span>
        @elseif ($variant === 'borrowed')
            <form method="POST" action="{{ route('lends.return', $lend) }}" class="inline-flex items-center gap-1">
                @csrf
                <select name="condition" class="text-xs border-gray-300 rounded-md shadow-sm">
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
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                Active
            </span>
        @endif
    </div>
</div>
@props(['lend', 'variant' => 'lent'])

<div class="card p-5 flex flex-col gap-4 {{ $lend->isOverdue() ? 'border-red-200 dark:border-red-800/40 ring-1 ring-red-100 dark:ring-red-900/20' : '' }}">
    <!-- Header: Resource Title -->
    <div class="flex items-start justify-between gap-3">
        <div class="flex-1 min-w-0">
            <h4 class="text-base font-semibold text-gray-900 dark:text-gray-100 leading-tight">
                {{ $lend->resource?->title ?? 'Unknown resource' }}
            </h4>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                @if ($variant === 'lent')
                    Lent to <span class="font-medium text-gray-700 dark:text-gray-300">{{ $lend->toUser?->preferredDisplayName() ?? 'Unknown' }}</span>
                @else
                    Borrowed from <span class="font-medium text-gray-700 dark:text-gray-300">{{ $lend->fromUser?->preferredDisplayName() ?? 'Unknown' }}</span>
                @endif
            </p>
        </div>
        @if ($lend->isReturned())
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 flex-shrink-0">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Returned
            </span>
        @elseif ($lend->isEscalated())
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300 flex-shrink-0">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                Escalated
            </span>
        @elseif ($lend->isOverdue())
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300 flex-shrink-0">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Overdue
            </span>
        @else
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300 flex-shrink-0">
                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Active
            </span>
        @endif
    </div>

    <!-- Details -->
    <div class="flex flex-col gap-1.5 text-sm text-gray-600 dark:text-gray-400">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <span>Lent on <span class="font-medium text-gray-800 dark:text-gray-200">{{ $lend->lent_at->format('M d, Y') }}</span></span>
        </div>
        @if ($lend->return_by)
            <div class="flex items-center gap-2 {{ $lend->isOverdue() ? 'text-red-600 dark:text-red-400' : '' }}">
                <svg class="w-4 h-4 flex-shrink-0 {{ $lend->isOverdue() ? 'text-red-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Return by <span class="font-medium {{ $lend->isOverdue() ? 'text-red-700 dark:text-red-300' : 'text-gray-800 dark:text-gray-200' }}">{{ $lend->return_by->format('M d, Y') }}</span></span>
                @if ($lend->isOverdue())
                    <span class="text-xs font-semibold text-red-600 dark:text-red-400">({{ $lend->return_by->diffForHumans() }} overdue)</span>
                @endif
            </div>
        @endif
        @if ($lend->isReturned())
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Returned on <span class="font-medium text-gray-800 dark:text-gray-200">{{ $lend->returned_at->format('M d, Y') }}</span></span>
            </div>
        @endif
    </div>

    @if ($lend->isEscalated())
        <div class="text-xs text-red-600 dark:text-red-400 font-medium flex items-center gap-1.5 bg-red-50 dark:bg-red-900/10 rounded-lg px-3 py-2">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            Moderator has been notified
        </div>
    @endif

    <!-- Actions -->
    <div class="pt-2 mt-auto">
        @if ($lend->isReturned())
            <div class="flex items-center gap-2 text-sm text-emerald-600 dark:text-emerald-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium">Resource returned successfully</span>
            </div>
        @elseif ($lend->isEscalated())
            <div class="text-sm text-red-600 dark:text-red-400">
                <span class="font-medium">Awaiting moderator resolution</span>
            </div>
        @elseif ($variant === 'borrowed')
            <form method="POST" action="{{ route('lends.return', $lend) }}" class="flex flex-col gap-2">
                @csrf
                <div class="flex items-center gap-2">
                    <select name="condition" class="text-xs input-field flex-1">
                        <option value="">No condition</option>
                        @foreach (\App\Domain\Lends\Enums\LendCondition::cases() as $cond)
                            <option value="{{ $cond->value }}">{{ $cond->label() }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-primary text-xs !py-1.5 !px-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        Return
                    </button>
                </div>
            </form>
        @else
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Awaiting return from borrower</span>
            </div>
        @endif
    </div>
</div>

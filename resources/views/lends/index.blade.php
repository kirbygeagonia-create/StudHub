<x-app-layout>
    <x-page-header title="My Lends" />

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- LENT OUT --}}
                <div class="card p-6 flex flex-col min-h-[24rem]">
                    <h3 class="section-title mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-seait-100 dark:bg-seait-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                        </span>
                        Lent Out
                        @if (!$lentOut->isEmpty())
                            <span class="ml-auto text-xs font-medium px-2 py-0.5 rounded-full bg-seait-100 text-seait-700 dark:bg-seait-900/30 dark:text-seait-300">
                                {{ $lentOut->total() }}
                            </span>
                        @endif
                    </h3>

                    @if ($lentOut->isEmpty())
                        <div class="flex-1 flex flex-col items-center justify-center text-center py-8">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Nothing lent out yet</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Resources you lend will appear here.</p>
                        </div>
                    @else
                        <div class="space-y-3 flex-1">
                            @foreach ($lentOut as $lend)
                                <x-lend-row :lend="$lend" variant="lent" />
                            @endforeach
                        </div>
                        <div class="mt-4">{{ $lentOut->links() }}</div>
                    @endif
                </div>

                {{-- BORROWED --}}
                <div class="card p-6 flex flex-col min-h-[24rem]">
                    <h3 class="section-title mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                            </svg>
                        </span>
                        Borrowed
                        @if (!$borrowed->isEmpty())
                            <span class="ml-auto text-xs font-medium px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                {{ $borrowed->total() }}
                            </span>
                        @endif
                    </h3>

                    @if ($borrowed->isEmpty())
                        <div class="flex-1 flex flex-col items-center justify-center text-center py-8">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Nothing borrowed yet</p>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Resources you borrow will appear here.</p>
                        </div>
                    @else
                        <div class="space-y-3 flex-1">
                            @foreach ($borrowed as $lend)
                                <x-lend-row :lend="$lend" variant="borrowed" />
                            @endforeach
                        </div>
                        <div class="mt-4">{{ $borrowed->links() }}</div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

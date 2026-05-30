<x-app-layout>
    <x-page-header title="My Lends" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-2">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-seait-500 dark:hover:text-seait-400 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Dashboard
        </a>
    </div>

    {{-- How lending works guidance --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
        <div x-data="{ showGuide: {{ $incomingRequests->isNotEmpty() ? 'true' : 'false' }} }"
             class="card p-4 border-l-4 border-seait-400 bg-seait-50/40 dark:bg-seait-900/10 dark:border-seait-500">
            <button @click="showGuide = !showGuide" type="button"
                    class="flex items-center gap-2 w-full text-left">
                <div class="w-8 h-8 rounded-lg bg-seait-100 dark:bg-seait-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z"/></svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">How lending works</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">A quick guide to lending and borrowing on StudHub</p>
                </div>
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="showGuide && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="showGuide" x-collapse class="mt-3 pt-3 border-t border-seait-200 dark:border-seait-800/30">
                <div class="grid sm:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-400">
                    <div class="p-3 rounded-xl bg-white dark:bg-navy-800/50">
                        <p class="font-semibold text-gray-800 dark:text-gray-200 mb-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-seait-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                            Lending
                        </p>
                        <p class="text-xs">When someone requests a resource and you have it, make an offer on their request. If they accept, go to the request page and click <strong>"Record Lend"</strong> to log it here.</p>
                    </div>
                    <div class="p-3 rounded-xl bg-white dark:bg-navy-800/50">
                        <p class="font-semibold text-gray-800 dark:text-gray-200 mb-1 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            Borrowing
                        </p>
                        <p class="text-xs">When someone accepts your offer on a request you posted, the requester records the lend. Both parties can mark items as returned when done.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Incoming Borrow Requests --}}
            @if ($incomingRequests->isNotEmpty())
                <div class="card p-6 mb-6 border-l-4 border-amber-400">
                    <h3 class="section-title mb-4 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </span>
                        Incoming Requests
                        <span class="ml-auto text-xs font-medium px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                            {{ $incomingRequests->count() }}
                        </span>
                    </h3>
                    <div class="space-y-3">
                        @foreach ($incomingRequests as $req)
                            @php
                                $myOffer = $req->offers->first();
                            @endphp
                            <div class="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-navy-700/50 transition-colors border border-gray-100 dark:border-navy-700/50">
                                <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                        {{ $req->subject->code }} — {{ $req->type_wanted }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Requested by {{ $req->requester->display_name ?? $req->requester->name }}
                                        @if ($myOffer && $myOffer->resource)
                                            · Your offer: {{ $myOffer->resource->title }}
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($req->status->value === 'matched')
                                        <a href="{{ route('requests.show', $req) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            Record Lend
                                        </a>
                                    @else
                                        <a href="{{ route('requests.show', $req) }}"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-seait-500 hover:bg-seait-600 text-white text-xs font-semibold rounded-lg transition-colors shadow-sm">
                                            View Request
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

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

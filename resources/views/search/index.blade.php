<x-app-layout>
    <x-page-header title="{{ __('Search') }}" />

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mt-2">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-seait-500 dark:hover:text-seait-400 transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Dashboard
        </a>
    </div>

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Search bar --}}
            <form method="GET" action="{{ route('search') }}">
                <div class="flex gap-3">
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input type="text" name="q" value="{{ $query }}"
                               placeholder="Search resources, requests, messages…"
                               autofocus
                               class="input-field w-full pl-10 text-sm">
                    </div>
                    <button type="submit" class="btn-primary text-sm">Search</button>
                </div>
            </form>

            @if ($query !== '')
                @php $total = $resources->count() + $requests->count() + $messages->count(); @endphp

                @if ($total === 0)
                    <div class="card p-12 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">No results for "{{ $query }}"</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Try different keywords or check your spelling.</p>
                    </div>
                @else
                    <p class="text-xs text-gray-500 dark:text-gray-400 px-1">
                        <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $total }}</span>
                        {{ Str::plural('result', $total) }} for "<span class="font-semibold text-gray-700 dark:text-gray-300">{{ $query }}</span>"
                    </p>

                    @if ($resources->isNotEmpty())
                        <div>
                            <h3 class="section-title mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-seait-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Resources
                                <span class="text-xs font-medium text-gray-400">({{ $resources->count() }})</span>
                            </h3>
                            <div class="space-y-2">
                                @foreach ($resources as $resource)
                                    <a href="{{ route('resources.show', $resource) }}" class="card card-hover p-4 flex items-center gap-3 block">
                                        <div class="w-9 h-9 rounded-xl bg-seait-100 dark:bg-seait-900/30 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $resource->title }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $resource->subject?->code }} · {{ $resource->type->label() }} · by {{ $resource->owner?->display_name ?? $resource->owner?->name }}</p>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($requests->isNotEmpty())
                        <div>
                            <h3 class="section-title mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                Requests
                                <span class="text-xs font-medium text-gray-400">({{ $requests->count() }})</span>
                            </h3>
                            <div class="space-y-2">
                                @foreach ($requests as $request)
                                    <a href="{{ route('requests.show', $request) }}" class="card card-hover p-4 flex items-center gap-3 block">
                                        <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">{{ $request->subject?->code ?? '' }} — {{ $request->type_wanted }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $request->status->label() }} · by {{ $request->requester?->display_name ?? $request->requester?->name }}</p>
                                        </div>
                                        <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($messages->isNotEmpty())
                        <div>
                            <h3 class="section-title mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                Chat Messages
                                <span class="text-xs font-medium text-gray-400">({{ $messages->count() }})</span>
                            </h3>
                            <div class="space-y-2">
                                @foreach ($messages as $message)
                                    <a href="{{ route('chat.index') }}" class="card card-hover p-4 flex items-start gap-3 block">
                                        <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">{{ $message->body }}</p>
                                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $message->sender?->display_name ?? $message->sender?->name }} · {{ $message->created_at->diffForHumans() }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
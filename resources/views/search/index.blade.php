<x-app-layout>
    <x-page-header title="{{ __('Search') }}" />

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('search') }}" class="mb-8">
                <div class="flex gap-3">
                    <input type="text" name="q" value="{{ $query }}"
                           placeholder="Search resources, requests, messages..."
                           class="flex-1 input-field text-sm">
                    <button type="submit"
                            class="btn-primary text-xs">
                        Search
                    </button>
                </div>
            </form>

            @if ($query !== '')
                @php
                    $total = $resources->count() + $requests->count() + $messages->count();
                @endphp

                @if ($total === 0)
                    <p class="text-gray-500 dark:text-gray-400 text-center py-8">No results found for "{{ $query }}".</p>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">{{ $total }} result(s) for "{{ $query }}"</p>

                    @if ($resources->isNotEmpty())
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-3">Resources</h3>
                        <div class="space-y-3 mb-8">
                            @foreach ($resources as $resource)
                                <a href="{{ route('resources.show', $resource) }}"
                                   class="block p-4 bg-white dark:bg-navy-800 rounded-lg border border-gray-200 dark:border-navy-700 hover:border-seait-100 transition">
                                    <div class="font-medium text-seait-600">{{ $resource->title }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $resource->subject?->code }} &middot;
                                        {{ $resource->type->label() }} &middot;
                                        by {{ $resource->owner?->display_name ?? $resource->owner?->name }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if ($requests->isNotEmpty())
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-3">Requests</h3>
                        <div class="space-y-3 mb-8">
                            @foreach ($requests as $request)
                                <a href="{{ route('requests.show', $request) }}"
                                   class="block p-4 bg-white dark:bg-navy-800 rounded-lg border border-gray-200 dark:border-navy-700 hover:border-seait-100 transition">
                                    <div class="font-medium text-seait-600">{{ $request->subject?->code ?? '' }} — {{ $request->type_wanted }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $request->subject?->code }} &middot;
                                        {{ $request->status->label() }} &middot;
                                        by {{ $request->requester?->display_name ?? $request->requester?->name }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if ($messages->isNotEmpty())
                        <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-3">Chat Messages</h3>
                        <div class="space-y-3 mb-8">
                            @foreach ($messages as $message)
                                <a href="{{ route('chat.show', $message->room) }}"
                                   class="block p-4 bg-white dark:bg-navy-800 rounded-lg border border-gray-200 dark:border-navy-700 hover:border-seait-100 transition">
                                    <div class="font-medium text-gray-800 dark:text-gray-200">{{ Str::limit($message->body, 120) }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        in {{ $message->room?->title }} &middot;
                                        by {{ $message->sender?->display_name ?? $message->sender?->name }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </div>
</x-app-layout>
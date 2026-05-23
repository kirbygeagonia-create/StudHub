<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Search') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('search') }}" class="mb-8">
                <div class="flex gap-3">
                    <input type="text" name="q" value="{{ $query }}"
                           placeholder="Search resources, requests, messages..."
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-seait-400 focus:ring-seait-400">
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-seait-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-seait-600">
                        Search
                    </button>
                </div>
            </form>

            @if ($query !== '')
                @php
                    $total = $resources->count() + $requests->count() + $messages->count();
                @endphp

                @if ($total === 0)
                    <p class="text-gray-500 text-center py-8">No results found for "{{ $query }}".</p>
                @else
                    <p class="text-sm text-gray-500 mb-6">{{ $total }} result(s) for "{{ $query }}"</p>

                    @if ($resources->isNotEmpty())
                        <h3 class="font-semibold text-lg text-gray-800 mb-3">Resources</h3>
                        <div class="space-y-3 mb-8">
                            @foreach ($resources as $resource)
                                <a href="{{ route('resources.show', $resource) }}"
                                   class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-seait-100 transition">
                                    <div class="font-medium text-seait-600">{{ $resource->title }}</div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ $resource->subject?->code }} &middot;
                                        {{ $resource->type->label() }} &middot;
                                        by {{ $resource->owner?->display_name ?? $resource->owner?->name }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if ($requests->isNotEmpty())
                        <h3 class="font-semibold text-lg text-gray-800 mb-3">Requests</h3>
                        <div class="space-y-3 mb-8">
                            @foreach ($requests as $request)
                                <a href="{{ route('requests.show', $request) }}"
                                   class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-seait-100 transition">
                                    <div class="font-medium text-seait-600">{{ $request->subject?->code ?? '' }} — {{ $request->type_wanted }}</div>
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ $request->subject?->code }} &middot;
                                        {{ $request->status->label() }} &middot;
                                        by {{ $request->requester?->display_name ?? $request->requester?->name }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if ($messages->isNotEmpty())
                        <h3 class="font-semibold text-lg text-gray-800 mb-3">Chat Messages</h3>
                        <div class="space-y-3 mb-8">
                            @foreach ($messages as $message)
                                <a href="{{ route('chat.show', $message->room) }}"
                                   class="block p-4 bg-white rounded-lg border border-gray-200 hover:border-seait-100 transition">
                                    <div class="font-medium text-gray-800">{{ Str::limit($message->body, 120) }}</div>
                                    <div class="text-sm text-gray-500 mt-1">
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
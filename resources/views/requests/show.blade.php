<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $request->subject->code }} — {{ $request->type_wanted }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-3">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Requested by</dt>
                        <dd class="text-gray-900">
                            {{ $request->requester->display_name ?: $request->requester->name }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Subject</dt>
                        <dd class="text-gray-900">{{ $request->subject->code }} · {{ $request->subject->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Type wanted</dt>
                        <dd class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $request->type_wanted)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Urgency</dt>
                        <dd class="text-gray-900">{{ $request->urgency?->label() ?? 'Normal' }}</dd>
                    </div>
                    @if ($request->needed_by)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Needed by</dt>
                            <dd class="text-gray-900">{{ $request->needed_by->format('M d, Y') }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Status</dt>
                        <dd class="text-gray-900">{{ $request->status?->label() ?? 'Open' }}</dd>
                    </div>
                </dl>

                @if ($request->description)
                    <div class="border-t border-gray-100 pt-3">
                        <h3 class="text-xs uppercase tracking-wide text-gray-500 mb-1">Description</h3>
                        <p class="text-sm text-gray-800 whitespace-pre-line">{{ $request->description }}</p>
                    </div>
                @endif
            </div>

            @if ($request->routes->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-semibold text-gray-800 mb-3">Routing</h3>
                    <ul class="space-y-1 text-sm">
                        @foreach ($request->routes as $route)
                            <li class="flex justify-between text-gray-600">
                                <span>{{ $route->program->code }}</span>
                                <span>Score: {{ number_format($route->score, 3) }} · {{ $route->notified_user_count }} notified</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Offers ({{ $request->offers->count() }})</h3>

                @if ($request->offers->isEmpty())
                    <p class="text-sm text-gray-500">No offers yet.</p>
                @else
                    <ul class="divide-y divide-gray-100">
                        @foreach ($request->offers as $offer)
                            <li class="py-3 flex items-start justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $offer->offerer->display_name ?: $offer->offerer->name }}
                                    </p>
                                    @if ($offer->resource)
                                        <p class="text-xs text-gray-500">
                                            Offering: {{ $offer->resource->title }}
                                        </p>
                                    @endif
                                    @if ($offer->message)
                                        <p class="text-xs text-gray-600 mt-0.5">{{ $offer->message }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($request->isOpen() && auth()->id() === $request->requester_user_id && $offer->status?->value === 'pending')
                                        <form method="POST" action="{{ route('requests.offers.accept', [$request, $offer]) }}">
                                            @csrf
                                            <button type="submit"
                                                    class="px-2 py-1 bg-green-600 border border-transparent rounded text-xs font-semibold text-white uppercase tracking-widest hover:bg-green-700">
                                                Accept
                                            </button>
                                        </form>
                                    @endif
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $offer->status?->value === 'accepted' ? 'bg-green-100 text-green-800' : ($offer->status?->value === 'rejected' || $offer->status?->value === 'withdrawn' ? 'bg-gray-100 text-gray-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $offer->status?->label() ?? 'Pending' }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($request->isOpen() && auth()->id() !== $request->requester_user_id)
                    <form method="POST" action="{{ route('requests.offers.store', $request) }}" class="mt-4 space-y-3 border-t border-gray-100 pt-4">
                        @csrf

                        <div>
                            <label for="resource_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Your resource (optional)
                            </label>
                            <select id="resource_id" name="resource_id"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">No resource — just a message</option>
                                @foreach ($userResources as $resource)
                                    <option value="{{ $resource->id }}" @selected(old('resource_id') == $resource->id)>
                                        {{ $resource->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message (optional)</label>
                            <textarea id="message" name="message" rows="2" maxlength="1000"
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                      placeholder="I have a reviewer that covers this topic…">{{ old('message') }}</textarea>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Make offer
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            <div class="text-right">
                <a href="{{ route('requests.index') }}"
                   class="text-sm text-indigo-600 hover:text-indigo-900">
                    &larr; Back to request board
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-page-header title="{{ $request->subject->code }} — {{ $request->type_wanted }}" />

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="card overflow-hidden">
                {{-- Header banner --}}
                <div class="px-6 py-5 border-b border-gray-100 dark:border-navy-700/50">
                    <div class="flex items-start gap-4">
                        @php
                            $urgBg = match ($request->urgency?->value) {
                                'urgent' => 'from-red-400 to-red-600',
                                'low'    => 'from-gray-400 to-gray-500',
                                default  => 'from-amber-400 to-amber-600',
                            };
                            $urgBadge = match ($request->urgency?->value) {
                                'urgent' => 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300',
                                'low'    => 'bg-gray-100 text-gray-600 dark:bg-navy-700 dark:text-gray-400',
                                default  => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300',
                            };
                        @endphp
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $urgBg }} flex items-center justify-center flex-shrink-0 shadow-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <div class="flex-1">
                            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $request->subject->code }} — {{ $request->type_wanted }}
                            </h1>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $urgBadge }}">
                                    {{ $request->urgency?->label() ?? 'Normal' }} urgency
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $request->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-3">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Requested by</dt>
                        <dd class="text-gray-900 dark:text-gray-100">
                            {{ $request->requester->display_name ?: $request->requester->name }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Subject</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $request->subject->code }} · {{ $request->subject->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Type wanted</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ ucfirst(str_replace('_', ' ', $request->type_wanted)) }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Urgency</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $request->urgency?->label() ?? 'Normal' }}</dd>
                    </div>
                    @if ($request->needed_by)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Needed by</dt>
                            <dd class="text-gray-900 dark:text-gray-100">{{ $request->needed_by->format('M d, Y') }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</dt>
                        <dd class="text-gray-900 dark:text-gray-100">{{ $request->status?->label() ?? 'Open' }}</dd>
                    </div>
                </dl>

                @if ($request->description)
                    <div class="border-t border-gray-100 dark:border-navy-700 pt-3">
                        <h3 class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400 mb-1">Description</h3>
                        <p class="text-sm text-gray-800 dark:text-gray-200 whitespace-pre-line">{{ $request->description }}</p>
                    </div>
                @endif
            </div>
            </div>

            @if ($request->routes->isNotEmpty())
                <div class="card p-6">
                    <h3 class="section-title mb-3">Routing</h3>
                    <ul class="space-y-1 text-sm">
                        @foreach ($request->routes as $route)
                            <li class="flex justify-between text-gray-600 dark:text-gray-400">
                                <span>{{ $route->program->code }}</span>
                                <span>Score: {{ number_format($route->score, 3) }} · {{ $route->notified_user_count }} notified</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card p-6">
                <h3 class="section-title mb-3">Offers ({{ $request->offers->count() }})</h3>

                @if ($request->offers->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">No offers yet.</p>
                @else
                    <ul class="divide-y divide-gray-100 dark:divide-navy-700">
                        @foreach ($request->offers as $offer)
                            <li class="py-3 flex items-start justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $offer->offerer->display_name ?: $offer->offerer->name }}
                                    </p>
                                    @if ($offer->resource)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Offering: {{ $offer->resource->title }}
                                        </p>
                                    @endif
                                    @if ($offer->message)
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">{{ $offer->message }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($request->isOpen() && auth()->id() === $request->requester_user_id && $offer->status?->value === 'pending')
                                        <form method="POST" action="{{ route('requests.offers.accept', [$request, $offer]) }}">
                                            @csrf
                                            <button type="submit" onclick="this.disabled=true; this.form.submit();"
                                                    class="btn-primary text-xs !px-2 !py-1 !bg-emerald-500 hover:!bg-emerald-600">
                                                Accept
                                            </button>
                                        </form>
                                    @endif
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                        {{ $offer->status?->value === 'accepted' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-300' : ($offer->status?->value === 'rejected' || $offer->status?->value === 'withdrawn' ? 'bg-gray-100 text-gray-700 dark:bg-navy-700 dark:text-gray-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300') }}">
                                        {{ $offer->status?->label() ?? 'Pending' }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($request->status?->value === 'matched' && auth()->id() === $request->requester_user_id && $request->fulfilled_offer_id)
                    @php
                        $acceptedOffer = $request->offers->firstWhere('id', $request->fulfilled_offer_id);
                    @endphp
                    @if ($acceptedOffer && $acceptedOffer->resource_id)
                        <div class="border-t border-gray-100 dark:border-navy-700 pt-4 mt-4">
                            <h3 class="section-title mb-3">Record as Lend</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                The offer from <strong>{{ $acceptedOffer->offerer->display_name ?: $acceptedOffer->offerer->name }}</strong> has been accepted.
                                Record the resource "{{ $acceptedOffer->resource->title }}" as lent.
                            </p>
                            <form method="POST" action="{{ route('lends.record', [$request, $acceptedOffer]) }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label for="return_by" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Expected return date
                                    </label>
                                    <input type="date" id="return_by" name="return_by"
                                           class="input-field text-sm">
                                </div>
                                <button type="submit" onclick="this.disabled=true; this.form.submit();"
                                        class="btn-primary text-xs">
                                    Record Lend
                                </button>
                            </form>
                        </div>
                    @endif
                @endif

                @if ($request->isOpen() && auth()->id() !== $request->requester_user_id)
                    <form method="POST" action="{{ route('requests.offers.store', $request) }}" class="mt-4 space-y-3 border-t border-gray-100 dark:border-navy-700 pt-4">
                        @csrf

                        <div>
                            <label for="resource_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Your resource (optional)
                            </label>
                            <select id="resource_id" name="resource_id"
                                    class="w-full input-field text-sm">
                                <option value="">No resource — just a message</option>
                                @foreach ($userResources as $resource)
                                    <option value="{{ $resource->id }}" @selected(old('resource_id') == $resource->id)>
                                        {{ $resource->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message (optional)</label>
                            <textarea id="message" name="message" rows="2" maxlength="1000"
                                      class="w-full input-field text-sm"
                                      placeholder="I have a reviewer that covers this topic…">{{ old('message') }}</textarea>
                        </div>

                        <div class="flex justify-end">
<button type="submit" onclick="this.disabled=true; this.form.submit();"
                                        class="btn-primary text-xs">
                                    Make offer
                                </button>
                        </div>
                    </form>
                @endif
            </div>

            <div class="text-right">
                <a href="{{ route('requests.index') }}"
                   class="text-sm text-seait-500 hover:text-seait-800">
                    &larr; Back to request board
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
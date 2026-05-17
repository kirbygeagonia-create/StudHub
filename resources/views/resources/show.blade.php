<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $resource->title }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-3">
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Type</dt>
                        <dd class="text-gray-900">{{ $resource->type->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Subject</dt>
                        <dd class="text-gray-900">{{ $resource->subject->code }} · {{ $resource->subject->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Posted by</dt>
                        <dd class="text-gray-900">
                            {{ $resource->owner?->display_name ?: $resource->owner?->name ?: 'Unknown' }}
                            @if ($resource->program)
                                · {{ $resource->program->code }}
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Year level</dt>
                        <dd class="text-gray-900">{{ $resource->year_level ? 'Year ' . $resource->year_level : '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Availability</dt>
                        <dd class="text-gray-900">{{ $resource->availability->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-gray-500">Visibility</dt>
                        <dd class="text-gray-900">{{ $resource->visibility->label() }}</dd>
                    </div>
                    @if ($resource->course_code)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Course code</dt>
                            <dd class="text-gray-900">{{ $resource->course_code }}</dd>
                        </div>
                    @endif
                    @if ($resource->condition)
                        <div>
                            <dt class="text-xs uppercase tracking-wide text-gray-500">Condition</dt>
                            <dd class="text-gray-900">{{ ucfirst(str_replace('_', ' ', $resource->condition)) }}</dd>
                        </div>
                    @endif
                </dl>

                @if ($resource->description)
                    <div class="border-t border-gray-100 pt-3">
                        <h3 class="text-xs uppercase tracking-wide text-gray-500 mb-1">Description</h3>
                        <p class="text-sm text-gray-800 whitespace-pre-line">{{ $resource->description }}</p>
                    </div>
                @endif

                <div class="border-t border-gray-100 pt-4 flex items-center gap-3">
                    @if ($resource->file_url)
                        <a href="{{ route('resources.download', $resource) }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                           target="_blank" rel="noopener">
                            Download attachment
                        </a>
                        @if ($resource->is_watermarked)
                            <span class="text-xs text-gray-500">(watermarked)</span>
                        @endif
                        @if ($resource->file_size)
                            <span class="text-xs text-gray-400">{{ round($resource->file_size / 1024) }} KB</span>
                        @endif
                    @endif

                    <form method="POST" action="{{ route('resources.toggle-save', $resource) }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-xs font-semibold uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            @if ($isSaved)
                                <svg class="w-4 h-4 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                Saved
                            @else
                                <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                                Save to shelf
                            @endif
                        </button>
                    </form>
                </div>

                <div class="flex items-center gap-4 text-xs text-gray-400">
                    <span>{{ $resource->save_count }} saves</span>
                    <span>{{ $resource->lend_count }} lends</span>
                    <span>Posted {{ $resource->published_at?->diffForHumans() }}</span>
                </div>
            </div>

            <div>
                <a href="{{ route('resources.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to resources</a>
                <a href="{{ route('resources.shelf') }}" class="text-sm text-gray-500 hover:text-gray-700 ml-4">My shelf →</a>
            </div>
        </div>
    </div>
</x-app-layout>
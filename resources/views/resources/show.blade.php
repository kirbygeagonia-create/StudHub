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
                </dl>

                @if ($resource->description)
                    <div>
                        <h3 class="text-xs uppercase tracking-wide text-gray-500 mb-1">Description</h3>
                        <p class="text-sm text-gray-800 whitespace-pre-line">{{ $resource->description }}</p>
                    </div>
                @endif

                @if ($resource->file_url)
                    <div class="border-t border-gray-100 pt-4">
                        <a href="{{ asset('storage/' . $resource->file_url) }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                           target="_blank" rel="noopener">
                            Download attachment
                        </a>
                        @if ($resource->is_watermarked)
                            <span class="ml-2 text-xs text-gray-500">(watermarked)</span>
                        @endif
                    </div>
                @endif
            </div>

            <div>
                <a href="{{ route('resources.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Back to resources</a>
            </div>
        </div>
    </div>
</x-app-layout>

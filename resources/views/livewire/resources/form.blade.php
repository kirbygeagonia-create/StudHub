<div class="space-y-4">
    @if (session('status'))
        <div class="rounded-md bg-green-50 border border-green-200 p-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                <input type="text" id="title" wire:model="title"
                       class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="e.g. DSA midterm reviewer (sorting + trees)" />
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <select id="subject_id" wire:model="subject_id"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">— pick a subject —</option>
                    @foreach ($this->subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->code }} — {{ $subject->name }}</option>
                    @endforeach
                </select>
                @error('subject_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="type" wire:model="type"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach ($this->types as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
                @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="course_code" class="block text-sm font-medium text-gray-700 mb-1">Course code <span class="text-gray-400">(optional)</span></label>
                <input type="text" id="course_code" wire:model="course_code" placeholder="e.g. IT 211"
                       class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500" />
                @error('course_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="year_level" class="block text-sm font-medium text-gray-700 mb-1">Year level taken</label>
                <select id="year_level" wire:model="year_level"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">—</option>
                    @for ($y = 1; $y <= 5; $y++)
                        <option value="{{ $y }}">Year {{ $y }}</option>
                    @endfor
                </select>
                @error('year_level') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="availability" class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                <select id="availability" wire:model="availability"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach ($this->availabilities as $availability)
                        <option value="{{ $availability->value }}">{{ $availability->label() }}</option>
                    @endforeach
                </select>
                @error('availability') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="visibility" class="block text-sm font-medium text-gray-700 mb-1">Visibility</label>
                <select id="visibility" wire:model="visibility"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach ($this->visibilities as $visibility)
                        <option value="{{ $visibility->value }}">{{ $visibility->label() }}</option>
                    @endforeach
                </select>
                @error('visibility') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400">(optional)</span></label>
                <textarea id="description" wire:model="description" rows="4"
                          class="w-full border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="What's inside? What topics does it cover?"></textarea>
                @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="file" class="block text-sm font-medium text-gray-700 mb-1">File <span class="text-gray-400">(optional, ≤ 25 MB)</span></label>
                <input type="file" id="file" wire:model="file"
                       class="w-full text-sm text-gray-700" />
                <p class="mt-1 text-xs text-gray-500">PDF, image, Word/Excel/PowerPoint, ZIP, or plain text.</p>
                @error('file') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
            <a href="{{ route('resources.index') }}" class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
            <button type="submit" wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50">
                <span wire:loading.remove>Post resource</span>
                <span wire:loading>Posting…</span>
            </button>
        </div>
    </form>
</div>

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
                       class="w-full border-gray-300 rounded-md text-sm focus:ring-seait-400 focus:border-seait-400"
                       placeholder="e.g. DSA midterm reviewer (sorting + trees)" />
                @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div x-data="subjectAutocomplete2()">
                <label for="subject_id_search" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                <input type="text" id="subject_id_search" x-model="query" @input="search()" @focus="open = true" @click.away="open = false"
                       placeholder="Type to search subjects..."
                       class="w-full border-gray-300 rounded-md text-sm focus:ring-seait-400 focus:border-seait-400">
                <ul x-show="open && filtered.length > 0 && !selectedId"
                    x-cloak
                    class="mt-1 max-h-48 overflow-y-auto bg-white border border-gray-300 rounded-md shadow-sm text-sm divide-y divide-gray-100">
                    <template x-for="subject in filtered" :key="subject.id">
                        <li @click="pick(subject)"
                            class="px-3 py-2 hover:bg-seait-50 cursor-pointer"
                            x-text="subject.code + ' — ' + subject.name">
                        </li>
                    </template>
                </ul>
                <p x-show="selectedId" class="mt-1 text-xs text-seait-500">
                    Selected: <span x-text="selectedLabel"></span>
                    <button @click="clear()" type="button" class="ml-1 text-red-500 hover:underline">Change</button>
                </p>
                @error('subject_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            @push('scripts')
            <script>
                function subjectAutocomplete2() {
                    return {
                        subjects: @json($this->subjects->map(fn($s) => ['id' => $s->id, 'code' => $s->code, 'name' => $s->name])),
                        query: '',
                        selectedId: @json($subject_id ?? ''),
                        selectedLabel: '',
                        open: false,
                        init() {
                            if (this.selectedId) {
                                const s = this.subjects.find(x => x.id == this.selectedId);
                                if (s) this.selectedLabel = s.code + ' — ' + s.name;
                            }
                            this.$watch('selectedId', v => { document.getElementById('subject_id_hidden').value = v; });
                        },
                        get filtered() {
                            if (!this.query) return this.subjects.slice(0, 50);
                            const q = this.query.toLowerCase();
                            return this.subjects.filter(s =>
                                s.code.toLowerCase().includes(q) || s.name.toLowerCase().includes(q)
                            ).slice(0, 50);
                        },
                        search() { this.open = true; },
                        pick(subject) {
                            this.selectedId = subject.id;
                            this.selectedLabel = subject.code + ' — ' + subject.name;
                            this.query = '';
                            this.open = false;
                            setTimeout(() => { this.$el.closest('form').dispatchEvent(new Event('input', { bubbles: true })); }, 0);
                        },
                        clear() {
                            this.selectedId = '';
                            this.selectedLabel = '';
                            this.query = '';
                        }
                    }
                }
            </script>
            @endpush
            <input type="hidden" id="subject_id_hidden" wire:model="subject_id" />

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select id="type" wire:model="type"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-seait-400 focus:border-seait-400">
                    @foreach ($this->types as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
                @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="course_code" class="block text-sm font-medium text-gray-700 mb-1">Course code <span class="text-gray-400">(optional)</span></label>
                <input type="text" id="course_code" wire:model="course_code" placeholder="e.g. IT 211"
                       class="w-full border-gray-300 rounded-md text-sm focus:ring-seait-400 focus:border-seait-400" />
                @error('course_code') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="year_level" class="block text-sm font-medium text-gray-700 mb-1">Year level taken</label>
                <select id="year_level" wire:model="year_level"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-seait-400 focus:border-seait-400">
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
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-seait-400 focus:border-seait-400">
                    @foreach ($this->availabilities as $availability)
                        <option value="{{ $availability->value }}">{{ $availability->label() }}</option>
                    @endforeach
                </select>
                @error('availability') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="visibility" class="block text-sm font-medium text-gray-700 mb-1">Visibility</label>
                <select id="visibility" wire:model="visibility"
                        class="w-full border-gray-300 rounded-md text-sm focus:ring-seait-400 focus:border-seait-400">
                    @foreach ($this->visibilities as $visibility)
                        <option value="{{ $visibility->value }}">{{ $visibility->label() }}</option>
                    @endforeach
                </select>
                @error('visibility') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400">(optional)</span></label>
                <textarea id="description" wire:model="description" rows="4"
                          class="w-full border-gray-300 rounded-md text-sm focus:ring-seait-400 focus:border-seait-400"
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
                    class="inline-flex items-center px-4 py-2 bg-seait-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-seait-600 disabled:opacity-50">
                <span wire:loading.remove>Post resource</span>
                <span wire:loading>Posting…</span>
            </button>
        </div>
    </form>
</div>

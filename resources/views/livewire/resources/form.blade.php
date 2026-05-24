<div class="space-y-6">
    <form wire:submit="save" class="space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div class="sm:col-span-2">
                <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Title <span class="text-red-400">*</span></label>
                <input type="text" id="title" wire:model="title" class="input-field"
                       placeholder="e.g. DSA midterm reviewer (sorting & trees)" />
                @error('title') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div x-data="subjectAutocomplete2()" class="sm:col-span-2">
                <label for="subject_id_search" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Subject <span class="text-red-400">*</span></label>
                <div class="relative">
                    <input type="text" id="subject_id_search" x-model="query" @input="search()" @focus="open = true" @click.away="open = false"
                           placeholder="Search subjects…" class="input-field" autocomplete="off">
                    <ul x-show="open && filtered.length > 0 && !selectedId" x-cloak
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute z-10 mt-1 w-full max-h-60 overflow-y-auto bg-white dark:bg-navy-800 rounded-xl border border-gray-200 dark:border-navy-700 shadow-card-lg divide-y divide-gray-100 dark:divide-navy-700">
                        <template x-for="subject in filtered" :key="subject.id">
                            <li @click="pick(subject)"
                                class="px-4 py-2.5 hover:bg-seait-50 dark:hover:bg-seait-900/20 cursor-pointer transition-colors flex items-center gap-3">
                                <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-seait-50 text-seait-600 dark:bg-seait-800/30 dark:text-seait-400 flex items-center justify-center text-xs font-bold" x-text="subject.code.substring(0, 2)"></span>
                                <div class="min-w-0">
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="subject.code + ' — ' + subject.name"></span>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
                <p x-show="selectedId" class="mt-2 inline-flex items-center gap-2 px-3 py-1.5 bg-seait-50 dark:bg-seait-900/20 rounded-lg text-sm">
                    <span class="text-seait-700 dark:text-seait-400 font-medium" x-text="selectedLabel"></span>
                    <button @click="clear()" type="button" class="text-red-500 hover:text-red-600 text-xs font-medium">Change</button>
                </p>
                @error('subject_id') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror

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
            </div>

            <div>
                <label for="type" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Type</label>
                <select id="type" wire:model="type" class="input-field">
                    @foreach ($this->types as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
                @error('type') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="course_code" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Course code <span class="text-gray-400 font-normal">(optional)</span></label>
                <input type="text" id="course_code" wire:model="course_code" placeholder="e.g. IT 211" class="input-field" />
                @error('course_code') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="year_level" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Year level</label>
                <select id="year_level" wire:model="year_level" class="input-field">
                    <option value="">—</option>
                    @for ($y = 1; $y <= 5; $y++)
                        <option value="{{ $y }}">Year {{ $y }}</option>
                    @endfor
                </select>
                @error('year_level') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="availability" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Availability</label>
                <select id="availability" wire:model="availability" class="input-field">
                    @foreach ($this->availabilities as $availability)
                        <option value="{{ $availability->value }}">{{ $availability->label() }}</option>
                    @endforeach
                </select>
                @error('availability') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="visibility" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Visibility</label>
                <select id="visibility" wire:model="visibility" class="input-field">
                    @foreach ($this->visibilities as $visibility)
                        <option value="{{ $visibility->value }}">{{ $visibility->label() }}</option>
                    @endforeach
                </select>
                @error('visibility') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Description <span class="text-gray-400 font-normal">(optional)</span></label>
                <textarea id="description" wire:model="description" rows="4" class="input-field"
                          placeholder="What's inside? What topics does it cover?"></textarea>
                @error('description') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            <div class="sm:col-span-2">
                <label for="file" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">File <span class="text-gray-400 font-normal">(optional, ≤ 25 MB)</span></label>
                <div class="mt-1 flex items-center gap-3">
                    <label class="btn-secondary cursor-pointer !px-4 !py-2 text-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        Choose file
                        <input type="file" id="file" wire:model="file" class="hidden" />
                    </label>
                    <span class="text-xs text-gray-500">PDF, JPEG, PNG, WebP</span>
                </div>
                @error('file') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-navy-700">
            <a href="{{ route('resources.index') }}" class="btn-ghost text-sm">Cancel</a>
            <button type="submit" wire:loading.attr="disabled" class="btn-primary">
                <svg wire:loading.remove.delay class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span wire:loading.remove>Post resource</span>
                <span wire:loading>Posting…</span>
            </button>
        </div>
    </form>
</div>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Request') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('requests.store') }}" class="space-y-4">
                    @csrf

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div x-data="subjectAutocomplete()">
                        <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
                        <input type="text" x-model="query" @input="search()" @focus="open = true" @click.away="open = false"
                               placeholder="Type to search subjects..."
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-seait-400 focus:border-seait-400 text-sm">
                        <input type="hidden" name="subject_id" x-model="selectedId">
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
                        function subjectAutocomplete() {
                            return {
                                subjects: @json($subjects->map(fn($s) => ['id' => $s->id, 'code' => $s->code, 'name' => $s->name])),
                                query: '',
                                selectedId: '',
                                selectedLabel: '',
                                open: false,
                                get filtered() {
                                    if (!this.query) return this.subjects;
                                    const q = this.query.toLowerCase();
                                    return this.subjects.filter(s =>
                                        s.code.toLowerCase().includes(q) || s.name.toLowerCase().includes(q)
                                    );
                                },
                                search() { this.open = true; },
                                pick(subject) {
                                    this.selectedId = subject.id;
                                    this.selectedLabel = subject.code + ' — ' + subject.name;
                                    this.query = '';
                                    this.open = false;
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

                    <div>
                        <label for="type_wanted" class="block text-sm font-medium text-gray-700 mb-1">Type wanted *</label>
                        <select id="type_wanted" name="type_wanted" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-seait-400 focus:border-seait-400 text-sm">
                            <option value="">Select type</option>
                            @foreach ($types as $type)
                                <option value="{{ $type->value }}" @selected(old('type_wanted') == $type->value)>
                                    {{ $type->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="urgency" class="block text-sm font-medium text-gray-700 mb-1">Urgency *</label>
                        <select id="urgency" name="urgency" required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-seait-400 focus:border-seait-400 text-sm">
                            @foreach ($urgencies as $urgency)
                                <option value="{{ $urgency->value }}" @selected(old('urgency', 'normal') == $urgency->value)>
                                    {{ $urgency->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="needed_by" class="block text-sm font-medium text-gray-700 mb-1">Needed by (optional)</label>
                        <input type="date" id="needed_by" name="needed_by" value="{{ old('needed_by') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-seait-400 focus:border-seait-400 text-sm" />
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                        <textarea id="description" name="description" rows="4" maxlength="2000"
                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-seait-400 focus:border-seait-400 text-sm"
                                  placeholder="Tell people what you're looking for…">{{ old('description') }}</textarea>
                        <p class="text-xs text-gray-400 mt-1">Max 2,000 characters.</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('requests.index') }}"
                           class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" onclick="this.disabled=true; this.form.submit();"
                                class="px-4 py-2 bg-seait-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-seait-600 disabled:opacity-50">
                            Post request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
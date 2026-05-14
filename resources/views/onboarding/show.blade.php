<x-guest-layout>
    <h1 class="text-xl font-semibold text-gray-800 mb-2">Welcome to StudHub</h1>
    <p class="text-sm text-gray-600 mb-6">
        Tell us which program you're in so we can route resources and requests
        to the right people. You can change these later from your profile.
    </p>

    <form method="POST" action="{{ route('onboarding.store') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="display_name" :value="'Display name'" />
            <x-text-input id="display_name"
                          class="block mt-1 w-full"
                          type="text"
                          name="display_name"
                          :value="old('display_name', auth()->user()?->name)"
                          required
                          autofocus />
            <x-input-error :messages="$errors->get('display_name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="program_id" :value="'Degree program'" />
            <select id="program_id"
                    name="program_id"
                    required
                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">Select your program…</option>
                @foreach ($programs as $program)
                    <option value="{{ $program->id }}"
                        @selected(old('program_id') == $program->id)>
                        {{ $program->code }} — {{ $program->name }} ({{ $program->college?->code }})
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('program_id')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="year_level" :value="'Year level'" />
            <select id="year_level"
                    name="year_level"
                    required
                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <option value="">Pick a year…</option>
                @for ($y = $yearMin; $y <= $yearMax; $y++)
                    <option value="{{ $y }}" @selected(old('year_level') == $y)>Year {{ $y }}</option>
                @endfor
            </select>
            <x-input-error :messages="$errors->get('year_level')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end">
            <x-primary-button>
                Finish setup
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>

<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-14 h-14 flex items-center justify-center mx-auto mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" fill="none" class="w-14 h-14">
                <defs>
                    <linearGradient id="obG" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#FF6B35"/>
                        <stop offset="100%" stop-color="#C94B15"/>
                    </linearGradient>
                    <linearGradient id="obA" x1="0" y1="1" x2="1" y2="0">
                        <stop offset="0%" stop-color="#FFB347"/>
                        <stop offset="100%" stop-color="#FF8C5A"/>
                    </linearGradient>
                </defs>
                <path d="M100 18 C68 18 38 30 24 52 C10 74 10 100 10 100 C10 132 12 152 30 166 C48 180 72 182 100 182 C128 182 152 180 170 166 C188 152 190 132 190 100 C190 100 190 74 176 52 C162 30 132 18 100 18Z" fill="url(#obG)"/>
                <path d="M100 52 C88 54 66 62 54 74 L54 148 C66 138 88 132 100 132 Z" fill="white" opacity="0.16"/>
                <path d="M100 52 C112 54 134 62 146 74 L146 148 C134 138 112 132 100 132 Z" fill="white" opacity="0.10"/>
                <line x1="100" y1="52" x2="100" y2="148" stroke="white" stroke-width="2" opacity="0.28" stroke-linecap="round"/>
                <rect x="72" y="94" width="56" height="8" rx="4" fill="white" opacity="0.95"/>
                <path d="M100 70 L118 91 L100 99 L82 91 Z" fill="white" opacity="0.95"/>
                <line x1="116" y1="97" x2="116" y2="122" stroke="url(#obA)" stroke-width="3" stroke-linecap="round"/>
                <circle cx="116" cy="126" r="4.5" fill="url(#obA)"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Welcome to StudHub!</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            One last step — tell us about yourself so we can connect you with the right resources and classmates.
        </p>
    </div>

    {{-- Step indicator --}}
    <div class="flex items-center justify-center gap-2 mb-6">
        <div class="w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold flex items-center justify-center">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div class="h-0.5 w-8 bg-emerald-200 dark:bg-emerald-800"></div>
        <div class="w-6 h-6 rounded-full bg-seait-500 text-white text-xs font-bold flex items-center justify-center">2</div>
        <div class="h-0.5 w-8 bg-gray-200 dark:bg-navy-700"></div>
        <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-navy-700 text-gray-500 dark:text-gray-400 text-xs font-bold flex items-center justify-center">3</div>
    </div>

    @php $currentUser = auth()->user(); @endphp

    <form method="POST" action="{{ route('onboarding.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="display_name" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                Display name <span class="text-gray-400 font-normal">(how others will see you)</span>
            </label>
            <input id="display_name" type="text" name="display_name"
                   value="{{ old('display_name', $currentUser?->name) }}"
                   class="block w-full input-field" required autofocus
                   placeholder="e.g. Juan D. or JuanC2024">
            @error('display_name')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        @if ($currentUser?->isSao() || $currentUser?->isSuperAdmin())
            {{-- SAO and SuperAdmin: only need display name --}}
            <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800/30 rounded-xl p-3 text-xs text-blue-700 dark:text-blue-300">
                <p class="font-medium">You're being set up as a campus-wide administrator.</p>
                <p class="mt-0.5">No program or year level needed. You'll have access to all colleges and programs.</p>
            </div>
        @elseif ($currentUser?->isDean() || $currentUser?->isProgramHead())
            {{-- Dean and Program Head: need program selection for college assignment --}}
            <div>
                <label for="program_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Select a program <span class="text-gray-400 font-normal">(to determine your college)</span>
                </label>
                <select id="program_id" name="program_id" required class="block w-full input-field">
                    <option value="">Select a program…</option>
                    @foreach ($programs as $program)
                        <option value="{{ $program->id }}" @selected(old('program_id') == $program->id)>
                            {{ $program->code }} — {{ $program->name }} ({{ $program->college?->code }})
                        </option>
                    @endforeach
                </select>
                @error('program_id')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-200 dark:border-indigo-800/30 rounded-xl p-3 text-xs text-indigo-700 dark:text-indigo-300">
                <p class="font-medium">You're being set up as an administrator.</p>
                <p class="mt-0.5">Pick any program under your college to set your college affiliation. No year level needed.</p>
            </div>
        @else
            {{-- Students and Moderators: need full profile --}}
            <div>
                <label for="program_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Degree program</label>
                <select id="program_id" name="program_id" required class="block w-full input-field">
                    <option value="">Select your program…</option>
                    @foreach ($programs as $program)
                        <option value="{{ $program->id }}" @selected(old('program_id') == $program->id)>
                            {{ $program->code }} — {{ $program->name }} ({{ $program->college?->code }})
                        </option>
                    @endforeach
                </select>
                @error('program_id')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="year_level" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Year level</label>
                <select id="year_level" name="year_level" required class="block w-full input-field">
                    <option value="">Pick a year…</option>
                    @for ($y = $yearMin; $y <= $yearMax; $y++)
                        <option value="{{ $y }}" @selected(old('year_level') == $y)>Year {{ $y }}</option>
                    @endfor
                </select>
                @error('year_level')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        @endif

        <button type="submit" class="btn-primary w-full !py-2.5 mt-2">
            Finish setup → Go to Dashboard
        </button>
    </form>
</x-guest-layout>
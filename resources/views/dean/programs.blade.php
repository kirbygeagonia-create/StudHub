<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Programs & Program Heads') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card p-6 mb-6">
                <h3 class="section-title mb-4">Programs</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-navy-700/50">
                                <th class="pb-2 font-medium">Code</th>
                                <th class="pb-2 font-medium">Name</th>
                                <th class="pb-2 font-medium">Students</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($programs as $program)
                                <tr class="border-b border-gray-50 dark:border-navy-700/30">
                                    <td class="py-2.5 font-medium">{{ $program->code }}</td>
                                    <td class="py-2.5">{{ $program->name }}</td>
                                    <td class="py-2.5">{{ $program->student_count ?? 0 }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-4 text-center text-gray-400">No programs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="section-title mb-4">Assign Program Head</h3>
                <form method="POST" action="{{ route('dean.program_heads.assign') }}" class="flex flex-wrap gap-3 items-end">
                    @csrf
                    <div>
                        <label class="label-text">User ID</label>
                        <input type="number" name="user_id" required class="input-field" placeholder="User ID">
                    </div>
                    <div>
                        <label class="label-text">Program ID</label>
                        <input type="number" name="program_id" required class="input-field" placeholder="Program ID">
                    </div>
                    <button type="submit" class="btn-primary">Assign Program Head</button>
                </form>
                @if ($programHeads->isNotEmpty())
                    <div class="mt-6">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Current Program Heads</h4>
                        <div class="space-y-2">
                            @foreach ($programHeads as $ph)
                                <div class="flex items-center justify-between bg-gray-50 dark:bg-navy-800/60 rounded-lg px-4 py-2">
                                    <span class="text-sm">{{ $ph->preferredDisplayName() }}</span>
                                    <span class="text-xs text-gray-500">{{ $ph->college?->code ?? 'N/A' }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
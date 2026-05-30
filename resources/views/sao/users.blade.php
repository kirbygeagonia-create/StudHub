<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User Management') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="card p-4 mb-6">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <label class="label-text">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="input-field" placeholder="Name, email...">
                    </div>
                    <div>
                        <label class="label-text">Role</label>
                        <select name="role" class="input-field">
                            <option value="">All Roles</option>
                            <option value="student" @selected(request('role') === 'student')>Student</option>
                            <option value="moderator" @selected(request('role') === 'moderator')>Moderator</option>
                            <option value="program_head" @selected(request('role') === 'program_head')>Program Head</option>
                            <option value="dean" @selected(request('role') === 'dean')>Dean</option>
                            <option value="sao" @selected(request('role') === 'sao')>SAO</option>
                        </select>
                    </div>
                    <div>
                        <label class="label-text">College</label>
                        <select name="college_id" class="input-field">
                            <option value="">All Colleges</option>
                            @foreach ($colleges as $college)
                                <option value="{{ $college->id }}" @selected((int) request('college_id') === $college->id)>{{ $college->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn-primary">Filter</button>
                </form>
            </div>

            <!-- Users Table -->
            <div class="card p-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-navy-700/50">
                                <th class="pb-2 font-medium">Name</th>
                                <th class="pb-2 font-medium">Email</th>
                                <th class="pb-2 font-medium">Role</th>
                                <th class="pb-2 font-medium">College</th>
                                <th class="pb-2 font-medium">Program</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $u)
                                <tr class="border-b border-gray-50 dark:border-navy-700/30">
                                    <td class="py-2.5 font-medium">{{ $u->preferredDisplayName() }}</td>
                                    <td class="py-2.5 text-gray-500">{{ $u->email }}</td>
                                    <td class="py-2.5">
                                        <span class="badge-seait">{{ $u->role?->label() ?? 'Unknown' }}</span>
                                    </td>
                                    <td class="py-2.5 text-gray-500">{{ $u->college?->code ?? 'N/A' }}</td>
                                    <td class="py-2.5 text-gray-500">{{ $u->program?->code ?? 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 text-gray-400 text-center">No users found.</td>
                                </tr>
                            </endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
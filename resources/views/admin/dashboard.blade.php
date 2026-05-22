<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md text-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md text-sm">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Open Reports</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $openReports }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Program Moderators</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalModerators }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Active Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activeUsers }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-xs uppercase tracking-wide text-gray-500">New Signups (7d)</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $recentSignups }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Daily Active Users</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $dau }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Total Resources</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalResources }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Total Requests</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalRequests }}</p>
                </div>
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Messages Today</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $messagesToday }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <p class="text-xs uppercase tracking-wide text-gray-500">Active Lends</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activeLends }}</p>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Colleges Overview</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="py-2 pr-4 font-medium text-gray-600">College</th>
                                <th class="py-2 pr-4 font-medium text-gray-600">Code</th>
                                <th class="py-2 pr-4 font-medium text-gray-600">Programs</th>
                                <th class="py-2 pr-4 font-medium text-gray-600">Active Users</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($collegeStats as $college)
                                <tr class="border-b border-gray-100">
                                    <td class="py-2 pr-4 text-gray-900">{{ $college->name }}</td>
                                    <td class="py-2 pr-4 text-gray-600">{{ $college->code }}</td>
                                    <td class="py-2 pr-4 text-gray-600">{{ $college->program_count }}</td>
                                    <td class="py-2 pr-4 text-gray-600">{{ $college->active_user_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Cross-Program Flow (Top 10)</h3>
                @if ($crossProgramFlows->isEmpty())
                    <p class="text-sm text-gray-500">No cross-program resource sharing yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="py-2 pr-4 font-medium text-gray-600">From Program</th>
                                    <th class="py-2 pr-4 font-medium text-gray-600">To Program</th>
                                    <th class="py-2 pr-4 font-medium text-gray-600">Resources Shared</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($crossProgramFlows as $flow)
                                    <tr class="border-b border-gray-100">
                                        <td class="py-2 pr-4 text-gray-900">{{ $flow->from_program }}</td>
                                        <td class="py-2 pr-4 text-gray-900">{{ $flow->to_program }}</td>
                                        <td class="py-2 pr-4 text-gray-600">{{ $flow->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Assign Program Moderator</h3>
                <form method="POST" action="{{ route('admin.moderators.assign') }}" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="assign_user_id" class="block text-xs text-gray-600 mb-1">User ID</label>
                            <input type="number" id="assign_user_id" name="user_id" required
                                   class="text-sm border-gray-300 rounded-md shadow-sm w-full">
                        </div>
                        <div>
                            <label for="assign_program_id" class="block text-xs text-gray-600 mb-1">Program</label>
                            <select id="assign_program_id" name="program_id" required
                                    class="text-sm border-gray-300 rounded-md shadow-sm w-full">
                                <option value="">Select program</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->id }}">{{ $program->code }} — {{ $program->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="submit"
                            class="px-3 py-1.5 bg-seait-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-seait-600">
                        Assign Moderator
                    </button>
                </form>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Current Moderators</h3>

                @if ($moderators->isEmpty())
                    <p class="text-sm text-gray-500">No moderators assigned.</p>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($moderators as $mod)
                            <div class="py-2 flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $mod->user?->preferredDisplayName() ?? 'Unknown' }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $mod->program?->code ?? 'Unknown program' }}
                                    </p>
                                </div>
                                <form method="POST" action="{{ route('admin.moderators.remove') }}">
                                    @csrf
                                    <input type="hidden" name="moderator_id" value="{{ $mod->id }}">
                                    <button type="submit"
                                            class="text-xs text-red-600 hover:text-red-900">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-semibold text-gray-800 mb-4">Suspend / Unsuspend User (School-wide)</h3>
                <form method="POST" action="{{ route('admin.suspend') }}" class="space-y-3 mb-6">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="admin_suspend_user_id" class="block text-xs text-gray-600 mb-1">User ID</label>
                            <input type="number" id="admin_suspend_user_id" name="user_id" required
                                   class="text-sm border-gray-300 rounded-md shadow-sm w-full">
                        </div>
                        <div>
                            <label for="admin_suspend_days" class="block text-xs text-gray-600 mb-1">Days (1-365)</label>
                            <input type="number" id="admin_suspend_days" name="days" value="30" required min="1" max="365"
                                   class="text-sm border-gray-300 rounded-md shadow-sm w-32">
                        </div>
                    </div>
                    <div>
                        <label for="admin_suspend_reason" class="block text-xs text-gray-600 mb-1">Reason (optional)</label>
                        <input type="text" id="admin_suspend_reason" name="reason" maxlength="500"
                               class="text-sm border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <button type="submit"
                            class="px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700">
                        Suspend
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.unsuspend') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label for="admin_unsuspend_user_id" class="block text-xs text-gray-600 mb-1">User ID</label>
                        <input type="number" id="admin_unsuspend_user_id" name="user_id" required
                               class="text-sm border-gray-300 rounded-md shadow-sm w-full">
                    </div>
                    <button type="submit"
                            class="px-3 py-1.5 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                        Unsuspend
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
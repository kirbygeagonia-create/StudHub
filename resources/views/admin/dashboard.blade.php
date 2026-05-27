<x-app-layout>
    <x-page-header title="Admin Dashboard" />

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Stats Grid: all 9 stats in one responsive grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="stat-card">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Open Reports</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $openReports }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Program Moderators</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalModerators }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Active Users</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $activeUsers }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">New Signups (7d)</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $recentSignups }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Daily Active Users</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $dau }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Resources</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalResources }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Requests</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $totalRequests }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Messages Today</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $messagesToday }}</p>
                </div>
                <div class="stat-card">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Active Lends</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $activeLends }}</p>
                </div>
            </div>

            {{-- Moderator Management: side-by-side --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Assign Moderator --}}
                <div class="card p-6">
                    <h3 class="section-title mb-4">Assign Moderator</h3>
                    <form method="POST" action="{{ route('admin.moderators.assign') }}" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="assign_user_id" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">User ID</label>
                                <input type="number" id="assign_user_id" name="user_id" required
                                       class="text-sm input-field w-full">
                            </div>
                            <div>
                                <label for="assign_program_id" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Program</label>
                                <select id="assign_program_id" name="program_id" required
                                        class="text-sm input-field w-full">
                                    <option value="">Select program</option>
                                    @foreach ($programs as $program)
                                        <option value="{{ $program->id }}">{{ $program->code }} — {{ $program->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary text-xs !px-3 !py-1.5">
                            Assign Moderator
                        </button>
                    </form>
                </div>

                {{-- Current Moderators --}}
                <div class="card p-6">
                    <h3 class="section-title mb-4">Current Moderators</h3>
                    @if ($moderators->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400">No moderators assigned.</p>
                    @else
                        <div class="divide-y divide-gray-100 dark:divide-navy-700">
                            @foreach ($moderators as $mod)
                                <div class="py-2 flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $mod->user?->preferredDisplayName() ?? 'Unknown' }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $mod->program?->code ?? 'Unknown program' }}
                                        </p>
                                    </div>
                                    <form method="POST" action="{{ route('admin.moderators.remove') }}">
                                        @csrf
                                        <input type="hidden" name="moderator_id" value="{{ $mod->id }}">
                                        <button type="submit" class="btn-ghost text-xs !px-2 !py-1 text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300">
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- Colleges Overview --}}
            <div class="card p-6">
                <h3 class="section-title mb-4">Colleges Overview</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-navy-700">
                                <th class="py-2 pr-4 font-medium text-gray-500 dark:text-gray-400">College</th>
                                <th class="py-2 pr-4 font-medium text-gray-500 dark:text-gray-400">Code</th>
                                <th class="py-2 pr-4 font-medium text-gray-500 dark:text-gray-400">Programs</th>
                                <th class="py-2 pr-4 font-medium text-gray-500 dark:text-gray-400">Active Users</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($collegeStats as $college)
                                <tr class="border-b border-gray-100 dark:border-navy-700">
                                    <td class="py-2 pr-4 text-gray-900 dark:text-gray-100">{{ $college->name }}</td>
                                    <td class="py-2 pr-4 text-gray-600 dark:text-gray-400">{{ $college->code }}</td>
                                    <td class="py-2 pr-4 text-gray-600 dark:text-gray-400">{{ $college->program_count }}</td>
                                    <td class="py-2 pr-4 text-gray-600 dark:text-gray-400">{{ $college->active_user_count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Cross-Program Flow --}}
            <div class="card p-6">
                <h3 class="section-title mb-4">Cross-Program Flow (Top 10)</h3>
                @if ($crossProgramFlows->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400">No cross-program resource sharing yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-navy-700">
                                    <th class="py-2 pr-4 font-medium text-gray-500 dark:text-gray-400">From Program</th>
                                    <th class="py-2 pr-4 font-medium text-gray-500 dark:text-gray-400">To Program</th>
                                    <th class="py-2 pr-4 font-medium text-gray-500 dark:text-gray-400">Resources Shared</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($crossProgramFlows as $flow)
                                    <tr class="border-b border-gray-100 dark:border-navy-700">
                                        <td class="py-2 pr-4 text-gray-900 dark:text-gray-100">{{ $flow->from_program }}</td>
                                        <td class="py-2 pr-4 text-gray-900 dark:text-gray-100">{{ $flow->to_program }}</td>
                                        <td class="py-2 pr-4 text-gray-600 dark:text-gray-400">{{ $flow->count }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Suspend / Unsuspend User --}}
            <div class="card p-6">
                <h3 class="section-title mb-4">Suspend / Unsuspend User (School-wide)</h3>
                <form method="POST" action="{{ route('admin.suspend') }}" class="space-y-3 mb-6">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label for="admin_suspend_user_id" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">User ID</label>
                            <input type="number" id="admin_suspend_user_id" name="user_id" required
                                   class="text-sm input-field w-full">
                        </div>
                        <div>
                            <label for="admin_suspend_days" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Days (1-365)</label>
                            <input type="number" id="admin_suspend_days" name="days" value="30" required min="1" max="365"
                                   class="text-sm input-field w-32">
                        </div>
                    </div>
                    <div>
                        <label for="admin_suspend_reason" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Reason (optional)</label>
                        <input type="text" id="admin_suspend_reason" name="reason" maxlength="500"
                               class="text-sm input-field w-full">
                    </div>
                    <button type="submit" class="btn-primary text-xs !px-3 !py-1.5 !bg-red-500 hover:!bg-red-600">
                        Suspend
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.unsuspend') }}" class="space-y-3">
                    @csrf
                    <div>
                        <label for="admin_unsuspend_user_id" class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">User ID</label>
                        <input type="number" id="admin_unsuspend_user_id" name="user_id" required
                               class="text-sm input-field w-full">
                    </div>
                    <button type="submit" class="btn-primary text-xs !px-3 !py-1.5 !bg-emerald-500 hover:!bg-emerald-600">
                        Unsuspend
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>

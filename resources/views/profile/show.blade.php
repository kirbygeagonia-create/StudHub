<x-app-layout>
    <x-page-header title="Profile" />

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="card p-6">
                {{-- Header: Avatar, Name, Karma/Badge --}}
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100 dark:border-navy-700">
                    <div class="w-14 h-14 rounded-full bg-seait-500 flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                        {{ strtoupper(substr($user?->preferredDisplayName() ?? '?', 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $user?->preferredDisplayName() ?? 'User' }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user?->program ? $user->program->code . ' · Year ' . $user->year_level : '' }}</p>
                        @if ($user?->student_number)
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $user->student_number }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-900 dark:text-gray-100" data-testid="karma-score">{{ $karma }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Karma</div>
                        @if ($badge)
                            <span class="inline-flex items-center gap-1 mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ in_array($badge->rarity()->value, ['legendary', 'rare']) ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300' : 'bg-seait-50 text-seait-700 dark:bg-seait-900/20 dark:text-seait-300' }}"
                                data-testid="badge-tier">
                                {{ $badge->label() }}
                            </span>
                            @php $nextTier = $badge->next(); $karmaToNext = $nextTier ? $badge->karmaToNext($karma) : 0; @endphp
                            @if ($nextTier)
                                <div class="mt-2 text-left" style="min-width:120px">
                                    <div class="flex items-center justify-between mb-0.5">
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500">Next: {{ $nextTier->label() }}</span>
                                        <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $karmaToNext }} left</span>
                                    </div>
                                    <div class="h-1.5 w-full bg-gray-200 dark:bg-navy-700 rounded-full overflow-hidden">
                                        @php
                                            $progress = $nextTier
                                                ? min(100, round(($karma - $badge->threshold()) / ($nextTier->threshold() - $badge->threshold()) * 100))
                                                : 100;
                                        @endphp
                                        <div class="h-full bg-gradient-to-r from-seait-400 to-seait-500 rounded-full transition-all" style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                            @else
                                <p class="text-[10px] text-amber-600 dark:text-amber-400 mt-1 font-semibold flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 0 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M18.75 4.236c.982.143 1.954.317 2.916.52A6.003 6.003 0 0 1 16.27 9.728M18.75 4.236V4.5c0 2.108-.966 3.99-2.48 5.228m0 0a6.023 6.023 0 0 1-2.77.896m0 0a6.022 6.022 0 0 1-2.77-.896m0 0a6.023 6.023 0 0 1-2.77-.896"/></svg>Max tier reached</p>
                            @endif
                        @endif
                    </div>
                </div>

                {{-- Edit Account Settings --}}
                @if (auth()->id() === ($user?->id ?? -1))
                    <div class="mb-6">
                        <a href="{{ route('profile.edit') }}" class="btn-primary inline-flex items-center gap-2" data-testid="edit-account-settings">
                            <x-icon name="settings" class="w-4 h-4" />
                            Edit account settings
                        </a>
                    </div>
                @endif

                {{-- Details Grid --}}
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="stat-card p-4 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Display name</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $user?->preferredDisplayName() ?? '—' }}
                        </dd>
                    </div>
                    <div class="stat-card p-4 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100 break-all">{{ $user?->email }}</dd>
                    </div>
                    <div class="stat-card p-4 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $user?->role?->label() ?? 'Student' }}
                        </dd>
                    </div>
                    <div class="stat-card p-4 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">School</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $user?->school?->short_name ?? '—' }}
                        </dd>
                    </div>
                    <div class="stat-card p-4 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">College</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $user?->college ? $user->college->code.' — '.$user->college->name : '—' }}
                        </dd>
                    </div>
                    <div class="stat-card p-4 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Program</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $user?->program ? $user->program->code.' — '.$user->program->name : '—' }}
                        </dd>
                    </div>
                    <div class="stat-card p-4 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Year level</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $user?->year_level ? 'Year '.$user->year_level : '—' }}
                        </dd>
                    </div>
                    <div class="stat-card p-4 rounded-lg">
                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Resources uploaded</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $resourceCount }}</dd>
                    </div>
                </dl>

                {{-- Report Form --}}
                @if (auth()->id() !== ($user?->id ?? -1))
                    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-navy-700" data-testid="report-user-form">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Report this user</h4>
                        <form method="POST" action="{{ route('reports.store') }}" class="flex flex-wrap items-end gap-3">
                            @csrf
                            <input type="hidden" name="reported_type" value="user">
                            <input type="hidden" name="reported_id" value="{{ $user->id }}">
                            <div class="flex-1 min-w-[200px]">
                                <label for="report-reason" class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">Reason</label>
                                <select id="report-reason" name="reason" required class="input-field w-full text-sm">
                                    <option value="">Select reason</option>
                                    @foreach (\App\Domain\Moderation\Enums\ReportReason::cases() as $reason)
                                        <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit"
                                    class="btn-primary !bg-red-500 hover:!bg-red-600 inline-flex items-center gap-2">
                                <x-icon name="flag" class="w-4 h-4" />
                                Report this user
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
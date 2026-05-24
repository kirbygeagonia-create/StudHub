<x-app-layout>
    <x-page-header title="Profile" />

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="card p-6">
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
                                {{ $badge->value === 'gold' ? 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300' : ($badge->value === 'silver' ? 'bg-gray-200 text-gray-700 dark:bg-navy-600 dark:text-gray-300' : 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300') }}"
                                data-testid="badge-tier">
                                {{ $badge->label() }}
                            </span>
                        @endif
                    </div>
                </div>

                <dl class="divide-y divide-gray-100 dark:divide-navy-700">
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Display name</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2 sm:mt-0">
                            {{ $user?->preferredDisplayName() ?? '—' }}
                        </dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2 sm:mt-0">{{ $user?->email }}</dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Role</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2 sm:mt-0">
                            {{ $user?->role?->label() ?? 'Student' }}
                        </dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">School</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2 sm:mt-0">
                            {{ $user?->school?->short_name ?? '—' }}
                        </dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">College</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2 sm:mt-0">
                            {{ $user?->college ? $user->college->code.' — '.$user->college->name : '—' }}
                        </dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Program</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2 sm:mt-0">
                            {{ $user?->program ? $user->program->code.' — '.$user->program->name : '—' }}
                        </dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Year level</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2 sm:mt-0">
                            {{ $user?->year_level ? 'Year '.$user->year_level : '—' }}
                        </dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">Resources uploaded</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 sm:col-span-2 sm:mt-0">{{ $resourceCount }}</dd>
                    </div>
                </dl>

                <div class="mt-6 flex items-center gap-3">
                    <a href="{{ route('profile.edit') }}" class="text-sm text-seait-500 hover:underline">
                        Edit account settings
                    </a>
                    @if (auth()->id() !== ($user?->id ?? -1))
                        <form method="POST" action="{{ route('reports.store') }}" class="inline">
                            @csrf
                            <input type="hidden" name="reported_type" value="user">
                            <input type="hidden" name="reported_id" value="{{ $user->id }}">
                            <select name="reason" required class="text-xs input-field mr-1">
                                <option value="">Select reason</option>
                                @foreach (\App\Domain\Moderation\Enums\ReportReason::cases() as $reason)
                                    <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                                @endforeach
                            </select>
                            <button type="submit"
                                    class="btn-primary text-xs !px-2 !py-1 !bg-red-500 hover:!bg-red-600">
                                Report this user
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
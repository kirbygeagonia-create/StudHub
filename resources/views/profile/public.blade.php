<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $user?->preferredDisplayName() ?? 'User' }}'s Profile</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                    <div class="w-14 h-14 rounded-full bg-seait-500 flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                        {{ strtoupper(substr($user?->preferredDisplayName() ?? '?', 0, 2)) }}
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $user?->preferredDisplayName() ?? 'User' }}</h3>
                        <p class="text-sm text-gray-500">{{ $user?->program ? $user->program->code . ' · Year ' . $user->year_level : '' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-gray-900" data-testid="karma-score">{{ $karma }}</div>
                        <div class="text-xs text-gray-500">Karma</div>
                        @if ($badge)
                            <span class="inline-flex items-center gap-1 mt-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $badge->value === 'gold' ? 'bg-yellow-100 text-yellow-800' : ($badge->value === 'silver' ? 'bg-gray-200 text-gray-700' : 'bg-amber-100 text-amber-800') }}"
                                data-testid="badge-tier">
                                {{ $badge->label() }}
                            </span>
                        @endif
                    </div>
                </div>

                <dl class="divide-y divide-gray-100">
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600">Display name</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            {{ $user?->preferredDisplayName() ?? '—' }}
                        </dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600">Role</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            {{ $user?->role?->label() ?? 'Student' }}
                        </dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600">School</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            {{ $user?->school?->short_name ?? '—' }}
                        </dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600">College</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            {{ $user?->college ? $user->college->code.' — '.$user->college->name : '—' }}
                        </dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600">Program</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            {{ $user?->program ? $user->program->code.' — '.$user->program->name : '—' }}
                        </dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600">Year level</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                            {{ $user?->year_level ? 'Year '.$user->year_level : '—' }}
                        </dd>
                    </div>
                    <div class="py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600">Resources uploaded</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:col-span-2 sm:mt-0">{{ $resourceCount }}</dd>
                    </div>
                </dl>

                @if (! $isSelf)
                    <div class="mt-6 border-t border-gray-100 pt-3">
                        <button type="button" onclick="document.getElementById('report-form-user').classList.toggle('hidden')"
                                class="text-xs text-red-400 hover:text-red-600">
                            Report this user
                        </button>
                        <form id="report-form-user" method="POST" action="{{ route('reports.store') }}" class="hidden mt-2 space-y-2">
                            @csrf
                            <input type="hidden" name="reported_type" value="user">
                            <input type="hidden" name="reported_id" value="{{ $user->id }}">
                            <select name="reason" required class="text-xs border-gray-300 rounded-md shadow-sm">
                                <option value="">Select reason</option>
                                @foreach (\App\Domain\Moderation\Enums\ReportReason::cases() as $reason)
                                    <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="notes" maxlength="1000" placeholder="Optional note"
                                   class="text-xs border-gray-300 rounded-md shadow-sm w-full">
<button type="submit" onclick="this.disabled=true; this.form.submit();"
                                            class="px-2 py-1 bg-red-500 border border-transparent rounded text-xs font-semibold text-white uppercase tracking-widest hover:bg-red-600 disabled:opacity-50">
                                        Submit Report
                                    </button>
                        </form>
                    </div>
                @endif
            </div>

            <div class="text-right">
                <a href="{{ route('profile.show') }}" class="text-sm text-seait-500 hover:text-seait-800">
                    &larr; Back to your profile
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
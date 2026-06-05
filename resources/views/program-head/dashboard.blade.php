@extends('layouts.admin')

@section('sidebar')
    @include('program-head._sidebar')
@endsection

@section('pageHeader')
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                Program Head Dashboard
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                {{ auth()->user()->college?->code }}
                — {{ auth()->user()->college?->name }}
            </p>
        </div>
        <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">
            {{ now()->format('l, F j') }}
        </span>
    </div>
@endsection

@section('content')
    {{-- ═══ Stat Cards ═══ --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <x-admin-stat-card
            label="Open Reports"
            :value="$openReports"
            icon="flag"
            :tone="$openReports > 0 ? 'alert' : 'good'"
            sub="campus-wide"
        />
        <x-admin-stat-card
            label="Moderators"
            :value="$totalModerators"
            icon="moderation"
            :tone="$totalModerators === 0 ? 'warning' : 'default'"
        />
        <x-admin-stat-card
            label="Active Users"
            :value="number_format($activeUsers)"
            icon="users"
            sub="in your college"
        />
        <x-admin-stat-card
            label="Resources"
            :value="number_format($totalResources)"
            icon="resources"
            sub="across all programs"
        />
    </div>

    {{-- ═══ Unread Feedback Alert ═══ --}}
    @if ($unreadFeedback > 0)
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200
                    dark:border-amber-800/50 rounded-xl p-4 mb-5
                    flex items-center gap-3">
            <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none"
                 stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0
                         1.502-1.275.722-1.845l-6.928-5.013c-.752-.545
                         -1.792-.545-2.544 0L5.094 17.155c-.78.57-.332
                         1.845.722 1.845z"/>
            </svg>
            <p class="text-sm font-semibold text-amber-700 dark:text-amber-300 flex-1">
                {{ $unreadFeedback }} unread
                {{ Str::plural('feedback', $unreadFeedback) }}
            </p>
            <a href="{{ route('program_head.feedback') }}"
               class="btn-primary !text-xs !px-3 !py-1.5 flex-shrink-0">
                View feedback
            </a>
        </div>
    @endif

    {{-- ═══ Quick Actions ═══ --}}
    <div class="grid grid-cols-4 gap-3 mb-5">
        <a href="{{ route('program_head.feedback') }}" class="quick-action">
            @if ($unreadFeedback > 0)
                <span class="quick-action-badge">{{ $unreadFeedback }}</span>
            @endif
            <div class="quick-action-icon"><x-icon name="feedback" /></div>
            <span>Feedback</span>
        </a>
        <a href="{{ route('moderation.dashboard') }}" class="quick-action">
            @if ($openReports > 0)
                <span class="quick-action-badge">{{ $openReports }}</span>
            @endif
            <div class="quick-action-icon"><x-icon name="flag" /></div>
            <span>Reports</span>
        </a>
        <a href="{{ route('resources.index') }}" class="quick-action">
            <div class="quick-action-icon"><x-icon name="resources" /></div>
            <span>Resources</span>
        </a>
        <a href="{{ route('leaderboard') }}" class="quick-action">
            <div class="quick-action-icon"><x-icon name="leaderboard" /></div>
            <span>Leaderboard</span>
        </a>
    </div>

    {{-- ═══ Bottom Grid: Moderators + Chart ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

        {{-- Moderators list (3/5) --}}
        <div class="lg:col-span-3 card">
            <div class="p-5 border-b border-gray-100 dark:border-navy-700/50
                        flex items-center justify-between">
                <h3 class="section-title">Program Moderators</h3>
                <a href="{{ route('moderation.dashboard') }}"
                   class="text-xs font-medium text-navy-700 dark:text-navy-300
                          hover:underline">
                    View reports →
                </a>
            </div>

            {{-- Inline search filter (no server request needed) --}}
            <div class="p-3 border-b border-gray-50 dark:border-navy-700/30">
                <input type="text"
                       @input="
                           const q = $event.target.value.toLowerCase();
                           document.querySelectorAll('[data-mod-row]').forEach(
                               r => r.style.display =
                                   r.dataset.modRow.includes(q) ? '' : 'none'
                           )
                       "
                       x-data
                       class="input-field !text-xs !py-2"
                       placeholder="Search moderators…">
            </div>

            @if ($moderators->isEmpty())
                <x-empty-state
                    icon="moderation"
                    title="No moderators yet"
                    description="Assign moderators to programs in your college."
                />
            @else
                <div class="divide-y divide-gray-50 dark:divide-navy-700/30">
                    @foreach ($moderators as $mod)
                        <div class="flex items-center gap-3 px-5 py-3
                                    hover:bg-gray-50/50 dark:hover:bg-navy-800/30
                                    transition-colors"
                             data-mod-row="{{ strtolower(
                                 ($mod->user?->preferredDisplayName() ?? '') . ' ' .
                                 ($mod->program?->code ?? '')
                             ) }}">

                            {{-- Avatar initials --}}
                            <div class="w-7 h-7 rounded-full bg-emerald-100
                                        dark:bg-emerald-900/30 flex items-center
                                        justify-center flex-shrink-0">
                                <span class="text-xs font-bold text-emerald-600
                                             dark:text-emerald-400">
                                    {{ strtoupper(substr(
                                        $mod->user?->preferredDisplayName() ?? '?', 0, 1
                                    )) }}
                                </span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900
                                           dark:text-gray-100 truncate">
                                    {{ $mod->user?->preferredDisplayName() ?? 'Unknown' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $mod->program?->code }}
                                    — {{ $mod->program?->name }}
                                </p>
                            </div>

                            <span class="text-xs text-gray-400 flex-shrink-0">
                                {{ $mod->created_at->diffForHumans() }}
                            </span>

                            <form method="POST"
                                  action="{{ route('program_head.moderators.remove') }}"
                                  onsubmit="return confirm(
                                      'Remove {{ $mod->user?->preferredDisplayName() }}
                                       as moderator?')">
                                @csrf
                                <input type="hidden"
                                       name="moderator_id"
                                       value="{{ $mod->id }}">
                                <button type="submit"
                                        class="text-xs text-red-400
                                               hover:text-red-600 transition-colors
                                               font-medium">
                                    Remove
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                @if ($moderators->hasPages())
                    <div class="px-5 py-3 border-t border-gray-100
                                dark:border-navy-700/50">
                        {{ $moderators->links() }}
                    </div>
                @endif
            @endif
        </div>

        {{-- Activity chart (2/5) --}}
        <div class="lg:col-span-2 card p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="section-title">
                    {{ $selectedRange === 'semester' ? 'Semester' : ($selectedRange === '30' ? '30-Day' : '7-Day') }} Activity
                </h3>
                <form method="GET" action="{{ route('program_head.dashboard') }}" class="flex items-center gap-2">
                    <select name="range" onchange="this.form.submit()"
                            class="text-xs border-gray-200 dark:border-navy-700 rounded-lg bg-gray-50 dark:bg-navy-800 text-gray-600 dark:text-gray-300 px-2 py-1">
                        <option value="7" {{ $selectedRange === '7' ? 'selected' : '' }}>Last 7 days</option>
                        <option value="30" {{ $selectedRange === '30' ? 'selected' : '' }}>Last 30 days</option>
                        <option value="semester" {{ $selectedRange === 'semester' ? 'selected' : '' }}>This semester</option>
                    </select>
                    <noscript><button type="submit" class="btn-primary text-xs !px-2 !py-1">Go</button></noscript>
                </form>
            </div>

            <div class="bar-chart flex-1">
                @php
                    $maxBars = max(collect($chartData)->pluck('count')->max(), 1);
                @endphp
                @foreach ($chartData as $day)
                    <div class="bar-chart-col">
                        <div class="bar-chart-bar {{ $loop->last ? 'today' : '' }}"
                             style="height: {{ max(4, ($day['count'] / $maxBars) * 100) }}%">
                        </div>
                        <span class="bar-chart-label">{{ $day['label'] }}</span>
                    </div>
                @endforeach
            </div>

            <p class="text-xs text-gray-400 mt-3 text-center">
                Active today:
                <strong class="text-gray-700 dark:text-gray-200">
                    {{ $chartData[6]['count'] ?? 0 }}
                </strong>
            </p>
        </div>

    </div>
@endsection
@extends('layouts.admin')

@section('sidebar')
    @include('dean._sidebar')
@endsection

@section('pageHeader')
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                Dean Dashboard
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                {{ $college?->code }} — {{ $college?->name }}
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
            label="Programs"
            :value="$programs->count()"
            icon="college"
        />
        <x-admin-stat-card
            label="Students"
            :value="number_format($totalStudents)"
            icon="users"
        />
        <x-admin-stat-card
            label="Moderators"
            :value="$totalModerators"
            icon="moderation"
            :tone="$totalModerators === 0 ? 'warning' : 'default'"
        />
        <x-admin-stat-card
            label="Open Reports"
            :value="$openReports"
            icon="flag"
            :tone="$openReports > 0 ? 'alert' : 'good'"
            sub="campus-wide"
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
            <a href="{{ route('dean.feedback') }}"
               class="btn-primary !text-xs !px-3 !py-1.5 flex-shrink-0">
                View feedback
            </a>
        </div>
    @endif

    {{-- ═══ Quick Actions ═══ --}}
    <div class="grid grid-cols-4 gap-3 mb-5">
        <a href="{{ route('dean.feedback') }}" class="quick-action">
            @if ($unreadFeedback > 0)
                <span class="quick-action-badge">{{ $unreadFeedback }}</span>
            @endif
            <div class="quick-action-icon"><x-icon name="feedback" /></div>
            <span>Feedback</span>
        </a>
        <a href="{{ route('dean.programs') }}" class="quick-action">
            <div class="quick-action-icon"><x-icon name="college" /></div>
            <span>Programs</span>
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
    </div>

    {{-- ═══ Bottom Grid: Programs + Chart ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

        {{-- Programs grid (3/5) --}}
        <div class="lg:col-span-3 card">
            <div class="p-5 border-b border-gray-100 dark:border-navy-700/50
                        flex items-center justify-between">
                <h3 class="section-title">
                    Programs — {{ $college?->code }}
                </h3>
                <a href="{{ route('dean.programs') }}"
                   class="text-xs font-medium text-indigo-600
                          dark:text-indigo-400 hover:underline">
                    Manage Program Heads →
                </a>
            </div>

            @if ($programs->isEmpty())
                <x-empty-state
                    icon="college"
                    title="No programs yet"
                    description="Programs under this college will appear here."
                />
            @else
                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($programs as $program)
                        <div class="bg-gray-50 dark:bg-navy-800/50
                                    rounded-xl p-4 border border-gray-100
                                    dark:border-navy-700/30
                                    hover:border-indigo-200
                                    dark:hover:border-indigo-800/50
                                    transition-colors">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50
                                            dark:bg-indigo-900/30 flex items-center
                                            justify-center flex-shrink-0">
                                    <x-icon name="college"
                                            class="w-4 h-4 text-indigo-500" />
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-900
                                               dark:text-gray-100">
                                        {{ $program->code }}
                                    </p>
                                    <p class="text-xs text-gray-500
                                               dark:text-gray-400 mt-0.5
                                               leading-snug">
                                        {{ $program->name }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Activity chart + role breakdown (2/5) --}}
        <div class="lg:col-span-2 card p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="section-title">7-Day Activity</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">
                    Students / day
                </span>
            </div>

            <div class="bar-chart">
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

            {{-- Role breakdown --}}
            <div class="mt-4 pt-4 border-t border-gray-100
                        dark:border-navy-700/50 space-y-1.5">
                <p class="text-xs font-medium text-gray-500
                           dark:text-gray-400 mb-2">
                    Role breakdown
                </p>
                @foreach ([
                    ['label' => 'Program Heads', 'value' => $totalProgramHeads],
                    ['label' => 'Moderators',    'value' => $totalModerators],
                    ['label' => 'Students',      'value' => number_format($totalStudents)],
                ] as $row)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-600 dark:text-gray-400">
                            {{ $row['label'] }}
                        </span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ $row['value'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
@endsection
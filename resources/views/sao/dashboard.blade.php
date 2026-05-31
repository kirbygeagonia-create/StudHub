@extends('layouts.admin')

@section('sidebar')
    @include('sao._sidebar')
@endsection

@section('pageHeader')
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                SAO Dashboard
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                SEAIT Campus Administration
            </p>
        </div>
        <span class="text-xs text-gray-400 dark:text-gray-500 mt-1">
            {{ now()->format('l, F j') }}
        </span>
    </div>
@endsection

@section('content')
    {{-- ═══ Stat Cards (6-across) ═══ --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-5">
        <x-admin-stat-card
            label="Total Users"
            :value="number_format($totalUsers)"
            icon="users"
        />
        <x-admin-stat-card
            label="Students"
            :value="number_format($totalStudents)"
            icon="profile"
        />
        <x-admin-stat-card
            label="Moderators"
            :value="$totalModerators"
            icon="moderation"
        />
        <x-admin-stat-card
            label="Program Heads"
            :value="$totalProgramHeads"
            icon="building"
        />
        <x-admin-stat-card
            label="Deans"
            :value="$totalDeans"
            icon="college"
        />
        <x-admin-stat-card
            label="Open Reports"
            :value="$openReports"
            icon="flag"
            :tone="$openReports > 0 ? 'alert' : 'good'"
        />
    </div>

    {{-- ═══ Alert Banners ═══ --}}
    @if ($unreadFeedback > 0 || $openReports > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-5">
            @if ($unreadFeedback > 0)
                <div class="bg-amber-50 dark:bg-amber-900/20
                            border border-amber-200 dark:border-amber-800/50
                            rounded-xl p-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0
                                 1.502-1.275.722-1.845l-6.928-5.013c-.752
                                 -.545-1.792-.545-2.544 0L5.094 17.155c
                                 -.78.57-.332 1.845.722 1.845z"/>
                    </svg>
                    <p class="text-sm font-semibold text-amber-700
                               dark:text-amber-300 flex-1">
                        {{ $unreadFeedback }} unread escalated
                        {{ Str::plural('feedback', $unreadFeedback) }}
                    </p>
                    <a href="{{ route('sao.feedback') }}"
                       class="btn-primary !text-xs !px-3 !py-1.5 flex-shrink-0">
                        View
                    </a>
                </div>
            @endif

            @if ($openReports > 0)
                <div class="bg-red-50 dark:bg-red-900/20
                            border border-red-200 dark:border-red-800/50
                            rounded-xl p-4 flex items-center gap-3">
                    <svg class="w-5 h-5 text-red-500 flex-shrink-0"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0
                                 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-sm font-semibold text-red-700
                               dark:text-red-300 flex-1">
                        {{ $openReports }} open reports campus-wide
                    </p>
                    <a href="{{ route('moderation.dashboard') }}"
                       class="btn-primary !text-xs !px-3 !py-1.5
                              !bg-red-500 hover:!bg-red-600 flex-shrink-0">
                        View
                    </a>
                </div>
            @endif
        </div>
    @endif

    {{-- ═══ Quick Actions ═══ --}}
    <div class="grid grid-cols-4 gap-3 mb-5">
        <a href="{{ route('sao.feedback') }}" class="quick-action">
            @if ($unreadFeedback > 0)
                <span class="quick-action-badge">{{ $unreadFeedback }}</span>
            @endif
            <div class="quick-action-icon"><x-icon name="feedback" /></div>
            <span>Feedback</span>
        </a>
        <a href="{{ route('sao.users') }}" class="quick-action">
            <div class="quick-action-icon"><x-icon name="users" /></div>
            <span>Users</span>
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

    {{-- ═══ Bottom Grid: Colleges + Chart ═══ --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

        {{-- Colleges overview (3/5) --}}
        <div class="lg:col-span-3 card">
            <div class="p-5 border-b border-gray-100 dark:border-navy-700/50">
                <h3 class="section-title">Colleges Overview</h3>
            </div>

            @if ($colleges->isEmpty())
                <x-empty-state
                    icon="college"
                    title="No colleges found"
                />
            @else
                <div class="divide-y divide-gray-50 dark:divide-navy-700/30">
                    @foreach ($colleges as $college)
                        <div class="flex items-center gap-4 px-5 py-3.5
                                    hover:bg-gray-50/50 dark:hover:bg-navy-800/30
                                    transition-colors">
                            <div class="w-9 h-9 rounded-lg bg-slate-100
                                        dark:bg-slate-800/50 flex items-center
                                        justify-center flex-shrink-0">
                                <x-icon name="college"
                                        class="w-4 h-4 text-slate-500" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-900
                                           dark:text-gray-100">
                                    {{ $college->code }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400
                                           truncate">
                                    {{ $college->name }}
                                </p>
                            </div>
                            <div class="flex gap-5 flex-shrink-0 text-right">
                                <div>
                                    <p class="text-sm font-bold text-gray-900
                                               dark:text-gray-100">
                                        {{ $college->program_count ?? 0 }}
                                    </p>
                                    <p class="text-xs text-gray-400">programs</p>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900
                                               dark:text-gray-100">
                                        {{ number_format($college->active_user_count ?? 0) }}
                                    </p>
                                    <p class="text-xs text-gray-400">users</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Campus activity chart + role summary (2/5) --}}
        <div class="lg:col-span-2 card p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="section-title">Campus Activity</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">
                    Active users / day
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
                Campus active today:
                <strong class="text-gray-700 dark:text-gray-200">
                    {{ $chartData[6]['count'] ?? 0 }}
                </strong>
            </p>

            {{-- Role summary --}}
            <div class="mt-4 pt-4 border-t border-gray-100
                        dark:border-navy-700/50 space-y-1.5">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">
                    Role breakdown
                </p>
                @foreach ([
                    ['label' => 'Deans',          'value' => $totalDeans],
                    ['label' => 'Program Heads',   'value' => $totalProgramHeads],
                    ['label' => 'Moderators',      'value' => $totalModerators],
                    ['label' => 'Students',        'value' => number_format($totalStudents)],
                ] as $row)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-600 dark:text-gray-400">
                            {{ $row['label'] }}
                        </span>
                        <span class="font-semibold text-gray-900
                                     dark:text-gray-100">
                            {{ $row['value'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
@endsection
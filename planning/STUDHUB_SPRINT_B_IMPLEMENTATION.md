# StudHub — Sprint B Implementation
> Program Head · Dean · SAO Admin Panel Refinement
> Prerequisite: Sprint A confirmed complete (commit `39a3f14`)
> Base: commit `39a3f14` (2026-05-30)

---

## Sprint A Verification (confirmed before starting)

| Item | Status |
|------|--------|
| `panel-moderator` CSS with emerald `#059669` | ✅ Done |
| `panelClass()` returns `'panel-moderator'` for Moderator | ✅ Done |
| `stat-card::before` uses `var(--panel-accent, #FF6B35)` | ✅ Done |
| Role context banner — Moderator emerald strip | ✅ Done |
| `ModerationController` — `$resolvedToday`, `$totalActioned` | ✅ Done |
| `ModerationController::userSearch()` endpoint | ✅ Done |
| `/moderation/users/search` route | ✅ Done |
| Moderation dashboard — full rebuild with type filters, icons, user-search suspend | ✅ Done |

`last_seen_at` column also confirmed present on `users` table — no migration needed for chart data.

---

## What Sprint B Delivers

| Role | Before | After |
|------|--------|-------|
| Program Head | Plain `<x-app-layout>`, 4 bare number cards | Dark navy sidebar, icon stat cards, quick actions, moderator table search, 7-day bar chart |
| Dean | Plain `<x-app-layout>`, 4 bare number cards | Deep indigo sidebar, icon stat cards, quick actions, programs grid, 7-day bar chart |
| SAO | Plain `<x-app-layout>`, 6 bare number cards | Dark slate sidebar, icon stat cards, quick actions, colleges overview, campus-wide bar chart |

---

## Files to Create or Edit

| File | Action |
|------|--------|
| `resources/css/app.css` | Add sidebar + admin stat card + quick action + bar chart CSS |
| `resources/views/layouts/admin.blade.php` | **Create new** |
| `resources/views/components/admin-stat-card.blade.php` | **Create new** |
| `resources/views/components/icon.blade.php` | Add 5 icon paths |
| `app/Http/Controllers/ProgramHeadController.php` | Add `$chartData` to `dashboard()` |
| `app/Http/Controllers/DeanController.php` | Add `$chartData` to `dashboard()` |
| `app/Http/Controllers/SaoController.php` | Add `$chartData` to `dashboard()` |
| `resources/views/program-head/dashboard.blade.php` | **Full replacement** |
| `resources/views/dean/dashboard.blade.php` | **Full replacement** |
| `resources/views/sao/dashboard.blade.php` | **Full replacement** |

---

## Step 1 — CSS Additions

**File:** `resources/css/app.css` — append after the last existing panel block.

```css
/* ============================================
   Admin Sidebar Layout
   ============================================ */

.admin-layout {
    display: flex;
    min-height: calc(100vh - 112px);
}

.admin-sidebar {
    width: 220px;
    flex-shrink: 0;
    position: sticky;
    top: 0;
    height: calc(100vh - 112px);
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    padding: 1.25rem 0.75rem;
    gap: 2px;
    scrollbar-width: none;
}

.admin-sidebar::-webkit-scrollbar { display: none; }

.admin-content {
    flex: 1;
    min-width: 0;
    padding: 1.5rem 1.5rem 1.5rem 1.25rem;
}

.sidebar-link {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.8125rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.60);
    transition: background 150ms, color 150ms;
    text-decoration: none;
    position: relative;
}

.sidebar-link:hover {
    background: rgba(255, 255, 255, 0.08);
    color: rgba(255, 255, 255, 0.90);
}

.sidebar-link.active {
    background: rgba(255, 255, 255, 0.12);
    color: #fff;
    font-weight: 600;
}

.sidebar-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 6px;
    bottom: 6px;
    width: 3px;
    border-radius: 0 2px 2px 0;
    background: #fff;
}

.sidebar-badge {
    margin-left: auto;
    min-width: 18px;
    height: 18px;
    padding: 0 5px;
    border-radius: 9px;
    background: rgba(255, 255, 255, 0.18);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}

.sidebar-badge.urgent { background: #EF4444; }

.sidebar-section-label {
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: rgba(255, 255, 255, 0.30);
    padding: 0.75rem 0.75rem 0.25rem;
}

.sidebar-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.07);
    margin: 0.5rem 0.75rem;
}

/* Sidebar background per role */
.panel-program-head .admin-sidebar { background: #1E2D3D; }
.panel-dean         .admin-sidebar { background: #312E81; }
.panel-sao          .admin-sidebar { background: #1E293B; }

/* ============================================
   Enriched Admin Stat Cards
   ============================================ */

.admin-stat-card {
    position: relative;
    background: white;
    border: 0.5px solid #E5E7EB;
    border-radius: 0.75rem;
    padding: 1rem 1.125rem;
    overflow: hidden;
    display: flex;
    align-items: flex-start;
    gap: 0.875rem;
}

.dark .admin-stat-card {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.07);
}

.admin-stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(
        90deg,
        var(--panel-accent, #FF6B35),
        var(--panel-accent-light, #FF8C5A)
    );
}

.admin-stat-icon {
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 0.625rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 0.125rem;
    background: color-mix(in srgb,
        var(--panel-accent, #FF6B35) 12%, transparent);
}

.dark .admin-stat-icon {
    background: color-mix(in srgb,
        var(--panel-accent, #FF6B35) 18%, transparent);
}

.admin-stat-icon svg {
    width: 1.125rem;
    height: 1.125rem;
    color: var(--panel-accent, #FF6B35);
}

.admin-stat-label {
    font-size: 0.6875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #6B7280;
    margin: 0 0 0.25rem;
}

.dark .admin-stat-label { color: #9CA3AF; }

.admin-stat-value {
    font-size: 1.625rem;
    font-weight: 700;
    line-height: 1;
    color: #111827;
}

.dark .admin-stat-value { color: #F9FAFB; }

.admin-stat-value.alert   { color: #DC2626; }
.admin-stat-value.warning { color: #D97706; }
.admin-stat-value.good    { color: #059669; }

.dark .admin-stat-value.alert   { color: #F87171; }
.dark .admin-stat-value.warning { color: #FBBF24; }
.dark .admin-stat-value.good    { color: #34D399; }

.admin-stat-sub {
    font-size: 0.6875rem;
    color: #9CA3AF;
    margin-top: 0.25rem;
}

/* ============================================
   Quick Action Cards
   ============================================ */

.quick-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 0.75rem;
    border-radius: 0.75rem;
    border: 0.5px solid #E5E7EB;
    background: white;
    text-decoration: none;
    font-size: 0.75rem;
    font-weight: 500;
    color: #374151;
    transition: all 150ms;
    position: relative;
    text-align: center;
    cursor: pointer;
}

.dark .quick-action {
    background: rgba(255, 255, 255, 0.04);
    border-color: rgba(255, 255, 255, 0.07);
    color: #D1D5DB;
}

.quick-action:hover {
    border-color: var(--panel-accent, #FF6B35);
    box-shadow: 0 0 0 2px color-mix(in srgb,
        var(--panel-accent, #FF6B35) 15%, transparent);
}

.quick-action-icon {
    width: 2rem;
    height: 2rem;
    border-radius: 0.5rem;
    background: color-mix(in srgb,
        var(--panel-accent, #FF6B35) 10%, transparent);
    display: flex;
    align-items: center;
    justify-content: center;
}

.dark .quick-action-icon {
    background: color-mix(in srgb,
        var(--panel-accent, #FF6B35) 18%, transparent);
}

.quick-action-icon svg {
    width: 1rem;
    height: 1rem;
    color: var(--panel-accent, #FF6B35);
}

.quick-action-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    min-width: 16px;
    height: 16px;
    padding: 0 4px;
    border-radius: 8px;
    background: #EF4444;
    color: white;
    font-size: 9px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ============================================
   Inline Bar Chart (pure CSS, no JS library)
   ============================================ */

.bar-chart {
    display: flex;
    align-items: flex-end;
    gap: 3px;
    height: 52px;
}

.bar-chart-col {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    height: 100%;
    justify-content: flex-end;
}

.bar-chart-bar {
    width: 100%;
    border-radius: 2px 2px 0 0;
    background: color-mix(in srgb,
        var(--panel-accent, #FF6B35) 45%, transparent);
    min-height: 2px;
}

.bar-chart-bar.today {
    background: var(--panel-accent, #FF6B35);
}

.bar-chart-label {
    font-size: 9px;
    color: #9CA3AF;
    white-space: nowrap;
}
```

---

## Step 2 — Add 5 Icons to `icon.blade.php`

**File:** `resources/views/components/icon.blade.php`

Find the closing `];` of the `$icons` array and add these 5 entries
**before** it:

```php
    'users'      => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>',
    'college'    => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>',
    'chart-bar'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
    'shield'     => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>',
    'megaphone'  => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>',
```

---

## Step 3 — Create `layouts/admin.blade.php`

**File:** `resources/views/layouts/admin.blade.php` — new file

```blade
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ dark: localStorage.getItem('dark') === 'true' }"
      x-init="if (dark) { document.documentElement.classList.add('dark') };
               $watch('dark', v => {
                   localStorage.setItem('dark', v);
                   document.documentElement.classList.toggle('dark', v)
               })"
      :class="{ 'dark': dark }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=2" sizes="any">
        <meta name="theme-color" content="#FF6B35">
        <meta name="color-scheme" content="light dark">
        <title>@yield('title', config('app.name', 'StudHub'))</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap"
              rel="stylesheet"/>
        <script>
            if (localStorage.getItem('dark') === 'true')
                document.documentElement.classList.add('dark')
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>

    <body class="font-sans antialiased text-gray-900 dark:text-gray-100
                 {{ auth()->user()?->panelClass() }}">

        {{-- Role banner + top navigation --}}
        <x-role-context-banner />
        @include('layouts.navigation')

        {{-- Flash messages --}}
        <div class="fixed top-20 inset-x-0 z-[200] flex flex-col items-center
                    gap-2 px-4 pointer-events-none">

            @if (session('status') || session('success'))
                <div x-data="{ show: true }" x-show="show"
                     x-init="setTimeout(() => show = false, 5000)"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="flash-success pointer-events-auto w-full max-w-md">
                    <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="flex-1">
                        {{ session('status') ?? session('success') }}
                    </span>
                    <button @click="show = false"
                            class="ml-auto text-emerald-500 hover:text-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div x-data="{ show: true }" x-show="show"
                     x-init="setTimeout(() => show = false, 6000)"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="flash-error pointer-events-auto w-full max-w-md">
                    <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="flex-1">{{ session('error') }}</span>
                    <button @click="show = false"
                            class="ml-auto text-red-500 hover:text-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div x-data="{ show: true }" x-show="show"
                     x-init="setTimeout(() => show = false, 10000)"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-300"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 -translate-y-2"
                     class="flash-error pointer-events-auto w-full max-w-md">
                    <svg class="w-5 h-5 flex-shrink-0 text-red-500 mt-0.5"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <ul class="flex-1 list-disc list-inside space-y-0.5 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button @click="show = false"
                            class="ml-auto text-red-500 hover:text-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @endif

        </div>

        {{-- Two-column admin layout --}}
        <div class="admin-layout">

            {{-- Sidebar — hidden on mobile, visible md+ --}}
            <aside class="admin-sidebar hidden md:flex flex-col">
                {{ $sidebar }}
            </aside>

            {{-- Main content --}}
            <main class="admin-content">
                @isset($pageHeader)
                    <div class="mb-5">{{ $pageHeader }}</div>
                @endisset
                {{ $slot }}
            </main>

        </div>

        @stack('scripts')
        @livewireScripts
    </body>
</html>
```

---

## Step 4 — Create `admin-stat-card` Component

**File:** `resources/views/components/admin-stat-card.blade.php` — new file

```blade
@props([
    'label' => 'Stat',
    'value' => '—',
    'icon'  => 'chart-bar',
    'tone'  => 'default',   {{-- default | alert | warning | good --}}
    'sub'   => null,
])

<div class="admin-stat-card">
    <div class="admin-stat-icon">
        <x-icon :name="$icon" class="w-4 h-4" />
    </div>
    <div class="min-w-0">
        <p class="admin-stat-label">{{ $label }}</p>
        <p class="admin-stat-value {{ $tone !== 'default' ? $tone : '' }}">
            {{ $value }}
        </p>
        @if ($sub)
            <p class="admin-stat-sub">{{ $sub }}</p>
        @endif
    </div>
</div>
```

---

## Step 5 — Update `ProgramHeadController::dashboard()`

**File:** `app/Http/Controllers/ProgramHeadController.php`

Add `$chartData` inside the `dashboard()` method, directly before the
`return view(...)` call:

```php
// 7-day active-user bar chart for this college
$chartData = collect(range(6, 0))->map(function (int $daysAgo) use ($collegeId): array {
    $date = now()->subDays($daysAgo);
    return [
        'label' => $date->format('D'),
        'count' => User::where('college_id', $collegeId)
            ->whereDate('last_seen_at', $date->toDateString())
            ->count(),
    ];
})->values()->all();

return view('program-head.dashboard', [
    'openReports'     => $openReports,
    'totalModerators' => $totalModerators,
    'activeUsers'     => $activeUsers,
    'totalResources'  => $totalResources,
    'moderators'      => $moderators,
    'unreadFeedback'  => $unreadFeedback,
    'chartData'       => $chartData,        // ← new
]);
```

---

## Step 6 — Replace `program-head/dashboard.blade.php`

**File:** `resources/views/program-head/dashboard.blade.php` — full replacement

```blade
<x-admin-layout>

    {{-- ═══ Sidebar ═══ --}}
    <x-slot name="sidebar">
        <div class="sidebar-section-label">Program Head</div>

        <a href="{{ route('program_head.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('program_head.dashboard') ? 'active' : '' }}">
            <x-icon name="home" class="w-4 h-4 flex-shrink-0" />
            Dashboard
        </a>

        <a href="{{ route('program_head.feedback') }}"
           class="sidebar-link {{ request()->routeIs('program_head.feedback*') ? 'active' : '' }}">
            <x-icon name="feedback" class="w-4 h-4 flex-shrink-0" />
            Feedback
            @if ($unreadFeedback > 0)
                <span class="sidebar-badge {{ $unreadFeedback > 5 ? 'urgent' : '' }}">
                    {{ $unreadFeedback }}
                </span>
            @endif
        </a>

        <a href="{{ route('moderation.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('moderation.*') ? 'active' : '' }}">
            <x-icon name="flag" class="w-4 h-4 flex-shrink-0" />
            Reports
            @if ($openReports > 0)
                <span class="sidebar-badge urgent">{{ $openReports }}</span>
            @endif
        </a>

        <div class="sidebar-divider"></div>
        <div class="sidebar-section-label">Quick Access</div>

        <a href="{{ route('resources.index') }}"
           class="sidebar-link {{ request()->routeIs('resources.*') ? 'active' : '' }}">
            <x-icon name="resources" class="w-4 h-4 flex-shrink-0" />
            Resources
        </a>

        <a href="{{ route('leaderboard') }}"
           class="sidebar-link {{ request()->routeIs('leaderboard') ? 'active' : '' }}">
            <x-icon name="leaderboard" class="w-4 h-4 flex-shrink-0" />
            Leaderboard
        </a>

        <a href="{{ route('chat.index') }}"
           class="sidebar-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
            <x-icon name="chat" class="w-4 h-4 flex-shrink-0" />
            Chat
        </a>

        <div class="sidebar-divider"></div>

        <a href="{{ route('profile.show') }}"
           class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <x-icon name="profile" class="w-4 h-4 flex-shrink-0" />
            Profile
        </a>
    </x-slot>

    {{-- ═══ Page Header ═══ --}}
    <x-slot name="pageHeader">
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
    </x-slot>

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

        {{-- 7-day chart (2/5) --}}
        <div class="lg:col-span-2 card p-5 flex flex-col">
            <div class="flex items-center justify-between mb-4">
                <h3 class="section-title">7-Day Activity</h3>
                <span class="text-xs text-gray-400 dark:text-gray-500">
                    Active users / day
                </span>
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

</x-admin-layout>
```

---

## Step 7 — Update `DeanController::dashboard()`

**File:** `app/Http/Controllers/DeanController.php`

Add `$chartData` before the `return view(...)` call in `dashboard()`:

```php
$chartData = collect(range(6, 0))->map(function (int $daysAgo) use ($programIds): array {
    $date = now()->subDays($daysAgo);
    return [
        'label' => $date->format('D'),
        'count' => User::whereIn('program_id', $programIds)
            ->whereDate('last_seen_at', $date->toDateString())
            ->count(),
    ];
})->values()->all();

return view('dean.dashboard', [
    'college'           => $college,
    'programs'          => $programs,
    'totalStudents'     => $totalStudents,
    'totalModerators'   => $totalModerators,
    'totalProgramHeads' => $totalProgramHeads,
    'unreadFeedback'    => $unreadFeedback,
    'openReports'       => $openReports,
    'chartData'         => $chartData,         // ← new
]);
```

---

## Step 8 — Replace `dean/dashboard.blade.php`

**File:** `resources/views/dean/dashboard.blade.php` — full replacement

```blade
<x-admin-layout>

    {{-- ═══ Sidebar ═══ --}}
    <x-slot name="sidebar">
        <div class="sidebar-section-label">Dean</div>

        <a href="{{ route('dean.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dean.dashboard') ? 'active' : '' }}">
            <x-icon name="home" class="w-4 h-4 flex-shrink-0" />
            Dashboard
        </a>

        <a href="{{ route('dean.feedback') }}"
           class="sidebar-link {{ request()->routeIs('dean.feedback*') ? 'active' : '' }}">
            <x-icon name="feedback" class="w-4 h-4 flex-shrink-0" />
            Feedback
            @if ($unreadFeedback > 0)
                <span class="sidebar-badge {{ $unreadFeedback > 5 ? 'urgent' : '' }}">
                    {{ $unreadFeedback }}
                </span>
            @endif
        </a>

        <a href="{{ route('moderation.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('moderation.*') ? 'active' : '' }}">
            <x-icon name="flag" class="w-4 h-4 flex-shrink-0" />
            Reports
            @if ($openReports > 0)
                <span class="sidebar-badge urgent">{{ $openReports }}</span>
            @endif
        </a>

        <div class="sidebar-divider"></div>
        <div class="sidebar-section-label">College</div>

        <a href="{{ route('dean.programs') }}"
           class="sidebar-link {{ request()->routeIs('dean.programs*') ? 'active' : '' }}">
            <x-icon name="college" class="w-4 h-4 flex-shrink-0" />
            Programs
        </a>

        <a href="{{ route('resources.index') }}"
           class="sidebar-link {{ request()->routeIs('resources.*') ? 'active' : '' }}">
            <x-icon name="resources" class="w-4 h-4 flex-shrink-0" />
            Resources
        </a>

        <a href="{{ route('leaderboard') }}"
           class="sidebar-link {{ request()->routeIs('leaderboard') ? 'active' : '' }}">
            <x-icon name="leaderboard" class="w-4 h-4 flex-shrink-0" />
            Leaderboard
        </a>

        <div class="sidebar-divider"></div>

        <a href="{{ route('profile.show') }}"
           class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <x-icon name="profile" class="w-4 h-4 flex-shrink-0" />
            Profile
        </a>
    </x-slot>

    {{-- ═══ Page Header ═══ --}}
    <x-slot name="pageHeader">
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
    </x-slot>

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

</x-admin-layout>
```

---

## Step 9 — Update `SaoController::dashboard()`

**File:** `app/Http/Controllers/SaoController.php`

Add `$chartData` before the `return view(...)` call in `dashboard()`:

```php
$chartData = collect(range(6, 0))->map(function (int $daysAgo) use ($user): array {
    $date = now()->subDays($daysAgo);
    return [
        'label' => $date->format('D'),
        'count' => User::where('school_id', $user->school_id)
            ->whereDate('last_seen_at', $date->toDateString())
            ->count(),
    ];
})->values()->all();

return view('sao.dashboard', [
    'totalUsers'        => $totalUsers,
    'totalStudents'     => $totalStudents,
    'totalModerators'   => $totalModerators,
    'totalProgramHeads' => $totalProgramHeads,
    'totalDeans'        => $totalDeans,
    'openReports'       => $openReports,
    'unreadFeedback'    => $unreadFeedback,
    'colleges'          => $colleges,
    'chartData'         => $chartData,         // ← new
]);
```

---

## Step 10 — Replace `sao/dashboard.blade.php`

**File:** `resources/views/sao/dashboard.blade.php` — full replacement

```blade
<x-admin-layout>

    {{-- ═══ Sidebar ═══ --}}
    <x-slot name="sidebar">
        <div class="sidebar-section-label">SAO</div>

        <a href="{{ route('sao.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('sao.dashboard') ? 'active' : '' }}">
            <x-icon name="home" class="w-4 h-4 flex-shrink-0" />
            Dashboard
        </a>

        <a href="{{ route('sao.feedback') }}"
           class="sidebar-link {{ request()->routeIs('sao.feedback*') ? 'active' : '' }}">
            <x-icon name="feedback" class="w-4 h-4 flex-shrink-0" />
            All Feedback
            @if ($unreadFeedback > 0)
                <span class="sidebar-badge {{ $unreadFeedback > 5 ? 'urgent' : '' }}">
                    {{ $unreadFeedback }}
                </span>
            @endif
        </a>

        <a href="{{ route('moderation.dashboard') }}"
           class="sidebar-link {{ request()->routeIs('moderation.*') ? 'active' : '' }}">
            <x-icon name="flag" class="w-4 h-4 flex-shrink-0" />
            Reports
            @if ($openReports > 0)
                <span class="sidebar-badge urgent">{{ $openReports }}</span>
            @endif
        </a>

        <div class="sidebar-divider"></div>
        <div class="sidebar-section-label">Administration</div>

        <a href="{{ route('sao.users') }}"
           class="sidebar-link {{ request()->routeIs('sao.users*') ? 'active' : '' }}">
            <x-icon name="users" class="w-4 h-4 flex-shrink-0" />
            User Management
        </a>

        @if (config('studhub.announcements_enabled'))
            <a href="{{ route('sao.announcements') }}"
               class="sidebar-link
                      {{ request()->routeIs('sao.announcements*') ? 'active' : '' }}">
                <x-icon name="megaphone" class="w-4 h-4 flex-shrink-0" />
                Announcements
            </a>
        @endif

        <div class="sidebar-divider"></div>

        <a href="{{ route('profile.show') }}"
           class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <x-icon name="profile" class="w-4 h-4 flex-shrink-0" />
            Profile
        </a>
    </x-slot>

    {{-- ═══ Page Header ═══ --}}
    <x-slot name="pageHeader">
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
    </x-slot>

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

</x-admin-layout>
```

---

## Implementation Checklist

```
[ ] Step 1  — Append sidebar + stat card + quick action + bar chart CSS to app.css
[ ] Step 2  — Add 5 icon paths (users, college, chart-bar, shield, megaphone) to icon.blade.php
[ ] Step 3  — Create resources/views/layouts/admin.blade.php
[ ] Step 4  — Create resources/views/components/admin-stat-card.blade.php
[ ] Step 5  — Add $chartData to ProgramHeadController::dashboard()
[ ] Step 6  — Replace resources/views/program-head/dashboard.blade.php
[ ] Step 7  — Add $chartData to DeanController::dashboard()
[ ] Step 8  — Replace resources/views/dean/dashboard.blade.php
[ ] Step 9  — Add $chartData to SaoController::dashboard()
[ ] Step 10 — Replace resources/views/sao/dashboard.blade.php

Post-implementation checks:
[ ] Visit /program-head — dark navy sidebar visible, stat cards show icons
[ ] Visit /dean        — deep indigo sidebar visible, programs grid shows
[ ] Visit /sao         — dark slate sidebar visible, all 6 colleges listed
[ ] Confirm sidebar hidden on mobile (md:flex), layout stacks correctly
[ ] Confirm unread feedback badges show/hide correctly
[ ] Run php artisan test — all 233 tests still pass
```

---

*Sprint B document — 2026-05-30.*
*Build on top of Sprint A (commit `39a3f14`).*
*All route names and controller variables verified against actual codebase.*

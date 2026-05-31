# StudHub — Admin Panel UI/UX Full Plan
> Combines: STUDHUB_ADMIN_UI_ASSESSMENT.md + STUDHUB_MODERATOR_UI_PLAN.md
> Based on codebase audit of commit `79cad23` (latest main, 2026-05-30)
> All backend fixes from STUDHUB_IMPLEMENTATION_REVIEW_FINAL.md are confirmed done.

---

## Backend Implementation Status (from Review Final)

All critical and minor backend issues are resolved in commit `79cad23`:

| Issue | Status |
|-------|--------|
| `hasCompletedOnboarding()` blocks Dean/Program Head/SAO | ✅ Fixed — role-aware checks in place |
| ProgramHeadController scoped by `college_id` not `program_id` | ✅ Fixed — all 15 references corrected |
| ScopeGuard uses `college_id` for Program Head | ✅ Fixed |
| SubmitFeedback routes to `college_id` for Program Head | ✅ Fixed |
| DevUsersSeeder — Program Head has `null` program_id | ✅ Fixed |
| Open Reports label says "(campus-wide)" in Dean dashboard | ✅ Fixed |
| `read_at` marked when feedback viewed (all three controllers) | ✅ Fixed |
| SAO Announcements hidden behind feature flag | ✅ Fixed — `config/studhub.php` has `announcements_enabled` |
| Duplicate Dean/Program Head guard in SAO panel | ✅ Fixed — both checks present in SaoController |

**All backend work is done. What remains is entirely UI/UX.**

---

## Current UI State — Honest Assessment

### The Infrastructure (What Exists)

The panel skin system is wired up correctly in `app.blade.php`:

```blade
<body class="... {{ auth()->user()?->panelClass() }}">
    <x-role-context-banner />
```

CSS panel classes exist for 4 roles with:
- `.btn-primary` gradient override per role
- `.stat-card` 3px left border override per role

The role context banner shows correctly for Program Head (navy),
Dean (indigo), SAO (slate), and Super Admin (red warning).

### The Gap — What Is Actually Seen on Screen

Beyond the thin banner strip and a subtly different button color,
**every admin dashboard looks and feels identical.** A Dean and a
Program Head see the same layout, the same card shape, the same
plain bold numbers, and the same top navigation bar as a student.

The student-facing pages — chat, resources, leaderboard — are
significantly more polished than any of the admin panels built
in the role hierarchy sprint. The admin panels are functional
but visually unrefined.

---

## Confirmed Missing — Full List

### Across All Admin Roles

| Feature | Status |
|---------|--------|
| `stat-card::before` top strip uses role accent color | ❌ Still hardcoded orange |
| Admin sidebar layout (`layouts/admin.blade.php`) | ❌ Not built |
| Stat card icons (role-colored SVG per stat) | ❌ Not built |
| Color-coded stat values (red for alerts, green for resolved) | ❌ Partially — SAO has red for open reports only |
| Quick action panels on dashboards | ❌ Not built |
| Activity charts / data visualizations | ❌ Not built |
| Table search / sort / filter controls | ❌ Not built |
| Empty state designs (illustration + CTA) | ❌ Only bare `<p>` text |
| Role-distinct page header treatment | ❌ Same `<h2>` style for all roles |

### Moderator Specifically

| Feature | Status |
|---------|--------|
| `panel-moderator` CSS class | ❌ Not in app.css |
| `panelClass()` returns `''` for Moderator | ❌ Same as Student — no differentiation |
| Moderator in role context banner | ❌ Not in banner component |
| Moderation dashboard — stat counters (resolved today, total actioned) | ❌ Not in controller or view |
| Moderation dashboard — suspend by User ID (broken UX) | ❌ Still uses numeric ID input |
| Moderation dashboard — user search for suspend | ❌ Not built |
| Moderation dashboard — type filter tabs | ❌ Not built |
| Moderation dashboard — report type icons | ❌ Not built |
| Moderation dashboard — empty state design | ❌ Bare `<p>No open reports.</p>` |

---

## All Required Changes — Complete Code

---

## Change 1 — Fix `stat-card::before` (2 min, all roles)

The top accent strip on every stat card is hardcoded orange.
This single change makes every admin's stat cards instantly
reflect their role's color.

**File:** `resources/css/app.css`

```css
/* BEFORE */
.stat-card::before {
    background: linear-gradient(90deg, #FF6B35, #FF8C5A);
}

/* AFTER */
.stat-card::before {
    background: linear-gradient(
        90deg,
        var(--panel-accent, #FF6B35),
        var(--panel-accent-light, #FF8C5A)
    );
}
```

---

## Change 2 — Add `panel-moderator` CSS (5 min)

**File:** `resources/css/app.css` — add after the existing panel blocks:

```css
/* Moderator — emerald / trusted reviewer */
.panel-moderator {
    --panel-accent:       #059669;
    --panel-accent-light: #10B981;
    --panel-accent-hover: #047857;
    --panel-badge-bg:     #ECFDF5;
    --panel-badge-text:   #065F46;
}

.panel-moderator .btn-primary {
    background: linear-gradient(135deg, #059669, #047857);
}

.panel-moderator .btn-primary:hover {
    background: linear-gradient(135deg, #047857, #065F46);
}

.dark .panel-moderator .btn-primary {
    box-shadow: 0 1px 3px rgba(0,0,0,0.15),
                0 0 15px rgba(5, 150, 105, 0.08);
}

.panel-moderator .stat-card {
    border-left: 3px solid #10B981;
}
```

---

## Change 3 — Add Moderator to `UserRole::panelClass()` (2 min)

**File:** `app/Domain/Identity/Enums/UserRole.php`

```php
public function panelClass(): string
{
    return match ($this) {
        self::Moderator   => 'panel-moderator',     // ← ADD
        self::ProgramHead => 'panel-program-head',
        self::Dean        => 'panel-dean',
        self::Sao         => 'panel-sao',
        self::SuperAdmin  => 'panel-super',
        default           => '',
    };
}
```

---

## Change 4 — Add Moderator to Role Context Banner (5 min)

**File:** `resources/views/components/role-context-banner.blade.php`

Add the Moderator case at the very top, before `@if ($user?->isProgramHead())`:

```blade
@php $user = auth()->user(); @endphp

@if ($user?->isModerator())
    <div class="bg-emerald-700 text-white text-xs px-4 py-1.5 flex items-center gap-2">
        <svg class="w-3.5 h-3.5 opacity-70" fill="none" stroke="currentColor"
             viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955
                     11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824
                     10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133
                     -2.052-.382-3.016z"/>
        </svg>
        <span class="opacity-70">Program Moderator —</span>
        <span class="font-semibold">
            {{ $user->program?->code }}: {{ $user->program?->name }}
        </span>
    </div>

@elseif ($user?->isProgramHead())
    {{-- existing navy banner --}}
```

---

## Change 5 — Rebuild Moderation Dashboard (60–90 min)

The current dashboard has three problems beyond aesthetics:
- Suspend form uses a raw numeric User ID (nobody knows this)
- No stat counters (resolved today, total actioned)
- No type filter tabs or report icons

**File:** `resources/views/moderation/dashboard.blade.php` — full replacement:

```blade
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-900 dark:text-gray-100">
                    Moderation Dashboard
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ auth()->user()->program?->code }}
                    — {{ auth()->user()->program?->name }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Stat Row --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="stat-card text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium
                               uppercase tracking-wide">
                        Open Reports
                    </p>
                    <p class="text-3xl font-bold mt-1
                       {{ $reports->total() > 0
                          ? 'text-amber-600 dark:text-amber-400'
                          : 'text-gray-900 dark:text-gray-100' }}">
                        {{ $reports->total() }}
                    </p>
                </div>
                <div class="stat-card text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium
                               uppercase tracking-wide">
                        Resolved Today
                    </p>
                    <p class="text-3xl font-bold text-emerald-600
                               dark:text-emerald-400 mt-1">
                        {{ $resolvedToday }}
                    </p>
                </div>
                <div class="stat-card text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium
                               uppercase tracking-wide">
                        Total Actioned
                    </p>
                    <p class="text-3xl font-bold text-gray-900
                               dark:text-gray-100 mt-1">
                        {{ $totalActioned }}
                    </p>
                </div>
            </div>

            {{-- Reports List --}}
            <div class="card">
                <div class="p-5 border-b border-gray-100 dark:border-navy-700/50
                            flex items-center justify-between">
                    <h3 class="section-title">Open Reports</h3>

                    {{-- Type filter tabs --}}
                    <div class="flex gap-1 bg-gray-100 dark:bg-navy-800 p-1 rounded-lg">
                        @foreach (['all', 'message', 'resource', 'user'] as $filter)
                            <a href="{{ route('moderation.dashboard',
                                            $filter !== 'all' ? ['type' => $filter] : []) }}"
                               class="text-xs px-3 py-1.5 rounded-md font-medium
                                      transition-colors
                                      {{ request('type', 'all') === $filter
                                         ? 'bg-white dark:bg-navy-700 text-gray-900
                                            dark:text-gray-100 shadow-sm'
                                         : 'text-gray-500 dark:text-gray-400
                                            hover:text-gray-700' }}">
                                {{ ucfirst($filter) }}
                            </a>
                        @endforeach
                    </div>
                </div>

                @if ($reports->isEmpty())
                    {{-- Empty state --}}
                    <div class="p-12 text-center">
                        <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-900/20
                                    rounded-full flex items-center justify-center
                                    mx-auto mb-4">
                            <svg class="w-7 h-7 text-emerald-500" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0
                                         0118 0z"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-900 dark:text-gray-100">
                            All clear!
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            No open reports right now.
                        </p>
                    </div>
                @else
                    <div class="divide-y divide-gray-50 dark:divide-navy-700/30">
                        @foreach ($reports as $report)
                            <div class="p-5 hover:bg-gray-50/50
                                        dark:hover:bg-navy-800/30
                                        transition-colors duration-150">
                                <div class="flex items-start gap-4">

                                    {{-- Type icon --}}
                                    <div class="w-9 h-9 rounded-lg flex items-center
                                                justify-center flex-shrink-0
                                                bg-amber-50 dark:bg-amber-900/20">
                                        @if ($report->reported_type === 'message')
                                            <svg class="w-4 h-4 text-amber-500"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M8 12h.01M12 12h.01M16
                                                         12h.01M21 12c0 4.418-4.03
                                                         8-9 8a9.863 9.863 0
                                                         01-4.255-.949L3 20l1.395
                                                         -3.72C3.512 15.042 3
                                                         13.574 3 12c0-4.418 4.03
                                                         -8 9-8s9 3.582 9 8z"/>
                                            </svg>
                                        @elseif ($report->reported_type === 'resource')
                                            <svg class="w-4 h-4 text-amber-500"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M9 12h6m-6 4h6m2 5H7a2
                                                         2 0 01-2-2V5a2 2 0 012
                                                         -2h5.586a1 1 0 01.707.293
                                                         l5.414 5.414a1 1 0
                                                         01.293.707V19a2 2 0
                                                         01-2 2z"/>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 text-amber-500"
                                                 fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round"
                                                      stroke-linejoin="round"
                                                      stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0
                                                         018 0zM12 14a7 7 0 00-7
                                                         7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        @endif
                                    </div>

                                    {{-- Content --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2
                                                    flex-wrap">
                                            <span class="text-sm font-semibold
                                                         text-gray-900
                                                         dark:text-gray-100">
                                                {{ ucfirst($report->reported_type) }}
                                                reported
                                            </span>
                                            @if ($report->reason)
                                                <span class="badge badge-amber text-xs">
                                                    {{ str_replace('_', ' ',
                                                       $report->reason) }}
                                                </span>
                                            @endif
                                            <span class="text-xs text-gray-400
                                                         ml-auto flex-shrink-0">
                                                {{ $report->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500
                                                   dark:text-gray-400 mt-0.5">
                                            Reported by
                                            <span class="font-medium text-gray-700
                                                         dark:text-gray-300">
                                                {{ $report->reporter
                                                   ?->preferredDisplayName()
                                                   ?? 'Unknown' }}
                                            </span>
                                        </p>
                                        @if ($report->notes)
                                            <p class="text-xs text-gray-600
                                                       dark:text-gray-400
                                                       bg-gray-50 dark:bg-navy-800
                                                       rounded-lg p-2.5 mt-2
                                                       border border-gray-100
                                                       dark:border-navy-700">
                                                {{ $report->notes }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex gap-2 mt-3 ml-13">
                                    <form method="POST"
                                          action="{{ route('moderation.resolve',
                                                          $report) }}"
                                          class="inline-flex items-center gap-1.5">
                                        @csrf
                                        <input type="hidden"
                                               name="resolution"
                                               value="actioned">
                                        <input type="text"
                                               name="resolution_note"
                                               maxlength="1000"
                                               class="text-xs input-field !py-1 w-44"
                                               placeholder="Add a note (optional)">
                                        <button type="submit"
                                                class="btn-primary !text-xs
                                                       !px-3 !py-1.5">
                                            ✓ Action
                                        </button>
                                    </form>
                                    <form method="POST"
                                          action="{{ route('moderation.resolve',
                                                          $report) }}">
                                        @csrf
                                        <input type="hidden"
                                               name="resolution"
                                               value="dismissed">
                                        <button type="submit"
                                                class="btn-secondary !text-xs
                                                       !px-3 !py-1.5">
                                            Dismiss
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-5 py-3 border-t border-gray-100
                                dark:border-navy-700/50">
                        {{ $reports->links() }}
                    </div>
                @endif
            </div>

            {{-- Suspend User — FIXED: user search replaces raw User ID --}}
            <div class="card p-6">
                <h3 class="section-title mb-1">Suspend a User</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Search by name or email. Only students in your
                    program can be suspended.
                </p>

                <form method="POST" action="{{ route('moderation.suspend') }}"
                      class="space-y-4"
                      x-data="userSearch()">
                    @csrf

                    {{-- User search input --}}
                    <div class="relative">
                        <label class="label-text">Search Student</label>
                        <input type="text"
                               x-model="query"
                               @input.debounce.300ms="search"
                               @keydown.escape="results = []"
                               class="input-field"
                               placeholder="Type a name or email…"
                               autocomplete="off">

                        {{-- Dropdown --}}
                        <div x-show="results.length > 0"
                             x-cloak
                             class="absolute z-20 mt-1 w-full bg-white
                                    dark:bg-navy-800 rounded-xl shadow-lg
                                    border border-gray-100 dark:border-navy-700
                                    divide-y divide-gray-50
                                    dark:divide-navy-700/50
                                    max-h-48 overflow-y-auto">
                            <template x-for="u in results" :key="u.id">
                                <button type="button"
                                        @click="select(u)"
                                        class="w-full text-left px-4 py-2.5
                                               hover:bg-gray-50
                                               dark:hover:bg-navy-700
                                               transition-colors">
                                    <p class="text-sm font-medium text-gray-900
                                               dark:text-gray-100"
                                       x-text="u.display_name || u.name"></p>
                                    <p class="text-xs text-gray-500
                                               dark:text-gray-400"
                                       x-text="u.email"></p>
                                </button>
                            </template>
                        </div>
                    </div>

                    {{-- Hidden user_id --}}
                    <input type="hidden" name="user_id" x-model="selectedId">

                    {{-- Selected user chip --}}
                    <div x-show="selectedName" x-cloak
                         class="flex items-center gap-2 bg-emerald-50
                                dark:bg-emerald-900/20 border border-emerald-200
                                dark:border-emerald-800/50 rounded-lg
                                px-3 py-2 text-sm">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0"
                             fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2"
                                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12
                                     14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="font-medium text-emerald-700
                                     dark:text-emerald-300"
                              x-text="selectedName"></span>
                        <button type="button" @click="clear"
                                class="ml-auto text-emerald-400
                                       hover:text-emerald-600">
                            ✕
                        </button>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="label-text">Duration</label>
                            <select name="days" class="input-field">
                                <option value="1">1 day</option>
                                <option value="3">3 days</option>
                                <option value="7" selected>7 days</option>
                                <option value="14">14 days</option>
                                <option value="30">30 days</option>
                                <option value="90">90 days</option>
                            </select>
                        </div>
                        <div class="flex-1">
                            <label class="label-text">Reason (optional)</label>
                            <input type="text" name="reason" maxlength="500"
                                   class="input-field"
                                   placeholder="e.g. Spam, harassment">
                        </div>
                    </div>

                    <button type="submit"
                            class="btn-primary !bg-red-500 hover:!bg-red-600"
                            :disabled="!selectedId"
                            :class="!selectedId
                                    ? 'opacity-40 cursor-not-allowed' : ''">
                        Suspend User
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
function userSearch() {
    return {
        query:        '',
        results:      [],
        selectedId:   '',
        selectedName: '',

        async search() {
            if (this.query.length < 2) { this.results = []; return; }
            const res = await fetch(
                `/moderation/users/search?q=${encodeURIComponent(this.query)}`,
                { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
            );
            this.results = await res.json();
        },

        select(user) {
            this.selectedId   = user.id;
            this.selectedName = user.display_name || user.name;
            this.results      = [];
            this.query        = '';
        },

        clear() {
            this.selectedId   = '';
            this.selectedName = '';
        }
    };
}
</script>
@endpush
```

---

## Change 6 — Add `$resolvedToday` + `$totalActioned` to ModerationController (15 min)

**File:** `app/Http/Controllers/ModerationController.php` — update `dashboard()`:

```php
public function dashboard(HttpRequest $request): View
{
    $user = $request->user();

    $query = Report::with(['reporter:id,display_name,name'])
        ->where('status', ReportStatus::Open)
        ->where('school_id', $user->school_id)
        ->when(
            $user->isModerator(),
            fn ($q) => $q->where('program_id', $user->program_id)
        )
        ->when(
            $request->filled('type'),
            fn ($q) => $q->where('reported_type', $request->type)
        );

    $reports = $query->latest()->paginate(15);

    $resolvedToday = Report::where('school_id', $user->school_id)
        ->when(
            $user->isModerator(),
            fn ($q) => $q->where('program_id', $user->program_id)
        )
        ->where('status', '!=', ReportStatus::Open)
        ->whereDate('updated_at', today())
        ->count();

    $totalActioned = Report::where('school_id', $user->school_id)
        ->when(
            $user->isModerator(),
            fn ($q) => $q->where('program_id', $user->program_id)
        )
        ->where('status', '!=', ReportStatus::Open)
        ->count();

    return view('moderation.dashboard', compact(
        'reports', 'resolvedToday', 'totalActioned'
    ));
}
```

---

## Change 7 — Add User Search Endpoint (20 min)

**File:** `app/Http/Controllers/ModerationController.php` — add method:

```php
public function userSearch(HttpRequest $request): JsonResponse
{
    $user  = $request->user();
    $query = $request->string('q')->trim();

    abort_unless(
        $user !== null && ($user->isModerator() || $user->isProgramHead()),
        403
    );
    abort_if($query->isEmpty(), 422);

    $users = User::where('program_id', $user->program_id)
        ->where('role', UserRole::Student)
        ->where(function ($q) use ($query): void {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('display_name', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%");
        })
        ->select('id', 'name', 'display_name', 'email')
        ->limit(8)
        ->get();

    return response()->json($users);
}
```

**File:** `routes/web.php` — add inside the moderation route group:

```php
Route::get('/moderation/users/search',
           [ModerationController::class, 'userSearch'])
    ->name('moderation.users.search');
```

---

## Future UI Work — Bigger Efforts (Next Sprint)

These are the larger improvements that require more planning time
and are best handled as a dedicated UI sprint:

### Admin Sidebar Layout (~4 hrs)

Create `resources/views/layouts/admin.blade.php` with a
dark sidebar for Program Head, Dean, and SAO. Each sidebar
shows role-specific quick links with unread count badges.

```
┌──────────────────────────────────────────────────┐
│ Top nav (logo, notifications, profile)            │
├──────────────────────────────────────────────────┤
│ Role banner (full width)                          │
├────────────────┬─────────────────────────────────┤
│  Dark sidebar  │                                  │
│                │     Main content area            │
│  • Dashboard   │                                  │
│  • Feedback  3 │                                  │
│  • Reports     │                                  │
│  • Manage      │                                  │
│                │                                  │
└────────────────┴─────────────────────────────────┘
```

Sidebar colors per role:
- Program Head: `bg-[#1E2D3D]` (dark navy)
- Dean: `bg-[#312E81]` (deep indigo)
- SAO: `bg-[#1E293B]` (dark slate)

### Enriched Stat Cards With Icons (~2 hrs)

Each stat card gets a role-colored icon in a tinted circle,
color-coded number values (red for alerts, amber for warnings,
emerald for positive), and a small trend label where applicable.

### Activity Chart (~2 hrs)

A 30-day line chart (Chart.js, already in npm deps) showing
active users or report volume — placed below the stat grid on
each dashboard. Gives administrators a visual sense of platform
activity at a glance.

### Quick Action Panels (~2 hrs)

A row of shortcut cards directly below the stat grid:
"View Feedback", "Open Reports", "Add Moderator", "Resources".
Removes the need to navigate away for common tasks.

### Table Search and Filter (~2 hrs)

Live search input on the Moderators table (Program Head panel),
Programs grid (Dean panel), and Users list (SAO panel).

---

## Implementation Order

### This Sprint (Short — ~2.5 hrs total)

| # | Change | File | Effort |
|---|--------|------|--------|
| 1 | Fix `stat-card::before` accent color | `app.css` | 2 min |
| 2 | Add `panel-moderator` CSS | `app.css` | 5 min |
| 3 | Add `panel-moderator` to `panelClass()` | `UserRole.php` | 2 min |
| 4 | Add Moderator to role context banner | `role-context-banner.blade.php` | 5 min |
| 5 | Add `$resolvedToday` + `$totalActioned` to `ModerationController` | `ModerationController.php` | 15 min |
| 6 | Add `userSearch()` endpoint to `ModerationController` | `ModerationController.php` | 20 min |
| 7 | Add search route to `web.php` | `web.php` | 2 min |
| 8 | Rebuild `moderation/dashboard.blade.php` | `moderation/dashboard.blade.php` | 60–90 min |

### Next Sprint (Larger — ~10–12 hrs)

| # | Change | Effort |
|---|--------|--------|
| 9 | Create `layouts/admin.blade.php` with sidebar | 4 hrs |
| 10 | Apply sidebar layout to Program Head, Dean, SAO views | 2 hrs |
| 11 | Enriched stat cards with icons per role | 2 hrs |
| 12 | 30-day activity chart per dashboard | 2 hrs |
| 13 | Quick action panels | 2 hrs |
| 14 | Table search / filter controls | 2 hrs |

---

## Final Role UI Status — All 6 Roles

| Role | Accent | Panel CSS | Banner | Sidebar | Charts | Quality |
|------|--------|-----------|--------|---------|--------|---------|
| Student | Orange `#FF6B35` | *(default)* | None | No | No | ✅ Polished |
| Moderator | Emerald `#059669` | ❌ missing | ❌ missing | No | No | 🔴 Needs this sprint |
| Program Head | Navy `#2D4258` | ✅ done | ✅ done | ❌ future | ❌ future | 🟡 Functional stub |
| Dean | Indigo `#4338CA` | ✅ done | ✅ done | ❌ future | ❌ future | 🟡 Functional stub |
| SAO | Slate `#475569` | ✅ done | ✅ done | ❌ future | ❌ future | 🟡 Functional stub |
| Super Admin | Dark `#1F2937` | ✅ done | ✅ done | No | No | ⚪ Dev only |

---

*Plan issued 2026-05-30.*
*Combines: STUDHUB_ADMIN_UI_ASSESSMENT.md + STUDHUB_MODERATOR_UI_PLAN.md.*
*Backend status based on commit `79cad23`.*

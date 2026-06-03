# StudHub — Sprint B Full Review
> Commit `1b853f6` (latest main, 2026-05-30)
> Covers: UI/UX · Functionality · Features · Organization

---

## Overall Verdict: ✅ Mostly Good — 4 Issues to Fix

The Sprint B implementation is solid. The layout system is
correct, all icons resolve, all routes exist, chart data is
properly wired, and the sidebar background colors per role
are applied correctly. There are 4 issues — one syntax bug
that will crash a page, two UX problems with raw ID inputs,
and one layout consistency gap across the secondary pages.

---

## ✅ What Is Confirmed Working

**Layout system** — All three dashboards correctly use
`@extends('layouts.admin')` with `@section('sidebar')`,
`@section('pageHeader')`, and `@section('content')`, matching
exactly what `admin.blade.php` yields. No slot mismatch.

**Sidebar colors per role** — CSS correctly applies:
- Program Head: `#1E2D3D` (dark navy) ✅
- Dean: `#312E81` (deep indigo) ✅
- SAO: `#1E293B` (dark slate) ✅

**All 14 icons resolve** — `home`, `feedback`, `flag`,
`resources`, `leaderboard`, `chat`, `profile`, `college`,
`users`, `moderation`, `building`, `megaphone`, `shield`,
`chart-bar` — all present in `icon.blade.php`. ✅

**All routes exist** — Every `route()` call in all three
dashboards maps to a named route in `web.php`. ✅

**Chart data** — All three controllers correctly query
`last_seen_at` (confirmed present on `users` table since
the original `add_studhub_fields` migration) and pass
`$chartData` to the view. ✅

**SAO colleges overview** — `College::withCount(['programs',
'users'])` is used, so `$college->program_count` and
`$college->active_user_count` are both populated. ✅

**Duplicate Dean/Program Head guard** — `assignRole()`
correctly checks for existing Dean or Program Head per
college before assigning. ✅

**Flash messages** — All three success/error/validation
flash blocks are present in `admin.blade.php`. ✅

**Mobile sidebar** — Sidebar is `hidden md:flex`, so it
collapses on mobile and content fills full width. ✅

---

## 🔴 Bug 1 — Blade Syntax Error in `sao/users.blade.php`

**File:** `resources/views/sao/users.blade.php`, line 69

The `@forelse` closing tag is written as an HTML tag instead
of a Blade directive. This will throw a parse error when the
SAO users page loads.

```blade
{{-- BROKEN — causes Blade parse error --}}
</endforelse>

{{-- FIXED --}}
@endforelse
```

**Impact:** The entire `/sao/users` page crashes with a
500 error for every SAO account.

---

## 🔴 Bug 2 — `dean/programs.blade.php` Uses Raw User ID Input

**File:** `resources/views/dean/programs.blade.php`, lines 40–47

The "Assign Program Head" form asks for a raw numeric User ID:

```blade
{{-- CURRENT — unusable, nobody knows their database ID --}}
<label class="label-text">User ID</label>
<input type="number" name="user_id" required class="input-field"
       placeholder="User ID">
<label class="label-text">Program ID</label>
<input type="number" name="program_id" required class="input-field"
       placeholder="Program ID">
```

This is the same problem that was fixed in the moderation
dashboard (suspend form) during Sprint A. The Dean has no
way to know a user's numeric database ID or a program's ID.

**Fix** — Replace with a user search input (same Alpine.js
pattern used in the moderation suspend form) and a program
dropdown:

```blade
{{-- FIXED --}}
<div class="card p-6" x-data="userSearch()">
    <h3 class="section-title mb-4">Assign Program Head</h3>
    <form method="POST"
          action="{{ route('dean.program_heads.assign') }}"
          class="space-y-4">
        @csrf

        {{-- User search --}}
        <div class="relative">
            <label class="label-text">Search User</label>
            <input type="text"
                   x-model="query"
                   @input.debounce.300ms="search"
                   @keydown.escape="results = []"
                   class="input-field"
                   placeholder="Type a name or email…"
                   autocomplete="off">
            <div x-show="results.length > 0" x-cloak
                 class="absolute z-20 mt-1 w-full bg-white
                        dark:bg-navy-800 rounded-xl shadow-lg
                        border border-gray-100 dark:border-navy-700
                        max-h-48 overflow-y-auto divide-y
                        divide-gray-50 dark:divide-navy-700/50">
                <template x-for="u in results" :key="u.id">
                    <button type="button" @click="select(u)"
                            class="w-full text-left px-4 py-2.5
                                   hover:bg-gray-50 dark:hover:bg-navy-700
                                   transition-colors">
                        <p class="text-sm font-medium"
                           x-text="u.display_name || u.name"></p>
                        <p class="text-xs text-gray-500"
                           x-text="u.email"></p>
                    </button>
                </template>
            </div>
        </div>

        <input type="hidden" name="user_id" x-model="selectedId">

        {{-- Selected chip --}}
        <div x-show="selectedName" x-cloak
             class="flex items-center gap-2 bg-indigo-50
                    dark:bg-indigo-900/20 border border-indigo-200
                    dark:border-indigo-800/50 rounded-lg px-3 py-2">
            <span class="text-sm font-medium text-indigo-700
                         dark:text-indigo-300"
                  x-text="selectedName"></span>
            <button type="button" @click="clear"
                    class="ml-auto text-indigo-400 hover:text-indigo-600">
                ✕
            </button>
        </div>

        {{-- Program dropdown --}}
        <div>
            <label class="label-text">Program</label>
            <select name="program_id" required class="input-field">
                <option value="">Select a program…</option>
                @foreach ($programs as $program)
                    <option value="{{ $program->id }}">
                        {{ $program->code }} — {{ $program->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn-primary"
                :disabled="!selectedId"
                :class="!selectedId ? 'opacity-40 cursor-not-allowed' : ''">
            Assign Program Head
        </button>
    </form>
</div>

@push('scripts')
<script>
function userSearch() {
    return {
        query: '', results: [], selectedId: '', selectedName: '',
        async search() {
            if (this.query.length < 2) { this.results = []; return; }
            const res = await fetch(
                `/moderation/users/search?q=${encodeURIComponent(this.query)}`,
                { headers: { 'X-Requested-With': 'XMLHttpRequest' } }
            );
            this.results = await res.json();
        },
        select(u) {
            this.selectedId = u.id;
            this.selectedName = u.display_name || u.name;
            this.results = []; this.query = '';
        },
        clear() { this.selectedId = ''; this.selectedName = ''; }
    };
}
</script>
@endpush
```

Note: the `/moderation/users/search` endpoint already exists
from Sprint A. The Dean can reuse it since it searches within
the same college scope.

---

## 🟡 Issue 3 — Secondary Pages Still Use `x-app-layout`

**Files:** `feedback.blade.php` (all 3 roles), `sao/users.blade.php`,
`dean/programs.blade.php`, `sao/announcements.blade.php`

All secondary admin pages (feedback inbox, user management,
programs, announcements) still use `<x-app-layout>` instead
of `@extends('layouts.admin')`. When a Dean or Program Head
navigates away from the dashboard to their feedback page,
the dark sidebar disappears and they are back to the full-width
student layout. The visual identity is lost immediately.

**Fix** — Convert each secondary page to use the admin layout.
Pattern for each file:

```blade
{{-- BEFORE --}}
<x-app-layout>
    <x-slot name="header">
        <h2 ...>Page Title</h2>
    </x-slot>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            ...content...
        </div>
    </div>
</x-app-layout>

{{-- AFTER --}}
@extends('layouts.admin')

@section('sidebar')
    {{-- Copy the sidebar section from the dashboard view --}}
    @include('program-head._sidebar')  {{-- or inline it --}}
@endsection

@section('pageHeader')
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">
                Page Title
            </h1>
        </div>
    </div>
@endsection

@section('content')
    ...content without the max-w wrapper (admin-content handles padding)...
@endsection
```

**Files to convert:**

| File | Role |
|------|------|
| `program-head/feedback.blade.php` | Program Head |
| `dean/feedback.blade.php` | Dean |
| `dean/programs.blade.php` | Dean |
| `sao/feedback.blade.php` | SAO |
| `sao/users.blade.php` | SAO |
| `sao/announcements.blade.php` | SAO |

**Recommended approach** — extract each sidebar into a
shared partial to avoid repeating it in every file:

```
resources/views/program-head/_sidebar.blade.php
resources/views/dean/_sidebar.blade.php
resources/views/sao/_sidebar.blade.php
```

Then include it in both the dashboard and all secondary pages:

```blade
@section('sidebar')
    @include('program-head._sidebar')
@endsection
```

---

## 🟡 Issue 4 — Sticky Sidebar Height Offset

**File:** `resources/css/app.css`, lines 386 and 394

The admin layout uses `calc(100vh - 112px)` for both
`min-height` and `height` on the sidebar. The actual
combined height of the navigation bar (`h-16` = 64px)
plus the role context banner (`py-1.5 text-xs` ≈ 28px)
is approximately 92px — not 112px.

The 20px discrepancy means the sidebar is shorter than the
viewport and may show a gap at the bottom, or the sticky
sidebar may not reach the bottom of the page correctly.

**Fix:**

```css
/* Option A — use CSS custom property set by JS (most accurate) */
.admin-layout {
    min-height: calc(100vh - var(--header-height, 92px));
}
.admin-sidebar {
    height: calc(100vh - var(--header-height, 92px));
}

/* Option B — simple fix, close enough for most screens */
.admin-layout {
    min-height: calc(100vh - 92px);
}
.admin-sidebar {
    height: calc(100vh - 92px);
}
```

Or measure the actual header in JS once and set the property:

```blade
{{-- In admin.blade.php, after the nav include --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nav = document.querySelector('nav');
        const banner = document.querySelector('[data-role-banner]');
        const total = (nav?.offsetHeight ?? 64) + (banner?.offsetHeight ?? 0);
        document.documentElement.style.setProperty('--header-height', total + 'px');
    });
</script>
```

---

## Summary Table

| # | Issue | Severity | File | Fix |
|---|-------|----------|------|-----|
| 1 | `</endforelse>` Blade syntax error crashes SAO users page | 🔴 Critical | `sao/users.blade.php` line 69 | Change to `@endforelse` |
| 2 | Dean assign Program Head uses raw User ID input | 🔴 Bug | `dean/programs.blade.php` | User search + program dropdown |
| 3 | Secondary pages lose sidebar when navigating from dashboard | 🟡 UX | 6 feedback/users/programs views | Convert to `@extends('layouts.admin')` |
| 4 | Sticky sidebar height offset is 112px vs actual ~92px | 🟡 Minor | `app.css` | Update calc value |

---

## Fix Priority

**Fix now (before showing to any admin):**
1. `</endforelse>` → `@endforelse` in `sao/users.blade.php`
2. Dean programs assign form — user search + program dropdown

**Fix soon (noticeable UX gap):**
3. Convert 6 secondary pages to admin layout with sidebar
4. Sidebar height offset correction

---

## What Is Not a Problem (Confirmed Fine)

- `color-mix()` in CSS — supported in all modern browsers
  (Chrome 111+, Firefox 113+, Safari 16.2+). School systems
  likely use modern browsers. No fallback needed urgently.
- `leaderboard` route name — confirmed as `route('leaderboard')`
  not `route('leaderboard.index')`. Sidebar link is correct.
- `profile.show` route — exists. Sidebar link is correct.
- SAO filter showing `sao` as a role option — this is a filter
  only, not an assign form. SAO can legitimately filter by
  SAO role to see SAO accounts. Not a security issue.
- `$chartData[6]` index access — `collect(range(6, 0))` always
  produces 7 items (indices 0–6), so `$chartData[6]` (today)
  is always safe. No null risk.
- Mobile layout — sidebar correctly hidden on mobile via
  `hidden md:flex`. Content fills full width on small screens.

---

*Review completed 2026-05-30. Based on commit `1b853f6`.*

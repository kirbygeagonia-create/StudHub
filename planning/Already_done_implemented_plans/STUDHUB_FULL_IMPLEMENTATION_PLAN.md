# StudHub — Full Role Hierarchy Implementation Plan
> Based on codebase audit of commit `origin/main` (2026-05-27).  
> Stack: Laravel 11 · PHP 8.2 · Livewire 3 · Tailwind CSS · Alpine.js · Laravel Reverb · MySQL 8

---

## 0. Executive Summary

The codebase is well-structured (DDD, 221 tests, PHPStan L6 clean). The role system already has
`super_admin` and the `college_id`/`program_id` FK columns on `users` already exist.
What is missing is: (1) the `dean` and `sao` role enum values, (2) scoped queries for each role,
(3) a feedback routing chain, and (4) distinct panels with their own visual identities.

**No structural rewrites are needed.** Every change below is additive or a targeted edit.

---

## 1. Current vs. Target Role Mapping

| Old Label | Old Value | New Label | New Value | Scope |
|-----------|-----------|-----------|-----------|-------|
| Student | `student` | Student | `student` | `program_id` + `year_level` |
| Program Moderator | `moderator` | Program Moderator | `moderator` | `program_id` |
| Program Head / Dean *(confusingly combined)* | `admin` | Program Head | `program_head` | `program_id` |
| *(new)* | — | Dean | `dean` | `college_id` |
| *(new)* | — | SAO | `sao` | `school_id` (campus-wide) |
| Super Admin | `super_admin` | Super Admin | `super_admin` | unrestricted |

> **Note on renaming `admin` → `program_head`:** MySQL allows altering ENUM columns.
> Existing `admin` rows must be migrated to `program_head` before removing `admin` from the enum.
> The migration below handles this in a single transaction.

---

## 2. Answer: Should Each Role Have a Different UI Feel?

**Yes — absolutely. Here is the rationale and the design spec for each.**

Each panel serves people with fundamentally different mental models:
- A **student** is browsing and contributing; warm, motivational UI.
- A **Program Moderator** is reviewing reports; slightly elevated, same approachable feel.
- A **Program Head** is managing a program; professional, data-dense sidebar layout.
- A **Dean** is overseeing a whole college; authoritative, college-branded header.
- **SAO** is the institutional safety authority; institutional, clean, formal.
- **Super Admin** is the technical system owner; dark, minimal, developer-adjacent.

All six share the same navigation shell (`x-app-layout`) and Tailwind base. The distinction
comes from a **role-aware layout skin** (a CSS class on `<body>` or the page wrapper) and
**accent color overrides** — not full rebuilds.

### 2.1 Color Tokens per Role

| Role | Accent Color | Tailwind Class | Body Modifier Class |
|------|-------------|----------------|---------------------|
| Student | Seait orange (`#FF6B35`) | `seait-500` | *(default, no override)* |
| Moderator | Seait orange + green badge | `seait-500` + `emerald-500` | `panel-moderator` |
| Program Head | Navy blue | `navy-700` | `panel-program-head` |
| Dean | Indigo / royal blue | `indigo-600` | `panel-dean` |
| SAO | Slate / institutional gray | `slate-700` | `panel-sao` |
| Super Admin | Dark neutral | `gray-900` | `panel-super` |

### 2.2 Layout Shape per Role

| Role | Layout | Navigation | Header |
|------|--------|-----------|--------|
| Student / Moderator | Full-width `max-w-7xl`, horizontal nav | Top bar (current) | Welcome header |
| Program Head | Sidebar + content area, `max-w-6xl` | Top bar + quick-action sidebar | Scope badge: program code |
| Dean | Sidebar + content area | Top bar + college-scoped sidebar | Scope badge: college name |
| SAO | Full-width, two-column | Top bar | "SAO Dashboard — SEAIT" header |
| Super Admin | Full-width, dark surface | Top bar + system tabs | "System Administration" header |

---

## 3. Phase 1 — Role Enum & Model Update

### 3.1 New `UserRole` Enum
**File:** `app/Domain/Identity/Enums/UserRole.php`

```php
enum UserRole: string
{
    case Student      = 'student';
    case Moderator    = 'moderator';
    case ProgramHead  = 'program_head';   // renamed from 'admin'
    case Dean         = 'dean';           // NEW
    case Sao          = 'sao';            // NEW
    case SuperAdmin   = 'super_admin';

    public function label(): string
    {
        return match ($this) {
            self::Student     => 'Student',
            self::Moderator   => 'Program Moderator',
            self::ProgramHead => 'Program Head',
            self::Dean        => 'College Dean',
            self::Sao         => 'Safety & Security Office',
            self::SuperAdmin  => 'Super Admin',
        };
    }

    public function panelClass(): string
    {
        return match ($this) {
            self::Student, self::Moderator => '',
            self::ProgramHead => 'panel-program-head',
            self::Dean        => 'panel-dean',
            self::Sao         => 'panel-sao',
            self::SuperAdmin  => 'panel-super',
        };
    }

    /** Roles that have at least the given role's permissions (inheritance chain). */
    public function inheritedRoles(): array
    {
        return match ($this) {
            self::SuperAdmin  => ['super_admin', 'sao', 'dean', 'program_head', 'moderator'],
            self::Sao         => ['sao', 'dean', 'program_head', 'moderator'],
            self::Dean        => ['dean', 'program_head', 'moderator'],
            self::ProgramHead => ['program_head', 'moderator'],
            self::Moderator   => ['moderator'],
            self::Student     => ['student'],
        };
    }

    public static function values(): array
    {
        return array_map(fn (self $role) => $role->value, self::cases());
    }
}
```

### 3.2 Update `User` Model
**File:** `app/Models/User.php` — update / add helper methods:

```php
public function isProgramHead(): bool
{
    return $this->role === UserRole::ProgramHead;
}

public function isDean(): bool
{
    return $this->role === UserRole::Dean;
}

public function isSao(): bool
{
    return $this->role === UserRole::Sao;
}

// Update isAdmin() to cover program_head + dean + sao + super_admin
public function isAdmin(): bool
{
    return in_array($this->role, [
        UserRole::ProgramHead,
        UserRole::Dean,
        UserRole::Sao,
        UserRole::SuperAdmin,
    ]);
}

// Scope: returns the program_id this user manages (null if they manage all)
public function managedProgramId(): ?int
{
    return $this->role === UserRole::ProgramHead ? $this->program_id : null;
}

// Scope: returns the college_id this user manages (null if they manage all)
public function managedCollegeId(): ?int
{
    return $this->role === UserRole::Dean ? $this->college_id : null;
}

// CSS panel class for role-aware UI
public function panelClass(): string
{
    return $this->role instanceof UserRole
        ? $this->role->panelClass()
        : '';
}
```

### 3.3 Update `EnsureHasRole` Middleware
**File:** `app/Http/Middleware/EnsureHasRole.php`

```php
// Add SAO to the bypass list (same as SuperAdmin — SAO passes any role check)
if ($user->isSuperAdmin() || $user->isSao()) {
    return $next($request);
}
```

---

## 4. Phase 2 — Database Migrations

### 4.1 Rename `admin` to `program_head` in users table
**New file:** `database/migrations/2026_05_29_000001_rename_admin_role_to_program_head.php`

```php
public function up(): void
{
    // Step 1: Extend enum to include new values + keep old ones temporarily
    DB::statement("ALTER TABLE users MODIFY COLUMN role
        ENUM('student','moderator','admin','program_head','dean','sao','super_admin')
        NOT NULL DEFAULT 'student'");

    // Step 2: Migrate existing data
    DB::statement("UPDATE users SET role = 'program_head' WHERE role = 'admin'");

    // Step 3: Remove old 'admin' from enum
    DB::statement("ALTER TABLE users MODIFY COLUMN role
        ENUM('student','moderator','program_head','dean','sao','super_admin')
        NOT NULL DEFAULT 'student'");
}

public function down(): void
{
    DB::statement("ALTER TABLE users MODIFY COLUMN role
        ENUM('student','moderator','admin','program_head','dean','sao','super_admin')
        NOT NULL DEFAULT 'student'");
    DB::statement("UPDATE users SET role = 'admin' WHERE role = 'program_head'");
    DB::statement("ALTER TABLE users MODIFY COLUMN role
        ENUM('student','moderator','admin','super_admin')
        NOT NULL DEFAULT 'student'");
}
```

### 4.2 Add Feedback Routing Fields
**New file:** `database/migrations/2026_05_29_000002_add_routing_to_feedback_table.php`

```php
public function up(): void
{
    Schema::table('feedback', function (Blueprint $table) {
        // Which role-level should receive/see this feedback
        $table->string('recipient_role', 20)->default('sao')->after('type');
        // College/program scope for routing
        $table->foreignId('recipient_college_id')->nullable()->constrained('colleges')->nullOnDelete();
        $table->foreignId('recipient_program_id')->nullable()->constrained('programs')->nullOnDelete();
        // Escalation chain
        $table->foreignId('escalated_from_id')->nullable()->constrained('feedback')->nullOnDelete();
        // Status
        $table->string('status', 16)->default('open'); // open | read | resolved | escalated
        $table->timestamp('read_at')->nullable();
        $table->timestamp('resolved_at')->nullable();
        $table->text('resolution_note')->nullable();

        $table->index(['recipient_role', 'status']);
        $table->index(['recipient_program_id', 'status']);
        $table->index(['recipient_college_id', 'status']);
    });
}
```

### 4.3 Add College Moderators Table (Dean Management)
**New file:** `database/migrations/2026_05_29_000003_create_college_deans_table.php`

```php
// Tracks which users serve as Dean for which college
// (mirrors program_moderators for programs)
Schema::create('college_deans', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('college_id')->constrained()->cascadeOnDelete();
    $table->foreignId('assigned_by_user_id')->constrained('users')->cascadeOnDelete();
    $table->timestamps();
    $table->unique(['user_id', 'college_id']);
});
```

### 4.4 Update Seeder for New Roles
**File:** `database/seeders/DevUsersSeeder.php` — add SAO and Dean dev accounts:

```php
// SAO dev account
User::updateOrCreate(['email' => 'sao@seait.edu.ph'], [
    'school_id' => $school->id,
    'name' => 'SAO Officer',
    'display_name' => 'SAO',
    'email_verified_at' => now(),
    'password' => $password,
    'role' => UserRole::Sao,
    'onboarded_at' => now(),
]);

// Dean dev account (assign to CICT)
$cict = College::where('code', 'CICT')->where('school_id', $school->id)->firstOrFail();
User::updateOrCreate(['email' => 'dean.cict@seait.edu.ph'], [
    'school_id' => $school->id,
    'college_id' => $cict->id,
    'name' => 'Dean CICT',
    'display_name' => 'Dean',
    'email_verified_at' => now(),
    'password' => $password,
    'role' => UserRole::Dean,
    'onboarded_at' => now(),
]);
```

---

## 5. Phase 3 — Feedback Routing Action

Update **`app/Domain/Feedback/Actions/SubmitFeedback.php`** to auto-route:

```php
public function handle(User $user, array $data): Feedback
{
    // ... existing validation ...

    [$recipientRole, $collegeId, $programId] = $this->resolveRecipient($user);

    return DB::transaction(function () use ($user, $body, $type, $recipientRole, $collegeId, $programId) {
        return Feedback::create([
            'user_id'              => $user->id,
            'type'                 => $type->value,
            'body'                 => $body,
            'recipient_role'       => $recipientRole,
            'recipient_college_id' => $collegeId,
            'recipient_program_id' => $programId,
            'status'               => 'open',
        ]);
    });
}

private function resolveRecipient(User $user): array
{
    return match ($user->role) {
        // Student → goes to their Program Head
        UserRole::Student, UserRole::Moderator =>
            ['program_head', $user->college_id, $user->program_id],

        // Program Head → goes to their Dean
        UserRole::ProgramHead =>
            ['dean', $user->college_id, null],

        // Dean → goes directly to SAO
        UserRole::Dean =>
            ['sao', null, null],

        // SAO / SuperAdmin → goes to SuperAdmin (system-level)
        default => ['super_admin', null, null],
    };
}
```

---

## 6. Phase 4 — Route Restructuring

### 6.1 Updated `routes/web.php` — New Route Groups

```php
// Program Head routes (was 'admin')
Route::middleware(['auth', 'verified', 'onboarded', 'role:program_head,super_admin'])->group(function () {
    Route::get('/program-head', [ProgramHeadController::class, 'dashboard'])->name('program_head.dashboard');
    Route::post('/program-head/moderators/assign', [ProgramHeadController::class, 'assignModerator'])->name('program_head.moderators.assign');
    Route::post('/program-head/moderators/remove', [ProgramHeadController::class, 'removeModerator'])->name('program_head.moderators.remove');
    Route::get('/program-head/feedback', [ProgramHeadController::class, 'feedback'])->name('program_head.feedback');
    Route::post('/program-head/feedback/{feedback}/resolve', [ProgramHeadController::class, 'resolveFeedback'])->name('program_head.feedback.resolve');
    Route::post('/program-head/feedback/{feedback}/escalate', [ProgramHeadController::class, 'escalateFeedback'])->name('program_head.feedback.escalate');
});

// Dean routes
Route::middleware(['auth', 'verified', 'onboarded', 'role:dean,super_admin'])->group(function () {
    Route::get('/dean', [DeanController::class, 'dashboard'])->name('dean.dashboard');
    Route::get('/dean/feedback', [DeanController::class, 'feedback'])->name('dean.feedback');
    Route::post('/dean/feedback/{feedback}/resolve', [DeanController::class, 'resolveFeedback'])->name('dean.feedback.resolve');
    Route::post('/dean/feedback/{feedback}/escalate', [DeanController::class, 'escalateFeedback'])->name('dean.feedback.escalate');
    Route::get('/dean/programs', [DeanController::class, 'programs'])->name('dean.programs');
    Route::post('/dean/program-heads/assign', [DeanController::class, 'assignProgramHead'])->name('dean.program_heads.assign');
});

// SAO routes
Route::middleware(['auth', 'verified', 'onboarded', 'role:sao,super_admin'])->group(function () {
    Route::get('/sao', [SaoController::class, 'dashboard'])->name('sao.dashboard');
    Route::get('/sao/feedback', [SaoController::class, 'feedback'])->name('sao.feedback');
    Route::post('/sao/feedback/{feedback}/resolve', [SaoController::class, 'resolveFeedback'])->name('sao.feedback.resolve');
    Route::get('/sao/announcements', [SaoController::class, 'announcements'])->name('sao.announcements');
    Route::post('/sao/announcements', [SaoController::class, 'storeAnnouncement'])->name('sao.announcements.store');
    Route::get('/sao/users', [SaoController::class, 'users'])->name('sao.users');
});

// Keep legacy /admin routes as redirects for backwards-compat
Route::middleware(['auth'])->group(function () {
    Route::redirect('/admin', '/program-head')->name('admin.dashboard');
    Route::redirect('/admin/super', '/admin/super-legacy');
});
```

---

## 7. Phase 5 — New Controllers

### 7.1 `ProgramHeadController.php` (replaces `AdminController`)
- `dashboard()` — scoped by `$user->program_id`, same data as current AdminController but narrower
- `assignModerator()` — can only assign moderators within own program
- `feedback()` — shows feedback where `recipient_role = 'program_head'` AND `recipient_program_id = $user->program_id`
- `resolveFeedback()` — marks as resolved
- `escalateFeedback()` — marks as escalated, creates new Feedback row routed to Dean

### 7.2 `DeanController.php` (new)
- `dashboard()` — college stats (programs count, student count, open reports, unread feedback count)
- `programs()` — lists all programs under their college
- `assignProgramHead()` — assigns/removes program_head role for users within their college
- `feedback()` — shows feedback where `recipient_role = 'dean'` AND `recipient_college_id = $user->college_id`
- `resolveFeedback()` / `escalateFeedback()`

### 7.3 `SaoController.php` (new)
- `dashboard()` — campus-wide stats, all unread feedback counts per level, open report counts
- `feedback()` — shows ALL feedback where `recipient_role = 'sao'` or escalated to SAO
- `users()` — full user list with search/filter (role, program, college)
- `announcements()` — manage system announcements (future: shown on dashboard)
- Dean assignment — SAO can assign/remove dean role for college accounts

---

## 8. Phase 6 — Panel UI Design Implementation

### 8.1 CSS additions to `resources/css/app.css`

```css
/* ============================================
   Panel Skin Overrides (role-aware UI)
   ============================================ */

/* Program Head — navy professional */
.panel-program-head {
    --panel-accent: #2D4258;
    --panel-accent-light: #4A6175;
    --panel-accent-hover: #1E2D3D;
    --panel-btn-bg: var(--panel-accent);
    --panel-badge-bg: #E3E8EF;
    --panel-badge-text: #2D4258;
}
.panel-program-head .btn-primary {
    background: linear-gradient(135deg, #2D4258, #1E2D3D);
}
.panel-program-head .stat-card {
    border-left: 3px solid #4A6175;
}

/* Dean — indigo/royal blue */
.panel-dean {
    --panel-accent: #4338CA;
    --panel-accent-light: #6366F1;
    --panel-badge-bg: #EEF2FF;
    --panel-badge-text: #4338CA;
}
.panel-dean .btn-primary {
    background: linear-gradient(135deg, #4338CA, #3730A3);
}
.panel-dean .stat-card {
    border-left: 3px solid #6366F1;
}

/* SAO — slate/institutional */
.panel-sao {
    --panel-accent: #475569;
    --panel-accent-light: #64748B;
    --panel-badge-bg: #F1F5F9;
    --panel-badge-text: #334155;
}
.panel-sao .btn-primary {
    background: linear-gradient(135deg, #475569, #334155);
}
.panel-sao .stat-card {
    border-left: 3px solid #64748B;
}

/* Super Admin — dark, minimal */
.panel-super {
    --panel-accent: #1F2937;
    --panel-badge-bg: #374151;
    --panel-badge-text: #F9FAFB;
}
.panel-super .btn-primary {
    background: linear-gradient(135deg, #374151, #1F2937);
}
```

### 8.2 Role Context Banner Component
**New file:** `resources/views/components/role-context-banner.blade.php`

```blade
@php $user = auth()->user(); @endphp

@if ($user?->isProgramHead())
    <div class="bg-navy-800 text-white text-xs px-4 py-1.5 flex items-center gap-2">
        <svg class="w-3.5 h-3.5 opacity-60" ...></svg>
        <span class="opacity-60">Program Head —</span>
        <span class="font-semibold">{{ $user->program?->code }}: {{ $user->program?->name }}</span>
    </div>
@elseif ($user?->isDean())
    <div class="bg-indigo-800 text-white text-xs px-4 py-1.5 flex items-center gap-2">
        <span class="opacity-60">Dean —</span>
        <span class="font-semibold">{{ $user->college?->code }}: {{ $user->college?->name }}</span>
    </div>
@elseif ($user?->isSao())
    <div class="bg-slate-700 text-white text-xs px-4 py-1.5 flex items-center gap-2">
        <span class="opacity-60">Safety & Security Office —</span>
        <span class="font-semibold">SEAIT Campus Administration</span>
    </div>
@elseif ($user?->isSuperAdmin())
    <div class="bg-gray-900 text-white text-xs px-4 py-1.5 flex items-center gap-2">
        <span class="font-semibold text-red-400">⚠ SYSTEM ADMINISTRATION MODE</span>
    </div>
@endif
```

### 8.3 Update `app.blade.php` Layout

In `<body>` tag, inject panel class and role banner:

```blade
<body class="font-sans antialiased text-gray-900 dark:text-gray-100 {{ auth()->user()?->panelClass() }}">
    <div class="min-h-screen">
        @auth
            <x-role-context-banner />
            @include('layouts.navigation')
        @endauth
        ...
    </div>
</body>
```

---

## 9. Phase 7 — Navigation Update

Update `resources/views/layouts/navigation.blade.php` — add new role-based links:

```blade
@if (Auth::user()?->isModerator() || Auth::user()?->isAdmin())
    <x-nav-link :href="route('moderation.dashboard')" ...>Moderation</x-nav-link>
@endif

@if (Auth::user()?->isProgramHead())
    <x-nav-link :href="route('program_head.dashboard')" ...>
        <x-icon name="admin" class="w-4 h-4 mr-1.5" /> Program Head
    </x-nav-link>
@endif

@if (Auth::user()?->isDean())
    <x-nav-link :href="route('dean.dashboard')" ...>
        <x-icon name="college" class="w-4 h-4 mr-1.5" /> Dean Panel
    </x-nav-link>
@endif

@if (Auth::user()?->isSao())
    <x-nav-link :href="route('sao.dashboard')" ...>
        <x-icon name="shield" class="w-4 h-4 mr-1.5" /> SAO Panel
    </x-nav-link>
@endif

@if (Auth::user()?->isSuperAdmin())
    <x-nav-link :href="route('admin.super')" ...>
        <x-icon name="admin" class="w-4 h-4 mr-1.5" /> System
    </x-nav-link>
@endif
```

---

## 10. Phase 8 — Scope Guard Helper

**New file:** `app/Domain/Identity/Support/ScopeGuard.php`

```php
namespace App\Domain\Identity\Support;

use App\Domain\Identity\Enums\UserRole;
use App\Models\User;

class ScopeGuard
{
    /**
     * Returns WHERE clause additions for scoping queries to the user's authority.
     * Returns null for unrestricted access (SAO, SuperAdmin).
     *
     * @return array<string, mixed>|null
     */
    public static function programScope(User $user): ?array
    {
        return match ($user->role) {
            UserRole::SuperAdmin, UserRole::Sao => null,
            UserRole::Dean      => null,  // Dean sees all programs in college; use collegeScope
            UserRole::ProgramHead, UserRole::Moderator => ['program_id' => $user->program_id],
            default             => ['program_id' => $user->program_id, 'year_level' => $user->year_level],
        };
    }

    public static function collegeScope(User $user): ?array
    {
        return match ($user->role) {
            UserRole::SuperAdmin, UserRole::Sao => null,
            UserRole::Dean      => ['college_id' => $user->college_id],
            default             => ['college_id' => $user->college_id],
        };
    }

    /**
     * Checks if $user can administer $target (based on role hierarchy and scope).
     */
    public static function canAdminister(User $user, User $target): bool
    {
        if ($user->isSuperAdmin() || $user->isSao()) {
            return true;
        }
        if ($user->isDean()) {
            return $target->college_id === $user->college_id
                && in_array($target->role->value, ['student', 'moderator', 'program_head']);
        }
        if ($user->isProgramHead()) {
            return $target->program_id === $user->program_id
                && in_array($target->role->value, ['student', 'moderator']);
        }
        return false;
    }
}
```

---

## 11. Phase 9 — Views to Create

| File | Role | Purpose |
|------|------|---------|
| `resources/views/program-head/dashboard.blade.php` | Program Head | Program stats, moderator management, recent reports |
| `resources/views/program-head/feedback.blade.php` | Program Head | Feedback inbox scoped to program |
| `resources/views/dean/dashboard.blade.php` | Dean | College overview, program list, stats |
| `resources/views/dean/feedback.blade.php` | Dean | Feedback inbox scoped to college |
| `resources/views/dean/programs.blade.php` | Dean | List programs, manage Program Heads |
| `resources/views/sao/dashboard.blade.php` | SAO | Campus-wide stats, feedback summary, all alerts |
| `resources/views/sao/feedback.blade.php` | SAO | All escalated feedback from across campus |
| `resources/views/sao/users.blade.php` | SAO | Full user list, role management |
| `resources/views/sao/announcements.blade.php` | SAO | Campus-wide announcements |
| `resources/views/components/role-context-banner.blade.php` | All | Role & scope indicator strip |

---

## 12. Feedback Inbox UX Rules

Each inbox level should show:
- Unread count badge on nav link
- Color-coded status chips: `open` (red), `read` (gray), `resolved` (green), `escalated` (amber)
- Submitter info: name, program, role
- "Escalate Up" button (routes a copy to the next level)
- "Resolve" button with optional note

**Escalation creates a new Feedback row** with `escalated_from_id` pointing to the original,
so the full chain is traceable at any level.

---

## 13. Dev Account Summary

| Email | Role | Scope |
|-------|------|-------|
| `test@seait.edu.ph` | Student | BSIT, Year 2 |
| `mod@seait.edu.ph` | Moderator | BSCE |
| `admin@seait.edu.ph` | Program Head | BSBA-MM *(old `admin`)* |
| `dean.cict@seait.edu.ph` | Dean | CICT college |
| `sao@seait.edu.ph` | SAO | Campus-wide |
| *(add manually)* | Super Admin | Unrestricted |

---

## 14. Implementation Order (Sprint Plan)

| Sprint | Tasks | Effort |
|--------|-------|--------|
| Sprint 1 | Phase 1 (enum + model) + Phase 2 (migrations) | 2–3 hours |
| Sprint 2 | Phase 3 (feedback routing action) + Phase 5 (ProgramHeadController) | 3–4 hours |
| Sprint 3 | Phase 7 (routes) + Phase 5 (DeanController) | 3–4 hours |
| Sprint 4 | Phase 5 (SaoController) + Phase 4 (route wiring) | 2–3 hours |
| Sprint 5 | Phase 6 (CSS skins) + Phase 8 (role context banner) | 2–3 hours |
| Sprint 6 | Phase 9 (views for Dean + SAO panels) | 4–6 hours |
| Sprint 7 | Tests: update existing + add new for dean/sao scoping | 3–4 hours |

**Total estimate: ~20–27 hours of development**

---

## 15. Test Updates Required

The existing 221 tests pass on the current `admin` role value.
After renaming, these must be updated:

- All factories that set `role => 'admin'` → `role => 'program_head'`
- Middleware tests in `tests/Feature/` for `EnsureHasRole`
- Add new tests:
  - Dean cannot see another college's feedback
  - Program Head cannot see another program's feedback
  - SAO can see all feedback
  - Escalation creates correct Feedback row with `escalated_from_id`
  - `ScopeGuard::canAdminister()` boundary conditions

---

## 16. What Does NOT Change

- Student experience: zero changes. Orange theme, same nav, same features.
- Moderator experience: same as today, just the nav link text updates.
- `program_moderators` pivot table stays as-is.
- All existing migrations remain unchanged.
- The `ProgramModerator` model stays as-is.
- `EnsureNotSuspended`, `EnsureUserIsOnboarded`, chat, resources, lends, requests, leaderboard — no changes.
- The 221 tests still run; only seeder/factory role strings need updating.

---

*Plan prepared 2026-05-29. Based on codebase at `https://github.com/kirbygeagonia-create/StudHub.git`.*

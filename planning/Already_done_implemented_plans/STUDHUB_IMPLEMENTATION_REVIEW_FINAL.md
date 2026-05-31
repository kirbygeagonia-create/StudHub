# StudHub — Full Implementation Review
> Reviewed commit: `fe3b50d` (latest main, 2026-05-29)
> Stack: Laravel 11 · PHP 8.2 · Livewire 3 · Tailwind CSS · Alpine.js · MySQL 8
> Status: **Ready to fix — 1 critical bug, 4 minor issues**

---

## Overall Verdict: ✅ Strong Foundation — Fix 1 Critical Bug Before Going Live

The role hierarchy implementation is well-structured and closely follows
the architectural plan. 233 tests pass. PHPStan Level 6 clean. Pint-formatted.

There is **1 critical bug** and **1 critical scope misalignment** that must
both be corrected before any real Dean, Program Head, or SAO accounts are used.
Everything else is minor or future work.

---

## School Structure Confirmation

Before reviewing the code, these are the confirmed facts about SEAIT's
authority structure that all decisions below are based on:

```
VPAA (Vice President for Academic Affairs)  ← future role; not yet implemented
  └── SAO (Safety & Security Office)        ← highest active school authority in system
        └── Dean                            ← 1 per college (6 total)
              └── Program Head              ← 1 per college (6 total)
                    └── Moderator           ← per program (assigned by Program Head)
                          └── Student       ← per program + year level
```

**Key facts:**
- There are **6 colleges** at SEAIT
- Each college has exactly **1 Dean** and **1 Program Head**
- Program Head manages **the entire college** — not one specific program
- Moderators remain **per-program** (assigned by the Program Head)
- SAO is the highest active authority in the system
- VPAA sits above SAO but is **not yet implemented** (planned for next sprint)
- Super Admin is **developer-only** — no school staff ever holds this role

### The 6 Colleges and Their Programs

| College | Code | Programs |
|---------|------|----------|
| College of Information and Communication Technology | CICT | BSIT, BSIT-BAST, ACT |
| Department of Civil Engineering | DCE | BSCE |
| College of Business and Good Governance | CBGG | BSBA-MM, BSAIS, BSHM, BSTM, AHM, BPA, BSSW |
| College of Teacher Education | CTE | BEEd, BECEd, BSEd-Eng, BSEd-Math, BSEd-SS, BSEd-Fil, BSEd-Sci, BTLEd-ICT |
| College of Agriculture and Fisheries | CAF | BSAgri-PBG, BSAgri-Horti, BSAgri-AS, BSAgri-CS, BSF, BSAT |
| College of Criminal Justice Education | CCJE | BSCrim |

### Account Scope Reference

| Role | Scope column used | What they see |
|------|------------------|---------------|
| Student | `program_id` + `year_level` | Own program data |
| Moderator | `program_id` | One program |
| Program Head | `college_id` | All programs in their college |
| Dean | `college_id` | All programs in their college |
| SAO | none | All colleges, all programs |
| Super Admin | none | Everything (developer only) |

---

## Critical Bug #1 — `hasCompletedOnboarding()` Blocks Dean, Program Head, and SAO

**File:** `app/Models/User.php`

**The problem:**

The current implementation requires `program_id` and `year_level` to
be non-null before any account is considered "onboarded." This was
written for students but never updated for the new administrative roles.
The `EnsureUserIsOnboarded` middleware calls this on every single request.
If it returns `false`, the user is permanently bounced to `/onboarding`
where they are asked to select a program and year level — fields that
simply do not apply to Dean, Program Head, or SAO.

```php
// CURRENT — broken for all admin roles
public function hasCompletedOnboarding(): bool
{
    return $this->onboarded_at !== null
        && $this->program_id !== null   // Dean has null · Program Head has null · SAO has null
        && $this->year_level !== null   // Dean has null · Program Head has null · SAO has null
        && $this->display_name !== null
        && $this->school_id !== null
        && $this->college_id !== null;  // SAO has null
}
```

**Who is blocked:**

| Role | `program_id` | `year_level` | `college_id` | Can log in? |
|------|------------|------------|------------|-------------|
| Student | ✅ set | ✅ set | ✅ set | ✅ Yes |
| Moderator | ✅ set | ✅ set | ✅ set | ✅ Yes |
| Program Head | ❌ null | ❌ null | ✅ set | ❌ **Blocked** |
| Dean | ❌ null | ❌ null | ✅ set | ❌ **Blocked** |
| SAO | ❌ null | ❌ null | ❌ null | ❌ **Blocked** |

This affects all 6 Dean accounts and all 6 Program Head accounts
across campus — not just the dev seeder. Any real account created
through the SAO panel will hit this wall on first login.

**The fix:**

```php
// FIXED — role-aware onboarding check
public function hasCompletedOnboarding(): bool
{
    // SAO and SuperAdmin: campus-wide scope — only need school_id + display_name
    if ($this->isSao() || $this->isSuperAdmin()) {
        return $this->onboarded_at !== null
            && $this->display_name !== null
            && $this->school_id !== null;
    }

    // Dean and Program Head: college-scoped — need college_id but NOT program_id or year_level
    if ($this->isDean() || $this->isProgramHead()) {
        return $this->onboarded_at !== null
            && $this->display_name !== null
            && $this->school_id !== null
            && $this->college_id !== null;
    }

    // Students and Moderators: need full profile including program + year
    return $this->onboarded_at !== null
        && $this->program_id !== null
        && $this->year_level !== null
        && $this->display_name !== null
        && $this->school_id !== null
        && $this->college_id !== null;
}
```

**Also update** `managedCollegeId()` and `managedProgramId()` helpers
to reflect the corrected scope:

```php
public function managedCollegeId(): ?int
{
    return ($this->isDean() || $this->isProgramHead())
        ? $this->college_id
        : null;
}

public function managedProgramId(): ?int
{
    // Program Head manages an entire college — not one specific program
    return null;
}
```

---

## Critical Scope Misalignment — Program Head Uses `program_id` Instead of `college_id`

**Files affected:** `ProgramHeadController.php`, `ScopeGuard.php`,
`SubmitFeedback.php`, `DevUsersSeeder.php`

**The problem:**

The current `ProgramHeadController` scopes every query by `program_id`
(one program). But Program Head manages the entire college — they need
to be scoped by `college_id`, the same as the Dean. There are about
15 places in the codebase where `$user->program_id` is used for Program
Head logic that must be changed to `$user->college_id`.

---

### Fix A — `app/Domain/Identity/Support/ScopeGuard.php`

```php
public static function programScope(User $user): ?array
{
    return match ($user->role) {
        UserRole::SuperAdmin,
        UserRole::Sao         => null,              // unrestricted
        UserRole::Dean,
        UserRole::ProgramHead => null,              // college-scoped via collegeScope() instead
        UserRole::Moderator   => ['program_id' => $user->program_id],
        default               => ['program_id' => $user->program_id],
    };
}

public static function collegeScope(User $user): ?array
{
    return match ($user->role) {
        UserRole::SuperAdmin,
        UserRole::Sao         => null,
        UserRole::Dean,
        UserRole::ProgramHead => ['college_id' => $user->college_id], // both same scope
        default               => ['college_id' => $user->college_id],
    };
}

public static function canAdminister(User $user, User $target): bool
{
    if ($user->isSuperAdmin() || $user->isSao()) {
        return true;
    }

    $targetRole = $target->role instanceof UserRole
        ? $target->role->value
        : (string) $target->role;

    // Dean: can manage anyone in their college at or below program_head level
    if ($user->isDean()) {
        return $target->college_id === $user->college_id
            && in_array($targetRole, ['student', 'moderator', 'program_head'], true);
    }

    // Program Head: can manage students and moderators in their college
    if ($user->isProgramHead()) {
        return $target->college_id === $user->college_id
            && in_array($targetRole, ['student', 'moderator'], true);
    }

    return false;
}
```

---

### Fix B — `app/Http/Controllers/ProgramHeadController.php`

Every `program_id` reference for scoping changes to `college_id`.
Full corrected controller:

```php
public function dashboard(HttpRequest $httpRequest): View
{
    $user      = $httpRequest->user();
    $collegeId = $user->college_id;                 // ← was: $user->program_id

    // All programs under this college (for resource counts etc.)
    $programIds = Program::where('college_id', $collegeId)->pluck('id');

    $openReports = Report::where('status', ReportStatus::Open->value)
        ->where('school_id', $user->school_id)
        ->count();                                  // campus-wide (reports has no college_id yet)

    $totalModerators = User::where('role', UserRole::Moderator->value)
        ->where('college_id', $collegeId)           // ← was: program_id
        ->count();

    $activeUsers = User::whereNotNull('onboarded_at')
        ->whereNull('suspended_until')
        ->where('college_id', $collegeId)           // ← was: program_id
        ->count();

    $totalResources = LearningResource::whereNull('deleted_at')
        ->whereIn('program_id', $programIds)        // resources remain per-program
        ->count();

    $moderators = ProgramModerator::with([
            'user:id,display_name,name,program_id',
            'program:id,code,name'
        ])
        ->whereIn('program_id', $programIds)        // ← was: where('program_id', $programId)
        ->latest()
        ->paginate(50);

    $unreadFeedback = Feedback::where('recipient_role', 'program_head')
        ->where('recipient_college_id', $collegeId) // ← was: recipient_program_id
        ->whereNull('read_at')
        ->count();

    return view('program-head.dashboard', compact(
        'openReports', 'totalModerators', 'activeUsers',
        'totalResources', 'moderators', 'unreadFeedback'
    ));
}

public function assignModerator(HttpRequest $httpRequest, LogAudit $logAudit): RedirectResponse
{
    $user      = $httpRequest->user();
    $validated = $httpRequest->validate([
        'user_id'    => ['required', 'integer', 'exists:users,id'],
        'program_id' => ['required', 'integer', 'exists:programs,id'],
    ]);

    $userId    = (int) $validated['user_id'];
    $programId = (int) $validated['program_id'];

    // Ensure the program belongs to this Program Head's college
    $program = Program::findOrFail($programId);
    if ($program->college_id !== $user->college_id) {  // ← was: $programId !== $user->program_id
        return redirect()->back()->withErrors([
            'error' => 'You can only assign moderators to programs within your college.',
        ]);
    }

    DB::transaction(function () use ($userId, $programId, $user): void {
        ProgramModerator::firstOrCreate(
            ['user_id' => $userId, 'program_id' => $programId],
            ['assigned_by_user_id' => $user->id]
        );
        User::where('id', $userId)->update(['role' => UserRole::Moderator]);
    });

    $logAudit->handle($user, 'moderator.assign', 'User', $userId, ['program_id' => $programId]);
    session()->flash('status', 'Moderator assigned.');

    return redirect()->route('program_head.dashboard');
}

public function removeModerator(HttpRequest $httpRequest, LogAudit $logAudit): RedirectResponse
{
    $user      = $httpRequest->user();
    $validated = $httpRequest->validate([
        'moderator_id' => ['required', 'integer', 'exists:program_moderators,id'],
    ]);

    $moderator = ProgramModerator::findOrFail((int) $validated['moderator_id']);
    $program   = Program::findOrFail($moderator->program_id);

    if ($program->college_id !== $user->college_id) {  // ← was: $moderator->program_id !== $user->program_id
        return redirect()->back()->withErrors([
            'error' => 'You can only remove moderators from programs within your college.',
        ]);
    }

    DB::transaction(function () use ($moderator): void {
        $userId = $moderator->user_id;
        $moderator->delete();
        if (! ProgramModerator::where('user_id', $userId)->exists()) {
            User::where('id', $userId)
                ->where('role', UserRole::Moderator)
                ->update(['role' => UserRole::Student]);
        }
    });

    $logAudit->handle($user, 'moderator.remove', 'User', $moderator->user_id, []);
    session()->flash('status', 'Moderator removed.');

    return redirect()->route('program_head.dashboard');
}

public function suspend(HttpRequest $httpRequest, SuspendUser $suspendUser): RedirectResponse
{
    $user      = $httpRequest->user();
    $validated = $httpRequest->validate([
        'user_id' => ['required', 'integer', 'exists:users,id'],
        'days'    => ['required', 'integer', 'min:1', 'max:365'],
        'reason'  => ['nullable', 'string', 'max:500'],
    ]);

    $target = User::findOrFail((int) $validated['user_id']);

    if ($target->college_id !== $user->college_id) {   // ← was: program_id
        return redirect()->back()->withErrors([
            'error' => 'You can only suspend users within your college.',
        ]);
    }

    try {
        $suspendUser->handle($user, $target, (int) $validated['days'], $validated['reason'] ?? null);
    } catch (\RuntimeException $e) {
        return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }

    session()->flash('status', 'User suspended.');

    return redirect()->route('program_head.dashboard');
}

public function unsuspend(HttpRequest $httpRequest, SuspendUser $suspendUser): RedirectResponse
{
    $user      = $httpRequest->user();
    $validated = $httpRequest->validate([
        'user_id' => ['required', 'integer', 'exists:users,id'],
    ]);

    $target = User::findOrFail((int) $validated['user_id']);

    if ($target->college_id !== $user->college_id) {   // ← was: program_id
        return redirect()->back()->withErrors([
            'error' => 'You can only unsuspend users within your college.',
        ]);
    }

    try {
        $suspendUser->unsuspend($user, $target);
    } catch (\RuntimeException $e) {
        return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }

    session()->flash('status', 'User unsuspended.');

    return redirect()->route('program_head.dashboard');
}

public function feedback(HttpRequest $httpRequest): View
{
    $user = $httpRequest->user();

    // Mark all unread feedback for this college as read
    Feedback::where('recipient_role', 'program_head')
        ->where('recipient_college_id', $user->college_id)  // ← was: recipient_program_id
        ->whereNull('read_at')
        ->update(['read_at' => now()]);

    $feedbacks = Feedback::with('user:id,display_name,name,email,program_id')
        ->where('recipient_role', 'program_head')
        ->where('recipient_college_id', $user->college_id)  // ← was: recipient_program_id
        ->latest()
        ->paginate(25);

    return view('program-head.feedback', compact('feedbacks'));
}

public function resolveFeedback(HttpRequest $httpRequest, Feedback $feedback): RedirectResponse
{
    $user = $httpRequest->user();

    if ($feedback->recipient_college_id !== $user->college_id) {  // ← was: recipient_program_id
        return redirect()->back()->withErrors([
            'error' => 'This feedback does not belong to your college.',
        ]);
    }

    $feedback->update([
        'status'          => 'resolved',
        'resolved_at'     => now(),
        'resolution_note' => $httpRequest->input('resolution_note'),
    ]);

    session()->flash('status', 'Feedback resolved.');

    return redirect()->route('program_head.feedback');
}

public function escalateFeedback(HttpRequest $httpRequest, Feedback $feedback): RedirectResponse
{
    $user = $httpRequest->user();

    if ($feedback->recipient_college_id !== $user->college_id) {  // ← was: recipient_program_id
        return redirect()->back()->withErrors([
            'error' => 'This feedback does not belong to your college.',
        ]);
    }

    DB::transaction(function () use ($feedback, $user): void {
        $feedback->update(['status' => 'escalated']);

        Feedback::create([
            'user_id'              => $feedback->user_id,
            'type'                 => $feedback->type,
            'body'                 => $feedback->body,
            'recipient_role'       => 'dean',
            'recipient_college_id' => $user->college_id,
            'recipient_program_id' => null,
            'escalated_from_id'    => $feedback->id,
            'status'               => 'open',
        ]);
    });

    session()->flash('status', 'Feedback escalated to Dean.');

    return redirect()->route('program_head.feedback');
}
```

---

### Fix C — `app/Domain/Feedback/Actions/SubmitFeedback.php`

Student/Moderator feedback routes to Program Head via `college_id`.
`recipient_program_id` is `null` since Program Head is no longer
program-scoped:

```php
private function resolveRecipient(User $user): array
{
    return match ($user->role) {
        // Student and Moderator → Program Head of their college
        UserRole::Student,
        UserRole::Moderator   => ['program_head', $user->college_id, null], // ← was: $user->program_id

        // Program Head → Dean of their college
        UserRole::ProgramHead => ['dean', $user->college_id, null],

        // Dean → SAO
        UserRole::Dean        => ['sao', null, null],

        // SAO → Super Admin (system-level issues only)
        default               => ['super_admin', null, null],
    };
}
```

---

### Fix D — `database/seeders/DevUsersSeeder.php`

The `admin@seait.edu.ph` dev account has `program_id` set. It should
have `college_id` only — Program Head no longer carries a `program_id`:

```php
$cbgg = College::where('code', 'CBGG')->where('school_id', $school->id)->firstOrFail();

User::updateOrCreate(
    ['email' => 'admin@seait.edu.ph'],
    [
        'school_id'          => $school->id,
        'college_id'         => $cbgg->id,
        'program_id'         => null,       // ← was: program_id set to BSBA-MM
        'year_level'         => null,       // ← was: year_level set
        'name'               => 'Program Head CBGG',
        'display_name'       => 'Program Head',
        'email_verified_at'  => now(),
        'password'           => $password,
        'role'               => UserRole::ProgramHead,
        'onboarded_at'       => now(),
    ]
);
```

---

## Minor Issue #1 — Open Reports Stat Card Shows Campus-Wide Count

**Files:** `DeanController.php` and `ProgramHeadController.php`

Both controllers count open reports using only `school_id`:

```php
$openReports = Report::where('status', ReportStatus::Open->value)
    ->where('school_id', $user->school_id)
    ->count();
```

The `reports` table has no `college_id` column, so there is no way
to filter by college yet. Both Dean and Program Head see the
total campus-wide count, which is misleading.

**Short-term fix** — rename the stat card label in both dashboard views:

```blade
{{-- In dean/dashboard.blade.php and program-head/dashboard.blade.php --}}
<p class="text-sm text-gray-500">
    Open Reports
    <span class="text-xs opacity-60">(campus-wide)</span>
</p>
```

**Long-term fix** — add `college_id` to the reports table in a future
migration and update both controllers to filter by `$user->college_id`.

---

## Minor Issue #2 — Feedback `read_at` Never Set on View

The `read_at` column was added in migration `2026_05_29_000002` but
no controller sets it when a feedback list is viewed. The unread count
badge on the nav never decreases after opening the feedback page.

**Fix** — add bulk `read_at` marking at the top of each controller's
`feedback()` method. The Program Head fix is already included above.
Apply the same pattern to `DeanController` and `SaoController`:

```php
// DeanController::feedback()
Feedback::where('recipient_role', 'dean')
    ->where('recipient_college_id', $user->college_id)
    ->whereNull('read_at')
    ->update(['read_at' => now()]);

// SaoController::feedback()
Feedback::where('recipient_role', 'sao')
    ->whereNull('read_at')
    ->update(['read_at' => now()]);
```

Then update the unread count queries in all three dashboards
to use `whereNull('read_at')` instead of `where('status', 'open')`:

```php
// More accurate — counts genuinely unread items, not all open ones
$unreadFeedback = Feedback::where('recipient_role', 'dean')
    ->where('recipient_college_id', $user->college_id)
    ->whereNull('read_at')
    ->count();
```

---

## Minor Issue #3 — SAO Announcements Page Is an Empty Stub

`SaoController::announcements()` returns a view with no data and
the store route has no working implementation. The SAO nav link
is live but leads to a blank page.

**Quick fix** — hide it behind a feature flag until the feature
is built:

```php
// config/studhub.php
'announcements_enabled' => env('STUDHUB_ANNOUNCEMENTS_ENABLED', false),
```

```bash
# .env
STUDHUB_ANNOUNCEMENTS_ENABLED=false
```

```blade
{{-- navigation.blade.php — inside SAO nav section --}}
@if (config('studhub.announcements_enabled'))
    <x-nav-link :href="route('sao.announcements')">Announcements</x-nav-link>
@endif
```

---

## Minor Issue #4 — No Duplicate-Dean Guard in SAO Panel

The SAO `assignRole()` method has no check for whether a college
already has a Dean before assigning another one. Two Dean accounts
could accidentally be assigned to the same college.

**Fix** — add an application-layer guard in `SaoController::assignRole()`:

```php
if ($validated['role'] === UserRole::Dean->value) {
    $alreadyHasDean = User::where('role', UserRole::Dean->value)
        ->where('college_id', $validated['college_id'])
        ->exists();

    if ($alreadyHasDean) {
        return redirect()->back()->withErrors([
            'error' => 'This college already has a Dean assigned.
                        Remove the existing Dean first.',
        ]);
    }
}

// Apply the same check for Program Head
if ($validated['role'] === UserRole::ProgramHead->value) {
    $alreadyHasProgramHead = User::where('role', UserRole::ProgramHead->value)
        ->where('college_id', $validated['college_id'])
        ->exists();

    if ($alreadyHasProgramHead) {
        return redirect()->back()->withErrors([
            'error' => 'This college already has a Program Head assigned.
                        Remove the existing Program Head first.',
        ]);
    }
}
```

Use an application-layer check rather than a DB constraint — easier to
temporarily override during handover periods or when an acting Dean
is appointed.

---

## ✅ What Was Done Well — No Changes Needed

**UserRole enum** — all methods present and correct: `label()`,
`panelClass()`, `inheritedRoles()`, `isSchoolRole()`, `values()`.
The `isSchoolRole()` check that hides `super_admin` from school-facing
UI is a good touch.

**DeanController** — already correctly college-scoped. Every sensitive
operation checks `$target->college_id === $user->college_id`. No changes
needed.

**SaoController** — correctly campus-wide with no scope filter. The
`assignRole()` whitelist (`dean`, `program_head`, `moderator`, `student`)
correctly prevents SAO from promoting anyone to SAO via the UI.

**Escalation chain** — correctly implemented. Program Head escalates to
Dean of same college. Dean escalates to SAO. Each escalation creates a
new `Feedback` row with `escalated_from_id` pointing to the original,
so the full chain is traceable at every level.

**Migration** — the `if (DB::getDriverName() === 'mysql')` guard correctly
handles the ENUM ALTER for MySQL production while keeping SQLite working
for the test suite. Clean.

**Role context banner** — correctly shows scope per role. The banner uses
`$user->college?->name` and `$user->program?->name` so it automatically
displays the correct college/program name for every account without
any additional logic.

**Navigation** — fully updated for all six roles in the desktop nav,
responsive menu, and profile dropdown.

**Legacy redirects** — `/admin` → `/program-head` and `/admin/super` → `/sao`
are present for backwards compatibility.

**Test count grew from 221 → 233** — `UserRoleTest` covers enum labels,
panel classes, inherited roles, and `isSchoolRole()` comprehensively.

---

## Future Work — Not a Bug, Planned Separately

### VPAA Role

The VPAA (Vice President for Academic Affairs) sits above SAO in the
school hierarchy. It has been planned and documented but not yet
implemented. The codebase is clean and ready. When the sprint starts:

1. Add `case Vpaa = 'vpaa'` to `UserRole` enum
2. Add `isVpaa()` and `panelClass()` → `'panel-vpaa'` to `User` model
3. Add `panel-vpaa` CSS (deep maroon `#6B1E3C`)
4. Add `vpaa` to the ENUM ALTER migration
5. Create `VpaaController` — read-only executive dashboard, escalated
   feedback inbox only (SAO escalates up to VPAA for major issues)
6. Add `/vpaa` route group with `role:vpaa,super_admin` middleware
7. Update `SubmitFeedback::resolveRecipient()` — Dean escalates to SAO,
   SAO escalates to VPAA
8. Add VPAA to navigation and role context banner
9. Seed `vpaa@seait.edu.ph` dev account with no college or program

### Super Admin Nav Points to SAO Dashboard

The Super Admin nav link currently routes to `route('sao.dashboard')`.
This works (Super Admin passes the SAO middleware bypass) but the page
header reads "SAO Dashboard" which is confusing for the developer.
Address when a dedicated developer panel is needed.

---

## Summary of All Issues

| # | Issue | Severity | File(s) | Action |
|---|-------|----------|---------|--------|
| 1 | `hasCompletedOnboarding()` blocks Dean, Program Head, SAO | 🔴 Critical | `User.php` | Fix now — see Critical Bug #1 |
| 2 | `ProgramHeadController` scoped by `program_id` not `college_id` | 🔴 Critical | `ProgramHeadController.php`, `ScopeGuard.php`, `SubmitFeedback.php`, `DevUsersSeeder.php` | Fix now — see Critical Scope Misalignment |
| 3 | Open Reports stat shows campus-wide count to Dean and Program Head | 🟡 Minor | `DeanController.php`, `ProgramHeadController.php` | Rename label now; scope in future migration |
| 4 | `read_at` never set when feedback is viewed | 🟡 Minor | All three feedback controllers | Easy fix |
| 5 | SAO Announcements page is an empty stub | 🟡 Minor | `SaoController.php`, `navigation.blade.php` | Hide behind feature flag |
| 6 | No duplicate Dean/Program Head guard in SAO panel | 🟡 Minor | `SaoController.php` | Add application-layer check |
| 7 | VPAA role not yet implemented | 🔵 Future | — | Separate sprint |
| 8 | Super Admin nav points to SAO dashboard | 🔵 Cosmetic | `navigation.blade.php` | Acceptable for now |

---

## Priority Order — What to Fix Next

**1. Fix `hasCompletedOnboarding()` — 30 min**
Must be done before any Dean, Program Head, or SAO account is
used outside the dev seeder.

**2. Fix `ProgramHeadController` scope — 45 min**
Change all `program_id` scope references to `college_id`. Update
`ScopeGuard`, `SubmitFeedback`, and `DevUsersSeeder` in the same
commit. Run `php artisan test` after to confirm no regressions.

**3. Add duplicate Dean/Program Head guard — 15 min**
Add application-layer checks in `SaoController::assignRole()`
before issue #1 and #2 are deployed to production.

**4. Mark feedback as read on view — 15 min**
Bulk `read_at` update in each controller's `feedback()` method.
Update unread count queries to use `whereNull('read_at')`.

**5. Rename Open Reports label — 5 min**
Add "(campus-wide)" to both dashboard stat cards.

**6. Hide SAO Announcements nav link — 5 min**
Feature-flag it in `config/studhub.php`.

**7. VPAA sprint — 1–2 days**
Follow the 9-step checklist in the section above.

---

## Production Account Creation Flow

When the system goes live, SAO creates all accounts in this order:

```
SAO creates Dean for CICT    → college_id = CICT,  no program_id
SAO creates Dean for DCE     → college_id = DCE,   no program_id
SAO creates Dean for CBGG    → college_id = CBGG,  no program_id
SAO creates Dean for CTE     → college_id = CTE,   no program_id
SAO creates Dean for CAF     → college_id = CAF,   no program_id
SAO creates Dean for CCJE    → college_id = CCJE,  no program_id

SAO creates Program Head for CICT    → college_id = CICT,  no program_id
SAO creates Program Head for DCE     → college_id = DCE,   no program_id
SAO creates Program Head for CBGG    → college_id = CBGG,  no program_id
SAO creates Program Head for CTE     → college_id = CTE,   no program_id
SAO creates Program Head for CAF     → college_id = CAF,   no program_id
SAO creates Program Head for CCJE    → college_id = CCJE,  no program_id

Each Program Head then assigns Moderators to specific programs
within their college as needed.
```

Total administrative accounts at launch: **1 SAO + 6 Deans + 6 Program Heads = 13**

---

## Dev Accounts Reference

| Email | Role | college_id | program_id | year_level |
|-------|------|-----------|-----------|-----------|
| `test@seait.edu.ph` | Student | CICT | BSIT | 2 |
| `mod@seait.edu.ph` | Moderator | CICT | BSIT | null |
| `admin@seait.edu.ph` | Program Head | CBGG | **null** | **null** |
| `dean.cict@seait.edu.ph` | Dean | CICT | null | null |
| `sao@seait.edu.ph` | SAO | null | null | null |
| *(artisan tinker only)* | Super Admin | null | null | null |

---

*Final review document — 2026-05-29.*
*Incorporates: implementation review, multi-college clarification, program head scope correction.*

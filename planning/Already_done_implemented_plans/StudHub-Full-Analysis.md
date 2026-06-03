# StudHub — Full Codebase Analysis Report

**Date**: 2026-05-31  
**Analyzer**: Claude Sonnet 4.6  
**Repo**: `https://github.com/kirbygeagonia-create/StudHub.git`  
**Stack**: Laravel 11 · PHP 8.2 · Livewire 4 · Tailwind CSS · Alpine.js · Laravel Reverb · MySQL 8 / SQLite (tests) · Redis 7  

---

## Table of Contents

1. [Executive Summary](#1-executive-summary)
2. [Architecture & Design](#2-architecture--design)
3. [File Structure Walkthrough](#3-file-structure-walkthrough)
4. [Database Schema & Migrations](#4-database-schema--migrations)
5. [Backend — Controllers, Actions, Jobs](#5-backend--controllers-actions-jobs)
6. [Real-time Chat System](#6-real-time-chat-system)
7. [Routing & Middleware](#7-routing--middleware)
8. [Authentication & Onboarding Flow](#8-authentication--onboarding-flow)
9. [Reputation & Badge System](#9-reputation--badge-system)
10. [Request Routing Engine](#10-request-routing-engine)
11. [Test Coverage](#11-test-coverage)
12. [Confirmed Bugs](#12-confirmed-bugs)
13. [Security Issues](#13-security-issues)
14. [Design & Architectural Concerns](#14-design--architectural-concerns)
15. [Performance Observations](#15-performance-observations)
16. [Docker & Infrastructure](#16-docker--infrastructure)
17. [Recommendations Summary](#17-recommendations-summary)

---

## 1. Executive Summary

StudHub is a well-engineered Laravel 11 school-wide platform for academic resource exchange, request fulfillment, and program-scoped chat. The codebase follows Domain-Driven Design principles with clean module boundaries, strong typing, and a healthy test suite. Overall health is **good-to-excellent**, but the review uncovered **5 confirmed bugs** (two of which cause runtime errors in production), **4 security issues**, and **7 architectural/design concerns** that warrant attention before a wider rollout.

| Category | Count | Severity |
|---|---|---|
| Confirmed Bugs | 5 | Critical to Minor |
| Security Issues | 4 | High to Low |
| Architectural Concerns | 7 | Medium |
| Performance Notes | 4 | Low-Medium |

---

## 2. Architecture & Design

### Overall Pattern: Domain-Driven Design

The application is structured as a Laravel monolith with a DDD-inspired internal layout:

```
app/
├── Domain/
│   ├── Catalog/       — Resources, subjects, shelves, watermarking
│   ├── Chat/          — Rooms, messages, events, mentions
│   ├── Feedback/      — Feedback submission & routing chain
│   ├── Identity/      — Roles, email domain rules, scope guards
│   ├── Lends/         — Physical resource lending, reminders, escalation
│   ├── Moderation/    — Reports, suspensions, audit logs
│   ├── Reputation/    — Karma, 12 badge tiers, 36 achievement badges
│   ├── Requests/      — Requests, offers, weighted routing engine
│   └── Search/        — Global search, daily digest job
├── Http/
│   ├── Controllers/   — Thin HTTP handlers, delegate to Domain Actions
│   └── Middleware/    — Role, suspension, onboarding guards
├── Livewire/          — Chat real-time component
└── Models/            — Eloquent models
```

This is the correct approach. Each domain has its own `Actions/`, `Enums/`, `Jobs/`, and `Notifications/` sub-folders. Controllers are thin and delegate to domain actions. The separation is consistently applied across all modules.

### Technology Choices

| Technology | Purpose | Assessment |
|---|---|---|
| Laravel 11 | Framework | Modern, correct |
| Livewire 4 | Chat real-time UI | Correct for this scale |
| Laravel Reverb | WebSocket server | Appropriate (OSS, first-party) |
| Redis 7 | Cache + queues | Correct |
| MySQL 8 | Primary DB | Correct |
| SQLite `:memory:` | Test DB | Correct for speed |
| Intervention Image | Thumbnail generation | Correct |
| FPDI | PDF watermarking | Correct |
| Pest 3 | Test framework | Correct |
| PHPStan Lv6 | Static analysis | Good — could go to Level 9 |

---

## 3. File Structure Walkthrough

### Every File, Assessed

#### `app/Http/Controllers/` (16 files)

| File | Purpose | Issues |
|---|---|---|
| `ResourceController.php` | CRUD, download, shelf, mark-helpful | `markHelpful` uses session-only deduplication (bug) |
| `ChatController.php` | Room list + room view | Clean |
| `ChatAttachmentController.php` | Authenticated file download | Year-level check has redundant inner condition |
| `RequestController.php` | Request CRUD + offer flow | Clean |
| `LendController.php` | Lend record + return flow | Clean |
| `ModerationController.php` | Mod dashboard, suspend, user search | Clean; `userSearch` scope could expose nulls if `program_id` is null |
| `ProgramHeadController.php` | PH dashboard + feedback routing | Clean |
| `DeanController.php` | Dean dashboard + programs | Clean |
| `SaoController.php` | SAO-level views + role assignment | Clean; duplicate Dean/PH one-per-college guard is good |
| `OnboardingController.php` | Role-aware onboarding form | Hardcodes `School::where('code', 'SEAIT')` |
| `ProfileController.php` | Profile, leaderboard, notifications | Clean |
| `SearchController.php` | Global search gateway | Delegates correctly |
| `ReportController.php` | Submit a report | Clean |
| `FeedbackController.php` | Submit feedback | Clean |
| `NotificationController.php` | Mark-read + list | Clean |
| `Auth/*.php` (9 files) | Breeze-generated auth | `RegisteredUserController` bypasses email verification |

#### `app/Domain/` (50+ files)

All domain action files follow the single-responsibility pattern correctly. Actions are invocable classes with `handle()` methods. Enums are exhaustive with helper methods. Notifications and Jobs are correctly declared as `ShouldQueue`. No issues with file organization.

#### `app/Models/` (22 files)

| Model | Issues |
|---|---|
| `User` | `'role'` in `$fillable` — see Security |
| `ChatMessage` | `is_system` not in migration or `$fillable`; `pinned_at` has no UI |
| `LearningResource` | Good; uses `SoftDeletes` correctly |
| `Report` | Uses `ReportSchoolScope` via `whereHas` instead of direct `school_id` column |
| `Lend` | `scopeDueSoon()` well-implemented; `escalate()` method is clean |
| All others | Clean |

#### `resources/views/` (46 blade files)

Views are well-organized. Layouts (`app.blade.php`, `admin.blade.php`, `guest.blade.php`) are clean. Component extraction is solid (`stat-card`, `page-header`, `lend-row`, `empty-state`). Dark mode via Alpine.js + localStorage is consistent.

The icon component (`components/icon.blade.php`) embeds raw SVG paths in a PHP array inside a Blade template — functional but difficult to maintain as the icon set grows.

---

## 4. Database Schema & Migrations

### Migration Order & Completeness

All 29 migrations run in the correct dependency order. Foreign key relationships are well-declared with appropriate cascade strategies. The migration dates (2025–2026) tell the product history clearly.

### Schema Snapshot

| Table | Key Fields | Notes |
|---|---|---|
| `users` | role, karma, school_id, college_id, program_id, year_level, onboarded_at, suspended_until | Role stored as MySQL ENUM (flexible varchar on SQLite) |
| `schools`, `colleges`, `programs` | Hierarchical org structure | Clean |
| `subjects`, `subject_aliases`, `program_subjects` | Curriculum graph with `weight` + `typical_year_level` pivots | Powers routing engine |
| `chat_rooms` | kind, program_id, year_level, school_id, slug | `request_id` column added later but foreign key added via separate migration |
| `chat_messages` | sender_id, body, attachment_*, reply_to_message_id, is_helpful, pinned_at | `is_system` column MISSING (see Bug #2) |
| `chat_room_memberships` | joined_at, last_read_at, is_muted | Defined but never populated (see Design #1) |
| `resources` | type, availability, visibility, file_url, thumbnail_url, helpful_count | `helpful_count` added as late migration |
| `shelf_items` / `shelves` | User-resource saves | Clean |
| `requests` / `offers` / `request_routes` | Request lifecycle | `requests` table has no `title` column (see Bug #1) |
| `lends` | from_user, to_user, return_by, reminder_count, escalated_at | Well-designed with escalation chain |
| `karma_events` | delta, reason, related_type, related_id | Clean audit trail |
| `user_badges` | badge (varchar), earned_at | Correct: string column avoids schema-change on new badges |
| `reports` | school_id, reporter_user_id, reported_type, reported_id | `school_id` added as a later nullable column |
| `feedback` | routing fields added in last migration (recipient_role, escalated_from_id) | Late migration adds nullable columns — acceptable |
| `audit_log` | actor_user_id, action, target_type, target_id, metadata JSON | Clean |
| `notifications` | Standard Laravel notifications table | Correct |

### Indexing

The migration `2026_05_23_000000_add_missing_db_indexes.php` is a good hygiene pass that adds missing indexes on:
- `reports.school_id`
- `chat_rooms.request_id`
- `requests.requester_user_id`
- `chat_messages.sender_id`
- `users.college_id`, `users.role`
- `lends.escalated_at`, `lends.offer_id`, `lends.request_id`
- `offers.offerer_user_id`

This is well-done. The composite indexes on `[status, subject_id]` (requests) and `[chat_room_id, created_at]` (messages) are correct for the query patterns used.

**Missing index**: `resources.owner_user_id` — used heavily in `CheckAndAwardBadges` and `SearchGlobal` but has no index.

---

## 5. Backend — Controllers, Actions, Jobs

### Data Flow for Key Features

#### Resource Upload Flow
```
POST /resources (Livewire ResourceForm)
  → CreateResource::handle()
    → Validates MIME (PDF, JPEG, PNG, WebP only)
    → Stores file to public disk
    → Creates LearningResource record
    → Dispatches WatermarkResourceFile job (async)
    → Awards karma via AwardKarma
    → Triggers CheckAndAwardBadges (upload category)
  → Redirect to resource show
```

#### Resource Download Flow
```
GET /resources/{resource}/download
  → DownloadResourceFile::handle()
    → Auth/school/program visibility check
    → Checks watermark cache (local disk, per-user hash)
    → If PDF: FPDI stamps every page with "Downloaded by {name} ({email})"
    → If image: Intervention Image stamps bottom-left text
    → Falls back to serving original if watermarking fails
    → Returns StreamedResponse
```
Note: Download count is NOT tracked. No `increment('download_count')` anywhere.

#### Resource Request + Routing Flow
```
POST /requests
  → CreateRequest::handle() → validates, stores
  → RouteRequest::handle() (weighted scoring engine)
    → Scores all programs that share the subject
    → Picks top users per program (karma + matching resource)
    → Dispatches NotifyRoutedUsers job (async)
    → If urgency=urgent AND score >= 0.65: CrossPostRequest job
  → Redirect to request show
```

#### Lend Flow
```
POST /requests/{req}/offers/{offer}/lend
  → RecordLend::handle()
    → Validates offer is accepted
    → Creates Lend record
    → Increments resource.lend_count
    → Awards karma
    → Triggers badge checks
  → Redirect to lends index
```

#### Moderation Flow
```
POST /reports
  → CreateReport::handle() → deduplicates reporter+target
POST /moderation/reports/{report}/resolve
  → ResolveReport::handle() → marks status, logs audit
POST /moderation/suspend
  → SuspendUser::handle() → sets suspended_until, logs audit
POST /moderation/unsuspend
  → SuspendUser::unsuspend() → clears suspended_until, logs audit
```

### Jobs

| Job | Triggered by | Queue |
|---|---|---|
| `WatermarkResourceFile` | Resource creation | Default |
| `NotifyRoutedUsers` | Request routing | Default |
| `CrossPostRequest` | Urgent request routing | Default |
| `SendReturnReminders` | Scheduler (09:00 daily) | Default |
| `SendDailyDigest` | Scheduler (07:00 daily) | Default |

All jobs implement `ShouldQueue`. `SendReturnReminders` uses `cursor()` for memory efficiency. `SendDailyDigest` does **not** respect `notification_preferences.digest_enabled` (Bug #4).

### Scheduled Commands

| Command | Schedule | Status |
|---|---|---|
| `studhub:expire-requests` | 03:00 daily | Exists, tested in SmokeTest |
| `studhub:recalc-routing-weights` | Weekly | Exists |
| `studhub:backup-database` | 02:00 daily | Exists |
| `SendReturnReminders` | 09:00 daily | Works correctly |
| `SendDailyDigest` | 07:00 daily | Bug — ignores user prefs |

---

## 6. Real-time Chat System

### Architecture

```
User types → Livewire RoomConversation.send()
  → PostChatMessage::handle()
    → Persists ChatMessage in transaction
    → Resolves @mentions from school roster
    → Syncs mention pivot records
    → broadcast(ChatMessagePosted) → Reverb WebSocket
    → Notification::send(mentioned users)
  → Livewire clears input, invalidates computed property
  → Echo listener on `chat-room.{id}` triggers onMessageBroadcast()
  → Re-fetches last 50 messages
```

### Chat Authorization (Channels)

`routes/channels.php` correctly authorizes the `chat-room.{roomId}` private channel by checking:
- User is not suspended
- User has completed onboarding
- Room program_id matches user's program_id (if not null)
- Room year_level matches user's year_level (if not null)

This is **duplicated** across `channels.php`, `ChatController`, `ChatAttachmentController`, and `RoomConversation::mount()`. Any one of these could drift. A shared authorization policy or a model method would be cleaner.

### Chat Limitations

- Messages are loaded as last-50 only. There is no pagination or infinite scroll. Older messages are permanently inaccessible to users.
- The `chat_room_memberships` table is defined but `joined_at`, `last_read_at`, and `is_muted` are never written (see Design Concern #1). Unread count badges on rooms are therefore not implemented.
- The `pinned_at` field exists on `ChatMessage` but there is no "pin message" UI or logic.
- File types accepted in chat (Word, Excel, PowerPoint, plain text) are wider than what `PostChatMessage::ALLOWED_ATTACHMENT_MIMES` validates. The Livewire component accepts `.docx`/`.xlsx` etc. but the action only allows PDF/images. This creates a mismatch: uploads succeed at the Livewire level but the action rejects them silently (throws `InvalidArgumentException` caught nowhere in the component — the component does not wrap `$action->handle()` in try-catch).

---

## 7. Routing & Middleware

### Route Groups (Summary)

| Middleware Stack | Routes Covered |
|---|---|
| `auth` only | Onboarding |
| `auth, verified, onboarded, not_suspended` | All student-facing routes |
| `auth, verified, onboarded, role:moderator,…` | Moderation dashboard |
| `auth, verified, onboarded, role:program_head,…` | Program Head panel |
| `auth, verified, onboarded, role:dean,…` | Dean panel |
| `auth, verified, onboarded, role:sao,super_admin` | SAO panel |

Legacy `/admin` routes redirect correctly to `/program-head`.

### `EnsureHasRole` Middleware — Partial Inheritance Bug

The middleware directly checks if the user's role value is in the `$roles` array passed to it. It only special-cases `isSuperAdmin()` and `isSao()` for full bypass. It does **not** use the `inheritedRoles()` method defined in `UserRole` enum.

```php
// Current (partial):
if ($user->isSuperAdmin() || $user->isSao()) { return $next($request); }
if (!in_array($roleValue, $roles, true)) { abort(403); }
```

This works only because every route group explicitly lists all inherited roles:
```php
'role:program_head,dean,sao,super_admin'
'role:dean,sao,super_admin'
```

If a developer adds a new route and writes `role:program_head` without including `dean` and above, a Dean would get a 403 on a page they should access. The fix is to use `inheritedRoles()` in the middleware or document this requirement.

### Throttle Coverage

Rate limiting is correctly applied to all mutation endpoints:
- `POST /requests`, `/resources`, `/reports`, `/feedback` — `throttle:10,1`
- `GET /resources/{id}/download`, `toggle-save`, `mark-helpful` — `throttle:30,1`
- Admin actions — `throttle:20,1`
- `DELETE /profile` — `throttle:5,1`

The `GET /search` endpoint has `throttle:30,1`, which is appropriate.

---

## 8. Authentication & Onboarding Flow

### Registration

```
POST /register
  → AllowedSchoolEmailDomain validation rule (domain whitelist from env)
  → User created with role=student, email_verified_at=now() ← BYPASS
  → Redirect to /onboarding
```

The `email_verified_at = now()` line effectively disables the `MustVerifyEmail` interface even though the User model implements it and all routes have the `verified` middleware. This is an intentional shortcut (common in school intranets where email access is controlled), but it should be documented as an explicit design decision rather than an implicit bypass.

### Onboarding Flow

Role-aware onboarding correctly segments required fields:

| Role | Required Fields |
|---|---|
| Student / Moderator | program_id, year_level, display_name |
| Dean / Program Head | program_id (for college derivation), display_name |
| SAO / SuperAdmin | display_name only |

`hasCompletedOnboarding()` on the User model correctly mirrors these requirements.

---

## 9. Reputation & Badge System

### Karma Tiers (Dynamic, 12 levels)

The `BadgeTier` system is well-designed. Tiers are computed from `karma` at runtime and never stored, which is correct. The 12-tier progression from `Seedling` (0 karma) to `StudHubLegend` (6,000 karma) is well-thought-out.

**Minor issue**: `Archivist` and `Custodian` share identical SVG icon paths — they're both `Building/Columns` icons. Different tiers should be visually distinct.

### Achievement Badges (36 badges, stored in `user_badges`)

The badge system has excellent design:
- Badges use a `varchar(64)` column, so new badges can be added without schema changes.
- The `UniqueConstraintViolationException` catch in `award()` handles race conditions gracefully.
- The `CheckAndAwardBadges::handle()` accepts a `$trigger` to limit which badge categories are checked on each action.

**Critical issue**: Several `qualifies()` checks use MySQL-specific functions:
- `TIMESTAMPDIFF()` — SignalBoost, QuickDraw badges
- `YEARWEEK()` — SemesterStrong badge
- `DATE_ADD(..., INTERVAL 24 HOUR)` — Crammer badge
- `HOUR()` — NightOwl, NightShift, EarlyBird badges

Tests use SQLite in-memory (`DB_CONNECTION=sqlite`). These badge checks will **fail with a SQL error** if any badge tests invoke them on SQLite. The current test suite may not cover these specific badges, but they will fail in production on any non-MySQL database.

---

## 10. Request Routing Engine

### Algorithm

`RouteRequest` is the most sophisticated piece of code in the codebase. It scores programs using a weighted formula:

```
score = 0.40 × edge_weight        (curriculum graph weight)
      + 0.25 × normalized_resources (resource density for subject)
      + 0.20 × historical_fulfillment_rate
      + 0.10 × year_proximity_bonus
      + 0.05 × urgency_multiplier
      − 0.05 × self_program_penalty
```

Programs scoring ≥ 0.35 are routed. Users within those programs are ranked by matching-resource ownership + karma, capped at 8 per program and 25 total.

### Assessment

The algorithm is well-conceived. Constants are named and documented. The `routingConstants()` static method exposes them for inspection.

**Issues found:**

1. `historicalFulfillmentRate()` uses a `static $cache = []` inside an instance method. This is a **PHP-level process cache** that persists across ALL invocations of `RouteRequest::handle()` within a single PHP process lifetime. In FPM workers, this means stale fulfillment rates until the worker is recycled. In tests, it leaks state between test cases that create multiple requests.

2. The `normalizedResourceCount()` method executes two separate COUNT queries per program. For a subject shared by 10 programs, that's 20 queries. This should be a single grouped query.

3. The idempotency test (`'routing is idempotent — running twice does not double routes'`) has an assertion that `secondCount == firstCount * 2`, which **explicitly tests that routing is not idempotent**. The test name is misleading — it documents a known non-idempotent behavior as expected. Running routing twice does create double the routes.

---

## 11. Test Coverage

### Summary

```
tests/
├── Feature/
│   ├── Auth/             (6 tests) — Registration, login, password, email verification
│   ├── Catalog/          (6 tests) — Resources, search, shelf, seeder
│   ├── Chat/             (4 tests) — Access, posting, provisioning, Livewire
│   ├── Feedback/         (1 test)  — Submission
│   ├── Identity/         (4 tests) — Domain rules, onboarding, roles
│   ├── Lends/            (1 test)  — Lend lifecycle
│   ├── Moderation/       (2 tests) — Reports, enum coverage
│   ├── Reputation/       (1 test)  — Karma events
│   ├── Requests/         (4 tests) — Request creation, offers, routing, accept
│   └── SmokeTest.php     (11 tests)— Page loads, health check, rate limiting
└── Unit/
    └── ExampleTest.php   (1 test)
```

**Strengths:**
- Route access tests (unauthenticated, wrong school, suspended user) are present
- `RouteRequestTest` has 11 cases covering scoring, self-penalty, notification dispatch, fallback routing
- Livewire component tests properly test send/validate flow
- Seeder tests validate SEAIT data integrity

**Gaps:**
- No test for `SearchGlobal` hitting the non-existent `requests.title` column
- No test for `CrossPostRequest` (the `is_system` bug goes undetected)
- No test for `SendDailyDigest` notification preferences
- No test for `CheckAndAwardBadges` with MySQL-specific SQL
- No test covering the `markHelpful` duplicate-vote bypass
- No tests for `DeanController` or `SaoController` admin panels
- No test for `DownloadResourceFile` (watermark logic)
- Throttle test is skipped: `->skip('Throttle middleware returns 302 redirect in test environment')`

---

## 12. Confirmed Bugs

### Bug #1 — CRITICAL: `SearchGlobal` queries non-existent `requests.title` column

**File**: `app/Domain/Search/Actions/SearchGlobal.php`, lines 44–46  
**Impact**: Any search query that hits the requests section throws a SQL error in MySQL production.

The `requests` table (created in `2025_06_01_000001_create_requests_table.php`) has no `title` column. It only has `description`. The `SearchGlobal` action queries:

```php
$q->where('title', 'like', '%' . $query . '%')
  ->orWhere('description', 'like', '%' . $query . '%');
```

In SQLite (tests), unknown columns may silently fail or return empty. In MySQL (production), this throws `SQLSTATE[42S22]: Column not found`.

**Fix**: Remove the `->where('title', ...)` line. Only `description` exists on the `requests` table.

---

### Bug #2 — HIGH: `CrossPostRequest` writes `is_system` to a column that doesn't exist

**File**: `app/Domain/Requests/Jobs/CrossPostRequest.php`, line 48  
**Impact**: System-posted cross-program messages are silently not flagged as system messages.

```php
$chatRoom->messages()->create([
    'sender_id' => $request->requester_user_id,
    'body'      => sprintf('Routed request: ...'),
    'is_system' => true,   // ← column doesn't exist in migration or $fillable
]);
```

`is_system` does not exist in the `chat_messages` migration, nor in `ChatMessage::$fillable`. Laravel's mass assignment protection silently drops it. The message is created successfully, but it's indistinguishable from a regular user message — it shows up attributed to the requester with no visual distinction.

**Fix**: Add `is_system boolean default false` to `chat_messages` via a new migration and add `'is_system'` to `ChatMessage::$fillable`. Add UI handling to render system messages differently (e.g., gray italics, no avatar).

---

### Bug #3 — MEDIUM: `markHelpful` deduplication is session-only

**File**: `app/Http/Controllers/ResourceController.php`, lines 169–196  
**Impact**: Users can vote multiple times by clearing cookies, using incognito, or switching browsers.

```php
$key = 'helpful-vote-' . $resource->id . '-' . $user->id;
if (Session::has($key)) {
    return redirect()->back()->with('status', 'Already marked...');
}
DB::transaction(function () use ($resource, $key): void {
    $resource->increment('helpful_count');
    Session::put($key, true);
});
```

The deduplication key lives in the session store only. There is no database-level unique constraint.

**Fix**: Add a `resource_helpful_votes` table with a `UNIQUE(user_id, resource_id)` constraint, or add a `helpful_votes` JSON column to resources, or use a pivot table. The session-based approach is unsuitable for a persistent counter.

---

### Bug #4 — MEDIUM: `SendDailyDigest` ignores `notification_preferences.digest_enabled`

**File**: `app/Domain/Search/Jobs/SendDailyDigest.php`  
**Impact**: Users who opt out of the daily digest in their preferences still receive it.

The `ProfileController` stores `digest_enabled` in `notification_preferences` and there's a UI for it. But `SendDailyDigest` never checks this preference:

```php
foreach ($users as $user) {
    // ... no check for $user->notification_preferences['digest_enabled']
    Mail::to($user)->queue(new DailyDigest($user, [...]));
}
```

**Fix**:
```php
$prefs = $user->notification_preferences ?? [];
if (($prefs['digest_enabled'] ?? true) === false) {
    continue;
}
```

---

### Bug #5 — LOW: `historicalFulfillmentRate()` static cache leaks across requests

**File**: `app/Domain/Requests/Actions/RouteRequest.php`, line 203  
**Impact**: In long-lived FPM workers, fulfillment rates are stale. In tests, state leaks between test cases.

```php
private function historicalFulfillmentRate(Program $program, Subject $subject): float
{
    static $cache = [];  // ← PHP process-level cache
    ...
}
```

**Fix**: Move the cache to a per-request or per-job-invocation array (instance property on `RouteRequest`), or use Laravel's Cache facade with a short TTL (e.g., 10 minutes).

---

## 13. Security Issues

### Security Issue #1 — HIGH: Email verification is bypassed on registration

**File**: `app/Http/Controllers/Auth/RegisteredUserController.php`, line 52

```php
$user = User::create([
    ...
    'email_verified_at' => now(),  // ← immediately verified
]);
```

The `User` model implements `MustVerifyEmail` and all application routes require the `verified` middleware — but registration pre-verifies every account. Any email address within the allowed domain list (or any address if `STUDHUB_ALLOWED_EMAIL_DOMAINS` is empty) can access the full application immediately.

**Recommendation**: Either remove `email_verified_at => now()` and rely on the standard email flow, or explicitly document and enforce that this is only suitable for controlled intranet environments where all registrants are trusted. The `AllowedSchoolEmailDomain` rule provides some protection, but it does not verify that the registrant actually controls the address.

---

### Security Issue #2 — MEDIUM: `'role'` is in `User::$fillable`

**File**: `app/Models/User.php`, line 36

```php
protected $fillable = [
    ...
    'role',
    ...
];
```

Role is a sensitive field. While the `ProfileUpdateRequest` correctly limits what fields can be updated via the profile form, having `role` in `$fillable` means any code path that calls `$user->fill($untrustedArray)` or `User::create($untrustedArray)` without a validated form request could allow role escalation.

**Recommendation**: Remove `'role'` from `$fillable`. Set role exclusively via `$user->forceFill(['role' => ...])` in the `SaoController` and seeder, making the privilege of setting the role explicit and auditable.

---

### Security Issue #3 — LOW: `EnsureHasRole` doesn't use `inheritedRoles()`

**File**: `app/Http/Middleware/EnsureHasRole.php`

The `UserRole` enum has a well-defined `inheritedRoles()` method, but the middleware does not use it. The current approach of explicitly listing all inherited roles in every route declaration (e.g., `'role:program_head,dean,sao,super_admin'`) works correctly today, but is fragile:

- A developer who adds a new route and writes `'role:program_head'` without listing `dean,sao,super_admin` would inadvertently lock Deans and SAOs out of that route.
- There is no test that verifies role inheritance at the middleware level.

**Recommendation**: Update `EnsureHasRole` to check if the user's role value is in the inherited roles of any of the required roles, not just an exact match.

---

### Security Issue #4 — LOW: School code is hardcoded in two controllers

**Files**: `RegisteredUserController.php` (line 46), `OnboardingController.php` (line 22)

```php
$school = School::where('code', 'SEAIT')->first();
```

If `SEAIT` is not seeded, `$school` is `null`, and `school_id` is silently set to `null` during registration. Users with `null` school_id bypass all school-scoped filters. This is unlikely in production but a risk in staging/CI environments where the school seeder may not have run.

**Recommendation**: Use `config('studhub.school_short')` instead of the hardcoded string, and `abort(500, 'School not configured.')` if the school record is missing.

---

## 14. Design & Architectural Concerns

### Concern #1 — `chat_room_memberships` table is defined but never populated

The `chat_room_memberships` table exists with `joined_at`, `last_read_at`, and `is_muted` columns. The `User::chatRooms()` and `ChatRoom::members()` relationships reference it with `withPivot()`. However, no code ever inserts into this table. Chat room access is controlled entirely by `program_id`/`year_level` matching.

This means:
- Unread message counts cannot be computed
- Per-room mute is not functional
- `joined_at` is never recorded

Either implement these features (populate the table on first room visit) or remove the table and the relationships to avoid confusion.

---

### Concern #2 — Chat room authorization is duplicated in 4 places

The same `program_id` and `year_level` matching logic appears in:
1. `ChatController::authorizeRoom()`
2. `ChatAttachmentController::download()`
3. `Livewire/Chat/RoomConversation::mount()`
4. `routes/channels.php` broadcast authorization

These four implementations can drift. A bug fix in one place would need to be applied to all four. Extract a `ChatRoomPolicy` or a `ChatRoom::authorizeForUser(User $user)` method.

---

### Concern #3 — `Archivist` and `Custodian` badge tiers share identical icons

In `BadgeTier::icon()`, both `Archivist` and `Custodian` return the same SVG path (the building/columns icon). Different tiers in a 12-tier progression should be visually distinct.

---

### Concern #4 — `CrossPostRequest` creates messages without `is_system` (see Bug #2)

Even after fixing the column issue, the design of cross-posting a message attributed to `requester_user_id` is confusing — the message appears in the chat as if the student themselves posted it. A proper system actor (e.g., a dedicated "StudHub Bot" user or a null sender_id with `is_system=true`) would be clearer.

---

### Concern #5 — `super_admin` feedback goes unrendered

`SubmitFeedback::resolveRecipient()` routes SAO and SuperAdmin feedback to `recipient_role = 'super_admin'`. No controller or view in the application surfaces feedback with `recipient_role = 'super_admin'`. This feedback is submitted to the database but never readable by anyone through the UI.

---

### Concern #6 — Chat message history is capped at 50 with no pagination

`RoomConversation::roomMessages()` returns `->limit(50)`. There is no "load earlier" button or infinite scroll. In active programs, chat history older than 50 messages is permanently inaccessible to users via the UI.

---

### Concern #7 — `RouteRequest` idempotency test asserts non-idempotent behavior

The test `'routing is idempotent — running twice does not double routes'` has the assertion:
```php
expect($secondCount)->toBe($firstCount * 2);
```

This confirms that running routing twice *doubles* the route records — the opposite of idempotent. The test name is misleading and the behavior it tests is a production risk if routing is accidentally triggered twice for the same request.

---

## 15. Performance Observations

### Full-text Search

`SearchGlobal` uses `LIKE '%query%'` on all three tables. This does not use indexes and causes full table scans. For a school with thousands of resources and messages, this will degrade. Consider adding MySQL FULLTEXT indexes or integrating MeiliSearch/Scout.

### `CheckAndAwardBadges` Query Volume

When triggered with `$trigger = 'all'` (36 badge checks), this action can execute 15–25+ SQL queries per user. This is acceptable when triggered asynchronously but should never be called synchronously in a request cycle.

### `resources.owner_user_id` Missing Index

Used in `CheckAndAwardBadges` (multiple `COUNT(*)` queries), `SearchGlobal`, and `DownloadResourceFile`. Add:
```php
$table->index('owner_user_id');
```

### Chat Message Load

The `RoomConversation` Livewire component fires a fresh database query on every Reverb broadcast event (`onMessageBroadcast()` unsets the computed property). For high-traffic rooms, this is `n_users × messages_per_second` database queries. A better pattern would be to append only the new message from the broadcast payload.

---

## 16. Docker & Infrastructure

The `docker-compose.yml` is clean and appropriate for local development:
- PHP-FPM 8.3, Nginx 1.27, MySQL 8.4, Redis 7, Mailpit
- Health checks on all services
- Volume persistence for DB and Redis
- `WWWUSER`/`WWWGROUP` args for correct file permissions

**Notable**: The compose file comment says "We do NOT containerize Reverb yet; it lands in Week 3." Reverb appears to be in use (it's in `composer.json` and the chat event uses `ShouldBroadcastNow`), so the comment is stale.

The `Makefile` provides convenient `make dev`, `make test`, `make analyse` targets — good developer experience.

---

## 17. Recommendations Summary

### Fix Immediately (Before Production)

| Priority | Issue | File |
|---|---|---|
| P0 | `requests.title` column doesn't exist — crashes search | `SearchGlobal.php` |
| P0 | `is_system` column missing from `chat_messages` | `CrossPostRequest.php` + new migration |
| P1 | `SendDailyDigest` ignores notification preferences | `SendDailyDigest.php` |
| P1 | `markHelpful` session-only deduplication | `ResourceController.php` |

### Fix Soon (Before Wide Rollout)

| Priority | Issue | File |
|---|---|---|
| P2 | Email verification bypass should be documented or fixed | `RegisteredUserController.php` |
| P2 | `'role'` in `User::$fillable` — latent escalation risk | `User.php` |
| P2 | `EnsureHasRole` doesn't use `inheritedRoles()` | `EnsureHasRole.php` |
| P2 | `historicalFulfillmentRate` static cache leak | `RouteRequest.php` |
| P2 | Hardcoded `'SEAIT'` school code | 2 controllers |
| P2 | `super_admin` feedback never surfaces in any UI | `SubmitFeedback.php` |

### Address in Next Sprint

| Priority | Issue |
|---|---|
| P3 | Extract shared chat authorization logic into a Policy |
| P3 | Add `resources.owner_user_id` database index |
| P3 | Implement chat message pagination (>50 messages) |
| P3 | Populate `chat_room_memberships` on room entry (enable unread counts) |
| P3 | Fix `Archivist`/`Custodian` duplicate badge icons |
| P3 | Add tests for `DeanController`, `SaoController`, `DownloadResourceFile` |
| P3 | Replace MySQL-specific SQL in `CheckAndAwardBadges` for SQLite compatibility |
| P3 | Fix misleading test name in `RouteRequestTest` |
| P3 | Consider FULLTEXT indexing for global search |

---

## Appendix — Files Reviewed

All files in the following paths were read and analyzed:

- `app/` (all 80+ PHP files across Controllers, Domain, Livewire, Models, Middleware, View)
- `database/` (all 29 migrations + 7 seeders)
- `resources/views/` (all 46 Blade templates)
- `routes/` (web.php, auth.php, channels.php, console.php)
- `tests/` (all 35 test files)
- `config/` (app, auth, database, studhub, lends, filesystems, queue)
- `bootstrap/app.php`
- `composer.json`, `phpunit.xml`, `phpstan.neon`, `docker-compose.yml`
- `docs/` (all 10 documentation files)

---

*Report generated by Claude Sonnet 4.6 on 2026-05-31.*

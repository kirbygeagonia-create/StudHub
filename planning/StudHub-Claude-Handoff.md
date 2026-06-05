# StudHub — Complete Findings & Handoff Document

> **Purpose:** This document is a full audit trail of three rounds of code review conducted on the StudHub GitHub repository (`https://github.com/kirbygeagonia-create/StudHub.git`). Hand this to Claude as context before asking for any further implementation help. It covers every bug, security issue, design concern, performance finding, and improvement recommendation — with current fix status for each.

---

## Project Overview

| Property | Value |
|---|---|
| Framework | Laravel 11 · PHP 8.2 · Livewire 4 |
| Frontend | Tailwind CSS · Alpine.js · Laravel Reverb (WebSockets) |
| Database | MySQL 8 (production) · SQLite in-memory (tests) |
| Queue / Cache | Redis 7 |
| Test suite | 256 Pest tests across 42 test files |
| Static analysis | PHPStan Larastan Level 6 (clean) |
| Formatter | Pint (clean) |
| CI | GitHub Actions — lint → analyse → test on every PR |
| Migrations | 40 migration files |
| App PHP files | 118 files in `app/` |
| Models | 24 Eloquent models |
| Domain modules | Catalog · Chat · Feedback · Identity · Lends · Moderation · Reputation · Requests · Search |

**What StudHub is:** A school-wide academic resource exchange platform for SEAIT (South East Asian Institute of Technology). Students upload and borrow study materials, make cross-program resource requests, chat in program-scoped rooms, earn karma and badges, and are moderated by a hierarchy of moderators, program heads, deans, SAO, and super admins.

---

## Architecture Summary

```
app/
├── Domain/              ← DDD modules. Each has Actions/, Enums/, Jobs/, Notifications/
│   ├── Catalog/         ← Resources, shelves, file watermarking, thumbnails
│   ├── Chat/            ← Rooms, messages, Reverb broadcast events, mentions
│   ├── Feedback/        ← Submission, routing chain (student → mod → PH → Dean → SAO)
│   ├── Identity/        ← Roles, email domain rules, onboarding scope guard
│   ├── Lends/           ← Physical lending, return reminders, escalation
│   ├── Moderation/      ← Reports, suspensions, audit log
│   ├── Reputation/      ← Karma events, 12 badge tiers, 36 achievement badges
│   ├── Requests/        ← Requests, offers, weighted routing engine (RouteRequest)
│   └── Search/          ← Global search, daily digest job
├── Http/
│   ├── Controllers/     ← Thin handlers, delegate to Domain Actions
│   └── Middleware/      ← EnsureHasRole · EnsureNotSuspended · EnsureUserIsOnboarded
├── Livewire/            ← RoomConversation (chat real-time component)
├── Models/              ← 24 Eloquent models
└── Policies/            ← ChatRoomPolicy (view authorization)
```

**Key design decisions:**
- Controllers are thin — all business logic lives in `app/Domain/*/Actions/`
- Role hierarchy: `Student < Moderator < ProgramHead < Dean < Sao < SuperAdmin`
- `EnsureHasRole` middleware uses `inheritedRoles()` on the `UserRole` enum so higher roles automatically pass lower-role checks
- `ChatRoomPolicy` is the single source of truth for room access (used in controller, Livewire component, and `channels.php`)
- Chat broadcasts via `ShouldBroadcastNow` (synchronous, inside the HTTP request)
- All queue jobs implement `ShouldQueue`
- Tests use SQLite in-memory; badge SQL was rewritten to be SQLite-compatible

---

## Round 1 Findings — Original Analysis (2026-05-31)

All 16 items from this round were fixed in commit `24df68d`.

### Bugs Fixed ✅

| # | File | Issue | Fix Applied |
|---|---|---|---|
| B1 | `app/Domain/Search/Actions/SearchGlobal.php` | Queried `requests.title` — column does not exist in the `requests` migration. Crashed MySQL search. | Removed the `->where('title', ...)` line; only `description` queried. |
| B2 | `app/Domain/Requests/Jobs/CrossPostRequest.php` | Set `'is_system' => true` on `ChatMessage::create()` but `is_system` column didn't exist in migration or `$fillable`. Silently dropped by mass assignment. | Migration `2026_06_01_000001` adds the column; `StudHubBot` system user created; `$fillable` updated. |
| B3 | `app/Http/Controllers/ResourceController.php` | `markHelpful` used session key for deduplication only. Clear cookies = vote again. | `resource_helpful_votes` table with `UNIQUE(resource_id, user_id)` constraint added. `ResourceHelpfulVote` model created. |
| B4 | `app/Domain/Search/Jobs/SendDailyDigest.php` | Never read `notification_preferences['digest_enabled']` before sending. Users who opted out still received emails. | Guard added: `if (($prefs['digest_enabled'] ?? true) === false) { continue; }` |
| B5 | `app/Domain/Requests/Actions/RouteRequest.php` | `historicalFulfillmentRate()` used `static $cache = []` inside an instance method — PHP process-level cache. Stale data in FPM workers; leaked state between test cases. | Replaced with `private array $fulfillmentCache = []` on the instance. |

### Security Issues Fixed ✅

| # | File | Issue | Fix Applied |
|---|---|---|---|
| S1 | `app/Http/Controllers/Auth/RegisteredUserController.php` | `email_verified_at = now()` set immediately on registration, bypassing `MustVerifyEmail`. | Documented as intentional for intranet use; `firstOrFail()` added so missing school record throws 500 instead of silently setting `null`. |
| S2 | `app/Models/User.php` | `'role'` was in `$fillable` — latent privilege escalation risk. | Removed from `$fillable`. All role assignments now use `forceFill(['role' => ...])` in `SaoController` and `DeanController`. |
| S3 | `app/Http/Middleware/EnsureHasRole.php` | Middleware didn't use `inheritedRoles()`. Worked only because every route declaration explicitly listed all inherited roles — fragile. | Middleware now calls `inheritedRoles()` on each required role and checks membership. |
| S4 | `app/Http/Controllers/OnboardingController.php` · `RegisteredUserController.php` | Hardcoded `School::where('code', 'SEAIT')` in both. Null school → user gets `school_id = null`, bypassing all school-scoped filters. | Both now use `config('studhub.school_code', 'SEAIT')` + `firstOrFail()`. |

### Design / Architectural Concerns Fixed ✅

| # | Issue | Fix Applied |
|---|---|---|
| D1 | `chat_room_memberships` table defined but never populated — `joined_at`, `last_read_at`, `is_muted` were dead weight. | `RoomConversation::mount()` upserts membership on room entry; `PostChatMessage` increments `unread_count`; badge shown on chat index. |
| D2 | Chat room auth logic duplicated across `ChatController`, `ChatAttachmentController`, `RoomConversation`, and `channels.php`. | `ChatRoomPolicy::view()` extracted. All 4 sites now call `$user->can('view', $room)`. |
| D3 | `BadgeTier::Archivist` and `::Custodian` had identical SVG icon paths. | Fixed — all 12 tier icons are now distinct. |
| D4 | `CrossPostRequest` attributed system messages to the requester user. | `StudHubBot` system user created; messages sent from bot with `is_system = true`; rendered as centered italic pill in chat UI. |
| D5 | `super_admin` feedback (`SubmitFeedback::resolveRecipient()`) had no UI to surface it. | `SaoController` now queries `recipient_role IN ('sao', 'super_admin')`; SAO feedback view renders both. |
| D6 | Chat history hard-capped at 50 messages, no pagination. | `loadMore()` and `hasMoreMessages()` implemented; "Load older messages" button shown when more exist. |
| D7 | `RouteRequest` idempotency test had a name that said "is idempotent" but asserted double-routing. | Test renamed to describe actual (non-idempotent) behavior. |

### Performance Observations Fixed ✅

| # | Issue | Fix Applied |
|---|---|---|
| P1 | `SearchGlobal` used `LIKE '%q%'` — full table scans. | `FULLTEXT` index added; `LearningResource::scopeSearch()` uses `MATCH...AGAINST` on MySQL, LIKE fallback on SQLite. |
| P2 | `CheckAndAwardBadges` used MySQL-only SQL (`TIMESTAMPDIFF`, `YEARWEEK`, `DATE_ADD`, `HOUR()`). Failed on SQLite (test environment). | Rewritten using `datetime(..., '+N hours')` syntax (SQLite-compatible) and PHP-level date math. |
| P3 | `resources.owner_user_id` had no index despite heavy use in badge checks and search. | Index added in `2026_05_23_000000_add_missing_db_indexes.php`. |
| P4 | Chat component re-fetched all 50 messages from DB on every Reverb broadcast. | `onMessageBroadcast()` now constructs a lightweight `ChatMessage` from the broadcast payload and appends it to `$broadcastMessages` — no DB query on incoming message. |

---

## Round 2 Findings — Verification Review (2026-06-02)

All 16 items from Round 1 were confirmed fixed. Round 2 introduced 7 new findings.

### New Bugs Found in Round 2

| # | Severity | File | Issue | Status |
|---|---|---|---|---|
| NB1 | Minor | `app/Domain/Chat/Events/ChatMessagePosted.php` | `broadcastWith()` did not include `is_system` in payload. System messages rendered as normal user messages in the live feed until page reload. | **Fixed** in commit `0d41bf1` — `'is_system' => $this->message->is_system` added to the return array. |
| NB2 | Medium | `app/Domain/Requests/Jobs/NotifyRoutedUsers.php` | Notification preferences `only_urgent` and `muted_programs` were stored and shown in the UI but never read by the job. | **Fixed** — job now reads both prefs and skips notifications accordingly. `NotifyRoutedUsersTest` added. |

### New Improvements Implemented in Round 2 ✅

All items below were implemented:

| Item | File | What was done |
|---|---|---|
| Resource thumbnails displayed | `resources/views/resources/index.blade.php` | `thumbnail_url` now shown on resource cards with `loading="lazy"`, fallback to type icon. |
| View Composer for admin sidebar | `app/Providers/AppServiceProvider.php` | `View::composer([...admin views...])` shares `$openReports` and `$unreadFeedback` so each controller method doesn't re-query. |
| Job retry config | All 5 queue jobs | `$tries`, `$backoff`, `$timeout` added to `WatermarkResourceFile`, `NotifyRoutedUsers`, `CrossPostRequest`, `SendReturnReminders`, `SendDailyDigest`. |
| `.env.example` broadcast fix | `.env.example` | Changed to `BROADCAST_CONNECTION=reverb` with all `REVERB_*` variables. |
| DashboardController | `app/Http/Controllers/DashboardController.php` | Dashboard route moved from inline closure to proper controller. Passes `$user`, `$karma`, `$badge` to view — `Auth::user()` calls in `dashboard.blade.php` eliminated (0 remaining). |
| `WatermarkResourceFile` ShouldBeUnique | `app/Domain/Catalog/Jobs/WatermarkResourceFile.php` | Implements `ShouldBeUnique` with `uniqueId()` returning resource ID. Prevents double-processing. |
| LendEscalated notification | `app/Domain/Lends/Notifications/LendEscalated.php` | New `ShouldQueue` notification dispatched from `Lend::escalate()`. Notifies borrower via database channel. |
| Ghostscript path allowlist | `app/Domain/Catalog/Jobs/WatermarkResourceFile.php` | Binary path validated against allowlist `['gs', 'gswin64c', 'gswin32c', '/usr/bin/gs', '/usr/local/bin/gs', '/opt/homebrew/bin/gs']` before use. |
| Cursor-based chat pagination | `app/Livewire/Chat/RoomConversation.php` | `roomMessages()` now uses `where('id', '<', $this->oldestMessageId)->limit(50)` — always fetches exactly 50 rows regardless of page depth. |
| `make fresh` / `make sh` / DevSeeder | `Makefile` · `database/seeders/DatabaseSeeder.php` | `make fresh` added; `make sh` added; `DevUsersSeeder` included in `DatabaseSeeder` by default. |
| `phpunit.xml` broadcast fix | `phpunit.xml` | `BROADCAST_CONNECTION=null` added — CI no longer fails on broadcast-related tests. |
| 4 new test files | `tests/Feature/` | `ChatMessageBroadcastTest` · `NotifyRoutedUsersTest` · `HelpfulVoteTest` · `RoleInheritanceTest` — 11 new tests, 26 assertions. |
| PWA manifest → SVG icons | `public/manifest.json` | Updated to reference `.svg` instead of `.png`. |
| Download count tracking | `app/Domain/Catalog/Actions/DownloadResourceFile.php` · migration | `download_count` column added; `increment('download_count')` called on every successful download. |

---

## Round 3 Findings — Current State (2026-06-03)

All 16 items from Round 2 confirmed implemented. **0 new bugs found.** 5 items remain unresolved.

### Remaining Items — Not Yet Fixed

These are all low-risk (no crashes, no security holes). Listed in order of urgency.

---

#### ⚠️ Item R1 — PWA icon SVG files missing from build pipeline

**Risk:** Medium — "Add to Home Screen" install prompt shows broken icon.

**Files involved:**
- `public/manifest.json` — references `/build/assets/icon-192.svg` and `/build/assets/icon-512.svg`
- `vite.config.js` — only bundles `resources/css/app.css` and `resources/js/app.js`

**Problem:** The icon files do not exist anywhere in the repository (not in `resources/`, not in `public/`, not as Vite inputs). After `npm run build`, the paths referenced in `manifest.json` will not exist, so PWA install prompts render a broken/missing icon.

**Fix options (pick one):**

Option A — commit static icons to `public/` (simplest):
```bash
# Create icon SVGs in public/ directly — these are served as-is
# public/icon-192.svg
# public/icon-512.svg
```
Then update `manifest.json`:
```json
"src": "/icon-192.svg",
"src": "/icon-512.svg"
```

Option B — include in Vite build (proper):
```js
// vite.config.js
laravel({
    input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/images/icon-192.svg',   // ← add these
        'resources/images/icon-512.svg',
    ],
})
```
Then update `manifest.json` to use `{{ Vite::asset('resources/images/icon-192.svg') }}` in a controller that returns the manifest, or hard-code the hashed path after build.

---

#### ⚠️ Item R2 — Reverb not in `docker-compose.yml`

**Risk:** Medium — real-time chat silently non-functional for new developers.

**File:** `docker-compose.yml`

**Problem:** `.env.example` correctly sets `BROADCAST_CONNECTION=reverb` with all `REVERB_*` variables. But `docker-compose.yml` has no `reverb` service. A developer who runs `make up` has everything except a running Reverb server. Chat messages save to the DB but are never broadcast. The developer has no idea why the live chat isn't updating.

**Fix:** Add a `reverb` service to `docker-compose.yml`:
```yaml
reverb:
  image: php:8.3-cli
  working_dir: /var/www/html
  volumes:
    - .:/var/www/html
  command: php artisan reverb:start --host=0.0.0.0 --port=8080
  ports:
    - "${REVERB_PORT:-8080}:8080"
  depends_on:
    - app
  networks:
    - studhub
```
Or use the same app image as the `app` service. Also add `make reverb` to the Makefile:
```makefile
reverb: ## Start the Reverb WebSocket server
    php artisan reverb:start --host=0.0.0.0 --port=8080
```

---

#### 🔵 Item R3 — `navigation.blade.php` calls `Auth::user()` 22 times

**Risk:** Low — cosmetic/performance, not a bug.

**File:** `resources/views/layouts/navigation.blade.php`

**Problem:** `dashboard.blade.php` was correctly fixed (0 `Auth::user()` calls — data passed from `DashboardController`). But `navigation.blade.php` still calls `Auth::user()` 22 times across role checks, notification counts, avatar rendering, etc. The session guard caches the result, so it's not 22 DB queries — but it's noisy, hard to test, and inconsistent with the dashboard fix.

**Fix:** Add a `View::share` in `AppServiceProvider::boot()` for the app layout:
```php
// app/Providers/AppServiceProvider.php — inside boot()
View::composer('layouts.app', function ($view): void {
    $user = Auth::user();
    if ($user) {
        $view->with('authUser', $user);
    }
});
```
Then replace all `Auth::user()` in `navigation.blade.php` with `$authUser`.

Alternatively, since `navigation.blade.php` is a partial included in `layouts.app`, and `layouts.app` is used everywhere, passing `$authUser` from a base layout share is the cleanest approach.

---

#### 🔵 Item R4 — `StudHubBot` has `super_admin` role

**Risk:** Low — bot cannot authenticate, but it passes every role guard if somehow used as a subject.

**File:** `database/migrations/2026_06_01_000005_create_studhub_bot_user.php`

**Problem:** The bot user is created with `role = 'super_admin'`. This means it passes every single `EnsureHasRole` middleware check. In practice the bot has no password and can't be authenticated, but it's semantically incorrect and any future code that iterates all users with a given role would include the bot unintentionally.

**Fix:** Add a `system` case to the `UserRole` enum that sits outside the normal hierarchy:
```php
// app/Domain/Identity/Enums/UserRole.php
case System = 'system';
```
Update `inheritedRoles()` to return `[]` for `System`. Update the bot migration to use `UserRole::System`. Update `EnsureHasRole` to explicitly reject `system` role users from all checks (the bot should never be acting as a logged-in user).

---

#### 🔵 Item R5 — Account deletion cascades and hard-deletes all resources

**Risk:** Low-medium — data loss for other users who saved/borrowed deleted user's resources.

**Files:**
- `database/migrations/2025_03_01_000004_create_resources_table.php` — `owner_user_id` is `cascadeOnDelete`
- `app/Http/Controllers/ProfileController.php` — `$user->delete()` with no pre-processing

**Problem:** When a user deletes their account, all `LearningResource` records they own are immediately hard-deleted via cascade. Any other student who had that resource saved on their shelf or currently in a `Lend` gets a broken reference. The lend's `resource_id` foreign key is `nullOnDelete` on the `offers` table but the actual resource record vanishes.

**Fix (recommended approach):**
1. Change the migration constraint to `nullOnDelete` on `owner_user_id`:
```php
// New migration: change_resources_owner_user_id_to_null_on_delete
$table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete()->change();
```
2. Add a `deleting` observer on `User` to archive their resources before deletion:
```php
// app/Observers/UserObserver.php
public function deleting(User $user): void
{
    $user->resources()->update([
        'availability' => ResourceAvailability::Archived->value,
        'owner_user_id' => null,
    ]);
}
```
3. Register the observer in `AppServiceProvider`.
4. Update `resources/show.blade.php` to show "Original uploader's account has been deleted" when `owner_user_id` is null.

---

## All Improvement Recommendations (Future Work)

These were identified across all three review rounds. None are bugs — all are quality improvements. Listed by category.

### UI / UX

| ID | Recommendation | Effort |
|---|---|---|
| UX-1 | **Live search** — current search requires full page reload. Add Livewire or Alpine.js + debounced fetch so results appear as the user types. | Medium |
| UX-2 | **Chat character counter** — body is capped at 4,000 chars but no counter visible. Add live `420 / 4000` below textarea. | Small |
| UX-3 | **Mobile bottom tab bar** — hamburger-only nav on mobile. A sticky bottom bar (Home / Chat / Resources / Requests) matches native-app mental model and pairs with the existing PWA manifest. | Medium |
| UX-4 | **Shelf empty-state CTA** — when shelf is empty, show a direct "Browse resources →" link to convert passive page view to action. | Small |
| UX-5 | **Group notifications by type** — notification page lists all items chronologically. 10 mentions in one day = 10 identical rows. Group by type with collapsible sections. | Medium |
| UX-6 | **Fulfilled request → resource link** — when a request has `status = fulfilled`, the show page doesn't link to the resource that fulfilled it. Add "See the resource →" link. | Small |
| UX-7 | **Chat file upload progress** — no feedback while file uploads. Add "Uploading…" state on the attach button during Livewire request. Prevents double-submission. | Small |
| UX-8 | **Return-by date picker in lend form** — `Lend.return_by` exists and is used by reminders, but `LendController::store()` doesn't accept it. Add a `<input type="date">` to the lend form. | Small |
| UX-9 | **Admin chart time-range selector** — SAO/Dean/PH dashboards show last-7-days charts only. Add dropdown: "Last 7 days / 30 days / This semester" driven by query parameter. | Medium |
| UX-10 | **Accessibility — 61 buttons without aria-label** — `grep -c '<button'` across all views finds many buttons with no `aria-label`. Add labels to icon-only buttons. | Medium |

### Backend & Architecture

| ID | Recommendation | Effort |
|---|---|---|
| BE-1 | **`ShouldBroadcast` instead of `ShouldBroadcastNow`** — `ChatMessagePosted` broadcasts synchronously inside the HTTP request. Under load this increases P95 latency for message sends. Switch to `ShouldBroadcast` (queued). | Small |
| BE-2 | **`hasMoreMessages()` fires extra DB query per render** — every chat render calls `roomMessages` to find the oldest ID, then `hasMoreMessages()` fires another `->exists()`. Embed this info in the `roomMessages` computed property (e.g., fetch 51 rows, if count > 50 there are more, return first 50). | Small |
| BE-3 | **`RecalculateRoutingWeights` O(N) UPDATE loop** — loops over every program-subject fulfillment count and issues one `UPDATE` per row. Replace with a single `DB::table('program_subjects')->upsert(...)` call. | Small |
| BE-4 | **`muted_programs` preference as typed DTO** — `notification_preferences` is a JSON blob cast to array. As it grows, introduce a typed DTO or value object so preferences are validated exhaustively and not spread as raw array access across the codebase. | Medium |
| BE-5 | **Announcements feature — build or remove** — `studhub.announcements_enabled` flag exists, route exists, but the view is a "coming soon" placeholder. Either implement (SAO broadcasts school-wide banner, students see it on dashboard) or remove the route and view to reduce surface area. | Large/Remove |
| BE-6 | **Enum labels in views** — two Blade files use `ucfirst(str_replace('_', ' ', $value))` instead of the enum's `label()` method. `requests/show.blade.php` for `type_wanted` and `resources/show.blade.php` for `condition`. Fix to use `ResourceType::from($value)->label()`. | Small |

### Security

| ID | Recommendation | Effort |
|---|---|---|
| SEC-1 | **Search rate limit** — `GET /search` is `throttle:30,1`. At 30 req/min an automated script can scrape the full message history. Consider `throttle:10,1` specifically on search and add `min_length: 3` validation to reject single-character wildcard queries. | Small |
| SEC-2 | **Validate email domain on profile email change** — `ProfileUpdateRequest` allows any email address as the updated email. It should run the same `AllowedSchoolEmailDomain` rule as registration to prevent a student from changing their email to a non-school address. | Small |

### Testing

| ID | Recommendation | Effort |
|---|---|---|
| T-1 | **PHPStan → Level 8** — codebase is clean at Level 6. Levels 7–8 add strict return type checking on Eloquent relations and dead code detection. With Livewire 4 type stubs, Level 8 is realistic. | Medium |
| T-2 | **`WatermarkResourceFile` job test** — no test covering the watermarking pipeline (PDF thumbnail generation, image overlay, Ghostscript fallback path). | Medium |
| T-3 | **`DownloadResourceFile` end-to-end test** — download streaming, watermark application, download count increment. Currently untested. | Medium |
| T-4 | **`CheckAndAwardBadges` badge-specific tests** — badge logic is complex (36 badge qualifiers). Only karma is tested. Specific badge qualification tests (e.g., QuickDraw badge timing, NightOwl hour window) should be added. | Large |

### Developer Experience

| ID | Recommendation | Effort |
|---|---|---|
| DX-1 | **Storybook / component preview route** — `x-icon`, `x-stat-card`, `x-empty-state`, `x-page-header`, `x-lend-row` components have no isolated preview. Add a `GET /dev/components` route (gated to `APP_ENV=local`) showing all components with all prop variants. | Medium |
| DX-2 | **Service worker cache version** — `public/sw.js` uses `const CACHE_NAME = 'studhub-v1'`. After deployment the version string doesn't change, so returning users may see stale assets. Inject the Vite build hash into the cache name at build time. | Small |

---

## File Reference Map

Key files mentioned in this document, by path:

| Path | What it does |
|---|---|
| `app/Domain/Requests/Actions/RouteRequest.php` | Weighted routing engine — scores programs, picks target users |
| `app/Domain/Reputation/Actions/CheckAndAwardBadges.php` | Evaluates all 36 badge qualifiers; triggered by AwardKarma |
| `app/Domain/Chat/Events/ChatMessagePosted.php` | Reverb broadcast event — `broadcastWith()` payload sent to clients |
| `app/Livewire/Chat/RoomConversation.php` | Livewire chat component — send, loadMore, onMessageBroadcast |
| `app/Policies/ChatRoomPolicy.php` | Single source of truth for room access authorization |
| `app/Providers/AppServiceProvider.php` | View Composer for admin sidebar badge counts |
| `app/Http/Middleware/EnsureHasRole.php` | Role middleware using `inheritedRoles()` |
| `app/Domain/Identity/Enums/UserRole.php` | Role enum with `inheritedRoles()` — hierarchy definition |
| `app/Domain/Catalog/Jobs/WatermarkResourceFile.php` | Async job: PDF thumbnail via Ghostscript, image watermarking |
| `app/Domain/Catalog/Actions/DownloadResourceFile.php` | Sync download with per-user watermark, download_count increment |
| `app/Domain/Search/Actions/SearchGlobal.php` | FULLTEXT + LIKE search across resources, requests, chat messages |
| `app/Console/Commands/RecalculateRoutingWeights.php` | Weekly: updates `program_subjects.weight` from real fulfillment data |
| `routes/web.php` | All application routes with middleware stacks |
| `routes/channels.php` | Reverb channel authorization using `ChatRoomPolicy` |
| `config/studhub.php` | App-specific config: school code, email domains, feature flags |
| `database/migrations/2026_06_01_000005_create_studhub_bot_user.php` | Creates StudHubBot system user (currently super_admin — should be system role) |
| `public/manifest.json` | PWA manifest — icon paths currently broken (see R1) |
| `public/sw.js` | Service worker — cache version hardcoded as `studhub-v1` |
| `docker-compose.yml` | Dev stack — missing Reverb service (see R2) |
| `.env.example` | All environment variables documented including REVERB_* |
| `phpunit.xml` | Test config — SQLite in-memory, BROADCAST_CONNECTION=null |
| `phpstan.neon` | PHPStan config — Level 6 |
| `.github/workflows/ci.yml` | GitHub Actions: lint → analyse → test |

---

## Current Health Score

| Area | Status |
|---|---|
| Bugs | ✅ None known |
| Security | ✅ No active vulnerabilities · 1 low-risk architectural item (bot role) |
| Tests | ✅ 256 tests · 42 files · PHPStan L6 clean · Pint clean |
| Performance | ✅ Fulltext indexes · cursor pagination · instance-scoped caches |
| Remaining items | ⚠️ 2 medium (PWA icons, Reverb in Docker) · 3 low (Auth calls, bot role, cascade delete) |
| Overall | **Production-ready for pilot deployment** |

---

*Document compiled from three code review rounds by Claude Sonnet 4.6 · 2026-06-03*
*Repository: `https://github.com/kirbygeagonia-create/StudHub.git`*
*Last reviewed commit: `6f68e92` — "Implement remaining report items: thumbnails, cursor pagination, missing tests"*

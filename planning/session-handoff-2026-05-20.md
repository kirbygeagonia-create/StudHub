# Session Handoff — Week 11 Complete → Week 12 Ready

**Date:** 2026-05-20
**Project:** StudHub (SEAIT cross-program resource exchange)
**Status:** ✅ Week 11 delivered. Green CI. Ready for Week 12 launch.

---

## 1. Current State Snapshot

| Metric | Value |
|--------|-------|
| **Tests** | 186 passed, 459 assertions, 1 skipped |
| **PHPStan** | Level 6 — 0 errors |
| **Pint** | Passes (PSR-12, Laravel preset) |
| **Laravel** | 11.x on PHP 8.2.12 |
| **Test DB** | SQLite in-memory (green, 22s runtime) |
| **Audit findings** | 15/17 resolved (F15 skipped intentionally, F17 remaining) |
| **Documentation** | ER diagram + routing sequence diagram in `docs/diagrams/` |

---

## 2. Week 11 — Pilot Prep & Hardening ✅ COMPLETE

### Security & Rate Limiting
- 20 POST routes throttled (requests: 10/min, resources: 30/min, reports: 10/min, lends: 20/min, chat: 60/min)
- MIME allow-list enforced at both Livewire `ResourceForm` validation AND `CreateResource` action
- Suspended users blocked from HTTP routes (`EnsureNotSuspended` middleware) AND WebSocket channels (`routes/channels.php`)

### Operations
- `studhub:backup-database` command with 7-day rotation (scheduled daily at 02:00)
- `studhub:expire-requests` command (scheduled daily at 03:00)
- `/up` healthcheck route configured in `bootstrap/app.php`
- `config/filesystems.php` has `backups` disk configured

### User-Facing
- Landing page (`welcome.blade.php`) — custom StudHub branding, replaces Laravel default
- `/help` page with usage guide + karma badge tiers
- `/aup` page with Acceptable Use Policy
- Report buttons on: chat messages, user profiles, resources

### Documentation
- `docs/diagrams/er-diagram.md` — full entity relationship diagram
- `docs/diagrams/routing-sequence.md` — routing engine sequence diagram with weight breakdown
- `AGENTS.md` updated to Week 11 state

### Refactoring (F16)
- `App\Models\Request` renamed to `ResourceRequest` (25+ files)
- Old `Request.php` kept as backward-compat alias: `class Request extends ResourceRequest {}`
- All imports, type-hints, PHPDocs, factories updated
- Factory resolution via `newFactory()` override

---

## 3. Go / No-Go Checklist — Pre-Pilot Status

### ✅ Code Quality
- [x] All 14 applicable core audit findings (F1–F14) closed
- [x] F16 (Request → ResourceRequest rename) done
- [ ] **F17: Report global scope** — NOT STARTED (see §5.1)
- [x] All 6 pending UX items (W9-1 through W9-6) closed
- [x] PHPStan level 6 green (0 errors)
- [ ] **220+ Pest tests** — currently 186; need ~34 more (see §5.2)
- [x] Pint formatting clean

### ✅ Security
- [x] Rate limits on all user-action POST routes
- [x] File MIME allow-list on resource + chat uploads
- [ ] **Real per-user PDF watermarking** — 🟡 still SVG stub (see §5.3)
- [x] Suspended user cannot post or receive real-time chat (verified manually)

### ✅ Operations
- [x] Daily DB backup command running
- [x] `/up` healthcheck returns 200
- [ ] **End-to-end smoke test** — NOT BUILT (see §5.8)

### ✅ User-Facing
- [x] Landing page replaces Laravel welcome
- [x] `/help` page published
- [x] AUP page published
- [ ] **Feedback form** — NOT BUILT (see §5.4)

### ✅ Documentation
- [x] ER diagram + routing sequence diagram in `docs/diagrams/`
- [x] `planning/audit-final-2026-05-18.md` committed
- [ ] **README.md** — 🟡 stale, still says "Week 1" (see §5.5)
- [ ] **Paper draft + demo deck** — NOT STARTED (see §5.6, §5.7)

### ⚠️ Process (blockers need human action)
- [ ] Adviser signoff in writing
- [ ] Invite list of 30 confirmed across BSIT × BSCE × BSBA-MM
- [ ] Office hours scheduled
- [ ] SEAIT registrar curriculum confirmation

---

## 4. Architecture Quick-Reference

### Directory Map (key files only)
```
app/
  Domain/
    Catalog/
      Actions/CreateResource.php         # Resource upload + validation
      Jobs/WatermarkResourceFile.php     # Thumbnail generation (svg stub → Imagick)
    Chat/
      Actions/PostChatMessage.php        # Chat messages + @mentions
      Events/ChatMessagePosted.php       # Reverb broadcast
    Moderation/
      Actions/CreateReport.php           # Self-report guard exists (F6)
      Actions/ResolveReport.php          # Message snapshot before hide (F5)
      Actions/SuspendUser.php            # Suspension + auto-expiry
      Enums/ReportedType.php             # message | resource | user
      Enums/ReportStatus.php             # open | dismissed | actioned
    Reputation/
      Actions/AwardKarma.php             # Atomic increment (F9)
      Enums/KarmaEventReason.php         # Points per event type
    Requests/
      Actions/RouteRequest.php           # Weighted scoring + historicalFulfillmentRate (F1)
  Models/
    Report.php                           # F17 target: add school_id global scope
    User.php                             # isSuspended() uses isFuture() (F3)
    ResourceRequest.php                  # Formerly Request (F16)
  Console/Commands/
    ExpireRequests.php                   # Daily stale-request cleanup (F10)
    BackupDatabase.php                   # Daily DB dump + rotation
routes/
  channels.php                           # Suspended users blocked from Reverb (F7)
  console.php                            # Scheduler: backup + expire-requests
tests/
  Feature/
    Moderation/ModerationTest.php        # Primary test expansion target
    Chat/ChatAccessTest.php              # Suspension + channel auth tests
    SmokeTest.php                        # Health endpoint + backup command
```

### Laravel Conventions (follow these)
- `DB::transaction()` for all writes in Actions
- Backed string enums for every state field
- `abort_unless($user !== null, 403)` at controller method top
- Test factories: `User::factory()->onboarded()->create()`
- CI runs: Pint → PHPStan L6 → Pest (SQLite in-memory)

---

## 5. Week 12 — Full Task Breakdown with Effort Estimates

### 5.1 F17: Report Global Scope (🟡 20 min)

**What:** Reports currently show across all schools. Add a global scope that joins through `users` to filter by `school_id`.

**File:** `app/Models/Report.php`

**Approach:**
1. Create `app/Models/Scopes/ReportSchoolScope.php` — anonymous global scope that sub-selects `reporter_user_id` against `users` WHERE `school_id` matches the authenticated user's `school_id`.
2. Apply in `Report::booted()`.
3. Moderation dashboard already has per-program filtering via F4, so the global scope adds the school layer.
4. Test: create reports from two different schools, verify only own school's reports appear.

**Note:** F15 (`add school_id column to reports table`) was intentionally skipped — the scope approach is cleaner and doesn't require a schema migration.

---

### 5.2 Expand Test Suite to 220+ (🔴 3–4 h)

**Target:** +34 tests to reach 220. Spread across three domains.

#### 5.2a Moderation (+10 tests) — `tests/Feature/Moderation/ModerationTest.php`
- [ ] Program-filtered dashboard: moderator sees only their program's reports
- [ ] Admin sees all reports regardless of school
- [ ] Report status transitions: open → dismissed, open → actioned
- [ ] Duplicate report rejection (already reported)
- [ ] Report non-existent entity returns 404
- [ ] Report with `ReportedType` enum validation (rejects invalid type)
- [ ] SuspendUser: sets `suspended_until`, creates audit log
- [ ] SuspendUser: rejects suspending admins
- [ ] ResolveReport: non-moderator cannot resolve
- [ ] Audit log records correct entity type and action

#### 5.2b Chat (+12 tests) — `tests/Feature/Chat/ChatAccessTest.php` + `PostChatMessageTest.php`
- [ ] Suspended user gets 403 on chat-room page
- [ ] Suspended user channel rejection at `routes/channels.php` level
- [ ] Message body exceeds 2000 chars → 422
- [ ] File attachment exceeds 25 MB → 422
- [ ] File attachment with disallowed MIME → 422
- [ ] Mention parsing: multiple @mentions in one message
- [ ] Empty message body → 422
- [ ] Non-program-member cannot access room
- [ ] @mention notification delivered to mentioned user
- [ ] Chat message factory generates valid data
- [ ] Room provisioning creates rooms if missing
- [ ] Room provisioning skips if already exists

#### 5.2c Smoke Tests (+10 tests) — `tests/Feature/SmokeTest.php`
- [ ] `/up` returns 200 with expected JSON body
- [ ] `/` (landing) returns 200
- [ ] `/help` returns 200
- [ ] `/aup` returns 200
- [ ] `/login` returns 200
- [ ] `/register` returns 200
- [ ] Authenticated dashboard loads (any role)
- [ ] Leaderboard page loads
- [ ] Catalog browse loads
- [ ] Backup command runs successfully via Artisan call

#### 5.2d Requests (+2 tests) — `tests/Feature/Requests/RouteRequestTest.php`
- [ ] RouteRequest with real fulfillment data from seeded offers
- [ ] Cross-post request to programs teaching the same subject

**Verification:** `php vendor/bin/pest` after each batch. Target: 220 passed.

---

### 5.3 Real PDF Watermarking (🟡 3 h)

**What:** Replace the SVG thumbnail stub in `WatermarkResourceFile.php` with actual GD/Imagick rasterization and per-user text overlay.

**File:** `app/Domain/Catalog/Jobs/WatermarkResourceFile.php`

**Current state:** Generates SVG mockups with book icon + title + page count. Saves as `.svg`.

**Target state:**
1. Use `Imagick` (if installed) or GD fallback to render the first page of the PDF as a 400px-wide PNG.
2. Overlay semi-transparent text: `"Downloaded by {display_name} on {date}"`.
3. Save to `thumbs/{resource_id}.png`.
4. Update `thumbnail_url` column.

**Check first:** Is Imagick available?
```powershell
php -r "var_dump(extension_loaded('imagick'));"
```
If false, check `php.ini` for `extension=imagick` or use GD fallback (render PDF first page via Ghostscript CLI `gs` then process with GD).

**Alternative lighter approach (recommended for pilot):**
- Use the `thumbnail_url` field to store a generated PNG via Ghostscript CLI (no PHP extension needed)
- Watermark applied at download-time in the `DownloadResourceFile` action via header injection or appended page

**Ghostscript command for thumbnail:**
```bash
gs -q -dNOPAUSE -dBATCH -sDEVICE=png16m -r150 -dFirstPage=1 -dLastPage=1 \
   -sOutputFile=thumb.png input.pdf
```

---

### 5.4 Feedback Form (🟡 1 h)

**What:** Simple in-app feedback submission. DB table + form + thank-you page.

**Steps:**
1. Create migration: `feedback` table with columns `id`, `user_id`, `body`, `type` (bug/feature/praise), `created_at`.
2. Create `Feedback` model in `app/Models/`.
3. Create `app/Domain/Feedback/Actions/SubmitFeedback.php` — validates body length, stores row.
4. Create Blade view `resources/views/feedback/create.blade.php` — textarea + type select + submit.
5. Add route `POST /feedback` → `FeedbackController@store`.
6. Add nav link in layout (footer or sidebar).
7. Flash message "Thanks! We'll review your feedback."

Test: submit feedback as authenticated user, verify row exists.

---

### 5.5 README.md Update (🟡 30 min)

**What:** README still says "Week 1 — Project skeleton." Update to reflect 12 weeks of delivered features.

**Sections to update:**
1. **Status badge** → Remove "Week 1" line, add full feature list
2. **Features** → bullets: per-program chat, resource catalog, request board + routing, karma/badges, lend tracking, moderation dashboard
3. **Tech stack** → Laravel 11, Livewire 3, Reverb, Alpine.js, Tailwind, MySQL, Redis
4. **Installation** → keep Docker + standard install, verify both paths still work
5. **Testing** → explain Pest + SQLite, list test domains
6. **Architecture** → link to `docs/02-architecture.md`
7. **Roadmap** → link to `docs/05-roadmap.md`
8. **Contributing** → PR guidelines, CI pipeline
9. **License** → TBD (keep placeholder)

**Key file:** `README.md` (root). Current content at lines 44–49 says "Week 1" — replace entire status section.

---

### 5.6 Demo Deck / Screen Recording (🔴 6 h)
> **Skill:** Load `python-pptx-generator` for the PowerPoint script.

**Deliverables:** `.pptx` file + 3-minute `.mp4` screen recording.

**Deck structure (10–12 slides):**
1. Title slide — StudHub, SEAIT, your name
2. Problem statement — cross-program resource discovery gap
3. Solution overview — catalog + chat + request board
4. Architecture diagram — reuse `docs/diagrams/routing-sequence.md`
5. Key feature #1 — Per-program chat with @mentions (show UI screenshot)
6. Key feature #2 — Resource catalog with search + shelves (show UI)
7. Key feature #3 — Request routing engine with weighted scoring (show diagram)
8. Key innovation — `historicalFulfillmentRate()` making cross-program matching real
9. Moderation & reputation — reports, karma, badges, audit log
10. Pilot results — user count, resources uploaded, cross-program fulfillments
11. Lessons learned — what worked, what would change
12. Q&A / Thank you

**Screen recording:** 3 minutes max. Demo flow:
- 0:00–0:30 — Landing page, register (speed up), onboard
- 0:30–1:00 — Browse catalog, search for a subject
- 1:00–1:30 — Post a request, show routing result
- 1:30–2:00 — Chat message with @mention across programs
- 2:00–2:30 — Leaderboard + badge showcase
- 2:30–3:00 — Moderation dashboard (admin view)

**Tool:** OBS Studio, Screen Recorder, or Loom for the capture.

---

### 5.7 Paper Draft (🔴 4 h)

**Template sections (standard panel format):**

| Section | Content | Source |
|---------|---------|--------|
| **Title Page** | StudHub: Cross-Program Academic Resource Exchange for SEAIT | — |
| **Abstract** | 250-word summary: problem, approach, key innovation (routing engine), results | — |
| **1. Introduction** | SEAIT context, resource discovery problem, why per-program matters | `docs/07-seait-context.md` |
| **2. Related Work** | Existing solutions (Google Classroom, LMS forums), why StudHub is different | `docs/00-product-overview.md` |
| **3. System Design** | Architecture overview, domain model, technology choices | `docs/02-architecture.md` + `docs/03-database-schema.md` |
| **4. Core Features** | Walk through catalog, chat, request board, routing engine, moderation | `docs/01-mvp-spec.md` |
| **5. Routing Engine** | Weighted scoring formula, historicalFulfillmentRate, cross-post algorithm (key innovation) | `docs/04-request-routing.md` |
| **6. Pilot Results** | User metrics, resource counts, cross-program fulfillments, feedback | Data from pilot run (placeholder until pilot completes) |
| **7. Conclusion & Future Work** | What was achieved, DMs, versioned resources, mobile app | `docs/05-roadmap.md` §Post-MVP |
| **References** | Laravel docs, Reverb, Livewire, PHP, MySQL | Standard format |

**Success criteria for §5** (Routing Engine) from `docs/00-product-overview.md`:
- ≥ 1 cross-program request fulfillment during pilot
- Routing correctly scores program candidates with real historical data
- Panel can trace the score calculation step-by-step

---

### 5.8 End-to-End Smoke Test (🟡 2 h)

**What:** A Pest HTTP test that simulates a complete user journey.

**Test file:** `tests/Feature/SmokeTest.php`

**Journey:**
```
1. User registers → verifies email → onboards
2. User creates a resource (upload PDF)
3. User browses catalog → finds the resource
4. User posts a resource request → request is routed
5. User sends a chat message → message appears
6. User reports a message → report appears in moderation queue
7. Moderator resolves the report → message is hidden
8. Admin views analytics dashboard
```

Use `User::factory()->onboarded()->create()` + `actingAs()` throughout.
Run with SQLite in-memory (no MySQL required).

---

## 6. Effort Summary & Suggested Order

| # | Task | Est. | Priority | Depends on |
|---|------|------|----------|------------|
| 5.1 | F17: Report global scope | 20 min | 🟡 Medium | None |
| 5.2 | Expand tests to 220+ | 3–4 h | 🔴 High | None |
| 5.5 | README.md update | 30 min | 🟡 Medium | None |
| 5.8 | E2E smoke test | 2 h | 🔴 High | None |
| 5.4 | Feedback form | 1 h | 🟡 Medium | None |
| 5.3 | Real watermarking | 3 h | 🟡 Medium | Ghostscript/Imagick check |
| 5.7 | Paper draft | 4 h | 🔴 High | Pilot data (provisional ok) |
| 5.6 | Demo deck + recording | 6 h | 🔴 High | Paper draft (content reuse) |
| — | **Total** | **~20 h** | | |

**Suggested work order:**
1. **F17** (quick win, closes last audit finding)
2. **Tests → 220** (highest Go/No-Go blocker)
3. **README** (documentation visible to panel)
4. **Smoke test** + **Feedback form** (parallel, independent)
5. **Watermarking** (technical, not blocked)
6. **Paper** (can start with provisional data)
7. **Deck + recording** (last, reuses paper content)

---

## 7. Skills the Next Agent Should Load

| Skill | Why |
|-------|-----|
| `squirrel` | Full-cycle workflow: build → test → lint → fix → docs. Auto-detects project state |
| `bug-hunter` | Debugging any test failures during expansion from 186 → 220 |
| `brooks-lint` | Code review after adding F17 scope, feedback form, smoke tests |
| `codebase-audit-pre-push` | Pre-push audit before final commit |
| `python-pptx-generator` | Generate the demo deck PowerPoint programmatically |

---

## 8. Quick-Start Commands

```powershell
# MUST run from the project root: C:\Users\ADMIN\OneDrive\Desktop\Another_Project

# Verify everything is green
cd C:\Users\ADMIN\OneDrive\Desktop\Another_Project
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pest
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/phpstan analyse --no-progress --memory-limit=1G
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pint --test

# Filtered test runs
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pest --filter="Moderation"
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pest --filter="Chat"
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pest --filter="Smoke"

# Count tests only (fast)
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pest --compact

# Dev server
$env:Path = "C:\xampp\php;$env:Path"; php artisan serve
```

---

## 9. Starting Point for the Next Agent

```
1. Read this file: planning/session-handoff-2026-05-20.md
2. Read the audit (for F17 context): planning/audit-final-2026-05-18.md
3. Read the roadmap (for Week 12 section): docs/05-roadmap.md
4. Load the `squirrel` skill for the build/test/lint/fix cycle

Priority order:
  1. F17 global scope (20 min, single file)
  2. Expand tests to 220+ (3–4 h, multiple test files)
  3. README update (30 min)
  4. E2E smoke test + feedback form (3 h)
  5. Real watermarking (3 h)
  6. Paper draft + demo deck + recording (10 h)

CI gates: Pint → PHPStan L6 → Pest (all must be green at every commit)
```

---

*Handoff prepared 2026-05-20. Source audit: `planning/audit-final-2026-05-18.md`. Roadmap: `docs/05-roadmap.md`.*
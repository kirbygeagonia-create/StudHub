# StudHub — New Session Continuity Handoff

**Date:** 2026-05-20
**Last commit:** `2fe8413` on `origin/main`
**Project:** StudHub (SEAIT cross-program academic resource exchange)
**Repo:** `https://github.com/kirbygeagonia-create/StudHub.git`

---

## 1. Quick-Start (Copy-Paste to New Session)

```
Read: planning/session-handoff-2026-05-20.md
Read: planning/studhub-analysis.md
Read: docs/05-roadmap.md
Read: AGENTS.md
```

---

## 2. Verification Commands

```powershell
# Set PHP path first
$env:Path = "C:\xampp\php;$env:Path"

# Verify everything green (should all pass)
php vendor/bin/pint --test
php vendor/bin/phpstan analyse --no-progress --memory-limit=1G
php vendor/bin/pest --compact

# Expected output:
# Pint:  PASSED
# PHPStan: [OK] No errors (Level 6)
# Pest: 1 skipped, 221 passed (526 assertions)
```

---

## 3. What Is Complete (DO NOT RE-DO)

| Feature | Status | File |
|---------|--------|------|
| F17: Report global school scope | ✅ | `app/Models/Scopes/ReportSchoolScope.php` |
| Real PDF watermarking (setasign/fpdi + setasign/fpdf) | ✅ | `app/Domain/Catalog/Actions/DownloadResourceFile.php` |
| `last_seen_at` middleware (60s throttle) | ✅ | `app/Http/Middleware/UpdateLastSeenAt.php` |
| Empty states on catalog, request board, chat index | ✅ | `resources/views/resources/index.blade.php` + others |
| Program badge on resource cards | ✅ | `resources/views/resources/index.blade.php` |
| PWA manifest + SVG favicon | ✅ | `public/manifest.json` + `public/favicon.svg` |
| MIME re-verification on download | ✅ | `app/Domain/Catalog/Actions/DownloadResourceFile.php` |
| Rich dashboard (stats + quick actions) | ✅ | `resources/views/dashboard.blade.php` |
| Feedback form (model, migration, action, controller, view) | ✅ | `app/Domain/Feedback/` + `app/Models/Feedback.php` |
| ChatMessage SoftDeletes | ✅ | Already existed in model |
| Chat dev text replaced with proper empty state | ✅ | `resources/views/chat/index.blade.php` |
| README.md updated to Week 12 | ✅ | `README.md` |
| All 69 routes verified (no broken references) | ✅ | `routes/web.php` |
| PDF thumbnail SVG upgraded (polished design) | ✅ | `app/Domain/Catalog/Jobs/WatermarkResourceFile.php` |
| Routes: 69 named routes, 91 view references, all resolve | ✅ | — |
| Environment: SQLite dev setup complete | ✅ | `.env` configured |

---

## 4. What Needs Implementation (v1.1 Backlog — 16 Items)

### Implementation Order (Suggested)

### Batch 1 — Quick wins (~3h)
| # | Item | Files to touch | Notes |
|---|------|---------------|-------|
| 1 | **Human validation messages** | `lang/en/validation.php` (create) | Override `subject_id.required` → "Please select a subject." etc. |
| 2 | **"Why was I notified?" in routed notifications** | `app/Domain/Requests/Jobs/NotifyRoutedUsers.php` or notification view | Add one sentence: "You're receiving this because you may have taken [subject]." |
| 3 | **Audit log download events** | `app/Domain/Catalog/Actions/DownloadResourceFile.php` | Log "User X downloaded Resource Y" using `LogAudit` action |
| 4 | **Request expiry warning** | `resources/views/requests/show.blade.php` | Show "Expires in X days" if `needed_by` is within 3 days |
| 5 | **Student number column** | New migration + `app/Models/User.php` (`$fillable`) | Nullable `student_number` varchar(20) |

### Batch 2 — Features (~6h)
| # | Item | Files to touch | Notes |
|---|------|---------------|-------|
| 6 | **Subject autocomplete** | `resources/views/requests/create.blade.php` + `resources/views/livewire/resources/form.blade.php` | Livewire `wire:model.live` + search subjects/aliases |
| 7 | **"I found it elsewhere" request close** | New route `POST /requests/{request}/close-external` + `RequestController` | Closes request without offer/accept cycle |
| 8 | **Resource "helpful" rating** | New migration + thumbs-up button on `resources/show.blade.php` | AJAX POST → increments counter on `program_subjects` eventually |
| 9 | **Dark mode** | `tailwind.config.js` (`darkMode: 'class'`) + `resources/views/layouts/app.blade.php` | Toggle in nav dropdown. Use Tailwind `dark:` variants. |
| 10 | **Lend escalation path** | New migration `lend_reminder_count` + `resources/views/components/lend-row.blade.php` | "Mark as returned" button for lender, escalate if 3+ reminders |

### Batch 3 — Dashboard / Analytics (~4h)
| # | Item | Files to touch | Notes |
|---|------|---------------|-------|
| 11 | **Colleges-table admin analytics** | `app/Http/Controllers/AdminController.php`, `resources/views/admin/dashboard.blade.php` | Group stats by college: CICT / DCE / CBGG / CTE / CAF / CCJE |
| 12 | **Cross-program flow admin widget** | Same as above | "BSIT → BSCE: 12 resources this week" query |
| 13 | **Routing weight recalculation job** | New `app/Console/Commands/RecalculateRoutingWeights.php` + `routes/console.php` schedule | Weekly job that updates `program_subjects.weight` from real fulfillment data |
| 14 | **Interactive onboarding modal** | `resources/views/dashboard.blade.php` | Alpine.js modal on first login showing 3 steps |

### Batch 4 — UX polish (~4h)
| # | Item | Files to touch | Notes |
|---|------|---------------|-------|
| 15 | **Notification preferences UI** | New route `GET/POST /notification-preferences` + `ProfileController` method + view | Checkbox: "Only urgent requests", "Mute from [program]" |
| 16 | **PWA install prompt** | `public/sw.js` (new) + `resources/views/layouts/app.blade.php` | Service worker + `beforeinstallprompt` handler, bottom banner "Install StudHub" |

---

## 5. Key Files Reference

| File | Purpose |
|------|---------|
| `app/Domain/Catalog/Actions/DownloadResourceFile.php` | PDF watermarking + MIME verification |
| `app/Domain/Catalog/Jobs/WatermarkResourceFile.php` | Thumbnail generation (SVG) |
| `app/Models/Report.php` | Has `ReportSchoolScope` global scope |
| `app/Models/Scopes/ReportSchoolScope.php` | Filters reports by `reporter.school_id` |
| `app/Http/Middleware/UpdateLastSeenAt.php` | Updates `users.last_seen_at` every 60s |
| `app/Domain/Feedback/Actions/SubmitFeedback.php` | Feedback submission action |
| `app/Http/Controllers/FeedbackController.php` | Feedback create + store |
| `bootstrap/app.php` | Middleware aliases + `UpdateLastSeenAt` appended to web |
| `resources/views/dashboard.blade.php` | Rich dashboard with stats + quick actions |
| `resources/views/requests/show.blade.php` | "Record as Lend" form present |
| `resources/views/livewire/chat/room-conversation.blade.php` | Report button on each message |
| `resources/views/profile/public.blade.php` | Report button on user profiles |
| `resources/views/resources/show.blade.php` | Download + report buttons |
| `routes/web.php` | 69 routes, all verified |
| `routes/channels.php` | Suspended users blocked from WebSocket rooms |
| `routes/console.php` | Schedule: backup (02:00), expire-requests (03:00), reminders (09:00), digest (07:00) |
| `database/migrations/2026_05_20_000000_create_feedback_table.php` | Feedback table |
| `tests/Feature/Moderation/ModerationTest.php` | 27 moderation tests (most expanded) |
| `tests/Feature/SmokeTest.php` | 12 smoke tests including health, login, pages |

---

## 6. Environment for New Session

```
PHP: 8.2.12 at C:\xampp\php\php.exe
Composer: C:\xampp\php\composer
Node: v24.13.0 / npm 11.14.1
Database: SQLite (database/database.sqlite)
Test DB: SQLite in-memory

Commands:
  Tests:    $env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pest
  PHPStan:  $env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/phpstan analyse --no-progress --memory-limit=1G
  Pint:     $env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pint --test
  Dev:      $env:Path = "C:\xampp\php;$env:Path"; php artisan serve
  Build:    npm run build

Composer install (if needed):
  $env:Path = "C:\xampp\php;$env:Path"; & "C:\xampp\php\php.exe" "C:\xampp\php\composer" install --no-interaction

Login credentials for local dev:
  Student:   test@seait.edu.ph    / password
  Moderator: mod@seait.edu.ph     / password
  Admin:     admin@seait.edu.ph   / password
```

---

## 7. CI Pipeline (Run After Every Batch)

```powershell
# Full CI — must all pass
$env:Path = "C:\xampp\php;$env:Path"
php vendor/bin/pint --test
php vendor/bin/phpstan analyse --no-progress --memory-limit=1G
php vendor/bin/pest --compact

# Target after v1.1: 250+ tests (currently 221)
```

---

## 8. Git After Each Batch

```powershell
git add -A
git commit -m "feat: v1.1 batch N — [description]"
git push
```

---

## 9. System Architecture Recap

```
app/
  Domain/
    Catalog/     → Resources, shelves, download, watermarking
    Chat/        → Messages, rooms, mentions, broadcasting
    Identity/    → Users, roles, email domain validation
    Lends/       → Lend tracking, return reminders
    Moderation/  → Reports, audit log, suspension
    Reputation/  → Karma events, badge tiers
    Requests/    → Request board, routing engine, offers
    Search/      → Global search, daily digest
    Feedback/    → In-app feedback submission (NEW)
  Http/
    Controllers/ → 11 main + 9 auth controllers
    Middleware/   → EnsureUserIsOnboarded, EnsureHasRole, EnsureNotSuspended, UpdateLastSeenAt
  Models/
    → 22 Eloquent models including Report, Feedback, ResourceRequest (renamed from Request)
    → ReportSchoolScope applies via booted()

Tests: 221 Pest tests, organized by domain (Feature/{Domain}/)
```

---

*Ready for new session. Send this entire file as context to the next agent.*
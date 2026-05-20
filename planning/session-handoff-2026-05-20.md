# Session Handoff — Week 11 Complete → Week 12 Ready

**Date:** 2026-05-20
**Project:** StudHub (SEAIT cross-program resource exchange)

---

## Current State

### Tests: 186 passed, 459 assertions, 1 skipped
### PHPStan: Level 6 — 0 errors
### Pint: Passes

---

## Week 11 — Pilot Prep & Hardening ✅ COMPLETE

### Security & Rate Limiting
- 20 POST routes throttled (requests: 10/min, resources: 30/min, reports: 10/min, etc.)
- MIME allow-list enforced at both Livewire `ResourceForm` validation AND `CreateResource` action
- Suspended users blocked from HTTP routes AND WebSocket channels

### Operations
- `studhub:backup-database` command with 7-day rotation (scheduled daily at 02:00)
- `studhub:expire-requests` command (scheduled daily at 03:00)
- `/up` healthcheck route configured in `bootstrap/app.php`
- `config/filesystems.php` has `backups` disk configured

### User-Facing
- Landing page (`welcome.blade.php`) — custom StudHub branding, not Laravel default
- `/help` page with usage guide
- `/aup` page with Acceptable Use Policy
- Report buttons on: chat messages, user profiles, resources

### Documentation
- `docs/diagrams/er-diagram.md` — full entity relationship diagram
- `docs/diagrams/routing-sequence.md` — routing engine sequence diagram with weights
- `AGENTS.md` updated to Week 11 state

### Refactoring
- `App\Models\Request` renamed to `ResourceRequest` (25+ files)
- Old `Request.php` kept as backward-compat alias: `class Request extends ResourceRequest {}`
- All imports, type-hints, PHPDocs, factories updated
- Factory resolution via `newFactory()` override

---

## Pre-Pilot Go/No-Go Checklist Status

| Category | Item | Status |
|----------|------|--------|
| Code | All 17 audit findings (applicable 15/17) | ✅ |
| Code | All 6 pending UX items | ✅ |
| Code | PHPStan level 6 green | ✅ |
| Code | 220+ Pest tests (currently 186) | ❌ Need ~34 more |
| Security | Rate limits on all POST routes | ✅ |
| Security | File MIME allow-list | ✅ |
| Security | Real per-user PDF watermarking | 🟡 Stub (SVG only) |
| Security | Suspended user can't post/receive chat | ✅ |
| Operations | Daily DB backup | ✅ |
| Operations | `/up` healthcheck | ✅ |
| Operations | End-to-end smoke test | ❌ Not built |
| User-facing | Landing page | ✅ |
| User-facing | `/help` page | ✅ |
| User-facing | AUP + feedback form | 🟡 No feedback form |
| Docs | ER diagram + routing diagram | ✅ |
| Docs | Audit document committed | ✅ |

---

## Week 12 — What's Needed

### Core Tasks (see `docs/05-roadmap.md` and `planning/audit-final-2026-05-18.md`)

1. **F17: Report global scope** — Add `App\Models\Scopes\ReportSchoolScope` filtering reports by `school_id` through a joined user query. 20 min.
2. **Expand test suite to 220+** — Currently at 186 (459 assertions). Need ~34 more tests. Focus areas:
   - More moderation edge cases (program-filtered dashboard, report on self already has guard)
   - Chat edge cases (suspended user channel rejection, attachment validation)
   - Request routing with real fulfillment data
   - Smoke tests for `/up` health endpoint, backup command
3. **Real per-user PDF watermarking** — Replace SVG thumbnail stub with Imagick/Ghostscript rendering. ~3h
4. **Feedback form** — Simple in-app feedback submission (POST route + DB table or email). ~1h
5. **README.md update** — Currently stale (says "Week 1"). Update to reflect full project status. ~30 min
6. **Demo deck / 3-min screen recording** — PowerPoint + screen capture. ~6h
7. **Paper draft** — Follow panel template sections. ~4h
8. **End-to-end smoke test** — Pest browser test or manual script. ~2h

### Key files for Week 12

| File | Purpose |
|------|---------|
| `app/Models/Report.php` | Add global scope (F17) |
| `app/Domain/Catalog/Jobs/WatermarkResourceFile.php` | Upgrade to real watermarking |
| `tests/Feature/Moderation/ModerationTest.php` | Add program-filtered dashboard test |
| `tests/Feature/SmokeTest.php` | Add `/up` health endpoint test |
| `README.md` | Full project documentation |
| `docs/00-product-overview.md` §5 | Success criteria for paper |

---

## Environment
- PHP 8.2.12 at `C:\xampp\php\php.exe`
- Laravel 11, Livewire, Reverb, SQLite in-memory
- Tests: `$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pest`
- PHPStan: `$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/phpstan analyse --no-progress --memory-limit=1G`
- Format: `$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pint`

## For the Next Session
```
Please read planning/session-handoff-2026-05-20.md first.
Complete Week 12: expand tests to 220+, add Report global scope (F17),
upgrade PDF watermarking, update README, prepare demo materials.
```
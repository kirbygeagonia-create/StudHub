# Session Handoff — Week 7 Complete

**Date:** 2026-05-16
**Project:** StudHub (SEAIT cross-program resource exchange)
**Last commit:** `1bdb50b` — pushed to `origin/main`

---

## Current State

### Weeks Completed

| Week | Status | Tests |
|------|--------|-------|
| 0 | ✅ Planning docs approved | — |
| 1 | ✅ Laravel skeleton, CI, Docker, PHPStan, Pint, Pest | — |
| 2 | ✅ Auth, onboarding, school/program/college seeding | — |
| 3 | ✅ Real-time chat with Livewire + Reverb, mentions | — |
| 4 | ✅ Resource catalog, search, subject graph, resource form | — |
| 5 | ✅ Shelves, resource detail, watermarking, thumbnails | 99 |
| **6** | **✅ Request board + routing engine** | +27 |
| **7** | **✅ Karma, badges, reputation, leaderboard** | +11 |

### Test Results
```
Tests:    137 passed (333 assertions)
Duration: 26.21s
```

---

## Week 6 — Request Board + Routing Engine

### New Files
| File | Purpose |
|------|---------|
| `database/migrations/2025_06_01_000001_create_requests_table.php` | requests, request_routes, offers, lends tables |
| `app/Domain/Requests/Enums/RequestUrgency.php` | low/normal/urgent |
| `app/Domain/Requests/Enums/RequestStatus.php` | open/matched/fulfilled/expired/withdrawn |
| `app/Domain/Requests/Enums/OfferStatus.php` | pending/accepted/rejected/withdrawn |
| `app/Models/Request.php` | Request model (table `requests`) |
| `app/Models/RequestRoute.php` | RequestRoute model |
| `app/Models/Offer.php` | Offer model |
| `app/Models/Lend.php` | Lend model |
| `app/Domain/Requests/Actions/CreateRequest.php` | Validate + create, 5-open max, 10-min cooldown |
| `app/Domain/Requests/Actions/RouteRequest.php` | **Core routing algorithm** — 267 lines |
| `app/Domain/Requests/Actions/CreateOffer.php` | Offer validation + creation |
| `app/Domain/Requests/Actions/AcceptOffer.php` | Accept offer + reject others + award karma |
| `app/Domain/Requests/Jobs/NotifyRoutedUsers.php` | Queued notification fan-out |
| `app/Domain/Requests/Jobs/CrossPostRequest.php` | Cross-post system message to program chat for urgent |
| `app/Http/Controllers/RequestController.php` | index, create, store, show, storeOffer, acceptOffer |
| `resources/views/requests/index.blade.php` | Request board with filters |
| `resources/views/requests/create.blade.php` | Request creation form |
| `resources/views/requests/show.blade.php` | Detail page with offers + accept button |
| `database/factories/RequestFactory.php` | Test factory |
| `tests/Feature/Requests/CreateRequestTest.php` | 8 tests |
| `tests/Feature/Requests/RouteRequestTest.php` | 8 tests (scoring, thresholds, users, fallback, caps) |
| `tests/Feature/Requests/OfferTest.php` | 6 tests |
| `tests/Feature/Requests/AcceptOfferTest.php` | 6 tests |

### Routing Engine (RouteRequest.php) Features
- 6 weighted scoring components: w_edge 0.40, w_resource 0.25, w_history 0.20, w_proximity 0.10, w_urgency 0.05, penalty_self 0.05
- Program threshold 0.35, chat threshold 0.65
- Max 8 users per program, global cap 25
- year_level >= typical_year_level filter
- -0.5 anti-spam penalty for 24h notifications
- 3 notifications/day cap per user
- Subject-not-in-curriculum fallback (routes to requester's own program)
- CrossPostRequest job for urgent requests above CHAT_THRESHOLD
- NotifyRoutedUsers job for database notification fan-out

---

## Week 7 — Karma, Badges, Reputation

### New Files
| File | Purpose |
|------|---------|
| `database/migrations/2025_07_01_000001_create_karma_events_table.php` | karma_events table + is_helpful on chat_messages |
| `app/Domain/Reputation/Enums/KarmaEventReason.php` | 5 reasons with deltas: +5 upload, +5 save, +10 fulfill, +2 helpful, -5 report |
| `app/Domain/Reputation/Enums/BadgeTier.php` | Bronze 25, Silver 75, Gold 150 |
| `app/Models/KarmaEvent.php` | Append-only ledger model |
| `app/Domain/Reputation/Actions/AwardKarma.php` | Creates event + recalculates user.karma from SUM |
| `resources/views/profile/leaderboard.blade.php` | Top 20 sharers per program, program switcher |
| `tests/Feature/Reputation/KarmaTest.php` | 11 tests |

### Modified Files
| File | Change |
|------|--------|
| `app/Domain/Catalog/Actions/CreateResource.php` | +5 karma to uploader on resource creation |
| `app/Domain/Catalog/Actions/ToggleShelfItem.php` | +5 karma to resource owner when save_count >= 1 |
| `app/Domain/Requests/Actions/AcceptOffer.php` | +10 karma to offerer on fulfillment |
| `app/Http/Controllers/ProfileController.php` | Added leaderboard() + updated show() with badge |
| `resources/views/profile/show.blade.php` | Redesigned with karma score, badge tier, resource count |
| `routes/web.php` | Added GET /leaderboard route |

### Karma Triggers
| Trigger | Delta | Action |
|---------|-------|--------|
| Resource uploaded | +5 | CreateResource |
| Resource gets >=1 save | +5 | ToggleShelfItem (attach path) |
| Request fulfilled (offer accepted) | +10 | AcceptOffer |
| Chat marked helpful | +2 | Not yet wired (needs UI) |
| Report confirmed (spam/abuse) | -5 | Pending Week 9 |

### Badge Tiers
- **Bronze** — 25 karma
- **Silver** — 75 karma  
- **Gold** — 150 karma
- Below 25: no badge (null)

---

## Environment

- PHP 8.2.12 at `C:\xampp\php\php.exe`
- Composer installed at `C:\xampp\php\composer`
- composer.json relaxed to `^8.2` for local compat
- To run tests: `$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pest`
- SQLite in-memory (CI mode), MySQL for production
- `.env` configured for MySQL but MySQL not running locally — tests use SQLite
- `.gitignore` excludes `composer-setup.php`

---

## Project Skills (global)

Available from `C:/Users/ADMIN/.agents/skills/`:

| Skill | Relevance |
|-------|-----------|
| `api-endpoint-builder` | **Week 6+ routes**, validation, auth patterns |
| `bug-hunter` | Debugging test failures or edge cases |
| `brooks-lint` | Post-build code review |
| `codebase-audit-pre-push` | Pre-push audit |
| `testing-studhub-chat` | **Week 3 chat testing** (local in `.agents/skills/`) |
| `squirrel` | Full-cycle build pipeline |
| `technical-change-tracker` | Change tracking |

---

## Next: Week 8 — Lend Tracking + Return Reminders 🟡

Per `docs/05-roadmap.md` §Week 8:

- `lends` migration (already done in Week 6 migration!)
- Mark a fulfilled request as a "lend" with `return_by` date
- Daily scheduled job: email reminder 2 days before return
- "My lends" page for both sides

**Exit:** I can record a lend and get a return reminder.

### Reference Materials
- `docs/05-roadmap.md` §Week 8
- `docs/01-mvp-spec.md` §1.5 (karma for fulfillment is done, lends tracking next)
- `docs/03-database-schema.md` §2.15 — lends table schema (already migrated)
- `app/Models/Lend.php` — already created
- Reuse: `app/Domain/Requests/Actions/AcceptOffer.php` — can extend to create a lend record

---

## For the Next Session — /init command

```
Please read planning/session-handoff-2026-05-16.md first. Then load
the most relevant skills:

- api-endpoint-builder — for Week 8 route/controller work
- bug-hunter — if any test failures arise
- brooks-lint — code review after building

Week 8 tasks: Lend tracking + return reminders. The `lends`
table migration already exists. Build the CreateLend action (on offer
acceptance for physical items), a daily scheduled job for return
reminders (email 2 days before return_by), and "My Lends" page.
Tests: 137 passed (333 assertions) — maintain this green bar.
```
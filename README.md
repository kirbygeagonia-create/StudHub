# StudHub

> A school-wide chat + cross-program resource exchange, built for
> **SEAIT** (South East Asian Institute of Technology, Tupi, South
> Cotabato).
>
> Each SEAIT degree program gets its own group chat, and a smart
> resource layer lets students discover, request, and lend textbooks,
> e-modules, reviewers, and past exams **across programs and across
> colleges** — not just inside their own bubble.

This repository now contains the **Laravel 11 application** for StudHub.
Weeks 0–12 are complete with full CI. See
[`docs/05-roadmap.md`](docs/05-roadmap.md) for the week-by-week
build plan.

---

## What problem does StudHub solve?

Today at SEAIT, when a 2nd-year BSCE student needs the *Mathematics in
the Modern World* reviewer that a 3rd-year BSIT student already has,
they:

1. Post in a random Facebook group and hope.
2. Scroll Messenger chat history.
3. Or just give up and re-make the reviewer from scratch.

There is no per-school directory of *who has what*, no easy way to find a
senior in another program who already took the same subject, and no way for
the institution to see what learning resources are actually in demand.

StudHub fixes this by combining three things:

1. **Per-program group chats** for everyday coordination.
2. **A structured resource catalog** (textbooks, e-modules, reviewers,
   past exams, lab manuals, thesis samples) tagged by **subject**, not by
   program.
3. **A request board** where a request from one program is **auto-routed**
   to the other programs that historically took the same subject.

---

## Status

**Week 12 — Pilot launch ready.** 221 Pest tests passing, PHPStan level 6 clean.
All core features delivered: per-program chat, resource catalog, request board with
cross-program auto-routing, karma/badges, lend tracking, moderation dashboard.
See [`planning/session-handoff-2026-05-20.md`](planning/session-handoff-2026-05-20.md)
for the latest state.

---

## Quickstart (dev)

### Without Docker (host PHP 8.2 + Composer + SQLite for dev)

```bash
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

### With Docker

```bash
cp .env.example .env
make up                       # boots app + nginx + mysql + redis + mailpit
make sh                       # shell into the php container
# inside the container:
composer install
php artisan key:generate
php artisan migrate
exit
# back on the host:
open http://localhost:8080    # Laravel welcome page
open http://localhost:8025    # Mailpit web UI
```

## Dev commands

| Command | What it does |
| --- | --- |
| `composer lint` | Apply Pint code style fixes |
| `composer lint:check` | Check style without writing (used in CI) |
| `composer analyse` | Run PHPStan Level 6 |
| `composer test` | Run 221 Pest tests (SQLite in-memory) |
| `composer ci` | Lint check + analyse + test (full local CI) |
| `make ci` | Same as `composer ci` |

**Windows quick commands (PowerShell):**
```powershell
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pest
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/phpstan analyse --no-progress --memory-limit=1G
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pint --test
```

---

## Features (all delivered)

| Feature | Domain | Status |
|---------|--------|--------|
| School-restricted email sign-up | Identity | ✅ |
| Onboarding (program, year level, display name) | Identity | ✅ |
| 3 user roles (Student, Moderator, Admin) | Identity | ✅ |
| Per-program + per-year chat rooms | Chat | ✅ |
| Real-time messaging via Reverb WebSockets | Chat | ✅ |
| @display_name mentions with notifications | Chat | ✅ |
| 25 MB file attachments (MIME-validated) | Chat | ✅ |
| Subject-tagged resource catalog | Catalog | ✅ |
| Full-text search across resources | Catalog | ✅ |
| Personal shelves (save/bookmark resources) | Catalog | ✅ |
| Request board with auto-routing engine | Requests | ✅ |
| Weighted scoring: curriculum match + resources + fulfillment history | Requests | ✅ |
| Offer/accept flow for matched requests | Requests | ✅ |
| Karma system (5 events, atomic increment) | Reputation | ✅ |
| Badge tiers (Bronze 25 / Silver 75 / Gold 150) | Reputation | ✅ |
| Leaderboard by program | Reputation | ✅ |
| Lend tracking + return reminders | Lends | ✅ |
| Report system (messages, resources, users) | Moderation | ✅ |
| Moderator program dashboard with SQL filtering | Moderation | ✅ |
| Admin dashboard (signups, DAU, activity metrics) | Moderation | ✅ |
| User suspension (blocked from HTTP + WebSockets) | Moderation | ✅ |
| Audit log for all moderation actions | Moderation | ✅ |
| Self-report prevention guard | Moderation | ✅ |
| Report school-scope filtering (F17) | Moderation | ✅ |
| Rate limiting on 20 POST routes | Security | ✅ |
| MIME allow-list on uploads | Security | ✅ |
| Daily DB backup + request expiration jobs | Operations | ✅ |
| Landing page, Help page, AUP page | UX | ✅ |

## Planning docs (still relevant)

| Doc | Purpose |
| --- | --- |
| [docs/00-product-overview.md](docs/00-product-overview.md) | Vision, problem, users, success criteria |
| [docs/01-mvp-spec.md](docs/01-mvp-spec.md) | MVP features and user stories |
| [docs/02-architecture.md](docs/02-architecture.md) | Tech stack and system design |
| [docs/03-database-schema.md](docs/03-database-schema.md) | Tables, relationships, key fields |
| [docs/04-request-routing.md](docs/04-request-routing.md) | Cross-program auto-routing algorithm |
| [docs/05-roadmap.md](docs/05-roadmap.md) | Week-by-week build plan |
| [docs/06-glossary.md](docs/06-glossary.md) | Shared vocabulary |
| [docs/07-seait-context.md](docs/07-seait-context.md) | SEAIT-specific facts: colleges, programs, auth strategy, pilot plan |
| [planning/checklist.md](planning/checklist.md) | Working checklist for the team |
| [planning/risks.md](planning/risks.md) | Known risks and mitigations |

---

## Project at a glance

- **Stack:** PHP 8.2, Laravel 11, Laravel Reverb (real-time), MySQL 8 / SQLite (tests),
  Redis 7, Livewire 3 + Alpine.js + Tailwind.
- **Tests:** 221 Pest tests (526 assertions), PHPStan Level 6, Pint PSR-12.
- **Audience:** SEAIT students across 6 colleges, 26 programs.
- **Pilot:** 3 programs — BSIT, BSCE, BSBA-MM.
- **Primary innovation:** Cross-program resource routing via weighted scoring +
  historical fulfillment rate — *not* "yet another chat app."

---

## Repository layout

```
app/                Laravel application code
  Domain/           Domain modules (Identity, Chat, Catalog, ...)
bootstrap/          Laravel bootstrap
config/             Laravel config
database/           Migrations, factories, seeders
docker/             Dev-stack Dockerfile + nginx config
docs/               Stable design documents (treat as contract)
planning/           Live working artifacts (edit freely)
public/             HTTP entry point
resources/          Blade views, Tailwind sources
routes/             web.php, console.php
tests/              Pest test suite
.github/workflows/  GitHub Actions CI
docker-compose.yml  Local dev stack
Makefile            Common dev commands
```

---

## License

To be decided before v1 ships. Until then, treat this code as **all
rights reserved**.

# 02 — Architecture & Tech Stack

This is a single-school, web-only application. Architecture is deliberately
boring — no microservices, no event sourcing, no premature scaling.

## 1. Tech stack (planned)

| Layer | Choice | Why |
| --- | --- | --- |
| Language | **PHP 8.3** | Requirement of the project; mature, fast enough. |
| Framework | **Laravel 11** | Batteries-included; auth, queues, mail, storage, broadcasting all built-in. |
| Real-time | **Laravel Reverb** | First-party WebSocket server; no Node/Pusher dependency. |
| UI | **Livewire 3 + Alpine.js + Tailwind CSS** | Keeps everything in PHP; reactive UI without an SPA build. |
| DB | **MySQL 8 / MariaDB 11** | Standard, well-supported, easy to host on shared infra. |
| Cache / queue | **Redis** | Pub/sub for Reverb, queues for emails + watermarking. |
| Storage | **S3-compatible (R2, MinIO, or local disk)** | Pluggable; local disk fine in dev. |
| Search | **MySQL FULLTEXT** in v1, **Meilisearch** in v2 if needed | Defer extra infra until proven necessary. |
| Auth | **Laravel Fortify + email verification** | Domain-restricted sign-up. |
| PDF watermarking | **`setasign/fpdi` + `tecnickcom/tcpdf`** | Pure PHP, no shellouts. |
| Testing | **Pest 3** | Cleaner DX than PHPUnit, still runs PHPUnit under the hood. |
| Lint / static analysis | **Laravel Pint**, **PHPStan (level 8)**, **Rector** | Standard Laravel toolchain. |
| CI | **GitHub Actions** | Free for the project's scale. |

## 2. High-level system diagram (text)

```
                        ┌─────────────────────┐
                        │   Browser (PWA)     │
                        │  Livewire + Alpine  │
                        └──────────┬──────────┘
                                   │ HTTPS
                                   │ WebSocket
                                   ▼
                ┌──────────────────────────────────────┐
                │   Laravel app (PHP-FPM behind Nginx) │
                │                                      │
                │  ┌─────────┐  ┌──────────────────┐   │
                │  │ HTTP    │  │ Reverb (WS)      │   │
                │  │ routes  │  │ broadcasting     │   │
                │  └────┬────┘  └────────┬─────────┘   │
                │       │                │             │
                │  ┌────▼──────┐    ┌────▼────────┐    │
                │  │ Services  │    │ Notifications│   │
                │  │ (domain)  │    │  (queued)    │   │
                │  └────┬──────┘    └────┬─────────┘   │
                └───────┼────────────────┼─────────────┘
                        │                │
              ┌─────────▼──────┐   ┌─────▼────────┐
              │   MySQL 8      │   │   Redis      │
              │ (primary DB)   │   │ (queue+pub)  │
              └────────────────┘   └──────────────┘
                        │
              ┌─────────▼──────────┐
              │   Object storage    │
              │   (S3-compatible)   │
              └─────────────────────┘
```

## 3. Module boundaries

We keep code in **Laravel-idiomatic** structure, organized by domain
inside `app/Domain/*`:

```
app/
├── Domain/
│   ├── Identity/        # users, school email verification, programs
│   ├── Chat/            # rooms, messages, presence
│   ├── Catalog/         # resources, subjects, shelves
│   ├── Requests/        # request board, offers, routing engine
│   ├── Reputation/      # karma, badges, reports
│   └── Moderation/      # bans, audit log, content reports
├── Http/
│   ├── Controllers/
│   └── Livewire/
├── Jobs/
└── Notifications/
```

Each domain owns its models, actions (single-purpose classes), and
events. Cross-domain calls go through public actions only — no direct
model imports across domains.

## 4. Real-time strategy

- One Reverb instance behind Nginx.
- Channels:
  - `program.{program_id}` — per-program chat firehose.
  - `program.{program_id}.year.{year}` — per year sub-channel.
  - `user.{user_id}` — per-user notifications.
  - `request.{request_id}` — request thread between requester and
    responders.
- Presence channels for "who's online in your program" (later).

## 5. File uploads & watermarking

1. Client uploads to a signed S3 URL (chunked if > 10 MB).
2. Backend records a `pending` resource row + queues a
   `ProcessResourceUpload` job.
3. Job: virus scan (ClamAV, optional), thumbnail generation (for
   images / PDF first page), record final size + MIME.
4. On **download**, the file is **re-rendered** with a watermark
   (downloader's name + timestamp). Cached for that user for 24h.
5. Original is never served publicly — always behind a signed,
   short-lived URL after auth check.

## 6. Search

- v1: `subjects` and `resources` tables get MySQL `FULLTEXT` indexes
  on `title`, `aliases`, `description`. Manual ranking via
  `MATCH AGAINST` + a few hand-tuned boosts.
- v2 (if needed): mirror to Meilisearch via a queued
  `IndexResource` job. Add typo-tolerance and synonyms there.

## 7. Authentication & authorization

- **Sign-up:** restricted to a configurable email domain list per
  deployment (e.g. `@students.school.edu`).
- **Email verification** required.
- **Roles:** `student`, `moderator`, `admin`. Stored as a single
  `role` column + a `program_moderator` pivot for per-program scope.
- **Policies:** Laravel Gate policies per resource. E.g.
  `ResourcePolicy@view` checks visibility + school membership.

## 8. Privacy & data handling

- All files school-scoped by default. Cross-program visibility is an
  explicit toggle on each resource.
- PII stored: name, school email, program, year, avatar URL, karma.
- No phone numbers, no addresses.
- Audit log for moderation actions (immutable append-only table).
- Soft deletes everywhere; hard delete on request via a documented
  process (compliance with whatever your school's policy is — to be
  confirmed).

## 9. Deployment

- **Dev:** `php artisan serve`, Reverb on `:8080`, Redis + MySQL via
  `docker compose`.
- **Staging:** one VPS (2 vCPU / 4 GB RAM) running Nginx, PHP-FPM,
  Reverb, MySQL, Redis. Cloudflare in front for TLS + caching.
- **Prod:** same shape but with backups (daily MySQL dump → S3) and
  uptime monitoring (UptimeRobot or similar).

## 10. Observability

- Application logs → file → optional shipping to Better Stack /
  Papertrail.
- Slow query log on.
- Lightweight in-app dashboard for admins: DAU, signups by program,
  resources uploaded this week, top requested subjects.

## 11. Performance budgets

- p95 page render < 400 ms on staging hardware.
- p95 chat message round-trip < 250 ms.
- Resource search returning ≤ 50 results < 300 ms.

If we miss these in pilot, we revisit — don't pre-optimize.

## 12. Open questions

These need decisions before code starts; tracked in
[planning/checklist.md](../planning/checklist.md):

1. Single Laravel app vs. separate Reverb container? (Default: single
   app + supervisor.)
2. Sign-in via Google Workspace SSO if the school provides it?
   (Likely yes for v1.5.)
3. Do we need offline reading of e-modules? (Probably v2.)
4. Where does StudHub live in production — school's own infra or
   external VPS? (Compliance question.)

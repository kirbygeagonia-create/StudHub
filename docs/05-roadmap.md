# 05 — Roadmap (Week-by-Week Build Plan)

Assumption: **one developer, ~15 hours per week, 12-week semester.**
Adjust as needed if you have teammates or more hours.

Legend:
- 🟢 must-have for MVP demo
- 🟡 nice to have if time permits
- 🔵 deferred to a post-pilot release

---

## Week 0 — Planning & sign-off (this PR)

- [x] Lock product vision & MVP scope (`00-product-overview.md`,
      `01-mvp-spec.md`).
- [x] Architecture decision (`02-architecture.md`).
- [x] Database schema draft (`03-database-schema.md`).
- [x] Routing algorithm spec (`04-request-routing.md`).
- [x] This roadmap.
- [ ] Stakeholder review (adviser / project panel sign-off).
- [ ] Confirm school will provide list of programs + curriculum subjects.

**Exit criteria:** plan approved by adviser; this PR merged.

---

## Week 1 — Project skeleton 🟢

- Spin up Laravel 11 project, commit to repo.
- Configure Pint, PHPStan (level 5 to start, raise later), Pest.
- GitHub Actions: lint + typecheck + tests on every PR.
- Docker compose for local dev: PHP-FPM, MySQL 8, Redis, Mailpit.
- `make` / `composer` scripts: `setup`, `serve`, `test`, `lint`.
- Empty domain folders under `app/Domain/*`.

**Exit:** `composer test` and `composer lint` both pass on CI on a
hello-world route.

---

## Week 2 — Identity & onboarding 🟢

- `schools`, `programs`, `users` migrations.
- Sign-up restricted by school email domain.
- Email verification (Mailpit in dev).
- Onboarding flow: pick program + year + display name.
- Seeder for the pilot school + a sample list of programs.
- Profile page (read-only for now).

**Exit:** I can sign up with a school email, verify, pick a program,
land on a homepage.

---

## Week 3 — Per-program chat 🟢

- `chat_rooms`, `chat_messages` migrations.
- Auto-create one program chat + year sub-channels on program seed.
- Livewire chat UI (list + composer).
- Reverb broadcasting on `program.{id}` channel.
- File/image attachments (≤ 25 MB) via S3-compatible storage.
- `@mentions` parsing + notification.

**Exit:** Two test users in the same program can chat in real time.
Messages and attachments persist.

---

## Week 4 — Subjects & resource catalog (read + create) 🟢

- `subjects`, `subject_aliases`, `program_subjects`,
  `resources` migrations.
- Seeder for subjects + aliases for the pilot programs.
- Resource create form (with file upload + watermarking job stub).
- Resource listing page with filters (subject, type, program, year).
- MySQL FULLTEXT search across `title` + `description`.

**Exit:** I can upload a resource, see it in search, filter by
subject.

---

## Week 5 — Shelves, resource detail, and watermarking 🟢

- `shelves`, `shelf_items` migrations.
- Resource detail page.
- "Save to shelf" button.
- PDF watermarking job: re-render PDF with downloader's name +
  timestamp; cache per-user for 24h.
- Image thumbnail generation on upload.

**Exit:** I can save someone else's resource and download a
watermarked copy.

---

## Week 6 — Request board + routing engine (without ML) 🟢

- `requests`, `request_routes`, `offers` migrations.
- Request create form (subject, type, urgency, needed_by).
- Routing engine implementation per `04-request-routing.md`, with the
  default hand-tuned weights.
- `NotifyRoutedUsers` job.
- Routed-notification UI (bell + dropdown).
- Offer flow: respond to a request with an existing resource or a
  fresh upload.

**Exit:** A request posted by a BSECE user routes to BSCS / BSIT /
BSMath users (per worked example) and they get notified.

---

## Week 7 — Karma, badges, and reputation 🟢

- `karma_events` migration.
- Event-driven karma updates (upload, save, fulfillment, reports).
- Badge tiers (Bronze/Silver/Gold) computed from karma thresholds.
- Profile updates to show karma + badges + counts.
- "Top sharers per program" simple leaderboard.

**Exit:** Karma increases for the right actions, badges show up on
profiles, leaderboard is correct.

---

## Week 8 — Lend tracking + return reminders 🟡

- `lends` migration.
- Mark a fulfilled request as a "lend" with `return_by` date.
- Daily scheduled job: email reminder 2 days before return.
- "My lends" page for both sides.

**Exit:** I can record a lend and get a return reminder.

---

## Week 9 — Moderation & reports 🟢

- `reports`, `program_moderators`, `audit_log` migrations.
- Report button on messages, resources, users.
- Moderator dashboard per program.
- Admin dashboard (school-wide).
- Suspend / ban actions with audit trail.

**Exit:** A reported message can be reviewed, hidden, and the user
sanctioned, with an audit log entry.

---

## Week 10 — Search polish, notifications, admin analytics 🟡

- Global search bar across resources, requests, chat messages
  (FULLTEXT).
- Email digest job (daily) summarizing routed requests + chat
  activity.
- Admin analytics dashboard: signups, DAU, top subjects, unanswered
  requests.

**Exit:** Search returns relevant results across resources, requests,
and chat. Admin dashboard reflects activity.

---

## Week 11 — Pilot prep & hardening 🟢

- Rate limiting on requests, uploads, login.
- Security pass: CSRF, XSS, file MIME whitelist, signed URLs only.
- Backup strategy: nightly MySQL dump → S3.
- Onboarding copy + Help page.
- Pilot user list: invite ~30 students across 3 programs.
- End-to-end smoke test (Pest browser tests).

**Exit:** Staging is hardened, monitored, and ready for the pilot.

---

## Week 12 — Pilot + final polish 🟢

- Launch pilot internally.
- Daily triage of bugs + feedback (use the request board for itself!).
- Iterate on routing weights based on real fulfillment data.
- Prepare project paper / demo deck.

**Exit:** Working pilot, ≥ the success metrics in
`00-product-overview.md` §5 (or a documented gap analysis), and a
demo ready for the panel.

---

## Post-MVP backlog 🔵

- DMs (one-to-one private chat).
- Versioned resources (`resource_revisions`).
- Per-user routing (`user_subjects`).
- Mobile PWA polish (install prompt, offline reading).
- Google Workspace SSO.
- Multi-school tenancy.
- Meilisearch.
- ML-learned routing weights.

---

## Risks to plan around

See [planning/risks.md](../planning/risks.md) for the full list. Top
three to keep in mind every week:

1. **Curriculum mapping is the moat.** If `program_subjects` is
   garbage, routing is garbage. Block out an hour every week to
   refine seeds.
2. **Real-time can be a time sink.** If Reverb wobbles in week 3,
   fall back to short polling and revisit later.
3. **Copyright complaints.** Watermarking + clear ToS from week 5.
   If a school admin objects, we may need an opt-in upload approval
   flow.

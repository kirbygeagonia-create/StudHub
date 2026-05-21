# StudHub — Comprehensive Analysis, Review & Improvement Plan
> Analyzed: `kirbygeagonia-create/StudHub` · `main` · Weeks 0–11 complete
> Reviewer: Claude Sonnet 4.6 · Date: 2026-05-20

---

## 0. Bottom Line Up Front

StudHub is a **genuinely well-engineered capstone project** — far above the typical student standard. The domain-driven structure, action pattern, backed enums, routing algorithm, and CI discipline are practices that most professional teams aspire to. The core value proposition (subject-aware cross-program routing) is architecturally sound and unique.

**Current state:** Demo-ready. Not yet fully pilot-ready. Roughly 80 % of the way to the success criteria in `00-product-overview.md`.

The sections below go feature by feature, layer by layer. Each section ends with **prioritized, actionable suggestions** tagged `[CRITICAL]`, `[HIGH]`, `[MEDIUM]`, or `[LOW]` so you know what to tackle in the remaining Week 12 time vs. what goes to the v1.1 backlog.

---

## 1. Does StudHub Satisfy Its Own Purpose?

### The stated purpose (from `00-product-overview.md`)

> "Make it as easy for a SEAIT student in one program to find learning resources from another program as it is to send a Messenger reply."

### Verdict: **Mostly yes, with one critical gap**

| Success criterion | Status | Notes |
|---|---|---|
| ≥ 60 % of pilot programs have daily-active students | ❓ Untestable until pilot | The tooling is there; adoption is the question |
| ≥ 100 resources uploaded | ❓ Needs pilot | Upload flow works; seeder provides test data |
| ≥ 50 cross-program requests fulfilled | ❓ Needs pilot | **Core flow is complete** end-to-end |
| Median request → match < 24 hours | ❓ Needs routing data | The routing engine is functional since F1 fix |
| Zero leaked copyright incidents | 🟡 Partial | Watermarking is still an SVG stub — not real PDF watermarking |

**The key differentiator — cross-program routing — works.** A request from a BSCE student can reach BSIT students who took the same subject. That's the moat, and it's real.

**The critical gap** is that PDF watermarking (the copyright protection mechanism) is still generating SVG placeholder thumbnails, not actual per-user watermarked PDFs. The `setasign/fpdi` and `tecnickcom/tcpdf` libraries listed in `02-architecture.md` are **not in `composer.json`**. This means the anti-piracy promise to the school administration cannot currently be kept.

---

## 2. System Architecture

### What's excellent

- **Boring-on-purpose monolith** is exactly right for this scale and team size. No microservices, no premature abstraction. A single Laravel app behind Nginx is the correct call.
- **Reverb for WebSockets** is a pragmatic first-party choice. Avoiding Pusher costs is wise for a school project.
- **Redis for both pub/sub and queues** means real-time chat and background jobs share one infrastructure piece.
- **S3-compatible storage** with a pluggable disk means local dev works without cloud credentials, and production just changes an env variable.
- **Domain-driven directory layout** (`app/Domain/{Domain}/Actions|Enums|Jobs|Notifications|Rules`) is a standout decision. It makes the codebase readable without a framework-specific decoder ring.
- **`DB::transaction()` + `lockForUpdate()`** in `RecordLend` — correct TOCTOU prevention.

### Gaps & Suggestions

**[CRITICAL] PDF watermarking is architecturally incomplete.**  
`composer.json` has no `setasign/fpdi` or `tecnickcom/tcpdf`. The `WatermarkResourceFile` job generates SVG stubs. Before pilot:
```bash
composer require setasign/fpdi tecnickcom/tcpdf
```
Then implement real per-user watermarking in the job. The architecture doc described the right approach; it just hasn't been implemented.

**[HIGH] No CDN or asset versioning strategy documented.**  
The architecture mentions Cloudflare for TLS + caching in staging but there's no config for `ASSET_URL` or cache-busting for compiled Vite assets. For production, add:
```env
ASSET_URL=https://your-cdn-domain.com
```
And ensure `vite.config.js` outputs content-hashed filenames (Vite does this by default in build mode — verify it's not disabled).

**[MEDIUM] Reverb single-instance is fine for pilot, but document the scaling path.**  
When the pilot succeeds and more than one program is active simultaneously, Reverb on a single box with 30+ concurrent WebSocket connections will be the first thing that struggles. Document the horizontal scaling path (multiple Reverb nodes + Redis pub/sub) in `02-architecture.md` so it's on the v1.5 radar.

**[MEDIUM] Missing: health endpoint in CI smoke tests.**  
`/up` exists (good), but the CI pipeline doesn't assert it. Add a smoke test job that hits `/up` on the staging URL after a deploy. This catches broken deploys before users do.

**[LOW] Consider a `colleges` table.**  
The schema doc identified this under "open schema questions" and it's been four weeks. The SEAIT-specific view (showing CICT vs. CTE vs. DCE breakdowns) is a natural admin analytics slice. One migration, one belongsTo relation, immediate value in the admin dashboard.

---

## 3. Database Schema

### What's excellent

- **`subjects` as the spine** — this is the core architectural insight. Everything connects through subjects, not programs. It's the right call.
- **`subject_aliases`** table cleanly handles the `DSA` ↔ `Data Structures and Algorithms` mapping problem.
- **`karma_events` as an append-only ledger** — the `SUM(delta)` approach means you can audit every karma change forever.
- **`audit_log` as an immutable table** — moderation actions are never deleted.
- **Soft deletes everywhere** — correct for a school system where you may need to restore content.
- **`program_subjects.weight`** as a learnable decimal — the schema already accommodates ML-learned weights in v2 without a migration.

### Gaps & Suggestions

**[HIGH] `resources` table: `save_count` and `lend_count` can drift from reality.**  
These are denormalized counters, documented as such. But there's no reconciliation job. If a `shelf_item` row is deleted directly (e.g., by a test seeder), `save_count` silently goes stale. Add a `studhub:recount-resources` command that reconciles these, and run it in the weekly maintenance schedule.

**[HIGH] `users.last_seen_at` is updated nowhere in the codebase (not visible in the routes/middleware).**  
The routing engine uses this field to rank notification candidates. If it's never updated, all users look equally "active." Add an event subscriber or middleware that updates `last_seen_at` on authenticated requests (throttled — max once per minute to avoid a write per page).

**[MEDIUM] `chat_messages` has no soft-delete.**  
When moderation hides a message (F5 fix), it calls `$reported->delete()`. Without soft-delete, the message is gone from the audit trail even though the audit log references it by ID. Add `SoftDeletes` to `ChatMessage` — it's one trait and one migration column.

**[MEDIUM] `requests` table: no index on `(requester_user_id, status)`.**  
The "My Requests" page (if it exists) will do a full scan on a table that grows indefinitely. Add this composite index in the next migration.

**[MEDIUM] No `student_number` column on `users`.**  
This was flagged as an open question in `03-database-schema.md`. For a school system, student numbers are the canonical identifier for the registrar. Without it, if a student re-enrolls with a different email, there's no way to link their history. Add it as nullable now, make it required after registrar sign-off.

**[LOW] `lends` table: `condition_on_return` enum doesn't match `resources.condition` enum.**  
`resources.condition` has: `like_new`, `good`, `worn`. `lends.condition_on_return` adds `damaged`. The asymmetry could confuse users. Either add `damaged` to `resources.condition` or rename `condition_on_return` values to match.

---

## 4. Backend & Domain Design

### What's excellent

- **Action pattern** — single `handle()` entry point, `RuntimeException` for invariant violations, `DB::transaction()` wrapping. Textbook usage.
- **Backed enums everywhere** — `RequestStatus`, `ResourceType`, `KarmaEventReason`, etc. Zero magic strings.
- **PHPDoc generics on Eloquent relations** — Larastan-friendly and IDE-friendly. Uncommon at student level.
- **Polymorphic morph map** registered in `AppServiceProvider` — decouples DB strings from PHP class names correctly.
- **`cursor()` in scheduled jobs** — correct OOM prevention for large datasets.
- **Routing engine architecture** — the weighted scoring algorithm with cold-start fallback, alias resolution, and notification fan-out via a queued job is well-designed.
- **`historicalFulfillmentRate()` implemented** (F1 fix) — the routing engine now actually does what the paper claims.

### Gaps & Suggestions

**[CRITICAL] `setasign/fpdi` and `tecnickcom/tcpdf` are missing from `composer.json`.**  
As above — watermarking is the legal protection layer. It must be real before pilot users upload anything.

**[HIGH] No rate limiting on `GET` routes for resource downloads.**  
You rate-limit `POST` routes (good — 20 routes, per Week 11 checklist), but a user can hammer the download endpoint in a loop, bypassing the per-user watermark cache and generating hundreds of watermarked PDFs. Add `throttle:30,1` (30 per minute) to the resource download route.

**[HIGH] `RouteRequest::historicalFulfillmentRate()` uses a static `$cache` array.**  
The per-request in-memory cache works for a single `RouteRequest::handle()` call but won't survive across queue workers for long-running processes. If this method is ever called outside a single request lifecycle (e.g., a scheduled weight-recalculation job), the stale cache could serve wrong values. Wrap it in a Redis cache with a 1-hour TTL instead:
```php
return Cache::remember("fulfillment_rate.{$programId}", 3600, fn () => ...);
```

**[MEDIUM] No `CreatingResource` / `ResourceCreated` domain events.**  
The action pattern is used but domain events are not. Cross-domain side effects (karma, search indexing, notifications) are currently handled by `AwardKarma::handle()` calls scattered inside actions. Adding domain events would let you decouple: `ResourceCreated` → `AwardKarma`, `IndexResource`, `NotifySubscribers`. This is a v1.1 refactor, not a blocker, but start wiring it now so it's not a massive migration later.

**[MEDIUM] `App\Models\Request` backward-compat alias is a liability.**  
The alias (`class Request extends ResourceRequest {}`) in `app/Models/Request.php` means both class names resolve to the same model. If a future developer writes `use App\Models\Request` thinking they're getting `Illuminate\Http\Request`, the alias silently redirects them. Remove the alias now that the rename is complete and update any remaining references.

**[MEDIUM] `CreateReport` checks for existing open reports, but not dismissed ones.**  
A user whose report was dismissed can immediately re-file the same report. This enables harassment (spam a moderator by repeatedly reporting the same post). Add a check for `status IN ('open', 'dismissed')` within the last N days.

**[LOW] No CQRS separation for read-heavy pages.**  
The resource catalog and search are read-heavy. Right now they go through Eloquent models. This is fine for v1. For v2, separate `App\Domain\Catalog\Queries\*` (read models) from `Actions\*` (write models) to enable caching strategies without cluttering actions with cache invalidation.

---

## 5. Request Routing Engine (the Core Innovation)

This section gets its own space because it's the project's key claim.

### What's excellent

The scoring formula is sound. Five weighted factors with a self-program penalty produces sensible results in the worked examples. The algorithm is well-documented with a real SEAIT scenario (`Calculus 2`, BSCE → BSIT/BSEd-Math/BSAIS). The cold-start fallback (no history → pure edge + resource score) is correctly handled.

### Gaps & Suggestions

**[HIGH] `program_subjects.weight` is never updated at runtime.**  
The schema has `weight` as a `DECIMAL(4,3)` with the note "learned over time." But there's no job that recomputes these weights from actual request fulfillment data. The routing engine's history factor (`w_history = 0.20`) is now correctly querying `request_routes` + `offers`, but the `w_edge` contribution (40 % of the score) still uses static seed data forever. Add a `studhub:recalculate-routing-weights` job scheduled weekly after the pilot starts.

**[MEDIUM] The alias resolution `fuzzy()` call is pseudocode, not an implementation.**  
Looking at `RouteRequest.php`, the fuzzy alias matching described in `04-request-routing.md` §4 (`candidates = subject_aliases.fuzzy(input, max_distance = 2)`) is pseudocode in the docs but the actual implementation likely falls back to exact match only. Verify that the Livewire subject picker does fuzzy matching client-side and that the action's `resolveSubject()` handles typos. If not, this is a UX gap — students searching for `"Discr. Math"` won't find `Discrete Mathematics`.

**[LOW] The self-program penalty (0.05) seems too small.**  
In the worked example, BSECE's own program scores 0.37 — just barely above the 0.35 threshold. This means the requester's own program will almost always be notified, which adds noise. Consider raising the self-program penalty to 0.15 or making it threshold-based (if own program score < 0.50, exclude it entirely). Tune this with real pilot data.

---

## 6. Frontend & UI

### What's excellent

- **Welcome page** — the landing page is clean, communicates the value prop well ("your school's resource exchange, not your inbox"), and has proper CTAs.
- **Lexend font** — a deliberate, readable choice. Good for a student-facing academic platform.
- **Tailwind-based consistency** — indigo primary, slate neutrals, consistent rounded-md + shadow-sm card style across pages.
- **Resource listing with filters** — 5-column filter grid (search, subject, type, program, year) is comprehensive and matches the user stories.

### Gaps & Suggestions

**[HIGH] No empty states anywhere visible.**  
When a user first signs up and visits the resource catalog, request board, or their shelf, they see blank content. Empty states should tell users what to do next:
```html
<!-- Example empty state for resources catalog -->
<div class="text-center py-16">
  <svg ...><!-- book icon --></svg>
  <h3>No resources yet for this subject</h3>
  <p>Be the first to share a reviewer or e-module.</p>
  <a href="{{ route('resources.create') }}">Upload a resource →</a>
</div>
```

**[HIGH] The dashboard is generic and static.**  
The current dashboard shows Karma / Badge / Program / Year as four isolated stat boxes plus three quick-action cards. It doesn't show:
- Unread notifications count
- Open requests that match resources you own
- Recent activity in your chat rooms
- "New since last visit" resources

A dashboard that shows nothing actionable trains users to ignore it. At minimum, add a "Needs your attention" panel showing open routed requests that you could fulfill.

**[HIGH] The chat index page exposes developer instructions to real users.**  
`chat/index.blade.php` shows: `"No chat rooms yet... run php artisan db:seed and refresh."` This is appropriate for dev but must be replaced with a proper empty state before pilot.

**[MEDIUM] No visual hierarchy between program chat and year sub-channels.**  
The chat index just lists rooms as a flat `<ul>`. BSIT has a program chat + up to 5 year channels = 6 rooms. With 26 programs, a moderator's view would be unmanageable. Group by program, then show year sub-channels indented underneath.

**[MEDIUM] Resource cards don't show the source program.**  
The resource listing (from the code) shows title, type, subject, availability. It does NOT prominently show "from BSIT" when a BSCE student is viewing. The cross-program discovery is the differentiator — make it visible. Every resource card should badge the source program prominently.

**[MEDIUM] No loading indicators on Livewire components.**  
The `ResourceForm` and the chat composer use Livewire, but there are no `wire:loading` states visible. A slow server response on file upload (which triggers a queue job) will look like a frozen UI. Add:
```html
<button wire:loading.attr="disabled">
  <span wire:loading.remove>Upload</span>
  <span wire:loading>Uploading…</span>
</button>
```

**[MEDIUM] The resource filter form submits via full page reload (`<form method="GET">`).**  
This is fine for SEO and initial load, but it loses scroll position and feels sluggish compared to instant filtering. Consider making the filters Livewire reactive properties so filtering updates results without a full page reload.

**[LOW] No favicon or PWA manifest beyond the empty `favicon.ico`.**  
The `00-product-overview.md` explicitly targets PWA as a delivery mechanism. `public/favicon.ico` is empty (0 bytes per the directory listing). Add at minimum a proper SVG favicon and a `manifest.json` with the app name, colors, and icons. This is a 30-minute task with major perceived polish impact.

**[LOW] No dark mode.**  
Not a blocking issue, but Filipino students often study late at night. A dark mode toggle is low-effort in Tailwind (just add `dark:` variants) and high-impact for nightly study sessions.

---

## 7. UX & Human-Friendliness

### What's excellent

- **Onboarding flow** (pick program + year) is the right first step and makes subsequent routing accurate from day one.
- **Karma + badge system** provides social proof and incentivizes contribution without being gamified to the point of distraction.
- **Request urgency levels** (`low` / `normal` / `urgent`) give requesters control over how aggressively the system fans out.
- **Shelf feature** — "save for later" is a small feature that pays off big in retention.

### Gaps & Suggestions

**[CRITICAL] No feedback loop when a routed notification arrives.**  
The routing engine sends a notification to up to 25 users. When a notified user taps the bell, they see "BSCE student needs a Calculus 2 reviewer." Then what? The path to "offer to help" must be **one tap**. Verify the notification links directly to the request's offer button, not to a generic notifications list.

**[HIGH] Error messages are technical, not human.**  
Laravel's default validation errors are developer-friendly (`"The subject_id field is required"`). Translate them to human-friendly language: `"Please select which subject this resource is for."` Add a custom `lang/en/validation.php` override for every field in the key forms.

**[HIGH] No confirmation after key actions.**  
When a user:
- Uploads a resource → they should see "✅ Your reviewer is live! Students searching for [subject] can now find it."
- Posts a request → "✅ Request posted. We'll notify students who can help."
- Fulfills a request → "🎉 You earned +10 karma for helping."

Right now these likely redirect back to a list with no visible feedback. Add flash messages to every key action.

**[HIGH] The lend return reminder UX is underspecified.**  
The email reminder is built (Week 8), but what happens if the borrower doesn't return the book? There's no escalation path: no second reminder, no "mark as returned by me" button for the lender, no way to flag a non-return to a moderator. Physical lends without enforcement erode trust fast.

**[MEDIUM] Onboarding doesn't explain karma or the request board.**  
New users pick their program and land on the dashboard. They've never heard of "request routing" before. The "Getting Started" card is good but brief. Consider a 3-step interactive onboarding modal on first login that shows: (1) how to upload, (2) how to post a request, (3) how karma works — each with a short demo animation or screenshot.

**[MEDIUM] No "I can help with" subject declaration UI visible.**  
The routing engine's `pickUsersToNotify()` gives +0.3 to users who declared a subject under "I can help with." But there's no visible UI for setting this. Either expose it in the profile settings, or add a prompt when a user uploads a resource tagged with a subject: "Would you like to be notified when others need [subject] materials?"

**[MEDIUM] The request board has no expiry warning visible to the requester.**  
`ExpireRequests` runs nightly and will mark requests as `expired`. But requesters don't see a "Expires in 2 days" warning on their own open requests. Add an `urgency_days_remaining` calculated field to the request show page.

**[LOW] Notification preferences exist as a concept (`notification_pref != 'mute_routed'`) but no UI for them.**  
Users who get notified too often will uninstall notifications system-wide. Give them a preferences page where they can set: "Only notify me for urgent requests" or "Mute routed notifications from [program]."

---

## 8. Security

### What's excellent

- Email domain restriction on sign-up closes the "outsider access" threat correctly.
- File MIME allow-list on resource + chat uploads (Week 11).
- Suspended users blocked from both HTTP routes and WebSocket channels (F3, F7 fixes).
- Signed URLs for file downloads (never served publicly).
- Rate limiting on 20 POST routes.
- Soft deletes + audit log combination for forensic integrity.

### Gaps & Suggestions

**[CRITICAL] Real PDF watermarking must be in place before users upload real files.**  
As stated above — this is both a security and a legal risk. A school administrator who downloads a file and sees no watermark will lose trust in the system immediately.

**[HIGH] No MIME verification on the download path.**  
MIME validation happens on upload (good), but the download path trusts `resources.file_mime` from the database without re-checking the actual stored file. If a file was manipulated at the storage layer (unlikely but possible), the browser could receive a mis-typed response. Add a `finfo_file()` check on the stored path before serving.

**[HIGH] The `visibility = 'private_link'` resource type is only a database enum — it needs a signed URL enforcement.**  
`private_link` visibility means the resource is only accessible to someone with the direct link. But if the signed URL is constructed from a predictable path (e.g., `/resources/{id}/download`), any authenticated user can access it by ID. Verify that the download policy checks `visibility` and that `private_link` resources generate a truly random token (not just the auto-increment ID).

**[MEDIUM] No CAPTCHA or bot-detection on sign-up.**  
The email domain restriction prevents non-school users, but it doesn't prevent automated account creation from a school email domain. A simple Honeypot field (hidden input that bots fill but humans don't) would catch naive automation without friction.

**[MEDIUM] `audit_log` doesn't record download events.**  
The audit log records moderation actions but not "User X downloaded Resource Y." For the watermarking promise to hold (deterrence, not just identification), users must know their download is logged. Add a `resource.download` event to the audit log on every signed download.

**[LOW] F15 (add `school_id` to `reports`) and F17 (Report global scope) are still open.**  
These are low-priority for a single-school deployment but are explicit data leakage paths in multi-school v2. Do F17 now (20 minutes per the audit) and note F15 as a v2 migration.

---

## 9. Testing & Quality

### What's excellent

- **186 tests, 459 assertions, ~22s runtime** — a green, fast suite.
- **Organized by domain** (`Feature/Catalog`, `Feature/Requests`, etc.) mirrors the domain structure.
- **Factory states** (`User::factory()->onboarded()->create()`) make test setup readable.
- **PHPStan Level 6** — beyond what most student projects attempt.
- **CI on every push** — non-negotiable practice and you're doing it.

### Gaps & Suggestions

**[HIGH] Test count is 186; target was 220+ before pilot.**  
The audit identified this gap (§3 of `session-handoff-2026-05-20.md`). The delta is ~34 tests. Focus areas:
- `Feature/Search` — the global search feature is completely untested
- `Feature/Moderation` — the 4 new tests from the audit patch bundle
- `Feature/Identity` — onboarding edge cases (non-school email, duplicate email)
- `Feature/Lends` — return reminder logic

**[HIGH] No browser/E2E test for the real-time chat flow.**  
The chat uses Livewire + Reverb. Unit tests can verify the `PostChatMessage` action, but they can't verify that messages actually appear in the UI without a page refresh. The Week 11 checklist called for an end-to-end smoke test on staging — this hasn't been built. Even a single Pest browser test using `pest-plugin-laravel`'s Dusk integration would validate the critical real-time path.

**[MEDIUM] Routing algorithm tests cover scoring but not notification fan-out.**  
`RouteRequestTest.php` has 261 lines covering scoring logic. But the test doesn't assert that `NotifyRoutedUsers::dispatch()` was called with the correct `user_id`s for a given routing scenario. Add a `Queue::fake()` + `Queue::assertPushed()` assertion on a full scenario test.

**[MEDIUM] No test for the `ExpireRequests` command.**  
This scheduled command runs daily and silently marks requests as expired. A regression here means requests never expire. Add:
```php
it('expires requests past their needed_by date', function () {
    $req = ResourceRequest::factory()->create([
        'status' => 'open',
        'needed_by' => now()->subDay()->toDateString(),
    ]);
    $this->artisan('studhub:expire-requests')->assertSuccessful();
    expect($req->fresh()->status)->toBe(RequestStatus::Expired);
});
```

**[LOW] SQLite in-memory test DB masks MySQL-specific bugs.**  
The FULLTEXT search fallback is explicitly noted ("LIKE fallback on SQLite/dev") in the Week 4 checklist. This means the actual production search path is untested in CI. Consider adding a MySQL-backed test suite job in GitHub Actions (using a MySQL service container) that runs the search and routing tests against the real DB engine.

---

## 10. Documentation & Maintainability

### What's excellent

- **7 docs files** covering vision, spec, architecture, schema, routing, roadmap, glossary, and SEAIT context — this is a complete product documentation set.
- **Session handoff documents** — the `planning/session-handoff-*.md` pattern is excellent. Anyone reading the repo can understand what was done last and what comes next.
- **ER diagram + routing sequence diagram** in `docs/diagrams/`.
- **`AGENTS.md`** providing AI-agent context for the repo is a forward-thinking practice.

### Gaps & Suggestions

**[HIGH] `README.md` still says "Week 1."**  
This is F14 from the audit, flagged as medium but critically important because **the panel and adviser will read README first**. A README that says "Week 1" on a Week 11 codebase is an immediate credibility hit. Update it to reflect:
- Current feature set
- How to run locally
- How to run tests
- Link to `docs/` for the full design documentation

**[MEDIUM] `docs/06-glossary.md` exists but isn't referenced anywhere.**  
New contributors and panel members won't find the glossary unless they read the full `docs/` directory. Add a "Glossary" link to the README and to the in-app Help page.

**[MEDIUM] The routing algorithm weights are buried in PHP constants, not in config.**  
```php
private const W_EDGE = 0.40;
private const W_RESOURCE = 0.25;
```
These should be in `config/studhub.php` (or already are — `config/lends.php` and `config/studhub.php` exist, check if weights are there). Tunable config means you can adjust routing weights during the pilot without a code deploy.

**[LOW] No `CHANGELOG.md`.**  
The session handoffs serve this purpose informally but a `CHANGELOG.md` following Keep a Changelog format would give the panel a clear "what shipped when" narrative.

---

## 11. Roadmap Execution Assessment

| Week | Planned | Delivered | Gap |
|---|---|---|---|
| 0 | Planning docs | ✅ All docs delivered | None |
| 1 | Project skeleton | ✅ Laravel 11, CI, Docker | None |
| 2 | Identity/onboarding | ✅ Email auth, program/year | None |
| 3 | Chat | ✅ Reverb, Livewire, attachments | Presence channels deferred |
| 4 | Resource catalog | ✅ 24 tests, FULLTEXT, upload | None |
| 5 | Shelves, watermarking | 🟡 Shelves ✅, watermarking is SVG stub | **Real PDF watermark missing** |
| 6 | Request board + routing | ✅ Routing engine, notifications | `historicalFulfillmentRate` was stub until F1 fix |
| 7 | Karma, badges | ✅ Atomic increment (F9), badge tiers | None |
| 8 | Lend tracking | ✅ `lends` table, return reminders | No escalation path for non-return |
| 9 | Moderation | ✅ Reports, dashboard, suspend/ban | Several bugs fixed in audit |
| 10 | Search, digest, analytics | ✅ All three delivered | None |
| 11 | Pilot prep, hardening | ✅ Rate limits, MIME, help, AUP | Watermark still stub; README stale; 186 vs 220+ tests |
| 12 | **Pilot** | 🚧 In progress | Depends on above gaps being closed |

**The pace is impressive.** Eleven weeks delivered on schedule with CI discipline. The gaps are real but fixable.

---

## 12. What's Missing That Wasn't in the Roadmap

These weren't planned for v1, but they're worth flagging as quick wins that would meaningfully improve the pilot:

**[HIGH] A "Why was I notified?" explanation.**  
When a student gets a routed notification, they don't know why. "You were notified because you're a BSIT student who may have taken Data Structures." This one sentence increases response rates dramatically by making the system feel intelligent, not spammy.

**[HIGH] A "Mark as resolved outside the system" option for requests.**  
A student might find the reviewer via Messenger and forget to update StudHub. Give requesters a "I found it elsewhere" option that closes the request without a formal offer/accept cycle. This prevents the request board from filling up with ghost open requests.

**[MEDIUM] Subject autocomplete in the request form.**  
Typing in a subject name should show autocomplete suggestions from the `subjects` + `subject_aliases` tables. Without this, users will pick the wrong subject or leave it blank. This is a Livewire `wire:model.live` + `$this->search()` pattern — about 30 minutes to implement.

**[MEDIUM] Program-to-program messaging summary.**  
An admin dashboard widget showing "BSIT → BSCE resource flow this week: 12 resources shared" would be the most compelling demo screen for the panel. The data is all there; it just needs a query.

**[MEDIUM] A "resource was helpful" signal.**  
Currently, karma is awarded for saves (+5). But a save might not mean the resource was actually useful. Add a simple "thumbs up / not useful" rating (visible only after download) that feeds into `program_subjects.weight` recalculation. This is the data flywheel that makes routing better over time.

**[LOW] Mobile PWA install prompt.**  
The `00-product-overview.md` explicitly says "Web + PWA only." There's no `manifest.json` or service worker. An installable PWA dramatically changes mobile engagement (icon on home screen vs. typing a URL). A bare-minimum manifest + `beforeinstallprompt` event is 1 hour of work.

---

## 13. Priority Action Matrix for Week 12

Given the time constraints (Week 12 = launch week), here's the suggested order:

### Before pilot opens (Days 1–2)
1. Install `setasign/fpdi` + `tecnickcom/tcpdf`, implement real PDF watermarking [CRITICAL]
2. Update `README.md` to reflect Week 11 state [HIGH]
3. Fix empty states on catalog, request board, shelf, and chat index [HIGH]
4. Add "Why was I notified?" text to routed notifications [HIGH]
5. Close F17 (Report global scope, 20 min per audit doc) [MEDIUM]

### During pilot (Days 3–7)
6. Add 34+ tests to reach the 220+ target [HIGH]
7. Wire subject autocomplete in request/resource forms [MEDIUM]
8. Add `users.last_seen_at` updater middleware [HIGH]
9. Add `audit_log` download events [MEDIUM]
10. Add the "I found it elsewhere" request resolution option [HIGH]

### Post-pilot / v1.1 backlog
11. `colleges` table + college-level admin analytics
12. Per-user "I can help with" declaration UI
13. `studhub:recalculate-routing-weights` scheduled job
14. `resource_revisions` table for versioned PDFs
15. `user_subjects` table for individual-level routing
16. Mobile PWA manifest + install prompt
17. Dark mode
18. Meilisearch (if FULLTEXT proves limiting during pilot)

---

## 14. Final Verdict

StudHub is the right system for the right problem. The subject-as-spine philosophy is correct, the routing algorithm is the real differentiator, and the engineering quality is genuinely above the norm for a student capstone.

**The system will satisfy its purpose if:**
1. Real PDF watermarking is implemented before users upload real files.
2. Empty states and action feedback are added before the pilot cohort touches the UI.
3. The routing engine gets the "Why was I notified?" UX layer so students trust it.
4. The README is updated so it doesn't undermine the codebase's quality.

**The system will exceed its purpose if** (during or after pilot):
- The "resource was helpful" signal feeds back into routing weights.
- Subject autocomplete is added to the request form.
- The admin dashboard shows cross-program resource flow.

The project demonstrates a clear understanding of the user's actual problem (not just a chat app, but a structured resource exchange with intelligent cross-program routing) and has built something architecturally capable of delivering on that promise. The remaining gaps are engineering work, not design failures.

---

*This analysis was generated from a full clone and read of the `kirbygeagonia-create/StudHub` repository, all `docs/`, `planning/`, and key source files in `app/` and `resources/views/`.*

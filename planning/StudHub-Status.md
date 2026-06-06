# StudHub — Current State & Items to Address

**Last reviewed:** 2026-06-03  
**Last commit:** `7a3d0cf` — Fix 28 PHPStan Level 7 errors  
**Repo:** `https://github.com/kirbygeagonia-create/StudHub.git`

---

## Current System State

The system has gone through **4 rounds of code review**. All critical bugs, security issues, architectural concerns, and improvement recommendations have been implemented.

| Metric | Value |
|---|---|
| Test suite | 239 `it()` tests · 28 `test()` tests · 45 test files · 649 assertions |
| Static analysis | PHPStan Larastan **Level 7** (clean) |
| Code style | Pint (clean) |
| Migrations | 42 files |
| App PHP files | 121 files |
| CI | GitHub Actions — Pint → PHPStan → Pest on every push |

### What's working

- **Resources** — upload, watermark (PDF + image), thumbnail generation, download with per-user watermark stamp, download count tracking, helpful vote with DB-level deduplication, shelf (save/unsave), fulltext search
- **Requests** — create, offer, accept, weighted routing engine (scoring by curriculum graph, resource density, historical fulfillment rate, year proximity, urgency), cross-program routing via `StudHubBot`, daily digest email
- **Chat** — program-scoped rooms, real-time via Laravel Reverb (`ShouldBroadcast` queued), cursor-based pagination (load older), unread count badge, file attachments, `@mention` notifications, system messages rendered as italic pill, upload progress indicator, character counter
- **Lends** — record lend, return flow, return-by date picker, overdue detection, return reminders (daily scheduled job), escalation with `LendEscalated` notification
- **Reputation** — karma events, 12 badge tiers (computed from karma, not stored), 36 achievement badges (DB-backed, SQLite-compatible SQL), leaderboard
- **Moderation** — reports, suspend/unsuspend, audit log, moderator dashboard, role-aware moderation scoping
- **Admin panels** — SAO, Dean, Program Head dashboards with chart time-range selector (7 days / 30 days / semester), feedback routing chain (student → moderator → program head → dean → SAO → super admin), announcements feature (create, publish, shown on student dashboard)
- **Auth & onboarding** — school email domain enforcement on registration and profile update, role-aware onboarding, `EnsureHasRole` uses `inheritedRoles()` correctly
- **Notifications** — grouped by type with collapsible sections, real-time bell fetch every 60 s, respects `only_urgent` and `muted_programs` preferences, `digest_enabled` preference respected by digest job
- **PWA** — manifest with SVG icons in `public/`, service worker with versioned cache (`studhub-v2`), install banner, mobile bottom tab bar
- **Infrastructure** — Docker Compose with PHP-FPM, Nginx, MySQL, Redis, Mailpit, and **Reverb** (containerized); `make up`, `make fresh`, `make test`, `make reverb`, `make reverb-docker`
- **Developer experience** — `GET /dev/components` preview route (local only), `DevUsersSeeder` in default seeder, `NotificationPreferences` typed DTO, `ChatRoomPolicy` single source of truth for room access, `UserObserver` archives resources on account deletion, `UserRole::System` for the StudHubBot (blocked from all role checks)

---

## Items Still to Address

There are **3 remaining items**. None are bugs or security issues — all are polish and tooling improvements.

---

### 1. PHPStan Level 8

**Current state:** PHPStan is clean at Level 7. Level 8 was not reached.

**What Level 8 adds:**
- Strict return type checking on `mixed` returns (Eloquent relations, array shapes)
- Detection of calling methods on potentially-null values without null-checks
- Dead code detection (unreachable branches)

**How to do it:**

1. Change `phpstan.neon`:
```neon
parameters:
    level: 8
```

2. Run `./vendor/bin/phpstan analyse` and fix the errors it surfaces. Based on the codebase patterns, expect errors in:
   - Eloquent relation return types (e.g. `->user()` returning `BelongsTo` without generic type hints)
   - `nullable` return values used without null-checks in controllers
   - A few `array<mixed>` shapes that need explicit key/value typing

3. Add `@phpstan-return` or `@return` docblocks where Larastan can't infer the type automatically.

**Effort:** ~2–4 hours.

---

### 2. Live Search — Still a Full Page Navigation

**Current state:** The search bar uses an Alpine.js watcher that auto-submits the form after a 500 ms debounce when the query is ≥ 3 characters. This navigates to `/search?q=...` — a full page reload. The spinner appears while waiting, which gives a sense of responsiveness, but the user is still taken away from whatever page they were on.

**What was recommended:** Results appearing inline below the search bar without leaving the current page (AJAX / fetch-based).

**How to do it:**

Option A — Livewire component (recommended for consistency with the rest of the stack):
```php
// app/Livewire/Search/GlobalSearchDropdown.php
class GlobalSearchDropdown extends Component
{
    public string $query = '';

    #[Computed]
    public function results(): array
    {
        if (mb_strlen($this->query) < 3) return [];
        return app(SearchGlobal::class)->handle(auth()->user(), $this->query);
    }

    public function render(): View
    {
        return view('livewire.search.global-search-dropdown');
    }
}
```

Then replace the current `<form>` in `navigation.blade.php` with `@livewire('search.global-search-dropdown')`.

The dropdown view shows `resources`, `requests`, and `messages` sections inline with a "See all results →" link to `/search?q=...` at the bottom.

Option B — Alpine.js + `fetch()` (no Livewire):
```js
// Inside navigation.blade.php x-data block
x-data="{
    q: '',
    results: null,
    searching: false,
    open: false,
    search(val) {
        if (val.length < 3) { this.results = null; this.open = false; return; }
        this.searching = true;
        clearTimeout(this._t);
        this._t = setTimeout(() => {
            fetch('/search/inline?q=' + encodeURIComponent(val), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => { this.results = data; this.open = true; this.searching = false; });
        }, 350);
    }
}"
```

Add a `GET /search/inline` route that returns JSON for the fetch call, throttled at `throttle:20,1`.

**Effort:** ~3–5 hours for the Livewire approach.

---

### 3. Service Worker Cache Version — Manual Bump Required

**Current state:** `public/sw.js` has:
```js
// Bump this version when deploying to force cache refresh
const CACHE_VERSION = '2';
```

This requires a developer to manually edit `sw.js` and increment the number before every production deployment. If forgotten, returning users will continue to serve stale JS/CSS assets from the cache indefinitely.

**What was recommended:** Inject the Vite build hash into the version string automatically.

**How to do it:**

Option A — Inject via a Laravel Blade route (cleanest):

Instead of serving `sw.js` as a static file, serve it through a controller that injects the current Vite manifest hash:

```php
// routes/web.php
Route::get('/sw.js', function () {
    $hash = cache()->remember('vite_manifest_hash', 3600, function () {
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        return substr(md5(json_encode($manifest)), 0, 8);
    });
    return response()
        ->view('sw', ['version' => $hash])
        ->header('Content-Type', 'application/javascript')
        ->header('Cache-Control', 'no-store');
})->name('sw');
```

Then rename `public/sw.js` → `resources/views/sw.blade.php` and replace the first line:
```js
const CACHE_VERSION = '{{ $version }}';
```

Delete `public/sw.js` so the static file doesn't intercept the route.

Option B — CI script (simpler, no Laravel changes):

In the GitHub Actions deploy step, run:
```bash
BUILD_HASH=$(md5sum public/build/manifest.json | cut -c1-8)
sed -i "s/const CACHE_VERSION = '[^']*'/const CACHE_VERSION = '${BUILD_HASH}'/" public/sw.js
```

**Effort:** ~30–60 minutes for Option A.

---

## Nothing Else Open

Every other item identified across all 4 review rounds has been implemented and verified:

- All 5 bugs fixed (search SQL, is_system column, helpful vote dedup, digest prefs, static cache)
- All 4 security issues fixed (email verify bypass documented, role removed from fillable, middleware inheritance, hardcoded school code)
- All 7 design concerns fixed (memberships table, chat policy, bot messages, feedback routing, chat pagination, icon dedup, test naming)
- All 4 performance items fixed (fulltext index, SQLite-compatible badge SQL, owner index, broadcast payload optimization)
- All 5 R-items fixed (PWA icons, Reverb in Docker, nav Auth calls, bot role, resource cascade)
- All 16 improvement recommendations implemented (UX-1 through UX-10, BE-1 through BE-6, SEC-1, SEC-2, T-2 through T-4, DX-1, DX-2)

---

*Document generated 2026-06-03 from 4 rounds of review by Claude Sonnet 4.6.*

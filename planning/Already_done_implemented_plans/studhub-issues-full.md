# StudHub — Full Issue Report (Verified Against Codebase)

**Prepared for:** AI Developer / Code Agent  
**Repository:** `https://github.com/kirbygeagonia-create/StudHub.git`  
**Stack:** Laravel 11 · Livewire 3 · Alpine.js · Tailwind CSS · Laravel Reverb (WebSockets)  
**Report Date:** 2026-05-28  
**Note:** Every issue below was verified directly against the cloned source code before writing. File paths are relative to the repository root.

---

## How to Read This Document

Each issue contains:
- **Where to look** — exact file(s)
- **What the code does now** — observed behaviour
- **What it should do** — expected/desired behaviour
- **Fix guidance** — concrete direction for the fix

Issues are grouped by module and ordered by severity (critical → cosmetic).

---

## SECTION 1 — Authentication & Login

### ISSUE 1-A · "These credentials do not match" message shows BLANK or in wrong place

**Severity:** High  
**File:** `resources/views/welcome.blade.php` (lines ~291–298)

**Current behaviour:**  
The error banner checks `$errors->first('login')`. The `LoginRequest` class does throw the error under the `'login'` key, so this is correctly wired. However, the error banner position is **between** the modal heading and the email field, which the developer placed correctly. If the user still sees it below the email field, this is most likely a **stale browser cache** of an older build. Confirm by doing a hard refresh.

**What the user wants:**  
The error should appear clearly **above all form fields**, directly below the "Welcome back" heading and the "Log in to your StudHub account" subtext — which is what the current code already does. Verify that no `@error('email')` directive was accidentally added below the email `<input>` tag.

**Fix:**  
1. Confirm the error banner div is **outside** and **before** the `<form>` tag.
2. Also add `@error('email')` and `@error('password')` inline under their respective fields, showing field-specific inline errors *in addition to* the top-level banner. This way, a wrong password gives inline feedback on the password field, and a wrong email gives feedback on the email field.
3. Clear the browser cache and re-test.

---

### ISSUE 1-B · Registered accounts won't log in / "Can't log in with other emails"

**Severity:** High  
**Files:** `app/Http/Controllers/Auth/RegisteredUserController.php`, `app/Domain/Identity/Rules/AllowedSchoolEmailDomain.php`, `config/studhub.php`

**Root cause:**  
Registration is restricted to email domains listed in `STUDHUB_ALLOWED_EMAIL_DOMAINS` in `.env`. If that env var is set to `seait.edu.ph`, only `@seait.edu.ph` emails can register — any other email will fail with "Registration is restricted to school email addresses."

This is **by design** for production. In local development, set `STUDHUB_ALLOWED_EMAIL_DOMAINS=` (empty string) in `.env` to allow any email.

**Additional check:**  
After registration, the controller sets `email_verified_at => now()` (auto-verified) and redirects to `/onboarding`. If the user did not complete onboarding (did not select program and year level), the `onboarded` middleware blocks all authenticated routes and keeps redirecting them away. Make sure every test account completes onboarding.

**Fix:**  
1. For local testing, set `STUDHUB_ALLOWED_EMAIL_DOMAINS=` (blank) in `.env`.
2. Add a clear error message on the register form: *"Only @seait.edu.ph email addresses are permitted."* This should appear as an inline validation error below the email field after a failed attempt.
3. Verify that newly registered users are redirected to `/onboarding`, and that onboarding completion is required before accessing any module.

---

## SECTION 2 — Chat Module

### ISSUE 2-A · Empty white message bubble sent when only a file/attachment is sent

**Severity:** High  
**File:** `resources/views/livewire/chat/room-conversation.blade.php`

**Current behaviour:**  
For **your own messages**, the text bubble is guarded correctly:
```blade
@if ($message->body !== '')
    <div class="bg-seait-500 ... rounded-2xl">...</div>
@endif
```
But for **other users' messages**, the text bubble is rendered unconditionally — even when `$message->body` is empty:
```blade
{{-- No guard here! Always renders an empty white box --}}
<div class="max-w-[75%] bg-white dark:bg-navy-800 border rounded-2xl ...">
    {{ $message->body }}
</div>
```
This is also present in the compact-continuation block for other users.

**Fix:**  
Wrap both text bubbles (the `$showHeader` block and the compact-continuation block) for other users' messages in the same guard:
```blade
@if ($message->body !== '')
    <div class="max-w-[75%] bg-white ...">
        {{ $message->body }}
    </div>
@endif
```
After this fix, sending only a file will render only the attachment pill, with no empty bubble.

---

### ISSUE 2-B · Attachment name is unreadable — orange text on orange background (own messages)

**Severity:** High  
**File:** `resources/views/livewire/chat/room-conversation.blade.php`

**Current behaviour:**  
For own messages, the attachment download link uses:
```blade
class="text-seait-300 hover:text-seait-200 ... bg-seait-500/20 hover:bg-seait-500/30"
```
`seait-300` is a light orange. The background `seait-500/20` is a semi-transparent orange. The resulting text is almost invisible against the bubble's `bg-seait-500` background.

**Fix:**  
Change own-message attachment pill styling to use legible colours on the orange bubble:
```blade
class="mt-1.5 inline-flex items-center gap-1.5 text-xs text-white/90 hover:text-white
       transition-colors float-right bg-white/20 hover:bg-white/30 px-2 py-1 rounded-lg"
```
This gives white/semi-transparent contrast, consistent with other messaging apps.

---

### ISSUE 2-C · Chat messages still appear to render newest at top instead of bottom

**Severity:** Medium  
**Files:** `app/Livewire/Chat/RoomConversation.php`, `resources/views/livewire/chat/room-conversation.blade.php`

**Current behaviour:**  
`roomMessages()` fetches with `->latest()->limit(50)->get()->reverse()->values()`, which is correct (oldest first). The JS `chatLog()` function scrolls `#chat-log` to the bottom on mount, and uses:
```js
Livewire.hook('morph.updated', ({ el }) => {
    if (this.atBottom) {
        this.$nextTick(() => { log.scrollTop = log.scrollHeight; });
    }
});
```
The logic is correct. However, the user consistently observes new messages appearing at the top. The likely cause is that `this` inside the `morph.updated` callback may lose Alpine.js context in certain Livewire 3 versions, causing `this.atBottom` to always be `false` and the auto-scroll to never fire.

**Fix:**  
Replace the `Livewire.hook` approach with a more reliable method — use a MutationObserver on `#chat-bottom` or use Livewire 3's `$wire` dispatch:
```js
// More reliable: re-scroll any time child count changes
const observer = new MutationObserver(() => {
    if (this.atBottom) {
        this.$nextTick(() => { log.scrollTop = log.scrollHeight; });
    }
});
observer.observe(log, { childList: true, subtree: false });
```
Also add an explicit `scrollIntoView` call in the Livewire `send()` method by dispatching a browser event:
```php
// In RoomConversation.php send() after unset($this->roomMessages):
$this->dispatch('message-sent');
```
```js
// In the JS init():
window.addEventListener('message-sent', () => {
    this.$nextTick(() => { log.scrollTop = log.scrollHeight; });
});
```

---

### ISSUE 2-D · Clicking an attachment downloads with a 403 Forbidden error

**Severity:** Medium  
**Files:** `app/Http/Controllers/ChatAttachmentController.php`, `routes/web.php`

**Current behaviour:**  
`ChatAttachmentController::download()` runs three `abort(403)` checks for school_id, program_id, and year_level mismatch. Files are stored with `Storage::disk('public')`. The download opens in a new browser tab.

**Most likely causes of 403:**
1. **Storage symlink not created.** Run `php artisan storage:link`. Without it, `Storage::disk('public')->exists($path)` returns `false` and the controller hits a 404, not 403. If 403 is actually occurring, it is the access check.
2. **Year-level room mismatch.** If the room is year-level-restricted (e.g., Year 2) and the currently logged-in user has year_level = 3 (e.g., they updated their profile), the year check aborts with 403 even though they were in the room when the file was sent.

**Fix:**  
1. Run `php artisan storage:link` if not already done.
2. In `ChatAttachmentController`, relax the year-level check: if the user is in the same program and same school as the room, allow the download regardless of year level. Year-level restriction should only gate entry to the chat room, not prevent access to files from shared rooms.
3. Add a helpful 403 message: *"You no longer have access to this room's files."*

---

### ISSUE 2-E · File upload UI — no visible confirmation that file is ready to send

**Severity:** Medium  
**File:** `resources/views/livewire/chat/room-conversation.blade.php`

**Current behaviour:**  
The file preview card (`<template x-if="hasFile">`) shows when `hasFile = true`. The `hasFile` flag is set in the `@change` handler. However, Alpine.js `x-data` state can be reset on Livewire DOM patches, causing the preview to disappear even though a file is selected.

**Fix:**  
Move the Alpine.js file-upload state (`uploading`, `progress`, `fileName`, `fileSize`, `hasFile`) to a named Alpine store so it persists across Livewire re-renders:
```js
// In app.js:
Alpine.store('chatUpload', { uploading: false, progress: 0, fileName: '', fileSize: 0, hasFile: false });
```
Then reference `$store.chatUpload.hasFile` etc. in the template. Alternatively, use `wire:ignore` on the upload preview container so Livewire skips it during re-renders.

---

### ISSUE 2-F · Chat file input only accepts images and PDFs — no documents (.docx, .xlsx, etc.)

**Severity:** Low  
**Files:** `resources/views/livewire/chat/room-conversation.blade.php`, `app/Livewire/Chat/RoomConversation.php`

**Current state:**  
```html
<input type="file" accept="image/*,application/pdf">
```
Validation in `send()`:
```php
'mimetypes:image/jpeg,image/png,image/webp,image/gif,application/pdf'
```

**Fix:**  
Expand accepted types to match the Resources module. Update both the `accept` attribute and the Livewire validation:
```html
accept="image/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,
        application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,
        application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation,
        text/plain"
```
And add the corresponding MIME types to the Livewire `mimetypes:` validation rule.

---

## SECTION 3 — Guest / Welcome Page Navigation

### ISSUE 3-A · Footer "Help" and "Acceptable Use Policy" links redirect to full pages instead of opening modals

**Severity:** Medium  
**File:** `resources/views/welcome.blade.php` (footer, around line 228–238)

**Current behaviour:**  
The top-navbar buttons correctly use `@click="showHelpModal = true"` and `@click="showPolicyModal = true"`. But the **footer** links are:
```blade
<a href="{{ route('help') }}">Help</a>
<a href="{{ route('aup') }}">Acceptable Use Policy</a>
```
These redirect away from the welcome page to separate full-page routes, breaking the modal flow.

**Fix:**  
Replace the footer `<a>` tags with Alpine-powered buttons:
```blade
<button type="button" @click="showHelpModal = true" class="text-sm ...">Help</button>
<button type="button" @click="showPolicyModal = true" class="text-sm ...">Acceptable Use Policy</button>
```

---

### ISSUE 3-B · `/help` and `/aup` standalone pages exist and redirect to `/login` and `/register` hard routes

**Severity:** Medium  
**Files:** `resources/views/layouts/guest.blade.php`, `routes/web.php`, `resources/views/pages/help.blade.php`, `resources/views/pages/aup.blade.php`

**Current behaviour:**  
When a user navigates directly to `/help` or `/aup` (e.g., via bookmark or a link from outside the app), the guest layout's navbar has:
```blade
<a href="{{ route('login') }}">Log in</a>      {{-- → redirects to /login full page --}}
<a href="{{ route('register') }}">Register</a>  {{-- → redirects to /register full page --}}
```
The `/login` and `/register` full-page routes still exist in `routes/auth.php`.

The `/help` and `/aup` full-page routes also exist (`Route::view('/help', ...)`, `Route::view('/aup', ...)`). This is redundant with the modals on the welcome page and creates two different UX flows.

**Fix (recommended approach):**  
Remove the standalone `/help` and `/aup` pages entirely. Redirect those routes to the welcome page with a query parameter:
```php
// In routes/web.php:
Route::get('/help', fn() => redirect('/?open=help'))->name('help');
Route::get('/aup', fn() => redirect('/?open=aup'))->name('aup');
```
Then in `welcome.blade.php`, read the query parameter to auto-open the right modal:
```js
// In x-init:
if (new URLSearchParams(window.location.search).get('open') === 'help') showHelpModal = true;
if (new URLSearchParams(window.location.search).get('open') === 'aup') showPolicyModal = true;
```

**Alternative (keep the standalone pages but add modal-style nav):**  
In `guest-layout.blade.php`, change the Log in / Register hard links to pass the user back to the welcome page:
```blade
<a href="/?open=login">Log in</a>
<a href="/?open=register">Register</a>
```
And in the welcome page, handle `open=login` / `open=register` the same way.

---

### ISSUE 3-C · "Help &amp; Guide" HTML entity rendering bug

**Severity:** Low  
**File:** `resources/views/pages/help.blade.php` (page title area)

**Current behaviour:**  
The page title shows "Help &amp; Guide" in the browser instead of "Help & Guide". This happens when a Blade `@section('title', 'Help & Guide')` or `{!! 'Help &amp; Guide' !!}` is used. Blade's `{{ }}` auto-escapes `&` to `&amp;`, which then renders as literal `&amp;` if the template engine escapes twice.

**Fix:**  
Use a raw `&` directly in a non-double-escaped context, or use `{!! __('Help & Guide') !!}` in Blade. In the guest layout `<title>` tag, use `{!! $title ?? 'StudHub' !!}` if the title is being passed through a variable that's already been HTML-encoded.

---

## SECTION 4 — Favicon / Logo Consistency

### ISSUE 4-A · Favicon still shows old "SH" logo on some browsers

**Severity:** Low  
**File:** `public/favicon.svg`

**Current state:**  
The `public/favicon.svg` is the correct new StudHub logo (orange gradient book shape). All layouts reference `/favicon.svg`. Chrome picks up the new icon; other browsers (Firefox, Edge, Safari) may still show the old "SH" cached icon.

**Fix:**  
1. This is a **browser caching** issue, not a code bug. Users should do a hard refresh (`Ctrl+Shift+R` / `Cmd+Shift+R`) or clear the browser cache.
2. To force browsers to pick up the new favicon, add a cache-busting version parameter: `<link rel="icon" href="/favicon.svg?v=2">` in all three layout files (`welcome.blade.php`, `layouts/app.blade.php`, `layouts/guest.blade.php`).
3. Ensure all three layouts use the **same** favicon reference so it's consistent across all pages.

---

## SECTION 5 — Resources Module

### ISSUE 5-A · "My Shelf" page has no type filter or search

**Severity:** Medium  
**Files:** `resources/views/resources/` (shelf view), `app/Http/Controllers/ResourceController.php` (`shelf()` method)

**Current behaviour:**  
The shelf page at `/my-shelf` lists all saved resources but has no filtering by type (PDF, image, text, document) and no search bar.

**Fix:**  
1. Add a type filter — a row of buttons or a `<select>` for: All / Document / PDF / Image / Text.
2. Add a search `<input>` that filters by resource title (client-side with Alpine.js or server-side via query param).
3. In the `ResourceController::shelf()` method, accept `?type=` and `?q=` query params and pass them to the query builder.

---

## SECTION 6 — Lends Module

### ISSUE 6-A · How lending is initiated is not discoverable

**Severity:** Medium  
**Files:** `resources/views/lends/index.blade.php`, `resources/views/requests/show.blade.php`

**Current behaviour:**  
The "Record Lend" button is on the **Request show page** (`/requests/{id}`), not on the Lends page. The full flow is:
1. Someone posts a Request.
2. You make an Offer on their Request.
3. They accept your Offer (request status → `matched`).
4. You go back to that Request's show page (`/requests/{id}`) and click **"Record Lend"**.
5. Only then does it appear in `/lends`.

The user can't find step 4 because they expect to initiate it from `/lends`, not from `/requests/{id}`.

**Fix:**  
1. On the `/lends` page, under the "Incoming Requests" section, make each request card's "View Request" link more prominent and add an explicit call-to-action: *"Go to request to record lend →"*.
2. Add a prominent **pending action banner** or notification when a user's offer has been accepted, with a direct link to the request page to record the lend. This could be a dashboard notification or a banner in the Lends module itself.
3. The "How lending works" collapsible guide on the Lends page already explains this, but it's collapsed by default. Consider showing it expanded for first-time visitors or users with pending offers.

---

## SECTION 7 — Requests Module

### ISSUE 7-A · "Create New Request" button is hard to find

**Severity:** Medium  
**Files:** `resources/views/requests/index.blade.php`, `resources/views/dashboard.blade.php`

**Current behaviour:**  
The "Create Request" button exists at `/requests/create` but the user reports not being able to find it. The request index (`/requests`) likely has a button, but it may not be prominent enough.

**Fix:**  
1. On the `/requests` index page, place a large, visually prominent **"+ New Request"** button in the top-right of the page header.
2. On the Dashboard, add a quick-action card: **"Need a resource? Post a request →"** linking to `/requests/create`.
3. Ensure the nav link under the requests menu, if any, includes a clear "Create" sub-action.

---

## SECTION 8 — Leaderboard Module

### ISSUE 8-A · "How ranking works" info button is misplaced — should be next to the page title

**Severity:** Low  
**File:** `resources/views/profile/leaderboard.blade.php`

**Current behaviour:**  
The info button is positioned **below the leaderboard list**, aligned right (`flex justify-end -mt-2 mb-2`). It reads "How ranking works" as a text link. It's easy to miss.

**Desired behaviour:**  
Move the `?` icon circle to sit directly **beside the "Leaderboard" page heading**, and apply this pattern consistently to all module pages (Chat, Requests, Resources, Lends, My Shelf, etc.).

**Fix:**  
1. Replace `<x-page-header title="{{ __('Leaderboard') }}" />` with an inline version that includes the info trigger:
```blade
<x-page-header title="{{ __('Leaderboard') }}">
    <x-slot:actions>
        <button x-data x-on:click="$dispatch('open-info-modal')" ...>
            <svg><!-- circle ? icon --></svg>
        </button>
    </x-slot:actions>
</x-page-header>
```
2. Alternatively, update the `x-page-header` component to accept an optional `info` slot.
3. The modal content (karma table, badge tiers, etc.) stays exactly as it is — just the trigger button moves.
4. Apply the same `?` button pattern to: Chat room headers, Resources page, Requests page, Lends page.

---

### ISSUE 8-B · Badge tiers in "How ranking works" still mention Bronze/Silver/Gold in some places

**Severity:** Low  
**Files:** `resources/views/profile/leaderboard.blade.php`, Help section in `welcome.blade.php`

**Current behaviour:**  
The leaderboard modal correctly shows the new tier names (Seedling, Bookworm, Scribe, Scholar, etc.). However, the **Help modal** inside `welcome.blade.php` at line ~551 shows old karma explanations and may still reference old tier names. Double-check and reconcile.

**Desired behaviour:**  
- The Leaderboard module should show visible (non-hidden) badge tiers with their icons, karma thresholds, and a one-line description.
- Hidden tiers must **not** be named. Show only: *"There are additional hidden tiers to discover as you participate more."*
- The same tier reference section should appear both in the Leaderboard "How rankings work" modal and in the Help guide.

---

## SECTION 9 — Admin / Role Structure

### ISSUE 9-A · Admin role hierarchy needs redesign for faculty-based structure

**Severity:** Medium  
**Files:** `app/Domain/Identity/Enums/UserRole.php`, `app/Http/Controllers/AdminController.php`, `routes/web.php`

**Background:**  
Currently the system has four roles: `student`, `moderator`, `admin`, `super_admin`. Admin users can have a `course` and `year_level`, which makes them look like student-admins. The school's actual organisational hierarchy is:
- **Program Head** — manages one academic program within a department
- **Dean** — manages the entire college/department
- **Safety and Security Office (SAO)** — campus-wide administrative authority, primary recipient of feedback

**Proposed structure:**

| Role | Maps To | Scope | Can receive feedback |
|------|---------|-------|----------------------|
| `student` | Student | Own program | No |
| `moderator` | Program Head | Their program's content | Yes — only their program |
| `admin` | Dean | Their college/department | Yes — all programs in their college |
| `sao` | SAO staff | Campus-wide | Yes — all feedback |
| `super_admin` | IT/System Owner | Entire system | Yes — everything |

**Fix recommendations:**
1. Add a new `sao` role value to `UserRole` enum (or repurpose admin → sao if no current admins are staff).
2. Remove `year_level` and `program_id` from Admin/Moderator user profiles — these fields only make sense for students.
3. Route feedback: Feedback submitted by any user goes to `sao` users. Feedback about a specific course/program additionally notifies the `moderator` (Program Head) for that program.
4. `admin` users (Deans) can see all feedback within their college. `super_admin` sees all feedback system-wide.
5. The Admin dashboard at `/admin` should show college-scoped data for Deans. A new SAO dashboard (or an extension of `/admin/super`) should show campus-wide feedback for SAO staff.

---

## SECTION 10 — Back Navigation

### ISSUE 10-A · Back buttons missing on several module pages

**Severity:** Low  
**Files:** Multiple — audit required across all views

**Current state:**  
Some pages already have back buttons (e.g., `lends/index.blade.php` → "Back to Dashboard", `chat/show.blade.php` → "Back to Chat Rooms"). Others do not.

**Pages that need a back button added (verify each):**
- `/requests/{id}` (Request detail) → "Back to Requests"
- `/requests/create` → "Back to Requests"
- `/resources/{id}` (Resource detail) → "Back to Resources"
- `/resources/create` → "Back to Resources"
- `/profile/edit` → "Back to Profile"
- `/users/{user}` (Public profile) → "← Back" (browser history)
- `/leaderboard` → "Back to Dashboard"
- `/notifications` → "Back to Dashboard"
- `/my-shelf` → "Back to Resources"
- `/search` → "Back to Dashboard"

**Consistent back button component to use (matches existing pattern):**
```blade
<a href="{{ route('target.route') }}"
   class="inline-flex items-center gap-1 text-xs text-gray-400 hover:text-seait-500 dark:hover:text-seait-400 transition-colors">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
    Back to [Page Name]
</a>
```

---

## SECTION 11 — Login/Register Modal Blur Delay

### ISSUE 11-A · Background blur has a noticeable delay when login/register modal opens

**Severity:** Low  
**File:** `resources/views/welcome.blade.php` (modal backdrop)

**Current behaviour:**  
The backdrop element uses `backdrop-blur-sm` as a Tailwind class. The Alpine.js transition is:
```html
x-transition:enter="transition ease-out duration-300"
```
The `backdrop-blur-sm` filter takes a full 300ms to fade in, causing the blur to "arrive late" visually compared to the overlay darkening.

**Fix:**  
Pre-render the backdrop at opacity 0 so the browser has the blur filter ready, and animate only the opacity:
```blade
{{-- Replace the backdrop div with: --}}
<div
  x-show="showLoginModal"
  x-transition:enter="transition duration-150"
  x-transition:enter-start="opacity-0"
  x-transition:enter-end="opacity-100"
  x-transition:leave="transition duration-100"
  x-transition:leave-start="opacity-100"
  x-transition:leave-end="opacity-0"
  class="absolute inset-0 bg-navy-950/50 backdrop-blur-sm"
  @click="showLoginModal = false">
</div>
```
Reduce the enter duration from 300ms to 150ms to feel snappier. Apply the same change to the Register and Forgot Password modal backdrops.

---

## Summary of All Issues

| # | Module | Issue | Severity |
|---|--------|-------|----------|
| 1-A | Auth | Credential error message placement | High |
| 1-B | Auth | Registration with non-school emails blocked by env config | High |
| 2-A | Chat | Empty white bubble when sending file-only message | High |
| 2-B | Chat | Attachment name unreadable (orange on orange) | High |
| 2-C | Chat | New messages not scrolling to bottom reliably | Medium |
| 2-D | Chat | Attachment download returns 403 | Medium |
| 2-E | Chat | No visible file-ready indicator after selecting attachment | Medium |
| 2-F | Chat | File upload only accepts images/PDF, not documents | Low |
| 3-A | Guest Page | Footer Help/AUP links go to full pages, not modals | Medium |
| 3-B | Guest Page | `/help` and `/aup` standalone pages redirect to `/login` not modal | Medium |
| 3-C | Guest Page | "Help &amp; Guide" entity rendering bug | Low |
| 4-A | Favicon | Old favicon cached in non-Chrome browsers | Low |
| 5-A | Resources | My Shelf has no type filter or search | Medium |
| 6-A | Lends | "Record Lend" button not discoverable (hidden on request page) | Medium |
| 7-A | Requests | "Create New Request" button hard to find | Medium |
| 8-A | Leaderboard | "How ranking works" info icon should be next to page title | Low |
| 8-B | Leaderboard | Old badge tier names may still appear in Help section | Low |
| 9-A | Admin | Admin role hierarchy needs faculty-based redesign | Medium |
| 10-A | All Pages | Back buttons missing on several module pages | Low |
| 11-A | Modals | Background blur has delay on modal open | Low |

---

*End of report. All file locations and code snippets were verified against the live repository checkout.*

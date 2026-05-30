# StudHub — Comprehensive Bug Report & Solution Guide

**Repository:** `github.com/kirbygeagonia-create/StudHub`
**Prepared:** For the developer AI / engineer in charge of the system
**Purpose:** Full list of identified bugs, UX issues, architecture decisions, and recommended solutions — organized by module and priority.

---

## Table of Contents

1. [Authentication & Login](#1-authentication--login)
2. [Chat Interface](#2-chat-interface)
3. [File Attachments in Chat](#3-file-attachments-in-chat)
4. [Resources Module](#4-resources-module)
5. [My Shelf Module](#5-my-shelf-module)
6. [Lends Module](#6-lends-module)
7. [Leaderboard Module](#7-leaderboard-module)
8. [Admin / Role Architecture — Full Redesign](#8-admin--role-architecture--full-redesign)
9. [Logo & Branding Consistency](#9-logo--branding-consistency)
10. [Guest-Side Navigation Issues](#10-guest-side-navigation-issues)
11. [Help & Policy Pages](#11-help--policy-pages)
12. [Badge & Tier System](#12-badge--tier-system)
13. [Global: Back Navigation](#13-global-back-navigation)
14. [Global: Info / Help Icon Pattern](#14-global-info--help-icon-pattern)
15. [Master Implementation Checklist](#15-master-implementation-checklist)

---

## Issue Summary Table

| Module | Issues Found | Priority |
|---|---|---|
| Authentication | 3 | 🔴 Critical |
| Chat Interface | 3 | 🟠 High |
| File Attachments in Chat | 4 | 🔴 Critical |
| Resources Module | 2 | 🟡 Medium |
| My Shelf Module | 2 | 🟡 Medium |
| Lends Module | 2 | 🟠 High |
| Leaderboard Module | 2 | 🟡 Medium |
| Admin / Role Architecture | 5 | 🔴 Critical |
| Logo & Branding | 3 | 🟢 Low |
| Guest-Side Navigation | 3 | 🟡 Medium |
| Help & Policy Pages | 2 | 🟢 Low |
| Badge & Tier System | 4 | 🟡 Medium |
| Back Navigation | 1 | 🟠 High |
| Info / Help UI Pattern | 2 | 🟢 Low |

---

## 1. Authentication & Login

> 🔴 **CRITICAL** — These bugs prevent normal use of the system.

---

### 1.1 Registered accounts are not being accepted on login

**Problem:** When a new account is registered, subsequent login attempts with those credentials fail.

**Root Cause Investigation Checklist:**

1. Verify the registration controller is properly hashing passwords (bcrypt/argon2) before storing in the database.
2. Check that the `users` table migration ran correctly and `email_verified_at` (if applicable) is not blocking login.
3. Confirm the login controller uses the same hashing method for comparison.
4. Check if email verification is enforced — if so, auto-verify in dev or send the verification email properly.
5. Add a debug log: after registration, immediately query the user record and confirm it exists.

**Fix (Laravel example):**

```php
// In AuthController@login
$user = User::where('email', $request->email)->first();

if (!$user || !Hash::check($request->password, $user->password)) {
    return back()->withErrors(['email' => 'These credentials do not match our records.']);
}
```

---

### 1.2 Error message placement — wrong field and wrong position

**Problem:** When an incorrect password is entered, the error message appears **under the email field** instead of a more prominent location, and the message references the email field even when the password was wrong.

**Fix:**

- Move the validation error banner to appear **directly below the "StudHub" heading/logo** and **above the email input field** — this makes it the first visible element the user sees.
- Use a single non-field-specific message: `"These credentials do not match our records."` — never reveal which field is wrong (security best practice).
- Style it as a full-width banner with a red/rose background, a warning icon, and clear text.

**Implementation (Blade/HTML):**

```blade
@if ($errors->any())
  <div class="w-full bg-red-50 border border-red-300 text-red-700 rounded px-4 py-3 mb-4 flex items-center gap-2">
    <!-- warning icon SVG here -->
    <span>{{ $errors->first('email') }}</span>
  </div>
@endif

<h1>StudHub</h1>
<!-- Then the email input field -->
```

---

### 1.3 Login redirects to `/login` instead of opening the modal

**Problem:** On the guest home page, clicking any protected nav link or action redirects to `localhost:8000/login` as a full page instead of opening the login modal.

**Fix:**

- All login triggers across the entire guest side must open the login modal via JavaScript — **never** redirect to `/login`.
- The route `/login` can still exist for fallback, but for JS-enabled browsers, always use the modal.
- Audit ALL nav links, dropdown items, and buttons on guest pages. Replace `<a href="/login">` with a JS event:

```javascript
document.getElementById('loginModal').showModal(); // or .show()
```

---

## 2. Chat Interface

> 🟠 **HIGH PRIORITY**

---

### 2.1 Messages appear in reverse order (bottom-to-top)

**Problem:** New messages appear above older messages — the reverse of every standard messaging application.

**Fix:**

- Messages **must** flow **top to bottom** — oldest at the top, newest at the bottom.
- The chat container should auto-scroll to the bottom when a new message is sent or received.
- Ensure the message list query is ordered ascending: `ORDER BY created_at ASC`.

```javascript
// On new message append, always scroll to bottom
const chatBox = document.getElementById('chat-messages');
chatBox.scrollTop = chatBox.scrollHeight;
```

- On initial page load, also scroll to bottom so the user sees the latest messages first.

---

### 2.2 No visible upload progress or confirmation when attaching a file

**Problem:** When a file is attached in chat, there is no loading indicator and no visible confirmation that a file was successfully attached before sending.

**Fix:**

- Show a **file preview card** below the text input immediately after file selection.
- The preview card should show: file type icon, file name, file size, and an ✕ button to remove it.
- Show a spinner/progress bar while the file is uploading to the server.
- Only enable the **Send** button after the upload is complete. While uploading, show `"Uploading..."` status text.

---

### 2.3 Add contextual help (?) icon for Chat

- Add a circular outline question-mark icon `(?)` next to the "Chat" heading.
- On click, show a small popover or modal explaining how chat works, group rules, and acceptable content.
- See [Section 14](#14-global-info--help-icon-pattern) for the global design pattern.

---

## 3. File Attachments in Chat

> 🔴 **CRITICAL**

---

### 3.1 Sent attachment shows "Attachment" instead of the real filename

**Problem:** After sending a file in chat, the message bubble shows `"Attachment"` as plain text instead of the actual filename with extension.

**Fix:**

- The sent message bubble must display the real file name (e.g., `thesis_chapter1.pdf`).
- Include the appropriate file-type icon alongside the name.

```javascript
// When rendering chat messages with attachments
const icon = getFileIcon(attachment.mime_type); // returns SVG icon based on type

// Render:
// [icon] thesis_chapter1.pdf  •  234 KB
```

---

### 3.2 Clicking an attachment returns a 403 Forbidden error

**Problem:** When a user clicks on a sent attachment to view or download it, they receive a `403 Forbidden` error.

**Root Cause Investigation:**

- Check that the file download route verifies the user is an authenticated participant of the chat.
- Verify storage permissions: if using Laravel + `storage:link`, confirm the symbolic link exists and the file is in the correct disk (`public` vs `private`).
- Use **signed URLs** or an authenticated download controller for private chat files — not direct public URLs.

**Recommended fix (Laravel):**

```php
// Route: GET /chat/attachments/{attachment}/download
public function download(Attachment $attachment)
{
    $this->authorize('view', $attachment); // Policy: user must be in the chat
    return Storage::download($attachment->path, $attachment->original_name);
}
```

---

### 3.3 File type icons for chat attachments

**Problem:** All attachments look the same — there are no visual indicators of file type.

**Fix:** Map MIME types or extensions to icons (same system already working in Resources module):

| Extension | Icon |
|---|---|
| `.pdf` | Red PDF icon |
| `.docx` / `.doc` | Blue Word icon |
| `.xlsx` / `.xls` | Green Excel icon |
| `.pptx` / `.ppt` | Orange PowerPoint icon |
| `.jpg` / `.png` / `.gif` / `.webp` | Image thumbnail preview |
| `.txt` / `.md` | Plain text icon |
| `.zip` / `.rar` | Archive icon |
| Unknown | Generic file icon |

> **Note:** Create a shared utility function/component (e.g., `FileIcon.vue` or `fileIcon.js`) used by **both** Resources and Chat to avoid code duplication.

---

### 3.4 Attachment viewer — open in modal/viewer instead of raw redirect

**Fix:**

- **Images:** Show inline in a lightbox/modal overlay.
- **PDFs:** Open in an embedded PDF viewer or a new browser tab.
- **Other files:** Trigger a proper browser download — never redirect to a login page.

---

## 4. Resources Module

> 🟡 **MEDIUM PRIORITY**

---

### 4.1 File type icons are working — confirm and standardize ✅

The Resources module already shows different icons per file type. This is correct behavior.

**Action items:**

- Verify all supported types (PDF, DOCX, XLSX, PPTX, images, text) show the correct icon.
- Carry this same icon-mapping system over to Chat attachments (see Section 3.3).
- Consolidate into one shared component used across the whole app.

---

### 4.2 Standardize the icon library

- Use a **single consistent icon library** (e.g., Heroicons, Lucide, or a custom SVG set) across the entire application.
- Do not mix icon libraries between modules.

---

## 5. My Shelf Module

> 🟡 **MEDIUM PRIORITY**

---

### 5.1 Missing filter feature

**Problem:** My Shelf has no way to filter saved items by type.

**Fix:**

- Add a horizontal filter tab bar or chip-style filter buttons at the top of My Shelf.
- Filter categories: **All, Documents, Images, Text Files, PDFs, Other**
- Clicking a filter instantly updates the displayed items (client-side filtering is fine for small datasets; server-side for large ones).

---

### 5.2 Missing search feature

**Problem:** My Shelf has no search bar to find specific saved resources.

**Fix:**

- Add a search input above the file list/grid.
- Search should match on: file name, uploader name, subject/course tag, and description.
- Use real-time search with debounce (300ms) or trigger on Enter.
- Search and filter should work **together** (e.g., search `"thesis"` AND filter to `"Documents"`).

---

## 6. Lends Module

> 🟠 **HIGH PRIORITY**

---

### 6.1 Lend/Borrow workflow is unclear — cannot find where to initiate it

**Problem:** The product owner cannot find where Lending or Borrowing is initiated. The flow is invisible.

**Recommended Workflow Design:**

1. A user visits a resource listing (in the Resources module).
2. They click a **"Request to Borrow"** button on the resource card (visible only to non-owners).
3. A modal appears asking for: duration, purpose/note, and a confirm button.
4. The resource owner receives a notification and sees the request in their **Lends → Requests** tab.
5. They approve or reject the request.
6. Approved lends appear in:
   - **Lender's** "Lent Items" tab (items I lent out)
   - **Borrower's** "Borrowed Items" tab (items I borrowed)

**Status badges** on each lend entry: `Pending` / `Active` / `Returned` / `Rejected`

---

### 6.2 Incoming Borrow Requests are impossible to find

**Problem:** There is nowhere visible for a user to see incoming borrow requests waiting for their approval.

**Fix:**

- The Lends module must have a **"Requests" tab** showing all incoming borrow requests.
- A **notification badge** should appear on the Lends nav item when there are pending requests.
- Clicking the notification takes the user directly to the Requests tab.

---

## 7. Leaderboard Module

> 🟡 **MEDIUM PRIORITY**

---

### 7.1 Move "How Ranking Works" to a (?) info icon modal

**Problem:** The "How ranking works" explanation block is displayed inline on the leaderboard, cluttering the UI.

**Fix:**

- Remove the inline explanation block.
- Add a **circular outlined (?) icon** directly beside the "Leaderboard" page title.
- On hover: show a tooltip that says `"How ranking works"`.
- On click: open a modal or popover with the full ranking explanation.

```html

  Leaderboard
  ?

```

---

### 7.2 Badge & Tier display in Leaderboard

- Show each user's current tier badge icon beside their name in the leaderboard table.
- Hovering over the badge shows a tooltip with the tier name.
- A link or button inside the ranking-info modal can lead to the full Badges & Tiers guide.
- See [Section 12](#12-badge--tier-system) for full badge/tier system details.

---

## 8. Admin / Role Architecture — Full Redesign

> 🔴 **CRITICAL — Read fully before implementing**

---

### 8.1 Current Problems

- Admins currently have a **course and year** assigned, implying they are students — this is incorrect and confusing.
- There is no clear hierarchy between Admin roles.
- Feedback routing is undefined.

---

### 8.2 Recommended Role Hierarchy (5 Tiers)

| Role | Real-world Equivalent | Capabilities | Feedback Access |
|---|---|---|---|
| **Super Admin** | IT / System Owner | Full system access, manage all roles, system settings | Receives all escalated feedback |
| **SAO Admin** | Safety & Security Office | System-wide feedback & reports, content moderation, user restriction | **PRIMARY** recipient of all feedback from all users |
| **Admin** | Program Head (per course/dept) | Manage resources & users in their course, approve content | Can submit feedback; receives feedback from students in their course |
| **Moderator** | Dean (per college) | Oversee all programs in their college, cross-program visibility | Can submit feedback; receives feedback escalated from Admins in their college |
| **Student** | Regular student user | All student features (chat, resources, leaderboard, lends, shelf) | Can submit feedback → goes to SAO + their course Admin |

---

### 8.3 Key Implementation Rules

1. **Admin and Moderator accounts must NOT have a `year_level` field** — they are faculty/staff, not students.
2. Admin and Moderator roles are tied to a **department/college**, not a year level.
3. Super Admin has **no department restriction** — sees everything.
4. SAO Admin is a **special role** separate from the academic hierarchy.
5. When creating an Admin or Moderator account, the registration form should ask for: Name, Email, Password, College, and Department (Admin only). The year field must be hidden for non-student roles.

---

### 8.4 Feedback Routing Rules

| Submitter | Goes To |
|---|---|
| Student | SAO Admin + course's Program Head (Admin) |
| Admin (Program Head) | SAO Admin + college's Moderator (Dean) |
| Moderator (Dean) | SAO Admin only |
| SAO Admin | Super Admin only |
| All feedback | Always visible to Super Admin |

---

### 8.5 Database Changes Required

- Add `role` enum to `users` table: `super_admin`, `sao_admin`, `admin`, `moderator`, `student`
- Add `college_id` and `department_id` columns to `users` table (nullable for students, SAO, Super Admin)
- Remove or make `year_level` nullable — hide it in admin/moderator creation forms
- Update `feedback` table to include: `recipient_role`, `college_id`, `department_id` for proper routing

---

## 9. Logo & Branding Consistency

> 🟢 **LOW PRIORITY**

---

### 9.1 Browser tab favicon is inconsistent

**Problem:** The favicon (the small icon in the browser tab/window header) is inconsistent — some pages show the StudHub logo, others show a generic globe/earth icon. The globe is the default Laravel favicon and was never replaced.

**Fix:**

- Place the official StudHub favicon at `/public/favicon.ico` and `/public/favicon.png`.
- Add to the **base layout** (`layouts/app.blade.php`):

```blade
<link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
```

- Ensure **every page** — including the guest home page — extends this base layout.

---

### 9.2 Guest home page still uses the globe icon

- Check if the guest home page uses a **separate layout** (e.g., `layouts/guest.blade.php`).
- If so, add the same favicon link there.

---

### 9.3 Logo consistency across all pages

- Audit every page and module for the StudHub logo in headers/navbars.
- Use a **single reusable Blade component**: `<x-logo />` — never hardcode the logo separately on each page.

---

## 10. Guest-Side Navigation Issues

> 🟡 **MEDIUM PRIORITY**

---

### 10.1 Nav links redirect to `/login` instead of modal

**Problem:** On the guest home page, clicking any nav or dropdown item redirects to `localhost:8000/login` instead of opening the login modal.

**Fix:** Intercept all protected links with JavaScript:

```javascript
document.querySelectorAll('[data-requires-auth]').forEach(el => {
  el.addEventListener('click', (e) => {
    e.preventDefault();
    document.getElementById('loginModal').showModal();
  });
});
```

Add `data-requires-auth` to every nav item/button that requires login.

---

### 10.2 Help and Acceptable Use Policy are correctly accessible as guest ✅

These pages correctly show in guest mode — this is right behavior. However, verify that any links **within** them do not accidentally trigger auth-only redirects.

---

### 10.3 After login modal — redirect to intended page

- When the login modal is triggered by a protected link click, **store the intended URL**.
- After successful login, redirect to the originally intended page, not the generic dashboard.

---

## 11. Help & Policy Pages

> 🟢 **LOW PRIORITY**

---

### 11.1 HTML entity encoding error in Help heading

**Problem:** The Help page title renders as `Help &amp; Guide` instead of `Help & Guide`.

**Fix:** Find and correct the encoding error in the template:

```blade
{{-- Wrong: --}}
<h2>Help &amp; Guide</h2>   {{-- Renders as: Help &amp; Guide --}}

{{-- Correct: --}}
<h2>Help & Guide</h2>

{{-- Or in Blade (safe): --}}
<h2>Help {!! '&' !!} Guide</h2>
```

---

### 11.2 Back button needed on Help and Policy pages

See [Section 13](#13-global-back-navigation) — all pages need a back button. Apply to Help and Policy pages:

- Help page → **Back to Home**
- Acceptable Use Policy page → **Back to Home** or previous page

---

## 12. Badge & Tier System

> 🟡 **MEDIUM PRIORITY**

---

### 12.1 "About Badges & Tiers" page must reflect the new tier system

**Problem:** The current about section still shows the old Bronze/Silver/Gold tiers.

**What to show:**

- **Visible tiers:** Show name, icon, and requirements to achieve them.
- **Visible badges:** Show icon and a short general description. Avoid spoiling how to earn discovery-based ones.
- **Hidden tiers/badges:** Do NOT show names or icons. Show a mystery/locked `???` placeholder with the text:
  > *"There are hidden badges and tiers waiting to be discovered. Keep participating to unlock them!"*

---

### 12.2 Tier display in Leaderboard

- Show each user's current tier badge icon beside their name in the leaderboard.
- Hovering over the badge shows a tooltip with the tier name.

---

### 12.3 Badge progress in user profile

- In the user's own profile, show their earned badges and progress toward the next tier.
- For hidden/unearned badges, show a `???` or padlock placeholder that reveals once earned.

---

### 12.4 Link "About Badges & Tiers" from Leaderboard (optional but recommended)

- Add a link inside the Leaderboard's ranking-info modal (from Section 7.1) to the Badges & Tiers guide.
- This improves discoverability for new users.

---

## 13. Global: Back Navigation

> 🟠 **HIGH PRIORITY**

---

### 13.1 Add back button to ALL interior pages

**Problem:** The entire system has no back buttons anywhere.

**Recommended approach: Context-aware back links**

Each page/controller defines where "back" goes. This is more reliable than `history.back()` which can exit the app.

```php
// In ResourceController@show
return view('resources.show', [
    'resource' => $resource,
    'backUrl'  => route('resources.index'),
]);
```

```blade
{{-- In the Blade view --}}
<a href="{{ $backUrl }}" class="flex items-center gap-1 text-sm text-gray-500 hover:text-blue-600 mb-4">
  ← Back to Resources
</a>
```

**Pages that MUST have a back button:**

| Page | Back destination |
|---|---|
| Resource detail / view | → Resources index |
| User profile | → Previous page |
| Chat conversation | → Chat list |
| Leaderboard detail view | → Leaderboard |
| Help page | → Home |
| Acceptable Use Policy | → Home or previous page |
| Lend / request detail | → Lends module |
| Settings subpages | → Settings main |
| Admin sub-pages | → Admin dashboard |

---

## 14. Global: Info / Help Icon Pattern

> 🟢 **LOW PRIORITY**

---

### 14.1 Standardize contextual help using (?) icons across all modules

**Replace inline help text blocks with a reusable (?) icon that opens a modal or popover.**

**Apply to:**

- Leaderboard — "How ranking works" *(already identified, Section 7.1)*
- Chat — Chat rules, acceptable content
- Lends — How lending/borrowing works
- Resources — Upload guidelines, acceptable formats
- My Shelf — What this section is for
- Badges & Tiers — Accessible from Leaderboard or Profile

**Design spec:**

| Property | Value |
|---|---|
| Shape | Circle with ? inside — outline style, not filled |
| Size | 20×20px or 24×24px |
| Default color | Gray |
| Hover color | Blue |
| Hover tooltip | Short label e.g. `"How does this work?"` |
| On click | Open modal or popover with explanation |
| Modal | Title + body text + close button (or click-outside-to-close) |

**Reusable Blade component:**

```blade
{{-- Usage --}}
<x-info-icon title="How Ranking Works">
  Points are awarded for uploading resources, participating in chat...
</x-info-icon>
```

---

## 15. Master Implementation Checklist

Use this checklist to track progress. Complete in priority order.

---

### 🔴 Critical — Fix First (System Broken Without These)

- [ ] Fix: New registered accounts cannot log in — (Section 1.1)
- [ ] Fix: `/login` full-page redirect on guest side — replace with modal everywhere — (Section 1.3, 10.1)
- [ ] Fix: Chat attachment `403 Forbidden` error on download — (Section 3.2)
- [ ] Fix: Chat messages appear in reverse order (bottom-to-top) — (Section 2.1)
- [ ] Fix: Sent attachment label shows `"Attachment"` instead of filename — (Section 3.1)

---

### 🟠 High Priority

- [ ] Fix: Error message position — move to below StudHub heading, above email field — (Section 1.2)
- [ ] Add: File upload progress/confirmation indicator in chat — (Section 2.2)
- [ ] Add: File type icons for chat attachments — (Section 3.3)
- [ ] Add: "Request to Borrow" button on resource cards — (Section 6.1)
- [ ] Add: Lends module — "Requests" tab for incoming borrow requests — (Section 6.2)
- [ ] Add: Back buttons to all interior pages — (Section 13)

---

### 🟡 Medium Priority

- [ ] Add: My Shelf — filter by file type — (Section 5.1)
- [ ] Add: My Shelf — search feature — (Section 5.2)
- [ ] Redesign: Admin role hierarchy — implement the 5-tier system — (Section 8)
- [ ] Update: Badge & Tier about page — reflect new tiers, hide hidden ones — (Section 12)
- [ ] Fix: HTML entity encoding error in Help heading `&amp;` → `&` — (Section 11.1)
- [ ] Move: Leaderboard "How ranking works" to (?) icon modal — (Section 7.1)
- [ ] Add: (?) info icons to all modules using standard component — (Section 14)
- [ ] Fix: Guest nav links trigger modal, not `/login` redirect — (Section 10.1)

---

### 🟢 Low Priority / Polish

- [ ] Fix: Favicon — replace globe icon with StudHub logo on ALL pages — (Section 9.1, 9.2)
- [ ] Fix: Logo consistency — create and use single `<x-logo>` component — (Section 9.3)
- [ ] Add: Badge/tier info link inside Leaderboard ranking modal — (Section 12.4)
- [ ] Add: Chat contextual help (?) icon — (Section 2.3)
- [ ] Add: Attachment viewer — lightbox for images, PDF viewer for PDFs — (Section 3.4)
- [ ] Add: After login redirect to originally intended page — (Section 10.3)
- [ ] Shared: Extract file icon logic into a single shared component for Resources + Chat — (Section 4.1)

---

*— End of Document —*
*StudHub Comprehensive Bug Report | github.com/kirbygeagonia-create/StudHub*
# Week 3 — Per-Program Chat: Adversarial Test Plan

PR: https://github.com/kirbygeagonia-create/StudHub/pull/8
Branch: `devin/1778716843-week3-program-chat`

## What changed (user-visible)
- Authenticated users now see a `Chat` link in the nav.
- `/chat` lists rooms scoped to the user's program (one `#<code>-general` + one `#<code>-yN` for the user's year).
- `/chat/{room}` opens a Livewire conversation: 50-message backlog, composer with `@mentions`, 25 MB attachments, polling fallback every 10s.
- Posting a message persists it, broadcasts `chat.message.posted` on `chat-room.{id}`, and writes a row to `notifications` for each `@`-mention.
- Cross-program / wrong-year access to `/chat/{id}` returns **403 Forbidden**.
- `db:seed` auto-provisions all rooms for SEAIT programs (127 rooms total for 26 programs).

## Primary flow under test
Two SEAIT users register and onboard into the **same** program/year (BSIT Y2) and a **third** user into a different program (BSCE Y3). The BSIT pair exchanges a chat message with a mention; the BSCE user attempts cross-program access and is blocked.

Code evidence:
- Routes: `routes/web.php:22-23` — `/chat` and `/chat/{room}` behind `auth + verified + onboarded`.
- Guard: `app/Http/Controllers/ChatController.php:45-60` — throws `AccessDeniedHttpException` (HTTP 403) when scopes mismatch.
- Livewire send + mention parse: `app/Domain/Chat/Actions/PostChatMessage.php:27-63`.
- Mention regex: `app/Domain/Chat/Actions/PostChatMessage.php:73` — `/(?<![\w@])@([A-Za-z0-9_.\-]{2,40})/u`.
- Composer view: `resources/views/livewire/chat/room-conversation.blade.php:26-46`.

## Test cases

### T1 — Rooms list is program-scoped (POSITIVE + adversarial)
**Setup**: Logged in as BSIT-Y2 user (`alice.bsit@seait.edu.ph`, display_name `alice.bsit`).
**Steps**:
1. Click `Chat` in nav → land on `/chat`.
**Expected**:
- Page header reads `Chat`.
- Body shows `Rooms scoped to your program (BSIT).`
- Exactly **2** rooms visible: `#bsit-general` (PROGRAM) and `#bsit-y2` (PROGRAM YEAR).
- **None** of these slugs appear: `#bsit-y1`, `#bsit-y3`, `#bsit-y4`, `#bsce-general`, `#bsce-y3`.

Why adversarial: a broken scope query (e.g. dropping the `year_level` filter, or only filtering by school) would show all BSIT year rooms or all 127 rooms.

### T2 — Posting a chat message persists, renders for sender, and refreshes via polling for the other user
**Setup**: BSIT-Y2 user Alice in browser tab A on `/chat/<bsit-general-id>`. BSIT-Y2 user Bob (`bob.bsit@seait.edu.ph`) in tab B on the same room.
**Steps**:
1. In tab A, type `hello from alice` in the textarea, click **Send**.
2. Observe tab A.
3. Wait up to 12 seconds and observe tab B.
**Expected**:
- Tab A: empty-state text `No messages yet — be the first to say hi.` disappears; one `data-testid="chat-message"` article appears with body `hello from alice` and sender label `alice.bsit BSIT·Y2`.
- Textarea clears to empty.
- Tab B: within 12 s (polling is 10 s) the same message appears.
- DB: `select count(*) from chat_messages where body='hello from alice'` returns `1`.

Why adversarial: a no-op send / Livewire wiring break would leave the empty state and a stale tab B. The 12 s ceiling distinguishes "real wiring" from "lucky reload".

### T3 — `@`-mention writes a `notifications` row (and excludes the sender)
**Setup**: same room as T2, Alice still logged in.
**Steps**:
1. In tab A, post message: `@bob.bsit ping you on the new app @alice.bsit`.
2. Inspect database.
**Expected**:
- `select count(*) from chat_message_mentions where chat_message_id = <new id>` returns `1` (only bob — sender excluded).
- `select count(*) from notifications where notifiable_id = <bob_id> and notifiable_type='App\Models\User'` returns `1`, `type` = `App\Domain\Chat\Notifications\ChatMentionNotification`.
- No notification row for Alice (sender).

Why adversarial: if the regex or sender-exclusion is broken, you either get zero rows (regex miss) or two rows (sender included). Including the self-mention is the critical "broken" signal.

### T4 — Cross-program 403 guard
**Setup**: log in as Carol (BSCE Y3). Use Alice's BSIT-general room id from T2.
**Steps**:
1. Navigate directly to `/chat/<bsit-general-id>`.
**Expected**: HTTP 403 page (Symfony default). URL stays on `/chat/<id>`; no chat composer renders.

Why adversarial: if `authorizeRoom()` is wrong (e.g. only checks school not program), Carol could read BSIT's room. This single hop is the most important security guarantee of Week 3.

### T5 — Wrong-year sub-channel 403 (regression for ProgramYear guard)
**Setup**: still logged in as Alice (BSIT-Y2).
**Steps**:
1. Navigate directly to `/chat/<id-of-#bsit-y1>` (her program but Y1).
**Expected**: HTTP 403. Composer does not render.

Why adversarial: catches a guard that filters by program but ignores `year_level`.

## Out of scope for this run
- Live Reverb socket delivery (`reverb:start`) — Week 3 documents poll fallback as acceptable. Will exercise T2 via polling and explicitly mark Reverb live-socket as **untested** in the report.
- File-attachment upload to S3-like storage (the public disk path is present but no real S3 in local). Will mark **untested** if not part of the primary recording.
- Subject graph / request board / karma — Weeks 4+.

## Evidence to capture
- Recording covering T1–T5.
- Screenshots: rooms list, sent message in both tabs, 403 page, DB query results.
- DB output for chat_messages / chat_message_mentions / notifications counts.

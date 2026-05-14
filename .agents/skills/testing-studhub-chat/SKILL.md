---
name: testing-studhub-chat
description: Test the StudHub per-program chat feature end-to-end. Use when verifying chat UI, messaging, mentions, or access control changes.
---

# Testing StudHub Chat

## Prerequisites

- PHP 8.3 + Composer installed
- No MySQL/Redis required — SQLite works for local E2E testing

## Local Environment Setup

1. Copy `.env.example` to `.env` if not present, then adjust for local testing:
   ```bash
   cp .env.example .env  # if needed
   # In .env, set:
   # DB_CONNECTION=sqlite
   # DB_DATABASE=/absolute/path/to/database/database.sqlite
   # CACHE_STORE=file
   # QUEUE_CONNECTION=sync
   # BROADCAST_CONNECTION=log
   # MAIL_MAILER=log
   ```
2. Create the SQLite file and run migrations + seed:
   ```bash
   touch database/database.sqlite
   php artisan migrate:fresh --seed --force
   php artisan storage:link
   ```
3. Verify seeding produced chat rooms:
   ```bash
   php artisan tinker --execute='echo App\Models\ChatRoom::count() . " chat rooms"'
   # Expected: 127 chat rooms (for 26 SEAIT programs)
   ```
4. Start the dev server:
   ```bash
   php artisan serve --host=127.0.0.1 --port=8000
   ```

## Creating Test Users

Use tinker to seed pre-onboarded, email-verified test users (avoids the email verification blocker):

```bash
php artisan tinker --execute='
$school = App\Models\School::first();
$bsit = App\Models\Program::where("code","BSIT")->first();
$bsce = App\Models\Program::where("code","BSCE")->first();
foreach ([
  ["alice.bsit@seait.edu.ph","Alice BSIT","alice.bsit",$bsit,2],
  ["bob.bsit@seait.edu.ph","Bob BSIT","bob.bsit",$bsit,2],
  ["carol.bsce@seait.edu.ph","Carol BSCE","carol.bsce",$bsce,3],
] as [$email,$name,$dn,$program,$year]) {
  $u = App\Models\User::firstOrNew(["email"=>$email]);
  $u->name = $name;
  $u->display_name = $dn;
  $u->password = Hash::make("Password123!");
  $u->school_id = $school->id;
  $u->college_id = $program->college_id;
  $u->program_id = $program->id;
  $u->year_level = $year;
  $u->role = App\Domain\Identity\Enums\UserRole::Student;
  $u->karma = 0;
  $u->onboarded_at = now();
  $u->email_verified_at = now();
  $u->save();
  echo "Saved: $email ($dn) program={$program->code} y$year\n";
}'
```

All users have password: `Password123!`

## Key Room IDs (after fresh seed)

These may vary if seeder order changes, but typically:
- `bsit-general` = id 1
- `bsit-y1` = id 2
- `bsit-y2` = id 3
- `bsce-general` = id 14

Verify with: `php artisan tinker --execute='App\Models\ChatRoom::where("slug","bsit-general")->value("id")'`

## Testing Flows

### 1. Room Scoping
- Login as Alice (BSIT Y2) → navigate to `/chat`
- Expected: exactly 2 rooms visible (`#bsit-general` + `#bsit-y2`)
- No rooms from other programs or other year levels should appear

### 2. Message Posting + Polling
- Open `#bsit-general` as Alice in one browser window
- Open same room as Bob in incognito (separate session)
- Alice posts a message → should render immediately for Alice
- Bob's view should update within ~12 seconds (wire:poll.10s)
- Note: without Reverb running, only polling works

### 3. @Mentions
- Alice posts: `@bob.bsit hello`
- Verify via tinker:
  ```bash
  php artisan tinker --execute='
  $msg = App\Models\ChatMessage::latest("id")->first();
  echo "Mentions: " . $msg->mentions()->count() . "\n";
  echo "Notifications for Bob: " . DB::table("notifications")->where("notifiable_id",2)->count();
  '
  ```
- Expected: 1 mention (Bob), 1 notification for Bob, 0 for Alice (sender excluded)

### 4. Cross-Program 403
- Login as Carol (BSCE) → navigate directly to `/chat/<bsit-general-id>`
- Expected: 403 page with "You cannot view this chat room."

### 5. Wrong-Year 403
- Login as Alice or Bob (BSIT Y2) → navigate to `/chat/<bsit-y1-id>`
- Expected: 403 page

## Known Quirks

- **Livewire `wire:model` sync timing**: The textarea uses `wire:model` (deferred), which syncs on blur, not on every keystroke. If you type and immediately click Send, the value may not be synced yet. Tab out of the textarea first, then click Send.
- **Redis not required**: The app defaults to Redis for cache/queue, but for local testing, `CACHE_STORE=file` and `QUEUE_CONNECTION=sync` work fine.
- **Email verification**: Users created via the registration form need `email_verified_at` set. The tinker script above handles this. If registering manually, mark verified via: `App\Models\User::where('email','...')->update(['email_verified_at'=>now()])`
- **Reverb WebSocket**: Optional. Without it, `wire:poll.10s` provides the fallback. To test Reverb: set `BROADCAST_CONNECTION=reverb`, configure `REVERB_*` env vars, and run `php artisan reverb:start`.

## Devin Secrets Needed

None required for local testing. All test users are created via tinker with known passwords.

# StudHub — Complete Bug Audit & Fix Guide v5
> Applies in order. Each section states the ROOT CAUSE, then the exact fix.
> Items marked 🔴 are broken (system doesn't work). 🟡 are UX problems. 🟢 are improvements.

---

## 🔴 BUG 1 — Chrome Autofill: Text Invisible (White on White)

**Root cause:** Chrome applies `-webkit-autofill` CSS on filled inputs, which overrides the background colour with an internal fill colour. The `.input-field` class has `bg-white` which Chrome ignores when autofill is active, and the text colour can become white/invisible. Edge handles this differently (it respects the declared colour).

**File:** `resources/css/app.css`

Inside the `.input-field` block, after the current `@apply` rules, add:

```css
  .input-field {
    @apply w-full rounded-xl border-gray-200 bg-white text-sm shadow-sm
           placeholder:text-gray-400
           focus:border-seait-400 focus:ring-2 focus:ring-seait-100
           transition-colors duration-200
           dark:bg-navy-800/80 dark:border-navy-700 dark:text-gray-100
           dark:placeholder:text-gray-500 dark:focus:border-seait-500 dark:focus:ring-seait-800/30;

    /* Chrome autofill fix — prevent invisible white-on-white text */
    &:-webkit-autofill,
    &:-webkit-autofill:hover,
    &:-webkit-autofill:focus {
      -webkit-text-fill-color: #111827; /* gray-900 equivalent */
      -webkit-box-shadow: 0 0 0px 1000px #ffffff inset;
      transition: background-color 5000s ease-in-out 0s;
    }
  }
```

Also add a dark-mode variant by appending to the same block:

```css
    .dark .input-field:-webkit-autofill,
    .dark .input-field:-webkit-autofill:hover,
    .dark .input-field:-webkit-autofill:focus {
      -webkit-text-fill-color: #f3f4f6; /* gray-100 */
      -webkit-box-shadow: 0 0 0px 1000px #1e2a3b inset; /* navy-800 */
    }
```

---

## 🔴 BUG 2 — Login/Register Modal Dismisses on Error

**Root cause:** When the login or register form submits (real POST, not AJAX), Laravel redirects back with `$errors` in the session. The page reloads and Alpine initialises with `showLoginModal: false` — so the modal is gone. The error messages render outside the modal in the page body, where the user has to click "Log in" again to see them.

**Fix:** Use PHP to set the initial Alpine state based on whether errors or a route match is present.

**File:** `resources/views/welcome.blade.php`

Find the opening `<html>` tag with `x-data`:
```html
<html lang="en" class="scroll-smooth" x-data="{ dark: localStorage.getItem('dark') === 'true', showLoginModal: false, showRegisterModal: false, showForgotPasswordModal: false }" ...>
```

Replace with:
```html
<html lang="en" class="scroll-smooth" x-data="{
    dark: localStorage.getItem('dark') === 'true',
    showLoginModal: {{ ($errors->hasBag('default') || $errors->has('email') || $errors->has('password')) && !$errors->has('name') ? 'true' : 'false' }},
    showRegisterModal: {{ $errors->has('name') || $errors->has('student_number') || $errors->has('password_confirmation') ? 'true' : 'false' }},
    showForgotPasswordModal: false
}" x-init="$watch('dark', v => { localStorage.setItem('dark', v); document.documentElement.classList.toggle('dark', v) }); document.documentElement.classList.toggle('dark', dark)" :class="{ 'dark': dark }">
```

**Logic:**
- If errors contain `email` or `password` (but NOT `name`) → login modal was the source → reopen `showLoginModal`
- If errors contain `name`, `student_number`, or `password_confirmation` → register modal was the source → reopen `showRegisterModal`

---

## 🔴 BUG 3 — Registration Redirects to Blank Onboarding Page

**Root cause:** After registration, `RegisteredUserController` calls `redirect(route('onboarding.show'))`, which loads a new page using `x-guest-layout` — a blank whitish card with no context about what StudHub is or that the user just registered.

**Two-part fix:**

### Part A — Make the onboarding page feel branded

**File:** `resources/views/onboarding/show.blade.php` — REPLACE ENTIRE FILE:

```blade
<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-14 h-14 flex items-center justify-center mx-auto mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" fill="none" class="w-14 h-14">
                <defs>
                    <linearGradient id="obG" x1="0" y1="0" x2="1" y2="1">
                        <stop offset="0%" stop-color="#FF6B35"/>
                        <stop offset="100%" stop-color="#C94B15"/>
                    </linearGradient>
                    <linearGradient id="obA" x1="0" y1="1" x2="1" y2="0">
                        <stop offset="0%" stop-color="#FFB347"/>
                        <stop offset="100%" stop-color="#FF8C5A"/>
                    </linearGradient>
                </defs>
                <path d="M100 18 C68 18 38 30 24 52 C10 74 10 100 10 100 C10 132 12 152 30 166 C48 180 72 182 100 182 C128 182 152 180 170 166 C188 152 190 132 190 100 C190 100 190 74 176 52 C162 30 132 18 100 18Z" fill="url(#obG)"/>
                <path d="M100 52 C88 54 66 62 54 74 L54 148 C66 138 88 132 100 132 Z" fill="white" opacity="0.16"/>
                <path d="M100 52 C112 54 134 62 146 74 L146 148 C134 138 112 132 100 132 Z" fill="white" opacity="0.10"/>
                <line x1="100" y1="52" x2="100" y2="148" stroke="white" stroke-width="2" opacity="0.28" stroke-linecap="round"/>
                <rect x="72" y="94" width="56" height="8" rx="4" fill="white" opacity="0.95"/>
                <path d="M100 70 L118 91 L100 99 L82 91 Z" fill="white" opacity="0.95"/>
                <line x1="116" y1="97" x2="116" y2="122" stroke="url(#obA)" stroke-width="3" stroke-linecap="round"/>
                <circle cx="116" cy="126" r="4.5" fill="url(#obA)"/>
            </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Welcome to StudHub! 🎓</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            One last step — tell us about yourself so we can connect you with the right resources and classmates.
        </p>
    </div>

    {{-- Step indicator --}}
    <div class="flex items-center justify-center gap-2 mb-6">
        <div class="w-6 h-6 rounded-full bg-emerald-500 text-white text-xs font-bold flex items-center justify-center">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div class="h-0.5 w-8 bg-emerald-200 dark:bg-emerald-800"></div>
        <div class="w-6 h-6 rounded-full bg-seait-500 text-white text-xs font-bold flex items-center justify-center">2</div>
        <div class="h-0.5 w-8 bg-gray-200 dark:bg-navy-700"></div>
        <div class="w-6 h-6 rounded-full bg-gray-200 dark:bg-navy-700 text-gray-500 dark:text-gray-400 text-xs font-bold flex items-center justify-center">3</div>
    </div>

    <form method="POST" action="{{ route('onboarding.store') }}" class="space-y-4">
        @csrf

        <div>
            <label for="display_name" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                Display name <span class="text-gray-400 font-normal">(how classmates will see you)</span>
            </label>
            <input id="display_name" type="text" name="display_name"
                   value="{{ old('display_name', auth()->user()?->name) }}"
                   class="block w-full input-field" required autofocus
                   placeholder="e.g. Juan D. or JuanC2024">
            @error('display_name')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="program_id" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Degree program</label>
            <select id="program_id" name="program_id" required class="block w-full input-field">
                <option value="">Select your program…</option>
                @foreach ($programs as $program)
                    <option value="{{ $program->id }}" @selected(old('program_id') == $program->id)>
                        {{ $program->code }} — {{ $program->name }} ({{ $program->college?->code }})
                    </option>
                @endforeach
            </select>
            @error('program_id')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="year_level" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Year level</label>
            <select id="year_level" name="year_level" required class="block w-full input-field">
                <option value="">Pick a year…</option>
                @for ($y = $yearMin; $y <= $yearMax; $y++)
                    <option value="{{ $y }}" @selected(old('year_level') == $y)>Year {{ $y }}</option>
                @endfor
            </select>
            @error('year_level')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="btn-primary w-full !py-2.5 mt-2">
            Finish setup → Go to Dashboard
        </button>
    </form>
</x-guest-layout>
```

### Part B — Update `layouts/guest.blade.php` logo

Find line 97:
```html
<x-application-logo class="w-20 h-20 fill-current text-gray-500 dark:text-gray-300" />
```
Replace with:
```html
<x-application-logo class="w-16 h-16" />
```

---

## 🔴 BUG 4 — Email Domain Too Restrictive

**Root cause:** `config/studhub.php` defaults `STUDHUB_ALLOWED_EMAIL_DOMAINS` to `seait.edu.ph,students.seait.edu.ph`. Any other email gets rejected. The rule already handles empty list = accept all.

**File:** `config/studhub.php`

Find:
```php
'allowed_email_domains' => env('STUDHUB_ALLOWED_EMAIL_DOMAINS', 'seait.edu.ph,students.seait.edu.ph'),
```

Change the default to empty so any email is accepted unless the environment variable is set:
```php
'allowed_email_domains' => env('STUDHUB_ALLOWED_EMAIL_DOMAINS', ''),
```

Also in `.env` (if it has the key), set:
```
STUDHUB_ALLOWED_EMAIL_DOMAINS=
```

---

## 🔴 BUG 5 — Feedback Type "feedback" Fails Validation Silently

**Root cause:** `FeedbackController::store()` validates `type` as `'in:bug,feature,praise,other'`. The default selected type is `"feedback"`, which is NOT in that list. Passing `nullable` means Laravel allows null, but the value `"feedback"` is a non-null string that fails the `in:` rule — so the submission is rejected. Bug type, feature, praise, other all pass because they ARE in the list.

**File:** `app/Http/Controllers/FeedbackController.php`

Find:
```php
'type' => ['nullable', 'string', 'in:bug,feature,praise,other'],
```

Replace with:
```php
'type' => ['required', 'string', 'in:feedback,bug,feature,praise,other'],
```

---

## 🔴 BUG 6 — Resource Upload Silently Fails

This has TWO causes — both must be fixed.

### Cause A — Missing `public/storage` symlink

The Livewire component stores files to `storage/app/public/resources/` via `Storage::disk('public')`. But the web server can only serve files from `public/`. Without the symlink `public/storage → storage/app/public`, uploaded files are stored but never accessible, and the `CreateResource` action may throw silently.

**Fix — run this command once on the server:**
```bash
php artisan storage:link
```

This creates `public/storage` → `storage/app/public`. After running it, file URLs like `/storage/resources/file.pdf` will work.

### Cause B — Alpine→Livewire subject_id sync broken

The subject autocomplete uses Alpine.js to set `document.getElementById('subject_id_hidden').value = subject.id`, but this DOM property assignment does NOT trigger Livewire's event listener. Livewire only updates `wire:model` bindings when it receives a native `input` or `change` event on the element.

**File:** `resources/views/livewire/resources/form.blade.php`

Find the `pick(subject)` function inside the `subjectAutocomplete2()` Alpine component:
```javascript
pick(subject) {
    this.selectedId = subject.id;
    this.selectedLabel = subject.code + ' — ' + subject.name;
    this.query = '';
    this.open = false;
    setTimeout(() => { this.$el.closest('form').dispatchEvent(new Event('input', { bubbles: true })); }, 0);
},
```

Replace with:
```javascript
pick(subject) {
    this.selectedId = subject.id;
    this.selectedLabel = subject.code + ' — ' + subject.name;
    this.query = '';
    this.open = false;
    // Notify Livewire about the subject_id change
    this.$nextTick(() => {
        const hidden = document.getElementById('subject_id_hidden');
        if (hidden) {
            hidden.value = subject.id;
            hidden.dispatchEvent(new Event('input', { bubbles: true }));
            hidden.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
},
```

Also update the `clear()` function the same way:
```javascript
clear() {
    this.selectedId = '';
    this.selectedLabel = '';
    this.query = '';
    this.$nextTick(() => {
        const hidden = document.getElementById('subject_id_hidden');
        if (hidden) {
            hidden.value = '';
            hidden.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });
}
```

---

## 🔴 BUG 7 — Chat Messages: Wrong Alignment & No Own-Message Bubble

**Root cause A (messages appear to "go up"):** The component fetches `.latest()` (DESC) then `.reverse()` (ASC), which is correct ordering. The issue is `wire:poll.10s.visible` re-renders the whole component. On re-render, Alpine's `x-init` does NOT re-run (it only runs once on mount). The `MutationObserver` should catch new DOM nodes added by the poll, but if Livewire's morphing replaces rather than appends nodes, the observer may miss it. The fix: use `@this.hook('updated', ...)` in Alpine to scroll after every Livewire update.

**Root cause B (own messages on left):** All messages are rendered identically regardless of sender. Own messages should be right-aligned with a distinct bubble colour, and the avatar should move to the right.

**File:** `resources/views/livewire/chat/room-conversation.blade.php` — REPLACE ENTIRE FILE:

```blade
<div class="flex flex-col h-full" wire:poll.10s.visible>

    {{-- Messages --}}
    <div id="chat-log"
         role="log"
         aria-live="polite"
         data-testid="chat-message-list"
         x-data="chatLog()"
         x-init="init()"
         class="flex-1 overflow-y-auto px-4 py-4 space-y-0.5 bg-gray-50/40 dark:bg-navy-900/20 scroll-smooth">

        @php
            $colors = ['from-violet-400 to-violet-600','from-sky-400 to-sky-600','from-emerald-400 to-emerald-600','from-rose-400 to-rose-600','from-amber-400 to-amber-600','from-fuchsia-400 to-fuchsia-600','from-cyan-400 to-cyan-600','from-teal-400 to-teal-600'];
            $messages = $this->roomMessages;
        @endphp

        @forelse ($messages as $index => $message)
            @php
                $isOwn      = $message->sender_id === Auth::id();
                $isAdmin    = $message->sender?->isAdmin();
                $isMod      = $message->sender?->isModerator();
                $prev       = $messages[$index - 1] ?? null;
                $showHeader = !$prev || $prev->sender_id !== $message->sender_id
                    || $message->created_at->diffInMinutes($prev->created_at) > 3;
                $colorClass = $colors[$message->sender_id % count($colors)];
                $initials   = strtoupper(substr($message->sender?->preferredDisplayName() ?? '?', 0, 2));
            @endphp

            <article data-testid="chat-message"
                     wire:key="message-{{ $message->id }}"
                     class="{{ $showHeader ? 'mt-4 first:mt-0' : 'mt-0.5' }} group">

                @if ($isOwn)
                    {{-- ===== OWN MESSAGE (right-aligned) ===== --}}
                    @if ($showHeader)
                        <div class="flex flex-col items-end mb-1">
                            <span class="text-[11px] text-gray-400 dark:text-gray-500 mr-1">You · {{ $message->created_at?->diffForHumans() }}</span>
                        </div>
                    @endif
                    <div class="flex justify-end">
                        <div class="max-w-[75%]">
                            <div class="bg-seait-500 dark:bg-seait-600 text-white text-sm px-4 py-2.5 rounded-2xl rounded-tr-md shadow-sm">
                                <p class="whitespace-pre-wrap leading-relaxed break-words">{{ $message->body }}</p>
                            </div>
                            @if ($message->hasAttachment())
                                <a href="{{ $message->attachment_url }}" target="_blank" rel="noopener"
                                   class="mt-1.5 inline-flex items-center gap-1 text-xs text-seait-300 hover:text-seait-200 transition-colors float-right">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                    Attachment
                                </a>
                            @endif
                        </div>
                    </div>

                @else
                    {{-- ===== OTHER'S MESSAGE (left-aligned) ===== --}}
                    @if ($showHeader)
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-gradient-to-br {{ $colorClass }} flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                {{ $initials }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                        {{ $message->sender?->preferredDisplayName() ?? 'Unknown' }}
                                    </span>
                                    @if ($isAdmin)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 uppercase tracking-wide">Admin</span>
                                    @elseif ($isMod)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 uppercase tracking-wide">Mod</span>
                                    @elseif ($message->sender?->program?->code)
                                        <span class="badge-seait text-[10px]">{{ $message->sender->program->code }}{{ $message->sender->year_level ? ' Y'.$message->sender->year_level : '' }}</span>
                                    @endif
                                    <time class="text-[11px] text-gray-400 dark:text-gray-500"
                                          title="{{ $message->created_at?->format('M d, Y g:i A') }}">
                                        {{ $message->created_at?->diffForHumans() }}
                                    </time>
                                </div>
                                <div class="max-w-[75%] bg-white dark:bg-navy-800 border border-gray-100 dark:border-navy-700/50 rounded-2xl rounded-tl-md px-4 py-2.5 shadow-sm text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed break-words">
                                    {{ $message->body }}
                                </div>
                                @if ($message->hasAttachment())
                                    <a href="{{ $message->attachment_url }}" target="_blank" rel="noopener"
                                       class="mt-1.5 inline-flex items-center gap-1.5 text-xs text-seait-600 bg-seait-50 hover:bg-seait-100 px-2.5 py-1 rounded-lg dark:text-seait-400 dark:bg-seait-900/20 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                        Attachment
                                    </a>
                                @endif
                            </div>
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0 mt-0.5">
                                <button type="button" onclick="document.getElementById('rf-{{ $message->id }}').classList.toggle('hidden')"
                                        class="p-1 rounded text-gray-300 hover:text-red-400 dark:text-gray-600 dark:hover:text-red-400 transition-colors" title="Report">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                                </button>
                            </div>
                        </div>
                    @else
                        {{-- Compact continuation --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 flex-shrink-0 flex items-center justify-center">
                                <time class="text-[10px] text-gray-300 dark:text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity">{{ $message->created_at?->format('g:i') }}</time>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="max-w-[75%] bg-white dark:bg-navy-800 border border-gray-100 dark:border-navy-700/50 rounded-2xl px-4 py-2.5 shadow-sm text-sm text-gray-800 dark:text-gray-200 whitespace-pre-wrap leading-relaxed break-words">
                                    {{ $message->body }}
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Report form --}}
                    <form id="rf-{{ $message->id }}" method="POST" action="{{ route('reports.store') }}"
                          class="hidden mt-2 ml-11 p-3 rounded-xl bg-red-50/70 dark:bg-red-900/10 border border-red-100 dark:border-red-800/20 space-y-2">
                        @csrf
                        <input type="hidden" name="reported_type" value="message">
                        <input type="hidden" name="reported_id" value="{{ $message->id }}">
                        <p class="text-xs font-semibold text-red-700 dark:text-red-400">Report message</p>
                        <select name="reason" required class="text-xs input-field w-full">
                            <option value="">Select reason…</option>
                            @foreach (\App\Domain\Moderation\Enums\ReportReason::cases() as $reason)
                                <option value="{{ $reason->value }}">{{ $reason->label() }}</option>
                            @endforeach
                        </select>
                        <div class="flex gap-2">
                            <input type="text" name="notes" maxlength="1000" placeholder="Optional note" class="text-xs input-field flex-1">
                            <button type="submit" onclick="this.disabled=true;this.form.submit();"
                                    class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white text-xs font-semibold rounded-lg transition-colors disabled:opacity-50">Submit</button>
                            <button type="button" onclick="this.closest('form').classList.add('hidden')"
                                    class="px-3 py-1.5 border border-gray-200 dark:border-navy-600 text-xs text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors">Cancel</button>
                        </div>
                    </form>
                @endif

            </article>
        @empty
            <div class="flex flex-col items-center justify-center h-full text-center py-16">
                <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-seait-100 to-seait-200 dark:from-seait-900/30 dark:to-seait-800/20 flex items-center justify-center mb-4">
                    <x-icon name="chat" class="w-10 h-10 text-seait-400 dark:text-seait-500" />
                </div>
                <p class="text-base font-semibold text-gray-700 dark:text-gray-300" data-testid="chat-empty-state">No messages yet</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Be the first to say something! 👋</p>
            </div>
        @endforelse

        {{-- Scroll sentinel --}}
        <div id="chat-bottom"></div>
    </div>

    {{-- Jump to bottom button --}}
    <div class="relative flex-shrink-0 pointer-events-none">
        <div id="scroll-btn-wrap" class="absolute -top-12 right-4 z-10 pointer-events-auto hidden">
            <button onclick="document.getElementById('chat-bottom').scrollIntoView({ behavior: 'smooth' })"
                    class="flex items-center gap-1.5 px-3 py-1.5 bg-seait-500 text-white text-xs font-medium rounded-full shadow-lg hover:bg-seait-600 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                Jump to latest
            </button>
        </div>
    </div>

    {{-- Input bar --}}
    <div class="flex-shrink-0 border-t border-gray-200/60 dark:border-navy-700/40 bg-white/80 dark:bg-navy-800/80 backdrop-blur-sm px-4 py-3">
        <form wire:submit="send" class="flex items-end gap-2">
            <label class="flex-shrink-0 cursor-pointer p-2.5 rounded-xl hover:bg-gray-100 dark:hover:bg-navy-700 transition-colors text-gray-400 hover:text-gray-600 border border-gray-200 dark:border-navy-700" title="Attach file">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                <input type="file" wire:model="attachment" accept="image/*,application/pdf" class="hidden">
            </label>
            <div class="flex-1 relative">
                <label for="chat-body" class="sr-only">Message</label>
                <textarea id="chat-body" wire:model="body" rows="1"
                          class="input-field resize-none w-full max-h-32 overflow-y-auto !py-2.5"
                          placeholder="Send a message… @name to mention"
                          @keydown.enter.prevent="if(!$event.shiftKey){$wire.send()}else{$event.target.value+='\n'}"
                          oninput="this.style.height='auto';this.style.height=Math.min(this.scrollHeight,128)+'px'"></textarea>
            </div>
            <button type="submit" wire:loading.attr="disabled"
                    class="flex-shrink-0 p-2.5 rounded-xl bg-seait-500 hover:bg-seait-600 text-white transition-colors disabled:opacity-50 shadow-sm">
                <x-icon wire:loading.remove.delay name="send" class="w-5 h-5" />
                <x-icon wire:loading name="spinner" class="w-5 h-5 animate-spin" />
            </button>
        </form>
        @error('body') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        @error('attachment') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        <p class="mt-1 text-[10px] text-gray-400 dark:text-gray-600">
            <kbd class="px-1 rounded bg-gray-100 dark:bg-navy-700 font-mono text-[9px]">Enter</kbd> send &nbsp;·&nbsp;
            <kbd class="px-1 rounded bg-gray-100 dark:bg-navy-700 font-mono text-[9px]">Shift+Enter</kbd> new line
        </p>
    </div>
</div>

@push('scripts')
<script>
function chatLog() {
    return {
        atBottom: true,
        init() {
            const log = this.$el;
            // Scroll to bottom on mount
            log.scrollTop = log.scrollHeight;

            // Track scroll position
            log.addEventListener('scroll', () => {
                this.atBottom = (log.scrollHeight - log.scrollTop - log.clientHeight) < 100;
                const btn = document.getElementById('scroll-btn-wrap');
                if (btn) btn.classList.toggle('hidden', this.atBottom);
            });

            // Re-scroll after every Livewire update (poll re-render)
            Livewire.hook('morph.updated', ({ el }) => {
                if (this.atBottom) {
                    this.$nextTick(() => {
                        log.scrollTop = log.scrollHeight;
                    });
                }
            });
        }
    }
}
</script>
@endpush
```

---

## 🟡 UX 1 — Flash Banners Cause Layout Shift

**Root cause:** Flash banners (`.flash-success`, `.flash-error`) render inside `<main>` in the document flow. When they fade out, the content below jumps upward. The logout banner is fixed-positioned (top-center) which is correct. All banners should match that behaviour.

**File:** `resources/views/layouts/app.blade.php`

**Step 1** — Wrap the entire flash messages section with a `fixed` container. Find the section that starts with `@if (session('status') || session('success'))` and ends before `<main>`. Replace the wrapper div:

Change from:
```html
@if (session('status') || session('success'))
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
```

To:
```html
{{-- Fixed top-center flash container — all banners float above content --}}
<div class="fixed top-20 left-1/2 -translate-x-1/2 z-[200] w-full max-w-md px-4 space-y-2 pointer-events-none">
@if (session('status') || session('success'))
    <div class="pointer-events-auto">
```

Then close the outer `<div class="fixed...">` after `@endif` for all flash types (after the `$errors->any()` block), just before `<main>`.

**Step 2** — Update `resources/css/app.css` `.flash-banner` to remove the `shadow-sm` and add a drop shadow more suitable for floating:

```css
  .flash-banner {
    @apply flex items-start gap-3 px-4 py-3 rounded-xl text-sm
           shadow-lg ring-1 ring-black/5
           transition-all duration-300;
  }
```

**Full replacement of the flash section in `app.blade.php`:**

```blade
{{-- ======= FLOATING FLASH MESSAGES (fixed, top-center, no layout shift) ======= --}}
<div class="fixed top-20 inset-x-0 z-[200] flex flex-col items-center gap-2 px-4 pointer-events-none">

    @if (session('status') || session('success'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="flash-success pointer-events-auto w-full max-w-md">
            <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="flex-1">{{ session('status') ?? session('success') }}</span>
            <button @click="show = false" class="ml-auto flex-shrink-0 text-emerald-500 hover:text-emerald-700 dark:hover:text-emerald-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 6000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="flash-error pointer-events-auto w-full max-w-md">
            <svg class="w-5 h-5 flex-shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="flex-1">{{ session('error') }}</span>
            <button @click="show = false" class="ml-auto flex-shrink-0 text-red-500 hover:text-red-700 dark:hover:text-red-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if (session('warning'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 7000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="flash-warning pointer-events-auto w-full max-w-md">
            <svg class="w-5 h-5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.054 0 1.502-1.275.722-1.845l-6.928-5.013c-.752-.545-1.792-.545-2.544 0L5.094 17.155c-.78.57-.332 1.845.722 1.845z"/></svg>
            <span class="flex-1">{{ session('warning') }}</span>
            <button @click="show = false" class="ml-auto flex-shrink-0 text-amber-500 hover:text-amber-700 dark:hover:text-amber-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if (session('info'))
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 5000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="flash-info pointer-events-auto w-full max-w-md">
            <svg class="w-5 h-5 flex-shrink-0 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="flex-1">{{ session('info') }}</span>
            <button @click="show = false" class="ml-auto flex-shrink-0 text-blue-500 hover:text-blue-700 dark:hover:text-blue-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div x-data="{ show: true }" x-show="show"
             x-init="setTimeout(() => show = false, 10000)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="flash-error pointer-events-auto w-full max-w-md">
            <svg class="w-5 h-5 flex-shrink-0 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <ul class="flex-1 list-disc list-inside space-y-0.5 text-xs">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button @click="show = false" class="ml-auto flex-shrink-0 text-red-500 hover:text-red-700 dark:hover:text-red-300">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

</div>
{{-- ======= END FLASH MESSAGES ======= --}}
```

---

## 🟡 UX 2 — Email Verification Page: Doesn't Show Which Email

**File:** `resources/views/auth/verify-email.blade.php` — REPLACE ENTIRE FILE:

```blade
<x-guest-layout>
    <div class="text-center mb-6">
        <div class="w-14 h-14 rounded-2xl bg-seait-100 dark:bg-seait-900/30 flex items-center justify-center mx-auto mb-3">
            <svg class="w-7 h-7 text-seait-600 dark:text-seait-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
        </div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Verify your email</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Almost there! Check your inbox to activate your account.</p>
    </div>

    <div class="bg-gray-50 dark:bg-navy-700/40 rounded-xl px-4 py-3 mb-5 text-center">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">We sent a verification link to</p>
        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 break-all">{{ auth()->user()?->email }}</p>
    </div>

    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mb-5">
        Can't find it? Check your <strong>spam/junk folder</strong>. The link expires after 60 minutes.
    </p>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 px-4 py-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/50 text-sm text-emerald-700 dark:text-emerald-300 text-center">
            ✓ A new verification link was sent to your email.
        </div>
    @endif

    <div class="flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="btn-primary w-full">
                Resend verification email
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-ghost w-full text-sm text-gray-500 dark:text-gray-400">
                Use a different account
            </button>
        </form>
    </div>
</x-guest-layout>
```

---

## 🟡 UX 3 — Remove "Notif. Settings" from Nav Dropdown

"Notification Settings" is already accessible from the notification bell dropdown (Settings link at the bottom). Having it in the profile dropdown too is cluttering.

**File:** `resources/views/layouts/navigation.blade.php`

Find and DELETE these four lines (desktop dropdown):
```blade
<x-dropdown-link :href="route('profile.notification-preferences')">
    <x-icon name="settings" class="w-4 h-4 mr-2 flex-shrink-0" />
    Notif. Settings
</x-dropdown-link>
```

Also find and DELETE the mobile equivalent:
```blade
<x-responsive-nav-link :href="route('profile.notification-preferences')">Notif. Settings</x-responsive-nav-link>
```

---

## 🟡 UX 4 — Admin: Replace "Feedback" with "View Feedback"

Admins should see and manage feedback, not submit it. Students submit; admin reads.

### Step 1 — Add admin feedback route

**File:** `routes/web.php`

Inside the admin middleware group, add after the last admin route:
```php
Route::get('/admin/feedback', [AdminController::class, 'feedback'])->name('admin.feedback');
```

### Step 2 — Add `feedback()` method to AdminController

**File:** `app/Http/Controllers/AdminController.php`

Add this method before the closing `}`:
```php
public function feedback(HttpRequest $httpRequest): View
{
    $user = $httpRequest->user();
    abort_unless($user?->isAdmin(), 403);

    $feedbacks = \App\Models\Feedback::with('user:id,display_name,name,email')
        ->latest()
        ->paginate(25);

    return view('admin.feedback', ['feedbacks' => $feedbacks]);
}
```

### Step 3 — Create admin feedback view

**New file:** `resources/views/admin/feedback.blade.php`

```blade
<x-app-layout>
    <x-page-header title="Feedback Inbox" />

    <div class="py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="card overflow-hidden">
                @if ($feedbacks->isEmpty())
                    <div class="p-12 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-gray-100 dark:bg-navy-700/50 flex items-center justify-center mx-auto mb-3">
                            <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">No feedback yet</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-navy-700/50">
                        @foreach ($feedbacks as $fb)
                            @php
                                $typeStyle = match($fb->type) {
                                    'bug'     => 'bg-red-50 text-red-700 dark:bg-red-900/20 dark:text-red-300',
                                    'feature' => 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300',
                                    'praise'  => 'bg-amber-50 text-amber-700 dark:bg-amber-900/20 dark:text-amber-300',
                                    default   => 'bg-gray-100 text-gray-700 dark:bg-navy-700 dark:text-gray-300',
                                };
                                $typeEmoji = match($fb->type) {
                                    'bug' => '🐛', 'feature' => '💡', 'praise' => '🌟', default => '💬',
                                };
                            @endphp
                            <div class="px-6 py-5">
                                <div class="flex items-start justify-between gap-4 mb-2">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $fb->user?->display_name ?: $fb->user?->name ?: 'Anonymous' }}
                                        </span>
                                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $fb->user?->email }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $typeStyle }}">
                                            {{ $typeEmoji }} {{ ucfirst($fb->type ?? 'feedback') }}
                                        </span>
                                        <time class="text-xs text-gray-400 dark:text-gray-500">{{ $fb->created_at->diffForHumans() }}</time>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $fb->body }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-navy-700/50">
                        {{ $feedbacks->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
```

### Step 4 — Fix nav: admin sees "View Feedback", not "Submit Feedback"

**File:** `resources/views/layouts/navigation.blade.php`

Find the dropdown feedback link (appears for all users):
```blade
<x-dropdown-link :href="route('feedback.create')">
    <x-icon name="feedback" class="w-4 h-4 mr-2 flex-shrink-0" />
    Feedback
</x-dropdown-link>
```

Replace with:
```blade
@if (Auth::user()?->isAdmin())
    <x-dropdown-link :href="route('admin.feedback')">
        <x-icon name="feedback" class="w-4 h-4 mr-2 flex-shrink-0" />
        View Feedback
    </x-dropdown-link>
@else
    <x-dropdown-link :href="route('feedback.create')">
        <x-icon name="feedback" class="w-4 h-4 mr-2 flex-shrink-0" />
        Feedback
    </x-dropdown-link>
@endif
```

Do the same for the mobile responsive nav links section.

---

## SUMMARY TABLE

| # | Severity | File(s) | Fix |
|---|----------|---------|-----|
| 1 | 🔴 Bug | `resources/css/app.css` | `-webkit-autofill` CSS — fixes Chrome invisible text |
| 2 | 🔴 Bug | `resources/views/welcome.blade.php` | PHP sets `showLoginModal`/`showRegisterModal` initial state from `$errors` — modal reopens on validation fail |
| 3 | 🔴 UX | `resources/views/onboarding/show.blade.php` | Branded onboarding page with logo, step indicator, proper labels |
| 3b | 🔴 UX | `resources/views/layouts/guest.blade.php` | Remove `fill-current text-gray-500` from logo |
| 4 | 🔴 Bug | `config/studhub.php` + `.env` | Remove default email domain restriction |
| 5 | 🔴 Bug | `app/Http/Controllers/FeedbackController.php` | Add `'feedback'` to `in:` validation list |
| 6A | 🔴 Bug | Server (one-time command) | Run `php artisan storage:link` to create `public/storage` symlink |
| 6B | 🔴 Bug | `resources/views/livewire/resources/form.blade.php` | Dispatch `input`/`change` events on hidden input after Alpine picks subject |
| 7 | 🔴 Bug | `resources/views/livewire/chat/room-conversation.blade.php` | Own messages right-aligned (bubble style), others left-aligned; Admin/Mod role badges; proper auto-scroll via Livewire hook |
| 8 | 🟡 UX | `resources/views/layouts/app.blade.php` + `resources/css/app.css` | All flash banners `fixed top-20 center` — no layout shift |
| 9 | 🟡 UX | `resources/views/auth/verify-email.blade.php` | Show the user's actual email address, improve copy and layout |
| 10 | 🟡 UX | `resources/views/layouts/navigation.blade.php` | Remove redundant "Notif. Settings" from dropdown |
| 11 | 🟡 UX | `routes/web.php` + `AdminController` + new `admin/feedback.blade.php` + `navigation.blade.php` | Admin gets "View Feedback" page; students only see "Submit Feedback" |

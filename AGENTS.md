# StudHub — AI Agent Guide

## Project Overview

**StudHub** is a Laravel 11 cross-program academic resource exchange platform for SEAIT (South East Asian Institute of Technology, Inc.). Students across 6 colleges and 26 programs share reviewers, textbooks, e-modules, and past exams.

## Quick Start for Agents

```powershell
# Run tests (uses SQLite in-memory)
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pest

# Run filtered tests
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pest --filter="Request"

# Dev server (no MySQL needed for tests)
$env:Path = "C:\xampp\php;$env:Path"; php artisan serve
```

## Tech Stack

- **PHP:** 8.2.12 at `C:\xampp\php\php.exe`
- **Composer:** `C:\xampp\php\composer`
- **Framework:** Laravel 11 (composer.json relaxed to `^8.2`)
- **Testing:** Pest PHP, SQLite in-memory
- **Production DB:** MySQL

## Architecture Conventions (MUST FOLLOW)

### Directory Structure
```
app/Domain/{Domain}/
├── Actions/       # Stateless action classes with handle() method
├── Enums/         # PHP 8.1+ backed string enums
├── Jobs/          # Queued jobs (ShouldQueue + Queueable)
├── Events/        # Laravel broadcast events
├── Notifications/ # Laravel notifications
└── Rules/         # Custom validation rules
```

### Action Pattern
- Single `handle()` method entry point
- Model parameters injected, validation via RuntimeException
- `DB::transaction()` for writes
- Stateless — instantiated via `new` or Laravel DI

### Controller Pattern
- Actions injected via method parameter or instantiated inline
- School-scoped authorization (`school_id` check → 404)
- `abort_unless($user !== null, 403)` guard at method top
- `session()->flash('status', ...)` for success messages

### Model Pattern
- `HasFactory` trait
- Full PHPDoc `@return` with generic template params
- Domain enum casting (e.g., `'status' => RequestStatus::class`)
- Custom `is{State}()` helper methods

### Test Pattern
- `beforeEach()` seeds SEAIT seeders (School, Colleges, Programs, Subjects)
- `User::factory()->onboarded()->create()` for test users
- `Bus::fake()` for job assertions
- `$this->actingAs($user)->get/post(route(...))` for HTTP

## Active Skills (load when relevant)

| Skill | When to Use |
|-------|-------------|
| `api-endpoint-builder` | Building routes, controllers, validation |
| `bug-hunter` | Debugging test failures, edge cases |
| `brooks-lint` | Code review after building features |
| `codebase-audit-pre-push` | Pre-push audit |
| `testing-studhub-chat` | Testing chat functionality |

## Current Status (Week 11 In Progress)

- **Tests:** 186 passed, 459 assertions
- **Weeks 0-11:** All complete, all audit findings from `planning/audit-final-2026-05-18.md` applied
  - F1: `historicalFulfillmentRate()` implemented
  - F2: N+1 eliminated in `pickUsersToNotify()`
  - F3: `User::isSuspended()` uses `->isFuture()`
  - F4: Moderation dashboard filters via SQL
  - F5: Message snapshot before hide in audit log
  - F6: Self-report guard
  - F7: Suspended users blocked from chat channels
  - F8: Thumbnails saved as `.svg`
  - F9: Atomic karma increment
  - F10: `ExpireRequests` scheduled command
  - F16: `App\Models\Request` renamed to `ResourceRequest` (backward-compat alias kept)$
  - W9-1: Record-as-Lend form on request show page
  - W9-2: Report button on chat messages
  - W9-3: Report button on user profiles
  - W9-6: Navigation links conditional on role
- **PHPStan:** Level 6 clean
- **Next:** Week 12 — Streaming search, email digest, admin analytics
- **Handoff doc:** `planning/session-handoff-2026-05-17.md`
- **Handoff doc:** `planning/session-handoff-2026-05-17.md`

## Key Commands

```powershell
# Tests
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pest

# Specific test file
$env:Path = "C:\xampp\php;$env:Path"; php vendor/bin/pest tests/Feature/Reputation/KarmaTest.php

# Migration status (needs MySQL running)
php artisan migrate:status

# Dev server
php artisan serve
```
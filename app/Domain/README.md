# `app/Domain/` — Domain modules

This directory holds StudHub's **domain-driven module boundaries**.
Each subdirectory is one bounded context from
[`docs/02-architecture.md`](../../docs/02-architecture.md):

| Module | Owns |
| --- | --- |
| `Identity/` | `users`, `schools`, `programs`, sign-up, sessions, profile, role checks |
| `Chat/` | `chat_rooms`, `chat_messages`, real-time broadcasting, message attachments |
| `Catalog/` | `subjects`, `subject_aliases`, `program_subjects`, `resources`, `shelves`, `shelf_items` |
| `Requests/` | `requests`, `request_routes`, `offers`, `lends`, the routing engine |
| `Reputation/` | `karma_events`, badges, leaderboards |
| `Moderation/` | `reports`, suspensions, audit log |

Each domain module is internally free to use whatever sub-folders make
sense (`Models/`, `Actions/`, `Jobs/`, `Events/`, `Listeners/`,
`Policies/`, etc.) — but it **must not** reach into another module's
internals. Inter-module communication goes through:

- **Domain events** (Laravel events) for fire-and-forget signals.
- **Action classes** exposed as the public surface of the module.
- **HTTP / Livewire components** in `app/Http/` / `app/Livewire/`
  that compose multiple modules.

Models that don't fit cleanly in a single module stay in
`app/Models/` for now (e.g. the default Laravel `User`). The first
real migration round in Week 2 will start moving things into
`app/Domain/Identity/Models/`.

> **Rule of thumb:** if you're tempted to import a class from
> another module's `Models/` directly, route the call through that
> module's `Actions/` instead.

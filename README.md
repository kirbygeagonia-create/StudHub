# StudHub

> A school-wide chat + cross-program resource exchange.
> Each degree program gets its own group chat, and a smart resource layer lets
> students discover, request, and lend textbooks, e-modules, reviewers, and
> past exams **across programs** — not just inside their own bubble.

This repository currently holds the **planning and design phase** of the
project. Code will follow once the design is locked in.

---

## What problem does StudHub solve?

Today, when a 2nd-year ECE student needs the Discrete Math reviewer that a
3rd-year CS student already has, they:

1. Post in a random Facebook group and hope.
2. Scroll Messenger chat history.
3. Or just give up and re-make the reviewer from scratch.

There is no per-school directory of *who has what*, no easy way to find a
senior in another program who already took the same subject, and no way for
the institution to see what learning resources are actually in demand.

StudHub fixes this by combining three things:

1. **Per-program group chats** for everyday coordination.
2. **A structured resource catalog** (textbooks, e-modules, reviewers,
   past exams, lab manuals, thesis samples) tagged by **subject**, not by
   program.
3. **A request board** where a request from one program is **auto-routed**
   to the other programs that historically took the same subject.

---

## Status

**Planning phase.** No application code yet — just design documents,
schema sketches, and a build roadmap.

| Doc | Purpose |
| --- | --- |
| [docs/00-product-overview.md](docs/00-product-overview.md) | Vision, problem, users, success criteria |
| [docs/01-mvp-spec.md](docs/01-mvp-spec.md) | MVP features and user stories |
| [docs/02-architecture.md](docs/02-architecture.md) | Tech stack and system design |
| [docs/03-database-schema.md](docs/03-database-schema.md) | Tables, relationships, key fields |
| [docs/04-request-routing.md](docs/04-request-routing.md) | Cross-program auto-routing algorithm |
| [docs/05-roadmap.md](docs/05-roadmap.md) | Week-by-week build plan |
| [docs/06-glossary.md](docs/06-glossary.md) | Shared vocabulary |
| [planning/checklist.md](planning/checklist.md) | Working checklist for the team |
| [planning/risks.md](planning/risks.md) | Known risks and mitigations |

---

## Project at a glance

- **Stack (planned):** PHP 8.3, Laravel 11, Laravel Reverb (real-time),
  MySQL/MariaDB, Livewire + Alpine.js + Tailwind, S3-compatible storage.
- **Audience:** Students and faculty of a single school (multi-tenant
  comes later, if ever).
- **Primary innovation:** Cross-program resource routing via a subject
  graph — *not* "yet another chat app."

---

## License

To be decided before any code is committed.

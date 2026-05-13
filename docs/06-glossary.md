# 06 — Glossary

Shared vocabulary for the team. If a term is ambiguous in the docs,
add it here and link to it.

| Term | Definition |
| --- | --- |
| **StudHub** | The product. A school-internal chat + cross-program resource exchange. |
| **Program** | A degree program (e.g. BSCS, BSIT, BSECE). One per row in `programs`. |
| **Year level** | Student's year within a program (1–5). |
| **Program chat** | The single group chat for an entire program. |
| **Year sub-channel** | A sub-room within a program chat scoped to one year level. |
| **Subject** | A canonical academic subject (e.g. "Data Structures and Algorithms"). The **spine** of resource discovery. |
| **Subject alias** | An alternative name for a subject ("DSA", "Algorithms", "CS 211"). |
| **Curriculum graph** | The `program_subjects` table — which programs offer which subjects, with weights. |
| **Resource** | A structured shareable item: textbook, e-module, reviewer, past exam, lab manual, thesis sample, lecture notes. |
| **Shelf** | A user's personal saved-for-later collection of resources. |
| **Request** | A structured "I need X" record posted to the request board. |
| **Offer** | A response to a request; optionally tied to a specific resource. |
| **Lend** | A recorded physical handover with a return-by date. |
| **Routing engine** | The algorithm that decides which programs and users see a request. See `04-request-routing.md`. |
| **Routing score** | A program-level score in [0, 1] produced by the routing engine. |
| **Karma** | A user's reputation, derived from a ledger of `karma_events`. |
| **Badge** | A visual tier (Bronze / Silver / Gold) earned at karma thresholds. |
| **Moderator** | A student given moderation rights inside one or more programs. |
| **Admin** | A school-wide moderator with access to analytics + global actions. |
| **Pilot** | The initial controlled rollout to ~30 students across 3 programs. |
| **MVP** | The minimum lovable product per `01-mvp-spec.md`. Anything not listed there is out of scope for v1. |
| **PWA** | Progressive Web App — installable web app, our only "mobile" deliverable for v1. |

# 00 — Product Overview

## 1. Vision

> Make it as easy for a SEAIT student in one program to find learning
> resources from another program as it is to send a Messenger reply.

StudHub is a SEAIT-internal platform where every degree program has its
own group chat, and where students share academic resources (textbooks,
e-modules, reviewers, past exams, lab manuals, thesis samples) through a
single searchable catalog that crosses program **and college**
boundaries (CICT ↔ DCE ↔ CBGG ↔ CTE ↔ CAF ↔ CCJE).

The differentiator is **not** the chat — chat is table stakes. The
differentiator is the **subject-aware resource layer** that bridges
programs: a BSIT student's *Data Structures and Algorithms* notes
should automatically surface when a BSCE student searches for "DSA,"
and BSCE statics reviewers should reach BSIT students who take the
related GE math course.

## 2. Problem

| Pain | Today's workaround | Why it fails |
| --- | --- | --- |
| Need a reviewer / e-module a senior already has | Post in the program's FB group, ask in DMs | Information is buried; people in other SEAIT programs don't see it |
| Want to lend a textbook to anyone who needs it | Sell it / let it sit | No directory of borrowers |
| Need to find someone who took the same subject in another program | Ask classmates randomly | No mapping between program A's "DSA" and program B's "Data Structures and Algorithms" |
| As faculty / school admin, want to see which courses students struggle to find materials for | None | No signal exists |

## 3. Target users

### Primary
- **Undergraduate students of SEAIT**, segmented by college,
  program, and year level.

### Secondary
- **Class officers / SSC representatives** — moderate their
  program's group and curate resources.
- **Faculty** — optional viewing of the most-requested topics for
  their courses.
- **CICT department head** (as academic sponsor for v1) and **SEAIT
  admin** — anonymized analytics on resource demand.
- **Registrar / IT services** — only for the optional roster /
  SSO integration described in [07-seait-context.md](07-seait-context.md) §4.

## 4. Non-goals (for v1)

- Multi-school / multi-tenant deployment (single SEAIT instance).
- K–12 and TESDA programs (deferred to v1.5+).
- Public access (everything is behind SEAIT-roster auth).
- Direct payments / paid resources (SEAIT is tuition-free; no
  marketplace dynamic needed).
- A full LMS replacement — StudHub does **not** host lectures, grade
  students, or run classes; it complements SEAIT's existing blended
  modality (face-to-face / modular / LMS).
- Mobile apps. Web + PWA only.

## 5. Success criteria

A successful v1 means, after one semester of pilot use at SEAIT
(targeting BSIT × BSCE × BSBA-MM per
[07-seait-context.md](07-seait-context.md) §7):

1. ≥ 60% of pilot programs have at least one daily-active student in
   the chat.
2. ≥ 100 resources uploaded across the school.
3. ≥ 50 cross-program requests fulfilled (a request from program A
   answered by program B in a different SEAIT college).
4. Median time from request → first offered match < 24 hours.
5. Zero leaked copyrighted material incidents reaching the SEAIT
   admin / registrar.

## 6. Guiding principles

1. **Subject is the spine, not program.** Programs are how people are
   grouped; subjects are how resources are connected.
2. **Structure beats chat.** Anything important (resources, requests,
   announcements) is a structured record, not a chat message.
3. **Trust comes from identity.** Every account is tied to a verified
   school email and a known program/year.
4. **Reputation, not gamification for its own sake.** Karma exists to
   discourage leeching, not to turn the app into a game.
5. **Privacy by default.** Files are school-only unless explicitly
   shared more widely. PDFs are watermarked.
6. **One school at a time.** Don't optimize for multi-tenant until the
   single-school product is loved.

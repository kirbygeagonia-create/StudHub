# 00 — Product Overview

## 1. Vision

> Make it as easy for a student in one program to find learning resources
> from another program as it is to send a Messenger reply.

StudHub is a school-internal platform where every degree program has its
own group chat, and where students share academic resources (textbooks,
e-modules, reviewers, past exams, lab manuals, thesis samples) through a
single searchable catalog that crosses program boundaries.

The differentiator is **not** the chat — chat is table stakes. The
differentiator is the **subject-aware resource layer** that bridges
programs: a CS student's "Data Structures" notes should automatically
surface when an ECE student searches for "DSA."

## 2. Problem

| Pain | Today's workaround | Why it fails |
| --- | --- | --- |
| Need a reviewer / e-module a senior already has | Post on FB, ask in DMs | Information is buried; people in other programs don't see it |
| Want to lend a textbook to anyone who needs it | Sell it / let it sit | No directory of borrowers |
| Need to find someone who took the same subject in another program | Ask classmates randomly | No mapping between program A's "DSA" and program B's "Data Structures and Algorithms" |
| As faculty / school admin, want to see which courses students struggle to find materials for | None | No signal exists |

## 3. Target users

### Primary
- **Undergraduate students** of the school, segmented by program and year.

### Secondary
- **Student officers / class reps** — moderate their program's group and
  curate resources.
- **Faculty** — optional viewing of requested topics for their courses.
- **School admin** — anonymized analytics on resource demand.

## 4. Non-goals (for v1)

- Multi-school / multi-tenant deployment.
- Public access (everything is behind school-email auth).
- Direct payments / paid resources.
- A full LMS replacement — StudHub does **not** host lectures, grade
  students, or run classes.
- Mobile apps. Web + PWA only.

## 5. Success criteria

A successful v1 means, after one semester of pilot use within one school:

1. ≥ 60% of pilot programs have at least one daily-active student in the
   chat.
2. ≥ 100 resources uploaded across the school.
3. ≥ 50 cross-program requests fulfilled (a request from program A
   answered by program B).
4. Median time from request → first offered match < 24 hours.
5. Zero leaked copyrighted material incidents reaching the school admin.

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

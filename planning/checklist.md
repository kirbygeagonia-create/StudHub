# Working Checklist

A flat, week-agnostic checklist. Update freely — don't wait for a
formal review. Once a task is done, check it off; don't delete it
(history matters for the paper).

## Pre-build

- [ ] Get adviser / panel sign-off on `docs/00-product-overview.md`.
- [ ] Get the school's official list of degree programs (CSV).
- [ ] Get curriculum for each program (subjects + typical year).
- [ ] Decide email domain(s) allowed for sign-up.
- [ ] Decide hosting: school infra vs. external VPS.
- [ ] Choose a license (MIT? Custom school license?).
- [ ] Create a private staging server (or staging path) before
      starting Week 1.
- [ ] Confirm whether Google Workspace SSO is available at the school.

## Open architecture questions (from `02-architecture.md`)

- [ ] Single Laravel app vs. separate Reverb container?
- [ ] Sign-in via Google Workspace SSO in v1 or v1.5?
- [ ] Offline reading of e-modules — needed in v1?
- [ ] Production hosting location (compliance).

## Open schema questions (from `03-database-schema.md`)

- [ ] Should `users` carry `student_number`?
- [ ] Add `resource_revisions` in v1 or v1.1?
- [ ] Add `user_subjects` in v1 or v1.1?

## Open routing questions (from `04-request-routing.md`)

- [ ] Default values of `PROGRAM_THRESHOLD` and `CHAT_THRESHOLD`
      after pilot data?
- [ ] Should urgent cross-posts require moderator approval?
- [ ] Per-user notification cap — 3/day is a guess; tune in pilot.

## Cross-cutting

- [ ] Write a short "Acceptable Use Policy" before pilot.
- [ ] Write a "How to ask for resources" guide for students.
- [ ] Decide who owns moderation per program (class president by
      default?).
- [ ] Decide retention policy for chat (forever? 12 months?).

## Pilot prep

- [ ] Invite list (~30 users across 3 programs).
- [ ] Onboarding email template.
- [ ] Feedback form (Google Form is fine for v1).
- [ ] Weekly office hours during pilot.

# Working Checklist

A flat, week-agnostic checklist. Update freely — don't wait for a
formal review. Once a task is done, check it off; don't delete it
(history matters for the paper).

## Pre-build

- [ ] Get adviser / panel sign-off on `docs/00-product-overview.md`
      and `docs/07-seait-context.md`.
- [x] **Identify the school.** → SEAIT (Tupi, South Cotabato).
      Captured in `docs/07-seait-context.md`.
- [x] **Compile preliminary list of SEAIT degree programs.** → see
      `docs/07-seait-context.md` §3. **Still needs registrar sign-off**
      for naming / completeness.
- [ ] Get the official SEAIT curriculum (subjects + typical year level)
      for each program — will be used to seed `subjects`,
      `subject_aliases`, and `program_subjects`.
- [ ] **Auth strategy decision.** Options in `docs/07-seait-context.md`
      §4: (A) Google Sign-In + roster, (B) SEAIT portal SSO, (C)
      invite-only. Recommendation: start with A.
- [ ] Confirm SEAIT does **not** issue per-student `@seait.edu.ph`
      emails (the website only mentions Gmail for faculty). If they
      do, switch to domain-restricted email sign-up.
- [ ] Decide hosting: SEAIT infra vs. external VPS.
- [ ] Choose a license (MIT? Custom SEAIT license?).
- [ ] Create a private staging server (or staging path) before
      starting Week 1.
- [ ] Confirm whether SEAIT has a Google Workspace tenancy for
      students.

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

## SEAIT-specific approvals & contacts (from `07-seait-context.md`)

- [ ] Confirm a faculty / department head as the **academic sponsor**
      (default: CICT department head).
- [ ] Confirm contact at SEAIT **registrar's office** (for program /
      curriculum data and, optionally, the student email roster).
- [ ] Confirm contact at SEAIT **IT services** (for SSO / hosting
      options).
- [ ] Confirm contact for **Data Privacy** review (Data Privacy Act
      of 2012 in PH).
- [ ] Confirm whether the school wants its name / logo used in the
      UI; obtain branding assets if yes.
- [ ] Pilot program selection. Default per `07-seait-context.md` §7:
      BSIT × BSCE × BSBA-MM.

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

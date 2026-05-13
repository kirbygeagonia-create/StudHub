# Risks

Top risks for the project, with mitigations. Re-read this at the start
of every week.

## 1. Curriculum mapping is the moat

**Risk:** The whole point of cross-program routing rests on the
`program_subjects` table. If it's wrong, requests go to the wrong
programs and the "innovation" looks like spam.

**Mitigation:**
- Block out an hour weekly to refine seed data.
- Track unresolved subject aliases and review them weekly.
- During pilot, expose a "this routing was wrong" button on routed
  notifications and feed corrections back into weights.

## 2. Real-time chat eats time

**Risk:** Reverb / WebSockets are easy in theory and full of edge
cases in practice (reconnects, presence, scaling beyond one box).

**Mitigation:**
- Build with broadcasting from day one; don't bolt it on later.
- If staging Reverb is flaky in Week 3, fall back to **short
  polling** for chat (5–10 s) and keep Reverb only for notifications.
  Defer presence to v2.

## 3. Copyright complaints

**Risk:** Students upload copyrighted textbook PDFs; publisher or
school admin sends a complaint.

**Mitigation:**
- Watermark all PDFs with the downloader's identity + timestamp.
- Clear ToS forbidding pirated materials; require an "I own a copy"
  checkbox on upload.
- Moderator queue for resources with sensitive `type`
  (`textbook`, `e_module`).
- Easy takedown flow (admin can soft-delete + audit).

## 4. Privacy / data protection

**Risk:** Storing student PII (name, school email, year, program)
makes the app a target for misuse, and may have legal implications
(Data Privacy Act in PH, GDPR if any EU students).

**Mitigation:**
- Minimal PII: no phone numbers, no addresses.
- All files behind auth, signed URLs short-lived.
- Documented retention + deletion policy.
- Pilot only after a privacy notice is reviewed (adviser / school
  data protection officer).

## 5. Scope creep

**Risk:** "Wouldn't it be cool if…" features push MVP past the
semester. Classic capstone failure mode.

**Mitigation:**
- `docs/01-mvp-spec.md` §2 is the **non-negotiable** out-of-scope
  list. Any new feature request goes to "Post-MVP backlog" in
  `docs/05-roadmap.md`, not in flight.
- Adviser sign-off on the MVP spec; changes need explicit
  re-approval.

## 6. Solo developer bus factor

**Risk:** One person doing everything = one illness or family event
away from a missed semester.

**Mitigation:**
- Keep docs current. Anyone with read access should be able to
  understand the system from `docs/`.
- Use Conventional Commits + good PR descriptions.
- Push code at least twice a week; never let the local branch get
  far ahead of `main`.

## 7. Hosting / domain

**Risk:** App lives only on `localhost` until the panel demo, then
something breaks during the live demo because production was never
exercised.

**Mitigation:**
- Spin up staging in Week 1, not Week 11.
- Deploy on every merge to `main`.
- Practice the demo on staging, not localhost.

## 8. Adoption inside the pilot

**Risk:** App is technically working but nobody uses it because the
chat alternatives (Messenger, Discord, FB Groups) are already
"good enough."

**Mitigation:**
- Lead with the **resource layer**, not chat. The pitch to pilot
  users is "find materials from other programs," not "another chat
  app."
- Pre-seed each pilot program's chat with the moderator and a
  starter set of resources.
- Hold a 15-min in-person onboarding for each pilot cohort.

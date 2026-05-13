# 01 — MVP Spec

This document defines the minimum lovable product for StudHub. Anything
not listed here is explicitly out of scope for v1.

## 1. MVP scope (the "must ship" set)

### 1.1 Identity & onboarding
- Sign up with a school email (e.g. `@students.school.edu`). Domain is
  configured per deployment.
- Email verification required before any action.
- On first login, user selects:
  - **Program** (from a pre-seeded list)
  - **Year level** (1–5, configurable)
  - Display name + optional avatar
- Profile page shows: name, program, year, karma score, badges,
  resources uploaded, resources lent.

### 1.2 Group chats (per program)
- Each program has exactly one **program chat** (auto-created).
- Each year level inside a program has a **year sub-channel** (e.g.
  `BSCS / 3rd Year`).
- Real-time messages (text + image + file attachments up to 25 MB).
- Pinning messages (by moderators).
- Mentions: `@username`, `@program`, `@year`.

### 1.3 Resource catalog
A **resource** is a structured record, not a chat message.

Fields:
- `type` — one of: `textbook`, `e_module`, `reviewer`, `past_exam`,
  `lab_manual`, `thesis_sample`, `lecture_notes`, `other`.
- `title`
- `subject_id` (FK to a normalized subject)
- `course_code` (optional, free text — e.g. `CS 211`)
- `year_taken` (optional)
- `condition` (for physical items) — `like_new`, `good`, `worn`.
- `availability` — `available`, `lent_out`, `digital_only`,
  `archived`.
- `visibility` — `school`, `program_only`, `private_link`.
- `file` — optional PDF/image upload, watermarked on download.
- `description` — free text, up to 2,000 chars.
- `owner_user_id`

Operations:
- Create / edit / archive a resource.
- Search & filter by subject, type, program, year, availability.
- Save resources to a personal "shelf."

### 1.4 Request board
A **request** is a structured "I need X" record.

Fields:
- `subject_id` (required)
- `type_wanted` (same enum as resource `type`)
- `urgency` — `low`, `normal`, `urgent`.
- `needed_by` (optional date)
- `description` (free text)
- `status` — `open`, `matched`, `fulfilled`, `expired`,
  `withdrawn`.

Behavior:
- On creation, the **routing engine** (see
  [04-request-routing.md](04-request-routing.md)) decides which
  programs and which users to notify.
- Any user who owns a matching resource can offer it with one click.
- The requester picks an offer → both sides get a confirmation chat
  thread + a return-by reminder (for physical lends).

### 1.5 Karma & reputation
- +5 karma for uploading a resource that gets ≥ 1 save.
- +10 karma for fulfilling a request.
- +2 karma for answering a chat question marked "helpful."
- −5 karma for a confirmed report (spam, copyright, abuse) — after
  moderator review.
- Karma is displayed as a number + badge tiers (Bronze / Silver / Gold).

### 1.6 Moderation
- Each program has 1–3 student moderators (assigned by school admin).
- Moderators can pin, delete messages, archive resources, suspend a
  user from their program chat.
- School admin can promote/demote moderators and ban users globally.

### 1.7 Notifications
- In-app bell + optional email digest (daily by default).
- Triggered by: new chat mention, new offer on your request, new
  request matching a subject you've tagged as "I can help with."

### 1.8 Search
- Single global search bar.
- Searches resources, requests, and (later) chat messages.
- Filters: type, subject, program, year, availability.

## 2. Out of scope for v1

- DMs (one-on-one direct messages outside of request threads).
- Voice / video calls.
- Mobile native apps.
- Multi-school tenancy.
- Payments / tipping.
- Public sharing of resources outside the school.
- AI-generated summaries of resources.
- Plagiarism detection.

## 3. User stories

Format: *As a {role}, I want {action}, so that {outcome}.*

### Onboarding
- As a new student, I want to sign up using my school email so that I
  know everyone here is from my school.
- As a new student, I want to pick my program and year once so that I'm
  dropped into the right group chats automatically.

### Resource owner
- As a student with old reviewers, I want to upload them once with a
  subject tag so that students in any program can find them later.
- As a textbook owner, I want to mark a book as "lent out until
  Nov 30" so that I don't get spammed with requests for it.

### Resource seeker
- As a student looking for a Discrete Math module, I want to search by
  subject so that I find materials from CS, IT, **and** ECE in one
  result list.
- As a student in a hurry, I want to post a request with "needed by
  Friday" so that the right people see it before my exam.

### Cross-program bridge
- As a CS senior who took DSA, I want to be notified when an ECE
  sophomore asks for DSA materials so that I can offer mine in one
  click.
- As a moderator, I want to see which subjects in my program have the
  most unanswered requests so that I can rally classmates to help.

### Trust & safety
- As a user, I want every account to be tied to a verified school
  email so that I'm not negotiating with bots or strangers.
- As an uploader, I want my PDFs watermarked with the borrower's name
  so that copies don't spread anonymously.

### School admin
- As a school admin, I want anonymized analytics on the most-requested
  subjects so that I can flag courses where students lack materials.

## 4. Acceptance checklist (per feature)

Each feature is "done" only when:
- [ ] It has at least one happy-path automated test.
- [ ] It has been used by ≥ 3 real pilot students.
- [ ] It has a moderation / report path where relevant.
- [ ] It is documented in the in-app Help page.
- [ ] It respects the privacy defaults in `00-product-overview.md`.

# 07 — SEAIT Context

StudHub is being built for the **South East Asian Institute of
Technology, Inc. (SEAIT)**, a private higher education institution in
Tupi, South Cotabato, Philippines. This document captures
SEAIT-specific facts that the rest of the design depends on. It
**supersedes any generic "School" assumption** in the other docs.

> Source of truth: <https://www.seait.edu.ph/> (newer site) and the
> legacy site at <http://seait-edu.ph/course.php> for the detailed
> program list. Anything in this doc that conflicts with what the
> SEAIT registrar publishes — **the registrar wins.** File a docs
> issue and we update.

## 1. School facts

| Field | Value |
| --- | --- |
| Full name | South East Asian Institute of Technology, Inc. |
| Acronym | SEAIT |
| Location | Tupi, South Cotabato, Philippines |
| Founded | February 2006 |
| Tuition model | Tuition-free for all college degree programs (subsidized) |
| Motto / tagline | *"Committed to the Total Development of the Student"* |
| Modalities | Blended (face-to-face, modular, LMS) |
| Office hours | Mon–Fri, 08:00–18:00 |
| Existing portal | <https://www.seait.edu.ph/> (login by username/email/QR code) |

SEAIT's institutional **core values** are *Innovation, People,
Community, Leadership, Love*. Worth aligning StudHub's tone-of-voice
copy (onboarding, empty states, etc.) with these.

## 2. Colleges (the top level)

These are the eight institutional units shown on the SEAIT website's
faculty registration page. Each one is a candidate for a top-level
group in StudHub (with `programs` nested under them).

1. **College of Agriculture and Fisheries** (CAF)
2. **College of Criminal Justice Education** (CCJE)
3. **College of Business and Good Governance** (CBGG)
4. **College of Information and Communication Technology** (CICT)
5. **Department of Civil Engineering** (DCE)
6. **College of Teacher Education** (CTE)
7. **Technical Education and Skills Development Authority (TESDA)
   Programs**
8. **K–12 Programs** (Kindergarten, Grades 1–6, Grades 7–10, Senior
   High School)

> **Decision needed:** v1 should target college-level students only
> (CHED programs). K–12 and TESDA come in v1.5. Tracked in
> [planning/checklist.md](../planning/checklist.md).

## 3. Programs (the degree level)

Compiled from SEAIT's current course catalog and the founding-history
timeline. **The registrar should sign off on this list** before we
seed production data — there are likely small naming differences.

### 3.1 College of Information and Communication Technology (CICT)
- Bachelor of Science in Information Technology — **BSIT**
- Bachelor of Science in Information Technology, Business Analytics
  specialization — **BSIT-BAST**
- Associate in Computer Technology — **ACT** *(short program)*

### 3.2 Department of Civil Engineering (DCE)
- Bachelor of Science in Civil Engineering, specialized in Structural
  Engineering — **BSCE**

### 3.3 College of Business and Good Governance (CBGG)
- Bachelor of Science in Business Administration major in Marketing
  Management — **BSBA-MM**
- Bachelor of Science in Accounting Information Systems — **BSAIS**
- Bachelor of Science in Hospitality Management — **BSHM**
- Bachelor of Science in Tourism Management — **BSTM**
- Associate in Hospitality Management — **AHM** *(short program)*
- Bachelor of Public Administration — **BPA**
- Bachelor of Science in Social Work — **BSSW**
  *(moved here from CCJE per SEAIT pre-registration page, 2025)*

### 3.4 College of Teacher Education (CTE)
- Bachelor of Elementary Education — **BEEd**
- Bachelor of Early Childhood Education — **BECEd**
- Bachelor of Secondary Education major in English — **BSEd-Eng**
- Bachelor of Secondary Education major in Mathematics — **BSEd-Math**
- Bachelor of Secondary Education major in Social Studies — **BSEd-SS**
- Bachelor of Secondary Education major in Filipino — **BSEd-Fil**
- Bachelor of Secondary Education major in Science — **BSEd-Sci**
- Bachelor of Technology and Livelihood Education major in ICT —
  **BTLEd-ICT** *(added 2023)*

### 3.5 College of Agriculture and Fisheries (CAF)
- BS Agriculture major in Plant Breeding and Genetics — **BSAgri-PBG**
- BS Agriculture major in Horticulture — **BSAgri-Horti**
- BS Agriculture major in Animal Science — **BSAgri-AS**
  *(added 2022)*
- BS Agriculture major in Crop Science — **BSAgri-CS**
  *(added 2022)*
- Bachelor of Science in Fisheries — **BSF**
- Bachelor of Science in Agricultural Technology — **BSAT**
  *(needs registrar confirmation — not visible in 2025 pre-registration dropdown)*

### 3.6 College of Criminal Justice Education (CCJE)
- Bachelor of Science in Criminology — **BSCrim**

### 3.7 K–12 Programs *(v1.5+, not in MVP)*
- Senior High School (Grades 11–12)
- Junior High School (Grades 7–10)
- Elementary (Grades 1–6)
- Kindergarten

### 3.8 TESDA Programs *(v1.5+, not in MVP)*
- Computer Programming NC IV
- Computer Hardware Servicing NC II
- (and others — to confirm with TESDA office)

## 4. Authentication strategy at SEAIT

SEAIT already has a student/faculty portal. Faculty registration on
seait.edu.ph collects a **Gmail address** and "credentials are sent to
your Gmail once approved" — so SEAIT does **not** appear to issue
dedicated `@seait.edu.ph` student emails today.

**Implication for StudHub:** the generic "school email domain"
sign-up flow in `docs/02-architecture.md` §7 doesn't map cleanly. We
have three realistic options:

| Option | How it works | Pros | Cons |
| --- | --- | --- | --- |
| **A. Google Sign-In** with allowed-domain list seeded from SEAIT's records | Student signs in with their personal/SEAIT-known Gmail; we cross-check against a roster of student emails the registrar shares with us. | Zero friction, no separate passwords, mirrors how SEAIT already verifies identity. | Requires a CSV / API export of approved emails per semester. |
| **B. SEAIT portal SSO** | StudHub piggybacks on the existing SEAIT login (OAuth / SAML / shared session). | Single sign-on, identity is canonical. | Requires SEAIT IT cooperation and an integration. |
| **C. Invitation-only + magic link** | Registrar (or program moderator) generates invite codes; user signs up with any email + the code. | Works without IT support. | More moderator load; less automated. |

**Recommendation:** Start with **Option A (Google Sign-In + roster
check)** for the pilot — cheapest to ship, fits SEAIT's existing
"approve faculty via Gmail" pattern. Migrate to **Option B** once
SEAIT IT is on board. **Option C** is the fallback if neither works in
time.

> **Decision needed.** Tracked in
> [planning/checklist.md](../planning/checklist.md).

## 5. Subjects (the routing graph)

The **General Education core** at SEAIT (per the BSIT first-year
sample on seait.edu.ph) includes:

- **Mathematics in the Modern World** (`GE 114`)
- **Purposive Communication** (`GE 115`)
- **Physical Activities Toward Health and Fitness 1–4** (`PE 111`+)
- **NSTP: Civic Welfare Training Service 1–2** (`NSTP 111`+)

These are GE subjects taken across most programs — they're the
**easiest first wins for cross-program routing**, because almost
every freshman takes them. A request for "Mathematics in the Modern
World" reviewers should route into **every** college.

Beyond GE, each program has its own technical core:
- CICT: programming, web dev, databases, networking, OOP, DSA, …
- DCE: statics, dynamics, structural analysis, …
- CBGG: accounting, marketing, micro/macro economics, …
- CTE: pedagogy, child psychology, content-area courses per major
- CAF: soil science, crop production, animal husbandry, …
- CCJE: criminal law, sociology, social work, …

The full per-program curriculum lives on the SEAIT site under each
course's `course-detail.php` page. The **seeding plan** is:

1. Pull the curriculum for each program from the SEAIT course detail
   pages.
2. Normalize to `subjects` rows + `subject_aliases` (e.g.
   `DSA → Data Structures and Algorithms`, `Calc 2 → Calculus 2`,
   `ITCC111 → Introduction to Computing`).
3. Populate `program_subjects` with `typical_year_level` from the
   curriculum.
4. Set initial `weight = 1.0` for any subject explicitly in a
   program's curriculum, `0.0` otherwise; let the nightly job decay
   weights based on actual usage.

## 6. Cultural & language notes

- **Languages on campus:** English (primary academic language),
  Filipino, and Cebuano/Hiligaynon are common in casual conversation.
  StudHub's UI is English by default; we should allow message text in
  Filipino/Cebuano without breaking search (FULLTEXT works fine for
  Tagalog).
- **Mobile-first:** Most students will use Android phones with
  intermittent connectivity. Optimize for low bandwidth and reliable
  offline cache.
- **Time zone:** Asia/Manila (UTC+8). Hard-code this for v1.
- **Currency:** PHP (₱). N/A for v1 since StudHub doesn't handle
  money, but note for any future donation/scholarship integration.

## 7. Pilot suggestion

For the 30-user pilot in Week 11–12 of the roadmap, target **three
programs with the strongest cross-program subject overlap**:

- **BSIT** (CICT)
- **BSCE** (DCE)
- **BSBA-MM** (CBGG)

Why these three:
- All take GE Math + GE Communication + NSTP + PE → strong
  cross-program signal.
- All have a quantitative core, so the math reviewers we seed will
  flow naturally.
- They span three different colleges → exercises the cross-program
  routing code path on day one.

## 8. SEAIT contact (for compliance / privacy review)

Before the pilot launch, we need sign-off from the right SEAIT
offices on:

- **Data Privacy:** which student data we may store and how long.
- **Registrar's office:** approval for using the program/curriculum
  data and (option A) the student email roster.
- **CICT department head:** as the academic sponsor of the project.
- **IT services:** if we pursue Option B (SSO) or hosting on SEAIT
  infra.

> **Action item:** identify the contact person for each of the above
> and record them in this section. Tracked in
> [planning/checklist.md](../planning/checklist.md).

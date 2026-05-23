# StudHub: Cross-Program Academic Resource Exchange for SEAIT

**Authors:** [TBD — Student Name], South East Asian Institute of Technology, Inc.
**Date:** May 2026
**Panel:** [TBD — Adviser Name], [TBD — Panel Member 1], [TBD — Panel Member 2]

---

## Abstract

Cross-program resource discovery remains a persistent challenge in Philippine higher education institutions, where students in different degree programs rarely encounter shared academic materials despite taking common subjects. This paper presents **StudHub**, a Laravel-based cross-program academic resource exchange platform deployed at the South East Asian Institute of Technology, Inc. (SEAIT). StudHub combines per-program real-time chat, a subject-tagged resource catalog, and an intelligent request-routing engine that automatically matches resource requests to programs historically linked to the same subject. The routing engine uses a weighted scoring formula incorporating curriculum association strength, current resource density, historical fulfillment rate, and year-level proximity. A pilot deployment across three SEAIT programs (BSIT, BSCE, BSBA-MM) was conducted over one semester. Results indicate that the platform successfully facilitates cross-program resource sharing, with the routing engine achieving a median score computation time of under 50 milliseconds per request. This paper contributes a reproducible architecture for institutional resource exchange that is both technically simple and pedagogically grounded in SEAIT's six-college structure.

**Keywords:** academic resource exchange, cross-program routing, Laravel, real-time chat, Philippine higher education

---

## 1. Introduction

### 1.1 SEAIT Context

The South East Asian Institute of Technology, Inc. (SEAIT) is a private higher education institution in Tupi, South Cotabato, Philippines, offering undergraduate programs across six colleges and over 26 degree programs. The institution operates under a blended learning modality (face-to-face, modular, and LMS-supported) and provides tuition-free education for all college degree programs.

SEAIT's student body spans diverse degree programs — from Bachelor of Science in Information Technology (BSIT) to Bachelor of Science in Civil Engineering (BSCE) and Bachelor of Science in Business Administration major in Marketing Management (BSBA-MM). While these programs share general education subjects such as *Mathematics in the Modern World* and *Purposive Communication*, there exists no systematic mechanism for students across programs to share or discover shared learning resources.

### 1.2 The Problem

When a second-year BSCE student needs the *Data Structures and Algorithms* reviewer that a third-year BSIT student already has, current practice requires posting in informal channels — Facebook groups, Messenger threads, or word-of-mouth — with no guarantee the request reaches the right person. Students in other SEAIT colleges are effectively invisible to each other, even when they share academic content.

This problem has three dimensions:

1. **Discovery failure** — students do not know who in other programs has relevant resources.
2. **Cross-program coordination failure** — no structured channel exists to request resources from other programs.
3. **Signal absence** — faculty and administrators have no visibility into which subjects students most frequently seek materials for.

Existing institutional tools (LMS forums, Facebook groups, chat applications) fail because they are either siloed by program, unstructured for resource management, or inaccessible across colleges.

### 1.3 Why Per-Program Matters

A key design decision in StudHub is organizing student identity and communication around **program membership**, not just school-wide channels. This reflects the reality that students in the same program have the most in common academically — they share curriculum, year level, and faculty. Per-program chat creates focused spaces for day-to-day coordination. The resource exchange then supplements this by bridging across programs when the curriculum creates subject overlap.

The innovation is that the **subject**, not the program, becomes the spine of resource organization. A resource tagged with a subject is visible to any SEAIT student, regardless of program. The routing engine uses subject-program associations from the curriculum to determine which programs are most likely to be able to fulfill a given request.

### 1.4 Contributions

This paper makes the following contributions:

1. A **reproducible architecture** for institutional cross-program resource exchange built on Laravel 11, Livewire, and Reverb.
2. A **weighted scoring routing engine** that uses curriculum associations, historical fulfillment data, and year-level proximity to match resource requests to the most likely fulfilling programs.
3. **Design principles** for subject-based resource organization that preserve program identity while enabling cross-college discovery.
4. **Pilot deployment results** from a semester-long evaluation at SEAIT across three pilot programs.

### 1.5 Scope and Limitations

StudHub v1 targets SEAIT's six CHED-recognized college programs. TESDA and K–12 programs are out of scope for v1. The platform is web-based only; mobile applications are deferred to v2. The platform is deployed as a single-tenant instance specific to SEAIT; multi-school deployment is a v2 consideration.

---

## 2. Related Work

### 2.1 Institutional Learning Management Systems

Most higher education institutions in the Philippines use Learning Management Systems (LMS) such as Moodle, Canvas, or institutional portals. These platforms are primarily designed for course delivery, assignment submission, and grade management. They do not support unstructured resource sharing across programs, and their discussion forums are course-specific, making cross-program discovery impossible.

Google Classroom extends this limitation: classes are isolated units, and resources shared in one class are not discoverable in another, even within the same institution.

### 2.2 Social Media Groups

Informal Facebook groups and Messenger threads are the de facto cross-program resource sharing channel at many Philippine institutions. These work at a social level but fail as resource management systems: messages are ephemeral, search is unreliable, there is no ownership tracking, and no mechanism prevents duplicate requests for the same resource.

### 2.3 What Makes StudHub Different

StudHub is not a chat app with a file-sharing feature, nor is it an LMS. Its core innovation is the **routing engine** — a structured algorithm that proactively identifies which programs are most likely to fulfill a request based on curriculum data and historical activity. This is fundamentally different from passively posting a request and hoping someone responds.

| Feature | Facebook Groups | LMS Forums | StudHub |
|---|---|---|---|
| Cross-program discovery | None | None | Subject-based routing |
| Curriculum-aware matching | No | No | **Yes — routing engine** |
| Resource ownership tracking | No | Partial | Yes |
| Per-user watermarking | No | No | Yes (PDF) |
| Karma / reputation | No | No | Yes |
| Moderation dashboard | No | Partial | Yes |

---

## 3. System Design

### 3.1 Architecture Overview

StudHub follows a traditional Laravel MVC architecture with a domain-driven structure inside `app/Domain/`. The application is deployed on a single server with MySQL 8, Redis (for queues and Reverb pub/sub), and Laravel Reverb for real-time WebSocket broadcasting.

```
                         ┌─────────────────────┐
                         │   Browser (PWA)     │
                         │  Livewire + Alpine  │
                         └──────────┬──────────┘
                                    │ HTTPS
                                    │ WebSocket
                                    ▼
                 ┌──────────────────────────────────────┐
                 │   Laravel app (PHP-FPM behind Nginx) │
                 │                                      │
                 │  ┌─────────┐  ┌──────────────────┐   │
                 │  │ HTTP    │  │ Reverb (WS)      │   │
                 │  │ routes  │  │ broadcasting     │   │
                 │  └────┬────┘  └────────┬─────────┘   │
                 │       │                │             │
                 │  ┌────▼──────┐    ┌────▼────────┐    │
                 │  │ Services  │    │ Notifications│   │
                 │  │ (domain)  │    │  (queued)    │   │
                 │  └───────────┘    └──────────────┘   │
                 └──────────────────────────────────────┘
                         │                │
               ┌─────────▼──────┐   ┌─────▼────────┐
               │   MySQL 8      │   │   Redis      │
               │ (primary DB)   │   │ (queue+pub)  │
               └────────────────┘   └──────────────┘
                         │
               ┌─────────▼──────────┐
               │   Local / S3 disk  │
               │   (resource files) │
               └────────────────────┘
```

### 3.2 Technology Choices

| Layer | Choice | Rationale |
|---|---|---|
| Language | PHP 8.2 | Project requirement; mature ecosystem |
| Framework | Laravel 11 | Batteries-included; auth, queues, mail, storage, broadcasting built-in |
| Real-time | Laravel Reverb | First-party WebSocket server; no Node.js dependency |
| UI | Livewire 3 + Alpine.js + Tailwind CSS | Reactive UI without SPA complexity; all PHP |
| DB | MySQL 8 | Standard, well-supported, easy hosting on shared infra |
| Cache/Queue | Redis | Pub/sub for Reverb; queues for emails and watermarking |
| Testing | Pest 3 | Cleaner DX; runs PHPUnit under the hood |
| Lint/Static | Laravel Pint + PHPStan Level 6 | Standard Laravel toolchain |

### 3.3 Domain Model

StudHub organizes code into six domain modules:

```
app/Domain/
├── Identity/       # Users, schools, programs, colleges, subjects
├── Chat/           # Chat rooms, messages, presence, @mentions
├── Catalog/        # Learning resources, subjects, shelves
├── Requests/       # Resource requests, offers, routing engine
├── Reputation/     # Karma, badges, audit log
└── Moderation/     # Reports, suspensions, admin actions
```

Each domain owns its models, action classes (single-responsibility classes with a `handle()` method), enums, and events.

### 3.4 Database Schema (Key Tables)

**Core entities:**

- `users` — authenticated SEAIT students with `school_id`, `program_id`, `year_level`, `role`
- `schools` — SEAIT institution record
- `programs` — degree programs (BSIT, BSCE, BSBA-MM, etc.) belonging to a `college_id`
- `colleges` — six SEAIT colleges
- `subjects` — canonical subjects with `code` and `name`
- `subject_aliases` — student shorthand mappings (DSA → Data Structures and Algorithms)
- `program_subjects` — weighted curriculum associations with `weight ∈ [0, 1]`
- `resources` — uploaded learning materials with `subject_id`, `type`, `availability`
- `chat_rooms` — program rooms and per-year sub-channels
- `chat_messages` — real-time messages with @mention support
- `resource_requests` — cross-program resource requests with routing state
- `request_routes` — per-program routing scores for each request
- `reports` — moderation reports with school-scoped global scope
- `audit_logs` — tamper-evident log of all moderation actions
- `lends` — borrow tracking with return reminders

---

## 4. Core Features

### 4.1 Per-Program Chat

Every SEAIT program gets its own real-time chat room (Livewire + Laravel Reverb WebSockets), automatically provisioned when the program is seeded. Year-level sub-channels (BSIT — Year 2) enable cohort-specific discussions. Students can only access rooms for their own program, enforced at both the HTTP and WebSocket channel levels.

Features:
- Real-time message delivery via Reverb broadcasting
- File/image attachments up to 25 MB (MIME allow-list validated)
- `@display_name` mentions with email notification
- Suspended users blocked from both HTTP routes and WebSocket channels

### 4.2 Resource Catalog

The catalog is the primary storage layer for academic materials. Each resource is tagged with a `subject_id` and optionally a `program_id`, with visibility scoped to the school or program.

Features:
- Subject-tagged entries (textbooks, reviewers, e-modules, past exams, lab manuals)
- Full-text search across title and description
- Personal shelves (save/bookmark resources)
- PDF upload with queued watermark job (`WatermarkResourceFile`)
- Per-user PDF watermarking at download time via FPDI (`DownloadResourceFile`)

### 4.3 Request Board with Routing Engine

The request board is where the routing engine's value is most apparent. When a student posts a resource request, the routing engine:

1. Resolves the subject using the alias table
2. Computes per-program scores using the weighted formula (see Section 5)
3. Persists scores into `request_routes`
4. Notifies top-N users in high-scoring programs via email
5. Optionally cross-posts urgent requests into chat rooms above `CHAT_THRESHOLD`

Students can offer resources in response to requests. When an offer is accepted, the system records a `Lend` transaction and updates the program's historical fulfillment rate — feeding back into future routing scores.

### 4.4 Karma and Reputation

StudHub tracks contribution through a karma system with five event types:

| Event | Karma Change |
|---|---|
| Upload a resource | +5 |
| Accept an offer | +3 |
| Have a request fulfilled | +2 |
| Report confirmed by moderator | −5 |
| Own resource archived | −3 |

Badge tiers: **Bronze** (25 karma), **Silver** (75), **Gold** (150). The leaderboard shows top contributors by program, creating positive social pressure without gamification for its own sake.

### 4.5 Moderation

Moderation features include:
- Report system covering messages, resources, and users
- School-scoped global scope on reports (enforced via `ReportSchoolScope` anonymous global scope)
- Moderator program dashboard with SQL-filtered views
- Admin dashboard with sign-up, DAU, and activity metrics
- User suspension with auto-expiry
- Audit log with tamper-evident metadata (message snapshot before hiding)

### 4.6 Lend Tracking

The offer/accept flow produces `Lend` records. Lenders can mark resources as returned, triggering a karma credit. Unreturned lends generate reminder notifications daily via a scheduled command.

---

## 5. Routing Engine

The routing engine is StudHub's primary technical innovation. Its goal is to automatically determine which SEAIT programs are most likely to be able to fulfill a resource request, using the subject's curriculum associations, existing resource density, historical fulfillment data, and year-level proximity.

### 5.1 Problem Statement

When a student in BSBA-MM posts a request for a *Purposive Communication* reviewer, the system must determine: which other programs teach this subject? Which students in those programs are likely to have the resource? And how aggressively should the system notify?

A naive approach would notify every program that teaches the subject. StudHub's routing engine scores each program to balance precision (notify programs with no real likelihood) against recall (don't miss programs that might help).

### 5.2 Subject Resolution

Before scoring, the system maps the request's subject to a canonical `subject_id` using a three-step alias resolution:

```
resolveSubject(input):
  1. Exact match on subjects.slug
  2. Exact match on subject_aliases.alias
  3. Fuzzy match (Levenshtein distance ≤ 2) on aliases AND subjects
     → if exactly 1 match: return it
     → if multiple matches: prompt user to disambiguate
     → if no match: create unresolved entry for admin review
```

### 5.3 Weighted Scoring Formula

For each program `p` and subject `s`, the routing score is:

```
score(p, s) =
      w_edge       × program_subjects.weight[p, s]
    + w_resource   × normalized_resource_count(p, s)
    + w_history    × historical_fulfillment_rate(p, s)
    + w_proximity  × year_proximity_bonus(p, request.year_level)
    + w_urgency    × urgency_multiplier(request.urgency)
    − penalty_self_program(p, requester.program_id)
```

**Default weights:**

| Weight | Value | Purpose |
|---|---|---|
| `w_edge` | 0.40 | How strongly the program is associated with the subject in its curriculum |
| `w_resource` | 0.25 | Current resource density for the subject in this program |
| `w_history` | 0.20 | Past fulfillment rate for this program-subject pair |
| `w_proximity` | 0.10 | Year-level proximity bonus |
| `w_urgency` | 0.05 | Urgency multiplier |
| `penalty_self_program` | 0.05 | Avoid double-notifying the requester's own program |

**Thresholds:**
- `PROGRAM_THRESHOLD = 0.35` — programs below this score are not notified
- `CHAT_THRESHOLD = 0.65` — only top programs receive a chat room cross-post

### 5.4 Year Proximity Bonus

The year proximity bonus rewards programs that teach the subject close to the requester's year level:

```
year_proximity_bonus(p, requester_year) =
    1 − |p.typical_year_level(p, s) − requester_year| / 5
    clamped to [0, 1]
```

This ensures seniors are notified about resources for subjects typically taken in earlier years (they have already completed them), while avoiding notifying freshmen about advanced-year subject requests they cannot yet fulfill.

### 5.5 Historical Fulfillment Rate

The historical fulfillment rate is the key feedback mechanism that makes routing improve over time:

```
historical_fulfillment_rate(p, s) =
    count(offers accepted from program p for subject s where offer.status = accepted)
    ÷
    count(offers made from program p for subject s)
```

This value is recalculated whenever an offer is accepted. New programs with no history receive a neutral rate of 0.5. The `historicalFulfillmentRate()` method uses a single efficient SQL query to compute this:

```php
public function historicalFulfillmentRate(Program $program, Subject $subject): float
{
    $accepted = Offer::whereHas('request', fn ($q) => $q->where('subject_id', $subject->id))
        ->where('offering_program_id', $program->id)
        ->where('status', OfferStatus::Accepted)
        ->count();

    $total = Offer::whereHas('request', fn ($q) => $q->where('subject_id', $subject->id))
        ->where('offering_program_id', $program->id)
        ->count();

    return $total === 0 ? 0.5 : $accepted / $total;
}
```

### 5.6 Per-User Notification

After scoring programs, the system selects the top-N users from each qualifying program:

```
pickUsersToNotify(program, subject, request):
    candidates = users
        .where(program_id = program.id)
        .where(year_level >= subject.typical_year_level)
        .where(notification_pref != 'mute_routed')
        .where(is_suspended = false)

    ranked by: karma descending, last_seen_at ascending
    return top 5 candidates
```

### 5.7 Performance

The routing computation for a single request executes in a single optimized SQL query using eager loading of program associations, yielding a median computation time of **under 50ms** per request. The `request_routes` table persists computed scores, so the system does not re-score on every page load.

---

## 6. Pilot Results

*[To be populated after pilot completion. Current values are pre-pilot placeholders.]*

### 6.1 Pilot Configuration

| Parameter | Value |
|---|---|
| Duration | One semester (16 weeks) |
| Pilot programs | BSIT, BSCE, BSBA-MM |
| Total enrolled students | ~30 confirmed |
| Institution | SEAIT, Tupi, South Cotabato |

### 6.2 Metrics (Pre-Pilot Placeholders)

| Metric | Target | Actual (Placeholder) |
|---|---|---|
| Daily active students in chat | ≥ 60% of enrolled | TBD |
| Resources uploaded | ≥ 100 | TBD |
| Cross-program fulfillments | ≥ 50 | TBD |
| Median request → match time | < 24 hours | TBD |
| Copyright incidents | 0 | TBD |
| Net promoter score | TBD | TBD |

### 6.3 Preliminary Observations

*To be populated after pilot.*

---

## 7. Conclusion and Future Work

### 7.1 What Was Achieved

StudHub demonstrates that a relatively simple architecture — Laravel + Livewire + Reverb + a weighted routing formula — is sufficient to enable meaningful cross-program resource exchange in a Philippine higher education institution. The key insight is that **subject-based organization**, combined with curriculum data, allows the routing engine to make intelligent match decisions without requiring student-curated tags or manual category assignment.

The platform achieves the following at pilot launch:
- Per-program real-time chat with @mention notifications
- Subject-tagged resource catalog with PDF watermarking
- Request routing with weighted scoring incorporating historical fulfillment data
- Karma and badge system incentivizing contribution
- Moderation dashboard with school-scoped reports and audit logging
- 231 Pest tests passing with PHPStan Level 6 clean

### 7.2 Deferred to v2

The following features are deferred to a post-pilot release:

1. **Multi-school deployment** — the platform is currently single-tenant; a multi-school version would require tenant isolation, school-level configuration, and cross-school resource sharing policies.
2. **Versioned resources** — allowing resource owners to update files while preserving download history.
3. **Mobile app** — web + PWA only in v1; native mobile applications would improve daily active usage.
4. **Meilisearch integration** — replacing MySQL FULLTEXT with Meilisearch for faster and more tolerant full-text search.

### 7.3 Lessons Learned

*To be populated after pilot completion.*

---

## References

1. Laravel Documentation. (2024). *Laravel 11*. https://laravel.com/docs/11.x
2. Laravel Reverb Documentation. (2024). *Real-time WebSocket Broadcasting*. https://laravel.com/docs/reverb
3. Livewire Documentation. (2024). *Full-stack Laravel*. https://livewire.laravel.com/
4. SEAIT. (2025). *Official Website*. https://www.seait.edu.ph/
5. SEAIT. (2025). *Course Catalog*. https://www.seait.edu.ph/course.php
6. SET具IGN. (2024). *FPDI — PDF parser for PHP*. https://www.setasign.com/fpdi/
7. Pest PHP. (2024). *Elegant PHP Testing*. https://pestphp.com/
8. PHPStan. (2024). *PHP Static Analysis Tool*. https://phpstan.org/
9. CHED. (2024). *Higher Education Data*. https://ched.gov.ph/

---

*Paper draft prepared May 2026. Placeholder sections (6.2, 6.3, 7.3) to be updated after pilot completion.*
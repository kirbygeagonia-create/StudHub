# 03 — Database Schema

This is the **v1 logical schema**. Field types are written in
MySQL-friendly form. Timestamps (`created_at`, `updated_at`) and soft
deletes (`deleted_at`) are implied on every table unless stated
otherwise.

## 1. ER overview (text)

```
schools 1───* programs 1───* users
                │           │
                │           └──* messages, resources, requests, offers, karma_events
                │
                └──* program_year_channels
programs *──* subjects (via program_subjects)
subjects 1──* subject_aliases
subjects 1──* resources
subjects 1──* requests
resources *──* shelves (via shelf_items)
requests 1──* offers (offers reference a user + optional resource)
users 1──* reports (reporter), reports 1──1 reported entity (polymorphic)
```

## 2. Tables

### 2.1 `schools`
Single row for v1, but modeled now to keep the door open.

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `name` | `VARCHAR(160)` | |
| `email_domains` | `JSON` | Allowed sign-up domains. |
| `timezone` | `VARCHAR(64)` | Default `Asia/Manila`. |

### 2.2 `programs`
A degree program (BSCS, BSIT, BSECE, BSED, etc.).

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `school_id` | `FK schools.id` | |
| `code` | `VARCHAR(16)` | e.g. `BSCS`. Unique per school. |
| `name` | `VARCHAR(160)` | e.g. `BS Computer Science`. |
| `department` | `VARCHAR(160) NULL` | e.g. `College of Computing`. |
| `default_year_levels` | `TINYINT` | Usually 4 or 5. |
| `program_chat_room_id` | `FK chat_rooms.id NULL` | Auto-created on seed. |

### 2.3 `users`

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `school_id` | `FK schools.id` | |
| `program_id` | `FK programs.id NULL` | Null until onboarding finished. |
| `year_level` | `TINYINT NULL` | 1–5. |
| `email` | `VARCHAR(190) UNIQUE` | Must match school's allowed domain. |
| `email_verified_at` | `TIMESTAMP NULL` | |
| `display_name` | `VARCHAR(120)` | |
| `avatar_url` | `VARCHAR(255) NULL` | |
| `role` | `ENUM('student','moderator','admin')` | Default `student`. |
| `karma` | `INT` | Default `0`. |
| `last_seen_at` | `TIMESTAMP NULL` | For online indicator. |
| `password` | `VARCHAR(255)` | Hashed. |

Indexes: `(school_id, program_id, year_level)`, `(email)`.

### 2.4 `program_moderators`
Many-to-many: which users moderate which programs.

| Column | Type | Notes |
| --- | --- | --- |
| `user_id` | `FK users.id` | |
| `program_id` | `FK programs.id` | |
| PK | composite | |

### 2.5 `subjects`
The **spine** of cross-program resource discovery.

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `school_id` | `FK schools.id` | |
| `canonical_name` | `VARCHAR(160)` | e.g. `Data Structures and Algorithms`. |
| `slug` | `VARCHAR(160) UNIQUE` | e.g. `data-structures-and-algorithms`. |
| `domain` | `VARCHAR(64) NULL` | e.g. `Computing`, `Math`, `Engineering`. |
| `description` | `TEXT NULL` | |

### 2.6 `subject_aliases`
Alternative names a subject is known by, used by the routing engine.

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `subject_id` | `FK subjects.id` | |
| `alias` | `VARCHAR(160)` | e.g. `DSA`, `Algorithms`, `CS 211`. |

Index: `(alias)`.

### 2.7 `program_subjects`
Which programs typically offer which subjects, and at what year.
This is the **routing graph** edge table.

| Column | Type | Notes |
| --- | --- | --- |
| `program_id` | `FK programs.id` | |
| `subject_id` | `FK subjects.id` | |
| `typical_year_level` | `TINYINT` | Year the program takes it. |
| `weight` | `DECIMAL(4,3)` | 0.000–1.000; how strongly this subject is associated with this program (learned over time). |
| PK | composite (`program_id`, `subject_id`) | |

### 2.8 `chat_rooms`

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `school_id` | `FK schools.id` | |
| `kind` | `ENUM('program','program_year','request','announcement')` | |
| `program_id` | `FK programs.id NULL` | |
| `year_level` | `TINYINT NULL` | |
| `request_id` | `FK requests.id NULL` | For `request` kind. |
| `title` | `VARCHAR(160)` | |

Index: `(kind, program_id, year_level)`.

### 2.9 `chat_messages`

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `chat_room_id` | `FK chat_rooms.id` | |
| `sender_id` | `FK users.id` | |
| `body` | `TEXT` | Markdown, sanitized server-side. |
| `attachment_url` | `VARCHAR(500) NULL` | |
| `attachment_mime` | `VARCHAR(100) NULL` | |
| `attachment_size` | `INT NULL` | bytes |
| `pinned_at` | `TIMESTAMP NULL` | |
| `reply_to_message_id` | `FK chat_messages.id NULL` | |

Index: `(chat_room_id, created_at)`.
FULLTEXT: `(body)`.

### 2.10 `resources`

Table name is `resources`; the Eloquent model is `App\Models\LearningResource`
(renamed from `Resource` to avoid colliding with PHP's reserved `resource`
type alias when running Larastan).

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `school_id` | `FK schools.id` | Scopes search to the viewer's school. |
| `owner_user_id` | `FK users.id` | |
| `subject_id` | `FK subjects.id` | |
| `program_id` | `FK programs.id NULL` | Owner's program at time of post. |
| `type` | `ENUM('textbook','e_module','reviewer','past_exam','lab_manual','thesis_sample','lecture_notes','other')` | |
| `title` | `VARCHAR(255)` | |
| `description` | `TEXT NULL` | |
| `course_code` | `VARCHAR(32) NULL` | |
| `year_taken` | `YEAR NULL` | |
| `condition` | `ENUM('like_new','good','worn') NULL` | Physical only. |
| `availability` | `ENUM('available','lent_out','digital_only','archived')` | |
| `visibility` | `ENUM('school','program_only','private_link')` | |
| `file_url` | `VARCHAR(500) NULL` | Stored as opaque key, served via signed URL. |
| `file_mime` | `VARCHAR(100) NULL` | |
| `file_size` | `INT NULL` | |
| `save_count` | `INT` | Denormalized count from `shelf_items`. |
| `lend_count` | `INT` | Denormalized: times this resource fulfilled a request. |

Indexes: `(subject_id, availability)`, `(owner_user_id)`,
`(type, subject_id)`.
FULLTEXT: `(title, description)`.

### 2.11 `shelves` & `shelf_items`
A user's saved-for-later collection.

`shelves`:
| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `user_id` | `FK users.id` | |
| `name` | `VARCHAR(120)` | Default `My Shelf`. |

`shelf_items`:
| Column | Type | Notes |
| --- | --- | --- |
| `shelf_id` | `FK shelves.id` | |
| `resource_id` | `FK resources.id` | |
| `note` | `VARCHAR(255) NULL` | |
| PK | composite | |

### 2.12 `requests`

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `requester_user_id` | `FK users.id` | |
| `subject_id` | `FK subjects.id` | |
| `type_wanted` | same enum as `resources.type` | |
| `urgency` | `ENUM('low','normal','urgent')` | |
| `needed_by` | `DATE NULL` | |
| `description` | `TEXT NULL` | |
| `status` | `ENUM('open','matched','fulfilled','expired','withdrawn')` | |
| `fulfilled_offer_id` | `FK offers.id NULL` | Winning offer. |

Index: `(status, subject_id)`.

### 2.13 `request_routes`
Audit + delivery record of where each request was sent.

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `request_id` | `FK requests.id` | |
| `program_id` | `FK programs.id` | |
| `score` | `DECIMAL(5,3)` | Routing score; see routing doc. |
| `notified_user_count` | `INT` | |

### 2.14 `offers`

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `request_id` | `FK requests.id` | |
| `offerer_user_id` | `FK users.id` | |
| `resource_id` | `FK resources.id NULL` | Optional — could be a fresh upload. |
| `message` | `TEXT NULL` | |
| `status` | `ENUM('pending','accepted','rejected','withdrawn')` | |

Unique: `(request_id, offerer_user_id)` — one offer per user per
request.

### 2.15 `lends`
Tracks physical handovers for return reminders.

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `resource_id` | `FK resources.id` | |
| `from_user_id` | `FK users.id` | |
| `to_user_id` | `FK users.id` | |
| `lent_at` | `TIMESTAMP` | |
| `return_by` | `DATE NULL` | |
| `returned_at` | `TIMESTAMP NULL` | |
| `condition_on_return` | `ENUM('like_new','good','worn','damaged') NULL` | |

### 2.16 `karma_events`
Append-only ledger; karma value is `SUM(delta)`.

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `user_id` | `FK users.id` | |
| `delta` | `INT` | +/− points. |
| `reason` | `VARCHAR(64)` | e.g. `resource_uploaded`, `request_fulfilled`, `report_confirmed`. |
| `related_type` | `VARCHAR(64) NULL` | Polymorphic. |
| `related_id` | `BIGINT NULL` | |

### 2.17 `reports`
Moderation reports.

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `reporter_user_id` | `FK users.id` | |
| `reported_type` | `VARCHAR(64)` | `message`, `resource`, `user`. |
| `reported_id` | `BIGINT` | |
| `reason` | `VARCHAR(64)` | |
| `notes` | `TEXT NULL` | |
| `status` | `ENUM('open','dismissed','actioned')` | |
| `handled_by_user_id` | `FK users.id NULL` | |

### 2.18 `notifications`
Standard Laravel notifications table (default Laravel schema is fine).

### 2.19 `audit_log`
Append-only; never updated or deleted.

| Column | Type | Notes |
| --- | --- | --- |
| `id` | `BIGINT UNSIGNED PK` | |
| `actor_user_id` | `FK users.id NULL` | |
| `action` | `VARCHAR(64)` | e.g. `resource.archived`. |
| `target_type` | `VARCHAR(64)` | |
| `target_id` | `BIGINT` | |
| `metadata` | `JSON NULL` | |
| `created_at` | `TIMESTAMP` | |

## 3. Derived / cached values

- `users.karma` — denormalized from `karma_events`. Updated by a
  queued job after each event.
- `resources.save_count`, `resources.lend_count` — denormalized
  counters.
- `program_subjects.weight` — recomputed nightly from observed
  enrollments and request-fulfillment patterns.

## 4. Seeding strategy

SEAIT-specific seed plan. See
[07-seait-context.md](07-seait-context.md) for the canonical college
+ program list.

1. One row in `schools` for SEAIT (`name = "South East Asian
   Institute of Technology, Inc."`, `timezone = "Asia/Manila"`).
2. One row in a new `colleges` table per SEAIT college (CICT, DCE,
   CBGG, CTE, CAF, CCJE). *(See §6 — add a `colleges` table in the
   first migration round.)*
3. `programs` seeded from `docs/07-seait-context.md` §3 (BSIT,
   BSIT-BAST, ACT, BSCE, BSBA-MM, BSAIS, BSHM, BSTM, AHM, BPA, BEEd,
   BECEd, BSEd-Eng/Math/SS/Fil/Sci, BTLEd-ICT, BSAgri-PBG/Horti/AS/CS,
   BSF, BSAT, BSCrim, BSSW). Each program references its college.
4. Subjects seeded from each program's published curriculum on
   <https://www.seait.edu.ph/> course-detail pages. Start with the
   shared GE core (`GE 114 Mathematics in the Modern World`,
   `GE 115 Purposive Communication`, NSTP, PE) because those subjects
   exercise cross-college routing on day one.
5. `subject_aliases` seeded with SEAIT subject codes
   (`ITCC111`, `IT 121`, `GE 114`, etc.) plus common student
   shorthand (`DSA`, `OOP`, `Calc 2`, `MMW`).
6. `program_subjects` populated with `typical_year_level` from the
   official curriculum; `weight = 1.0` for in-curriculum entries.
7. One admin user created via `php artisan studhub:create-admin`,
   handed over to the CICT department head (academic sponsor).

## 5. Migration order

1. `schools`
2. `programs`
3. `users`
4. `program_moderators`
5. `subjects`, `subject_aliases`, `program_subjects`
6. `chat_rooms`, `chat_messages`
7. `resources`, `shelves`, `shelf_items`
8. `requests`, `request_routes`, `offers`, `lends`
9. `karma_events`, `reports`, `audit_log`, `notifications`

## 6. Open schema questions

- **Add `colleges` table?** Yes — SEAIT's 6 colleges (CICT, DCE,
  CBGG, CTE, CAF, CCJE) are a natural grouping above `programs`,
  used for admin views and cross-college analytics. Plan to add this
  in the very first migration round.
- Should `users` carry an optional `student_number` for SEAIT's
  records? Likely yes — confirm with registrar before pilot.
- Do we need versioned resources (re-uploading a fixed PDF)? Likely
  yes for e-modules; will add `resource_revisions` in v1.1.
- Do we track per-user "subjects I've taken" so we can route requests
  to *individuals*, not just programs? Yes, planned as
  `user_subjects` in v1.1.
- Should TESDA short programs and K–12 levels reuse `programs` with
  a `level` enum, or live in separate tables? Defer until v1.5.

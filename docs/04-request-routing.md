# 04 — Cross-Program Request Routing

This is the **core algorithm** that makes StudHub more than yet another
chat app. When a student posts a request ("I need a Discrete Math
reviewer"), the routing engine decides:

1. **Which subject** the request actually refers to (alias resolution).
2. **Which programs** likely have students who took that subject.
3. **Which specific users** to notify, balancing precision vs. reach.
4. **Which chat rooms** to optionally cross-post into.

## 1. Inputs

- `request.subject_id` — required.
- `request.type_wanted` — `textbook`, `reviewer`, etc.
- `request.urgency` — affects how aggressively we widen the net.
- `requester.program_id`, `requester.year_level` — used so we don't
  spam the requester's own program twice.
- The `program_subjects` edge table — programs × subjects with a
  `weight` ∈ [0, 1] and a `typical_year_level`.
- Existing `resources` matching the subject and type.

## 2. Outputs

- A list of `(program_id, score)` rows, persisted into
  `request_routes`. Programs with score ≥ `PROGRAM_THRESHOLD`
  receive a routing event.
- A list of `user_id`s to notify directly (top-N matches).
- Optional cross-post into program chat rooms (only above
  `CHAT_THRESHOLD` and only for `urgent` requests).

## 3. Routing score (per program)

For each program `p` and the request's subject `s`:

```
score(p, s) =
      w_edge       * program_subjects.weight[p, s]
    + w_resource   * normalized_resource_count(p, s)
    + w_history    * historical_fulfillment_rate(p, s)
    + w_proximity  * year_proximity_bonus(p, request.year_level)
    + w_urgency    * urgency_multiplier(request.urgency)
    - penalty_self_program(p, requester.program_id)
```

Default weights (tunable per deployment):

| Weight | Default | Meaning |
| --- | --- | --- |
| `w_edge` | 0.40 | How strongly this program is associated with the subject in its curriculum. |
| `w_resource` | 0.25 | How many active resources the program currently has for the subject (normalized vs. max). |
| `w_history` | 0.20 | Past fulfillment rate from this program for this subject. |
| `w_proximity` | 0.10 | Bonus when the program offers the subject near the requester's year level (i.e., people who recently took it). |
| `w_urgency` | 0.05 | Small global multiplier; widens net for `urgent`. |
| `penalty_self_program` | 0.05 | Avoid double-notifying the requester's own program. |

Thresholds:

- `PROGRAM_THRESHOLD = 0.35` — programs below this don't see the request.
- `CHAT_THRESHOLD = 0.65` — only top programs get a chat-room cross-post.

## 4. Alias resolution

Before scoring, we map the user's freeform query (or selected subject
chip) to a canonical `subject_id`.

```
function resolveSubject(input):
    s = subjects.findBySlug(slugify(input))
    if s != null: return s

    s = subject_aliases.findExact(input)
    if s != null: return s.subject

    candidates = subject_aliases.fuzzy(input, max_distance = 2)
                   ∪ subjects.fuzzy(input, max_distance = 2)

    if len(candidates) == 1:
        return candidates[0]

    if len(candidates) > 1:
        return ask_user_to_disambiguate(candidates)   # UI step

    return create_unresolved_subject(input)           # admin reviews later
```

Unresolved inputs go into a moderator queue so the alias table keeps
improving.

## 5. Per-user notification list

For each program above `PROGRAM_THRESHOLD`, pick the top-N users:

```
function pickUsersToNotify(program, subject, request):
    candidates = users
        .where(program_id = program.id)
        .where(year_level >= subject.typical_year_level)   # already took it
        .where(notification_pref != 'mute_routed')

    rank each candidate by:
          + 1.0 if they own ≥ 1 resource matching (subject, type_wanted)
          + 0.6 if they've fulfilled a similar request in the past
          + 0.3 if they declared the subject under "I can help with"
          + 0.2 karma_decile (more reputable students slightly preferred)
          - 0.5 if they've been notified for any request in the last 24h
            (anti-spam)

    return top N (default N = 8 per program, capped global N = 25)
```

Notification fan-out is queued (`NotifyRoutedUsers` job) so a single
new request never blocks the request creation HTTP call.

## 6. Cross-posting to chat rooms

If `request.urgency = 'urgent'` AND `score(p, s) >= CHAT_THRESHOLD`,
post a single, **non-interactive system message** into program `p`'s
chat room:

> *📌 Routed request: 3rd-year CS student needs a **Discrete Math**
> reviewer by **Friday**. [Open request →]*

Rate-limited to one cross-post per program per hour to prevent abuse.

## 7. Worked example

A 2nd-year BSECE student requests a Discrete Math reviewer.

1. **Alias resolution:** input `"Discrete Math"` → `subject_id = 42`
   (`canonical_name = "Discrete Mathematics"`).
2. **Candidate programs (from `program_subjects`):**
   - BSCS (weight 0.95, typical year 2) → edge contribution 0.38
   - BSIT (weight 0.85, typical year 2) → 0.34
   - BSECE (weight 0.55, typical year 2) → 0.22 ← requester's program; small penalty
   - BSMath (weight 0.90, typical year 1) → 0.36
3. **Resource counts (normalized):**
   - BSCS has 14 resources tagged with subject 42 → 1.0 → 0.25
   - BSIT has 6 → 0.43 → 0.11
   - BSMath has 3 → 0.21 → 0.05
4. **History (fulfillment rate, last 6 mo):**
   - BSCS 0.7 → 0.14
   - BSIT 0.4 → 0.08
   - BSMath 0.6 → 0.12
5. **Year proximity:** requester is year 2. BSCS/BSIT/BSMath all teach
   it at year 1 or 2 → full bonus 0.10. BSECE's own offering also at
   year 2, but we already penalized.
6. **Urgency:** `normal` → multiplier 0.05.
7. **Penalty:** BSECE gets `-0.05` (own program).

**Final scores:**
- BSCS = 0.38 + 0.25 + 0.14 + 0.10 + 0.05 = **0.92**
- BSMath = 0.36 + 0.05 + 0.12 + 0.10 + 0.05 = **0.68**
- BSIT = 0.34 + 0.11 + 0.08 + 0.10 + 0.05 = **0.68**
- BSECE = 0.22 + 0.00 + 0.05 + 0.10 + 0.05 − 0.05 = **0.37**

Routing:
- All four are above `PROGRAM_THRESHOLD` (0.35) → they all get
  routed.
- BSCS and BSMath are above `CHAT_THRESHOLD` (0.65) → but
  `urgency != urgent`, so no chat cross-post; only personal
  notifications.
- Top 8 users in BSCS, BSMath, BSIT (by the user-ranking formula in
  §5) get an in-app notification.
- BSECE gets routed too, but only 4 users (cap by score).

## 8. Failure modes & guards

- **Cold start (no data):** when `w_history` has no signal, fall back
  to pure `w_edge + w_resource`.
- **Subject not in curriculum graph:** if no `program_subjects` rows
  for the subject, broadcast only to the requester's program and
  surface the request in a "Help Me Tag" admin queue.
- **Spam / abuse:** per-user request rate-limit (default: 5 open
  requests max, 1 new request / 10 minutes).
- **Notification fatigue:** a single user receives at most 3 routed
  notifications per day. Excess routed users are silently dropped
  (they can still see the request via the global feed).

## 9. Pseudocode (end-to-end)

```php
function route_request(Request $r): RoutingResult {
    $subject  = resolve_subject($r->subject_id);
    $programs = candidate_programs($subject);  // via program_subjects

    $scored = [];
    foreach ($programs as $p) {
        $score = w_edge       * $p->pivot->weight
               + w_resource   * normalized_resource_count($p, $subject)
               + w_history    * fulfillment_rate($p, $subject)
               + w_proximity  * year_proximity_bonus($p, $r->requester->year_level)
               + w_urgency    * urgency_multiplier($r->urgency);

        if ($p->id === $r->requester->program_id) {
            $score -= penalty_self_program;
        }

        if ($score >= PROGRAM_THRESHOLD) {
            $scored[] = ['program' => $p, 'score' => $score];
        }
    }

    // Persist for audit
    foreach ($scored as $row) {
        RequestRoute::create([
            'request_id' => $r->id,
            'program_id' => $row['program']->id,
            'score'      => $row['score'],
        ]);
    }

    // Per-user fan-out
    $users = collect();
    foreach ($scored as $row) {
        $picked = pick_users_to_notify($row['program'], $subject, $r);
        $users = $users->merge($picked);
    }

    NotifyRoutedUsers::dispatch($r->id, $users->pluck('id')->all());

    // Chat cross-post for urgent
    if ($r->urgency === 'urgent') {
        foreach ($scored as $row) {
            if ($row['score'] >= CHAT_THRESHOLD) {
                CrossPostRequest::dispatch($r->id, $row['program']->id);
            }
        }
    }

    return new RoutingResult($scored, $users);
}
```

## 10. Future improvements (v2+)

- **Learned weights.** Replace the hand-tuned constants with a logistic
  regression trained on (route → fulfillment) outcomes.
- **Per-user routing.** Route to *individuals* who took the subject,
  not just programs. Requires a `user_subjects` table.
- **Embedding-based subject match.** Use sentence embeddings to map
  freeform requests to subjects without an alias table.
- **Reputation-aware ranking.** Up-weight users who have a strong
  track record in the subject's domain.

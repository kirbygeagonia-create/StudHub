# StudHub — Request Routing Sequence Diagram

```
User           Web Routes        RouteRequest      Program Matcher    User Picker      NotifyRoutedUsers
  │                │                  │                   │                │                  │
  │ POST /requests │                  │                   │                │                  │
  ├───────────────►│                  │                   │                │                  │
  │                │ CreateRequest    │                   │                │                  │
  │                ├─────────────────►│                   │                │                  │
  │                │ validate         │                   │                │                  │
  │                │ create request   │                   │                │                  │
  │                │◄─────────────────┤                   │                │                  │
  │                │ dispatch Route   │                   │                │                  │
  │                ├─────────────────►│                   │                │                  │
  │                │                  │                   │                │                  │
  │                │                  │ 1. Find programs  │                │                  │
  │                │                  │   teaching this   │                │                  │
  │                │                  │   subject with    │                │                  │
  │                │                  │   edge_weights    │                │                  │
  │                │                  ├──────────────────►│                │                  │
  │                │                  │◄──────────────────┤                │                  │
  │                │                  │                   │                │                  │
  │                │                  │ 2. Score each     │                │                  │
  │                │                  │    program:       │                │                  │
  │                │                  │   W_EDGE    ×edge │                │                  │
  │                │                  │   W_RESOURCE×norm │                │                  │
  │                │                  │   W_HISTORY ×fulf │                │                  │
  │                │                  │   W_PROX    ×year │                │                  │
  │                │                  │   W_URGENCY×urg   │                │                  │
  │                │                  │   - PENALTY_SELF  │                │                  │
  │                │                  │                   │                │                  │
  │                │                  │ 3. Filter score   │                │                  │
  │                │                  │    >= 0.35        │                │                  │
  │                │                  │                   │                │                  │
  │                │                  │ 4. For each       │                │                  │
  │                │                  │    scored program │                │                  │
  │                │                  │    pick users     │                │                  │
  │                │                  ├──────────────────────────────────►│                  │
  │                │                  │                  │                │ max 8/program    │
  │                │                  │                  │                │ max 25 global    │
  │                │                  │◄──────────────────────────────────┤                  │
  │                │                  │                   │                │                  │
  │                │                  │ 5. Insert         │                │                  │
  │                │                  │    request_routes │                │                  │
  │                │                  │    (DB::txn)      │                │                  │
  │                │                  │                   │                │                  │
  │                │                  │ 6. Dispatch       │                │                  │
  │                │                  │    Notify         │                │                  │
  │                │                  ├──────────────────────────────────────────────────────►│
  │                │                  │                   │                │    DB notify     │
  │                │                  │◄──────────────────────────────────────────────────────┤
  │                │                  │                   │                │                  │
  │                │                  │ 7. If urgent      │                │                  │
  │                │                  │    (score >=.65)  │                │                  │
  │                │                  │                     ──► CrossPostRequest               │
  │                │                  │                          (posts to chat)               │
  │                │◄─────────────────┤                   │                │                  │
  │                │                  │                   │                │                  │
  │◄───────────────┤                  │                   │                │                  │
  │  redirect to   │                  │                   │                │                  │
  │  request board │                  │                   │                │                  │
  │  + flash status│                  │                   │                │                  │
```

## Routing Weight Formula

```
Score = 0.40 * edge_weight
      + 0.25 * normalized_resource_count
      + 0.20 * historical_fulfillment_rate
      + 0.10 * year_proximity_bonus
      + 0.05 * urgency_multiplier
      - 0.05 * is_own_program_penalty
```

### Weight Rationale

| Weight | Factor | Why |
|--------|--------|-----|
| **0.40** W_EDGE | Curriculum edge weight (how strongly this subject maps to this program) | Most important signal — the subject *is* in this program's curriculum |
| **0.25** W_RESOURCE | Normalized resource count (program's resources / total resources for subject) | More resources = more likely to have a match |
| **0.20** W_HISTORY | Historical fulfillment rate (how often past requests to this program got accepted) | Real data on actual success |
| **0.10** W_PROXIMITY | Year level proximity (bonus for 0-2 year difference) | Closer year levels = more relevant materials |
| **0.05** W_URGENCY | Urgency boost (1.0 for urgent, 0.5 for normal, 0.3 for low) | Minor boost for time-sensitive requests |
| **-0.05** self | Self-program penalty | Prevents routing only to own program |

### Thresholds
- **PROGRAM_THRESHOLD = 0.35**: Programs scoring below this are excluded
- **CHAT_THRESHOLD = 0.65**: Urgent requests cross-post to chat for programs above this
- **MAX_USERS_PER_PROGRAM = 8**: Don't flood a single program
- **GLOBAL_USER_CAP = 25**: Don't flood the entire school
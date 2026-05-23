# StudHub Demo Deck — Recording Guide

## Files

- `studhub_demo_deck.pptx` — 12-slide PowerPoint presentation (auto-generated)
- `studhub_deck.py` — Python script that regenerates the `.pptx`
- `README.md` — This file

## Prerequisites

```bash
pip install python-pptx
python studhub_deck.py          # regenerates studhub_demo_deck.pptx
```

## Recording the Deck (3 minutes max)

**Tool:** OBS Studio, Loom, or any screen recorder.

**Setup:**
- 1920×1080 display
- Slide show set to "Present with Speaker View" in PowerPoint
- Speaker notes visible on second monitor (or use Presenter View)

**Narration flow (target 2:30–3:00):**

| Slide | Timer | What to say |
|---|---|---|
| 1 — Title | 0:00 | "Good morning, panel. I am [Name] from BSIT. Today I present StudHub, a cross-program academic resource exchange platform for SEAIT." |
| 2 — Problem | 0:15 | "The problem: when a BSCE student needs a Data Structures reviewer that a BSIT student already has, the workflow is post in a Facebook group, scroll Messenger, or give up. No directory. No cross-program discovery." |
| 3 — Solution overview | 0:35 | "StudHub solves this with three layers. Per-program chat for everyday coordination. A subject-tagged resource catalog. And a routing engine that automatically matches requests to the programs most likely to fulfill them." |
| 4 — Architecture | 0:50 | "Built on Laravel 11 with Livewire and Laravel Reverb for real-time WebSocket messaging. MySQL, Redis, Pest testing, PHPStan Level 6." |
| 5 — Per-program chat | 1:05 | "Every program gets its own real-time chat room. Rooms are auto-provisioned. Students can only see their own program's rooms. @mentions trigger email notifications. File attachments are MIME-validated at 25 MB." |
| 6 — Resource catalog | 1:20 | "Resources are tagged by subject, not program. This means a BSIT student's Data Structures notes are visible to any SEAIT student searching for that subject. PDFs are watermarked per-user on download." |
| 7 — Routing engine | 1:40 | "This is the core innovation. When a request is posted, the routing engine scores every program using five factors: curriculum weight, resource density, historical fulfillment rate, year proximity, and urgency. Programs above a 0.35 threshold get notified." |
| 8 — Key innovation | 1:55 | "The self-improving feedback loop is historicalFulfillmentRate. Every time an offer is accepted, that program's fulfillment rate for that subject goes up, boosting its routing weight for future requests. New programs start neutral at 0.5." |
| 9 — Moderation | 2:10 | "StudHub has a full moderation layer: school-scoped reports, karma system with atomic increments, badge tiers, and an audit log that snapshots message content before hiding." |
| 10 — Pilot | 2:25 | "The pilot runs this semester with BSIT, BSCE, and BSBA-MM. Target: 100 resources uploaded, 50 cross-program fulfillments, median response under 24 hours." |
| 11 — Lessons | 2:40 | "Key lessons: [to be filled after pilot]. Areas for v2: multi-school deployment, resource versioning, mobile app." |
| 12 — Q&A | 2:55 | "Thank you. I am ready for questions." |

## Running the Demo (live, 3 minutes)

Recommended demo flow alongside the slides:

```
0:00–0:30  — Landing page, register, onboard as BSIT student
0:30–1:00  — Browse catalog, search "IT 211", save to shelf
1:00–1:30  — Post a resource request, show routing result
1:30–2:00  — Send a chat message with @mention
2:00–2:30  — Show leaderboard + badge tier
2:30–3:00  — Moderation dashboard as admin (pre-created)
```

## Customizing the Deck

Edit `studhub_deck.py` — all slide content is in the `build_deck()` function.
Re-run with: `python studhub_deck.py`

Replace `[Student Name]`, `[TBD — Adviser Name]`, etc. in `studhub_deck.py` before generating.
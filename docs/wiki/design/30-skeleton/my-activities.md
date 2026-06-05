---
title: My activities — page brief
tags: [design, skeleton]
sources: [wiki/design/20-structure, wiki/design/01-concerns, wiki/strategy/20-personas]
phase: design
updated: 2026-06-05
---

# My activities — page brief

Account-only view. **Audience: volunteers** (the only kind of site account — `group_user`). Not for families.

> **Status:** rests on `D-1` (**evidence gate closed, Alexandre/J3 2026-06-05** — back-office IN, per-event attendance CUT, volunteer roster added) and `D-2` (**closed**: meetups fully public, surfaced on chapter pages only). Remaining: the default-municipality filter on *this* page is a to-test skeleton detail.

## Strategy

*Why this page exists:* give a logged-in volunteer one personal place for what's coming up for their chapter(s) and a way into their chapter back-office + the volunteer roster. Meetups themselves are public (D-2); the value of *this* page is personal — my chapter's agenda + my way in — not exclusive access. **There is no per-event attendance** (cut — D-1; volunteers confirm turnout via WhatsApp polls).

*User mental state:* "What's on for my chapter — which rides and meetups are coming up, and where do I get the materials?"

## Scope

**In:**
- **Upcoming rides + meetups for my chapter(s)** — soonest first, default-filtered to your municipality (to test), with cross-group filtering. (Meetups are public — `D-2` — so this is convenience, not gated access.)
- **Shortcut into my chapter's back-office** (material library + new-chapter onboarding) and the **volunteer roster** — a *separate* per-chapter surface, linked from here.

**Out (won't-have):**
- **Per-event attendance / "I'm coming"** — cut (D-1, Alexandre/J3 2026-06-05); no attendance list on this page.
- Family-facing content (families have no account).
- The back-office itself — that's its own per-chapter page, not embedded here.
- Spacefunding/membership status — paying is external (Growfunding), not a site account.

## Structure

1. **My upcoming** — rides + meetups for my chapter(s), soonest first.
2. **Meetups & workshops** — default-filtered to mine, cross-group filterable (public; here as a personal shortlist).
3. **My chapter(s)** — link(s) into the back-office + volunteer roster for the group(s) I belong to.

## Skeleton (desktop)

```
┌───────────────────────────────────────────────┐
│  My activities                                  │
│  ┌── My upcoming (my chapter(s)) ─────────────┐ │
│  │ Sun 14 Sep · Schaerbeek ride               │ │
│  │ Tue 16 Sep · Volunteer meetup              │ │
│  └────────────────────────────────────────────┘ │
│  ┌── Meetups & workshops (all groups) ────────┐ │
│  │ [activity cards · PAT-1]                   │ │
│  └────────────────────────────────────────────┘ │
│  My chapter: Schaerbeek → [ Open back-office ]   │
└───────────────────────────────────────────────┘
```

Mobile: my upcoming → meetups → my chapter (stacked).

## Notes / open

- Reuses the event card [PAT-1]; no bespoke layout, **no attendance control** (PAT-18 retired — attendance cut, D-1).
- `group_user` already models the volunteer↔group link (many-to-many, per content model). **Do not add an `Attendance` relation** — the social need is met by the standing volunteer roster (`group_user.is_public`).
- The **per-chapter back-office** (material library + roster) is a related new surface (flagged provisional in the [page registry](00-page-registry.md)); content brief is now concrete (D-1) — spec for Build.

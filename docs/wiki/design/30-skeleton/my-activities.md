---
title: My activities — page brief
tags: [design, skeleton]
sources: [wiki/design/20-structure, wiki/design/01-concerns, wiki/strategy/20-personas]
phase: design
updated: 2026-06-02
---

# My activities — page brief

Account-only view. **Audience: volunteers** (the only kind of site account — `group_user`). Not for families.

> **Status:** rests on `D-1` (validated — build it) and `D-2` (**closed**: meetups fully public, surfaced on chapter pages only). Remaining: D-1 detail (pending Alexandre); the default-municipality filter on *this* page is a to-test skeleton detail.

## Strategy

*Why this page exists:* give a logged-in volunteer one personal place for what they've said they'll attend (rides + meetups) and a way into their chapter back-office. Meetups themselves are public (D-2); the value of *this* page is personal — attendance + my chapter — not exclusive access.

*User mental state:* "What's on for me — which rides am I helping at, what meetups are coming up?"

## Scope

**In:**
- **Upcoming meetups/workshops** — a personal view, default-filtered to your municipality (to test), with cross-group filtering. (Meetups are public — `D-2` — so this is convenience, not gated access.)
- **Activities I'm attending** — rides *and* meetups the volunteer marked **"I'm coming"** ([PAT-18](../40-patterns.md)). Attendance is account-only and volunteer-only.
- **Shortcut into my chapter's back-office** (materials/checklist/archive + attendance) — a *separate* per-chapter surface, linked from here.

**Out (won't-have):**
- Family-facing content (families have no account).
- The back-office itself — that's its own per-chapter page, not embedded here.
- Spacefunding/membership status — paying is external (Growfunding), not a site account.

## Structure

1. **My upcoming** — rides + meetups I'm attending, soonest first.
2. **Meetups & workshops** — default-filtered to mine, cross-group filterable (public; here as a personal shortlist).
3. **My chapter(s)** — link(s) into the back-office for the group(s) I belong to.

## Skeleton (desktop)

```
┌───────────────────────────────────────────────┐
│  My activities                                  │
│  ┌── I'm attending ───────────────────────────┐ │
│  │ Sun 14 Sep · Schaerbeek ride · ✓ coming    │ │
│  │ Tue 16 Sep · Volunteer meetup · ✓ coming   │ │
│  └────────────────────────────────────────────┘ │
│  ┌── Meetups & workshops (all groups) ────────┐ │
│  │ [activity cards · PAT-1 · with I'm-coming] │ │
│  └────────────────────────────────────────────┘ │
│  My chapter: Schaerbeek → [ Open back-office ]   │
└───────────────────────────────────────────────┘
```

Mobile: I'm attending → meetups → my chapter (stacked).

## Notes / open

- Reuses the event card [PAT-1] with an attendance control [PAT-18]; no bespoke layout.
- Confirm `group_user` already models the volunteer↔group link (it does, per content model) and add an **Attendance** relation (volunteer ↔ activity).
- The **per-chapter back-office** is a related new surface (flagged provisional in the [page registry](00-page-registry.md)); brief it separately once `D-1` detail lands (Alexandre).

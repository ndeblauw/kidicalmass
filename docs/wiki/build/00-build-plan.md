---
title: Build — plan & status
tags: [build]
sources: [wiki]
phase: build
updated: 2026-06-01
---

# Build — plan & status

**Method:** make it real in the Laravel/Filament platform Nico is building.

> **Status: not opened (YAGNI).** Per Cascade, later phases stay undecided until reached. Build is not yet open. Build-time decisions currently ride along as design concerns and will graduate to `build/01-concerns.md` when this phase opens (coding days were scheduled for 2026-06-16/17).

## Already on `main` (built ahead of this structure)

- `Activity` model — types `kidicalmass` / `meeting` / `workshop` / `other`, bilingual NL/FR, linked to one or more groups, has an organizer.
- `Article` model (News) and a `group_user` pivot.
- **Not built yet:** the per-chapter **back-office** (material library) and the **volunteer roster** (opt-in `group_user.is_public` flag) — that's what login gates now (D-1). **Do NOT build an `Attendance` (volunteer ↔ activity) relation** — per-event attendance was cut (Alexandre/J3, 2026-06-05). Meetups are **public to view** (D-2), so no view-gate column is needed.

## When this phase opens, it will need

- `DESIGN.md` — design tokens (the Surface plane, see [design/00-design-plan.md](../design/00-design-plan.md)). Not yet created.
- `01-concerns.md` — the build register.
- A redirect map (old Wix URLs → new), critical before launch.

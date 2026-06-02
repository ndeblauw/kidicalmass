---
title: Design — plan & status
tags: [design]
sources: [wiki/ux-planning, notion]
phase: design
updated: 2026-06-01
---

# Design — plan & status

**Method:** turn the locked [strategy brief](../strategy/00-strategy-brief.md) into an actionable plan across Garrett's planes: Scope → Structure → Skeleton → Surface. Upper planes constrain lower — work top-down.

## Plane status

| Plane | File | Status |
|---|---|---|
| Scope (plane 2) | [`10-scope.md`](10-scope.md) | ✅ complete |
| Structure (plane 3) — sitemap + nav | [`20-structure.md`](20-structure.md) | ✅ complete |
| Structure — content model | [`20-structure.md`](20-structure.md) § Content Model | ✅ tabulated (verify vs live schema) |
| Content migration | [`25-content-migration.md`](25-content-migration.md) | ✅ complete |
| Skeleton (plane 4) — per-page briefs | [`30-skeleton/`](30-skeleton/) — 7 specs + 7 content companions | ✅ complete |
| Skeleton — page registry / build tracker | [`30-skeleton/00-page-registry.md`](30-skeleton/00-page-registry.md) | ✅ created |
| Patterns library | [`40-patterns.md`](40-patterns.md) | 🟡 first pass (extracted from specs) |
| Surface (plane 5) | [`50-surface.md`](50-surface.md) → seeds `DESIGN.md` | 🟡 direction only; tokens pending |
| Asset map | [`60-asset-map.md`](60-asset-map.md) + [`61-asset-slots.md`](61-asset-slots.md) | ✅ complete |

Open design-plane decisions: [`01-concerns.md`](01-concerns.md).

## Principles

*Project-specific tuning. These take precedence over any default UX playbook.*

**Kept as-is:**
- User needs lead; org goals follow — strategy is consistently family-first throughout
- Prioritise 2–3 audiences explicitly — three named and ranked audiences
- Organise around user tasks, not org structure — Events = location-first discovery, not org nav
- Mobile-first always — confirmed for all page specs

**Project-specific principles:**
- **Template over approval** — strict design constraints (fixed templates, design system) replace Leticia's manual sign-off. The system guarantees quality, not a person.
- **Bilingual as structural** — NL/FR/EN are routed URL paths, not content stacks. Every content decision must be trilingually viable.
- **Local before national** — chapter pages are first-class citizens. The national site enables local discovery; it doesn't replace it.
- **The site mirrors the org's governance** — the IA reflects the structure (local → national); it doesn't flatten it. *(Leticia: "de website geeft de structuur aan van de organisatie.")*

**Tuned:**
- **Maintainability gate** — the backstage is a Laravel/Filament platform built by Nico. The maintainability test = can a chapter lead do this without coordination duo involvement? If not, it is a scope risk.
- **Content lifecycle** — formal lifecycle documentation is replaced by a clear ownership model: coordination duo owns national content; chapter leads own chapter-level content; any chapter lead can publish news.

**Suspended for this project:**
- **North Star metric** — the movement is community-driven, not conversion-driven. "Families who show up to their first ride" is the closest proxy, but is not currently trackable via the site alone. Suspended as a formal KPI for MVP.
- **Free-text search** — suspended for MVP. Volume (~60 events/season, ~20 chapters) makes location filter sufficient.

---
title: Image Map — New Site Slots
tags: [assets, images, design]
sources: [wiki/design/30-skeleton]
phase: design
updated: 2026-04-13 (photography expanded)
---

# Image Map — New Site Slots

Per-page image slot map for kidicalmass.be. Each row is one slot on one page, cross-referenced against the asset catalogue in [60-asset-map.md](60-asset-map.md).

**Status key:**
- ✅ Have it — a specific named asset is ready to use
- ⚠️ Candidate — asset exists but needs quality review or may not be ideal
- ❌ Missing — nothing usable exists; production or sourcing required

---

## Home `/`

| Slot | Spec | Status | Asset | Notes |
|------|------|--------|-------|-------|
| Hero — photo | Full-bleed landscape photo, children on bikes in a Belgian city street, joyful and alive | ✅ Have it | `photography/ride-brussels-cinquantenaire-crowd.jpg` | 5472×3648 — excellent resolution. Strong alternatives: `ride-brussels-palais-crowd.jpg`, `ride-girl-smiling-on-bike.jpg`, `ride-brussels-two-boys-at-start.jpg` |
| Hero — video (alternative) | Short looping video of a ride | ❌ Missing | — | Does not exist; would need production |
| Decorative illustrations | Brand characters scattered around the page (events strip, stats section, footer area) | ✅ Have it | `illustrations/` — full set of 9 characters | Already in `public/img/illustrations/`, actively used |

---

## About — Mission & Overview `/about/mission`

| Slot | Spec | Status | Asset | Notes |
|------|------|--------|-------|-------|
| Hero photo | Full-width photo: children on bikes in a real Belgian city street setting; not a posed shot; Brussels preferred | ✅ Have it | `photography/ride-brussels-cinquantenaire-crowd.jpg` | 5472×3648 — Cinquantenaire Arch as landmark. Also strong: `ride-brussels-palais-crowd.jpg`, `ride-girl-smiling-on-bike.jpg`, `ride-girl-pink-jacket-crossing.jpg` |

---

## Activity Detail (event page) `/events/[slug]`

| Slot | Spec | Status | Asset | Notes |
|------|------|--------|-------|-------|
| Ride photo (per chapter, CMS) | Optional landscape photo associated with the chapter's ride — uploaded by organisers | ❌ Missing | — | No chapter-specific ride photos are organised; must be tagged and sourced per chapter over time |
| Route / location map | Interactive embed (not a photo) | N/A | — | Not an image slot |

---

## Chapters overview & chapter page `/chapters`

| Slot | Spec | Status | Asset | Notes |
|------|------|--------|-------|-------|
| Chapter organiser photo (per chapter, CMS) | Optional portrait photo of team members — uploaded by organisers | ❌ Missing | — | Entirely CMS-dependent on organiser uploads; no photos exist |
| Brussels region map illustration | Overview map showing all chapter areas | ⚠️ Candidate | `illustrations/chapter-map-brussels.png` | 288×268 px — low resolution; diagram only shows chapter bubbles, no street detail. Suitable as a simplified icon/graphic, not for a detailed map slot |
| "Viens rouler" chapter flyer (per chapter) | Optional branded chapter recruitment poster | ✅ Have it | `posters/chapters/chapter-[municipality].png` | All 12 Brussels municipalities covered; ~200×281 px thumbnails (print-scale originals may exist) |

---

## News `/news`

| Slot | Spec | Status | Asset | Notes |
|------|------|--------|-------|-------|
| Article cover image (per article, CMS) | Optional 16:9 image per news article | ❌ Missing | — | Per-article; sourced over time as articles are written |

---

## Help Out `/help-out`

Five volunteer roles. Each needs an illustration or photo.

| Slot | Role | Spec | Status | Asset | Notes |
|------|------|------|--------|-------|-------|
| Role image — Safety volunteer (Gilet rose) | Ride safety volunteer in pink vest | Photo or illustration of a pink-vest volunteer | ✅ Have it | `photography/volunteer-pink-vest.jpg` (also at `public/img/pink-vest-volunteer.jpg`) | Already in use; portrait orientation 740×987 |
| Role image — Chapter organiser | Ride organiser, community leader | Photo or illustration showing someone leading a group ride | ✅ Have it | `photography/volunteers-pink-vests-with-flag.jpg` or `volunteers-group-pink-vests-park.avif` | Multiple strong options now available |
| Role image — Communication / social media | Online community management | Illustration or photo | ⚠️ Candidate | `photography/team-blue-sweatshirts-celebration.jpg` or brand character `illustrations/person-with-boombox.png` | Team photo works; not role-specific but warm and on-brand |
| Role image — Logistics | Event logistics support | Illustration or photo | ⚠️ Candidate | `photography/volunteer-selfie-stop-sign.jpg` | Volunteer with STOP sign directly represents traffic/safety logistics role |
| Role image — Start your own chapter | Potential chapter lead | Illustration or photo | ⚠️ Candidate | `photography/volunteers-pink-vests-cinquantenaire.jpg` | Joyful group of volunteers with Brussels landmark; works as aspirational image |

---

## Getting Started `/getting-started`

| Slot | Spec | Status | Asset | Notes |
|------|------|--------|-------|-------|
| Decorative illustrations | Brand characters interspersed through the FAQ / fact cards section | ✅ Have it | `illustrations/` — full character set | Already in use across the site |

---

## Events Overview `/events`

| Slot | Spec | Status | Asset | Notes |
|------|------|--------|-------|-------|
| Decorative / empty-state illustration | Illustration shown when no upcoming events match the filter | ✅ Have it | Any character from `illustrations/` | No dedicated empty-state illustration; use existing characters |

---

## Summary: Gaps to resolve

| Priority | Gap | Recommended action |
|----------|-----|--------------------|
| ~~🔴 High~~ ✅ Resolved | Homepage + About hero — no high-res ride photos | Multiple 5–50 MP Brussels ride photos now available |
| 🔴 High | Homepage hero video | Would need dedicated video production during a 2026 ride |
| 🟡 Medium | Help Out role illustrations for 4 roles other than safety volunteer | Commission 4 new illustrations matching the brand character style, or use photography |
| 🟡 Medium | Chapter team photos | Organisers to upload via CMS when chapter pages go live |
| 🟡 Medium | Per-chapter ride photos | Tag and collect photos per chapter after each ride season |
| 🟢 Low | Chapter map illustration at higher resolution | Request or recreate SVG version of the Brussels chapter map diagram |
| 🟢 Low | News cover images | Accumulate naturally as news articles are written |

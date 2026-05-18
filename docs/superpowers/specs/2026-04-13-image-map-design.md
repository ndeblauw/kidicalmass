# Image Map Design

**Date:** 2026-04-13  
**Status:** Approved

## Goal

Create a structured image library from the old Wix site assets, document it in the wiki, and produce a per-page image slot map for the new site so the team knows exactly what photography and illustration exists and what still needs to be produced or sourced.

---

## Deliverables

Three outputs:

1. **`docs/raw/assets/`** — organised, renamed copies of usable assets from `docs/raw/website/assets/`
2. **`docs/wiki/image-map.md`** — catalogue of those assets (the old site inventory)
3. **`docs/wiki/image-map-new-site.md`** — per-page image slot map for the new site (have / candidate / missing)

The original files in `docs/raw/website/assets/` remain untouched (immutable source rule).

---

## Section 1 — Asset organisation

### Folder structure

```
docs/raw/assets/
  illustrations/       # brand characters + decorative elements
  photography/         # usable event, ride, and team photos
  posters/
    chapters/          # 12 municipality "Viens rouler" flyers
    events/            # Big Kidical Mass banners, end-of-year posters, etc.
  logos/               # partner logos + Kidical Mass brand logos
```

### Naming convention

`kebab-case`, descriptive, no dates unless version-relevant.

- Illustrations: `bird-with-helmet.png`, `volunteer-with-sign.png`
- Photos: `ride-brussels-street-crowd.jpg`, `volunteer-team-blue-tshirts.jpg`
- Posters: `chapter-anderlecht.png`, `event-big-kidical-mass-2026.jpg`
- Logos: `partner-families-on-bike.png`, `partner-fiets-bieb.png`

### Exclusion criteria

Do NOT copy assets that match any of the following:

- Pixel dimensions too small to be usable on a screen (under ~600px wide)
- Visually blurry or dark beyond salvage
- Wix UI elements (social media icons, PDF icon) — not website assets
- Screenshots
- QR codes and one-off campaign items (e.g. Growfunding QR)
- Stock photos with unclear license (iStock)
- Third-party event assets (e.g. S4K Spring 2025)
- Instagram/social story formats (formatted for a different medium, dated)

---

## Section 2 — Wiki image map (`docs/wiki/image-map.md`)

One document cataloguing everything in `docs/raw/assets/`. Divided into four sections matching the folder structure: Illustrations, Photography, Posters, Logos.

### Entry fields

| Field | Description |
|-------|-------------|
| **Filename** | Clean name in `docs/raw/assets/` |
| **Type** | Illustration / Photography / Poster / Logo |
| **Description** | Subjects, mood, setting, colours |
| **Quality** | ★★★ excellent · ★★ good · ★ marginal |
| **In use** | Whether already in `public/img/` on the new site |
| **Source** | Original filename from `docs/raw/website/assets/` |

Photography entries additionally note:
- **Orientation** — landscape / portrait / square (matters for hero slots)
- **People visible** — yes / no (GDPR/consent context)

---

## Section 3 — New site image map (`docs/wiki/image-map-new-site.md`)

Structured around pages and their image slots, drawn from the UX specs in `docs/wiki/ux/`. Each row is one slot on one page.

### Entry fields

| Field | Description |
|-------|-------------|
| **Page** | e.g. Home, About, Activity Detail |
| **Slot** | e.g. Hero photo, Illustration (decorative), Team photo |
| **Spec** | What the UX doc says the slot needs |
| **Status** | ✅ Have it · ⚠️ Candidate (needs review) · ❌ Missing |
| **Asset** | Filename from `image-map.md` if one exists |
| **Notes** | Constraints: orientation, resolution, consent, production needed |

### Pages in scope (from UX specs)

| Page | Slots |
|------|-------|
| Home | Hero photo or looping video; decorative illustrations |
| About | Full-width hero photo (children on bikes, Belgian city street) |
| Activity Detail | Optional ride photo per chapter (CMS); map embed (not a photo) |
| Chapters | Optional team member photos per chapter (CMS-driven) |
| News | Optional 16:9 cover image per article |
| Help Out | Illustration or photo per volunteer role (5 roles) |
| Getting Started | Decorative illustrations |

---

## Section 4 — Preliminary have vs. need

### Have (confirmed usable)

- **Illustrations** — complete brand character set; already clean in `public/img/`
- **Professional ride photos** — 4 high-quality photos from the `3O4A` series (Brussels setting)
- **Crowd/street ride photos** — ~2 strong candidates from the `cf0153_` set (more to assess)
- **Chapter posters** — 12 municipality flyers (one per Brussels chapter)
- **Event posters** — Big Kidical Mass 2025 and 2026
- **Partner logos** — Families on Bike, Fiets Bieb, Loopz
- **Brussels chapter map** — diagram showing all chapter locations

### Gaps

| Slot | Gap | Notes |
|------|-----|-------|
| Homepage hero photo | Candidates exist, need full-res quality check | A looping video doesn't exist — would need production |
| About hero photo | Same candidates, same caveat | Must be real Belgian city street, not stock |
| Chapter team photos | None | Entirely CMS-dependent on organiser uploads |
| Per-chapter ride photos | None organised by chapter | Need tagging or sourcing per chapter |
| News cover images | None | Per-article, sourced over time |
| Help Out role illustrations | Partial | Volunteer-with-sign covers one role; others use generic characters |

---

## Implementation notes

- View all remaining unassessed photos during implementation to apply quality threshold
- Duplicate webp/jpg pairs in `docs/raw/website/assets/` (e.g. same image in both formats): copy only the higher-quality format
- `public/img/` already has clean illustration names — cross-reference to avoid redundancy in the catalogue
- The `docs/raw/assets/` copies are for documentation and design reference only, not for direct use in the app (app uses `public/img/`)

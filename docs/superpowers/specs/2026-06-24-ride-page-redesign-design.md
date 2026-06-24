---
title: Ride page redesign — align to the new (chapter-style) look
tags: [design, ride-page, activities, surface]
sources:
  - public/ride-page-redesign-prototype.html
  - resources/views/activities/show.blade.php
  - resources/views/groups/show.blade.php
  - resources/css/pages/activity.css
  - resources/css/pages/chapters.css
phase: design
updated: 2026-06-24
---

# Ride page redesign — align to the new look

## Problem

The ride page (`activities.show`, rides only) was the first page designed and its
look has drifted. It leans on **intense colour and full-viewport blocks**: seven
stacked full-bleed colour bands (blue → yellow → sky → white → light-blue →
light-yellow → yellow), tilted "sticker" cards, no calm white reading column.

Recent pages (the chapter page, `groups.show`) settled on a **calmer** language:
a white in-container body with soft rounded cards (`--radius-card` +
`--shadow-card`), quiet uppercase eyebrows, light icon-chips, and full-bleed
colour reserved for just the hero and the closing band.

**Goal:** bring the ride page onto that newer language — more white, fixed
container, colour as accent rather than as the whole page.

## Chosen direction

**Direction 1 ("Chapter-trouw")** from the prototype
(`public/ride-page-redesign-prototype.html`, view "1 · Chapter-trouw"): port the
chapter page's structure directly. Two decisions resolved with Frederik:

- **Steun + Deel:** keep both, but as **quiet contained sections** inside the
  white body — not full-bleed colour bands.
- **Hero:** adopt the **chapter identity hero** — blue band with a 3°-tilted
  photo card dipping into the white below (`.page-hero--photo-tilt`), replacing
  the current circular-photo hero.

## Target layout (top → bottom)

Full-bleed colour is reserved for exactly **two** moments: the blue hero and the
yellow closing band. Everything between lives in a centered container with the
chapter body rhythm (`clamp(3rem … 4.5rem)` vertical section gaps).

1. **Hero — blue, full-bleed.** Chapter identity-hero pattern: `<h1>` white, date
   in yellow (Caprasimo), the chapter "pin" lockup, and a tilted photo card on
   the right dipping into the white body. Reuse `.page-hero` / `--photo-tilt`
   metrics rather than the bespoke `.activity-hero` poster grid.
2. **Praktisch (facts + map) — contained white feature card.** A single
   `--radius-card` + `--shadow-card` card: left column = the meta `<dl>`
   (Startuur / Vertrekpunt / Afstand / Duur / Deelname) rendered as **light
   icon-chips** (`<x-icon-chip>` style, light-blue tiles) instead of the rotated
   red rounded-square tiles; right column = `<x-route-map>` with the "Bekijk op
   Komoot" action. Models the chapter `<x-next-ride>` card, but it is a dedicated
   *facts* card (we are already on the ride), not the next-ride preview component.
3. **Beschrijving — contained prose.** Free-text description in a narrow measure
   (~58ch), `lead` type for the opening line.
4. **Wat kun je verwachten? — contained white section.** Uppercase eyebrow + the
   four promises as **soft chapter-style cards** (`--radius-card`,
   `--shadow-card`, **no tilt**), 2×2 grid within the container. Drops the
   full-bleed sky band.
5. **Van en voor de buurt — contained white section.** Eyebrow + heading
   crediting the organising group(s), the organiser team row, and the
   `<livewire:volunteer-signup>` reveal ("Roze hesje worden?" → inline form).
   Drops the asymmetric full-bleed edge photo.
6. **Steun — contained quiet section.** Keep `<x-support-callout variant="event">`
   but render it inside the container as a quiet section, not a full-bleed
   light-blue band.
7. **Deel — contained quiet section.** Keep `<x-share-band>` inside the
   container as a quiet section, not a full-bleed light-yellow band.
8. **Slot-CTA — yellow, full-bleed.** Keep `<x-closing-cta>` in the `closing`
   slot (yellow band fused with the footer). Unchanged — this is the second and
   final full-bleed colour moment.

## Components & reuse

- **Reuse as-is:** `<x-route-map>`, `<livewire:volunteer-signup>`,
  `<x-closing-cta>`.
- **Reuse, restyle into container:** `<x-support-callout>`, `<x-share-band>` —
  may need a `variant`/modifier for the quiet contained treatment so the
  full-bleed-band variant stays available elsewhere.
- **Adopt shared pattern:** the `.page-hero` / `--photo-tilt` hero, the
  `<x-icon-chip>` light meta chips, the soft-card + eyebrow rhythm from
  `.chapter-body`.
- **Retire:** the `.activity-hero` poster grid, the rotated red rounded-square
  icon tiles, the full-bleed 50/50 info+map band, the full-bleed sky promises
  band with tilted sticker cards, the 3fr/2fr full-bleed organisers band.

## CSS

- Page styles stay in `resources/css/pages/activity.css` (rewrite the
  `.activity-*` band machinery toward container + soft-card rules; keep the
  basic-page rules at `activity.css:701+` untouched).
- Any treatment shared with the chapter page (body rhythm, soft card, icon-chip)
  should reference existing components/tokens rather than duplicate values. New
  reusable CSS goes in `resources/css/components/*`, never `app.css`. Honour
  `CssArchitectureTest` (no raw hex/px in `.blade.php` components; partials
  registered).
- Headings stay raw `<h1>`–`<h6>`; type scale comes from `@layer base`.

## Out of scope

- `activities/show-basic.blade.php` (workshops/meetings) — left untouched for
  now; a follow-up can align it once the ride page lands.
- Real photography and the live map data — placeholders/real data wired by the
  backend; this work is layout + surface only.
- Copy rewrites beyond what the structure requires (tone-of-voice pass is
  separate).

## Verification

- `php artisan test --filter=CssArchitectureTest` passes.
- Render `activities.show` for a real ride and confirm: two full-bleed colour
  bands only (hero + slot), everything else contained; soft cards, no tilted
  sticker cards; map + volunteer reveal still work.
- Page-registry (P-nn) Wire/UI stages bumped per `/pipeline` once Frederik has
  done his own critique pass.

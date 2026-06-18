---
title: Start-een-groep — three scrollytelling story slides
tags: [design, build, start-een-groep, scroll-sequence, photo-collage]
sources:
  - resources/views/groups/start.blade.php
  - resources/views/volunteer.blade.php
  - resources/views/components/scroll-sequence.blade.php
  - resources/css/pages/start-een-groep.css
  - resources/css/components/scroll-sequence.css
  - resources/css/pages/help-out.css
phase: build
updated: 2026-06-18
---

# Start-een-groep — three scrollytelling story slides

## Problem

`/nl/chapters/start-een-groep` reads dry and packed. Three text-dense sections
(*Wat jij brengt* / *Wat wij dragen*, then *Wat het écht vraagt*) stack tightly,
followed by a 6-cell editorial photo grid. The page needs breathing room and a
clearer rhythm.

## Goal

Give the content room to breathe by turning the three sections into three
**scrollytelling story slides** — the Help-out (`/nl/help-out`) treatment: a
single **sticky** collage column on one side that **crossfades** through one
collage per slide as the reader scrolls the text beside it. The collage column
does **not** alternate sides; it stays on the right for all three slides.

## Scope of change

Four edits, no new components. Reuses `x-scroll-sequence`, `x-titled-list-block`,
`x-cta-button`, and the existing "Er is animo" card markup.

### 1. Intro CTA — center it

Today `.sg-intro` is a two-column grid (`minmax(0,1fr) auto`), so the CTA is
pushed to the far right. Collapse `.sg-intro` to a single column and center the
CTA below the intro paragraph (`.sg-intro__action { justify-self: center }`;
drop the `48rem` two-column rule). Markup unchanged apart from this being a
single-column flow.

### 2. Three story slides — replaces `.sg-deal` + `.sg-asks` + `.sg-proof` gallery

One `<x-scroll-sequence media-side="right">` containing three collages in the
`media` slot and three text blocks. `x-scroll-sequence` already drives N blocks:
its IntersectionObserver reads each `[data-seq-block]` index and toggles the
matching `[data-seq-media]` to `.is-active`. No component change needed.

Keep the umbrella heading **"Je hoeft dit niet alleen te dragen"** as a section
heading introducing the whole sequence (the emotional throughline framing all
three slides).

| # | `data-seq-block` text (existing copy, verbatim) | Collage `data-seq-media` |
|---|---|---|
| 0 | **Wat jij brengt** — `x-titled-list-block variant="ask"` (red chevrons), 4 items | collage 0 |
| 1 | **Wat wij dragen** — `x-titled-list-block variant="get"` (green checks), 6 items | collage 1 |
| 2 | **Wat het écht vraagt** — its lead sentence ("Eerlijk is eerlijk…") + the 4-item red-chevron list | collage 2 |

Slide 2's lead sentence and custom red-chevron list are preserved. Because
`x-titled-list-block variant="ask"` already renders the same red-chevron bullets
as the current `.sg-asks__list`, slide 2 can either keep its bespoke
`.sg-asks`-style list inside the block or move to `x-titled-list-block`; the
visual result (red chevrons) is identical. Implementation picks whichever keeps
the lead sentence cleanest — preserve the lead paragraph above the list either way.

**Collage CSS — page-local `sg-story__*`, mirroring `ho-deal__collage`:**

- A 1:1 collage stage; 2 photos per collage, absolutely placed by percentage
  (`--photo-x/y/w/r` custom properties) so the scatter holds at every width.
- Three distinct scatters (vary x/y/w/rotation per collage) so the beats don't
  read as the same frame three times.
- Crossfade choreography copied from `.ho-deal`: the non-active collage sits in a
  slightly "tossed" state (more rotation + `scale` up); the active one settles its
  photos into place; trail photo lands a beat after the lead
  (`transition-delay`). Opacity fade is the shared component's job.
- **No doodle** — photos only (decided).
- `@media (prefers-reduced-motion: reduce)`: photos rest at their base rotation,
  no transitions (mirror `.ho-deal`'s reduced-motion block).
- Mobile/tablet (`< 1024px`): the shared component stacks the collages and shows
  them at rest (no swap) — same as Help-out. Three collages stack inline.

**Photos:** six photos = three pairs. Seed from the five photos currently in the
gallery (`ride-brussels-two-boys-at-start`, `cargo-bike-mother-two-kids-flag`,
`ride-girl-smiling-on-bike`, `ride-group-celebration-station`,
`ride-brussels-boulevard-crowd`) plus one more from `img/photography/`. The wide
crowd photo (`ride-park-crowd-cheering-namur`) is reserved for the closing element
(below). **Photo curation is the designer's to refine** — pairings/placement are
trivially swappable via the markup + custom-property values.

### 3. Closing "Er is animo" element — replaces the rest of the gallery

A standalone two-column band where the 6-cell gallery used to be (before the FAQ,
keeping the current order): the **wide crowd photo**
(`ride-park-crowd-cheering-namur.webp`) beside the **light-blue "Er is animo"
card** (heading + live `$groupCount` sentence + `Ik wil starten` CTA). Reuse the
existing `.sg-proof__animo-card` markup/CSS; drop `.sg-proof__gallery` and its
`nth-child` placement rules entirely.

### 4. Untouched

Hero (`x-page-hero`), FAQ section (`.sg-faq-section` + its slide-in illustration
script), the Livewire signup form, and the closing yellow intent-form band
(`x-slot:closing`).

## Files

- `resources/views/groups/start.blade.php` — swap `.sg-deal` + `.sg-asks` +
  `.sg-proof` gallery for the `x-scroll-sequence` block and the closing animo
  band; collapse `.sg-intro` markup to single column.
- `resources/css/pages/start-een-groep.css` — rework `.sg-intro` (center CTA),
  add `.sg-story*` collage + crossfade rules, rework `.sg-proof` to the two-column
  closing band, delete `.sg-proof__gallery` / `.sg-proof__cell` grid rules.

No new CSS partial (page CSS already registered). No new Blade component.

## Testing

- Extend `tests/Feature/GroupsTest.php` (start-page test): assert the page renders
  the three slide titles ("Wat jij brengt", "Wat wij dragen", "Wat het écht
  vraagt"), the umbrella heading, the "Er is animo" text + `$groupCount`, and the
  intro CTA — and assert the old 6-cell gallery is gone (no `sg-proof__gallery`).
- Run `php artisan test --filter=CssArchitectureTest` (no raw hex/px; partials
  registered) and the start-page test.

## Build-pipeline note

`start-een-groep` is page P-?? in the registry. After implementation + Frederik's
own critique pass, update its row (Wire/UI) per the `/pipeline` flow and log it.

## Open / refine (non-blocking)

- Final photo selection + per-collage scatter values (designer).
- Which sticky side reads best (defaulting to `right`, matching Help-out).
- Whether slide 2 keeps its bespoke list or moves to `x-titled-list-block`
  (visual result identical).

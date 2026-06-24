---
title: Ride page lifecycle — upcoming vs just-past vs recap
tags: [ux, ride-page, activities, lifecycle, design]
sources:
  - resources/views/activities/show.blade.php
  - resources/views/groups/show.blade.php
  - app/Models/Activity.php
  - app/Livewire/RideCalendar.php
  - docs/superpowers/specs/2026-06-24-ride-page-redesign-design.md
phase: design
updated: 2026-06-24
---

# Ride page lifecycle — how the ride page behaves before and after the ride

UX planning (Garrett's planes, page-level) for the temporal behaviour of the ride
detail page (`activities.show`, rides only). Companion to the surface redesign in
[`2026-06-24-ride-page-redesign-design.md`](2026-06-24-ride-page-redesign-design.md):
that spec settles the *look*; this one settles how the page changes through a
ride's lifecycle. Site-level IA is already settled; this is a page-level deep-dive.

## Problem

The ride page today is built for one tense: **upcoming**. Every section assumes
the ride is still ahead — "Wat kun je verwachten?", "Roze hesje worden?", the
Steun/Deel asks, the closing "Lees hoe je meerijdt". Nothing changes once the
ride is over. A visitor who lands on a past ride still reads future-tense copy
for an event that already happened, which erodes trust (it can read as "this
movement is dead").

## Strategy

The page's core job **flips** when the ride ends, and it flips through three
states, not two.

| State | Trigger | Primary job | Dominant feeling |
|---|---|---|---|
| **Upcoming** | `begin_date` in the future | Convert this visitor → attendee of *this* ride | Reassurance, anticipation |
| **Just past** | past + no gallery photos | Collect photos (nudge vests + visitors) + warm "that was lovely" + gentle nudge forward | Belonging, participation |
| **Recap** | past + `hasGallery()` true | Pride & reliving (photos are the hero) + gentle convert to next | Memory, warmth |

**Who lands on a past page** (priority order): (1) someone who *was there*, looking
for photos / a memory to share — pride and reliving; (2) someone who *missed it*,
now curious or regretful — leave a warm impression and convert, **nudge not push**.
Press/partners and stale-link newcomers are secondary.

**Two constants:**
- **No attendance numbers exist** — photos carry the pride. Plan for zero headcount.
- **Tone is always nudge, never push.**

**The "just past" insight:** the no-photos-yet window is not a passive empty state.
It is an active *collection moment*: nudge the pink vests to upload their photos,
and tell visitors who have photos to hand them to the vests. The page turns its
own emptiness into participation, and a vest uploading photos *is* the publish
action that flips the page to Recap.

## Scope

One template, sections swap per state.

| Section | Upcoming | Just past | Recap |
|---|---|---|---|
| Hero | as-is | past tense + "Voorbij" marker | past tense, real ride photo |
| Praktisch (time/start/map) | priority | kept as record (demoted) | kept as record (bottom) |
| Beschrijving | as-is | keep | keep (context for the photos) |
| Wat kun je verwachten (4 promises) | shown | **dropped** | **dropped** |
| Photo block | — | **"deel je foto's" nudge** (vests + visitors) | **gallery wall — the hero of the body** |
| Van en voor de buurt (organisers + roze-hesje signup) | as-is | keep | keep |
| Steun | — (not shown) | keep | keep |
| Deel | "nodig een gezin uit" | "deel de herinnering" | "deel de herinnering" |
| Slot-CTA | "Lees hoe je meerijdt" | → **chapter** (+ newsletter secondary) | → **chapter** (+ newsletter secondary) |

**Data layer — already in place** (no new backend needed for the gallery):
- `Activity` has a `gallery` multi-file media collection (`card` + `thumb`
  conversions) plus a `hasGallery()` helper — the state trigger.
- Pink vests already upload via the backstage uploader (`ActivityPhotoUpload`),
  so "uploading photos" is already the publish action.
- The chapter page's gallery is a CSS-grid photo wall + custom Alpine lightbox,
  built inline in `groups/show.blade.php` (`.chapter-gallery__*`) — **not yet a
  reusable component**. Reusing "that layout" on the recap means extracting or
  copying that pattern.

## Structure

**State machine (content-driven, automatic):**
- future date → **Upcoming**
- past date + `hasGallery()` false → **Just past**
- past date + `hasGallery()` true → **Recap**

A vest uploading photos flips Just-past → Recap with zero extra admin. No manual
"publish recap" flag.

**One template, one swapping block:** Just-past and Recap are the same past-page;
only the photo block swaps (nudge ↔ gallery wall). Seamless transition when photos
land.

**Recap reading order inverts:** the gallery rises to the top of the body (under
the hero, above Praktisch); the practical record sinks toward the bottom. Relive
first, details last. Beschrijving sits between gallery and Praktisch as context.

### Cross-page IA

Adding a recap state makes the ride page a *detail/destination*; other pages
become previews/entry points. Most plumbing already exists:

- **Already linked:** the chapter's next-ride card → upcoming ride page; the
  calendar (`activities.index`) has a "voorbije ritten" toggle listing the 24 most
  recent past rides, each → its recap; home/calendar/ride-pills → `activities.show`.
- **New link to build:** the chapter "In beeld" wall is currently self-contained
  (buttons open an in-page lightbox; no link to the ride). **Decision: keep the
  wall's own lightbox AND add a "bekijk de hele rit" link through to the recap.**
  The wall becomes a teaser that also has a destination.
- **Discoverability:** latest recap is enough. No per-chapter past-rides archive
  for v1 (the chapter shows the latest recap; the global calendar "voorbije"
  toggle is the archive). `$pastRidesCount` stays unsurfaced for now.
- **Just-past lag accepted:** the chapter shows the latest ride *with photos*, so
  in the just-past window it still shows the previous recap. Let it lag; the
  just-past acknowledgement lives on the ride page only.
- **Forward path from a past page:** the **chapter is the primary CTA** ("Meer
  ritten van Kidical Mass [groep]" → `groups.show`) — a warmer, more local
  "what's next" than a generic newsletter. Newsletter is **secondary**: in
  Just-past the photo-nudge carries a quiet "schrijf je in zodat je de volgende
  rit niet mist"; in Recap a quiet opt-in can sit near the slot-CTA.

## Skeleton

### State 1 — Upcoming (today's page, unchanged)

```
┌──────────────────────────────────────────────┐
│ NAV                                          │
├──────────────────────────────────────────────┤
│ HERO ░ blue full-bleed                       │
│  h1 ride title · date (FUTURE) · chapter pin │
│  ▸ tilted photo card dips into white below   │
├──────────────────────────────────────────────┤
│ PRAKTISCH ░ white soft card                  │
│  meta dl (startuur/vertrek/afstand/duur) │map │
│  + "Bekijk op Komoot"                        │
├──────────────────────────────────────────────┤
│ BESCHRIJVING ░ prose, ~58ch                  │
├──────────────────────────────────────────────┤
│ WAT KUN JE VERWACHTEN ░ 4 promise cards 2×2  │
├──────────────────────────────────────────────┤
│ VAN EN VOOR DE BUURT ░ organisers + roze-    │
│  hesje signup reveal                         │
├──────────────────────────────────────────────┤
│ STEUN (contained)   │  DEEL (contained)      │
│  support callout    │  "nodig een gezin uit" │
├──────────────────────────────────────────────┤
│ SLOT-CTA ░ yellow ░ "Lees hoe je meerijdt"   │
│ FOOTER (fused)                               │
└──────────────────────────────────────────────┘
```

### State 2 — Just past (over, no photos yet)  ◆ = differs from upcoming

```
┌──────────────────────────────────────────────┐
│ NAV                                          │
├──────────────────────────────────────────────┤
│ HERO ░ blue full-bleed                       │
│  ◆ small "Voorbij" marker / past-tense date  │
│  h1 · chapter pin · tilted photo             │
├──────────────────────────────────────────────┤
│ ◆ PHOTO-NUDGE ░ takes the gallery's slot     │
│  "Dat was fijn. Foto's volgen binnenkort."   │
│  → roze hesje: deel je foto's (upload)       │
│  → bezoeker: bezorg ze aan de roze hesjes    │
│  ◆ quiet: schrijf je in voor de volgende rit │
├──────────────────────────────────────────────┤
│ PRAKTISCH ░ kept as record (demoted below)   │
├──────────────────────────────────────────────┤
│ BESCHRIJVING ░ prose                         │
├──────────────────────────────────────────────┤
│ ◆ (WAT KUN JE VERWACHTEN — dropped)          │
├──────────────────────────────────────────────┤
│ VAN EN VOOR DE BUURT ░ organisers + roze-    │
│  hesje signup                                │
├──────────────────────────────────────────────┤
│ STEUN (contained)   │ ◆ DEEL "deel de        │
│  support callout    │   herinnering"         │
├──────────────────────────────────────────────┤
│ ◆ SLOT-CTA ░ yellow ░ → chapter              │
│   "Meer ritten van Kidical Mass [groep]"     │
│ FOOTER (fused)                               │
└──────────────────────────────────────────────┘
```

### State 3 — Recap (photos up)  ◆ = differs from upcoming

```
┌──────────────────────────────────────────────┐
│ NAV                                          │
├──────────────────────────────────────────────┤
│ HERO ░ blue full-bleed                       │
│  ◆ past-tense date · h1 · chapter pin        │
│  ▸ tilted photo (ideally from the ride)      │
├──────────────────────────────────────────────┤
│ ◆ GALLERY WALL ░ HERO OF THE BODY            │
│  reuse chapter ".chapter-gallery" pattern:   │
│  feature poster + tiles + Alpine lightbox    │
│  ┌────────┬────┬────┐                        │
│  │feature │tile│tile│                        │
│  │poster  ├────┼────┤                        │
│  │        │tile│tile│                        │
│  └────────┴────┴────┘                        │
│  ◆ slim strip: "Nog foto's? Bezorg ze aan    │
│    de roze hesjes." (keeps collection open)  │
├──────────────────────────────────────────────┤
│ BESCHRIJVING ░ prose (context for the photos)│
├──────────────────────────────────────────────┤
│ PRAKTISCH ░ record, demoted near bottom      │
├──────────────────────────────────────────────┤
│ ◆ (WAT KUN JE VERWACHTEN — dropped)          │
├──────────────────────────────────────────────┤
│ VAN EN VOOR DE BUURT ░ organisers + roze-    │
│  hesje signup                                │
├──────────────────────────────────────────────┤
│ STEUN (contained)   │ ◆ DEEL "deel de        │
│  support callout    │   herinnering"         │
├──────────────────────────────────────────────┤
│ ◆ SLOT-CTA ░ yellow ░ → chapter (+ quiet     │
│   newsletter opt-in nearby)                  │
│ FOOTER (fused)                               │
└──────────────────────────────────────────────┘
```

## Out of scope / open for build

- Surface/visual styling — owned by the companion redesign spec.
- `activities/show-basic.blade.php` (workshops/meetings) — rides only.
- Copy is indicative; a tone-of-voice pass is separate.
- The calendar "voorbije" archive cap (24, no pagination) is a known limit; not
  addressed here.

# Fixed hero + scroll-over panel — unified interior-page hero

**Date:** 2026-06-03
**Status:** approved (design), pending implementation
**Author:** Frederik (design) + Claude

## Problem

The public site has two divergent hero systems:

- `.activity-hero` — poster layout (copy left, circular visual right): voor het eerst,
  meehelpen, steun-ons, about + leaves, activity detail.
- `.index-hero` — slim blue band for scanner/directory pages: kalender, lokale groepen.

We want a single, more aspirational standard hero, modelled on the MindMarket reference
(floating white nav pill over a coloured hero, big aspirational title, right-side
illustration, white content panel that scrolls *over* a pinned hero).

## Goals

1. One unified hero for all interior pages, replacing both `.activity-hero` and
   `.index-hero`.
2. **Eyebrow = page name**, rendered in the lead treatment (Nunito regular, yellow).
3. **Title = aspirational headline** aligned to the page's core goal (big, white).
4. Brand **blue** hero extends *behind* the menu.
5. Menu stays sticky but becomes a compact **floating white pill**, always visible
   (over blue in the hero, over white on content) — like the reference.
6. **Right-side illustration** from the existing `public/img/illustrations/` set. No
   bottom illustration anywhere.
7. Hero is **shorter** than today's poster hero.
8. The section beneath the hero (white, **rounded top corners**, soft shadow) scrolls
   **over** the hero. The **hero stays fixed**.

Non-goals: per-page hero accent colours (blue everywhere for now); commissioning new
illustrations; changing page copy beyond the new eyebrow/title; the home page hero
(`.home-hero`) is out of scope unless trivially compatible.

## Mechanic (pure CSS, no JS)

- Hero: `position: fixed; top: 0; left/right: 0`, full-width blue, **lowest z-layer**.
  Height is modest and responsive (shorter on mobile).
- A transparent **spacer** of equal height sits in normal flow, so the page opens with
  the hero fully visible.
- The first content section is a **white panel** with rounded top corners + soft shadow,
  at a **higher z-layer**; it and all following sections (incl. partners strip + footer)
  scroll up *over* the pinned hero and fully cover it at the bottom.
- The **floating nav pill** sits at the **highest z-layer**, above hero and content.
- No Alpine/JS needed for the effect. (Existing header JS for mobile toggle stays.)

Rejected alternative: `position: sticky` hero (drifts instead of truly pinning) and
JS-driven parallax (overkill).

## Shared component

Consolidate per-page hero markup into one reusable Blade component. Hero plumbing
(fixed positioning, spacer, z-layers) and the floating pill live in the layout +
`app.css`, not per page.

```blade
<x-page-hero
    eyebrow="Kalender"
    title="Spring op de fiets, wij rijden samen."
    illustration="img/illustrations/kid-on-bike.png">
    <x-slot:controls>
        {{-- optional: page controls that stay IN the hero (filter, stats) --}}
    </x-slot:controls>
</x-page-hero>
```

- `eyebrow` (string) — page name, lead treatment (Nunito regular, yellow).
- `title` (string) — aspirational headline (big, white; raw `<h1>`, never `flux:heading`).
- `illustration` (string|null) — asset path, right side, decorative (`alt=""`,
  `aria-hidden`). Null = no illustration.
- `controls` (slot, optional) — in-hero controls (kalender picker, groepen stats).

## Per-page mapping

Eyebrow = existing page name. Titles below are **drafts** (tone-of-voice: joyful, warm,
local, committed; no em-dashes) for Frederik to refine post-build.

| Page | Eyebrow | Draft aspirational title | Illustration | Controls kept in hero |
|---|---|---|---|---|
| Kalender | Kalender | Spring op de fiets, wij rijden samen. | kid-on-bike | location picker |
| Lokale groepen | Lokale groepen | Jouw buurt fietst al, rij mee. | person-with-boombox | stat counters |
| Voor het eerst | Voor het eerst | Kom zoals je bent, je eerste rit wordt een feest. | kid-on-scooter | — |
| Meehelpen | Meehelpen | Jouw handen maken de stoet. | kid-waving | — |
| Steun ons | Steun ons | Help de beweging groeien. | crocodile-on-tricycle | — |
| Over ons (hub) | Over ons | (draft per page) | tree-round | — |
| Missie | Missie | (draft per page) | tree-tall | — |
| Visie | Visie | (draft per page) | bird-with-helmet | — |
| Organisatie | Organisatie | (draft per page) | kid-waving | — |

Activity **detail** hero (`.activity-hero` poster with real photo + chapter pin) is a
distinct, content-rich layout; keep it for now and revisit separately if desired.

## Header changes

- Header becomes a **floating white pill**: compact height, rounded, contained width,
  margin from the viewport top, highest z-index, sits over the hero blue.
- Logo + nav + support CTA stay; just more compact (the reference is denser than today).
- Mobile menu behaviour unchanged (Alpine toggle).

## Responsive

- Hero shortens on mobile; right illustration scales down or hides on the narrowest
  widths. No bottom illustration.
- Floating pill stays usable on mobile (compact, with the existing hamburger).

## Testing

- Feature/structure tests: each interior page renders the new hero — assert eyebrow
  (page name) and the aspirational `<h1>` are present; assert the white panel exists.
- Kalender: location picker still renders inside the hero and filters (existing
  Livewire test stays green).
- Lokale groepen: stat counters still render inside the hero (existing GroupsTest stays
  green).
- Update `PublicStructureTest` / `GroupsTest` expectations where hero markup assertions
  changed.

## Risks / notes

- Shared working tree: Nico commits concurrently. Stage only the specific files; never
  `git add -A`; do not push `main`.
- `app.css` is large; add the new `.page-hero*` block and floating-pill styles, then
  retire `.index-hero*` and the generic `.activity-hero*` usages once all pages migrate.
  Keep activity-detail's poster styles if that page is excluded.
- Verify on the live Herd site (`https://kidicalmass.test`) that the scroll-over and
  pinned hero read correctly, plus reduced-motion.

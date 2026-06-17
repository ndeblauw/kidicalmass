# Home scroll-sequence: photo + half-size overlapping illustration

**Date:** 2026-06-17
**Status:** Approved design, ready for implementation plan
**Scope:** Home page (`/`) "drie routes" scrollytelling section only (`.home-routes`). Desktop (lg+) enhancement; mobile unchanged.

## Problem

The home "drie routes" section (`<x-scroll-sequence class="home-routes">`) shows three text blocks beside a sticky media column. Today the sticky column holds only the three illustrations (bikes / a helper), each filling the cell and "riding" in from the right as you scroll.

Two things to improve:

1. **No photo.** Each section should have a suitable photo beside it, with the illustration arriving *over* the photo (slightly overlapping), at about half its current size — so the illustration reads as a playful accent on a real photo, not the whole picture.

2. **Illustrations clip at the container edge, not the screen edge.** `overflow-x: clip` lives on `.home-routes`, which sits inside the centered max-width `.container`. So a bike riding off vanishes at the container boundary, leaving a white gap between there and the actual viewport edge. It should read as riding on and off the *real screen*.

## Existing pattern to reuse

`/help-out` (`.ho-deal`) already runs this exact `<x-scroll-sequence>` component with a **crossfading photo**: `<img class="ho-deal__photo" data-seq-media="0|1|2">` (first one `is-active`), a rounded + shadowed sticky frame, opacity crossfade plus a subtle ken-burns scale. The home version is that same proven photo treatment **plus** the existing bike riding in on top of it. We reuse the convention rather than invent one.

Key mechanics already in place (no JS change needed):
- The component's Alpine `setActive(i)` toggles `.is-active` / `.is-past` on **every** `[data-seq-media]` whose `data-seq-media` matches the active block index. Adding photo `<img>`s with `data-seq-media="0|1|2"` makes them crossfade for free.
- The base component CSS (`resources/css/components/scroll-sequence.css`) gives every `[data-seq-media]` child `position:absolute; inset:0; width/height:100%; opacity` crossfade at lg+. Photos get correct behaviour with zero new base rules.
- The sticky frame uses `contain: layout` (containing block for the absolute children) and, on home, `overflow: visible clip` (x visible so bikes leave the frame; y clip so block-3's helper rises from below).

## Design

All changes are scoped to home (`.home-routes`, `.page-panel--home`). The shared component (`scroll-sequence.blade.php`, `scroll-sequence.css`) and `/help-out` are **not** touched.

### Layers per sticky media cell (lg+)

Each cell stacks two layers, both keyed off the same `is-active` the bikes already use:

1. **Photo layer (back).** Fills the cell, `object-fit: cover`, rounded corners + soft shadow, crossfades section-to-section (opacity, optionally a subtle ken-burns scale mirroring `/help-out`).
2. **Illustration layer (front).** The existing bike / helper, scaled to **~50%** of the cell, anchored **bottom-left**, sitting on top of the photo with its left edge breaking slightly past the photo's edge. Keeps the full ride-in choreography (bike rolls in from the right and parks bottom-left; the previous bolts off left; block-3's helper rises from below). Just smaller and layered.

DOM order in the `media` slot: **photos first, then illustrations** → illustrations render on top naturally, no z-index juggling.

**Initial state:** the first *photo* (`data-seq-media="0"`) gets `is-active` so the backdrop is painted on first view (mirrors `/help-out`). The first *illustration* keeps **no** `is-active` — preserving today's behaviour where the bike rolls in fresh rather than starting parked.

### Photo selection (reuse existing optimized assets)

| Block | Section | Illustration (rides in) | Photo |
|---|---|---|---|
| 0 | Nieuw hier? | `waving-rider.svg` | `img/photography/kids-thumbsup-at-ride.jpg` |
| 1 | Vind je lokale groep | `longtail-with-kid.svg` | `img/photography/ride-cinquantenaire-crowd.jpg` |
| 2 | Help mee | `volunteer-with-wrench.svg` | `img/photography/volunteers/volunteers-pink-vests-with-flag.jpg` |

Photos carry NL `alt` text (warm, concrete, per tone-of-voice) and `loading="lazy"`. The whole media column is `aria-hidden="true"` on the component, so the photos are decorative reinforcement of copy that already carries the meaning — no accessibility regression.

### Ride on/off the real screen edge

Move the horizontal clip boundary from the centered container out to the full-bleed wrapper:

- **Remove** `overflow-x: clip` from `.home-routes`.
- **Add** `overflow-x: clip` to `.page-panel--home`. It is already `width: 100vw`, so the bike now clips at the **true viewport edge**.
  - Use `clip`, **never `hidden`**: `overflow: hidden` would create a scroll container and break the `position: sticky` pinning; `overflow: clip` does not establish a scroll container, so sticky stays pinned to the viewport.
  - This is scoped to `.page-panel--home`; the shared base `.page-panel` (other pages) is untouched.
- **Keep** the sticky frame's `overflow: visible clip` (vertical clip still lets block-3's helper rise from below the frame).
- **Retune** the ride transforms to be viewport-relative (large / `vw`-based translate) so the bike genuinely enters from the right screen edge and exits past the left screen edge, instead of popping in/out at the container boundary. Exact distances tuned during build with one screenshot pass.

### Mobile (below lg) — unchanged

The whole media column is `display:none` below lg, so photos live only in the desktop sticky column and require **no new mobile markup**. Each section keeps its existing inline illustration (`.home-routes__block-illu`) above the copy.

### Reduced motion

Preserve the existing `prefers-reduced-motion` fallback: copy appears without slide; illustrations fall back to a plain opacity treatment (no riding/transforms). Photos may keep their gentle opacity crossfade (no scale) — consistent with `/help-out`'s reduced-motion handling.

## Files touched

1. **`resources/views/home.blade.php`** — add three photo `<img>` (with NL alt + `loading="lazy"`) into the `media` slot, before the three illustration imgs. First photo gets `is-active`.
2. **`resources/css/pages/home.css`** —
   - add `.home-routes__photo` (cover, rounded corners, shadow, crossfade; optional ken-burns scale);
   - override `.home-routes__illu` at lg+ to ~50% size, anchored bottom-left, with viewport-relative ride transforms;
   - remove `overflow-x: clip` from `.home-routes`; add `overflow-x: clip` to `.page-panel--home`;
   - extend the reduced-motion block for the photo layer.
   Raw rem/color-mix values follow the existing convention in `help-out.css` (page partials may carry values; the no-raw-value rule applies to `.blade.php` components, not partials).
3. **Test** — a Pest feature assertion that `/` renders the three photo asset paths (and that the illustrations are still present).

## Testing

- New/updated Pest feature test: GET `/` returns 200 and the response contains the three photo paths + the three illustration paths.
- `php artisan test --filter=CssArchitectureTest` (partials registered; no raw values leaked into `.blade.php`).
- Run the home render test + CssArchitectureTest after implementation.
- One screenshot pass (lg viewport) to tune illustration size, overlap, and ride distances; verify no white gap on ride-out and no horizontal scrollbar.

## Out of scope / non-goals

- No changes to the shared `scroll-sequence` component or `/help-out`/`/volunteer`.
- No new image assets sourced; reuse existing optimized photography.
- No mobile photo treatment.
- No JS / Alpine changes.

## Risks & mitigations

- **Sticky breaks if wrong overflow used** → mandated `overflow: clip` (not `hidden`) on `.page-panel--home`; verify pinning after the change.
- **Bike doesn't fully clear the viewport at ~50% size** → use viewport-relative (`vw`) travel distances, tuned with a screenshot.
- **Photo file weight** → reuse assets already shipped and used elsewhere; `loading="lazy"`.

---
title: Photo-collage media unit (organic snapshot collage)
tags: [design, patterns, frontend, scroll-sequence, motion]
sources: [resources/views/volunteer.blade.php, resources/css/pages/help-out.css]
phase: build
updated: 2026-06-18
---

# Photo-collage media unit

A small **organic collage of 2–3 photos** used as a media block: photos scattered
on a square stage at slight angles, with an optional fixed brand doodle anchored in
a corner and a staggered "settle" entrance. Replaces a single framed photo when a
section wants more life and warmth.

First built for the `.ho-deal` "Wat je krijgt / Wat we vragen" beats on **Meehelpen**
(`/help-out`), where it sits inside the shared **scroll-sequence** so the two beats'
collages crossfade as you read. It also works standalone (no scroll-sequence) as a
static collage beside a text lockup.

Catalogue ID: **PAT-20** ([`40-patterns.md`](../../wiki/design/40-patterns.md)).

---

## Anatomy

- **Stage** (`.ho-deal__collage`) — a `position: relative; aspect-ratio: 1/1` box. Each
  beat is one stage. Inside scroll-sequence it's a `[data-seq-media]` item, so the
  component fades it in/out; standalone it just renders.
- **Photos** (`.ho-deal__photo--lead` / `--trail`, each wrapping one `<img>`) —
  absolutely placed by **CSS custom properties**, so one rule positions any photo:
  - `--ho-photo-x` / `--ho-photo-y` — centre point (%) on the stage
  - `--ho-photo-w` — width (% of stage)
  - `--ho-photo-r` — resting rotation
- **Arrangement modifier** (`.ho-deal__collage--a` / `--b`) — sets those vars per beat
  so two collages don't read as the same frame twice. A is top-left/bottom-right,
  B mirrors it (top-right/lower-left, opposite tilts).
- **Doodle** (`.ho-deal__doodle`) — a brand illustration (e.g. `waving-rider.svg`)
  pinned in a corner, `z-index` above the photos. **Static by design** (Frederik:
  don't animate it). One per stage; placing the same doodle at the same coords in
  both beats makes it read as constant through the crossfade.

```blade
<div class="ho-deal__collage ho-deal__collage--a is-active" data-seq-media="0">
    <figure class="ho-deal__photo ho-deal__photo--lead">
        <img src="{{ asset('img/photography/…') }}" alt="…" loading="lazy">
    </figure>
    <figure class="ho-deal__photo ho-deal__photo--trail">
        <img src="{{ asset('img/photography/…') }}" alt="…" loading="lazy">
    </figure>
    <img class="ho-deal__doodle" src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" aria-hidden="true">
</div>
```

## Key CSS decisions (why it works)

- **Centre with `translate`, animate with `transform`.** Each photo uses
  `translate: -50% -50%` for centring, which leaves the `transform` property free to
  animate `rotate()`/`scale()` on crossfade without fighting the centring offset.
- **Percentage placement = resolution-independent.** Because x/y/w are %, the same
  scatter holds at every width; mobile just shows the collages stacked, at rest.
- **The stage must not clip.** The rotated photos and the doodle spill past the stage
  edges, so the scroll-sequence sticky frame is overridden to `overflow: visible`
  (and drops its own radius/shadow — each photo carries its own).
- **Settle choreography** (lg only): the collage that *isn't* reading sits in a
  slightly "tossed" state (`rotate(var(--ho-photo-r) * ~1.8) scale(~1.08)`); the active
  beat settles to rest on `cubic-bezier(0.22, 1, 0.36, 1)`, with the **trail photo
  delayed ~0.1s** after the lead so the pair don't move in lockstep. Opacity is the
  scroll-sequence component's job; this layer only animates `transform`.
- **Reduced motion:** a `prefers-reduced-motion` block forces every photo to its rest
  transform with no transition/delay. The doodle is already static.

Source of truth for the actual values: `resources/css/pages/help-out.css` (`.ho-deal*`).

## How to reuse it

**Add a photo / arrangement:** copy a `--lead`/`--trail` figure and define a new
`.ho-deal__collage--x` modifier with fresh `--ho-photo-*` values. A third photo just
needs a `--mid` variant. The values for the current scatter (spread ≈ 1.19×, ±6°) were
tuned in a throwaway HTML playground; eyeball new ones or rebuild a quick playground.

**Pick fresh photos.** Before reusing, grep usage so you don't re-show an over-exposed
shot: `grep -rhoE "img/photography/[A-Za-z0-9/_-]+\.(jpg|webp|avif)" resources/views | sort | uniq -c | sort -rn`.
The catalogue is [`60-asset-map.md`](../../wiki/design/60-asset-map.md) (★★★ = hero-ready;
many strong shots are still unused).

**On the SECOND page that wants this, extract it.** Right now the pattern is baked
into `help-out.css` / `volunteer.blade.php` (page-scoped, fine for one use). Per the
[CSS-partials architecture](2026-06-06-css-partials-architecture-design.md) and the
component rule in `CLAUDE.md`, the next reuse should promote it to:
- `resources/views/components/photo-collage.blade.php` (props: photos[], arrangement,
  doodle, `data-seq-media`) — owns markup + appearance.
- `resources/css/components/photo-collage.css` (registered in the partials manifest) —
  rename `.ho-deal__*` → `.photo-collage__*`, keep the custom-property scatter system.
Then `.ho-deal` just composes the component, and other pages drop it in.

## Gotchas

- Inside scroll-sequence, keep the doodle **inside** each collage (not a constant
  sibling) so it works on mobile, where the sticky frame becomes a stacking grid.
- Landscape photos cropped to the `4/5` photo aspect lose their sides — check the
  `object-position` focal point per image.
- Don't give the doodle the settle motion (explicit Frederik preference).

# Chapter team carousel — design

**Date:** 2026-06-09
**Page:** P-11 chapter page (`resources/views/groups/show.blade.php`)
**Status:** approved look, ready for implementation plan

## Problem

The closing yellow band of the chapter page introduces *who runs the group* with a row
of small circular initials-avatars (`chapter-team__faces`). It is flat and forgettable,
and it reads as a fallback rather than a feature. Frederik wants the people shown bigger
and more characterful — a full-width, swipeable carousel of person cards in the
KidicalMass illustration style, inspired by horizontal "member" carousels (cult-member
reviews + a "what sets us apart" slider).

We do not have per-person portrait photos yet, and that is fine: the card is built so a
real photo drops into the same slot later with no layout change.

## The look (settled via visual companion)

A **flat illustrated polaroid**, drawn in the same language as the illustration set
(`public/img/illustrations/*.svg`): solid navy outline, a *flat hard* offset shadow
(no blurred photo shadow), alternating slight tilt. NOT skeuomorphic.

Per card:
- **Photo slot** — square, soft brand tint behind, holding a brand illustration today
  (see Placeholder). A real portrait photo replaces the illustration later.
- **Name** — first name, bold.
- **Role** — plain muted uppercase text (`trekker` / `roze hesje` / `communicatie`).
  Not a button/chip.

Card spec (final values from the approved mockup; tune in CSS, not markup):
- width ~13.5rem (≈11.5rem under 640px)
- `border: 2px solid` navy (`--color-kidical-ink` / illustration ink `#1d1d3d`)
- flat shadow `4px 5px 0 color-mix(in oklab, ink, transparent 12%)`
- tilt `nth-child(odd) -3deg`, `nth-child(even) 2.5deg`
- photo tint cycles through soft brand tints (cream, light-blue, soft pink, soft green, soft orange)

## Section layout (the yellow closing band)

Replaces the current two-column `chapter-team-band__top` (crew photo left + headline/faces
right). New top-to-bottom order inside the band:

1. **Head row** — `<h2>Wij zwaaien je welkom aan de start</h2>` left, prev/next buttons right.
2. **Carousel** — full-width track of polaroid cards.
3. **Crew photo** — the existing `volunteers-pink-vests-with-flag.jpg`, at full **container**
   width (not full-bleed), below the carousel.
4. **"Help mee" CTA** — the existing on-demand reveal + `livewire:chapter-volunteer-signup`,
   unchanged.

The earlier full-bleed `chapter-photo` (top of page) is untouched.

### Nav buttons (keep the drawn look)

Round blue buttons with a navy outline and a flat offset shadow that presses in on
`:active` (`3px 3px 0` → `1px 1px 0`, translate 2px). `aria-label` "Vorige" / "Volgende".
Visible at all sizes; on touch the native swipe is the primary path.

## Carousel behaviour

- Native horizontal scroll with `scroll-snap-type: x mandatory`, cards
  `scroll-snap-align: start`; scrollbar hidden. Free swipe on mobile/trackpad.
- Buttons page **one card at a time**: scroll by `cardWidth + gap`.
- Small Alpine `x-data` on the carousel handles the button → `scrollBy`. Under
  `prefers-reduced-motion`, scroll behaviour is `auto` (no smooth animation); the tilt is
  a static transform and stays.
- Optional polish: disable a button when the track is scrolled to that end (scroll
  listener toggling a `disabled` attribute). Nice-to-have, not required for v1.

## Placeholder → real photo

- **Curated illustration subset** (person/family riders, exclude sign-only and
  caterpillar): `waving-rider`, `relaxed-rider`, `cyclist-peace-sign`, `rider-with-flag`,
  `volunteer-with-wrench`, `longtail-with-kid`, `cargo-bike-family`.
- **Deterministic assignment** so a person keeps the same illustration across reloads and
  pages: hash the member name (e.g. `crc32(name) % count`) into the subset. Implemented as
  a small helper in the blade `@php` block (alongside `$initialsOf`), or a tiny support
  method — keep it where the existing faux-team logic lives.
- The photo slot renders an `<img>`. Today `src` = the chosen illustration, `alt=""`,
  `aria-hidden="true"` (decorative). The eventual per-person portrait (no model field yet —
  GitHub #37 / D-1, faux for now) slots into the same `<img>`, at which point `alt` becomes
  the person's name. No layout change.

## Data

Unchanged: the existing `$team` collection in `groups/show.blade.php`
(real `$group->users` as role `trekker`, concatenated with `$fauxVolunteers`). The
carousel iterates `$team`; `initials` is no longer displayed (can stay in the array,
harmless). The empty-team branch is **untouched** — no carousel, the invitation + signup
form leads.

## Accessibility

- Track wrapper: `role="region"` (or a labelled list) with `aria-label` like
  "Team van {gemeente}".
- Illustrations decorative (`alt=""` / `aria-hidden`); name + role carry the information.
- Buttons have text alternatives; focus-visible styles inherited from base.

## CSS placement

Page-only styling → **`resources/css/pages/chapters.css`** (where the current
`chapter-team*` rules already live). New BEM classes under the existing namespace:
`chapter-team__carousel`, `__track`, `__nav`, `__card`, `__photo`, `__name`, `__role`.
Remove the now-unused `chapter-team__faces` / `__face` / `__avatar` rules. All colours,
radii and shadows reference theme tokens (no raw hex/px in component markup;
`CssArchitectureTest` covers `.blade.php` components, not page CSS, but keep tokens anyway).
Headings stay raw `<h2>` (no `flux:heading`).

## Testing

Extend the existing chapter page Pest test:
- A group **with** a team renders the carousel: the band contains `chapter-team__card`
  entries for each `$team` member (count = real users + faux), each showing the member
  name and role, and an illustration `<img>`.
- The crew photo and the "help mee" CTA still render below the carousel.
- The **empty-team** group still shows the signup form and no carousel.
Run: `php artisan test --compact --filter=Group` (or the chapter test file).

## Out of scope

- Real per-person portrait photos / a portrait field or media collection (faux until the
  backend lands).
- Reusing the carousel on other team listings (about/organisation) — chapter page only for now.
- Drag-to-scroll niceties beyond native overflow; "disabled at ends" is optional polish.

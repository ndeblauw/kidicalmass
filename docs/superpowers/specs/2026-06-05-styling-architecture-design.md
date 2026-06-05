---
title: Styling architecture — three-layer model (tokens / components / composition)
tags: [design, frontend, css, conventions]
sources: [resources/css/app.css, CLAUDE.md, resources/views/getting-started.blade.php]
phase: design
updated: 2026-06-05
---

# Styling architecture: tokens / components / composition

## Problem

`app.css` has grown to ~4,480 lines of BEM blocks inside `@layer components`. Two pains
follow from BEM-everywhere:

1. **Boundary collapsed.** BEM classes bundle *appearance* and *positioning/spacing*
   together (e.g. `.gs-expect-left`, `.gs-expect-card`, `.gs-expect-cards` each set their
   own layout). The "templates hold structure only" ideal broke: nudging a page's layout
   means editing the shared CSS file, and Tailwind utilities can't cleanly override
   layout that's baked into a BEM block.
2. **Collision.** Multiple agent threads run in parallel against the same checkout. Every
   page tweak lands in the one giant `app.css`, so the threads collide there.

The fix is a more nuanced split: keep reuse (colour, type, reusable units) systematic,
but make page-level placement freely editable per-page without touching shared CSS.

## The three-layer model

Every styling decision sorts into exactly one layer. The test an author applies:
**"Am I styling a *thing*, or *placing* things?"**

| Layer | Lives in | Owns | Example |
|---|---|---|---|
| **Tokens** | `@theme` + `@layer base` in `app.css` | Colour, type scale, radius, shadow; link + heading defaults | `--color-kidical-blue`, `h2` size, `--shadow-card` |
| **Components** | `resources/views/components/*.blade.php` (x-components) | A reusable unit's *appearance + internal spacing*, written as Tailwind utilities baked into the component markup; parametrised by props/slots | `<x-feature-card>` → `bg-white rounded-card shadow-card p-7` |
| **Composition** | page Blade templates | How units are *arranged*: section gaps, page margins, grid/flex, alignment, widths, order | `<div class="grid lg:grid-cols-2 gap-12 mt-24">` |

Decision rules:

- Styling a **thing** (a card, a chip, a hero) → it's a component; its appearance lives as
  utilities *inside* that component's `.blade.php`. The component is the single source of
  truth for how that unit looks.
- **Placing** things on a page → Tailwind composition utilities in the page template.
- Colour / type / radius / shadow → **always a token**, never a raw hex or px value, in
  either layer.

### Why x-components hold their own appearance

This is the key shift from the previous convention. Appearance for a reusable unit lives in
its `.blade.php` as token-backed utilities — there is **no `app.css` entry** for it. Benefits:

- **Collision-proof.** Two agents editing two different components are in two different
  files. `app.css` stops growing per-page and per-component.
- **Self-contained.** A component looks correct in isolation; you read one file to know its
  look and its internal spacing.
- **Single source of truth preserved.** Editing the component changes every usage — same
  guarantee BEM gave, in a per-component file instead of the shared sheet.

## Worked example: the feature card (`<x-feature-card>`)

The pilot's central unit. Investigation found the icon-chip card appears under at least two
different class names with the same anatomy, confirming it is one component, not several:

- `gs-expect-card` (getting-started) — the scroll-stacking "expectations" deck.
- `activity-promises__item` (mission's "drie dingen die we doen", and reused on steun,
  chapters, etc.) — a static 3-up grid of tilted white cards.

**Identical anatomy:** white background, rounded corners, drop shadow, flex column with a
gap; a rotated square-ish icon chip (`border-radius: 28%`, `rotate(-3deg)`, coloured
background, white Flux icon); then `<strong>` (Caprasimo heading face) + `<p>` body.

**The differences are all parametric, contextual, or placement — not intrinsic to the card:**

- *Content* → props: the Flux icon, the chip colour (getting-started rotates
  red→blue→orange→…; mission is all red), the title, the body.
- *Placement / behaviour* → owned by the page: the per-card tilt (`:nth-child` rotation),
  grid-vs-deck, and the scroll-stacking JS. The card never knows which context it's in.
- *Scale* → **unified.** getting-started used a larger treatment (radius 2rem, padding
  2.5rem, bigger chip + type); mission used a smaller one (radius 1.5rem, padding 1.75rem).
  Decision: **one canonical scale, no `size` prop. The getting-started (larger) scale is the
  canonical truth.** mission and the other reused cards grow *up* to it. Exact token values
  are lifted from the getting-started card and confirmed visually during the pilot.

Resulting shape:

```blade
<x-feature-card icon="clock" color="red" title="Kort en rustig">
    5 à 7 km op het tempo van het jongste kind, zelden meer dan een uur.
</x-feature-card>
```

- `icon` — Flux icon name (rendered `variant="solid"`, `aria-hidden`).
- `color` — maps to a brand token for the chip background (`red|blue|orange|green|…` →
  `--color-kidical-*`). The page passes the rotation; the card does not own it.
- `title` — rendered as the card's `<strong>`.
- default slot — the body copy. **May contain an inline link** (e.g. `… echt verwelkomen.
  <a href="…">Lees onze visie →</a>`). The link sits inside the body paragraph; no separate
  prop needed. The card gives body links its own treatment — **blue, bold (700), no
  yellow underline-fill, plain underline on hover** — overriding the base `a` rule. Today
  that treatment is a composition-scoped override (`.about-card-grid .activity-promises__item
  a`, app.css:2368); it moves *into* the component so it travels with the card on any page.
  Mechanism: descendant-targeting utilities on the card wrapper
  (`[&_a]:text-kidical-blue [&_a]:font-bold [&_a]:bg-none hover:[&_a]:underline`), keeping the
  appearance inside the component markup rather than in `app.css`.
- All appearance lives as token-backed utilities inside `feature-card.blade.php`; no
  `app.css` entry. Tilt, grid/deck and scroll JS stay in the page template.

### Card taxonomy (so the pilot stays scoped)

Not every "card" is a feature card. Keep these distinct:

- **Feature card** (icon chip + title + body, static, non-clickable) → `<x-feature-card>`.
  *This is the pilot.* Replaces `gs-expect-card` and `activity-promises__item`.
- **Nav card** (clickable, hover lift, arrow, links somewhere) → `about-nav-card`,
  `about-intent-card`. A separate component family; convert later, not in the pilot.
- **Event card / article card** → already x-components; leave as-is.
- One-offs (`about-contact-card`, `about-partner-card`, `about-stat`) → convert
  opportunistically when their page is touched.

## Token enrichment (one-time, small)

"Utilities in components" only stays consistent if the values are named tokens. Colours and
type already exist as tokens (`--color-kidical-*`, heading sizes in `@layer base`). The gap
is radius and shadow, currently ad-hoc (`rounded-2xl`, hand-written `box-shadow` per block).
Add semantic tokens to `@theme`:

- `--radius-card`, `--radius-chip`  → usable as `rounded-card`, `rounded-chip`
- `--shadow-card`  → usable as `shadow-card`

Because the feature card unifies to a single scale, one canonical value each is enough (no
`-lg` shadow variant). Set the values from the **getting-started** card styling (the canonical
scale) during the pilot; they become the one source of truth for every feature card.

## CLAUDE.md rule change (the carve-out)

Today's "Public Site — Frontend Rules" say *"Templates hold structure only … Strip `bg-*`,
`text-{color}`, `font-*`, `shadow-*`, `rounded-*` …"* and *"appearance belongs in app.css."*
That rule is scoped, not deleted:

- **Page templates:** rule **stands**. Composition utilities only
  (`grid flex gap-* p-* m-* max-w-* overflow-* aspect-* object-*`). No appearance utilities,
  no BEM layout scaffolding.
- **x-components:** appearance utilities are **expected** — this is the unit's single source
  of truth. Must use tokens (`bg-kidical-*`, `text-kidical-*`, `rounded-card`, `shadow-card`),
  never raw hex/px. The headings rule is unchanged: raw `<h1>`–`<h6>`, look from `@layer base`,
  never `flux:heading`, never inline size/weight/line-height on headings.
- **`app.css`:** stops growing per-page. New entries only for genuinely global / cross-cutting
  styles (footer, nav, prose, link treatment) or complex effects no single component owns
  (e.g. the scroll-stacking deck's keyframe-ish JS-driven CSS).

## Scope & migration

No big-bang refactor of the existing 4,480 lines. Order:

1. **Update the CLAUDE.md rule now** — so all parallel agent threads start applying the new
   boundary immediately. This stops the bleeding first.
2. **Add the radius/shadow tokens now.**
3. **Pilot on `getting-started.blade.php`** (the page in active work):
   - Build `<x-feature-card>` (token-backed utilities, one canonical scale).
   - Replace the six `gs-expect-card` blocks with `<x-feature-card>`, passing the icon,
     the rotating `color`, and the title; body in the slot.
   - Dissolve the `.gs-expect-*` layout scaffolding (`__left`, `__right`, `__cards`, section
     margins) into Tailwind composition utilities in the template.
   - Keep page-local: the per-card tilt, the deck layout, and the scroll-stacking JS
     (`@push('scripts')` + whatever minimal CSS the deck genuinely needs).
   - Then point the mission page's `activity-promises__item` cards at the same
     `<x-feature-card>` to prove reuse across pages. (Mission's tilt/grid stay page-side.)
   - Confirm visually that the unified scale reads well in both the deck and the 3-up grid;
     tune the canonical token values if needed.
   This page becomes the worked example the rule points to.
4. **Migrate the rest opportunistically** — when an agent next touches a page, it converts
   that page to the model. No dedicated refactor sprint; the old BEM blocks remain valid
   until their page is touched.

### Non-goals

- Not splitting `app.css` into `@import` partials (considered, set aside — x-components carry
  the load instead).
- Not removing existing BEM blocks wholesale; they're retired page-by-page.
- No `size` prop on `<x-feature-card>` — scale is unified to one canonical value.
- Not converting the nav-card / event-card / one-off card families in the pilot.

One intentional visual change is accepted: mission and the other reused feature cards grow *up*
to the canonical (getting-started, larger) scale. getting-started itself is visually unchanged.
Otherwise the rendered output should be unchanged — this is a structural move.

## Risks / watch-items

- **Specificity.** Tailwind v4 emits utilities in the `utilities` layer, which wins over
  `@layer components`. So page composition utilities reliably override anything left in
  `app.css`. Confirm during the pilot that no leftover BEM layout fights the template.
- **Token drift.** The whole point of the radius/shadow tokens is to stop `rounded-2xl` /
  bespoke shadows reappearing. Code review should flag raw appearance values in components.
- **"It depends" creep.** The rule must stay binary (thing vs placement) so parallel threads
  apply it the same way. Avoid per-case exceptions.

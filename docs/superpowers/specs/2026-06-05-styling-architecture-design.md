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
| **Components** | `resources/views/components/*.blade.php` (x-components) | A reusable unit's *appearance + internal spacing*, written as Tailwind utilities baked into the component markup | `<x-card>` → `bg-white rounded-card shadow-card p-6` |
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

## Token enrichment (one-time, small)

"Utilities in components" only stays consistent if the values are named tokens. Colours and
type already exist as tokens (`--color-kidical-*`, heading sizes in `@layer base`). The gap
is radius and shadow, currently ad-hoc (`rounded-2xl`, hand-written `box-shadow` per block).
Add semantic tokens to `@theme`:

- `--radius-card`, `--radius-chip`  → usable as `rounded-card`, `rounded-chip`
- `--shadow-card`, `--shadow-card-lg`  → usable as `shadow-card`, `shadow-card-lg`

(Exact values to be set from the existing card/chip styling during the pilot so nothing
visually shifts.)

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
3. **Pilot on `getting-started.blade.php`** (the page in active work): extract `<x-card>`
   and `<x-icon-chip>` (or similarly named) as the reusable units, and dissolve the
   `.gs-expect-*` layout scaffolding into Tailwind composition utilities in the template.
   This page becomes the worked example the rule points to. The scroll-stacking JS and any
   CSS it genuinely needs stay page-local (`@push('scripts')` / a small `app.css` block).
4. **Migrate the rest opportunistically** — when an agent next touches a page, it converts
   that page to the model. No dedicated refactor sprint; the old BEM blocks remain valid
   until their page is touched.

### Non-goals

- Not splitting `app.css` into `@import` partials (considered, set aside — x-components carry
  the load instead).
- Not removing existing BEM blocks wholesale; they're retired page-by-page.
- Not changing the token *values* or any page's visual result during the pilot — this is a
  structural move, the rendered output should be unchanged.

## Risks / watch-items

- **Specificity.** Tailwind v4 emits utilities in the `utilities` layer, which wins over
  `@layer components`. So page composition utilities reliably override anything left in
  `app.css`. Confirm during the pilot that no leftover BEM layout fights the template.
- **Token drift.** The whole point of the radius/shadow tokens is to stop `rounded-2xl` /
  bespoke shadows reappearing. Code review should flag raw appearance values in components.
- **"It depends" creep.** The rule must stay binary (thing vs placement) so parallel threads
  apply it the same way. Avoid per-case exceptions.

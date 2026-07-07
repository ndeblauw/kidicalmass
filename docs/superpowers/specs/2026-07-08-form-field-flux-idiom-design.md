# One form-field idiom, delivered by restyled Flux — design

**Date:** 2026-07-08 · **Brief:** `docs/wiki/design/30-skeleton/form-field-flux-idiom.md`

## Goal

One field look, one delivery mechanism: theme Flux's form components to render
the site idiom (white field, 2px ink-tint border, 0.75rem radius, red focus
border, uppercase letterspaced label, red error line), then put every public
form on Flux. Restores Flux's niceties: viewable password, error wiring,
`aria-invalid`/`role="alert"` for free.

## Mechanism: global CSS overrides on `data-flux-*` hooks

Chosen over the two alternatives Flux documents:

- **Theme variables** only remap colours (base zinc scale + accent); they cannot
  express border-width, radius, label case, or field height. Insufficient.
- **Publishing components** (`flux:publish`) forks 6+ Blade files out of Flux
  updates and scatters the idiom back into per-component utility classes — the
  exact disease the idiom cures.
- **Global CSS on `data-flux-*` attributes** (chosen) is Flux's documented
  customization seam, keeps Flux updatable, and puts the whole idiom in ONE
  registered partial: `resources/css/components/form-field.css`.

Two load-bearing implementation facts:

1. **The overrides are unlayered on purpose.** Flux's field styling is emitted
   as Tailwind utilities — the *last* cascade layer — so nothing inside
   `@layer components` can ever beat it (layer order trumps specificity). Only
   unlayered rules win. Same precedent as the flux overrides in `chrome.css`
   and the unlayered rules documented in `location-picker.css`.
2. **Checked checkbox/radio colour rides custom properties, not selectors.**
   The indicators paint their checked state with `var(--color-accent)`;
   re-pointing `--color-accent`/`--color-accent-foreground` on the indicator
   element itself wins regardless of cascade layers. Checked = kidical red,
   white check/dot (Frederik's pick over ink or the site-wide yellow accent).

Scope decision (Frederik): the overrides land **everywhere**, backstage/settings
included — one definition, no public-only scoping. If a settings page looks
cramped with the chunkier fields, that's a follow-up.

## What moved where

- `chrome.css` starter-kit flux rules (grid field, label margin reset, the
  harsh yellow `ring-accent` focus ring that got auth kicked off Flux in
  commit `c9bc958`) — **superseded by** `form-field.css`. Focus is now the
  idiom's red border (`outline: none` on text controls only; the global
  `:focus-visible` outline still guards checkboxes/radios/everything else).
- `.volunteer-signup__field/__label/__input/__error` — **retired from**
  `pages/steun.css`; `__form`, `__success*`, `__next-ride` layout shells stay.
- `<legend>` in custom fieldsets (contact topic pills, chapter roles) can't be
  a Flux label; they use the `.form-legend` alias defined next to
  `[data-flux-label]` in the same partial.
- All 8 plain-idiom forms migrated (back) to `flux:input/textarea/select` +
  `flux:field/label/error`; `wire:model`, honeypots, ids, `data-test` hooks and
  `__('key')` strings preserved. Bespoke designed controls stayed bespoke:
  contact topic pills, chapter role cards, start-group path cards.

## A11y: Flux does NOT wire `aria-describedby`

Verified against the runtime (`flux.min.js`) and a live browser: Flux
associates label↔input at runtime (`aria-labelledby`) and renders
`aria-invalid` + `role="alert"` server-side, but never associates input→error.
The old manual markup did. So every migrated control keeps an explicit
`aria-describedby="<field>-error"` pointing at an `id` on its `flux:error`
(via the `error:id` forwarded attribute on shorthand `:label` inputs). The
error div always renders (hidden/empty when no error), so the unconditional
reference is safe. `FormAccessibilityTest` enforces this and stays unchanged.

## Verification

- `/styleguide` "form-field (Flux)" entry is the reference rendering
  (input, viewable password, invalid state, textarea, select, checkbox, radio).
- Full suite green at the known-failure baseline (FilamentAdminTest only).
- Gotcha for posterity: a CSS comment containing `ps-*/pe-*` self-terminated at
  the `*/` and silently swallowed the next rule in the build. Don't put `*/`
  inside CSS comments.

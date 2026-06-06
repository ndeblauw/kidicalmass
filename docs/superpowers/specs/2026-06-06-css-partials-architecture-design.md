---
title: CSS partials — role-based split of app.css + enforcement
tags: [design, frontend, css, conventions, build]
sources: [resources/css/app.css, vite.config.js, CLAUDE.md, docs/superpowers/specs/2026-06-05-styling-architecture-design.md]
phase: design
updated: 2026-06-06
---

# CSS partials: role-based split of app.css + enforcement

## Problem

The 2026-06-05 three-layer model (tokens / components / composition) was meant to stop
`app.css` growing per-page and to end the collisions caused by every page tweak landing in
one shared file. One day later the boundary is not holding:

- **`app.css` grew, it did not shrink.** 4,367 lines at the pilot merge (`73e0dd4`) →
  **4,728** now (+361). The plan's headline goal ("`app.css` stops growing per-page") is
  inverted.
- **New features were built as fresh BEM straight into `app.css`.** Post-spec commits:
  `location-picker` redesign +277 lines (`c3b8a1b`), `kalender` filter row +211 (`e58ba26`),
  `steun-ons` +89 (`f419899`). None were converted to components.
- **Components that exist still leak appearance into `app.css`** — a split brain:
  `cta-button` (19 lines), `event-card` (14), `partners` (27), `gs-expect` (12 leftover from
  the feature-card pilot). Two agents editing one "migrated" unit still collide in `app.css`.
- **Everything still funnels into one physical file.** ~30 page-specific BEM families
  (`about*`, `activity*`, `chapter*`, `kal*`, `steun*`, `gs-*`, `ho-deal`, …) live in
  `app.css`, so any two agents (and Nico, who commits concurrently into the same checkout) on
  any two different pages still edit the same file.

### Root cause

The migration rested entirely on a **soft prose rule in CLAUDE.md that nothing enforced.**
Agents read it, then took the faster route (extend an existing BEM block). A half-done
conversion (new component + leftover `app.css`) sailed through. There was no check that fails
when new CSS lands in the shared file or when a component carries raw values.

The 2026-06-05 spec explicitly set aside splitting `app.css` into partials (its §Non-goals),
betting x-components would carry the load. That bet did not pay off. This spec adds the split
back **and** adds real enforcement, while keeping the three-layer model intact.

## Goals

1. **End single-file collisions** — work on different parts of the site lands in different
   files, so concurrent threads (and Nico) stop colliding in `app.css`.
2. **Make reuse the default, not a problem** — a unit used on many pages has exactly one home.
3. **Make the convention enforced, not advisory** — a test fails when the structure breaks or
   when components carry raw appearance values.

This is a **content-preserving relocation**, not a redesign. Rendered output must be unchanged.

## Approach: organize by role, with a per-page escape hatch

`app.css` today contains two genuinely different kinds of CSS, which is why a pure per-page
split fails (reused units have no home) and a pure per-component split over-reaches (page-only
sections would have to become components now). We split along the axis that matches reality:

- **Reusable units** (used on more than one page) → `resources/css/components/<role>.css`.
- **Page-only sections** (appear on exactly one page) → `resources/css/pages/<page>.css`.
- **Global shell** (footer, nav, page frame) → `resources/css/chrome.css`.
- **Cross-cutting effects** (`@keyframes`, reduced-motion, scroll-deck) → `resources/css/effects.css`.
- **Tokens + base typography** stay in `app.css` (rarely a collision point; keeps the entry
  file self-documenting).

### Classification rule

> **Reusable across pages → `components/`. Appears on one page only → `pages/`.**

This is the binary an author applies when deciding where a block goes, mirroring the
"thing vs placement" test from the three-layer model. When genuinely unsure whether a unit is
reused, default to `components/` (a one-page unit in `components/` is harmless; a reused unit in
`pages/` invites duplication).

### Target structure

```
resources/css/
  app.css              ← entry ONLY: @import 'tailwindcss'; flux.css; @source;
                         @theme tokens; @layer theme; @layer base; then the @import block
  components/          ← reusable units, one file per role
    location-picker.css, partners.css, event-card.css, cta-button.css,
    feature-card.css, support-callout.css, page-hero.css, kal-bands.css, …
  pages/               ← genuinely page-only sections
    home.css, calendar.css, chapters.css, activity.css, about.css,
    steun.css, getting-started.css
  chrome.css           ← global shell: site-footer, nav, page / page-panel frame, link-plain
  effects.css          ← @keyframes, prefers-reduced-motion block, scroll-stacking deck CSS
```

File names above are representative. The exact set is finalised during implementation by
reading each block and sorting it with the classification rule; the model (four destinations +
the binary rule) is what is fixed here.

### Indicative mapping of current BEM families

Reference for implementation; not exhaustive, and each block is confirmed by reading it.

| Destination | Current families |
|---|---|
| `components/` (reusable) | `location-picker`, `partner`/`partner-strip`, `event`/`event-card`, `cta-button`, `gs-expect`→`feature-card`, `support-callout`, `page-hero`, `kal-day-band`/`kal-month-band` |
| `pages/` (page-only) | `about*`, `activity*`, `chapter*`/`grp*`, `steun*`/`volunteer-signup`, `gs-faq`/`gs*`, `ho`/`ho-deal`/`index*`, `kal`/`kal-filterrow`/`kal-optin` |
| `chrome.css` | `site-footer`, nav, `page`/`page-panel`, `link-plain` |
| `effects.css` | `@keyframes`, `prefers-reduced-motion` block, scroll-deck CSS |
| stays in `app.css` | `@theme` tokens, `@layer base` typography, link/heading defaults |

## Mechanism

The project builds CSS with `@tailwindcss/vite` (Tailwind v4). `app.css` already uses
`@import` for `tailwindcss` and `flux.css`, and the Tailwind bundler resolves and inlines
`@import` at build time. So partials work natively — **no `postcss-import`, no extra Vite
entries, no dependency changes.**

`app.css` ends with an explicit import block, e.g.:

```css
/* Reusable units */
@import './components/location-picker.css';
@import './components/partners.css';
/* … */
/* Global shell + page sections */
@import './chrome.css';
@import './pages/about.css';
/* … */
@import './effects.css';
```

**Cascade preservation is critical.** Every relocated rule keeps the exact `@layer` it lives
in today (the page BEM lives in `@layer components`). Each partial therefore wraps its rules in
`@layer components { … }` (or `base`, for anything from `@layer base`). Because Tailwind v4
emits utilities in the `utilities` layer — which still wins over `@layer components` — page
composition utilities keep overriding partial styles exactly as before. Net: identical
specificity and source order after inlining.

## Enforcement

A new `tests/Feature/CssArchitectureTest.php`, run in the normal `php artisan test` flow
(local + CI), with two checks:

### 1. Partials are registered

- Every `*.css` under `resources/css/components/` and `resources/css/pages/` (plus
  `chrome.css`, `effects.css`) appears in an `@import` in `app.css`.
- Every `@import './…'` in `app.css` that points into the partials tree resolves to a file
  that exists.

Result: no orphan partials (a file no one imports) and no dangling imports (an import to a
missing file). Keeps the split coherent as files are added/removed.

### 2. No raw hex / px in components

- Scan `resources/views/components/**/*.blade.php`.
- Fail on raw colour hex (`#rgb`/`#rrggbb`/`#rrggbbaa`) or `px` values found **in styling
  contexts only**: Tailwind arbitrary values (`class="… [color:#fff] w-[12px] …"`) and inline
  `style="…"` declarations.
- **Exemptions** (otherwise legit markup false-positives):
  - SVG presentation attributes: `fill=`, `stroke=`, `stroke-width=`, `d=`, `viewBox=`,
    `points=`, `cx/cy/r/x/y/width/height` on SVG shapes.
  - A short allowlist of files that are inherently raw-value (icon / logo / pattern
    components, e.g. `bike-icon`, `app-logo`, `app-logo-icon`, `placeholder-pattern`).

The regex is tuned so **the current component tree passes green** after the split. If a
non-exempt component carries a stray raw value, it is tokenised (or, if genuinely one-off and
legitimate, added to the allowlist with a comment) as part of the work — the test must be green
on merge.

This check applies to `.blade.php` components only (the user-selected scope). `components/*.css`
partials should also use tokens by convention but are not gated by this test.

## CLAUDE.md rule update

Extend "Public Site — Frontend Rules" with the partials convention:

- Non-component, page-specific CSS goes in its `resources/css/pages/<page>.css` partial.
- Reusable-unit CSS goes in `resources/css/components/<role>.css` (until/unless absorbed into
  the unit's `.blade.php`).
- `app.css` holds only tokens, `@layer base`, and the `@import` block — never page or component
  BEM.
- Point to `tests/Feature/CssArchitectureTest.php` as the enforcement, and note the
  "reusable → components/, one-page → pages/" classification rule.

The three-layer model (tokens / components / composition) and the headings rule (raw
`<h1>`–`<h6>`, never `flux:heading`, no inline size/weight) are unchanged.

## Rollout (collision-with-Nico risk)

This is a large rewrite of `app.css` in a shared checkout where Nico commits concurrently.

1. Do the split **in a git worktree**, as **one content-preserving commit** (cut blocks from
   `app.css`, paste into partials, add the `@import` block — no rule changes).
2. Verify equivalence before merge:
   - `npm run build` succeeds.
   - Spot-check a few representative pages (home, calendar, an about/chapter page) render
     identically — visual check / screenshot compare.
   - `php artisan test` green, including the new `CssArchitectureTest`.
3. Merge fast, ideally when Nico is not mid-edit on `app.css`. **Do not push `main`.**
4. If a concurrent `app.css` edit from Nico conflicts, his hunk resolves into the relevant
   partial (the block moved; his change applies there).

## Scope & non-goals

In scope: the directory split, the `@import` wiring, cascade-preserving relocation of existing
CSS, the enforcement test, and the CLAUDE.md rule update.

Non-goals (this round):

- **Not** pulling the leaked component appearance (`cta-button`, `event-card`, `partners`,
  `gs-expect`/`feature-card`) into their `.blade.php` files. It moves into `components/*.css`
  for now; full `.blade.php` absorption is a later opportunistic pass.
- **Not** extending the no-raw-values check to the `.css` partials (only `.blade.php`
  components, per decision).
- **No** blunt `app.css` line-count ceiling (rejected to avoid friction with Nico's commits).
  Collision relief comes from the split itself plus the registered-partials check; nothing
  hard-stops new BEM from re-accumulating in `app.css`, so the convention + code review still
  matter.
- **No** redesign, no visual changes, no dependency changes, no `@import` partials for
  vendor/Tailwind internals.

## Risks / watch-items

- **Cascade drift.** If a relocated block loses its `@layer components` wrapper, specificity
  changes and pages break subtly. Mitigation: every partial wraps rules in its original layer;
  visual spot-check before merge.
- **`@import` ordering.** Source order within `@layer components` still matters for
  same-specificity rules. Mitigation: keep the `@import` order in `app.css` matching the prior
  top-to-bottom order of the blocks as relocated.
- **Re-bloat.** With no size ceiling, an agent can still dump CSS into `app.css`. Mitigation:
  CLAUDE.md rule + the registered-partials test as a structural nudge + code review. Revisit a
  size gate if re-bloat recurs.
- **False positives in the raw-value check.** SVG-heavy components. Mitigation: styling-context
  scoping + allowlist, tuned to green on the current tree.

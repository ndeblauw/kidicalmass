---
title: Mobile responsive polish — public site
tags: [design, frontend, responsive, mobile, typography]
sources: [mobile-audit-2026-06-25]
phase: build
updated: 2026-06-25
---

# Mobile responsive polish — public site

## Goal

Make the public marketing site (NL-only, `/nl`) feel intentionally designed on
phones, not desktop-shrunk. This is a **targeted polish pass, not a responsive
rebuild** — the site is already mobile-first (viewport meta, working hamburger
nav, mobile-first CSS partials, global `overflow-x: clip` guard).

## Audit method & evidence

All 20 public pages were screenshotted at a 375px-wide phone viewport (2× DPR,
mobile emulation) plus the open mobile-nav state. Two automated checks ran:
horizontal-overflow measurement per page, and a DOM probe for offending
elements. A visual review of every screenshot caught the softer issues
(type scale, spacing, tap targets).

**Key correction from verification:** the audit's two largest flagged items —
"home dead space" and "about dead space" — are **not bugs**. They are
scroll-reveal sections (`<x-scroll-sequence>` on home, `<x-about-reveal>` on
about) whose content starts at `opacity: 0` and fades in on scroll. A flat
full-page screenshot captures them pre-reveal. Scrolling them into view on a
375px viewport confirmed all content reveals correctly (`is-inview`,
`opacity: 1`) with good spacing. **No fix needed.**

## Root cause

Headings are a fixed, non-responsive scale defined once in `@layer base`
(`resources/css/app.css`):

```
h1 { font-size: var(--text-6xl); }   h4 { font-size: var(--text-2xl); }
h2 { font-size: var(--text-5xl); }   h5 { font-size: var(--text-xl);  }
h3 { font-size: var(--text-3xl); }   h6 { font-size: var(--text-lg);  }
```

These pixel sizes do not step down for small viewports. On a 375px phone the
display headings (`h1`/`h2`) eat a large share of the screen and wrap to 2–3
lines, and the longest unbreakable Dutch compound — "coördinatieduo" on
`/nl/about/organisation` — renders past the right edge at hero size, dragging
the whole page into a 14px horizontal scroll (the only true overflow on the
site). **One responsive type pass fixes both the overflow and the
oversized-heading cluster sitewide**, because they share this single cause.

## Scope

### In scope

**Wave 1 — Responsive display type (the high-leverage fix).**

1. Make the heading scale responsive in `@layer base` of `app.css` (the one
   correct place; the architecture mandates the type scale lives here and
   nowhere else). Convert the display tiers (`h1`, `h2`, and `h3` if it reads
   too large on mobile) from a fixed token to a fluid `clamp()`:
   - **`max` = the current token value**, so desktop rendering is pixel-identical
     (no desktop regression).
   - **`min` = a mobile-appropriate size** so headings stop dominating the phone
     viewport.
   - Fluid middle term scales with viewport width.
   - `h4`–`h6` are already body-adjacent sizes; leave them unless a specific page
     shows a problem.
2. Add a long-word safety net to headings in `@layer base`:
   `overflow-wrap: break-word;` (and `hyphens: auto;`, which is honoured because
   `<html lang>` is set to `nl`). This guarantees no single long compound can
   ever force horizontal overflow again, independent of the size change.

**Wave 2 — Targeted spacing polish.** Each fix lives in the partial that owns
the unit (never `app.css`), authored mobile-first or layered down at the `md`
edge per the file's existing convention:

3. `/nl/find-a-bike` — heading sits too tight to the top; partner blocks
   (Kidical Mouse / My Kids Bikes / Fietsdeelb) are cramped. → find-a-bike page
   partial.
4. `/nl/steun-ons` — three stat cards (28 / 62 / 5.500) have inconsistent
   width/alignment rhythm when stacked. → `steun.css`.
5. Footer "Mede mogelijk gemaakt door" partner-logo lockup sits tight against
   the column edges on mobile. → `chrome.css`.
6. `/nl/about/news` and `/nl/about/partners` — long stacked-card pages with
   loose inter-card spacing. → respective page partials.
7. `/nl/about/organisation` — re-measure after Wave 1; confirm the organigram
   and the two-column `.ho-deal` "Wie wat doet" section also sit within the
   viewport at 375px (the heading was the primary cause, but verify the section
   too).

### Out of scope

- **Dead-space "bugs"** — confirmed scroll-reveal artifacts, not real.
- **`/nl/contact` and `/nl/privacy` stubs** — known placeholders awaiting
  backend (Nico); not a mobile concern.
- **No-JS reveal fallback** — scroll-reveal content stays `opacity: 0` when JS is
  disabled. This is a robustness gap on desktop too, not a mobile-responsive
  issue. Noted, deliberately not fixed here.
- Pages confirmed good on mobile, left untouched: home (scrolling), kalender,
  ride-detail, chapter-detail, chapters-index, nieuwsbrief, getting-started,
  help-out, start-een-groep, about-mission, about-vision, about-press.

## Architecture constraints

- Type scale changes go **only** in `@layer base` of `app.css`. Never set
  heading size/weight/line-height inline or per-component.
- All other CSS goes in role-based partials under `resources/css/`
  (`pages/*.css`, `chrome.css`), never piled into `app.css`. Default to
  `components/` only when a fix is genuinely reusable.
- Reference tokens only — no raw hex/px in `.blade.php` components
  (enforced by `tests/Feature/CssArchitectureTest.php`).
- Headings stay raw `<h1>`–`<h6>`; never `flux:heading`.
- Copy is not being rewritten; if any label is touched, follow
  `docs/tone-of-voice.md` (no em-dashes).

## Verification

- Re-run the 375px screenshot + overflow-probe pass; **every page must report
  `overflowX = 0`**, including about-organisation.
- Spot-check the revised display headings at 375px and at desktop width to
  confirm desktop is unchanged and mobile is stepped down.
- `php artisan test --filter=CssArchitectureTest` passes (partials registered,
  no raw values in components).
- Full suite (`php artisan test --compact`) stays green — note the known flaky
  `CalendarProximityTest` (order-dependent, not a regression signal).
- Update the build pipeline / page registry only if a page's UI stage genuinely
  advances; otherwise log the polish pass in `docs/wiki/log.md`.

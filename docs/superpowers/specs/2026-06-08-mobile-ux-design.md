---
title: Mobile UX refinement — public site
tags: [design, responsive, mobile, css]
phase: build
updated: 2026-06-08
sources:
  - mobile audit (390×844) of all 19 public pages, 2026-06-08
  - DESIGN.md (design system)
  - docs/tone-of-voice.md
  - CLAUDE.md "Public Site — Frontend Rules" + CSS-partials architecture
---

# Mobile UX refinement — public site

## 1. Problem

The public site is **functional but not designed for mobile**. It was built
desktop-first and mobile was patched in ad-hoc. A 390×844 audit of all 19 public
pages (all return 200; fonts, colour and the hamburger menu all work) surfaced:

**Root cause, not symptoms:** responsive handling is inconsistent and, on key
pages, missing.

- Breakpoints are ad-hoc with no shared scale: `768` (28×), plus one-off
  `767, 640, 600, 560, 480, 900, 1024`.
- Three important partials have **zero `@media` queries**: `calendar.css`,
  `home.css`, `local-groups.css`.
- No container queries anywhere (acceptable — see non-goals).

**Concrete defects found**

| ID | Page | Issue | Likely cause |
|----|------|-------|--------------|
| M1 | Kalender (`/events`) | Horizontal overflow +254px | `.kal-body { grid-template-columns: 1fr 300px }` and `.kal-sidebar` (fixed 300px) never collapse; `calendar.css` has no `@media`. `event-card__place { min-width: 10rem }` compounds it. |
| M2 | Event detail (`/events/{id}`) | Horizontal overflow +123px | Per-page; confirm after shared pass (hero/figure or wide inline element). |
| M3 | Kalender filter row | Location picker + 3 radius tabs cramped at 390px | `.kal-filterrow` is a non-wrapping flex row with `padding/margin 2rem`; locatie-zoeken Surface pass still pending. |
| M4 | About, Meehelpen | Large vertical dead-space (empty bands) | Real layout — entrance animations are on-load keyframes (`both` fill), not scroll reveals, so content is present. Suspect oversized illustration slots or band padding at small widths. Verify live. |
| M5 | Global | Mobile nav is unstyled plain-text dropdown; tap targets unverified | `site/header.blade.php` works (Alpine via `@fluxScripts`) but is visually plain; stale "no Alpine" comment. |
| M6 | Global | Touch-target / tap-spacing not audited | Footer links, inline links, "wijzig" control — verify ≥44px and adequate spacing. |

*(The recurring horizontal strip in audit screenshots was the Laravel Debugbar,
a dev-only overlay — not a layout bug.)*

## 2. Goals / non-goals

**Goals**
- Eliminate all horizontal overflow at 390px.
- Make the shared layer (nav, page-hero, cards, bands, filter row, footer)
  respond correctly on mobile so fixes cascade to every page.
- Establish a small, named, mobile-first breakpoint convention.
- Comfortable touch targets and reading rhythm on phones.
- A permanent programmatic regression guard against overflow.

**Non-goals (YAGNI)**
- No container queries.
- No navigation redesign (keep the working Alpine dropdown; restyle only).
- No new components or IA/content changes.
- No churn-migration of every existing `@media` — only those we touch.

## 3. Approach

**Fix the system, not the screenshots.** Per agreed scope: *mobile-first
refinement*, *shared-layer first*. Each shared fix cascades via the CSS-partials
architecture. Stay inside `docs/superpowers/specs/2026-06-06-css-partials-architecture-design.md`
rules — new CSS in role-based partials, never `app.css`; tokens not raw hex/px;
no `flux:heading`; honour tone-of-voice for any copy.

### 3.1 Breakpoint convention (foundation)
Adopt a named, **min-width mobile-first** scale as the going-forward convention:

- `sm` = 480px, `md` = 768px, `lg` = 1024px.

Document it (in the CSS-partials design note or a short comment block in
`app.css`). Apply it to partials we modify; do not rewrite untouched queries.

### 3.2 Shared-layer fixes (done first — cascade)
1. **`calendar.css` (M1, M3):** add the missing mobile layer.
   - Below `md`: `.kal-body` → single column.
   - `.kal-sidebar` "Mis geen rit" newsletter CTA → **folds into page flow below
     the agenda** on mobile (decision: keep it, don't drop it).
   - `.kal-filterrow` → allow wrap; reduce horizontal padding/negative margin at
     small widths so it doesn't push width.
2. **`event-card.css` (M1):** make `.event-card__place { min-width }` conditional
   (token-based or removed below `md`) so cards reflow and never force overflow in
   any container.
3. **`page-hero.css` (M4 partial):** audit the illustration slot ≤480px so art
   doesn't reserve dead space or crowd the title (a `--page-hero-h: 30rem` mobile
   override already exists in `effects.css`).
4. **Nav (M5):** brand-style the mobile dropdown panel, ensure ≥44px tap targets,
   remove the stale "no Alpine" comment in `site/header.blade.php`.
5. **Bands / footer / spacing (M6):** verify the `100vw` full-bleed mechanic
   (`width:100vw; margin-left:calc(50% - 50vw)`) never overflows at 390px; set a
   fluid section-gap rhythm (`clamp()`) so spacing breathes on small screens.

### 3.3 Page-specific pass (after cascade)
Re-screenshot at 390px; fix only residue. Known targets: **event detail (M2)**,
**About + Meehelpen dead-space (M4)**, **`home.css` / `local-groups.css`** (no
breakpoints today), and the global **touch-target / tap-spacing sweep (M6)**.

## 4. Testing & verification

CLAUDE.md requires programmatic tests for every change.

- **New Pest browser test** (`tests/Browser/MobileOverflowTest.php` or similar):
  load the key public routes at 390px width and assert no horizontal overflow
  (`document.documentElement.scrollWidth <= clientWidth`). Permanent regression
  guard. Cover at minimum: home, events index, event detail, chapters index,
  chapter detail, getting-started, steun-ons, help-out, about, contact.
- **Re-run `scripts/mobile-audit.cjs`** for visual confirmation, with Debugbar
  disabled so overflow numbers are clean.
- **`php artisan test --filter=CssArchitectureTest`** stays green (no raw hex/px,
  partials registered).
- `vendor/bin/pint --dirty --format agent` before finalising.

## 5. Sequencing (phases for the implementation plan)

1. Breakpoint tokens + documented convention.
2. Shared layer: calendar, event-card, page-hero, nav, bands/footer/spacing.
3. Page-specific mop-up: event detail, about, meehelpen, home, local-groups,
   touch-target sweep.
4. Overflow regression test + final 390px screenshot sweep + Pint.

## 6. Isolation note

The working tree is shared (Nico commits concurrently). Implementation should run
in a git worktree; stage specific files, never `git add -A`; do not push `main`.

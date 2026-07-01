# Mobile Responsive Polish Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the public marketing site feel designed for phones — fix the one real overflow and the oversized-heading cluster sitewide, then a small spacing-polish sweep.

**Architecture:** The site is already mobile-first. Headings use a *fixed* scale in `@layer base` of `resources/css/app.css` (`h1 = --text-6xl`, `h2 = --text-5xl`, …) with no mobile step-down — the single root cause of both the oversized phone headings and the lone horizontal overflow (the long Dutch compound "coördinatieduo", an `<h2 class="section-heading">`, at `text-5xl` = 48px). Wave 1 makes that scale responsive (fluid `clamp()`, desktop unchanged) plus adds a long-word safety net, both in `@layer base`. Wave 2 is targeted spacing polish, each fix in the partial that owns the unit.

**Tech Stack:** Tailwind CSS v4 (`@theme` tokens + `@layer base`/`components`), Blade, Laravel 13, Pest 4. Verification via Playwright (global install) DOM probes — no new browser-test infra.

## Global Constraints

- Type scale (size/weight/line-height) lives **only** in `@layer base` of `resources/css/app.css`. Never inline on headings, never per-component.
- All other CSS goes in role-based partials under `resources/css/` (`pages/*.css`, `chrome.css`); never pile new rules into `app.css`. Default to `components/` only when genuinely reusable.
- Reference tokens only — **no raw hex/px in `.blade.php` components** (enforced by `tests/Feature/CssArchitectureTest.php`). `rem`/`clamp()`/`vw` in CSS partials and `app.css` is fine.
- Headings stay raw `<h1>`–`<h6>`; never `flux:heading`.
- Page templates carry only *placement* utilities (`grid`, `flex`, `gap-*`, `p-*`, `m-*`, `max-w-*`); strip appearance utilities.
- Copy is not being rewritten. If any label is touched, follow `docs/tone-of-voice.md` — **no em-dashes**.
- Desktop rendering must not regress: every `clamp()` max equals the current token value.
- Known flaky test: `CalendarProximityTest` (order-dependent, passes in isolation) — not a regression signal.
- Worktree is `mobile-responsive` at `mobile-responsive.test`; global Playwright needs `NODE_PATH="$(npm root -g)"`; mobile probes use viewport 375×812, `deviceScaleFactor:2`, `isMobile:true`, `ignoreHTTPSErrors:true`.

---

## Task 0: Create the verification probe (shared by later tasks)

**Files:**
- Create: `scratchpad/mobile-check.cjs` (throwaway; in the session scratchpad, not committed)

The scratchpad path for this session is under `/private/tmp/claude-501/.../scratchpad/`. If unknown, create the file anywhere writable and adjust paths.

- [ ] **Step 1: Write the probe script**

```js
// mobile-check.cjs — overflow + computed-heading-size probe at 375px and 1280px
const { chromium } = require('playwright');
const BASE = 'https://mobile-responsive.test';
const PAGES = [
  ['home', '/nl'], ['about-organisation', '/nl/about/organisation'],
  ['steun-ons', '/nl/steun-ons'], ['find-a-bike', '/nl/find-a-bike'],
  ['about-news', '/nl/about/news'], ['about-partners', '/nl/about/partners'],
  ['about', '/nl/about'], ['kalender', '/nl/events'],
];
(async () => {
  const browser = await chromium.launch();
  for (const width of [375, 1280]) {
    const ctx = await browser.newContext({
      ignoreHTTPSErrors: true, viewport: { width, height: 900 },
      deviceScaleFactor: 2, isMobile: width === 375, hasTouch: width === 375,
    });
    const page = await ctx.newPage();
    console.log(`\n=== width ${width} ===`);
    for (const [name, path] of PAGES) {
      await page.goto(BASE + path, { waitUntil: 'networkidle', timeout: 30000 });
      const r = await page.evaluate(() => {
        const de = document.documentElement;
        const h1 = document.querySelector('h1'); const h2 = document.querySelector('h2');
        const px = el => el ? Math.round(parseFloat(getComputedStyle(el).fontSize)) : null;
        return { ov: de.scrollWidth - de.clientWidth, h1: px(h1), h2: px(h2) };
      });
      console.log(`${name.padEnd(20)} overflowX=${String(r.ov).padEnd(4)} h1=${r.h1}px h2=${r.h2}px`);
    }
    await ctx.close();
  }
  await browser.close();
})();
```

- [ ] **Step 2: Run it to capture the BEFORE baseline**

Run: `cd <scratchpad> && NODE_PATH="$(npm root -g)" node mobile-check.cjs`
Expected (current state): at width 375, `about-organisation` reports `overflowX=14`; `h1`/`h2` show the large fixed sizes (e.g. h1≈60px, h2≈48px) identical at 375 and 1280. Save this output to compare against later.

---

## Task 1: Long-word safety net on headings (fixes the P1 overflow)

**Files:**
- Modify: `resources/css/app.css` (the `h1, h2, h3, h4, h5, h6` rule in `@layer base`, currently ~line 157)

**Interfaces:**
- Produces: headings that can never force horizontal overflow from an unbreakable word. Task 2 builds on the same rule block.

- [ ] **Step 1: Verify the failing condition**

Run: `cd <scratchpad> && NODE_PATH="$(npm root -g)" node mobile-check.cjs`
Expected: `about-organisation overflowX=14` at width 375. This is the failure we fix.

- [ ] **Step 2: Add the safety net to the shared heading rule**

In `resources/css/app.css`, change:

```css
    h1, h2, h3, h4, h5, h6 {
        font-family: var(--font-heading);
        font-weight: 800;
        color: var(--color-kidical-ink);
    }
```

to:

```css
    h1, h2, h3, h4, h5, h6 {
        font-family: var(--font-heading);
        font-weight: 800;
        color: var(--color-kidical-ink);
        overflow-wrap: break-word;
        hyphens: auto;
    }
```

(`hyphens: auto` is honoured because `<html lang>` is set to `nl`; `overflow-wrap: break-word` is the hard guarantee that no single long compound overflows.)

- [ ] **Step 3: Rebuild assets**

Run: `npm run build`
Expected: build succeeds, new `app-*.css` hash emitted.

- [ ] **Step 4: Verify overflow is gone**

Run: `cd <scratchpad> && NODE_PATH="$(npm root -g)" node mobile-check.cjs`
Expected: `about-organisation overflowX=0` at width 375; every page reports `overflowX=0` at both widths.

- [ ] **Step 5: Commit**

```bash
git add resources/css/app.css public/build
git commit -m "fix(type): break long headings so no word forces mobile overflow

Why: \"coördinatieduo\" (an h2) ran past the 375px viewport edge,
dragging the whole about-organisation page into horizontal scroll."
```

---

## Task 2: Responsive display-type scale (fixes the P2 oversized headings)

**Files:**
- Modify: `resources/css/app.css` (the `h1`/`h2`/`h3` `font-size` lines in `@layer base`, currently ~lines 163-165)

**Interfaces:**
- Consumes: the heading block from Task 1.
- Produces: `h1`/`h2`/`h3` that are fluid — identical to today at ≥1024px, stepped down on phones. Page-level heading overrides (home hero, etc.) keep their own `clamp()` and are unaffected.

- [ ] **Step 1: Capture current heading sizes**

Run the probe (Task 0). Note h1/h2 px at width 375 vs 1280 — currently identical (no step-down). That is the problem.

- [ ] **Step 2: Make the display tiers fluid**

In `resources/css/app.css`, change:

```css
    h1 { font-size: var(--text-6xl); line-height: 1.05; }
    h2 { font-size: var(--text-5xl); line-height: 1.1; }
    h3 { font-size: var(--text-3xl); line-height: 1.25; }
```

to:

```css
    /* Fluid display tiers: max == the original token, so desktop (≥1024px) is
       pixel-identical; phones step down. h4–h6 are body-adjacent, left fixed. */
    h1 { font-size: clamp(2.5rem, 1.8rem + 3vw,   var(--text-6xl)); line-height: 1.05; }
    h2 { font-size: clamp(2rem,   1.4rem + 2.5vw, var(--text-5xl)); line-height: 1.1; }
    h3 { font-size: clamp(1.5rem, 1.3rem + 0.9vw, var(--text-3xl)); line-height: 1.25; }
```

Leave `h4`/`h5`/`h6` unchanged.

- [ ] **Step 3: Rebuild assets**

Run: `npm run build`
Expected: build succeeds.

- [ ] **Step 4: Verify desktop unchanged, mobile stepped down**

Run the probe (Task 0).
Expected at width **1280**: `h1≈60` `h2≈48` (unchanged from baseline — `--text-6xl`/`--text-5xl`).
Expected at width **375**: `h1≈40` `h2≈32` (stepped down), and still `overflowX=0` everywhere.

- [ ] **Step 5: Visual spot-check**

Re-run the full screenshot pass (the session's `mobile-shots.cjs`, or screenshot `/nl`, `/nl/about/organisation`, `/nl/about` at 375px). Confirm display headings no longer dominate the viewport and nothing looks too small. Confirm a desktop-width (1280) screenshot of `/nl/about/organisation` is visually unchanged.

- [ ] **Step 6: Commit**

```bash
git add resources/css/app.css public/build
git commit -m "feat(type): step display headings down on small viewports

- h1/h2/h3 become fluid clamp(); max equals the prior token so desktop
  is pixel-identical, phones get a readable, non-dominating scale
Why: the fixed desktop heading scale ate the 375px viewport and wrapped
oversized titles across 2-3 lines."
```

---

## Task 3: Wave 2 spacing-polish sweep

These are minor P3 visual items. Re-screenshot after Wave 1 first — some improve automatically once headings shrink. Fix only what still reads as off. Each edit lives in the partial that owns the unit; tune values against the 375px screenshot (visual gate), not blind.

**Files (touch only those still needed after re-screenshot):**
- Modify: `resources/css/pages/steun.css` — `.steun-proof__deck` mobile card rhythm
- Modify: `resources/css/chrome.css` — footer funder/partner lockup (`.site-footer__funder-logo` / partner area) edge spacing
- Modify: `resources/css/pages/about.css` and/or the articles partial — `/nl/about/news` & `/nl/about/partners` inter-card spacing
- Modify: `resources/views/find-a-bike.blade.php` — header top spacing + provider grid gap (composition utilities only)

- [ ] **Step 1: Re-screenshot after Wave 1**

Run the session screenshot pass at 375px for: `steun-ons`, `find-a-bike`, `about-news`, `about-partners`, plus any footer-bearing page. Compare against the original audit shots. List which P3 items still look off; skip the rest.

- [ ] **Step 2: steun-ons stat-card rhythm (if still off)**

In `resources/css/pages/steun.css`, find the `.steun-proof__deck` mobile/base rule. Ensure the stacked `<x-stat-card>` items share one width and align consistently. Concrete starting point — give the deck a consistent single-column width and centered alignment on mobile:

```css
/* inside the existing mobile/base block for .steun-proof__deck */
.steun-proof__deck {
    align-items: stretch;   /* cards share full column width when stacked */
    gap: 1rem;
}
```

Adjust the exact selector/value to match the file's existing structure and convention. Verify against the 375px screenshot.

- [ ] **Step 3: Footer funder/partner lockup edge spacing (if still tight)**

In `resources/css/chrome.css`, locate the footer funder credit (`.site-footer__funder-logo` and its `<span>` "Mede mogelijk gemaakt door") and the `<x-partners />` lockup. Add breathing room from the column edge on mobile, e.g.:

```css
/* near the .site-footer rules; keep within the file's existing layer */
.site-footer__funder-logo,
.site-footer__main {
    /* small inline padding so the lockup never touches the viewport edge on phones */
    padding-inline: 0.25rem;
}
```

Use the file's actual selectors and existing padding convention; do not introduce raw px. Verify visually.

- [ ] **Step 4: about-news / about-partners inter-card spacing (if still loose)**

In the partial that styles those stacked cards (`about.css` or the articles list partial — confirm by grepping the card class), tighten the vertical gap between cards on mobile to a single consistent token value (e.g. `gap: 1.25rem;` or matching the site's card rhythm). Verify visually.

- [ ] **Step 5: find-a-bike composition (if still cramped after Wave 1)**

In `resources/views/find-a-bike.blade.php`, this is wireframe-minimal styling with composition utilities only. If the header still sits tight to the top or the provider grid is cramped, adjust *placement* utilities only — e.g. ensure the page wrapper has top spacing consistent with sibling pages and bump the provider grid gap (`grid gap-6 sm:grid-cols-2` → `grid gap-8 sm:grid-cols-2`). No appearance utilities, no new CSS.

- [ ] **Step 6: Rebuild and verify architecture + overflow**

Run:
```bash
npm run build
php artisan test --filter=CssArchitectureTest --compact
cd <scratchpad> && NODE_PATH="$(npm root -g)" node mobile-check.cjs
```
Expected: build succeeds; `CssArchitectureTest` passes (partials registered, no raw values in components); every page still `overflowX=0` at both widths.

- [ ] **Step 7: Commit**

```bash
git add resources/css public/build resources/views/find-a-bike.blade.php
git commit -m "style(mobile): spacing polish on steun deck, footer lockup, cards, find-a-bike

Why: tighten the remaining small-screen rhythm issues surfaced by the
375px audit after the type pass."
```

---

## Task 4: Final verification + log

**Files:**
- Modify: `docs/wiki/log.md`

- [ ] **Step 1: Full test suite**

Run: `php artisan test --compact`
Expected: green. If `CalendarProximityTest` fails, re-run it in isolation (`php artisan test --filter=CalendarProximityTest`) to confirm it's the known order-dependent flake, not a regression.

- [ ] **Step 2: Final overflow sweep across ALL pages**

Re-run the session's full `mobile-shots.cjs` (all 20 pages). Expected: every page `overflowX=0`. Eyeball the headings on home, about, about-organisation, find-a-bike at 375px.

- [ ] **Step 3: Log the pass**

Append a `## [2026-06-25] build | mobile responsive polish` entry to `docs/wiki/log.md` summarising: fluid display type + word-wrap safety in `@layer base`, fixed the about-organisation overflow, Wave 2 spacing polish. Note that the page-registry stages were not bumped (no page's UI stage materially advanced; this is cross-cutting polish) — or bump any page whose UI genuinely advanced per `/pipeline` rules.

- [ ] **Step 4: Commit**

```bash
git add docs/wiki/log.md
git commit -m "docs(build): log mobile responsive polish pass"
```

---

## Self-review notes

- **Spec coverage:** Wave 1 type → Tasks 1-2. Wave 2 polish (find-a-bike, steun, footer, about cards) → Task 3. Overflow re-verify on about-organisation → Tasks 1 & 3 & 4. Out-of-scope items (dead-space artifacts, contact/privacy stubs, no-JS fallback) intentionally absent. Verification (overflow probe, CssArchitectureTest, full suite, log) → Tasks 0/3/4.
- **Desktop-regression guard:** every `clamp()` max == prior token; Task 2 Step 4 asserts identical desktop sizes.
- **No new test infra:** verification uses deterministic Playwright DOM probes, consistent with the spec's decision to avoid net-new Pest browser tests.

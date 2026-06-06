# CSS Partials Architecture Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Split the 4,728-line `resources/css/app.css` into role-based partials (`components/`, `pages/`, `chrome.css`, `effects.css`) and add a Pest test that enforces the structure, so concurrent work stops colliding in one shared file.

**Architecture:** Content-preserving relocation. `app.css` becomes a thin entry file (Tailwind/Flux imports, `@theme` tokens, `@layer base`, then an `@import` block). Each BEM family moves into a partial wrapped in its original `@layer`, so cascade and rendered output are unchanged. Tailwind v4's `@tailwindcss/vite` bundler inlines the `@import`s at build time — no new dependencies. A `CssArchitectureTest` then locks the structure in.

**Tech Stack:** Tailwind CSS v4, `@tailwindcss/vite`, Laravel 12, Pest 4, Vite, Playwright (visual spot-check).

---

## Spec

Source spec: `docs/superpowers/specs/2026-06-06-css-partials-architecture-design.md`.

## Ground rules for the executor

- **This is a relocation, not a rewrite.** Never change a CSS rule's selector, properties, or
  values while moving it. Cut the exact text, paste it, wrap it in the same `@layer`.
- **Preserve source order.** Work top-to-bottom through `app.css`. Within `@layer components`,
  same-specificity rules depend on source order, so the `@import` block in `app.css` must list
  partials in the order their blocks first appeared.
- **Build + look after every relocation task.** The safety net is "does it still render
  identically", not unit assertions — relocation has no behaviour to assert per-block.
- **Shared checkout.** Nico commits concurrently into the same tree. Do this in a worktree
  (Task 1), commit per task, and do **not** push `main`.

## File structure

| File | Responsibility |
|---|---|
| `resources/css/app.css` | Entry only: `@import 'tailwindcss'`, flux.css, `@source`, `@theme`, `@layer theme`, `@layer base`, then the partial `@import` block. No page/component BEM. |
| `resources/css/chrome.css` | Global shell: `site-footer`, nav, `page`/`page-panel` frame, `link-plain`. |
| `resources/css/effects.css` | `@keyframes`, `prefers-reduced-motion` block, scroll-stacking deck CSS. |
| `resources/css/components/*.css` | Reusable units, one file per role (location-picker, partners, event-card, cta-button, feature-card, support-callout, page-hero, kal-bands). |
| `resources/css/pages/*.css` | Page-only sections (home, calendar, chapters, activity, about, steun, getting-started). |
| `tests/Feature/CssArchitectureTest.php` | Enforces: every partial is imported + no dangling imports; no raw hex/px in `.blade.php` components. |
| `CLAUDE.md` | Adds the partials convention + classification rule + pointer to the test. |

---

### Task 1: Isolated worktree + baseline screenshots

**Files:**
- Create: `/tmp/css-baseline.cjs` (throwaway screenshot script)

- [ ] **Step 1: Create the worktree**

REQUIRED SUB-SKILL: Use `superpowers:using-git-worktrees` to create an isolated worktree off
`main` for this work (branch e.g. `refactor/css-partials`). All subsequent tasks run inside it.

- [ ] **Step 2: Confirm a clean baseline build**

Run: `npm run build`
Expected: build succeeds, no errors.

- [ ] **Step 3: Capture baseline screenshots of representative pages**

Use the Write tool to create `/tmp/css-baseline.cjs` (`.cjs` because the project is ESM):

```js
const { chromium } = require('playwright');
const PAGES = [
  ['home', 'https://kidicalmass.test/'],
  ['calendar', 'https://kidicalmass.test/nl/kalender'],
  ['about', 'https://kidicalmass.test/nl/over-ons'],
  ['getting-started', 'https://kidicalmass.test/nl/zo-werkt-het'],
  ['steun', 'https://kidicalmass.test/nl/steun-ons'],
  ['chapters', 'https://kidicalmass.test/nl/lokale-groepen'],
];
(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ ignoreHTTPSErrors: true, viewport: { width: 1440, height: 900 } });
  const page = await ctx.newPage();
  for (const [name, url] of PAGES) {
    await page.goto(url, { waitUntil: 'networkidle' });
    await page.screenshot({ path: `/tmp/css-before-${name}.png`, fullPage: true });
    console.log(`/tmp/css-before-${name}.png`);
  }
  await browser.close();
})();
```

Confirm the actual NL route paths first with `php artisan route:list --path=nl` and fix any
URL that 404s before capturing. Then run:

Run: `node /tmp/css-baseline.cjs`
Expected: six `/tmp/css-before-*.png` files written.

- [ ] **Step 4: Commit the baseline script (optional, keeps it out of the repo)**

Do not commit `/tmp` files. Nothing to commit this task; proceed.

---

### Task 2: Scaffold structure + relocate `chrome.css` (proves the mechanism end-to-end)

**Files:**
- Create: `resources/css/chrome.css`, `resources/css/components/.gitkeep`, `resources/css/pages/.gitkeep`
- Modify: `resources/css/app.css`

- [ ] **Step 1: Create the directories**

```bash
mkdir -p resources/css/components resources/css/pages
touch resources/css/components/.gitkeep resources/css/pages/.gitkeep
```

- [ ] **Step 2: Locate the chrome blocks in app.css**

Run: `grep -nE '\.(site-footer|link-plain)' resources/css/app.css`
Also find the nav and `page`/`page-panel` shell blocks:
Run: `grep -nE '^\s*\.(page|page-panel)[^a-z-]' resources/css/app.css`

Note the contiguous rule blocks (including any nested `@media`) for: `site-footer*`, nav,
`page`/`page-panel`, `link-plain`.

- [ ] **Step 3: Move the chrome blocks into `resources/css/chrome.css`**

Cut each identified block from `app.css` and paste into `chrome.css`, **wrapped in the same
layer it came from**. If the source block was inside `@layer components { … }`, the partial is:

```css
@layer components {
    /* site-footer, nav, page/page-panel, link-plain — moved verbatim from app.css */
    .site-footer { /* …exact original rules… */ }
    /* … */
}
```

Do not edit any property or value. Preserve the order the blocks appeared in `app.css`.

- [ ] **Step 4: Wire the import in app.css**

In `app.css`, after `@layer base { … }`, add an import block and the first entry:

```css
/* ── Partials (role-based; see docs/superpowers/specs/2026-06-06-css-partials-architecture-design.md) ── */
@import './chrome.css';
```

- [ ] **Step 5: Build and visually verify**

Run: `npm run build`
Expected: success.
Then re-run the screenshot script writing to `/tmp/css-after-*.png` (edit the path prefix) and
compare `home` + `calendar` before/after — footer, nav, page frame must be pixel-identical.

- [ ] **Step 6: Commit**

```bash
git add resources/css/chrome.css resources/css/components/.gitkeep resources/css/pages/.gitkeep resources/css/app.css
git commit -m "refactor(css): extract chrome (footer/nav/page shell) into chrome.css partial"
```

---

### Task 3: Relocate `effects.css`

**Files:**
- Create: `resources/css/effects.css`
- Modify: `resources/css/app.css`

- [ ] **Step 1: Locate the cross-cutting effect blocks**

Run: `grep -nE '@keyframes|prefers-reduced-motion|gs-expect-scroll' resources/css/app.css`

These are the `@keyframes` rules, the `@media (prefers-reduced-motion: reduce)` block, and the
scroll-stacking deck CSS (`gs-expect-scroll*` keyframe-driven bits that are behaviour, not the
card's appearance).

- [ ] **Step 2: Move them into `resources/css/effects.css`**

Cut and paste verbatim. `@keyframes` are top-level (not inside `@layer`), so do **not** wrap
those in a layer; keep any `@media`/`@layer` exactly as it was in the source.

- [ ] **Step 3: Wire the import (last in the block, so animations layer over base styles)**

In `app.css`, add at the end of the `@import` partial block:

```css
@import './effects.css';
```

- [ ] **Step 4: Build and verify**

Run: `npm run build`
Expected: success. Spot-check the getting-started page's scroll-stacking deck still animates.

- [ ] **Step 5: Commit**

```bash
git add resources/css/effects.css resources/css/app.css
git commit -m "refactor(css): extract keyframes/reduced-motion/scroll-deck into effects.css"
```

---

### Task 4: Relocate reusable units into `components/*.css`

**Files:**
- Create: `resources/css/components/location-picker.css`, `partners.css`, `event-card.css`, `cta-button.css`, `feature-card.css`, `support-callout.css`, `page-hero.css`, `kal-bands.css`
- Modify: `resources/css/app.css`

**Procedure (apply per unit below):** find the family's blocks → cut from `app.css` → paste
into the named partial wrapped in `@layer components` (verbatim, preserving order) → add
`@import './components/<file>.css';` to `app.css` (keep import order matching original block
order) → `npm run build` → spot-check the page(s) that use it → commit.

- [ ] **Step 1: `location-picker.css`**

Run: `grep -nE '\.location-picker' resources/css/app.css`
Move all `.location-picker*` blocks. Used on calendar + local groups. Verify both pages render
identically after build.

- [ ] **Step 2: `partners.css`**

Run: `grep -nE '\.partner(-strip)?' resources/css/app.css`
Move `.partner*` / `.partner-strip*`. Verify home + about partner strip.

- [ ] **Step 3: `event-card.css`**

Run: `grep -nE '\.event(-card)?' resources/css/app.css`
Move `.event*` / `.event-card*`. Verify the calendar agenda rows.

- [ ] **Step 4: `cta-button.css`**

Run: `grep -nE '\.cta-button' resources/css/app.css`
Move `.cta-button*`. Verify a CTA on home/about/support.

- [ ] **Step 5: `feature-card.css`**

Run: `grep -nE '\.gs-expect[^-]|\.gs-expect-card|\.gs-expect-deck' resources/css/app.css`
Move the feature-card *appearance* leftovers (`gs-expect-card` and siblings that style the card
itself). Leave page-side placement (tilt, deck grid) for `pages/getting-started.css` in Task 5.
Verify getting-started deck + mission promise cards.

- [ ] **Step 6: `support-callout.css`**

Run: `grep -nE '\.support-callout' resources/css/app.css`
Move `.support-callout*`. Verify wherever the callout appears.

- [ ] **Step 7: `page-hero.css`**

Run: `grep -nE '\.page-hero' resources/css/app.css`
Move `.page-hero*`. Verify a hero on any inner page.

- [ ] **Step 8: `kal-bands.css`**

Run: `grep -nE '\.kal-(day|month)-band' resources/css/app.css`
Move only the `kal-day-band` / `kal-month-band` blocks (these are reused calendar components).
Leave `kal`, `kal-filterrow`, `kal-optin` for `pages/calendar.css`. Verify the calendar bands.

- [ ] **Step 9: Commit (one commit per unit is fine; or batch this task)**

```bash
git add resources/css/components/*.css resources/css/app.css
git commit -m "refactor(css): extract reusable units into components/*.css partials"
```

---

### Task 5: Relocate page-only sections into `pages/*.css`

**Files:**
- Create: `resources/css/pages/home.css`, `calendar.css`, `chapters.css`, `activity.css`, `about.css`, `steun.css`, `getting-started.css`
- Modify: `resources/css/app.css`

**Procedure:** same as Task 4 (find → cut → paste in `@layer components` → import → build →
spot-check → commit), one page file at a time.

- [ ] **Step 1: `pages/home.css`**

Run: `grep -nE '\.(ho|ho-deal|index)[^a-z-]' resources/css/app.css`
Move home-only families. Verify the home page.

- [ ] **Step 2: `pages/calendar.css`**

Run: `grep -nE '\.(kal|kal-filterrow|kal-optin)[^a-z-]' resources/css/app.css`
Move the calendar-page sections (NOT the `kal-*-band` components moved in Task 4). Verify
calendar.

- [ ] **Step 3: `pages/chapters.css`**

Run: `grep -nE '\.(chapter|grp)[^a-z]' resources/css/app.css`
Move `.chapter*` / `.grp*`. Verify local-groups + a chapter page.

- [ ] **Step 4: `pages/activity.css`**

Run: `grep -nE '\.activity' resources/css/app.css`
Move `.activity*` (organizers, info, promises *placement* — the tilt/grid, since the card
appearance went to `components/feature-card.css`). Verify an activity detail page.

- [ ] **Step 5: `pages/about.css`**

Run: `grep -nE '\.about' resources/css/app.css`
Move `.about*` (nav, intent, organigram, section, press, reveal). Verify about pages.

- [ ] **Step 6: `pages/steun.css`**

Run: `grep -nE '\.(steun|volunteer-signup)' resources/css/app.css`
Move `.steun*` + `.volunteer-signup*`. Verify steun-ons.

- [ ] **Step 7: `pages/getting-started.css`**

Run: `grep -nE '\.gs(-faq)?[^a-z-]' resources/css/app.css`
Move remaining `gs*` page sections (`gs-faq`, getting-started layout) NOT already in
`feature-card.css`/`effects.css`. Verify getting-started.

- [ ] **Step 8: Confirm app.css now holds only entry content**

Run: `grep -nE '^\s*\.[a-z]' resources/css/app.css`
Expected: no page/component BEM selectors remain — only `@theme`, `@layer base` element rules,
and the `@import` block. Any leftover class block belongs in a partial; move it.

- [ ] **Step 9: Full build + screenshot diff across all six baseline pages**

Run: `npm run build`
Re-run the screenshot script to `/tmp/css-after-*.png` and compare all six before/after pairs.
Expected: visually identical. Investigate any diff before committing (usually a lost `@layer`
wrapper or reordered import).

- [ ] **Step 10: Commit**

```bash
git add resources/css/pages/*.css resources/css/app.css
git commit -m "refactor(css): extract page-only sections into pages/*.css partials"
```

---

### Task 6: Enforcement test — partials registered

**Files:**
- Create: `tests/Feature/CssArchitectureTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use Illuminate\Support\Facades\File;

function cssPartials(): \Illuminate\Support\Collection
{
    return collect(File::allFiles(resource_path('css')))
        ->filter(fn ($f) => $f->getExtension() === 'css')
        ->reject(fn ($f) => $f->getFilename() === 'app.css');
}

test('every css partial is imported by app.css', function () {
    $appCss = File::get(resource_path('css/app.css'));

    foreach (cssPartials() as $partial) {
        $relative = './'.str_replace(resource_path('css').DIRECTORY_SEPARATOR, '', $partial->getPathname());
        $relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);
        expect($appCss)->toContain("@import '{$relative}'");
    }
});

test('every local @import in app.css resolves to an existing file', function () {
    $appCss = File::get(resource_path('css/app.css'));
    preg_match_all("/@import\s+'(\.\/[^']+)'/", $appCss, $matches);

    foreach ($matches[1] as $importPath) {
        $abs = resource_path('css/'.ltrim($importPath, './'));
        expect(File::exists($abs))->toBeTrue("Dangling @import in app.css: {$importPath}");
    }
});
```

(The `\.\/` regex only matches local `./` imports, so `@import 'tailwindcss'` and the flux.css
vendor import are correctly ignored.)

- [ ] **Step 2: Run it — expect PASS (structure already exists from Tasks 2–5)**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: both registration tests PASS. If "every partial is imported" fails, an orphan partial
exists — add its `@import` to `app.css`. If "resolves to existing file" fails, fix the typo'd
import path.

- [ ] **Step 3: Prove the test bites (temporary negative check)**

Temporarily create an empty `resources/css/pages/orphan-temp.css` and run the test again.
Expected: "every css partial is imported" FAILS. Delete `orphan-temp.css` and confirm it passes
again. (Do not commit the orphan.)

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/CssArchitectureTest.php
git commit -m "test(css): enforce all partials are registered, no dangling imports"
```

---

### Task 7: Enforcement test — no raw hex/px in components

**Files:**
- Modify: `tests/Feature/CssArchitectureTest.php`

- [ ] **Step 1: Add the failing test**

Append to `tests/Feature/CssArchitectureTest.php`:

```php
test('blade components do not hardcode raw colors or px in styling contexts', function () {
    // Inherently raw-value components (SVG icons / logos / patterns).
    $allowlist = ['bike-icon', 'app-logo', 'app-logo-icon', 'placeholder-pattern'];

    $files = collect(File::allFiles(resource_path('views/components')))
        ->filter(fn ($f) => str_ends_with($f->getFilename(), '.blade.php'))
        ->reject(fn ($f) => in_array(str_replace('.blade.php', '', $f->getFilename()), $allowlist, true));

    $raw = '#[0-9a-fA-F]{3,8}\b|\b\d+px\b';
    $violations = [];

    foreach ($files as $file) {
        $content = File::get($file->getPathname());

        // Tailwind arbitrary values: class="… [color:#fff] min-h-[60px] …"
        preg_match_all('/\[[^\]\s]*(?:'.$raw.')[^\]]*\]/', $content, $arbitrary);
        // Inline style declarations: style="color:#fff; width:12px"
        preg_match_all('/style\s*=\s*"[^"]*(?:'.$raw.')[^"]*"/', $content, $inline);

        $hits = array_merge($arbitrary[0], $inline[0]);
        if ($hits !== []) {
            $violations[$file->getRelativePathname()] = array_values(array_unique($hits));
        }
    }

    expect($violations)->toBe([], 'Raw hex/px in components (use tokens, or add to allowlist if SVG/icon): '.json_encode($violations, JSON_PRETTY_PRINT));
});
```

- [ ] **Step 2: Run it and triage**

Run: `php artisan test --compact --filter='do not hardcode'`
Expected: it lists any current violations. For each:
- If it is a styling value that has a token (colour, radius, shadow) → replace with the token
  utility (`bg-kidical-*`, `rounded-card`, `shadow-card`) in the component `.blade.php`.
- If it is a genuine one-off SVG/icon/pattern component → add its base name to `$allowlist`
  with the others.
- If it is a legitimately needed hairline (e.g. `1px` border with no rem equivalent) → prefer a
  Tailwind border utility; only allowlist the file as a last resort, with a comment.

Iterate until the test is green. Re-run `npm run build` after any `.blade.php` edit.

- [ ] **Step 3: Confirm green**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: all CssArchitectureTest tests PASS.

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/CssArchitectureTest.php resources/views/components
git commit -m "test(css): forbid raw hex/px in blade components; tokenise stragglers"
```

---

### Task 8: Update CLAUDE.md convention

**Files:**
- Modify: `CLAUDE.md` (the "Public Site — Frontend Rules" section)

- [ ] **Step 1: Add the partials rule**

Under "Public Site — Frontend Rules", after the three-layer styling block, add:

```markdown
- CSS lives in role-based partials under `resources/css/`, never piled into `app.css`:
  - **Reusable across pages** → `resources/css/components/<role>.css` (until absorbed into the
    unit's `.blade.php`).
  - **Appears on one page only** → `resources/css/pages/<page>.css`.
  - **Global shell** (footer/nav/page frame) → `resources/css/chrome.css`; **cross-cutting
    effects** (keyframes/reduced-motion/scroll-deck) → `resources/css/effects.css`.
  - `app.css` holds ONLY `@theme` tokens, `@layer base`, and the `@import` block.
  - Classification rule when unsure: default to `components/`.
  - Enforced by `tests/Feature/CssArchitectureTest.php` (partials must be registered; no raw
    hex/px in `.blade.php` components). Run `php artisan test --filter=CssArchitectureTest`.
```

- [ ] **Step 2: Sanity check the docs build/readme references are intact**

Run: `grep -n 'three-layer\|app.css' CLAUDE.md`
Expected: the existing three-layer rule is still present and the new partials rule sits beside
it (no contradiction — three-layer says *what* kind of style; partials say *which file*).

- [ ] **Step 3: Commit**

```bash
git add CLAUDE.md
git commit -m "docs(claude): require role-based CSS partials; point to enforcement test"
```

---

### Task 9: Final verification

- [ ] **Step 1: Full build**

Run: `npm run build`
Expected: success, no warnings about missing imports.

- [ ] **Step 2: Full test suite**

Run: `php artisan test --compact`
Expected: green, including `CssArchitectureTest`.

- [ ] **Step 3: Final visual diff**

Re-run the screenshot script and compare all six pages before/after the whole change.
Expected: visually identical across home, calendar, about, getting-started, steun, chapters.

- [ ] **Step 4: Confirm app.css shrank to an entry file**

Run: `wc -l resources/css/app.css`
Expected: a few hundred lines (tokens + base + imports), down from 4,728.

- [ ] **Step 5: Hand off**

Report the new `app.css` size, the partial file list, and the green test run. Per the shared
checkout rule, do **not** push `main` — surface the branch/worktree for review and let Frederik
merge.

---

## Self-review notes

- **Spec coverage:** mechanism (Tasks 2–5), `@layer` preservation (each relocation step),
  enforcement check #1 registered-partials (Task 6), check #2 no-raw-values (Task 7), CLAUDE.md
  rule (Task 8), worktree rollout + visual verification (Tasks 1, 5, 9), non-goals respected
  (component appearance parked in `components/*.css`, not pulled into `.blade.php`; no size cap).
- **No size-ceiling test** — intentional per spec (avoid friction with Nico).
- **Classification edge cases** (`feature-card` appearance vs getting-started placement;
  `kal-bands` component vs `kal` page) are split explicitly across Tasks 4 and 5.

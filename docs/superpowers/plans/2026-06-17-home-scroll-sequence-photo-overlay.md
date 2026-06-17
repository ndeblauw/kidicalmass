# Home scroll-sequence photo + half-size overlapping illustration — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Give each of the home page's three "drie routes" sections a crossfading photo behind it, with the existing bike/helper illustration riding in at ~50% size over the photo's bottom-left, and make the illustrations ride on/off the real viewport edge instead of clipping at the container.

**Architecture:** Pure front-end, scoped to home. Add three photo `<img>`s to the existing `<x-scroll-sequence>` media slot (reusing the proven `/help-out` `ho-deal__photo` crossfade pattern — Alpine already toggles `is-active` on every `[data-seq-media]`). Style the new photo layer and shrink/anchor the illustration layer in `resources/css/pages/home.css`. Move the horizontal clip from the centered `.home-routes` out to the full-bleed `.page-panel--home`. No JS, no shared-component changes.

**Tech Stack:** Laravel 12 Blade, Livewire/Alpine (already loaded), Tailwind v4 + role-based CSS partials, Pest 4.

## Global Constraints

- Scope strictly to home: touch only `.home-routes`, `.page-panel--home`, and the home media markup. Do NOT change `resources/views/components/scroll-sequence.blade.php`, `resources/css/components/scroll-sequence.css`, `/help-out`, or `/volunteer`.
- CSS goes in `resources/css/pages/home.css` (a registered page partial). New CSS never goes in `app.css`. Raw rem / `color-mix` values are acceptable in page partials (the no-raw-value rule applies to `.blade.php` components, not partials) — mirror the existing convention in `help-out.css`.
- Headings: raw `<h1>`–`<h6>` only (already the case here; no new headings added).
- Decorative imagery: `alt=""` only where the element carries no meaning; here photos get warm, concrete NL `alt` text per `docs/tone-of-voice.md`. No em-dashes in any copy (alt text included).
- Use `overflow: clip` (never `hidden`) for the new horizontal clip — `hidden` creates a scroll container and breaks `position: sticky`.
- Mobile (below lg) stays byte-for-byte unchanged: the media column is already `display:none` there; photos are a lg+-only enhancement.
- Reduced motion (`prefers-reduced-motion: reduce`) must remain quiet: no riding/scale; copy and images settle via opacity only.

---

## File Structure

- `resources/views/home.blade.php` — add three photo `<img>`s into the `<x-slot:media>` (before the three illustration imgs). Markup only.
- `resources/css/pages/home.css` — photo layer styles, illustration resize/anchor + viewport-relative ride transforms, the overflow-clip move, reduced-motion extension.
- `tests/Feature/HomeRoutesMediaTest.php` — new Pest feature test asserting the three photos and three illustrations render on `/`.

---

## Task 0: Workspace setup & clean baseline

This worktree has no `vendor/` or `node_modules/` yet; tests and the screenshot pass need them. Per the project's worktree-verification recipe: a real composer install (not a symlinked vendor), an npm build, and a Herd link.

**Files:** none (environment only).

- [ ] **Step 1: Install PHP deps**

Run: `composer install`
Expected: completes, `vendor/bin/pest` exists.

- [ ] **Step 2: Install + build front-end**

Run: `npm install && npm run build`
Expected: Vite build succeeds, `public/build/manifest.json` present.

- [ ] **Step 3: Link this worktree to Herd (for the later screenshot pass)**

Run: `herd link home-scroll-photos`
Expected: prints a `.test` URL (e.g. `https://home-scroll-photos.test`). Note it for Task 2's screenshot.

- [ ] **Step 4: Verify clean baseline**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS (this guards the CSS-partials architecture and runs fast). Report any failure before proceeding.

---

## Task 1: Photos in the media slot (+ render test)

Add the three crossfading photos to the home scroll-sequence media slot and lock the markup with a render test. The first photo gets `is-active` (backdrop painted on first view); the illustrations keep their current state (no `is-active` on the first, so the bike still rolls in fresh).

**Files:**
- Test: `tests/Feature/HomeRoutesMediaTest.php` (create)
- Modify: `resources/views/home.blade.php` (the `<x-slot:media>` of `<x-scroll-sequence class="home-routes">`, around lines 94–98)

**Interfaces:**
- Consumes: the existing `<x-scroll-sequence>` component and its Alpine `setActive(i)`, which toggles `is-active`/`is-past` on every `[data-seq-media]` matching the active block index. No component change.
- Produces: photo `<img>`s with class `home-routes__photo` and `data-seq-media="0|1|2"` (styled in Task 2).

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/HomeRoutesMediaTest.php`:

```php
<?php

use function Pest\Laravel\get;

it('renders the three route photos and three illustrations on the home page', function () {
    $response = get('/');

    $response->assertOk();

    $photos = [
        'img/photography/kids-thumbsup-at-ride.jpg',
        'img/photography/ride-cinquantenaire-crowd.jpg',
        'img/photography/volunteers/volunteers-pink-vests-with-flag.jpg',
    ];

    foreach ($photos as $photo) {
        $response->assertSee($photo, false);
    }

    $illustrations = [
        'img/illustrations/waving-rider.svg',
        'img/illustrations/longtail-with-kid.svg',
        'img/illustrations/volunteer-with-wrench.svg',
    ];

    foreach ($illustrations as $illustration) {
        $response->assertSee($illustration, false);
    }
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter=HomeRoutesMediaTest`
Expected: FAIL — the three `img/photography/...` paths are not yet in the markup (the illustration assertions already pass).

- [ ] **Step 3: Add the photos to the media slot**

In `resources/views/home.blade.php`, replace the `<x-slot:media>` block:

```blade
            <x-slot:media>
                <img class="home-routes__photo is-active" data-seq-media="0" src="{{ asset('img/photography/kids-thumbsup-at-ride.jpg') }}" alt="Twee kinderen met fietshelm steken vrolijk hun duim op tijdens een Kidical Mass" loading="lazy">
                <img class="home-routes__photo" data-seq-media="1" src="{{ asset('img/photography/ride-cinquantenaire-crowd.jpg') }}" alt="Een grote groep gezinnen fietst samen weg onder de triomfboog van het Jubelpark" loading="lazy">
                <img class="home-routes__photo" data-seq-media="2" src="{{ asset('img/photography/volunteers/volunteers-pink-vests-with-flag.jpg') }}" alt="Een warme bende vrijwilligers in hesjes zwaait blij met de Kidical Mass-vlag" loading="lazy">

                <img class="home-routes__illu" data-seq-media="0" src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" loading="lazy">
                <img class="home-routes__illu" data-seq-media="1" src="{{ asset('img/illustrations/longtail-with-kid.svg') }}" alt="" loading="lazy">
                <img class="home-routes__illu" data-seq-media="2" src="{{ asset('img/illustrations/volunteer-with-wrench.svg') }}" alt="" loading="lazy">
            </x-slot:media>
```

(The three illustration `<img>`s are unchanged from the current markup; they now follow the photos in DOM so they render on top.)

- [ ] **Step 4: Run the test to verify it passes**

Run: `php artisan test --compact --filter=HomeRoutesMediaTest`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add tests/Feature/HomeRoutesMediaTest.php resources/views/home.blade.php
git commit -m "feat(home): add crossfading photos behind the three route sections"
```

---

## Task 2: Photo layer, half-size overlapping illustration, viewport-edge ride

Style the photo layer, shrink and anchor the illustration over each photo's bottom-left, and move the horizontal clip to the full-bleed wrapper so illustrations ride on/off the real screen. All edits in `resources/css/pages/home.css`.

**Files:**
- Modify: `resources/css/pages/home.css` (the `.page-panel.page-panel--home` rule ~line 252; the `.home-routes` overflow rule ~line 294; the `.home-routes__illu` rules ~line 328 and inside the `@media (min-width: 1024px)` block ~line 359; the `@media (prefers-reduced-motion: reduce)` block ~line 397)
- Test: `tests/Feature/HomeRoutesMediaTest.php` (already covers markup; CSS is verified by `CssArchitectureTest` + a screenshot)

**Interfaces:**
- Consumes: `.home-routes__photo` and `.home-routes__illu` elements with `data-seq-media="0|1|2"` from Task 1; the base component rule `.scroll-sequence__media-sticky > [data-seq-media]` (absolute `inset:0; 100%`, opacity crossfade at lg+); the sticky frame's existing `overflow: visible clip`.
- Produces: final rendered behaviour. No symbols consumed downstream.

- [ ] **Step 1: Add `overflow-x: clip` to the full-bleed home panel**

In `resources/css/pages/home.css`, extend the existing rule (~line 252):

```css
    .page-panel.page-panel--home {
        padding-top: 4.375rem;
        /* Horizontal clip lives here (the full-bleed 100vw panel) rather than on
           .home-routes (inside the centered container), so a bike riding off
           clips at the real viewport edge, not the container edge. `clip` not
           `hidden`: hidden would create a scroll container and break the sticky
           media's pinning. */
        overflow-x: clip;
    }
```

- [ ] **Step 2: Remove the container-edge clip from `.home-routes`**

Replace the `.home-routes` overflow rule (~line 294). The old comment + `overflow-x: clip` become a pointer to the new home for the clip:

```css
    /* The horizontal clip that lets a bike ride off-screen now lives on
       .page-panel--home (the full-bleed 100vw wrapper) so it clips at the
       viewport edge, not this centered container's edge. */
```

(Delete the `.home-routes { overflow-x: clip; }` declaration entirely — do not leave an empty rule.)

- [ ] **Step 3: Style the photo layer**

Add near the other `.home-routes` media rules in `home.css` (the opacity crossfade itself comes free from the base component rule; this adds cover/radius/shadow and an optional ken-burns scale at lg+):

```css
    /* Photo behind each section: fills the sticky cell, crossfades via the base
       [data-seq-media] opacity rule. Rounded + shadowed on the photo itself (not
       the frame) so the illustration's edge overlap is never clipped. */
    .home-routes__photo {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 1.75rem;
        box-shadow: 0 28px 64px -22px color-mix(in oklab, var(--color-kidical-ink), transparent 40%);
    }

    @media (min-width: 1024px) {
        /* Subtle ken-burns settle as a photo becomes active (mirrors /help-out). */
        .home-routes__photo {
            transform: scale(1.04);
            transition: opacity 0.8s ease, transform 1.3s ease;
        }

        .home-routes__photo.is-active {
            transform: scale(1);
        }
    }
```

- [ ] **Step 4: Shrink + anchor the illustration, make the ride viewport-relative**

Inside the existing `@media (min-width: 1024px)` block, replace the illustration rules (~lines 359–387). Keep the helper's vertical rise; change size/anchor and switch ride distances to `vw` so the bike enters from the right screen edge and exits past the left:

```css
        /* Half-size illustration parked over the photo's bottom-left, breaking a
           touch past its left edge. Rides in from beyond the right screen edge and
           (when left) bolts past the left screen edge; the panel clips at 100vw so
           it reads as on/off the real screen. Sits above the photo (later in DOM). */
        .home-routes .home-routes__illu {
            inset: auto auto 1rem -1.5rem;   /* bottom-left, slight left overlap */
            width: 52%;
            height: 52%;
            z-index: 1;
            opacity: 1;
            transform: translateX(70vw) rotate(-2deg);
            transition: transform 0.85s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: transform;
        }

        .home-routes .home-routes__illu.is-active {
            transform: translateX(0) rotate(0deg);
            transition: transform 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.28s;
        }

        .home-routes .home-routes__illu.is-past {
            transform: translateX(-120vw) rotate(-2deg);
            transition: transform 0.55s cubic-bezier(0.4, 0, 1, 1);
        }

        /* Block 3's helper is a standing figure, not a bike: it rises from below the
           frame (vertically clipped by the sticky frame's `overflow: visible clip`)
           instead of riding. */
        .home-routes .home-routes__illu[data-seq-media="2"] {
            transform: translateY(120%);
            transition: transform 0.85s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .home-routes .home-routes__illu[data-seq-media="2"].is-active {
            transform: translateY(0);
            transition: transform 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.28s;
        }
```

Note the base `.home-routes .home-routes__illu { width:100%; height:100%; object-fit: contain; }` rule (~line 328) stays as-is for `object-fit`; the lg+ rule above overrides `inset`/`width`/`height`. Leave `object-fit: contain` in place.

- [ ] **Step 5: Extend the reduced-motion fallback for the photo layer**

In the `@media (prefers-reduced-motion: reduce)` block (~line 397), add photo handling alongside the existing illustration fallback (photos keep a plain opacity crossfade, no scale):

```css
        .home-routes__photo {
            transform: none;
            transition: opacity 0.3s ease;
        }

        .home-routes__photo.is-active {
            transform: none;
        }
```

- [ ] **Step 6: Rebuild assets**

Run: `npm run build`
Expected: build succeeds.

- [ ] **Step 7: Verify the CSS architecture test still passes**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS (partials still registered; no raw values leaked into `.blade.php`).

- [ ] **Step 8: Screenshot pass at lg and tune**

Use the project Playwright pattern (`.cjs`, `ignoreHTTPSErrors: true`) against the Herd URL from Task 0 (e.g. `https://home-scroll-photos.test`), viewport 1440×900, scrolling through the three sections. Verify and tune the starting values from Step 4 (`width/height: 52%`, `inset` overlap, `70vw`/`-120vw` travel) until:
  - the illustration reads as ~half the photo, overlapping the photo's bottom-left edge;
  - the bike visibly enters from the right screen edge and exits past the left screen edge with **no white gap** at the container boundary;
  - there is **no horizontal scrollbar** on the page;
  - the sticky photo stays pinned across all three blocks (confirms `overflow: clip` did not break sticky).

Adjust the numeric values in `home.css` as needed, re-run `npm run build`, re-screenshot. (This is presentation tuning, not a behavioural change — no test churn.)

- [ ] **Step 9: Run the full affected test set**

Run: `php artisan test --compact --filter='HomeRoutesMediaTest|CssArchitectureTest'`
Expected: PASS.

- [ ] **Step 10: Format PHP (only the test file is PHP here)**

Run: `vendor/bin/pint --dirty --format agent`
Expected: clean.

- [ ] **Step 11: Commit**

```bash
git add resources/css/pages/home.css
git commit -m "feat(home): half-size illustration over crossfading photo; ride off the viewport edge

- Photo layer (cover, rounded, shadowed) behind each of the 3 sections, crossfading
- Illustration scaled to ~50%, anchored bottom-left, overlapping the photo edge
- Move overflow-x clip from .home-routes (centered container) to .page-panel--home
  (100vw) so bikes ride on/off the real viewport edge, not the container edge
- Viewport-relative (vw) ride distances; reduced-motion keeps photo opacity-only"
```

---

## Self-Review

**Spec coverage:**
- Photo behind each section, crossfading → Task 1 (markup) + Task 2 Step 3 (style; crossfade via base rule). ✓
- Illustration ~50%, bottom-left, overlapping edge → Task 2 Step 4. ✓
- Keep ride-in choreography → Task 2 Step 4 (vw transforms, helper rise retained). ✓
- First photo `is-active`, first illustration not → Task 1 Step 3. ✓
- Clip at viewport not container → Task 2 Steps 1–2. ✓
- `clip` not `hidden`; sticky verified → Task 2 Step 1 + Step 8. ✓
- Mobile unchanged → no mobile rules touched; media column already `display:none` < lg. ✓
- Reduced motion → Task 2 Step 5. ✓
- Reuse existing assets; NL alt; no em-dashes → Task 1 Step 3. ✓
- Test for the three photo paths → Task 1 Steps 1–4. ✓

**Placeholder scan:** none — all steps carry concrete code/commands. Numeric tuning in Step 8 has explicit starting values and acceptance checks, not "TBD".

**Type/name consistency:** classes `home-routes__photo` / `home-routes__illu`, `data-seq-media="0|1|2"`, and `is-active`/`is-past` are used identically across Tasks 1 and 2 and match the existing component's Alpine contract.

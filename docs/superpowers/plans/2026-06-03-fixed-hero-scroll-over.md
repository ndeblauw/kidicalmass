# Fixed Hero + Scroll-Over Panel Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the two divergent interior-page heroes (`.activity-hero`, `.index-hero`) with one shared `<x-page-hero>` component: a brand-blue hero pinned with `position: fixed`, an eyebrow (page name, Nunito-regular yellow), an aspirational white title, a right-side illustration, optional in-hero controls, and a white rounded-top panel that scrolls *over* the pinned hero — all under a floating white nav pill.

**Architecture:** Pure-CSS scroll-over (no JS): the hero is `position: fixed` at the lowest z-layer; a spacer of equal height holds its place in flow; the page body is wrapped in a `.page-panel` (white, rounded top, soft shadow) at a higher z-layer that scrolls up over the hero; the header becomes a `position: fixed` floating pill at the top z-layer. One Blade component owns hero markup + panel wrapper; all positioning/appearance lives in `app.css`.

**Tech Stack:** Laravel 12, Blade components, Tailwind v4 (`@layer` in `resources/css/app.css`), Flux UI, Livewire 4 (kalender), Pest 4. Spec: `docs/superpowers/specs/2026-06-03-fixed-hero-scroll-over-design.md`.

---

## Working notes (read first)

- **Shared working tree:** Nico commits concurrently in this same checkout. Stage only the exact files each task lists. **Never `git add -A`. Never push `main`.**
- **Frontend rules (`CLAUDE.md`):** templates carry structure only (`grid/flex/gap/p/m/max-w/aspect/object`); colour/bg/shadow/rounded/font live in `app.css`. Headings use raw `<h1>`, never `flux:heading`.
- **Bundling:** after CSS/Blade changes, the dev/build step must run for the live site. Ask Frederik to run `npm run dev` (or run `npm run build`) before each visual-verify step.
- **Visual verify:** use the existing helper `scripts/screenshot.cjs` (Herd HTTPS, self-signed certs). The site is `https://kidicalmass.test`. Public pages need no auth.
- **Out of scope:** home page (`.home-hero`) and the activity *detail* poster hero (`.activity-hero` on `activities/show`) — leave both untouched.
- **Colour/font tokens:** `--color-kidical-blue: #1d67cd`, `--color-kidical-yellow: #f9d924`, `--font-sans: 'Nunito Sans'`, `--font-heading: 'Caprasimo','Poppins'`.

---

## Task 1: `<x-page-hero>` component + render test

**Files:**
- Create: `resources/views/components/page-hero.blade.php`
- Test: `tests/Feature/PageHeroComponentTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/PageHeroComponentTest.php`:

```php
<?php

use Illuminate\Support\Facades\Blade;

it('shows the eyebrow, title, illustration, controls and body content', function () {
    $html = Blade::render(<<<'BLADE'
        <x-page-hero
            eyebrow="Kalender"
            title="Spring op de fiets, wij rijden samen."
            illustration="img/illustrations/kid-on-bike.png">
            <x-slot:controls><div class="probe-control">picker</div></x-slot:controls>
            <p class="probe-body">page body</p>
        </x-page-hero>
    BLADE);

    expect($html)
        ->toContain('Kalender')
        ->toContain('Spring op de fiets, wij rijden samen.')
        ->toContain('kid-on-bike.png')
        ->toContain('probe-control')
        ->toContain('probe-body')
        ->toContain('page-hero__spacer')
        ->toContain('page-panel');
});

it('omits the illustration when none is given', function () {
    $html = Blade::render(<<<'BLADE'
        <x-page-hero eyebrow="Meehelpen" title="Jouw handen maken de stoet.">
            <p>body</p>
        </x-page-hero>
    BLADE);

    expect($html)->not->toContain('page-hero__visual');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=PageHeroComponentTest`
Expected: FAIL — component `page-hero` does not exist.

- [ ] **Step 3: Create the component**

Create `resources/views/components/page-hero.blade.php`:

```blade
@props([
    'eyebrow',
    'title',
    'illustration' => null,
])

{{-- Fixed brand-blue hero. Pinned at the lowest z-layer; .page-panel scrolls over it.
     The floating nav pill (site header) sits above this. --}}
<header class="page-hero">
    <div class="page-hero__inner container mx-auto px-4">
        <div class="page-hero__copy">
            <p class="page-hero__eyebrow">{{ $eyebrow }}</p>
            <h1 class="page-hero__title">{{ $title }}</h1>
            @isset($controls)
                <div class="page-hero__controls">{{ $controls }}</div>
            @endisset
        </div>

        @if ($illustration)
            <div class="page-hero__visual">
                <img src="{{ asset($illustration) }}" alt="" aria-hidden="true" class="page-hero__illustration">
            </div>
        @endif
    </div>
</header>

{{-- Holds the hero's place in normal flow (the hero itself is position:fixed). --}}
<div class="page-hero__spacer" aria-hidden="true"></div>

{{-- White rounded-top panel; scrolls up over the pinned hero. --}}
<div class="page-panel">
    <div class="page-panel__inner container mx-auto px-4">
        {{ $slot }}
    </div>
</div>
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=PageHeroComponentTest`
Expected: PASS (2 tests).

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/components/page-hero.blade.php tests/Feature/PageHeroComponentTest.php
git commit -m "feat(hero): x-page-hero component (eyebrow + title + illustration + panel)"
```

---

## Task 2: Hero mechanic CSS (fixed hero, spacer, panel)

**Files:**
- Modify: `resources/css/app.css` (add a new `@layer components` block near the existing `.index-hero` block, ~line 2878)

- [ ] **Step 1: Add the mechanic CSS**

Append this block inside a `@layer components { … }` in `resources/css/app.css`:

```css
@layer components {
    /* ══════════════════════════════════════════════════════════════════════
       Unified interior-page hero: a fixed brand-blue hero that the white
       .page-panel scrolls OVER. See spec 2026-06-03-fixed-hero-scroll-over.
       ══════════════════════════════════════════════════════════════════════ */

    :root {
        --page-hero-h: 26rem;          /* desktop hero height (tune live) */
    }

    .page-hero {
        position: fixed;
        inset: 0 0 auto 0;             /* top:0; left:0; right:0 */
        z-index: 0;                    /* lowest layer; pill and panel sit above */
        height: var(--page-hero-h);
        background-color: var(--color-kidical-blue);
        color: white;
        overflow: hidden;
    }

    .page-hero__inner {
        display: grid;
        grid-template-columns: 1fr;
        align-items: center;
        height: 100%;
        gap: 1.5rem;
        padding-top: 5.5rem;           /* clears the floating pill */
        padding-bottom: 2rem;

        @media (min-width: 768px) {
            grid-template-columns: 1.2fr 0.8fr;
            gap: 3rem;
        }
    }

    .page-hero__eyebrow {
        font-family: var(--font-sans);
        font-weight: 400;              /* Nunito regular, per brief */
        color: var(--color-kidical-yellow);
        font-size: clamp(var(--text-lg), 1.6vw, var(--text-2xl));
        line-height: 1.3;
        margin-bottom: 0.5rem;
        animation: fade-up 0.6s cubic-bezier(0.22, 1, 0.36, 1) 0.05s both;
    }

    .page-hero__title {
        color: white;
        font-size: clamp(var(--text-4xl), 4.5vw, var(--text-7xl));
        line-height: 1.0;
        max-width: 18ch;
        margin: 0;
        animation: hero-h1-in 0.6s cubic-bezier(0.22, 1, 0.36, 1) 0.1s both;
    }

    .page-hero__controls {
        margin-top: 1.75rem;
        animation: fade-up 0.6s cubic-bezier(0.22, 1, 0.36, 1) 0.25s both;
    }

    .page-hero__visual {
        display: none;                 /* hidden on narrow screens, no bottom illo */
        justify-content: center;
        align-items: center;
        height: 100%;

        @media (min-width: 768px) {
            display: flex;
        }
    }

    .page-hero__illustration {
        max-height: 90%;
        max-width: 100%;
        width: auto;
        object-fit: contain;
        animation: hero-photo-in 0.7s cubic-bezier(0.22, 1, 0.36, 1) 0.15s both;
    }

    /* Spacer occupies the fixed hero's height in normal flow. Full-bleed escape
       so it spans the same width regardless of the .container main padding. */
    .page-hero__spacer {
        width: 100vw;
        margin-left: calc(50% - 50vw);
        margin-top: calc(var(--spacing) * -8);   /* cancel main's py-8 top pad */
        height: var(--page-hero-h);
    }

    /* White panel that scrolls over the hero. Full-bleed; rounded top; on top. */
    .page-panel {
        position: relative;
        z-index: 10;
        width: 100vw;
        margin-left: calc(50% - 50vw);
        background-color: white;
        border-radius: 2rem 2rem 0 0;
        box-shadow: 0 -1.5rem 3rem -1rem color-mix(in oklab, var(--color-kidical-ink), transparent 80%);
        min-height: 60vh;              /* guarantees it can cover the hero */
        padding-top: 2.5rem;
        padding-bottom: 3rem;
    }

    @media (max-width: 767px) {
        :root { --page-hero-h: 20rem; }
        .page-hero__inner { padding-top: 5rem; }
    }
}
```

- [ ] **Step 2: Honour reduced motion**

In the existing `@media (prefers-reduced-motion: reduce)` block (search `prefers-reduced-motion`), add:

```css
    .page-hero__eyebrow,
    .page-hero__title,
    .page-hero__controls,
    .page-hero__illustration {
        animation: none;
    }
```

- [ ] **Step 3: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/css/app.css
git commit -m "feat(hero): fixed-hero + scroll-over panel CSS mechanic"
```

> No automated test for raw CSS values; they are verified visually in Task 4.

---

## Task 3: Floating nav pill (header restyle)

**Files:**
- Modify: `resources/views/layouts/site/header.blade.php`
- Modify: `resources/css/app.css`
- Test: `tests/Feature/PublicStructureTest.php` (already asserts nav links render; just keep green)

- [ ] **Step 1: Restructure the header markup**

Replace the opening of `resources/views/layouts/site/header.blade.php` (the `<header …>` and the first wrapping `<div>`s) so the bar is a contained pill. Keep all nav items, the support CTA, and the mobile toggle exactly as they are — only the wrapper classes and Alpine height logic change:

```blade
<header class="site-header" x-data="{ mobileOpen: false }">
    <div class="container mx-auto px-4">
        <div class="site-nav">
            <div class="site-nav__bar flex items-center justify-between gap-4">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('img/logo.png') }}" alt="Kidical Mass" class="site-nav__logo w-auto">
                </a>

                <!-- Main Navigation -->
                <flux:navbar class="hidden md:flex">
                    {{-- …unchanged nav items… --}}
                </flux:navbar>

                <!-- Support CTA + auth (unchanged inner content) -->
                <div class="hidden md:flex items-center gap-3">
                    {{-- …unchanged… --}}
                </div>

                <!-- Mobile Menu Button -->
                <flux:button icon="bars-3" variant="ghost" class="md:hidden" x-on:click="mobileOpen = !mobileOpen" aria-label="Toggle menu" />
            </div>

            <!-- Mobile Navigation (unchanged inner content) -->
            <nav x-show="mobileOpen" x-transition class="md:hidden pb-4 space-y-1">
                {{-- …unchanged… --}}
            </nav>
        </div>
    </div>
</header>
```

> Drop the old `scrolled` Alpine state and the `:class` height toggles — the pill has a single compact height. Keep `mobileOpen`. Do **not** alter the `flux:navbar.item` lines, the support CTA, or the mobile nav contents.

- [ ] **Step 2: Add pill CSS**

Add to `resources/css/app.css` (a `@layer components` block):

```css
@layer components {
    .site-header {
        position: fixed;
        inset: 0 0 auto 0;
        z-index: 60;                   /* above .page-panel (10) and .page-hero (0) */
    }

    .site-nav {
        margin-top: 1rem;
        background-color: white;
        border-radius: 1.25rem;
        box-shadow: 0 0.5rem 1.5rem -0.5rem color-mix(in oklab, var(--color-kidical-ink), transparent 70%);
    }

    .site-nav__bar {
        height: 4.5rem;
        padding-inline: 1.25rem;
    }

    .site-nav__logo {
        height: 3rem;
    }
}
```

- [ ] **Step 3: Run structure tests**

Run: `php artisan test --compact --filter=PublicStructureTest`
Expected: PASS — nav links still render (markup unchanged inside the bar).

- [ ] **Step 4: Visual check + commit**

Ask Frederik to run `npm run dev` (or run `npm run build`). Screenshot the home page top with `scripts/screenshot.cjs` and confirm the pill floats and nav is clickable. Then:

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/layouts/site/header.blade.php resources/css/app.css
git commit -m "feat(nav): floating compact white pill header"
```

---

## Task 4: Migrate Kalender (controls-in-hero, end-to-end verify)

**Files:**
- Modify: `resources/views/livewire/ride-calendar.blade.php`
- Test: existing Livewire test for the calendar (find with `grep -rl RideCalendar tests/`)

- [ ] **Step 1: Swap the hero, keep the picker in the controls slot**

In `resources/views/livewire/ride-calendar.blade.php`, replace the `<section class="index-hero">…</section>` block with the component. Move the `<flux:select>` location picker verbatim into `<x-slot:controls>`:

```blade
<x-page-hero
    eyebrow="Kalender"
    title="Spring op de fiets, wij rijden samen."
    illustration="img/illustrations/kid-on-bike.png">

    <x-slot:controls>
        <div class="kal-herofilter">
            <label class="kal-herofilter__label" for="kal-gemeente">Waar fiets je?</label>
            <flux:select
                id="kal-gemeente"
                variant="listbox"
                searchable
                clearable
                wire:model.live="gemeente"
                placeholder="Alle gemeenten"
                class="kal-herofilter__select"
            >
                <flux:select.option value="">Alle gemeenten</flux:select.option>
                @foreach ($gemeenten as $g)
                    <flux:select.option :value="$g->id">{{ $g->zip ? $g->zip.' – '.$g->name : $g->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </x-slot:controls>

    {{-- …the rest of the calendar body (the existing <div class="kal-body">…</div>)… --}}

</x-page-hero>
```

> The Livewire root must stay a single element. Keep the outer `<div>…</div>` wrapper of the component file; place `<x-page-hero>` inside it. The old `<p class="kal-hero__lead">` line is removed (its job is now the eyebrow + title).

- [ ] **Step 2: Run the calendar Livewire test**

Run: `php artisan test --compact --filter=RideCalendar`
Expected: PASS — the picker still drives `gemeente`; filtering behaviour unchanged. If the test asserted the old lead copy (`Vind een fietstocht bij jou in de buurt.`), update that assertion to `Spring op de fiets, wij rijden samen.`.

- [ ] **Step 3: Visual verify the mechanic on Herd**

Run `npm run dev`/`npm run build`, then screenshot `https://kidicalmass.test/kalender` (or the actual route — confirm with `php artisan route:list --path=kalender`) at desktop and mobile widths with `scripts/screenshot.cjs`. Confirm: blue hero behind the pill, yellow eyebrow, white title, picker in hero, illustration right, and the white panel scrolling over the hero. **Tune `--page-hero-h`, padding, and illustration size in `app.css` here** — this is the reference page for all others.

- [ ] **Step 4: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/livewire/ride-calendar.blade.php resources/css/app.css tests/
git commit -m "feat(kalender): adopt x-page-hero with in-hero location picker"
```

---

## Task 5: Migrate Lokale groepen (stats-in-hero)

**Files:**
- Modify: `resources/views/groups/index.blade.php`
- Test: `tests/Feature/GroupsTest.php`

- [ ] **Step 1: Update the test expectation for the new hero copy**

In `tests/Feature/GroupsTest.php`, the directory test asserts the old lead `'Samen op straat, overal in België.'` (≈ line 113). Replace that assertion with the new aspirational title, keeping the stat-label assertions (`'lokale groepen'`, `'activiteiten dit jaar'`) which remain in the controls slot:

```php
        ->assertSee('Jouw buurt fietst al, rij mee.')
        ->assertSee('lokale groepen')
        ->assertSee('activiteiten dit jaar')
```

- [ ] **Step 2: Run it to verify it fails**

Run: `php artisan test --compact --filter=GroupsTest`
Expected: FAIL — page still renders the old `.index-hero` lead, not the new title.

- [ ] **Step 3: Swap the hero, move stats into the controls slot**

In `resources/views/groups/index.blade.php`, replace the `<section class="index-hero">…</section>` with the component. The movement-context `<p class="grp-hero__body">` paragraph moves into the `.page-panel` body (place it as the first element after the hero, before the directory section). The `<dl class="grp-hero__stats">` moves into `<x-slot:controls>`:

```blade
<x-page-hero
    eyebrow="Lokale groepen"
    title="Jouw buurt fietst al, rij mee."
    illustration="img/illustrations/person-with-boombox.png">

    <x-slot:controls>
        <dl class="grp-hero__stats">
            <div class="grp-hero__stat">
                <dt class="grp-hero__stat-label">lokale {{ $groups->count() === 1 ? 'groep' : 'groepen' }}</dt>
                <dd class="grp-hero__stat-num">{{ $groups->count() }}</dd>
            </div>
            <div class="grp-hero__stat">
                <dt class="grp-hero__stat-label">activiteiten dit jaar</dt>
                <dd class="grp-hero__stat-num">{{ $activityCount }}</dd>
            </div>
        </dl>
    </x-slot:controls>

    <p class="grp-hero__body">
        Kidical Mass is één grote beweging die op vaste momenten samen uitrijdt en het hele jaar door lokaal verschil maakt. In elke gemeente trekt een groep buren de straat op voor veilig fietsen met kinderen.
    </p>

    {{-- …existing directory + CTA sections, unchanged… --}}

</x-page-hero>
```

> `.grp-hero__body` was styled for a blue background (light-on-dark). Since it now sits on the white panel, override its colour: in `app.css` add `.page-panel .grp-hero__body { color: var(--color-text-body); }` (or drop the class and restyle as a normal lead). Verify it reads on white.

- [ ] **Step 4: Run tests to verify pass**

Run: `php artisan test --compact --filter=GroupsTest`
Expected: PASS.

- [ ] **Step 5: Visual verify + commit**

Screenshot the groups index; confirm stats sit in the hero and the body paragraph reads on white. Then:

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/groups/index.blade.php resources/css/app.css tests/Feature/GroupsTest.php
git commit -m "feat(groepen): adopt x-page-hero with in-hero stat counters"
```

---

## Task 6: Migrate the no-control poster pages

**Files (one commit per page):**
- Modify: `resources/views/getting-started.blade.php`
- Modify: `resources/views/volunteer.blade.php`
- Modify: `resources/views/steun-ons.blade.php`
- Test: `tests/Feature/PublicStructureTest.php`

For each page, replace its `<section class="activity-hero …">…</section>` hero block with `<x-page-hero>` (no `controls` slot), wrapping the page's remaining sections as the default slot. Use these exact params:

| File | eyebrow | title | illustration |
|---|---|---|---|
| `getting-started.blade.php` | `Voor het eerst` | `Kom zoals je bent, je eerste rit wordt een feest.` | `img/illustrations/kid-on-scooter.png` |
| `volunteer.blade.php` | `Meehelpen` | `Jouw handen maken de stoet.` | `img/illustrations/kid-waving.png` |
| `steun-ons.blade.php` | `Steun ons` | `Help de beweging groeien.` | `img/illustrations/crocodile-on-tricycle.png` |

- [ ] **Step 1 (per page): Wrap content in the component**

Pattern (getting-started shown; apply the same shape to the other two with their params). Replace the hero `<section>` and wrap the rest of the body:

```blade
<x-layouts::site title="Voor het eerst mee">
    <x-page-hero
        eyebrow="Voor het eerst"
        title="Kom zoals je bent, je eerste rit wordt een feest."
        illustration="img/illustrations/kid-on-scooter.png">

        {{-- the page's existing sections (WAT JE MAG VERWACHTEN, FAQ, CTA, …) go here verbatim --}}

    </x-page-hero>
</x-layouts::site>
```

> Remove the now-duplicated `*-hero__lead` paragraph on each page (its content is replaced by eyebrow + title). Leave every other section unchanged. Some pages have full-bleed bands (yellow CTA, sky promises) using `width:100vw; margin-left:calc(50%-50vw)`; these sit inside `.page-panel` (which is itself 100vw) so the math still resolves to full width — confirm visually in Step 2.

- [ ] **Step 2 (per page): Run structure tests**

Run: `php artisan test --compact --filter=PublicStructureTest`
Expected: PASS — these tests assert on body content (`'Wat je mag verwachten op een rit'`, `'Klaar voor je eerste rit?'`, etc.), which is unchanged. If any test asserted an old hero lead string, update it to the new title.

- [ ] **Step 3 (per page): Visual verify**

Screenshot each page; confirm the full-bleed bands inside `.page-panel` still span edge-to-edge and the scroll-over reads correctly.

- [ ] **Step 4 (per page): Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/<page>.blade.php
git commit -m "feat(<page>): adopt x-page-hero"
```

---

## Task 7: Migrate the About cluster

**Files (one commit per page):**
- Modify: `resources/views/about/index.blade.php`
- Modify: `resources/views/about/mission.blade.php`
- Modify: `resources/views/about/vision.blade.php`
- Modify: `resources/views/about/organisation.blade.php`
- (Check also `about/partners.blade.php`, `about/press.blade.php` — if they use `.activity-hero`/`.index-hero`, migrate them with the same pattern; if their hero differs, leave a note and skip.)
- Test: `tests/Feature/PublicStructureTest.php`

- [ ] **Step 1: Confirm which about pages use the shared hero**

Run: `grep -ln "activity-hero\|index-hero" resources/views/about/*.blade.php`
For each match, apply the Task 6 pattern. Use these params (titles are **drafts** — flag for Frederik to refine):

| File | eyebrow | draft title | illustration |
|---|---|---|---|
| `about/index.blade.php` | `Over ons` | `Samen maken we straten van kinderen.` | `img/illustrations/tree-round.png` |
| `about/mission.blade.php` | `Missie` | `Veilige straten, voor elk kind.` | `img/illustrations/tree-tall.png` |
| `about/vision.blade.php` | `Visie` | `Een stad op kindermaat.` | `img/illustrations/bird-with-helmet.png` |
| `about/organisation.blade.php` | `Organisatie` | `Buren die de straat op trekken.` | `img/illustrations/kid-waving.png` |

- [ ] **Step 2: Apply, test, visually verify each (same loop as Task 6)**

Run after each: `php artisan test --compact --filter=PublicStructureTest` → PASS. Keep the existing body assertions green; update any that referenced an old hero lead.

- [ ] **Step 3: Commit (per page)**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/about/<page>.blade.php
git commit -m "feat(about/<page>): adopt x-page-hero"
```

---

## Task 8: Retire dead hero CSS + full regression

**Files:**
- Modify: `resources/css/app.css`
- Test: whole suite

- [ ] **Step 1: Find remaining references**

Run: `grep -rn "index-hero\|activity-hero" resources/views/`
Expected: only `activities/show` (detail poster — intentionally kept) and any page deliberately skipped in Task 7. Everything else should now use `<x-page-hero>`.

- [ ] **Step 2: Remove now-unused CSS**

In `resources/css/app.css`, delete the `.index-hero`, `.index-hero__inner`, `.index-hero h1`, `.index-hero__daisy` rules and the `.kal-hero__lead`/`.grp-hero__lead` lead rules (their job moved to `.page-hero__eyebrow`/`__title`). **Keep** all `.activity-hero*` rules if `activities/show` still uses them (Step 1 confirms). Keep `.grp-hero__stats`/`__stat*` (still used in the kalender/groepen controls), `.kal-herofilter*`, `.grp-hero__body` (with its white-panel colour override from Task 5).

- [ ] **Step 3: Run the full suite**

Run: `php artisan test --compact`
Expected: PASS (all green).

- [ ] **Step 4: Final visual + reduced-motion sweep**

Screenshot kalender, groepen, voor-het-eerst, meehelpen, steun-ons, about hub at desktop + mobile. Confirm: consistent pinned-hero scroll-over, floating pill legible over both blue and white, no clipped full-bleed bands, illustrations contained. Toggle OS reduce-motion and confirm hero animations are suppressed.

- [ ] **Step 5: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/css/app.css
git commit -m "refactor(hero): retire .index-hero in favour of x-page-hero"
```

---

## Self-review notes

- **Spec coverage:** unified hero (Tasks 1–7), eyebrow=page name in lead treatment (Task 2 `.page-hero__eyebrow`), aspirational title (component + per-page params), blue behind menu (Tasks 2–3 z-layers), floating pill always (Task 3), right illustration from existing set (Tasks 4–7, `public/img/illustrations/`), shorter hero (Task 2 `--page-hero-h`), white rounded panel scrolls over fixed hero (Task 2), controls kept in hero (Tasks 4–5), tests updated (Tasks 4,5,8). Covered.
- **Out-of-scope honoured:** home `.home-hero` and `activities/show` `.activity-hero` untouched (Task 8 Step 1/2 explicitly preserve them).
- **Type/name consistency:** component classes `.page-hero`, `.page-hero__inner/__eyebrow/__title/__controls/__visual/__illustration/__spacer`, `.page-panel`, `.page-panel__inner`, `.site-header`, `.site-nav*`, var `--page-hero-h` are used identically across Tasks 1–8.
- **Known risk to verify live (not assert-able):** full-bleed child bands inside the 100vw `.page-panel`, and the exact `--page-hero-h`/padding — both are explicit visual-tune steps (Tasks 4, 6, 8), not silent assumptions.
- **Open copy:** aspirational titles are drafts; Frederik refines post-build (noted in spec + Tasks 6–7).
```

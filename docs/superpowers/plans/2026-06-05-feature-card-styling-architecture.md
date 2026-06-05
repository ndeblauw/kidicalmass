# Feature-Card & Styling-Architecture Pilot — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Introduce the three-layer styling model (tokens / components / composition) by building one parametrised `<x-feature-card>`, converting the getting-started and mission pages to use it, and amending the CLAUDE.md frontend rules to match.

**Architecture:** Reusable card *appearance* moves out of BEM-in-`app.css` and into a single self-contained Blade component whose look is written as token-backed Tailwind utilities. Page-level *placement* (the getting-started scroll-deck, the mission grid, tilt) stays in the page/CSS. New semantic tokens (`--radius-card`, `--shadow-card`, two blended brand colours) keep the inline utilities consistent.

**Tech Stack:** Laravel 12, Blade components, Livewire Flux v2 (`<flux:icon>`), Tailwind CSS v4 (`@theme` tokens, arbitrary + descendant variants), Pest v4 (`Blade::render` component tests).

---

## Background / key facts (read before starting)

- The card appears today under two BEM names with identical anatomy: `.gs-expect-card` (getting-started scroll-deck, the **larger/canonical** scale) and `.activity-promises__item` (mission + reused elsewhere, smaller). Spec decided: **one component, one scale = the getting-started scale**, no `size` prop.
- `.activity-promises__item` is reused on other pages (steun, chapters, ho-roles) — **do NOT delete it from `app.css`**. Mission simply stops using it; other pages migrate later.
- Tailwind v4 generates `bg-kidical-red` etc. from `--color-kidical-red` in `@theme`. **Class names must appear as literal strings** in the source Tailwind scans, so the component maps the `color` prop with a `match()` returning full literal class strings (never `"bg-kidical-{$color}"` interpolation).
- Flux dynamic icon syntax (verified via docs): `<flux:icon name="clock" variant="solid" />`.
- Component tests use `Blade::render(<<<'BLADE' ... BLADE)` + `expect($html)->toContain(...)` — see `tests/Feature/PageHeroComponentTest.php`.
- After CSS/utility changes, assets must be rebuilt: `npm run build`.
- Shared working tree: this repo is edited concurrently by another dev. **`git add` only the exact files listed in each commit step — never `git add -A`. Do not push.**

## File structure

- **Create** `resources/views/components/feature-card.blade.php` — the component (props `icon`, `title`, `color`; body slot). Owns all card appearance as utilities. No `app.css` entry.
- **Create** `tests/Feature/FeatureCardComponentTest.php` — Pest render tests for the component.
- **Modify** `resources/css/app.css` — add tokens (`@theme`); strip the card-*appearance* rules from `.gs-expect-card` (keep deck layout + tilt); retarget the mission tilt selector.
- **Modify** `resources/views/getting-started.blade.php` — replace the 6 card blocks with `<x-feature-card>`.
- **Modify** `resources/views/about/mission.blade.php` — replace the 3 promise cards with `<x-feature-card>` (proves cross-page reuse + inline link).
- **Modify** `CLAUDE.md` — scope the "Templates hold structure only" rule and add the component carve-out.

---

### Task 1: Add design tokens to `@theme`

**Files:**
- Modify: `resources/css/app.css:39-47` (inside the `@theme { … }` block, after the existing brand colours)

- [ ] **Step 1: Add the radius, shadow, and two blended brand-colour tokens**

In `resources/css/app.css`, find the end of the brand-colour list inside `@theme` (the line `--color-kidical-light-yellow: #FEF3D5;` at ~line 39). Immediately **after** it, add:

```css
    /* Blended chip colours used in the getting-started feature-card rotation,
       promoted to named tokens so <x-feature-card color="violet|coral"> works. */
    --color-kidical-violet: color-mix(in oklab, var(--color-kidical-blue), var(--color-kidical-red) 45%);
    --color-kidical-coral: color-mix(in oklab, var(--color-kidical-orange), var(--color-kidical-red) 40%);

    /* Feature-card surface tokens (canonical = getting-started scale). */
    --radius-card: 2rem;
    --radius-chip: 28%;
    --shadow-card: 0 6px 30px rgb(0 0 0 / 0.1);
```

These generate the utilities `bg-kidical-violet`, `bg-kidical-coral`, `rounded-card`, `rounded-chip`, `shadow-card`.

- [ ] **Step 2: Rebuild assets and confirm the tokens compile**

Run: `npm run build`
Expected: build completes with no CSS errors (exit 0). The new custom properties appear in the generated CSS.

- [ ] **Step 3: Commit**

```bash
git add resources/css/app.css
git commit -m "feat(css): add feature-card surface tokens + blended chip colours

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 2: Build `<x-feature-card>` (TDD)

**Files:**
- Create: `tests/Feature/FeatureCardComponentTest.php`
- Create: `resources/views/components/feature-card.blade.php`

- [ ] **Step 1: Write the failing component test**

Create `tests/Feature/FeatureCardComponentTest.php`:

```php
<?php

use Illuminate\Support\Facades\Blade;

it('renders the chip icon, title and body with the default red chip', function () {
    $html = Blade::render(<<<'BLADE'
        <x-feature-card icon="clock" title="Kort en rustig">
            5 à 7 km op het tempo van het jongste kind.
        </x-feature-card>
    BLADE);

    expect($html)
        ->toContain('Kort en rustig')
        ->toContain('5 à 7 km op het tempo')
        ->toContain('bg-kidical-red')   // default chip colour
        ->toContain('rounded-card')
        ->toContain('shadow-card')
        ->toContain('aria-hidden="true"'); // decorative icon
});

it('maps the color prop to the matching chip background utility', function () {
    $html = Blade::render(
        '<x-feature-card icon="map-pin" color="violet" title="X">body</x-feature-card>'
    );

    expect($html)
        ->toContain('bg-kidical-violet')
        ->not->toContain('bg-kidical-red');
});

it('styles an inline body link as a bold blue card link', function () {
    $html = Blade::render(<<<'BLADE'
        <x-feature-card icon="megaphone" color="red" title="X">
            Lees <a href="/visie">onze visie →</a>
        </x-feature-card>
    BLADE);

    expect($html)
        ->toContain('onze visie')
        ->toContain('[&_a]:text-kidical-blue') // link treatment travels with the card
        ->toContain('[&_a]:font-bold');
});

it('passes extra attributes (e.g. a page layout class) onto the card root', function () {
    $html = Blade::render(
        '<x-feature-card class="gs-expect-card" icon="ticket" title="X">body</x-feature-card>'
    );

    expect($html)->toContain('gs-expect-card');
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter=FeatureCardComponentTest`
Expected: FAIL — the `feature-card` component view does not exist yet (Blade throws "Unable to locate a class or view for component [feature-card]").

- [ ] **Step 3: Create the component**

Create `resources/views/components/feature-card.blade.php`:

```blade
@props([
    'icon',           // Flux (Heroicons) icon name, e.g. "clock"
    'title',          // rendered as the card's <strong>
    'color' => 'red', // chip colour: red | blue | orange | ink | green | violet | coral
])

{{-- Feature card: an icon chip + title + body. The single source of truth for the
     "icon chip card" look used across the site (getting-started deck, about/mission, …).
     Appearance lives here as token-backed utilities — there is no app.css entry.
     Placement (grid vs deck, tilt, scroll behaviour) is owned by the page that uses it.
     `feature-card` is an identity hook only; it carries NO CSS. --}}
@php
    // Literal class strings (NOT interpolated) so Tailwind's scanner generates them.
    $chipBg = match ($color) {
        'blue' => 'bg-kidical-blue',
        'orange' => 'bg-kidical-orange',
        'ink' => 'bg-kidical-ink',
        'green' => 'bg-kidical-green',
        'violet' => 'bg-kidical-violet',
        'coral' => 'bg-kidical-coral',
        default => 'bg-kidical-red',
    };
@endphp

<div {{ $attributes->merge(['class' => 'feature-card flex flex-col gap-[1.125rem] bg-white rounded-card p-10 shadow-card [&_a]:text-kidical-blue [&_a]:font-bold [&_a]:bg-none [&_a:hover]:underline']) }}>
    <div class="flex items-center justify-center shrink-0 size-[4.25rem] -rotate-3 rounded-chip {{ $chipBg }}">
        <flux:icon name="{{ $icon }}" variant="solid" class="size-[2.4rem] text-white" aria-hidden="true" />
    </div>
    <strong class="font-heading text-[1.625rem] font-normal leading-[1.2] text-kidical-ink">{{ $title }}</strong>
    <p class="text-[1.3125rem] leading-[1.6] text-kidical-ink/75">{{ $slot }}</p>
</div>
```

Notes for the implementer:
- The title is a `<strong>` (not a heading element), so the "no inline type on headings" rule does not apply; its type is the component's own appearance and correctly lives here.
- `[&_a]:bg-none` removes the global yellow underline-fill background on links; `[&_a:hover]:underline` gives the plain hover underline — matching the old `.about-card-grid .activity-promises__item a` treatment.

- [ ] **Step 4: Run the test to verify it passes**

Run: `php artisan test --compact --filter=FeatureCardComponentTest`
Expected: PASS (4 passed).

- [ ] **Step 5: Rebuild assets so the new utilities are generated**

Run: `npm run build`
Expected: exit 0. The component's literal utility classes (`bg-kidical-*`, `rounded-card`, `shadow-card`, the `[&_a]…` variants) are now in the compiled CSS.

- [ ] **Step 6: Commit**

```bash
git add resources/views/components/feature-card.blade.php tests/Feature/FeatureCardComponentTest.php
git commit -m "feat(components): add x-feature-card (token-backed, self-contained appearance)

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 3: Convert getting-started to `<x-feature-card>` + strip card-appearance CSS

The getting-started deck keeps its layout/JS; only the card *appearance* moves into the component. The card elements keep `class="gs-expect-card"` so the deck-layout CSS and the scroll JS (which select `.gs-expect-card`) keep working.

**Files:**
- Modify: `resources/views/getting-started.blade.php:30-76` (the six card blocks)
- Modify: `resources/css/app.css:1481-1539` (strip appearance from `.gs-expect-card`; delete `.gs-expect-card__icon` + colour rotation)

- [ ] **Step 1: Replace the six card blocks in the template**

In `resources/views/getting-started.blade.php`, replace the entire `<div class="gs-expect-cards"> … </div>` block (lines 28-78, the six `gs-expect-card` divs) with:

```blade
                <div class="gs-expect-cards">

                    <x-feature-card class="gs-expect-card" icon="clock" color="red" title="Kort en rustig">
                        5 à 7 km op het tempo van het jongste kind, zelden meer dan een uur.
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="musical-note" color="blue" title="Muziek onderweg">
                        Er is altijd een geluidssysteem. Een vrolijke, luidruchtige fietsparade door de buurt.
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="map-pin" color="orange" title="Vaste startplaats">
                        Elke rit vertrekt op een vaste plek, vermeld op de eventpagina. Gewoon daar opdagen.
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="ticket" color="ink" title="Gratis, geen inschrijving">
                        Geen ticket, geen registratie, geen kosten. Kom gewoon naar de start.
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="users" color="violet" title="Alle leeftijden welkom">
                        Vanaf een jaar of 3, op eigen fiets, in een bakfiets of op een kinderzitje.
                    </x-feature-card>

                    <x-feature-card class="gs-expect-card" icon="shield-check" color="coral" title="Minstens vier roze hesjes">
                        Opgeleide begeleiders rijden vooraan en achteraan en houden elke kruising vrij, zodat geen kind achterblijft.
                    </x-feature-card>

                </div>
```

(Icon names and the colour rotation red→blue→orange→ink→violet→coral reproduce the original chip colours exactly. The `data-idx` attributes are dropped — the scroll JS uses array index, not `data-idx`.)

- [ ] **Step 2: Strip the card-appearance rules from `app.css`**

In `resources/css/app.css`, replace the block from `.gs-expect-card {` (line 1481) through the colour-rotation comment+rules ending at line 1539 with this trimmed version — keeping **only** the per-position tilt (placement), deleting bg/radius/padding/strong/p and the entire `.gs-expect-card__icon` + colour rotation (now owned by the component):

```css
    /* Per-position tilt for the static (mobile / reduced-motion) card list.
       Card appearance + icon chip now live in <x-feature-card>; the scroll JS
       overrides this transform on lg+. */
    .gs-expect-card {
        &:nth-child(1) { transform: rotate(-1.5deg); }
        &:nth-child(2) { transform: rotate(1deg);    }
        &:nth-child(3) { transform: rotate(-1.5deg); }
        &:nth-child(4) { transform: rotate(1deg);    }
        &:nth-child(5) { transform: rotate(-1.5deg); }
        &:nth-child(6) { transform: rotate(1deg);    }
    }
```

Leave untouched: `.gs-expect-cards` (1475-1479) and every `.gs-expect-scroll--ready …` rule (1543-1595, including `--ready .gs-expect-card { position: absolute … }`). Those are deck layout, not appearance.

- [ ] **Step 3: Rebuild assets**

Run: `npm run build`
Expected: exit 0.

- [ ] **Step 4: Verify getting-started renders without errors**

Run: `php artisan test --compact --filter=PublicStructureTest`
Expected: PASS — `PublicStructureTest` loads `/nl/getting-started` (dataset at line 12 + `get('/nl/getting-started')` at line 112) and asserts HTTP 200, so a Blade/Flux render error in the new cards would fail it.

- [ ] **Step 5: Visually confirm the deck is unchanged**

Take a screenshot with the project helper or a `/tmp/*.cjs` Playwright script (Herd HTTPS, `ignoreHTTPSErrors: true`), URL `https://kidicalmass.test/nl/getting-started` (resolve the exact path with the `get-absolute-url` tool if unsure). Confirm: six white cards, correct chip colours (red/blue/orange/ink/violet/coral), Caprasimo titles, and the scroll-stacking deck still animates on desktop. getting-started should look visually identical to before.

- [ ] **Step 6: Commit**

```bash
git add resources/views/getting-started.blade.php resources/css/app.css
git commit -m "refactor(getting-started): cards use x-feature-card; deck keeps page-local layout

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 4: Convert mission cards to `<x-feature-card>` (prove cross-page reuse)

Mission's three promise cards become `<x-feature-card>`, demonstrating reuse on a second page and exercising the inline body link. `.activity-promises__item` stays defined in `app.css` (other pages still use it); mission just stops using it. The mission grid (`.about-card-grid`) and tilt stay page-side; the tilt selector is retargeted to the new markup.

**Files:**
- Modify: `resources/views/about/mission.blade.php:28-50` (the `<ul class="about-card-grid">` block)
- Modify: `resources/css/app.css:2366-2374` (retarget tilt; remove the now-dead in-card link rule)

- [ ] **Step 1: Replace the three promise cards in the template**

In `resources/views/about/mission.blade.php`, replace the `<ul class="about-card-grid" role="list"> … </ul>` block (lines 28-50) with — each card wrapped in an `<li>` so the `<ul>` list semantics and the grid stay valid:

```blade
            <ul class="about-card-grid" role="list">
                <li>
                    <x-feature-card icon="rocket-launch" color="red" title="Gemeenschappen helpen starten">
                        Elke Kidical Mass begint met een handvol mensen die iets beters willen voor hun buurt. We helpen nieuwe groepen een lokale fietsparade op te starten, van de eerste vergadering tot de eerste rit.
                    </x-feature-card>
                </li>
                <li>
                    <x-feature-card icon="lifebuoy" color="red" title="Bestaande groepen ondersteunen">
                        Lokale groepen staan er niet alleen voor. We bieden vorming, coördinatiemiddelen, materiaal en nationale zichtbaarheid, zodat elke groep zich kan richten op wat telt: mensen samenbrengen.
                    </x-feature-card>
                </li>
                <li>
                    <x-feature-card icon="megaphone" color="red" title="Pleiten voor kindvriendelijke straten">
                        Vrolijke fietsparades zijn een begin, geen eindpunt. We werken samen met steden en regio's voor veiligere infrastructuur, trager verkeer en straten die kinderen en gezinnen echt verwelkomen. <a href="{{ route('about.vision') }}">Lees onze visie →</a>
                    </x-feature-card>
                </li>
            </ul>
```

Note: the `@push('scripts')` reveal at the bottom of the page targets `.about-band .activity-promises__item` (mission.blade.php:97). Update that selector in the same edit to `.about-band .about-card-grid > li` so the scroll-reveal still animates the cards:

```blade
    <x-about-reveal selector=".about-band .about-card-grid > li" />
```

- [ ] **Step 2: Retarget the mission tilt and drop the dead in-card link rule**

In `resources/css/app.css`, replace lines 2365-2374 (the comment + the `:nth-child(3)` tilt + the `.about-card-grid .activity-promises__item a` block) with tilt that targets the new `<li>` grid items. The link styling is now owned by `<x-feature-card>`, so its rule is deleted here:

```css
    /* About grids run three across; tilt the <li> grid items (cards are x-feature-card). */
    .about-card-grid > li:nth-child(1) { transform: rotate(-1.5deg); }
    .about-card-grid > li:nth-child(2) { transform: rotate(1deg);    }
    .about-card-grid > li:nth-child(3) { transform: rotate(-1deg);   }
```

(The original tilt came from `.activity-promises__item:nth-child(1|2)` at app.css:703-704 plus the about-grid `:nth-child(3)`; this reproduces all three on the new markup.)

- [ ] **Step 3: Rebuild assets**

Run: `npm run build`
Expected: exit 0.

- [ ] **Step 4: Verify mission renders and the existing About test still passes**

Run: `php artisan test --compact --filter=AboutJourneyTest`
Expected: PASS. If `AboutJourneyTest` asserts on `.activity-promises__item` text/markup for the mission page, update those assertions to match the new `<x-feature-card>` output (the visible copy is unchanged, so text assertions should still hold; structural class assertions, if any, switch to `feature-card` / `about-card-grid > li`). Re-run until green.

- [ ] **Step 5: Visually confirm cross-page reuse**

Screenshot `https://kidicalmass.test/nl/about/mission`. Confirm: three cards now at the **canonical (larger) scale**, red chips, the third card's "Lees onze visie →" link rendered **bold blue** with a hover underline, and the gentle tilt preserved.

- [ ] **Step 6: Commit**

```bash
git add resources/views/about/mission.blade.php resources/css/app.css
git commit -m "refactor(mission): promise cards reuse x-feature-card; tilt retargeted

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 5: Amend the CLAUDE.md frontend rules (the carve-out)

**Files:**
- Modify: `CLAUDE.md:403-405` (the "Templates hold structure only" bullet + its Why)

- [ ] **Step 1: Replace the structure-only bullet with the scoped three-layer rule**

In `CLAUDE.md`, replace this bullet:

```markdown
- Templates hold structure only. Keep: `grid`, `flex`, `gap-*`, `p-*`, `m-*`, `max-w-*`, `overflow-*`, `aspect-*`, `object-*`. Strip: `bg-*`, `text-{color}`, `font-*`, `shadow-*`, `rounded-*`, `opacity-*`, `hover:*`, `transition-*`.
  Why: appearance belongs in `app.css`, not templates.
```

with:

```markdown
- Styling has three layers — put each decision in exactly one (test: am I styling a *thing* or *placing* things?):
  - **Tokens** (`@theme` + `@layer base`): colour, type scale, radius, shadow, link/heading defaults. Never a raw hex/px anywhere — use the token.
  - **Components** (`resources/views/components/*.blade.php`): a reusable unit's appearance **and** internal spacing, written as token-backed Tailwind utilities baked into the component markup (e.g. `<x-feature-card>` → `bg-white rounded-card shadow-card p-10`). This is the single source of truth for that unit's look; there is no `app.css` entry for it. Appearance utilities are expected here, but must reference tokens (`bg-kidical-*`, `rounded-card`, `shadow-card`), never raw values.
  - **Composition** (page Blade templates): how units are *placed* — section gaps, margins, grid/flex, alignment, widths, order. Keep: `grid`, `flex`, `gap-*`, `p-*`, `m-*`, `max-w-*`, `overflow-*`, `aspect-*`, `object-*`. Still strip appearance utilities (`bg-*`, `text-{color}`, `font-*`, `shadow-*`, `rounded-*`, …) and BEM layout scaffolding from page templates.
  Why: reusable appearance lives in the component (collision-proof, self-contained); page layout stays freely editable in the template. `app.css` stops growing per-page — new entries only for genuinely global styles (footer, nav, prose) or complex effects no single component owns. Worked example: `<x-feature-card>` (used on getting-started + about/mission). See `docs/superpowers/specs/2026-06-05-styling-architecture-design.md`.
```

(The headings rule directly above — raw `<h1>`–`<h6>`, look from `@layer base` — is unchanged and still applies inside components; a component's *non-heading* title element like a `<strong>` may set its own type as utilities.)

- [ ] **Step 2: Commit**

```bash
git add CLAUDE.md
git commit -m "docs(claude): scope styling rule to three layers (tokens/components/composition)

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 6: Full verification pass

**Files:** none (verification only)

- [ ] **Step 1: Run the affected test suites**

Run: `php artisan test --compact --filter=FeatureCardComponentTest`
Then: `php artisan test --compact --filter=PublicStructureTest`
Then: `php artisan test --compact --filter=AboutJourneyTest`
Expected: all PASS.

- [ ] **Step 2: Lint PHP (component file)**

Run: `vendor/bin/pint --dirty --format agent`
Expected: clean (or auto-fixes applied). If files were changed, `git add` them and amend/commit:

```bash
git add -u resources/ tests/
git commit -m "style: pint

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

(Only `git add` the specific paths shown — never `-A`.)

- [ ] **Step 3: Final production build**

Run: `npm run build`
Expected: exit 0, no warnings about missing classes.

- [ ] **Step 4: Side-by-side visual sign-off**

Screenshot both `…/nl/getting-started` and `…/nl/about/mission`. Confirm: getting-started visually unchanged; mission cards grown to the canonical scale with the bold-blue inline link. This is the human checkpoint before any pipeline status bump.

---

## Self-review notes

- **Spec coverage:** three-layer model → Task 5 (CLAUDE.md) + embodied in Tasks 2-4. Token enrichment → Task 1. `<x-feature-card>` (one component, unified getting-started scale, no `size` prop, `color` free prop, inline blue/bold link via `[&_a]…`) → Task 2. Pilot on getting-started → Task 3. Prove reuse on mission → Task 4. Opportunistic migration / don't delete shared `.activity-promises__item` → honoured in Task 4. Card taxonomy (nav/event cards out of scope) → not touched.
- **No placeholders:** every code block is concrete; icon names, colours, copy, token values, and selectors are all real.
- **Type/name consistency:** component is `feature-card` → `<x-feature-card>` throughout; props `icon`/`title`/`color`; chip classes match the `match()` arms and the Task 1 tokens (`bg-kidical-violet`/`coral`); `gs-expect-card` retained for deck layout in Task 3 matches the JS selector; mission tilt retargeted to `.about-card-grid > li` consistently in template (`<li>` wrappers) and CSS.

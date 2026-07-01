# Start-een-groep Story Slides Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Rework `/nl/chapters/start-een-groep` into three sticky-collage scrollytelling story slides, replace the 6-cell gallery with a wide-crowd-photo + "Er is animo" closing band, and center the intro CTA.

**Architecture:** Reuse the existing `x-scroll-sequence` component (already drives N blocks via per-`data-seq-block` IntersectionObserver) with a single sticky collage column on the right that crossfades through one collage per slide. All visual work is page-local Blade + CSS in `groups/start.blade.php` and `pages/start-een-groep.css`. No new components, no new CSS partials.

**Tech Stack:** Laravel 12, Blade, Tailwind v4 tokens, the project's role-based CSS partials, Pest.

## Global Constraints

- **Tokens only, no raw hex/px** in components and CSS partials — enforced by `tests/Feature/CssArchitectureTest.php`. Use `var(--color-*)`, `var(--radius-*)`, `var(--text-*)`, `var(--spacing)`, etc. (`rem`/`%`/`vh`/`deg`/`fr` values and `clamp()` are allowed; bare pixel and hex literals are not.)
- **NL copy only**; keep existing Dutch strings verbatim. No em-dashes in copy.
- **Headings:** raw `<h1>`–`<h6>`, never `flux:heading`.
- **Styling layers:** appearance utilities/CSS reference tokens; page templates carry composition only. New page CSS goes in `resources/css/pages/start-een-groep.css` (already registered + imported), never `app.css`.
- **Run Pint** (`vendor/bin/pint --dirty --format agent`) before finalizing if any PHP changed.
- **Shared checkout:** stage by explicit path, never `git add -A`. Do not push `main`.

---

### Task 1: Three scrollytelling story slides

Replace the `.sg-deal` (two-column "deal") and `.sg-asks` (honest filter) sections with one `<x-scroll-sequence>` carrying three text blocks and three crossfading collages. Keep the umbrella heading. Slide copy is unchanged.

**Files:**
- Modify: `resources/views/groups/start.blade.php` (replace lines 32–65, the `.sg-deal` + `.sg-asks` sections)
- Modify: `resources/css/pages/start-een-groep.css` (add `.sg-story*` rules; remove the now-unused `.sg-deal*` and `.sg-asks`/`.sg-asks__title` rules — keep `.sg-asks__lead` + `.sg-asks__list*`)
- Test: `tests/Feature/StartGroupEnquiryTest.php` (extend the existing render test)

**Interfaces:**
- Consumes: `x-scroll-sequence` (`media-side`, `active-margin` props; `media` slot; `[data-seq-block]` text blocks; `[data-seq-media]` media items), `x-titled-list-block` (`title`, `variant` = `get|ask`, `level`).
- Produces: section `.sg-story` with `.sg-story__title` and three `.sg-story__collage--{a,b,c}` each holding `.sg-story__photo--lead` + `.sg-story__photo--trail`.

- [ ] **Step 1: Extend the failing render test**

In `tests/Feature/StartGroupEnquiryTest.php`, replace the first test with:

```php
it('renders the start-a-group page with the three story slides, the honest asks and the intent form', function () {
    $this->get(route('groups.start'))
        ->assertOk()
        ->assertSee('Breng Kidical Mass naar jouw buurt')
        ->assertSee('Je hoeft dit niet alleen te dragen')
        ->assertSee('Wat jij brengt')
        ->assertSee('Wat wij dragen')
        ->assertSee('Wat het écht vraagt')
        ->assertSee('sg-story__collage', escape: false)
        ->assertSee('data-seq-block="2"', escape: false)
        ->assertSee('Zin om te beginnen?');
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter="three story slides"`
Expected: FAIL — `sg-story__collage` / `data-seq-block="2"` not found (page still renders `.sg-deal`/`.sg-asks`).

- [ ] **Step 3: Replace the markup**

In `resources/views/groups/start.blade.php`, delete the `{{-- DE DEAL --}}` section and the `{{-- WAT HET ÉCHT VRAAGT --}}` section (lines 32–65) and put this in their place:

```blade
        {{-- DRIE STORY SLIDES — a sticky collage column (right) crossfades through one
             collage per slide as the reader scrolls. Non-alternating: media stays right.
             Slide 1+2 dissolve "te groot een klus"; slide 3 is the honest filter.
             Mobile: the shared component stacks the collages at rest (no swap). --}}
        <section class="sg-story">
            <h2 class="sg-story__title">Je hoeft dit niet alleen te dragen</h2>
            <x-scroll-sequence media-side="right" active-margin="-40% 0px -45% 0px">
                <x-slot:media>
                    <div class="sg-story__collage sg-story__collage--a is-active" data-seq-media="0">
                        <figure class="sg-story__photo sg-story__photo--lead">
                            <img src="{{ asset('img/photography/ride-brussels-two-boys-at-start.webp') }}"
                                 alt="Twee jongens arm in arm met hun fietsen en groene helmen aan de start van een rit in Brussel" loading="lazy">
                        </figure>
                        <figure class="sg-story__photo sg-story__photo--trail">
                            <img src="{{ asset('img/photography/cargo-bike-mother-two-kids-flag.webp') }}"
                                 alt="Een glimlachende vrouw rijdt op een cargobike met twee kinderen en een Kidical Mass vlag" loading="lazy">
                        </figure>
                    </div>
                    <div class="sg-story__collage sg-story__collage--b" data-seq-media="1">
                        <figure class="sg-story__photo sg-story__photo--lead">
                            <img src="{{ asset('img/photography/ride-group-celebration-station.webp') }}"
                                 alt="Tientallen gezinnen in fluohesjes juichen met opgeheven armen voor een sierlijk bakstenen station" loading="lazy">
                        </figure>
                        <figure class="sg-story__photo sg-story__photo--trail">
                            <img src="{{ asset('img/photography/ride-girl-smiling-on-bike.webp') }}"
                                 alt="Een lachend meisje in een roze helm rijdt mee in een groep" loading="lazy">
                        </figure>
                    </div>
                    <div class="sg-story__collage sg-story__collage--c" data-seq-media="2">
                        <figure class="sg-story__photo sg-story__photo--lead">
                            <img src="{{ asset('img/photography/ride-trio-pink-vest-lei-portrait.webp') }}"
                                 alt="Drie vrijwilligers lachen samen tijdens een rit, één met een roze hesje en een bloemenkrans" loading="lazy">
                        </figure>
                        <figure class="sg-story__photo sg-story__photo--trail">
                            <img src="{{ asset('img/photography/ride-brussels-boulevard-crowd.webp') }}"
                                 alt="Een dichte menigte gezinnen met fietsen op een zonnige Brusselse boulevard" loading="lazy">
                        </figure>
                    </div>
                </x-slot:media>

                <div class="scroll-sequence__block" data-seq-block="0">
                    <x-titled-list-block title="Wat jij brengt" variant="ask" level="h3">
                        <li>Een kernteam van twee of drie mensen</li>
                        <li>Kennis van je eigen buurt</li>
                        <li>Een vertrekpunt en een route-idee</li>
                        <li>Energie en goesting</li>
                    </x-titled-list-block>
                </div>

                <div class="scroll-sequence__block" data-seq-block="1">
                    <x-titled-list-block title="Wat wij dragen" variant="get" level="h3">
                        <li>Het merk en al het materiaal, van flyers tot hesjes</li>
                        <li>Opleiding rond veilige begeleiding en routeplanning</li>
                        <li>Nationale zichtbaarheid en communicatie</li>
                        <li>Coaching en een vast aanspreekpunt</li>
                        <li>Contacten met gemeenten, partners en fietsbrigades</li>
                        <li>Subsidieaanvragen voor de hele organisatie</li>
                    </x-titled-list-block>
                </div>

                <div class="scroll-sequence__block" data-seq-block="2">
                    <div class="titled-list-block titled-list-block--ask">
                        <h3 class="titled-list-block__title">Wat het écht vraagt</h3>
                        <p class="sg-asks__lead">Eerlijk is eerlijk: een groep dragen is een engagement over een
                        heel seizoen. Dit verwachten we van een lokale trekker.</p>
                        <ul class="sg-asks__list" role="list">
                            <li>Een paar ritten per jaar mee plannen en begeleiden</li>
                            <li>Eén afgevaardigde naar de vier jaarlijkse Kidical-meetings</li>
                            <li>Je scharen achter ons huishoudelijk reglement rond veiligheid en goede vibes</li>
                            <li>Genoeg begeleiders verzamelen: minstens één roze hesje per tien deelnemers</li>
                        </ul>
                    </div>
                </div>
            </x-scroll-sequence>
        </section>
```

- [ ] **Step 4: Add the `.sg-story` CSS**

In `resources/css/pages/start-een-groep.css`, inside `@layer components { … }`, **replace** the `.sg-deal*` block (the `/* DE DEAL … */` rules) with the `.sg-story` rules below, and **delete** the `.sg-asks` and `.sg-asks__title` rules (keep `.sg-asks__lead`, `.sg-asks__list`, `.sg-asks__list li`, and the `.sg-asks__list li::before` chevron rule — slide 3 still uses them):

```css
    /* DRIE STORY SLIDES — sticky collage column (right) crossfading through one
       collage per slide. Mirrors the help-out .ho-deal collage choreography
       (1:1 stage, percentage-placed rotated photos, tossed→settle on crossfade);
       photos only, no doodle. The shared component owns sticky + opacity fade. */
    .sg-story {
        margin-block: 4rem;
    }

    .sg-story__title {
        margin-bottom: 2rem;
    }

    .sg-story__collage {
        position: relative;
        aspect-ratio: 1 / 1;
        width: 100%;
    }

    /* Each photo is centred on its (x,y) via `translate`, leaving `transform`
       free to animate the settle (rotate + scale) on crossfade. */
    .sg-story__photo {
        position: absolute;
        top: var(--photo-y);
        left: var(--photo-x);
        width: var(--photo-w);
        margin: 0;
        overflow: hidden;
        border-radius: 1.25rem;
        box-shadow: 0 18px 45px -14px color-mix(in oklab, var(--color-kidical-ink), transparent 52%);
        translate: -50% -50%;
        transform: rotate(var(--photo-r));
        transition: transform 0.95s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .sg-story__photo img {
        display: block;
        width: 100%;
        aspect-ratio: 4 / 5;
        object-fit: cover;
        object-position: center 30%;
    }

    /* Three distinct scatters so the beats don't read as the same frame thrice. */
    .sg-story__collage--a .sg-story__photo--lead  { --photo-w: 52%; --photo-x: 31%; --photo-y: 26%; --photo-r: -6deg; z-index: 1; }
    .sg-story__collage--a .sg-story__photo--trail { --photo-w: 48%; --photo-x: 74%; --photo-y: 45%; --photo-r: 6deg;  z-index: 2; }
    .sg-story__collage--b .sg-story__photo--lead  { --photo-w: 50%; --photo-x: 68%; --photo-y: 29%; --photo-r: 5deg;  z-index: 2; }
    .sg-story__collage--b .sg-story__photo--trail { --photo-w: 53%; --photo-x: 36%; --photo-y: 56%; --photo-r: -7deg; z-index: 1; }
    .sg-story__collage--c .sg-story__photo--lead  { --photo-w: 50%; --photo-x: 33%; --photo-y: 30%; --photo-r: 5deg;  z-index: 2; }
    .sg-story__collage--c .sg-story__photo--trail { --photo-w: 50%; --photo-x: 70%; --photo-y: 53%; --photo-r: -6deg; z-index: 1; }

    /* a touch more separation between the stacked collages on mobile */
    .sg-story .scroll-sequence__media-sticky {
        gap: 2.5rem;
    }

    @media (min-width: 1024px) {
        .sg-story .scroll-sequence__media-sticky {
            /* square stage; let the rotated photos spill past its edges */
            height: auto;
            aspect-ratio: 1 / 1;
            overflow: visible;
        }

        /* tossed → settle: the collage that isn't reading sits more rotated +
           scaled up; the active beat settles its photos into place, trail a beat
           after lead. (Opacity fade is the shared component's job.) */
        .sg-story__collage:not(.is-active) .sg-story__photo--lead {
            transform: rotate(calc(var(--photo-r) * 1.7)) scale(1.07);
        }

        .sg-story__collage:not(.is-active) .sg-story__photo--trail {
            transform: rotate(calc(var(--photo-r) * 2)) scale(1.09);
        }

        .sg-story__collage.is-active .sg-story__photo--trail {
            transition-delay: 0.1s;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sg-story__photo,
        .sg-story__collage:not(.is-active) .sg-story__photo,
        .sg-story__collage.is-active .sg-story__photo {
            transform: rotate(var(--photo-r));
            transition: none;
            transition-delay: 0s;
        }
    }
```

- [ ] **Step 5: Run the test to verify it passes**

Run: `php artisan test --compact --filter="three story slides"`
Expected: PASS.

- [ ] **Step 6: Build assets and screenshot-verify the scroll behavior**

Run: `npm run build`
Then: `node scripts/screenshot.cjs /nl/chapters/start-een-groep /tmp/sg-desktop.png desktop` and `node scripts/screenshot.cjs /nl/chapters/start-een-groep /tmp/sg-mobile.png mobile`
Read both screenshots. Expected: the three slides render; on desktop the collage column sits on the right beside the text; on mobile the three collages stack at rest. If the crossfade swap timing feels off when scrolling, tune `active-margin` on the `<x-scroll-sequence>` (larger bottom inset = swaps later) — this is the one value worth iterating.

- [ ] **Step 7: Commit**

```bash
git add resources/views/groups/start.blade.php resources/css/pages/start-een-groep.css tests/Feature/StartGroupEnquiryTest.php
git commit -m "feat(start-een-groep): three scrollytelling story slides"
```

---

### Task 2: "Er is animo" closing band — replaces the gallery

Replace the 6-cell `.sg-proof__gallery` with a two-column band: the wide crowd photo beside the light-blue "Er is animo" card.

**Files:**
- Modify: `resources/views/groups/start.blade.php` (replace the `.sg-proof` section — the `<ul class="sg-proof__gallery">…</ul>`)
- Modify: `resources/css/pages/start-een-groep.css` (rework `.sg-proof*`)
- Test: `tests/Feature/StartGroupEnquiryTest.php`

**Interfaces:**
- Consumes: `x-cta-button` (`href`, `variant`), `$groupCount` (already passed by `GroupController::start`).
- Produces: `.sg-proof` → `.sg-proof__layout` (`.sg-proof__photo` + `.sg-proof__animo-card`).

- [ ] **Step 1: Extend the failing test**

In `tests/Feature/StartGroupEnquiryTest.php`, add a new test below the render test:

```php
it('shows the Er is animo closing band and no longer the photo gallery', function () {
    $this->get(route('groups.start'))
        ->assertOk()
        ->assertSee('Er is animo')
        ->assertSee('ride-park-crowd-cheering-namur.webp', escape: false)
        ->assertDontSee('sg-proof__gallery', escape: false);
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter="Er is animo closing band"`
Expected: FAIL — `sg-proof__gallery` is still present.

- [ ] **Step 3: Replace the gallery markup**

In `resources/views/groups/start.blade.php`, replace the whole `{{-- ER IS ANIMO --}}` `<section class="sg-proof">…</section>` (the `<ul class="sg-proof__gallery">` and all its `<li>` cells) with:

```blade
        {{-- ER IS ANIMO — proof the movement exists: the wide crowd photo beside the
             light-blue animo card. Sits before the FAQ so the visual proof frames the
             practical questions. --}}
        <section class="sg-proof">
            <div class="sg-proof__layout">
                <figure class="sg-proof__photo">
                    <img src="{{ asset('img/photography/ride-park-crowd-cheering-namur.webp') }}"
                         alt="Een grote menigte gezinnen juicht met opgeheven armen op een zonnige verzamelplaats in Namen"
                         loading="lazy">
                </figure>
                <div class="sg-proof__animo-card">
                    <h2>Er is animo</h2>
                    <p>Kidical Mass groeit door heel België. Het netwerk telt intussen
                    {{ $groupCount }} lokale groepen, van grote steden tot kleine gemeenten.
                    Jouw stad kan de volgende zijn.</p>
                    <x-cta-button href="#start" variant="secondary">Ik wil starten</x-cta-button>
                </div>
            </div>
        </section>
```

- [ ] **Step 4: Rework the `.sg-proof` CSS**

In `resources/css/pages/start-een-groep.css`, replace the entire `.sg-proof` block (from `/* ER IS ANIMO … */` through the closing `@media (min-width: 48rem) { .sg-proof__gallery { … } }` and the `@media (prefers-reduced-motion: reduce) { .sg-proof__img … }` rule) with:

```css
    /* ER IS ANIMO — the wide crowd photo beside the light-blue animo card.
       Replaces the editorial gallery; sits before the FAQ. */
    .sg-proof {
        margin-block: 4rem 3rem;
    }

    .sg-proof__layout {
        display: grid;
        gap: calc(var(--spacing) * 3);
        align-items: stretch;
    }

    @media (min-width: 48rem) {
        .sg-proof__layout {
            grid-template-columns: 7fr 5fr;
            gap: calc(var(--spacing) * 4);
        }
    }

    .sg-proof__photo {
        margin: 0;
        overflow: hidden;
        border-radius: calc(var(--radius-card) - 0.25rem);
    }

    .sg-proof__photo img {
        display: block;
        width: 100%;
        height: 100%;
        aspect-ratio: 16 / 9;
        object-fit: cover;
    }

    /* The animo card is now itself the light-blue block. */
    .sg-proof__animo-card {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 1.25rem;
        padding: clamp(1.5rem, 3vw, 2.5rem);
        height: 100%;
        background-color: var(--color-kidical-light-blue);
        border-radius: calc(var(--radius-card) - 0.25rem);
    }
```

- [ ] **Step 5: Run the test to verify it passes**

Run: `php artisan test --compact --filter="Er is animo closing band"`
Expected: PASS.

- [ ] **Step 6: Build and screenshot-verify**

Run: `npm run build` then `node scripts/screenshot.cjs /nl/chapters/start-een-groep /tmp/sg-proof.png desktop`
Read it. Expected: the wide crowd photo sits left, the light-blue "Er is animo" card right (stacked on mobile); the old 6-cell grid is gone.

- [ ] **Step 7: Commit**

```bash
git add resources/views/groups/start.blade.php resources/css/pages/start-een-groep.css tests/Feature/StartGroupEnquiryTest.php
git commit -m "feat(start-een-groep): replace gallery with Er is animo closing band"
```

---

### Task 3: Center the intro CTA + final verification

Collapse the two-column intro grid so the CTA centers below the intro copy instead of being pushed to the far right. Then run the full affected suite, Pint, and a final screenshot.

**Files:**
- Modify: `resources/css/pages/start-een-groep.css` (the `.sg-intro` rules)

**Interfaces:**
- Consumes: existing `.sg-intro` / `.sg-intro__action` markup in `start.blade.php` (unchanged).

- [ ] **Step 1: Rework the `.sg-intro` CSS**

In `resources/css/pages/start-een-groep.css`, replace the `.sg-intro` block (the `.sg-intro { … }` rule **and** the `@media (min-width: 48rem) { .sg-intro { … } }` rule) with:

```css
    /* INTRO — opening copy with the "start" CTA centred below it (no longer a
       right-hand column). The intro-text owns its own vertical rhythm. */
    .sg-intro {
        display: grid;
        gap: 1.5rem;
    }

    .sg-intro__action {
        justify-self: center;
    }
```

- [ ] **Step 2: Build and screenshot-verify the CTA is centered**

Run: `npm run build` then `node scripts/screenshot.cjs /nl/chapters/start-een-groep /tmp/sg-intro.png desktop`
Read it. Expected: the "Ik wil starten" CTA sits centered below the intro paragraph, not pushed right.

- [ ] **Step 3: Run the full affected suite**

Run: `php artisan test --compact --filter=StartGroupEnquiry && php artisan test --compact --filter=CssArchitectureTest && php artisan test --compact --filter=PublicStructure`
Expected: all PASS. (CssArchitectureTest guards against raw hex/px and unregistered partials.)

- [ ] **Step 4: Run Pint (PHP touched in tests)**

Run: `vendor/bin/pint --dirty --format agent`
Expected: clean / auto-fixed.

- [ ] **Step 5: Commit**

```bash
git add resources/css/pages/start-een-groep.css
git commit -m "fix(start-een-groep): center the intro CTA"
```

---

## Notes for the implementer

- The three slide titles must read consistently: slides 1–2 use `x-titled-list-block` (`.titled-list-block__title`); slide 3 reuses the same `.titled-list-block__title` class on a raw `<h3>` so all three match. Do not reintroduce `.sg-asks__title`.
- `active-margin="-40% 0px -45% 0px"` is a starting value. If the collage swaps too early/late while scrolling, increase the bottom inset to swap later. This is the only value worth iterating; everything else mirrors the proven `.ho-deal` setup.
- After Frederik's own critique + refine pass, bump the `start-een-groep` row (Wire/UI) in the page registry via `/pipeline` and log it. Claude's render check tops out at 🟠.
- At `/wrap`, squash these three commits into one curated commit (guard against Nico's commits first).

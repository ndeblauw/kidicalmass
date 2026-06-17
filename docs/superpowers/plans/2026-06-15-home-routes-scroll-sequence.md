# Home routes → scroll-sequence Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the home's 3-card `home-routes` dispatcher with a scrollytelling sequence (3 sections: title + longer copy + CTA, one sticky illustration that crossfades on scroll), built on a reusable `<x-scroll-sequence>` component that also replaces the page-specific `.ho-deal` mechanism on `/help-out`.

**Architecture:** A new shared Blade component `<x-scroll-sequence>` owns only the 2-column layout, sticky positioning, and an Alpine + IntersectionObserver crossfade. Media items and text blocks are slotted, so the same component serves the home (bare illustrations on white, title+text+CTA blocks) and `/help-out` (photo-in-frame, titled-list-block). Below `lg` the component falls back to stacked media items (current `ho-deal` mobile behaviour); the home opts into an illustration-per-section mobile variant via its own page CSS. Sticky offsets are CSS custom properties so each page tunes them without duplicate rules.

**Tech Stack:** Laravel 12 Blade components, Alpine.js (already used on `/help-out`), Tailwind v4 + role-based CSS partials, Pest feature tests.

**Spec:** `docs/superpowers/specs/2026-06-15-home-routes-scroll-sequence-design.md`

---

## File Structure

- **Create** `resources/views/components/scroll-sequence.blade.php` — the shared component (layout + Alpine crossfade). Thin; no raw hex/px.
- **Create** `resources/css/components/scroll-sequence.css` — `@layer components`; layout, sticky, crossfade, default (mobile) stacking. Registered in `app.css`.
- **Modify** `resources/css/app.css` — add the `@import` for the new partial.
- **Modify** `resources/views/home.blade.php` — replace the `home-routes` `<section>`; retarget the closing-CTA.
- **Modify** `resources/css/pages/home.css` — home-specific scroll-sequence tweaks (mobile illustration-per-section, hide sticky media < lg).
- **Modify** `resources/views/volunteer.blade.php` — replace the `.ho-deal` section + inline crossfade `@push('scripts')` with `<x-scroll-sequence>`.
- **Modify** `resources/css/pages/help-out.css` — remove `.ho-deal*`; keep the photo-frame look under a help-out-scoped class on the media items.
- **Delete** `resources/views/components/route-card.blade.php` — orphaned after the home rework.
- **Modify** `tests/Feature/PublicStructureTest.php` and any home/help-out test that asserts on `.home-routes` / `route-card` / `.ho-deal` structure.
- **Create** `tests/Feature/HomeRoutesSequenceTest.php` — asserts the home renders the 3 titles, 3 CTAs (correct routes, correct order) and the membership closing-CTA.

---

## Task 1: Shared `<x-scroll-sequence>` component + CSS partial

**Files:**
- Create: `resources/views/components/scroll-sequence.blade.php`
- Create: `resources/css/components/scroll-sequence.css`
- Modify: `resources/css/app.css` (add `@import`)
- Test: `tests/Feature/CssArchitectureTest.php` (existing — must stay green)

- [ ] **Step 1: Create the CSS partial**

Create `resources/css/components/scroll-sequence.css`:

```css
@layer components {
    /* Reusable scrollytelling unit: a scrolling text column beside a sticky media
       column whose items crossfade as each text block reaches the viewport centre.
       Owns layout + sticky + crossfade only; pages style the media items' own look
       (cover photo vs contained illustration) and may override the mobile fallback.
       Tunable sticky offsets via custom properties. */
    .scroll-sequence {
        --scroll-sequence-top: 8rem;       /* sticky offset (clears the fixed nav) */
        --scroll-sequence-gutter: 4rem;     /* breathing room below the sticky frame */
    }

    .scroll-sequence__layout {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2.5rem;
    }

    .scroll-sequence__block + .scroll-sequence__block {
        margin-top: 3rem;
    }

    /* Default (mobile/tablet) media: items flow stacked — mirrors the old ho-deal
       mobile behaviour so /help-out is unchanged below lg. */
    .scroll-sequence__media-sticky {
        display: grid;
        gap: 1rem;
    }

    @media (min-width: 1024px) {
        .scroll-sequence__layout {
            grid-template-columns: 1fr 1fr;
            gap: 4.5rem;
            align-items: start;
        }

        /* media-side modifier: place the media column left or right. */
        .scroll-sequence--media-right .scroll-sequence__media { order: 2; }
        .scroll-sequence--media-right .scroll-sequence__text  { order: 1; }
        .scroll-sequence--media-left  .scroll-sequence__media { order: 1; }
        .scroll-sequence--media-left  .scroll-sequence__text  { order: 2; }

        .scroll-sequence__block {
            min-height: 78vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .scroll-sequence__block + .scroll-sequence__block {
            margin-top: 0;
        }

        .scroll-sequence__media-sticky {
            position: sticky;
            top: var(--scroll-sequence-top);
            display: block;
            height: calc(100vh - var(--scroll-sequence-top) - var(--scroll-sequence-gutter));
        }

        /* Crossfade: items stack and fade. Pages set object-fit / radius / shadow. */
        .scroll-sequence__media-sticky > [data-seq-media] {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 0.8s ease;
        }

        .scroll-sequence__media-sticky > [data-seq-media].is-active {
            opacity: 1;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .scroll-sequence__media-sticky > [data-seq-media] {
            transition: none;
        }
    }
}
```

- [ ] **Step 2: Register the partial in app.css**

In `resources/css/app.css`, add the import alongside the other component partials (after the `titled-list-block.css` line, before the `pages/` block):

```css
@import './components/titled-list-block.css';
@import './components/scroll-sequence.css';
@import './pages/about.css';
```

- [ ] **Step 3: Create the Blade component**

Create `resources/views/components/scroll-sequence.blade.php`:

```blade
@props([
    'mediaSide' => 'right', // right | left — which side the sticky media column sits on (lg+)
])

{{-- Reusable scrollytelling unit. The text column (default slot) scrolls; the media
     column (`media` slot) is sticky on lg+ and crossfades between its items as each
     [data-seq-block] reaches the viewport centre. Layout/sticky/crossfade live in
     resources/css/components/scroll-sequence.css; pages style the media items' own
     look and may override the mobile fallback. Alpine drives the crossfade (the public
     layout ships no global JS, but Alpine is already loaded for other components). --}}
<div
    {{ $attributes->merge(['class' => 'scroll-sequence scroll-sequence--media-'.$mediaSide]) }}
    x-data="{
        setActive(i) {
            this.$refs.media?.querySelectorAll('[data-seq-media]').forEach(el => {
                el.classList.toggle('is-active', Number(el.dataset.seqMedia) === i);
            });
        },
        init() {
            if (! this.$refs.media) return;
            const io = new IntersectionObserver((entries) => {
                entries.forEach(e => {
                    if (e.isIntersecting) this.setActive(Number(e.target.dataset.seqBlock) || 0);
                });
            }, { rootMargin: '-45% 0px -45% 0px', threshold: 0 });
            this.$el.querySelectorAll('[data-seq-block]').forEach(b => io.observe(b));
        }
    }"
>
    <div class="scroll-sequence__layout">
        <div class="scroll-sequence__media" x-ref="media" aria-hidden="true">
            <div class="scroll-sequence__media-sticky">
                {{ $media }}
            </div>
        </div>
        <div class="scroll-sequence__text">
            {{ $slot }}
        </div>
    </div>
</div>
```

- [ ] **Step 4: Run the CSS architecture test**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS (the new partial is imported; the component has no raw hex/px).

- [ ] **Step 5: Run Pint and commit**

Run: `vendor/bin/pint --dirty --format agent`

```bash
git add resources/views/components/scroll-sequence.blade.php resources/css/components/scroll-sequence.css resources/css/app.css
git commit -m "feat(ui): add reusable scroll-sequence component"
```

---

## Task 2: Rebuild the home section + retarget closing-CTA

**Files:**
- Modify: `resources/views/home.blade.php` (the `home-routes` `<section>`, lines ~83-95; the closing-CTA, lines ~100-103)
- Modify: `resources/css/pages/home.css` (home-specific scroll-sequence rules)
- Test: `tests/Feature/HomeRoutesSequenceTest.php` (create)

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/HomeRoutesSequenceTest.php`:

```php
<?php

use function Pest\Laravel\get;

it('renders the three home route sections with titles, copy and CTAs in order', function () {
    $response = get(route('home'));

    $response->assertOk();

    // Titles, in the agreed funnel order.
    $body = $response->getContent();
    $newPos = strpos($body, 'Nieuw hier?');
    $findPos = strpos($body, 'Vind je lokale groep');
    $helpPos = strpos($body, 'Help mee');

    expect($newPos)->not->toBeFalse();
    expect($findPos)->not->toBeFalse();
    expect($helpPos)->not->toBeFalse();
    expect($newPos)->toBeLessThan($findPos);
    expect($findPos)->toBeLessThan($helpPos);

    // Each section routes out to its page.
    $response->assertSee(route('getting-started'), false);
    $response->assertSee(route('groups.index'), false);
    $response->assertSee(route('volunteer'), false);
});

it('points the home closing-CTA at membership, not the groups index', function () {
    $response = get(route('home'));

    $response->assertOk();
    $response->assertSee(route('membership'), false);
    $response->assertSee('Word lid');
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter=HomeRoutesSequenceTest`
Expected: FAIL — order assertion and/or `Word lid` / `route('membership')` not present (closing-CTA still targets groups).

- [ ] **Step 3: Replace the `home-routes` section**

In `resources/views/home.blade.php`, replace the entire `{{-- ③ DISPATCHER … --}}` `<section class="home-routes …">…</section>` block with:

```blade
{{-- ③ DRIE ROUTES — scrollytelling. Each section reads on its own; one sticky
     illustration crossfades to match the section you're reading (see <x-scroll-sequence>).
     Mobile: each section shows its own illustration inline (home.css), no crossfade. --}}
<x-scroll-sequence media-side="right" class="home-routes">
    <x-slot:media>
        <img class="home-routes__illu is-active" data-seq-media="0" src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" loading="lazy">
        <img class="home-routes__illu" data-seq-media="1" src="{{ asset('img/illustrations/longtail-with-kid.svg') }}" alt="" loading="lazy">
        <img class="home-routes__illu" data-seq-media="2" src="{{ asset('img/illustrations/volunteer-with-wrench.svg') }}" alt="" loading="lazy">
    </x-slot:media>

    <div class="scroll-sequence__block" data-seq-block="0">
        <img class="home-routes__block-illu" src="{{ asset('img/illustrations/waving-rider.svg') }}" alt="" aria-hidden="true" loading="lazy">
        <h2 class="text-kidical-ink">Nieuw hier?</h2>
        <p class="text-kidical-ink/70">Nog nooit meegefietst? Geen zorgen. Een Kidical Mass is een rustige, vrolijke fietsparade door je eigen buurt, op kindertempo, met de kruispunten veilig vrijgehouden. Je hoeft niets te kunnen en je hoeft je niet in te schrijven. Gewoon komen en meefietsen.</p>
        <p><x-cta-button :href="route('getting-started')" variant="secondary">Zo werkt een rit</x-cta-button></p>
    </div>

    <div class="scroll-sequence__block" data-seq-block="1">
        <img class="home-routes__block-illu" src="{{ asset('img/illustrations/longtail-with-kid.svg') }}" alt="" aria-hidden="true" loading="lazy">
        <h2 class="text-kidical-ink">Vind je lokale groep</h2>
        <p class="text-kidical-ink/70">Kidical Mass is geen organisatie ver weg, maar de mensen in jouw buurt. Overal in Vlaanderen en Brussel plannen lokale groepen hun eigen ritten. Vind de groep bij jou, en je weet meteen wanneer de volgende rit vertrekt en wie erachter zit.</p>
        <p><x-cta-button :href="route('groups.index')" variant="secondary">Vind je groep</x-cta-button></p>
    </div>

    <div class="scroll-sequence__block" data-seq-block="2">
        <img class="home-routes__block-illu" src="{{ asset('img/illustrations/volunteer-with-wrench.svg') }}" alt="" aria-hidden="true" loading="lazy">
        <h2 class="text-kidical-ink">Help mee</h2>
        <p class="text-kidical-ink/70">Een rit ontstaat niet vanzelf. Achter elke parade staan ouders en buren die de route uittekenen, de boel aankondigen en in een roze hesje meefietsen. Een paar uur per maand, en je krijgt er een warme bende vrienden voor terug.</p>
        <p><x-cta-button :href="route('volunteer')" variant="secondary">Word vrijwilliger</x-cta-button></p>
    </div>
</x-scroll-sequence>
```

> Note: the per-block `home-routes__block-illu` images are the mobile-only inline illustrations; the `home-routes__illu` images in the media slot are the desktop sticky/crossfade set. `home.css` (next step) shows one set per breakpoint.

- [ ] **Step 4: Retarget the closing-CTA**

In `resources/views/home.blade.php`, change the `<x-slot:closing>` block from the groups target to membership:

```blade
<x-slot:closing>
    <x-closing-cta heading="Geef de straat terug aan kinderen"
        :href="route('membership')" label="Word lid" icon="heart" />
</x-slot:closing>
```

- [ ] **Step 5: Add the home-specific scroll-sequence CSS**

In `resources/css/pages/home.css`, inside the existing `@layer components { … }`, add (near the end, after the existing `.home-routes`-related rules if any remain — otherwise append):

```css
    /* ─── Home — drie routes (scroll-sequence) ───
       Desktop: bare illustrations crossfade in the sticky frame, object-contain on
       white (no frame/shadow). Mobile: hide the sticky media column; each section
       shows its own inline illustration above the copy. */
    .home-routes .home-routes__illu {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    /* Mobile-only inline illustration per section. */
    .home-routes__block-illu {
        display: block;
        width: 8rem;
        height: auto;
        margin-bottom: 1.25rem;
    }

    @media (min-width: 1024px) {
        /* On desktop the sticky media set carries the illustrations; hide the inline ones. */
        .home-routes__block-illu {
            display: none;
        }
    }

    @media (max-width: 63.99rem) {
        /* Below lg the sticky media column is redundant (inline illus do the work). */
        .home-routes .scroll-sequence__media {
            display: none;
        }
    }
```

- [ ] **Step 6: Run the test to verify it passes**

Run: `php artisan test --compact --filter=HomeRoutesSequenceTest`
Expected: PASS.

- [ ] **Step 7: Run Pint and commit**

Run: `vendor/bin/pint --dirty --format agent`

```bash
git add resources/views/home.blade.php resources/css/pages/home.css tests/Feature/HomeRoutesSequenceTest.php
git commit -m "feat(home): rebuild routes as scroll-sequence; closing-CTA → membership"
```

---

## Task 3: Port `/help-out` onto the shared component

**Files:**
- Modify: `resources/views/volunteer.blade.php` (the `.ho-deal` `<section>` lines ~90-128 and its `@push('scripts')` block lines ~130-151)
- Modify: `resources/css/pages/help-out.css` (remove `.ho-deal*`, add a help-out-scoped photo-frame look)
- Test: `tests/Feature/PublicStructureTest.php` and any help-out test (existing — must stay green)

- [ ] **Step 1: Run the existing help-out coverage to capture the baseline**

Run: `php artisan test --compact --filter=PublicStructure`
Expected: PASS (record current green state before refactor).

- [ ] **Step 2: Replace the `.ho-deal` section with `<x-scroll-sequence>`**

In `resources/views/volunteer.blade.php`, replace the entire `{{-- WAT MEEDOEN INHOUDT … --}}` `<section class="ho-deal">…</section>` block with:

```blade
{{-- WAT MEEDOEN INHOUDT — scroll-sequence (gedeelde component). De foto rechts
     crossfade't naar het blok dat je leest. Mobiel: beide foto's gestapeld, geen swap. --}}
<section class="ho-deal">
    <div class="container mx-auto px-4">
        <x-scroll-sequence media-side="right">
            <x-slot:media>
                <img class="ho-deal__photo is-active" data-seq-media="0" src="{{ asset('img/photography/volunteers/volunteers-pink-vests-with-flag.jpg') }}" alt="Een warme bende vrijwilligers in hesjes zwaait blij met de Kidical Mass-vlag" loading="lazy">
                <img class="ho-deal__photo" data-seq-media="1" src="{{ asset('img/photography/volunteers/volunteer-selfie-stop-sign.jpg') }}" alt="Vrijwilliger in roze hesje houdt met een stopbord een kruispunt vrij" loading="lazy">
            </x-slot:media>

            <div class="scroll-sequence__block" data-seq-block="0">
                <x-titled-list-block title="Wat je krijgt" variant="get" level="h2">
                    <li>Kidical Mass-materiaal en steun vanaf dag één</li>
                    <li>Opleiding rond veiligheid en routeplanning, als je dat wil</li>
                    <li>Vier gezellige vrijwilligersmomenten per jaar, met lekker eten</li>
                    <li>Een warme bende ouders en fietsers die echte vrienden worden</li>
                </x-titled-list-block>
            </div>

            <div class="scroll-sequence__block" data-seq-block="1">
                <x-titled-list-block title="Wat we vragen" variant="ask" level="h2">
                    <li>Kom met goesting en een vrolijke, respectvolle houding</li>
                    <li>Onderschrijf onze afspraken rond vriendelijkheid en veiligheid</li>
                    <li>Maak je deel uit van een lokaal team? Stuur één afgevaardigde naar het jaarlijkse meetup-moment</li>
                </x-titled-list-block>
            </div>
        </x-scroll-sequence>
    </div>
</section>
```

- [ ] **Step 3: Delete the now-dead crossfade `@push('scripts')`**

In `resources/views/volunteer.blade.php`, delete the first `@push('scripts') … @endpush` block (the `IntersectionObserver` that toggled `.ho-deal__img.is-active`, lines ~130-151). Leave the second `@push('scripts')` (the role-card scroll reveal) intact.

- [ ] **Step 4: Rework the help-out CSS**

In `resources/css/pages/help-out.css`, delete every `.ho-deal*` rule from Task 1's listing (the `.ho-deal`, `.ho-deal__layout`, `.ho-deal__block`, `.ho-deal__frame`, `.ho-deal__img`, `.ho-deal__media-sticky` rules and their media-query variants). Replace them with the section padding + the photo's own look (the shared component owns layout/sticky/crossfade; this is just the photo frame):

```css
    .ho-deal {
        padding-block: clamp(4.5rem, 9vw, 7.5rem);
    }

    /* Help-out media: real photos in a rounded frame. Mobile = 4:5 card with shadow;
       lg+ = fill the sticky frame edge-to-edge (the shared component stacks + crossfades). */
    .ho-deal__photo {
        width: 100%;
        aspect-ratio: 4 / 5;
        object-fit: cover;
        object-position: center 30%;
        display: block;
        border-radius: 1.5rem;
        box-shadow: 0 18px 44px -18px color-mix(in oklab, var(--color-kidical-ink), transparent 45%);
    }

    .ho-deal .scroll-sequence {
        --scroll-sequence-top: 10.5rem;
        --scroll-sequence-gutter: 10.5rem;
    }

    @media (min-width: 1024px) {
        .ho-deal .scroll-sequence__media-sticky {
            border-radius: 1.75rem;
            overflow: hidden;
            box-shadow: 0 28px 64px -22px color-mix(in oklab, var(--color-kidical-ink), transparent 40%);
        }

        .ho-deal__photo {
            aspect-ratio: auto;
            border-radius: 0;
            box-shadow: none;
            transform: scale(1.05);
            transition: opacity 0.8s ease, transform 1.3s ease;
        }

        .ho-deal__photo.is-active {
            transform: scale(1);
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .ho-deal__photo {
            transform: none;
            transition: none;
        }
        .ho-deal__photo.is-active {
            transform: none;
        }
    }
```

- [ ] **Step 5: Run the help-out coverage**

Run: `php artisan test --compact --filter=PublicStructure`
Expected: PASS — the page still renders "Wat je krijgt"/"Wat we vragen" and both photos. If a selector asserted on `.ho-deal__img` / `.ho-deal__block`, update it to the new structure (`.ho-deal__photo` / `.scroll-sequence__block`).

- [ ] **Step 6: Run Pint and commit**

Run: `vendor/bin/pint --dirty --format agent`

```bash
git add resources/views/volunteer.blade.php resources/css/pages/help-out.css tests/Feature/PublicStructureTest.php
git commit -m "refactor(help-out): port ho-deal onto shared scroll-sequence"
```

---

## Task 4: Remove the orphaned `route-card` + full verification

**Files:**
- Delete: `resources/views/components/route-card.blade.php`
- Test: full public suite

- [ ] **Step 1: Confirm `route-card` is unused**

Run: `grep -rn "route-card" resources/views`
Expected: no matches (the home no longer uses it). If any remain, stop and resolve.

- [ ] **Step 2: Delete the component**

```bash
git rm resources/views/components/route-card.blade.php
```

- [ ] **Step 3: Run the full public test suite**

Run: `php artisan test --compact --filter="Home|Public|HelpOut|CssArchitecture"`
Expected: PASS across home, public structure, help-out and CSS architecture tests.

- [ ] **Step 4: Build assets and verify visually (one screenshot pass)**

Run: `npm run build`
Then capture home + `/help-out` at desktop and mobile widths with the project screenshot helper (`scripts/screenshot.cjs` if present, else a `/tmp/*.cjs` Playwright script per the global Playwright rules). Confirm: desktop crossfade swaps the illustration/photo as you scroll each section; mobile stacks (home shows one inline illustration per section, help-out shows both photos), no swap.

- [ ] **Step 5: Commit**

```bash
git add -- resources/views/components/route-card.blade.php
git commit -m "chore: drop orphaned route-card component"
```

---

## Task 5: Update the build pipeline

- [ ] **Step 1:** Run `/pipeline` for the home page row (P-nn) in `docs/wiki/design/30-skeleton/00-page-registry.md`. Keep Wire 🟠 (awaits Frederik's own critique+refine pass). Note in Top gaps that the routes section is now a shared scroll-sequence; note `/help-out` mechanism is now shared. Append a `## [2026-06-15] build | …` entry to `docs/wiki/log.md`.

---

## Self-Review

**Spec coverage:**
- Decisions 1-4 (CTA per section, bare illustration on white, shared component + help-out port, funnel order + membership closing-CTA) → Tasks 1-3. ✓
- Reusable `<x-scroll-sequence>` with `media-side` prop + slotted media/blocks → Task 1. ✓
- Responsive (lg+ sticky crossfade; mobile stack; home inline-per-section) → Task 1 (default) + Task 2 (home override). ✓
- `/help-out` port, `.ho-deal` removal, photo-frame preserved as media-item look → Task 3. ✓
- Cleanups: route-card delete (Task 4), signposts removed (Task 2, part of the section replacement), `.ho-deal` CSS removed (Task 3). ✓
- Tests: CssArchitectureTest (Task 1), HomeRoutesSequenceTest (Task 2), PublicStructure (Task 3), full suite (Task 4). ✓
- Build pipeline (Task 5). ✓

**Placeholder scan:** No TBD/TODO; all code shown; P-nn resolved via `/pipeline` which looks up the row.

**Type/name consistency:** `data-seq-media` / `data-seq-block` / `.is-active` / `.scroll-sequence__media` / `.scroll-sequence__media-sticky` / `.scroll-sequence__block` used identically in the component (Task 1), home (Task 2) and help-out (Task 3). `media-side` prop matches `.scroll-sequence--media-{right,left}` modifier. CSS custom properties `--scroll-sequence-top` / `--scroll-sequence-gutter` defined in the partial and overridden in help-out.css consistently.

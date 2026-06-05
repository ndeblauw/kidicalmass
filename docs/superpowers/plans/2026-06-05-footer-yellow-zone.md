# Footer Yellow Zone Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Turn the page bottom into one continuous yellow field — a page-owned closing CTA, a partner card, the footerbunch illustration, and an inset rounded-top dark footer — wired on all 17 content pages.

**Architecture:** A new `<x-closing-cta>` component is the page-owned closing block, slotted by the layout (`<x-slot:closing>`) directly above the footer. The footer becomes a yellow zone (`bg-kidical-yellow`) containing the relocated partner card, the illustration, and the dark footer restyled as an inset rounded-top card. Same `kidical-yellow` token on the page's closing block and the zone, zero gap → one seamless field.

**Tech Stack:** Laravel 12 Blade (anonymous components + named slots), Tailwind CSS v4 (token-backed utilities + `@theme` tokens in `app.css`), Pest 4 feature tests.

**Spec:** [`docs/superpowers/specs/2026-06-05-footer-yellow-zone-design.md`](../specs/2026-06-05-footer-yellow-zone-design.md)

---

## File Structure

- **Create** `resources/views/components/closing-cta.blade.php` — the closing CTA (heading + one button on yellow).
- **Create** `tests/Feature/ClosingCtaTest.php` — component unit test + per-page wiring smoke.
- **Create** `tests/Feature/FooterZoneTest.php` — zone structure, partner relocation, about-cta removal.
- **Modify** `resources/views/layouts/site.blade.php` — render `{{ $closing }}`, drop the standalone `<x-partners />`.
- **Modify** `resources/views/layouts/site/footer.blade.php` — yellow zone wrapper + partner card + illustration + inset dark footer.
- **Modify** `resources/css/app.css` — `.site-footer` inset/rounded, partner card restyle, remove about-cta container styles (keep `.about-cta__btn*`).
- **Delete** `resources/views/components/about-cta.blade.php`.
- **Modify** 17 page views — add `<x-slot:closing>`; the 4 about pages also drop `<x-about-cta>`.

**Per-page CTA copy** (final for this plan; Frederik may refine later):

| Page view | heading | href route | label | icon |
|---|---|---|---|---|
| `home.blade.php` | Klaar voor je eerste rit? | `activities.index` | Vind een rit | arrow |
| `activities/index.blade.php` | Zelf een rit in je buurt? | `getting-started` | Zo begin je | arrow |
| `activities/show.blade.php` | Nog niet zeker hoe het werkt? | `getting-started` | Lees hoe je meerijdt | arrow |
| `groups/index.blade.php` | Geen groep in je buurt? | `getting-started` | Zo begin je | arrow |
| `groups/show.blade.php` | Rij mee in je buurt | `membership` | Word lid | heart |
| `getting-started.blade.php` | Klaar om mee te rijden? | `activities.index` | Vind een rit | arrow |
| `find-a-bike.blade.php` | Toch nog een vraag? | `contact` | Neem contact op | arrow |
| `volunteer.blade.php` | Geef de straat terug aan kinderen | `membership` | Word lid | heart |
| `steun-ons.blade.php` | Zin gekregen om mee te rijden? | `activities.index` | Vind een rit | arrow |
| `articles/index.blade.php` | Zin gekregen om mee te rijden? | `activities.index` | Vind een rit | arrow |
| `articles/show.blade.php` | Zin gekregen om mee te rijden? | `activities.index` | Vind een rit | arrow |
| `about/index.blade.php` | Rij mee met de buurt | `activities.index` | Vind een rit | arrow |
| `about/mission.blade.php` | Samen maken we straten veiliger | `activities.index` | Vind een rit | arrow |
| `about/vision.blade.php` | Geloof je hierin? | `membership` | Word lid | heart |
| `about/organisation.blade.php` | Een afdeling starten of vervoegen? | `getting-started` | Zo begin je | arrow |
| `about/partners.blade.php` | Samen op pad? | `contact` | Neem contact op | arrow |
| `about/press.blade.php` | Vragen van de pers? | `contact` | Neem contact op | arrow |

---

## Task 1: closing-cta component

**Files:**
- Create: `resources/views/components/closing-cta.blade.php`
- Test: `tests/Feature/ClosingCtaTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/ClosingCtaTest.php`:

```php
<?php

use Illuminate\Support\Facades\Blade;

it('renders a closing CTA heading and button on a yellow band', function () {
    $html = Blade::render(
        '<x-closing-cta heading="Klaar voor je eerste rit?" href="/events" label="Vind een rit" />'
    );

    expect($html)
        ->toContain('Klaar voor je eerste rit?')
        ->toContain('Vind een rit')
        ->toContain('bg-kidical-yellow')
        ->toContain('href="/events"');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter='renders a closing CTA heading'`
Expected: FAIL — `x-closing-cta` component view not found.

- [ ] **Step 3: Write the component**

Create `resources/views/components/closing-cta.blade.php`:

```blade
@props([
    'heading',
    'href',
    'label',
    'icon' => 'arrow', // arrow | heart (heart for membership/support targets)
])

{{-- Page-owned closing block: big heading + one button on a full-bleed yellow band.
     Rendered by the layout's `closing` slot, directly above the footer zone. The
     shared kidical-yellow token + zero gap fuses it with the footer's yellow field.
     Raw <h2> so it inherits the @layer base heading scale (never size headings inline). --}}
<section {{ $attributes->merge(['class' => 'bg-kidical-yellow']) }}>
    <div class="container mx-auto px-4 py-20 flex flex-col items-center gap-7 text-center">
        <h2 class="max-w-3xl">{{ $heading }}</h2>

        <x-cta-button :href="$href" :icon="$icon" variant="blue">{{ $label }}</x-cta-button>
    </div>
</section>
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter='renders a closing CTA heading'`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add resources/views/components/closing-cta.blade.php tests/Feature/ClosingCtaTest.php
git commit -m "feat(footer): add closing-cta component"
```

---

## Task 2: Footer zone — relocate partners, add illustration, render closing slot

This is one atomic refactor: partners moves from the layout into the footer zone, the layout gains the `closing` slot, and the illustration is added. Doing it in one commit keeps the partner tests green (partners is never absent).

**Files:**
- Modify: `resources/views/layouts/site/footer.blade.php`
- Modify: `resources/views/layouts/site.blade.php`
- Test: `tests/Feature/FooterZoneTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/FooterZoneTest.php`:

```php
<?php

use function Pest\Laravel\get;

it('wraps the page bottom in a yellow footer zone', function () {
    get(route('home'))
        ->assertOk()
        ->assertSee('site-footer-zone', escape: false)
        ->assertSee('footerbunch-yellow.png', escape: false);
});

it('keeps the partner card inside the footer zone on showcase routes', function () {
    // partner-strip renders only on showcase routes; it must still appear once relocated.
    get(route('home'))
        ->assertOk()
        ->assertSee('partner-strip', escape: false);
});

it('still renders the dark footer bottom bar site-wide', function () {
    get(route('activities.index'))
        ->assertOk()
        ->assertSee('Kidical Mass Belgium', escape: false);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=FooterZoneTest`
Expected: FAIL — `site-footer-zone` / `footerbunch-yellow.png` not present yet.

- [ ] **Step 3: Wrap the footer in the yellow zone**

In `resources/views/layouts/site/footer.blade.php`, wrap the existing `<footer class="site-footer">…</footer>` so the file becomes:

```blade
{{-- Yellow footer zone: one continuous kidical-yellow field holding the partner
     card, the footerbunch illustration, and the inset dark footer card. The page's
     closing CTA (rendered by the layout just above this) shares the same yellow. --}}
<div class="site-footer-zone bg-kidical-yellow">

    {{-- Partner recognition — a white card floating on the yellow (showcase routes only) --}}
    <x-partners />

    {{-- Footerbunch illustration, on the yellow, leading into the dark card --}}
    <figure class="mx-auto max-w-5xl px-4 -mb-px" aria-hidden="true">
        <img src="{{ asset('img/illustrations/footerbunch-yellow.png') }}" alt="" class="block w-full">
    </figure>

    {{-- Dark footer card — inset + rounded-top (styling in .site-footer) --}}
    <footer class="site-footer">
        <div class="container mx-auto px-4">

            <div class="site-footer__main">

                {{-- Brand + persistent membership CTA --}}
                <div>
                    <img src="{{ asset('img/logos/footer-logo.avif') }}" alt="Kidical Mass Belgium" class="site-footer__logo">
                    <p class="site-footer__tagline">{{ __('footer.tagline') }}</p>
                    <x-cta-button :href="route('membership')" icon="heart" size="sm" class="mt-5">{{ __('footer.membership_cta') }}</x-cta-button>
                </div>

                {{-- Discover — mirrors the main nav --}}
                <div>
                    <h3 class="site-footer__col-title">{{ __('footer.discover') }}</h3>
                    <ul class="site-footer__links">
                        <li><a href="{{ route('activities.index') }}">{{ __('nav.events') }}</a></li>
                        <li><a href="{{ route('groups.index') }}">{{ __('nav.chapters') }}</a></li>
                        <li><a href="{{ route('getting-started') }}">{{ __('nav.getting_started') }}</a></li>
                        <li><a href="{{ route('volunteer') }}">{{ __('nav.help_out') }}</a></li>
                    </ul>
                </div>

                {{-- About — mirrors the About dropdown --}}
                <div>
                    <h3 class="site-footer__col-title">{{ __('footer.about') }}</h3>
                    <ul class="site-footer__links">
                        <li><a href="{{ route('about.mission') }}">{{ __('nav.mission') }}</a></li>
                        <li><a href="{{ route('about.vision') }}">{{ __('nav.vision') }}</a></li>
                        <li><a href="{{ route('about.organisation') }}">{{ __('nav.organisation') }}</a></li>
                        <li><a href="{{ route('articles.index') }}">{{ __('nav.news') }}</a></li>
                        <li><a href="{{ route('about.press') }}">{{ __('nav.press') }}</a></li>
                        <li><a href="{{ route('about.partners') }}">{{ __('nav.partners') }}</a></li>
                    </ul>
                </div>

                {{-- Follow --}}
                <div>
                    <h3 class="site-footer__col-title">{{ __('footer.follow_us') }}</h3>
                    <div class="site-footer__social">
                        <a href="https://www.instagram.com/kidicalmass.belgium/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="site-footer__social-link">
                            <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="https://www.facebook.com/Kidicalmass.brussels" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="site-footer__social-link">
                            <svg fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                        </a>
                    </div>
                </div>

            </div>

            {{-- Funder acknowledgment — quiet, site-wide --}}
            <div class="site-footer__funder">
                <span>{{ __('partners.funder_credit') }}</span>
                <img src="{{ asset('img/sponsors/bm-nl.avif') }}" alt="Mede mogelijk gemaakt door Brussel Mobiliteit" class="site-footer__funder-logo">
            </div>

            {{-- Bottom bar — utilities --}}
            <div class="site-footer__bottom">
                <span>&copy; {{ date('Y') }} Kidical Mass Belgium</span>
                <span>{{ __('footer.website_by') }} <a href="https://bluepundit.eu/" target="_blank" rel="noopener noreferrer">Blue Pundit</a> &amp; <a href="https://frederikvincx.com/" target="_blank" rel="noopener noreferrer">Impact Studio</a></span>
                <ul class="site-footer__bottom-links">
                    <li><a href="{{ route('contact') }}">{{ __('common.contact') }}</a></li>
                    <li><a href="{{ route('privacy') }}">{{ __('common.privacy_cookies') }}</a></li>
                    <li><a href="{{ route('login') }}">{{ __('nav.login') }}</a></li>
                </ul>
            </div>

        </div>
    </footer>
</div>
```

- [ ] **Step 4: Update the layout to render the closing slot and drop the standalone partners**

In `resources/views/layouts/site.blade.php`, replace this block:

```blade
    {{-- Partner recognition strip (PAT-5, slim). Full story on /about/partners. --}}
    <x-partners />

    <x-layouts::site.footer />
```

with:

```blade
    {{-- Page-owned closing block (e.g. <x-closing-cta>), rendered full-width directly
         above the footer zone. The page paints it yellow so it fuses with the zone. --}}
    @isset($closing)
        {{ $closing }}
    @endisset

    <x-layouts::site.footer />
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --compact --filter='FooterZoneTest|PartnersPlacementTest'`
Expected: PASS — footer zone wrapper + illustration present, partner-strip still appears once on home, funder credit still site-wide.

- [ ] **Step 6: Commit**

```bash
git add resources/views/layouts/site/footer.blade.php resources/views/layouts/site.blade.php tests/Feature/FooterZoneTest.php
git commit -m "feat(footer): yellow zone with relocated partners, illustration, closing slot"
```

---

## Task 3: Style the inset dark card and the partner card

Visual-only (no behaviour). No new tests; run the existing footer/partner tests to confirm nothing breaks.

**Files:**
- Modify: `resources/css/app.css`

- [ ] **Step 1: Make `.site-footer` an inset, rounded-top dark card**

In `resources/css/app.css`, replace the `.site-footer` rule (currently starting at `/* ─── Fat footer ─── */`):

```css
.site-footer {
    background-color: var(--color-kidical-ink);
    color: rgba(255, 255, 255, 0.55);
    font-size: var(--text-base);
}
```

with:

```css
.site-footer {
    /* inset dark card floating on the yellow zone, rounded only at the top */
    max-width: 85rem;
    margin-inline: auto;
    border-radius: var(--radius-card) var(--radius-card) 0 0;
    background-color: var(--color-kidical-ink);
    color: rgba(255, 255, 255, 0.55);
    font-size: var(--text-base);
}
```

- [ ] **Step 2: Restyle the partner band into a white card on the yellow**

In `resources/css/app.css`, replace the `.partner-strip` rule:

```css
.partner-strip {
    background-color: var(--color-kidical-blue);
    /* generous breathing room; top absorbs 20px card overlap + natural space */
    padding-top: calc(2rem + 1.25rem);
    padding-bottom: 2.25rem;
}
```

with:

```css
.partner-strip {
    /* sits on the yellow zone now; the card is .partner-strip__inner */
    padding-top: 3rem;
    padding-bottom: 3rem;
}

.partner-strip__inner {
    background-color: white;
    border-radius: var(--radius-card);
    box-shadow: var(--shadow-card);
    padding: 1.5rem 2rem;
}
```

Note: a second `.partner-strip__inner` rule already exists below (the flex layout). Keep it — these are additive (same selector, both apply). Then update the text/chip colours from white-on-blue to ink-on-white. Replace `.partner-strip__label`:

```css
.partner-strip__label {
    font-size: var(--text-xs);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(255, 255, 255, 0.5);
}
```

with:

```css
.partner-strip__label {
    font-size: var(--text-xs);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: color-mix(in oklab, var(--color-kidical-ink), transparent 45%);
}
```

And replace `.partner-strip__more`:

```css
.partner-strip__more {
    font-size: var(--text-sm);
    font-weight: 600;
    white-space: nowrap;
    color: white;
    background-image:
        linear-gradient(var(--color-kidical-ink), var(--color-kidical-ink)),
        linear-gradient(rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.2));

    &:hover {
        color: white;
    }
}
```

with:

```css
.partner-strip__more {
    font-size: var(--text-sm);
    font-weight: 600;
    white-space: nowrap;
    color: var(--color-kidical-ink);

    &:hover {
        color: var(--color-kidical-blue);
    }
}
```

- [ ] **Step 3: Build CSS and run the affected tests**

Run: `npm run build`
Expected: builds with no errors.

Run: `php artisan test --compact --filter='FooterZoneTest|PartnersPlacementTest|PartnerStripComponentTest'`
Expected: PASS (visual change, structure unchanged).

- [ ] **Step 4: Commit**

```bash
git add resources/css/app.css
git commit -m "style(footer): inset rounded dark card + white partner card on yellow"
```

---

## Task 4: Migrate the 4 about pages off about-cta, delete the component

**Files:**
- Modify: `resources/views/about/mission.blade.php`, `resources/views/about/vision.blade.php`, `resources/views/about/organisation.blade.php`, `resources/views/about/index.blade.php`
- Delete: `resources/views/components/about-cta.blade.php`
- Modify: `resources/css/app.css` (remove about-cta container styles, keep `.about-cta__btn*`)
- Test: `tests/Feature/FooterZoneTest.php`

- [ ] **Step 1: Add the failing test**

Append to `tests/Feature/FooterZoneTest.php`:

```php
it('replaces the about-cta card with the new closing CTA on about pages', function () {
    get(route('about.mission'))
        ->assertOk()
        ->assertSee('Samen maken we straten veiliger', escape: false)
        ->assertDontSee('about-cta__content', escape: false);
});
```

- [ ] **Step 2: Run to verify it fails**

Run: `php artisan test --compact --filter='replaces the about-cta'`
Expected: FAIL — heading not present; old `about-cta__content` still rendered.

- [ ] **Step 3: Replace the `<x-about-cta>` block on each about page**

On each page below, delete the entire `<x-about-cta …> … </x-about-cta>` element (including any `<x-slot:actions>`) and add a closing slot before `</x-layouts::site>`. Use exactly:

`resources/views/about/mission.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Samen maken we straten veiliger"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>
```

`resources/views/about/vision.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Geloof je hierin?"
            :href="route('membership')" label="Word lid" icon="heart" />
    </x-slot:closing>
```

`resources/views/about/organisation.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Een afdeling starten of vervoegen?"
            :href="route('getting-started')" label="Zo begin je" />
    </x-slot:closing>
```

`resources/views/about/index.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Rij mee met de buurt"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>
```

- [ ] **Step 4: Delete the about-cta component**

```bash
git rm resources/views/components/about-cta.blade.php
```

- [ ] **Step 5: Remove about-cta container CSS, keep the reused button classes**

In `resources/css/app.css`, delete only the container/visual/content rules — `.about-cta`, `.about-cta__visual`, `.about-cta__visual img`, `.about-cta__content`, `.about-cta h2`, `.about-cta__sub`, `.about-cta__actions` (the block from `/* ─── Shared closing CTA — compact floating card… */` down to just before `.about-cta__btn {`).

**KEEP** `.about-cta__btn`, `.about-cta__btn--primary`, `.about-cta__btn--primary:hover`, `.about-cta__btn--ink`, `.about-cta__btn--ink:hover`, and `.about-cta__btn__disc` — `resources/views/livewire/partner-enquiry.blade.php` reuses `.about-cta__btn`/`--primary` for its submit button. Removing them breaks the enquiry form.

- [ ] **Step 6: Run the tests**

Run: `php artisan test --compact --filter='FooterZoneTest|AboutJourneyTest|PartnerEnquiryTest'`
Expected: PASS — about pages show the new CTA, no `about-cta__content`, enquiry form unaffected.

- [ ] **Step 7: Commit**

```bash
git add resources/views/about resources/css/app.css tests/Feature/FooterZoneTest.php
git commit -m "feat(footer): migrate about pages to closing-cta, remove about-cta"
```

---

## Task 5: Wire the closing slot on the remaining 13 pages

**Files:**
- Modify: `home.blade.php`, `activities/index.blade.php`, `activities/show.blade.php`, `groups/index.blade.php`, `groups/show.blade.php`, `getting-started.blade.php`, `find-a-bike.blade.php`, `volunteer.blade.php`, `steun-ons.blade.php`, `articles/index.blade.php`, `articles/show.blade.php`, `about/partners.blade.php`, `about/press.blade.php` (all under `resources/views/`)
- Test: `tests/Feature/ClosingCtaTest.php`

- [ ] **Step 1: Add the failing wiring test**

Append to `tests/Feature/ClosingCtaTest.php`:

```php
use function Pest\Laravel\get;

dataset('pages with a closing CTA', [
    ['home', 'Klaar voor je eerste rit?'],
    ['activities.index', 'Zelf een rit in je buurt?'],
    ['getting-started', 'Klaar om mee te rijden?'],
    ['find-a-bike', 'Toch nog een vraag?'],
    ['volunteer', 'Geef de straat terug aan kinderen'],
    ['articles.index', 'Zin gekregen om mee te rijden?'],
    ['about.partners', 'Samen op pad?'],
    ['about.press', 'Vragen van de pers?'],
]);

it('renders the page-specific closing CTA', function (string $route, string $heading) {
    get(route($route))
        ->assertOk()
        ->assertSee($heading, escape: false);
})->with('pages with a closing CTA');
```

(These 8 routes need no model factories. `activities.show`, `groups.*`, `articles.show`, `steun-ons` are wired the same way but route-model-bound or out of this smoke set.)

- [ ] **Step 2: Run to verify it fails**

Run: `php artisan test --compact --filter='renders the page-specific closing CTA'`
Expected: FAIL — headings not present yet.

- [ ] **Step 3: Add the closing slot to each page**

In each page, add the slot just before the closing `</x-layouts::site>` tag. Use exactly:

`home.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Klaar voor je eerste rit?"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>
```

`activities/index.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Zelf een rit in je buurt?"
            :href="route('getting-started')" label="Zo begin je" />
    </x-slot:closing>
```

`activities/show.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Nog niet zeker hoe het werkt?"
            :href="route('getting-started')" label="Lees hoe je meerijdt" />
    </x-slot:closing>
```

`groups/index.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Geen groep in je buurt?"
            :href="route('getting-started')" label="Zo begin je" />
    </x-slot:closing>
```

`groups/show.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Rij mee in je buurt"
            :href="route('membership')" label="Word lid" icon="heart" />
    </x-slot:closing>
```

`getting-started.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Klaar om mee te rijden?"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>
```

`find-a-bike.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Toch nog een vraag?"
            :href="route('contact')" label="Neem contact op" />
    </x-slot:closing>
```

`volunteer.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Geef de straat terug aan kinderen"
            :href="route('membership')" label="Word lid" icon="heart" />
    </x-slot:closing>
```

`steun-ons.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Zin gekregen om mee te rijden?"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>
```

`articles/index.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Zin gekregen om mee te rijden?"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>
```

`articles/show.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Zin gekregen om mee te rijden?"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>
```

`about/partners.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Samen op pad?"
            :href="route('contact')" label="Neem contact op" />
    </x-slot:closing>
```

`about/press.blade.php`:
```blade
    <x-slot:closing>
        <x-closing-cta heading="Vragen van de pers?"
            :href="route('contact')" label="Neem contact op" />
    </x-slot:closing>
```

- [ ] **Step 4: Run the wiring test**

Run: `php artisan test --compact --filter='renders the page-specific closing CTA'`
Expected: PASS — all 8 sampled pages show their heading.

- [ ] **Step 5: Commit**

```bash
git add resources/views/home.blade.php resources/views/activities resources/views/groups resources/views/getting-started.blade.php resources/views/find-a-bike.blade.php resources/views/volunteer.blade.php resources/views/steun-ons.blade.php resources/views/articles resources/views/about/partners.blade.php resources/views/about/press.blade.php tests/Feature/ClosingCtaTest.php
git commit -m "feat(footer): wire closing CTA on remaining 13 pages"
```

---

## Task 6: Full verification

**Files:** none (verification only)

- [ ] **Step 1: Run the full suite**

Run: `php artisan test --compact`
Expected: PASS — no regressions across the suite.

- [ ] **Step 2: Format PHP/Blade**

Run: `vendor/bin/pint --dirty --format agent`
Expected: clean (fixes applied if any).

- [ ] **Step 3: Build assets**

Run: `npm run build`
Expected: builds with no errors.

- [ ] **Step 4: Visual smoke (manual)**

Load `https://kidicalmass.test/nl` and one about page. Confirm: continuous yellow from the CTA through partners → illustration → into the dark card; dark footer is inset with rounded top corners; no sky-blue partner band; no double CTA on about pages.

- [ ] **Step 5: Commit any formatting changes**

```bash
git add -u
git commit -m "chore(footer): pint formatting" || echo "nothing to format"
```

---

## Notes & non-goals

- **Stub pages get no closing CTA** — they render the yellow footer zone only. Do not add `<x-slot:closing>` to `components/stub.blade.php`.
- **Top hairline** (from the Pencil design) is intentionally omitted — with a page-owned yellow closing block it would land mid-field.
- **Bottom-bar credits** (website-by + funder line) are kept as-is.
- **`.site-footer` max-width (85rem)** and zone spacing are first-pass values for Frederik to refine visually; they're layout values, not design tokens.
- **`body:has(.activity-actions-bar) .site-footer` padding fix** stays as-is — it still lifts the dark card above the fixed activity bar.
- Per-page CTA copy is a starting point; refine in the page views directly.

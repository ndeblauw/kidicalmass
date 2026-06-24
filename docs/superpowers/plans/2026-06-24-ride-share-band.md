# Ride-page share band Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the buried fixed-bar `Deel` button on the ride page with a warm, framed share band that reframes sharing as inviting a specific gezin and surfaces four explicit channels.

**Architecture:** A new reusable `<x-share-band>` Blade component (BEM markup + a registered CSS partial, matching the sibling `support-callout` pattern) is dropped into `activities/show.blade.php` after the support callout. The fixed `.activity-actions-bar` (share + iCal save) is removed entirely along with its CSS and the footer-padding hack.

**Tech Stack:** Laravel 13, Blade components, Alpine.js (clipboard copy), Tailwind v4 token-backed CSS partials, Pest 4 feature tests.

## Global Constraints

- Public-site headings use raw `<h1>`–`<h6>`, never `flux:heading`. `flux:icon.*` is allowed.
- No raw hex/px in component `.blade.php` files (enforced by `CssArchitectureTest`). Use tokens; SVGs use `fill="currentColor"` and are coloured/sized via the CSS partial.
- Every CSS partial must be `@import`-ed in `resources/css/app.css` (enforced by `CssArchitectureTest`). Reusable component CSS lives in `resources/css/components/<role>.css`.
- Interface copy follows `docs/tone-of-voice.md`: warm, local, joyful. **No em-dashes** in any copy.
- Run `vendor/bin/pint --dirty --format agent` before each commit (PHP files only).
- Run tests with `php artisan test --compact`.

---

### Task 1: Build the `<x-share-band>` component + CSS partial

**Files:**
- Create: `resources/views/components/share-band.blade.php`
- Create: `resources/css/components/share-band.css`
- Modify: `resources/css/app.css` (add one `@import` line in the components block, ~line 209)
- Test: `tests/Feature/ShareBandTest.php`

**Interfaces:**
- Produces: `<x-share-band :url="string" :title="string" :date="string" heading?="string" subline?="string" />`
  - `url` — absolute ride URL (required)
  - `title` — ride title (required)
  - `date` — lowercase human date, e.g. `"zondag 28 juni"` (required)
  - `heading` / `subline` — optional copy overrides; defaults baked in.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/ShareBandTest.php`:

```php
<?php

use Illuminate\Support\Facades\Blade;

it('renders the share band with framed copy and all four channels', function () {
    $url = 'https://kidicalmass.test/nl/events/17';

    $html = Blade::render(
        '<x-share-band :url="$url" title="Kidical Mass Gent" date="zondag 28 juni" />',
        ['url' => $url]
    );

    $encodedUrl = rawurlencode($url);

    expect($html)
        ->toContain('Ken je een gezin dat dit leuk zou vinden?')
        ->toContain('Kopieer link')
        ->toContain('wa.me/?text=')
        ->toContain('facebook.com/sharer/sharer.php?u='.rawurlencode($url))
        ->toContain('mailto:?subject=')
        ->toContain($encodedUrl) // the ride URL is encoded into the WhatsApp message
        ->toContain('aria-label="Deel via WhatsApp"')
        ->toContain('aria-label="Deel op Facebook"')
        ->toContain('aria-label="Deel via e-mail"');
});

it('lets callers override the heading and subline', function () {
    $html = Blade::render(
        '<x-share-band :url="$url" title="T" date="d" heading="Anders" subline="Ook anders" />',
        ['url' => 'https://example.test/x']
    );

    expect($html)->toContain('Anders')->toContain('Ook anders');
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter=ShareBandTest`
Expected: FAIL — component `share-band` does not exist (`Unable to locate a class or view for component [share-band]`).

- [ ] **Step 3: Create the component**

Create `resources/views/components/share-band.blade.php`:

```blade
@props([
    'url',
    'title',
    'date',
    'heading' => 'Ken je een gezin dat dit leuk zou vinden?',
    'subline' => 'Samen fietsen is leuker. Stuur deze rit door, dan staat de straat zondag nog voller met kinderen.',
])

@php
    $message = "Zin om samen te fietsen? {$title} op {$date}, een vrolijke gezinsrit door autovrije straten. Rij je mee? {$url}";
    $whatsappUrl = 'https://wa.me/?text='.rawurlencode($message);
    $facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($url);
    $emailSubject = 'Een leuke fietstocht voor jullie gezin';
    $mailtoUrl = 'mailto:?subject='.rawurlencode($emailSubject).'&body='.rawurlencode($message);
@endphp

<section class="share-band" x-data="{ copied: false }">
    <div class="container mx-auto px-4">
        <div class="share-band__inner">
            <div class="share-band__text">
                <h2 class="share-band__title">{{ $heading }}</h2>
                <p class="share-band__body">{{ $subline }}</p>
            </div>

            <div class="share-band__channels">
                {{-- Copy link --}}
                <button type="button"
                    class="share-band__copy"
                    x-on:click="navigator.clipboard.writeText(@js($url)).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                    :aria-label="copied ? 'Link gekopieerd' : 'Kopieer de link naar deze rit'">
                    <flux:icon.link class="share-band__copy-icon" aria-hidden="true" />
                    <span x-show="!copied">Kopieer link</span>
                    <span x-show="copied" x-cloak>Gekopieerd!</span>
                </button>

                {{-- WhatsApp --}}
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener"
                    class="share-band__icon share-band__icon--whatsapp"
                    aria-label="Deel via WhatsApp">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                    </svg>
                </a>

                {{-- Facebook --}}
                <a href="{{ $facebookUrl }}" target="_blank" rel="noopener"
                    class="share-band__icon share-band__icon--facebook"
                    aria-label="Deel op Facebook">
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>

                {{-- Email --}}
                <a href="{{ $mailtoUrl }}"
                    class="share-band__icon share-band__icon--email"
                    aria-label="Deel via e-mail">
                    <flux:icon.envelope aria-hidden="true" />
                </a>
            </div>
        </div>
    </div>
</section>
```

- [ ] **Step 4: Create the CSS partial**

Create `resources/css/components/share-band.css`:

```css
@layer components {
    .share-band {
        width: 100vw;
        margin-left: calc(50% - 50vw);
        margin-bottom: calc(var(--spacing) * -8);
        background-color: var(--color-kidical-light-yellow);
        padding-block: clamp(2.5rem, 5vw, 3.5rem);
    }

    .share-band__inner {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 1.5rem;

        @media (min-width: 768px) {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 2.5rem;
        }
    }

    .share-band__title {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: var(--text-2xl);
        line-height: 1.15;
        color: var(--color-kidical-ink);
        max-width: 22ch;
    }

    .share-band__body {
        margin-top: 0.5rem;
        max-width: 32rem;
        color: var(--color-text-body);
    }

    .share-band__channels {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    /* Copy-link pill */
    .share-band__copy {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.125rem;
        border-radius: 999px;
        background-color: white;
        color: var(--color-kidical-ink);
        font-weight: 600;
        box-shadow: var(--shadow-float);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        cursor: pointer;
    }

    .share-band__copy:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-hover);
    }

    .share-band__copy-icon {
        width: 1.125rem;
        height: 1.125rem;
        color: var(--color-kidical-blue);
    }

    /* Circular icon buttons */
    .share-band__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
        border-radius: 999px;
        background-color: white;
        box-shadow: var(--shadow-float);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .share-band__icon:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-hover);
    }

    .share-band__icon svg {
        width: 1.375rem;
        height: 1.375rem;
    }

    .share-band__icon--whatsapp { color: var(--color-kidical-green); }
    .share-band__icon--facebook { color: var(--color-kidical-blue); }
    .share-band__icon--email    { color: var(--color-kidical-red); }
}
```

- [ ] **Step 5: Register the partial in `app.css`**

In `resources/css/app.css`, add this line to the components `@import` block (after line 209, `@import './components/photo-collage.css';`):

```css
@import './components/share-band.css';
```

- [ ] **Step 6: Run the tests to verify they pass**

Run: `php artisan test --compact --filter=ShareBandTest`
Expected: PASS (both examples).

Also run the architecture guard to confirm the new partial is registered and has no raw values:
Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS.

- [ ] **Step 7: Commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/components/share-band.blade.php resources/css/components/share-band.css resources/css/app.css tests/Feature/ShareBandTest.php
git commit -m "feat(share): add reusable share-band component

- Framed band (heading + subline) with copy-link pill and WhatsApp,
  Facebook, email channel buttons; pre-filled NL invite message
- Token-backed CSS partial, registered in app.css

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 2: Drop the band into the ride page and remove the fixed bar

**Files:**
- Modify: `resources/views/activities/show.blade.php` (remove lines 351-371; insert band after line 272)
- Modify: `resources/css/pages/activity.css` (delete `.activity-actions-bar*` and unused `.activity-hero__actions` rules)
- Test: `tests/Feature/ShareBandTest.php` (add an integration example)

> **DEFERRED — `resources/css/chrome.css`:** the plan originally removed the
> `body:has(.activity-actions-bar) .site-footer` footer-padding rule (chrome.css lines
> 1-3). At execution time `chrome.css` had unrelated uncommitted work by the other developer
> (Nico) in the same file (roze-shell-bar, ~line 365). Touching it would force his hunk into
> our commit. Once the action bar markup is gone the selector matches nothing, so the rule is
> harmless dead code. **Leave chrome.css untouched**; remove the rule in a follow-up once
> Nico's chrome.css changes land.

**Interfaces:**
- Consumes: `<x-share-band>` from Task 1.

- [ ] **Step 1: Write the failing integration test**

Append to `tests/Feature/ShareBandTest.php` (add the imports at the top of the file if not present):

```php
use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the share band on the ride page and no longer shows the old action bar', function () {
    $activity = Activity::factory()->create();

    $response = $this->get(route('activities.show', $activity));

    $response->assertOk()
        ->assertSee('Ken je een gezin dat dit leuk zou vinden?')
        ->assertSee('wa.me/?text=', false)
        ->assertDontSee('activity-actions-bar')
        ->assertDontSee('Bewaar in agenda');
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter=ShareBandTest`
Expected: FAIL — page still contains `activity-actions-bar` / `Bewaar in agenda` and not yet the band heading.

- [ ] **Step 3: Remove the fixed action bar**

In `resources/views/activities/show.blade.php`, delete the entire `{{-- FIXED ACTION BAR --}}` block (lines 351-371):

```blade
    {{-- FIXED ACTION BAR --}}
    <div class="activity-actions-bar" x-data="{ copied: false, shareTitle: @js($activity->title_nl) }">
        <flux:button href="{{ route('activities.ical', $activity) }}" icon="calendar-days" variant="ghost">
            Bewaar in agenda
        </flux:button>
        <flux:button
            icon="share"
            variant="ghost"
            x-on:click="
                if (navigator.share) {
                    navigator.share({ title: shareTitle, url: window.location.href })
                } else {
                    navigator.clipboard.writeText(window.location.href).then(() => {
                        copied = true
                        setTimeout(() => copied = false, 2000)
                    })
                }
            "
        >Deel</flux:button>
        <span x-show="copied" class="activity-actions-bar__copied">Gekopieerd!</span>
    </div>
```

- [ ] **Step 4: Insert the share band after the support callout**

In `resources/views/activities/show.blade.php`, immediately after the `<x-support-callout variant="event" />` line (line 272), add:

```blade
    {{-- Share — the warm "invite a gezin" moment, end of page --}}
    <x-share-band
        :url="route('activities.show', $activity)"
        :title="$activity->title_nl"
        :date="$activity->begin_date->translatedFormat('l j F')" />
```

- [ ] **Step 5: Delete the dead CSS**

In `resources/css/pages/activity.css`, delete these rule blocks:
- `.activity-actions-bar__copied` (lines ~234-238)
- `.activity-actions-bar` (lines ~239-253)
- `.activity-actions-bar [data-flux-button]` (lines ~254-266)
- `.activity-hero__actions` (lines ~228-233, unused dead code)

**Do not touch `resources/css/chrome.css`** — see the DEFERRED note in this task's Files
section. The `body:has(.activity-actions-bar)` rule stays for now (harmless dead selector
once the bar markup is gone).

- [ ] **Step 6: Run the tests to verify they pass**

Run: `php artisan test --compact --filter=ShareBandTest`
Expected: PASS (all examples, including the new integration one).

Then run the broader guards:
Run: `php artisan test --compact --filter="CssArchitectureTest|ActivityIcalTest|ActivityMapTest"`
Expected: PASS (iCal route still works; share button removal did not touch it).

- [ ] **Step 7: Build assets and commit**

```bash
npm run build
vendor/bin/pint --dirty --format agent
git add resources/views/activities/show.blade.php resources/css/pages/activity.css tests/Feature/ShareBandTest.php
git commit -m "feat(share): use share-band on ride page, drop fixed action bar

- Place the framed share band after the support callout, end of page
- Remove the fixed Deel/iCal action bar and its CSS
- Drop the unused .activity-hero__actions dead rules

Why: the old share button was undiscoverable and buried WhatsApp.

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

## Manual verification (after Task 2)

Load `https://kidicalmass.test/nl/events/17`:
- The band appears near the bottom with the heading, subline, and four channels.
- "Kopieer link" copies the URL and flips to "Gekopieerd!" for 2s.
- WhatsApp / Facebook / email buttons open with the pre-filled invite + ride URL.
- No fixed bar at the viewport bottom; footer sits flush.

## Notes for the build thread

- This is mechanical Blade/CSS/test work — a good moment to run on Sonnet.
- After both tasks land, consider a `/pipeline` bump for the ride detail page (P-nn) if Wire/UI status advances, gated on Frederik's own critique pass.

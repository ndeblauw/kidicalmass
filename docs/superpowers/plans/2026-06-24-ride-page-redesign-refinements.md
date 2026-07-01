# Ride Page Redesign — Refinements Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Refine the already-redesigned ride detail page (`activities.show`, rides only) — rework the hero, the Praktisch card, and the team section per Frederik's brief.

**Architecture:** The ride page is a single Blade view (`resources/views/activities/show.blade.php`) styled by the page partial `resources/css/pages/activity.css`, sitting on the shared site layout. We reshape the hero (date·time eyebrow + description-as-lead + group logo/zip + share links), expand the Praktisch card (2-col meta, full date, always-on route with a faux fallback, plus a newsletter "updates" card beside it), and replace the single-organiser + recruitment reveal with a compact real-volunteer avatar row. One small reusable component is extracted (`<x-share-links>`) so the hero and the existing share-band share one markup source.

**Tech Stack:** Laravel 13, Blade, Livewire/Flux (public-site rules — raw `<h1>`–`<h6>`, Flux for other components), Tailwind v4 + role-based CSS partials, Pest 4.

## Global Constraints

- **Public-site frontend rules** (CLAUDE.md): headings are raw `<h1>`–`<h6>`, never `flux:heading`. Other `flux:*` components are fine.
- **Three styling layers:** tokens (`@theme`/`@layer base`), component appearance (in the component's `.blade.php`), composition (page template). Never a raw hex/px in a `.blade.php` **component** — use tokens. Page **views** (`views/activities/*`) and CSS partials are not scanned by the architecture test, but still prefer tokens.
- **CSS lives in partials**, never `app.css`. Page-only rules → `resources/css/pages/activity.css`. Reusable component appearance baked into the component or `resources/css/components/<role>.css`, and every new partial must be `@import`-ed in `app.css` (enforced by `CssArchitectureTest`).
- **Tone of voice** (`docs/tone-of-voice.md`): joyful, warm, local, committed. No em-dashes in copy (use commas / "en" / parentheses).
- **Tests:** every change is programmatically tested. Run `php artisan test --compact --filter=...` for the affected tests, and `vendor/bin/pint --dirty --format agent` before finishing.
- **Locale:** ride routes are locale-prefixed; build URLs with `route('activities.show', ['locale' => 'nl', 'activity' => $activity])` in tests.
- **Design decisions resolved with Frederik (2026-06-24):**
  1. Hero "intro text" = the ride's **description** (`content_nl`) rendered as the hero lead; the separate body "Beschrijving" section is **removed** (no duplication).
  2. Team = a **compact avatar + first-name** row. No roles.
  3. Volunteers = the organising **group's real registered members only** (`$activity->groups->flatMap->users`); the section hides when there are none. No faux padding.
  4. The pink-vest recruitment CTA + inline volunteer-signup reveal are **removed** from the ride page.

---

## File Structure

- `app/Http/Controllers/ActivityController.php` — eager-load `groups.users` so the team row has data (Task 1).
- `resources/views/components/share-links.blade.php` — **new** reusable share-controls component (copy + WhatsApp + Facebook + e-mail), used by the hero and by `<x-share-band>` (Task 2).
- `resources/views/components/share-band.blade.php` — refactored to render `<x-share-links>` instead of inlining the channels (Task 2).
- `resources/views/activities/show.blade.php` — hero rebuild + description relocation (Task 3), Praktisch card + updates card (Task 4), team row + removals (Task 5).
- `resources/css/pages/activity.css` — hero, facts, and team rules reshaped (Tasks 3–5).
- `tests/Feature/RidePageRedesignTest.php` — **new** focused feature test covering the brief (Tasks 3–5).
- `tests/Feature/RideSurfacesSmokeTest.php`, `tests/Feature/PublicPagesTest.php`, `tests/Feature/CssArchitectureTest.php` — existing tests must keep passing (Task 6).

No new base folders. `<x-share-links>` is the only new reusable unit; its appearance reuses the existing `.share-band__*` classes (already in `resources/css/components/share-band.css`), so no new CSS partial is introduced.

---

### Task 1: Controller eager-loads the organising groups' members

**Files:**
- Modify: `app/Http/Controllers/ActivityController.php:22` (the `$activity->load([...])` call)
- Test: `tests/Feature/RidePageRedesignTest.php` (created here, extended by later tasks)

**Interfaces:**
- Produces: the `activities.show` view receives `$activity` with `groups.users` eager-loaded, so `$activity->groups->flatMap->users` is available without lazy-loading.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/RidePageRedesignTest.php`:

```php
<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;

use function Pest\Laravel\get;

function makeRide(array $attributes = []): Activity
{
    return Activity::factory()->create(array_merge([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Etterbeek',
        'content_nl' => 'Een vrolijke gezinsrit door autovrije straten.',
        'begin_date' => now()->addDays(5)->setTime(14, 0),
        'location' => 'Jubelpark, Brussel',
        'distance' => '6 km',
    ], $attributes));
}

function rideUrl(Activity $activity): string
{
    return route('activities.show', ['locale' => 'nl', 'activity' => $activity]);
}

it('eager-loads the organising group members so the team row has no lazy queries', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Etterbeek', 'zip' => '1040']);
    $member = User::factory()->create(['name' => 'Marieke Janssens']);
    $group->users()->attach($member, ['role' => 'trekker', 'is_public' => true]);

    $ride = makeRide();
    $ride->groups()->attach($group);

    get(rideUrl($ride))
        ->assertOk()
        ->assertSee('Marieke'); // first name from the real roster renders
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter=RidePageRedesignTest`
Expected: FAIL — the team row does not yet render real members (`Marieke` not seen). This proves the test exercises the new behaviour before it exists.

- [ ] **Step 3: Add `groups.users` to the eager-load**

In `app/Http/Controllers/ActivityController.php`, change the load call (currently `$activity->load(['author', 'groups']);`) to:

```php
$activity->load(['author', 'groups.users']);
```

- [ ] **Step 4: Re-run the test**

Run: `php artisan test --compact --filter=RidePageRedesignTest`
Expected: still FAIL on `assertSee('Marieke')` (the view doesn't render members yet — Task 5), but NOT a lazy-loading/ N+1 error. This task only guarantees the data is present. **Leave this test red until Task 5**; it goes green there.

- [ ] **Step 5: Commit**

```bash
git add app/Http/Controllers/ActivityController.php tests/Feature/RidePageRedesignTest.php
git commit -m "feat(ride): eager-load organising group members for the team row"
```

---

### Task 2: Extract `<x-share-links>` and reuse it in the share-band

**Files:**
- Create: `resources/views/components/share-links.blade.php`
- Modify: `resources/views/components/share-band.blade.php`
- Test: `tests/Feature/RidePageRedesignTest.php`

**Interfaces:**
- Produces: `<x-share-links :url :title :date :message :subject />` — a div with class `share-band__channels` containing the copy-link button (Alpine `copied` state) + WhatsApp + Facebook + e-mail anchors. Accepts extra classes via `$attributes`. Used by the hero (Task 3) and by `<x-share-band>`.
- Consumes: nothing from earlier tasks.

- [ ] **Step 1: Write the failing test**

Append to `tests/Feature/RidePageRedesignTest.php`:

```php
it('renders the shared share-links controls with all channels', function () {
    $html = Blade::render(
        '<x-share-links url="https://example.test/rit" title="Kidical Mass" date="zondag 28 juni" />'
    );

    expect($html)
        ->toContain('share-band__channels')
        ->toContain('wa.me')                 // WhatsApp
        ->toContain('facebook.com/sharer')   // Facebook
        ->toContain('mailto:')               // e-mail
        ->toContain('Kopieer link');         // copy button
});
```

Add `use Illuminate\Support\Facades\Blade;` at the top of the test file.

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter="renders the shared share-links"`
Expected: FAIL — `Unable to locate a class or view for component [share-links]`.

- [ ] **Step 3: Create the component**

Create `resources/views/components/share-links.blade.php` (moves the channel markup out of `share-band.blade.php` verbatim, wrapped in its own `x-data`):

```blade
@props([
    'url',
    'title',
    'date',
    'message' => null,
    'subject' => 'Een leuke fietstocht voor jullie gezin',
])

@php
    $shareMessage = $message ?? "Zin om samen te fietsen? {$title} op {$date}, een vrolijke gezinsrit door autovrije straten. Rij je mee? {$url}";
    $whatsappUrl = 'https://wa.me/?text='.rawurlencode($shareMessage);
    $facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($url);
    $mailtoUrl = 'mailto:?subject='.rawurlencode($subject).'&body='.rawurlencode($shareMessage);
@endphp

<div {{ $attributes->class('share-band__channels') }} x-data="{ copied: false }">
    {{-- Copy link --}}
    <button type="button"
        class="share-band__copy"
        x-on:click="navigator.clipboard.writeText(@js($url)).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
        :aria-label="copied ? 'Link gekopieerd' : 'Kopieer de link naar deze rit'">
        <flux:icon.link class="share-band__copy-icon" aria-hidden="true" />
        <span x-show="!copied">Kopieer link</span>
        <span x-show="copied" x-cloak aria-live="polite">Gekopieerd!</span>
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
```

> Note: the SVG `<path>` data above is raw, but `CssArchitectureTest` only flags raw hex/px in `[...]` arbitrary classes and `style="..."` attributes — path coordinates are neither, and this same markup already lives unflagged in `share-band.blade.php`.

- [ ] **Step 4: Refactor `share-band.blade.php` to use the component**

In `resources/views/components/share-band.blade.php`:
- Remove the `x-data="{ copied: false }"` from the `<section>` opening tag (it now lives inside `<x-share-links>`). The section becomes:
  ```blade
  <section @class(['share-band', 'share-band--contained' => $contained])>
  ```
- Replace the entire `<div class="share-band__channels"> ... </div>` block (the copy button + 3 channel anchors) with:
  ```blade
  <x-share-links :url="$url" :title="$title" :date="$date" :message="$message" :subject="$subject" />
  ```
- Keep the `@props`, the `@php` URL block can be deleted (the component computes the URLs now) — but leave `$message`/`$subject` in `@props` since they're passed through. Delete the now-unused `$whatsappUrl`/`$facebookUrl`/`$mailtoUrl`/`$shareMessage` `@php` block.

- [ ] **Step 5: Run the tests**

Run: `php artisan test --compact --filter="RidePageRedesignTest|RideSurfacesSmokeTest|PublicPagesTest"`
Expected: the new share-links test PASSES; the `Marieke` test still fails (Task 5); existing share-band usages (ride body Deel section, basic page) still render. If any existing test referencing `share-band__channels` exists, it stays green (the class is preserved).

- [ ] **Step 6: Commit**

```bash
git add resources/views/components/share-links.blade.php resources/views/components/share-band.blade.php tests/Feature/RidePageRedesignTest.php
git commit -m "refactor(share): extract <x-share-links> shared by hero and share-band"
```

---

### Task 3: Rebuild the hero (eyebrow, description-lead, group logo/zip, share)

**Files:**
- Modify: `resources/views/activities/show.blade.php` (hero `<header class="activity-head">` block, lines ~7–43; and remove the body `.activity-prose` section, lines ~136–141)
- Modify: `resources/css/pages/activity.css` (hero rules ~27–63, add new hero rules)
- Test: `tests/Feature/RidePageRedesignTest.php`

**Interfaces:**
- Consumes: `<x-share-links>` (Task 2); `$activity->dateFull`, `$activity->timeLabel`, `$activity->content_nl`, `$activity->groups` (each `->name`, `->zip`).
- Produces: hero markup using classes `activity-head__eyebrow`, `activity-head__lead`, `activity-head__org`, `activity-head__share` (consumed by no later task).

- [ ] **Step 1: Write the failing test**

Append to `tests/Feature/RidePageRedesignTest.php`:

```php
it('shows the date·time eyebrow, the description as hero lead, and the group zip', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Etterbeek', 'zip' => '1040']);
    $ride = makeRide([
        'content_nl' => 'Een vrolijke gezinsrit door autovrije straten.',
        'begin_date' => now()->setDate(2026, 6, 28)->setTime(14, 0),
    ]);
    $ride->groups()->attach($group);

    get(rideUrl($ride))
        ->assertOk()
        ->assertSee('activity-head__eyebrow', false)          // yellow date·time eyebrow exists
        ->assertSee('14:00')                                  // time present in the eyebrow
        ->assertSee('Een vrolijke gezinsrit')                 // description rendered as hero lead
        ->assertSee('1040')                                   // group zip on the logo lockup
        ->assertSee('activity-head__share', false)            // share links at the bottom of the hero
        ->assertDontSee('activity-head__date', false)         // old date treatment gone
        ->assertDontSee('activity-head__chapter', false);     // old pin lockup gone
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter="date·time eyebrow"`
Expected: FAIL — `activity-head__eyebrow` not present; `activity-head__date`/`activity-head__chapter` still present.

- [ ] **Step 3: Rewrite the hero markup**

In `resources/views/activities/show.blade.php`, replace the `<div class="activity-head__copy"> ... </div>` block (the title + `activity-head__date` + `activity-head__chapter` markup) with:

```blade
<div class="activity-head__copy">
    <p class="activity-head__eyebrow">
        <time datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ \Illuminate\Support\Str::ucfirst($activity->dateFull) }} &middot; {{ $activity->timeLabel }}</time>
    </p>

    <h1 class="page-hero__title">{{ $activity->title_nl }}</h1>

    @if($activity->content_nl)
        <x-intro-text class="activity-head__lead">{!! nl2br(e($activity->content_nl)) !!}</x-intro-text>
    @endif

    @if($activity->groups->isNotEmpty())
        <div class="activity-head__org">
            <img src="{{ asset('img/logos/logo-icon.png') }}" alt="" aria-hidden="true" class="activity-head__org-mark">
            <div class="activity-head__org-label">
                @foreach($activity->groups as $group)
                    <span class="activity-head__org-name">{{ $group->name }}</span>
                    @if($group->zip)
                        <span class="activity-head__org-zip">{{ $group->zip }}</span>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <x-share-links
        :url="route('activities.show', $activity)"
        :title="$activity->title_nl"
        :date="\Illuminate\Support\Str::ucfirst($activity->dateFull)"
        class="activity-head__share" />
</div>
```

Leave the `<figure class="activity-head__media">` photo block unchanged.

- [ ] **Step 4: Remove the now-duplicated body description**

In the same file, delete the `{{-- DESCRIPTION --}}` block entirely (the `@if($activity->content_nl) <section class="activity-prose"> ... </section> @endif`). The description now lives only in the hero (per decision 1).

- [ ] **Step 5: Update the hero CSS**

In `resources/css/pages/activity.css`:

Delete the `.activity-head__date`, `.activity-head__chapter`, `.activity-head__chapter-pin`, and `.activity-head__chapter-label` rule blocks (the old date + pin lockup). Then add, right after `.activity-head__inner`:

```css
    /* Yellow date·time eyebrow above the title */
    .activity-head__eyebrow {
        color: var(--color-kidical-yellow);
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: clamp(var(--text-lg), 1.6vw, var(--text-xl));
        margin: 0 0 0.75rem;
    }
    /* Description as a light lead on the blue band */
    .activity-head__lead {
        color: color-mix(in oklab, white, transparent 6%);
        margin-top: 1.25rem;
        max-width: 46ch;
    }
    /* Organising group identity lockup — brand mark + name + zip */
    .activity-head__org {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        margin-top: 1.75rem;
    }
    .activity-head__org-mark {
        width: 2.75rem;
        height: 2.75rem;
        flex-shrink: 0;
    }
    .activity-head__org-label {
        display: flex;
        flex-direction: column;
        line-height: 1.15;
    }
    .activity-head__org-name {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: var(--text-lg);
        color: white;
    }
    .activity-head__org-zip {
        font-size: var(--text-sm);
        font-weight: 600;
        color: color-mix(in oklab, white, transparent 28%);
    }
    .activity-head__share {
        margin-top: 2rem;
    }
```

(The `.activity-head__lead` overrides the `<x-intro-text>` default ink colour so it reads on blue. The share controls reuse the existing white `.share-band__*` pills/circles, which sit fine on the blue band.)

- [ ] **Step 6: Run the test**

Run: `php artisan test --compact --filter="date·time eyebrow"`
Expected: PASS — eyebrow + zip + lead + share present, old classes gone.

- [ ] **Step 7: Commit**

```bash
git add resources/views/activities/show.blade.php resources/css/pages/activity.css tests/Feature/RidePageRedesignTest.php
git commit -m "feat(ride): rework hero — date·time eyebrow, description lead, group zip, share"
```

---

### Task 4: Praktisch card — 2-col meta, full date, always-on route, updates card

**Files:**
- Modify: `resources/views/activities/show.blade.php` (the `{{-- PRAKTISCH --}}` `<article class="activity-facts">` block, lines ~48–134)
- Modify: `resources/css/pages/activity.css` (`.activity-facts*` rules ~126–191; add `.activity-praktisch`, faux-route, updates rules)
- Test: `tests/Feature/RidePageRedesignTest.php`

**Interfaces:**
- Consumes: `<x-route-map>` (existing), `<x-newsletter-optin :group>` (existing), `<x-icon-chip>` (existing), `$activity->route_coordinates`, `$activity->komoot_url`, `$activity->dateFull`/`timeLabel`/`location`/`distance`/`duration_label`, `$activity->groups->first()`.
- Produces: a two-column zone class `activity-praktisch` wrapping the facts card and the updates card; facts card meta in two columns; route panel that always renders (real map or faux fallback).

- [ ] **Step 1: Write the failing test**

Append to `tests/Feature/RidePageRedesignTest.php`:

```php
it('shows the full date in Startuur, an always-on route, and an updates card beside Praktisch', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Etterbeek', 'zip' => '1040']);
    $ride = makeRide([
        'begin_date' => now()->setDate(2026, 6, 28)->setTime(14, 0),
    ]); // no GPX media → faux route fallback must still render
    $ride->groups()->attach($group);

    get(rideUrl($ride))
        ->assertOk()
        ->assertSee('activity-praktisch', false)        // the two-column wrapper
        ->assertSee('activity-facts__route-faux', false) // route shown even without a GPX file
        ->assertSee('Startuur')
        ->assertSee('juni')                              // full date (not just the time) in Startuur
        ->assertSee('Mis geen rit');                     // <x-newsletter-optin> guest copy = the updates card
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter="always-on route"`
Expected: FAIL — `activity-praktisch` / `activity-facts__route-faux` absent; the map is currently gated behind `@if($hasMap)`.

- [ ] **Step 3: Restructure the Praktisch markup**

Replace the whole `{{-- PRAKTISCH --}}` `<article class="activity-facts"> ... </article>` block with this two-column zone (facts card + updates card). Note the meta `dl` no longer sits in a `__body`/`__main` grid; the route panel is moved below the meta inside the card, and always renders:

```blade
{{-- PRAKTISCH — facts + route, paired with a "stay in the loop" card --}}
<section class="activity-praktisch">
    <article class="activity-facts">
        <h2 class="activity-facts__title">Praktisch</h2>
        <dl class="activity-facts__meta">
            <div class="activity-facts__meta-item">
                <x-icon-chip color="light-blue" size="sm" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                </x-icon-chip>
                <div>
                    <dt>Startuur</dt>
                    <dd><time datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ \Illuminate\Support\Str::ucfirst($activity->dateFull) }}, {{ $activity->timeLabel }}</time></dd>
                </div>
            </div>

            <div class="activity-facts__meta-item">
                <x-icon-chip color="light-blue" size="sm" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-7 7-12a7 7 0 1 0-14 0c0 5 7 12 7 12z"/><circle cx="12" cy="9" r="2.5"/></svg>
                </x-icon-chip>
                <div>
                    <dt>Vertrekpunt</dt>
                    <dd>{!! nl2br(e($activity->location)) !!}</dd>
                </div>
            </div>

            @if($activity->distance)
                <div class="activity-facts__meta-item">
                    <x-icon-chip color="light-blue" size="sm" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7L4 11l4 4"/><path d="M4 11h16"/><path d="M16 17l4-4-4-4"/></svg>
                    </x-icon-chip>
                    <div>
                        <dt>Afstand</dt>
                        <dd>{{ $activity->distance }}</dd>
                    </div>
                </div>
            @endif

            @if($activity->duration_label)
                <div class="activity-facts__meta-item">
                    <x-icon-chip color="light-blue" size="sm" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    </x-icon-chip>
                    <div>
                        <dt>Duur</dt>
                        <dd>{{ $activity->duration_label }}</dd>
                    </div>
                </div>
            @endif

            <div class="activity-facts__meta-item">
                <x-icon-chip color="light-blue" size="sm" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 9a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2 2 2 0 0 0 0 4 2 2 0 0 1-2 2H6a2 2 0 0 1-2-2 2 2 0 0 0 0-4z"/></svg>
                </x-icon-chip>
                <div>
                    <dt>Deelname</dt>
                    <dd>Gratis &middot; geen inschrijving nodig</dd>
                </div>
            </div>
        </dl>

        {{-- Route — the real GPX track when present, else the stylised brand fallback
             (same dual logic as the chapter page's <x-next-ride>). --}}
        <div class="activity-facts__map">
            @if($hasMap)
                <x-route-map :coordinates="$routeCoords" :interactive="false" class="activity-facts__route" aria-hidden="true" />
            @else
                <div class="activity-facts__route-faux" aria-hidden="true">
                    <svg viewBox="0 0 440 320" preserveAspectRatio="xMidYMid slice" class="activity-facts__route-svg">
                        <path class="activity-facts__route-line" d="M50 270 C 120 260, 150 210, 200 200 S 300 180, 330 120 400 75 405 45" fill="none"/>
                        <circle class="activity-facts__route-dot" cx="200" cy="200" r="5"/>
                        <circle class="activity-facts__route-dot" cx="330" cy="120" r="5"/>
                        <circle class="activity-facts__route-start" cx="50" cy="270" r="10"/>
                    </svg>
                </div>
            @endif
            <div class="activity-map-info-strip">
                <div class="activity-map-info-strip__stats">
                    <span class="activity-map-stat">
                        <flux:icon.arrows-right-left class="activity-map-stat__icon" aria-hidden="true" />
                        {{ $activity->distance ?? '—' }}
                    </span>
                    <span class="activity-map-stat">
                        <flux:icon.clock class="activity-map-stat__icon" aria-hidden="true" />
                        {{ $activity->duration_label ?? '—' }}
                    </span>
                </div>
                @if($activity->komoot_url)
                    <a href="{{ $activity->komoot_url }}" target="_blank" rel="noopener noreferrer" class="activity-map-komoot-link">
                        Bekijk op Komoot
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                @endif
            </div>
        </div>
    </article>

    {{-- UPDATES — invite people to follow this chapter's rides --}}
    <x-newsletter-optin :group="$activity->groups->first()" class="activity-updates h-full flex flex-col justify-center" />
</section>
```

- [ ] **Step 4: Update the Praktisch CSS**

In `resources/css/pages/activity.css`, replace the `.activity-facts`, `.activity-facts__body`, `.activity-facts__main`, `.activity-facts__meta`, `.activity-facts__map`, and `.activity-facts__route` rule blocks with:

```css
    /* ── PRAKTISCH — facts card paired with a "stay in the loop" card ─────────── */
    .activity-praktisch {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
        align-items: stretch;

        @media (min-width: 64rem) {
            grid-template-columns: minmax(0, 1.7fr) minmax(0, 1fr);
        }
    }
    .activity-facts {
        background: white;
        border: 1px solid var(--color-kidical-hairline);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        padding: clamp(1.75rem, 3.5vw, 2.6rem);
    }
    .activity-facts__title {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: var(--text-2xl);
        line-height: 1.1;
        color: var(--color-kidical-red);
        margin: 0 0 1.5rem;
    }
    .activity-facts__meta {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem 1.75rem;
        margin: 0;

        @media (min-width: 30rem) {
            grid-template-columns: 1fr 1fr;
        }
    }
    .activity-facts__meta-item {
        display: flex;
        align-items: center;
        gap: 0.9rem;
    }
    .activity-facts__meta-item dt {
        font-size: var(--text-xs);
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 45%);
        margin-bottom: 0.1rem;
    }
    .activity-facts__meta-item dd {
        margin: 0;
        font-size: var(--text-lg);
        font-weight: 700;
        color: var(--color-kidical-ink);
        line-height: 1.25;
    }
    /* Route panel — sits below the meta, full width inside the card */
    .activity-facts__map {
        position: relative;
        display: flex;
        flex-direction: column;
        margin-top: clamp(1.5rem, 3vw, 2rem);
        min-height: 16rem;
        border-radius: var(--radius-chip);
        overflow: hidden;
        background: var(--color-kidical-light-blue);
    }
    .activity-facts__route,
    .activity-facts__route-faux {
        flex: 1;
        min-height: 13rem;
    }
    /* Stylised brand route — the fallback when a ride has no GPX track yet */
    .activity-facts__route-svg {
        width: 100%;
        height: 100%;
        display: block;
    }
    .activity-facts__route-line {
        stroke: var(--color-kidical-yellow);
        stroke-width: 6;
        stroke-linecap: round;
    }
    .activity-facts__route-dot {
        fill: white;
        stroke: var(--color-kidical-ink);
        stroke-width: 2;
    }
    .activity-facts__route-start {
        fill: var(--color-kidical-red);
        stroke: white;
        stroke-width: 3;
    }
```

(`.activity-map-info-strip*`, `.activity-map-stat*`, `.activity-map-komoot-link` rules below stay unchanged. `<x-newsletter-optin>` already carries its own `bg-kidical-light-blue rounded-card p-8`; the `h-full flex flex-col justify-center` utilities make it fill the grid cell height beside the facts card — no `.activity-updates` rule is required, the class is just a stable hook.)

- [ ] **Step 5: Run the test**

Run: `php artisan test --compact --filter="always-on route"`
Expected: PASS — `activity-praktisch`, `activity-facts__route-faux`, full-date `juni`, and the updates card copy all present.

- [ ] **Step 6: Commit**

```bash
git add resources/views/activities/show.blade.php resources/css/pages/activity.css tests/Feature/RidePageRedesignTest.php
git commit -m "feat(ride): expand Praktisch — 2-col meta, full date, always-on route, updates card"
```

---

### Task 5: Team — compact real-volunteer row, drop pink-vest CTA

**Files:**
- Modify: `resources/views/activities/show.blade.php` (the `{{-- VAN EN VOOR DE BUURT --}}` `<section class="activity-team" ...>` block, lines ~178–220; add a `@php` block near the top of the file)
- Modify: `resources/css/pages/activity.css` (replace the team / volunteer / signup rule blocks)
- Test: `tests/Feature/RidePageRedesignTest.php`

**Interfaces:**
- Consumes: `$activity->groups->flatMap->users` (eager-loaded in Task 1), each user's `->name`; the brand illustration SVGs in `public/img/illustrations/`.
- Produces: team markup classes `activity-team__people`, `activity-team__member`, `activity-team__face`, `activity-team__first`. Removes all `activity-volunteer*`, `activity-team__signup`, `activity-team__back*`, `activity-team__person*` markup and the `<livewire:volunteer-signup>` usage.

- [ ] **Step 1: Write the failing test**

Append to `tests/Feature/RidePageRedesignTest.php`:

```php
it('shows a compact real-volunteer row and no pink-vest recruitment CTA', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Etterbeek', 'zip' => '1040']);
    $member = User::factory()->create(['name' => 'Marieke Janssens']);
    $group->users()->attach($member, ['role' => 'trekker', 'is_public' => true]);

    $ride = makeRide();
    $ride->groups()->attach($group);

    get(rideUrl($ride))
        ->assertOk()
        ->assertSee('activity-team__people', false)   // the compact avatar row
        ->assertSee('Marieke')                        // real member, first name only
        ->assertDontSee('Janssens')                   // surname dropped
        ->assertDontSee('Roze hesje worden?')         // pink-vest CTA removed
        ->assertDontSee('activity-volunteer', false)  // recruitment block gone
        ->assertDontSee('volunteer-signup', false);   // inline livewire reveal gone
});

it('hides the team section when the organising group has no members', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Leeg', 'zip' => '9000']);
    $ride = makeRide();
    $ride->groups()->attach($group);

    get(rideUrl($ride))
        ->assertOk()
        ->assertDontSee('activity-team__people', false);
});
```

- [ ] **Step 2: Run the test to verify it fails**

Run: `php artisan test --compact --filter="compact real-volunteer"`
Expected: FAIL — `activity-team__people` absent; `Roze hesje worden?` / `activity-volunteer` / `volunteer-signup` still present.

- [ ] **Step 3: Add the volunteer-derivation `@php` block**

Near the top of `resources/views/activities/show.blade.php`, just after the existing `@php($mainImage = ...)` lines, add:

```blade
@php
    // The organising group's real registered members, deduped across multiple groups.
    // No per-ride volunteer roster exists yet (GitHub #37 / D-1); avatars are the
    // deterministic brand illustrations, keyed by name so each person keeps the same one.
    $volunteers = $activity->groups->flatMap->users->unique('id')->values();

    $teamIllustrations = [
        'waving-rider', 'relaxed-rider', 'rider-with-flag',
        'volunteer-with-wrench', 'longtail-with-kid', 'cargo-bike-family',
    ];
    $illustrationFor = fn (string $name) => $teamIllustrations[crc32($name) % count($teamIllustrations)];
@endphp
```

- [ ] **Step 4: Replace the team section markup**

Replace the entire `<section class="activity-team" x-data="{ open: false }"> ... </section>` block with this static version (no Alpine reveal, no recruitment CTA, no livewire signup):

```blade
{{-- VAN EN VOOR DE BUURT — the crew that makes this parade roll --}}
<section class="activity-team">
    <p class="activity-eyebrow">Van en voor de buurt</p>

    @if($activity->groups->isNotEmpty())
        <p class="activity-team__lead">
            Georganiseerd door vrijwilligers van Kidical Mass
            @foreach($activity->groups as $group)
                <a href="{{ route('groups.show', $group) }}">{{ $group->name }}</a>@if(!$loop->last), @endif
            @endforeach
        </p>
    @endif

    @if($volunteers->isNotEmpty())
        <ul class="activity-team__people" role="list">
            @foreach($volunteers as $person)
                <li class="activity-team__member">
                    <span class="activity-team__face">
                        <img src="{{ asset('img/illustrations/'.$illustrationFor($person->name).'.svg') }}" alt="" aria-hidden="true">
                    </span>
                    <span class="activity-team__first">{{ \Illuminate\Support\Str::before(trim($person->name), ' ') }}</span>
                </li>
            @endforeach
        </ul>
    @endif
</section>
```

- [ ] **Step 5: Replace the team CSS**

In `resources/css/pages/activity.css`, **delete** these rule blocks: `.activity-team__person`, `.activity-team__person > div`, `.activity-team__avatar`, `.activity-team__name`, `.activity-team__role`, `.activity-volunteer`, `.activity-volunteer h3`, `.activity-volunteer p`, `.activity-volunteer p a`, `.activity-volunteer__btn`, `.activity-team__signup`, `.activity-team__back`, `.activity-team__back-icon`. **Keep** `.activity-team__lead` and `.activity-team__lead a`. After `.activity-team__lead a`, add:

```css
    /* Compact crew row — illustration avatar + first name, no roles */
    .activity-team__people {
        display: flex;
        flex-wrap: wrap;
        gap: clamp(1.25rem, 3vw, 2rem);
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .activity-team__member {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.6rem;
        width: 5.5rem;
        text-align: center;
    }
    .activity-team__face {
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 999px;
        background: var(--color-kidical-light-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }
    .activity-team__face img {
        width: 78%;
        height: 78%;
        object-fit: contain;
    }
    .activity-team__first {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: var(--text-base);
        color: var(--color-kidical-ink);
        line-height: 1.1;
    }
```

- [ ] **Step 6: Run the affected tests (this also greens Task 1's `Marieke` assertion)**

Run: `php artisan test --compact --filter=RidePageRedesignTest`
Expected: PASS — every test in the file, including the first-task `assertSee('Marieke')` (now that the team row renders) and the empty-group hide case.

- [ ] **Step 7: Commit**

```bash
git add resources/views/activities/show.blade.php resources/css/pages/activity.css tests/Feature/RidePageRedesignTest.php
git commit -m "feat(ride): compact real-volunteer row, drop pink-vest recruitment CTA"
```

---

### Task 6: Full verification, formatting, and pipeline note

**Files:**
- Verify only (no source changes beyond Pint formatting): all files touched above.
- Optionally update: `docs/wiki/design/30-skeleton/00-page-registry.md` + `docs/wiki/log.md` (see Step 4).

- [ ] **Step 1: Run Pint on the dirty files**

Run: `vendor/bin/pint --dirty --format agent`
Expected: PASS / files formatted. (Only PHP files — the test, controller. Blade/CSS are untouched by Pint.)

- [ ] **Step 2: Run the full affected test set**

Run: `php artisan test --compact --filter="RidePageRedesignTest|RideSurfacesSmokeTest|PublicPagesTest|CssArchitectureTest"`
Expected: ALL PASS. In particular:
- `CssArchitectureTest` — no new CSS partial was added (share-links reuses `.share-band__*`), so the import check is unaffected; `share-links.blade.php` has no raw hex/px in arbitrary classes or `style=` attributes.
- `RideSurfacesSmokeTest` — still sees `activity-head__` and `<h1`, still does not see `ride-spotlight`.

- [ ] **Step 3: Visual render check**

Build assets and view a real ride: `npm run build` (or confirm `npm run dev` is running), then load `activities.show` for a seeded ride. Confirm:
- Hero: yellow date·time eyebrow above the title, description as the lead, group logo + zip lockup, share links at the bottom; no old big-date or pin lockup.
- Praktisch: meta in two columns on wide screens, Startuur shows the full date + time, the route panel renders even for a ride without a GPX file, and the updates card sits beside the facts card (stacks on narrow screens).
- Team: a compact row of illustration avatars + first names; no "Roze hesje worden?" block, no inline signup.
- Two full-bleed colour bands only (blue hero + yellow closing slot).

Take **one** screenshot pass (per token-discipline) only if a visual change needs confirming.

- [ ] **Step 4 (optional, Frederik-gated): bump the page registry**

Per `/pipeline`: once Frederik has done his own critique pass, bump the ride page's `Wire`/`UI` stages in `docs/wiki/design/30-skeleton/00-page-registry.md`, trim its Top-gaps cell, reconcile the roll-up, and append a `## [2026-06-24] build | …` line to `docs/wiki/log.md`. Do not bump `Wire` to 🟢 on Claude's render check alone (tops out at 🟠).

- [ ] **Step 5: Final commit (if Pint reformatted anything)**

```bash
git add -p   # stage only the files this plan touched
git commit -m "style(ride): apply pint formatting"
```

> At `/wrap`, the per-thread commits above are squashed into one curated commit so `main` reads as a single unit of work. Guard against Nico's concurrent commits first (`git log <upstream>..HEAD --format='%an'`).

---

## Self-Review

**Spec coverage** (against the brief):
- HERO · omit date + location-with-pin → Task 3 (delete `.activity-head__date` + `.activity-head__chapter*`, asserted `assertDontSee`).
- HERO · zip on the logo for local-group rides → Task 3 (`.activity-head__org` lockup, `assertSee('1040')`).
- HERO · intro text → Task 3 (description as `<x-intro-text>` lead; body description removed). Decision 1.
- HERO · share links at the bottom → Tasks 2 + 3 (`<x-share-links>`, `assertSee('activity-head__share')`).
- HERO · date·time yellow eyebrow → Task 3 (`activity-head__eyebrow`, `assertSee('14:00')`).
- PRAKTISCH · route on the card like the group page → Task 4 (always-on route + faux fallback, `assertSee('activity-facts__route-faux')`).
- PRAKTISCH · full date in Startuur → Task 4 (`dateFull, timeLabel`, `assertSee('juni')`).
- PRAKTISCH · two-column meta → Task 4 (`.activity-facts__meta` grid).
- PRAKTISCH · updates card beside it → Task 4 (`<x-newsletter-optin>`, `assertSee('Mis geen rit')`).
- TEAM · real volunteer overview (all of them) → Tasks 1 + 5 (`groups.users`, `.activity-team__people`, `assertSee('Marieke')`). Decisions 2 + 3.
- Pink-vest CTA · omit → Task 5 (`assertDontSee('Roze hesje worden?')`, `assertDontSee('volunteer-signup')`). Decision 4.

**Placeholder scan:** none — every code step shows full markup/CSS; SVG paths are concrete; test bodies are complete.

**Type/name consistency:** `<x-share-links>` props (`url`, `title`, `date`, `message`, `subject`) match its usages in Tasks 2 (share-band) and 3 (hero). Hero classes (`activity-head__eyebrow/__lead/__org/__org-mark/__org-label/__org-name/__org-zip/__share`) match between the markup (Task 3, step 3) and CSS (Task 3, step 5). Praktisch classes (`activity-praktisch`, `activity-facts__route-faux/__route-svg/__route-line/__route-dot/__route-start`) match markup (step 3) and CSS (step 4). Team classes (`activity-team__people/__member/__face/__first`) match markup (Task 5, step 4) and CSS (step 5). `$volunteers` is defined once (Task 5, step 3) and consumed once (step 4). `makeRide()` / `rideUrl()` helpers are defined in Task 1 and reused throughout.

**Note on test ordering:** Task 1's `Marieke` assertion is intentionally left red until Task 5 (the data exists from Task 1, the rendering arrives in Task 5). If executing tasks strictly one-at-a-time with a green gate, fold Task 1's assertion verification into Task 5's run, or temporarily mark it `->todo()` and lift it in Task 5.

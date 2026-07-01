# Ride Page Lifecycle Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the ride detail page (`activities.show`) behave in three lifecycle states — upcoming, just-past (no photos), recap (photos up) — driven automatically by the ride's date and gallery.

**Architecture:** A new `RideLifecycleState` enum + `Activity` accessors compute the state from `begin_date` + `hasGallery()`. The existing `activities/show.blade.php` branches on that state: the hero gains a "Voorbij" marker, the future-tense promises drop, the gallery slot swaps between a photo-collection nudge (just-past) and a reusable `<x-ride-gallery>` wall (recap), the recap reorders so photos lead, the Deel copy shifts to "deel de herinnering", and the closing CTA points past-state visitors to the organising chapter. The chapter page's inline gallery is extracted into the shared `<x-ride-gallery>` and gains a "bekijk de hele rit" link to the recap.

**Tech Stack:** Laravel 13, PHP 8.4, Blade, Livewire/Flux (public site), Tailwind v4 + CSS partials, Alpine.js (lightbox), Spatie media-library, Pest 4.

## Global Constraints

- **Headings:** raw `<h1>`–`<h6>` only, never `flux:heading`.
- **CSS partials:** new CSS goes in `resources/css/components/<role>.css` (reusable) or `resources/css/pages/<page>.css` (single page); register every partial in the `@import` block in `resources/css/app.css`; never add to `app.css` directly. No raw hex/px in `.blade.php` components — use tokens (`bg-kidical-*`, `rounded-card`, `shadow-card`, etc.). Enforced by `tests/Feature/CssArchitectureTest.php`.
- **Copy:** Dutch, tone-of-voice per `docs/tone-of-voice.md`; **no em-dashes** in site copy.
- **Pint:** run `vendor/bin/pint --dirty --format agent` before each commit after touching PHP.
- **Tests:** every change is covered by a test; run `php artisan test --compact --filter=<name>`.
- **Shared checkout:** stage by explicit path, never `git add -A`. Do not push `main`.
- **Spec:** `docs/superpowers/specs/2026-06-24-ride-page-lifecycle-ux.md` (this plan implements it); surface look governed by `docs/superpowers/specs/2026-06-24-ride-page-redesign-design.md`.

---

## File Structure

**Create:**
- `app/Enums/RideLifecycleState.php` — the three-state enum + helpers.
- `resources/views/components/ride-gallery.blade.php` — reusable photo wall + Alpine lightbox (extracted from the chapter page).
- `resources/css/components/ride-gallery.css` — gallery wall + lightbox styles (moved from `chapters.css`).
- `resources/views/components/ride-photo-nudge.blade.php` — the just-past "deel je foto's" block.
- `resources/css/components/ride-photo-nudge.css` — its styles.
- `tests/Feature/RideLifecycleTest.php` — feature tests for the three show-page states.
- `tests/Unit/RideLifecycleStateTest.php` — unit tests for the enum/accessors.

**Modify:**
- `app/Models/Activity.php` — add `hasEnded()`, `lifecycleState()`, `isUpcoming()`, `isAwaitingPhotos()`, `isRecap()`.
- `database/factories/ActivityFactory.php` — add `past()` and `withGallery()` states.
- `resources/views/activities/show.blade.php` — branch on lifecycle state.
- `resources/css/pages/activity.css` — "Voorbij" hero marker + recap reorder tweaks.
- `resources/views/groups/show.blade.php` — replace inline IN BEELD block with `<x-ride-gallery>`, add the "bekijk de hele rit" link.
- `resources/css/pages/chapters.css` — remove the `.chapter-gallery*` rules now living in the component partial.
- `resources/css/app.css` — register the two new component partials in the `@import` block.

---

## Task 1: Lifecycle state — enum, model accessors, factory states

**Files:**
- Create: `app/Enums/RideLifecycleState.php`
- Modify: `app/Models/Activity.php` (add methods near the existing `isPast()`/`hasGallery()` at lines 203–230)
- Modify: `database/factories/ActivityFactory.php`
- Test: `tests/Unit/RideLifecycleStateTest.php`

**Interfaces:**
- Produces:
  - `App\Enums\RideLifecycleState` enum: cases `Upcoming`, `AwaitingPhotos`, `Recap` (string-backed: `'upcoming'`, `'awaiting_photos'`, `'recap'`).
  - `Activity::hasEnded(): bool` — true when `end_date ?? begin_date` is in the past.
  - `Activity::lifecycleState(): RideLifecycleState`
  - `Activity::isUpcoming(): bool`, `Activity::isAwaitingPhotos(): bool`, `Activity::isRecap(): bool`
  - Factory states: `ActivityFactory::past(int $days = 7): static`, `ActivityFactory::withGallery(int $count = 3): static`

- [ ] **Step 1: Create the enum**

Create `app/Enums/RideLifecycleState.php` (mirror the style of `app/Enums/ActivityType.php`):

```php
<?php

namespace App\Enums;

enum RideLifecycleState: string
{
    case Upcoming = 'upcoming';
    case AwaitingPhotos = 'awaiting_photos';
    case Recap = 'recap';

    public function isPastState(): bool
    {
        return $this !== self::Upcoming;
    }
}
```

- [ ] **Step 2: Add the factory states**

In `database/factories/ActivityFactory.php`, add two state methods. Two correctness requirements from review:
1. **`past()` must force `activity_type => KIDICALMASS`** — the factory randomizes type across 4 cases, and `ActivityController::show` only renders `activities.show` for rides (others get `show-basic`). Without this, the lifecycle markup never renders and tests fail ~75% of the time.
2. **`withGallery()` must prime the media cache itself.** `attachMultipleMedia` reads `static::$mediaCache['images']`, which is only populated inside `attachImages()` (called by `withMedia()`). No current test calls `withMedia()`, so in a fresh process the cache is empty and `withGallery()` would silently attach nothing (`hasGallery()` stays false). Prime it first:

```php
public function past(int $days = 7): static
{
    return $this->state(fn () => [
        'activity_type' => \App\Enums\ActivityType::KIDICALMASS,
        'begin_date' => now()->subDays($days)->setTime(14, 0),
        'duration_minutes' => 90,
        'published' => true,
    ]);
}

public function withGallery(int $count = 3): static
{
    return $this->afterCreating(function (\App\Models\Activity $activity) use ($count): void {
        $this->primeMediaCache('images', fn () => \Database\Seeders\MediaSeeder::ensureImages(5));
        $this->attachMultipleMedia($activity, 'gallery', $count, $count, 'images');
    });
}
```

Confirm `MediaSeeder`'s namespace (`Database\Seeders\MediaSeeder`) and `primeMediaCache`/`attachMultipleMedia` signatures against the existing `withMedia()` state and the `AttachesMediaFromCache` trait while implementing; mirror exactly what `withMedia()` does for the `gallery` collection, forcing count = `$count`.

- [ ] **Step 3: Write the failing unit test**

Create `tests/Unit/RideLifecycleStateTest.php`:

```php
<?php

use App\Enums\RideLifecycleState;
use App\Models\Activity;

it('reports upcoming for a future ride', function () {
    $ride = Activity::factory()->create(['begin_date' => now()->addWeek()]);

    expect($ride->lifecycleState())->toBe(RideLifecycleState::Upcoming)
        ->and($ride->isUpcoming())->toBeTrue()
        ->and($ride->isAwaitingPhotos())->toBeFalse()
        ->and($ride->isRecap())->toBeFalse();
});

it('reports awaiting-photos for a past ride with no gallery', function () {
    $ride = Activity::factory()->past()->create();

    expect($ride->lifecycleState())->toBe(RideLifecycleState::AwaitingPhotos)
        ->and($ride->isAwaitingPhotos())->toBeTrue()
        ->and($ride->isRecap())->toBeFalse();
});

it('reports recap for a past ride that has gallery photos', function () {
    $ride = Activity::factory()->past()->withGallery(3)->create();

    expect($ride->lifecycleState())->toBe(RideLifecycleState::Recap)
        ->and($ride->isRecap())->toBeTrue()
        ->and($ride->isAwaitingPhotos())->toBeFalse();
});

it('treats a ride with no duration as ended once begin_date passes', function () {
    $ride = Activity::factory()->create([
        'begin_date' => now()->subDay(),
        'duration_minutes' => null,
    ]);

    expect($ride->hasEnded())->toBeTrue();
});
```

- [ ] **Step 4: Run the test to verify it fails**

Run: `php artisan test --compact --filter=RideLifecycleStateTest`
Expected: FAIL — `Call to undefined method App\Models\Activity::lifecycleState()`.

- [ ] **Step 5: Add the accessors to the model**

In `app/Models/Activity.php`, add (next to `isPast()` / `hasGallery()` around lines 203–230). Add `use App\Enums\RideLifecycleState;` to the imports:

```php
public function hasEnded(): bool
{
    $end = $this->end_date ?? $this->begin_date;

    return $end !== null && $end->isPast();
}

public function lifecycleState(): RideLifecycleState
{
    if (! $this->hasEnded()) {
        return RideLifecycleState::Upcoming;
    }

    return $this->hasGallery()
        ? RideLifecycleState::Recap
        : RideLifecycleState::AwaitingPhotos;
}

public function isUpcoming(): bool
{
    return $this->lifecycleState() === RideLifecycleState::Upcoming;
}

public function isAwaitingPhotos(): bool
{
    return $this->lifecycleState() === RideLifecycleState::AwaitingPhotos;
}

public function isRecap(): bool
{
    return $this->lifecycleState() === RideLifecycleState::Recap;
}
```

- [ ] **Step 6: Run the test to verify it passes**

Run: `php artisan test --compact --filter=RideLifecycleStateTest`
Expected: PASS (4 tests).

- [ ] **Step 7: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Enums/RideLifecycleState.php app/Models/Activity.php database/factories/ActivityFactory.php tests/Unit/RideLifecycleStateTest.php
git commit -m "feat(rides): add ride lifecycle state (upcoming/awaiting-photos/recap)"
```

---

## Task 2: Extract the gallery into a reusable `<x-ride-gallery>` component

This lifts the chapter page's inline IN BEELD gallery (photo wall + Alpine lightbox) into a shared component used by both the chapter page and the recap state, and adds the spec's "bekijk de hele rit" link (decision #17a).

**Files:**
- Create: `resources/views/components/ride-gallery.blade.php`
- Create: `resources/css/components/ride-gallery.css`
- Modify: `resources/css/app.css` (register the partial)
- Modify: `resources/views/groups/show.blade.php` (replace IN BEELD block at lines 187–374; data prep at 80–95)
- Modify: `resources/css/pages/chapters.css` (remove `.chapter-gallery*` rules at lines 102–166 and 253–394)
- Test: `tests/Feature/PublicPagesTest.php` (existing chapter assertions must still pass; add the link assertion)

**Interfaces:**
- Produces: `<x-ride-gallery :photos="$mediaCollection" :title="..." :date="$carbon" :commune="..." :href="..." />`
  - `photos` — a Spatie `MediaCollection` (`$activity->getMedia('gallery')`), required, non-empty.
  - `title` (string) — feature-cell overlay title.
  - `date` (Carbon) — used to build the feature-cell date tear-off rail via `RideDate::rail($date)` (preserve the `--ride-day-rot` lockup the chapter page uses). Pass the Carbon instance, not a pre-formatted string.
  - `commune` (string|null) — woven into photo alt text (the chapter block uses `$gemeente`). Pass the organising group's commune/name, or null.
  - `href` (string|null, default null) — when set, renders a "Bekijk de hele rit" link; when null, no link (recap page is already the ride).
  - `card` (optional named slot) — extra in-grid cell (chapter passes its opt-in here; recap passes nothing).
  - The **per-photo rotating lightbox accent array** stays INTERNAL to the component (copy it verbatim from the chapter `x-data`); it is not a prop. Do not collapse it to a single colour.

- [ ] **Step 1: Create the component, moving markup out of the chapter page**

Create `resources/views/components/ride-gallery.blade.php`. Copy the gallery markup currently at `resources/views/groups/show.blade.php:193–373` (the `<section ... x-data="{…}">` Alpine lightbox block including the internal `accents` array, the `<ul class="chapter-gallery__grid">`, the feature cell with its `.chapter-latest*` lockup, the photo-cells loop, and the lightbox markup). Mirror the chapter block's `@php` data prep at `groups/show.blade.php:80–95` for the cover/tile derivation and rail. Then parameterise it:

```blade
@props([
    'photos',
    'title' => 'In beeld',
    'date' => null,
    'commune' => null,
    'href' => null,
])

@php
    $coverPhoto = $photos->first();
    $tilePhotos = $photos->slice(1)->values();
    // keep the chapter page's EXACT ragged-row cap (groups/show.blade.php:97): 9 or 5
    $tilePhotos = $tilePhotos->take($tilePhotos->count() >= 9 ? 9 : 5);
    $rideRail = $date ? \App\Support\RideDate::rail($date) : null;
@endphp

{{-- paste the adapted gallery + lightbox markup here, renamed per below --}}
```

While pasting, apply these transformations:
- Rename every `chapter-gallery` class to `ride-gallery` (e.g. `.chapter-gallery__grid` → `.ride-gallery__grid`) AND every feature-cell `chapter-latest` class to `ride-gallery__feature` (e.g. `.chapter-latest__cal` → `.ride-gallery__feature-cal`). Keep the markup structure identical otherwise.
- Keep the internal `accents: [...]` array and the `--lb-accent` per-photo rotation verbatim — it is the lightbox's rotating accent, not a single colour.
- Replace the chapter-specific cover/tile source (`$latestRide`-derived) with the `$coverPhoto`/`$tilePhotos` computed above from `$photos`.
- Replace the hard-coded "Recentste parade" feature title with `{{ $title }}`, and the tear-off lockup with `$rideRail` (preserve the `--ride-day-rot` style and `ride-day__rail/__day/__date/__month` structure — confirm `RideDate::rail()`'s return shape in `groups/show.blade.php:80–95`).
- Replace `{{ $gemeente }}` in photo `alt` text with `{{ $commune }}`.
- Replace the in-grid opt-in cell with `{{ $card ?? '' }}` so callers inject their own (or none).
- Use `getUrl('card')` for grid tiles/poster and full-size in the lightbox, exactly as the chapter block does.
- After the grid, add the conditional link:

```blade
@if($href)
    <a href="{{ $href }}" class="ride-gallery__link">
        Bekijk de hele rit
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
@endif
```

- [ ] **Step 2: Move the CSS into a component partial**

Create `resources/css/components/ride-gallery.css`. Move the **contiguous `102–394` range** from `resources/css/pages/chapters.css` — this is one block, not two: `.chapter-gallery*` (102–166), the feature-cell `.chapter-latest*` rules (167–252, including `.chapter-latest__cal .ride-day__rail`), and the tiles/lightbox/nav/counter rules with their responsive + `prefers-reduced-motion` overrides (253–394). Do NOT carve out 167–252 — that is the feature-poster styling the markup depends on. Wrap in `@layer components { … }`. Rename selectors `.chapter-gallery*` → `.ride-gallery*` and `.chapter-latest*` → `.ride-gallery__feature*` to match the renamed markup. Add a `.ride-gallery__link` rule (token-backed: reuse the link colour token used by `.activity-map-komoot-link` in `activity.css`). Delete the moved rules from `chapters.css`. Grep `chapter-gallery` and `chapter-latest` across the repo afterward to confirm nothing else references the old names.

- [ ] **Step 3: Register the partial**

In `resources/css/app.css`, add to the `@import` block (alongside the other `components/*` imports):

```css
@import './components/ride-gallery.css';
```

- [ ] **Step 4: Swap the chapter page over to the component**

In `resources/views/groups/show.blade.php`, replace the IN BEELD block (lines 187–374) with the component, preserving the section wrapper/eyebrow and passing the chapter's existing opt-in cell into the `card` slot and the recap link via `:href`:

Preserve the existing render guard (the chapter block currently keys off `$latestRide`/`$hasRideGallery` — reuse the same condition, do not invent a new one):

```blade
@if($latestRide && $latestRide->hasGallery())
    <section class="chapter-body">
        <p class="chapter-eyebrow">In beeld</p>
        <x-ride-gallery
            :photos="$latestRide->getMedia('gallery')"
            title="Recentste parade"
            :date="$latestRide->begin_date"
            :commune="$gemeente ?? $group->commune"
            :href="route('activities.show', $latestRide)">
            <x-slot:card>
                {{-- paste the existing opt-in cell markup from the old block here --}}
            </x-slot:card>
        </x-ride-gallery>
    </section>
@endif
```

Pass `:date` as the Carbon instance (the component builds the rail). Pass `:commune` using whatever variable the chapter block already uses for `$gemeente` (confirm its source in `groups/show.blade.php`). Remove the now-dead `$coverPhoto`/`$ridePhotos`/`$rideRail` variables from the `@php` block at 80–95 that were specific to the inline gallery; keep any still referenced elsewhere on the page.

- [ ] **Step 5: Run the chapter page tests + CSS architecture test**

Run: `php artisan test --compact --filter="PublicPagesTest|CssArchitectureTest"`
Expected: PASS. (CssArchitectureTest confirms the new partial is registered and has no raw hex/px; PublicPagesTest confirms the chapter page still renders.)

- [ ] **Step 6: Add a test asserting the chapter gallery links to the ride**

In `tests/Feature/PublicPagesTest.php`, add a test that a chapter with a past ride that has gallery photos renders a link to that ride's recap:

```php
it('links the chapter gallery to the full ride recap', function () {
    $group = \App\Models\Group::factory()->create();
    $ride = \App\Models\Activity::factory()->past()->withGallery(3)->create();
    $ride->groups()->attach($group);

    $this->get(route('groups.show', ['locale' => 'nl', 'group' => $group]))
        ->assertSee(route('activities.show', ['locale' => 'nl', 'activity' => $ride]), escape: false)
        ->assertSee('Bekijk de hele rit');
});
```

If the chapter↔ride association uses a different relationship setup, mirror how `PublicPagesTest` already builds a group with rides elsewhere in the file.

- [ ] **Step 7: Run the new test**

Run: `php artisan test --compact --filter=PublicPagesTest`
Expected: PASS.

- [ ] **Step 8: Visually verify (one screenshot pass)**

Load the chapter page for a group whose latest ride has photos and confirm the gallery wall + lightbox still work and the "Bekijk de hele rit" link appears. Use `get-absolute-url` for the URL.

- [ ] **Step 9: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/components/ride-gallery.blade.php resources/css/components/ride-gallery.css resources/css/app.css resources/views/groups/show.blade.php resources/css/pages/chapters.css tests/Feature/PublicPagesTest.php
git commit -m "refactor(gallery): extract reusable x-ride-gallery, link chapter wall to recap"
```

---

## Task 3: The just-past photo-collection nudge component

**Files:**
- Create: `resources/views/components/ride-photo-nudge.blade.php`
- Create: `resources/css/components/ride-photo-nudge.css`
- Modify: `resources/css/app.css` (register the partial)
- Test: covered by the show-page feature tests in Task 4 (the component renders only inside the page).

**Interfaces:**
- Produces: `<x-ride-photo-nudge :activity="$activity" />` — a contained block with a warm "dat was fijn, foto's volgen" line, a vest-facing "deel je foto's" instruction, a visitor-facing "bezorg ze aan de roze hesjes" instruction, and a quiet "schrijf je in zodat je de volgende rit niet mist" newsletter line.

- [ ] **Step 1: Create the component**

Create `resources/views/components/ride-photo-nudge.blade.php`. Use the project's contained-section rhythm and token-backed utilities (match `<x-support-callout :contained="true">`'s container treatment). Copy is indicative — tone-of-voice pass is separate, no em-dashes:

```blade
@props(['activity'])

<section class="ride-photo-nudge">
    <p class="ride-photo-nudge__eyebrow">Net gereden</p>
    <h2 class="ride-photo-nudge__title">Dat was fijn. De foto's volgen binnenkort.</h2>
    <p class="ride-photo-nudge__lead">
        Was je erbij en heb je foto's gemaakt? Bezorg ze aan de roze hesjes, dan
        verschijnen ze hier voor iedereen om na te genieten.
    </p>
</section>
```

Do NOT add a newsletter link here: the page already renders `<x-newsletter-optin>` as the "updates" card in `.activity-praktisch` for every state, so a second opt-in in the nudge would duplicate it. (If a newsletter line is ever wanted here, the route is `newsletter.show` — `route('newsletter')` does not exist.)

- [ ] **Step 2: Create the CSS partial**

Create `resources/css/components/ride-photo-nudge.css` with `@layer components { … }` rules for `.ride-photo-nudge*`, token-backed only (no raw hex/px). Reuse the eyebrow/lead type already defined for `.activity-eyebrow` and prose.

- [ ] **Step 3: Register the partial**

In `resources/css/app.css`, add to the `@import` block:

```css
@import './components/ride-photo-nudge.css';
```

- [ ] **Step 4: Run the CSS architecture test**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS (partial registered, no raw values).

- [ ] **Step 5: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/components/ride-photo-nudge.blade.php resources/css/components/ride-photo-nudge.css resources/css/app.css
git commit -m "feat(rides): add just-past photo-collection nudge component"
```

---

## Task 4: Branch the ride page on lifecycle state

Wire the three states into `activities/show.blade.php`: hero "Voorbij" marker, drop promises when past, swap the photo block, reorder the recap so the gallery leads, shift Deel copy, and point the closing CTA at the chapter for past states.

**Files:**
- Modify: `resources/views/activities/show.blade.php`
- Modify: `resources/css/pages/activity.css` (append a clearly-commented state block after line 653, inside `@layer components`)
- Test: `tests/Feature/RideLifecycleTest.php`

**Interfaces:**
- Consumes: `$activity->lifecycleState()`, `$activity->isUpcoming()`, `$activity->isAwaitingPhotos()`, `$activity->isRecap()` (Task 1); `<x-ride-gallery>` (Task 2); `<x-ride-photo-nudge>` (Task 3).

- [ ] **Step 1: Write the failing feature tests**

Create `tests/Feature/RideLifecycleTest.php`:

```php
<?php

use App\Models\Activity;

function showRide(Activity $ride)
{
    return test()->get(route('activities.show', ['locale' => 'nl', 'activity' => $ride]));
}

it('upcoming ride shows promises and the how-it-works CTA', function () {
    $ride = Activity::factory()->create([
        'activity_type' => \App\Enums\ActivityType::KIDICALMASS,
        'begin_date' => now()->addWeek(),
    ]);

    showRide($ride)
        ->assertSee('Wat kun je verwachten')
        ->assertSee('Lees hoe je meerijdt')
        ->assertDontSee('Net gereden');
});

it('just-past ride shows the photo nudge, drops promises, points to the chapter', function () {
    $group = \App\Models\Group::factory()->create();
    $ride = Activity::factory()->past()->create();
    $ride->groups()->attach($group);

    showRide($ride)
        ->assertSee('Net gereden')
        ->assertDontSee('Wat kun je verwachten')
        ->assertSee(route('groups.show', ['locale' => 'nl', 'group' => $group]), escape: false);
});

it('recap ride shows the gallery, drops promises, points to the chapter', function () {
    $group = \App\Models\Group::factory()->create();
    $ride = Activity::factory()->past()->withGallery(3)->create();
    $ride->groups()->attach($group);

    showRide($ride)
        ->assertSee('ride-gallery__grid', escape: false)
        ->assertDontSee('Wat kun je verwachten')
        ->assertDontSee('Net gereden')
        ->assertSee(route('groups.show', ['locale' => 'nl', 'group' => $group]), escape: false);
});
```

- [ ] **Step 2: Run the tests to verify they fail**

Run: `php artisan test --compact --filter=RideLifecycleTest`
Expected: FAIL (the past states currently render the upcoming layout — promises still present, no nudge/gallery, CTA still the how-it-works link).

- [ ] **Step 3: Compute the state once at the top of the view**

In `resources/views/activities/show.blade.php`, after the existing `@php` lines at the top, add:

```blade
@php($state = $activity->lifecycleState())
@php($isPast = $state->isPastState())
@php($primaryGroup = $activity->groups->first())
```

- [ ] **Step 4: Add the "Voorbij" hero marker**

In the `.activity-head__copy` block, as the first child (above the existing `<p class="activity-head__eyebrow">` date·time line), add a past-state marker:

```blade
@if($isPast)
    <p class="activity-head__past">Voorbij</p>
@endif
```

The hero already renders the description inline via `<x-intro-text class="activity-head__lead">` and a `<x-share-links>` row — leave those untouched in all states.

- [ ] **Step 5: Swap the photo block / reorder for recap**

Replace the body so the photo block appears in the right slot per state. The recap puts the gallery first (above Praktisch); just-past puts the nudge first; upcoming keeps today's order with no photo block. Wrap the existing PRAKTISCH/DESCRIPTION sections so order can differ:

```blade
@if($activity->isRecap())
    <x-ride-gallery
        :photos="$activity->getMedia('gallery')"
        title="In beeld"
        :date="$activity->begin_date->translatedFormat('j F')" />
@elseif($activity->isAwaitingPhotos())
    <x-ride-photo-nudge :activity="$activity" />
@endif
```

Place this block immediately after the hero, as the first child of `.activity-stack`, before the `<section class="activity-praktisch">` wrapper (which holds the facts card + the existing `<x-newsletter-optin>` "updates" card). That satisfies the spec's "gallery leads the body" for the recap. Note: there is **no separate Beschrijving section in the stack** — the description renders inside the hero via `<x-intro-text>`, so it is already above everything; the spec's "gallery → beschrijving → praktisch" middle-ordering is moot given current markup and needs no extra reorder. Just ensure the gallery sits first in `.activity-stack`.

- [ ] **Step 6: Drop the promises on past states**

Wrap the WAT KUN JE VERWACHTEN section (lines ~143–176) so it only renders when upcoming:

```blade
@unless($isPast)
    <section class="activity-promises">
        {{-- existing promises markup --}}
    </section>
@endunless
```

- [ ] **Step 7: Shift the Deel copy for past states**

Update the `<x-share-band>` invocation (lines ~226–230). **Do not pass `:heading="null"`** — Blade `@props` defaults only apply when the attribute is *absent*; passing `null` overrides the default with an empty `<h2>`. Branch the markup so the upcoming state omits the override entirely, and the past state sets both `heading` and `subline` (the spec shifts both):

```blade
@if($isPast)
    <x-share-band
        :url="route('activities.show', $activity)"
        :title="$activity->title_nl"
        :date="$activity->begin_date->translatedFormat('l j F')"
        heading="Deel de herinnering"
        subline="Laat anderen zien hoe fijn het was."
        :contained="true" />
@else
    <x-share-band
        :url="route('activities.show', $activity)"
        :title="$activity->title_nl"
        :date="$activity->begin_date->translatedFormat('l j F')"
        :contained="true" />
@endif
```

Confirm `share-band.blade.php` exposes a `subline` prop (it does per its `@props`); if the wording needs tone work, that is a separate copy pass.

- [ ] **Step 8: Point the closing CTA at the chapter for past states**

Replace the `<x-slot:closing>` block (lines ~262–265) so past states link to the organising chapter:

```blade
<x-slot:closing>
    @if($isPast && $primaryGroup)
        <x-closing-cta
            heading="Meer ritten van Kidical Mass {{ $primaryGroup->name }}?"
            :href="route('groups.show', $primaryGroup)"
            label="Ontdek de groep" />
    @else
        <x-closing-cta heading="Nog niet zeker hoe het werkt?"
            :href="route('getting-started')" label="Lees hoe je meerijdt" />
    @endif
</x-slot:closing>
```

- [ ] **Step 9: Add the hero-marker + recap CSS**

In `resources/css/pages/activity.css`, append after line 653 (inside the `@layer components`), a commented state block with `.activity-head__past` (a small uppercase token-coloured badge) and any recap-order tweak you used in Step 5. Token-backed only.

- [ ] **Step 10: Run the feature tests**

Run: `php artisan test --compact --filter=RideLifecycleTest`
Expected: PASS (3 tests).

- [ ] **Step 11: Run the existing show-page tests (regression guard)**

Run: `php artisan test --compact --filter="PublicPagesTest|RideSurfacesSmokeTest|CssArchitectureTest"`
Expected: PASS. These assert the **upcoming** state still emits `activity-head__` chrome and the support callout — confirm the branching did not disturb the upcoming layout.

- [ ] **Step 12: Visually verify all three states (one screenshot pass)**

Create three rides (future; past no gallery; past with gallery) and screenshot each show page. Confirm: upcoming unchanged; just-past shows the nudge, no promises, chapter CTA; recap leads with the gallery wall, no promises, chapter CTA. Use the project `scripts/screenshot.cjs` helper if present.

- [ ] **Step 13: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/activities/show.blade.php resources/css/pages/activity.css tests/Feature/RideLifecycleTest.php
git commit -m "feat(rides): branch ride page into upcoming/just-past/recap states"
```

---

## Task 5: Full-suite check + pipeline status

**Files:**
- Modify (docs): `docs/wiki/design/30-skeleton/00-page-registry.md`, `docs/wiki/log.md` (per `/pipeline`).

- [ ] **Step 1: Run the whole suite**

Run: `php artisan test --compact`
Expected: PASS. (Note: `CalendarProximityTest` is a known order-dependent flake — if only it fails, re-run it in isolation to confirm it is not a regression.)

- [ ] **Step 2: Build assets and final visual check**

Run: `npm run build`, then reload the three ride states once more to confirm the compiled CSS matches.

- [ ] **Step 3: Update the build pipeline (optional, via `/pipeline`)**

If Frederik has done his own critique pass, bump the ride page (P-nn) Wire/UI stages in the page registry, update Top gaps + Roll-up, and append a `## [2026-06-24] build | …` entry to `docs/wiki/log.md`. Otherwise leave at 🟠.

---

## Self-Review

**Spec coverage** (against `2026-06-24-ride-page-lifecycle-ux.md`):
- Three content-driven states (`hasGallery()`) → Task 1 ✓
- Hero "Voorbij" marker → Task 4 Step 4 ✓
- Drop promises on past → Task 4 Step 6 ✓
- Photo block swap (nudge ↔ gallery), gallery leads on recap → Task 3 + Task 4 Step 5 ✓
- Praktisch kept as record → unchanged in Blade (still rendered) ✓
- Deel "deel de herinnering" → Task 4 Step 7 ✓
- Slot-CTA → chapter (primary) + newsletter secondary → Task 4 Step 8 (chapter) + Task 3 (newsletter line in nudge) ✓
- Chapter "In beeld" wall keeps lightbox AND links to recap → Task 2 Steps 4 + 6 ✓
- Latest-recap-only, lag accepted, no per-chapter archive → no work needed (explicitly out of scope) ✓
- Reuse chapter gallery layout → Task 2 (extracted shared component) ✓

**Codebase review applied (2026-06-24).** A fresh agent verified every assumption against the code. Fixes folded in: (1) `past()` + upcoming tests force `activity_type => KIDICALMASS` so the ride template renders; (2) `withGallery()` primes the media cache before attaching; (3) the gallery CSS move is the contiguous `102–394` range incl. `.chapter-latest*`; (4) `route('newsletter')` → `route('newsletter.show')`; (5) `<x-share-band>` is branched rather than passed `:heading="null"` (Blade defaults don't apply to explicit null), and the past state shifts `subline` too; (6) `<x-ride-gallery>` prop contract widened (rotating accent kept internal, Carbon `date` for the rail, `commune` for alt text). Confirmed safe: route names + locale prefix, `Activity::groups()` belongsToMany, component props, no method-name collisions.

**Residual risks for execution:**
- Task 2 (gallery extraction + chapter swap) remains the highest-risk task — it edits a shipped page and threads a rich prop contract. Its tests + screenshot guard it. Documented fallback if it proves too invasive: duplicate the layout into the component and defer the chapter swap (accepting temporary duplication), keeping decision #17's link as a tiny standalone edit to the inline block.
- Recap newsletter "secondary": **already satisfied** — the page now renders `<x-newsletter-optin :group="$activity->groups->first()">` as the "updates" card inside `.activity-praktisch` in every state (added in the concurrent redesign). So the just-past nudge's own newsletter line (Task 3 Step 1) is redundant; drop it from the nudge to avoid two opt-ins, OR keep the nudge line and confirm the duplication reads acceptably. No separate recap opt-in needed.
- Confirm during execution: `MediaSeeder` namespace + `primeMediaCache`/`attachMultipleMedia` signatures, `RideDate::rail()` return shape, and the chapter block's `$gemeente` source.

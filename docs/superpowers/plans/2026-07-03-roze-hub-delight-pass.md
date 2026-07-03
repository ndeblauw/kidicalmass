# Roze-hub Delight Pass Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the roze-hub Overview moment-aware: greeting by name, Monday-after recap card, ride countdown, new-member celebration, staggered entrance motion.

**Architecture:** A new `App\Support\RozeHub\OverviewMoment` support class (sibling of `HubTabs`) resolves ONE lead moment per page load (welcome > recap > pre-ride > default) and owns the countdown wording. `RozeHesjeController::overview()` feeds it; the Blade layer owns all greeting strings. One new component (`<x-roze-recap-card>`); everything else extends existing files.

**Tech Stack:** Laravel 13, Pest 4, Blade components, spatie/laravel-medialibrary, Tailwind 4 tokens in CSS partials.

**Spec:** `docs/superpowers/specs/2026-07-03-roze-hub-delight-pass-design.md` — read it first.

## Global Constraints

- NL copy only, **no em-dashes** anywhere (tone-of-voice rule).
- CSS: tokens only, no raw hex/px; additions go in `resources/css/pages/chapters-roze-hesjes.css` (page styles) and `resources/css/effects.css` (keyframes only). **Never touch `resources/css/app.css`** except nothing here needs it.
- Tests assert rendered text and `data-*`/BEM seams, never Tailwind utilities or keyframe names in HTML (see `docs/testing-conventions.md`).
- Run `vendor/bin/pint --dirty --format agent` before every commit.
- Shared checkout with another committer: `git add` by **explicit path only**, never `-A`. Do not push.
- Test runs: `php artisan test --compact --filter=<name>` (targeted, not the whole suite).
- Welcome-window cookie in tests: `->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())` closes the window; no cookie = window open.
- `CalendarProximityTest` is known-flaky in full-suite runs; not a signal from this work.

---

### Task 1: `OverviewMoment` support class

**Files:**
- Create: `app/Support/RozeHub/OverviewMoment.php`
- Test: `tests/Unit/OverviewMomentTest.php`

**Interfaces:**
- Consumes: `App\Models\Activity` (unsaved instances fine — casts work), `App\Enums\ActivityType::isRide()`.
- Produces (later tasks rely on these exact signatures):
  - `OverviewMoment::resolve(bool $showWelcome, ?Activity $recapRide, ?Activity $nextRide): string` → `'welcome' | 'recap' | 'pre-ride' | 'default'`
  - `OverviewMoment::countdownLabel(Activity $nextRide): ?string`
  - `OverviewMoment::RECAP_DAYS = 5`, `OverviewMoment::PRE_RIDE_DAYS = 7`

- [ ] **Step 1: Write the failing unit test**

Create `tests/Unit/OverviewMomentTest.php` (Unit = no DB; all Activity instances stay unsaved):

```php
<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Support\RozeHub\OverviewMoment;
use Illuminate\Support\Carbon;

beforeEach(fn () => Carbon::setTestNow('2026-07-01 10:00:00')); // a Wednesday
afterEach(fn () => Carbon::setTestNow());

function ride(string $beginDate, ActivityType $type = ActivityType::KIDICALMASS): Activity
{
    return new Activity(['begin_date' => $beginDate, 'activity_type' => $type]);
}

test('moment priority: welcome beats recap beats pre-ride beats default', function () {
    $recap = ride('2026-06-28 14:00:00');
    $next = ride('2026-07-05 14:00:00');

    expect(OverviewMoment::resolve(true, $recap, $next))->toBe('welcome')
        ->and(OverviewMoment::resolve(false, $recap, $next))->toBe('recap')
        ->and(OverviewMoment::resolve(false, null, $next))->toBe('pre-ride')
        ->and(OverviewMoment::resolve(false, null, null))->toBe('default');
});

test('a far-away or non-ride next activity does not make a pre-ride moment', function () {
    expect(OverviewMoment::resolve(false, null, ride('2026-07-20 14:00:00')))->toBe('default')
        ->and(OverviewMoment::resolve(false, null, ride('2026-07-05 19:30:00', ActivityType::MEETING)))->toBe('default');
});

test('countdown wording follows the nights until the ride', function () {
    expect(OverviewMoment::countdownLabel(ride('2026-07-01 14:00:00')))->toBe('Vandaag rijden we!')
        ->and(OverviewMoment::countdownLabel(ride('2026-07-02 14:00:00')))->toBe('Morgen is het zover.')
        ->and(OverviewMoment::countdownLabel(ride('2026-07-06 14:00:00')))->toBe('Nog 5 nachtjes slapen.')
        ->and(OverviewMoment::countdownLabel(ride('2026-07-08 14:00:00')))->toBe('Nog 7 nachtjes slapen.');
});

test('countdown stays silent beyond a week and for meetings', function () {
    expect(OverviewMoment::countdownLabel(ride('2026-07-09 14:00:00')))->toBeNull()
        ->and(OverviewMoment::countdownLabel(ride('2026-07-02 19:30:00', ActivityType::MEETING)))->toBeNull();
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=OverviewMomentTest`
Expected: FAIL — `Class "App\Support\RozeHub\OverviewMoment" not found`

- [ ] **Step 3: Write the implementation**

Create `app/Support/RozeHub/OverviewMoment.php`:

```php
<?php

namespace App\Support\RozeHub;

use App\Models\Activity;

/**
 * Which single "moment" leads the hub Overview. The page picks exactly one
 * (welcome > recap > pre-ride > default) so busy weeks stay calm: one lead
 * moment, everything else in its normal section. Greeting strings live in the
 * Blade layer (overzicht.blade.php); this class only decides and counts.
 */
class OverviewMoment
{
    /** Recap leads while the last ride (with photos) is at most this many days old. */
    public const RECAP_DAYS = 5;

    /** Pre-ride greeting + countdown appear within this many nights of the next ride. */
    public const PRE_RIDE_DAYS = 7;

    /** @return 'welcome'|'recap'|'pre-ride'|'default' */
    public static function resolve(bool $showWelcome, ?Activity $recapRide, ?Activity $nextRide): string
    {
        if ($showWelcome) {
            return 'welcome';
        }

        if ($recapRide !== null) {
            return 'recap';
        }

        if ($nextRide !== null && $nextRide->activity_type->isRide() && self::nightsUntil($nextRide) <= self::PRE_RIDE_DAYS) {
            return 'pre-ride';
        }

        return 'default';
    }

    /**
     * The playful countdown for the next-ride card. Rides only: "nachtjes slapen"
     * before a vergadering would be odd, so meetings render no line at all.
     */
    public static function countdownLabel(Activity $nextRide): ?string
    {
        if (! $nextRide->activity_type->isRide()) {
            return null;
        }

        $nights = self::nightsUntil($nextRide);

        return match (true) {
            $nights === 0 => 'Vandaag rijden we!',
            $nights === 1 => 'Morgen is het zover.',
            $nights <= self::PRE_RIDE_DAYS => "Nog {$nights} nachtjes slapen.",
            default => null,
        };
    }

    /** Whole nights between today and the ride day (0 on the day itself). */
    private static function nightsUntil(Activity $ride): int
    {
        return (int) now()->startOfDay()->diffInDays($ride->begin_date->copy()->startOfDay());
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=OverviewMomentTest`
Expected: PASS (4 tests)

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Support/RozeHub/OverviewMoment.php tests/Unit/OverviewMomentTest.php
git commit -m "feat(roze-hub): OverviewMoment resolver + countdown wording"
```

---

### Task 2: Greeting header on the Overview

**Files:**
- Modify: `app/Http/Controllers/RozeHesjeController.php:24-41` (`overview()`)
- Modify: `resources/views/groups/roze-hesjes/overzicht.blade.php`
- Modify: `resources/css/pages/chapters-roze-hesjes.css` (small spacing rule)
- Modify: `tests/Feature/RozeHesjeHubTest.php` (one test's assertions change with behaviour — update, do NOT delete)
- Test: `tests/Feature/RozeHubDelightTest.php` (new)

**Interfaces:**
- Consumes: `OverviewMoment::resolve()` / `::countdownLabel()` from Task 1.
- Produces for later tasks: view receives `'moment' => string`, `'recapRide' => ?Activity` (always `null` in this task; Task 3 fills it), `'countdown' => ?string`. Blade header `<header class="roze-greeting" data-moment="{{ $moment }}">` is the test seam.

- [ ] **Step 1: Write the failing feature tests**

Create `tests/Feature/RozeHubDelightTest.php`:

```php
<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;

use function Pest\Laravel\actingAs;

function delightHubUrl(Group $group): string
{
    return route('groups.roze-hesjes', ['locale' => 'nl', 'group' => $group]);
}

function establishedMember(Group $group, string $name = 'Lien Govaerts'): User
{
    $member = User::factory()->create(['name' => $name]);
    $group->users()->attach($member, ['role' => 'pinkvest']);
    // Pivot created outside the welcome window so the member card is not "new".
    $group->users()->updateExistingPivot($member->id, [
        'created_at' => now()->subMonths(3),
        'updated_at' => now()->subMonths(3),
    ]);

    return $member;
}

test('a new hesje is greeted with the welcome moment on first visit', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create(['name' => 'Lien Govaerts']);
    $group->users()->attach($member, ['role' => 'pinkvest']);

    actingAs($member)->get(delightHubUrl($group))
        ->assertSee('data-moment="welcome"', escape: false)
        ->assertSee('Welkom bij de hesjes, Lien.')
        ->assertSee('Fijn dat je meerijdt.');
});

test('an established hesje gets the default greeting when nothing is happening', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek']);
    $member = establishedMember($group);

    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertSee('data-moment="default"', escape: false)
        ->assertSee('Dag Lien.')
        ->assertSee('Dit is wat er leeft in Schaarbeek.');
});

test('a ride within a week makes the pre-ride moment with its weekday', function () {
    $group = Group::factory()->create();
    $member = establishedMember($group);
    $ride = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDays(3),
        'is_published' => true,
    ]);
    $ride->groups()->attach($group);

    $weekday = ucfirst(\App\Support\RideDate::weekday($ride->begin_date));

    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertSee('data-moment="pre-ride"', escape: false)
        ->assertSee("{$weekday} rijden we.");
});
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --compact --filter=RozeHubDelightTest`
Expected: FAIL — response does not contain `data-moment` (view has no greeting header yet)

- [ ] **Step 3: Wire the moment through the controller**

In `app/Http/Controllers/RozeHesjeController.php`, add the import and replace `overview()`:

```php
use App\Support\RozeHub\OverviewMoment;
```

```php
    public function overview(string $locale, Group $group): View
    {
        $context = $this->hubContext($group);

        // The next published ride (own chapter + its region/country lineage) anchors
        // the front door with something live every visit, not just the welcome block.
        $nextRide = Activity::query()
            ->published()
            ->with(['author', 'groups'])
            ->whereHas('groups', fn ($query) => $query->whereIn('groups.id', $this->lineageIds($group)))
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->first();

        $recapRide = null; // Task 3 fills this with the Monday-after album ride.

        return view('groups.roze-hesjes.overzicht', [
            ...$context,
            'nextRide' => $nextRide,
            'recapRide' => $recapRide,
            'moment' => OverviewMoment::resolve($context['showWelcome'], $recapRide, $nextRide),
            'countdown' => $nextRide ? OverviewMoment::countdownLabel($nextRide) : null,
            'feed' => $this->feed($group),
        ]);
    }
```

(`'countdown'` stays unused by the view until Task 4; passing it now keeps this the only controller edit.)

- [ ] **Step 4: Add the greeting header to the view**

In `resources/views/groups/roze-hesjes/overzicht.blade.php`:

1. Change the wrapper open tag to pass `own-heading` (the greeting is now the real h1):

```blade
<x-roze-hub :group="$group" active="overzicht" :is-captain="$isCaptain" :show-welcome="$showWelcome" :beheer-url="$beheerUrl" :own-heading="true">
```

2. Extend the existing `@php` block and insert the header as the FIRST child inside `<div class="roze-overview">` (inside, not above — Task 6's stagger targets `.roze-overview > *`):

```blade
    @php
        $nextRail = $nextRide ? \App\Support\RideDate::rail($nextRide->begin_date) : null;
        $gemeente = \Illuminate\Support\Str::of($group->name)->replaceMatches('/^\s*kidical\s+mass\s+/i', '')->trim();
        $firstName = \Illuminate\Support\Str::before(auth()->user()->name, ' ');
        // One lead moment (welcome > recap > pre-ride > default); strings live here,
        // the decision lives in OverviewMoment. No em-dashes in copy.
        $greeting = match ($moment) {
            'welcome' => ['title' => "Welkom bij de hesjes, {$firstName}.", 'lead' => 'Fijn dat je meerijdt. Begin bij Aan de slag, of kijk gewoon even rond.'],
            'recap' => ['title' => "Dag {$firstName}.", 'lead' => 'Dat was een mooie '.\App\Support\RideDate::weekday($recapRide->begin_date).'.'],
            'pre-ride' => ['title' => "Dag {$firstName}.", 'lead' => ucfirst(\App\Support\RideDate::weekday($nextRide->begin_date)).' rijden we.'],
            default => ['title' => "Dag {$firstName}.", 'lead' => "Dit is wat er leeft in {$gemeente}."],
        };
    @endphp

    <div class="roze-overview">
        <header class="roze-greeting" data-moment="{{ $moment }}">
            <h1 class="roze-hub-title">{{ $greeting['title'] }}</h1>
            <p class="roze-hub-lead">{{ $greeting['lead'] }}</p>
        </header>
```

3. In `resources/css/pages/chapters-roze-hesjes.css`, inside the existing `@layer components { … }` near the `.roze-overview` rules (`:335`), add:

```css
    /* 7 · GREETING — the overview's visible h1: the room greets you by name */
    .roze-greeting .roze-hub-lead {
        margin: 0.375rem 0 0;
    }
```

- [ ] **Step 5: Update the one existing test whose behaviour intentionally changed**

In `tests/Feature/RozeHesjeHubTest.php`, the test `the overview shows no welcome panel, even on a first visit` asserts `->assertDontSee('Fijn dat je meerijdt')`. The welcome moment now deliberately shows that line as the greeting (spec section "Composition model"). Update the test (do not delete it — its panel assertion still guards the removed `roze-hub-welcome` block):

```php
test('the overview greets a first-time visitor without the old welcome panel', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    // The welcome *panel* stays gone; the welcome now lives in the greeting header.
    actingAs($member)->get(hubUrl('groups.roze-hesjes', $group))
        ->assertDontSee('roze-hub-welcome', escape: false)
        ->assertSee('data-moment="welcome"', escape: false)
        ->assertSee('Fijn dat je meerijdt');
});
```

- [ ] **Step 6: Run the affected tests**

Run: `php artisan test --compact --filter="RozeHubDelightTest|RozeHesjeHubTest"`
Expected: PASS. If `RozeHesjesLivingHubTest` or `RozeHubComponentTest` assert overview copy, run them too: `php artisan test --compact --filter="RozeHesjes|RozeHub"` — the only legitimate breakage mode is an `assertSee`/`assertDontSee` on overview copy that the greeting changed; fix the assertion to the new copy, never by weakening a seam.

- [ ] **Step 7: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/RozeHesjeController.php resources/views/groups/roze-hesjes/overzicht.blade.php resources/css/pages/chapters-roze-hesjes.css tests/Feature/RozeHubDelightTest.php tests/Feature/RozeHesjeHubTest.php
git commit -m "feat(roze-hub): moment-aware greeting header on the overview"
```

---

### Task 3: The Monday-after recap card

**Files:**
- Create: `resources/views/components/roze-recap-card.blade.php`
- Modify: `app/Http/Controllers/RozeHesjeController.php` (`overview()` — fill `$recapRide`)
- Modify: `resources/views/groups/roze-hesjes/overzicht.blade.php` (render card in lead slot)
- Modify: `resources/css/pages/chapters-roze-hesjes.css`
- Test: `tests/Feature/RozeHubDelightTest.php` (extend)

**Interfaces:**
- Consumes: `'recapRide' => ?Activity` view slot from Task 2; `OverviewMoment::RECAP_DAYS`; `RideDate::weekday()`.
- Produces: `<x-roze-recap-card :ride :href />` — computes photo, count, and weekday internally from `$ride`. Card root is `<a class="roze-recap">`.

- [ ] **Step 1: Write the failing feature tests**

Append to `tests/Feature/RozeHubDelightTest.php` (uses `Illuminate\Http\UploadedFile` — add the import at the top of the file):

```php
use Illuminate\Http\UploadedFile;

function recapRideForGroup(Group $group, int $daysAgo, bool $withPhoto = true): Activity
{
    $ride = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->subDays($daysAgo),
        'is_published' => true,
    ]);
    $ride->groups()->attach($group);

    if ($withPhoto) {
        $ride->addMedia(UploadedFile::fake()->image('rit.jpg', 40, 30))->toMediaCollection('gallery');
    }

    return $ride;
}

test('a fresh ride with photos leads the overview as the recap moment', function () {
    $group = Group::factory()->create();
    $member = establishedMember($group);
    $ride = recapRideForGroup($group, daysAgo: 4);

    $weekday = \App\Support\RideDate::weekday($ride->begin_date);

    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertSee('data-moment="recap"', escape: false)
        ->assertSee("Dat was 'm.")
        ->assertSee("1 foto's van de rit van {$weekday} staan in het album");
});

test('the recap steps aside after the window or without photos', function () {
    $group = Group::factory()->create();
    $member = establishedMember($group);
    recapRideForGroup($group, daysAgo: 6);                    // too old
    recapRideForGroup($group, daysAgo: 2, withPhoto: false);  // fresh but photo-less

    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertDontSee("Dat was 'm.")
        ->assertDontSee('data-moment="recap"', escape: false);
});

test('the personal welcome outranks the recap', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create(['name' => 'Sara Janssens']);
    $group->users()->attach($member, ['role' => 'pinkvest']);
    recapRideForGroup($group, daysAgo: 2);

    // No cookie: the welcome window is open, so Sara's first Monday greets HER, not the album.
    actingAs($member)->get(delightHubUrl($group))
        ->assertSee('data-moment="welcome"', escape: false)
        ->assertSee('Welkom bij de hesjes, Sara.')
        ->assertDontSee("Dat was 'm.");
});
```

Note the deliberately ungrammatical `1 foto's` assertion: the card always says "foto's". A singular-count album is a seeded-data edge, not worth a pluralisation branch (YAGNI) — but the assertion documents it.

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --compact --filter=RozeHubDelightTest`
Expected: the 3 new tests FAIL (`data-moment="recap"` never rendered — `$recapRide` is still hardcoded null)

- [ ] **Step 3: Fill `$recapRide` in the controller**

In `RozeHesjeController::overview()`, replace `$recapRide = null; // Task 3 fills this…` with:

```php
        // The Monday-after moment: the chapter's own most recent ride, at most
        // RECAP_DAYS old, that already has album photos. No photos = no recap
        // (the feed's photo card still covers late uploads). Stateless — no cookies.
        $recapRide = Activity::query()
            ->published()
            ->with('media')
            ->whereHas('groups', fn ($query) => $query->whereKey($group->id))
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->whereBetween('begin_date', [now()->subDays(OverviewMoment::RECAP_DAYS), now()])
            ->whereHas('media', fn ($query) => $query->where('collection_name', 'gallery'))
            ->orderByDesc('begin_date')
            ->first();
```

- [ ] **Step 4: Create the component**

Create `resources/views/components/roze-recap-card.blade.php`:

```blade
@props([
    'ride', // past Activity with a non-empty gallery collection
    'href', // the chapter's Foto's page (its picker already defaults to this newest album)
])

@php
    $photo = $ride->getFirstMedia('gallery');
    $count = $ride->getMedia('gallery')->count();
    $weekday = \App\Support\RideDate::weekday($ride->begin_date);
@endphp

<a href="{{ $href }}" class="roze-recap">
    <span class="roze-recap__frame">
        {{ $photo->img('card', ['class' => 'roze-recap__img', 'alt' => "Sfeerbeeld van de rit van {$weekday}", 'loading' => 'eager']) }}
    </span>
    <span class="roze-recap__body">
        <h2 class="roze-recap__title">Dat was 'm.</h2>
        <span class="roze-recap__meta">{{ $count }} foto's van de rit van {{ $weekday }} staan in het album.</span>
        <span class="roze-recap__cta">Bekijk het album
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
        </span>
    </span>
</a>
```

- [ ] **Step 5: Render it in the lead-card slot**

In `overzicht.blade.php`, directly after the closing `</header>` of the greeting (still inside `.roze-overview`, before the `@if ($nextRide)` section):

```blade
        @if ($moment === 'recap')
            <x-roze-recap-card :ride="$recapRide" :href="route('groups.roze-hesjes.fotos', [$group, 'ride' => $recapRide->id])" />
        @endif
```

- [ ] **Step 6: Style the card**

In `resources/css/pages/chapters-roze-hesjes.css`, after the greeting rule from Task 2, still inside `@layer components`. Surface values mirror `.roze-next` / the feed cards (white ground, `--radius-lg`-family tokens, float shadow); check `.roze-next` (`:353`) and reuse ITS exact background/radius/shadow token names if they differ from these:

```css
    /* 8 · RECAP — the Monday-after moment: one big photo leads the page */
    .roze-recap {
        display: block;
        background-color: white;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-float);
        padding: 0.625rem;
        text-decoration: none;
        transition: transform 0.18s var(--ease-brand), box-shadow 0.18s var(--ease-brand);
    }

    .roze-recap:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
    }

    .roze-recap__frame {
        display: block;
        border-radius: var(--radius-md);
        overflow: hidden;
        aspect-ratio: 16 / 10;
    }

    .roze-recap__img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .roze-recap__body {
        display: block;
        padding: 0.875rem 0.5rem 0.375rem;
    }

    .roze-recap__title {
        font-family: var(--font-heading);
        font-weight: 800;
        font-synthesis: none;
        font-size: 1.3rem;
        color: var(--color-kidical-ink);
        margin: 0;
    }

    .roze-recap__meta {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.9rem;
        color: var(--color-text-body);
    }

    .roze-recap__cta {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        margin-top: 0.5rem;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--color-kidical-red);
    }

    @media (prefers-reduced-motion: reduce) {
        .roze-recap { transition: none; }
    }
```

- [ ] **Step 7: Run the tests**

Run: `php artisan test --compact --filter="RozeHubDelightTest|CssArchitectureTest"`
Expected: PASS (CssArchitecture guards the no-raw-hex rule; `white` keyword and tokens are fine — if it flags anything, swap to the token `.roze-next` uses)

- [ ] **Step 8: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/RozeHesjeController.php resources/views/components/roze-recap-card.blade.php resources/views/groups/roze-hesjes/overzicht.blade.php resources/css/pages/chapters-roze-hesjes.css tests/Feature/RozeHubDelightTest.php
git commit -m "feat(roze-hub): Monday-after recap card leads the overview"
```

---

### Task 4: Countdown on "Je volgende rit"

**Files:**
- Modify: `resources/views/groups/roze-hesjes/overzicht.blade.php` (next-ride card)
- Modify: `resources/css/pages/chapters-roze-hesjes.css`
- Test: `tests/Feature/RozeHubDelightTest.php` (extend)

**Interfaces:**
- Consumes: `'countdown' => ?string` already passed by the controller since Task 2.

- [ ] **Step 1: Write the failing feature tests**

Append to `tests/Feature/RozeHubDelightTest.php`:

```php
test('the next-ride card counts down the nights to a nearby ride', function () {
    $group = Group::factory()->create();
    $member = establishedMember($group);
    $ride = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDays(3)->setTime(14, 0),
        'is_published' => true,
    ]);
    $ride->groups()->attach($group);

    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertSee('Nog 3 nachtjes slapen.');
});

test('a meeting gets no countdown line', function () {
    $group = Group::factory()->create();
    $member = establishedMember($group);
    $meeting = Activity::factory()->create([
        'activity_type' => ActivityType::MEETING,
        'begin_date' => now()->addDays(2)->setTime(19, 30),
        'is_published' => true,
    ]);
    $meeting->groups()->attach($group);

    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertDontSee('nachtjes slapen')
        ->assertDontSee('roze-next__count', escape: false);
});
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --compact --filter=RozeHubDelightTest`
Expected: the 2 new tests FAIL (countdown line not rendered)

- [ ] **Step 3: Render the line**

In `overzicht.blade.php`, inside `<span class="roze-next__body">`, after the existing `roze-next__meta` span:

```blade
                        @if ($countdown)
                            <span class="roze-next__count">{{ $countdown }}</span>
                        @endif
```

- [ ] **Step 4: Style it**

In `chapters-roze-hesjes.css`, next to the `.roze-next__meta` rules (around `:353-385`):

```css
    .roze-next__count {
        display: block;
        margin-top: 0.25rem;
        font-weight: 700;
        font-size: 0.85rem;
        color: var(--color-kidical-red);
    }
```

- [ ] **Step 5: Run the tests, pint, commit**

Run: `php artisan test --compact --filter=RozeHubDelightTest`
Expected: PASS

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/groups/roze-hesjes/overzicht.blade.php resources/css/pages/chapters-roze-hesjes.css tests/Feature/RozeHubDelightTest.php
git commit -m "feat(roze-hub): nachtjes-slapen countdown on the next-ride card"
```

---

### Task 5: New-member celebration on the feed card

**Files:**
- Modify: `app/Http/Controllers/RozeHesjeController.php` (`feed()` signature + member item)
- Modify: `resources/views/components/roze-feed-card.blade.php` (celebrate prop)
- Modify: `resources/views/groups/roze-hesjes/overzicht.blade.php` (pass the prop)
- Modify: `resources/css/effects.css` (one keyframe), `resources/css/pages/chapters-roze-hesjes.css` (celebrate styling)
- Test: `tests/Feature/RozeHubDelightTest.php` (extend)

**Interfaces:**
- Consumes: feed array shape from `feed()`; `<x-roze-feed-card>` props.
- Produces: feed items gain `'celebrate' => bool`; card root gains `data-celebrate` when true (the test seam).

- [ ] **Step 1: Write the failing feature tests**

Append to `tests/Feature/RozeHubDelightTest.php`:

```php
test('a new member gets a celebrating feed card with a hello nudge when a ride is coming', function () {
    $group = Group::factory()->create();
    $viewer = establishedMember($group);
    $newbie = User::factory()->create(['name' => 'Sara Janssens']);
    $group->users()->attach($newbie, ['role' => 'pinkvest']);
    $ride = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDays(4),
        'is_published' => true,
    ]);
    $ride->groups()->attach($group);

    $weekday = \App\Support\RideDate::weekday($ride->begin_date);

    actingAs($viewer)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertSee('data-celebrate', escape: false)
        ->assertSee("Sara Janssens rijdt nu mee als roze hesje. Zeg {$weekday} zeker hallo.");
});

test('without an upcoming ride the member card celebrates without the hello nudge', function () {
    $group = Group::factory()->create();
    $viewer = establishedMember($group);
    $newbie = User::factory()->create(['name' => 'Sara Janssens']);
    $group->users()->attach($newbie, ['role' => 'pinkvest']);

    actingAs($viewer)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertSee('data-celebrate', escape: false)
        ->assertSee('Sara Janssens rijdt nu mee als roze hesje')
        ->assertDontSee('zeker hallo');
});
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --compact --filter=RozeHubDelightTest`
Expected: the 2 new tests FAIL (`data-celebrate` absent)

- [ ] **Step 3: Extend `feed()`**

In `RozeHesjeController`:

1. `overview()`: change the call to `'feed' => $this->feed($group, $nextRide),`
2. `feed()`: change the signature and the member item:

```php
    private function feed(Group $group, ?Activity $nextRide = null): array
```

```php
        if ($newMember) {
            // A joining hesje is the feed's one celebration; the hello nudge only
            // appears when there is an actual ride to say hello at (rides only,
            // and never a nudge toward a vergadering).
            $what = "{$newMember->name} rijdt nu mee als roze hesje";
            if ($nextRide !== null && $nextRide->activity_type->isRide()) {
                $weekday = \App\Support\RideDate::weekday($nextRide->begin_date);
                $what .= ". Zeg {$weekday} zeker hallo.";
            }

            $items->push([
                'color' => 'red',
                'icon' => 'user-plus',
                'what' => $what,
                'context' => 'Nieuw lid',
                'timestamp' => $newMember->pivot->created_at->toDateString(),
                'relative' => $newMember->pivot->created_at->diffForHumans(),
                'href' => route('groups.roze-hesjes.groep', $group),
                'celebrate' => true,
            ]);
        }
```

3. Add `'celebrate' => false` to the other two feed items (photos, draft) so the array shape is uniform, and update the `@return` PHPDoc shape to include `celebrate: bool`.

- [ ] **Step 4: Thread the prop through the card**

`resources/views/components/roze-feed-card.blade.php` — add to `@props`:

```blade
    'celebrate' => false, // one-time chip pop for feel-good events (new member)
```

and change the root element open tag to:

```blade
<a href="{{ $href }}" class="roze-feed" @if ($celebrate) data-celebrate @endif>
```

`overzicht.blade.php` — add to the `<x-roze-feed-card` call, after `:color="$item['color']"`:

```blade
                        :celebrate="$item['celebrate']"
```

- [ ] **Step 5: The chip pop**

`resources/css/effects.css` — add next to `check-pop` (`:22`). A dedicated keyframe because `check-pop` ends at `transform: scale(1)`, which would erase the chip's signature −3° tilt; this one keeps the rotation in both frames:

```css
/* Icon-chip entrance for celebrating feed cards (new member). Keeps the chip's
   intrinsic -3° tilt in both frames so the fill state never flattens it. */
@keyframes chip-pop {
    from { opacity: 0; transform: scale(0.4) rotate(-3deg); }
    to   { opacity: 1; transform: scale(1) rotate(-3deg); }
}
```

`resources/css/pages/chapters-roze-hesjes.css` — near the `.roze-feeds` rules (`:418`):

```css
    /* Celebrating feed cards (new member): the chip pops in once. */
    @media (prefers-reduced-motion: no-preference) {
        .roze-feed[data-celebrate] [data-icon-chip] {
            animation: chip-pop 0.4s var(--ease-brand) 0.45s both;
        }
    }
```

(The 0.45s delay lets Task 6's card fade-up land first so the pop reads as a beat, not a glitch. `[data-icon-chip]` is the chip component's root attribute — see `resources/views/components/icon-chip.blade.php:38`.)

- [ ] **Step 6: Run tests, pint, commit**

Run: `php artisan test --compact --filter="RozeHubDelightTest|RozeHesjeHubTest|CssArchitectureTest"`
Expected: PASS (RozeHesjeHubTest's feed test asserts `rijdt nu mee als roze hesje`, which is still a substring of the new copy)

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/RozeHesjeController.php resources/views/components/roze-feed-card.blade.php resources/views/groups/roze-hesjes/overzicht.blade.php resources/css/effects.css resources/css/pages/chapters-roze-hesjes.css tests/Feature/RozeHubDelightTest.php
git commit -m "feat(roze-hub): celebrate a new hesje in the feed"
```

---

### Task 6: Entrance motion + final verification

**Files:**
- Modify: `resources/css/pages/chapters-roze-hesjes.css`
- No new tests (motion is styling; the seams are already covered). Existing suites must stay green.

- [ ] **Step 1: Staggered fade-up on the Overview**

In `chapters-roze-hesjes.css`, with the `.roze-overview` rules (`:335`). Sections stagger 70ms apart; the three feed cards continue the cascade. `fade-up` already exists in `effects.css:27`:

```css
    /* Page entrance: the overview's blocks arrive as a gentle cascade (greeting
       first, then each section), so opening the hub feels like walking into a
       room, not loading a dashboard. Opt-in via no-preference; reduced motion
       gets the page instantly. */
    @media (prefers-reduced-motion: no-preference) {
        .roze-overview > * {
            animation: fade-up 0.45s var(--ease-brand) both;
        }

        .roze-overview > :nth-child(2) { animation-delay: 70ms; }
        .roze-overview > :nth-child(3) { animation-delay: 140ms; }
        .roze-overview > :nth-child(4) { animation-delay: 210ms; }
        .roze-overview > :nth-child(5) { animation-delay: 280ms; }

        .roze-feeds__list > .roze-feed { animation: fade-up 0.45s var(--ease-brand) both; }
        .roze-feeds__list > .roze-feed:nth-child(1) { animation-delay: 280ms; }
        .roze-feeds__list > .roze-feed:nth-child(2) { animation-delay: 350ms; }
        .roze-feeds__list > .roze-feed:nth-child(3) { animation-delay: 420ms; }
    }
```

- [ ] **Step 2: Build assets and verify the render**

```bash
npm run build
node scripts/screenshot.cjs   # sanity that the helper env works
```

Then screenshot the hub as a member (the hub auto-logs-in a demo member outside production; group binding is by ID — Schaarbeek is id 3 in the local DB):

Write a throwaway script per the global screenshot pattern (Write tool, `.cjs`, scratchpad) that logs in as `pinkvest@kidi.be` / `password` and captures `/nl/chapters/3/roze-hesjes` desktop + mobile. Verify: greeting h1 present, recap card or countdown visible depending on seeded data, feed cards staggered in (screenshot after `networkidle` catches the settled state), nothing overlapping.

- [ ] **Step 3: Full roze + css test pass**

Run: `php artisan test --compact --filter="RozeHesje|RozeHub|CssArchitectureTest"`
Expected: ALL PASS. (`CalendarProximityTest` flake in broader runs is pre-existing; ignore.)

- [ ] **Step 4: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/css/pages/chapters-roze-hesjes.css
git commit -m "feat(roze-hub): staggered entrance on the overview"
```

---

## After the last task

- The thread's commits stay as checkpoints; at `/wrap` they squash into ONE curated commit (guard against Nico's interleaved commits first: `git log origin/main..HEAD --format='%an'`).
- Offer Frederik the pipeline bump for the hub page row (Wire stays at his-critique gate; this pass touches UI/Surface).
- Deferred (spec's out-of-scope list): sub-page motion, pride stats band, lightbox/upload, milestones — all future threads.

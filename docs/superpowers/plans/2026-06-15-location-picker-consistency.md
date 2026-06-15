# Location-picker consistency + home volgende-rit states — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the location picker look and sit consistently across home, calendar and groups, and rework the home "volgende rit" section so it always shows rides with a paired picker / "alle ritten" affordance.

**Architecture:** The compact picker skin becomes a `.location-picker--compact` modifier driven by a `compact` prop, so it works anywhere. A new `<x-filter-bar>` Blade component (the full-bleed panel-top shell, CSS in `components/filter-bar.css`) hosts the picker on calendar + groups; the radius tabs stay agenda-only, passed into the bar's slot. Home keeps the picker in-section with `:compact`, inside a reworked three-state section fed by a new `upcoming_preview` from `NextRideFinder`.

**Tech Stack:** Laravel 12, Livewire 4, Blade, Tailwind v4 (CSS partials), Pest 4.

**Spec:** `docs/superpowers/specs/2026-06-15-location-picker-consistency-design.md`

---

## File structure

- **Modify** `app/Livewire/LocationPicker.php` — add `compact` prop (Task 1).
- **Modify** `resources/views/livewire/location-picker.blade.php` — modifier class (Task 1).
- **Modify** `resources/css/components/location-picker.css` — re-key compact overrides; drop the duplicate `__sep` (Task 3).
- **Create** `resources/views/components/filter-bar.blade.php` — bar shell (Task 2).
- **Create** `resources/css/components/filter-bar.css` — `.filter-bar*` shell + radius tabs (Task 2).
- **Modify** `resources/css/app.css` — `@import` the new partial (Task 2).
- **Modify** `resources/views/livewire/ride-calendar.blade.php` — use `<x-filter-bar>` (Task 3).
- **Modify** `resources/css/pages/calendar.css` — remove `.kal-filterrow*` (Task 3).
- **Modify** `resources/views/groups/index.blade.php` — move picker to panel top (Task 4).
- **Modify** `app/Support/Location/NextRideFinder.php` — add `upcoming_preview` (Task 5).
- **Modify** `app/Http/Controllers/HomeController.php` — pass `$upcomingRides` (Task 5).
- **Modify** `resources/views/home.blade.php` — three-state rework (Task 6).
- **Modify** `resources/css/pages/home.css` — paired-row appearance (Task 6).

Notes for the engineer:
- Public routes are under a `{locale}` prefix; use `route('home')`, `route('activities.index')`, `route('groups.index')` — they resolve the locale automatically. The agenda route name is `activities.index`; the calendar Livewire class is `App\Livewire\RideCalendar`.
- The location cookie name is `config('location.cookie')`; its value is JSON `{"zip","lat","lng","name"}`.
- Run a single test file with: `php artisan test --compact tests/Feature/Xxx.php`.
- After any PHP change, run `vendor/bin/pint --dirty --format agent` before committing.
- Frontend changes need `npm run build` to show in the browser (not required for tests).

---

## Task 1: `compact` prop on LocationPicker

**Files:**
- Modify: `app/Livewire/LocationPicker.php`
- Modify: `resources/views/livewire/location-picker.blade.php:1` (root element)
- Test: `tests/Feature/LocationPickerCompactTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/LocationPickerCompactTest.php`:

```php
<?php

use App\Livewire\LocationPicker;
use Livewire\Livewire;

test('picker gets the compact modifier only when compact is set', function () {
    Livewire::test(LocationPicker::class, ['compact' => true])
        ->assertSeeHtml('location-picker--compact');

    Livewire::test(LocationPicker::class)
        ->assertDontSeeHtml('location-picker--compact');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/LocationPickerCompactTest.php`
Expected: FAIL — `compact` is not a public property / class never appears.

- [ ] **Step 3: Add the prop**

In `app/Livewire/LocationPicker.php`, add below `public bool $editing = false;`:

```php
    public bool $compact = false;
```

- [ ] **Step 4: Add the modifier class**

In `resources/views/livewire/location-picker.blade.php`, the root element currently has `class="location-picker …"`. Add the conditional modifier to that class list:

```blade
class="location-picker {{ $compact ? 'location-picker--compact' : '' }} …"
```

Keep every other existing class on that element exactly as-is; only insert the `{{ $compact ? … }}` expression.

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --compact tests/Feature/LocationPickerCompactTest.php`
Expected: PASS

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/LocationPicker.php resources/views/livewire/location-picker.blade.php tests/Feature/LocationPickerCompactTest.php
git commit -m "feat(location-picker): add compact modifier prop"
```

---

## Task 2: `<x-filter-bar>` component + CSS partial

This is additive — nothing renders the bar yet, so no page changes. The compact overrides are still keyed to `.kal-filterrow` at this point (Task 3 re-keys them).

**Files:**
- Create: `resources/views/components/filter-bar.blade.php`
- Create: `resources/css/components/filter-bar.css`
- Modify: `resources/css/app.css` (add the `@import`)
- Test: `tests/Feature/FilterBarTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/FilterBarTest.php`:

```php
<?php

test('filter bar renders its wrapper and its slot', function () {
    $view = $this->blade('<x-filter-bar><span>SLOTTED</span></x-filter-bar>');

    $view->assertSee('filter-bar', false); // wrapper class
    $view->assertSee('SLOTTED');           // slotted controls
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/FilterBarTest.php`
Expected: FAIL — `Unable to locate a class or view for component [filter-bar]`.

- [ ] **Step 3: Create the component**

Create `resources/views/components/filter-bar.blade.php`:

```blade
{{-- Full-bleed filter bar pinned to the top of a page panel. Hosts the compact
     location picker; pass extra controls (e.g. the agenda radius tabs) as the slot. --}}
<div class="filter-bar">
    <div class="filter-bar__loc">
        <livewire:location-picker :compact="true" />
    </div>

    {{ $slot }}
</div>
```

- [ ] **Step 4: Create the CSS partial**

Create `resources/css/components/filter-bar.css`. Copy the shell + tab rules from
`resources/css/pages/calendar.css` and rename `kal-filterrow` → `filter-bar`
throughout. The exact content (from calendar.css lines 47–118 for the shell/tabs and
258–276 for the responsive block, both inside `@layer components`):

```css
@layer components {
    .filter-bar {
        display: flex;
        align-items: center;
        background: white;
        border-bottom: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 92%);
        padding: 0.75rem 2rem;
        margin: 0 -2rem;
        gap: 1.5rem;
    }
    .filter-bar__loc {
        flex: 0 1 auto;
        min-width: 0;
    }
    .filter-bar__sep {
        display: flex;
        align-items: center;
        flex-shrink: 0;
        width: auto;
        height: auto;
        background: none;
        margin: 0;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 65%);
        font-size: var(--text-base);
    }
    .filter-bar__radius {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 0.35rem;
        flex-shrink: 0;
    }
    .filter-bar__radius-label {
        font-family: var(--font-sans);
        font-size: var(--text-xs);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 52%);
        flex-shrink: 0;
    }
    .filter-bar__tabs {
        display: flex;
        gap: 4px;
    }
    .filter-bar__tab {
        padding: 0.35rem 0.85rem;
        font-family: var(--font-sans);
        font-size: var(--text-sm);
        font-weight: 700;
        border-radius: 6px;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 30%);
        background: color-mix(in oklab, var(--color-kidical-ink), transparent 93%);
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
        border: none;
    }
    .filter-bar__tab:hover {
        background: color-mix(in oklab, var(--color-kidical-blue), transparent 88%);
        color: var(--color-kidical-blue);
    }
    .filter-bar__tab--active {
        background: var(--color-kidical-blue);
        color: white;
    }

    @media (max-width: 767px) {
        .filter-bar {
            flex-wrap: wrap;
            gap: 0.75rem 1rem;
            padding: 0.75rem 1rem;
            margin: 0 -1rem;
        }
        .filter-bar__sep {
            display: none;
        }
        .filter-bar__radius {
            flex-wrap: wrap;
        }
        .filter-bar__tabs {
            flex-wrap: wrap;
        }
    }
}
```

(The `.kal-filterrow__radius-hint` and `.kal-filterrow__tabs--disabled` rules are not
used by any markup and are intentionally dropped — do not carry them over.)

- [ ] **Step 5: Register the partial in app.css**

In `resources/css/app.css`, in the role-based partials `@import` block, add next to the
other `components/` imports (alphabetical-ish, near `location-picker.css`):

```css
@import './components/filter-bar.css';
```

- [ ] **Step 6: Run tests to verify they pass**

Run: `php artisan test --compact tests/Feature/FilterBarTest.php tests/Feature/CssArchitectureTest.php`
Expected: PASS — the component renders, and the new partial is registered + has no raw hex/px in a component blade (the blade has none).

- [ ] **Step 7: Commit**

```bash
git add resources/views/components/filter-bar.blade.php resources/css/components/filter-bar.css resources/css/app.css tests/Feature/FilterBarTest.php
git commit -m "feat(filter-bar): add panel-top filter bar component + css"
```

---

## Task 3: Migrate the calendar to `<x-filter-bar>` (atomic re-key)

Done as one commit so the calendar never loses its compact skin: the markup swap (which
passes `:compact`) and the override re-key land together.

**Files:**
- Modify: `resources/views/livewire/ride-calendar.blade.php:8-38`
- Modify: `resources/css/pages/calendar.css` (remove `.kal-filterrow*`)
- Modify: `resources/css/components/location-picker.css` (re-key overrides; drop dup `__sep`)
- Test: `tests/Feature/CalendarFilterBarTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/CalendarFilterBarTest.php`:

```php
<?php

use Illuminate\Support\Facades\File;

test('calendar renders the shared filter bar, not the old kal-filterrow', function () {
    $response = $this->get(route('activities.index'));

    $response->assertOk();
    $response->assertSee('filter-bar', false);
    $response->assertDontSee('kal-filterrow', false);
});

test('no kal-filterrow rules remain in the stylesheets', function () {
    expect(File::get(resource_path('css/pages/calendar.css')))->not->toContain('kal-filterrow');
    expect(File::get(resource_path('css/components/location-picker.css')))->not->toContain('kal-filterrow');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/CalendarFilterBarTest.php`
Expected: FAIL — `kal-filterrow` still present in markup and CSS.

- [ ] **Step 3: Swap the calendar markup**

In `resources/views/livewire/ride-calendar.blade.php`, replace the whole filter-row
block (currently lines 8–38, the `@if ($when !== 'voorbije')` wrapping `.kal-filterrow`)
with:

```blade
        {{-- Filter row: shared bar + agenda-only radius tabs. Hidden on past-rides view. --}}
        @if ($when !== 'voorbije')
            <x-filter-bar>
                @if ($location)
                    <div class="filter-bar__radius">
                        <span class="filter-bar__sep" aria-hidden="true">·</span>
                        <span class="filter-bar__radius-label">Hoe ver</span>
                        <div class="filter-bar__tabs">
                            <button
                                type="button"
                                wire:click="setRadius('dichtbij')"
                                class="filter-bar__tab{{ $radius === 'dichtbij' ? ' filter-bar__tab--active' : '' }}"
                            >Dichtbij</button>
                            <button
                                type="button"
                                wire:click="setRadius('regio')"
                                class="filter-bar__tab{{ $radius === 'regio' ? ' filter-bar__tab--active' : '' }}"
                            >In de regio</button>
                            <button
                                type="button"
                                wire:click="setRadius('belgie')"
                                class="filter-bar__tab{{ $radius === 'belgie' ? ' filter-bar__tab--active' : '' }}"
                            >Heel België</button>
                        </div>
                    </div>
                @endif
            </x-filter-bar>
        @endif
```

- [ ] **Step 4: Remove `.kal-filterrow*` from calendar.css**

In `resources/css/pages/calendar.css`, delete these now-duplicated rules (they live in
`filter-bar.css` now):
- The shell + tab rules `.kal-filterrow`, `.kal-filterrow__loc`, `.kal-filterrow__sep`, `.kal-filterrow__radius`, `.kal-filterrow__radius-hint`, `.kal-filterrow__radius-label`, `.kal-filterrow__tabs`, `.kal-filterrow__tabs--disabled`, `.kal-filterrow__tab`, `.kal-filterrow__tab:hover`, `.kal-filterrow__tab--active` (lines ~47–118 inside the first `@layer components`).
- The responsive `.kal-filterrow*` rules in the `@media (max-width: 767px)` block (lines ~262–276).

Leave all other calendar rules (`.kal-body`, `.kal-day`, `.kal-sidebar`, etc.) untouched.

- [ ] **Step 5: Re-key the compact overrides in location-picker.css**

In `resources/css/components/location-picker.css`, in the compact-override block
(~lines 236–333):
- Replace every `.kal-filterrow .location-picker` descendant prefix with
  `.location-picker--compact .location-picker` (e.g. `.kal-filterrow .location-picker__input` → `.location-picker--compact .location-picker__input`; `.kal-filterrow .location-picker` → `.location-picker--compact.location-picker` — note: this last one is the picker root itself, so it becomes the **compound** `.location-picker--compact` rule; simplest is to write it as `.location-picker--compact {`).
- Delete the standalone `.kal-filterrow__sep { … }` rule entirely (it is a bar element, now defined in `filter-bar.css`).
- Keep the explanatory comment about these rules being unlayered.

After this edit, grep confirms zero `kal-filterrow` occurrences in the file.

- [ ] **Step 6: Run tests to verify they pass**

Run: `php artisan test --compact tests/Feature/CalendarFilterBarTest.php tests/Feature/CssArchitectureTest.php`
Expected: PASS

- [ ] **Step 7: Build + commit**

```bash
npm run build
git add resources/views/livewire/ride-calendar.blade.php resources/css/pages/calendar.css resources/css/components/location-picker.css tests/Feature/CalendarFilterBarTest.php
git commit -m "refactor(calendar): use shared filter bar; re-key compact picker skin"
```

---

## Task 4: Migrate groups to `<x-filter-bar>`

**Files:**
- Modify: `resources/views/groups/index.blade.php:15-19`
- Test: `tests/Feature/GroupsFilterBarTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/GroupsFilterBarTest.php`:

```php
<?php

test('groups index shows the filter bar in the panel, without radius tabs', function () {
    $response = $this->get(route('groups.index'));

    $response->assertOk();
    $response->assertSee('filter-bar', false);
    $response->assertDontSee('grp-hero__locate', false);
    $response->assertDontSee('filter-bar__tab', false); // radius tabs are agenda-only
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/GroupsFilterBarTest.php`
Expected: FAIL — `grp-hero__locate` still present, `filter-bar` absent.

- [ ] **Step 3: Move the picker out of the hero**

In `resources/views/groups/index.blade.php`, remove the `<x-slot:controls>` block
(the `.grp-hero__locate` wrapper around `<livewire:location-picker />`) and instead place
the bar as the first child of the page-hero default slot, immediately above
`<x-intro-text>`:

```blade
    <x-page-hero
        eyebrow="Lokale groepen"
        title="Jouw buurt fietst al, rij mee."
        illustration="img/illustrations/longtail-with-kid.svg">

        <x-filter-bar />

        <x-intro-text> … </x-intro-text>
```

(Keep the existing `<x-intro-text>` content and everything after it unchanged.)

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact tests/Feature/GroupsFilterBarTest.php`
Expected: PASS

- [ ] **Step 5: Build + commit**

```bash
npm run build
git add resources/views/groups/index.blade.php tests/Feature/GroupsFilterBarTest.php
git commit -m "refactor(groups): move location picker into the panel-top filter bar"
```

---

## Task 5: `upcoming_preview` from NextRideFinder + HomeController

**Files:**
- Modify: `app/Support/Location/NextRideFinder.php`
- Modify: `app/Http/Controllers/HomeController.php:17-23`
- Test: `tests/Feature/NextRidePreviewTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/NextRidePreviewTest.php`:

```php
<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Support\Location\NextRideFinder;

test('finder returns a grouped preview of upcoming rides', function () {
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDays(3),
    ]);
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDays(10),
    ]);

    $result = NextRideFinder::find(null);

    expect($result['has_upcoming'])->toBeTrue()
        ->and($result['ride'])->toBeNull()                 // no location → no single ride
        ->and($result['upcoming_preview'])->toBeArray()
        ->and($result['upcoming_preview'])->not->toBeEmpty();

    // shape: ['Y-m-d' => [['item' => Activity], …], …]
    $firstDay = array_values($result['upcoming_preview'])[0];
    expect($firstDay[0]['item'])->toBeInstanceOf(Activity::class);
});

test('preview is empty when there are no upcoming rides', function () {
    expect(NextRideFinder::find(null)['upcoming_preview'])->toBe([]);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/NextRidePreviewTest.php`
Expected: FAIL — `Undefined array key "upcoming_preview"`.

- [ ] **Step 3: Add the preview to the finder**

In `app/Support/Location/NextRideFinder.php`:

Update the docblock return shape to add `upcoming_preview: array<string, array<int, array{item: Activity}>>`.

In the empty branch, add the key:

```php
        if ($upcoming->isEmpty()) {
            return ['ride' => null, 'distance_km' => null, 'is_far' => false, 'has_upcoming' => false, 'upcoming_preview' => []];
        }
```

After the empty check (before the `if (! $location)` block), build the preview:

```php
        $preview = $upcoming->take(3)
            ->groupBy(fn (Activity $activity): string => $activity->begin_date->toDateString())
            ->map(fn ($group): array => $group->map(fn (Activity $activity): array => ['item' => $activity])->all())
            ->all();
```

Add `'upcoming_preview' => $preview,` to **both** remaining `return` statements (the
no-location return and the final located return).

- [ ] **Step 4: Pass it from the controller**

In `app/Http/Controllers/HomeController.php`, add to the `view('home', [...])` array:

```php
            'upcomingRides' => $next['upcoming_preview'],
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --compact tests/Feature/NextRidePreviewTest.php`
Expected: PASS

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Support/Location/NextRideFinder.php app/Http/Controllers/HomeController.php tests/Feature/NextRidePreviewTest.php
git commit -m "feat(home): expose upcoming-rides preview for the no-location state"
```

---

## Task 6: Home "volgende rit" three-state rework

**Files:**
- Modify: `resources/views/home.blade.php:27-48`
- Modify: `resources/css/pages/home.css` (append paired-row appearance)
- Test: `tests/Feature/HomeNextRideStatesTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/HomeNextRideStatesTest.php`:

```php
<?php

use App\Enums\ActivityType;
use App\Models\Activity;

beforeEach(function () {
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDays(5),
    ]);
});

test('no-location state shows generic heading, rides, picker and the agenda link', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Volgende ritten');           // generic heading
    $response->assertDontSee('De volgende rit bij jou');
    $response->assertSee('location-picker', false);     // the picker is present
    $response->assertSee('Alle ritten in de agenda');   // paired "see all" affordance
});

test('located state shows the personal heading and the compact picker', function () {
    $cookie = [config('location.cookie') => json_encode([
        'zip' => '9000', 'lat' => 51.05, 'lng' => 3.72, 'name' => 'Gent',
    ])];

    $response = $this->withCookies($cookie)->get(route('home'));

    $response->assertOk();
    $response->assertSee('De volgende rit bij jou');
    $response->assertSee('location-picker--compact', false);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact tests/Feature/HomeNextRideStatesTest.php`
Expected: FAIL — old markup shows "De volgende rit bij jou" always and no "Alle ritten in de agenda".

- [ ] **Step 3: Rework the section**

In `resources/views/home.blade.php`, replace the `home-nextride` section (lines 27–48)
with:

```blade
        {{-- ② DE VOLGENDE RIT BIJ JOU — location-aware rides (proof + utility). --}}
        <section class="home-nextride space-y-6 scroll-mt-24" id="volgende-rit">
            <div class="flex items-baseline justify-between gap-4">
                <h2 class="text-kidical-ink">{{ $hasLocation ? 'De volgende rit bij jou' : 'Volgende ritten' }}</h2>
                @if ($hasUpcoming && $hasLocation)
                    <a href="{{ route('activities.index') }}" class="shrink-0 font-bold text-kidical-blue hover:underline">Bekijk alle ritten →</a>
                @endif
            </div>

            @if (! $hasUpcoming)
                <p class="text-kidical-ink/70">
                    Het fietsseizoen loopt van maart tot november.
                    <a href="{{ route('getting-started') }}" class="font-bold text-kidical-blue hover:underline">Ontdek hoe een rit werkt →</a>
                </p>

            @elseif (! $hasLocation)
                @foreach ($upcomingRides as $periodKey => $rows)
                    <x-ride-day :period-key="$periodKey" :rows="$rows" />
                @endforeach

                <div class="home-nextride__pair grid gap-8 sm:grid-cols-2 sm:gap-0">
                    <div class="sm:pr-10">
                        <p class="home-nextride__eyebrow">Ritten bij jou</p>
                        <livewire:location-picker :compact="true" />
                        <p class="home-nextride__sub">Vul je gemeente in en we zetten de ritten dichtbij bovenaan.</p>
                    </div>
                    <div class="home-nextride__divide flex flex-col items-start justify-center sm:pl-10">
                        <p class="home-nextride__eyebrow">Of bekijk alles</p>
                        <a href="{{ route('activities.index') }}" class="font-bold text-kidical-blue hover:underline">Alle ritten in de agenda →</a>
                        <p class="home-nextride__sub">De volledige kalender, van maart tot november.</p>
                    </div>
                </div>

            @else
                <livewire:location-picker :compact="true" />

                @if ($nextRideIsFar)
                    <p class="text-kidical-ink/70">Geen rit vlakbij op dit moment. De eerstvolgende iets verderaf:</p>
                @endif

                <x-ride-day :period-key="$nextRide->begin_date->toDateString()" :rows="[['item' => $nextRide]]" />
            @endif
        </section>
```

- [ ] **Step 4: Add the paired-row appearance to home.css**

Append to `resources/css/pages/home.css` (a new `@layer components` block is fine — the
file already uses that layer):

```css
@layer components {
    .home-nextride__eyebrow {
        font-family: var(--font-sans);
        font-size: var(--text-sm);
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 55%);
        margin-bottom: 0.6rem;
    }
    .home-nextride__sub {
        font-size: var(--text-base);
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 35%);
        margin-top: 0.75rem;
    }

    @media (min-width: 640px) {
        .home-nextride__divide {
            border-left: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 88%);
        }
    }
}
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --compact tests/Feature/HomeNextRideStatesTest.php`
Expected: PASS

- [ ] **Step 6: Build + commit**

```bash
npm run build
git add resources/views/home.blade.php resources/css/pages/home.css tests/Feature/HomeNextRideStatesTest.php
git commit -m "feat(home): rework volgende-rit into no-location / located / far states"
```

---

## Task 7: Full verification + fix any fallout

**Files:** none new — this is a guard pass.

- [ ] **Step 1: Run the location/home/calendar/groups suites + architecture test**

Run:
```bash
php artisan test --compact tests/Feature/LocationPickerCompactTest.php tests/Feature/FilterBarTest.php tests/Feature/CalendarFilterBarTest.php tests/Feature/GroupsFilterBarTest.php tests/Feature/NextRidePreviewTest.php tests/Feature/HomeNextRideStatesTest.php tests/Feature/CssArchitectureTest.php
```
Expected: all PASS.

- [ ] **Step 2: Run any pre-existing home/calendar/groups tests that may assert the old markup**

Run: `php artisan test --compact --filter='Home|Calendar|Group|Location'`
Expected: PASS. If a pre-existing test asserted the old single picker placement or the
always-on "De volgende rit bij jou" heading, update its expectation to match the new
states (do not delete tests). The known `CalendarProximityTest` order-dependent flake
(passes in isolation) is not a regression — re-run it alone to confirm:
`php artisan test --compact tests/Feature/CalendarProximityTest.php`.

- [ ] **Step 3: Visual check (manual)**

`npm run build`, then load `https://kidicalmass.test/nl`, `/nl/kalender` (agenda) and the
groups index. Confirm: the bar looks identical (compact picker) on all three; tabs only
on the agenda; home no-location shows rides + the paired row; located shows the pill +
nearest ride.

- [ ] **Step 4: Final commit if Step 2 changed anything**

```bash
vendor/bin/pint --dirty --format agent
git add -p   # stage only the touched test files, by hunk
git commit -m "test: align pre-existing tests with the unified filter bar + home states"
```

---

## Self-review notes (done)

- **Spec coverage:** compact modifier (T1), filter-bar shell+CSS (T2), agenda migration + radius tabs in slot (T3), groups to panel top (T4), `upcoming_preview` backend (T5), home three states + paired affordance + heading swap + top-right-link rule (T6), tests incl. CssArchitectureTest (T1–T7). All spec sections mapped.
- **Naming consistency:** `compact` prop, `.location-picker--compact`, `.filter-bar` / `.filter-bar__loc|__sep|__radius|__radius-label|__tabs|__tab|__tab--active`, `upcoming_preview` / `$upcomingRides` used identically across tasks.
- **No placeholders:** every code/CSS step shows the actual content or an exact move+rename with line refs.

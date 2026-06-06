# Kalender Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Redesign the Kalender page (P-02) with an integrated location + radius filter row, two-column agenda + sidebar layout, compact date column, and cleaned-up event rows.

**Architecture:** `RideCalendar` keeps `LocationPicker` as a child component but moves it from the hero slot into a new filter row in the body. `RideCalendar` adds a URL-bound `$radius` property and flattens its render output to a single chronological `$byPeriod` list filtered by the active radius. All other components (`kal-day-band`, `event-card`) are updated to match the new data shape and visual spec.

**Tech Stack:** Laravel 12, Livewire 4, Tailwind CSS v4, Alpine.js, Pest 4

**Spec:** `docs/superpowers/specs/2026-06-06-calendar-redesign.md`

---

## File Map

| File | Change |
|------|--------|
| `config/location.php` | Add `regio_radius_km: 30` |
| `app/Livewire/RideCalendar.php` | Add `#[Url] $radius`; new `setRadius()` action; rewrite `render()` to annotate + filter; remove `groupAnnotated()` |
| `app/Livewire/LocationPicker.php` | Add `focus-picker` window event listener to template |
| `resources/views/livewire/location-picker.blade.php` | Add Alpine `@focus-picker.window` event handler |
| `resources/views/livewire/ride-calendar.blade.php` | Remove hero picker slot; add filter row; two-column body; sidebar; move past-rides link to bottom |
| `resources/views/components/kal-day-band.blade.php` | Two-column date/rides grid; remove h3; drop `$plain` prop |
| `resources/views/components/event-card.blade.php` | Pin icon before city; venue strip logic |
| `resources/css/app.css` | New styles: filter row, sidebar, compact date col, radius tabs; update kal-day and event-card rules |
| `tests/Feature/Location/CalendarProximityTest.php` | Update for new radius filter behaviour |

---

## Task 1: Add `regio_radius_km` to config

**Files:**
- Modify: `config/location.php`

- [ ] **Step 1: Add the config key**

```php
// config/location.php
return [
    'nearby_radius_km' => (float) env('LOCATION_NEARBY_RADIUS_KM', 7),
    'regio_radius_km'  => (float) env('LOCATION_REGIO_RADIUS_KM', 30),

    'cookie'      => 'kcm_location',
    'cookie_days' => 365,
];
```

- [ ] **Step 2: Verify via tinker**

```bash
php artisan tinker --execute 'echo config("location.regio_radius_km");'
```

Expected: `30`

- [ ] **Step 3: Commit**

```bash
git add config/location.php
git commit -m "feat(config): add regio_radius_km=30 for calendar radius filter"
```

---

## Task 2: Update `CalendarProximityTest` for new radius behaviour (TDD first)

The existing test asserts the old two-section UI (`In de buurt`, `Verderaf`). Replace it with tests for the new flat list + radius filtering.

**Files:**
- Modify: `tests/Feature/Location/CalendarProximityTest.php`

- [ ] **Step 1: Replace the test file contents**

```php
<?php

use App\Enums\ActivityType;
use App\Livewire\RideCalendar;
use App\Models\Activity;
use App\Models\PostalCode;
use Livewire\Livewire;

beforeEach(function () {
    PostalCode::insert([
        ['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174, 'created_at' => now(), 'updated_at' => now()],
    ]);

    // ~0 km from Jette (same zip)
    $this->near = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl'      => 'Kidical Mass Jette',
        'postal_code'   => '1090',
        'begin_date'    => now()->addDays(3),
    ]);

    // ~54 km from Jette
    $this->far = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl'      => 'Kidical Mass Gent',
        'postal_code'   => '9000',
        'begin_date'    => now()->addDays(5),
    ]);
});

it('shows all rides unfiltered when no location is set', function () {
    Livewire::test(RideCalendar::class)
        ->assertSee('Jette')
        ->assertSee('Gent')
        ->assertDontSee('In de buurt')
        ->assertDontSee('Verderaf');
});

it('shows only nearby rides when location set and radius is dichtbij', function () {
    Livewire::withCookie('kcm_location', json_encode([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]));

    Livewire::test(RideCalendar::class, ['radius' => 'dichtbij'])
        ->assertSee('Jette')
        ->assertDontSee('Gent')
        ->assertDontSee('In de buurt')
        ->assertDontSee('Verderaf');
});

it('shows rides within 30km when radius is regio', function () {
    // Add a ride 20km from Jette (Brussel - 1000 zip)
    PostalCode::insert([
        ['zip' => '1000', 'name' => 'Brussel', 'latitude' => 50.8503, 'longitude' => 4.3517, 'created_at' => now(), 'updated_at' => now()],
    ]);
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl'      => 'Kidical Mass Brussel',
        'postal_code'   => '1000',
        'begin_date'    => now()->addDays(4),
    ]);

    Livewire::withCookie('kcm_location', json_encode([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]));

    Livewire::test(RideCalendar::class, ['radius' => 'regio'])
        ->assertSee('Jette')
        ->assertSee('Brussel')
        ->assertDontSee('Gent');
});

it('shows all rides when radius is belgie', function () {
    Livewire::withCookie('kcm_location', json_encode([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]));

    Livewire::test(RideCalendar::class, ['radius' => 'belgie'])
        ->assertSee('Jette')
        ->assertSee('Gent');
});
```

- [ ] **Step 2: Run the tests — they must fail (RideCalendar doesn't have $radius yet)**

```bash
php artisan test --compact --filter=CalendarProximityTest
```

Expected: FAIL — property `$radius` not found.

---

## Task 3: Rewrite `RideCalendar` render logic

**Files:**
- Modify: `app/Livewire/RideCalendar.php`

- [ ] **Step 1: Rewrite the component**

```php
<?php

namespace App\Livewire;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;
use App\Support\Location\CurrentLocation;
use App\Support\Location\Proximity;
use Livewire\Attributes\Url;
use Livewire\Component;

class RideCalendar extends Component
{
    #[Url(as: 'when')]
    public string $when = 'aankomend';

    /** URL-bound radius tab: dichtbij | regio | belgie */
    #[Url(as: 'radius')]
    public string $radius = 'dichtbij';

    public function showPast(): void
    {
        $this->when = 'voorbije';
    }

    public function showUpcoming(): void
    {
        $this->when = 'aankomend';
    }

    public function setRadius(string $value): void
    {
        if (in_array($value, ['dichtbij', 'regio', 'belgie'], true)) {
            $this->radius = $value;
        }
    }

    public function render()
    {
        $when = $this->when === 'voorbije' ? 'voorbije' : 'aankomend';

        $query = Activity::query()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->with(['groups']);

        if ($when === 'voorbije') {
            $activities = $query->where('begin_date', '<', now()->startOfDay())
                ->orderByDesc('begin_date')->limit(24)->get();

            return view('livewire.ride-calendar', [
                'when'          => $when,
                'location'      => null,
                'radius'        => $this->radius,
                'byPeriod'      => $activities->groupBy(fn ($a) => $a->begin_date->format('Y-m')),
                'hasActivities' => $activities->isNotEmpty(),
                'isEmpty'       => $activities->isEmpty(),
            ]);
        }

        $activities = $query->where('begin_date', '>=', now()->startOfDay())
            ->orderBy('begin_date')->get();

        $location = CurrentLocation::resolve();

        // When no location is set, show all rides unfiltered (annotated with null distance).
        if (! $location) {
            $rows = $activities->map(fn ($a) => ['item' => $a, 'distance_km' => null]);

            return view('livewire.ride-calendar', [
                'when'          => $when,
                'location'      => null,
                'radius'        => $this->radius,
                'byPeriod'      => $rows->groupBy(fn ($r) => $r['item']->begin_date->format('Y-m-d')),
                'hasActivities' => $activities->isNotEmpty(),
                'isEmpty'       => false,
            ]);
        }

        // Resolve postal-code coordinates for every unique zip in the result set.
        $coordsByZip = PostalCode::whereIn('zip', $activities->pluck('postal_code')->filter()->unique())
            ->get()->keyBy('zip');

        $origin = ['lat' => $location['lat'], 'lng' => $location['lng']];

        // Annotate every activity with its distance from the user's location.
        $annotated = $activities->map(function ($activity) use ($origin, $coordsByZip) {
            $pc = $activity->postal_code ? $coordsByZip->get($activity->postal_code) : null;
            $coords = $pc ? ['lat' => $pc->latitude, 'lng' => $pc->longitude] : null;

            return [
                'item'        => $activity,
                'distance_km' => $coords ? round(Proximity::distanceKm($origin, $coords), 1) : null,
            ];
        });

        // Filter by active radius. 'belgie' shows everything.
        if ($this->radius !== 'belgie') {
            $radiusKm = $this->radius === 'regio'
                ? (float) config('location.regio_radius_km')
                : (float) config('location.nearby_radius_km');

            $annotated = $annotated->filter(
                fn ($row) => $row['distance_km'] === null || $row['distance_km'] <= $radiusKm
            );
        }

        $byPeriod = $annotated->values()->groupBy(fn ($r) => $r['item']->begin_date->format('Y-m-d'));

        return view('livewire.ride-calendar', [
            'when'          => $when,
            'location'      => $location,
            'radius'        => $this->radius,
            'byPeriod'      => $byPeriod,
            'hasActivities' => $activities->isNotEmpty(),
            'isEmpty'       => $byPeriod->isEmpty() && $activities->isNotEmpty(),
        ]);
    }
}
```

- [ ] **Step 2: Run Pint to fix formatting**

```bash
vendor/bin/pint app/Livewire/RideCalendar.php --format agent
```

- [ ] **Step 3: Run the proximity tests — they must pass now**

```bash
php artisan test --compact --filter=CalendarProximityTest
```

Expected: PASS (4 tests).

- [ ] **Step 4: Commit**

```bash
git add app/Livewire/RideCalendar.php tests/Feature/Location/CalendarProximityTest.php
git commit -m "feat(calendar): add radius URL property and flat annotated render output"
```

---

## Task 4: Update `kal-day-band` to compact two-column layout

The component now always receives annotated rows (`['item' => Activity, 'distance_km' => float|null]`). The `$plain` prop and its branch are removed. The h3 heading becomes a date column div.

**Files:**
- Modify: `resources/views/components/kal-day-band.blade.php`

- [ ] **Step 1: Rewrite the template**

```blade
@props(['periodKey', 'rows'])
@php
    $periodDate = \Illuminate\Support\Carbon::parse($periodKey)->locale('nl');
    $landmark = $periodDate->isToday() ? 'Vandaag'
        : ($periodDate->isTomorrow() ? 'Morgen'
        : (($periodDate->isCurrentWeek() && $periodDate->isWeekend()) ? 'Dit weekend' : null));
    // Compact date: "Zo" on one line, "7 jun" on the next.
    $dayAbbr  = \Illuminate\Support\Str::upper($periodDate->isoFormat('dd'));
    $dayNum   = $periodDate->isoFormat('D MMM');
@endphp
<section class="kal-day">
    <div class="kal-day__date-col">
        <time class="kal-day__date" datetime="{{ $periodDate->toDateString() }}">
            <span class="kal-day__date-dow">{{ $dayAbbr }}</span>
            <span class="kal-day__date-num">{{ $dayNum }}</span>
        </time>
        @if ($landmark)<span class="kal-day__landmark">{{ $landmark }}</span>@endif
    </div>
    <div class="kal-day__rides">
        @foreach ($rows as $row)
            <x-event-card :activity="$row['item']" :show-date="false" />
        @endforeach
    </div>
</section>
```

- [ ] **Step 2: Run tests to catch regressions**

```bash
php artisan test --compact --filter=CalendarProximityTest
```

Expected: PASS.

- [ ] **Step 3: Commit**

```bash
git add resources/views/components/kal-day-band.blade.php
git commit -m "refactor(kal-day-band): two-column compact layout, drop plain prop"
```

---

## Task 5: Update `event-card` — pin before city, venue strip

**Files:**
- Modify: `resources/views/components/event-card.blade.php`

- [ ] **Step 1: Rewrite the template**

```blade
@props(['activity', 'showDate' => true, 'featured' => null])

{{-- PAT-1 · Event row. PIN + CITY | VENUE | TIME.
     Pin moves to immediately before the municipality (the anchor a parent scans for).
     Venue strips trailing ", <municipality>" when it matches the display name. --}}
@php
    $headline = preg_replace('/^\s*kidical\s+mass\s+/i', '', $activity->title_nl);
    $headline = trim((string) $headline) !== '' ? $headline : $activity->title_nl;

    $isFeatured = $featured ?? \Illuminate\Support\Str::contains(
        \Illuminate\Support\Str::lower($activity->title_nl), ['grande', 'grote kidical']
    );

    // Strip trailing ", <municipality>" from venue when it duplicates the display city.
    $venueDisplay = $activity->location;
    if ($venueDisplay !== null && $venueDisplay !== '') {
        $venueDisplay = trim((string) preg_replace(
            '/,\s*' . preg_quote($headline, '/') . '\s*$/iu',
            '',
            $venueDisplay,
        ));
    }
@endphp
<a
    href="{{ route('activities.show', $activity) }}"
    {{ $attributes->merge(['class' => 'event-card link-plain'.($isFeatured ? ' event-card--featured' : '')]) }}
>
    <span class="event-card__place">
        <flux:icon.map-pin variant="solid" class="event-card__place-pin" aria-hidden="true" />
        @if ($isFeatured)<span class="event-card__star" aria-hidden="true">★</span>@endif{{ $headline }}
    </span>

    @if ($isFeatured)
        <span class="event-card__featured-badge">Uitgelicht</span>
    @endif

    @if ($venueDisplay)
        <span class="event-card__loc">
            <span class="event-card__loc-text">{{ $venueDisplay }}</span>
        </span>
    @endif

    <span class="event-card__when">
        @if ($showDate)
            <span class="event-card__date">{{ $activity->begin_date->locale('nl')->isoFormat('dd D MMM') }}</span>
        @endif
        <time class="event-card__time" datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ $activity->begin_date->format('H:i') }}</time>
    </span>
</a>
```

- [ ] **Step 2: Run tests**

```bash
php artisan test --compact --filter=CalendarProximityTest
```

Expected: PASS.

- [ ] **Step 3: Commit**

```bash
git add resources/views/components/event-card.blade.php
git commit -m "feat(event-card): move pin before city name, strip redundant city from venue"
```

---

## Task 6: Add `focus-picker` event to `LocationPicker`

The sidebar nudge button fires a `focus-picker` browser event on window. The picker's root element listens and sets editing mode.

**Files:**
- Modify: `resources/views/livewire/location-picker.blade.php`

- [ ] **Step 1: Add the window event listener to the root div**

Change the opening `<div class="location-picker" x-data="{...}">` to include the listener:

```blade
<div
    class="location-picker"
    x-data="{
        locating: false,
        geoError: false,
        locate() {
            this.geoError = false;
            if (! navigator.geolocation) { this.geoError = true; return; }
            this.locating = true;
            navigator.geolocation.getCurrentPosition(
                (pos) => $wire.setFromCoords(pos.coords.latitude, pos.coords.longitude),
                () => { this.locating = false; this.geoError = true; },
            );
        }
    }"
    @focus-picker.window="$wire.set('editing', true); $nextTick(() => $el.querySelector('input')?.focus())"
>
```

- [ ] **Step 2: Commit**

```bash
git add resources/views/livewire/location-picker.blade.php
git commit -m "feat(location-picker): respond to focus-picker window event for sidebar nudge"
```

---

## Task 7: Rewrite `ride-calendar.blade.php`

Remove hero picker slot. Add filter row + two-column body grid + sidebar. Move past-rides link to bottom. Use the flat `$byPeriod` for all upcoming renders.

**Files:**
- Modify: `resources/views/livewire/ride-calendar.blade.php`

- [ ] **Step 1: Rewrite the template**

```blade
<div>
    <x-page-hero
        eyebrow="Kalender"
        title="Spring op de fiets, wij rijden samen."
        illustration="img/illustrations/kid-on-bike.png">

        {{-- Filter row: location picker + radius tabs. Hidden on past-rides view. --}}
        @if ($when !== 'voorbije')
            <div class="kal-filterrow">
                <div class="kal-filterrow__loc">
                    <livewire:location-picker />
                </div>

                <div class="kal-filterrow__sep" aria-hidden="true"></div>

                <div class="kal-filterrow__radius">
                    @if ($location)
                        <div class="kal-filterrow__tabs">
                            <button
                                type="button"
                                wire:click="setRadius('dichtbij')"
                                class="kal-filterrow__tab{{ $radius === 'dichtbij' ? ' kal-filterrow__tab--active' : '' }}"
                            >Dicht bij</button>
                            <button
                                type="button"
                                wire:click="setRadius('regio')"
                                class="kal-filterrow__tab{{ $radius === 'regio' ? ' kal-filterrow__tab--active' : '' }}"
                            >Ruimere regio</button>
                            <button
                                type="button"
                                wire:click="setRadius('belgie')"
                                class="kal-filterrow__tab{{ $radius === 'belgie' ? ' kal-filterrow__tab--active' : '' }}"
                            >Heel België</button>
                        </div>
                    @else
                        <p class="kal-filterrow__radius-hint">Hoe ver wil je kijken?</p>
                        <div class="kal-filterrow__tabs kal-filterrow__tabs--disabled" aria-hidden="true">
                            <span class="kal-filterrow__tab">Dicht bij</span>
                            <span class="kal-filterrow__tab">Ruimere regio</span>
                            <span class="kal-filterrow__tab">Heel België</span>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Two-column body: agenda left, sticky sidebar right. --}}
        <div class="kal-body">
            <div class="kal-agenda">

                @if (! $hasActivities)
                    <p class="kal-empty">
                        @if ($when === 'voorbije')
                            Er zijn nog geen voorbije fietstochten om te tonen.
                        @else
                            Er zijn momenteel geen fietstochten gepland. Het seizoen loopt van maart tot november. Kom snel terug!
                        @endif
                    </p>

                @elseif ($when === 'voorbije')
                    <div class="kal-days">
                        @foreach ($byPeriod as $periodKey => $rides)
                            <x-kal-month-band :period-key="$periodKey" :rides="$rides" />
                        @endforeach
                    </div>

                @elseif ($isEmpty)
                    @php
                        $radiusLabel = match($radius) {
                            'regio'   => 'Ruimere regio',
                            'belgie'  => 'Heel België',
                            default   => 'Dicht bij',
                        };
                    @endphp
                    <p class="kal-empty">
                        Geen ritten in de categorie "{{ $radiusLabel }}" van {{ $location['name'] }}.
                        Kies een ruimere regio om meer te zien.
                    </p>

                @else
                    <div class="kal-days">
                        @foreach ($byPeriod as $periodKey => $rows)
                            <x-kal-day-band :period-key="$periodKey" :rows="$rows" />
                        @endforeach
                    </div>
                @endif

                {{-- Past-rides link at bottom of agenda --}}
                @if ($when === 'aankomend')
                    <div class="kal-pastbar">
                        <button type="button" wire:click="showPast" class="kal-pastlink">Bekijk voorbije ritten →</button>
                    </div>
                @else
                    <div class="kal-pastbar">
                        <button type="button" wire:click="showUpcoming" class="kal-pastlink">← Terug naar aankomende ritten</button>
                    </div>
                @endif

            </div>{{-- /.kal-agenda --}}

            {{-- Sticky sidebar (desktop only; hidden on mobile) --}}
            @if ($when !== 'voorbije')
                <aside class="kal-sidebar">
                    @if ($location)
                        {{-- Newsletter CTA --}}
                        <div class="kal-sidebar__panel kal-sidebar__panel--newsletter">
                            <h3 class="kal-sidebar__heading">Mis geen rit</h3>
                            <p class="kal-sidebar__body">Één seintje per maand met ritten bij jou in de buurt. Geen spam, altijd uitschrijfbaar.</p>
                            <button type="button" class="kal-sidebar__btn">Schrijf je in</button>
                        </div>
                    @else
                        {{-- Location nudge --}}
                        <div class="kal-sidebar__panel kal-sidebar__panel--nudge">
                            <flux:icon.map-pin variant="solid" class="kal-sidebar__nudge-icon" aria-hidden="true" />
                            <p class="kal-sidebar__body">Stel je buurt in en zie alleen de ritten bij jou in de buurt.</p>
                            <button
                                type="button"
                                class="kal-sidebar__btn"
                                @click="$dispatch('focus-picker')"
                            >Stel locatie in</button>
                        </div>
                    @endif
                </aside>
            @endif

        </div>{{-- /.kal-body --}}

    </x-page-hero>
</div>
```

- [ ] **Step 2: Run proximity tests**

```bash
php artisan test --compact --filter=CalendarProximityTest
```

Expected: PASS.

- [ ] **Step 3: Run the full public structure test suite to catch regressions**

```bash
php artisan test --compact tests/Feature/PublicStructureTest.php
```

Expected: PASS.

- [ ] **Step 4: Commit**

```bash
git add resources/views/livewire/ride-calendar.blade.php
git commit -m "feat(calendar): filter row, two-column layout, sidebar, past-rides link at bottom"
```

---

## Task 8: CSS — filter row, two-column layout, sidebar, compact date column, event-card pin

**Files:**
- Modify: `resources/css/app.css`

All changes go in the `@media (min-width: ...)` block that currently contains `.kal-body`. Find the comment `/* Period toggle (past/upcoming)` as the insertion anchor.

- [ ] **Step 1: Replace the `.kal-body` rule and add the new rules**

Find the current `.kal-body` rule at approximately line 3349:

```css
    .kal-body {
        max-width: none;
    }
```

Replace it (and the existing `.kal-periodbar` and `.kal-pastlink` rules below it) with the full new set:

```css
    /* ─── Filter row: location picker + radius tabs ─── */

    /* Filter row sits between hero and agenda. */
    .kal-filterrow {
        display: flex;
        align-items: stretch;
        gap: 0;
        background: white;
        border-top: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 90%);
        border-bottom: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 90%);
        padding: 0.75rem 2rem;
        margin: 0 -2rem; /* align to hero edge when hero has side padding */
    }

    .kal-filterrow__loc {
        flex: 1;
        min-width: 0;
    }

    /* Compact location picker inside filter row — override the hero-sized defaults */
    .kal-filterrow .location-picker {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-direction: row;
        padding: 0;
    }

    .kal-filterrow .location-picker__current,
    .kal-filterrow .location-picker__main {
        flex: 1;
    }

    .kal-filterrow__sep {
        width: 1px;
        align-self: stretch;
        background: color-mix(in oklab, var(--color-kidical-ink), transparent 88%);
        margin: 0 1.25rem;
        flex-shrink: 0;
    }

    .kal-filterrow__radius {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 0.35rem;
        flex-shrink: 0;
    }

    .kal-filterrow__radius-hint {
        font-size: var(--text-xs);
        font-weight: 700;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 60%);
    }

    .kal-filterrow__tabs {
        display: flex;
        gap: 4px;
    }

    .kal-filterrow__tabs--disabled {
        opacity: 0.3;
        pointer-events: none;
        user-select: none;
    }

    .kal-filterrow__tab {
        padding: 0.35rem 0.85rem;
        font-family: var(--font-heading);
        font-size: var(--text-sm);
        font-weight: 700;
        border-radius: 6px;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 30%);
        background: color-mix(in oklab, var(--color-kidical-ink), transparent 93%);
        cursor: pointer;
        transition: background 0.15s, color 0.15s;
        border: none;
    }

    .kal-filterrow__tab:hover {
        background: color-mix(in oklab, var(--color-kidical-blue), transparent 88%);
        color: var(--color-kidical-blue);
    }

    .kal-filterrow__tab--active {
        background: var(--color-kidical-blue);
        color: white;
    }

    /* ─── Two-column body: agenda + sidebar ─── */

    .kal-body {
        display: grid;
        grid-template-columns: 1fr 260px;
        gap: 0 3rem;
        align-items: start;
        max-width: none;
        margin-top: 2.5rem;
    }

    .kal-agenda {
        min-width: 0;
    }

    /* ─── Sticky sidebar ─── */

    .kal-sidebar {
        position: sticky;
        top: 1.5rem;
    }

    .kal-sidebar__panel {
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .kal-sidebar__panel--newsletter {
        background: var(--color-kidical-yellow);
    }

    .kal-sidebar__panel--nudge {
        background: color-mix(in oklab, var(--color-kidical-blue), transparent 90%);
    }

    .kal-sidebar__nudge-icon {
        width: 1.5rem;
        height: 1.5rem;
        color: var(--color-kidical-blue);
    }

    .kal-sidebar__heading {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: var(--text-lg);
        color: var(--color-kidical-ink);
        margin: 0;
    }

    .kal-sidebar__body {
        font-size: var(--text-sm);
        line-height: 1.55;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 18%);
    }

    .kal-sidebar__btn {
        align-self: flex-start;
        background: var(--color-kidical-ink);
        color: white;
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: var(--text-sm);
        padding: 0.55rem 1.1rem;
        border-radius: 999px;
        border: none;
        cursor: pointer;
        transition: opacity 0.15s;
    }

    .kal-sidebar__btn:hover {
        opacity: 0.85;
    }

    /* ─── Past-rides bar (now at bottom) ─── */

    .kal-pastbar {
        display: flex;
        justify-content: flex-start;
        margin-top: 3rem;
        padding-top: 1.5rem;
        border-top: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 88%);
    }

    .kal-pastlink {
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 30%);
        font-family: var(--font-heading);
        font-weight: 700;
        font-size: var(--text-sm);
        text-decoration: underline;
        text-decoration-color: color-mix(in oklab, var(--color-kidical-ink), transparent 70%);
        text-underline-offset: 3px;
        transition: color 0.18s;
        cursor: pointer;
    }

    .kal-pastlink:hover {
        color: var(--color-kidical-red);
    }

    /* ─── Compact day column: 64px date + 1fr rides ─── */

    .kal-days {
        /* margin-top is handled by kal-agenda, not kal-days */
    }

    .kal-day {
        display: grid;
        grid-template-columns: 64px 1fr;
        gap: 0 1.25rem;
        align-items: start;
    }

    .kal-day + .kal-day {
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 88%);
    }

    .kal-day__date-col {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.4rem;
        padding-top: 0.6rem; /* align with first ride row text */
    }

    .kal-day__date {
        display: flex;
        flex-direction: column;
        gap: 0;
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: var(--text-sm);
        line-height: 1.2;
        color: var(--color-kidical-ink);
    }

    .kal-day__date-dow {
        font-size: var(--text-xs);
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 40%);
    }

    .kal-day__date-num {
        font-size: var(--text-base);
        font-weight: 900;
    }

    /* Relative landmark ("Vandaag" / "Morgen" / "Dit weekend") — small red pill. */
    .kal-day__landmark {
        font-size: 0.6rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--color-kidical-red);
        background: color-mix(in oklab, var(--color-kidical-red), transparent 88%);
        padding: 0.15rem 0.55rem;
        border-radius: 999px;
        line-height: 1.5;
    }

    .kal-day__rides {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
```

- [ ] **Step 2: Update `event-card__place` to accommodate the pin icon**

Find `.event-card__place` (approximately line 3504) and add pin icon styles. Also remove `.event-card__loc-icon` since the pin moved:

Add after `.event-card__place { ... }`:

```css
    /* Pin icon that now sits before the municipality name. */
    .event-card__place-pin {
        width: 0.9rem;
        height: 0.9rem;
        flex-shrink: 0;
        color: var(--color-kidical-red);
        margin-right: 0.3rem;
        vertical-align: middle;
        position: relative;
        top: -1px;
    }
```

And remove the `color: var(--color-kidical-red)` line from `.event-card__loc-icon` since the icon is gone, or remove the rule entirely if nothing else uses `event-card__loc-icon`. Since we dropped the icon from the template in Task 5, you can remove the `.event-card__loc-icon` rule.

- [ ] **Step 3: Remove the old `.kal-periodbar` rule** (it's replaced by `.kal-pastbar`)

Find and remove:

```css
    /* Period toggle (past/upcoming) — a demoted link above the agenda. */
    .kal-periodbar {
        display: flex;
        justify-content: flex-end;
        margin-top: calc(var(--spacing) * 8);
        margin-bottom: 1rem;
    }
```

- [ ] **Step 4: Remove old `.kal-days { margin-top: 2.5rem; }` standalone rule** (replaced by new approach)

Find and remove:

```css
    .kal-days {
        margin-top: 2.5rem;
    }
```

- [ ] **Step 5: Remove `.kal-bandtitle` rules** (sections no longer exist)

Find and remove these rules — they described "In de buurt van X" and "Verderaf" headings that are gone:

```css
    .kal-bandtitle { ... }
    .kal-bandtitle__icon { ... }
    .kal-bandtitle--far { ... }
    .kal-bandtitle + .kal-days { ... }
```

- [ ] **Step 6: Run tests**

```bash
php artisan test --compact --filter=CalendarProximityTest
php artisan test --compact tests/Feature/PublicStructureTest.php
```

Expected: both PASS.

- [ ] **Step 7: Commit**

```bash
git add resources/css/app.css
git commit -m "feat(css): filter row, two-column layout, sidebar, compact day column"
```

---

## Task 9: Run full test suite + build assets

- [ ] **Step 1: Run Pint on all modified PHP**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 2: Run full test suite**

```bash
php artisan test --compact
```

Expected: all tests pass. If any fail, read the error and fix before continuing.

- [ ] **Step 3: Build assets**

```bash
npm run build
```

Expected: build completes without errors.

- [ ] **Step 4: Visual verification — take a screenshot**

```bash
node /tmp/kal-screenshot.cjs
```

Use `scripts/screenshot.cjs` if it exists, or create `/tmp/kal-screenshot.cjs`:

```js
// /tmp/kal-screenshot.cjs
const { chromium } = require('/Users/frederikvincx/Library/Application Support/Herd/config/nvm/versions/node/v22.22.3/lib/node_modules/playwright');
(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ ignoreHTTPSErrors: true, viewport: { width: 1440, height: 900 } });
  const page = await ctx.newPage();
  await page.goto('https://kidicalmass.test/nl/events');
  await page.waitForLoadState('networkidle');
  await page.screenshot({ path: '/tmp/kal-no-location.png', fullPage: true });
  console.log('saved /tmp/kal-no-location.png');
  await browser.close();
})();
```

- [ ] **Step 5: Final commit if any last tweaks were made**

```bash
git add -p
git commit -m "chore(calendar): final visual polish after screenshot review"
```

---

## Self-Review Checklist

**Spec coverage:**

| Spec section | Covered |
|---|---|
| §1 Two-column page layout | Task 7 (template) + Task 8 (CSS) |
| §1 Mobile: sidebar hidden | CSS `.kal-sidebar` inside desktop media query only |
| §2 Filter row State A (no location) | Task 7 — `kal-filterrow__tabs--disabled` |
| §2 Filter row State B (location set) | Task 7 — active tab via `$radius` |
| §2 Three radius tabs + config | Task 1 (config) + Task 3 (PHP) + Task 7 (template) |
| §2 Filter row hidden for past rides | Task 7 — `@if ($when !== 'voorbije')` |
| §3 Sidebar: nudge state | Task 7 — `kal-sidebar__panel--nudge` |
| §3 Sidebar: newsletter state | Task 7 — `kal-sidebar__panel--newsletter` |
| §3 Nudge button focuses picker | Task 6 (LocationPicker event) + Task 7 ($dispatch) |
| §4 Compact date column (64px / 1fr) | Task 4 (kal-day-band) + Task 8 (CSS) |
| §4 Landmark pill | Task 4 |
| §5 Pin before city | Task 5 (event-card) + Task 8 (CSS) |
| §5 Venue strip logic | Task 5 (event-card) |
| §6 Past-rides link at bottom | Task 7 (template) |
| §7 Empty state message | Task 7 — `$isEmpty` branch with `$radiusLabel` |
| §8 LocationPicker moved from hero | Task 7 — hero has no controls slot |
| §9 kal-optin unchanged | Not touched ✓ |
| §9 Cookie format unchanged | Not touched ✓ |

**Placeholder scan:** None found — all steps have full code.

**Type consistency:** `$byPeriod` is always `Collection<string, Collection<int, array{item: Activity, distance_km: float|null}>>` in upcoming mode. `kal-day-band` reads `$row['item']` without `$plain` branching — consistent. `event-card` drops the `$distance` prop — nothing passes it anymore after Task 4.

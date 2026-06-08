# Ride Display Consistency Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Consolidate every public ride rendering onto shared primitives — one locale-aware date/time vocabulary, one list row, one day/month lockup, one ride-spotlight.

**Architecture:** A stateless `RideDate` formatter (NL/FR) is the single source of date/time strings, surfaced through thin `Activity` accessors. Three Blade components (`<x-ride-row>`, `<x-ride-day>`/`<x-ride-month>`, `<x-ride-spotlight>`) replace `event-card`, `agenda-item`, `kal-day-band`, `kal-month-band`, the chapter next-ride card, and the detail hero. CSS follows the role-based partials architecture.

**Tech Stack:** Laravel 12, Blade anonymous components, Livewire 4, Tailwind v4 (`@theme` tokens + `@layer` partials), Flux UI, Pest 4. Spec: `docs/superpowers/specs/2026-06-08-ride-display-consistency-design.md`.

**Conventions to honour:**
- Public site: raw `<h1>`–`<h6>` (never `flux:heading`); other `flux:*` fine.
- No raw hex/px in `.blade.php` components — only token-backed utilities; real CSS lives in partials.
- Every new CSS partial must be `@import`-ed in `app.css` (enforced by `CssArchitectureTest`).
- Run `vendor/bin/pint --dirty --format agent` after PHP changes.
- Tests: `php artisan test --compact --filter=<name>`.

---

### Task 1: `RideDate` formatter

**Files:**
- Create: `app/Support/RideDate.php`
- Test: `tests/Unit/RideDateTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Unit/RideDateTest.php`:

```php
<?php

use App\Support\RideDate;
use Illuminate\Support\Carbon;

it('formats time in Belgian Dutch, dropping :00 on whole hours', function () {
    app()->setLocale('nl');
    expect(RideDate::time(Carbon::parse('2026-06-14 14:00')))->toBe('14u');
    expect(RideDate::time(Carbon::parse('2026-06-14 14:30')))->toBe('14u30');
});

it('formats time in Belgian French with an h separator', function () {
    app()->setLocale('fr');
    expect(RideDate::time(Carbon::parse('2026-06-14 14:00')))->toBe('14h');
    expect(RideDate::time(Carbon::parse('2026-06-14 14:30')))->toBe('14h30');
});

it('formats short, full and month-year dates per locale', function () {
    app()->setLocale('nl');
    expect(RideDate::short('2026-06-14'))->toBe('zo 14 jun.');
    expect(RideDate::full('2026-06-14'))->toBe('zondag 14 juni');
    expect(RideDate::monthYear('2026-06-14'))->toBe('juni 2026');

    app()->setLocale('fr');
    expect(RideDate::short('2026-06-14'))->toBe('di 14 juin');
    expect(RideDate::full('2026-06-14'))->toBe('dimanche 14 juin');
    expect(RideDate::monthYear('2026-06-14'))->toBe('juin 2026');
});

it('returns lowercase output so casing stays a CSS concern', function () {
    app()->setLocale('nl');
    $full = RideDate::full('2026-06-14');
    expect($full)->toBe(mb_strtolower($full));
});

it('builds the date-rail parts', function () {
    app()->setLocale('nl');
    expect(RideDate::rail('2026-06-14'))->toBe(['num' => '14', 'month' => 'juni', 'dow' => 'zo']);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=RideDateTest`
Expected: FAIL with "Class App\Support\RideDate not found".

- [ ] **Step 3: Write the implementation**

Create `app/Support/RideDate.php`:

```php
<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Single source of truth for how a ride's date and time render across the site.
 * Locale-aware (nl/fr); output is always lowercase — casing is a CSS concern.
 */
class RideDate
{
    /** Belgian time: "14u" / "14u30" (nl), "14h" / "14h30" (fr). Whole hours drop the minutes. */
    public static function time(Carbon|string $date): string
    {
        $carbon = self::resolve($date);
        $separator = app()->getLocale() === 'fr' ? 'h' : 'u';
        $minutes = $carbon->format('i');

        return $carbon->format('G').$separator.($minutes === '00' ? '' : $minutes);
    }

    /** Abbreviated date for dense rows: "zo 14 jun." / "di 14 juin". */
    public static function short(Carbon|string $date): string
    {
        return self::localized($date)->isoFormat('dd D MMM');
    }

    /** Spelled-out date for prose/heroes: "zondag 14 juni" / "dimanche 14 juin". */
    public static function full(Carbon|string $date): string
    {
        return self::localized($date)->isoFormat('dddd D MMMM');
    }

    /** Month grouping header: "juni 2026" / "juin 2026". */
    public static function monthYear(Carbon|string $date): string
    {
        return self::localized($date)->isoFormat('MMMM YYYY');
    }

    /**
     * Parts for the slim date-rail lockup.
     *
     * @return array{num: string, month: string, dow: string}
     */
    public static function rail(Carbon|string $date): array
    {
        $carbon = self::localized($date);

        return [
            'num' => $carbon->isoFormat('D'),
            'month' => $carbon->isoFormat('MMMM'),
            'dow' => $carbon->isoFormat('dd'),
        ];
    }

    private static function resolve(Carbon|string $date): Carbon
    {
        return $date instanceof Carbon ? $date : Carbon::parse($date);
    }

    private static function localized(Carbon|string $date): Carbon
    {
        return self::resolve($date)->copy()->locale(app()->getLocale());
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=RideDateTest`
Expected: PASS (5 passing).

- [ ] **Step 5: Format & commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Support/RideDate.php tests/Unit/RideDateTest.php
git commit -m "feat(rides): add RideDate locale-aware date/time formatter"
```

---

### Task 2: `Activity` accessors

**Files:**
- Modify: `app/Models/Activity.php`
- Test: `tests/Unit/ActivityDisplayAccessorsTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Unit/ActivityDisplayAccessorsTest.php`:

```php
<?php

use App\Models\Activity;

it('exposes ride display accessors that delegate to RideDate', function () {
    app()->setLocale('nl');
    $ride = Activity::factory()->make([
        'title_nl' => 'Kidical Mass Etterbeek',
        'title_fr' => 'Kidical Mass Etterbeek (fr)',
        'begin_date' => '2026-06-14 14:00',
    ]);

    expect($ride->timeLabel)->toBe('14u');
    expect($ride->dateShort)->toBe('zo 14 jun.');
    expect($ride->dateFull)->toBe('zondag 14 juni');
    expect($ride->dateMonthYear)->toBe('juni 2026');
    expect($ride->title)->toBe('Kidical Mass Etterbeek');
});

it('picks the French title when the locale is fr', function () {
    app()->setLocale('fr');
    $ride = Activity::factory()->make([
        'title_nl' => 'NL titel',
        'title_fr' => 'Titre FR',
    ]);

    expect($ride->title)->toBe('Titre FR');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=ActivityDisplayAccessorsTest`
Expected: FAIL (accessors return null).

- [ ] **Step 3: Add accessors to the model**

Add `use App\Support\RideDate;` to the imports at the top of `app/Models/Activity.php`, then add these methods inside the class (after `getDurationLabelAttribute`):

```php
    public function getTitleAttribute(): string
    {
        return app()->getLocale() === 'fr' && filled($this->title_fr)
            ? (string) $this->title_fr
            : (string) $this->title_nl;
    }

    public function getTimeLabelAttribute(): string
    {
        return RideDate::time($this->begin_date);
    }

    public function getDateShortAttribute(): string
    {
        return RideDate::short($this->begin_date);
    }

    public function getDateFullAttribute(): string
    {
        return RideDate::full($this->begin_date);
    }

    public function getDateMonthYearAttribute(): string
    {
        return RideDate::monthYear($this->begin_date);
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=ActivityDisplayAccessorsTest`
Expected: PASS (2 passing).

- [ ] **Step 5: Format & commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Models/Activity.php tests/Unit/ActivityDisplayAccessorsTest.php
git commit -m "feat(rides): add locale-aware display accessors on Activity"
```

---

### Task 3: `<x-ride-row>` component

Replaces `event-card` + `agenda-item`. Whole row is a link; commune name is the only bold/coloured anchor; quiet meta with semibold time; type chip only for non-rides; featured keeps star + badge; optional inline date for lockup-less lists.

**Files:**
- Create: `resources/views/components/ride-row.blade.php`
- Create: `resources/css/components/ride-row.css`
- Modify: `resources/css/app.css` (register the partial)
- Test: `tests/Feature/RideRowComponentTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/RideRowComponentTest.php`:

```php
<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    app()->setLocale('nl');
    URL::defaults(['locale' => 'nl']); // route('activities.show', …) needs the {locale} param
});

it('renders a normal ride without a type chip', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Etterbeek',
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => '2026-06-14 14:00',
        'location' => 'Jubelpark',
    ]);

    $html = Blade::render('<x-ride-row :activity="$activity" />', ['activity' => $ride]);

    expect($html)->toContain('Etterbeek')
        ->toContain('14u')
        ->toContain('Jubelpark')
        ->not->toContain('ride-row__chip');
});

it('shows a yellow chip for a workshop', function () {
    $workshop = Activity::factory()->create([
        'title_nl' => 'Fietsherstel',
        'activity_type' => ActivityType::WORKSHOP,
        'begin_date' => '2026-06-14 19:00',
    ]);

    $html = Blade::render('<x-ride-row :activity="$activity" />', ['activity' => $workshop]);

    expect($html)->toContain('ride-row__chip--workshop')->toContain('Workshop');
});

it('shows the inline date only when showDate is set', function () {
    $ride = Activity::factory()->create(['begin_date' => '2026-06-14 14:00']);

    $withDate = Blade::render('<x-ride-row :activity="$activity" :show-date="true" />', ['activity' => $ride]);
    $without = Blade::render('<x-ride-row :activity="$activity" />', ['activity' => $ride]);

    expect($withDate)->toContain('ride-row__date');
    expect($without)->not->toContain('ride-row__date');
});

it('marks a flagship ride as featured', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Grote Kidical Mass Gent',
        'begin_date' => '2026-06-14 14:00',
    ]);

    $html = Blade::render('<x-ride-row :activity="$activity" />', ['activity' => $ride]);

    expect($html)->toContain('ride-row--featured')->toContain('Uitgelicht');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=RideRowComponentTest`
Expected: FAIL ("Unable to locate a class or view for component [ride-row]").

- [ ] **Step 3: Create the component**

Create `resources/views/components/ride-row.blade.php`:

```blade
@props(['activity', 'showDate' => false])

{{-- One row for every ride. Commune = anchor; chip only when it's not a ride. --}}
@php
    $headline = preg_replace('/^\s*kidical\s+mass\s+/i', '', $activity->title);
    $headline = trim((string) $headline) !== '' ? $headline : $activity->title;

    $isFeatured = \Illuminate\Support\Str::contains(
        \Illuminate\Support\Str::lower($activity->title), ['grande', 'grote kidical']
    );

    // Strip a trailing ", <commune>" from the venue when it duplicates the headline.
    $venueDisplay = $activity->location;
    if ($venueDisplay !== null && $venueDisplay !== '') {
        $venueDisplay = trim((string) preg_replace(
            '/,\s*'.preg_quote($headline, '/').'\s*$/iu', '', $venueDisplay,
        ));
    }

    $chip = match ($activity->activity_type) {
        \App\Enums\ActivityType::WORKSHOP => ['label' => 'Workshop', 'variant' => 'workshop'],
        \App\Enums\ActivityType::MEETING => ['label' => 'Vergadering', 'variant' => 'meeting'],
        \App\Enums\ActivityType::OTHER => ['label' => 'Activiteit', 'variant' => 'other'],
        default => null,
    };
@endphp
<a
    href="{{ route('activities.show', $activity) }}"
    {{ $attributes->merge(['class' => 'ride-row link-plain'.($isFeatured ? ' ride-row--featured' : '')]) }}
>
    @if ($chip)
        <span class="ride-row__chip ride-row__chip--{{ $chip['variant'] }}">{{ $chip['label'] }}</span>
    @endif

    <span class="ride-row__place">
        @if ($isFeatured)<span class="ride-row__star" aria-hidden="true">★</span>@endif{{ $headline }}
    </span>

    @if ($isFeatured)
        <span class="ride-row__featured-badge">Uitgelicht</span>
    @endif

    <span class="ride-row__meta">
        @if ($showDate)
            <span class="ride-row__date">{{ \App\Support\RideDate::short($activity->begin_date) }} ·</span>
        @endif
        <time class="ride-row__time" datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ $activity->timeLabel }}</time>
        @if ($venueDisplay)
            <span class="ride-row__at" aria-hidden="true">@</span><span class="ride-row__venue">{{ $venueDisplay }}</span>
        @endif
    </span>
</a>
```

- [ ] **Step 4: Create the CSS partial**

Create `resources/css/components/ride-row.css`:

```css
@layer components {
    .ride-row {
        display: flex;
        align-items: baseline;
        flex-wrap: wrap;
        gap: 0.5rem 1.1rem;
        padding-block: 0.9rem;
    }
    .ride-row:hover .ride-row__place {
        color: var(--color-kidical-red);
    }

    /* Commune — the one bold, coloured anchor. */
    .ride-row__place {
        flex: 0 0 auto;
        min-width: 9rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: 1.35rem;
        line-height: 1.2;
        letter-spacing: -0.01em;
        color: var(--color-kidical-blue);
        transition: color 0.18s;
    }
    .ride-row--featured .ride-row__place {
        color: var(--color-kidical-orange);
    }
    .ride-row__star {
        margin-right: 0.2rem;
        color: var(--color-kidical-orange);
    }

    /* Quiet meta line: one muted colour; only the time keeps weight. */
    .ride-row__meta {
        flex: 1 1 auto;
        min-width: 0;
        display: flex;
        align-items: baseline;
        gap: 0.4rem;
        font-family: var(--font-sans);
        font-size: var(--text-lg);
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 45%);
    }
    .ride-row__time {
        font-weight: 700;
    }
    .ride-row__venue {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .ride-row__date {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: var(--text-xs);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--color-kidical-red);
        flex-shrink: 0;
    }

    .ride-row__featured-badge {
        flex-shrink: 0;
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: var(--text-xs);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--color-kidical-orange);
        background: color-mix(in oklab, var(--color-kidical-orange), transparent 86%);
        padding: 0.15rem 0.55rem;
        border-radius: 999px;
    }

    /* Type chip — only present for non-ride activities. */
    .ride-row__chip {
        flex-shrink: 0;
        align-self: center;
        font-family: var(--font-heading);
        font-size: var(--text-xs);
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
    }
    .ride-row__chip--workshop { background: var(--color-kidical-yellow); color: var(--color-kidical-ink); }
    .ride-row__chip--meeting  { background: color-mix(in oklab, var(--color-kidical-blue), white 78%); color: var(--color-kidical-blue); }
    .ride-row__chip--other    { background: color-mix(in oklab, var(--color-kidical-ink), transparent 88%); color: var(--color-kidical-ink); }
}
```

- [ ] **Step 5: Register the partial in `app.css`**

In `resources/css/app.css`, find the line:

```css
@import './components/event-card.css';
```

Replace it with:

```css
@import './components/ride-row.css';
```

- [ ] **Step 6: Run test to verify it passes**

Run: `php artisan test --compact --filter=RideRowComponentTest`
Expected: PASS (4 passing).

- [ ] **Step 7: Commit**

```bash
git add resources/views/components/ride-row.blade.php resources/css/components/ride-row.css resources/css/app.css tests/Feature/RideRowComponentTest.php
git commit -m "feat(rides): add unified <x-ride-row> component"
```

---

### Task 4: `<x-ride-day>` and `<x-ride-month>` grouping components

Date-rail lockup (used on home + calendar upcoming) and month header (calendar past). Both render `<x-ride-row>`.

**Files:**
- Create: `resources/views/components/ride-day.blade.php`
- Create: `resources/views/components/ride-month.blade.php`
- Create: `resources/css/components/ride-day.css`
- Modify: `resources/css/pages/calendar.css` (add `.ride-month` + remove obsolete `.kal-day*`)
- Modify: `resources/css/app.css` (register `ride-day.css`)
- Test: `tests/Feature/RideGroupingComponentsTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/RideGroupingComponentsTest.php`:

```php
<?php

use App\Models\Activity;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    app()->setLocale('nl');
    URL::defaults(['locale' => 'nl']); // ride-row links call route('activities.show', …)
});

it('renders a day lockup with a date rail and no distance text', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Etterbeek',
        'begin_date' => '2026-06-14 14:00',
    ]);

    $html = Blade::render(
        '<x-ride-day :period-key="$key" :rows="$rows" />',
        ['key' => '2026-06-14', 'rows' => [['item' => $ride]]],
    );

    expect($html)->toContain('ride-day__rail')
        ->toContain('14')        // day number
        ->toContain('juni')      // month
        ->toContain('Etterbeek')
        ->not->toContain('km van jou');
});

it('renders a month band header for past rides', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Gent',
        'begin_date' => '2026-06-14 14:00',
    ]);

    $html = Blade::render(
        '<x-ride-month :period-key="$key" :rides="$rides" />',
        ['key' => '2026-06', 'rides' => collect([$ride])],
    );

    expect($html)->toContain('Juni 2026')->toContain('Gent');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=RideGroupingComponentsTest`
Expected: FAIL (components not found).

- [ ] **Step 3: Create `<x-ride-day>`**

Create `resources/views/components/ride-day.blade.php`:

```blade
@props(['periodKey', 'rows'])

@php
    $date = \Illuminate\Support\Carbon::parse($periodKey);
    $rail = \App\Support\RideDate::rail($date);
@endphp
<section class="ride-day">
    <time class="ride-day__rail" datetime="{{ $date->toDateString() }}">
        <span class="ride-day__num">{{ $rail['num'] }}</span>
        <span class="ride-day__mon">{{ $rail['month'] }}</span>
        <span class="ride-day__dow">{{ $rail['dow'] }}</span>
    </time>
    <div class="ride-day__rides">
        @foreach ($rows as $row)
            <x-ride-row :activity="$row['item']" />
        @endforeach
    </div>
</section>
```

- [ ] **Step 4: Create `<x-ride-month>`**

Create `resources/views/components/ride-month.blade.php`:

```blade
@props(['periodKey', 'rides'])

@php($date = \Illuminate\Support\Carbon::parse($periodKey))
<section class="ride-month">
    <h3 class="ride-month__head">
        <time datetime="{{ $date->format('Y-m') }}">{{ \Illuminate\Support\Str::ucfirst(\App\Support\RideDate::monthYear($date)) }}</time>
    </h3>
    <div class="ride-month__rides">
        @foreach ($rides as $activity)
            <x-ride-row :activity="$activity" />
        @endforeach
    </div>
</section>
```

- [ ] **Step 5: Create `resources/css/components/ride-day.css`**

```css
@layer components {
    .ride-day {
        display: grid;
        grid-template-columns: 60px 1fr;
        gap: 0 1.6rem;
        align-items: start;
    }
    .ride-day + .ride-day {
        margin-top: 1.75rem;
        padding-top: 1.75rem;
        border-top: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 90%);
    }

    /* Slim typographic rail — no box, type only. */
    .ride-day__rail {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        line-height: 1;
        padding-top: 0.9rem;
    }
    .ride-day__num {
        font-family: var(--font-sans);
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--color-kidical-ink);
    }
    .ride-day__mon {
        font-family: var(--font-sans);
        font-size: var(--text-sm);
        font-weight: 700;
        text-transform: lowercase;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 45%);
        margin-top: 0.2rem;
    }
    .ride-day__dow {
        font-family: var(--font-sans);
        font-size: var(--text-xs);
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 60%);
        margin-top: 0.4rem;
    }

    .ride-day__rides {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .ride-day__rides .ride-row + .ride-row {
        border-top: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 92%);
    }
}
```

- [ ] **Step 6: Add `.ride-month` rules and remove obsolete `.kal-day*` from `resources/css/pages/calendar.css`**

In `resources/css/pages/calendar.css`, delete the now-obsolete blocks: `.kal-day`, `.kal-day + .kal-day`, `.kal-day__date-col`, `.kal-day__date`, `.kal-day__date-dow`, `.kal-day__date-num`, `.kal-day__landmark`, `.kal-day__rides`, and the `.kal-day__rides .event-card + .event-card, .event-list .event-card + .event-card` rule (lines ~188–245).

In their place, add:

```css
    .ride-month + .ride-month {
        margin-top: 2.5rem;
    }
    .ride-month__head {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: var(--text-xl);
        text-transform: capitalize;
        color: var(--color-kidical-ink);
        margin: 0 0 0.4rem;
    }
    .ride-month__rides {
        display: flex;
        flex-direction: column;
    }
    .ride-month__rides .ride-row + .ride-row {
        border-top: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 92%);
    }
```

- [ ] **Step 7: Register `ride-day.css` in `app.css`**

In `resources/css/app.css`, directly after the `@import './components/ride-row.css';` line, add:

```css
@import './components/ride-day.css';
```

- [ ] **Step 8: Run test to verify it passes**

Run: `php artisan test --compact --filter=RideGroupingComponentsTest`
Expected: PASS (2 passing).

- [ ] **Step 9: Commit**

```bash
git add resources/views/components/ride-day.blade.php resources/views/components/ride-month.blade.php resources/css/components/ride-day.css resources/css/pages/calendar.css resources/css/app.css tests/Feature/RideGroupingComponentsTest.php
git commit -m "feat(rides): add date-rail <x-ride-day> and <x-ride-month> grouping"
```

---

### Task 5: `<x-ride-spotlight>` component

One light spotlight for the chapter next-ride (with CTA) and the detail header (no CTA, `h1`). Optional photo with a daisy-motif fallback that holds the column.

**Files:**
- Create: `resources/views/components/ride-spotlight.blade.php`
- Create: `resources/css/components/ride-spotlight.css`
- Modify: `resources/css/app.css` (register the partial)
- Test: `tests/Feature/RideSpotlightComponentTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/RideSpotlightComponentTest.php`:

```php
<?php

use App\Models\Activity;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\URL;

beforeEach(function () {
    app()->setLocale('nl');
    URL::defaults(['locale' => 'nl']); // CTA + links call route('activities.show', …)
});

it('renders the spotlight with a CTA when requested', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Gent',
        'begin_date' => '2026-06-14 14:00',
        'location' => 'Citadelpark',
    ]);

    $html = Blade::render('<x-ride-spotlight :activity="$activity" :cta="true" />', ['activity' => $ride]);

    expect($html)->toContain('Naar de rit')
        ->toContain('zondag 14 juni')
        ->toContain('14u')
        ->toContain('Citadelpark');
});

it('omits the CTA by default and renders the requested heading level', function () {
    $ride = Activity::factory()->create(['title_nl' => 'Kidical Mass Gent', 'begin_date' => '2026-06-14 14:00']);

    $html = Blade::render('<x-ride-spotlight :activity="$activity" heading="h1" />', ['activity' => $ride]);

    expect($html)->toContain('<h1')->not->toContain('Naar de rit');
});

it('shows the daisy motif when there is no photo', function () {
    $ride = Activity::factory()->create(['title_nl' => 'Kidical Mass Gent', 'begin_date' => '2026-06-14 14:00']);

    $html = Blade::render('<x-ride-spotlight :activity="$activity" />', ['activity' => $ride]);

    expect($html)->toContain('ride-spotlight__media--empty')->not->toContain('ride-spotlight__img');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=RideSpotlightComponentTest`
Expected: FAIL (component not found).

- [ ] **Step 3: Create the component**

Create `resources/views/components/ride-spotlight.blade.php`:

```blade
@props(['activity', 'cta' => false, 'heading' => 'h3'])

@php($image = $activity->getFirstMedia('main'))
<article {{ $attributes->merge(['class' => 'ride-spotlight']) }}>
    <div class="ride-spotlight__media{{ $image ? '' : ' ride-spotlight__media--empty' }}">
        @if ($image)
            <img src="{{ $image->getUrl() }}" alt="{{ $activity->title }}" class="ride-spotlight__img">
        @else
            <span class="ride-spotlight__daisy" aria-hidden="true"></span>
        @endif
    </div>

    <div class="ride-spotlight__body">
        @if ($activity->groups->isNotEmpty())
            <p class="ride-spotlight__chapter">
                @foreach ($activity->groups as $group){{ $group->name }}@unless ($loop->last) · @endunless @endforeach
            </p>
        @endif

        <{{ $heading }} class="ride-spotlight__title">{{ $activity->title }}</{{ $heading }}>

        <p class="ride-spotlight__when">
            <time datetime="{{ $activity->begin_date->format('Y-m-d\TH:i') }}">{{ \Illuminate\Support\Str::ucfirst($activity->dateFull) }} · {{ $activity->timeLabel }}</time>
        </p>

        @if ($activity->location)
            <p class="ride-spotlight__loc">
                <flux:icon.map-pin variant="solid" class="ride-spotlight__loc-icon" aria-hidden="true" />
                Verzamelen: {{ $activity->location }}
            </p>
        @endif

        @if ($cta)
            <a href="{{ route('activities.show', $activity) }}" class="ride-spotlight__cta link-plain">Naar de rit →</a>
        @endif
    </div>
</article>
```

- [ ] **Step 4: Create the CSS partial**

Create `resources/css/components/ride-spotlight.css`:

```css
@layer components {
    .ride-spotlight {
        display: grid;
        grid-template-columns: 200px 1fr;
        background: white;
        border: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 90%);
        border-radius: var(--radius-card);
        overflow: hidden;
        box-shadow: var(--shadow-card);
    }
    .ride-spotlight__media {
        min-height: 11rem;
    }
    .ride-spotlight__img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* No-photo fallback: soft brand panel + daisy mark holds the column. */
    .ride-spotlight__media--empty {
        display: flex;
        align-items: center;
        justify-content: center;
        background: color-mix(in oklab, var(--color-kidical-blue), white 88%);
    }
    .ride-spotlight__daisy {
        width: 4.5rem;
        height: 4.5rem;
        background: url('/img/logos/logo-icon.png') center / contain no-repeat;
        opacity: 0.9;
    }

    .ride-spotlight__body {
        padding: 1.6rem 1.9rem;
    }
    .ride-spotlight__chapter {
        font-family: var(--font-heading);
        font-size: var(--text-xs);
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--color-kidical-blue);
        margin: 0 0 0.3rem;
    }
    .ride-spotlight__title {
        margin: 0 0 0.4rem;
        color: var(--color-kidical-ink);
    }
    .ride-spotlight__when {
        color: var(--color-kidical-blue);
        font-weight: 800;
        margin: 0 0 0.4rem;
    }
    .ride-spotlight__loc {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 40%);
        margin: 0 0 1.1rem;
    }
    .ride-spotlight__loc-icon {
        width: 1.1rem;
        height: 1.1rem;
        color: var(--color-kidical-red);
    }
    .ride-spotlight__cta {
        display: inline-block;
        background: var(--color-kidical-yellow);
        color: var(--color-kidical-ink);
        font-family: var(--font-heading);
        font-weight: 800;
        border-radius: 999px;
        padding: 0.6rem 1.2rem;
    }

    @media (max-width: 640px) {
        .ride-spotlight {
            grid-template-columns: 1fr;
        }
        .ride-spotlight__media--empty {
            min-height: 7rem;
        }
    }
}
```

- [ ] **Step 5: Register the partial in `app.css`**

In `resources/css/app.css`, after the `@import './components/ride-day.css';` line, add:

```css
@import './components/ride-spotlight.css';
```

- [ ] **Step 6: Run test to verify it passes**

Run: `php artisan test --compact --filter=RideSpotlightComponentTest`
Expected: PASS (3 passing).

- [ ] **Step 7: Commit**

```bash
git add resources/views/components/ride-spotlight.blade.php resources/css/components/ride-spotlight.css resources/css/app.css tests/Feature/RideSpotlightComponentTest.php
git commit -m "feat(rides): add unified <x-ride-spotlight> component"
```

---

### Task 6: Wire the calendar (Livewire view) to the new grouping components

**Files:**
- Modify: `resources/views/livewire/ride-calendar.blade.php:53-78`

- [ ] **Step 1: Replace the band component calls**

In `resources/views/livewire/ride-calendar.blade.php`, change the past-rides branch:

```blade
                @elseif ($when === 'voorbije')
                    <div class="kal-days">
                        @foreach ($byPeriod as $periodKey => $rides)
                            <x-kal-month-band :period-key="$periodKey" :rides="$rides" />
                        @endforeach
                    </div>
```

to:

```blade
                @elseif ($when === 'voorbije')
                    <div class="kal-days">
                        @foreach ($byPeriod as $periodKey => $rides)
                            <x-ride-month :period-key="$periodKey" :rides="$rides" />
                        @endforeach
                    </div>
```

And change the upcoming branch:

```blade
                @else
                    <div class="kal-days">
                        @foreach ($byPeriod as $periodKey => $rows)
                            <x-kal-day-band :period-key="$periodKey" :rows="$rows" />
                        @endforeach
                    </div>
                @endif
```

to:

```blade
                @else
                    <div class="kal-days">
                        @foreach ($byPeriod as $periodKey => $rows)
                            <x-ride-day :period-key="$periodKey" :rows="$rows" />
                        @endforeach
                    </div>
                @endif
```

- [ ] **Step 2: Add a `.kal-days` gap rule (the old `.kal-day + .kal-day` margin was removed)**

In `resources/css/pages/calendar.css`, add inside `@layer components`:

```css
    .kal-days {
        display: flex;
        flex-direction: column;
        gap: 1.75rem;
    }
```

(`.ride-day` already provides its own `+ .ride-day` border/margin; this gap covers the past-view month bands consistently. The `+` selectors still apply within each group.)

- [ ] **Step 3: Verify the calendar renders**

Run: `php artisan test --compact --filter=RideCalendar`
Expected: PASS (existing calendar tests still green). If no such test exists, smoke-check in Task 11.

- [ ] **Step 4: Commit**

```bash
git add resources/views/livewire/ride-calendar.blade.php resources/css/pages/calendar.css
git commit -m "refactor(calendar): render rides via ride-day/ride-month components"
```

---

### Task 7: Wire the homepage next-ride and drop the distance text

**Files:**
- Modify: `resources/views/home.blade.php:41-56`

- [ ] **Step 1: Replace the next-ride render block**

In `resources/views/home.blade.php`, replace:

```blade
                @if ($nextRideIsFar)
                    <p class="text-kidical-ink/70">Geen rit vlakbij op dit moment. De eerstvolgende iets verderaf:</p>
                @endif

                <div class="event-list">
                    <x-event-card :activity="$nextRide" />
                </div>

                @if ($nextRideDistanceKm !== null)
                    <p class="home-nextride__distance text-sm font-semibold text-kidical-ink/60">
                        {{ str_replace('.', ',', (string) $nextRideDistanceKm) }} km van jou
                    </p>
                @endif

                <livewire:location-picker />
```

with:

```blade
                @if ($nextRideIsFar)
                    <p class="text-kidical-ink/70">Geen rit vlakbij op dit moment. De eerstvolgende iets verderaf:</p>
                @endif

                <x-ride-day :period-key="$nextRide->begin_date->toDateString()" :rows="[['item' => $nextRide]]" />

                <livewire:location-picker />
```

- [ ] **Step 2: Remove the now-dead `.home-nextride__distance` rule if present**

Run: `rg -n "home-nextride__distance" resources/css`
If it returns a match, delete that rule block from the file it lives in. If no match, skip.

- [ ] **Step 3: Verify the homepage controller no longer needs the distance variable**

The distance value is still computed in the controller but no longer displayed; leave the controller untouched (proximity still orders the next ride). No code change required here.

- [ ] **Step 4: Commit**

```bash
git add resources/views/home.blade.php resources/css
git commit -m "refactor(home): render next ride via ride-day lockup; drop distance text"
```

---

### Task 8: Wire the chapter page (spotlight + agenda rows)

**Files:**
- Modify: `resources/views/groups/show.blade.php:96-146`

- [ ] **Step 1: Replace the populated next-ride card**

In `resources/views/groups/show.blade.php`, replace the `@if ($nextRide)` branch's `<article class="chapter-next__card"> … </article>` (lines ~97–112) with:

```blade
                <x-ride-spotlight :activity="$nextRide" :cta="true" />
```

Leave the `@else` empty-state branch (`chapter-next__card--empty` with the notify form) exactly as it is — it is a designed empty state, not a ride render.

- [ ] **Step 2: Replace the agenda list rows**

Replace the `$rest` loop (lines ~129–146):

```blade
            @if ($rest->isNotEmpty())
                <ul class="chapter-agenda__list">
                    @foreach ($rest as $activity)
                        @php $m = $typeMeta($activity->activity_type); @endphp
                        <x-agenda-item
                            :badge="$m['label']"
                            :badge-variant="$m['mod']"
                            :datetime="$activity->begin_date->format('Y-m-d\TH:i')"
                            :when="$activity->begin_date->locale('nl')->isoFormat('dd D MMM · HH:mm')"
                            :title="$activity->title_nl"
                            :location="$activity->location"
                            :cta-href="route('activities.show', $activity)"
                            :cta-label="$m['cta']"
                            :quiet="$m['quiet']"
                        />
                    @endforeach
                </ul>
            @endif
```

with:

```blade
            @if ($rest->isNotEmpty())
                <div class="chapter-agenda__list">
                    @foreach ($rest as $activity)
                        <x-ride-row :activity="$activity" :show-date="true" />
                    @endforeach
                </div>
            @endif
```

- [ ] **Step 3: Add a row separator for the flat agenda list**

In `resources/css/pages/chapters.css`, find the `.chapter-agenda__list` rule. Ensure consecutive rows get a separator by adding (inside `@layer components`):

```css
    .chapter-agenda__list .ride-row + .ride-row {
        border-top: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 92%);
    }
```

(If `.chapter-agenda__list` had `<ul>`-specific styling such as `list-style`, it is harmless on a `<div>`; leave other rules intact.)

- [ ] **Step 4: Verify the chapter page renders**

Run: `php artisan test --compact --filter=Group`
Expected: existing group/chapter tests pass. (Smoke-checked further in Task 11.)

- [ ] **Step 5: Commit**

```bash
git add resources/views/groups/show.blade.php resources/css/pages/chapters.css
git commit -m "refactor(chapter): use ride-spotlight + ride-row on the agenda"
```

---

### Task 9: Wire the detail-page header to the spotlight

**Files:**
- Modify: `resources/views/activities/show.blade.php:7-54` (hero) and `:70` (Startuur time)

- [ ] **Step 1: Replace the hero section**

In `resources/views/activities/show.blade.php`, replace the whole `<section class="activity-hero"> … </section>` block (lines ~8–54) with:

```blade
    {{-- HERO — unified ride spotlight (page subject, so h1, no self-CTA) --}}
    <section class="activity-hero-wrap">
        <div class="container mx-auto px-4">
            <x-ride-spotlight :activity="$activity" heading="h1" />
        </div>
    </section>
```

- [ ] **Step 2: Replace the Startuur time format**

In the same file, in the `Startuur` info item, replace:

```blade
                                <time datetime="{{ $activity->begin_date->toIso8601String() }}">{{ $activity->begin_date->translatedFormat('H\hi') }}</time>
```

with:

```blade
                                <time datetime="{{ $activity->begin_date->toIso8601String() }}">{{ $activity->timeLabel }}</time>
```

- [ ] **Step 3: Add a small wrapper spacing rule and remove dead hero CSS**

In `resources/css/pages/activity.css`, add inside `@layer components`:

```css
    .activity-hero-wrap {
        margin-top: 2rem;
        margin-bottom: 2.5rem;
    }
```

Then delete the now-unused `.activity-hero`, `.activity-hero__inner`, `.activity-hero__copy`, `.activity-hero__date`, `.activity-hero__chapter`, `.activity-hero__chapter-pin`, `.activity-hero__chapter-label`, `.activity-hero__visual`, `.activity-hero__photo`, `.activity-hero__img`, and `.activity-hero__daisy` rules from `activity.css`. (Leave all `.activity-info-*`, `.activity-map-*`, `.activity-promises*`, `.activity-organizers*`, and `.activity-actions-bar*` rules untouched.)

- [ ] **Step 4: Verify the detail page renders**

Run: `php artisan test --compact --filter=Activity`
Expected: existing activity tests pass.

- [ ] **Step 5: Commit**

```bash
git add resources/views/activities/show.blade.php resources/css/pages/activity.css
git commit -m "refactor(activity): use ride-spotlight as the detail header"
```

---

### Task 10: Update styleguide, remove old components & dead CSS

**Files:**
- Modify: `resources/views/styleguide.blade.php`
- Delete: `resources/views/components/event-card.blade.php`
- Delete: `resources/views/components/agenda-item.blade.php`
- Delete: `resources/views/components/kal-day-band.blade.php`
- Delete: `resources/views/components/kal-month-band.blade.php`
- Delete: `resources/css/components/event-card.css`
- Delete: `resources/css/components/agenda-item.css`
- Modify: `resources/css/app.css` (remove the `agenda-item.css` import)

- [ ] **Step 1a: Add workshop + meeting demo activities to the controller**

The agenda demos in the styleguide are hardcoded strings, so the merged row needs real models. In `app/Http/Controllers/StyleguideController.php`:

- Add `use App\Enums\ActivityType;` if not present.
- Give `sampleActivity()` an optional type param. Change its signature to:
  `private function sampleActivity(string $title, string $location, int $id, int $days = 2, ActivityType $type = ActivityType::KIDICALMASS): Activity`
  and inside, add `'activity_type' => $type,` to the `new Activity([...])` array.
- In the controller action, build two more samples and pass them to the view alongside `$activity`/`$activityB`:

```php
$workshop = $this->sampleActivity('Fietsherstel workshop', 'Wijkcentrum, Gent', 3, days: 5, type: ActivityType::WORKSHOP);
$meeting = $this->sampleActivity('Teamvergadering', 'Online', 4, days: 7, type: ActivityType::MEETING);
```

Add `'workshop' => $workshop, 'meeting' => $meeting,` to the array passed to `view('styleguide', …)`.

- [ ] **Step 1b: Update styleguide markup**

In `resources/views/styleguide.blade.php`:
- Replace the two `<x-event-card :activity="$activity" :show-date="false" />` / `$activityB` usages (lines ~174–175) with `<x-ride-row :activity="$activity" :show-date="true" />` and `<x-ride-row :activity="$activityB" :show-date="true" />`.
- Replace the three hardcoded `<x-agenda-item … />` demos (lines ~142–144) with:

```blade
                        <x-ride-row :activity="$activity" :show-date="true" />
                        <x-ride-row :activity="$workshop" :show-date="true" />
                        <x-ride-row :activity="$meeting" :show-date="true" />
```

- Replace the manual `<x-kal-day-band>` / two-line `.kal-day__date*` demo block (around lines 165–172) with `<x-ride-day :period-key="$activity->begin_date->toDateString()" :rows="[['item' => $activity]]" />`.

Run `rg -n "event-card|agenda-item|kal-day-band|kal-month-band|kal-day__" resources/views/styleguide.blade.php` and resolve every hit.

- [ ] **Step 2: Remove the `agenda-item.css` import from `app.css`**

In `resources/css/app.css`, delete the line:

```css
@import './components/agenda-item.css';
```

(The `event-card.css` import was already swapped to `ride-row.css` in Task 3.)

- [ ] **Step 3: Confirm no remaining references to the old components**

Run: `rg -n "x-event-card|x-agenda-item|x-kal-day-band|x-kal-month-band" resources/ app/`
Expected: no output. If anything remains, convert it before deleting.

- [ ] **Step 4: Delete the old files**

```bash
git rm resources/views/components/event-card.blade.php \
       resources/views/components/agenda-item.blade.php \
       resources/views/components/kal-day-band.blade.php \
       resources/views/components/kal-month-band.blade.php \
       resources/css/components/event-card.css \
       resources/css/components/agenda-item.css
```

- [ ] **Step 5: Confirm no dangling CSS class references**

Run: `rg -n "event-card|agenda-item|kal-day__|kal-day-band" resources/ ` 
Expected: no output (all `.kal-day*` rules were removed in Task 4; all component refs gone). Resolve any stragglers.

- [ ] **Step 6: Commit**

```bash
git add resources/views/styleguide.blade.php resources/css/app.css app/Http/Controllers/StyleguideController.php
git commit -m "refactor(rides): remove legacy event-card/agenda-item/kal-band patterns"
```

---

### Task 11: Full verification

**Files:** none (verification only)

- [ ] **Step 1: Run the CSS architecture test**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS. (Confirms every partial is registered and no raw hex/px leaked into a `.blade.php` component.)

- [ ] **Step 2: Build the frontend**

Run: `npm run build`
Expected: builds with no errors; Vite manifest written.

- [ ] **Step 3: Run the full test suite**

Run: `php artisan test --compact`
Expected: all green. Fix any failures caused by removed components/markup before proceeding.

- [ ] **Step 4: Smoke-test the four surfaces for JS/render errors**

Add `tests/Feature/RideSurfacesSmokeTest.php` (Pest browser/HTTP smoke — follow the project's existing smoke-test style; if HTTP-level):

```php
<?php

use App\Models\Activity;

it('renders the ride surfaces without errors', function () {
    Activity::factory()->count(3)->create(['begin_date' => now()->addDays(3)]);

    // Public routes are under a {locale} prefix; only 'nl' is supported today.
    $this->get(route('home', ['locale' => 'nl']))->assertOk();
    $this->get(route('activities.index', ['locale' => 'nl']))->assertOk();

    $activity = Activity::query()->first();
    $this->get(route('activities.show', ['locale' => 'nl', 'activity' => $activity]))->assertOk();
});
```

Run: `php artisan test --compact --filter=RideSurfacesSmokeTest`
Expected: PASS.

- [ ] **Step 5: Visual confirmation via Herd**

Use the `get-absolute-url` tool for each of `/`, the calendar route, a chapter route, and an activity detail route. Load them (Playwright screenshot helper or browser) and confirm: date-rail lockup on home + calendar, flat quiet rows, chip only on workshops/meetings, light spotlight with daisy fallback on chapter + detail. No `14:00` / `km van jou` / "dit weekend" anywhere.

- [ ] **Step 6: Final commit (if smoke test added)**

```bash
git add tests/Feature/RideSurfacesSmokeTest.php
git commit -m "test(rides): smoke-test the consolidated ride surfaces"
```

---

## Notes for the executor

- **Isolation:** run this in a git worktree (Nico commits in the same checkout). Stage by explicit path; never `git add -A`.
- **Order matters:** Tasks 1–5 build primitives (each independently testable); 6–9 wire them in; 10 removes the old; 11 verifies. Do not delete old components (Task 10) before all call sites are converted (Tasks 6–9).
- **Locale default:** the app is NL-only today; FR strings render correctly the day the locale flips, with no template changes.

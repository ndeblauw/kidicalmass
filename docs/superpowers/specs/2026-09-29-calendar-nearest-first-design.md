---
title: Kalender "nearest first, nothing hidden" - design + plan
tags: [calendar, location, proximity, livewire, build]
sources:
  - app/Livewire/RideCalendar.php
  - resources/views/livewire/ride-calendar.blade.php
  - app/Support/Location/Proximity.php
  - app/Support/Location/NextRideFinder.php
  - app/Livewire/LocationPicker.php
  - resources/views/livewire/location-picker.blade.php
phase: build
updated: 2026-09-29
---

# Kalender: nearest first, nothing hidden - design + plan

**Date:** 2026-09-29
**Decision:** direction B from Frederik's decision review.

## Problem

With a location set, three radius tabs (Dichtbij / In de regio / Heel België) filter the upcoming list.
The default is Dichtbij (5 km), so a visitor outside Brussels lands on an empty page while all upcoming rides are in Brussels.
The tab row also overflows on mobile (`.filter-bar__radius { flex-shrink: 0 }`).

## Direction

Nothing is filtered away any more.
With a location set, upcoming rides are grouped into three distance sections, each in date order.
Without a location, and in the past view, the page stays exactly as it is today.

| Band key | Heading (NL) | Rule |
|---|---|---|
| `nearby` | Bij jou in de buurt | distance <= `config('location.nearby_radius_km')` (5) |
| `region` | In de regio | > nearby and <= `config('location.regio_radius_km')` (30) |
| `far` | Verder in België | > regio, or postcode not resolvable |

Rendering rules:

- Only non-empty sections render, in band order.
- When the first one or more bands are empty, one quiet line sits above the first rendered section: "Nog geen rit binnen :km km van :place.", with `:km` the radius of the last empty leading band.
- An empty band after a filled one is simply skipped, no line.
- Every ride row shows its distance when known.
- So the page is never empty while any upcoming ride exists.

## Shared rule: `Proximity::partitionByBands`

New pure helper in `app/Support/Location/Proximity.php`, no DB, unit-tested:

```php
/**
 * @param  array<string, float>  $bands  name => inclusive upper bound in km, ascending
 * @return array<string, Collection<int, array{item: T, distance_km: float|null}>>
 *         one key per band in the given order, plus a trailing 'far'
 */
public static function partitionByBands(Collection $items, array $origin, array $bands, callable $coordsOf): array
```

- A row lands in the first band whose bound is >= its distance (bounds inclusive, as today).
- A null distance (unresolvable coordinates) or a distance beyond the last bound lands in `far`.
- Input order is preserved in every band.
- `far` is reserved: a caller-supplied band named `far` is a programming error (throw `InvalidArgumentException`), so it can never silently merge with the overflow bucket.
- `partitionByRadius()` becomes a one-line wrapper: `partitionByBands($items, $origin, ['nearby' => $radiusKm], $coordsOf)`.
  Its signature and return shape stay, so `NextRideFinder` and its tests do not change, and calendar and homepage share one rule: unresolvable postcodes count as far.

## Files to change

| File | Change |
|---|---|
| `app/Support/Location/Proximity.php` | add `partitionByBands`; `partitionByRadius` delegates to it |
| `app/Livewire/RideCalendar.php` | drop `#[Url] $radius`, `setRadius()`, the inline filter, `radius` and `isEmpty` view vars; build `sections` + `emptyLead` via `partitionByBands` |
| `resources/views/livewire/ride-calendar.blade.php` | drop the tabs from the `<x-filter-bar>` slot and the `$isEmpty` branch; add the grouped branch |
| `resources/views/components/ride-proximity-section.blade.php` | new: one band (heading, subline, days) |
| `resources/views/components/ride-day.blade.php` | pass `$row['distance_km'] ?? null` to `<x-ride-row>` |
| `resources/views/components/ride-row.blade.php` | new optional `distanceKm` prop, rendered at the end of the meta line |
| `resources/views/components/filter-bar.blade.php` | comment only: slot no longer used for radius tabs |
| `app/Livewire/LocationPicker.php` | fix reactive `clear()`: the re-render still shows the old place (see "wis" below) |
| `resources/views/livewire/location-picker.blade.php` | "wis" button next to "wijzig" |
| `resources/css/effects.css` | add `.location-picker__clear` to the reduced-motion list next to `.location-picker__change` |
| `resources/css/components/filter-bar.css` | delete dead radius/tab rules |
| `resources/css/components/ride-row.css` | `.ride-row__distance` |
| `resources/css/components/location-picker.css` | `.location-picker__clear`, wrap the compact current line |
| `config/location.php` | comment on `regio_radius_km` (no more tab; also used by `VolunteerController`) |
| `lang/nl/calendar.php`, `lang/fr/calendar.php` | remove + add keys (below) |
| `lang/nl/common.php`, `lang/fr/common.php` | add location clear + distance keys |
| `tests/Unit/Location/ProximityTest.php` | +1 test |
| `tests/Feature/Location/CalendarProximityTest.php` | rewrite: filter tests become grouping tests |
| `tests/Feature/Location/LocationPickerTest.php` | +3 tests, extend both key lists |

Dead-code check (grep of `app resources lang tests config routes`):

- `setRadius`, `filter_label`, `calendar.radius.*`, `empty_radius`, `empty_radius_hint`, `filter-bar__radius*`, `filter-bar__tab*` are used only by the calendar, so all go.
- `x-filter-bar` itself stays (calendar still hosts the compact picker in it); only its slot content goes.
- `config('location.regio_radius_km')` stays: it drives the region band and `VolunteerController`.
- `tests/_archive/*` mention `filter-bar__tab` but are archived and not run; leave them.
- Livewire ignores unknown query params, so an old `?radius=dichtbij` link loads fine once the `#[Url]` property is gone.

## Data shape passed to the view

Past view and no-location view: unchanged (`byPeriod` keyed by `Y-m` or `Y-m-d`, rows `['item' => Activity, 'distance_km' => null]`), with `sections => null` and `emptyLead => null`.

Upcoming with a location:

```php
[
    'when' => 'aankomend',
    'location' => array{zip, lat, lng, name},
    'byPeriod' => null,
    'sections' => list<array{
        band: 'nearby'|'region'|'far',
        radius_km: float|null,          // band bound, null for far
        byDay: Collection<string, Collection<int, array{item: Activity, distance_km: float|null}>>,  // keyed Y-m-d
    }>,                                 // non-empty bands only, in band order
    'emptyLead' => array{km: float, place: string}|null,  // set when the nearby band (and maybe region) is empty
    'hasActivities' => bool,
    'rideCount' => int,                 // all upcoming rides; nothing is hidden
]
```

`emptyLead.km` is the bound of the last empty band before the first rendered one: 5 when only nearby is empty, 30 when nearby and region are both empty.
`emptyLead` is `null` when the nearby band has rides, and also when there are no upcoming rides at all (that case keeps the existing `! $hasActivities` empty state, which the view checks first).
`sections` is therefore never an empty list while `hasActivities` is true, because `far` catches every ride the other bands do not.

## Blade structure

In `ride-calendar.blade.php`, the upcoming branch becomes:

```blade
@elseif ($sections)
    @if ($emptyLead)
        <p class="kal-empty" data-proximity-empty-lead>
            {{ __('calendar.proximity.none_within', ['km' => \Illuminate\Support\Number::format($emptyLead['km'], maxPrecision: 1, locale: app()->getLocale()), 'place' => $emptyLead['place']]) }}
        </p>
    @endif
    <div class="flex flex-col gap-12">
        @foreach ($sections as $section)
            <x-ride-proximity-section :band="$section['band']" :radius-km="$section['radius_km']" :by-day="$section['byDay']" />
        @endforeach
    </div>
@else
    {{-- unchanged no-location list --}}
```

New `resources/views/components/ride-proximity-section.blade.php`:

```blade
@props(['band', 'radiusKm' => null, 'byDay'])
<section data-proximity-section="{{ $band }}" aria-labelledby="proximity-{{ $band }}">
    <header class="mb-6">
        <h2 id="proximity-{{ $band }}">{{ __("calendar.proximity.$band.title") }}</h2>
        <p class="mt-2 text-text-body">{{ __("calendar.proximity.$band.sub", ['km' => \Illuminate\Support\Number::format((float) $radiusKm, maxPrecision: 1, locale: app()->getLocale())]) }}</p>
    </header>
    <div class="flex flex-col gap-7">
        @foreach ($byDay as $dayKey => $rows)
            <x-ride-day :period-key="$dayKey" :rows="$rows" />
        @endforeach
    </div>
</section>
```

- Raw `<h2>` so the heading takes its size from `@layer base`, the same as the past view's month headings (`ride-month__head`).
- Only token-backed utilities, no raw hex/px.
- `data-proximity-section` is the test seam.

In `ride-row.blade.php`, after the venue:

```blade
@if ($distanceKm !== null)
    <span class="ride-row__distance" data-distance-km="{{ $distanceKm }}">
        {{ $distanceKm < 1
            ? __('common.distance_under_km')
            : __('common.distance_km', ['km' => \Illuminate\Support\Number::format($distanceKm, maxPrecision: $distanceKm < 10 ? 1 : 0, locale: app()->getLocale())]) }}
    </span>
@endif
```

- `intl` is loaded, so `Number::format` gives the locale's decimal comma ("3,5 km").
- A plain span, not a `<dl>`: it is a single value in a line that is already a link.

In `location-picker.blade.php`, inside `.location-picker__current`, after the "wijzig" button:

```blade
<button type="button" wire:click="clear" x-on:click="stashScroll()"
        class="location-picker__clear link-plain" data-location-clear
        aria-label="{{ __('common.location.clear_label') }}">{{ __('common.location.clear') }}</button>
```

- Non-reactive mode (every current call site) already works: `clear()` queues a forgotten cookie and redirects to `url()->previous()` with `navigate: true`, so the page reloads without a location.
- Reactive mode is broken today and must be fixed: `clear()` sets `$selected = null`, but `render()` falls back to `$this->selected ?? CurrentLocation::resolve()`, and `resolve()` reads the cookie from the *current* Livewire request, which still carries it (the forget is only queued on the response).
  The picker re-renders "Je fietst rond Jette wijzig wis" as if nothing happened.
  Fix: add `public bool $cleared = false;`, set it in `clear()`, reset it in `persist()`, and make `render()` pass `'current' => $this->cleared ? null : ($this->selected ?? CurrentLocation::resolve())`.
  The existing reactive test only asserts the dispatch, which is why this was never caught.
- Nobody listens to `location-selected` yet and no call site passes `reactive`, so this fix has no visible effect on today's pages; it keeps the decision's "works in both modes" honest.
- Every current call site (calendar filter bar, Lokale groepen, homepage, Meehelpen) uses the compact picker, so one markup change covers all four.

Screen-reader status line: it keeps `calendar.none` when there are no upcoming rides, otherwise `trans_choice('calendar.found', $rideCount)` with `rideCount` = all upcoming rides.
The `$isEmpty` condition goes.

## Copy

All copy in `lang/nl` and `lang/fr`, no em dashes, FR says "parade".

Removed from `calendar.php` (both locales): `filter_label`, `radius.nearby`, `radius.region`, `radius.belgium`, `empty_radius`, `empty_radius_hint`.

Added to `calendar.php`:

| Key | NL | FR |
|---|---|---|
| `proximity.nearby.title` | Bij jou in de buurt | Près de chez vous |
| `proximity.nearby.sub` | tot :km km, de kinderen fietsen zelf | jusqu'à :km km, les enfants pédalent eux-mêmes |
| `proximity.region.title` | In de regio | Dans la région |
| `proximity.region.sub` | tot :km km, de kinderen achterop of in de bakfiets | jusqu'à :km km, les enfants à l'arrière ou dans le vélo-cargo |
| `proximity.far.title` | Verder in België | Ailleurs en Belgique |
| `proximity.far.sub` | voor een uitstap | pour une excursion |
| `proximity.none_within` | Nog geen rit binnen :km km van :place. | Pas encore de parade à moins de :km km de chez vous (:place). |

The FR line keeps every preposition away from :place, so no elision is needed ("de Anvers" would be wrong). The space between :km and "km" is a non-breaking space in the lang files, in NL and FR.

Added to `common.php`:

| Key | NL | FR |
|---|---|---|
| `location.clear` | wis | effacer |
| `location.clear_label` | Wis je locatie | Effacer votre position |
| `distance_km` | :km km | :km km |
| `distance_under_km` | minder dan 1 km | à moins d'un km |

Side fix S-1 (newsletter link capitalisation):

- The only mid-sentence embed was `empty_radius_hint` ("…, of Schrijf je in…" / "ou Inscrivez-vous…"), which is removed.
- The remaining embed, `empty_none`, puts `:link` at the start of a sentence, so the capitalised `calendar.newsletter_link` is correct there.
- No lowercase variant key is needed now; if a future sentence embeds the link mid-sentence, add `calendar.newsletter_link_inline` then.

Side fix (FR "parade" wording, same file):

- `empty_none`: "Aucune parade n'est prévue pour le moment. La saison s'étend de mars à novembre. :link, et les nouvelles parades apparaîtront immédiatement."
- `empty_past`: "Il n'y a pas encore de parades passées à afficher."

## CSS

- `resources/css/components/filter-bar.css`: delete `.filter-bar__radius`, `.filter-bar__radius-label`, `.filter-bar__tabs`, `.filter-bar__tab` (+ `::after`, `:hover`, `--active`), and their mobile overrides.
  Keep `.filter-bar`, `.filter-bar__loc` and the mobile `.filter-bar` padding rule.
- `resources/css/components/ride-row.css`, inside the existing `@layer components`: `.ride-row__distance { color: color-mix(in oklab, var(--color-text-body), transparent 30%); font-variant-numeric: tabular-nums; }`.
- `resources/css/components/location-picker.css`, unlayered like its siblings:
  - `.location-picker__clear` mirrors the compact `.location-picker__change` text-link look (muted underline, blue on hover, same enlarged tap area);
  - in the non-compact state it is a plain underlined text link, not a second yellow pill;
  - add `flex-wrap: wrap` to `.location-picker--compact .location-picker__current` so "Je fietst rond Sint-Pieters-Woluwe wijzig wis" wraps at 375 px instead of overflowing.
- Nothing goes in `app.css`, and `pages/calendar.css` stays as is (sections reuse `.kal-empty` and utilities).

## TDD task list

1. **Band helper.**
   Add a failing test to `tests/Unit/Location/ProximityTest.php`: "partitions items into ordered distance bands, with unresolvable items far".
   Use Jette origin, items jette (0), schaarbeek (~3.5), antwerpen (~38), vilvoorde (~9), unknown (null), bands `['nearby' => 5, 'region' => 30]`.
   Expect `nearby` = jette, schaarbeek; `region` = vilvoorde; `far` = antwerpen, unknown, in input order.
   Implement `partitionByBands`, rewrite `partitionByRadius` as the wrapper.
   Run `ProximityTest` and `NextRideFinderTest`: all green.
2. **Grouped calendar.**
   Rewrite `tests/Feature/Location/CalendarProximityTest.php` (same `beforeEach`: Jette ride, Gent ride):
   - keep "shows all rides unfiltered when no location is set", adding `assertDontSee('data-proximity-section', false)`;
   - "groups rides nearest first, keeping unresolvable postcodes in the far section": add a Vilvoorde ride (postcode 1800, lat 50.9281, lng 4.4250, ~9 km from Jette, so region band) and a ride with postcode `9999` (no row); cookie Jette; `assertSeeHtmlInOrder` (not `assertSeeInOrder`, which escapes the quotes in the attribute strings and can never match) on `data-proximity-section="nearby"`, Jette, `data-proximity-section="region"`, Vilvoorde, `data-proximity-section="far"`, Gent, the `9999` ride; `assertSee('data-distance-km', false)`;
   - "skips an empty band after a filled one": cookie Jette, `beforeEach` rides only; sees nearby + far, `assertDontSee('data-proximity-section="region"', false)`, no `data-proximity-empty-lead`;
   - "collapses empty leading bands into one line": cookie Brugge (8000, lat 51.2093, lng 3.2247), so both rides are more than 30 km away (Gent ~39 km, Jette ~85 km); `assertSee(__('calendar.proximity.none_within', ['km' => '30', 'place' => 'Brugge']))`, only the far section, and `trans_choice('calendar.found', 2)` in the status line;
   - "ignores a legacy radius query param": `Livewire::withQueryParams(['radius' => 'dichtbij'])` + Jette cookie; still sees Gent.
   Watch them fail, then rewrite `RideCalendar::render()`, the view branch, `x-ride-proximity-section`, the `ride-day`/`ride-row` distance prop and the `calendar.proximity.*` + `common.distance_*` keys.
   Run `--filter=CalendarProximity` and `tests/Feature/Events/PublicPagesTest.php`.
3. **Remove the radius filter.**
   Delete `$radius`, `setRadius`, the tab markup, the removed lang keys, the dead filter-bar CSS; update the `config/location.php` comment, the `filter-bar.blade.php` comment, and the sr-only status comment in `ride-calendar.blade.php` ("changes with every radius/when switch" becomes "with every when switch or location change").
   `grep -rnE "setRadius|filter_label|calendar\.radius|empty_radius|filter-bar__(radius|tab)" app resources lang config tests/Feature tests/Unit` returns nothing.
4. **"wis" link.**
   Add failing tests to `LocationPickerTest.php`:
   - "clears the location and redirects in non-reactive mode": `Livewire::test(LocationPicker::class)->call('clear')->assertRedirect()`, and the queued cookie for `config('location.cookie')` is expired;
   - "offers a clear link next to change when a location is set": `set('selected', …)` then `assertSeeHtml('data-location-clear')` and `assertSee(__('common.location.clear'))`.
   - "clears the shown location in reactive mode": `Livewire::withCookie('kcm_location', json_encode([...Jette...]))`, then `Livewire::test(LocationPicker::class, ['reactive' => true])->assertSeeHtml('data-location-clear')->call('clear')->assertDontSeeHtml('data-location-clear')->assertSee(__('common.location.prompt'))`.
     This one fails today (see the reactive-mode note under "wis"); watch it fail, then add the `$cleared` flag.
   Also add `common.location.clear` / `clear_label` to the two key lists already in that file.
   Then add the button, the keys and the CSS.
5. **FR copy fixes** in `lang/fr/calendar.php` (`empty_none`, `empty_past`).
6. **Finish.**
   - `"$HOME/Library/Application Support/Herd/bin/php84" vendor/bin/pint --dirty --format agent`;
   - `npm run build`;
   - `"$HOME/Library/Application Support/Herd/bin/php84" artisan test --compact`: 0 failed (baseline 316 passed / 85 skipped, plus the new tests), `CssArchitectureTest` included.
7. **End-to-end check, one screenshot pass.**
   On https://kidical-anadyr.test/nl/events and /fr/agenda:
   - with no location;
   - with a Brussels postcode (nearby section first);
   - with a far postcode such as 8000 Brugge (one quiet line, then "Verder in België");
   - after "wis" (back to the plain list);
   - also `/nl/events?radius=dichtbij` and `?when=voorbije`.
   Check at 375 px and 1440 px that the filter bar no longer overflows and the picker line wraps cleanly.
   Spot-check "wis" on the homepage, Lokale groepen and Meehelpen.

## Out of scope

- Homepage `NextRideFinder` behaviour (it only gains the shared helper underneath).
- Lokale groepen distance bands.
- A newsletter hint under the quiet line.
- Pipeline row update (offer a `/pipeline` bump for the Kalender row after Frederik's own critique).

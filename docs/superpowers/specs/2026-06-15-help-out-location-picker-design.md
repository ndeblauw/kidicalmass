---
title: Location picker on Help-out "Vind je lokale groep"
tags: [design, help-out, location-picker, groups]
sources: [resources/views/volunteer.blade.php, app/Http/Controllers/VolunteerController.php, app/Support/Location/Proximity.php]
phase: design
updated: 2026-06-15
---

# Location picker on Help-out — "Vind je lokale groep"

## Problem

The Help-out page (`P-13`, `volunteer.blade.php`) closes with a *"Vind je lokale groep"*
section that lists **every** visible chapter as a flat alphabetical pill list, each
linking to that chapter's volunteer sign-up form. As more chapters launch this list grows
unbounded, and a volunteer still has to scan the whole country to find their own town.

The rest of the site (home, calendar, groups index) already has a shared, cookie-backed
`<livewire:location-picker>` that lets a visitor set "where I cycle" once and have nearby
content surfaced. Help-out should use the same tool.

## Decision

Make the location picker the **gateway** for this section: the picker is the initial
content, and once a location is set we show the **4 nearest chapters** — never the full
list.

### Behaviour

- **No location set (initial):** the section shows its title, a short lead, and
  `<livewire:location-picker :compact="true" />`. No group list. The picker *is* the call
  to action, so the section can't balloon as chapters multiply.
- **Location set:** a heading *"Het dichtst bij {plaats}"* followed by the **4 nearest
  chapters** as pills. Each pill links to that chapter's volunteer sign-up form exactly as
  today:
  `route('groups.show', ['group' => $group, 'intent' => 'volunteer']) . '#aanmelden'`.
- The existing coda below (*"Nog geen lokale groep in je buurt?"* with the
  *"alle groepen →"* link to `groups.index`) stays unchanged. It is the escape hatch to
  the full directory, so the full list never needs to live in this section.

### Count-based, not radius-based

The groups index partitions chapters by a 5 km radius for the *"in de buurt"* band. Here
we want the **4 nearest regardless of distance**, so a volunteer always has something to
tap (decision: "Always show 4"). Because the nearest 4 can include a genuinely far
chapter, the heading uses *"Het dichtst bij {plaats}"* ("closest to you"), **not**
*"in de buurt"*, so it never overpromises proximity. No distance numbers on the pills
(matches the existing `.ho-group` pill style).

If fewer than 4 chapters exist, we show however many exist. Chapters with no resolvable
postal-code coordinates are excluded from the ranking (they cannot be sorted by distance);
they remain reachable via the *"alle groepen →"* link in the coda.

## Components

### `Proximity::nearest()` (new)

A count-based companion to the existing `partitionByRadius()`, in
`app/Support/Location/Proximity.php`:

```php
/**
 * The $count items closest to $origin, annotated with distance and sorted ascending.
 * Items whose coordinates resolve to null are dropped (cannot be ranked by distance).
 *
 * @template T
 * @param  Collection<int, T>  $items
 * @param  array{lat: float, lng: float}  $origin
 * @param  callable(T): (array{lat: float, lng: float}|null)  $coordsOf
 * @return Collection<int, array{item: T, distance_km: float}>
 */
public static function nearest(Collection $items, array $origin, int $count, callable $coordsOf): Collection
```

Implementation: map each item to `['item' => $item, 'distance_km' => distanceKm(...)]`,
reject null-coord items, `sortBy('distance_km')`, `take($count)`, `values()`.

### `VolunteerController` (changed)

Mirror the location handling in `GroupController::index`:

- Load visible chapters with `id`, `name`, `zip` (as today).
- Resolve `CurrentLocation::resolve()` into `$location`.
- When `$location` is set: look up the chapters' postal-code coordinates
  (`PostalCode::whereIn('zip', ...)->keyBy('zip')`) and build
  `$nearestGroups = Proximity::nearest($groups, [...], 4, $coordsOf)`.
- Pass `$location` and `$nearestGroups` to the view (both default to `null` / empty
  collection when no location).

The full `$groups` list is no longer rendered in the view, but the count lookup still
loads visible chapters to feed the proximity ranking.

### `volunteer.blade.php` (changed)

Replace the `.ho-groups` flat-list block inside `<section class="ho-find">` with:

1. The lead paragraph (kept; copy may be lightly trimmed since there is no list).
2. `<div class="ho-find__picker"><livewire:location-picker :compact="true" /></div>`
   (mirrors `grp-find__picker` on the groups index).
3. `@if ($location && $nearestGroups->isNotEmpty())` → an `<h3>` *"Het dichtst bij
   {{ $location['name'] }}"* and a `<ul class="ho-groups">` of the nearest chapters,
   reusing the existing `.ho-group` / `.ho-group__name` / `.ho-group__zip` markup and CSS.

No change to the location-picker component or the shared `kcm_location` cookie. Any new
styling (e.g. a wrapper around the picker) lives in `resources/css/pages/help-out.css`,
never `app.css`.

## Testing

Pest feature tests against the Help-out route (`route('volunteer')`):

- **No location cookie** → page renders, the picker is present, and no `.ho-group` pills
  are shown.
- **Location cookie set** (use a `PostalCode` near a seeded chapter) → exactly the 4
  nearest chapters render, in ascending distance order, each linking to a URL containing
  `intent=volunteer` and the `#aanmelden` fragment; a 5th, farther chapter is absent.
- A chapter with no `zip` / unresolvable coordinates is excluded from the nearest list.

Add a unit-level expectation for `Proximity::nearest()` (ordering, count cap, null-coord
exclusion) alongside the existing Proximity coverage.

Run: `php artisan test --compact --filter=HelpOut` (+ the Proximity filter).

## Out of scope

- Region grouping (Brussel / Wallonië / Vlaanderen) — Help-out stays flat.
- Distance labels on pills.
- Any change to the location-picker component, the cookie, or other pages.
- A map.

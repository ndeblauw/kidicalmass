---
title: Lokale groepen — list + map finder
tags: [design, groups, map, leaflet, location]
sources: [brainstorm session 2026-06-15, prototype .superpowers/brainstorm/68276-*]
phase: design
updated: 2026-06-15
---

# Lokale groepen — list + map finder

## Purpose

Replace the pill directory + stat-card rail on the Lokale groepen page (`P-10`,
`groups.index`) with a Booking/Airbnb-style **list + map finder**: a scannable
list of local groups on the left synced to a Leaflet map on the right. The list
serves people who don't want to read a map; the map serves people who think
spatially. A region selector and the shared location picker sit in a control bar
above both.

This supersedes the interim "arrange" pass done earlier this session (picker
moved under the find lead, cards pulled up) — that layout is replaced wholesale.

## Page composition (top to bottom)

1. **Blue page hero** — unchanged (`<x-page-hero>` eyebrow/title/illustration).
2. **Intro lead** — the existing one-paragraph `<x-intro-text>`, full width in the panel.
3. **Control bar** — region selector (left) + location picker (right).
4. **Split** — results list (left, ~45%) + Leaflet map (right, ~55%).
5. **Closing CTA** — unchanged (`<x-closing-cta>` "Staat jouw stad er nog niet bij?").

The `grp-directory` two-column grid, `grp-scale` stat cards, `grp-pill` region
groupings, and the full-bleed `filter-bar` are all removed from this page.

## Control bar

- **Region selector** — segmented pill buttons: `Heel België (27)` (default
  active) · `Brussel (n)` · `Wallonië (n)` · `Vlaanderen (n)`. Counts are
  rendered server-side from the grouped data. Each non-"all" button carries a
  colour dot matching its region's marker colour, so the selector doubles as the
  map legend.
- **Location picker** — the existing shared `<livewire:location-picker :compact="true" />`.
  It keeps owning the persistent shared location (cookie) used across Kalender /
  home. It is NOT reimplemented client-side.

Region filtering is a transient client-side view filter (no reload). Setting a
location goes through the Livewire picker (round-trip, sets cookie); the page
re-renders with the resolved location and the map reacts on load.

## Data flow

`GroupController@index` already resolves `$groups` (visible, with `parent`),
`$activityCount`, `$location`, `$nearby`, `$myGroups`. Extend it to build a
**markers view-model** for the island:

For each visible group with a resolvable `zip → lat/lng` (via
`PostalCode::coordsFor`), emit:

```
{ name, slug, url, region, regionLabel, zip, lat, lng }
```

- `region` = parent group name (`Brussels Capital Region` | `Wallonia` |
  `Flanders`); `regionLabel` = NL label (`Brussel` | `Wallonië` | `Vlaanderen`).
- `url` = `route('groups.show', $group)`.
- Region counts (`['Brussels Capital Region' => 17, ...]`) for the button labels.
- Groups without coordinates are still listed (as plain links) but get no pin.

Shape this in the controller (small private method `mapMarkers(Collection $groups): array`)
or a thin `App\Support\Groups\GroupMarker` transformer if it grows. Pass
`$markers`, `$regionCounts`, and the resolved `$location` coords to the view.

## Rendering — progressive enhancement

The list is **server-rendered as real links** so the page works with no JS and
is crawlable:

- Each card is an `<a href="{{ route('groups.show', $group) }}">` with the
  region-coloured dot, group name, and postcode.
- The user's own groups (`$myGroups`) are pinned to the top of the list,
  visually marked, when authenticated.

JS then **enhances**: it reads a `@json($markers)` island, initialises Leaflet,
draws the pins, and wires the list ↔ map sync. Without JS the map shell is
simply absent (or shows a static fallback note) and the link list stands alone.

## Map + sync (client-side island)

Reuse the `activities/show` pattern: Leaflet 1.9 via the existing CDN include and
CartoDB `light_all` tiles, `divIcon` teardrop pins. No new dependencies, no Vite
Leaflet bundle.

- **Markers** colour-coded by region (Brussel `--color-kidical-blue`, Wallonië
  `--color-kidical-orange`, Vlaanderen `--color-kidical-green`).
- **Default** — all pins, `fitBounds` to Belgium; list alphabetical.
- **Hover card** → matching pin scales up / comes to front.
- **Click card** → `flyTo` the pin, open its popup (name + "Bekijk groep" link),
  highlight the card.
- **Click pin** → scroll the list to that card + highlight.
- **Region button** → filter list to the region, fade out-of-region pins,
  `fitBounds` to the region, update the results count + status pill.
- **Location set** (server-resolved on load, or via the picker) → sort the list
  by distance with a "~X km" badge per card, drop a "Jij bent hier" marker,
  `fitBounds` to the nearest ~5 groups.

Sync state (active group, current region filter) lives in a small Alpine
component or a plain module reading the island; keep it isolated from Livewire.

## Layout / responsive

- Split is a CSS grid `minmax(330px, 0.82fr) 1fr`, fixed height
  `clamp(540px, 74vh, 780px)`; the list scrolls internally beside a steady map.
- Under 760px: single column — list first, then a ~360px map below (validated in
  prototype; no floating "Kaart" toggle for v1).

## Styling

- Page-specific styles → `resources/css/pages/local-groups.css` (extend; remove
  the now-dead `grp-directory` / `grp-scale` / `grp-pill` rules).
- Marker/pin appearance shared with the picker pin lives near
  `components/location-picker.css`; add a small `components/group-map.css` only if
  the map chrome (status pill, card sync states) is reused elsewhere — otherwise
  keep it in `pages/local-groups.css`.
- No raw hex/px in Blade components; tokens only (enforced by `CssArchitectureTest`).
- Headings use raw `<h1>`–`<h3>`; Caprasimo display / Nunito Sans body via tokens.

## Testing

- Update `tests/Feature/GroupsFilterBarTest.php` (currently asserts
  `grp-find__picker`): assert the finder renders — control bar present, location
  picker present, region buttons with correct server-side counts, and the
  markers island contains each visible group.
- Feature test: each visible group name appears as a link to `groups.show`
  (no-JS list works); a group with a known `zip` produces a marker with lat/lng.
- Feature test: region counts match the grouped data.
- Keep `assertDontSee('filter-bar__tab')` (radius tabs are agenda-only).
- Optional Pest browser smoke test for JS errors on the page.

## Out of scope (v1)

- Next-ride date on list cards (declined — keep the list lean).
- Region text label on cards (declined — the colour dot conveys region).
- Marker clustering (27 groups; revisit only if the count grows large).
- Mobile "Kaart" toggle button (stacked layout is enough for now).

## Pipeline

After build + verify, bump `P-10` in the page registry: Wire/UI as appropriate,
update Top gaps ("map finder live"), refresh the roll-up, and append a
`build` entry to `docs/wiki/log.md`.

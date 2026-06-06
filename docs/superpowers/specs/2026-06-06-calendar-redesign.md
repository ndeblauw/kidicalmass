---
title: Kalender redesign — geïntegreerde locatiefilter + compact agenda
tags: [kalender, ux, livewire, frontend]
phase: build
updated: 2026-06-06
---

# Kalender redesign — geïntegreerde locatiefilter + compact agenda

## Overview

A set of coordinated changes to the Kalender page (P-02) addressing three problems from the June 2026 critique:

1. **Horizontal space wasted** — agenda fills full container width with no constraint.
2. **Location picker and radius control are separate** — conceptually one control, visually split across hero and body.
3. **Event row reads badly** — city name redundant in venue string, pin icon misaligned.

The solution: a filter row that bridges hero and agenda containing both the location picker and the radius selector as one integrated unit; a two-column page layout (agenda + sticky sidebar); a compact date column within the agenda; and event row cleanup.

The existing `location-picker` Livewire component is absorbed into `RideCalendar` — one component owns all location + filter state.

---

## 1. Page layout

### Two-column grid

The `kal-body` area becomes a two-column grid:

| Column | Width | Content |
|--------|-------|---------|
| Left | `1fr` | Agenda (filter row + ride list) |
| Right | `260px` fixed | Sticky sidebar panel |

On mobile (below `md` breakpoint), the right column is hidden entirely. The existing yellow `kal-optin` band at the bottom remains the mobile CTA.

### Filter row placement

A new filter row sits **between the hero and the agenda** — permanently visible, spanning the full width of the left agenda column (not across the sidebar). It replaces the location picker that previously lived inside the hero. The hero becomes purely brand copy, no UI controls.

---

## 2. Filter row — the integrated location + radius control

### State A — No location set (first visit)

```
[ 📍  Jouw buurt   Typ postcode of gemeente… ]  |  Dicht bij   Ruimere regio   Heel België
```

- **Left (picker)**: A pill/input with dashed border, label "Jouw buurt", muted placeholder text "Typ postcode of gemeente…". Clicking opens the existing autocomplete behaviour from `location-picker`.
- **Separator**: 1px vertical rule.
- **Right (radius tabs)**: Three tab buttons — "Dicht bij", "Ruimere regio", "Heel België" — all visually greyed out (opacity ~0.3, `cursor: not-allowed`). A small label above reads "Hoe ver wil je kijken?".
- The full structure is visible immediately. Greyed tabs teach the user what's possible before they act.
- The ride list shows **all rides** unfiltered — the filter row is an invitation, not a gate.

### State B — Location set

```
[ 📍  Ganshoren  ▾ ]  |  [Dicht bij]  Ruimere regio  Heel België
```

- **Left (picker)**: Solid pill, location name in blue, small caret to change.
- **Separator**: 1px vertical rule.
- **Right (radius tabs)**: Three tab buttons, active one filled (blue background, white text). Default active: "Dicht bij".
- The ride list filters to match the active radius.

### Three radius positions

| Tab | Label | Radius | Config key |
|-----|-------|--------|------------|
| 1 (default) | Dicht bij | 7 km | `location.nearby_radius_km` |
| 2 | Ruimere regio | 30 km | `location.regio_radius_km` (new) |
| 3 | Heel België | ∞ | — |

### State persistence

The active radius tab is stored as a URL-bound Livewire property (`radius`). Values: `dichtbij` | `regio` | `belgie`. Refreshing or sharing the URL preserves the filter.

### Past rides view

The filter row is hidden when viewing past rides (`when === 'voorbije'`). Past rides are always a flat monthly list — proximity is irrelevant.

---

## 3. Sticky sidebar

### With location set → Newsletter CTA

Yellow `kal-optin`-style box:
- Heading: "Mis geen rit"
- Body: "Één seintje per maand met ritten bij jou in de buurt. Geen spam, altijd uitschrijfbaar."
- Button: "Schrijf je in"

### Without location set → Location nudge

Blue tinted box:
- Pin icon
- Body: "Stel je buurt in en zie alleen de ritten bij jou in de buurt."
- Button: "Stel locatie in" — clicking focuses the picker in the filter row.

The sidebar switches between these two states reactively as the location is set or cleared.

---

## 4. Compact day column within the agenda

Each `kal-day` section changes from a stacked layout to a two-column grid:

```
[ Date col: 64px ]  [ Rides col: 1fr ]
```

**Date column** (left, 64px):
- Day abbreviated + date on two lines: "Zo / 7 jun" — uppercase, bold, ink colour.
- "Vandaag" / "Morgen" / "Dit weekend" landmark pill below the date.

**Rides column** (right):
- Event rows stack with hairline dividers between rows within a day.

Day-to-day dividers are full-width hairlines spanning both columns.

The existing `h3` date heading is removed — the date column IS the heading.

---

## 5. Event row

### Pin before city name

The map-pin icon moves to immediately before the city/municipality:

```
📍 Laken   Ossegempark   14:00
```

Pin and city are flush left. Venue grows to fill. Time at right edge.

### Simplified venue string

Strip trailing `, <municipality>` from the venue string when it matches the display name (case-insensitive). Done in the blade template — no database change.

Example: venue "Karreveldpark, Molenbeek" + city "Molenbeek" → display "Karreveldpark".

---

## 6. "Bekijk voorbije ritten" link

Moves from its current top-right position to **below the last day group** in the ride list. Same styling (muted underline link).

---

## 7. Behaviour matrix

| Location set? | Active tab | Ride list |
|---|---|---|
| No | — (tabs disabled) | All rides, flat chronological |
| Yes | Dicht bij | Rides ≤ 7 km, flat chronological |
| Yes | Ruimere regio | Rides ≤ 30 km, flat chronological |
| Yes | Heel België | All rides, flat chronological |

**Empty state** (location set, no rides in radius):
> "Geen ritten binnen [label] van [naam]. Kies een ruimere regio om meer te zien."

---

## 8. Component architecture

### RideCalendar absorbs location-picker

The existing `location-picker` Livewire component is no longer rendered separately in the hero. Its logic (autocomplete, cookie write, geolocation) moves into `RideCalendar` or a dedicated Alpine component wired to it. One component owns all state.

The hero slot in `ride-calendar.blade.php` removes `<livewire:location-picker />` and the `kal-herofilter` wrapper. The filter row appears in the `kal-body` section instead.

### Files affected

| File | Change |
|------|--------|
| `app/Livewire/RideCalendar.php` | Add `#[Url] $radius`; absorb location state; filter by radius in `render()` |
| `app/Livewire/LocationPicker.php` | Retire or keep as utility; no longer rendered standalone in the hero |
| `resources/views/livewire/ride-calendar.blade.php` | Remove hero picker slot; add filter row; two-column body grid; sidebar; move past-rides link |
| `resources/views/components/kal-day-band.blade.php` | Two-column date/rides layout; remove h3 heading |
| `resources/views/components/event-card.blade.php` | Pin before city; venue strip logic |
| `resources/css/app.css` | Two-column body grid; filter row styles; sidebar sticky; compact date col; tab styles; update kal-day and event-card rules |
| `tests/Feature/Location/CalendarProximityTest.php` | Update for new radius filter behaviour |
| `config/location.php` | Add `regio_radius_km: 30` |

### Proximity filtering

`Proximity::partitionByRadius()` is no longer called. `RideCalendar::render()` uses `Proximity::distanceKm()` directly to annotate activities with distance, then filters to those within the active radius. No section grouping — one flat chronological list.

---

## 9. What is NOT changing

- Cookie format (`kcm_location`) and `CurrentLocation::resolve()`.
- `Proximity::distanceKm()` haversine calculation.
- The `kal-optin` yellow closing band (mobile CTA, remains unchanged).
- Route names and URL structure.
- Past rides view — flat monthly list, filter row hidden.

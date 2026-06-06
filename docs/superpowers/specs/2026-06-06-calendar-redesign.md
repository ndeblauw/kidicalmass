---
title: Kalender redesign — layout B + radius slider
tags: [kalender, ux, livewire, frontend]
phase: build
updated: 2026-06-06
---

# Kalender redesign — layout B + radius slider

## Overview

A set of coordinated changes to the Kalender page (P-02) that solve three problems identified in the June 2026 critique:

1. **Horizontal space wasted** — the agenda fills full container width with no constraint.
2. **Proximity distinction unclear** — "In de buurt / Verderaf" section headers are too subtle to signal a zone change.
3. **Event row reads badly** — city name redundant in location string, pin icon misaligned.

The solution: a two-column page layout (agenda + sticky CTA), a compact date column within the agenda, a three-position radius slider replacing the section headers, and event row cleanup.

---

## 1. Page layout

### Two-column grid

The `kal-body` area becomes a two-column grid:

| Column | Width | Content |
|--------|-------|---------|
| Left | `1fr` (fills available) | Agenda list |
| Right | `260px` fixed | Sticky CTA panel |

The right column is sticky — the CTA panel scrolls with the user until the bottom of the agenda, then stops. On mobile (below `md` breakpoint), the right column is hidden entirely; the existing yellow opt-in band at the bottom of the page remains the mobile CTA.

### Agenda max-width

The left column itself gets no explicit max-width — it naturally fills the remaining space. The two-column grid effectively constrains row width on wide viewports.

---

## 2. Compact day column within the agenda

Each `kal-day` section changes from a stacked layout (date heading, then rides below) to a two-column grid:

```
[Date col: 64px]  [Rides col: 1fr]
```

**Date column** (left, 64px):
- Day abbreviated + date on two lines, e.g. "Zo / 7 jun" — uppercase, bold, ink colour.
- The "Vandaag" / "Morgen" / "Dit weekend" landmark pill appears below the date, smaller.

**Rides column** (right):
- Event rows stack exactly as today, with hairline dividers between rows.

Dividers between day groups remain but are now full-width hairlines spanning both columns.

The existing large `h3` day heading is removed — the date column IS the heading.

---

## 3. Radius slider

### Concept

Replaces the binary "In de buurt van X / Verderaf" section structure with a single three-position discrete slider that filters the flat ride list. The list is always one chronological sequence — no section headers.

### Three positions

| Position | Label | Radius |
|----------|-------|--------|
| 1 (default) | Dicht bij | 7 km (existing `location.nearby_radius_km` config) |
| 2 | Ruimere regio | 30 km |
| 3 | Heel België | ∞ (no filter) |

The slider snaps — it is not continuous. Dragging or clicking any position triggers a Livewire update that re-filters the ride list.

### Placement

The slider sits above the agenda list (immediately below the hero area, before the first day group). It spans the full width of the left column.

### Label display

Active label is shown at full opacity; the other two are muted. Example when on position 1:

```
📍 Dicht bij    Ruimere regio    Heel België
[●────────────────────────────────────]
```

### Without location set

The slider renders but is visually disabled (track greyed out, thumb locked at position 1, labels muted). A short inline prompt appears below: *"Stel je locatie in om te filteren op afstand."* Clicking the prompt focuses the location picker in the hero.

### State persistence

The selected position is stored in the Livewire component as a URL-bound property (`radius`) so that refreshing or sharing the URL preserves the filter. Values: `dichtbij` | `regio` | `belgie`.

### Voorbije ritten

The slider is hidden when the user is viewing past rides (`when === 'voorbije'`).

---

## 4. Event row changes

### Pin before city name

The map-pin icon moves to immediately before the city/municipality name — not before the venue. Every row reads as one location unit: `📍 Laken · Ossegempark · 14:00`.

Visually: pin and city are flush left together, venue in the middle (grows), time at the right edge.

### Simplified venue string

The city name is frequently repeated at the end of the venue string (e.g. "Karreveldpark, Molenbeek" when city is already "Molenbeek"). Strip the trailing `, <city>` portion when it matches the municipality name (case-insensitive). This is done in the blade template with a small PHP helper, not in the database.

Matching rule: if the venue string ends with `, {municipality}` (after stripping the "Kidical Mass " prefix from the title), remove it. No match = leave unchanged.

### Row column order

```
[📍 City — bold blue]   [Venue — muted, grows]   [Time — right edge]
```

This order is already implemented from the earlier session. No change needed here.

---

## 5. "Bekijk voorbije ritten" link

Moves from its current position (top-right of the agenda, above the slider) to **below the ride list**, after the last day group. Same styling (muted underline link), same behaviour.

---

## 6. What is NOT changing

- The location picker in the hero (Livewire `location-picker` component) — unchanged.
- The `kcm_location` cookie format and `CurrentLocation::resolve()` — unchanged.
- `Proximity::distanceKm()` — reused to compute distances for filtering. `partitionByRadius()` is no longer called in RideCalendar; replaced by a simple annotate-then-filter step using the slider's active radius.
- The `kal-optin` yellow closing band — remains as-is; it's the mobile CTA.
- Past rides view (`when === 'voorbije'`) — flat monthly list, no slider, no sidebar needed. Slider hidden. Sidebar still visible.
- The `RideCalendar` Livewire component name and route — unchanged.

---

## 7. Behaviour matrix

| Location set? | Slider position | Result |
|---------------|----------------|--------|
| No | — (disabled) | All rides, flat chronological list |
| Yes | Dicht bij | Rides ≤ 7 km, flat chronological |
| Yes | Ruimere regio | Rides ≤ 30 km, flat chronological |
| Yes | Heel België | All rides, flat chronological |

When location is set and no rides fall within the selected radius, show an inline empty-state message: *"Geen ritten binnen [label] van [naam]. Schuif de slider verder om meer te zien."*

---

## 8. Components affected

| File | Change |
|------|--------|
| `app/Livewire/RideCalendar.php` | Add `#[Url] $radius` property; filter by radius in render() |
| `resources/views/livewire/ride-calendar.blade.php` | Two-column grid; slider markup; remove section headers; move past-rides link |
| `resources/views/components/kal-day-band.blade.php` | Two-column date/rides layout; remove h3 heading |
| `resources/views/components/event-card.blade.php` | Move pin before city; venue strip logic |
| `resources/css/app.css` | New two-column grid; slider styles; compact date column; sidebar sticky; update kal-day, event-card rules |
| `tests/Feature/Location/CalendarProximityTest.php` | Update for new slider/radius behaviour |
| `config/location.php` | Add `regio_radius_km: 30` alongside existing `nearby_radius_km: 7` |

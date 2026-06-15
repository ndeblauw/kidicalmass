---
title: Location-picker consistency + home "volgende rit" states
tags: [design, location-picker, home, calendar, groups]
sources: [audit of home/calendar/groups usages, onboard prototype]
phase: build
updated: 2026-06-15
---

# Location-picker consistency + home onboarding states

## Problem

The location picker appears on three public pages and feels inconsistent. All three
already render the **same** `<livewire:location-picker />` — the divergence is in
placement and skin:

| Page | Where it sits | Skin |
|---|---|---|
| **Kalender** (target) | Top of white panel, full-bleed bar, with radius tabs beside it | Compact |
| **Lokale groepen** | Inside the blue hero (`controls` slot) | Big default form |
| **Home** | Mid-page in "De volgende rit bij jou" (rendered twice) | Big default form |

The calendar's **compact skin** is the wanted look everywhere. Separately, the home
section has a content gap: when no location is set it shows **only the picker and no
rides at all**.

## Decisions (from brainstorming)

- The **compact skin** is shared by all three pages.
- The **radius tabs** (Dichtbij / In de regio / Heel België) stay **agenda-only**.
  Home and Groups get the picker without tabs. No shared radius state — radius stays
  calendar-local (URL-bound on `RideCalendar`), unchanged.
- **Groups** moves out of the blue hero to the **top of the white panel** (to match
  the calendar). **Home** keeps the picker **in the "volgende rit" section** (its
  current spot), only restyled — because there the picker is contextual to the rides.
- Home must **always show rides**: nearby preferred, falling back to the nearest
  reachable ride (this is what `NextRideFinder` already does for the single ride).

## Part 1 — The shared picker skin

### Compact as a picker modifier

`LocationPicker` gains a `public bool $compact = false;` prop. Its view root becomes
`location-picker {{ $compact ? 'location-picker--compact' : '' }}`. The compact
overrides in `resources/css/components/location-picker.css` (currently lines ~236–333,
scoped under `.kal-filterrow …`) re-key to `.location-picker--compact …` so the skin
works **anywhere**, not only inside the calendar bar. The compact skin hides the big
pin and label, shrinks the input, and flattens the located pill.

### `<x-filter-bar>` — the panel-top shell

New Blade component `resources/views/components/filter-bar.blade.php`: the full-bleed,
panel-top bar that wraps `<livewire:location-picker :compact="true" />` plus a
`{{ $slot }}` for optional controls (the radius tabs). Used by **calendar** and
**groups** only.

New CSS partial `resources/css/components/filter-bar.css` holds the bar shell + radius
tab styling, renamed from `.kal-filterrow*` → `.filter-bar*`. These rules move out of
`resources/css/pages/calendar.css`. The partial is registered in
`tests/Feature/CssArchitectureTest.php`. No raw hex/px — tokens only.

## Part 2 — Per-page placement

- **Agenda** (`ride-calendar.blade.php`): replace the inline `.kal-filterrow` markup
  with `<x-filter-bar>` and pass the radius tabs into its slot. The tabs keep their
  `wire:click="setRadius(...)"` bindings (they resolve to the enclosing `RideCalendar`
  Livewire component). `RideCalendar` radius logic is unchanged.
- **Groepen** (`groups/index.blade.php`): remove the `<x-slot:controls>` +
  `.grp-hero__locate` wrapper from the blue hero; render `<x-filter-bar />` at the top
  of the panel (default slot). `GroupController` ordering logic unchanged. Delete the
  unused `.grp-hero__locate` reference.
- **Home**: see Part 3 — does **not** use `<x-filter-bar>`; the picker stays in the
  section with `:compact="true"`.

## Part 3 — Home "De volgende rit bij jou" states

Reworks the `home-nextride` section. All rides render with the existing
`<x-ride-day>` component; the picker is `<livewire:location-picker :compact="true" />`.

### State A — no location (first visit)

- Heading: **"Volgende ritten"** (the honest, generic title).
- Show the **soonest few upcoming rides** (target 3), grouped by date into
  `<x-ride-day>` blocks — same pattern the calendar uses for `$byPeriod`.
- Below the rides, a **paired-affordance row** (two doors to the same content):
  - **Left — "Ritten bij jou":** the compact picker (input + "Mijn locatie" geo
    button) with sub-copy *"Vul je gemeente in en we zetten de ritten dichtbij
    bovenaan."*
  - **Right — "Of bekijk alles":** link to the agenda, *"Alle ritten in de agenda →"*,
    sub-copy *"De volledige kalender, van maart tot november."*
- The top-right "Bekijk alle ritten →" link is **omitted in this state** (its job is
  done by the paired row's right side). It remains in the located states.

### State B — location set

- Heading: **"De volgende rit bij jou"**.
- Compact located **pill** at the top of the section: `📍 Ritten rond {gemeente} ·
  wijzig`.
- The nearest ride below, via `<x-ride-day>`, with its distance badge.
- Top-right "Bekijk alle ritten →" link present.

### State B′ — located, nothing nearby (fallback)

- Same as B, plus the existing note *"Geen rit vlakbij op dit moment. De eerstvolgende
  iets verderaf:"* and the nearest reachable ride flagged far (amber distance badge).

### State (off-season) — unchanged

`! $hasUpcoming` keeps the current season message.

### Backend

`NextRideFinder::find()` already builds the full `$upcoming` collection. Extend its
return array with `upcoming_preview` (the soonest ~3 rides) so State A can render them
without a second query. `HomeController` passes `$upcomingRides` to the view. The
single-ride logic (`ride`, `distance_km`, `is_far`, `has_upcoming`) is unchanged.

## Out of scope

- No shared radius cookie / `RadiusBand` enum / `CurrentRadius` (rejected in favour of
  keeping radius calendar-local).
- No changes to `GroupController` proximity ordering or the calendar radius behaviour.
- No new picker behaviour — autocomplete, geolocation, cookie persistence and redirect
  all stay as they are in `LocationPicker`.

## Testing

- `LocationPicker` renders `.location-picker--compact` when `:compact` is set.
- `<x-filter-bar>` renders on calendar and groups at panel top; radius tabs appear in
  the slot only on the calendar.
- Home: State A (no cookie) shows ≥1 ride **and** the picker; heading is "Volgende
  ritten". State B (cookie set) heading is "De volgende rit bij jou" and shows the
  pill. State B′ shows the far-ride note.
- `CssArchitectureTest` green: `filter-bar.css` registered, no raw hex/px, no orphan
  `.kal-filterrow*`.
- Note: `CalendarProximityTest` has a known order-dependent flake in the full suite
  (passes in isolation) — not a regression signal.

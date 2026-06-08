---
title: Ride display consistency — one vocabulary, one row, one lockup, one spotlight
tags: [design, components, rides, activities, date-format, i18n]
sources: [visual-companion brainstorm 2026-06-08, inventory of ride surfaces]
phase: design
updated: 2026-06-08
---

# Ride display consistency

## Problem

A ride (`Activity`) is rendered across six public surfaces, each having grown its
own markup and its own date/time strings. The same field renders five different
ways (`14u` vs `14:00`, `zo 14 jun` vs `zondag 14 juni` vs UPPERCASE), two
near-identical "row" components exist (`event-card`, `agenda-item`), and two
bespoke "spotlight" treatments exist (chapter next-ride card, detail-page hero)
sharing no code. The site must also work in **NL and FR**, but every date today
hardcodes `->locale('nl')`.

## Goal

Reduce the number of patterns: **one date/time vocabulary, one row, one day
lockup, one ride-spotlight** — all locale-aware. Consistency comes from shared
primitives, not from forcing every surface to look identical.

## Scope

In scope: the date/time formatting layer; the list-row component; the
day/date lockup and list framing; the ride-spotlight (hero) component; a field
audit deciding which ride fields appear where.

Out of scope: the detail-page **info panel + route map** (`PAT-4`,
genuinely single-use — left as-is), backend/data changes, and the
`activity-promises` band (not a ride display).

---

## Decision 1 — Date/time vocabulary (`RideDate`)

A small stateless formatter `app/Support/RideDate.php`. Locale comes from
`app()->getLocale()` (`nl`/`fr`) — never hardcoded. Accepts any `Carbon`
(so calendar period keys work too, not just `Activity` dates).

| Method | NL | FR | Carbon basis |
|---|---|---|---|
| `RideDate::time($d)` | `14u` / `14u30` | `14h` / `14h30` | hour + locale separator + minutes; drop `00` |
| `RideDate::short($d)` | `zo 14 jun` | `di 14 jan` | `isoFormat('dd D MMM')` |
| `RideDate::full($d)` | `zondag 14 juni` | `dimanche 14 juin` | `isoFormat('dddd D MMMM')` |
| `RideDate::monthYear($d)` | `juni 2026` | `juin 2026` | `isoFormat('MMMM YYYY')` |

Rules:
- The **only** locale-specific custom logic is the time separator (`nl → u`,
  `fr → h`). Everything else is Carbon's own localisation.
- Output is **lowercase**. Casing is a CSS concern (`text-transform`), never
  baked into the string. A leading capital (e.g. spotlight `Zondag…`) is done
  with `ucfirst()` in the template — presentation, not data.
- **Date + time combos are composed in the template**, not new methods:
  `{{ ucfirst(RideDate::full($d)) }} · {{ RideDate::time($d) }}`.
- **Thin `Activity` accessors** delegate for clean ride templates:
  `timeLabel`, `dateShort`, `dateFull`, `dateMonthYear` →
  `RideDate::time($this->begin_date)` etc. Calendar bands call `RideDate::`
  directly on the period date.

## Decision 2 — One merged row (`<x-ride-row>`)

Replaces both `<x-event-card>` and `<x-agenda-item>` with a single component.

Anatomy:
- **Commune name** is the sole emphasised anchor: bold, `--color-kidical-blue`
  (or `--color-kidical-orange` when featured). Derived from `title_nl`/`title_fr`
  with the "Kidical Mass " prefix stripped, as today.
- **Meta line** `14u @ Jubelpark`: one muted colour; **time stays semibold**,
  `@` and venue share the muted venue colour (treatment "A" — quiet, one meta
  colour, two weights total on the line).
- **Type chip** appears **only when the activity is not a normal ride**
  (workshop / meeting get a coloured chip). A normal Kidical Mass ride shows no
  chip. Featured rides keep the orange star + "Uitgelicht" badge.
- The date comes from the lockup (Decision 3) in grouped contexts, so the row
  is date-free there. For **lockup-less contexts** (the chapter agenda is a flat
  list, not grouped by day), the row takes a `showDate` prop that prepends an
  inline `RideDate::short()` badge. Default: `showDate = false` (grouped is the
  common case); the chapter agenda passes `showDate = true`.

CSS: `resources/css/components/ride-row.css` (token-backed utilities only).

## Decision 3 — One framing + lockup (date rail everywhere)

**Framing:** the date-lockup wraps **every** ride list, including the homepage
single "volgende rit" teaser (which uses the exact same lockup as the calendar,
just with one ride under it). No separate inline-date treatment.

**Lockup — slim date rail:** a light typographic rail anchors each day group:
- big day number (`14`), month lowercase (`juni`), weekday uppercase (`zo`) —
  stacked, **no box**, type only.
- rides for that day sit to the right as `<x-ride-row>`s.
- **No "vandaag / morgen / dit weekend" landmark.** Removed.

**Drop the distance text.** No "3,4 km van jou" anywhere. Proximity still drives
which rides surface and their ordering; the commune name communicates nearness.
No precise distance number is shown.

Grouping:
- **Upcoming** rides → grouped by **day**, each day a date-rail lockup.
- **Past** rides → grouped by **month**, a light `RideDate::monthYear()` header
  above the rows (lighter than the day rail; long history reads better by month).

Components: a `<x-ride-day>` (date rail + its rows) reused on home + calendar,
and a `<x-ride-month>` (month header + rows) for past. Both replace the current
`kal-day-band` / `kal-month-band` and both render `<x-ride-row>`.
CSS stays in `resources/css/pages/calendar.css` for grouping layout; the rail
is a component partial if reused on home (`components/ride-day.css`).

## Decision 4 — One ride-spotlight (`<x-ride-spotlight>`)

Replaces the chapter "next ride" card and the detail-page hero header with one
component. Light treatment ("B"):
- white surface, `--radius`/`shadow-card`, side photo column, blue used as an
  **accent** (not a full fill), yellow CTA pill.
- Fields: chapter tag, title, `full · time`, location ("Verzamelen: …"),
  optional photo, optional CTA.
- Props: `cta` (toggles the CTA — present on the chapter teaser, absent on the
  detail header which is the ride's own page) and the photo (optional).
- **No-photo fallback = brand motif** ("B"): when there is no image, the photo
  column stays and is filled with a soft brand panel + daisy mark, keeping the
  identical two-column rhythm photo-or-not. (Photos are the exception, so the
  layout must look intentional without one.)

CSS: `resources/css/components/ride-spotlight.css`.

## Decision 5 — Field audit (which fields appear where)

| Field | Row | Spotlight | Detail info panel (PAT-4) |
|---|---|---|---|
| commune / title | ✓ | ✓ | — |
| date | via lockup | ✓ (`full`) | — |
| time | ✓ | ✓ | ✓ (`Startuur`) |
| location / venue | ✓ | ✓ | ✓ (`Vertrekpunt`) |
| activity type | chip if ≠ ride | — | — |
| featured | star + badge | — | — |
| chapter / group | — | ✓ | ✓ |
| distance | — | — | ✓ (`Afstand`) |
| duration | — | — | ✓ (`Duur`) |
| description | — | — | ✓ |
| photo | — | optional | — |
| route map / komoot | — | — | ✓ |

Distance and duration live **only** in the detail info panel. The row never
carries distance/duration/chapter/image. This is the deliberate vocabulary.

---

## Call sites to convert

- `resources/views/components/event-card.blade.php` → delete; replaced by
  `<x-ride-row>`.
- `resources/views/components/agenda-item.blade.php` → delete; chapter agenda
  uses `<x-ride-row>` (chip shows for workshop/meeting).
- `resources/views/components/kal-day-band.blade.php` →
  `<x-ride-day>` (new date rail).
- `resources/views/components/kal-month-band.blade.php` → `<x-ride-month>`.
- `resources/views/home.blade.php` — next-ride block uses `<x-ride-day>` with a
  single ride; remove the `home-nextride__distance` line.
- `resources/views/livewire/ride-calendar.blade.php` — render the new
  day/month group components; the `dichtbij/regio` proximity still orders rides
  but no distance is displayed.
- `resources/views/groups/show.blade.php` — next-ride card →
  `<x-ride-spotlight :cta>`; agenda → `<x-ride-row>`s.
- `resources/views/activities/show.blade.php` — hero header →
  `<x-ride-spotlight>` (no CTA); `Startuur` uses `timeLabel`; info panel + map
  unchanged otherwise.
- `resources/views/styleguide.blade.php` — update entries to the new components.
- Anywhere using `->locale('nl')->isoFormat(...)` or manual `G\u` / `H\hi`
  time logic → `RideDate` / accessors.

## CSS architecture compliance

New partials registered per the CSS-partials rules: reusable units →
`resources/css/components/*.css` (`ride-row.css`, `ride-spotlight.css`,
`ride-day.css`), page-level grouping → `resources/css/pages/calendar.css`.
No raw hex/px in component `.blade.php`; appearance utilities reference tokens
(`bg-kidical-*`, `rounded-card`, `shadow-card`). Old `event-card.css` /
`agenda-item.css` removed. Must pass `CssArchitectureTest`.

## Testing

- `RideDate` unit test (Pest): all four methods in **both** locales, including
  the `14u`/`14h` separator and the whole-hour `:00` drop.
- `<x-ride-row>` render test: normal ride shows no chip; workshop shows chip;
  featured shows star/badge; time renders `14u`.
- `<x-ride-spotlight>` render test: CTA present/absent by prop; no-photo
  fallback renders the motif, not a broken image.
- Calendar grouping test: upcoming groups by day rail, past groups by month;
  no distance string in output.
- Smoke test the affected pages (home, calendar, chapter, detail) for JS errors.

## Out of scope / YAGNI

- No new date variants beyond the four.
- No change to the detail info panel + route map.
- No FR content authoring — the formatter is FR-ready; FR strings render the day
  the locale flips, with zero template changes.

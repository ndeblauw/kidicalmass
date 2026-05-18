# Activity Map — Design Spec

**Date:** 2026-04-08
**Branch:** design/activity-page

---

## Context

The activity detail page (`/activities/{id}`) already renders a Leaflet map when a GPX file is attached. Routes are planned in Komoot and exported as GPX. The current map shows only a pink polyline and a plain circle marker at the start point.

The map's job is to help visitors answer: *"Is this ride worth the trip — right neighbourhood, right distance, right kind of route?"* It is a decision-support tool, not a navigation tool.

---

## Approach

**Improve the existing Leaflet map + add an optional Komoot link.**

Keep the Leaflet + OpenStreetMap stack but upgrade it: better tile layer, branded departure marker, smart loop/point-to-point detection, distance/duration strip, and an optional "Bekijk op Komoot →" link for riders who want full route details or navigation.

---

## Design Decisions

### Tile layer
Replace the standard OSM tiles with **CartoDB Positron** (`https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png`). The very light neutral background makes the pink route line stand out clearly. Free, no API key required.

### Departure marker
Replace the plain circle marker with a **custom `L.divIcon`** shaped like the KidicalMass location pin (same SVG shape used in the hero section). A "Vertrekpunt" pill label floats to the right of the pin. Rendered in brand pink (`#E63A7B`).

### End marker
Detect loop vs point-to-point by comparing the first and last GPX track points:
- If the Haversine distance between start and end is **≤ 150 m** → treat as a loop. No end marker. Info strip shows "Lus" pill. (150 m chosen to absorb typical GPS drift at a start/finish plaza without misclassifying short point-to-point legs.)
- If distance **> 150 m** → show a smaller outlined pin (white fill, pink border) at the last track point with an "Aankomst" label pill. Info strip shows "Punt naar punt" pill.

### Info strip
A slim bar directly below the map canvas, inside the same rounded container. Contains:
- Distance (from `activity.distance` field, e.g. "7,4 km")
- Duration (from `activity.duration` field, e.g. "~45 min")
- Loop/point-to-point pill (derived client-side from GPX coordinates)
- "Bekijk op Komoot →" link — **only rendered if `activity.komoot_url` is set**

Distance and duration come from the existing model fields (already filled in by the admin). They are not recalculated from the GPX.

### Komoot URL field
Add a nullable `komoot_url` string column to the `activities` table. Admins paste the public Komoot tour URL when creating or editing an activity. The field is optional — the map works without it.

---

## Data Model Changes

| Change | Detail |
|---|---|
| Migration | Add `komoot_url` (nullable string) to `activities` table |
| Filament field | `TextInput::make('komoot_url')->url()->nullable()` in the activity resource form |
| No new media collections | GPX upload flow unchanged |

---

## Frontend Changes

All changes are in `resources/views/activities/show.blade.php` and the inline `<script>` block at the bottom.

### Blade template
- Pass `$activity->komoot_url` to the map container via a `data-komoot-url` attribute.
- Add the info strip HTML below the map `<div>`, populated from `$activity->distance`, `$activity->duration`, and `$activity->komoot_url`.

### JavaScript (inline script)
- Change tile layer URL to CartoDB Positron.
- Replace `L.circleMarker` with `L.divIcon` for the departure marker (inline SVG pin + label pill).
- After fitting bounds, run loop detection: compute Haversine distance between `coords[0]` and `coords[coords.length - 1]`. If > 150 m, add the end marker.
- Update the loop/p2p pill text in the info strip based on the detection result.

### CSS (`app.css`)
- Add styles for `.activity-map-info-strip`, `.activity-map-badge`, and the div icon marker classes.

---

## What Is Not In Scope

- Elevation profile (left to Komoot)
- Turn-by-turn directions
- GPX download button (riders use the Komoot link for this)
- Changing the GPX upload flow
- Any change to the map on the admin/Filament side

---

## Open Questions

None — all decisions resolved during design session.

---
title: Newsletter signup — email-first with progressive group disclosure
tags: [newsletter, livewire, location-picker, progressive-disclosure]
sources: [resources/views/livewire/newsletter-signup.blade.php, app/Livewire/NewsletterSignup.php, app/Livewire/LocationPicker.php]
phase: build
updated: 2026-06-18
---

# Newsletter signup — email-first with progressive group disclosure

## Problem

`/nl/nieuwsbrief` is an **email** newsletter signup, but the page currently leads
with the location picker. That inverts the funnel: the heaviest, optional input
(location — which today triggers a full page reload) sits above the essential one
(email). Every visitor pays the location cost even though the minimum viable
signup is a single email address.

We want to:

1. Lead with email capture as the primary path.
2. Hide group/location selection behind **progressive disclosure** — a quiet link
   that reveals the picker + nearby-chapter chips only when the visitor wants to
   refine.

## The core constraint

`LocationPicker::choose()` / `setFromCoords()` persist the chosen place with
`Cookie::queue(...)` and then call `$this->redirect($this->currentUrl(), navigate: true)`.
The redirect exists **because a cookie queued during a Livewire action is not
readable until the next HTTP request** — the page must reload for
`CurrentLocation::resolve()` to see the new location.

That full navigation is fine on Kalender and Lokale groepen (they read the cookie
server-side on reload to compute proximity bands). But on an email-first form it
would **wipe a typed-but-unsubmitted email**. The design must route around the
redirect without breaking the other pages that depend on it.

## Behaviour — three states

1. **Cold visitor, no location cookie.** Sees the email field + **Schrijf me in**
   as the only prominent action. Below it, a quiet text link:
   *"Ritten bij jou in de buurt kiezen"*. Email-only signup is fully valid.
2. **Disclosure link clicked.** Reveals the location picker inline. Once the
   visitor picks a place, the nearby-chapter chips appear pre-selected (opt-out
   model), with the email field above untouched.
3. **Location already known** (shared `kcm_location` cookie set elsewhere on the
   site). Chips show expanded on load with an *"andere locatie"* change link, plus
   a quiet *"Toch geen groepen kiezen"* link that collapses back to email-only.

## Architecture

### `LocationPicker` — add a reactive mode

- New public prop: `public bool $reactive = false;` (default preserves current
  behaviour for every other page — Kalender, Lokale groepen).
- When `$reactive === true`:
  - `choose()` / `setFromCoords()` **still queue the cookie** (so the choice
    persists site-wide and across future reloads), but **dispatch a
    `location-selected` event** carrying `['zip', 'lat', 'lng', 'name']` instead of
    redirecting.
  - Store the just-picked place in a public prop (e.g. `?array $selected`) so the
    picker can display "Gent · andere locatie" — the freshly-queued cookie is not
    readable this request.
  - `clear()` resets `$selected`, dispatches the same event with a null payload,
    and does not redirect.
- When `$reactive === false`: behaviour is exactly as today (queue cookie +
  `redirect(navigate: true)`).

### `NewsletterSignup` — disclosure-aware

- New state:
  - `public bool $showGroups = false;`
  - `public ?array $pickedLocation = null;` — coords received from the picker
    event, overriding the cookie for this request.
- `mount()`: if `CurrentLocation::resolve()` returns a location, set
  `$showGroups = true` and pre-select nearby groups (current behaviour). Otherwise
  leave collapsed with no selection.
- `#[On('location-selected')] setLocation(?array $payload)`: store coords in
  `$pickedLocation` (or null on clear), recompute `nearbyGroups()`, and pre-select
  all of them (opt-out default). Email is untouched.
- `revealGroups()`: `$showGroups = true`.
- `hideGroups()`: `$showGroups = false`, `$selectedGroups = []`,
  `$pickedLocation = null` — returns to a clean email-only state.
- `nearbyGroups()`: resolve origin from `$pickedLocation` if set, else
  `CurrentLocation::resolve()`. Logic otherwise unchanged (visible chapters within
  `regio_radius_km`, nearest first, capped at 5).
- **Conditional validation:** `selectedGroups` must contain at least one id **only
  when `$showGroups === true`**. A cold visitor who never reveals groups submits
  with email only; a visitor who opened the section must keep one group or use the
  collapse link. Validation message: *"Kies minstens één groep bij jou in de
  buurt."*

### View (`newsletter-signup.blade.php`)

Order within the `@else` (unauthenticated, not-yet-submitted) branch:

1. **Email field + Schrijf me in** — primary, first.
2. **Groups disclosure**, below the email:
   - When `!$showGroups`: a plain text link *"Ritten bij jou in de buurt kiezen"*
     (`wire:click="revealGroups"`).
   - When `$showGroups`: `<livewire:location-picker :reactive="true" :compact="true" />`
     followed by the chips fieldset (pre-selected, "klik een groep weg" hint) and a
     quiet *"Toch geen groepen kiezen"* link (`wire:click="hideGroups"`).

The benefits `<aside>` and the page hero are unchanged.

## Copy (NL, in-voice)

- Disclosure link: **Ritten bij jou in de buurt kiezen**
- Collapse link: **Toch geen groepen kiezen**
- Deselect-all error: **Kies minstens één groep bij jou in de buurt.**
- Existing legend ("Groepen bij jou in de buurt") and hint ("We sturen je
  standaard de ritten van deze groepen. Klik een groep weg die je niet wil
  volgen.") are retained.

## Styling

- Disclosure and collapse links are plain text links (secondary weight, e.g.
  `link-plain`) — **never** a solid button. The blue **Schrijf me in** stays the
  only loud element in the form, preserving the hierarchy fixed in the prior pass.
- The reveal animates with a `grid-template-rows` 0fr→1fr transition (no animating
  of `height`/`padding`/`margin`), wrapped in a `prefers-reduced-motion` guard.
- All CSS lives in `resources/css/pages/nieuwsbrief.css` under `@layer components`,
  tokens only — no raw hex/px (CssArchitectureTest).

## Testing (Pest + Livewire)

Feature tests on `NewsletterSignup` (and a focused `LocationPicker` test):

- Cold visitor (no cookie) can submit with email only — `submitted` true, no group
  validation fires.
- Location cookie present on mount → `showGroups` true and nearby chips
  pre-selected.
- `revealGroups()` sets `showGroups` true and renders the picker.
- Dispatching `location-selected` populates `pickedLocation`, recomputes groups,
  and pre-selects them.
- **Email is retained across a location pick** (assert `email` property survives
  `setLocation`, proving no navigation).
- Deselect-all while `showGroups` → validation error, not submitted.
- `hideGroups()` clears `selectedGroups`/`pickedLocation` and allows an email-only
  submit.
- `LocationPicker` reactive mode dispatches `location-selected` and does **not**
  redirect; default (non-reactive) mode still redirects — guarding Kalender and
  Lokale groepen.

## Out of scope

- The backend persistence + double opt-in mail (still `TODO(backend, Nico)` in
  `subscribe()`); this work keeps the optimistic, non-persisting confirmation.
- Any change to the location-picker behaviour on Kalender / Lokale groepen.

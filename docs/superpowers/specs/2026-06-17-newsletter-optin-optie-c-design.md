---
title: Newsletter opt-in — "Optie C — Voordelen + formulierkaart"
tags: [design, build, component, newsletter, public-site]
sources:
  - Pencil frame "CTA — Op de hoogte (3 varianten)" → VariantC (node ABk4T)
  - resources/views/livewire/ride-calendar.blade.php
  - resources/views/groups/show.blade.php
phase: build
updated: 2026-06-17
---

# Newsletter opt-in — Optie C

Implement the "Voordelen + formulierkaart" newsletter call-to-action from the Pencil
frame on the calendar (`/events`) and on every chapter page (`chapters/{group}`).

## Source design

Optie C is a light-blue rounded panel with two regions:

- **Voordelen (left):** bold heading "Blijf op de hoogte" + three benefit bullets, each
  with a blue check icon:
  1. De nieuwste ritten, elke maand als eerste
  2. Het laatste nieuws uit jouw lokale groep
  3. Eén rustige mail, makkelijk uit te schrijven
- **Formulierkaart (right):** a white sub-card holding the email label ("Je e-mailadres"),
  an email input (placeholder `jouw@email.be`), a blue submit button
  ("Ja, hou me op de hoogte"), and a fine-print reassurance line below.

Brand tokens: panel `--color-kidical-light-blue` (#B7E7F0), button + check icons
`--color-kidical-blue` (#1d67cd), text `--color-kidical-ink`, radii `--radius-card`,
shadow `--shadow-card`. No raw hex/px in the component (enforced by `CssArchitectureTest`).

## Scope decisions (confirmed with Frederik 2026-06-17)

- **Styled placeholder, no backend.** No `Subscriber` model, no migration, no real
  subscription stored. The form is client-side only (Alpine), matching the site's
  current faked-backend convention (e.g. the chapter empty-state form it replaces).
- **"Already subscribed" = logged in.** Rather than handle a duplicate submission, an
  authenticated visitor never sees the form; they see a "you're already on board, manage
  your preferences" panel instead.
- **Group specificity is deferred** to a future "kies je groepen" preferences page. No
  `group_id` is stored now. The chapter version only *reads* local (copy + a query hint).

## Component: `<x-newsletter-optin>`

One reusable Blade component at `resources/views/components/newsletter-optin.blade.php`.
Appearance + internal spacing are token-backed Tailwind utilities baked into the markup
(the `<x-feature-card>` pattern); there is no `app.css`/partial entry for it.

### Props

| Prop | Type | Default | Purpose |
|---|---|---|---|
| `:group` | `?App\Models\Group` | `null` | When set, copy reads local ("nieuws uit {gemeente}") and the deferred-prefs CTA carries `?gemeente={id}` as a hint. When null, generic copy. |

`gemeente` is derived from the group name the same way `groups/show.blade.php` already
does (strip a leading "Kidical Mass " prefix).

### Three states

1. **Opt-in** — guest, not yet submitted. The full Optie C card (voordelen +
   formulierkaart). The benefit bullet about "lokale groep" reads with the gemeente name
   when `:group` is set.
2. **Thanks** — guest just submitted. The form region is replaced by a warm confirmation
   line; the voordelen region may stay. Driven by Alpine: `x-data="{ sent: false }"`,
   form `@submit.prevent="sent = true"`, `x-show`/`x-cloak` to toggle. Mirrors the
   existing `chapter-notify` pattern exactly.
3. **Already on board** — `@auth`. No form is rendered at all. A compact panel: a short
   "Je bent erbij" line + a link to `route('settings')` to manage news preferences
   (placeholder target until the dedicated voorkeuren page exists).

State precedence: `@auth` (state 3) wins over the guest states; within guest, Alpine
toggles 1 ↔ 2.

### Responsive

The component adapts to **its own container width**, not the viewport (the events
sidebar stays narrow even on a wide desktop). Use a Tailwind v4 **container query**: the
component root is a `@container`, default `flex-col`, switching to `@lg:flex-row` (or a
suitable `@`-breakpoint) once its container is wide enough.

- **Narrow (events sidebar, ~300px):** voordelen + formulierkaart **stacked vertically**.
- **Wide (chapter page, full container):** **two-column**, voordelen left / formulierkaart
  right — the true Optie C look.

The white formulierkaart stays a distinct sub-card in both layouts. Callers pass no layout
flag; the component decides from its container.

### Copy (NL, tone-of-voice compliant, no em-dashes)

- Heading: **Blijf op de hoogte**
- Bullets: as listed above (bullet 2 localised when `:group` is set, e.g. "Het laatste
  nieuws uit Schaarbeek").
- Field label: **Je e-mailadres**, input placeholder `jouw@email.be`
- Submit: **Ja, hou me op de hoogte**
- Fine print: a short reassurance, e.g. "Eén mail per maand. Uitschrijven kan altijd."
- Thanks: warm, e.g. "Bedankt! Je staat op de lijst."
- Auth panel: e.g. "Je bent al mee. Je nieuwsvoorkeuren beheer je in je profiel." +
  "Beheer voorkeuren" link to `route('settings')`.

Final wording to be tuned against `docs/tone-of-voice.md` during implementation.

## Placement

### `/events` — sidebar (narrow, stacked)

In `resources/views/livewire/ride-calendar.blade.php`, replace the
`kal-sidebar__panel--newsletter` block (the yellow "Mis geen rit" card, lines ~91-95)
with `<x-newsletter-optin />`. Keep the sticky `<aside class="kal-sidebar">` wrapper.

The wide closing "Mis geen fietstocht / Binnenkort" band in `activities/index.blade.php`
stays untouched (informational, no CTA).

### chapter page — end of agenda, always (wide, two-column)

In `resources/views/groups/show.blade.php`:

- Keep an honest **text-only** "Nog geen fietstocht gepland" note for the no-ride case
  (`@unless ($hasRide)`), without its own form.
- Place `<x-newsletter-optin :group="$group" />` at the **end of the agenda section**
  (after the ride list / "alle activiteiten" foot), shown **always** (every chapter gets
  a newsletter moment). Rides remain the priority content above it.

## Cleanup (dead CSS once markup is swapped)

- `resources/css/pages/calendar.css`: remove the `.kal-sidebar__panel--newsletter`,
  `.kal-sidebar__heading`, `.kal-sidebar__body`, `.kal-sidebar__btn` rules (now unused).
  Keep the `.kal-sidebar` / sticky base rules.
- `resources/css/pages/chapters.css`: remove the `.chapter-notify*` rules and any
  `.chapter-next__card--empty` form styling no longer used. Keep `.chapter-next__empty-lead`
  /`-body` if the honest note still uses them.

Verify partials stay registered and no raw hex/px enters a `.blade.php` component:
`php artisan test --filter=CssArchitectureTest`.

## Testing

Feature/Livewire tests (Pest), run with `--compact`:

- `/events` renders `<x-newsletter-optin>` in the sidebar; the old "Mis geen rit" /
  "Schrijf je in" markup is gone.
- A chapter page renders the opt-in always (both with and without a scheduled ride), and
  the localised bullet shows the gemeente name.
- Guest sees the email form; an authenticated user sees the "manage preferences" panel and
  **no** email input.
- The honest "Nog geen fietstocht gepland" note still appears on a chapter with no ride.
- `CssArchitectureTest` passes.

Client-side toggle (form → thanks) is Alpine; assert the markup/attributes are present
rather than the toggled state.

## Out of scope

- Real subscription storage, double opt-in, email delivery, unsubscribe.
- The "kies je groepen" preferences page (group-specificity selection).
- Touching the `/events` closing band or the chapter yellow team/closing band.

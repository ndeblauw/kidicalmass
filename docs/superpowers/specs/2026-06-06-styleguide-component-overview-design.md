# Styleguide — component overview + extraction audit

- **Date:** 2026-06-06
- **Status:** Design approved, pending spec review
- **Owner:** Frederik (design), Claude (build)

## Goal

A single internal page, `/styleguide`, that serves two jobs:

1. **Living reference** — every public-site component rendered live, plus the design
   tokens (colours, type scale), with example markup, so we build consistent UI by
   reusing what exists instead of re-inventing it.
2. **Extraction audit tool** — a "nog te extraheren" section listing recurring UI
   found in page templates that should become components, to actively drive
   componentisation.

Inspired by Hartverwarmers' `admin/design-system.blade.php` (+ `DesignSystemController`):
single view, sticky TOC, live demos, "Toon markup" `<details>`, sample models fabricated
in the controller.

## Approach (C — hybrid)

- **Backbone** is a single hand-written Blade view (like HV). Component demos are
  inherently bespoke (each needs example markup + sample data), so there is no
  auto-generated registry — that plumbing buys little here.
- **Only the candidates list** is data-driven: a small PHP array `[name, where, props]`
  so items are easy to add and tick off as they get extracted.

Rejected: a markdown/array SSOT parsed to auto-build the whole page (the `/build`
dashboard pattern) — demos can't be honestly auto-generated, so the sync payoff is small.

## Placement & routing

- Route `/styleguide`, name `styleguide`, registered in `routes/web.php` **inside the
  existing `if (! app()->isProduction())` block** alongside `build.dashboard`.
- Unlinked: no nav entry, not in sitemap.
- Rendered through `layouts/site` so it inherits the real public tokens and CSS — the
  showcase looks exactly like the live site.

## Controller

`App\Http\Controllers\StyleguideController`, single `__invoke(): View`.

- Fabricates **in-memory** sample models (no DB writes) for data-backed components:
  `new Activity([...])` (for `event-card`, `kal-*`), `new Article([...])` (for
  `article-card`), plus any sample data `group-statistics` / `partners` need. Set `->id`
  and `setRelation(...)` as required, exactly like HV's `DesignSystemController`.
- Passes the candidates array to the view.

## Page structure (`resources/views/styleguide.blade.php`)

Sticky TOC sidebar (desktop) + content sections:

1. **Tokens**
   - Colour swatches sourced from the `@theme` block in `app.css`. Small
     `x-styleguide.swatch` sub-component (name + visual + token var), like HV's swatch.
   - Type scale from `@layer base` (h1–h4, body, meta), rendered live.
2. **Componenten** — grouped, each with live render + props list + "Toon markup"
   `<details>`:
   - *Knoppen & CTA's:* `cta-button`, `closing-cta`, `support-callout`
   - *Kaarten:* `feature-card`, `event-card`, `article-card`, `stat-card`
   - *Pagina-onderdelen:* `page-hero`, `partners`, `group-statistics`, `about-reveal`
   - *Kalender:* `kal-day-band`, `kal-month-band`
   - *Formulieren:* `contact-form`
   - *Primitieven:* `bike-icon`, `placeholder-pattern`
3. **Nog te extraheren** — driven by the controller's candidates array. Each row:
   suggested component name, where it currently appears, proposed props. This is where
   the audit output lands.

### Out of scope (not demoed)

Auth / settings / Filament scaffolding: `app-logo*`, `auth-*`, `auth-session-status`,
`action-message`, `desktop-user-menu`, `settings/*`, `wire/*`, `stub`. Listed once as
"buiten scope — geen onderdeel van de publieke designtaal", not rendered.

## The extraction audit (thorough sweep, part of this build)

Systematically read every public page template hunting repeated markup not yet behind a
component:

`home`, `about/*`, `activities/*`, `groups/*`, `articles/*`, `steun-ons`,
`getting-started`, `membership`, `volunteer`, `contact`, `find-a-bike`, `privacy`.

- Run as a parallel fan-out across page groups for speed + completeness, then dedupe.
- Each finding → a candidate `[name, occurrences (file:area), proposed props]`.
- Result populates the section-3 array so the candidates section ships filled in.

## Copy & styling rules

- **Language: Dutch** for all UI copy (section titles, labels, "Toon markup",
  candidates). Follow `docs/tone-of-voice.md` where copy is user-facing in spirit
  (this is internal, so keep it plain and clear; no marketing voice needed).
- **No raw hex/px** in the new view or sub-component — use tokens (enforced by
  `CssArchitectureTest`).
- **No new entries in `app.css`.** Any styleguide-only CSS goes in
  `resources/css/pages/styleguide.css` (registered per the partials architecture);
  default to reusing existing component/token styling so ideally little or none is needed.
- Headings use raw `<h1>`–`<h6>`, never `flux:heading` (public-site rule).

## Testing

- New Pest feature test `tests/Feature/StyleguideTest.php`:
  - returns 200 in non-production;
  - route is **not** registered in production (assert 404 / route missing);
  - a few key section anchors render (`#tokens`, `#componenten`, `#nog-te-extraheren`).
- `CssArchitectureTest` must still pass (partials registered; no raw hex/px in
  components).

## Explicit non-goals

- No registry/auto-generation infrastructure.
- No changes to the existing components themselves — extraction is follow-up work that
  the candidates list feeds.
- No public exposure — non-prod only, unlinked.

## Follow-up (not this build)

- Work through the candidates list, extracting each into a component and moving it from
  section 3 to section 2.

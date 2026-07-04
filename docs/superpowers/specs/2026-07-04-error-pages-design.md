# Error Pages — Design Spec

**Date:** 2026-07-04
**Status:** approved direction, awaiting implementation
**Owner:** Frederik (design), built by Claude

## Goal

Replace Laravel's default framework error pages (English, unstyled, off-brand) with
on-brand, warm, *useful* error pages. The 404 matters most: the site replaces a live
Wix site, so dead legacy URLs will be common right after launch. A good 404 turns a
dead link into a route back into the site instead of a bounce.

## Scope

Five error views in this pass:

| Code | Layout | Illustration | Job |
|------|--------|--------------|-----|
| 404 | `x-layouts::site` | `heart-30-sign.svg` | Redirect lost visitors to useful pages |
| 403 | `x-layouts::site` | `heart-sign-holder.svg` | Nudge begeleiders to log in |
| 419 | `x-layouts::site` | `relaxed-rider.svg` | Session expired, retry the form |
| 500 | standalone | `volunteer-with-wrench.svg` (inlined) | Reassure: our fault, we're on it |
| 503 | standalone | `volunteer-with-wrench.svg` (inlined) | Maintenance mode |

Out of scope: smart/dynamic 404 content (next ride, nearest group). Deliberately kept
off the error path; can be layered on later.

## Architecture

### Site-layout trio (404 / 403 / 419)

- `resources/views/errors/{404,403,419}.blade.php`, each on `x-layouts::site` so nav
  and footer remain and the visitor is never stranded.
- Shared component `resources/views/components/error-page.blade.php` (`x-error-page`):
  - Props: `code` (string), `title` (string), `illustration` (path).
  - Slot: body copy + action buttons/cards.
  - Emits `data-error-page="{code}"` as the stable test seam.
  - Appearance (colours, radius, type usage) lives inside the component as
    token-backed utilities per the styling architecture; each error view holds only
    composition utilities (grid/flex/gap/max-w).
- **404 content:** the sign illustration stands beside the headline (register:
  "Oeps, je bent verkeerd gereden" / "Deze pagina bestaat niet (meer)"), a short
  reassuring line, then `x-nav-card`s to **Kalender, Lokale groepen, Voor het eerst
  mee** plus a plain home link. No animation, no custom sign artwork: the existing
  illustration is the scene.
- **403 content:** warm, assumes good faith: this is a page for begeleiders. Primary
  action: log in (`route('login')`); secondary: home.
- **419 content:** "je was er even tussenuit" register. Primary action: back and
  retry (`history.back()` button); secondary: home.

### Standalone pair (500 / 503)

- `resources/views/errors/{500,503}.blade.php`: full HTML documents that depend on
  **nothing**: no site layout, no `@vite`, no DB, no lang files. Inline `<style>`
  (token values copied in as literals; this is the one sanctioned place raw values
  appear, because the token pipeline itself may be down), logo, inlined
  `volunteer-with-wrench.svg`, one line of copy.
- 500: "Er ging iets mis bij ons, niet bij jou. We sleutelen eraan."-register.
- 503: "We zijn even aan het sleutelen. Zo terug!"-register.
- The two files may share markup via a plain `@include('errors.partials.minimal')`
  if duplication feels wrong during build; two small standalone files are also
  acceptable. Builder's choice, no other view may depend on the partial.

### CSS

- Expected: **no new CSS partial.** Composition is utilities in the views; appearance
  is inside `x-error-page`. Only if a rule genuinely can't live in either place does
  `resources/css/pages/errors.css` get created, registered in the `app.css` import
  block (CssArchitectureTest enforces registration).

## Copy

- Hardcoded NL in the Blade views, like the rest of the public site.
- Follows `docs/tone-of-voice.md` (joyful, warm, local, committed); no em-dashes;
  every line passes the one-line test. Error pages skew warm and light: the visitor
  hit a wall, the page should feel like a friendly wave, not a shrug.
- `<html lang>` on the standalone pages: hardcode `nl` (the app is NL-only and the
  locale layer may not have run on a 500).

## Testing (per `docs/testing-conventions.md`)

- **404:** feature test hits a bogus URL, asserts status 404, `data-error-page="404"`,
  and an `href` to `route('activities.index')` (kalender).
- **403:** hit an existing guarded route as guest (e.g. roze-hesjes member page),
  assert status + `data-error-page="403"` + login `href`.
- **419 / 500 / 503:** render the views directly (`view('errors.419')` etc.), assert
  the seam and the primary action. No forced crashes or real maintenance mode.
- No assertions on utility classes, hex values, or full copy sentences.

## Non-goals / notes

- No changes to exception handling in `bootstrap/app.php`; Laravel picks up
  `resources/views/errors/*` by convention.
- 400 (one `abort_unless` in the admin ContactForm controller) is admin-facing and
  keeps the framework default.
- Pipeline/page-registry: error pages are not a `P-nn` page; log the work in
  `docs/wiki/log.md` at commit time instead.

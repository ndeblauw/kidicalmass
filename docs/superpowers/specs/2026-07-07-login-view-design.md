# Branded login view (P-07) — design

**Date:** 2026-07-07
**Brief:** `docs/wiki/design/30-skeleton/login-view.md`
**Decided with Frederik via visual companion (mockups in `.superpowers/brainstorm/`, gitignored).**

## Direction: "Geel veld"

One warm light-yellow field (`bg-kidical-light-yellow`) for the whole page. No
floating card, no split zones: a photo collage on the left and the form
breathing directly on the yellow to the right. Desktop is a centered two-column
composition; mobile stacks with a compact collage strip on top (M1).

Chosen over two rejected directions: a hartverwarmers-style two-zone split
(photo panel + hero/form zones) and a dark ink brand panel.

## Layout

Rebuild `resources/views/layouts/auth.blade.php` as the branded shell (the
starter `auth/simple|card|split.blade.php` variants become unused; leave them in
place, they are vendor-published stubs). All six auth views render inside it.

- **Desktop (`lg:`)**: two columns centered vertically and horizontally,
  generous gap. Left: `<x-photo-collage>` on a square stage (~430px). Right: a
  ~340-400px form column.
- **Mobile**: single column; compact collage strip (three photos in a row-ish
  scatter, ~2.4:1 stage) above the form column.
- The shell exposes slots/props: `title` (h1), `intro` (one sentence), and the
  default slot for the form. Views without a collage need nothing extra — the
  collage is part of the shell, identical on every auth view.

## Components & reuse

- **Collage**: reuse `<x-photo-collage>` (PAT-20) as-is, including the settle
  entrance. Photos (Set 1 "Mensen"):
  1. `img/photography/volunteers-pink-vest-group-cobbles.webp` (x 32% y 27% w 56% r -3deg)
  2. `img/photography/ride-trio-pink-vest-lei-portrait.webp` (x 77% y 40% w 50% r 2.5deg, pos 60% 30%)
  3. `img/photography/volunteer-pink-vest.webp` (x 45% y 75% w 54% r -2deg, pos center 20%)
  Placement tightened for mobile via the page CSS partial (edge-contact only,
  no deep overlap — Frederik's explicit correction).
- **Logo row**: colour logo `img/logos/logo.png` (NOT the white
  `footer-logo.avif` — unreadable on yellow), ~59px tall on desktop (a third
  bigger than the first mockup), with a **role pill** beside it following the
  header postcode-pill pattern (`site-nav__postcode`): text "VRIJWILLIGERS",
  **white capsule, pink text** (`--color-kidical-red-text` on white, soft
  shadow) — chosen over yellow-on-darkblue, white-on-pink and darkblue-on-pink.
  Logo links to `route('home')`.
- **Heading**: raw `<h1>` "Welkom terug" (Caprasimo via `@layer base`, never
  `flux:heading`), no kicker line (the pill carries the "voor vrijwilligers"
  message). One intro sentence below in muted body colour.
- **Form**: keep Flux form components (`flux:input`, `flux:checkbox`) — allowed
  by the brief. Submit button becomes the site's `.cta-button--yellow` pill with
  the red disc arrow (`<x-cta-button>` if a component exists, else the CSS
  classes on a `<button type="submit">`).
- **Dev quick-login** (non-prod only, existing routes `login/as/{role}`): stays
  at the bottom of the form column under a hairline, muted uppercase label
  "Snel inloggen (dev)" + 2x2 ghost pill grid (Roze hesje / Kapitein /
  Gebruiker / Admin). Keep existing `data-test` hooks.

## CSS

New page partial `resources/css/pages/auth.css`, registered in `app.css`'s
import block (never rules in `app.css` itself). It holds the page composition
(`.auth-page`, `.auth-page__collage`, `.auth-page__form`, `.role-pill` if not
inlineable) and the mobile collage placement overrides. Token-backed utilities
inline in the blade where they suffice; no raw hex/px in blade components
(CssArchitectureTest enforces this).

## Copy (NL, tone-of-voice, no em-dashes)

All user-facing strings via `lang/nl/auth.php` keys (follows the per-domain
lang convention; tests assert `__('key')`). Includes Fortify's
validation-adjacent strings (failed / password / throttle) in NL.

- **Login**: h1 "Welkom terug" (Frederik's pick, over "Welkom terug, roze
  hesje!"), intro "Fijn dat je er weer bent. Log in en ga verder waar je
  gebleven was." Labels: E-mailadres / Wachtwoord / Wachtwoord vergeten? /
  Ingelogd blijven / button "Log in".
- **Siblings** (same shell, own h1 + intro, NL): forgot-password ("Wachtwoord
  vergeten?"), reset-password ("Kies een nieuw wachtwoord"), confirm-password,
  two-factor-challenge, verify-email. Register stays disabled; the "Sign up"
  block only renders if the route exists, which it doesn't.

## Untouched

Fortify backend, roles, `LoginResponse` redirects, routes, `DemoLoginController`.
No new auth features (no register, no socials).

## Testing

Existing `tests/Feature/Auth/` must stay green; update only assertions that
pin default EN copy (assert `__('auth.…')` keys instead). New seams `data-*`
only. No coverage expansion beyond behaviour (thin-Auth backlog note).

## Pipeline afterwards

- Assets → 🟢 (photo collage decided), Wire → 🟢-candidate via `/pipeline P-07
  wire=good ui=wip` after live verification (login as each demo role, one
  screenshot pass).
- Registry row + Top gaps + Roll-up + `log.md` entry; runway "Branded login
  view" row. Frederik's own critique in `/build/review` flips Wire/UI 🟢.

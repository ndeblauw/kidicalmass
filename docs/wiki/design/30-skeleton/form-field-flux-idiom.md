---
title: Handoff — One form-field idiom, delivered by restyled Flux
tags: [design, build, handoff, forms, flux]
sources: [login-view.md, ../../log.md]
phase: design
updated: 2026-07-07
---

# Handoff — One form-field idiom, delivered by restyled Flux (cross-cutting)

**Start a fresh thread with this brief.** From Frederik's P-07 critique session (2026-07-07), verbatim:

> "How about we make the flux components also look like the components we have now? And then we use the flux components? I'd like consistency. Here, and on other forms."

## The idea

Today the site has ONE field look but TWO delivery mechanisms. Invert that:
theme the Flux form components to render the site's field idiom, then put every
public form on Flux. One definition, consistency for free, and Flux's
niceties (viewable password, validation wiring, a11y) come back.

## What exists

- **The idiom** (`.volunteer-signup__field/__label/__input/__error`, defined in
  `resources/css/pages/steun.css:7-45`): white field, 2px ink-tint border,
  radius 0.75rem, focus = border turns `--color-kidical-red`, uppercase
  letterspaced label, red error line. No ring, no glow.
- **Plain-idiom forms (8):** `livewire/contact-form-component.blade.php`,
  `livewire/partner-enquiry.blade.php`, `livewire/chapter-volunteer-signup.blade.php`,
  `livewire/start-group-enquiry.blade.php`, and `livewire/auth/{login,forgot-password,reset-password,confirm-password}.blade.php`.
- **Flux still in place:** `auth/two-factor-challenge.blade.php` (`flux:otp` +
  recovery `flux:input`), `flux:checkbox` on login, `auth/register.blade.php`
  (route disabled), and the whole backstage/settings area (Flux starter defaults).
- **History (why auth is plain today):** P-07 shipped on `flux:input`, but the
  accent focus ring (yellow, from `--color-accent` theming in `app.css`) read
  too harsh; commit `c9bc958` swapped auth onto the plain idiom as the quick
  fix. Trade-off noted then: the `viewable` password eye was lost — this
  project restores it.

## Target state

- Flux `input`, `textarea`, `select`, `checkbox`, `radio`, `field/label/error`
  render the site idiom out of the box on the public site.
- All 8 plain-idiom forms migrated (back) to Flux components; Livewire
  `wire:model` bindings and honeypot fields preserved exactly.
- `.volunteer-signup__*` retired from `pages/steun.css` (or reduced to what no
  Flux component covers), with `pages/contact.css`'s references reconciled.
- Backstage/settings: out of scope unless the theming lands there for free —
  don't chase it.

## Constraints

- **Research first:** activate the `fluxui-development` skill and Boost
  `search-docs` for Flux customization (publish components vs CSS overrides vs
  theme variables) before choosing the mechanism. Pick ONE mechanism and note
  why in the design doc.
- **CSS architecture rules apply:** whatever CSS this produces goes in a
  registered partial (`resources/css/components/form-field.css` is the natural
  home — the idiom is reusable-across-pages by definition), `@layer`-wrapped,
  tokens/`color-mix()` only, never `app.css`. `CssArchitectureTest` enforces.
- Keep every `data-test` hook and `__('key')` string exactly; the suite must
  stay green (`AuthViewsTest`, `PartnerEnquiryTest`, contact/start-group tests).
  Known-failure baseline is FilamentAdminTest only.
- `/styleguide` (`resources/views/styleguide.blade.php`) is the verification
  surface — add the themed field set there and screenshot it.
- Focus visibility is an a11y requirement: the red-border focus state must
  remain clearly visible for keyboard users (the global `:focus-visible`
  outline in `app.css` is the fallback — don't suppress it without an equally
  visible replacement).
- Shared checkout: work on main, stage by explicit path, never `git add -A`.

## Suggested flow

1. Quick brainstorm + research spike: how does Flux v2 want to be themed?
   Decide publish-vs-CSS, write the short design doc.
2. Theme the field set; prove it on `/styleguide` next to the current idiom.
3. Migrate form-by-form, auth first (restores the password eye), then contact,
   partner, chapter-volunteer, start-group. Test + screenshot per form batch.
4. Retire the old classes from `steun.css`/`contact.css`; run the full suite.
5. Log entry + runway note; no P-nn row (cross-cutting), but the touched pages'
   rows may earn a "Top gaps" cleanup.

## Done when

Every public form renders identical field styling from one Flux-level
definition; the old `.volunteer-signup__*` CSS is gone or minimal; suite green;
Frederik eyeballs `/styleguide`, one migrated public form, and `/login`.

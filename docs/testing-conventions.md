---
title: Testing conventions
tags: [build, testing]
updated: 2026-06-29
---

# Testing conventions

A short rubric for what our tests should assert. Born out of the 2026-06-29 test
audit (see `docs/wiki/log.md`). The one-line version:

> **Assert what a user or browser can observe. Don't assert how it's styled.**

## Assert these (durable)

- **Rendered text** the user reads: copy, headings, labels, error messages.
  Prefer `__('key')` translation keys over literal strings where a key exists, so
  a copy tweak doesn't break the test (`SteunOnsPageTest` is the model).
- **Attributes** the browser acts on: `href`, `aria-*`, `srcset`, `loading`,
  `decoding`, `data-*` hooks, form `name`/`type`.
- **Behaviour & state**: conditional rendering (shown/hidden), slot projection,
  redirects, status codes (`assertOk()`, `assertForbidden()`), business logic
  (de-duplication, ordering, filtering, accessor output).
- **Stable semantic / BEM state hooks** when there's no observable alternative:
  `ride-row--featured`, `roze-subnav__tab--active`. These survive a re-theme; a
  Tailwind reshuffle does not touch them.

## Don't assert these (brittle)

- **Tailwind utility classes**: `p-5`, `size-6`, `text-sm`, `bg-kidical-red`,
  `rounded-card`, `-rotate-3`. A purely visual refactor breaks the test while the
  page still works — and a genuinely broken page that keeps the class passes it.
- **Raw hex / px values**: `#B7E7F0`, `border-radius:9999px`. (Also violates the
  no-raw-hex rule in `CssArchitectureTest`.)
- **Implementation source**: Alpine expressions (`x-ref="prevBtn"`,
  `open: false`, `trapTab($event)`), raw SVG path coordinates (`M2 7h10`). These
  test the JS/markup string, not runtime behaviour.
- **Exact long copy sentences** when a translation key or a short distinctive
  phrase would do.

## When there's no observable hook, add one

If the only way to assert a variant is its utility class, **add a stable seam to
the component** instead of asserting the utility — a `data-*` attribute that
encodes the *intent*, not the styling. It doubles as a debugging/JS hook and is
ignored by email clients.

```blade
{{-- icon-chip.blade.php --}}
<span data-icon-chip data-color="{{ $color }}" data-size="{{ $size }}" @if($shadow) data-shadow @endif
      {{ $attributes->merge(['class' => "... {$chipBg} {$chipSize}"]) }}>
```

```php
// assert the seam (retheme-proof), not bg-kidical-blue / size-[2.75rem]
expect($html)->toContain('data-color="blue"')->toContain('data-size="md"');
```

## Good vs bad examples in this repo

- **Follow**: `RideRowComponentTest`, `PhotoComponentTest`, `RozeHubComponentTest`,
  `PartnerStripComponentTest`, `SteunOnsPageTest`. They assert text, attributes,
  slots and domain logic.
- **Was brittle, now rehooked** (2026-06-29): `FeatureCardComponentTest`,
  `IconChipTest`, `EmailNotificationComponentTest`.

## Smoke / route coverage

Public routes live in one place: the `public routes` dataset in `tests/Pest.php`.
Reuse it (`->with('public routes')`) rather than re-listing routes per file.

## Mutation testing

Mutation testing is the objective version of "does this test actually catch a
regression?" Pest mutates the source (flips `&&`→`||`, `>=`→`>`, return values,
etc.) and checks whether a test fails. An **untested** mutation is a line whose
change no test notices — a missing assertion, not redundant code. The fix is to
strengthen the test, *not* to delete it.

```bash
composer test:mutate        # scoped to App\Support business logic, covered lines only
```

Other scopes / a stricter gate:

```bash
XDEBUG_MODE=coverage php vendor/bin/pest --mutate --covered-only --class="App\Support\Build"
XDEBUG_MODE=coverage php vendor/bin/pest --mutate --covered-only --class="App\Support" --min=70
```

- **Driver:** mutation testing needs Xdebug 3+ or PCOV. Locally, Herd already
  bundles Xdebug; it's enabled at `~/Library/Application Support/Herd/config/php/84/xdebug.ini`
  with `xdebug.mode=off` (≈no overhead — normal `php artisan test` stays fast),
  and the `XDEBUG_MODE=coverage` prefix turns coverage on for just that run. That
  ini is per-machine (not in the repo): another machine/CI needs its own driver
  (PCOV is a fine CI choice; `XDEBUG_MODE` is harmless when it's absent).
- **Speed:** runs are slow (each mutant re-runs its covering tests). Narrow with
  `--class=…`, add `covers(SomeClass::class)` / `mutates(…)` at the top of a test
  to target it, or pass `--parallel`.
- **Baseline (2026-06-29):** `App\Support\Build` ≈ 58% (185 tested / 135 untested).
  Use the untested list as a to-do for missing assertions; don't chase 100%.

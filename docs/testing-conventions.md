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

# Translation-key issues (nl vs fr)

Inventory of `lang/nl/*.php` vs `lang/fr/*.php`: keys that exist in only one
locale, and keys that are empty in one locale but filled in the other (these
drive the translation-driven visibility/review gates). Regenerate after content
changes.

## Keys that exist in only one locale

### `support.callout.*` — only in `nl`

- `support.callout.home.title`, `support.callout.home.body`
- `support.callout.event.title`, `support.callout.event.body`

**Impact:** `resources/views/components/support-callout.blade.php:16-17` reads
`support.callout.{variant}.*`. On `fr` the keys are missing, so `__()` falls back
to `nl` → **Dutch callout copy renders on French pages** (the `event` variant on
past-activity pages; `home` in the styleguide).

**Fix:** add the French copy (and English later), or set explicit values.

> No keys exist only in `fr` right now.

## Intentionally one-sided keys (empty in one locale, filled in the other)

These are the translation-driven gates: a new locale must define them (empty to
hide / not mark).

### `about.php`

- empty in `nl` / filled in `fr`: `organisation.local.note`,
  `organisation.duo_review`, `organisation.parcours_review`
- filled in `nl` / empty in `fr`: `organisation.duo.link`

### `partners.php`

- empty in `nl` / filled in `fr`: `page.formules.vat_note_review`,
  `page.collab.offer_review`

### `support.php`

- empty in `nl` / filled in `fr`: `funds_review`, `donation.heading`,
  `donation.body`, `donation.iban`

## List-length differences

- None currently (all shared list keys have the same number of items in `nl` and
  `fr`).

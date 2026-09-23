# Translation-key issues (nl vs fr)

Inventory of `lang/nl/*.php` vs `lang/fr/*.php`: keys that exist in only one
locale, and keys that are empty in one locale but filled in the other (these
drive the translation-driven visibility/review gates). Regenerate after content
changes.

## Keys that exist in only one locale

- None currently.

> Resolved: `support.callout.*` used to exist only in `nl`, which made
> `resources/views/components/support-callout.blade.php` render Dutch copy on
> French pages. French copy was added to `lang/fr/support.php`; the callout keys
> are now defined in both locales (and are guarded by a regression test in
> `tests/Feature/Content/LocaleDrivenContentTest.php`).

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

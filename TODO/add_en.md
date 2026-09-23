# Adding English (`en`) — rollout plan

Status: **planned, not started.** The site currently supports `nl` (default) + `fr`.

Prerequisite work already landed (this branch):
- The locale route-prefix scheme is centralised in `app/Support/helpers.php`
  (`locale_route_name()`, `route_base_name()`, `route_is()`), driven by
  `SetLocale::SUPPORTED`.
- Locale-specific values moved out of code into lang keys (RideDate separator,
  site name, privacy authority URL/date, Growfunding URL).
- The language-switch order lives in `SetLocale::DISPLAY`; the live/disabled state
  is derived from `SetLocale::SUPPORTED` via `alternate_locale_url()`.
- Locale smoke/home tests are parameterised over `SetLocale::SUPPORTED`, so they
  will cover `en` automatically once it is added.

## 5. `lang/en/*`

- Create `lang/en/` mirroring `lang/nl/` (`nl` is the fallback locale).
- For keys that are intentionally one-sided, set `''` so the translation-driven
  gates keep working (empty = hidden/off):
  - review markers: `about.organisation.duo_review`,
    `about.organisation.parcours_review`,
    `partners.page.formules.vat_note_review`,
    `partners.page.collab.offer_review`, `support.funds_review`
  - FR-only content: `about.organisation.local.note`, `support.donation.*`
  - decide per EN: `about.organisation.duo.link` (filled in `nl`, empty in `fr`)
- See `TODO/translation_key_issues.md` for the current one-sided keys.

## 6. `SetLocale::SUPPORTED`

- Add `'en'` to `SetLocale::SUPPORTED` (`app/Http/Middleware/SetLocale.php`).
- This automatically enables:
  - `localized_route('…', ['locale' => 'en'])`
  - `alternate_locale_url('en')` → the language switch becomes a live link
  - `hreflang` alternates in `partials/site-head.blade.php`
  - the `/` browser-language redirect (`detectFromRequest`) and `Vary`
  - the partner strip and header nav (both locale-agnostic since this branch)
- `SetLocale::DISPLAY` already lists `en`; no change needed.

## 7. Routes

`routes/web.php` declares each page twice today — `nl` under `{locale}` with
`where('locale', 'nl')`, and `fr` as explicit `fr/…` routes with `fr.` names.
Adding `en` means a third copy per page.

- Recommended: refactor to **data-driven registration** — a per-page map of
  locale → slug, looped once. Naming: default locale unprefixed (`home`), others
  prefixed (`fr.home`, `en.home`); keep the `{locale}` URL default.
- Files: `routes/web.php` (all public routes) and `routes/redirects.php`
  (legacy redirects are locale-mapped).
- Add the `en.*` names + `en/…` paths (or generate them in the loop).
- Watch the special cases: detail routes with `{group}` / `{activity}` /
  `{article}` model binding, `groups.roze-hesjes.*`, and `LocalGroupScope`.

## 8. Verify

- `php artisan route:list` shows the `en` routes.
- `/` redirect honours `Accept-Language: en`; `Vary: Accept-Language` unchanged.
- Language switch: EN is live; `hreflang` includes `en-BE`.
- Partner strip + nav highlighting render on `/en`.
- `meta.site_name`, `privacy.updates.*` / authority URL, `common.time_separator`
  (pick the EN separator, e.g. `h` or `:`), `support.growfunding_url`.
- Run the full suite. The parameterised locale tests now cover `en` and will fail
  until routes + lang exist — that is the signal the work is finished.

## Risks

- Route duplication is the main risk; the data-driven refactor deserves its own PR.
- Growfunding may not have an `/en/` project URL — confirm the EN support URL
  (`support.growfunding_url`).
- Some copy is FR-only or legally locale-specific; only fall back to `nl` where
  that is acceptable.

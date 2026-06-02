---
title: Public Site Structure — NL-only Implementation (design)
date: 2026-06-02
phase: build
status: approved
related:
  - docs/wiki/design/20-structure.md
  - docs/wiki/design/30-skeleton/00-page-registry.md
  - docs/wiki/design/25-content-migration.md
---

# Public Site Structure — NL-only Implementation

## Goal

Stand up the **full public information architecture** from [`20-structure.md`](../../wiki/design/20-structure.md) as real, navigable routes in the Laravel app — reusing what's already built, adding the missing pages as honest skeleton **stubs**. After this pass, the entire public structure is walkable in the browser in Dutch.

## Locked decisions (from brainstorming, 2026-06-02)

1. **URLs:** locale-prefixed now — `/{locale}/…` constrained to `nl` (option C). Widening to `fr` later is additive. Slugs stay **neutral** under the prefix (`/nl/help-out`, not `/nl/vrijwilliger`), matching the [redirect map](../../wiki/design/26-redirect-map.md).
2. **i18n:** **hybrid** — shared chrome strings (nav, footer, buttons, shared labels) in `lang/nl/`; page-body prose hardcoded NL. DB content is already bilingual in the models (locale picks the NL column).
3. **Scope:** **public tier only.** The logged-in tier (My activities, backstage) is deferred — it depends on the unbuilt Attendance model and the parked Alexandre brief (`D-1`).

## Locale architecture

- One route group: `Route::prefix('{locale}')->whereIn('locale', ['nl'])->group(...)` wrapping all public routes.
- A `SetLocale` middleware reads the `{locale}` param, validates it against the allowed set, and calls `app()->setLocale($locale)`. Registered in `bootstrap/app.php` and applied to the group.
- Root `/` → `redirect('/nl')` (named `home` stays the canonical entry; the prefixed home is `/nl`).
- **Unprefixed, untouched this pass:** Filament `/admin`, and the existing auth/settings routes (`login`, `register`, `dashboard`, `settings.*`) — they belong to the deferred logged-in tier.
- No locale package — plain Laravel (CLAUDE.md: no new deps without approval).

## Route map (reuse vs. stub)

All routes below live under the `/{locale}` group. "Reuse" = existing controller/view, re-routed + reference updates + NL body copy. "Stub" = new skeleton page.

| New route (`/nl/…`) | Page | Source | Status |
|---|---|---|---|
| `/` | Home | `HomeController` + `home.blade.php` | reuse |
| `/events` | Events overview | `ActivityController@index` | reuse (was `/activities`) |
| `/events/{activity}` | Event detail | `ActivityController@show` (branded) | reuse |
| `/events/{activity}/ical` | iCal export | `ActivityController@ical` | reuse |
| `/chapters` | Chapters overview | `GroupController@index` | reuse (was `/groups`) |
| `/chapters/{group}` | Chapter page | `GroupController@show` | reuse (postal-code binding = follow-up) |
| `/help-out` | Help out | `volunteer.blade.php` + `volunteer-signup` | reuse (was `/vrijwilliger`) |
| `/about/news` | News | `ArticleController@index` | reuse (was `/articles`) |
| `/about/news/{article}` | Article | `ArticleController@show` | reuse |
| `/about/partners` | Partners | `partners` component | thin page wrapping the component |
| `/contact` | Contact (national) | `ContactFormComponent` | semi-real (component exists) |
| `/getting-started` | Getting Started | — | **stub** |
| `/about` | About overview | — | **stub** (sub-page nav cards) |
| `/about/mission` | Mission | — | **stub** |
| `/about/vision` | Vision | — | **stub** |
| `/about/organisation` | Organisation | — | **stub** |
| `/about/press` | Press | — | **stub** |
| `/membership` | Membership / spacefunding | — | **stub** |
| `/privacy` | Privacy | — | **stub** |
| `/cookies` | Cookies | — | **stub** |

Net new stub pages: **~10**. Everything else is re-routing + updating every `route()` reference.

### Route naming

Route names move to a dotted, IA-aligned scheme so `route()` calls and `routeIs()` nav highlighting are stable: `events.index`/`events.show`/`events.ical`, `chapters.index`/`chapters.show`, `help-out`, `news.index`/`news.show`, `about` + `about.mission`/`about.vision`/`about.organisation`/`about.press`/`about.partners`, `getting-started`, `membership`, `contact`, `privacy`, `cookies`. The `{locale}` param is supplied by a URL default (so existing `route('events.index')` calls don't each need the locale passed).

## Stub format

Each stub is a real Blade page using `<x-layouts::site>` that renders its **skeleton sections from the page-registry brief** as labelled placeholder blocks:

```
<x-layouts::site title="...">
  <h1>{{ NL page title }}</h1>
  {{-- section from the skeleton brief --}}
  <section>
    <h2>{{ section heading }}</h2>
    <p class="placeholder">[ placeholder: wat hier komt ]</p>
  </section>
  ...
</x-layouts::site>
```

- Sections come from the matching `30-skeleton/*.md` brief where one exists (Membership, About pages), otherwise from the structure tree.
- A small `.placeholder` style makes unfinished blocks visually obvious. **No lorem ipsum masquerading as content.**

## Chrome & navigation (the hybrid i18n)

- Rebuild `layouts/site/header.blade.php` nav to the 5-item structure: **Events · Chapters · Getting Started · Help out · About**. About is a dropdown (Mission / Vision / Organisation / News / Press / Partners).
- Rebuild `layouts/site/footer.blade.php` to the footer cluster: **Contact · Membership CTA · Privacy · Cookies · login link**. (Login link points at the existing unprefixed `login` route.)
- All nav/footer/button/shared-label strings come from **`lang/nl/`** (e.g. `lang/nl/nav.php`, `lang/nl/common.php`) via `__()`. Page bodies stay hardcoded NL.
- Update `routeIs()` highlight checks to the new route names.

## Out of scope (this pass)

- Logged-in tier: My activities, backstage. (Own pass once Attendance exists.)
- New models: Attendance, EmailSubscription, Membership.
- FR/EN content and the `fr` locale (architecture is ready; just not populated/enabled).
- The Wix→new **301 redirect map** — that's launch work ([`26-redirect-map.md`](../../wiki/design/26-redirect-map.md)).
- Slug refinements: `/events/{slug}`, postal-code binding for `/chapters/{postal}`. Keep current model binding; noted as follow-ups.
- Surface/visual polish of stubs — structure only.

## Testing

- **Pest smoke test** hitting every public `/nl/…` route, asserting `200` + renders without error. One sweep covers reuses and stubs.
- A test asserting `/` redirects to `/nl`.
- A test asserting an invalid locale (e.g. `/fr/events` while `fr` is disabled) returns `404`.
- Run with `php artisan test --compact --filter=...` after the change.

## Follow-ups (logged, not now)

- Logged-in tier implementation pass (after Attendance model + Alexandre brief).
- Slug + postal-code binding refinement.
- Wix→new 301 redirects at launch.
- Enable `fr`: widen the locale constraint, add `lang/fr/`, populate FR DB columns/content.
- Replace stub bodies with real copy (ToV-guided), per the content-migration plan.

## File-level change summary

- `routes/web.php` — wrap public routes in the `/{locale}` group; rename to the new paths + route names; root redirect.
- `app/Http/Middleware/SetLocale.php` — new; `bootstrap/app.php` — register it + URL locale default.
- `resources/views/layouts/site/header.blade.php` + `footer.blade.php` — nav/footer rebuild via `__()`.
- `lang/nl/*.php` — new chrome strings.
- `resources/views/getting-started.blade.php`, `about/*.blade.php`, `membership.blade.php`, `press`, `privacy.blade.php`, `cookies.blade.php`, `contact.blade.php`, `about/partners.blade.php` — new stub/thin pages (+ controllers or `Route::view` as appropriate).
- Move `articles` views/route under `about/news`; keep controllers.
- `tests/Feature/PublicRoutesSmokeTest.php` — new.
- Update all `route('groups.*'|'activities.*'|'articles.*'|'volunteer')` references across views/components.

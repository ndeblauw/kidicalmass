# Public site head: SEO & social sharing baseline

**Date:** 2026-07-03
**Status:** Approved by Frederik
**Closes (partly):** design concern D-10 (page metadata, SEO & social share previews)
**Scope:** items 1-9 of the 2026-07-03 SEO audit: title pattern, favicons, default + per-page meta descriptions, canonical, OG/Twitter baseline, per-page OG images, theme-color/manifest, public/ prototype cleanup. NOT in scope: sitemap, JSON-LD, slugs, Wix redirect map (D-7), RSS, hreflang, analytics, custom 404.

## Context

The public layout head (`resources/views/layouts/site.blade.php`) is a stub: bare unsuffixed titles ("Kalender"), one hardcoded site-wide description, no OG/Twitter/canonical/favicon links on public pages. Documented as concern D-10. Favicon assets exist in `public/` but are only linked from the admin head partial.

Verified model facts that shape the design:

- `Activity` and `Article` have `content_nl` (HTML) and a Medialibrary `main` collection with `thumb`/`card` conversions.
- `Group` has NO intro/content text field (only `name`, `shortname`, `zip`).
- All public pages already pass `title="..."` as a prop to `<x-layouts::site>`.

## Decisions

1. **Meta API: Blade props cascade.** Pages pass `title` / `description` / `og-image` / `og-type` attributes to `<x-layouts::site>`; no SEO package, no PHP meta objects.
2. **Title pattern:** `{Page} · Kidical Mass België` for every page except home. Home is standalone: `Kidical Mass België · Samen fietsen met kinderen` (home stops passing a `title` prop; the null fallback IS the home title).
3. **OG images: two-tier.** One branded 1200×630 default (`public/img/og-default.jpg`, designed by Frederik; a clearly-marked placeholder may ship first so the mechanism is testable). Activity pages override with an auto-cropped hero conversion. Articles use the same override.
4. **Static page descriptions live in `lang/nl/meta.php`**, one key per page; blades pass `:description="__('meta.calendar')"` etc. All copy follows `docs/tone-of-voice.md`; no em-dashes.
5. **Detail page descriptions are auto-derived**, no new DB columns:
   - Activity/Article: strip tags from `content_nl`, squish, limit ~155 chars, via a `metaDescription()` method.
   - Group: templated lang key with `:name` placeholder (no content field exists).

## Architecture

### `resources/views/partials/site-head.blade.php` (new)

The entire `<head>` moves out of `layouts/site.blade.php` into this partial. The layout's prop list becomes:

```blade
@props(['title' => null, 'description' => null, 'ogImage' => null, 'ogType' => 'website', 'navChapter' => null])
```

The partial renders:

- `<title>`: `$title` set → `{$title} · Kidical Mass België`; null → home fallback title.
- `<meta name="description">`: `$description` → per-page; fallback → `__('meta.default')` (new Dutch site-wide default, replacing the current hardcoded string).
- Canonical `<link rel="canonical">` = `request()->url()` (path, no query string). Same value used for `og:url`. Correctness in production depends on `APP_URL`; see deploy notes.
- Open Graph: `og:title` (bare `$title`, no brand suffix; `og:site_name` carries the brand), `og:description`, `og:image` (absolute URL; `$ogImage` or the branded default), `og:image:width` 1200 / `og:image:height` 630, `og:image:alt` (the page title, or the site name when no title is set), `og:url`, `og:type` (`$ogType`), `og:site_name` "Kidical Mass België", `og:locale` `nl_BE`.
- `<meta name="twitter:card" content="summary_large_image">` only; Twitter falls back to OG for the rest.
- Favicon links (`favicon.ico`, `favicon.svg`, `apple-touch-icon.png`) copied from `partials/head.blade.php`; assets already exist in `public/`.
- `<meta name="theme-color">` with the brand blue hex, copied from the corresponding `@theme` token in `resources/css/app.css` (head meta cannot reference CSS variables).
- `<link rel="manifest" href="/site.webmanifest">` plus a minimal manifest file (name, icons, theme/background color).
- Existing charset/viewport/bunny-fonts/@vite lines move over unchanged (keep the pipe-separated `family=` comment).

### Model changes (`Activity`, `Article`)

- New `og` conversion on the `main` collection: 1200×630 crop, **jpg** format (scraper-safe; avoids webp/AVIF edge cases with the GD driver). One-off `php artisan media-library:regenerate` needed for existing media.
- `metaDescription(): string` — strip tags from `content_nl`, squish, limit ~155 chars.
- `ogImageUrl(): ?string` — `getFirstMediaUrl('main', 'og')` or null (partial then falls back to the default image).

### Page wiring

- Every static page keeps its `title` and gains `:description="__('meta.<key>')"`.
- `activities/show.blade.php` (and `show-basic`): `:description="$activity->metaDescription()" :og-image="$activity->ogImageUrl()"`.
- `articles/show.blade.php`: same, plus `og-type="article"`.
- `groups/show.blade.php`: `:description="__('meta.chapter', ['name' => $group->name])"`.
- `home.blade.php`: remove the `title` prop.

### Cleanup

Move `public/chapter-stats-prototype.html`, `public/ride-page-redesign-prototype.html`, `public/team-cta-prototype.html` to `docs/wiki/design/prototypes/` (kept as design references, no longer crawlable).

## Testing

One feature test, `tests/Feature/SiteHeadTest.php`, asserting observable head behaviour per `docs/testing-conventions.md` (lang keys, not literal copy):

- Static page title ends with `· Kidical Mass België`; home title does not carry the suffix.
- Meta description present; static page uses its `__('meta.…')` value.
- Canonical equals the named route URL.
- Activity WITH a main image: `og:image` is an absolute URL containing the `og` conversion; activity WITHOUT one: `og:image` is the default asset.
- `og:locale` `nl_BE` and `twitter:card` present.

Existing public-routes smoke dataset untouched.

## Deploy / follow-up notes

- `APP_URL` must be the canonical production URL before launch (canonical + og:url + og:image absoluteness depend on it).
- Replace the placeholder `og-default.jpg` with Frederik's designed card before launch.
- Validate with Facebook Sharing Debugger and LinkedIn Post Inspector after deploy.
- Update concern D-10 to Partly (remaining: hreflang when FR lands, JSON-LD, sitemap) and append a `log.md` entry when this ships.

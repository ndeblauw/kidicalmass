---
title: Redirect Map (Wix → Laravel)
tags: [design, migration, launch]
sources: [wiki/design/25-content-migration, wiki/design/20-structure, wiki/site-audit]
phase: design
updated: 2026-07-07
---

# Redirect Map (Wix → Laravel)

Old Wix URLs → new routes, for launch cutover. Closes design concern [`D-7`](01-concerns.md). Derived from the [Content Migration Plan](25-content-migration.md) (the canonical page-by-page mapping) + the [Site Audit](../site-audit.md) URL inventory. **Launch-blocking:** Facebook links point at `/agenda`, and direct links exist to chapter postal-code pages.

**Built 2026-07-07** — `routes/redirects.php` + `app/Http/Controllers/LegacyRedirectController.php`, covered by `tests/Feature/LegacyRedirectTest.php`. The path set was re-verified against the live `sitemap.xml` that day; two pages missing from the original map were added (`/bxltour2026`, `/1330`). Two content-gated refinements stay open — see the hand-off list.

## Rules

- **All redirects are `301` (permanent)** — preserves SEO and keeps old links/bookmarks working.
- **Language resolution (decided 2026-06-02):** old Wix URLs are **language-neutral** (NL/FR were stacked on one page); new routes are **language-prefixed** (`/nl/…`, `/fr/…`). Redirects target the **neutral new path** (e.g. `/events`); the locale then resolves per request via **Accept-Language → cookie → geo**, **fallback NL**. So this map stays language-neutral — one runtime rule handles all of it. *As built:* the redirect controller resolves Accept-Language against `SetLocale::SUPPORTED` (today `nl` only, so a single hop to `/nl/…`); cookie/geo refinement becomes meaningful when FR lands.
- **Unmapped legacy paths:** standard **404** — don't mask unknown URLs by funnelling them to home.
- New paths shown **without** the `/nl/`–`/fr/` prefix throughout; the runtime rule adds it.

## National & content

| Old Wix URL | New route | Action / note |
|---|---|---|
| `/le-projet-het-project` | `/about/mission` | rewrite |
| `/organisation` | `/about/organisation` | |
| `/what-we-want` | `/about/vision` | merged into Vision |
| `/nos-revendications-onze-aanbevelingen` | `/about/vision` | merged into Vision |
| `/volunteer` | `/help-out` | |
| `/jobs` | `/help-out` | submenu folded in |
| `/help-je-n-ai-pas-de-vélo` | `/getting-started#no-bike` | absorbed → FAQ anchor (id added 07-07) |
| `/activités-vélo-fietsactiviteiten-kids` | `/getting-started` | absorbed; the built page has no "other activities" section, so no anchor |
| `/wallonie` | `/chapters` | region grouping on overview |
| `/en-image-in-beeld` | `/` | gallery dropped → home |
| `/downloads` | `/about` | materials distributed → About hub |

## Events ⚠️ critical (Facebook + bookmarks)

| Old Wix URL | New route | Note |
|---|---|---|
| `/agenda` | `/events` | **most critical** — Facebook links land here |
| `/event-list` | `/events` | |
| `/2026` | `/events` | season info lives in the calendar |
| `/bxltour2026` | `/events` | found in live sitemap 07-07 (missing from original map) |
| `/grande-grote-kidical-mass-2025` | `/events` | → event detail once real events migrate (hand-off 2) |
| `/grande-kidical-2024` | `/events` | → event detail once real events migrate (hand-off 2) |
| `/2023` | `/events` | → event detail once real events migrate (hand-off 2) |

## Chapters ⚠️ direct links exist

| Old Wix URL | New route | Note |
|---|---|---|
| `/all-groups` | `/chapters` | becomes the overview |
| `/bruxelles` | `/chapters` | Brussels clustering on the map |
| `/1330` | `/chapters` | Rixensart — no chapter in the new structure (found in live sitemap 07-07) |
| `/1000` `/1030` `/1040` `/1050` `/1060` `/1070` `/1080` `/1090` `/1120` `/1170` `/1190` `/5000` `/7000` | chapter page | resolved **zip → chapter at request time** (chapter URLs key on id, not postal); falls back to `/chapters` if the chapter vanishes |
| `/1081-82-83` (+ `/1081` `/1082` `/1083`) | chapter page | canonical postal **1081** = Koekelberg |
| `/1150-1200` (+ `/1150` `/1200`) | chapter page | canonical postal **1200** = Woluwe |

## News, press & shop

| Old Wix URL | New route | Note |
|---|---|---|
| `/my-blog` | `/about/news` | |
| `/post/{slug}` | `/about/news` | blanket for now — articles have no slugs yet; per-post targets when posts migrate (hand-off 4) |
| `/my-blog/hashtags/*` | `/about/news` | Wix tag pages dropped → news feed |
| `/press` | `/about/press` | |
| `/interview-fr` | `/about/press` | absorbed |
| `/product-page/*` | `/membership` | shop dropped → t-shirt **is** the membership |
| `/category/all-products` | `/membership` | shop dropped → membership |

*Newsletter (external Google Form): not a redirect we own; the replacement is the per-region email subscription on `/events`.*

## Build hand-off (Nico)

Status after the 2026-07-07 build (`routes/redirects.php`):

1. ~~**Locale resolution**~~ — **done**: `LegacyRedirectController` resolves Accept-Language against `SetLocale::SUPPORTED`, fallback `nl`. Extends automatically when FR is added; cookie/geo refinement optional then.
2. **Event detail targets** — *open, content-gated*: the three Grande Kidical Mass pages 301 to `/events` until the real events exist; then point each at its detail page.
3. ~~**Chapter canonical postals**~~ — **done**: `/1081-82-83` → Koekelberg (1081), `/1150-1200` → Woluwe (1200), every covered code mapped; resolution is zip → chapter at request time.
4. **Post slugs** — *open, content-gated*: `/post/{slug}` blankets to `/about/news`; add per-post targets when the blog posts migrate (articles currently have no slugs).
5. **Verify post-launch** — crawl the old URL list against the live site; the critical set (`/agenda`, all postal-code pages) must return `301` to a `200`. (Also on the [launch runway](../build/30-launch-runway.md), Lane 4.)

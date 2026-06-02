---
title: Redirect Map (Wix → Laravel)
tags: [design, migration, launch]
sources: [wiki/design/25-content-migration, wiki/design/20-structure, wiki/site-audit]
phase: design
updated: 2026-06-02
---

# Redirect Map (Wix → Laravel)

Old Wix URLs → new routes, for launch cutover. Closes design concern [`D-7`](01-concerns.md). Derived from the [Content Migration Plan](25-content-migration.md) (the canonical page-by-page mapping) + the [Site Audit](../site-audit.md) URL inventory. **Launch-blocking:** Facebook links point at `/agenda`, and direct links exist to chapter postal-code pages.

## Rules

- **All redirects are `301` (permanent)** — preserves SEO and keeps old links/bookmarks working.
- **Language resolution (decided 2026-06-02):** old Wix URLs are **language-neutral** (NL/FR were stacked on one page); new routes are **language-prefixed** (`/nl/…`, `/fr/…`). Redirects target the **neutral new path** (e.g. `/events`); a **locale middleware** then resolves to `/nl/` or `/fr/` via **Accept-Language → cookie → geo**, **fallback NL**. So this map stays language-neutral — one runtime rule handles all of it. *(Middleware + 301 config is build work — see hand-off.)*
- **Unmapped legacy paths:** standard **404** — don't mask unknown URLs by funnelling them to home.
- New paths shown **without** the `/nl/`–`/fr/` prefix throughout; the middleware adds it.

## National & content

| Old Wix URL | New route | Action / note |
|---|---|---|
| `/le-projet-het-project` | `/about/mission` | rewrite |
| `/organisation` | `/about/organisation` | |
| `/what-we-want` | `/about/vision` | merged into Vision |
| `/nos-revendications-onze-aanbevelingen` | `/about/vision` | merged into Vision |
| `/volunteer` | `/help-out` | |
| `/jobs` | `/help-out` | submenu folded in |
| `/help-je-n-ai-pas-de-vélo` | `/getting-started#no-bike` | absorbed → section anchor |
| `/activités-vélo-fietsactiviteiten-kids` | `/getting-started#other-activities` | absorbed → section anchor |
| `/wallonie` | `/chapters` | region grouping on overview |
| `/en-image-in-beeld` | `/` | gallery dropped → home |
| `/downloads` | `/about` | materials distributed → About hub |

## Events ⚠️ critical (Facebook + bookmarks)

| Old Wix URL | New route | Note |
|---|---|---|
| `/agenda` | `/events` | **most critical** — Facebook links land here |
| `/event-list` | `/events` | |
| `/2026` | `/events` | season info lives in the calendar |
| `/grande-grote-kidical-mass-2025` | `/events/{slug}` | featured event — **slug filled at seed** |
| `/grande-kidical-2024` | `/events/{slug}` | past event — **slug filled at seed** |
| `/2023` | `/events/{slug}` | past event — **slug filled at seed** |

## Chapters ⚠️ direct links exist

| Old Wix URL | New route | Note |
|---|---|---|
| `/all-groups` | `/chapters` | becomes the overview |
| `/bruxelles` | `/chapters` | Brussels clustering on the map |
| `/1000` `/1030` `/1040` `/1050` `/1060` `/1070` `/1080` `/1090` `/1120` `/1170` `/1190` `/5000` `/7000` | `/chapters/{postal}` | 1:1 — same postal code |
| `/1081-82-83` | `/chapters/{canonical-postal}` | combined page → chapter's **canonical postal** (set at seed); also map 1081/1082/1083 → same |
| `/1150-1200` | `/chapters/{canonical-postal}` | combined page → chapter's **canonical postal** (set at seed); also map 1150/1200 → same |

## News, press & shop

| Old Wix URL | New route | Note |
|---|---|---|
| `/my-blog` | `/about/news` | |
| `/post/{slug}` | `/about/news/{slug}` | slug preserved where possible (Nico migrates posts) |
| `/my-blog/hashtags/*` | `/about/news` | Wix tag pages dropped → news feed |
| `/press` | `/about/press` | |
| `/interview-fr` | `/about/press` | absorbed |
| `/product-page/*` | `/membership` | shop dropped → t-shirt **is** the membership |
| `/category/all-products` | `/membership` | shop dropped → membership |

*Newsletter (external Google Form): not a redirect we own; the replacement is the per-region email subscription on `/events`.*

## Build hand-off (Nico)

Open items before this map is launch-ready — all build-side, not design decisions:

1. **Locale middleware** — implement neutral-path → `/nl/`|`/fr/` resolution (Accept-Language → cookie → geo, fallback NL).
2. **Event slugs** — fill `{slug}` for the three Grande Kidical Mass redirects once events are seeded.
3. **Chapter canonical postals** — confirm the canonical postal code for the two combined pages (`/1081-82-83`, `/1150-1200`) and map every covered code to it.
4. **Post slugs** — preserve `/post/{slug}` slugs through blog migration where feasible; otherwise add per-post redirects.
5. **Verify post-launch** — crawl the old URL list against the live site; the critical set (`/agenda`, all postal-code pages) must return `301` to a `200`.

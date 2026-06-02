---
title: Content Migration Plan
tags: [design]
sources: [wiki/ux-planning, notion]
phase: design
updated: 2026-06-02
---

# Content Migration Plan

*Maps every current Wix page to its destination in the new structure. Derived from [Structure](20-structure.md) + the [Site Audit](../site-audit.md).*

*Maps every current page to its destination in the new Laravel structure.*

### How to Read This Plan

- **Rewrite** — content exists but needs to be rewritten (tone of voice, structure, bilingual separation)
- **Migrate** — content moves to a new location, light editing only
- **Merge** — content from multiple old pages combines into one new page
- **Absorb** — content doesn't get its own page; it gets distributed across other pages
- **Drop** — page is retired, content is not carried forward
- **Seed** — Nico builds database seeders from existing content (events, chapters)

### Key Structural Changes vs. Current Site

1. **Language routing replaces stacking** — every page goes from ~2x needed length to clean NL/FR/EN paths
2. **Events replace Agenda** — hand-typed calendar becomes database-driven with detail pages, killing the Facebook dependency
3. **Chapter pages surface from hiding** — 14+ hidden postal code pages become first-class citizens
4. **Getting Started fills the onboarding gap** — no current page answers "I'm curious, how do I actually do this?"
5. **Advocacy content consolidates** — two separate manifesto pages merge into one vision statement
6. **Contact becomes contextual** — no more 3 email addresses; forms route to the right person
7. **Downloads distribute** — materials live where they're needed
8. **Photo gallery dissolves** — images go everywhere instead of being siloed
9. **Grande Kidical normalises** — annual flagship uses the same Events system, just featured
10. **News and Partners move under About** — low volume doesn't need top-level nav real estate
11. **Press coverage is dual-homed** — national on /about/press, local on chapter pages (optional)

### Page-by-Page Mapping

Every current Wix page mapped to its destination. Source: [Site Audit](../site-audit.md) + the [Sitemap](20-structure.md).

**National & content pages**

| Current page | What it is now | Action | New home |
|---|---|---|---|
| `/` | Home + Contact — hero links to Facebook, stacked FR/NL mission, hardcoded stats, Wallonie/Vlaanderen blocks, spacefunding, 3 news, partner logos, 3 contact emails | **Rewrite** | `/` — new hero with a primary "find a ride" CTA, dynamic events strip, chapter map, live stats, news preview, partners bar; contact becomes contextual |
| `/le-projet-het-project` | Mission — 3 axes, inclusivity, hardcoded stats | **Rewrite** | `/about/mission` — ToV rewrite, bilingual split, live stats, clean slug |
| `/organisation` | Governance, coordination duo, static SVG organigram | **Rewrite** | `/about/organisation` — accessible organigram, chapters linked |
| `/what-we-want` | Child Friendly City manifesto (FR-only essay, parent quotes, PDF) | **Merge** | `/about/vision` — merged with revendications into one Vision page |
| `/nos-revendications-onze-aanbevelingen` | 4 policy demands FR+NL | **Merge** | `/about/vision` — merged with what-we-want |
| `/volunteer` | 5 roles, email-only signup, Google Docs rules, YouTube safety video | **Rewrite** | `/help-out` — 5 roles confirmed, routed contact form, honest "what joining looks like" section |
| `/jobs` | Jobs (volunteer submenu) | **Merge** | `/help-out` |
| `/help-je-n-ai-pas-de-vélo` | "I don't have a bike" help | **Absorb** | `/getting-started` → "Don't have a bike?" section (Loopz, Fietsbieb, Kidical Mouse) |
| `/activités-vélo-fietsactiviteiten-kids` | Other bike activities for kids | **Absorb** | `/getting-started` → "Other bike activities" section |
| `/en-image-in-beeld` | Photo gallery | **Drop** | — gallery dissolves; images distributed across pages (explicit scope cut) |
| `/downloads` | 2025 flyer/poster PDFs; broken unlabelled 2024 thumbnails | **Absorb** | chapter pages + `/about` — materials live where needed; broken 2024 archive dropped |
| `/wallonie` | Sparse city list + email CTA | **Seed** + **Absorb** | `/chapters` — region grouping on the overview; Liège = external pin |

**Events**

| Current page | What it is now | Action | New home |
|---|---|---|---|
| `/agenda` | Hand-typed trilingual calendar, every event links to Facebook | **Seed** + **Drop** | `/events` (+ `/events/[slug]`) — DB-driven (Nico seeds); page retired; **redirect critical** (Facebook links + bookmarks point here) |
| `/event-list` | Wix events list | **Seed** + **Drop** | `/events` |
| `/2026` | 2026 season landing | **Absorb** + **Drop** | `/events` — season info lives in the calendar |
| `/grande-grote-kidical-mass-2025` | Annual flagship event | **Seed** | `/events/[slug]` — normalised into Events as a *featured* event |
| `/grande-kidical-2024` | Past flagship | **Seed** | `/events/[slug]` — past, via Events upcoming/past toggle |
| `/2023` | Past flagship | **Seed** | `/events/[slug]` — past, via Events toggle |

**Chapters**

| Current page | What it is now | Action | New home |
|---|---|---|---|
| `/all-groups` | Group directory | **Seed** | `/chapters` — becomes the overview (map + list) |
| `/bruxelles` | Brussels hub | **Seed** | `/chapters` — Brussels clustering on the map |
| `/1000` `/1030` `/1040` `/1050` `/1060` `/1070` `/1080` `/1081-82-83` `/1090` `/1120` `/1150-1200` `/1170` `/1190` `/5000` `/7000` | 15 hidden per-municipality pages | **Seed** | `/chapters/[postal-code]` — first-class pages, fixed template; **redirects critical** (direct links exist) |

**News, Press, Store & external**

| Current page | What it is now | Action | New home |
|---|---|---|---|
| `/my-blog` + `/post/[slug]` (13 posts, FR/NL mixed inline) | Wix blog (unbranded URL) | **Migrate** | `/about/news` (+ `/about/news/[slug]`) — branded URL, bilingual split, author attribution; Nico migrates posts |
| `/my-blog/hashtags/*` (5 auto tag pages) | Wix-generated tag pages | **Drop** | — Wix artifact, no equivalent |
| `/press` | Chronological link list, PDFs on Wix CDN | **Rewrite** | `/about/press` — logos/excerpts, language labels, media kit; local press dual-homed on chapter pages |
| `/interview-fr` | Single press interview (press submenu) | **Absorb** | `/about/press` |
| `/product-page/*` · `/category/all-products` | Wix shop — 2 t-shirts | **Drop** ✅ | — confirmed cut (interview 2026-05-18): no public shop; the t-shirt is the spacefunding membership |
| Newsletter (external Google Form) | Email signup off-site | **Drop** / replace | `/events` — replaced by per-region email notification subscriptions |
| Contact (`bike@`, `cecilia@`, `contact@kidicalmass.brussels`) | 3 inconsistent email addresses | **Absorb** | site-wide — contextual routed forms + single footer contact; domain inconsistency resolved |

### Decision Flags for Leticia

Status after the Leticia interview (2026-05-18):

1. **Web store (2 t-shirts) — RESOLVED:** confirmed cut. No public shop; the t-shirt *is* the spacefunding membership, linked from the membership page.
2. **Photo gallery dissolves — RESOLVED:** confirmed. A few inline photos per chapter to show it's fun; no gallery system. Rides go to social media.
3. **"I don't have a bike" demoted — RESOLVED:** confirmed. Very small % (like Fietsbieb); lives as a Getting Started section.
4. **Grande Kidical Mass loses its dedicated page — STILL OPEN:** not explicitly raised in the interview. Confirm in a later check that the flagship as a *featured event* (not a hand-built yearly page) is acceptable.
5. **Liège stays external — RESOLVED (refined):** KM wants Liège's ride data *in* the site and to bring them back into the network; show as a pin and include the data even though it duplicates their own site.

### Open Questions for Migration

- **Redirect map:** ✅ documented in [`26-redirect-map.md`](26-redirect-map.md) (closes the design side of `D-7`). Build fill-ins (locale middleware, event slugs, combined-postal canonicals) tracked there.
- **Build order for Nico:** Proposed: (1) Events + Event detail → (2) Chapters + Chapter pages → (3) Help out → (4) Getting Started → (5) About section → (6) Home. Validate with Nico.
- **Cutover plan:** When do chapter leads switch from Wix to Filament? Is there a parallel-running period?

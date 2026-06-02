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
5. **Liège — REVISED 2026-06-02:** now a **hosted full chapter** (like Mons), not an external pin — both bring their data *and* a hosted `/chapters/` page into the site, though they keep their own domains (page may link out). *(Earlier: external pin with ride data included.)*

### Content-level cross-check (2026-06-02)

Page-by-page mapping above is complete — **no whole page is lost.** But reading the raw page *bodies* (not just titles) surfaced content fragments and risks the page map hides. Logged here so none evaporate.

**Transition risks**

- **Chapter pages launch near-empty (biggest risk).** Only **two** chapters have real scraped content: **Namur (`/5000`)** and **Mons (`/7000`)**. The Brussels postal pages (`/1000`…) are *not in the scrape* — and the [Site Audit](../site-audit.md) confirms "13+ active local groups but not one has its own page." So "seed the chapter pages" only yields a name + postal + agenda-derived rides for ~13 Brussels chapters; **intro, team, partners, history, photos must be authored fresh by each lead.** This is the cutover/maintainability question, not a migration. → ties to the cutover plan below.
- **Mons + Liège both hosted as full chapters (decided 2026-06-02).** Both run their own domains (`mons.bike`, `kidicalmassliege.org`); the hosted page may link out but is first-class. **This revises the earlier "Liège = external pin" call** (Decision Flag #5 below) — Liège now gets a hosted `/chapters/` page authored from their site's data (no `kidicalmass.be` page exists to migrate), not just a map pin.

**Structural fixes already applied** (to [Structure](20-structure.md))

- **Chapter template gained two fields** the real pages use but the template lacked: an **intro / "what our rides are like"** lead paragraph, and an optional **History / our story** block (Mons-style founders + milestones, hidden if empty).

**Content fragments to carry (don't drop silently)**

- **One-off donation IBAN** ("Donation unique / Eénmalige donaties — BE72 8919 4405 3116") is live on home + spacefunding, but [Scope](10-scope.md) says membership is *recurring-only, no one-off path*. **Decided 2026-06-02:** **drop the one-off IBAN for v1** (recurring-only stands); **confirm with Leticia** before final, as money channels are hers.
- **"What is a ride" practical spec** (ages ~4–12, **no draisiennes/balance bikes**, child accompanied by an adult, max 5–7 km, slow pace, max 1 h, free/no registration, music) — the canonical ride definition, on home + every chapter page. → `/getting-started` "what to expect" + event-detail field defaults.
- **"Don't have a bike" provider list mismatch.** Live `/help-je-n-ai-pas-de-vélo` lists **Cyclo** (2nd-hand), **Fietsbieb** (10 named Brussels communes), **Loopz** (promo code `KIDICALMASS` = 2 months free), **My Kids Bikes**. Structure/scope say "Loopz, Fietsbieb, **Kidical Mouse**." **Decided 2026-06-02: out of scope for v1 core** — the whole "don't have a bike" provider section is **deferred** (verify current providers later; focus on core). Preserve the Loopz promo code for when it returns.
- **Vision is heavier than "merge."** `what-we-want` is **FR-only**, **dated** (2024 elections), **Brussels-specific coalition** manifesto + parent quotes + external PDF (`cloud.heroesforzero.be`). `/about/vision` needs a **rewrite + a fresh NL translation** + de-dating, not a light merge. The external manifesto PDF is third-party — link, don't rehost.
- **Volunteer rules (ROI / huishoudelijk reglement, on Google Docs) + "Safety First" YouTube video** — currently external; these are the back-office "documents + video" (D-1). Decide host-vs-link at build.
- **National partners/funders + campaign affiliation** — home names Bruxelles Mobilité, Clean Cities, Bruxelles Ville, Commune de Schaerbeek, spacefunders, + "#StreetsForKids by Clean Cities" affiliation. → `/about/partners` (don't lose the campaign affiliation).
- **Contact domain inconsistency** (`.be` vs `.brussels`; bike@/cecilia@/contact@kidicalmass.brussels + local emails) — resolved by routed forms + `/contact`; pick one canonical domain at build.

### Open Questions for Migration

- **Redirect map:** ✅ documented in [`26-redirect-map.md`](26-redirect-map.md) (closes the design side of `D-7`). Build fill-ins (locale middleware, event slugs, combined-postal canonicals) tracked there.
- **Build order for Nico:** Proposed: (1) Events + Event detail → (2) Chapters + Chapter pages → (3) Help out → (4) Getting Started → (5) About section → (6) Home. Validate with Nico.
- **Cutover plan:** When do chapter leads switch from Wix to Filament? Is there a parallel-running period? **Sharpened by the cross-check:** because Brussels chapter pages have no migratable content, cutover needs a *content-authoring* onboarding for leads (not just a data move) — Namur + Mons can seed as worked examples.

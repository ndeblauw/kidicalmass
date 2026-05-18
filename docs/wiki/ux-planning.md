---
title: UX Planning
tags: []
sources: [notion]
updated: 2026-04-13
---

Site-level UX planning for kidicalmass.be. Strategy, Scope, and Structure are complete. Page specs live in `ux/`.

**Page specs:** [Activity Detail](ux/activity-detail.md) · [Events Overview](ux/events-overview.md) · [Home](ux/home.md) · [Help Out](ux/help-out.md) · [Getting Started](ux/getting-started.md) · [Chapters](ux/chapters.md) · [About](ux/about.md)

---

## Principles

*Project-specific tuning. These take precedence over any default UX playbook.*

**Kept as-is:**
- User needs lead; org goals follow — strategy is consistently family-first throughout
- Prioritise 2–3 audiences explicitly — three named and ranked audiences
- Organise around user tasks, not org structure — Events = location-first discovery, not org nav
- Mobile-first always — confirmed for all page specs

**Project-specific principles:**
- **Template over approval** — strict design constraints (fixed templates, design system) replace Leticia's manual sign-off. The system guarantees quality, not a person.
- **Bilingual as structural** — NL/FR/EN are routed URL paths, not content stacks. Every content decision must be trilingually viable.
- **Local before national** — chapter pages are first-class citizens. The national site enables local discovery; it doesn't replace it.

**Tuned:**
- **Maintainability gate** — the backstage is a Laravel/Filament platform built by Nico. The maintainability test = can a chapter lead do this without coordination duo involvement? If not, it is a scope risk.
- **Content lifecycle** — formal lifecycle documentation is replaced by a clear ownership model: coordination duo owns national content; chapter leads own chapter-level content; any chapter lead can publish news.

**Suspended for this project:**
- **North Star metric** — the movement is community-driven, not conversion-driven. "Families who show up to their first ride" is the closest proxy, but is not currently trackable via the site alone. Suspended as a formal KPI for MVP.
- **Free-text search** — suspended for MVP. Volume (~60 events/season, ~20 chapters) makes location filter sufficient.

---

## Strategy

*Why does this exist? For whom?*

### Primary Audiences (public site)

**1. Families — first-timers and returnees**
- Arrive curious and already half-sold — they've seen others do it and assume they can too
- Primary need: quick answers — when is the next ride, where, can we come?
- Reassurance is not the main goal; informing is
- No significant participation barrier to communicate (inclusive by default; bike availability via Kidical Mouse is an edge case)

**2. Potential volunteers**
- Want to contribute but don't yet know how or where they fit
- Need a clear path from curiosity to contact

### Secondary Audiences (public site)

- **Potential chapter leads** — *demoted from primary (Leticia interview 2026-05-18): the challenge is more participants in existing chapters, not more groups (~45 groups already, enough; new groups fail without support).* The chapter map + growth story still serve them and grant applications, but recruiting new leads is not a primary site job.
- **Sponsors/partners** — get a dedicated section, not a deeply designed experience
- **Press** — not a primary audience; no specific design needed

### Existing chapter leads

Served through the logged-in/admin experience, not the public site.

### Organisational Objectives

- **Bring money in (top objective — Leticia interview 2026-05-18):** grow recurring individual membership ("spacefunding") and donations so KM can hire and stay self-sufficient. Prefer recurring individual donors over grant dossiers. The site treats this as a first-class job — dedicated membership page + persistent site-wide CTA.
- Make it effortless for families to find and attend rides
- Convert curious visitors into volunteers through a clear path
- **Grow participation in existing chapters; support new chapters well rather than maximise their number** (reframed from "enable new chapter leads to emerge" — interview 2026-05-18)
- Give Leticia and Cecilia their time back — less admin, more trust in the system
- Make movement growth legible (map, chapters, national reach) for grants and new partnerships
- Enable chapter self-publishing within strict design constraints — no approval flows needed

### Emotional Register

- **Public site:** joyful, alive, bold, scrappy, child-centred, slightly activist — playful and direct, not institutional
- **Guardrail (interview 2026-05-18):** keep it light and broad — appeal to the mass, never hardcore-cyclist ("fiets-fiets-fiets") or niche. Mildly activist, not militant.
- **Partner/sponsor content:** a notch more serious, but not stiff or corporate

### Language

**v1 = bilingual NL + FR** (interview 2026-05-18: *"doe maar twee talen"*). English is deferred to a later phase — same structure, added when the team's review capacity allows (the bottleneck is review, not generation).

### Key Tension

Leticia wants brand consistency and quality without being the bottleneck. Resolution: strict design constraints (templates, design system) replace approval flows. The system guarantees quality, not a person.

### Open Questions

- **Digital mission statement:** Validate working draft with Leticia — *"kidicalmass.be is the front door to a growing Belgian movement — it gets families to their next ride and turns curiosity into participation."*
- **Value proposition:** Confirm one-sentence VP for hero copy — *"Kidical Mass is a free monthly bike ride for families in your neighbourhood — all ages, all bikes, just show up."*
- **Facebook vs. site role — RESOLVED (interview 2026-05-18):** the site is the canonical source of ride detail; Facebook stays for reach and as Leticia's turnout signal (she reads FB "interested" counts to decide whether to mobilise). The site does not replace Facebook — it is the link Facebook points to.
- **Private organiser back-office + "who's coming" — OPEN (decision deferred):** a per-chapter logged-in area (checklist, ride archive, materials) and an "I'm attending"/hosts-visible nudge. Leticia leans positive for a minimal materials area but is unsure it's essential. Resolve after the chapter-lead and roze-hesje interviews. Supersedes the flat "deferred" framing in Out of Scope.
- **Meetup visibility breadth — VALIDATE with Leticia:** the 2026-05-18 decision is that *any logged-in account* sees *all* groups' meetings/workshops. This is broader than Leticia's stated "strong local communities / no cross-community browsing" preference — confirm she's comfortable with cross-group visibility for accounts (vs. group-scoped).

---

## Scope

*What are we building? (MVP)*

### Core

1. **MVP core:** Activity database — one model covering public rides *and* internal meetings/workshops, gated by type (calendar + detail pages + iCal) + Chapter pages (self-published, fixed template) + Volunteer path (routed contact form) + Accounts (logged-in users see internal meetups) + National pages (Home, About section, Getting Started)
2. **Backstage as constraint:** everything in scope must be maintainable by chapter leads without coordination duo involvement
3. **Genuine cuts (confirmed, interview 2026-05-18):** photo-gallery system, poster/flyer auto-generation, public web store — removed, not deferred. (Volunteer attendance / private organiser back-office is NOT a settled cut — it is an open question; see Strategy → Open Questions.)
4. **Content ownership by default:** coordination duo → national content; chapter leads → chapter content; any lead → news
5. **Migration is Nico's:** database seeders for existing content. Key pages rewritten using the ToV guide.

### Functional Specifications

**Activities — public rides + internal meetups (added/clarified — interview 2026-05-18)**
- One `Activity` model, four types: `kidicalmass` (public family ride), `meeting`, `workshop`, `other`. Bilingual (NL/FR), linked to one or more groups, has an organizer. *(Already built by Nico on `main` — the Articles/Activities split exists. Not yet built: a visibility field — see build implication.)*
- **Visibility rule (confirmed 2026-05-18):** `kidicalmass` = public (anyone). `meeting` / `workshop` / `other` = visible to **any logged-in account**, across all groups — not gated by group membership, not gated by paying/spacefunding status.
- Find rides by location; iCal export
- Per-region/per-chapter email subscriptions — low frequency (rides published ~Jan & Sept on a school-year rhythm; not an editorial newsletter). "Next ride near you" + nearby gemeentes + latest press/photos.
- Event detail pages with full practical info: date, time, meeting point, distance, duration, age range, cost, Komoot route link
- **Build implication for Nico:** activities have no public/private/visibility field yet — gating internal meetups to logged-in accounts needs a type-based rule (or a visibility column). Flag in build.

**Chapter pages**
- Self-published by chapter leads (no going through Brussels)
- Content per chapter: schedule, team members, local partners, press coverage, downloads
- Chapter leads manage their own content within design constraints

**Volunteer onboarding**
- Contact form per chapter, routed to the correct local lead (not the central mailbox)
- A clear "Zo word je vrijwilliger" page: explicit steps + what to expect
- Auto reminder/explainer email after sign-up ("here's how it works")
- Not a structured workflow/dashboard — that is an open question (see Strategy → Open Questions)

**Contributor section**
- Covers both "I want to volunteer" and "I want to start a chapter"
- Chapter overview page includes a "start a chapter" CTA
- Full chapter-start intake process is static for MVP; structured workflow deferred

**News / Blog**
- Already built by Nico — included in MVP

**Membership / funding (added — interview 2026-05-18, top org objective)**
- Dedicated membership page explaining "spacefunding": recurring individual membership via the Growthfunding platform (€36/yr ≈ €3/mo, includes t-shirt, makes you a co-financer)
- Persistent site-wide CTA (global footer, every page) to become a member / donate
- The t-shirt is the membership, not a shop — link it to membership; no separate web store
- Prefer recurring individual donations over grant dossiers

**Accounts (added — interview 2026-05-18)**
- Two distinct concepts, kept separate: a **group-volunteer account** (`group_user` — a person belongs to a chapter; already modelled) and a **spacefunding member** (recurring donor — a funding status, see Membership). A person can be both; neither implies the other.
- Access to internal meetings/workshops depends on **being a logged-in account**, not on paying status.
- A logged-in account gets a personal view of upcoming meetings/workshops (across groups) on top of the public ride calendar.

**Bilingual routing**
- NL + FR — routed, not stacked (v1). English deferred to a later phase.

**Map of chapters**
- Shows national reach across Belgium
- Supports movement growth story for grants and new chapter leads

**Sponsor section**
- Basic display of active sponsors/partners
- Full tracking of sponsor obligations (tiers, logo placement, contract status) is deferred

### Content Requirements

**National pages:** Home, What is Kidical Mass / how it works, Events / calendar, Chapter overview + map, Getting Started, Help Out, News, Sponsors / partners, **Membership / spacefunding** (new). Mission/Vision/Organisation: shorten and audience-segment — keep internal-org detail off the public site (interview 2026-05-18).

**Chapter pages (per chapter):** Local schedule (public rides), Team, Local partners, Press coverage, Downloads, + the chapter's meetings/workshops shown to any logged-in account

**Event pages (per event):** All practical details a family needs to show up

### Out of Scope for MVP

- Photo-gallery system (confirmed cut, interview 2026-05-18 — a few inline photos per chapter only; rides go to social media)
- Public web store (confirmed cut — the t-shirt is the membership; buy via spacefunding)
- Poster / flyer auto-generation (confirmed cut — too much to build; layout stays in InDesign with interns)
- Worked-out "start a chapter" intake flow (static page only — challenge is participants, not new groups)
- Automated photo tagging (deferred)
- Full sponsor obligation tracking (deferred)
- *Private organiser back-office (per-chapter checklist / ride-archive / materials) + attendance "who's coming": NOT settled — see Strategy → Open Questions. Distinct from member-visible meetups, which IS decided (see Activities above).*

---

## Structure

*How does it fit together? Information architecture and sitemap.*

### Navigation Model

Primary discovery is through **location/date-first search** (Events page + Home), not through chapter browsing. Chapters exist as a directory and individual pages, but are not the primary path for families trying to find a ride.

### Language Routing

Parallel URL paths: `/nl/`, `/fr/` (v1) — not a language switcher; `/en/` added in a later phase, same structure. Chapter pages render in the chapter's own language (FR chapters in FR, NL chapters in NL). National content is available in NL + FR for v1.

### Main Nav (5 items)

```
Events  |  Chapters  |  Getting Started  |  Help out  |  About
```

About expands to: Mission, Vision, Organisation, News, Press, Partners.

"Help out" / "Meehelpen" / "S'engager" ✅ Warmer than "Volunteer" — better fit for Tone of Voice.

### Sitemap

```
kidicalmass.be (NL / FR — routed per path; EN later)
│
├── Home (/)
│   └── Upcoming events near me, chapter map, movement stats, news preview
│
├── Events (/events)
│   ├── Calendar — public rides (kidicalmass type); filter by location, date, iCal
│   │   └── Toggle: upcoming (default) / past events
│   │   └── Logged-in account also sees meeting/workshop activities (any group)
│   └── Event detail (/events/[slug])
│       └── Date, time, meeting point, distance, route, chapter info
│       └── "Grande Kidical Mass" = featured event, same system
│
├── My activities (account-only)  ← added 2026-05-18
│   └── Upcoming meetings/workshops across groups (any logged-in account)
│
├── Chapters (/chapters)
│   ├── Overview — map + list of all chapters (grouped by region), "start a chapter" CTA
│   │   └── Liège shown as regular pin → kidicalmassliege.org (external) ✅
│   │   └── Flanders group hidden until at least one Flemish chapter is active ✅
│   └── Chapter page (/chapters/[postal-code])
│       ├── Local schedule (public rides — pulls from Activities, auto, never manual)
│       ├── Meetings/workshops of this chapter — shown to any logged-in account
│       ├── Team (optional — hidden if no team added)
│       ├── Volunteer mini form (routed to local lead)
│       ├── Local partners (optional — hidden if empty)
│       ├── Press coverage (optional — hidden if empty)
│       └── Downloads (optional — hidden if empty)
│       └── Template is strictly uniform — no chapter colours or logos ✅
│
├── Getting Started (/getting-started)
│   └── Practical info for families new to cycling with kids
│       ├── What to expect at a ride
│       ├── Practical FAQ (age, gear, weather, registration)
│       ├── Don't have a bike? (Loopz ✅ active, Fietsbieb, Kidical Mouse ✅ operational)
│       └── Other bike activities for kids in Belgium
│
├── Help out (/help-out) ✅
│   ├── Roles + how to volunteer (5 roles confirmed: pink vest, co-organiser,
│   │   communicator, photographer, DJ) ✅
│   ├── Contact form → routed to nearest chapter
│   └── Start a chapter (short overview + email CTA) ✅
│
├── About (/about)
│   ├── Mission (/about/mission)
│   │   └── What Kidical Mass is, 3 axes, stats, inclusivity
│   ├── Vision (/about/vision)
│   │   └── Policy demands + Child Friendly City manifesto (merged) ✅
│   ├── Organisation (/about/organisation)
│   │   └── Governance, coordination duo, local group structure
│   ├── News (/about/news)
│   │   └── Article (/about/news/[slug])
│   ├── Press (/about/press)
│   │   └── National + local coverage (local also shows on chapter pages)
│   └── Partners (/about/partners)
│       └── National sponsors/partners with logo display
│
├── Membership / Spacefunding (/membership)  ← added 2026-05-18
│   └── What spacefunding is · become a member (recurring) · t-shirt = membership
│   └── Persistent CTA in the global footer on every page
│
└── Admin (/admin — Filament panel, separate)
    ├── Coordination duo: full access across all chapters
    └── Chapter lead: own chapter only
```

### Key Structural Decisions

- **No chapter as primary nav path** — families use Events (calendar + location filter), not Chapters, to find a ride
- **Volunteer contact form lives on chapter pages** — the Help Out page explains roles and routes people to the right chapter
- **Chapter pages are self-published** — chapter leads manage their own content within design constraints. No approval flow needed.
- **Chapters overview supports grants + existing groups** — the map mainly serves grant applications and helps families find existing groups; recruiting new leads is secondary (reframed, interview 2026-05-18)
- **Membership is site-wide** — dedicated `/membership` page + a persistent CTA in the global footer on every page (top org objective)
- **Site is canonical, not a Facebook replacement** — Facebook stays for reach and turnout signal; the site is the link Facebook points to
- **One Activity model, gated by type** — public rides (`kidicalmass`) are open to all; `meeting`/`workshop`/`other` show only to logged-in accounts. Internal meetups live in the same system as rides, not a separate product.
- **Account ≠ paying member** — a logged-in group-volunteer account (which gates meetups) is distinct from a spacefunding donor (funding status). A person can be both; neither implies the other.
- **Mission and Vision are separate** — Mission = what Kidical Mass is + impact stats; Vision = policy demands + advocacy
- **News in About section** — consistent with current site; low volume doesn't justify top-level nav

---

## Content Migration Plan

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

Every current Wix page mapped to its destination. Source: [Site Audit](site-audit.md) + the new Sitemap above.

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

- **Redirect map:** Document old → new URL redirects before launch. Particularly important for Facebook links to `/agenda` and direct links to chapter postal code pages (`/1030`, `/1050`, etc.).
- **Build order for Nico:** Proposed: (1) Events + Event detail → (2) Chapters + Chapter pages → (3) Help out → (4) Getting Started → (5) About section → (6) Home. Validate with Nico.
- **Cutover plan:** When do chapter leads switch from Wix to Filament? Is there a parallel-running period?

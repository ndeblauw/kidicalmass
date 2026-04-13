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

**3. Potential chapter leads**
- Considering starting a chapter in a city that doesn't have one yet
- The map of chapters and the growth story of the movement is for them
- Also serves grant and subsidy applications

### Secondary Audiences (public site)

- **Sponsors/partners** — get a dedicated section, not a deeply designed experience
- **Press** — not a primary audience; no specific design needed

### Existing chapter leads

Served through the logged-in/admin experience, not the public site.

### Organisational Objectives

- Make it effortless for families to find and attend rides
- Convert curious visitors into volunteers through a clear path
- Enable new chapter leads to emerge from the site (not just word of mouth)
- Give Leticia and Cecilia their time back — less admin, more trust in the system
- Make movement growth legible (map, chapters, national reach) for grants and new partnerships
- Enable chapter self-publishing within strict design constraints — no approval flows needed

### Emotional Register

- **Public site:** joyful, alive, bold, scrappy, child-centred, slightly activist — playful and direct, not institutional
- **Partner/sponsor content:** a notch more serious, but not stiff or corporate

### Language

Three real audiences: NL, FR, EN — not aspirational, all three in scope.

### Key Tension

Leticia wants brand consistency and quality without being the bottleneck. Resolution: strict design constraints (templates, design system) replace approval flows. The system guarantees quality, not a person.

### Open Questions

- **Digital mission statement:** Validate working draft with Leticia — *"kidicalmass.be is the front door to a growing Belgian movement — it gets families to their next ride and turns curiosity into participation."*
- **Value proposition:** Confirm one-sentence VP for hero copy — *"Kidical Mass is a free monthly bike ride for families in your neighbourhood — all ages, all bikes, just show up."*
- **Facebook vs. site role:** Is Facebook the channel for real-time notifications (cancellations, reminders) while the site serves as permanent reference + first-timer conversion? If yes, make this explicit — it affects how time-sensitive the site needs to feel.

---

## Scope

*What are we building? (MVP)*

### Core

1. **MVP core:** Events database (calendar + detail pages + iCal) + Chapter pages (self-published, fixed template) + Volunteer path (routed contact form) + National pages (Home, About section, Getting Started)
2. **Backstage as constraint:** everything in scope must be maintainable by chapter leads without coordination duo involvement
3. **Three genuine cuts:** photo galleries, volunteer attendance tracking, poster/flyer generation — removed, not deferred
4. **Content ownership by default:** coordination duo → national content; chapter leads → chapter content; any lead → news
5. **Migration is Nico's:** database seeders for existing content. Key pages rewritten using the ToV guide.

### Functional Specifications

**Event / Calendar system**
- Find rides by location
- iCal export (add to personal calendar)
- Per-region notification subscriptions (email, as Facebook alternative)
- Event detail pages with full practical info: date, time, meeting point, distance, duration, age range, cost, Komoot route link

**Chapter pages**
- Self-published by chapter leads (no going through Brussels)
- Content per chapter: schedule, team members, local partners, press coverage, downloads
- Chapter leads manage their own content within design constraints

**Volunteer onboarding**
- Contact form per chapter, routed to the correct local lead
- Not a structured workflow — a well-routed contact form is enough for MVP

**Contributor section**
- Covers both "I want to volunteer" and "I want to start a chapter"
- Chapter overview page includes a "start a chapter" CTA
- Full chapter-start intake process is static for MVP; structured workflow deferred

**News / Blog**
- Already built by Nico — included in MVP

**Trilingual routing**
- NL, FR, EN — routed, not stacked

**Map of chapters**
- Shows national reach across Belgium
- Supports movement growth story for grants and new chapter leads

**Sponsor section**
- Basic display of active sponsors/partners
- Full tracking of sponsor obligations (tiers, logo placement, contract status) is deferred

### Content Requirements

**National pages:** Home, What is Kidical Mass / how it works, Events / calendar, Chapter overview + map, Getting Started, Help Out, News, Sponsors / partners

**Chapter pages (per chapter):** Local schedule, Team, Local partners, Press coverage, Downloads

**Event pages (per event):** All practical details a family needs to show up

### Out of Scope for MVP

- Photo galleries on chapter pages (deferred)
- Private volunteer back-of-events (attendance tracking — deferred)
- Automated photo tagging (deferred)
- Poster / flyer generation (deferred)
- Volunteer attendance dashboard (deferred)
- Structured chapter-start intake flow (static page for MVP, workflow deferred)
- Full sponsor obligation tracking (deferred)

---

## Structure

*How does it fit together? Information architecture and sitemap.*

### Navigation Model

Primary discovery is through **location/date-first search** (Events page + Home), not through chapter browsing. Chapters exist as a directory and individual pages, but are not the primary path for families trying to find a ride.

### Language Routing

Parallel URL paths: `/nl/`, `/fr/`, `/en/` — not a language switcher. Chapter pages render in the chapter's own language (FR chapters in FR, NL chapters in NL). National content is available in all three languages.

### Main Nav (5 items)

```
Events  |  Chapters  |  Getting Started  |  Help out  |  About
```

About expands to: Mission, Vision, Organisation, News, Press, Partners.

"Help out" / "Meehelpen" / "S'engager" ✅ Warmer than "Volunteer" — better fit for Tone of Voice.

### Sitemap

```
kidicalmass.be (NL / FR / EN — routed per path)
│
├── Home (/)
│   └── Upcoming events near me, chapter map, movement stats, news preview
│
├── Events (/events)
│   ├── Calendar — filter by location, date, iCal subscription
│   │   └── Toggle: upcoming (default) / past events
│   └── Event detail (/events/[slug])
│       └── Date, time, meeting point, distance, route, chapter info
│       └── "Grande Kidical Mass" = featured event, same system
│
├── Chapters (/chapters)
│   ├── Overview — map + list of all chapters (grouped by region), "start a chapter" CTA
│   │   └── Liège shown as regular pin → kidicalmassliege.org (external) ✅
│   │   └── Flanders group hidden until at least one Flemish chapter is active ✅
│   └── Chapter page (/chapters/[postal-code])
│       ├── Local schedule (pulls from Events — auto, never manual)
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
└── Admin (/admin — Filament panel, separate)
    ├── Coordination duo: full access across all chapters
    └── Chapter lead: own chapter only
```

### Key Structural Decisions

- **No chapter as primary nav path** — families use Events (calendar + location filter), not Chapters, to find a ride
- **Volunteer contact form lives on chapter pages** — the Help Out page explains roles and routes people to the right chapter
- **Chapter pages are self-published** — chapter leads manage their own content within design constraints. No approval flow needed.
- **Chapters overview doubles as growth story** — the map of all chapters supports new chapter leads and grant applications
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

### Open Questions for Migration

- **Redirect map:** Document old → new URL redirects before launch. Particularly important for Facebook links to `/agenda` and direct links to chapter postal code pages (`/1030`, `/1050`, etc.).
- **Build order for Nico:** Proposed: (1) Events + Event detail → (2) Chapters + Chapter pages → (3) Help out → (4) Getting Started → (5) About section → (6) Home. Validate with Nico.
- **Cutover plan:** When do chapter leads switch from Wix to Filament? Is there a parallel-running period?

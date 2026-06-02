---
title: Design — Structure (plane 3)
tags: [design]
sources: [wiki/ux-planning, notion]
phase: design
updated: 2026-06-02
---

# Design — Structure (plane 3)

*Information architecture: navigation, sitemap, content model. Plane 3. Constrained by [Scope](10-scope.md); constrains the [Skeleton](30-skeleton/00-page-registry.md).*

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
│   ├── Calendar — public rides (kidicalmass type) only; filter by location, date, iCal
│   │   └── Toggle: upcoming (default) / past events
│   │   └── Meetups/workshops do NOT appear here — they live on chapter pages (D-2)
│   └── Event detail (/events/[slug])
│       └── Date, time, meeting point, distance, route, chapter info
│       └── "Grande Kidical Mass" = featured event, same system
│
├── My activities (account-only)  ← added 2026-05-18
│   └── Rides/meetups I'm attending + my chapter back-office
│
├── Chapters (/chapters)
│   ├── Overview — map + list of all chapters (grouped by region), "start a chapter" CTA
│   │   └── Liège shown as regular pin → kidicalmassliege.org (external) ✅
│   │   └── Flanders group hidden until at least one Flemish chapter is active ✅
│   └── Chapter page (/chapters/[postal-code])
│       ├── Local schedule (public rides — pulls from Activities, auto, never manual)
│       ├── Meetings/workshops of this chapter — publicly visible
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
- **One Activity model; all types public to view** — rides and meetups/workshops alike (meetups public as a traction signal — D-2). Login gates attendance + back-office, not viewing. Meetups live in the same system as rides, not a separate product. **Public meetups surface on chapter pages only** — `/events` stays rides-only (J1); there is no national meetup-aggregation view (a logged-in volunteer's cross-group view is [My activities](30-skeleton/my-activities.md)).
- **Account ≠ paying member** — a logged-in group-volunteer account (which gates attendance + back-office) is distinct from a spacefunding donor (funding status). A person can be both; neither implies the other.
- **Mission and Vision are separate** — Mission = what Kidical Mass is + impact stats; Vision = policy demands + advocacy
- **News in About section** — consistent with current site; low volume doesn't justify top-level nav

### Content Model (entities)

The authoritative entity list. Status: ✅ on `main` · 🟡 partial · ❌ to build. Verify against the live schema before building (use the `database-schema` tool).

| Entity | Key fields | Relationships | Status | Notes |
|---|---|---|---|---|
| **Activity** | type (`kidicalmass`/`meeting`/`workshop`/`other`), title NL/FR, date, time, meeting point, distance, duration, age range, cost, komoot_url | belongs to ≥1 **Group**; has an organizer; has many **Attendances** | ✅/🟡 | Built by Nico. **All types public to view** (meetups public too — D-2); login gates attendance + back-office, not viewing. No view-gate field needed. |
| **Attendance** ("I'm coming") | — | pivot **volunteer (`group_user`/User)** ↔ **Activity** | ❌ | New (D-1). **Account-only, volunteers-only**, on **all** activity types. **Display = hosts/organisers attending, not all attendees** (Leticia); lead may see the full roster. |
| **Article** (News) | title NL/FR, body, author, published_at | optional **Group** (any lead can post) | ✅ | Already built. |
| **Group** (chapter) | name (bilingual official), postal code(s), language, map location | has many Activities, group_users, local partners | 🟡 | Drives `/chapters/[postal-code]`; fixed template. |
| **group_user** (volunteer account) | role | pivot User ↔ Group | ✅ | **The only kind of site account — accounts are volunteers only.** Gates attendance + back-office (meetup *viewing* is public). **≠ spacefunding member.** |
| **Spacefunding member** | recurring-donor status | external (Growfunding) — **no site account** | ❌ | Recurring support via the **Growfunding** platform (Spacefunding model); the site links out, doesn't store it. Not a shop. See [scope § Membership](10-scope.md). |
| **Volunteer enquiry** | name, email, chosen role, chosen chapter, message | routed to a **Group**'s lead | ❌ | Replaces the `bike@` inbox; per-chapter routing (J2). |
| **Partner / Sponsor** | name, logo, active flag, scope (national/local) | optional **Group** (local) | ❌ | Basic display only; full obligation tracking deferred. |
| **Press item** | outlet, title, url/pdf, date, language | national or **Group** (local, dual-homed) | ❌ | `/about/press` + optional chapter section. |
| **Download / material** | label, file, language | **Group** or national | ❌ | Distributed where needed (no central Downloads page). |

---

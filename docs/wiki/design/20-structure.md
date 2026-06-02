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

**Footer & utilities (not main nav):** national **Contact**, **Membership** CTA (persistent), **Privacy** + **Cookies** (GDPR-mandatory), and a discreet **volunteer login**. There is **no public Register** — volunteer accounts are invite/lead-provisioned (recruited via WhatsApp/phone per [persona P4](../strategy/20-personas.md)), so the site exposes a login link only.

### Sitemap

```
kidicalmass.be (NL / FR — routed per path; EN later)
│
══ PUBLIC ════════════════════════════════════════════════════════════
│
├── Home (/)
│   └── Upcoming events near me, chapter map, movement stats, news preview
│
├── Events (/events)
│   ├── Calendar — public rides (kidicalmass type) only; filter by location, date, iCal
│   │   ├── Toggle: upcoming (default) / past events
│   │   ├── Subscribe to rides near me — per-region email opt-in (low-frequency) ← Scope feature, now homed
│   │   └── Meetups/workshops do NOT appear here — they live on chapter pages (D-2)
│   └── Event detail (/events/[slug])
│       ├── Date, time, meeting point, distance, route, chapter info
│       ├── "I'm coming" + hosts-attending display — logged-in volunteers only (D-1)
│       └── "Grande Kidical Mass" = featured event, same system (D-3)
│
├── Chapters (/chapters)
│   ├── Overview — map + list of all chapters (grouped by region), "start a chapter" CTA
│   │   ├── Liège + Mons = hosted full chapters (both keep own domains; page links out) ← revised 2026-06-02
│   │   └── Flanders group hidden until at least one Flemish chapter is active ✅
│   └── Chapter page (/chapters/[postal-code])
│       ├── Intro / "what our rides are like" — short description (the lead paragraph every real chapter page opens with) ← template gap caught in cross-check
│       ├── Local schedule (public rides — pulls from Activities, auto, never manual)
│       ├── Meetings/workshops of this chapter — publicly visible (D-2)
│       ├── Subscribe to this chapter — per-chapter email opt-in ← Scope feature, now homed
│       ├── History / our story (optional — hidden if empty; Mons-style founders + milestones) ← template gap caught in cross-check
│       ├── Team (optional — hidden if no team added)
│       ├── Volunteer mini form (routed to local lead — J2)
│       ├── Local partners (optional — hidden if empty)
│       ├── Press coverage (optional — hidden if empty)
│       ├── Downloads (optional — hidden if empty)
│       └── Template is strictly uniform — no chapter colours or logos ✅
│
├── Getting Started (/getting-started)
│   └── Practical info for families new to cycling with kids
│       ├── What to expect at a ride
│       ├── Practical FAQ (age, gear, weather, registration)
│       ├── Don't have a bike? — DEFERRED, post-core (provider list to verify; not v1) ← 2026-06-02
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
│   └── One-off bank donation (BE72…) DROPPED for v1 — recurring-only; confirm with Leticia ← 2026-06-02
│
└── Footer / utilities (not main nav)
    ├── Contact (/contact) ← NEW: national front door → coordination duo
    │   └── Buckets: press · partnership/sponsor · general (per-chapter volunteer ≠ here → Help out)
    ├── Privacy (/privacy) · Cookies (/cookies) ← NEW: GDPR-mandatory (form + email capture)
    ├── 404 / error page
    └── Volunteer login (/login) — discreet; NO public Register (invite-only)
│
══ LOGGED-IN FRONTEND (volunteers only) ══════════════════════════════
│  account = group_user, invite/lead-provisioned; gates attendance + back-office
│
├── My activities (/my-activities)  ← post-login landing
│   ├── I'm attending — rides + meetups I marked "I'm coming", soonest first
│   ├── Meetups & workshops — default = my municipality, cross-group filter (public; here as shortlist)
│   └── My chapter(s) → link(s) into the back-office
│
└── Chapter back-office (/backstage/[postal-code] — per chapter)
    │  Separate BRANDED frontend surface, read-mostly — NOT the Filament panel
    ├── Before signing up: what to expect as a volunteer
    └── Once logged in (the "things now in WhatsApp"):
        ├── How it works · documents · intro video
        ├── Meetup/workshop schedule for this chapter
        ├── Who leads the chapter + their role
        ├── What roles exist + what yours is / could be
        └── Attendance management (lead sees full roster)
        └── ⚠ Content detail deferred to Alexandre/J3 interview (D-1)
│
══ ADMIN (separate — content CMS) ════════════════════════════════════
│
└── Admin (/admin — Filament panel; leads P5 + duo P6)
    ├── Coordination duo: full access across all chapters
    ├── Chapter lead: own chapter only (events, team, partners, press, downloads, news)
    └── Volunteer provisioning — leads add volunteers (invite model; no public Register)
```

### Key Structural Decisions

- **No chapter as primary nav path** — families use Events (calendar + location filter), not Chapters, to find a ride
- **Volunteer contact form lives on chapter pages** — the Help Out page explains roles and routes people to the right chapter
- **Chapter pages are self-published** — chapter leads manage their own content within design constraints. No approval flow needed.
- **Chapters overview supports grants + existing groups** — the map mainly serves grant applications and helps families find existing groups; recruiting new leads is secondary (reframed, interview 2026-05-18)
- **Liège + Mons are hosted full chapters, not external pins** (revised 2026-06-02) — both run their own domains (`kidicalmassliege.org`, `mons.bike`) but get first-class hosted `/chapters/[postal]` pages (page may link out to their domain). Reverses the earlier "Liège = external pin" call; aligns with [P5](../strategy/20-personas.md) ("make a hosted page more attractive than a separate domain"). Liège's page is authored from their site's data (no `kidicalmass.be` page exists to migrate)
- **Membership is site-wide** — dedicated `/membership` page + a persistent CTA in the global footer on every page (top org objective)
- **Site is canonical, not a Facebook replacement** — Facebook stays for reach and turnout signal; the site is the link Facebook points to
- **One Activity model; all types public to view** — rides and meetups/workshops alike (meetups public as a traction signal — D-2). Login gates attendance + back-office, not viewing. Meetups live in the same system as rides, not a separate product. **Public meetups surface on chapter pages only** — `/events` stays rides-only (J1); there is no national meetup-aggregation view (a logged-in volunteer's cross-group view is [My activities](30-skeleton/my-activities.md)).
- **Account ≠ paying member** — a logged-in group-volunteer account (which gates attendance + back-office) is distinct from a spacefunding donor (funding status). A person can be both; neither implies the other.
- **Mission and Vision are separate** — Mission = what Kidical Mass is + impact stats; Vision = policy demands + advocacy
- **News in About section** — consistent with current site; low volume doesn't justify top-level nav
- **Three tiers, three audiences** — (1) public site (families, would-be volunteers, sponsors, press); (2) logged-in *frontend* for volunteers (P4) — `My activities` + a **separate branded** per-chapter back-office, **read-mostly**; (3) Filament `/admin` — the content CMS for leads (P5) + duo (P6). The back-office is **not** the admin panel: rank-and-file volunteers never touch Filament
- **Accounts are invite-only** — leads provision volunteers in `/admin` (recruited via WhatsApp/phone — [P4](../strategy/20-personas.md)); the public site exposes a **login link only, no Register**. Post-login landing = `My activities`
- **National contact has its own front door** — `/contact` routes to the coordination duo with press / partnership / general buckets, serving the secondary audiences (sponsors, press) the chapter-routed Help out form does not. Per-chapter volunteer enquiries still go through Help out (J2)
- **Legal/utility pages are in scope** — Privacy + Cookies are GDPR-mandatory given the volunteer enquiry form and per-region email opt-in; plus a 404. They live in the footer cluster, not main nav
- **Per-region email subscription is homed** — the low-frequency "next ride near you" opt-in (Scope feature) lives as a control on **Events** (per-region) and on each **chapter page** (per-chapter); it is not a separate page

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
| **Contact message** (national) | name, email, topic (`press`/`partnership`/`general`), message | routed to the **coordination duo** (no Group) | ❌ | New — the `/contact` front door. Distinct from per-chapter Volunteer enquiry; serves sponsors/press (secondary audiences). |
| **Email subscription** | email, scope (region *or* **Group**), locale, confirmed_at | optional **Group** (per-chapter) | ❌ | New — low-frequency "next ride near you" opt-in (Scope). Control lives on Events + chapter pages, not a page. Double opt-in for GDPR. |
| **Partner / Sponsor** | name, logo, active flag, scope (national/local) | optional **Group** (local) | ❌ | Basic display only; full obligation tracking deferred. |
| **Press item** | outlet, title, url/pdf, date, language | national or **Group** (local, dual-homed) | ❌ | `/about/press` + optional chapter section. |
| **Download / material** | label, file, language | **Group** or national | ❌ | Distributed where needed (no central Downloads page). |

---

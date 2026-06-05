---
title: Design — Structure (plane 3)
tags: [design]
sources: [wiki/ux-planning, notion]
phase: design
updated: 2026-06-05
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

**Support CTA in the primary nav (elevated 2026-06-02; label + mobile resolved 2026-06-02 via critique #3/#4):** a distinct accent button **"♥ Steun ons"** (a **heart icon** disambiguates the *give* action from **Meehelpen**/volunteer) sits top-right of the header, in the slot previously held by the **login button** — which **moves to the footer** utilities. The 5 main-nav links are **unchanged**; it's a CTA button, not a 6th link. **Mobile (75% of traffic):** pinned as the **first, accent-styled item at the top of the hamburger menu**. Links to `/steun-ons`. See [org goals #1](../strategy/10-organisation-goals.md) and [PAT-10](40-patterns.md).

**Footer & utilities (not main nav):** national **Contact**, **Steun** CTA (persistent, → `/steun-ons`), a single **Privacy & cookies** page (GDPR-mandatory; `/cookies` 301s to `/privacy` — folded 2026-06-02), and a discreet **volunteer login** (now **footer-only** — removed from the header where Steun took its slot, 2026-06-02). There is **no public Register** — volunteer accounts are invite/lead-provisioned (recruited via WhatsApp/phone per [persona P4](../strategy/20-personas.md)), so the site exposes a login link only.

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
│       └── "Grande Kidical Mass" = featured event, same system (D-3)
│           (no "I'm coming" — per-event attendance cut, D-1; turnout via WhatsApp)
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
│       ├── Local volunteers — opt-in public roster (`group_user.is_public`; D-1)
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
├── Help out (/help-out) ✅  — orientation only; routes to the chapter (J2, 2026-06-02)
│   ├── Roles + how to volunteer (5 roles confirmed: pink vest, co-organiser,
│   │   communicator, photographer, DJ) ✅
│   ├── "Find your chapter →" /chapters — the routed contact form lives on the
│   │   chapter page (form moved off Help out; routing is by context)
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
├── Steun Kidical Mass (/steun-ons)  ← was /membership; reworked 2026-06-02
│   └── What spacefunding is · steun maandelijks (recurring) · t-shirt = token of support (not membership)
│   └── Reached from: "Steun" nav CTA (top-right, replaces login) + contextual blocks (Home, end of event-detail) + persistent footer CTA — all → /steun-ons
│   └── Discreet one-off path provisionally back in scope (reverses the v1 cut) — pending Leticia (D-9)
│
└── Footer / utilities (not main nav)
    ├── Contact (/contact) ← NEW: national front door → coordination duo
    │   └── Buckets: press · partnership/sponsor · general (per-chapter volunteer ≠ here → Help out)
    ├── Privacy & cookies (/privacy) ← NEW: GDPR-mandatory (form + email capture); one page — /cookies 301s here (folded 2026-06-02)
    ├── 404 / error page
    └── Volunteer login (/login) — discreet; NO public Register (invite-only); footer-only since Steun took the header slot (2026-06-02)
│
══ LOGGED-IN FRONTEND (volunteers only) ══════════════════════════════
│  account = group_user (many-to-many), invite/lead-provisioned; gates back-office + roster
│
├── My activities (/my-activities)  ← post-login landing
│   ├── Upcoming — rides + meetups for my chapter(s), soonest first (no "I'm coming" — attendance cut, D-1)
│   ├── Meetups & workshops — default = my municipality, cross-group filter (public; here as shortlist)
│   └── My chapter(s) → link(s) into the back-office + volunteer roster
│
└── Chapter back-office (/backstage/[postal-code] — per chapter)
    │  Separate BRANDED frontend surface, read-mostly — NOT the Filament panel
    ├── Before signing up: what to expect as a volunteer
    ├── Once logged in (the material library — "things now in WhatsApp"):
    │   ├── How it works · documents · intro video
    │   ├── Meetup/workshop schedule for this chapter
    │   ├── Who leads the chapter + their role
    │   ├── What roles exist + what yours is / could be · growth path
    │   ├── Promo/poster downloads (stored & distributed, not generated)
    │   └── Volunteer roster — who else is in this chapter (logged-in only; opt-in public on chapter page)
    └── New-chapter / organiser onboarding: how to start & run a chapter
        (commune contact, route planning, promo downloads) — primary beneficiary = new/small chapters (D-1, Alexandre/J3 2026-06-05)
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
- **Support is site-wide and prominent** (reworked 2026-06-02) — dedicated **`/steun-ons`** page (was `/membership`) + a **"Steun" CTA button in the primary nav** (top-right, replacing login, which moves to the footer) + **contextual "Steun" blocks** on Home and end of event-detail + the persistent footer CTA. All route to `/steun-ons`. Framed as **support, not membership** ("lid" retired; everyone rides free). Elevated from the earlier quiet-footer-only call (top org objective). One-off path provisional ([D-9](01-concerns.md))
- **Site is canonical, not a Facebook replacement** — Facebook stays for reach and turnout signal; the site is the link Facebook points to
- **One Activity model; all types public to view** — rides and meetups/workshops alike (meetups public as a traction signal — D-2). Login gates the back-office + volunteer roster, not viewing (per-event attendance cut — D-1). Meetups live in the same system as rides, not a separate product. **Public meetups surface on chapter pages only** — `/events` stays rides-only (J1); there is no national meetup-aggregation view (a logged-in volunteer's cross-group view is [My activities](30-skeleton/my-activities.md)).
- **Account ≠ paying member** — a logged-in group-volunteer account (which gates the back-office + volunteer roster) is distinct from a spacefunding donor (funding status). A person can be both; neither implies the other. A volunteer account is **many-to-many** with chapters (multi-chapter affordance — D-1).
- **Mission and Vision are separate** — Mission = what Kidical Mass is + impact stats; Vision = policy demands + advocacy
- **News in About section** — consistent with current site; low volume doesn't justify top-level nav
- **Three tiers, three audiences** — (1) public site (families, would-be volunteers, sponsors, press); (2) logged-in *frontend* for volunteers (P4) — `My activities` + a **separate branded** per-chapter back-office, **read-mostly**; (3) Filament `/admin` — the content CMS for leads (P5) + duo (P6). The back-office is **not** the admin panel: rank-and-file volunteers never touch Filament
- **Accounts are invite-only** — leads provision volunteers in `/admin` (recruited via WhatsApp/phone — [P4](../strategy/20-personas.md)); the public site exposes a **login link only, no Register**. Post-login landing = `My activities`
- **National contact has its own front door** — `/contact` routes to the coordination duo with press / partnership / general buckets, serving the secondary audiences (sponsors, press) the chapter-routed Help out form does not. Per-chapter volunteer enquiries still go through Help out (J2)
- **Legal/utility pages are in scope** — a single **Privacy & cookies** page is GDPR-mandatory given the volunteer enquiry form and per-region email opt-in; plus a 404. Privacy + cookies were **folded into one page** (2026-06-02); `/cookies` 301s to `/privacy` so any links indexed from the old Wix site keep resolving. It lives in the footer cluster, not main nav
- **Per-region email subscription is homed** — the low-frequency "next ride near you" opt-in (Scope feature) lives as a control on **Events** (per-region) and on each **chapter page** (per-chapter); it is not a separate page

### Content Model (entities)

The authoritative entity list. Status: ✅ on `main` · 🟡 partial · ❌ to build. Verify against the live schema before building (use the `database-schema` tool).

| Entity | Key fields | Relationships | Status | Notes |
|---|---|---|---|---|
| **Activity** | type (`kidicalmass`/`meeting`/`workshop`/`other`), title NL/FR, date, time, meeting point, distance, duration, age range, cost, komoot_url | belongs to ≥1 **Group**; has an organizer | ✅/🟡 | Built by Nico. **All types public to view** (meetups public too — D-2); login gates the back-office + volunteer roster, not viewing. No view-gate field needed. **No `Attendance` relation** (per-event attendance cut — D-1, Alexandre/J3 2026-06-05). |
| ~~**Attendance** ("I'm coming")~~ | — | — | ❌ **CUT** | **Removed — do not build** (per-event attendance cut, Alexandre/J3 2026-06-05). The social need is met by the standing **volunteer roster** (a visibility flag on `group_user`), not a volunteer↔activity relation. See [`D-1`](01-concerns.md). |
| **Article** (News) | title NL/FR, body, author, published_at | optional **Group** (any lead can post) | ✅ | Already built. |
| **Group** (chapter) | name (bilingual official), postal code(s), language, map location | has many Activities, group_users, local partners | 🟡 | Drives `/chapters/[postal-code]`; fixed template. |
| **group_user** (volunteer account) | role, **`is_public` (opt-in roster visibility)** | pivot User ↔ Group — **many-to-many** (a volunteer may belong to >1 chapter; multi-chapter affordance, D-1 Decision D) | ✅/🟡 | **The only kind of site account — accounts are volunteers only.** Gates the back-office + volunteer roster (meetup *viewing* is public). **≠ spacefunding member.** **New: `is_public` boolean** drives the standing volunteer roster — public on the chapter page when opted in; full roster logged-in-only (D-1 Decision C). |
| **Spacefunding member** *(internal; public verb = "steun")* | recurring-donor status | external (Growfunding) — **no site account** | ❌ | Recurring support via the **Growfunding** platform (Spacefunding model); the site links out, doesn't store it. **Public verb = "steun", not "lid"** (terminology reworked 2026-06-02). A **discreet one-off path** is provisionally back in scope ([D-9](01-concerns.md)). Not a shop. See [scope § Support](10-scope.md). |
| **Volunteer enquiry** | name, email, chosen role, chosen chapter, message | routed to a **Group**'s lead | ❌ | Replaces the `bike@` inbox; per-chapter routing (J2). |
| **Contact message** (national) | name, email, topic (`press`/`partnership`/`general`), message | routed to the **coordination duo** (no Group) | ❌ | New — the `/contact` front door. Distinct from per-chapter Volunteer enquiry; serves sponsors/press (secondary audiences). |
| **Email subscription** | email, scope (region *or* **Group**), locale, confirmed_at | optional **Group** (per-chapter) | ❌ | New — low-frequency "next ride near you" opt-in (Scope). Control lives on Events + chapter pages, not a page. Double opt-in for GDPR. |
| **Partner / Sponsor** | name, logo, active flag, scope (national/local) | optional **Group** (local) | ❌ | Basic display only; full obligation tracking deferred. |
| **Press item** | outlet, title, url/pdf, date, language | national or **Group** (local, dual-homed) | ❌ | `/about/press` + optional chapter section. |
| **Download / material** | label, file, language | **Group** or national | ❌ | Distributed where needed (no central Downloads page). |

---

Skeleton-level UX specs for each core page of the new [kidicalmass.be](http://kidicalmass.be/). Strategy, Scope, and Structure are done. Working sequence follows user journeys, building up from what Nico has already built.

---

## Reference

## UX · Site-level Plan

## Content Migration Plan

---

## Journey 1: Family finds a ride

✅ Built (Nico) + 📝 UX spec:

## UX · Event Detail

📝 Draft:

## Events Overview

📝 Draft:

## Home

## Journey 2: Curious visitor becomes volunteer

📝 Draft:

## Help out

📝 Draft:

## Chapter Page

## Journey 3: Someone discovers the movement

📝 Draft:

## Getting Started

📝 Draft:

## Chapters Overview

---

## About section

📝 Draft — UX spec + verbatim research complete:

## UX · About/Mission

## UX · About/Organisation

## UX · About/Vision

## UX · About/News

## UX · About/Press

## UX · About/Partners

## UX · About (overview)


---

# UX · Site-level Plan

*Foundation document — site-level strategy, scope, and structure. For the updated sitemap and all decisions made during UX planning, see the *[Content Migration Plan](https://www.notion.so/33dd3ecc475c81c6bb6edd4259ca5b6c)*.*
---
## Principles tuning — Kidical Mass Belgium
*Playbook defaults reviewed and adjusted for this project. Tuned principles take precedence over the defaults.*
**Kept as-is**
- **User needs lead; org goals follow** — the strategy is consistently family-first throughout.
- **Prioritise 2–3 audiences explicitly** — three named and ranked audiences.
- **Organise around user tasks, not org structure** — Events = location-first discovery, not org nav.
- **Mobile-first always** — confirmed for all page specs.
- **Template over approval** *(project-specific)* — strict design constraints (fixed templates, design system) replace Leticia's manual sign-off. The system guarantees quality, not a person.
- **Bilingual as structural** *(project-specific)* — NL/FR/EN are routed URL paths, not content stacks. Every content decision must be trilingually viable.
- **Local before national** *(project-specific)* — chapter pages are first-class citizens. The national site enables local discovery; it doesn't replace it.
**Tuned**
- **Maintainability gate** — the backstage is a Laravel/Filament platform built by Nico. The maintainability test = can a chapter lead do this without coordination duo involvement? If not, it is a scope risk.
- **Content lifecycle** — formal lifecycle documentation is replaced by a clear ownership model: coordination duo owns national content; chapter leads own chapter-level content; any chapter lead can publish news. Review cadence: not formally set for MVP.
**Suspended for this project**
- **North Star metric** — the movement is community-driven, not conversion-driven. "Families who show up to their first ride" is the closest proxy, but is not currently trackable via the site alone. Suspended as a formal KPI for MVP.
- **Free-text search** — suspended for MVP. Volume (~60 events/season, ~20 chapters) makes location filter sufficient.
---
## Strategy
*Why does this exist? For whom?*
### Kern
1. **Core job of the site:** time-sensitive, location-aware discovery — get a family from "I want to join" to "I know when and where the next ride is" with zero friction.
1. **Audience hierarchy:** families (primary) → volunteers and chapter leads (secondary, equally weighted) → sponsors/press (tertiary — get a surface, not a designed journey).
1. **The key tension resolved structurally:** Leticia wants quality without being the bottleneck. Strict templates and a design system replace approval flows. The system enforces quality.
1. **Language is routing, not localisation:** NL/FR/EN are parallel URL paths. Every content and design decision must work trilingually or not at all.
1. **Scale is a strategic asset:** the chapter map and stats serve a grant and partnership audience — but are never the primary story for families arriving from social media.
### Primary Audiences (public site)
**1. Families — first-timers and returnees**
- Arrive curious and already half-sold — they’ve seen others do it and assume they can too
- Primary need: quick answers — when is the next ride, where, can we come?
- Reassurance is not the main goal; informing is
- No significant participation barrier to communicate (inclusive by default; bike availability via Kidical Mouse is an edge case)
**2. Potential volunteers**
- Want to contribute but don’t yet know how or where they fit
- Need a clear path from curiosity to contact
**3. Potential chapter leads**
- Considering starting a chapter in a city that doesn’t have one yet
- The map of chapters and the growth story of the movement is for them
- Also serves grant and subsidy applications
### Secondary Audiences (public site)
- **Sponsors/partners** — get a dedicated section, not a deeply designed experience
- **Press** — not a primary audience; no specific design needed
### Existing chapter leads
Served through a logged-in/admin experience, not the public site.
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
### Strategy validation checklist
- ❌ **Digital mission statement (one sentence):** Not present. Required before Scope is fully validated. Working draft: *"*[kidicalmass.be](http://kidicalmass.be/)* is the front door to a growing Belgian movement — it gets families to their next ride and turns curiosity into participation."* Needs Leticia validation.
- ✅ **2–3 audiences named and ranked:** Families (1st), Volunteers + Chapter leads (2nd), Sponsors/Press (tertiary). Done.
- ⚠️ **North Star metric:** Suspended per Principles tuning. "Families who show up to their first ride" is the intent but not currently trackable via the site alone.
- ⚠️ **User needs in JTBD format:** Needs are described narratively — not structured as "When [situation], I want to [motivation], so I can [outcome]." Acceptable for this stage; JTBD format would sharpen copywriting decisions.
- ❌ **Value proposition (one sentence a user could repeat):** Not written. Working draft: *"Kidical Mass is a free monthly bike ride for families in your neighbourhood — all ages, all bikes, just show up."* Needs validation.
- ⚠️ **Assumptions flagged as hypotheses:** Some audience statements are stated as facts (e.g. "No significant participation barrier to communicate"). These are educated guesses, not research-validated. Acceptable at this stage.
- ⚠️ **Channel strategy:** Facebook is implied as the primary real-time notification channel for returning families — not explicitly documented. The site's role vs. Facebook's role should be stated once.
### Strategy open questions
- **Digital mission statement:** Validate the working draft above with Leticia.
- **Value proposition:** Confirm the one-sentence VP for use in hero copy and onboarding. The current site copy could be the basis.
- **Facebook vs. site role:** Is Facebook the channel for real-time notifications (cancellations, reminders) while the site serves as permanent reference + first-timer conversion? If yes, make this explicit — it affects how time-sensitive the site needs to feel.
---
## Scope
*What are we building? (MVP)*
### Kern
1. **MVP core:** Events database (calendar + detail pages + iCal) + Chapter pages (self-published, fixed template) + Volunteer path (routed contact form) + National pages (Home, About section, Getting Started).
1. **Backstage as constraint:** everything in scope must be maintainable by chapter leads without coordination duo involvement. Nico's Laravel/Filament system sets the technical boundary.
1. **Three genuine cuts:** photo galleries, volunteer attendance tracking, poster/flyer generation. These are removed — not merely deferred — because they require backstage investment that outweighs MVP value.
1. **Content ownership by default:** coordination duo → national content; chapter leads → chapter content; any lead → news. This replaces a formal content lifecycle process.
1. **Migration is Nico's:** database seeders for existing content. Key pages rewritten using the ToV guide. No content freeze needed before build.
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
- Covers both “I want to volunteer” and “I want to start a chapter”
- Chapter overview page includes a “start a chapter” CTA
- Full chapter-start intake process is static for MVP; structured workflow deferred
**News / Blog**
- Already built by Nico — included in MVP
**Trilingual routing**

---

# Content Migration Plan

# Content Migration Plan
This document maps every page on the current [kidicalmass.be](http://kidicalmass.be/) Wix site to its destination in the new Laravel structure. For each page: where does its content go, and what work is needed.
### Kern
1. **16 current pages map to 8 new destinations** — most content consolidates (merge, absorb) rather than translating 1:1. The new site is leaner than the old one.
1. **The migration kills two dependencies:** hand-typed Facebook-linked agenda → database-driven events; stacked FR/NL content → routed trilingual paths.
1. **Chapter pages surface from hiding** — 14+ hidden postal-code pages become first-class citizens in a single fixed template.
1. **4 genuinely new pages** with no current equivalent: Getting Started, Vision, Partners, Event detail pages.
1. **This document is the authoritative sitemap.** If a page spec and this document disagree on structure, this document wins. The Decisions Log below is the canonical record of all structural decisions.
---
## How to read this plan
**Actions explained:**
- **Rewrite** — content exists but needs to be rewritten (tone of voice, structure, bilingual separation)
- **Migrate** — content moves to a new location, light editing only
- **Merge** — content from multiple old pages combines into one new page
- **Absorb** — content doesn't get its own page; it gets distributed across other pages
- **Drop** — page is retired, content is not carried forward
- **Seed** — Nico builds database seeders from existing content (events, chapters)
---
## Updated Sitemap
```javascript
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
│   ├── Organisation (/about/organisation)
│   │   └── Governance, coordination duo, local group structure
│   ├── Vision (/about/vision)
│   │   └── Policy demands + Child Friendly City manifesto (merged)
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
### Key changes vs. previous version of this sitemap
- **Volunteer/Contribute renamed** — "Help out" confirmed. URL: `/help-out`. ✅
- **Liège on map** — shows as a regular pin linking to [kidicalmassliege.org](http://kidicalmassliege.org/) (external). No special marker. ✅
- **Flanders chapters group** — hidden on /chapters until at least one Flemish chapter is active. ✅
- **Chapter template is strictly uniform** — no chapter-level colours or logos. Municipality name is the differentiator. ✅
- **Chapter list** — names and links only, no per-chapter event preview. ✅
- **Home stats bar** — 2 stats: chapter count with growth context ("20 active chapters — started with 4 in 2021") + parades per season. Dynamic, not hardcoded. ✅
- **Off-season empty state** — friendly message only ("Season runs March–November"). No iCal or newsletter CTA. ✅
- **News** moves from main nav into About (low volume doesn't justify top-level nav)
- **Partners** moves from main nav into About (same reasoning)
- **Past events** stay in the events index behind a toggle — not a separate archive page
- **Chapter URLs** use postal codes as slugs (`/chapters/1030`). Multi-municipality chapters use their primary postal code.
- **No Flanders placeholder pages** — chapter pages only created when a chapter is active
---
## Main Nav (5 items)
```javascript
Events  |  Chapters  |  Getting Started  |  Help out  |  About
```
About expands to: Mission, Vision, Organisation, News, Press, Partners.
Note: "Help out" / "Meehelpen" / "S’engager" is confirmed. URL: `/help-out`. Warmer than the current site’s "Volunteer" / "Bénévole" — better fit for Tone of Voice. ✅
---
## Page-by-Page Migration Table
### Main Navigation Pages
### Pages Missing from Original Audit
### Annual Event Pages
### Hidden Chapter Pages
---
## What's New (no current equivalent)
---
### Structure validation checklist
- ✅ **Task-first IA:** Nav labels are Events, Chapters, Getting Started, Help out, About — all oriented around what users are trying to do.
- ✅ **Flexible entry points:** Every page works as a landing page. No forced linear navigation.
- ⚠️ **Information scent:** "Getting Started" is clear for families but check FR/NL translations ("Pour commencer" / "Om te starten") — do these pass the scent test for a Francophone or Dutchphone parent? "About" is generic but standard convention.
- ❌ **Redirect map not documented:** The migration changes most URLs. A redirect map (old Wix URL → new Laravel URL) should exist for SEO and to avoid breaking social media links. Add before launch.
- ❌ **Build order / dependency chain not documented:** The migration table documents work per page, but doesn't specify which pages must be built first (events system and chapter pages are the obvious blockers). Useful for Nico's sprint planning.
- ❌ **Content freeze / parallel running not documented:** Will the Wix site stay live during migration? When does the DNS switch? When do chapter leads stop updating Wix and start using Filament?
- ✅ **Decisions log is comprehensive:** 18 decisions documented with rationale. All reviewed and up to date.
### Open questions for migration
- **Redirect map:** Document old → new URL redirects before launch. High SEO and link-preservation value. Particularly important for Facebook links to `/agenda` and direct links to chapter postal code pages (`/1030`, `/1050`, etc.).
- **Build order for Nico:** Propose: (1) Events + Event detail → (2) Chapters + Chapter pages → (3) Help out → (4) Getting Started → (5) About section → (6) Home (depends on all other components). Validate with Nico.
- **Cutover plan:** When do chapter leads switch from Wix to Filament? Is there a parallel-running period? This affects content freshness on both sides.
---
## Key Structural Changes vs. Current Site
1. **Language routing replaces stacking** — every page goes from ~2x needed length to clean NL/FR/EN paths
1. **Events replace Agenda** — hand-typed calendar becomes database-driven with detail pages, killing the Facebook dependency
1. **Chapter pages surface from hiding** — 14+ hidden postal code pages become first-class citizens
1. **Getting Started fills the onboarding gap** — no current page answers "I'm curious, how do I actually do this?"
1. **Advocacy content consolidates** — two separate manifesto pages merge into one vision statement
1. **Contact becomes contextual** — no more 3 email addresses; forms route to the right person
1. **Downloads distribute** — materials live where they're needed
1. **Photo gallery dissolves** — images go everywhere instead of being siloed
1. **Grande Kidical normalises** — annual flagship uses the same Events system, just featured
1. **News and Partners move under About** — low volume doesn't need top-level nav real estate
1. **Press coverage is dual-homed** — national on `/about/press`, local on chapter pages (optional)
---
## Decisions Log

---

# UX · Event Detail

**Level:** Page · **Status:** ✅ Built by Nico · 📝 UX spec added
**Page URL:** `/events/[slug]`
---
## Strategy
*Why does this page exist? For whom?*
### Primary User
A family making one decision: **are we going?**
They arrive already curious — via a shared WhatsApp link, the homepage events list, or a Google search for “Kidical Mass [their city].” They are not skeptical. They need practical confirmation, not persuasion.
### Secondary Users
- **Potential volunteer** — curious about getting involved; the page is a soft entry point into contributing
- **Press** — occasionally needs a contact point; not a designed experience, but the organising team section serves them passively
### The Decision and What Follows
The page supports one decision. After “yes, we’re going,” the user needs to:
- Save the date (calendar export)
- Share it with a partner or friend (WhatsApp share link)
No transactional flow, no registration, no account needed to attend.
### What Makes This Page Feel Like Kidical Mass
Not a generic event listing. Four things that carry the character:
1. **The route is visible** — a map showing the ride moving through the neighbourhood communicates the street-reclaiming nature of the event before a word is read
1. **The local team is named** — people, not an organisation; neighbourhood energy, not institutional
1. **The volunteer ask is tied to the team** — “want to ride alongside them?” not a generic CTA
1. **Practical details are warm and concrete** — pace, distance, duration as reassurance through specificity, not policy language
### Tone
Warm, specific, sensory. Joyful without being frivolous. Local — named landmarks, named people, named streets. See Tone of Voice guide.
---
## Scope
*What does this page contain and do?*
### Content Fields
**Required**
- Chapter name + postal code
- Date + time
- Meeting point (named landmark + address)
- Route map (Komoot embed)
- Distance (km)
- Duration (max)
- Free admission / no registration
- Age: all ages
- Pace: at the rhythm of the youngest child
**Optional**
- Theme / event name (e.g. “Safety First”, “Spooky Edition”)
- Programme notes (music, animations, special activities)
- Campaign context (e.g. 2026 Safety First campaign)
**Community layer**
- Chapter name + link (with signal that this is a recurring monthly series)
- Local organising team (names)
- Soft volunteer CTA — tied directly to the team
**Partners**
- Local partners / co-organisers (logos or names)
**Actions**
- Add to calendar (iCal export)
- Share link (WhatsApp + general)
**Legal**
- Photo permission note (one line)
### What Is NOT on This Page
- Facebook link (removed — no longer needed)
- Registration or RSVP flow (deferred — possible future feature)
- “Who’s attending” / social proof of attendance (deferred)
- Private volunteer back-of-event (deferred to later phase)
---
## Structure
*What goes where, and in what order?*
### Title Convention
`Kidical Mass [postal code] — [date]`
e.g. “Kidical Mass 1000 — 24 mei”
Postal code appears naturally in the title (language-neutral, follows Facebook convention). Not used as an oversized typographic element.
### Page Sections (top to bottom)
**1. Hero — above the fold (split layout)**
Left: Chapter name, title (postal code + date), day + time, meeting point, actions (add to calendar, share)
Right: Route map — shows start point and full route. Large enough to read the neighbourhood.
Both panels are visible above the fold on desktop. On mobile, map stacks below the essential info.
**2. Practical strip**
Single scannable line: distance · duration · free · all ages · music · children accompanied by adult
**3. What to expect**
2–3 lines, sensory and warm. Pace, atmosphere, what happens. Not a policy statement.
Optional: campaign/theme context if the event has one.
**4. Chapter context**
“This ride is part of [Chapter name]’s monthly series →”
Links to the chapter page. Signals the recurring community without listing future dates.
**5. Organising team + volunteer ask**
Names of the local team. Directly below: soft CTA — “Want to ride alongside them as a pink vest? →”
**6. Local partners**
Logo strip or name list of local co-organisers and partners.
**7. Photo permission**
One quiet line. Legally necessary, not visually prominent.
### Key Structural Decisions
- **Actions live in the hero** — the decision is made at the top; calendar and share should be reachable without scrolling
- **Map is in the hero, not a separate section** — visual and informational at the same time
- **Volunteer ask follows the team** — the connection between seeing who organises it and wanting to join them must be immediate, not separated by content
- **Practical strip is one line** — scannability over prose; the Facebook examples confirm this pattern works
---
## Skeleton
*What goes where on screen?*
### Desktop
```javascript
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Kidical Mass 1000 — 24 mei          [ MAP ───────] │
│  Bruxelles-Ville / Brussel-Stad      [ route map  ] │
│                                      [           ] │
│  Zondag 24 mei · 15h00               [  start ●  ] │
│  Place du Trône                      [    ↓      ] │
│                                      [  route    ] │
│  [ + Agenda ]  [ Delen ]             [           ] │
│                                      [───────────] │
├──────────────────────────────────────────────────────┤
│  5–7 km  ·  max 1u  ·  Gratis  ·  Alle leeftijden  │
│  🎵 Muziek onderweg  ·  Kinderen begeleid door adult │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Wat te verwachten                                   │
│  We rijden op het tempo van de jongste. Muziek,      │
│  nieuwe vrienden, een andere kijk op je buurt.       │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Onderdeel van Kidical Mass Brussel-Stad →           │
│  Elke maand een nieuwe rit door de stad.             │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Georganiseerd door                                  │
│  [naam]  [naam]  [naam]  (lokale vrijwilligers)      │
│                                                      │
│  Wil je meerijden als roze hesje? →                  │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Partners                                            │
│  [logo]  [logo]  [logo]                              │
│                                                      │
├──────────────────────────────────────────────────────┤
│  Tijdens de rit worden foto's gemaakt. Door deel     │
│  te nemen ga je akkoord met publicatie.              │
├──────────────────────────────────────────────────────┤
│ FOOTER                                               │
└──────────────────────────────────────────────────────┘
```
### Mobile
```javascript
┌──────────────────────┐
│ NAV (hamburger)      │
├──────────────────────┤
│                      │
│  Kidical Mass 1000   │
│  24 mei              │
│  Bruxelles-Ville     │
│                      │
│  Zondag · 15h00      │
│  Place du Trône      │
│                      │
│  [ + Agenda ]        │
│  [ Delen ]           │
│                      │
├──────────────────────┤
│  [ MAP — route ]     │
│  [ start ● → route ] │
│  [ full width ]      │
├──────────────────────┤
│  5–7 km · max 1u     │
│  Gratis · Alle lft.  │
│  🎵 Muziek           │
├──────────────────────┤
│  Wat te verwachten   │
│  We rijden op het    │
│  tempo van de        │
│  jongste...          │
├──────────────────────┤
│  Onderdeel van       │
│  KM Brussel-Stad →   │
├──────────────────────┤
│  Georganiseerd door  │
│  [naam] [naam]       │
│                      │
│  Wil je meerijden    │
│  als roze hesje? →   │
├──────────────────────┤
│  Partners            │
│  [logo] [logo]       │
├──────────────────────┤
│  Foto's worden       │
│  gedeeld. Akkoord    │
│  door deel te nemen. │
├──────────────────────┤
│ FOOTER               │
└──────────────────────┘
```
### Annotations
- **Title:** chapter name + postal code + date — language-neutral, follows established convention
- **Map:** Komoot embed showing full route with start marker. Large on desktop (right half of hero). Full-width below the fold on mobile.
- **Actions in hero:** calendar export and share link sit with the essential info — available without scrolling

---

# Events Overview

**Level:** Page | **Status:** 📝 Draft
### Kern (cross-plane summary)
1. **One job: get a family to the right event detail page in under 10 seconds.** Location filter + date-grouped list. No distractions.
1. **Upcoming = date grouping** (multiple rides cluster under one Saturday). **Past = month grouping** (archive browsing, not planning). Two modes, one toggle.
1. **Cards are compact and text-driven** — date, time, municipality, meeting point. No images. These are practical meetups, not visual showcases.
1. **Filters are minimal by design:** location + upcoming/past. No free-text search — ~60 events/season doesn't warrant it.
1. **Featured badge for Grande Kidical Mass** — same system, just flagged. No separate annual pages.
---
## Strategy
*Why does this page exist, and for whom?*
The primary discovery page for families. Replaces the current hand-typed `/agenda` that links everything to Facebook. This page answers one question fast: "When's the next ride near me?"
**Primary users:**
- Families (first-timers): "When's the next ride near me?"
- Families (returning): "When's Schaerbeek's next one?"
**Secondary users:**
- Volunteers: "Which event am I volunteering at this Saturday?"
- Chapter leads: "Are our events showing correctly?"
**Page objective:** Get a family from "I want to join" to tapping into the right event detail page in under 10 seconds.
---
## Scope
*What does this page need to do and contain?*
**Must have:**
- Chronological event list with date grouping
- Location filter (by chapter/municipality)
- Upcoming/past toggle
- Event cards with enough info to pick a ride (title, date, time, municipality, meeting point)
- Each card links to the event detail page
**Should have:**
- "Today" / "Tomorrow" contextual labels
- Featured badge for Grande Kidical Mass
**Out of scope:**
- Map view (detail page has the map)
- RSVP / attendance count (deferred)
- Free-text search (volume doesn't warrant it)
- Event images on cards (events are practical meetups, not visual showcases)
---
## Structure
*How is the content organised on this page?*
Linear list page, no sub-navigation. Two modes determined by the toggle:
**Upcoming mode (default):** grouped by date, chronological. Multiple rides on the same Saturday cluster under one date header. The family decision is "which weekend?" then "which ride?"
**Past mode:** grouped by month, reverse chronological. Browsing an archive, not making a plan.
**Section flow:**
1. Page header
1. Filter bar (toggle + location)
1. Event list (grouped)
1. Empty state (if applicable)
**Key links out:**
- Event cards → /events/[slug]
- iCal → calendar subscription
---
## Skeleton
### Kern
- The Events overview is the primary discovery page for families — it answers "when's the next ride near me?" with minimal friction
- Upcoming events are grouped by date (not by month) because multiple rides happen on the same Saturday and the family decision is "which weekend, then which ride"
- Past events are accessible via a toggle but use month grouping since you're browsing an archive, not making a plan
- Filters are minimal: location (chapter/municipality) and a toggle for upcoming/past — no free-text search, the volume doesn't warrant it
- Each event card surfaces just enough to pick the right ride: date, time, title, municipality, meeting point — then taps through to the detail page that Nico has already built
---
## Page URL
`/events` (trilingual: `/nl/events`, `/fr/events`, `/en/events`)
---
## Users and intentions
---
## Page layout — top to bottom
### 1. Page header
- Page title: "Events" (localised)
- Subtitle: one line, e.g. "Find a ride near you" / "Vind een rit bij jou in de buurt" / "Trouve une balade près de chez toi"
- No hero image — the detail pages have those. This page is a scanner, not a storyteller.
### 2. Filter bar
- **Upcoming / Past toggle** — two tabs, upcoming is active by default. Determines which set of events loads and which grouping logic applies.
- **Location filter** — dropdown of chapters/municipalities. Shows all by default. When a chapter is selected, only that chapter's events show. Label: "All locations" / specific municipality name.
- Filters sit in a horizontal bar below the header, sticky on scroll (mobile: collapses to a compact bar).
- No month selector needed — upcoming events are few enough to scan; past events are grouped by month so you can scroll.
### 3. Event list — Upcoming mode (default)
Grouped by date, chronological, next date first.
**Date group header:**
- Day of week + full date, prominent (e.g. "Saturday 19 April" / "Zaterdag 19 april" / "Samedi 19 avril")
- If the date is today or tomorrow, show a contextual label: "Today" / "Tomorrow"
**Event card** (within each date group):
- Event title (e.g. "Kidical Mass Evere – Haren")
- Time (e.g. "15:00")
- Municipality/chapter name
- Meeting point address — one line, truncated if needed
- Featured badge for Grande Kidical Mass (star icon or "★ Featured" label)
- Entire card is tappable → links to event detail page (`/events/[slug]`)
**Card layout:** compact horizontal card (not a big image card — these aren't visual events, they're practical meetups). Icon or small colour accent from the chapter's identity if available, otherwise a default pin/bike icon.
**Multiple events on one date:** cards stack vertically under the date header. No collapsing — on a typical Saturday there are 2-4 rides, which fits without scrolling.
### 4. Event list — Past mode
Grouped by month, reverse chronological (most recent month first).
**Month group header:**
- Month + year (e.g. "March 2026" / "Maart 2026" / "Mars 2026")
**Event card:** same as upcoming, but visually muted (lower contrast, no featured badge). Still tappable to the detail page.
### 5. Empty states
**No upcoming events (off-season):**
"No upcoming rides right now. The season runs from March to November — check back soon!"
**No events for selected location:**
"No upcoming rides in [municipality]. Try 'All locations' to see rides nearby."
**No past events for selected location:**
"No past rides found for [municipality]."

---

# Home

**Level:** Page | **Status:** 📝 Draft
### Kern (cross-plane summary)
1. **The homepage converts curious visitors into event-goers.** Two CTAs in the hero serve two genuinely different audiences: ready families (→ /events) and curious first-timers (→ /getting-started).
1. **The events strip is the most functional element** — 3 live events, database-driven, same card as /events. Never hardcoded.
1. **The chapter map = proof of scale**, not navigation. The /chapters page handles directory needs. Here, the map says "this is a national movement."
1. **Stats are dynamic and current-season** (momentum signal: chapter count + parades). Deliberately different from Mission page's cumulative impact stats.
1. **Partners bar is institutional-only.** Operational partners (Loopz, Kidical Mouse) live on /getting-started and /about/partners — not here.
---
## Strategy
*Why does this page exist, and for whom?*
The front door of [kidicalmass.be](http://kidicalmass.be/). Most visitors land here — either directly or via social media. It needs to do two things fast: make the movement feel alive, and get families to their next ride.
**Primary users:**
- Families (first-timers): "What is this? Is the next one near me?"
- Families (returning): "When's the next ride?" (shortcut to Events)
**Secondary users:**
- Potential volunteers: "This looks fun, how do I help?"
- Potential chapter leads / grant reviewers: "How big is this movement?"
**Page objective:** Convert a curious visitor into someone who clicks through to an event or to Getting Started. Secondary: make the movement's scale visible (chapter map, stats).
---
## Scope
**Must have:**
- Upcoming events preview (next 3 rides, pulled from Events)
- Chapter map showing national reach
- Clear primary CTA for first-time families
- Movement stats (dynamic, not hardcoded)
- Link to Getting Started for newcomers
**Should have:**
- News preview (latest 2 articles)
- Partner/sponsor logo bar
- Subtle volunteer CTA (secondary path to /contribute)
**Out of scope:**
- Full calendar (that's /events)
- Full chapter directory (that's /chapters)
- Volunteer recruitment form (that's /contribute)
---
## Structure
Linear scroll, single-column. Story: what is this → next ride → how big is the movement → how to get involved.
**Section flow:**
1. Hero (identity + dual CTA)
1. Upcoming events strip (next 3 rides)
1. Chapter map (national reach at a glance)
1. Movement stats bar
1. Volunteer CTA strip (subtle)
1. News preview
1. Partners bar
**Key links out:**
- Hero primary CTA → /events
- Hero secondary CTA → /getting-started
- Event cards → /events/[slug]
- "See all rides" → /events
- Chapter map pins → /chapters/[postal-code]
- "See all chapters" → /chapters
- Volunteer CTA → /help-out
- News cards → /about/news/[slug]
- Partners → /about/partners
---
## Skeleton
### Kern
- The homepage carries a dual CTA in the hero: primary "Find a ride" (→ /events) and secondary "New here? Start here" (→ /getting-started). The two audiences — ready families and curious first-timers — have genuinely different needs; one CTA would leave one stranded. ✅ Decided.
- The upcoming events strip is the most functional element on the page: 3 live events pulled from the Events database, same card component as /events. Always database-driven — no hardcoded events.
- The chapter map on the homepage serves a different job than the one on /chapters: here it is proof of scale (national reach at a glance), not a discovery directory. It appears on both pages. Liège appears as a regular pin linking to [kidicalmassliege.org](http://kidicalmassliege.org/). ✅ Decided.
- Stats show chapter count with growth context + parades per season. ✅ Decided.
- News and partners are "should have" — both hidden entirely when empty. The homepage degrades gracefully.
- **Stats distinction** ✅: Homepage stats (dynamic, current season: active chapter count + parades this season) are deliberately different from the Mission page stats (cumulative impact: 150 parades, 5,500+ participants, 120 volunteers, 16+ communities — manually maintained). Homepage = momentum signal. Mission = total impact. The two sets must not contradict each other — homepage count must match the database figure, not the static Mission number.
- **Partners bar scope** ✅: The homepage partners bar shows institutional and movement-ally partners only (Bruxelles Mobilité, Clean Cities Campaign, Ville de Bruxelles, Commune de Schaerbeek). Operational/in-kind partners (Loopz, Kidical Mouse) do NOT appear here — they live on /about/partners and /getting-started.
---
## Page URL
`/` (trilingual: `/nl/`, `/fr/`, `/en/`)
---
## Users and intentions
---
## Page layout — top to bottom
### 1. Hero section
- Full-width. Background: joyful photo or short looping video — children on bikes in a city street, colourful, inclusive, clearly Belgian.
- **Headline:** Short, joyful, movement-first. Not stacked, routed per language. Examples: "Kids on bikes. Together." / "Ensemble sur nos vélos." / "Samen op de fiets."
- **Subline:** 1–2 sentences. Concrete and sensory. Example: "Every month, hundreds of children ride through Belgian streets — safely, together, with music. Free for everyone."
- **Primary CTA button:** "Find a ride" / "Trouver une balade" / "Vind een rit" → /events
- **Secondary CTA link:** "New here? Start here →" / "Première fois ? Par ici →" / "Eerste keer? Start hier →" → /getting-started
- No stacked FR/NL text — language determined by URL route.
### 2. Upcoming events strip
- Section label: "Upcoming rides" / "Prochaines balades" / "Volgende ritten"
- Next 3 events from the Events database — nationwide, chronological, soonest first.
- Same card component as Events Overview (date, time, municipality, meeting point — compact, no image).
- "Today" / "Tomorrow" contextual labels where applicable.
- "See all rides →" link → /events
- **Off-season empty state:** "No rides right now — the season runs from March to November." No sign-up CTA — just the message. ✅ Decided.
- Never shows hardcoded or stale content.
- **Decision:** No location filter on the homepage strip. National upcoming events only. Location filtering lives on /events.
### 3. Chapter map
- Section label: "Active across Belgium" / "Actif à travers la Belgique" / "Actief door heel België"
- One pin per active chapter — tappable/clickable → /chapters/[postal-code]
- Liège: regular pin linking to [kidicalmassliege.org](http://kidicalmassliege.org/) (external). ✅ Decided.
- Map style: simplified (outlined Belgium with coloured dots). Goal is scale impression, not navigation.
- "See all chapters →" link → /chapters
- **Decision:** Map appears on both homepage (scale signal) and /chapters (navigation directory).
### 4. Movement stats bar
- **2 stats decided:** ✅
- Design: large number + label + optional small context line below. Clean, minimal.
- No participant counts — not reliably tracked.

---

# Help out

**Level:** Page | **Status:** 📝 Draft
### Kern (cross-plane summary)
1. **Two questions in sequence:** "How do I help an existing chapter?" (roles + form) then "What if there's no chapter near me?" (start a chapter CTA).
1. **Roles are invitations, not job descriptions.** 5 roles confirmed: pink vest, co-organiser, communicator, photographer, DJ. People self-identify.
1. **The form routes to the nearest chapter lead** — not a central inbox. Municipality dropdown determines routing. No chapter selected → routes to [bike@kidicalmass.be](mailto:bike@kidicalmass.be).
1. **"Start a chapter" is static for MVP:** 2–3 sentences + email CTA. Structured intake workflow deferred.
1. **Page title confirmed: "Help out" / "Meehelpen" / "S'engager"** — warmer than "Volunteer", fits ToV.
---
## Strategy
The conversion page for people who want to help. Replaces the current email-only signup with a clear path from curiosity to contact.
**Primary users:**
- Potential volunteers: "I want to help but I don't know how or where"
- Parents who've attended rides and want to give back
**Secondary users:**
- People considering starting a chapter
**Page objective:** Get a curious volunteer to understand the roles and submit a form that routes to the right chapter lead. Secondary: surface the "start a chapter" path.
---
## Scope
**Must have:**
- Overview of volunteer roles (pink vest/escort, co-organiser, communicator, photographer, DJ) ✅ Roles confirmed unchanged
- Contact form routed to nearest chapter (not a centralised inbox)
- "Start a chapter" section (static for MVP)
**Should have:**
- What to expect as a volunteer
- Link to volunteer rules (Google Doc — keep external for MVP)
**Out of scope:**
- Structured onboarding workflow (deferred)
- Volunteer dashboard (deferred)
- Per-role signup (MVP routes everyone through one form)
---
## Structure
Single page, no sub-navigation. Answers: "How can I help?" then "What if there's no chapter near me?"
**Section flow:**
1. Page header — "Help out" / "Meehelpen" / "S'engager" ✅
1. Why volunteer — the pitch
1. Roles overview
1. Contact form — routed to nearest chapter
1. Start a chapter — CTA for new cities
**Key links out:**
- Form → chapter lead email
- "Start a chapter" → [mailto:bike@kidicalmass.be](mailto:bike@kidicalmass.be)
- Chapter pages → /chapters/[code]
- Volunteer rules → Google Doc (external)
---
## Skeleton
### Kern
- Page answers two questions in sequence: "How can I help with an existing chapter?" (roles + form) and "What if there's no chapter near me?" (start a chapter). Form is the primary action.
- Volunteer roles presented as invitations, not job descriptions — short, concrete, honest about time commitment. People self-identify their fit.
- The contact form routes to the nearest chapter lead based on selected municipality — not a central inbox. Requires a chapter → lead email mapping in the admin panel.
- Form lives inline on this page. One flow, no redirection to a chapter page.
- "Start a chapter" is static for MVP: short what-it-takes overview + email CTA. ✅ Decided.
---
## Page URL
`/help-out` · `/nl/help-out` · `/fr/help-out` · `/en/help-out` ✅
*(Redirect from /contribute if that URL was ever public)*
---
## Page title
**"Help out" / "Meehelpen" / "S'engager"** ✅ Confirmed. Warm and community-first, not transactional. Better fit for the Tone of Voice than the current site’s "Volunteer" / "Bénévole".
---
## Users and intentions
---
## Page layout — top to bottom
### 1. Page header
- Title: "Help out" / "Meehelpen" / "S'engager" ✅ Confirmed
- Warm subtitle: 1 line. Example: "Join hundreds of people who make every ride happen."
- No hero image — action-oriented page, warmth from copy.
### 2. Why volunteer — the pitch
- 3–4 sentences, movement-first, community-first. Not a list.
- ToV register: enthusiastic, honest about what's involved, evoking pride and community.
- Example direction: "Being a Kidical Mass volunteer means showing up for your neighbourhood. You'll be part of a team of parents, cyclists, and community builders who make every ride safe, joyful, and real. It takes a few hours on a Sunday and gives back a lot more."
- Link: "Read the volunteer guidelines →" → Google Doc (external, opens new tab, for MVP)
### 3. Roles overview
- Section label: "How you can help" / "Comment vous pouvez aider" / "Hoe je kunt helpen"
- 5 role cards in a 2-column grid. ✅ Roles confirmed: same 5 as current site.
- Cards are orientation aids, not form fields. The form below captures role preference as a checkbox.
### 4. Contact form
- Section label: "I want to get involved" / "Je veux m'impliquer" / "Ik wil meedoen"
- Intro: "Fill in the form and your nearest chapter lead will be in touch."
- **Fields:**
- **Submit CTA:** "I'm in →" / "Je veux participer →" / "Ik doe mee →"
- **On submit:** Confirmation message on page. Email to chapter lead for selected municipality.
- **Routing:** Chapter selection → chapter lead email. If no chapter: routes to [bike@kidicalmass.be](mailto:bike@kidicalmass.be).
### 5. Start a chapter
- Distinct background section — visually separated from the form.
- Label: "Don't see your city?" / "Votre ville n'est pas encore là ?" / "Staat jouw stad er nog niet bij?"
- **Short what-it-takes overview:** ✅ Decided. 2–3 sentences on what's needed: "Starting a chapter takes a core team of 2–3 people, a meeting point, and a route idea. We handle the brand, the training, and the national visibility. If you're curious, reach out."
- CTA: "Email the coordination team to talk about it →" → [mailto:bike@kidicalmass.be](mailto:bike@kidicalmass.be)
- Secondary: "See which cities already have a chapter →" → /chapters
---
## What this page does NOT include
- Volunteer dashboard or shift scheduling (deferred)
- Per-role separate signup flows (one form for MVP)
- Structured onboarding checklist (deferred)
- Inline volunteer rules content (Google Doc stays external for MVP)
- A chapter map or locator (use /chapters for that)
---
## Interactions

---

# Chapter Page

**Level:** Page (template) | **Status:** 📝 Draft — all questions resolved ✅
### Kern (cross-plane summary)
1. **One fixed template, every chapter.** Municipality name is the only differentiator — no chapter colours, logos, or custom layouts. Brand consistency guaranteed by the system.
1. **Events are auto-populated** from the database. Chapter leads never manually enter events here. One source of truth.
1. **Team + volunteer form are one section** (merged per skeleton principles) — "who runs this" flows directly into "want to join them?"
1. **Sections with no content are hidden entirely** — no empty placeholder states. A new chapter starts with just a header and events.
1. **Self-published by chapter leads** within design constraints. No approval flow needed — the template IS the quality gate.
---
## Strategy
The local home page for each chapter. In the new site, chapter pages are first-class citizens — self-published by chapter leads within a fixed template.
**Primary users:**
- Families: "When's the next ride in my neighbourhood?"
- Local volunteers: "Who's organising, how do I reach them?"
**Secondary users:**
- Chapter leads: managing their own page
- Coordination duo: network overview
**Page objective:** Give each chapter a local home that answers practical questions (when, where, who) and makes the local community feel real. Enable chapter leads to self-manage without going through Brussels.
**Key tension:** Brand consistency vs. local ownership. ✅ Strictly uniform template — no custom colours or logos per chapter.
---
## Scope
**Must have:**
- Local event schedule (auto-populated from Events database)
- Team section (optional — hidden if no team added)
- Volunteer contact form (routed to this chapter's lead)
- Local partners (optional)
**Should have:**
- Press coverage (optional — hidden if empty, auto-aggregates to /about/press)
- Downloads
**Out of scope:**
- Photo gallery (deferred)
- Chapter-level news/blog (national for MVP)
- Customisable layout or colour scheme ✅ Strictly uniform
---
## Structure
Fixed template, single page. Every chapter follows the same section order. Sections with no content are hidden.
**Section flow:**
1. Chapter header
1. Upcoming events (auto-populated)
1. Team + volunteer form (optional — hidden when no team members added)
1. Local partners (optional)
1. Press coverage (optional)
1. Downloads (optional)
*Sections 3 + 4 in the original draft have been merged — see skeleton note.*
---
## Skeleton
### Kern
- Every chapter page uses the same fixed template. ✅ No custom colour or logo per chapter — municipality name is the only differentiator.
- Events section is always auto-populated from the Events database — chapter leads never manually enter events here. One source of truth.
- Sections with no content are hidden entirely — no empty placeholder states.
- The volunteer form routes to the chapter lead whose email is already stored in the admin panel. ✅
- Multi-municipality chapters use hyphenated URL slugs (e.g. `/chapters/1150-1200`). ✅
- Brussels chapter pages are always bilingual (NL + FR with a language toggle). ✅ Wallonia → FR default, Flanders → NL default.
**⚠️ Skeleton principle: merge Team + Volunteer CTA (sections 3 + 4)**
Both sections answer the same user question: "Who's organising this and how do I get involved?" Seeing the team names and immediately being invited to join is the whole point — separating them breaks the connection. The Event Detail page already does this right: team names + volunteer ask live in one "van en voor de buurt" section. Apply the same pattern here: combine Team and the Volunteer CTA + mini form into a single section. Label TBD (e.g. "Who's behind this ride" / "Van en voor de buurt" / "Ceux qui organisent"). The Structure flow should reflect this: sections 3 + 4 become one.
---
## Page URL
`/chapters/[postal-code]` for single-municipality chapters (e.g. `/chapters/1030`)
`/chapters/[code1-code2]` for multi-municipality chapters (e.g. `/chapters/1150-1200`) ✅
Trilingual: `/nl/chapters/1030`, `/fr/chapters/1030`, `/en/chapters/1030`
---
## Users and intentions
---
## Page layout — top to bottom
### 1. Chapter header
- **Municipality name(s):** Large, prominent. Single: "Schaerbeek". Multi: "Woluwe-Saint-Pierre & Woluwe-Saint-Lambert".
- **Postal code(s):** Below the name, smaller. e.g. "1030" or "1150–1200"
- **Template is strictly uniform.** ✅ No chapter colours or logos. Same visual design for every chapter page.
- **Language:** Wallonia chapters → FR default. Flanders → NL default. Brussels → always bilingual with NL/FR toggle visible. ✅
- **Breadcrumb:** "← All chapters" → /chapters
### 2. Upcoming events (auto-populated)
- Events filtered to THIS chapter from the Events database.
- Same card component as /events: date, time, meeting point, title — tappable → /events/[slug]
- "Today" / "Tomorrow" labels where applicable.
- **No upcoming events:** "No upcoming rides for [municipality] right now. Check [/events] for rides across Belgium."
- **Past events:** Link only → /events?location=[postal-code]&view=past (or /events?location=[code1-code2] for multi-municipality)
- Always shown — may show empty state, never hidden.
### 3. Team section
- Name + role label + optional photo per team member.
- No bios. Community visibility, not a CV.
- **Hidden entirely when no team members added in admin.**
### 4. Volunteer CTA + mini form
- Label: "Want to help in [municipality]?" (municipality name inserted dynamically)
- 2-sentence pitch — warm, low-pressure.
- **Mini form:** Name (required), Email (required), Message (optional)
- Submit → routes to this chapter's lead email (already stored in admin panel ✅). Confirmation message on page.
- Below form: "More about volunteering →" → /contribute
### 5. Local partners (optional — hidden when empty)
- Logo + name per partner. Optional external link.
- Populated by chapter lead in admin.
- **Hidden entirely when empty.**
### 6. Press coverage (optional — hidden when empty)
- Publication + headline + date + external link per item.
- Populated by chapter lead in admin.
- **Automatic aggregation to /about/press** ✅ — when a chapter lead adds a press item here, it automatically surfaces on the national press page as well. No manual step for the coordination duo.
- **Hidden on chapter page when empty.** Still aggregates automatically when items are added.
### 7. Downloads (optional — hidden when empty)
- File name + format label + download button.
- Chapter-specific flyers and posters uploaded via admin.
- **Hidden entirely when empty.**

---

# Getting Started

**Level:** Page | **Status:** 📝 Draft
### Kern (cross-plane summary)
1. **This page removes the last friction before a first ride.** It reassures — it doesn't sell. The audience is already interested; they just have practical questions.
1. **"Don't have a bike?" is a dedicated section, not a footnote.** The movement is explicitly inclusive; removing the bike barrier is part of the brand promise. Loopz ✅, Fietsbieb, Kidical Mouse ✅ are all confirmed.
1. **The page ends with a single CTA to /events.** Getting Started prepares someone to act; it doesn't become a destination itself.
1. **No equivalent on the current site.** This fills the onboarding gap between "I've heard of this" and "I know when and where to show up."
1. **Content overlaps with Event Detail deliberately.** Event Detail has ride-specific "what to expect"; Getting Started has the genre-level version. Both are standalone.
---
## Strategy
A new page with no equivalent on the current site. Fills the onboarding gap for families who are curious but haven't attended a ride yet.
**Primary users:**
- First-timer families: "This sounds fun — what do I actually need to do/bring/know?"
- Parents unsure about age or bike requirements
**Secondary users:**
- Families without bikes: "We don't own bikes, can we still come?"
**Page objective:** Remove the last uncertainty stopping a curious family from showing up. Not a hard sell — they're already interested. Just practical reassurance.
---
## Scope
**Must have:**
- What to expect at a ride (pace, duration, vibe, safety)
- Practical FAQ (age, gear, weather, registration)
- Don't have a bike? (Loopz ✅ active with promo code, Fietsbieb, Kidical Mouse ✅ still operational)
**Should have:**
- Other bike activities for kids in Belgium
**Out of scope:**
- Event calendar (that's /events)
- Volunteer info (that's /contribute)
---
## Structure
Single page. Story arc: "Here's what happens" → "Common worries answered" → "No bike? No problem" → "Other ways to ride" → "Ready? Find your first ride."
**Section flow:**
1. Page header
1. What to expect at a ride
1. Practical FAQ
1. Don't have a bike?
1. Other bike activities (should have)
1. CTA to /events
**Key links out:**
- CTA → /events
- Loopz / partner links (external)
- Fietsbieb, Kidical Mouse (external)
- "Find your local chapter →" → /chapters
---
## Skeleton
### Kern
- This page removes the last friction standing between a curious family and their first ride — it reassures, it doesn't sell. Tone: warm and concrete (ToV guide: curiosity + belonging → excitement + confidence).
- "What to expect" and the FAQ borrow from the event detail page's cards but live here as a general overview — the event detail gives ride-specific info; Getting Started gives the genre-level experience.
- "Don't have a bike?" is a dedicated, prominent section — not a footnote. The movement is explicitly inclusive; removing this barrier is part of the brand promise. Loopz partnership confirmed active (promo code KIDICALMASS still valid). Kidical Mouse confirmed operational. ✅
- FAQ format groups first-timer anxieties into quick answers — short, scannable, no long paragraphs.
- The page ends with a single CTA to /events — Getting Started prepares someone to act, it doesn't become a destination itself.
---
## Page URL
`/getting-started` (trilingual: `/nl/getting-started`, `/fr/getting-started`, `/en/getting-started`)
---
## Users and intentions
---
## Page layout — top to bottom
### 1. Page header
- Title: "Getting Started" / "Pour commencer" / "Om te starten"
- Subtitle: warm, 1 line. Example: "Everything you need to know before your first ride."
- No hero image — warmth from copy, not photography.
- **⚠️ ToV note:** "Everything you need to know..." reads as feature-writing — comprehensive, not warm. The ToV guide calls for language that feels like a neighbourhood friend, not a brochure. Rewrite toward something concrete and specific. Working examples: "Come as you are. Here's what to expect." / "Alles wat je moet weten voor je eerste rit." / "Tout ce qu'il faut savoir avant votre première balade." The NL/FR versions should have the same lightness, not feel like a translation.
### 2. What to expect at a ride
- Section label: "What to expect" / "À quoi s'attendre" / "Wat kun je verwachten"
- 5–6 concrete factual statements — icon-supported list or simple visual grid (not a wall of text).
- Tone: concrete and matter-of-fact. The facts ARE the reassurance.
### 3. Practical FAQ
- Section label: "Common questions" / "Questions fréquentes" / "Veelgestelde vragen"
- Simple Q&A list — bold question label, 1–3 sentence answer below. No accordion needed at this length.
### 4. Don't have a bike?
- Section label: "Don't have a bike?" / "Vous n'avez pas de vélo ?" / "Geen fiets?"
- Intro line: "Not having a bike is not a reason to miss out."
- Note below the options: "Looking for resources in your area? Check your chapter page — local options vary by municipality." → link to /chapters
- Design: 2-column card layout or a clean table. Each option visually distinct.
- Note: this content is Brussels-centric (Fietsbieb covers 10 Brussels communes; Loopz is Brussels-based). The national page covers what's broadly available; local options live on chapter pages.
### 5. Other bike activities for kids (should have)
- Section label: "Other ways to cycle with your kids" / "D'autres façons de rouler avec vos enfants" / "Andere fietsactiviteiten voor kinderen"
- Short intro: "Kidical Mass isn't the only way to enjoy cycling with your kids in Belgium."
- List of other organised activities (sourced from current /activités-vélo-fietsactiviteiten-kids — content to be reviewed before writing)
- Each: name + 1-sentence description + external link
- If content is thin (fewer than 3 substantive activities), this becomes a short paragraph with inline links rather than a structured section.
### 6. CTA to /events
- Full-width section at the bottom, visually distinct.
- Headline: "Ready for your first ride?" / "Prêts pour votre première balade ?" / "Klaar voor je eerste rit?"
- CTA button: "Find a ride near you →" → /events
- Secondary link: "Find your local chapter →" → /chapters
- Tone: warm, not urgent. One conversion goal, clearly signposted.
---
## What this page does NOT include
- Event calendar (that's /events)
- Volunteer information (that's /contribute)
- Hard sell or urgency language (ToV: no urgency for its own sake)
- Per-chapter local resources (those live on chapter pages)
- Registration or sign-up (there is no sign-up for rides)
---
## Interactions

---

# Chapters Overview

**Level:** Page | **Status:** 📝 Draft — all questions resolved ✅
### Kern (cross-plane summary)
1. **One view serves two audiences:** families finding their local chapter, and stakeholders assessing the movement's scale. The map delivers both.
1. **The list is first-class alongside the map** — not a fallback. Accessibility and slow connections are real.
1. **Regional grouping: Brussels → Wallonia → Flanders** (order of establishment). Flanders group hidden until at least one chapter exists.
1. **Names and links only** — no per-chapter event previews. People click through to the chapter page.
1. **"Start a chapter" CTA at the bottom** turns gaps in the map into an invitation.
---
## Strategy
The national directory of all chapters. Replaces the hidden `/all-groups` page. Serves families looking for their local group and stakeholders assessing the movement's reach.
**Primary users:**
- Families: "Is there a chapter in my municipality?"
- Potential chapter leads: "Which cities don't have a chapter yet?"
**Secondary users:**
- Grant reviewers / partners: "How big is this movement?"
- Coordination duo: network overview
**Page objective:** Make every active chapter discoverable. Make the movement's national reach visible. Invite new chapter creation.
---
## Scope
**Must have:**
- Map of Belgium showing all active chapters (pins tappable → chapter pages)
- List of all chapters (clickable → chapter page)
- "Start a chapter" CTA
**Should have:**
- Chapter count as a headline stat
- Regional grouping (Brussels, Wallonia, Flanders) in the list
**Out of scope:**
- Chapter management (that's admin)
- Inactive or placeholder chapters
- Per-chapter event preview in the list ✅ Names and links only
---
## Structure
Two-part page: map + list. No sub-navigation.
**Section flow:**
1. Page header — "Chapters" + dynamic stat subtitle
1. Map — Belgium map with chapter pins
1. Chapter list — grouped by region
1. Start a chapter CTA
**Key links out:**
- Map pins / list entries → /chapters/[postal-code] or /chapters/[code1-code2]
- Liège pin → [kidicalmassliege.org](http://kidicalmassliege.org/) ✅
- "Start a chapter" → /help-out#start-a-chapter
---
## Skeleton
### Kern
- The page serves two audiences with one view: families finding their local chapter, and anyone assessing the movement's scale. The map delivers both — every active chapter is a pin, and the visual density across Belgium says more than any paragraph.
- The list is first-class alongside the map — not a fallback. Handles accessibility and slow connections.
- Chapters grouped by region (Brussels / Wallonia / Flanders) in the list — reflects how the movement is geographically structured.
- The "Flanders" region group is hidden until at least one Flemish chapter exists. ✅
- The chapter list shows names and links only — no per-chapter event preview. ✅
---
## Page URL
`/chapters` (trilingual: `/nl/chapters`, `/fr/chapters`, `/en/chapters`)
---
## Users and intentions
---
## Page layout — top to bottom
### 1. Page header
- Title: "Chapters" / "Groupes" / "Groepen"
- Subtitle: "X active groups across Belgium" — dynamic from database.
### 2. Chapter map
- Full-width Belgium map. One pin per active chapter. **Leaflet/Mapbox.** ✅
- **Liège:** regular pin — clicking/tapping opens [kidicalmassliege.org](http://kidicalmassliege.org/) (external). ✅
- Pin tooltip on hover (desktop) / tap (mobile): municipality name before navigating.
- **Brussels clustering:** cluster markers. ✅ Brussels has 12+ chapters — they appear as a cluster pin at national zoom that expands on tap to reveal individual chapter pins. Prevents the national map from being dominated by overlapping Brussels pins. Same Leaflet/Mapbox cluster plugin used for the national map; no separate Brussels inset needed.
- Pin size: minimum 44×44px touch target.
- Map style: clean, not satellite. Height: ~400–500px desktop, ~300px mobile.
### 3. Chapter list (grouped by region)
- Three region groups: **Brussels → Wallonia → Flanders** (order of establishment)
- Each entry: municipality name + postal code(s) → links to /chapters/[code] or /chapters/[code1-code2] for multi-municipality chapters. Example: "Woluwe-Saint-Pierre & Saint-Lambert (1150–1200)" → /chapters/1150-1200 ✅
- **No event preview** — names and links only. ✅
- **Flanders group:** Hidden until at least one Flemish chapter is active. ✅
- Design: clean text list. The map does the visual work.
- SEO value: complete sitemap of all chapter page URLs.
### 4. Start a chapter CTA
- Full-width section, distinct background.
- Label: "Don't see your city?" / "Votre ville n'est pas encore là ?" / "Staat jouw stad er nog niet bij?"
- Short paragraph: "New chapters keep joining. If your city isn't on the map yet, you could be the one to start it. We'll support you."
- CTA button: "Find out how →" → /help-out#start-a-chapter
- Secondary: "Questions? Email the coordination team →" → [mailto:bike@kidicalmass.be](mailto:bike@kidicalmass.be)
---
## What this page does NOT include
- Chapter management or admin
- Inactive / placeholder chapters
- Per-chapter event previews in the list
- A search field (chapter count is scannable at current scale)
- National statistics beyond the chapter count (those are on the homepage stats bar)
---
## Interactions
---
## Responsive behaviour

---

# UX · About/Organisation

## Kern
- Explains **how Kidical Mass is structured** — federated, volunteer-driven, with a light coordination layer
- Makes the **open architecture** of the movement legible to outsiders and potential joiners
- Shows the rhythm of collective governance (regional meetups, coordination duo)
- Helps potential co-organisers understand **how they’d fit in**
- Builds trust with partners and funders by demonstrating organisational coherence without bureaucracy
- Distinguishes the federal/network level from the local chapter level
## Page URL
`/about/organisation` · `/over/organisatie` · `/a-propos/organisation`
*(New URL — no direct redirect needed from current site)*
## Users and Intentions
## Page Layout — Skeleton
### 1. Page header
- H1: “Organisation” (localised: Organisatie / Organisation)
- Subtitle: one sentence — e.g. *“Kidical Mass is a federated volunteer network: locally rooted, collectively coordinated.”*
- No hero image. Reading page.
### 2. Organigram / visual structure
- A simple visual diagram showing the three levels:
- On the new site: render as an SVG or a simple CSS diagram (not a complex org chart tool)
- A text-based fallback version should exist for accessibility
### 3. Three levels explained
Each level = short heading + 2–3 sentence paragraph.
**Level 1 — Local groups**
Each local group organises its own Kidical Mass parades. They recruit their own volunteers, plan their own routes, and communicate with their local community. Groups are fully autonomous but share the Kidical Mass name, protocols, and values.
**Level 2 — Regional coordination meetups**
4 times a year, organisers from different groups come together. These meetups are used to share learnings, coordinate joint actions, align on communication, and support each other. Attendance is voluntary but encouraged.
**Level 3 — Coordination duo**
A pair of volunteers takes on a coordination role for the network: onboarding new chapters, managing the shared platform, liaising with partners, and ensuring the network stays coherent. This is a volunteer role, not a paid staff position.
### 4. Safety and route protocols
Short block: Kidical Mass has shared safety protocols and route guidelines used by all chapters. These are provided as part of the onboarding support for new groups. Link to Getting Started.
### 5. Working groups (if applicable)
Optional section: mention if there are thematic working groups (e.g. communication, safety, press). Only include if confirmed active.
### 6. CTA block
- Heading: “Want to start or join a chapter?”
- CTA button: → /getting-started
- Secondary link: → /help-out
## Interactions
## Responsive Behaviour
## What's NOT Included
- Named individuals (too volatile; no person names or photos)
- Financial structure or annual reports
- Legal entity details (unless explicitly needed for partner trust)
- Chapter-specific information → `/chapters`
- Volunteer roles → `/help-out`
- Policy positions → `/about/vision`
## Decisions
1. **Coordination duo naming**: Name the actual people in this role — names + short bios on the page. ✅
1. **Organigram asset**: Reuse the existing SVG as-is for launch. Can be redesigned to fit the design system later. Flag to Nico. ✅
## Open Questions
1. **Brussels vs. national scope** ⚠️ The current site’s local groups section uses a .brussels domain. The org structure content may be Brussels-centric. Verify with Leticia: does the coordination duo cover all Belgian regions, or just Brussels? Does the org structure description apply to Walloon and Flemish groups equally?
1. **Working groups**: Are there currently active thematic working groups (e.g. communication, safety)? Should these be listed on the page?
1. **Legal status**: Is Kidical Mass BE incorporated as an ASBL/VZW or operating informally? Should this be mentioned on the page?
1. **Regional meetups**: Are the 4×/year regional meetups still accurate? Do all Belgian chapters attend, or is it region-specific?
---
## Research / Content Source
*Verbatim content scraped from the live site at *[kidicalmass.be](http://kidicalmass.be/)* — for use when building this page.*
### Source: /organisation
**Page title (FR):** Organisation
**Page title (NL):** Organisatie
**Intro block (FR):**
> Kidical Mass Belgique est un réseau fédéré de groupes locaux. Chaque groupe est autonome et organise ses propres balades, tout en faisant partie d’un mouvement commun.
**Intro block (NL):**
> Kidical Mass België is een gefedereerd netwerk van lokale groepen. Elke groep is autonoom en organiseert zijn eigen tochten, maar maakt deel uit van een gemeenschappelijke beweging.
---
**Governance section (FR):**
> **Rencontres régionales**
> Quatre fois par an, les organisateurs des différents groupes se retrouvent pour partager leurs expériences, coordonner les actions communes et se soutenir mutuellement.
**Governance section (NL):**
> **Regionale bijeenkomsten**
> Vier keer per jaar komen de organisatoren van de verschillende groepen samen om ervaringen te delen, gezamenlijke acties te coördineren en elkaar te ondersteunen.
---
**Coordination duo (FR):**
> **Duo de coordination**
> Un duo de bénévoles assure la coordination du réseau : accueil des nouveaux groupes, gestion de la plateforme commune, lien avec les partenaires. Ce n’est pas une structure hiérarchique : le duo est au service du réseau.
**Coordination duo (NL):**
> **Coördinatieduo**
> Een duo van vrijwilligers zorgt voor de coördinatie van het netwerk: onboarding van nieuwe groepen, beheer van het gemeenschappelijk platform, contact met partners. Dit is geen hiërarchische structuur: het duo staat ten dienste van het netwerk.
---
**Local groups section (FR):**
> **Groupes locaux**
> Chaque groupe Kidical Mass est géré par des bénévoles locaux. Ils recrutent leurs propres bénévoles, planifient leurs itinéraires et communiquent avec leur communauté. Ils reçoivent un soutien de la part du réseau pour la sécurité, la communication et la logistique.
**Local groups section (NL):**
> **Lokale groepen**
> Elke Kidical Mass-groep wordt beheerd door lokale vrijwilligers. Ze werven hun eigen vrijwilligers, plannen hun routes en communiceren met hun gemeenschap. Ze ontvangen ondersteuning van het netwerk op het gebied van veiligheid, communicatie en logistiek.
---
**Safety & routes (FR):**
> Kidical Mass dispose de protocoles de sécurité et de directives d’itinéraires partagés, utilisés par tous les groupes. Ces protocoles sont fournis dans le cadre du soutien à l’intégration des nouveaux groupes.
**Safety & routes (NL):**
> Kidical Mass heeft gedeelde veiligheidsprotocollen en routerichtlijnen die door alle groepen worden gebruikt. Deze protocollen worden verstrekt als onderdeel van de ondersteuning bij de onboarding van nieuwe groepen.
---
**Organigram note:**
The current live site includes an SVG organigram showing three levels: local groups at the base, regional meetups in the middle, coordination duo at the top. The visual uses circular nodes connected by lines. Asset should be sourced from the current site or recreated. Text labels in the SVG (from source):
- Niveau 1 / Level 1: Groupes locaux / Lokale groepen
- Niveau 2 / Level 2: Rencontres régionales / Regionale bijeenkomsten (4×/an · 4×/jaar)
- Niveau 3 / Level 3: Duo de coordination / Coördinatieduo

---

# UX · About/Vision

## Kern
- Articulates **what Kidical Mass is fighting for** — the political and advocacy dimension of the movement
- Merges two current pages: `/nos-revendications-onze-aanbevelingen` (policy demands) and `/what-we-want` (Child Friendly City manifesto)
- Presents **4 concrete policy demands** around cycling infrastructure and urban safety
- Connects to the **Child Friendly City coalition** — the broader rights-based frame
- Balances advocacy clarity with an inclusive, non-preachy tone (per Tone of Voice guide)
- Useful for press, funders, politicians, and engaged parents who want to know what the movement stands for
## Page URL
`/about/vision` · `/over/visie` · `/a-propos/vision`
*(Merges and redirects from: *`/nos-revendications-onze-aanbevelingen`* and *`/what-we-want`*)*
## Users and Intentions
## Page Layout — Skeleton
### 1. Page header
- H1: “Vision” (localised: Visie / Vision)
- Subtitle: one sentence that bridges from parades to politics — e.g. *“We don’t just ride. We demand cities that are safe and joyful for every child.”*
- Optional: a quote from a parent (Julienne, Fatima, or Camille from the live site research) as a pull quote under the header.
### 2. Four policy demands
Four distinct cards or sections, each with:
- Icon or number
- Short heading (the demand)
- 2–3 sentence explanation
Layout: 2×2 grid on desktop, stacked on mobile. Cards are plain — no photos, just icon + text.
### 3. Child Friendly City section
- Heading: “Child Friendly Cities”
- Short intro paragraph: explains the coalition and the manifesto
- Key principle: cities should be designed around children’s needs — UN Convention on the Rights of the Child articles 6, 12, 13, 24, 31 referenced
- A few lines from the manifesto (not the full document)
- Optional: link to the full manifesto PDF or external site if available
### 4. Parent voices / quotes
Optional: 2–3 pull quotes from parents. Names: Julienne, Fatima, Camille (from live site). Short, human, emotionally grounded. Not activist rhetoric.
### 5. CTA block
- Heading: “Join the movement”
- CTA button: → /getting-started
- Secondary link: → /help-out
## Interactions
## Responsive Behaviour
## What's NOT Included
- Mission and axes → `/about/mission`
- Governance structure → `/about/organisation`
- Press coverage → `/about/press`
- Full manifesto text (too long — link to PDF if needed)
- Specific campaign updates or news → `/about/news`
## Decisions
1. **Merge**: Confirmed — /nos-revendications + /what-we-want merge into a single /about/vision page. ✅
1. **Manifesto**: Don’t embed the full document. Link to it externally (PDF or coalition site). Also create a dedicated news item about the manifesto on /about/news. ✅
1. **Parent quotes**: Julienne, Fatima, Camille are real people who have consented — safe to use on the new site. ✅
1. **Tone**: Keep the strong advocacy register on this page. It’s appropriate here. Don’t soften. ✅
## Open Questions
1. **Brussels vs. national scope** ⚠️ The 4 policy demands were likely drafted with Brussels in mind (cycling lanes, zone 30, school environments are communal/regional policies). Verify with Leticia: are these demands equally relevant for Walloon and Flemish cities? Do they need to be reframed as national demands, or are they inherently local and each chapter advocates at their own city level?
1. **UN Convention references**: Should the specific article numbers (6, 12, 13, 24, 31) be cited on the page, or is that too dense for a general audience?
1. **Manifesto link**: What is the URL of the Child Friendly City manifesto? Does a PDF exist, or does it live on an external coalition site?
---
## Research / Content Source
*Verbatim content scraped from the live site at *[kidicalmass.be](http://kidicalmass.be/)* — for use when building this page.*
### Source 1: /nos-revendications-onze-aanbevelingen
**Page title (FR):** Nos revendications
**Page title (NL):** Onze aanbevelingen
**Intro (FR):**
> Kidical Mass revendique des villes adaptées aux enfants. Voici nos quatre demandes concrètes aux pouvoirs publics.
**Intro (NL):**
> Kidical Mass eist kindvriendelijke steden. Dit zijn onze vier concrete aanbevelingen aan de overheid.
---
**Demand 1 (FR):**
> **Des pistes cyclables sécurisées sur les axes principaux**
> Les enfants doivent pouvoir se déplacer à vélo en toute sécurité. Nous demandons des aménagements cyclables sécurisés sur tous les axes principaux, séparés de la circulation automobile.
**Demand 1 (NL):**
> **Veilige fietspaden op de hoofdassen**
> Kinderen moeten veilig met de fiets kunnen reizen. We vragen veilige fietsinfrastructuur op alle hoofdassen, gescheiden van het autoverkeer.
---
**Demand 2 (FR):**
> **Des parkings vélo adaptés aux familles**
> Des abris vélo sécurisés et accessibles pour les vélos cargo, les remorques et les vélos d’enfants, dans les espaces publics, les écoles et les commerces.
**Demand 2 (NL):**
> **Gezinsvriendelijke fietsenstallingen**
> Beveiligde en toegankelijke fietsenstallingen voor bakfietsen, aanhangwagens en kinderfietsen, in publieke ruimtes, scholen en winkels.
---
**Demand 3 (FR):**
> **Des abords d’école sécurisés**
> Les entrées d’école doivent être des zones de sécurité. Nous demandons la suppression du stationnement devant les écoles, des aménagements prioritaires pour les piétons et les cyclistes, et des zones de dépose sécurisées.
**Demand 3 (NL):**
> **Veilige schoolomgevingen**
> Schoolingangen moeten veiligheidszones zijn. We vragen het verwijderen van parkeerplaatsen voor scholen, prioritaire inrichting voor voetgangers en fietsers, en beveiligde afzetpunten.
---
**Demand 4 (FR):**
> **L’application effective du zone 30**
> Le zone 30 existe dans de nombreuses communes, mais n’est pas respecté. Nous demandons une application stricte, des aménagements physiques qui réduisent la vitesse, et des contrôles réguliers.
**Demand 4 (NL):**
> **Effectieve handhaving van zone 30**
> Zone 30 bestaat in veel gemeenten, maar wordt niet gerespecteerd. We vragen strikte handhaving, fysieke inrichting die snelheid vermindert, en regelmatige controles.
---
### Source 2: /what-we-want
**Page title:** What We Want — Child Friendly Cities
**Intro:**
> We believe every child has the right to a city that is safe, healthy, and joyful. That’s why Kidical Mass is part of the Child Friendly Cities coalition — a movement that puts children’s rights at the centre of urban planning.
**Coalition framing:**
> The Child Friendly City manifesto is a coalition document signed by organisations across Belgium and Europe. It calls on cities to design public space around the needs of children, not cars.

---

# UX · About/News

## Kern
- The **editorial hub** for Kidical Mass updates — announcements, seasonal news, volunteer calls, event recaps
- Replaces the current `/my-blog` (Wix blog) with a purpose-built news feed
- Content is **bilingual by article** — each article is written in FR and/or NL (not stacked on same page)
- Audience is primarily existing followers and engaged community members (not first-time visitors)
- Volume is low (a few articles per year) — design must not feel empty when there’s little content
- No comments, no social sharing buttons, no newsletter signup (for now)
## Page URL
`/about/news` · `/over/nieuws` · `/a-propos/actualites`
*(Redirect from current *`/my-blog`*)*
## Users and Intentions
## Page Layout — Skeleton
### 1. Page header
- H1: “News” (localised: Nieuws / Actualités)
- Subtitle (optional): brief descriptor — e.g. *“Updates from the Kidical Mass network.”*
- No hero image. Clean list page.
### 2. Article feed
- **List layout**: articles displayed as cards in a vertical feed (not a grid)
- Each article card shows:
- Cards are sorted newest-first
- No pagination needed (low volume — load all on one page, or simple load-more if volume grows)
### 3. Empty state
- If no articles exist yet: friendly message — *“Nothing here yet — check back soon!”*
- Avoid empty white space; always show the header at minimum
### 4. Article detail page (child route: `/about/news/[slug]`)
- H1: article title
- Date + language tag
- Article body (rich text, may include images)
- “← Back to News” link at top
- No related articles section (too complex for low-volume content)
- No comments section
## Interactions
## Responsive Behaviour
## What's NOT Included
- Chapter-specific news → surfaced on individual chapter pages (`/chapters/[slug]`)
- Press coverage and media mentions → `/about/press`
- Event listings → homepage events strip or `/chapters` pages
- Newsletter subscription (no email list tool integrated at this stage)
- Social share buttons (not part of MVP)
- Comments or reactions
## Decisions
1. **Content ownership**: Any chapter lead can publish articles to the network news feed — distributed publishing model. ✅
1. **Language policy**: Bilingual preferred — aim for FR+NL per article — but mono (FR-only or NL-only) is acceptable when capacity is limited. Show a language badge on each article card. ✅
## Open Questions
1. **Image support**: Should articles support an optional cover image? (The current blog uses images.) Recommend yes — flag to Nico as a CMS field.
1. **Slug structure**: `/about/news/[slug]` or `/news/[slug]`? Recommend keeping under /about for nav consistency — flag to Nico.
1. **RSS feed**: Should a machine-readable RSS feed be generated? Useful for press and partners to follow network updates automatically.
---
## Research / Content Source
*Verbatim content scraped from the live site at *[kidicalmass.be](http://kidicalmass.be/)* — for use when building this page.*
### Source: /my-blog
**Page title:** Blog / Actualités / Nieuws
**3 articles visible at time of scrape:**
---
**Article 1:**
> **Title:** Appel aux bénévoles — Oproep aan vrijwilligers
> **Date:** (not shown in scrape — estimated early 2025 based on context)
> **Language:** FR + NL (bilingual article)
> **Excerpt (FR):** Kidical Mass cherche des bénévoles pour la saison 2025. Que tu sois disponible pour escorter les parades, gérer les réseaux sociaux ou photographier les événements, on a besoin de toi.
> **Excerpt (NL):** Kidical Mass zoekt vrijwilligers voor het seizoen 2025. Of je nu beschikbaar bent om parades te begeleiden, sociale media te beheren of evenementen te fotograferen, we hebben jou nodig.
---
**Article 2:**
> **Title:** Season Launch MeetUp — 7 maart 2025
> **Date:** Published around February 2025
> **Language:** FR + NL
> **Full content (FR):**
> Le coup d’envoi de la saison 2025 est lancé ! Rejoignez-nous le 7 mars pour notre réunion de lancement de saison. Au programme : bilan de 2024, présentation des nouveaux groupes, planification des parades, et bonne ambiance garantie. Lieu et heure à confirmer — suivez nos réseaux pour les détails.
> **Full content (NL):**
> Het startschot voor het seizoen 2025 is gegeven! Kom op 7 maart naar onze seizoensopstartbijeenkomst. Op het programma: terugblik op 2024, voorstelling van nieuwe groepen, planning van de parades, en gezelligheid gegarandeerd. Locatie en tijdstip worden bevestigd — volg onze sociale media voor de details.
---
**Article 3:**
> **Title:** End of Year Party — Fête de fin d’année — Eindejaarsfeest
> **Date:** Published around December 2024
> **Language:** FR + NL
> **Excerpt (FR):** La saison 2024 se termine ! Venez célébrer avec nous lors de notre fête de fin d’année. Une occasion de se retrouver, de partager les moments forts de l’année et de se projeter vers 2025.
> **Excerpt (NL):** Het seizoen 2024 zit erop! Kom vieren met ons op ons eindejaarsfeest. Een gelegenheid om samen te komen, de hoogtepunten van het jaar te delen en vooruit te kijken naar 2025.
---
**Observed patterns from current blog:**
- Articles are bilingual (FR + NL) within a single post
- Article types: announcements, seasonal events, volunteer calls
- No author bylines visible
- No categories or tags
- Estimated 3–5 articles/year
- No cover images visible in current blog list view (may be inside articles)

---

# UX · About/Press

## Kern
- Aggregates **all press coverage** of Kidical Mass — federal network level + individual chapters
- Complex page: press items at the **chapter level auto-surface here** (same mechanism as chapter-level press pages)
- Serves press contacts, researchers, funders, and the community as a credibility signal
- Organised **chronologically** — newest first
- Coverage spans 2020–2025+ and includes TV, radio, print, online
- Must handle **bilingual coverage** (some items are FR, some NL, some both)
- No editorial narrative needed — the list IS the content
## Page URL
`/about/press` · `/over/pers` · `/a-propos/presse`
*(Redirect from current *`/press`*)*
## Users and Intentions
## Page Layout — Skeleton
### 1. Page header
- H1: “Press” (localised: Pers / Presse)
- Optional subtitle: *“Media coverage of Kidical Mass Belgium, 2020–present.”*
- Press contact block (if relevant): name, email address for press enquiries. Only include if there’s a dedicated press contact role.
### 2. Press item feed
- Sorted: **newest first**
- Each press item = one row with:
- Layout: table or structured list — not cards (too heavy for a long list of links)
### 3. Auto-aggregation of chapter press items
- Chapter pages can have their own press section (local coverage)
- Those items automatically surface on `/about/press` with a chapter tag
- This is a **platform feature** — needs to be flagged to Nico as a data requirement
- Federation flag: `is_national` (bool) on each press item; false = chapter press, surfaced here with chapter attribution
### 4. Empty state
- Should never be empty — the press archive goes back to 2020
- But if the admin hasn’t added items yet: friendly placeholder
### 5. Press contact block (optional footer)
- “For press enquiries, contact:” + email
- Only shown if a press contact is designated
## Interactions
## Responsive Behaviour
## What's NOT Included
- Full article text (link only — copyright)
- Social media posts or mentions (press = formal media only)
- Internal publications or newsletters
- Photos or videos (except if a press item is a video, the link takes to the video)
- Press releases written by Kidical Mass → these could live under /about/news instead
## Data Model Note (for Nico / platform)
Each press item needs these fields:
- `outlet` (string)
- `headline` (string)
- `url` (string)
- `date` (date)
- `language` (enum: FR / NL / EN / bilingual)
- `media_type` (enum: TV / Radio / Print / Online)
- `chapter_id` (foreign key, nullable — null = federal/national)
- `is_featured` (bool, optional — for pinning key coverage)
## Decisions
1. **Press contact**: Yes — there is a designated press contact. List their email on the press page. (Get the actual email address from the coordination duo before build.) ✅
1. **Featured items**: Yes — pin 2–3 items to the top. Priority: video coverage from reputed sources (RTBF, BX1, etc.) first, then chronological list below. ✅
## Open Questions
1. **Auto-aggregation**: Confirm with Nico that chapter press items can be tagged with a chapter and automatically surface on /about/press. This is a data model requirement — see the Data Model Note section above.
1. **Dead links**: Some links from 2020–2021 may be dead (paywalls, removed articles). Strategy options: (a) keep entry and mark as “archived”, (b) remove dead links, (c) link to a web archive ([archive.org](http://archive.org/)) copy. Decide before build.
1. **Press items data**: Before build, the coordination duo needs to supply: full URLs for all items, exact publication dates, confirmation of which links are still live, and any NL-language coverage not yet listed.
---
## Research / Content Source
*Verbatim content scraped from the live site at *[kidicalmass.be](http://kidicalmass.be/)* — for use when building this page.*
### Source: /press
**Page title (FR):** Presse
**Page title (NL):** Pers
**Full press list (verbatim, as found on live site, newest first):**
---
**2025**
> **Politico** — 2025
> “Belgium’s Kidical Mass is turning children into cycling activists”
> [URL from live site: not captured in scrape — verify on live site]
> Language: EN · Media type: Online
---
**2024**
> **RTBF** — 2024
> “Kidical Mass : des centaines de familles à vélo dans les rues de Bruxelles”
> Language: FR · Media type: TV / Online
> **Bruzz** — 2024
> “Kidical Mass trekt opnieuw door Brussel: 'We willen veilige straten voor kinderen'”
> Language: NL · Media type: Online
> **BX1** — 2024
> “Kidical Mass à Bruxelles : les familles réclament des rues plus sûres”
> Language: FR · Media type: TV
> **La DH (La Dernière Heure)** — 2024
> “Kidical Mass : le mouvement vélo pour les enfants prend de l’ampleur en Belgique”
> Language: FR · Media type: Print / Online
---
**2023**
> **HLN (Het Laatste Nieuws)** — 2023
> “Kidical Mass groeit: meer dan duizend gezinnen op de fiets door Brussel”
> Language: NL · Media type: Print / Online
> **Het Nieuwsblad** — 2023
> “Op de fiets met de kinderen door de stad: Kidical Mass wil kindvriendelijke straten”
> Language: NL · Media type: Print / Online
> **RTBF** — 2023
> [Article title not captured in scrape — verify on live site]
> Language: FR · Media type: Online
---
**2022**

---

# UX · About/Partners

## Kern
- Lists and contextualises **who supports Kidical Mass** — institutional partners, sponsors, and allies
- Provides **social proof** and legitimacy for first-time visitors and funders
- Distinguishes between types of support (financial, in-kind, political, operational)
- Keeps partners **visible but not dominant** — this is Kidical Mass’s space, not a sponsor wall
- Serves as a reference for ongoing partnership conversations
- Low-maintenance page: partners don’t change frequently
## Page URL
`/about/partners` · `/over/partners` · `/a-propos/partenaires`
*(New URL — content currently scattered across homepage footer and the Organisation page)*
## Users and Intentions
## Page Layout — Skeleton
### 1. Page header
- H1: “Partners” (same in NL / FR)
- Subtitle: 1 sentence — e.g. *“Kidical Mass works with partners who share our commitment to child-friendly cities.”*
### 2. Partner categories (sections)
Group partners by relationship type. Suggested groupings (confirm with coordination duo):
**Section A: Institutional partners / supporters**
Organisations that provide structural support (funding, facilities, political backing).
Examples from live site: Bruxelles Mobilité, Ville de Bruxelles, Commune de Schaerbeek
**Section B: Movement allies / coalition partners**
Organisations aligned on advocacy goals (Child Friendly Cities coalition, cycling advocacy orgs).
Examples: Clean Cities Campaign
**Section C: Operational / in-kind partners**
Organisations that provide practical support (bike loans, logistics, venues).
Examples: Loopz (bike rental), Kidical Mouse (cargo bike) — if listed here vs. Getting Started page.
Each section:
- Section heading (H2)
- Short sentence explaining the type of partnership (1 line)
- Partner entries (see below)
### 3. Partner entries
Each partner = logo + name + 1-line description of the relationship.
- Logos: displayed as a clean grid (3–4 per row on desktop, 2 per row on mobile)
- Logos link to partner’s own website (external, new tab)
- Short description below each logo (optional — only if the relationship needs explaining)
- No large text blocks — this is a reference page, not an editorial page
### 4. “Become a partner” block (optional)
- If Kidical Mass is open to new partnerships: short text + email CTA
- If not actively seeking partners: omit this section
## Interactions
## Responsive Behaviour
## What's NOT Included
- Individual volunteers or donors (too volatile and privacy-sensitive)
- Chapter-specific local sponsors (those belong on individual chapter pages)
- Detailed partnership terms or financials
- Testimonials from partners (keep this page factual and clean)
## Decisions
1. **Partner list**: Confirmed complete — 4 partners: Bruxelles Mobilité, Clean Cities Campaign, Ville de Bruxelles, Commune de Schaerbeek. ✅
1. **Loopz + Kidical Mouse**: List on both the Partners page AND the Getting Started page. ✅
**Suggested categories with Loopz and Kidical Mouse added (6 entries total):**
- Institutional: Bruxelles Mobilité, Ville de Bruxelles, Commune de Schaerbeek
- Movement allies: Clean Cities Campaign
- Operational / in-kind: Loopz, Kidical Mouse
## Open Questions
1. **Brussels vs. national scope** ⚠️ All 4 confirmed partners are Brussels-based. Kidical Mass now operates nationally. Check with Leticia: are there Walloon or Flemish regional partners not listed on the live site? The Partners page should reflect the full national scope.
1. **Logo assets**: Do all partners have logos available for use? Are there any usage rights restrictions?
1. **Partner categories**: Confirm the 3-category structure above (Institutional / Movement allies / Operational), or adjust if needed.
1. **“Become a partner” CTA**: Is Kidical Mass actively seeking new partners? Should there be an email CTA, or keep the page purely informational?
---
## Research / Content Source
*Verbatim content from the live site at *[kidicalmass.be](http://kidicalmass.be/)* — for use when building this page.*
### Source 1: Homepage (/) — partner section / footer
**Partner list as shown on homepage (verbatim labels):**
> Bruxelles Mobilité
> Clean Cities Campaign
> Ville de Bruxelles
> Commune de Schaerbeek
*(These appear in the footer/bottom section of the homepage. No logos captured in scrape — must be sourced from live site or partner assets.)*
---
### Source 2: Homepage — Wallonie / Flanders sections
**From the Wallonie section of the homepage (FR):**
> *Kidical Mass soutenu par [partenaires régionaux — not captured in scrape]*
**From the Flanders section of the homepage (NL):**
> *[Flanders section content not fully captured in scrape — verify on live site]*
---
### Source 3: Getting Started research (cross-reference)
**Loopz partnership (from Getting Started page research):**
> Loopz is a bike rental partner. Promo code **KIDICALMASS** gives 2 free months of membership. This was confirmed as an active partnership during the Q&A session.
**Kidical Mouse cargo bike (from Getting Started page research):**
> The Kidical Mouse cargo bike is confirmed still operational. It can be borrowed by families who need a cargo bike to participate in parades.
---
### Source 4: Live site Organisation page — partner mentions
> *[No additional partner names found in the Organisation page scrape beyond those listed above.]*
---
**Editorial note for page build:**
The partners list on the live site is minimal (4 names). Before building this page, the coordination duo should provide:
- A complete and current list of all partners
- Partner logos in SVG or high-res PNG
- Preferred partner descriptions (1 line each)
- Confirmation of partnership categories
- Whether Loopz and Kidical Mouse should appear here or only on Getting Started

---

# UX · About (overview)

## Kern
- The **entry point to the About section** — orients visitors and routes them to the right sub-page
- Answers “what is Kidical Mass?” in a single glance, then invites deeper exploration
- Serves as a **section index**: mission, organisation, vision, news, press, partners
- Not a landing page — no hero, no large imagery — a clean, functional navigation page with enough context to orient
- Must work well for all three audiences: curious newcomer, press contact, potential partner
- Light on copy — each sub-section card leads to the full page
## Page URL
`/about` · `/over` · `/a-propos`
## Users and Intentions
## Page Layout — Skeleton
### 1. Page header
- H1: “About” (localised: Over / À propos)
- Subtitle: 2–3 line summary of Kidical Mass — the elevator pitch version. Should be different from the Mission page intro — shorter and more navigational in tone.
- Example: *“Kidical Mass organises family bike parades across Belgium and advocates for child-friendly cities. We are a volunteer-run federated network. Find out more about who we are, what we stand for, and how we’re organised.”*
### 2. Sub-section navigation cards
Six cards, one per sub-page. Each card:
- Icon (emoji or simple SVG)
- Sub-page title
- 1-line description (what you’ll find there)
- Entire card is clickable — links to sub-page
Layout: 3×2 grid on desktop, 2×3 on tablet, stacked on mobile.
### 3. Optional: mini stat bar
- If space allows and it doesn’t feel redundant with the homepage: 2–3 stats
- *“16+ communities · 150 parades · 120 volunteers”*
- Alternative: skip this and let Mission carry the stats
### 4. CTA block (bottom)
- Heading: “Want to get involved?”
- Two buttons: “Join a parade” → /chapters | “Help out” → /help-out
## Interactions
## Responsive Behaviour
## What's NOT Included
- Full content of any sub-page (the cards are teasers, not content)
- Events or parade information → homepage / /chapters
- Volunteer role details → /help-out
- Any imagery beyond optional banner photo
- Chapter map or list → /chapters
## Decisions
1. **Stat bar**: Include the mini stat bar on the overview page — it makes the page feel alive and substantive. ✅
1. **Sub-page order**: Mission → Vision → Organisation → News → Press → Partners. Story-first logic: why → what we stand for → how we work → content/credibility layers. ✅
1. **About in the nav**: Dropdown on desktop showing all 6 sub-pages. On mobile: About links directly to /about overview page. ✅
## Open Questions
1. **Elevator pitch copy**: The subtitle on this page needs to be distinct from the Mission page intro — shorter, more navigational in tone. Who writes it? Leticia / coordination duo before build.
1. **Brussels vs. national scope** ⚠️ The About overview sets the frame for the entire section. Ensure the subtitle and any stats explicitly reflect the national Belgian scope — not Brussels-only. Flag for copy review with Leticia.
---
## Sub-pages in this section
- [UX · About/Mission](https://www.notion.so/33dd3ecc475c81ac9b95fcdd802f31a8)
- [UX · About/Organisation](https://www.notion.so/33dd3ecc475c81348477ed188169ada9)
- [UX · About/Vision](https://www.notion.so/33dd3ecc475c8134b98ad3ebd246a2f2)
- [UX · About/News](https://www.notion.so/33dd3ecc475c81989ef7e718acbaa733)
- [UX · About/Press](https://www.notion.so/33dd3ecc475c8161badcd2ef91d4b08f)
- [UX · About/Partners](https://www.notion.so/33dd3ecc475c81e9afbcdbf2dc952c35)
---
## Research / Content Source
*Verbatim content from the live site at *[kidicalmass.be](http://kidicalmass.be/)* — for use when building this page.*
### Source: / (Homepage) — mission summary section
**FR (from homepage):**
> Kidical Mass Belgique est un réseau fédéré de balades à vélo pour les familles. Nous organisons des parades vélo sûres et joyeuses partout en Belgique, pour revendiquer des villes adaptées aux enfants.
**NL (from homepage):**
> Kidical Mass België is een gefedereerd netwerk van fietstochten voor gezinnen. We organiseren veilige en vrolijke fietsparades door heel België, om kindvriendelijke steden op te eisen.
---
**Growth stat (from homepage):**
> De 1 groupe en 2020 à 13 groupes actifs en 2024 — Van 1 groep in 2020 naar 13 actieve groepen in 2024
---
**Volunteer appeal (from homepage — possible source for CTA copy):**
**FR:**
> Envie d’aider ? Kidical Mass cherche des bénévoles pour escorter les parades, gérer la communication, photographier les événements et bien plus encore.
**NL:**
> Wil je helpen? Kidical Mass zoekt vrijwilligers om parades te begeleiden, communicatie te beheren, evenementen te fotograferen en nog veel meer.
---
**Note for page build:**
The About overview page is intentionally light on original copy. Its primary job is navigation and a brief orientation. The elevator pitch subtitle should be drafted fresh — not copied from Mission or Homepage — to feel like a meta-level introduction to the section.
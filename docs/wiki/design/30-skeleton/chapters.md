---
title: Lokale groepen — Chapters overview + page template
tags: []
sources: [notion, raw/website/index.md, raw/website/organisation.md, wiki/design/20-structure.md, wiki/glossary.md]
phase: design
updated: 2026-06-03
---

This file covers two related pages: the **Lokale groepen / Chapters Overview** (national directory) and the **Chapter Page** (per-chapter template).

> **Terminology — ✅ resolved 2026-06-03 (Frederik): the public NL term is "Lokale groepen".** Replaces the earlier nav label "Afdelingen", which read top-down/federated; "lokale groepen" is the [glossary](../../glossary.md)-approved, grassroots term the duo use themselves (their org page uses *Lokale groepen*; "afdeling" was already softened to "lokale groep" on Help out). Applied site-wide: `lang/nl/nav.php` `nav.chapters` → **"Lokale groepen"** (nav label = H1, for consistent wayfinding). "chapter" remains the internal EN label only. *(Internal route/file names stay `groups.*` / `chapters.md`.)*

---

# Lokale groepen / Chapters Overview

Status: ✅ UX re-planned 2026-06-03 (NL framing + Liège/Mons hosted + term resolved to "Lokale groepen"). Live view still EN wireframe stub (`groups/index.blade.php`) and has **drifted** to a card grid with count badges (see open-Q #6). Page URL: `/chapters` · **nav label NL = "Lokale groepen"** · route `groups.index` (`/nl/chapters`, `/fr/chapters`, `/en/chapters` later).

**Summary:** One view serves two audiences — families finding their local chapter, and stakeholders (grant reviewers, partners) assessing the movement's scale. The map delivers both. The list is first-class alongside the map. Regional grouping: Brussel → Wallonië → Vlaanderen. **Names and links only — no per-chapter event previews, no count badges** (the map carries the scale signal). **Liège + Mons are now hosted full chapters** (first-class `/chapters/[postal]` pages that may link out to their own domains), not external pins — revised 2026-06-02. "Begin een lokale groep" CTA at the bottom turns gaps in the map into an invitation.

---

## Strategy

The national directory of all chapters. Replaces the hidden `/all-groups` page and the scattered regional section on the homepage. Serves families looking for their local group and stakeholders assessing the movement's reach.

### Who arrives and in what mental state

**Families: "Is there a chapter in my municipality?"**
Arrive with a specific question. They want to find their local chapter. The map is visual confirmation ("yes, Schaerbeek has one") and the list is the clickable action. Speed matters — they're one step from the chapter page.

**Grant reviewers / partners: "How big is this movement?"**
Arrive with an evaluative intent. The visual density of pins on the map, the count in the header subtitle, the regional spread — these are the signals they're looking for. They may not click any individual chapter. **Reframed (interview 2026-05-18):** serving grants + helping families find *existing* groups is now the page's primary job; recruiting *new* leads is secondary. So the map's job is mostly "show the scale + the existing coverage", not "advertise the gaps".

**Potential chapter leads: "Which cities don't have a chapter yet?"** *(secondary)*
Arrive looking for gaps. Blank space on the map is the signal. The "Begin een lokale groep" CTA at the bottom catches them before they leave — but it's the quiet closing beat, not the page's headline.

### What good looks like

A family arrives, sees the map of Belgium with pins, spots their municipality (or sees there isn't one yet), clicks the pin or the list entry, and lands on the chapter page. Total time: under 15 seconds.

---

## Scope

**Must have:**
- Map of Belgium showing all active chapters (pins tappable → chapter pages); Liège + Mons are **hosted pins** → their hosted `/chapters/[postal]` pages (which may then link out to their own domains)
- List of all chapters (clickable → chapter page), region-grouped, **name + municipality only**
- "Begin een lokale groep" CTA

**Should have:**
- Chapter count as a headline stat (the scale signal grant reviewers scan)
- Regional grouping (Brussel, Wallonië, Vlaanderen) in the list

**Out of scope:**
- Chapter management (that's admin)
- Inactive or placeholder chapters
- **Per-chapter previews of any kind in the list** ✅ Names + links only. **No count badges** ("X activities / X articles") — the live build drifted into these; they're noise to a family (who cares how many news posts a group wrote?) and the map already carries the "how big" signal for grants. See open-Q #6.
- Per-chapter **email opt-in** — that "subscribe to this chapter" control lives on the *chapter page*, not the overview (it needs a single group scope)

---

## Structure

Two-part page: map + list. No sub-navigation.

**Section flow:**
1. Page header — "Chapters" + dynamic stat subtitle
2. Map — Belgium map with chapter pins
3. Chapter list — grouped by region
4. Start a chapter CTA

**Key links out:**
- Map pins / list entries → /chapters/[postal-code] or /chapters/[code1-code2]
- Liège + Mons → their **hosted** /chapters/[postal] pages (revised 2026-06-02 — no longer external pins; their page may link out to kidicalmassliege.org / mons.bike)
- "Begin een lokale groep" → /help-out#start-a-chapter

---

## Skeleton

**Brussels clustering ✅:** Brussels has 12+ chapters — at national zoom they appear as a cluster pin that expands on tap to reveal individual chapter pins.

**Flanders group hidden until at least one Flemish chapter is active. ✅**

**List is first-class alongside the map** — not a fallback. Handles accessibility and slow connections. **Plain region-grouped name links — no cards, no count badges** (see Scope; resolves the live drift).

### Desktop (NL route shown — `/nl/chapters`)

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  Lokale groepen                                      │
│  45 lokale groepen in heel België                    │
├──────────────────────────────────────────────────────┤
│                                                      │
│  [ MAP — full-width Belgium · ~450px tall ]          │
│  [ One pin per chapter — all hosted, none external ] │
│  [ Brussel: cluster pin → expands on tap ]           │
│  [ Liège / Mons: regular pins → hosted page ]        │
│  [ Tooltip on hover: gemeente naam ]                 │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Vind je groep                                       │
│  Tik je gemeente aan voor de volgende fietstocht en  │
│  het lokale team                                     │
│                                                      │
│  Brussel                                             │
│  Anderlecht · Berchem-Sainte-Agathe · Brussel-Stad   │
│  Etterbeek · Evere – Haren · Vorst – Forest          │
│  Elsene – Ixelles · Jette · Molenbeek                │
│  Neder-Over-Heembeek · Schaarbeek                    │
│  Watermaal-Bosvoorde & Oudergem                      │
│  Sint-Pieters-Woluwe & Sint-Lambrechts-Woluwe        │
│                                                      │
│  Wallonië                                            │
│  Liège · Mons · Namur                                │
│                                                      │
│  [Vlaanderen — verborgen tot er een groep actief is] │
│                                                      │
├──────────────────────────────────────────────────────┤
│  [distinct background — quiet closing beat]          │
│                                                      │
│  Staat jouw stad er nog niet bij?                    │
│  Er komen steeds nieuwe groepen bij. Je hoeft geen   │
│  fietsexpert te zijn. Gewoon iemand die zijn buurt   │
│  graag ziet. Wij helpen je op weg.                   │
│                                                      │
│  [ Zo begin je → ]                                   │
│  Vragen? Mail het coördinatieduo →                   │
│                                                      │
├──────────────────────────────────────────────────────┤
│ FOOTER                                               │
└──────────────────────────────────────────────────────┘
```

### Mobile

```
┌──────────────────────┐
│ NAV (hamburger)      │
├──────────────────────┤
│  Chapters            │
│  16 active groups    │
│  across Belgium      │
├──────────────────────┤
│ [ MAP — Belgium ]    │
│ [ ~300px tall ]      │
│ [ tappable pins ]    │
│ [ cluster for BXL ]  │
├──────────────────────┤
│  Brussels            │
│  Anderlecht          │
│  Berchem-Ste-Agathe  │
│  Bruxelles-Ville     │
│  Etterbeek           │
│  Evere – Haren       │
│  Forest – Vorst      │
│  Ixelles – Elsene    │
│  Jette               │
│  Molenbeek           │
│  Neder-Over-Heembeek │
│  Schaerbeek          │
│  Watermael & Auderghem│
│  Woluwe-St-P & St-L  │
│                      │
│  Wallonia            │
│  Liège (ext. ↗)      │
│  Mons                │
│  Namur               │
├──────────────────────┤
│  [section break]     │
│  Don't see your      │
│  city?               │
│  New chapters keep   │
│  joining...          │
│  [ Find out how → ]  │
│  Email the team →    │
├──────────────────────┤
│ FOOTER               │
└──────────────────────┘
```

### Annotations

- **Header subtitle:** Dynamic chapter count from database. "45 lokale groepen in heel België" — updates automatically as new chapters join. (Count comes from the `Group` model; ~45 per the glossary, not the stale "16".)
- **Map:** Leaflet/Mapbox, clean map style (not satellite). Pin touch target minimum 44×44px. Brussel cluster expands on tap to show individual municipality pins. **Liège + Mons are ordinary hosted pins** → their hosted `/chapters/[postal]` pages (no external ↗ on the map; their *page* may link out to their domain).
- **Chapter list:** Plain text region-grouped list, **no cards, no images, no count badges, no per-chapter event data**. Each entry is a link. Multi-municipality chapters show combined name and hyphenated postal codes: "Sint-Pieters-Woluwe & Sint-Lambrechts-Woluwe (1150–1200)". On the NL route, show the NL municipality form first ("Schaarbeek", "Vorst – Forest"); FR route mirrors.
- **Regional order:** Brussel → Wallonië → Vlaanderen (order of establishment). Vlaanderen section hidden when empty.
- **"Begin een lokale groep" CTA:** Distinct background section at the bottom — the quiet closing beat (lead-recruiting is secondary, per Strategy). Short, warm, inviting. Reuses the Help-out barrier-dissolver line ("je hoeft geen fietsexpert te zijn"). Links to /help-out#start-a-chapter; secondary mailto to the coördinatieduo.

---

## Open Questions / Necessary Refinements

1. **Complete chapter list:** The wireframe above shows approximate chapters based on the raw homepage. The exact list (all active chapters, their names, postal codes, and multi-municipality pairings) needs to come from Leticia/Nico before build. Glossary now says ~45 groups — confirm the live count.
2. **Map implementation:** Leaflet vs. Mapbox confirmed ✅ in existing spec. Confirm with Nico which library is in use and whether chapter coordinates are stored in the database.
3. **Brussels cluster behaviour:** When a user taps the Brussel cluster pin, do they see all individual Brussel pins on the map, or are they taken to a "Brussel chapters" filtered list? Proposed: expand on the map to show individual pins. Confirm UX approach.
4. ~~**Liège external link handling**~~ ✅ **Resolved 2026-06-02.** Liège + Mons are **hosted full chapters**, not external pins — ordinary pins → their hosted `/chapters/[postal]` pages (which may link out to their own domains). No external ↗ on the overview.
5. **Chapter name display language:** On the NL route, show the NL municipality form first ("Schaarbeek", "Vorst – Forest"); FR route mirrors. Bilingual official names where both exist. Confirm the `Group.name` field stores both forms (or a locale-aware accessor exists).
6. **Live-build drift — cards + count badges (NEW, blocks faithful build):** `groups/index.blade.php` currently renders a **3-col card grid** with **"X activities · X articles" count badges** and a "Part of: [parent]" line — contradicting the "names + links only, no per-chapter previews" decision (Scope). The count badges are noise to a family and the articles count is meaningless to anyone outside the org. **Proposed:** revert to the plain region-grouped name-link list; let the map carry the scale signal. Confirm with Frederik before the NL/build pass.
7. ~~**Nav/H1 term — "Afdelingen" vs "Lokale groepen"**~~ ✅ **Resolved 2026-06-03 (Frederik): "Lokale groepen".** Applied site-wide — `lang/nl/nav.php` `nav.chapters` → "Lokale groepen" (nav label = H1), `NavigationTest` assertion updated, full suite green. "chapter" stays the internal EN label; route/file names unchanged (`groups.*` / `chapters.md`).

---

---

# Chapter Page Template

Status: ✅ Complete. Page URL: `/chapters/[postal-code]` (single-municipality) or `/chapters/[code1-code2]` (multi-municipality) ✅

**Summary:** One fixed template, every chapter. Municipality name is the only differentiator — no chapter colours, logos, or custom layouts. Events are auto-populated from the database. Team + volunteer form are one section. Sections with no content are hidden entirely. Self-published by chapter leads within design constraints.

---

## Strategy

The local home page for each chapter. The user arrives with a specific local intent — they want to know when the next ride in their neighbourhood is, who organises it, and how to join. The chapter page is the answer to all three.

### Who arrives and in what mental state

**Families: "When's the next ride in my neighbourhood?"**
Arrive from a Google search ("Kidical Mass Schaerbeek"), from the events page, from the chapter map, or from a WhatsApp link. Their primary question is local and practical. They want the next date quickly.

**Local volunteers and curious joiners: "Who's organising, how do I reach them?"**
Arrive knowing they want to volunteer locally. The team section + form answer this. The proximity of team names to the contact form is deliberate.

**Chapter leads: managing their own page**
Not a primary UX audience for the public page — they interact via the admin panel. But the page reflects their work, so the quality of sections depends on them. Empty sections hidden by default means a sparse chapter page doesn't embarrass.

### Key tension resolved

**Brand consistency vs. local ownership:** Strictly uniform template ✅. No custom colours, logos, or layouts per chapter. The municipality name is the only differentiator. Chapter leads control content within the template; they don't control the template.

---

## Scope

**Must have:**
- Local event schedule (auto-populated from Events database)
- Team section (optional — hidden if no team added)
- Volunteer contact form (routed to this chapter's lead by context) — name, email, optional role checkboxes + message; the J2 conversion point (Help out only orients → routes here)
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
2. Upcoming events (auto-populated — always shown, may show empty state)
3. Team + volunteer form (merged section — hidden entirely when no team members added)
4. Local partners (optional — hidden when empty)
5. Press coverage (optional — hidden when empty)
6. Downloads (optional — hidden when empty)

---

## Skeleton

**Brussels bilingual toggle ✅:** Brussels chapter pages show a NL/FR toggle in the header. Wallonia → FR default. Flanders → NL default.

**Team + Volunteer CTA merged ✅:** Seeing the team names and immediately being invited to join is the point. One section, not two.

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│ ← All chapters                                       │
│                                                      │
│  Schaerbeek                    [NL | FR]  ← Brussels │
│  1030                                    only toggle │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Upcoming rides in Schaerbeek                        │
│                                                      │
│  ┌────────────────────────────────────────────────┐  │
│  │ Kidical Mass Schaerbeek       31 May · 15:00  │  │
│  │ Meeting point: Place Colignon                  │  │
│  └────────────────────────────────────────────────┘  │
│  ┌────────────────────────────────────────────────┐  │
│  │ Kidical Mass Schaerbeek       28 Jun · 15:00  │  │
│  │ Meeting point: Place Colignon                  │  │
│  └────────────────────────────────────────────────┘  │
│                                                      │
│                   Past rides → /events?...           │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Organised by                                        │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐           │
│  │ [photo]  │  │ [photo]  │  │ [photo]  │           │
│  │ Name     │  │ Name     │  │ Name     │           │
│  │ Pink vest│  │ Co-org   │  │ Comms    │           │
│  └──────────┘  └──────────┘  └──────────┘           │
│                                                      │
│  Want to help in Schaerbeek?                         │
│  Join Sofie, Marc & Lena. [2-sentence warm pitch]    │
│                                                      │
│  Name  ___________________________                   │
│  Email ___________________________                   │
│  I'd like to help with:  (optional)                  │
│   [ ] Pink vest  [ ] Co-org  [ ] Comms               │
│   [ ] Photo  [ ] DJ  [ ] Not sure yet                │
│  Message (optional) ______________________           │
│                    [ I'm in → ]                      │
│                                                      │
│  More about volunteering →                           │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Local partners  [hidden if empty]                   │
│  ┌────────┐  ┌────────┐                             │
│  │ [logo] │  │ [logo] │                             │
│  │ Name   │  │ Name   │                             │
│  └────────┘  └────────┘                             │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Press coverage  [hidden if empty]                   │
│  HLN · "Kidical Mass groeit"  Mar 2024  ↗            │
│  Bruzz · "Kidical Mass trekt..."  Jun 2024  ↗         │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Downloads  [hidden if empty]                        │
│  Flyer_Schaerbeek_2026.pdf  [↓ Download]             │
│                                                      │
├──────────────────────────────────────────────────────┤
│ FOOTER                                               │
└──────────────────────────────────────────────────────┘
```

### Mobile

```
┌──────────────────────┐
│ NAV (hamburger)      │
├──────────────────────┤
│ ← All chapters       │
│                      │
│  Schaerbeek          │
│  1030                │
│  [NL | FR]           │
├──────────────────────┤
│  Upcoming rides      │
│  in Schaerbeek       │
│                      │
│ ┌──────────────────┐ │
│ │ KM Schaerbeek    │ │
│ │ 31 May · 15:00   │ │
│ │ Place Colignon   │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ KM Schaerbeek    │ │
│ │ 28 Jun · 15:00   │ │
│ └──────────────────┘ │
│  Past rides →        │
├──────────────────────┤
│  Organised by        │
│                      │
│ [photo] Name · Role  │
│ [photo] Name · Role  │
│ [photo] Name · Role  │
│                      │
│  Want to help in     │
│  Schaerbeek?         │
│  Join Sofie, Marc &  │
│  Lena. [pitch]       │
│                      │
│  Name _____________  │
│  Email _____________ │
│  Help with: (opt.)   │
│  [ ] Pink vest       │
│  [ ] Co-org [ ] Comms│
│  [ ] Photo [ ] DJ    │
│  [ ] Not sure yet    │
│  Message (opt.) ___  │
│  [ I'm in → ]        │
│  More about vol. →   │
├──────────────────────┤
│  Local partners      │
│  [logo] [logo]       │
│  [hidden if empty]   │
├──────────────────────┤
│  Press coverage      │
│  Outlet · Title ↗    │
│  [hidden if empty]   │
├──────────────────────┤
│  Downloads           │
│  File.pdf [↓]        │
│  [hidden if empty]   │
├──────────────────────┤
│ FOOTER               │
└──────────────────────┘
```

### Annotations

- **Breadcrumb:** "← All chapters" → /chapters. Small, top-left, before the chapter name.
- **Chapter header:** Municipality name large. Postal code smaller below. No chapter colours or logos. Brussels chapters show the NL/FR language toggle.
- **Upcoming events:** Same compact card as /events. Auto-populated. If no upcoming events: "No upcoming rides for Schaerbeek right now. Check /events for rides across Belgium." Past events are not shown on the page — a "Past rides →" link routes to /events with pre-set location filter.
- **Team section:** Name + role label + optional photo (no bio). Photos optional — hidden if not uploaded. The section disappears entirely if no team members added in admin.
- **Volunteer form (J2 — the form lives here, not on Help out):** name, email, optional **role checkboxes** (pink vest · co-org · comms · photo · DJ · *Not sure yet*), optional message. Routing is **implicit by context** — submitting from this page sends the `Volunteer enquiry` ([content model](../20-structure.md)) to *this* chapter's lead; no municipality dropdown. Short by design. "I'm in →" on submit shows the inline confirmation below. The "*Not sure yet*" box removes the barrier of needing to know your role before reaching out. Names in the pitch ("join Sofie, Marc & Lena") make the team concrete — the trust work that lets the confirmation stay modest.
- **Faces before form** is deliberate: you see the team, *then* you're invited to join *them*. One merged section, not two.
- **Confirmation state (resolved — was open-Q #4).** Replaces the form in place on submit. Warm + one hook, nothing more: *"the team"* (no name, no SLA — keeps the promise honest and independent of [D-1](../01-concerns.md)) plus the chapter's **next ride**, which already sits directly above the form (zero new data dependency). This is the J2 "post-submit limbo" antidote — see [help-out.md § post-submit limbo](help-out.md).
- **Built (2026-06-02).** This section is live: the placeholder is replaced by the **`ChapterVolunteerSignup`** Livewire component (`#aanmelden` anchor, `?intent=volunteer` welcome banner, name/email/role-checkboxes/message → `ContactForm`, warm "Je bent erbij" + next-ride `<x-event-card>` confirmation). Distinct from the event-detail `VolunteerSignup` — do not merge. `[backend]` routes to the central comms inbox tagged with the chapter name; **per-lead routing still pending** (no per-group lead email on the `Group` model) — specced for Nico in [GitHub #37](https://github.com/ndeblauw/kidicalmass/issues/37). The rest of the chapter page is still in **English** and needs its own NL pass.

```
│  Want to help in Schaerbeek?                         │
│  ┌────────────────────────────────────────────────┐ │
│  │  🎉  You're in.                                 │ │
│  │  Someone from the Schaerbeek team will be       │ │
│  │  in touch soon.                                 │ │
│  │  Don't wait for the email — come say hi:        │ │
│  │  → Next ride: Sun 28 Jun · 15:00 · Pl. Colignon │ │
│  │    (the ride card is right above ↑)             │ │
│  └────────────────────────────────────────────────┘ │
```
- **Local partners:** Logo + name + optional external link. Populated by chapter lead in admin. Hidden when empty.
- **Press coverage:** Outlet + headline + date + link. Structured list, not cards. Hidden when empty. Items automatically surface on /about/press.
- **Downloads:** File name + format + download button. Chapter-specific flyers. Hidden when empty.

---

## Open Questions / Necessary Refinements

1. **Team photos:** Are team member photos required or optional? Proposed: optional. If no photo uploaded, show initials avatar or a generic silhouette placeholder. Confirm with Nico.
2. **Brussels bilingual toggle:** On a Brussels chapter page with the NL/FR toggle, does switching language change the URL (e.g., `/nl/chapters/1030` vs `/fr/chapters/1030`) or is it a client-side toggle that doesn't update the URL? URL-based routing is preferred for shareability. Confirm with Nico.
3. **Empty chapter page state:** A chapter lead might create a chapter page in admin but not add any team members, partners, or downloads. The result is a page with only an events section (or empty events). Is this acceptable at launch? Proposed: acceptable, with the empty sections hidden. An admin nudge could encourage completing the page.
4. **Volunteer form — confirmation state:** ✅ Resolved (J2 pass, 2026-06-02) — warm "you're in" + the chapter's next-ride hook, "the team" with no name/SLA. See the annotation above. Final ToV copy (NL/FR) still to write.
5. **Multi-municipality chapter header:** For chapters spanning multiple municipalities (e.g., Woluwe-St-Pierre & St-Lambert), how does the header display? Proposed: "Woluwe-Saint-Pierre & Saint-Lambert" as the primary name, "1150–1200" as the postal code line. Long names may wrap on mobile — confirm typography handles this.
6. **Press coverage aggregation:** The spec says chapter press items automatically surface on /about/press. Confirm with Nico this is implemented at the data model level — every press item entry has a chapter_id field that the press page queries.

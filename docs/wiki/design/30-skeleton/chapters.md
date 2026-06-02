---
title: Chapters (Overview + Page Template)
tags: []
sources: [notion, raw/website/index.md, raw/website/organisation.md]
phase: design
updated: 2026-04-13
---

This file covers two related pages: the **Chapters Overview** (national directory) and the **Chapter Page** (per-chapter template).

---

# Chapters Overview

Status: ✅ Complete. Page URL: `/chapters` (trilingual: `/nl/chapters`, `/fr/chapters`, `/en/chapters`)

**Summary:** One view serves two audiences — families finding their local chapter, and stakeholders assessing the movement's scale. The map delivers both. The list is first-class alongside the map. Regional grouping: Brussels → Wallonia → Flanders. Names and links only — no per-chapter event previews. "Start a chapter" CTA at the bottom turns gaps in the map into an invitation.

---

## Strategy

The national directory of all chapters. Replaces the hidden `/all-groups` page and the scattered regional section on the homepage. Serves families looking for their local group and stakeholders assessing the movement's reach.

### Who arrives and in what mental state

**Families: "Is there a chapter in my municipality?"**
Arrive with a specific question. They want to find their local chapter. The map is visual confirmation ("yes, Schaerbeek has one") and the list is the clickable action. Speed matters — they're one step from the chapter page.

**Potential chapter leads: "Which cities don't have a chapter yet?"**
Arrive looking for gaps. The map is their tool — blank space on the map is the signal. The "Start a chapter" CTA at the bottom catches them before they leave.

**Grant reviewers / partners: "How big is this movement?"**
Arrive with an evaluative intent. The visual density of pins on the map, the count in the header subtitle, the regional spread — these are the signals they're looking for. They may not click any individual chapter.

### What good looks like

A family arrives, sees the map of Belgium with pins, spots their municipality (or sees there isn't one yet), clicks the pin or the list entry, and lands on the chapter page. Total time: under 15 seconds.

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
2. Map — Belgium map with chapter pins
3. Chapter list — grouped by region
4. Start a chapter CTA

**Key links out:**
- Map pins / list entries → /chapters/[postal-code] or /chapters/[code1-code2]
- Liège pin → kidicalmassliege.org ✅
- "Start a chapter" → /help-out#start-a-chapter

---

## Skeleton

**Brussels clustering ✅:** Brussels has 12+ chapters — at national zoom they appear as a cluster pin that expands on tap to reveal individual chapter pins.

**Flanders group hidden until at least one Flemish chapter is active. ✅**

**List is first-class alongside the map** — not a fallback. Handles accessibility and slow connections.

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  Chapters                                            │
│  16 active groups across Belgium                     │
├──────────────────────────────────────────────────────┤
│                                                      │
│  [ MAP — full-width Belgium · ~450px tall ]          │
│  [ One pin per chapter ]                             │
│  [ Brussels: cluster pin → expands on tap ]          │
│  [ Liège: regular pin → kidicalmassliege.org ↗ ]     │
│  [ Tooltip on hover: municipality name ]             │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Brussels                                            │
│  Anderlecht · Berchem-Sainte-Agathe · Bruxelles-Ville│
│  Etterbeek · Evere – Haren · Forest – Vorst          │
│  Ixelles – Elsene · Jette · Molenbeek               │
│  Neder-Over-Heembeek · Schaerbeek                   │
│  Watermael-Boitsfort & Auderghem                     │
│  Woluwe-St-Pierre & Woluwe-St-Lambert               │
│                                                      │
│  Wallonia                                            │
│  Liège (kidicalmassliege.org ↗) · Mons · Namur      │
│                                                      │
│  [Flanders group — hidden until active]              │
│                                                      │
├──────────────────────────────────────────────────────┤
│  [distinct background]                               │
│                                                      │
│  Don't see your city?                                │
│  New chapters keep joining. If your city isn't on    │
│  the map yet, you could be the one to start it.      │
│  We'll support you.                                  │
│                                                      │
│  [ Find out how → ]                                  │
│  Questions? Email the coordination team →            │
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

- **Header subtitle:** Dynamic chapter count from database. "16 active groups across Belgium" — updates automatically as new chapters join.
- **Map:** Leaflet/Mapbox, clean map style (not satellite). Pin touch target minimum 44×44px. Brussels cluster expands on tap to show individual municipality pins. Liège opens external URL in new tab with a visual indicator (↗).
- **Chapter list:** Plain text list, no images or per-chapter event data. Each entry is a link. Multi-municipality chapters show combined name and hyphenated postal codes: "Woluwe-Saint-Pierre & Saint-Lambert (1150–1200)".
- **Regional order:** Brussels → Wallonia → Flanders (order of establishment). Flanders section hidden when empty.
- **"Start a chapter" CTA:** Distinct background section at the bottom. Short, warm, inviting. Email CTA links to /help-out anchor or mailto — consistent with Help Out page.

---

## Open Questions / Necessary Refinements

1. **Complete chapter list:** The wireframe above shows approximate chapters based on the raw homepage. The exact list (all active chapters, their names, postal codes, and multi-municipality pairings) needs to come from Leticia/Nico before build. The organigram on the current site mentions "14 groups" — the number may have grown.
2. **Map implementation:** Leaflet vs. Mapbox confirmed ✅ in existing spec. Confirm with Nico which library is in use and whether chapter coordinates are stored in the database.
3. **Brussels cluster behaviour:** When a user taps the Brussels cluster pin, do they see all individual Brussels pins on the map, or are they taken to a "Brussels chapters" filtered list? Proposed: expand on the map to show individual pins. Confirm UX approach.
4. **Liège external link handling:** Liège appears as a regular pin linking to kidicalmassliege.org. Should the list entry also have a visual ↗ indicator to signal it's external? Proposed: yes, for accessibility.
5. **Chapter name display language:** On the NL route, should chapter names appear in Dutch (e.g., "Schaerbeek" = "Schaarbeek")? On FR, in French? Or are postal-code-based names neutral? Proposed: use the bilingual official name where it exists (e.g., "Ixelles – Elsene") consistently across all language routes.

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
│  [2-sentence warm pitch]                             │
│                                                      │
│  Name ___________________________                    │
│  Email __________________________                    │
│  Message (optional) ______________                   │
│                    [ Send → ]                        │
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
│  [pitch — 2 lines]   │
│                      │
│  Name _____________  │
│  Email _____________ │
│  Message (opt.) ___  │
│  [ Send → ]          │
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
- **Volunteer form:** 3 fields only (name, email, optional message). Short by design — low friction. "Send →" on submit shows inline confirmation. Routes to chapter lead email.
- **Local partners:** Logo + name + optional external link. Populated by chapter lead in admin. Hidden when empty.
- **Press coverage:** Outlet + headline + date + link. Structured list, not cards. Hidden when empty. Items automatically surface on /about/press.
- **Downloads:** File name + format + download button. Chapter-specific flyers. Hidden when empty.

---

## Open Questions / Necessary Refinements

1. **Team photos:** Are team member photos required or optional? Proposed: optional. If no photo uploaded, show initials avatar or a generic silhouette placeholder. Confirm with Nico.
2. **Brussels bilingual toggle:** On a Brussels chapter page with the NL/FR toggle, does switching language change the URL (e.g., `/nl/chapters/1030` vs `/fr/chapters/1030`) or is it a client-side toggle that doesn't update the URL? URL-based routing is preferred for shareability. Confirm with Nico.
3. **Empty chapter page state:** A chapter lead might create a chapter page in admin but not add any team members, partners, or downloads. The result is a page with only an events section (or empty events). Is this acceptable at launch? Proposed: acceptable, with the empty sections hidden. An admin nudge could encourage completing the page.
4. **Volunteer form — confirmation state:** Same question as Help Out page. What does the inline confirmation message say? Copy needed.
5. **Multi-municipality chapter header:** For chapters spanning multiple municipalities (e.g., Woluwe-St-Pierre & St-Lambert), how does the header display? Proposed: "Woluwe-Saint-Pierre & Saint-Lambert" as the primary name, "1150–1200" as the postal code line. Long names may wrap on mobile — confirm typography handles this.
6. **Press coverage aggregation:** The spec says chapter press items automatically surface on /about/press. Confirm with Nico this is implemented at the data model level — every press item entry has a chapter_id field that the press page queries.

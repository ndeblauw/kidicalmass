---
title: Patterns Library
tags: [design, patterns]
sources: [wiki/design/30-skeleton]
phase: design
updated: 2026-06-18
---

# Patterns Library

Shared UI patterns with stable IDs. Per-page briefs in [`30-skeleton/`](30-skeleton/) should **reference a pattern by ID** rather than re-specify it. First pass — extracted from the existing 7 page specs; refine as build reveals real components.

> Naming: `PAT-n`. IDs are stable. When a page deviates from a pattern, the page brief states the delta, not a new copy of the spec.

| ID | Pattern | What it is | Used on | Notes / source of truth |
|---|---|---|---|---|
| **PAT-1** | **Event card (compact)** | Date · time · chapter · meeting point · age/distance chips; whole card links to the event. | Events list, Home events strip, Chapter schedule | Canonical field set: [`30-skeleton/events-overview-content.md`](30-skeleton/events-overview-content.md). Chapter pages reuse it verbatim. |
| **PAT-2** | **Split hero** | Image one side, headline + primary CTA the other; mobile stacks image-over-text. | Home, About/Mission, Activity detail | Primary CTA varies by page (see PAT-9). |
| **PAT-3** | **Practical strip** | Horizontal row of practical facts (date, time, meeting point, distance, duration, age, cost). | Activity detail; condensed on Event card | [`30-skeleton/activity-detail.md`](30-skeleton/activity-detail.md). |
| **PAT-4** | **Stats bar** | Row of movement numbers. Distinguish *dynamic momentum* (this season) from *cumulative impact* (all-time). | Home, About/Mission | Must be live data, never hardcoded (audit lesson). |
| **PAT-5** | **Partner logo bar** | Two scopes: (a) a **slim recognition strip** site-wide above the footer — "Mede mogelijk gemaakt door" + muted **national** logos + one link to `/about/partners`; **recognition only**. (b) the **full grouped bar** on the Partners page (categories + "Ook ondersteund door" + "partner worden"). | (a) Global, above footer · (b) About/Partners; also Activity detail (event partners), Chapter (local partners) | Reworked 2026-06-03 (Frederik): the global band was reduced to (a); acquisition + supporters list + categories moved to (b). **National vs local partners are different data** ([content model](20-structure.md)) — strip (a) is national-scoped (`group_id IS NULL`); chapter-local partners surface on their chapter page. Brief: [`30-skeleton/partners.md`](30-skeleton/partners.md). |
| **PAT-6** | **Routed contact form** | Name · email · role · chapter → routed to the chapter lead, not a central inbox. | Help out, Chapter page (volunteer mini-form) | The J2 hand-off. [`30-skeleton/help-out.md`](30-skeleton/help-out.md). |
| **PAT-7** | **Role card** | Icon/illustration · role name · one-line "what you'd do" · honest commitment note. | Help out (5 roles) | Illustrations: 1 of 5 exist ([asset slots](61-asset-slots.md)). |
| **PAT-8** | **Chapter map** | Belgium map with chapter pins; Brussels clusters; Liège = external pin; Flanders hidden until active. | Home, Chapters overview | [`30-skeleton/chapters.md`](30-skeleton/chapters.md). |
| **PAT-9** | **Primary CTA button** | The page's single most important action. "Find a ride" (family pages) or "Steun Kidical Mass" (support page). | Global | Hierarchy: one primary per view; the support CTA never outranks "find a ride" on family pages. |
| **PAT-10** | **Persistent support CTA (nav + contextual + footer)** | The "Steun Kidical Mass" ask, present on every page via a **nav accent button** (**"♥ Steun ons"** with a heart icon — disambiguates from *Meehelpen*; top-right, replaces login; **mobile = pinned first item at the top of the hamburger menu**) + the **footer CTA**, plus **contextual "Steun" blocks** at warm moments (Home, end of event-detail). All link to `/steun-ons`. | Every page (nav + footer); Home + event-detail (contextual blocks) | Reworked 2026-06-02: prominence **elevated** from quiet-footer-only (Frederik). Stays warm, never transactional, and **every touchpoint reassures riding stays free** ([org goals trade-off](../strategy/10-organisation-goals.md); [`steun-ons.md`](30-skeleton/steun-ons.md)). |
| **PAT-11** | **Optional section (hide-if-empty)** | A section that renders only if a chapter lead filled it (team, local partners, press, downloads). | Chapter page | Keeps self-published pages clean without coordination-duo policing. |
| **PAT-12** | **Date-grouped list + upcoming/past toggle** | Events grouped by month; default upcoming, toggle to past. No pagination at current volume. | Events overview | [`30-skeleton/events-overview.md`](30-skeleton/events-overview.md). |
| **PAT-13** | **Featured badge** | Marks the Grande Kidical Mass within the normal Events system. | Events, Event card | Tied to open concern [`D-3`](01-concerns.md). |
| **PAT-14** | **FAQ accordion** | Expandable practical Q&A. | Getting Started | [`30-skeleton/getting-started.md`](30-skeleton/getting-started.md). |
| **PAT-15** | **Sub-page nav cards** | Cards linking into a section's children, in a story-first order. | About overview | Order justified in [`30-skeleton/about.md`](30-skeleton/about.md). |
| **PAT-16** | **Empty state** | Friendly on-brand message when no data (no upcoming rides, no chapter team yet). | Events, Chapters, Chapter page | Tone of voice applies. |
| **PAT-17** | **News preview / article feed** | List of recent articles with author + date. | Home (preview), About/News (feed) | [`30-skeleton/about.md`](30-skeleton/about.md). |
| ~~**PAT-18**~~ | ~~**"I'm coming" attendance**~~ | **RETIRED** — per-event attendance cut (Alexandre/J3, 2026-06-05; [`D-1`](01-concerns.md)). Volunteers confirm turnout via WhatsApp polls; no site toggle. Replaced by PAT-19. | — | Do not build. The `Attendance` relation is dropped from the [content model](20-structure.md). |
| **PAT-19** | **Volunteer roster** | A standing **per-chapter** list of volunteers. **Opt-in public** entries show on the chapter page; the **full roster** is visible to **logged-in volunteers only**. Not per-event. | Chapter page (public, opted-in), back-office / [My activities](30-skeleton/my-activities.md) (full) | Backed by `group_user.is_public` — **no** volunteer↔activity relation ([content model](20-structure.md)). New (D-1, Alexandre/J3). |
| **PAT-20** | **Photo-collage media** | An organic collage of 2–3 photos scattered on a square stage at slight angles, optional fixed brand doodle anchored in a corner, with a staggered "settle" entrance. Standalone, or inside scroll-sequence where beats crossfade. | Help out (`.ho-deal`, two beats); reuse candidates: chapter pages, About | Source of truth + reuse/extraction recipe: [`specs/2026-06-18-photo-collage-media-design.md`](../../superpowers/specs/2026-06-18-photo-collage-media-design.md). Custom-property scatter (`--ho-photo-x/y/w/r`); **extract to a component on the 2nd page that uses it**. |

## Gaps / to confirm

- PAT-7 role illustrations: 4 of 5 missing ([asset slots](61-asset-slots.md)).
- PAT-2 hero on Home wants video; no footage exists yet (asset gap).
- Visual tokens are live in [`app.css`](../../../resources/css/app.css) `@theme`, documented in [`DESIGN.md`](../../../DESIGN.md) (`D-4` closed). Patterns should pull colour/type from tokens, not per-page values.

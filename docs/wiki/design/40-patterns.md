---
title: Patterns Library
tags: [design, patterns]
sources: [wiki/design/30-skeleton]
phase: design
updated: 2026-06-01
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
| **PAT-5** | **Partner logo bar** | Grid/row of partner logos, grouped by category; links to `/about/partners`. | Home, About/Partners, Activity detail (event partners), Chapter (local partners) | National vs local partners are different data ([content model](20-structure.md)). |
| **PAT-6** | **Routed contact form** | Name · email · role · chapter → routed to the chapter lead, not a central inbox. | Help out, Chapter page (volunteer mini-form) | The J2 hand-off. [`30-skeleton/help-out.md`](30-skeleton/help-out.md). |
| **PAT-7** | **Role card** | Icon/illustration · role name · one-line "what you'd do" · honest commitment note. | Help out (5 roles) | Illustrations: 1 of 5 exist ([asset slots](61-asset-slots.md)). |
| **PAT-8** | **Chapter map** | Belgium map with chapter pins; Brussels clusters; Liège = external pin; Flanders hidden until active. | Home, Chapters overview | [`30-skeleton/chapters.md`](30-skeleton/chapters.md). |
| **PAT-9** | **Primary CTA button** | The page's single most important action. "Find a ride" (family pages) or "Become a member" (membership). | Global | Hierarchy: one primary per view; membership CTA never outranks "find a ride" on family pages. |
| **PAT-10** | **Persistent membership footer CTA** | Quiet, always-present "become a member / donate" in the global footer. | Every page | Top org goal, but must not make the site feel transactional ([org goals trade-off](../strategy/10-organisation-goals.md)). |
| **PAT-11** | **Optional section (hide-if-empty)** | A section that renders only if a chapter lead filled it (team, local partners, press, downloads). | Chapter page | Keeps self-published pages clean without coordination-duo policing. |
| **PAT-12** | **Date-grouped list + upcoming/past toggle** | Events grouped by month; default upcoming, toggle to past. No pagination at current volume. | Events overview | [`30-skeleton/events-overview.md`](30-skeleton/events-overview.md). |
| **PAT-13** | **Featured badge** | Marks the Grande Kidical Mass within the normal Events system. | Events, Event card | Tied to open concern [`D-3`](01-concerns.md). |
| **PAT-14** | **FAQ accordion** | Expandable practical Q&A. | Getting Started | [`30-skeleton/getting-started.md`](30-skeleton/getting-started.md). |
| **PAT-15** | **Sub-page nav cards** | Cards linking into a section's children, in a story-first order. | About overview | Order justified in [`30-skeleton/about.md`](30-skeleton/about.md). |
| **PAT-16** | **Empty state** | Friendly on-brand message when no data (no upcoming rides, no chapter team yet). | Events, Chapters, Chapter page | Tone of voice applies. |
| **PAT-17** | **News preview / article feed** | List of recent articles with author + date. | Home (preview), About/News (feed) | [`30-skeleton/about.md`](30-skeleton/about.md). |
| **PAT-18** | **"I'm coming" attendance** | Per-activity toggle for a logged-in volunteer; **display shows the hosts/organisers attending (social nudge), not the full attendee list**. | Activity detail, [My activities](30-skeleton/my-activities.md) | **Account-only, volunteers only**, all activity types. Lead may see the full roster. Needs the Attendance relation ([content model](20-structure.md)). |

## Gaps / to confirm

- PAT-7 role illustrations: 4 of 5 missing ([asset slots](61-asset-slots.md)).
- PAT-2 hero on Home wants video; no footage exists yet (asset gap).
- Patterns have no visual tokens yet — see [Surface](50-surface.md) / `D-4`.

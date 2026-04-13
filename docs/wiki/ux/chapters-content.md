---
title: Chapters — Content
tags: [content]
sources: [ux/chapters.md]
updated: 2026-04-13
---

*Content companion to [chapters.md](chapters.md). Covers both the Chapters Overview page and the Chapter Page Template. All copy in English. FR/NL notes inline.*

---

# PART 1: Chapters Overview

## Page header

**H1:** Chapters

**Subtitle (dynamic):** [N] active groups across Belgium

*Note: number is pulled from the database. At time of writing, approximately 16 active chapters.*

---

## Map section (labels + tooltips only)

**Map pin tooltip — standard chapter:**
[Municipality name]

**Map pin tooltip — Brussels cluster (before expansion):**
Brussels — [N] chapters. Tap to explore.

**Map pin tooltip — Liège (external):**
Liège — kidicalmassliege.org ↗

*Note: Liège pin opens kidicalmassliege.org in a new tab. Visual ↗ indicator on both the pin tooltip and the list entry signals external link.*

---

## Chapter list (full list, regional grouping)

**Section heading:** Find your chapter

*Introductory line (optional, before the list):*
Click any chapter to see upcoming rides, meet the local team, and get in touch.

---

### Brussels

*Displayed as a linked list. Each entry links to /chapters/[postal-code].*

- [Anderlecht](../chapters/anderlecht)
- [Berchem-Sainte-Agathe / Sint-Agatha-Berchem](../chapters/berchem-sainte-agathe)
- [Bruxelles-Ville / Brussel-Stad](../chapters/bruxelles-ville)
- [Etterbeek](../chapters/etterbeek)
- [Evere – Haren](../chapters/evere-haren)
- [Forest – Vorst](../chapters/forest-vorst)
- [Ixelles – Elsene](../chapters/ixelles-elsene)
- [Jette](../chapters/jette)
- [Molenbeek](../chapters/molenbeek)
- [Neder-Over-Heembeek](../chapters/neder-over-heembeek)
- [Schaerbeek / Schaarbeek](../chapters/schaerbeek)
- [Watermael-Boitsfort – Watermaal-Bosvoorde & Auderghem – Oudergem](../chapters/watermael-boitsfort-auderghem)
- [Woluwe-Saint-Pierre & Woluwe-Saint-Lambert / Woluwe-Sint-Pieters & Woluwe-Sint-Lambrechts](../chapters/woluwe)

*Implementation note: bilingual official names shown throughout all language routes. Multi-municipality chapters show combined name and hyphenated postal codes in the chapter page header.*

---

### Wallonia

- [Liège](https://kidicalmassliege.org) ↗ *(external site)*
- [Mons](../chapters/mons)
- [Namur](../chapters/namur)

---

### Flanders

*This section is hidden until at least one Flemish chapter is active. No placeholder text shown to visitors.*

---

## Start a chapter section

*Displayed on a distinct background at the bottom of the page.*

**Heading:** Don't see your city?

**Body:**
New chapters keep joining. If your municipality isn't on the map yet, you could be the one to start it. You don't need to be a cycling expert — just someone who loves your neighbourhood and thinks kids should be able to get around it safely. We'll support you every step of the way.

**Primary CTA:** Find out how →
*Links to /help-out#start-a-chapter*

**Secondary CTA:** Questions? Get in touch with the coordination team →
*Links to the coordination team email or contact page — confirm with Nico.*

---

## Meta

**Page title (browser tab):** Chapters — Kidical Mass Belgium
**Meta description:** Find your local Kidical Mass chapter. [N] active groups organising monthly family bike rides across Belgium.

---
---

# PART 2: Chapter Page Template

*All copy below uses [Municipality] and [Postal code] as variables. [Municipality] = the full bilingual official name where applicable (e.g. "Ixelles – Elsene"). Brussels-only UI elements noted.*

---

## Chapter header

**Breadcrumb:** ← All chapters
*Links to /chapters.*

**H1:** [Municipality]

**Postal code line:** [Postal code]

**Language toggle (Brussels chapters only):** NL | FR
*Client-side or URL-based toggle — confirm with Nico. Proposed: URL-based (/nl/chapters/[code] and /fr/chapters/[code]) for shareability.*

---

## Upcoming events section

**Section heading:** Upcoming rides in [Municipality]

*Events are auto-populated from the database — same compact card as /events.*

**Empty state (no upcoming events):**
No upcoming rides in [Municipality] right now. Head to [all rides across Belgium](/events) to find one near you — or check back soon.

**Past events link (below the event cards):**
Past rides →
*Links to /events filtered by this chapter's location.*

---

## Organised by section (team + volunteer form)

*This entire section is hidden if no team members have been added in admin.*

**Section heading:** Organised by

*Team members displayed as: photo (optional) · Name · Role. If no photo uploaded, show initials avatar.*

---

**Volunteer form sub-heading:** Want to help in [Municipality]?

**Volunteer pitch (2 sentences):**
The [Municipality] rides happen because a small group of neighbours make them happen — before work, on weekends, with kids in tow. If you want to be part of that, we'd love to hear from you.

**Form fields:**

| Field | Label | Placeholder | Required |
|---|---|---|---|
| Name | Your name | — | Yes |
| Email | Your email | — | Yes |
| Message | Message (optional) | Anything you'd like us to know | No |

**Submit button:** Send →

**Form confirmation message (inline, replaces form on submit):**
Thanks — we'll be in touch soon. In the meantime, feel free to join our next ride and introduce yourself.

*Routes to the chapter lead's email address.*

**Secondary link below the form:** More about volunteering →
*Links to /help-out.*

---

## Local partners section

*Hidden entirely when empty.*

**Section heading:** Local partners

*Displayed as: logo + name + optional external link. Populated by the chapter lead in admin.*

---

## Press coverage section

*Hidden entirely when empty.*

**Section heading:** In the press

*Displayed as a structured list: Outlet · "Headline" · Date · ↗ link.*

*Implementation note: each press item has a chapter_id field. Items automatically surface on /about/press — confirm with Nico this is built at the data model level.*

---

## Downloads section

*Hidden entirely when empty.*

**Section heading:** Downloads

*Displayed as: file name + format badge + download button.*

**Download button label:** Download ↓

*Chapter-specific flyers and documents. Populated by the chapter lead in admin.*

---

## Meta (template)

**Page title (browser tab):** [Municipality] — Kidical Mass Belgium
**Meta description:** Kidical Mass [Municipality] organises monthly family bike rides through the neighbourhood. Find upcoming rides, meet the team, and get involved.

*For multi-municipality chapters, use the primary municipality name in the meta description: "Kidical Mass Woluwe-Saint-Pierre & Saint-Lambert organises…"*

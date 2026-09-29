---
title: Home
tags: []
sources: [notion, raw/website/index.md]
phase: design
updated: 2026-09-29
---

Status: ✅ Complete. Page URL: `/` (trilingual: `/nl/`, `/fr/`, `/en/`)

**Summary:** The homepage converts curious visitors into event-goers. Dual CTAs in the hero serve two genuinely different audiences: ready families (→ /events) and curious first-timers (→ /getting-started). The events strip is the most functional element. The chapter map = proof of scale, not navigation. Stats are dynamic and use the same figures as /steun-ons and About.

The current Home structure is in `docs/superpowers/specs/2026-06-06-home-ux-design.md`; where this page disagrees, the spec wins.

---

## Strategy

The front door of kidicalmass.be. Most visitors arrive via social media share, a Google search for "Kidical Mass [their city]", or word of mouth from another parent. The homepage must do two things fast: make the movement feel alive and joyful, and route each person to the right next step.

### Who arrives and in what mental state

**Families — first-timers**
Arrive curious, slightly uncertain. They've probably heard about it from a friend or seen photos on Instagram. They assume it's for them but haven't confirmed. The question forming is: "Is the next one near me? Is it soon?" They're not skeptical — they just need practical clarity fast. No fear barrier to overcome; mild logistics anxiety.

**Families — returning**
Arrive efficiently. They already love it. They just want the next date. The homepage is a shortcut to /events for them. They'll scan the events strip first.

**Potential volunteers**
Often former or current ride participants who had a great time and thought "I'd like to be part of the team." They arrive warm and inspired. The subtle volunteer CTA is the right register — not a recruitment pitch, just an invitation.

**Potential chapter leads / grant reviewers**
Arrive deliberately. They want to understand the movement's scale and credibility. The map and stats are for them. They may not click any CTA — they're assessing.

### What the page must feel like

Alive. Children on bikes, colour, music implied. The hero image or video carries this entirely — the rest of the page supports. The visitor should feel "this is happening right now, near me" within 5 seconds.

### Organisational objectives

Make the movement's scale legible while keeping the feel scrappy and community-first — not institutional.

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
- Subtle volunteer CTA (secondary path to /help-out)

**Out of scope:**
- Full calendar (that's /events)
- Full chapter directory (that's /chapters)
- Volunteer recruitment form (that's /help-out)
- Spacefunding / donation CTA (current site has this — removed for new site)

---

## Structure

Linear scroll, single-column. Story: what is this → next ride → how big is the movement → how to get involved.

**Section flow:**
1. Hero (identity + dual CTA)
2. Upcoming events strip (next 3 rides)
3. Chapter map (national reach at a glance)
4. Movement stats bar
5. Volunteer CTA strip (subtle)
6. News preview
7. Partners bar

**Key links out:**
- Hero primary CTA → /events
- Hero secondary CTA → /getting-started
- Event cards → /events/[slug]
- "See all rides" → /events
- "See all chapters" → /chapters
- Volunteer CTA → /help-out
- News cards → /about/news/[slug]
- Partners → /about/partners

---

## Skeleton

**Dual CTA:** Primary "Find a ride" (→ /events) and secondary "New here? Start here" (→ /getting-started). The two audiences — ready families and curious first-timers — have genuinely different needs; one CTA would leave one stranded. ✅ Decided.

**Events strip:** 3 live events pulled from the Events database, same card component as /events. Always database-driven — no hardcoded events.

**Chapter map:** On the homepage it serves as proof of scale (national reach at a glance), not a discovery directory. ✅ Decided.
It is a static server-rendered map: one dot per local group, all Brussels groups as one counted bubble, no links. The interactive map lives on Chapters.

**Stats:** Local groups, parades in the reference year and participants in the reference year. ✅ Decided.

**Stats source ✅:** Home, /steun-ons and About read the same figure definitions (`App\Support\ImpactFigures`), so they cannot contradict each other.
The reference year is the latest curated year figure, with last year as fallback. A figure without a value is left out, never shown as 0.

**Partners bar scope ✅:** The homepage partners bar shows institutional and movement-ally partners only (Bruxelles Mobilité, Clean Cities Campaign, Ville de Bruxelles, Commune de Schaerbeek). Operational/in-kind partners (Loopz, Kidical Mouse) do NOT appear here — they live on /about/partners and /getting-started.

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ LOGO                Events Chapters Getting Started  │
│                     Help out  About ▾                │
├──────────────────────────────────────────────────────┤
│                                                      │
│    [ HERO — full-width photo or looping video ]      │
│    Children on bikes · colour · music · Belgian city │
│                                                      │
│    Kids on bikes. Together.                          │
│    Every month, hundreds of children ride through    │
│    Belgian streets — safely, together, with music.   │
│    Free for everyone.                                │
│                                                      │
│    [ Find a ride → ]   New here? Start here →        │
│                                                      │
├──────────────────────────────────────────────────────┤
│  Upcoming rides                          See all →   │
│                                                      │
│  ┌──────────────┐  ┌──────────────┐  ┌────────────┐ │
│  │ Sat 19 Apr   │  │ Sat 26 Apr   │  │ Sun 3 May  │ │
│  │ 15:00        │  │ 15:00        │  │ 14:30      │ │
│  │ Evere–Haren  │  │ Forest–Vorst │  │ Heembeek   │ │
│  │ Place de la  │  │ Place St-    │  │ Rue...     │ │
│  │ Mairie       │  │ Denis        │  │            │ │
│  └──────────────┘  └──────────────┘  └────────────┘ │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Active across Belgium            See all chapters → │
│                                                      │
│  [ MAP — outlined Belgium with coloured chapter pins]│
│  [ Brussels: one counted bubble · static, no links ] │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│   27 groups · 62 parades in 2025 · 5.500 riders      │
│                                                      │
├──────────────────────────────────────────────────────┤
│  Want to help make rides happen?       Help out →    │
├──────────────────────────────────────────────────────┤
│  News                                                │
│                                                      │
│  ┌─────────────────────────┐  ┌───────────────────┐  │
│  │ Article title            │  │ Article title     │  │
│  │ 12 March 2026 · excerpt  │  │ 5 Jan 2026 · ...  │  │
│  └─────────────────────────┘  └───────────────────┘  │
│                                                      │
├──────────────────────────────────────────────────────┤
│  [Bruxelles Mobilité] [Clean Cities] [Ville BXL]     │
│  [Schaerbeek]                  → /about/partners     │
├──────────────────────────────────────────────────────┤
│ FOOTER                                               │
└──────────────────────────────────────────────────────┘
```

### Mobile

```
┌──────────────────────┐
│ LOGO     [≡ Menu]    │
├──────────────────────┤
│ [ HERO — photo/video]│
│                      │
│  Kids on bikes.      │
│  Together.           │
│                      │
│  Every month,        │
│  hundreds of children│
│  ride through        │
│  Belgian streets.    │
│  Free for everyone.  │
│                      │
│  [ Find a ride →  ]  │
│  New here? Start →   │
├──────────────────────┤
│  Upcoming rides      │
│                      │
│ ┌──────────────────┐ │
│ │ Sat 19 Apr 15:00 │ │
│ │ Evere – Haren    │ │
│ │ Place de la...   │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ Sat 26 Apr 15:00 │ │
│ │ Forest – Vorst   │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ Sun 3 May 14:30  │ │
│ │ Heembeek         │ │
│ └──────────────────┘ │
│       See all →      │
├──────────────────────┤
│  Active across       │
│  Belgium             │
│ [ MAP — Belgium ]    │
│  See all chapters →  │
├──────────────────────┤
│  27 groups · 62      │
│  parades · 5.500     │
├──────────────────────┤
│  Want to help?       │
│  Help out →          │
├──────────────────────┤
│  News                │
│ ┌──────────────────┐ │
│ │ Article title    │ │
│ │ Date · excerpt   │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ Article title    │ │
│ │ Date · excerpt   │ │
│ └──────────────────┘ │
├──────────────────────┤
│ [BM] [CC] [VB] [CS]  │
├──────────────────────┤
│ FOOTER               │
└──────────────────────┘
```

### Annotations

- **Hero:** Full-width joyful photo or short looping video — children on bikes in a city street, colourful, clearly Belgian. No text overlay needed beyond the headline. Language determined by URL route — no FR/NL stacking.
- **Primary CTA:** Solid button, highest visual weight. Leads families directly to event discovery.
- **Secondary CTA:** Text link (not a button). Lower visual weight. First-timers who need orientation before committing.
- **Events strip:** 3 cards, database-driven, same compact card component as /events. Off-season empty state: "No rides right now — the season runs from March to November."
- **Chapter map:** Impressionistic scale, not a navigation tool. Static: Brussels is one counted bubble, markers have no links.
- **Stats bar:** 3 stats: local groups, parades and participants in the reference year. Dynamic. Same figures as /steun-ons and About.
- **Volunteer CTA:** Single line + link. Not a section — a nudge. Appears between stats and news to catch motivated visitors on their way down.
- **News preview:** Hidden entirely when the news feed is empty. Never shows empty cards.
- **Partners bar:** Logo strip, institutional and movement-ally only.

---

## Open Questions / Necessary Refinements

1. **Hero visual:** Photo vs. looping video — video requires a dedicated asset. Confirm with Leticia whether a high-quality looping video is available or should be produced. Fallback = strong photo.
2. **Volunteer CTA copy:** "Want to help make rides happen?" is a working example. Needs a final pass against the ToV guide — does it pass the one-line test?
3. **Off-season behaviour:** The "No rides right now" empty state is decided ✅. Confirm the exact season window (March–November) with Leticia before hardcoding in copy.
4. **Homepage stats: data source ✅:** Visible local groups, published parades in the reference year, and the curated participant count for that year (`App\Support\ImpactFigures`).
5. **News section:** Hidden when empty ✅ — but at launch, is there at least 1 published article? If not, the news preview section disappears. Confirm content readiness before build.

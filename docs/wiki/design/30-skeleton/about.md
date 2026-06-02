---
title: About Section
tags: []
sources: [notion, raw/website/le-projet-het-project.md, raw/website/organisation.md, raw/website/what-we-want.md, raw/website/nos-revendications-onze-aanbevelingen.md, raw/website/press.md, raw/website/my-blog.md]
phase: design
updated: 2026-04-13
---

Covers all pages in the About section: Mission, Overview, Organisation, Vision, News, Press, Partners.

**Sub-page order ✅:** Mission → Vision → Organisation → News → Press → Partners (story-first logic: why → what we stand for → how we work → content/credibility layers)

**About in the nav ✅:** Dropdown on desktop showing all 6 sub-pages. On mobile: About links directly to /about overview.

---

# About / Mission ← NEW — full spec

Status: ✅ Complete. Page URL: `/about/mission` · `/over/missie` · `/a-propos/mission`

The Mission page was not specced previously. This is the first About sub-page in the nav and the one that grounds everything else.

---

## Strategy

### Who arrives and in what mental state

Mission is not a family page. Families don't click "About" → "Mission" to decide whether to attend a ride — they go to Events or Getting Started. The people who read the Mission page arrive with a deliberate, evaluative intent:

**Grant reviewers / funders**
Looking for: what does this organisation do, how big is it, what impact has it had? They need structured proof points — stats, scope, track record — alongside a sense that this is a credible, serious initiative. But Kidical Mass's strength is that it's warm and citizen-led, not institutional. The page must carry both.

**Press / journalists**
Looking for: the story. What's the angle? What are the numbers? Who are these people? They want a quick orienting read before they reach out. The Mission page should be quotable — concrete enough for a journalist to lift a fact or a line.

**Potential chapter leads**
Looking for: is this a movement I want to join? Is it my people? They need the "why" before they commit. The movement's values, its origin story, and its inclusive character are the signals they need.

**Partners / institutions**
Looking for: values alignment, scale, credibility. The 3 axes and the stats are what they're scanning for.

**Curious families going deeper**
After finding a ride and attending one, some families want to understand the bigger picture. They arrive warm and proud to be part of something larger. The Mission page should reward that curiosity — make them feel even more part of something meaningful.

### What the page must feel like

Warm and proud, like someone talking about something they built and believe in. Not a grant application. Not an NGO's annual report. The stats should feel like a celebration, not a performance indicator.

The "3 axes" framework from the current site (Start, Support, Spread) is useful structure but the language needs rewriting for the new ToV. "Rayonner" and "Accompagner" are internal movement terms — the page should speak to outsiders.

### Tone

Confident, grounded, community-first. A notch more serious than event pages, but still human. See the "Mission / About" row in the ToV context table.

---

## Scope

**Must have:**
- Movement description (what Kidical Mass is and does — national scope, not Brussels-only)
- 3 mission axes — rewritten in ToV language (Start, Support, Spread)
- Impact stats (150 parades, 5,500+ participants, 120 volunteers, 16+ communities — manually maintained)
- Inclusivity dimension (no bike, no experience — all welcome)
- CTA block (join / help out)

**Should have:**
- A parent quote or two (Julienne, Fatima, Camille — confirmed consented)
- Photo (ride photo, community, children)

**Out of scope:**
- Policy demands (that's /about/vision)
- Governance and org structure (that's /about/organisation)
- Local chapter content (that's /chapters)
- Financial statements or legal entity details

---

## Structure

**Section flow:**
1. Page header (H1 + subtitle)
2. Movement description — what Kidical Mass is
3. Three mission axes (visual grid)
4. Impact stats bar
5. Inclusivity section
6. Parent quote (optional pull-quote)
7. CTA block

---

## Skeleton

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  Mission                                             │
│  We organise joyful, safe bike parades for children  │
│  across Belgium — and we're just getting started.    │
├──────────────────────────────────────────────────────┤
│                                                      │
│  [ride photo — full width · children on bikes,       │
│   colour, music, city street · joyful]               │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Kidical Mass Belgium is a national network of local │
│  groups organising festive, safe, child-paced bike   │
│  parades across Belgium. We started in Brussels in   │
│  2020. Today we're active in 16+ communities across  │
│  Brussels, Wallonia, and Flanders — and growing.     │
│                                                      │
│  Every parade has music along the route. We ride at  │
│  the pace of the youngest child, on carefully chosen │
│  routes, with trained volunteers in pink vests.      │
│  Kidical Mass is a way to discover your neighbourhood│
│  together, make new friends, and build confidence    │
│  on a bike. For the kids — and often for the parents.│
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Three things we do                                  │
│                                                      │
│  ┌────────────────┐  ┌────────────────┐  ┌────────┐  │
│  │ Get on         │  │ Build          │  │ Spread │  │
│  │ the bike       │  │ confidence     │  │ the    │  │
│  │                │  │                │  │ word   │  │
│  │ Regular rides  │  │ Ride alongside │  │ Growing│  │
│  │ for beginners  │  │ novice cyclists│  │ a local│  │
│  │ and families.  │  │ in real        │  │ cycling│  │
│  │ Safe, slow,    │  │ traffic — with │  │ culture│  │
│  │ joyful.        │  │ support.       │  │ in each│  │
│  │                │  │                │  │ commune│  │
│  └────────────────┘  └────────────────┘  └────────┘  │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│   150          ·   5,500+      ·   120      ·  16+   │
│   parades          participants    volunteers   communities │
│   since 2020       in 2024         active       across BE  │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Everyone is welcome                                 │
│                                                      │
│  You don't need to be a confident cyclist. You       │
│  don't need a bike. You don't need to be from the    │
│  neighbourhood. Kidical Mass is designed to reflect  │
│  the full diversity of each commune — and to lower   │
│  every barrier that might stop a family from showing │
│  up.                                                 │
│                                                      │
│  No bike? → Getting Started                          │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  "Mon fils veut toujours aller loin, sur la route.   │
│   Je pense que c'est cette idée d'aller loin, de     │
│   découvrir autre chose."                            │
│  — Julienne, maman de deux enfants                   │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Join the movement                                   │
│  [ Find a ride → ]         [ Help out → ]           │
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
│  Mission             │
│  We organise joyful, │
│  safe bike parades   │
│  for children across │
│  Belgium.            │
├──────────────────────┤
│ [ride photo fw]      │
├──────────────────────┤
│  Kidical Mass        │
│  Belgium is a        │
│  national network... │
│  [2 paragraphs]      │
├──────────────────────┤
│  Three things we do  │
│                      │
│ ┌──────────────────┐ │
│ │ Get on the bike  │ │
│ │ Regular rides... │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ Build confidence │ │
│ │ Ride alongside...│ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ Spread the word  │ │
│ │ Growing a local  │ │
│ │ cycling culture  │ │
│ └──────────────────┘ │
├──────────────────────┤
│  150 parades         │
│  5,500+ participants │
│  120 volunteers      │
│  16+ communities     │
├──────────────────────┤
│  Everyone is welcome │
│  [2 sentences]       │
│  No bike? →          │
├──────────────────────┤
│  "Mon fils veut      │
│   toujours aller     │
│   loin..."           │
│  — Julienne          │
├──────────────────────┤
│  Join the movement   │
│  [ Find a ride → ]   │
│  [ Help out → ]      │
├──────────────────────┤
│ FOOTER               │
└──────────────────────┘
```

### Annotations

- **Subtitle:** "We organise joyful, safe bike parades for children across Belgium — and we're just getting started." Confident, present tense, growth signal. Not institutional.
- **Hero photo:** Full-width, after the header. Should show children on bikes in a real Belgian city street — not a stock photo. The movement's own photography is its best asset.
- **Movement description:** 2 paragraphs. First grounds the who/what/where/when. Second gives the sensory experience. Both written per ToV guide — vivid, local, warm.
- **Three axes:** Visual 3-column grid on desktop, stacked cards on mobile. Names rewritten from the current site's "Débuter / Accompagner / Rayonner" to plain language English/FR/NL equivalents. Each card has a short heading + 2–3 sentence description.
- **Stats bar:** 4 stats, manually maintained (not database-driven). Different from the homepage stats which are dynamic. Displayed as large numbers with small labels — clean and minimal. "since 2020" qualifier on the parades stat.
- **Inclusivity section:** 2–3 sentences. Not a list. Ends with a link to /getting-started (anchored to the "Don't have a bike?" section).
- **Parent quote:** 1 quote from the raw site. Julienne's quote about her son wanting to go far. Confirmed consented ✅. Pull-quote style — large text, attributed.
- **CTA block:** Two equal-weight buttons. "Find a ride" leads to /events. "Help out" leads to /help-out.

---

## Open Questions / Necessary Refinements

1. **Stats currency:** The raw site stats (150 parades, 5500 participants, 120 volunteers, 16+ communities) reference 2024 data. These need to be updated to the most current figures before launch. Who owns this update? Coordination duo. Flag as content dependency.
2. **National scope phrasing:** The current site sometimes defaults to Brussels framing ("16 communes Bruxelloises"). The new Mission page must explicitly be national from the first sentence. Confirm that all stats and descriptions apply to the full Belgian network, not just Brussels.
3. **Three axes naming:** The working titles (Get on the bike / Build confidence / Spread the word) are UX placeholder names. The actual headings need to be written in all three languages (NL/FR/EN) with ToV voice. Flag as copywriting task for the coordination duo.
4. **Photo asset:** The page needs a strong hero photo of children on bikes in a Belgian city. Confirm whether existing photography (from the raw site assets) is sufficient or whether new photography is needed.
5. **Parent quotes:** Julienne's quote is from the raw site (/what-we-want). Usage is confirmed consented ✅. Fatima and Camille quotes are also available. Propose using 1 quote on Mission and reserving others for Vision.

---

---

# About Overview

Page URL: `/about` · `/over` · `/a-propos`

Status: ✅ Complete.

---

## Strategy

### Who arrives and in what mental state

The About overview is a navigational hub. Most visitors arrive after clicking "About" in the main nav without a specific sub-page in mind — they're explorers, not seekers. They might be:
- A journalist scanning to understand the organisation
- A potential partner deciding whether to dig deeper
- A curious family member wanting to understand the movement they just joined
- A potential chapter lead assessing whether this is their people

All of them need the same thing: a quick orientation ("what's in this section?") and a clear next step ("where should I go?"). The page should not try to do everything — it routes people to the right sub-page.

The stat bar makes the page feel alive and substantive even if the visitor doesn't click anywhere.

---

## Scope

**Must have:**
- Short orienting subtitle
- 6 sub-section navigation cards (one per sub-page)
- Mini stat bar (2–3 stats)
- CTA block

**Out of scope:**
- Full content of any sub-page
- Feature-writing or long form copy

---

## Structure

**Section flow:**
1. Page header (H1 + elevator pitch subtitle)
2. Sub-section navigation cards (6 cards, 3×2 grid)
3. Mini stat bar
4. CTA block

---

## Skeleton

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  About                                               │
│  Kidical Mass organises family bike parades across   │
│  Belgium and advocates for child-friendly cities.    │
│  A volunteer-run federated network.                  │
├──────────────────────────────────────────────────────┤
│                                                      │
│  ┌────────────────┐  ┌────────────────┐  ┌────────┐  │
│  │ 🎯 Mission      │  │ 👁 Vision       │  │ 🏛 Org │  │
│  │ What we do and │  │ What we stand  │  │ How we │  │
│  │ why we do it   │  │ for            │  │ work   │  │
│  └────────────────┘  └────────────────┘  └────────┘  │
│  ┌────────────────┐  ┌────────────────┐  ┌────────┐  │
│  │ 📰 News        │  │ 🎙 Press        │  │ 🤝 Part│  │
│  │ Updates from   │  │ Media coverage │  │ ners   │  │
│  │ the network    │  │ of the movement│  │ Who    │  │
│  │                │  │                │  │ supports│  │
│  └────────────────┘  └────────────────┘  └────────┘  │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│   16+ communities  ·  150 parades  ·  120 volunteers │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Want to get involved?                               │
│  [ Join a parade → ]       [ Help out → ]           │
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
│  About               │
│  Kidical Mass        │
│  organises family    │
│  bike parades across │
│  Belgium. Volunteer- │
│  run, federated.     │
├──────────────────────┤
│ ┌──────────────────┐ │
│ │ 🎯 Mission        │ │
│ │ What we do       │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 👁 Vision         │ │
│ │ What we stand for│ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 🏛 Organisation   │ │
│ │ How we work      │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 📰 News           │ │
│ │ Network updates  │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 🎙 Press          │ │
│ │ Media coverage   │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 🤝 Partners       │ │
│ │ Who supports us  │ │
│ └──────────────────┘ │
├──────────────────────┤
│  16+ communities     │
│  150 parades         │
│  120 volunteers      │
├──────────────────────┤
│  Want to get         │
│  involved?           │
│  [ Join a parade → ] │
│  [ Help out → ]      │
├──────────────────────┤
│ FOOTER               │
└──────────────────────┘
```

### Annotations

- **Subtitle:** Distinct from the Mission page intro — shorter, more navigational in tone. "Kidical Mass organises family bike parades across Belgium and advocates for child-friendly cities. A volunteer-run federated network." Not a mission statement — an orientation.
- **Sub-section cards:** 3×2 grid on desktop, stacked on mobile. Each card: icon + sub-page title + 1-line description. All 6 cards are clickable. Design: clean, equal weight, no visual hierarchy between them (all sub-pages matter).
- **Stat bar:** Same 3 stats as Mission page. Manually maintained. Reminder: keep these in sync with Mission page stats.
- **CTA block:** "Join a parade" → /chapters (not /events — finding the chapter is the first step for a new joiner). "Help out" → /help-out.

---

## Open Questions / Necessary Refinements

1. **Elevator pitch copy:** The subtitle above is a working draft. Needs review from the coordination duo — particularly: does "advocates for child-friendly cities" correctly represent the national scope (not just Brussels)?
2. **Stat bar sync:** The About overview and Mission page both show the same stats. Confirm they're from the same source and updated together. Risk: drift between the two pages if one is updated and the other isn't.

---

---

# About / Organisation

Page URL: `/about/organisation` · `/over/organisatie` · `/a-propos/organisation`

Status: ✅ Complete.

---

## Strategy

Explains how Kidical Mass is structured — federated, volunteer-driven, with a light coordination layer. Makes the open architecture legible to outsiders and potential joiners. Helps potential co-organisers understand how they'd fit in.

**Who reads this:** Potential chapter leads, institutional partners, press, grant reviewers. They want to know: "Is this a real organisation? How does it make decisions? Who's in charge?"

---

## Scope

**Must have:**
- Visual org structure (3 levels)
- Three levels explained in plain language
- Safety and route protocols
- Coordination duo named (names + brief intro)
- CTA block

**Out of scope:**
- Financial structure
- Legal entity details
- Named individuals for non-duo roles (volatile)
- Chapter-specific info → /chapters

---

## Structure

**Section flow:**
1. Page header
2. Organigram / visual structure (reuse existing SVG ✅)
3. Three levels explained
4. Coordination duo — named
5. Safety and route protocols
6. CTA block

---

## Skeleton

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  Organisation                                        │
│  Kidical Mass is a federated volunteer network:      │
│  locally rooted, collectively coordinated.           │
├──────────────────────────────────────────────────────┤
│                                                      │
│  [ ORGANIGRAM — SVG / CSS diagram ]                  │
│  Coordination duo                                    │
│        ↓                                             │
│  Local leads (4 meetups/year)                        │
│        ↓                                             │
│  16+ local groups     Thematic working groups        │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  How it works                                        │
│                                                      │
│  Level 1 — Local groups                              │
│  Each local group organises its own Kidical Mass     │
│  parades. They recruit their own volunteers, plan    │
│  their own routes, and connect with their local      │
│  community. Fully autonomous — but sharing the       │
│  Kidical Mass name, protocols, and values.           │
│                                                      │
│  Level 2 — Regional meetups                          │
│  4 times a year, organisers from different groups    │
│  come together. These meetups are for sharing        │
│  learnings, coordinating joint actions, and staying  │
│  connected. Attendance is voluntary but encouraged.  │
│                                                      │
│  Level 3 — Coordination duo                          │
│  A pair of volunteers coordinates the network:       │
│  onboarding new chapters, managing the shared        │
│  platform, liaising with partners. A service role,   │
│  not a hierarchy. The duo serves the network.        │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  [Name] and [Name]                                   │
│  [photo]  [photo]                                    │
│  Coordination duo — 2 sentences each                 │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Safety and routes                                   │
│  Kidical Mass has shared safety protocols and route  │
│  guidelines used by all chapters. Every route passes │
│  by parks, playgrounds, and safe infrastructure.     │
│  Volunteers are trained at the start of each season. │
│  → Getting Started for practical details             │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Want to start or join a chapter?                    │
│  [ Getting Started → ]       [ Help out → ]         │
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
│  Organisation        │
│  Federated volunteer │
│  network: locally    │
│  rooted, collectively│
│  coordinated.        │
├──────────────────────┤
│ [ ORGANIGRAM ]       │
│ Coordination duo     │
│      ↓               │
│ Local leads          │
│      ↓               │
│ 16+ local groups     │
├──────────────────────┤
│  How it works        │
│                      │
│  Level 1 — Local     │
│  groups              │
│  [description]       │
│                      │
│  Level 2 — Regional  │
│  meetups             │
│  [description]       │
│                      │
│  Level 3 —           │
│  Coordination duo    │
│  [description]       │
├──────────────────────┤
│  [Name] + [Name]     │
│  [photo] [photo]     │
│  [brief intro]       │
├──────────────────────┤
│  Safety and routes   │
│  [2–3 sentences]     │
│  → Getting Started   │
├──────────────────────┤
│  Want to join?       │
│  [ Getting Started]  │
│  [ Help out → ]      │
├──────────────────────┤
│ FOOTER               │
└──────────────────────┘
```

---

## Open Questions / Necessary Refinements

1. **Coordination duo names:** The page should name the actual people (Leticia + Cecilia, or whoever currently holds the role). Names + brief bios need to be provided by the coordination duo before build. Photos optional.
2. **Brussels vs. national scope:** The current site's organisation page says "12 groups in 16 communes Bruxelloises" — this needs updating to the national scope. Confirm whether the coordination duo and regional meetup structure applies equally to Walloon and Flemish chapters.
3. **Thematic working groups:** The raw organigram mentions "groupes de travail thématiques / thematische werkgroepen." Are there currently active named working groups? If so, should they be listed? Proposed: list them briefly if they're active and stable. If they're informal, keep it generic.
4. **Legal status:** Is Kidical Mass BE incorporated as an ASBL/VZW or operating informally? This could matter for institutional credibility on this page. If formal, mention it briefly. If informal, skip.
5. **Organigram implementation:** The current site uses a static SVG. The spec says "reuse for launch ✅." Confirm with Nico whether this is feasible within the new design system or needs rebuilding.

---

---

# About / Vision

Page URL: `/about/vision` · `/over/visie` · `/a-propos/vision`

Merges: `/nos-revendications-onze-aanbevelingen` + `/what-we-want` ✅

Status: ✅ Complete.

---

## Strategy

Articulates what Kidical Mass is fighting for — the political and advocacy dimension of the movement. 4 concrete policy demands + the Child Friendly City manifesto. Balances advocacy clarity with an inclusive, non-preachy tone.

**Who reads this:** Partners, press, grant reviewers, politicians, and movement allies. Occasionally a motivated family who attended a ride and wants to understand the "why" behind it. The tone can be stronger here than on event pages — advocacy language is appropriate.

---

## Scope

**Must have:**
- 4 policy demands (safe cycling infrastructure, family-friendly bike parking, safe school environments, zone 30 enforcement)
- Child Friendly City section (manifesto reference, coalition explanation)
- Parent quotes (Julienne, Fatima, Camille — confirmed consented ✅)
- CTA block

**Out of scope:**
- Full manifesto text (link to PDF only — copyright)
- Governance/org info → /about/organisation
- Stats → /about/mission

---

## Structure

**Section flow:**
1. Page header + optional pull quote
2. Four policy demands (2×2 grid)
3. Child Friendly City coalition section
4. Parent voices / quotes
5. CTA block

---

## Skeleton

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  Vision                                              │
│  We don't just ride. We demand cities that are       │
│  safe and joyful for every child.                    │
├──────────────────────────────────────────────────────┤
│                                                      │
│  [optional pull quote — Julienne or Fatima]          │
│  "J'ai constamment peur des voitures..."             │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  What we're asking for                               │
│                                                      │
│  ┌──────────────────────┐  ┌──────────────────────┐  │
│  │ 1. Safe cycling      │  │ 2. Family-friendly   │  │
│  │    lanes on main     │  │    bike parking      │  │
│  │    roads             │  │                      │  │
│  │    Wide, physically  │  │    Secure, accessible│  │
│  │    separated lanes   │  │    parking for cargo │  │
│  │    on busy routes.   │  │    bikes, trailers,  │  │
│  │    Continuous routes │  │    and children's    │  │
│  │    without dangerous │  │    bikes in public   │  │
│  │    interruptions.    │  │    spaces and schools│  │
│  └──────────────────────┘  └──────────────────────┘  │
│  ┌──────────────────────┐  ┌──────────────────────┐  │
│  │ 3. Safe school       │  │ 4. Enforce zone 30   │  │
│  │    environments      │  │                      │  │
│  │    School streets    │  │    Zone 30 exists    │  │
│  │    during drop-off   │  │    but isn't         │  │
│  │    and pick-up.      │  │    respected. We ask │  │
│  │    Clear signage and │  │    for strict         │  │
│  │    safe pedestrian   │  │    enforcement,      │  │
│  │    crossings.        │  │    physical measures,│  │
│  │                      │  │    and regular checks│  │
│  └──────────────────────┘  └──────────────────────┘  │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  The Child Friendly City coalition                   │
│                                                      │
│  Kidical Mass is part of a broader coalition of      │
│  organisations demanding cities that respect         │
│  children's rights — as set out in the UN Convention │
│  on the Rights of the Child (articles 6, 12, 13,     │
│  24, and 31).                                        │
│                                                      │
│  [brief manifesto summary — 2–3 sentences]           │
│                                                      │
│  Read the full manifesto →         [PDF link]        │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  In their own words                                  │
│                                                      │
│  "J'ai l'impression que je passe mon temps à couper  │
│   l'élan de vie de mes enfants."                     │
│  — Camille, mère de deux enfants, Saint-Gilles       │
│                                                      │
│  "J'ai constamment peur des voitures, des trams...   │
│   le temps de rentrer de l'école, je suis épuisée."  │
│  — Fatima, mère de trois enfants, Jette              │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Join the movement                                   │
│  [ Find a ride → ]         [ Help out → ]           │
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
│  Vision              │
│  We don't just ride. │
│  We demand cities    │
│  that are safe and   │
│  joyful for every    │
│  child.              │
├──────────────────────┤
│  "J'ai constamment   │
│   peur des           │
│   voitures..."       │
│  — Fatima            │
├──────────────────────┤
│  What we're asking   │
│  for                 │
│                      │
│ ┌──────────────────┐ │
│ │ 1. Safe cycling  │ │
│ │    lanes         │ │
│ │    [description] │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 2. Family-       │ │
│ │    friendly      │ │
│ │    bike parking  │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 3. Safe school   │ │
│ │    environments  │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 4. Enforce       │ │
│ │    zone 30       │ │
│ └──────────────────┘ │
├──────────────────────┤
│  Child Friendly City │
│  [2–3 sentences]     │
│  Read manifesto →    │
├──────────────────────┤
│  "Je passe mon temps │
│   à couper l'élan    │
│   de vie de mes      │
│   enfants."          │
│  — Camille           │
├──────────────────────┤
│  Join the movement   │
│  [ Find a ride → ]   │
│  [ Help out → ]      │
├──────────────────────┤
│ FOOTER               │
└──────────────────────┘
```

---

## Open Questions / Necessary Refinements

1. **National scope of policy demands:** The 4 demands were drafted with Brussels in mind. Validate with Leticia: are they equally relevant for Walloon and Flemish cities? If yes, the page stands. If some demands are Brussels-specific (e.g., zone 30 is already more strictly enforced in some regions), note this or generalise the demands.
2. **Manifesto PDF link:** The raw site links to a PDF hosted on Wix. This link will break when Wix is decommissioned. The PDF needs to be re-hosted on the new Laravel site or an external permanent host. Flag as a content migration task.
3. **Parent quotes — photo inclusion:** The raw site shows parent quotes without photos. Should photos accompany the quotes on the new site? Proposed: no photos (privacy + sourced from a study, not first-party). Text attribution only.
4. **Child Friendly City coalition — coalition members:** The manifesto page lists the coalition but the wiki doesn't capture the member list. If this coalition is still active, naming the coalition members adds credibility. Confirm with Leticia.
5. **Tone calibration:** The Vision page is explicitly advocacy content. The ToV guide says "Keep the strong advocacy register on this page ✅." Confirm with Leticia that the stronger, more politically engaged tone is correct before the copywriter uses it.

---

---

# About / News

Page URL: `/about/news` · `/over/nieuws` · `/a-propos/actualites` *(Redirect from `/my-blog`)*

Status: ✅ Complete.

---

## Strategy

Editorial hub for Kidical Mass updates. Replaces the current Wix blog. Low volume (a few articles per year) — design must not feel empty.

**Who reads this:** Families curious about recent news, volunteers catching up, press looking for recent activity, grant reviewers checking that the organisation is active.

---

## Scope / Decisions

- **Content ownership ✅:** Any chapter lead can publish to the network news feed.
- **Language policy ✅:** Bilingual preferred (FR+NL per article) — but mono is acceptable. Language badge on each card.
- **Cover image:** Include as optional field — recommended yes. Flag to Nico as a CMS field. ✅
- **RSS feed:** Include if technically straightforward. Deferred if complex. Flag to Nico.

---

## Skeleton

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  News                                                │
│  Updates from the Kidical Mass network.              │
├──────────────────────────────────────────────────────┤
│                                                      │
│  ┌──────────────────────────────────────────────┐   │
│  │ [optional cover image]                        │   │
│  │ Article title                                 │   │
│  │ 12 March 2026  · [NL] [FR]                   │   │
│  │ Excerpt — first 1–2 sentences of the article. │   │
│  └──────────────────────────────────────────────┘   │
│                                                      │
│  ┌──────────────────────────────────────────────┐   │
│  │ Article title                                 │   │
│  │ 5 January 2026  · [FR]                       │   │
│  │ Excerpt...                                    │   │
│  └──────────────────────────────────────────────┘   │
│                                                      │
│  [empty state: "Nothing here yet — check back soon!"]│
│                                                      │
├──────────────────────────────────────────────────────┤
│ FOOTER                                               │
└──────────────────────────────────────────────────────┘
```

### Article detail (`/about/news/[slug]`)

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│ ← Back to News                                       │
│                                                      │
│  [optional cover image — full width]                 │
│                                                      │
│  Article title                                       │
│  12 March 2026  · [NL] [FR]                         │
│                                                      │
│  [Article body — rich text, may include images]      │
│                                                      │
├──────────────────────────────────────────────────────┤
│ FOOTER                                               │
└──────────────────────────────────────────────────────┘
```

---

## Open Questions / Necessary Refinements

1. **Cover image field:** Recommended yes — flag to Nico to include an optional cover image field in the news CMS. Dimension recommendation: 16:9, minimum 1200px wide.
2. **Article list layout:** Vertical feed (not a grid) as specced. If a cover image is included, the card becomes richer. If no cover image, the card is text-only. Both states must look intentional.
3. **RSS feed:** Include if Laravel generates it without significant effort. Flag to Nico.
4. **Author byline:** The current site has no bylines. The spec says no bylines for MVP. Confirm this is still the decision — chapter leads can publish but aren't attributed publicly.

---

---

# About / Press

Page URL: `/about/press` · `/over/pers` · `/a-propos/presse` *(Redirect from `/press`)*

Status: ✅ Complete.

---

## Strategy

Aggregates all press coverage — national + chapter level. Credibility signal for press contacts, researchers, funders, and the community.

**Who reads this:** Press/journalists, grant reviewers, partners, and curious community members.

---

## Scope / Decisions

- **Press contact ✅:** Designated press contact email on the page. (Email address TBC — from coordination duo.)
- **Featured items ✅:** Pin 2–3 items to the top. Priority: video coverage from RTBF, BX1 first.
- **Auto-aggregation ✅:** Chapter press items auto-surface here with a chapter tag.
- **Dead links:** Strategy for 2020–2021 dead links = mark as "archived" and retain. External links decay; keeping the record is more useful than hiding it.

**Data model per press item:** `outlet`, `headline`, `url`, `date`, `language` (FR/NL/EN), `media_type` (TV/Radio/Print/Online), `chapter_id` (nullable), `is_featured` (bool), `is_archived` (bool)

---

## Skeleton

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  Press                                               │
│  Media coverage of Kidical Mass Belgium, 2020–present│
│                                                      │
│  Press enquiries: [press@kidicalmass.be]             │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Featured                                            │
│  ┌────────────────────────────────────────────────┐  │
│  │ RTBF · "Kidical Mass : des centaines de        │  │
│  │ familles à vélo dans les rues de Bruxelles"    │  │
│  │ 2024 · [FR] · TV/Online  ↗                    │  │
│  └────────────────────────────────────────────────┘  │
│  ┌────────────────────────────────────────────────┐  │
│  │ BX1 · "Kidical Mass à Bruxelles"               │  │
│  │ 2024 · [FR] · TV  ↗                           │  │
│  └────────────────────────────────────────────────┘  │
│                                                      │
│  All coverage                                        │
│                                                      │
│  Outlet           Headline             Date  Lang  ↗ │
│  ─────────────────────────────────────────────────── │
│  Politico         "Belgium's Kidical..."  2025  EN  ↗ │
│  Bruzz            "Kidical Mass trekt..."  2024  NL  ↗ │
│  La DH            "Le mouvement vélo..."  2024  FR  ↗ │
│  HLN              "Kidical Mass groeit"   2023  NL  ↗ │
│  Het Nieuwsblad   "Op de fiets met..."    2023  NL  ↗ │
│  RTBF             [title TBC]             2023  FR  ↗ │
│  ...                                                 │
│                                                      │
│  [chapter tag shown for chapter-sourced items]       │
│                                                      │
├──────────────────────────────────────────────────────┤
│ FOOTER                                               │
└──────────────────────────────────────────────────────┘
```

---

## Open Questions / Necessary Refinements

1. **Press contact email:** "press@kidicalmass.be" is a placeholder. Confirm the actual press contact address with Leticia before build.
2. **Auto-aggregation implementation:** Confirm with Nico that chapter press items are tagged with a chapter_id and automatically included in this page's query. This is a data model requirement.
3. **Dead link strategy:** Confirmed as "mark archived ✅." What's the visual treatment? Proposed: muted row + "(archived)" label next to the link icon. Confirm with design.
4. **Complete press list:** The press items listed above are from the raw site. The coordination duo should audit and complete the full list, including any 2025–2026 items not yet on the current site.

---

---

# About / Partners

Page URL: `/about/partners` · `/over/partners` · `/a-propos/partenaires`

Status: ✅ Complete.

---

## Strategy

Lists and contextualises who supports Kidical Mass. Social proof and legitimacy. Low-maintenance page.

**Who reads this:** Potential partners evaluating whether to join, press confirming the organisation's credibility, grant reviewers checking partner relationships.

---

## Scope / Decisions

- **Partner list confirmed ✅:** 6 entries total:
  - Institutional: Bruxelles Mobilité, Ville de Bruxelles, Commune de Schaerbeek
  - Movement allies: Clean Cities Campaign
  - Operational/in-kind: Loopz, Kidical Mouse

- **Brussels-only scope ⚠️:** All confirmed partners are Brussels-based. This is acknowledged as a known gap — the page should include language that makes room for future national partners without implying the current list is exhaustive.

- **"Become a partner" CTA:** Include — Kidical Mass is actively growing and open to new partnerships.

---

## Skeleton

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  Partners                                            │
│  Kidical Mass works with partners who share our      │
│  commitment to child-friendly cities.                │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Institutional partners                              │
│  Organisations providing structural support.         │
│                                                      │
│  ┌────────┐  ┌────────┐  ┌────────┐                 │
│  │ [logo] │  │ [logo] │  │ [logo] │                 │
│  │ BXL    │  │ Ville  │  │ Schaer │                 │
│  │ Mobi.  │  │ de BXL │  │ beek   │                 │
│  └────────┘  └────────┘  └────────┘                 │
│                                                      │
│  Movement allies                                     │
│  Organisations aligned on advocacy goals.            │
│                                                      │
│  ┌────────┐                                          │
│  │ [logo] │                                          │
│  │ Clean  │                                          │
│  │ Cities │                                          │
│  └────────┘                                          │
│                                                      │
│  Operational & in-kind partners                      │
│  Organisations providing practical support.          │
│                                                      │
│  ┌────────┐  ┌────────┐                              │
│  │ [logo] │  │ [logo] │                              │
│  │ Loopz  │  │ Kidical│                              │
│  │        │  │ Mouse  │                              │
│  └────────┘  └────────┘                              │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Become a partner                                    │
│  Interested in supporting Kidical Mass?              │
│  Reach out at [bike@kidicalmass.be]                  │
│                                                      │
├──────────────────────────────────────────────────────┤
│ FOOTER                                               │
└──────────────────────────────────────────────────────┘
```

---

## Open Questions / Necessary Refinements

1. **Brussels-only scope:** All 6 confirmed partners are Brussels-based institutions or initiatives. Before launch, ask the coordination duo: are there any Walloon or Flemish regional partners to add? If not, the page should avoid implying national institutional support it doesn't have.
2. **Logo assets:** Do all 6 partners have SVG or high-res PNG logos cleared for use on the website? Logo usage rights need to be confirmed for each partner. Loopz and My Kids Bikes logos are already on the current Wix site — assume continued permission unless told otherwise.
3. **Partner descriptions:** Each partner entry currently shows logo + name only. Should a 1-line description be added (e.g., "Bruxelles Mobilité — Brussels regional mobility authority")? Recommended yes for accessibility and context. Flag as copywriting task.
4. **"Become a partner" email:** Confirm bike@kidicalmass.be is the correct address for partnership enquiries. Alternatively, a dedicated partners@ address may be cleaner.

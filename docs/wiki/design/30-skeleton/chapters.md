---
title: Lokale groepen — Chapters overview + page template
tags: []
sources: [notion, raw/website/index.md, raw/website/organisation.md, wiki/design/20-structure.md, wiki/glossary.md]
phase: design
updated: 2026-06-23
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

# Chapter Page — the local group's home

> **Re-planned from scratch 2026-06-03 (Frederik).** The previous version of this section was marked "✅ Complete" but produced a page Frederik rejected as **not a home**: English, off the ride/show kit, a flat stack of equal-weight sections that opens on metadata (zip + "X activities / X articles" count badges + "Part of:"), buries the next ride below a generic "Subgroups" grid, reduces the team to grey "Organiser" chips, and ends on a catch-all "More" dump of empty placeholders. This re-plan keeps the decisions that still hold (uniform template, J2 form lives here, hide-if-empty, Brussels NL/FR toggle) and rebuilds everything else around one idea: **this page is the local group's home, and a resident should feel it is theirs.** Not yet built — re-plan + critique first (no build todo). Old skeleton history: git + [`log.md`](../../log.md). The Impeccable critique that sharpened this draft is at the end of the section (§ Critique v1 → v2).

Page URL: `/chapters/[postal-code]` (single-municipality) or `/chapters/[code1-code2]` (multi-municipality). Internal route `groups.show` (`/nl/chapters/{group}`).

**The one-line brief:** every group gets the *same frame*, but each must feel *inhabited* — by its next ride, its faces, its neighbourhood, its own signs of life. Uniformity is in the chrome; character is in the contents. The old build failed because it made the contents generic too.

---

## Strategy

The chapter page is the **local home of one group** — the answer to a resident's three questions: *when do we ride next, who's behind this, and could I be part of it?* It is also, quietly, the proof that this particular local node is alive.

### Who arrives, in what mental state, and what they're really after

**1. The local family — "Is there a ride near us soon, and is it for people like us?"** *(primary — the page's headline job)*
Arrives from a Google search ("Kidical Mass Schaerbeek"), a flyer, an Instagram post, a friend's WhatsApp. The functional job is small and concrete: *the next date, time and meeting point*. But underneath sits the decisive question — **"is this for a family like mine?"** They half-fear a clique of sporty activists: that their toddler on a balance bike will be too slow, that they'll show up on the wrong bike, that they don't belong. They want to *become* the kind of family that does joyful, safe things in its own streets, and to *avoid* the small humiliation of arriving wrong. What converts them is not information — it's **recognition**: ordinary local parents' faces, a warm line, the sense that the streets get closed and toddlers are welcome.

**2. The would-be local helper — "Who runs this, and could that be me?"** *(secondary — often arrives via Help-out's group picker, `?intent=volunteer`)*
Already half-sold on the movement; hesitant about *this* commitment. Their fear is competence and time: *"I'm not a cycling expert, not an activist, I can't run anything."* They need to see the team as **real, ordinary, friendly people** (not an org, not "Organiser" labels) and to be invited to join *them* by name, at low stakes. They want to *become* someone who shapes their own neighbourhood.

**3. The returning local / past rider — "When's the next one? Is this still going?"**
Warm, already a fan; comes back to check the date or relive the last ride. For them the page is most literally a *home* — they should feel ownership and aliveness (recent rides, a photo, news). Their loyalty is the group's engine.

**4. Stakeholder / curious outsider (journalist, partner scout, grant reviewer)** *(tertiary — served better by /about + the overview)*
Wants proof this local node is real and active. We do **not** design for them, but the aliveness signals the other three need — a team with names, recent rides, news — serve them as a by-product.

### What good feels like

A Schaarbeek parent lands and within a few seconds knows: *there's a ride on Sunday the 28th at Place Colignon, toddlers welcome, run by Sofie and two neighbours — and I could join them.* They feel **invited, not processed.** The page greets them like a home, not a filing cabinet.

### Key tensions to resolve

- **Uniform template vs. feeling inhabited** *(the central one).* The uniform-template decision **stands** — same sections, same brand, no per-group colours/logos/layouts. But "uniform" was wrongly built as "blank." Resolution: **the frame is uniform; the room is furnished by each group's own content** — its next ride, its faces, its meeting place, its news. Character comes from contents surfaced *warmly*, not from custom chrome.
- **Most-wanted answer vs. cheap metadata.** The old build led with counts and "Part of:" because the model offered them cheaply. Resolution: **lead with the next ride.** Counts and parent-labels are near-zero value to every audience and are cut.
- **Hide-if-empty honesty vs. looking abandoned** *(the hard problem the old build ignored).* Many groups will have no team photos, no partners, no downloads — some no scheduled ride yet. A home with empty rooms reads as *abandoned*. Resolution: **graceful, warm empty states are a first-class design problem.** A barely-filled group must still feel like an intentional, welcoming placeholder ("deze groep start net — zo doe je mee"), never a broken page.
- **Leaf chapter vs. region/parent node** *(structural fork — newly surfaced).* Some `groups` rows are *parents* (a region like "Brussel") with children; most are *leaf* chapters. A region node is a **different page** — a directory of its local groups, not a local home. The old build showed a generic "Subgroups" grid on every page. Resolution below (Structure); flagged as an open question for Nico/Frederik.

---

## Scope

Everything below serves one of the three jobs; anything that serves none is cut.

**Must have (the home, in priority order):**
- **Identity header** — municipality name as *place*, subtle region context (breadcrumb, not a "Part of:" line), and **one warm identity line** ("De buurtfietstocht voor en door Schaarbeekse gezinnen."). Brussels chapters: NL/FR toggle.
- **Next ride — hero** — the single next upcoming ride given real weight: date, time, meeting point, a clear route to the event detail ("Ik kom"). The page's reason to exist.
- **The team, as faces** — organisers as real people: photo (optional → warm initials fallback), name, light role. Never grey "Organiser" chips.
- **Join the local crew (J2 form)** — *already built* (`ChapterVolunteerSignup`); sits *with* the team (faces-before-form). Keep.
- **Graceful empty states** — first-class copy for "no ride scheduled yet", "team not listed yet", and the just-started group.

**Should have (signs of life — only when there's something to show):**
- **Recent news** from this group + its region — a warm strip, not a generic grid dumped at the bottom; hide-if-empty.
- **Past rides →** a link into `/events` filtered to this group (proof of life without clutter).
- **Local friends/partners** (optional, hide-if-empty) — framed as "vrienden van de groep", not a logo wall.
- **Downloads** (optional) — the local flyer.
- **Press** (optional) — auto-aggregates to /about/press.
- **"Mis geen rit"** — per-chapter email opt-in (single-group scope; deliberately deferred here from the overview).

**Out of scope (unchanged + newly cut):**
- Per-group colours / logos / custom layout ✅ strictly uniform.
- Photo gallery (deferred); chapter-level blog (news is national for MVP).
- **Count badges** ("X activities / X articles") — noise; cut (consistent with the overview, open-Q #6 above).
- **"Part of: [parent]" metadata line** — replaced by subtle breadcrumb context.
- **The catch-all "More" section** — its contents become intentional, individually-hidden blocks (or nothing).

---

## Structure

Fixed template, single page, **one warm arc** rather than a flat stack — sections are weighted by job priority, not given equal billing. Sections with no content are hidden; the page degrades *gracefully*, never to a husk.

**Section flow — leaf chapter (the common case):**
1. **Breadcrumb + identity header** — ← Lokale groepen · region; H1 municipality; one warm identity line. (Brussels: NL/FR toggle.)
2. **Next ride — hero** — the climax, up top. Warm empty state when none scheduled.
3. **More upcoming rides** — compact list, only when there's more than one; else folded away.
4. **The team + join** — faces first, warm pitch naming them, then the J2 form (built). Empty-team state stays warm.
5. **Signs of life** — recent news from this group/region; hide-if-empty.
6. **Local extras** — friends/partners · downloads · press, each its *own* hide-if-empty block (no "More" dump). All-empty → nothing shows.
7. **Closing beat** — "mis geen rit" opt-in + a quiet "deze groep is van jou" line back to the overview.

**Region / parent node variant (has children) — flagged fork:** header → directory of the local groups in this region → (optional) region-wide next rides → "begin een groep" CTA. This is a *different page type*; see open-Q #7 below. Until resolved, a parent node should **not** render the leaf "home" layout.

**Key links out:** next/upcoming ride → `/events/[slug]` · past rides → `/events?group=…` · join → form in place + "meer over meehelpen" → `/help-out` · news → article / `/about/news` · breadcrumb → `/chapters`.

---

## Skeleton

The page reads as **one warm arc**: greet → the next ride (climax) → the faces and the invite → signs of life → quiet practicalities → a closing that hands the group back to the resident. Every optional block is hide-if-empty, and the empty states are designed, not defaulted (see the just-started variant).

**Brussels bilingual toggle ✅:** Brussels chapter pages show a NL/FR toggle in the header. Wallonië → FR default. Vlaanderen → NL default.

**Faces before form ✅:** you see the team, *then* you're invited to join *them*. One merged section, not two.

### Desktop — a filled leaf chapter

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│ ← Lokale groepen · Brussel              [ NL | FR ]  │
│                                                      │
│  Kidical Mass Schaarbeek                             │
│  De buurtfietstocht voor en door Schaarbeekse        │
│  gezinnen. Rustig tempo, afgezette straten,          │
│  iedereen welkom — van kinderfiets tot bakfiets.       │
├──────────────────────────────────────────────────────┤
│ [ ── one warm photo of an actual Schaarbeek ride ──] │
│ [ ── closed street · happy mixed-age kids · band  ──]│
├──────────────────────────────────────────────────────┤
│  VOLGENDE RIT   ← hero: the reason the page exists   │
│  ┌────────────────────────────────────────────────┐  │
│  │  ZONDAG 28 JUNI · 15:00                        │  │
│  │  Kidical Mass Schaarbeek                       │  │
│  │  Verzamelen: Place Colignon                    │  │
│  │  ± 3 km · rustig tempo · kinderen voorop       │  │
│  │                      [ Naar de fietstocht → ]  │  │
│  └────────────────────────────────────────────────┘  │
│  Ook later op de kalender:                           │
│  · zo 26 jul · 15:00 — Place Colignon                │
│  · zo 30 aug · 15:00 — Place Colignon  Alle ritten → │
├──────────────────────────────────────────────────────┤
│  WIE DIT TREKT   ← faces, then the invite            │
│  ┌────────┐  ┌────────┐  ┌────────┐                  │
│  │[photo] │  │[photo] │  │  S M   │ ← initials       │
│  │ Sofie  │  │ Marc   │  │ Lena   │   fallback       │
│  │ trekker│  │ mee    │  │ comms  │                  │
│  └────────┘  └────────┘  └────────┘                  │
│                                                      │
│  Help mee in Schaarbeek                              │
│  Een paar uur per maand, samen met Sofie, Marc &     │
│  Lena. Je hoeft geen fietsexpert te zijn.            │
│  ┌──────────────────────────────────────────────┐    │
│  │ Naam ________   E-mail ________              │    │
│  │ Ik help graag met: (optioneel)               │    │
│  │ [ ] fluohesje [ ] mee-organiseren [ ] comms  │    │
│  │ [ ] foto [ ] dj [ ] weet nog niet            │    │
│  │ Bericht (optioneel) ____________             │    │
│  │                         [ Ik doe mee → ]     │    │
│  └──────────────────────────────────────────────┘    │
│  Meer over meehelpen →                               │
├──────────────────────────────────────────────────────┤
│  UIT DE BUURT   [hide-if-empty]  ← signs of life     │
│  ┌────────────────┐  ┌────────────────┐              │
│  │ [cover] nieuws │  │ [cover] nieuws │ group +      │
│  │ titel · datum  │  │ titel · datum  │ region news  │
│  └────────────────┘  └────────────────┘              │
├──────────────────────────────────────────────────────┤
│  Local extras — each its own hide-if-empty block     │
│  Vrienden van de groep [hide]   Downloads [hide]     │
│  [logo] [logo]                  Flyer_2026.pdf ↓     │
│  In de pers [hide]                                   │
│  HLN · "Kidical Mass groeit" · mrt 2024 ↗            │
├──────────────────────────────────────────────────────┤
│  [light band — quiet closing beat]                   │
│  Mis geen enkele rit in Schaarbeek                   │
│  [ e-mail ____________   Hou me op de hoogte ]       │
│  Deze groep is van jou.  ← Alle lokale groepen       │
├──────────────────────────────────────────────────────┤
│ FOOTER                                               │
└──────────────────────────────────────────────────────┘
```

### Desktop — a just-started chapter (the empty state, designed)

```
┌──────────────────────────────────────────────────────┐
│ ← Lokale groepen · Wallonië                          │
│  Kidical Mass Namur                                  │
│  Deze groep is net gestart. Hij wacht op zijn        │
│  eerste rit — en misschien op jou.                   │
├──────────────────────────────────────────────────────┤
│  VOLGENDE RIT                                        │
│  ┌────────────────────────────────────────────────┐  │
│  │  Nog geen rit gepland.                         │  │
│  │  Laat je gegevens achter en je bent de eerste  │  │
│  │  die het hoort als Namur vertrekt.             │  │
│  │                       [ Hou me op de hoogte → ]│  │
│  └────────────────────────────────────────────────┘  │
├──────────────────────────────────────────────────────┤
│  Help Namur op gang                                  │
│  Er is nog geen team. Iemand moet de eerste zijn —   │
│  en dat hoef je niet alleen te doen.                 │
│  [ J2 form: naam · e-mail · bericht ]                │
│  Meer over een groep starten →                       │
├──────────────────────────────────────────────────────┤
│  ← Alle lokale groepen                               │
└──────────────────────────────────────────────────────┘
```
*(The "uit de buurt", extras and the duplicate closing band simply do not render — the page is short, warm and intentional, never a husk of empty placeholders.)*

### Mobile — filled leaf chapter

```
┌──────────────────────┐
│ NAV (hamburger)      │
├──────────────────────┤
│ ← Lokale groepen     │
│   · Brussel  [NL|FR] │
│  Kidical Mass        │
│  Schaarbeek          │
│  De buurtfietstocht  │
│  voor en door        │
│  Schaarbeekse        │
│  gezinnen.           │
├──────────────────────┤
│  VOLGENDE RIT        │
│ ┌──────────────────┐ │
│ │ ZO 28 JUNI·15:00 │ │
│ │ Place Colignon   │ │
│ │ ±3km · rustig ·  │ │
│ │ kinderen voorop  │ │
│ │   [ Ik kom → ]   │ │
│ └──────────────────┘ │
│ Later: 26 jul·30 aug │
│ Alle ritten →        │
├──────────────────────┤
│  WIE DIT TREKT       │
│ [photo] Sofie·trekker│
│ [photo] Marc · mee   │
│ [ SM ]  Lena · comms │
│                      │
│  Help mee in         │
│  Schaarbeek          │
│  Samen met Sofie,    │
│  Marc & Lena. Geen   │
│  fietsexpert nodig.  │
│  [ J2 form ]         │
│   [ Ik doe mee → ]   │
│  Meer over meehelpen→│
├──────────────────────┤
│  UIT DE BUURT [hide] │
│ [cover] titel·datum  │
│ [cover] titel·datum  │
├──────────────────────┤
│  Vrienden [hide]     │
│  Downloads [hide]    │
│  In de pers [hide]   │
├──────────────────────┤
│ [light band]         │
│ Mis geen rit in      │
│ Schaarbeek           │
│ [ e-mail _______ ]   │
│ [ Hou me op de hoogte]│
│ ← Alle groepen       │
├──────────────────────┤
│ FOOTER               │
└──────────────────────┘
```

### Annotations

- **Breadcrumb + region context:** "← Lokale groepen · Brussel" — top-left, small. The region replaces the old "Part of: [parent]" metadata line; it's wayfinding, not a stat.
- **Identity header:** Municipality name as a *place* (large). One **warm identity line** beneath — the recognition signal the tentative family needs ("iedereen welkom — van kinderfiets tot bakfiets"). No postal code in the headline (it's plumbing, not identity); no count badges. ~~Brussels NL/FR toggle~~ deferred (NL-only build; see Decisions). **The "home" feeling must not load-bear on the identity line** (a field leads may never fill — see open-Q #8): the always-group-specific contents (the ride photo, the faces, this group's own rides + meeting place + news) carry it; the identity line is a warm bonus. *(Cadence/"elke laatste zondag" line — dropped 2026-06-03, Frederik: that recurrence doesn't reliably exist; not in spec or build.)*
- **Warm ride photo:** one real photo of an actual ride for this group (closed street, mixed-age kids) — the single strongest "this is for me / this is alive" signal for a tentative family, and the brand's most native asset. Sits high (band under the header). Optional → **graceful fallback to a shared Kidical Mass ride photo** when the group has none, never a grey block. See open-Q #10. *(Distinct from the deferred photo gallery — this is one ambient image, not an album.)*
- **Next ride — hero:** the single next upcoming ride, given real visual weight (not one card in a flat list). Reuses the ride/show kit. Date · time · meeting point · a one-line reassurance (tempo / afstand / kinderen voorop) · **"Naar de fietstocht →"** to the event detail (the button *navigates* to the ride page for full practical detail; there is no RSVP — per-event attendance was cut, D-1). **Empty state is designed** — "Nog geen rit gepland" turns into the email opt-in, never a dead end.
- **More upcoming rides:** compact text list, only rendered when there's more than one upcoming ride; "Alle ritten →" links to `/events` filtered to this group (also the home for *past* rides — proof of life without cluttering the page).
- **The team, as faces:** photo (optional → **warm initials avatar** fallback, never a grey silhouette or an "Organiser" label), name, a light human role ("trekker", "mee", "comms"). The faces are the trust work; the names recur in the pitch. *(This build: initials avatars + names only — no photo or role field exists yet; the role label is dropped until one does. See Decisions.)*
- **Join (J2 — the form lives here, built):** the `ChapterVolunteerSignup` Livewire component — name, email, optional role checkboxes (fluohesje · mee-organiseren · comms · foto · dj · *weet nog niet*), optional message; `#aanmelden` anchor + `?intent=volunteer` welcome banner. Routing is **implicit by context** (no municipality dropdown). The "*weet nog niet*" box removes the need to know your role before reaching out. Pitch names the team ("samen met Sofie, Marc & Lena") so the ask feels like joining people, not an org.
- **Confirmation state (built):** replaces the form in place on submit — warm "Je bent erbij" + the chapter's **next ride** (the card sits directly above; zero new data dependency), "het team" with no name/SLA (keeps the promise honest, independent of [D-1](../01-concerns.md)). The J2 "post-submit limbo" antidote — see [help-out.md § post-submit limbo](help-out.md).
- **Empty-team state stays warm:** no team yet → "Help [stad] op gang. Iemand moet de eerste zijn — en dat hoef je niet alleen te doen." The form leads; the absence becomes an invitation, not an embarrassment.
- **Uit de buurt (signs of life):** recent news from this group + its region as a small warm strip (cover · title · date), hide-if-empty. Items still aggregate to `/about/news`. *Not* a generic full "News" grid dumped at the page foot.
- **Local extras:** "Vrienden van de groep" (logo + name + optional link, framed warmly, not a wall), Downloads (local flyer), In de pers (outlet · headline · date · ↗, aggregates to `/about/press`). **Each is its own hide-if-empty block** — the old catch-all "More" grid of placeholders is gone. All-empty → the whole zone is absent. *(This build: faux vrienden + downloads seeded to preview the blocks; "in de pers" stays hide-if-empty — never fabricate press. See Decisions.)*
- **Closing beat:** the per-chapter **"mis geen rit" email opt-in** (single-group scope) on a quiet light band, plus "Deze groep is van jou." back to `/chapters`. The page hands the group back to the resident. *(This build: the opt-in is **faked** — real-looking input + button, client-side "bedankt" state, does not persist; the subscription model is Nico's. See Decisions.)*
- **Backend note (carried):** the J2 form currently routes to the central comms inbox tagged with the chapter name; **per-lead routing still pending** (no per-group lead email on `Group`) — [GitHub #37](https://github.com/ndeblauw/kidicalmass/issues/37). The whole view still needs its **NL + ride/show-kit build pass** (the current live view is EN + off-kit).

---

## Build decisions (2026-06-03, Frederik — interview)

Schema reality: `groups` has only `name, shortname, zip, parent_id, invisible, started_at, ended_at`; `users` has no photo/role; `activities` carry `title_nl/fr, location, postal_code, distance, begin_date`; the news query already walks up to the parent region. So the "character" fields have no home yet. Decisions for **this build**:

- **Data strategy → defaults + spec for Nico.** Build the NL view now with existing data + graceful defaults; do **not** add migrations to the shared schema. Real per-group fields (`intro`/tagline, user photo + role, optional cover, lead email, subscription) become a backend spec / GitHub issue for Nico. *(Nico owns the shared public schema — we don't migrate it from here.)*
- **Identity line → templated default now** ("De buurtfietstocht voor en door <gemeente> — iedereen welkom, van kinderfiets tot bakfiets."), editable per-group field later (Nico). Not load-bearing.
- **Warm ride photo → reuse an existing site photo** as the shared fallback for every group (from `public/img/…`); per-group cover later.
- **Team → faces with initials-avatar fallback** (no photo field yet); **drop the role label** for now (no role data — "Organiser" chip is gone, names only) until a role field exists.
- **Cadence/rhythm line → DROPPED entirely.** That recurrence doesn't reliably exist; **not in spec, not built.** (Was open-Q #11 + critique finding #7.)
- **Brussels NL/FR toggle → deferred.** NL-only build; add when the FR locale layer launches.
- **Region/parent node → deferred.** Build the leaf "home" now. Parent nodes keep a minimal children list (don't break) but get no dedicated region-directory variant this pass.
- **"Mis geen rit" opt-in → fake it (visible, non-functional).** Frederik wants to see the functionality: render a real-looking email input + button with a client-side "bedankt" state; **does not persist** (no subscription model yet). Spec the model for Nico.
- **Local extras → faux "vrienden" + "downloads" to preview; "in de pers" hide-if-empty.** Never fabricate press coverage (matches the global press page / D-11). Faux vrienden+downloads are seed/demo content to show the blocks, clearly removable.
- **"Uit de buurt" news → region-fallback already works** (controller walks parents); hide-if-empty when even that is bare.

### Still open (for Nico / a later pass)
1. **Backend spec for the character fields** — `Group.intro` (editable identity line), `Group` cover image (medialibrary), `user`↔group **role** + photo, per-group **lead email** (per-lead routing, [GitHub #37](https://github.com/ndeblauw/kidicalmass/issues/37)), and the single-group **`Email subscription`** model (shared with the events-overview placeholder).
2. **Multi-municipality header** — "Woluwe-Saint-Pierre & Saint-Lambert" as the name, "1150–1200" sub-line; confirm long names wrap cleanly on mobile.
3. **Press / news aggregation** — confirm chapter press + news carry a `group_id` so `/about/press` + `/about/news` aggregate them.
4. **Region/parent node** — decide later whether parent nodes are public pages at all (directory variant vs 301 to `/chapters`).
5. **Final ToV copy (NL/FR)** for the identity-line default, empty states, and the faux opt-in confirmation.

---

## Critique v1 → v2

> Impeccable critique pass on the v1 skeleton above (2026-06-03). The mockups already incorporate the fixes; this records the reasoning so the build doesn't quietly regress.

**What held up (keep):** next-ride-as-hero correctly answers the primary job first; faces-before-form is sound; the designed just-started empty state is the single biggest gain over the old build; cutting count badges / "Part of:" / the "More" dump is right.

**What the critique sharpened:**

1. **Two CTAs competing in the hero zone.** "Ik kom →" (hero) and "Hou me op de hoogte" (empty state) are the *same slot* in two states — good. But on a *filled* page the closing "mis geen rit" opt-in and the hero's "Ik kom" both ask for commitment. **Fix:** the hero owns the immediate action (join the ride); the closing opt-in is visually quiet (light band, secondary) so it never competes with the hero. Reflected in the mockup (hero = solid card + primary button; closing = quiet band).
2. **The team section carries two jobs (show faces + capture a volunteer).** Risk: the form's visual weight buries the faces, undoing "faces before form". **Fix:** faces get their own visual row *above* the pitch; the form is one contained card, not the section's centre of gravity. The pitch line bridges them by naming the faces.
3. **"Local extras" still risks looking like a leftover drawer.** Three hide-if-empty blocks side by side can read as a footer junk-zone. **Fix:** they only ever appear when populated, and each is a titled mini-section (not a shared "More" header). When all three are empty — the launch reality for most groups — the entire zone is gone, so the common page is *header → ride → team → closing*, which is clean.
4. **Identity line is doing the heaviest emotional lifting but had no data home.** A site-wide generic string would flatten every group back to "blank uniform". **Fix:** promoted to open-Q #8 as a per-group editable field with a warm default — the mechanism by which "uniform frame, inhabited room" actually happens. Without it, the re-plan's core promise is just decoration.
5. **Hierarchy needs to survive a content-poor group.** The arc is satisfying when full, but most launch groups are sparse. **Fix:** the empty/just-started variant is now a *first-class mockup*, not a footnote — it's the page most visitors will actually meet first, so it has to feel intentional on its own.

**Net:** v2 = v1 with (a) the hero owning the one primary action and the closing opt-in demoted to a quiet band, (b) faces visually separated from and above the form, (c) extras strictly per-block hide-if-empty, (d) the identity line promoted to a real (editable) data dependency, and (e) the sparse-group state treated as the primary design target, not the exception.

**Second pass — what the Impeccable critique skill surfaced that the first pass missed** (these are the most "home/alive" of all, and were folded into the mockup + annotations):

6. **No photo of the actual thing.** Kidical Mass is intensely visual and joyful, yet v1 had only *team* photos and *news* covers — no image of a ride. For the tentative family, one real photo of a closed street full of mixed-age kids is the single strongest "this is for me / this is alive" signal, and it was absent. **Fix:** a warm ride-photo band high on the page, with a shared-photo fallback so even a brand-new group looks alive (open-Q #10). This is the biggest emotional gain of the second pass.
7. **Rhythm was invisible.** A home conveys *cadence* ("elke laatste zondag"), not just the next isolated date. ~~**Fix:** a rhythm line under the header.~~ **Dropped 2026-06-03 (Frederik):** that recurrence doesn't reliably exist across groups — faking a schedule we can't honour is worse than omitting it. Out of spec and build.
8. **The "home" feeling was over-loaded onto the editable identity line** — a field many leads will leave blank, which would collapse every group back to "blank uniform". **Fix:** explicitly re-seat the load on the always-group-specific contents (photo, faces, this group's own rides/meeting-place/news); the identity line is a bonus, not the foundation.
9. **Microcopy: hero CTA "Ik kom →" overpromised.** The button *navigates* to the event detail (where the real RSVP lives), so it shouldn't imply the commitment happens in place. **Fix:** "Naar de fietstocht →".

---

## Critique v3 — live-build review (Frederik 2026-06-03, `/critique`)

Reviewing the **built** page (Schaarbeek = filled; Anderlecht = workshop, no team; Brussel Stad = meeting) surfaced that the build **drifted from v2** and that v2 itself missed **activity-type awareness**. Diagnosis: the IA serves the *secondary* audience (would-be volunteer) first — a full-width always-open J2 form is the page's centre of gravity — while the *primary* family's needs are muddled. Resolved with Frederik:

1. **Activity-type blindness (the core bug).** `activity_type` is an enum (`kidicalmass` / `meeting` / `workshop` / `other`) but the hero showed `$activities->first()` as **"Volgende rit → Naar de fietstocht"** regardless — so Brussel Stad's **vrijwilligersmeeting** and Anderlecht's **fietscheck-workshop** masqueraded as the family's headline parade. **Decision (Frederik): one unified "events by de lokale groep" format** — rides, workshops, meetups in a single **type-labeled agenda**; the rendering (label + CTA) adapts per type (Fietstocht → "Naar de fietstocht"; Workshop → "Meer info"; Meeting → "voor vrijwilligers", no family CTA, but **kept** visible per D-2). The soonest **`kidicalmass` ride** gets the weighted card; when there's none, that slot is the "nog geen rit — hou me op de hoogte" empty state and the workshop/meeting still list below (honest, not misleading).
2. **J2 form was the centre of gravity → make it on-demand.** Port the **event-detail pattern** (`activities/show.blade.php`, `x-data="{ open }"`): faces + one-line pitch + a **"Zelf meehelpen in [gemeente]?" button** that reveals `ChapterVolunteerSignup` on click; auto-open on `?intent=volunteer`. The family page stays light; the helper is served on demand.
3. **National news → CUT (Frederik).** It was a 2×2 big-image grid, and the region-fallback made it **identical national news on every chapter** — it answers no one's "what's on near me". Removed from the chapter page; national news lives on `/about/news`. (Reinforces v2's "small strip, not a grid" — the build had drifted to the grid.)
4. **"Mis geen rit" opt-in promoted.** It was buried at the page foot *because the giant news pushed it there*. Move it **up next to the agenda** (calm band) — it's the family's most useful action when no ride is imminent. Cutting news also lifts it.
5. **"Alle ritten →" must be FILTERED.** The build linked to the bare `/events` index; it must deep-link to the calendar **filtered to this group, incl. past rides** (v2 already said this; build drifted).
6. **"Wie dit trekt" → lead + active volunteers, with roles (Frederik).** Not just the lonely lead: show the **trekker + the active roze hesjes**, each with a role. **Data dependency:** `group_user` currently holds only leads — needs volunteers attached + a pivot `role` (trekker / roze hesje / comms…). Until then, render whoever's in the pivot.

**Revised arc (family-first):** identity → ride photo → **agenda (typed, ride weighted) + opt-in** → **wie dit trekt (lead + crew) + on-demand meehelpen** → closing. News gone. The volunteer recruitment that dominated is demoted to an on-demand reveal — where the secondary audience actually needs it.

**New data deps for Nico (added to § Still open):** `activity_type`-aware agenda rendering; volunteers + pivot `role` on `group_user`; `/events?group=…` filtered link incl. past; per-chapter subscription model (shared with events-overview).

**Built 2026-06-03 (Frederik: "implement as much as possible, fake the rest").**

(— v3 build details continue below; the Critique v4 rethink is appended after this section. —) `groups/show.blade.php` rebuilt to the v3 arc. **Real now:** the **typed agenda** (reads `activity_type` → Fietstocht / Workshop / **Voor vrijwilligers** badge + per-type CTA; the next `kidicalmass` ride is the weighted hero, empty-ride state otherwise — verified on Schaarbeek/Anderlecht/Brussel-Stad); the **filtered "Alle activiteiten … (ook voorbije) →"** deep-link (`route('activities.index', ['gemeente' => $group->id])` — `RideCalendar` already reads `#[Url] gemeente` + `when`); the **on-demand meehelpen reveal** (Alpine `x-show`, auto-opens + welcome banner on `?intent=volunteer`); the J2 form; **news CUT**; opt-in **promoted** beside the agenda; closing demoted to a quiet hand-back. **Faked (clearly commented, removable):** active volunteers + roles (lead = `trekker` from the real pivot + faux `roze hesje`/`communicatie` crew — needs the `group_user` `role` field), friends + downloads, the client-side opt-in. CSS: `.chapter-agenda*`, `.chapter-join*`, `.chapter-team__role` on the ride/show kit; the old full-bleed closing band slimmed. Tests: `GroupsTest` updated to v3 (heading "Op de agenda", empty "Nog geen fietstocht gepland", faces+roles) + **2 new type-label tests** (workshop ≠ ride; meeting → "voor vrijwilligers"). **Verified:** full suite **138 green**, Pint clean, `npm run build` clean, screenshots (3 chapters + reveal + intent). Registry P-11 stage left to Frederik's pipeline. Per-lead routing still GitHub #37.

---

## Critique v4 — role-driven structure rethink (Frederik 2026-06-23, `/critique`)

> Reviewing the **shipped v3 page** (Schaarbeek, filled) against the question *"who is each section actually for?"*. The v3 build is sound and on-brand — this is not a surface problem. The diagnosis is structural: **the page is sequenced by content type (agenda → photos → press → friends → downloads → team → CTA), not by reader intent.** It's a well-dressed CMS dump. Each section announces "here is a kind of thing we have"; none is ordered around the question a specific visitor walked in with. Result: it opens *flat* — it lists before it orients — and the parade, the chapter's actual product, is just one undifferentiated row in a homogeneous day-grouped list.

### The four roles (Frederik's framing, reconciled with the Strategy personas)

The earlier persona set still holds; v4 sharpens it into four *intents the sequence must serve in order*:

- **A · The curious newcomer** — "Who are these people? What even is this chapter?" *(orientation — currently unserved on the first screen; the humans who'd answer this are buried 6 sections down inside the recruitment band.)*
- **B · The practical parent** — "What's Kidical, what does this chapter do, and when's the next ride?" *(= persona 1, primary. The single most-wanted answer has no special treatment — a film night and a parade render identically.)*
- **C · The proud member / returning local** — "This is us." *(= persona 3. Served by photos, but there's no "who we are / what we did" pride beat above the recruitment ask.)*
- **D · The prospective volunteer** — "What's next, what do I need to do?" *(= persona 2, secondary. The "Help mee" CTA lands cold — nothing builds to it.)*
- *(Stakeholder/media stays tertiary — served as a by-product, see press below.)*

### Answers to the four design questions (now load-bearing principles)

These were posed in the critique and resolved with Frederik; they are the *why* behind the revised arc:

1. **The next parade is the page's gravitational centre — commit fully.** Every other section must earn the right to sit above or beside it; almost none do. One loud element makes the rest legible by contrast — the flatness is the *absence* of that anchor. The only thing allowed to share its first screen is the warm what/why orientation line (a parade *of what?*). **Risk handled:** a chapter with no parade scheduled keeps the slot as the hero — the slot is always the star; sometimes the star is a promise ("nog geen parade — laat je mailadres achter") instead of a date. (Extends, not contradicts, v3's typed-agenda fix — see "Evolves v3" below.)
2. **"Wij zwaaien je welkom aan de start" is misfiled.** It's the page's warmest line and a welcome to *everyone who shows up*, not a recruitment pitch — yet it's gated behind the volunteer band, spending the best emotional asset on the narrowest audience. **Split the faces from the ask:** faces + welcome mid-page (role A/C orientation), the quieter recruitment ask at the bottom (role D), now earned. Same people, two jobs — welcome high, recruit low.
3. **Press works better as a line than a block.** On a chapter home, press is a *trust signal*, not content anyone reads — best small, early, glanceable ("ook gezien in BRUZZ, De Standaard"). The full clippings list has a real but niche reader (media / word-spreaders), served in a quiet "voor pers & delen" zone near the footer, never in the parent's flow. Both, demoted.
4. **Strip to the parent deciding whether to show up Saturday — that's the true spine.** What survives: *(i)* what is this / for my kid's age (orient) · *(ii)* when & where is the next parade, how long (decide) · *(iii)* will we fit in / is it safe — real-family photos + welcoming faces (reassure) · *(iv)* what if I can't make this one — the email reminder (catch the miss). Everything else is supporting cast that must never obstruct those four beats. **Build outward from them.**

**Through-line:** the page currently treats every section as a peer; none are. There's one decision (show up), one fear (fitting in), one fallback (next time). Serve those three loudly and let the rest go quiet — the flatness dissolves not by adding more, but by finally saying what the page is *for*.

### The moves

1. **Open by orienting, not listing.** Hero does triple duty: name + **one warm what/why line** + the **next-parade date as a glanceable anchor** + a micro-proof ("sinds 2023 · 12 ritten") + the **press trust line**. Serves A, B and C in the first three seconds.
2. **Tier the events: parade as hero.** Replace the single homogeneous day-grouped list with three weighted zones — **De volgende parade** (one featured card: date · time · start · route hint · "kom mee"), **Alle parades** (a compact rhythm strip so the cadence is felt), and **Ook in [gemeente]** (workshops / film / repair, visibly lighter, clearly secondary).
3. **Fold "mis geen rit" into the featured-parade card.** Intent peaks the instant a parent sees a date they can't make — the reminder belongs *there* ("Kan je er niet bij? Krijg een seintje voor de volgende"), not in a later band.
4. **Surface the faces mid-page; keep the ask at the bottom.** A short, warm **"Wie zijn wij"** after the photos answers role A and reassures role B; the recruitment reveal stays in the yellow closing band (role D), now set up by photos + faces so it lands.
5. **Regroup the resource drawer by the role each serves.** Press → one-line trust signal up top **and** full list in a quiet "voor pers & delen" zone near the footer. Friends/partners → a quiet "met dank aan" strip low on the page (never competes with the parent). Downloads → **split**: the **colouring page** is family delight, moved near the photos/parade; the **flyer/poster** is a spreader tool, grouped with press and shown as an actual **thumbnail** ("here's the poster"), not a boring file row.

### Revised arc (role-driven)

```
1. Identity hero (blue)   — name + warm what/why line + next-parade glance + micro-proof + press trust line   [A·B·C]
2. De volgende parade     — featured card; "mis geen rit" reminder folded in                                    [B]
3. Alle parades           — compact rhythm strip (cadence)                                                      [B·C]
4. Ook in [gemeente]      — workshops / film / repair, lighter weight                                           [B·D]
5. Zo ziet het eruit      — photos (keep, strongest section) + colouring-page download nearby                   [C·B]
6. Wie zijn wij           — short, warm faces (orientation + reassurance)                                        [A·B·C]
7. Help mee (yellow)      — recruitment ask, on-demand reveal — now earned                                       [D]
8. Voor pers & delen      — press list + poster/flyer thumbnails + "met dank aan" friends, quiet, near footer    [D·media]
   closing                — "deze groep is van jou" hand-back to the overview
```
Orient → next action → rhythm → extras → proof → people → join → quiet resources.

### Desktop — filled leaf chapter (v4)

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  Kidical Mass Schaarbeek            [ ride photo ]    │
│  Wij fietsen samen met kinderen door Schaarbeek —    │
│  veilig, vrolijk, op kindertempo.                    │
│  Volgende parade: zo 28 juni · sinds 2023 · 12 ritten│
│  Ook gezien in BRUZZ · De Standaard · VRT            │
├──────────────────────────────────────────────────────┤
│  DE VOLGENDE PARADE   ← the page's gravity            │
│  ┌────────────────────────────────────────────────┐  │
│  │ ZONDAG 28 JUNI · 15:00                         │  │
│  │ Verzamelen: Place Colignon · ±3 km · rustig    │  │
│  │                            [ Kom mee → ]       │  │
│  │ Kan je er niet bij? [ Geef me een seintje → ]  │  │
│  └────────────────────────────────────────────────┘  │
├──────────────────────────────────────────────────────┤
│  ALLE PARADES        · zo 26 jul · zo 30 aug   meer →│
├──────────────────────────────────────────────────────┤
│  OOK IN SCHAARBEEK   (lichter)                       │
│  · wo 10 jul — Fietscheck-workshop                   │
│  · vr 19 jul — Filmavond                             │
├──────────────────────────────────────────────────────┤
│  ZO ZIET HET ERUIT   [photo wall + lightbox]         │
│  ………………  +  [ Kleurplaat voor onderweg ↓ ]         │
├──────────────────────────────────────────────────────┤
│  WIE ZIJN WIJ        faces + "wij zwaaien je welkom" │
│  [Sofie] [Marc] [Lena]                               │
├──────────────────────────────────────────────────────┤
│  [YELLOW]  Help mee in Schaarbeek   [ on-demand → ]  │
├──────────────────────────────────────────────────────┤
│  Voor pers & delen [quiet]                           │
│  In de pers: HLN · BRUZZ ↗   |  Affiche [thumb] ↓    │
│  Met dank aan: Fietsersbond · buurthuis · …          │
├──────────────────────────────────────────────────────┤
│ FOOTER                                               │
└──────────────────────────────────────────────────────┘
```
*(The just-started empty-state variant from v2/v3 still holds: featured-parade slot becomes the "nog geen parade — hou me op de hoogte" promise, parades/other/extras zones don't render, the page stays short and intentional.)*

### Evolves v3 — read before building (so it doesn't look like a regression)

- **Event tiering vs. v3's "one unified typed agenda."** v3 *correctly* fixed activity-type blindness by typing a single day-grouped list and weighting the next `kidicalmass` ride. v4 **extends** that: the type-awareness becomes *visual tiering* — featured parade / parades strip / other activities as distinct zones, not one mixed list. The honest-empty-state and "never dress a workshop as a ride" rules from v3 **carry over unchanged**. This is a deliberate sharpening, not a reversal — but it *does* re-split what v3 unified, so it's a conscious build choice, not a drift.
- **"Mis geen rit" placement.** v3 promoted it beside the agenda; v4 moves it *tighter* — folded into the featured-parade card at peak intent. Same intent, better moment.
- **Faces location.** v3 kept faces only in the closing band (with the on-demand J2 reveal). v4 **surfaces a warm faces beat mid-page** (orientation) *in addition to* the closing recruitment reveal. The on-demand reveal pattern for the form **stays**.
- **Press/friends/downloads.** v3 had them as side-by-side hide-if-empty blocks; v4 re-roles them (press up + down, friends → "met dank", downloads split with the colouring page moving to family content). Still hide-if-empty.

### New / changed data deps (for Nico — append to § Still open)

- **`activity_type`-aware tiering** beyond v3's labels: the query needs to *separate* `kidicalmass` rides (featured + strip) from `workshop`/`meeting`/`other` (the "ook in …" zone). No new field — just grouping logic on the existing enum.
- **Per-group "since" / ride-count** for the hero micro-proof (`started_at` already exists; a completed-rides count is derivable from past `activities`).
- **Download categorisation** — a flag distinguishing *family* downloads (colouring page) from *spreader* downloads (flyer/poster) so they can route to different zones; plus a thumbnail/preview for poster-type downloads.
- **Press trust line** — needs the same `group_id`-scoped press already specced; the one-line form is just the top 2–3 outlets.

**Status:** rethink agreed in conversation (2026-06-23); **not yet built.** Mockups above are the target. Open question for Frederik before build: confirm the parade/other split is worth re-splitting v3's unified agenda (it reverses a deliberate v3 call), and whether the hero micro-proof ("sinds X · Y ritten") is data we can stand behind per group.

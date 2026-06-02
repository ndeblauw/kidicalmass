---
title: Events Overview
tags: []
sources: [notion, raw/website/agenda.md]
phase: design
updated: 2026-04-13
---

Status: ✅ Complete. Page URL: `/events` (trilingual: `/nl/events`, `/fr/events`, `/en/events`)

**Summary:** One job — get a family to the right event detail page in under 10 seconds. Location filter + date-grouped list. Upcoming = date grouping. Past = month grouping. Cards are compact and text-driven. Filters are minimal by design. Grande Kidical Mass = featured badge on a standard card.

---

## Strategy

The primary discovery page for families. Replaces the current hand-typed `/agenda` that links everything to Facebook. This page answers one question fast: "When's the next ride near me?"

### Who arrives and in what mental state

**Families — ready to commit**
They know about Kidical Mass and want to go. Their mental model is practical: "which Saturday works for us, and which neighbourhood?" They're not browsing — they're deciding. Decision latency is low; friction must be even lower. Every extra step between landing and tapping an event card is a failure.

**Families — comparing options**
"We can do the 19th or the 26th — let's see what's available on both." They might filter by location first or scan the full list and pick the geographically closest one.

**Returning families**
Efficient. They know the format. They're checking: "when's Schaerbeek's next one?" The location filter is their first action.

**Volunteers**
"Which event am I supposed to be at this Saturday?" Same mechanics as families. The Events page serves this secondary need passively.

### What good looks like

A family opens the page, sees events grouped by date, filters to their municipality (or keeps all locations), taps the nearest upcoming event card within 10 seconds, and lands on the detail page ready to save the date. No decision fatigue, no unnecessary choices.

---

## Scope

**Must have:**
- Chronological event list with date grouping
- Location filter (by chapter/municipality)
- Upcoming/past toggle
- Event cards with: title, date, time, municipality, meeting point
- Each card links to the event detail page

**Should have:**
- "Today" / "Tomorrow" contextual labels
- Featured badge for Grande Kidical Mass

**Out of scope:**
- Map view (detail page has the map)
- RSVP / attendance count (deferred)
- Free-text search (volume doesn't warrant it)
- Event images on cards (events are practical meetups, not visual showcases)
- iCal subscription button (lives on the event detail page, not the list)

---

## Structure

Linear list page, no sub-navigation. Two modes determined by the toggle.

**Upcoming mode (default):** grouped by date, chronological. Multiple rides on the same Saturday cluster under one date header. The family decision is "which weekend?" then "which ride?"

**Past mode:** grouped by month, reverse chronological. Browsing an archive, not making a plan.

**Section flow:**
1. Page header
2. Filter bar (toggle + location) — sticky on scroll
3. Event list (grouped)
4. Empty state (if applicable)

**Key links out:**
- Event cards → /events/[slug]

---

## Skeleton

**Filter bar is sticky on scroll** — on desktop it stays fixed below the nav so the location filter remains accessible while scrolling a long list. On mobile it collapses to a compact single-line bar.

**No pagination** — the event volume (~60/season = ~5–10 upcoming at any time) fits on one scroll. If future growth changes this, add load-more.

**Grande Kidical Mass** uses the same card component as any event but with a featured badge (★ icon + "Featured" label). The date header for that weekend may also carry a visual cue.

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  Events                                              │
│  Find a ride near you                                │
├──────────────────────────────────────────────────────┤
│ [Upcoming ●] [Past]    Location: [ All locations ▼ ] │  ← sticky
├──────────────────────────────────────────────────────┤
│                                                      │
│  Saturday 19 April                                   │
│  ┌────────────────────────────────────────────────┐  │
│  │ Kidical Mass Evere – Haren           15:00     │  │
│  │ Evere · Place de la Mairie                     │  │
│  └────────────────────────────────────────────────┘  │
│  ┌────────────────────────────────────────────────┐  │
│  │ Kidical Mass Ixelles – Elsene        15:00     │  │
│  │ Ixelles · Place Flagey                         │  │
│  └────────────────────────────────────────────────┘  │
│                                                      │
│  Saturday 26 April                                   │
│  ┌────────────────────────────────────────────────┐  │
│  │ Kidical Mass Forest – Vorst          15:00     │  │
│  │ Forest · Parvis St-Denis                       │  │
│  └────────────────────────────────────────────────┘  │
│  ┌────────────────────────────────────────────────┐  │
│  │ Kidical Mass Woluwe-St-Pierre        15:00     │  │
│  │ Woluwe-Saint-Pierre · ...                      │  │
│  └────────────────────────────────────────────────┘  │
│                                                      │
│  Saturday 10 May  ★ Featured                         │
│  ┌────────────────────────────────────────────────┐  │
│  │ ★  GRANDE KIDICAL MASS 2026          15:00     │  │
│  │ Bruxelles · Place du Trône                     │  │
│  └────────────────────────────────────────────────┘  │
│                                                      │
│  Saturday 17 May                                     │
│  ┌────────────────────────────────────────────────┐  │
│  │ Kidical Mass Mons                    14:00     │  │
│  │ Mons · ...                                     │  │
│  └────────────────────────────────────────────────┘  │
│  ...                                                 │
│                                                      │
├──────────────────────────────────────────────────────┤
│ FOOTER                                               │
└──────────────────────────────────────────────────────┘
```

**Past mode (desktop):**
```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  Events                                              │
│  Find a ride near you                                │
├──────────────────────────────────────────────────────┤
│ [Upcoming] [Past ●]    Location: [ All locations ▼ ] │
├──────────────────────────────────────────────────────┤
│                                                      │
│  March 2026                                          │
│  ┌────────────────────────────────────────────────┐  │
│  │ Kidical Mass Schaerbeek     29 Mar · 15:00     │  │ ← muted
│  │ Schaerbeek · Place Colignon                    │  │
│  └────────────────────────────────────────────────┘  │
│  ...                                                 │
│                                                      │
│  February 2026                                       │
│  ...                                                 │
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
│  Events              │
│  Find a ride near you│
├──────────────────────┤
│ [Upcoming] [Past]    │  ← sticky compact bar
│ [All locations  ▼ ]  │
├──────────────────────┤
│                      │
│  Sat 19 April        │
│ ┌──────────────────┐ │
│ │ KM Evere–Haren   │ │
│ │ 15:00 · Evere    │ │
│ │ Place de la...   │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ KM Ixelles       │ │
│ │ 15:00 · Ixelles  │ │
│ │ Place Flagey     │ │
│ └──────────────────┘ │
│                      │
│  Sat 26 April        │
│ ┌──────────────────┐ │
│ │ KM Forest–Vorst  │ │
│ │ 15:00 · Forest   │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ KM Woluwe-St-P.  │ │
│ │ 15:00 · Woluwe   │ │
│ └──────────────────┘ │
│                      │
│  Sat 10 May ★        │
│ ┌──────────────────┐ │
│ │ ★ GRANDE KM 2026 │ │
│ │ 15:00 · BXL      │ │
│ │ Place du Trône   │ │
│ └──────────────────┘ │
│  ...                 │
├──────────────────────┤
│ FOOTER               │
└──────────────────────┘
```

### Section annotations

- **Page header:** Title + 1-line subtitle only. No hero image — this is a scanner, not a storyteller.
- **Filter bar:** Upcoming/past toggle (tab style) + location dropdown. Sticky on scroll so filtering always accessible. On mobile: tabs on one line, location dropdown on the next line.
- **Date group header:** Day of week + full date, prominent typography. "Today" / "Tomorrow" labels replace the date when applicable.
- **Event card:** Compact horizontal. No image. Title, time, municipality name, meeting point address (truncated to one line if needed). Entire card is tappable.
- **Grande Kidical Mass:** Same card, different visual treatment — ★ icon prefix in title, star badge on the date group header. Not a separate list.
- **Past event cards:** Same structure, lower contrast (muted text, lighter border) — signals "archive" not "action".
- **Empty state — no upcoming:** "No upcoming rides right now. The season runs from March to November — check back soon!"
- **Empty state — location filtered, no results:** "No upcoming rides in [municipality]. Try 'All locations' to see rides nearby."

---

## Open Questions / Necessary Refinements

1. **Filter bar mobile — sticky behaviour:** On mobile, the sticky filter bar takes vertical space that's scarce. Proposed: it collapses to a compact single-line bar (showing selected filter state) after scrolling past it. Confirm UX implementation approach with Nico.
2. **Location filter options:** The dropdown lists chapters/municipalities. Should Walloon and Flemish municipalities be grouped separately from Brussels ones, or is a flat alphabetical list sufficient at current volume (~20 chapters)?
3. **Grande Kidical Mass visual differentiation:** The ★ badge is proposed. The exact visual treatment (colour, size, position) is a surface-level decision — confirm at design time.
4. **iCal / calendar subscription:** The site-level spec mentions per-region iCal subscriptions. Where does this CTA live? Proposed: on the event detail page only (not the list). If a global "subscribe to all events" subscription is wanted, a small CTA could appear at the top of the filter bar. Flag to Nico.
5. **Past events volume:** How far back does the archive go? 2020 = ~150+ parades. A year selector or lazy-load strategy may be needed to prevent an overwhelming archive list. Flag to Nico.

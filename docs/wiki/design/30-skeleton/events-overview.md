---
title: Kalender — Events overview
tags: []
sources: [notion, raw/website/agenda.md, wiki/design/20-structure.md]
phase: design
updated: 2026-06-03
---

Status: ✅ UX re-planned 2026-06-03 (NL framing + email opt-in homed). Live view still EN wireframe stub (`activities/index.blade.php`). Page URL: `/events` · **nav label NL = "Kalender"** · route `activities.index` (`/nl/events`, `/fr/events`, `/en/events` later).

**Summary:** One job — get a family to the right event detail page in under 10 seconds. Location filter + date-grouped list. Upcoming = date grouping. Past = month grouping. Cards are compact and text-driven. Filters are minimal by design. Grande Kidical Mass = featured badge on a standard card. **Rides only** — meetups/workshops never appear here (they live on chapter pages, D-2/J1). The **per-region "mis geen fietstocht" email opt-in is homed on this page** (resolves old open-Q #4) as a calm band *after* the list, scoped to the active location filter.

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
- Chronological **rides** list with date grouping (`kidicalmass` activity type only)
- Location filter (by chapter/municipality)
- Upcoming/past toggle
- Event cards with: title, date, time, municipality, meeting point
- Each card links to the event detail page

**Should have:**
- "Today" / "Tomorrow" contextual labels
- Featured badge for Grande Kidical Mass
- **Per-region "mis geen fietstocht" email opt-in** — the low-frequency "next ride near you" subscription (Scope feature, **homed here** per [20-structure.md](../20-structure.md)). Scoped to the active location filter; double opt-in for GDPR. Calm band *after* the list, not competing with the scan.

**Out of scope:**
- **Meetups / workshops** — rides only here (D-2/J1); meetups surface on chapter pages, never in this national list
- Map view (detail page has the map)
- RSVP / attendance count (deferred — "I'm coming" is volunteer-only, on the detail page, D-1)
- Free-text search (volume doesn't warrant it)
- Event images on cards (rides are practical meetups, not visual showcases)
- Per-event iCal button (lives on the event detail page, not the list — there is an `activities.ical` per-activity route; the list-level subscription is the email opt-in above, not iCal)

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
5. **"Mis geen fietstocht" email opt-in band** — contextual to the active location filter; sits after the list (post-scan), before the footer

**Key links out:**
- Event cards → /events/[slug]
- Email opt-in → confirmation (double opt-in); no page navigation

---

## Skeleton

**Filter bar is sticky on scroll** — on desktop it stays fixed below the nav so the location filter remains accessible while scrolling a long list. On mobile it collapses to a compact single-line bar.

**No pagination** — the event volume (~60/season = ~5–10 upcoming at any time) fits on one scroll. If future growth changes this, add load-more.

**Grande Kidical Mass** uses the same card component as any event but with a featured badge (★ icon + "Featured" label). The date header for that weekend may also carry a visual cue.

### Desktop (NL route shown — `/nl/events`)

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  Kalender                                            │
│  Vind een fietstocht bij jou in de buurt             │
├──────────────────────────────────────────────────────┤
│ [Aankomend ●] [Voorbije]    Gemeente: [ Alle ▼ ]     │  ← sticky
├──────────────────────────────────────────────────────┤
│                                                      │
│  Zaterdag 19 april                                   │
│  ┌────────────────────────────────────────────────┐  │
│  │ Kidical Mass Evere – Haren           15:00     │  │
│  │ Evere · Place de la Mairie                     │  │
│  └────────────────────────────────────────────────┘  │
│  ┌────────────────────────────────────────────────┐  │
│  │ Kidical Mass Ixelles – Elsene        15:00     │  │
│  │ Elsene · Place Flagey                          │  │
│  └────────────────────────────────────────────────┘  │
│                                                      │
│  Zaterdag 26 april                                   │
│  ┌────────────────────────────────────────────────┐  │
│  │ Kidical Mass Forest – Vorst          15:00     │  │
│  │ Vorst · Parvis St-Denis                        │  │
│  └────────────────────────────────────────────────┘  │
│                                                      │
│  Zaterdag 10 mei  ★ Uitgelicht                       │
│  ┌────────────────────────────────────────────────┐  │
│  │ ★  GRANDE KIDICAL MASS 2026          15:00     │  │
│  │ Brussel · Place du Trône                       │  │
│  └────────────────────────────────────────────────┘  │
│                                                      │
│  Zaterdag 17 mei                                     │
│  ┌────────────────────────────────────────────────┐  │
│  │ Kidical Mass Mons                    14:00     │  │
│  │ Mons · ...                                     │  │
│  └────────────────────────────────────────────────┘  │
│  ...                                                 │
│                                                      │
├──────────────────────────────────────────────────────┤
│  [ light band — contextual to the active filter ]    │
│  Mis geen fietstocht                                 │
│  Eén seintje per maand met de fietstochten bij jou   │
│  in de buurt. Geen spam, je kan je altijd            │
│  uitschrijven.                                       │
│  [ e-mail _______________ ]  [ Hou me op de hoogte ] │
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
- **"Mis geen fietstocht" email opt-in band (NEW — homes the Scope subscription):** A single calm band *after* the list, never before it — the page's one job is the scan; the reminder is the "before you go" beat. **Contextual to the active location filter:** "Alle gemeenten" → "fietstochten bij jou in de buurt" (per-region by the email's own geo, or a region picker on submit); a selected municipality → "fietstochten in [Gemeente]". One email field + one button, no account. Double opt-in (GDPR). On the empty-state (season break), this band becomes the *primary* action — when there's nothing to scan, "leave your email and we'll tell you when the season starts" is the right ask. Reuses the per-chapter opt-in control from the chapter page (same component, region vs group scope).

---

## Open Questions / Necessary Refinements

1. **Filter bar mobile — sticky behaviour:** On mobile, the sticky filter bar takes vertical space that's scarce. Proposed: it collapses to a compact single-line bar (showing selected filter state) after scrolling past it. Confirm UX implementation approach with Nico.
2. **Location filter options:** The dropdown lists chapters/municipalities. Should Walloon and Flemish municipalities be grouped separately from Brussels ones, or is a flat alphabetical list sufficient at current volume (~20 chapters)?
3. **Grande Kidical Mass visual differentiation:** The ★ badge is proposed. The exact visual treatment (colour, size, position) is a surface-level decision — confirm at design time.
4. ~~**iCal / calendar subscription**~~ ✅ **Resolved 2026-06-03.** The list-level subscription is the **per-region "mis geen fietstocht" email opt-in**, homed in a band *after* the list (see Skeleton). Per-event iCal stays on the event detail page (`activities.ical` route). No iCal control on the list.
5. **Past events volume:** How far back does the archive go? 2020 = ~150+ parades. A year selector or lazy-load strategy may be needed to prevent an overwhelming archive list. Flag to Nico.
6. **Email opt-in geo-scoping (NEW):** When "Alle gemeenten" is active, how does the opt-in know which region to subscribe the user to? Options: (a) a region picker appears on submit; (b) the email is national-but-low-frequency; (c) postal-code field. Proposed: (a) — a single region/chapter picker on submit so the promise ("ritten bij jou in de buurt") stays honest. Confirm the `Email subscription` model's `scope` field (region vs Group) with Nico.
7. **Live view is still EN + lacks the opt-in (NEW):** `activities/index.blade.php` renders English headers ("Activities", "Find a ride near you"), has no NL strings, and has no opt-in band. The filter bar is a placeholder. NL pass + opt-in band are build tasks once this plan lands.

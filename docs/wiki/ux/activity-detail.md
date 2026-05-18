---
title: Activity Detail
tags: []
sources: [notion]
updated: 2026-04-13
---

Built by Nico. UX spec added. Page URL: `/events/[slug]`

---

## Strategy

> Why does this page exist? For whom?

### Primary User

A family making one decision: **are we going?**

They arrive already curious — via a shared WhatsApp link, the homepage events list, or a Google search for "Kidical Mass [their city]." They are not skeptical. They need practical confirmation, not persuasion.

### Secondary Users

- **Potential volunteer** — curious about getting involved; the page is a soft entry point into contributing
- **Press** — occasionally needs a contact point; not a designed experience, but the organising team section serves them passively

### The Decision and What Follows

The page supports one decision. After "yes, we're going," the user needs to:
- Save the date (calendar export)
- Share it with a partner or friend (WhatsApp share link)

No transactional flow, no registration, no account needed to attend.

### What Makes This Page Feel Like Kidical Mass

Not a generic event listing. Four things that carry the character:

1. **The route is visible** — a map showing the ride moving through the neighbourhood communicates the street-reclaiming nature of the event before a word is read
2. **The local team is named** — people, not an organisation; neighbourhood energy, not institutional
3. **The volunteer ask is tied to the team** — "want to ride alongside them?" not a generic CTA
4. **Practical details are warm and concrete** — pace, distance, duration as reassurance through specificity, not policy language

### Tone

Warm, specific, sensory. Joyful without being frivolous. Local — named landmarks, named people, named streets. See [tone-of-voice.md](../tone-of-voice.md).

---

## Scope

> What does this page contain and do?

### Content Fields

**Required**
- Chapter name + postal code
- Date + time
- Meeting point (named landmark + address)
- Route map (Komoot embed)
- Distance (km)
- Duration (max)
- Free admission / no registration
- Age: all ages
- Pace: at the rhythm of the youngest child

**Optional**
- Theme / event name (e.g. "Safety First", "Spooky Edition")
- Programme notes (music, animations, special activities)
- Campaign context (e.g. 2026 Safety First campaign)

**Community layer**
- Chapter name + link (with signal that this is a recurring monthly series)
- Local organising team (names)
- Soft volunteer CTA — tied directly to the team

**Partners**
- Local partners / co-organisers (logos or names)

**Actions**
- Add to calendar (iCal export)
- Share link (WhatsApp + general)

**Legal**
- Photo permission note (one line)

### What Is NOT on This Page

- Facebook link (removed — no longer needed)
- Registration or RSVP flow (deferred — possible future feature)
- "Who's attending" / social proof of attendance (deferred)
- Private volunteer back-of-event (deferred to later phase)

---

## Structure

> What goes where, and in what order?

### Title Convention

`Kidical Mass [postal code] — [date]`
e.g. "Kidical Mass 1000 — 24 mei"

Postal code appears naturally in the title (language-neutral, follows Facebook convention). Not used as an oversized typographic element.

### Page Sections (top to bottom)

**1. Hero — above the fold (split layout)**
Left: Chapter name, title (postal code + date), day + time, meeting point, actions (add to calendar, share)
Right: Route map — shows start point and full route. Large enough to read the neighbourhood.

Both panels are visible above the fold on desktop. On mobile, map stacks below the essential info.

**2. Practical strip**
Single scannable line: distance · duration · free · all ages · music · children accompanied by adult

**3. What to expect**
2–3 lines, sensory and warm. Pace, atmosphere, what happens. Not a policy statement.
Optional: campaign/theme context if the event has one.

**4. Chapter context**
"This ride is part of [Chapter name]'s monthly series →"
Links to the chapter page. Signals the recurring community without listing future dates.

**5. Organising team + volunteer ask**
Names of the local team. Directly below: soft CTA — "Want to ride alongside them as a pink vest? →"

**6. Local partners**
Logo strip or name list of local co-organisers and partners.

**7. Photo permission**
One quiet line. Legally necessary, not visually prominent.

### Key Structural Decisions

- **Actions live in the hero** — the decision is made at the top; calendar and share should be reachable without scrolling
- **Map is in the hero, not a separate section** — visual and informational at the same time
- **Volunteer ask follows the team** — the connection between seeing who organises it and wanting to join them must be immediate, not separated by content
- **Practical strip is one line** — scannability over prose; the Facebook examples confirm this pattern works

---

## Skeleton

> What goes where on screen?

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Kidical Mass 1000 — 24 mei          [ MAP ───────] │
│  Bruxelles-Ville / Brussel-Stad      [ route map  ] │
│                                      [           ] │
│  Zondag 24 mei · 15h00               [  start ●  ] │
│  Place du Trône                      [    ↓      ] │
│                                      [  route    ] │
│  [ + Agenda ]  [ Delen ]             [           ] │
│                                      [───────────] │
├──────────────────────────────────────────────────────┤
│  5–7 km  ·  max 1u  ·  Gratis  ·  Alle leeftijden  │
│  🎵 Muziek onderweg  ·  Kinderen begeleid door adult │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Wat te verwachten                                   │
│  We rijden op het tempo van de jongste. Muziek,      │
│  nieuwe vrienden, een andere kijk op je buurt.       │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Onderdeel van Kidical Mass Brussel-Stad →           │
│  Elke maand een nieuwe rit door de stad.             │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Georganiseerd door                                  │
│  [naam]  [naam]  [naam]  (lokale vrijwilligers)      │
│                                                      │
│  Wil je meerijden als roze hesje? →                  │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Partners                                            │
│  [logo]  [logo]  [logo]                              │
│                                                      │
├──────────────────────────────────────────────────────┤
│  Tijdens de rit worden foto's gemaakt. Door deel     │
│  te nemen ga je akkoord met publicatie.              │
├──────────────────────────────────────────────────────┤
│ FOOTER                                               │
└──────────────────────────────────────────────────────┘
```

### Mobile

```
┌──────────────────────┐
│ NAV (hamburger)      │
├──────────────────────┤
│                      │
│  Kidical Mass 1000   │
│  24 mei              │
│  Bruxelles-Ville     │
│                      │
│  Zondag · 15h00      │
│  Place du Trône      │
│                      │
│  [ + Agenda ]        │
│  [ Delen ]           │
│                      │
├──────────────────────┤
│  [ MAP — route ]     │
│  [ start ● → route ] │
│  [ full width ]      │
├──────────────────────┤
│  5–7 km · max 1u     │
│  Gratis · Alle lft.  │
│  🎵 Muziek           │
├──────────────────────┤
│  Wat te verwachten   │
│  We rijden op het    │
│  tempo van de        │
│  jongste...          │
├──────────────────────┤
│  Onderdeel van       │
│  KM Brussel-Stad →   │
├──────────────────────┤
│  Georganiseerd door  │
│  [naam] [naam]       │
│                      │
│  Wil je meerijden    │
│  als roze hesje? →   │
├──────────────────────┤
│  Partners            │
│  [logo] [logo]       │
├──────────────────────┤
│  Foto's worden       │
│  gedeeld. Akkoord    │
│  door deel te nemen. │
├──────────────────────┤
│ FOOTER               │
└──────────────────────┘
```

### Annotations

- **Title:** chapter name + postal code + date — language-neutral, follows established convention
- **Map:** Komoot embed showing full route with start marker. Large on desktop (right half of hero). Full-width below the fold on mobile.
- **Actions in hero:** calendar export and share link sit with the essential info — available without scrolling
- **Practical strip:** one scannable line of metadata, not prose
- **"What to expect":** 2–3 lines max, sensory and warm — written per the tone of voice guide
- **Volunteer ask:** immediately follows the team names — the connection is the point
- **Photo permission:** visually quiet (small text), legally present

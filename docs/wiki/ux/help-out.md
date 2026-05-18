---
title: Help Out
tags: []
sources: [notion, raw/website/volunteer.md]
updated: 2026-04-13
---

Status: ✅ Complete. Page URL: `/help-out` · `/nl/help-out` · `/fr/help-out` · `/en/help-out` ✅

**Page title confirmed: "Help out" / "Meehelpen" / "S'engager"** — warmer than "Volunteer", fits ToV. *(Redirect from /contribute if that URL was ever public)*

**Summary:** Two questions in sequence: "How do I help an existing chapter?" (roles + form) then "What if there's no chapter near me?" (start a chapter CTA). Roles are invitations, not job descriptions. 5 roles confirmed: pink vest, co-organiser, communicator, photographer, DJ. The form routes to the nearest chapter lead — not a central inbox. Updated: "What we expect" honest commitment section added from raw volunteer page.

---

## Strategy

The conversion page for people who want to help. Replaces the current email-only signup with a clear path from curiosity to contact.

### Who arrives and in what mental state

**Former or current ride participant — warm and inspired**
The most common path. They attended a ride, they loved it, they thought "I want to be part of this." They arrive already sold. The page must not waste their energy with a heavy pitch — they're ready. What they need is: role clarity (what specifically could I do?), commitment honesty (what does it actually involve?), and a simple contact action.

**Curious outsider — "I've heard about this, can I help?"**
Arrived via a friend, social media, or the homepage volunteer CTA. Not a former rider. They need a bit more context on what Kidical Mass is before they can self-identify a role. The "why volunteer" pitch handles this.

**Potential chapter lead — "There's no chapter near me"**
Usually a parent in a city without a Kidical Mass. They may not even know "start a chapter" is an option — the page needs to surface it. The "Don't see your city?" section handles this, but it must be prominent enough to catch them before they leave.

### Key psychological insight

The raw volunteer page says explicitly: "Tu n'as pas besoin d'être un pro du vélo ou de l'événementiel, juste l'envie d'aider." This is the central message. Many potential volunteers are hesitating because they think they're not qualified. The page's job is to dissolve that hesitation with warmth and specificity, not a list of requirements.

At the same time: the raw page is honest about what the commitment involves (4×/year meetings, following the community guidelines). This honesty is a strength, not a deterrent — it sets expectations and attracts committed people. Include this clearly.

### Organisational objectives

Replace the bike@ email black hole with a structured, routed contact form. Every submission reaches the right chapter lead automatically. No central inbox.

---

## Scope

**Must have:**
- Overview of volunteer roles (5 confirmed: pink vest, co-organiser, communicator, photographer, DJ)
- "What to expect as a volunteer" — what the movement offers (community, meetups, kit, training)
- "What we expect" — honest commitment (4×/year meetings, following guidelines) — from raw volunteer page ✅
- Contact form routed to nearest chapter
- "Start a chapter" section (static for MVP)

**Should have:**
- Short pitch ("Why volunteer") before the roles
- Link to volunteer guidelines (Google Doc — external, opens new tab)

**Out of scope:**
- Structured onboarding workflow (deferred)
- Volunteer dashboard (deferred)
- Per-role signup (MVP routes everyone through one form)
- Inline volunteer rules content (Google Doc stays external for MVP)
- A chapter map or locator (use /chapters for that)

---

## Structure

Single page, no sub-navigation. Answers: "How can I help?" then "What if there's no chapter near me?"

**Section flow:**
1. Page header — "Help out" / "Meehelpen" / "S'engager"
2. Why volunteer — the pitch
3. What you'll get + what we expect (combined honest section)
4. Roles overview (5 roles as invitation cards)
5. Contact form — routed to nearest chapter
6. Start a chapter — CTA for new cities

**Key links out:**
- Form → chapter lead email (routed by municipality selection)
- "Start a chapter" → mailto:bike@kidicalmass.be
- Chapter pages → /chapters/[code]
- Volunteer guidelines → Google Doc (external, new tab)

---

## Skeleton

**Form is the primary action.** Everything above it is orientation and motivation. The form itself is short — name, email, municipality, role interest checkboxes, optional message.

**Roles as orientation, not form fields.** The 5 role cards help people self-identify before reaching the form. The form's role checkboxes mirror the role names. No one-role-per-form-path — all go through the same form.

**Honest commitment section.** Placed before the form, after roles. Warmly worded but not hidden. Inspired by the raw volunteer page: 4×/year meetings + following the community guidelines. Honest expectations attract the right people and reduce drop-off after signup.

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  Help out                                            │
│  Join hundreds of people who make every ride happen. │
├──────────────────────────────────────────────────────┤
│                                                      │
│  [Pitch — 3–4 sentences, movement-first]             │
│  Being a Kidical Mass volunteer means showing up for │
│  your neighbourhood. You'll be part of a team of     │
│  parents, cyclists, and community builders who make  │
│  every ride safe, joyful, and real. A few hours on   │
│  a Sunday — and much more back.                      │
│                                                      │
│  Read the volunteer guidelines →         [ext. link] │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  How you can help                                    │
│                                                      │
│  ┌──────────────┐  ┌──────────────┐                 │
│  │ 🦺 Pink vest  │  │ 🗓 Co-org    │                 │
│  │               │  │              │                 │
│  │ Ride alongside│  │ Plan & prep  │                 │
│  │ the group.    │  │ the route,   │                 │
│  │ Keep everyone │  │ timing, and  │                 │
│  │ together and  │  │ logistics.   │                 │
│  │ safe.         │  │ The backbone │                 │
│  └──────────────┘  └──────────────┘                 │
│  ┌──────────────┐  ┌──────────────┐                 │
│  │ 📢 Comms      │  │ 📸 Photo     │                 │
│  │               │  │              │                 │
│  │ Share the     │  │ Capture the  │                 │
│  │ rides online  │  │ best moments.│                 │
│  │ and in your   │  │ Your photos  │                 │
│  │ neighbourhood.│  │ bring new    │                 │
│  │               │  │ families in. │                 │
│  └──────────────┘  └──────────────┘                 │
│  ┌──────────────┐                                   │
│  │ 🎵 DJ        │                                   │
│  │               │                                   │
│  │ Set the mood  │                                   │
│  │ before, during│                                   │
│  │ and after.    │                                   │
│  │ Music is half │                                   │
│  │ the vibe.     │                                   │
│  └──────────────┘                                   │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  What joining looks like                             │
│                                                      │
│  You'll get:                                         │
│  · Kidical Mass kit and support from day one         │
│  · Optional training (safety, route planning)        │
│  · 4 volunteer meetups a year — good food included   │
│  · A community of parents and bike enthusiasts       │
│                                                      │
│  We ask:                                             │
│  · Show up with enthusiasm and a positive attitude   │
│  · Follow our community guidelines                   │
│  · Send one representative to each annual meetup     │
│    (if you're part of a chapter team)                │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  I want to get involved                              │
│  Fill in the form — your nearest chapter lead        │
│  will be in touch.                                   │
│                                                      │
│  Name ___________________________                    │
│  Email __________________________                    │
│  Municipality  [ dropdown ▼ ]                        │
│  I'm interested in:                                  │
│  [✓] Pink vest  [ ] Co-organiser  [ ] Comms          │
│  [ ] Photographer  [ ] DJ  [ ] Not sure yet          │
│  Message (optional) _____________________            │
│                       _____________________           │
│                                                      │
│                  [ I'm in → ]                        │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  [distinct background / section break]               │
│                                                      │
│  Don't see your city?                                │
│  Starting a chapter takes a core team of 2–3 people, │
│  a meeting point, and a route idea. We handle the    │
│  brand, training, and national visibility.           │
│  If you're curious, reach out.                       │
│                                                      │
│  [ Email the coordination team → ]                  │
│  See which cities already have a chapter →          │
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
│  Help out            │
│  Join hundreds of    │
│  people who make     │
│  every ride happen.  │
├──────────────────────┤
│  [Pitch — 3–4 lines] │
│  Being a KM          │
│  volunteer means...  │
│                      │
│  Guidelines →        │
├──────────────────────┤
│  How you can help    │
│                      │
│ ┌──────────────────┐ │
│ │ 🦺 Pink vest      │ │
│ │ Ride alongside.  │ │
│ │ Keep it safe.    │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 🗓 Co-organiser  │ │
│ │ Plan and prep    │ │
│ │ the ride.        │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 📢 Comms          │ │
│ │ Share online and │ │
│ │ in your area.    │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 📸 Photographer  │ │
│ │ Capture the best │ │
│ │ moments.         │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 🎵 DJ             │ │
│ │ Set the mood for │ │
│ │ the ride.        │ │
│ └──────────────────┘ │
├──────────────────────┤
│  What joining looks  │
│  like                │
│                      │
│  You'll get:         │
│  · Kit and support   │
│  · Optional training │
│  · 4 meetups/year    │
│  · A community       │
│                      │
│  We ask:             │
│  · Enthusiasm        │
│  · Follow guidelines │
│  · Annual meetup rep │
├──────────────────────┤
│  I want to get       │
│  involved            │
│                      │
│  Name _____________  │
│  Email _____________ │
│  Municipality  [▼]   │
│  Interested in:      │
│  [  ] Pink vest      │
│  [  ] Co-organiser   │
│  [  ] Comms          │
│  [  ] Photographer   │
│  [  ] DJ             │
│  [  ] Not sure yet   │
│  Message (opt.) ___  │
│                      │
│  [ I'm in → ]        │
├──────────────────────┤
│  [section break]     │
│  Don't see your      │
│  city?               │
│  It takes 2–3 people │
│  and a route idea.   │
│  We handle the rest. │
│                      │
│  [ Email the team →] │
│  See all chapters →  │
├──────────────────────┤
│ FOOTER               │
└──────────────────────┘
```

### Annotations

- **Header subtitle:** "Join hundreds of people who make every ride happen." Grounded in real numbers (100+ active volunteers per the raw site). Community-first, not movement-first.
- **Pitch paragraph:** 3–4 sentences max. Ends with a time/reward framing ("a few hours on a Sunday — and much more back"). Link to external Google Doc for guidelines — opens new tab.
- **Role cards:** 5 cards, 2-column grid on desktop. Icon + role name + 2-sentence description. Invitation language, not job description language. Odd fifth card (DJ) sits left-aligned in its own row on desktop; full width on mobile.
- **"What joining looks like":** Combined "get/ask" framing borrowed from the raw volunteer page. Short bullet pairs. Honest without being heavy. This section reduces anxiety by setting clear expectations.
- **Contact form:** Municipality dropdown determines routing. Role checkboxes let people pre-identify without obligation. "Not sure yet" checkbox is important — removes the barrier of needing to know your role before reaching out.
- **Submit CTA:** "I'm in →" — short, direct, confident. ToV register: enthusiastic, not corporate.
- **Start a chapter:** Visually distinct section (different background). Short, honest, warm. Email CTA only (mailto: link) — structured intake deferred. Secondary link to /chapters so they can see the map.

---

## Open Questions / Necessary Refinements

1. **Municipality dropdown options:** The form routes to a chapter lead by municipality. This requires the admin panel to have a chapter → lead email mapping. Confirm with Nico that this routing logic is in place at MVP.
2. **"No chapter near me" form routing:** If someone selects a municipality without a chapter, the form routes to bike@kidicalmass.be. Confirm this fallback with Leticia — is this the right address?
3. **Volunteer guidelines link:** The raw page links to a Google Doc. Confirm the link is permanent and accessible without a Google account. If not, consider hosting the PDF on the site itself.
4. **4 meetups/year — accuracy:** The raw site says 4 meetups per year. Confirm this is still the expectation for all Belgian chapters (not just Brussels). If Walloon/Flemish chapters have different meetup expectations, adjust the "We ask" copy.
5. **"I'm in →" post-submission:** On form submit, the page shows a confirmation message inline (not a redirect). Copy for the confirmation state needs to be written. Example: "Thanks! Your local Kidical Mass lead will be in touch soon. In the meantime, come find your local chapter →"
6. **Role cards — brief copy:** The descriptions above are working examples. Final copy needs a ToV pass — particularly ensuring the pink vest description doesn't sound intimidating ("safety" language can feel heavy).

---
title: Getting Started
tags: []
sources: [notion, raw/website/help-je-n-ai-pas-de-vélo.md, raw/website/activités-vélo-fietsactiviteiten-kids.md]
updated: 2026-04-13
---

Status: ✅ Complete. Page URL: `/getting-started` (trilingual: `/nl/getting-started`, `/fr/getting-started`, `/en/getting-started`)

**Summary:** This page removes the last friction before a first ride. It reassures — it doesn't sell. No equivalent on the current site. "Don't have a bike?" is a dedicated section, not a footnote. The page ends with a single CTA to /events. Updated: My Kids Bikes added to the "Don't have a bike?" section based on raw site content.

---

## Strategy

A new page with no equivalent on the current site. Fills the onboarding gap for families who are curious but haven't attended a ride yet.

### Who arrives and in what mental state

**First-timer families — "I think this is for us, but..."**
The main audience. They've heard about Kidical Mass (from a friend, from Facebook, from seeing a ride in the street) and they want to come. But they're arriving with a small stack of practical anxieties. These aren't emotional fears — they're logistics uncertainties. And because the uncertainties are practical, the reassurance must also be practical: concrete facts, not warm words.

Common first-timer worries (based on the ToV guide and raw volunteer page):
- "Our kids are young — is 4 years old OK?"
- "What if our kids can't keep up / get tired?"
- "Do I need to be a confident cyclist myself?" (Many parents are riding in traffic for the first time — the volunteer page names this explicitly)
- "What do we need to bring? What if it rains?"
- "Do we need to register? Is it free?"
- "What if we don't have bikes?"

Each of these anxieties has a short, factual answer. The FAQ format is exactly right for this page.

**Families without bikes**
A subset of first-timers with a specific barrier. The movement is explicitly inclusive — "no bike is no problem" is part of the brand promise. This group needs to be met with a dedicated section, not a footnote. Without it, they quietly assume the ride isn't for them.

**Parents unsure of their own cycling ability**
Related to above. Some parents haven't cycled in years or have never cycled in traffic. The volunteer page says: "Tu n'as pas besoin d'être un pro du vélo." The Getting Started page should say the same for ride participants.

### What good looks like

A family reads this page and thinks: "OK, we can do this." Then they click the CTA at the bottom and find a ride. The page has done its job. It does not need to be loved — it needs to be believed.

---

## Scope

**Must have:**
- What to expect at a ride (pace, duration, vibe, safety)
- Practical FAQ (age, gear, weather, registration)
- Don't have a bike? (Loopz, Fietsbieb, Kidical Mouse, My Kids Bikes)

**Should have:**
- Other bike activities for kids in Belgium (ProVelo, Cyclo, Ride Your Future — enough content for a structured section, not just a paragraph)

**Out of scope:**
- Event calendar (that's /events)
- Volunteer info (that's /help-out)
- Per-chapter local resources (those live on chapter pages)
- Hard sell or urgency language (ToV: no urgency for its own sake)
- Registration or sign-up (there is no sign-up for rides)

### "Don't have a bike?" resources (confirmed from raw site)

1. **Loopz** — bike subscription from €6/month via local partner shops. Promo code KIDICALMASS = 2 months free. National.
2. **Fietsbieb / Vélothèque** — borrow a child's bike (up to 12 years) for €20/year + €20 deposit. Available in 10 Brussels communes: Anderlecht, Ixelles, Etterbeek, Jette, Laeken, Molenbeek, Neder-Over-Heembeek, Schaerbeek, Sint-Agatha-Berchem, Uccle. Brussels-only.
3. **Kidical Mouse** — cargo bike available at the ride start for families who need it. Operational ✅.
4. **My Kids Bikes** — subscription service, Woom & BeMoov bikes. From raw site: mykidsbikes.be.

Note: Cyclo (second-hand bikes for sale) exists in the raw content but is a purchase option, not a loan/subscription. Include as a brief additional note rather than a full card.

---

## Structure

Single page. Story arc: "Here's what happens" → "Common worries answered" → "No bike? No problem" → "Other ways to ride" → "Ready? Find your first ride."

**Section flow:**
1. Page header
2. What to expect at a ride
3. Practical FAQ
4. Don't have a bike?
5. Other bike activities (structured section — 3 substantive activities from raw site)
6. CTA to /events

**Key links out:**
- CTA → /events
- Loopz / partner links (external)
- Fietsbieb, Kidical Mouse, My Kids Bikes (external)
- "Find your local chapter →" → /chapters
- ProVelo Families on Bike → external
- Cyclo → external
- Ride Your Future → external

---

## Skeleton

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  Getting Started                                     │
│  Come as you are. Here's what to expect.             │
├──────────────────────────────────────────────────────┤
│                                                      │
│  What to expect at a ride                            │
│                                                      │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐             │
│  │ 🚲        │ │ 🎵        │ │ 📍        │             │
│  │ 5–7 km   │ │ Music    │ │ Fixed    │             │
│  │ max 1 hr │ │ all the  │ │ meeting  │             │
│  │ slow pace│ │ way      │ │ point    │             │
│  └──────────┘ └──────────┘ └──────────┘             │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐             │
│  │ 🆓        │ │ 👶        │ │ 🦺        │             │
│  │ Free, no │ │ All ages │ │ Trained  │             │
│  │ sign-up  │ │ 3–12 +   │ │ pink     │             │
│  │          │ │ adults   │ │ vests    │             │
│  └──────────┘ └──────────┘ └──────────┘             │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Common questions                                    │
│                                                      │
│  Do I need to register?                              │
│  No. Just show up at the meeting point at the        │
│  listed time. No ticket, no name on a list.          │
│                                                      │
│  What age can children join?                         │
│  From around 3 years old and up. Children ride       │
│  on their own bike (no balance bikes) or sit on      │
│  a cargo bike or child seat. Adults always           │
│  responsible for their child's safety.               │
│                                                      │
│  Do I need to be a confident cyclist?                │
│  No. We ride at the pace of the youngest child.      │
│  Many parents are on a bike in traffic for the       │
│  first time. You're not alone.                       │
│                                                      │
│  What if it rains?                                   │
│  Rides happen in most weather. Check the             │
│  Facebook event or chapter page for cancellation     │
│  (rare — only in extreme conditions).                │
│                                                      │
│  What should we bring?                               │
│  Helmets recommended, not mandatory. Water.          │
│  That's it.                                          │
│                                                      │
│  Is it really free?                                  │
│  Yes — no registration, no entry fee, no cost.       │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Don't have a bike?                                  │
│  Not having a bike is not a reason to miss out.      │
│                                                      │
│  ┌────────────────────┐  ┌────────────────────┐     │
│  │ Loopz              │  │ Fietsbieb          │     │
│  │ Bike subscription  │  │ Borrow a child     │     │
│  │ from €6/month      │  │ bike for €20/year  │     │
│  │ Code KIDICALMASS   │  │ 10 Brussels        │     │
│  │ = 2 months free ✓  │  │ communes           │     │
│  └────────────────────┘  └────────────────────┘     │
│  ┌────────────────────┐  ┌────────────────────┐     │
│  │ Kidical Mouse      │  │ My Kids Bikes      │     │
│  │ Cargo bike at      │  │ Subscription       │     │
│  │ the ride start     │  │ Woom & BeMoov      │     │
│  │ (Brussels)         │  │ mykidsbikes.be     │     │
│  └────────────────────┘  └────────────────────┘     │
│                                                      │
│  Also: Cyclo (Brussels) sells second-hand bikes. →   │
│  Local options vary — check your chapter page. →    │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│  Other ways to cycle with your kids                  │
│  Kidical Mass isn't the only way to enjoy cycling    │
│  with your kids in Belgium.                          │
│                                                      │
│  ┌──────────────────────────────────────────────┐   │
│  │ Families on Bike — ProVelo                   │   │
│  │ Free coaching for Brussels families.          │   │
│  │ Learn to ride in traffic, choose a route,    │   │
│  │ test bikes. Anderlecht + Saint-Gilles/Forest.│   │
│  └──────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────┐   │
│  │ Duo mechanics — Cyclo                        │   │
│  │ Learn bike maintenance with your child (8+). │   │
│  │ Playful, practical, an hour together.        │   │
│  └──────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────┐   │
│  │ Pump Park — Ride Your Future                 │   │
│  │ 110m pumptrack + kids track in Laeken.       │   │
│  │ Build balance and confidence. All levels.    │   │
│  └──────────────────────────────────────────────┘   │
│                                                      │
├──────────────────────────────────────────────────────┤
│                                                      │
│         Ready for your first ride?                   │
│     [ Find a ride near you → ]                       │
│     Find your local chapter →                        │
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
│  Getting Started     │
│  Come as you are.    │
│  Here's what to      │
│  expect.             │
├──────────────────────┤
│  What to expect      │
│                      │
│ ┌────────┐ ┌───────┐ │
│ │ 🚲 5–7km│ │🎵Music│ │
│ │ 1hr max│ │on the │ │
│ │ slow   │ │ way   │ │
│ └────────┘ └───────┘ │
│ ┌────────┐ ┌───────┐ │
│ │📍 Fixed│ │🆓 Free│ │
│ │ meeting│ │no reg.│ │
│ └────────┘ └───────┘ │
│ ┌────────┐ ┌───────┐ │
│ │👶 All  │ │🦺Pink │ │
│ │ ages   │ │ vests │ │
│ └────────┘ └───────┘ │
├──────────────────────┤
│  Common questions    │
│                      │
│  Do I need to        │
│  register?           │
│  No. Just show up.   │
│                      │
│  What age?           │
│  From about 3 years. │
│                      │
│  Do I need to be a   │
│  confident cyclist?  │
│  No. Many parents    │
│  ride in traffic for │
│  the first time.     │
│                      │
│  What if it rains?   │
│  Rides happen in     │
│  most weather.       │
│                      │
│  What to bring?      │
│  Helmets + water.    │
│                      │
│  Is it really free?  │
│  Yes.                │
├──────────────────────┤
│  Don't have a bike?  │
│  Not a reason to     │
│  miss out.           │
│                      │
│ ┌──────────────────┐ │
│ │ Loopz            │ │
│ │ From €6/month    │ │
│ │ Code KIDICALMASS │ │
│ │ 2 months free    │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ Fietsbieb        │ │
│ │ €20/year child   │ │
│ │ bike borrow      │ │
│ │ 10 BXL communes  │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ Kidical Mouse    │ │
│ │ Cargo bike at    │ │
│ │ the start        │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ My Kids Bikes    │ │
│ │ Woom & BeMoov    │ │
│ │ subscriptions    │ │
│ └──────────────────┘ │
│  Also: Cyclo →       │
│  Chapter page →      │
├──────────────────────┤
│  Other ways to cycle │
│                      │
│ ┌──────────────────┐ │
│ │ Families on Bike │ │
│ │ ProVelo · Free   │ │
│ │ Brussels coaching│ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ Duo mechanics    │ │
│ │ Cyclo · Age 8+   │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ Pump Park        │ │
│ │ Ride Your Future │ │
│ │ Laeken           │ │
│ └──────────────────┘ │
├──────────────────────┤
│  Ready for your      │
│  first ride?         │
│  [ Find a ride → ]   │
│  Find your chapter → │
├──────────────────────┤
│ FOOTER               │
└──────────────────────┘
```

### Annotations

- **Page header subtitle:** "Come as you are. Here's what to expect." Warm and concrete. Not "everything you need to know" — that reads like a brochure. Passes the ToV one-line test.
- **What to expect:** 6 fact cards (icon + brief label + 1-line explanation). Not prose — facts as reassurance. Mobile: 2-column grid.
- **FAQ:** Bold question label, 1–3 sentence answer below. No accordion needed — 5–6 questions fit without excessive scrolling. Conversational, direct.
- **Don't have a bike?:** 4 resource cards in a 2×2 grid (desktop) / stacked list (mobile). Each card: name + 1-line description + key detail (price, promo, coverage). External links open new tab.
- **Other activities:** 3 structured cards. Not a bullet list — each deserves its own entry. These are external organisations; content is Brussels-centric but reflects actual available options.
- **Bottom CTA:** Full-width visually distinct section. Primary button to /events. Secondary text link to /chapters. Warm but not urgent.

---

## Open Questions / Necessary Refinements

1. **My Kids Bikes current status:** The raw site lists My Kids Bikes (mykidsbikes.be) as a sponsor. Verify it's still active and the website is live before including in the spec. If discontinued, remove.
2. **Cyclo note:** The raw site lists Cyclo for second-hand bike purchase. Proposed: include as a brief additional note ("Also: Cyclo Brussels sells second-hand bikes →") rather than a full card — it's a purchase, not a loan/subscription. Confirm this distinction is correct.
3. **"Other bike activities" — scope beyond Brussels:** The 3 activities (ProVelo, Cyclo, Ride Your Future) are all Brussels-based. The page is national. Proposed: include these as the Brussels examples with a note "More local activities may be available near you — ask at your chapter." If national equivalents are known, add them.
4. **Fietsbieb commune list:** The raw site lists 10 Brussels communes. These should be checked against the current Fietsbieb website before launch — the list may have changed.
5. **Kidical Mouse availability:** Operational ✅ per existing spec. Confirm it's available at any chapter's ride or only specific ones. If only some, the card copy should say "available at some rides — check your chapter page."
6. **FAQ — helmet policy:** The current proposal says "helmets recommended, not mandatory." Confirm this is the official Kidical Mass policy. If it varies by commune (some have mandatory laws), add a note.
7. **ProVelo Families on Bike:** Verify the programme is still active in 2026 and the locations (Anderlecht + Saint-Gilles/Forest area) are current before including.

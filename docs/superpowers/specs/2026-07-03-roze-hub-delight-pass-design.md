---
title: Roze-hub delight pass — moment-aware Overview (design)
tags: [design, roze-hesjes, chapters, delight]
sources:
  - docs/superpowers/specs/2026-06-18-roze-hesje-hub-split-design-handoff.md
  - docs/superpowers/specs/2026-06-18-roze-hesje-hub-overview-design.md
  - DESIGN.md
  - docs/wiki/tone-of-voice.md
phase: design
updated: 2026-07-03
---

# Roze-hub delight pass — moment-aware Overview

## Why

Demo feedback (Leticia): the hub UI could use more delight. The split-handoff brief already
set the bar the current build misses: arrival on the Overview should feel like *"a vibrant
community, we're doing this together"*, a *"warm shared room, not an app"*. Today the page
opens cold ("Je volgende rit", no greeting, no motion, photos land silently in a dropdown).

This pass makes the **Overview moment-aware**: it greets the hesje by name, reacts to what
is happening right now (fresh ride photos, an upcoming ride, a new member, the viewer being
new), and gains gentle entrance motion. Five delight ideas from the 2026-07-03 analysis are
in scope: greeting (1), staggered entrance (2), ride countdown (4), new-member celebration
(5), and the Monday-after recap card (7 — the priority).

**Out of scope:** sub-page motion, the group pride stats band, photo lightbox/upload,
milestone celebrations, anything depending on Nico's backend work (#37). Feed timestamps
stay approximate until the real change-feed lands; nothing here depends on it.

## Composition model

The Overview gets a visible header — **h1 greeting + one moment-aware lead line** — then a
**lead-card slot**, then the existing sections unchanged ("Je volgende rit", "Voor de rit",
"Sinds je laatste bezoek" keep their order below it).

A single resolver picks the page's **moment**, first match wins:

1. **welcome** — the viewer is inside their own welcome window (existing
   `roze_welcome_{group}` cookie logic, `ROZE_WELCOME_WEEKS = 2`).
2. **recap** — the chapter's most recent past published ride started **≤ 5 days ago** and
   its `gallery` media collection has **≥ 1 photo**.
3. **pre-ride** — the next published ride is **≤ 7 days** away.
4. **default** — none of the above.

The moment drives the greeting text and whether the recap card renders. Only the recap
moment adds a card; welcome and pre-ride are greeting-line variants. The page therefore
never gains more than one new block, and busy weeks stay calm: one lead moment, everything
else in its normal section.

### Greeting

First name = first word of `$user->name`. Same Caprasimo scale/treatment as the sub-page
h1s ("Agenda van Schaarbeek"). The current sr-only h1 on the Overview is replaced by this
visible h1. Copy (NL, no em-dashes; final wording open to Frederik's edit at build time):

| Moment | h1 | Lead line |
|---|---|---|
| welcome | Welkom bij de hesjes, Lien. | Fijn dat je meerijdt. Begin bij Aan de slag, of kijk gewoon even rond. |
| recap | Dag Lien. | Dat was een mooie zondag. |
| pre-ride | Dag Lien. | Zondag rijden we. |
| default | Dag Lien. | Dit is wat er leeft in Schaarbeek. |

The recap/pre-ride lead lines name the actual weekday of the ride (a Saturday ride reads
"Dat was een mooie zaterdag." / "Zaterdag rijden we.").

## The recap card — `<x-roze-recap-card>`

The Monday-after moment: for a few days after a ride with photos, the album leads the page.

- **Trigger:** most recent past published ride (same lineage query the feed uses) with
  start within the past 5 days AND ≥ 1 photo in its `gallery` collection. No photos yet =
  no recap (the feed's photo card still covers late uploads). Stateless — no cookies.
- **Anatomy** ("one big photo"): white card, `--radius-lg`, `--shadow-float`. One gallery
  photo in a rounded frame (~16/10, `object-cover`, responsive images via the existing
  media conversions). Below: **"Dat was 'm."** as a Caprasimo h2 + meta line
  *"{count} foto's van de rit van {weekday} staan in het album."* Whole card is one `<a>`
  to the chapter's Foto's page.
- The Foto's ride-picker already defaults to the newest album, which is this ride, so no
  deep-link plumbing. Verify at build; if the default ever diverges, add a query-param
  preselect then, not now.
- Sits in the lead-card slot, above "Je volgende rit". Hover: existing lift pattern
  (`translateY(-2px)` + `--shadow-hover` on `--ease-brand`).
- CSS in `resources/css/pages/chapters-roze-hesjes.css`, tokens only.

## Countdown on "Je volgende rit"

A small accent line inside the existing next-ride card. **Rides only** — meetings
(vrijwilligersmeeting etc.) keep the card exactly as it is; "nachtjes slapen" before a
meeting would be odd.

| Days until ride | Line |
|---|---|
| ride day | Vandaag rijden we! |
| 1 | Morgen is het zover. |
| 2–7 | Nog {n} nachtjes slapen. |
| > 7 | *(no line)* |

Pure Carbon math; one styled meta/accent line; no new component. The countdown renders
whether or not the recap card is present (it lives inside the next-ride section, which
never moves).

## New-member celebration

Two halves:

1. **Feed card variant.** `<x-roze-feed-card>` gains a `celebrate` boolean prop, rendered
   as a `data-celebrate` attribute (the testable seam; styling hangs off it). On a
   celebrating card the icon chip plays the existing `check-pop` keyframe once on
   entrance. Copy gains a warm closing clause only when a next ride exists:
   *"Sara rijdt nu mee als roze hesje. Zeg zondag zeker hallo."* No WhatsApp reference
   (that link is still faux, #37).
2. **Personal welcome.** Covered by the **welcome** moment above — it outranks recap, so a
   new hesje's first Monday greets *them*, not the album.

## Entrance motion

Overview only. Sections and feed cards fade-up on load with a 60–80 ms stagger, reusing
the `fade-up` keyframes already in `resources/css/effects.css`, on `--ease-brand`. The
recap card leads the stagger. Everything joins the existing `prefers-reduced-motion`
opt-out pattern. Sub-pages may adopt the same pattern later; not in this pass.

## Implementation shape

- New `App\Support\RozeHub\OverviewMoment` resolver (sibling of `HubTabs`, same pattern):
  input group, user, next ride, latest album ride; output the moment (enum-like) + its
  data (greeting strings stay in the Blade/lang layer, not the resolver).
- `RozeHesjeController::overview()` resolves the moment and passes it to the view; the
  recap ride/photo lookup extends the queries `feed()` already runs (no new query shape).
- `resources/views/groups/roze-hesjes/overzicht.blade.php`: greeting header + conditional
  `<x-roze-recap-card>`; countdown line in the next-ride section.
- New `resources/views/components/roze-recap-card.blade.php`.
- CSS: additions to `resources/css/pages/chapters-roze-hesjes.css` only; no new partial;
  no raw hex/px (CssArchitectureTest stays green).

## Testing (Pest, per docs/testing-conventions.md)

- **Moment priority:** welcome beats recap beats pre-ride beats default (feature tests on
  the Overview response asserting rendered greeting text).
- **Recap boundary:** ride 4 days ago with photos → card present; 6 days ago → absent;
  4 days ago without photos → absent.
- **Countdown:** each wording state incl. the > 7-days silence and the meeting exclusion.
- **Celebrate seam:** `data-celebrate` present on the member feed card, absent on others.
- **Greeting:** renders the user's first name.
- No Tailwind-utility/keyframe-name assertions; motion is styling, the seams are `data-*`
  attributes and rendered text.

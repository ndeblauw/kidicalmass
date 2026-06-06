---
title: Home (P-01) — UX design
tags: [design, ux, home, skeleton]
sources: [wiki/strategy/50-user-journeys, wiki/strategy/20-personas, wiki/design/30-skeleton/00-page-registry, wiki/design/30-skeleton/about-journey, docs/tone-of-voice]
phase: design
updated: 2026-06-06
---

# Home (P-01) — UX design

Page-level UX pass for the homepage, run through Garrett's planes (Strategy → Scope →
Structure → Skeleton) on top of the existing site-level wiki. Closes the registry's
standing gap: *"UX brief incomplete — homepage structure/hierarchy not well-planned."*

The current `home.blade.php` is the old English wireframe (off-kit, duplicated lists,
placeholder map). This spec replaces it. Surface (colour/type) is out of scope here;
the page will be built on the existing ride/show kit during the Surface pass.

---

## 1 · Strategy — why Home exists, for whom

**Primary job: an emotional pitch (B) wrapping a dispatcher spine (A).**
The hero makes a cold visitor *feel* the movement; the body is a calm dispatcher that
routes each visitor type to the page that actually owns their job. Home holds almost no
content of its own — it is a confident crossroads, not a destination.

**Who lands here.** People *without* intent yet: someone who heard the name "Kidical
Mass," typed it in, or clicked a generic link (primarily **P1 family first-timer**, also
**P2 returning family**, **P3 potential volunteer**, plus cold press/partner/curious
traffic). People *with* intent deep-link straight to Events — so Home must not try to be
Events.

**Boundary.** Per `about-journey.md`, About is the corridor for deciders/deepeners.
Home is the front door for the undecided. Events + Getting Started own the family
acquisition utility; Home points at them.

**The emotional barrier to dissolve.** For the first-time parent the dominant barrier is
**"is it worth the effort?"** (a busy parent guarding a precious weekend) — not safety,
belonging, or orientation. So the hero's emotional argument is **joy as payoff**: the
best hour on a bike, car-free streets, kids together. Make them feel it is worth showing
up for.

---

## 2 · Scope — what's on Home (mostly subtraction)

Driven by Strategy + the project's no-duplicated-content rule.

**Keep & elevate**
- **Hero** — video-led emotional pitch + one dominant CTA. The whole "worth it" job.
- **Dispatcher spine** — three confident routes, each a *route* not a content dump.

**Transform**
- A single quiet **support beat** near the end (→ Steun), kept calm, not a loud band.

**Rides on Home — decided: "one next ride near you."**
Reuse the already-built `<livewire:location-picker>` + `Proximity` (shared `kcm_location`
cookie) to surface the **single soonest ride near the visitor** as concrete "worth it"
proof and utility for returning families. Not a list. `Bekijk alle ritten →` routes to
Events. This is the only stateful piece on the page.

**Cut from Home** (each duplicates a page that owns it)
- News preview → owned by News (P-18)
- Stats-as-a-section → owned by About/Mission (P-15)
- Chapter map placeholder → owned by Chapters (P-10)

---

## 3 · Structure — order & flow

Vertical narrative: hero makes them *feel* it → the real nearby ride *proves* it's worth
it → the dispatcher routes everything else.

1. **Hero** — video background (joy), headline + mission proof line, primary CTA
   `Vind een rit in de buurt` + secondary text link `Nieuw hier? Zo werkt het`
   (Getting Started).
2. **De volgende rit bij jou** — location-aware single ride card. `Bekijk alle ritten →`
   to Events.
3. **Dispatcher spine** — three equal routes: `Nieuw hier? → Getting Started` ·
   `Help mee → Help out` · `Vind je groep → Chapters`.
4. **Quiet support beat** — one calm line → Steun.
5. **Closing CTA** — existing `<x-closing-cta>` ("Klaar voor je eerste rit?" → Events).

### Interaction states — "De volgende rit bij jou"
- **No location set** → show the location picker inline ("Waar fiets je?"). On pick →
  reveal the nearest next ride and set the `kcm_location` cookie.
- **Location set** (cookie present) → skip straight to the nearest next ride, with a
  small "niet in {plaats}? wijzig" affordance.
- **Located, no ride within range** → fall back to the soonest ride *anywhere* + a gentle
  "verderaf" note.
- **Off-season / nothing upcoming** → season message ("seizoen loopt maart–november") +
  dispatch to Getting Started.

---

## 4 · Skeleton — layout

```
┌───────────────────────────────────────────────┐
│ NAV (existing shell)                           │
├───────────────────────────────────────────────┤
│ ① HERO  — video bg (joy)                       │
│    "Het leukste uur op de fiets,               │
│     door autovrije straten."                   │
│    mission proof line                          │
│    [Vind een rit in de buurt]  Nieuw hier? →   │
├───────────────────────────────────────────────┤
│ ② De volgende rit bij jou      Bekijk alle → │
│   [📍 picker]  →  [ za 21 jun · Schaarbeek ]   │
│                     Vertrek … · 3,2 km van jou │
├───────────────────────────────────────────────┤
│ ③ Dispatcher — drie routes                     │
│   [Nieuw hier?] [Help mee] [Vind je groep]     │
├───────────────────────────────────────────────┤
│ ④ Steun-beat — één rustige regel  Steun ons → │
├───────────────────────────────────────────────┤
│ ⑤ Closing CTA — Klaar voor je eerste rit?      │
│    [Vind een rit]                              │
├───────────────────────────────────────────────┤
│ FOOTER (existing shell)                         │
└───────────────────────────────────────────────┘
```

---

## 5 · Final copy (working — open to build-time polish)

- **Headline:** `Het leukste uur op de fiets, door autovrije straten.`
- **Proof line:** `Een vrolijke gezinsfietstocht door autovrije straten, bij jou in de
  buurt. Samen laten we zien dat de straat ook van kinderen is.`
- **Primary CTA:** `Vind een rit in de buurt` · **Secondary:** `Nieuw hier? Zo werkt het`
- **Section 2 heading:** `De volgende rit bij jou` · link `Bekijk alle ritten →`
- **Support beat:** `Kidical Mass draait op vrijwilligers en kleine giften.` · `Steun ons →`

Tone: joyful, local, committed-not-preachy; **no em-dashes** (per tone-of-voice).
The dropped "gratis / voor iedereen" fact may resurface as a small chip near the hero or
on the ride card so the free signal isn't lost — decide at build.

---

## 6 · Assets & open items

- **Hero video:** YouTube `VXiIgU9vI-4` (the clip already embedded on the live Wix home).
  Recommended treatment: **muted autoplay loop background + poster fallback**. Caveat:
  a YouTube iframe carries branding and has mobile-autoplay limits — a **self-hosted MP4
  export would be cleaner**; request from Frederik. Implementation detail finalised at build.
- **Free-signal placement:** decide whether "gratis / voor iedereen" appears as a chip.
- **Reuse, don't rebuild:** `<livewire:location-picker>`, `Proximity`, `<x-event-card>`
  (`:show-date` already supported), `<x-closing-cta>`, `<x-support-callout>` idiom.
- **Localisation:** page is NL (current view is stale English — full rewrite).

## 7 · Explicitly out of scope
- Surface/visual design (colour, type, motion) — separate pass on the ride/show kit.
- Backend changes — relies only on already-built location/proximity + Activity model.
- News, movement stats, and the Belgium map — owned by other pages, cut from Home.

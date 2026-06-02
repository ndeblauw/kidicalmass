---
title: Steun Kidical Mass / Spacefunding — page brief
tags: [design, skeleton]
sources: [wiki/design/10-scope, wiki/strategy/10-organisation-goals, https://growfunding.be/fr/projects/kidicalmassbelgique]
phase: design
updated: 2026-06-02
---

# Steun Kidical Mass / Spacefunding — page brief

`/steun-ons` (was `/membership`; route name kept `membership`) · primary audience: families & supporters who've had a good ride experience. Serves the **#1 org goal** (recurring support → financial independence). Reworked 2026-06-02 (Frederik): the public frame is **"steun", never "lid"**, and the ask is **prominent**, not quiet.

## Strategy

*Why this page exists:* turn goodwill into recurring income — warmly, never transactionally. The movement runs on volunteers with no paid staff beyond the coordination duo; recurring supporters are how KM becomes financially independent (preferred over grant dossiers). It is the **destination** for the support touchpoints ([PAT-10](../40-patterns.md)): the **"♥ Steun ons"** nav CTA (mobile: pinned top of the hamburger menu), the contextual blocks (Home, end of event-detail), and the footer CTA.

*User mental state (primary):* a family that **just had a lovely ride** and wants it to keep existing. High trust (they just felt the value), warm, not guilty. "Dat was fijn / ik geloof hierin — hoe help ik mee?"

*The non-negotiable reassurance:* because "Steun" is now prominent, the page (and every ask) must make clear that **meefietsen altijd gratis blijft — je steunt zodat het gratis kán blijven, voor iedereen.** Otherwise a loud ask reads as "you must pay to ride," the exact confusion we kill by dropping "lid".

## Scope

**In:**
- Explain what **Spacefunding** is (recurring monthly/annual support) and **why** (independence, what it funds).
- **Lead the ask with the plain act and price: "Steun vanaf €3 per maand."** You're just *supporting* — no coined entry-term in the headline. The **t-shirt** is the **visible token of support ("draag je steun")**, a thank-you line *beneath* the act, not the lead. "Kidi Buddy" stays the tier's name on Growfunding; it does **not** front the card (too specialised — Frederik 2026-06-02).
- A single primary CTA **out to the Growfunding campaign** (the site does **not** process payments).
- A **secondary, discreet one-off path** ("liever één keer?") — **provisional, pending Leticia** ([D-9](../01-concerns.md)); most likely the BE72… transfer or a one-off gift. Do not publish an IBAN until confirmed.
- A secondary **"see all tiers"** link (tiers live on Growfunding).
- **Reassurance via movement scale, not a backer count** — e.g. "elke maand rijden honderden gezinnen mee". **No "X mensen steunen" count** (Frederik 2026-06-02: a small number undercuts; it's also the stale-stats trap). Plus the **riding-stays-free** line.

**Out (won't-have):**
- On-site payment or checkout.
- Reproducing/maintaining the full 6-tier ladder on-site (link out instead).
- A separate web store — **the t-shirt is a thank-you/token**, not merchandise.
- The word **"lid" / "lidmaatschap" / "member"** anywhere public.

**Cross-ref:** tiers **€20+ (Kidi Premium/Gold/World)** grant **logo + social/print placement** — i.e. high tiers behave like sponsors. Link those to [About/Partners](about.md); don't surface business-tier rewards as the family ask.

## Structure

1. **Hero** — "Steun Kidical Mass" + the one-line frame ("Iedereen fietst gratis mee. Jij zorgt dat dat zo blijft."). Warm, not corporate.
2. **What your support funds** — 2–3 concrete points (keeps it non-abstract; tone of voice).
3. **The ask (primary)** — headline **"Steun vanaf €3 per maand"**; t-shirt as the token beneath ("je krijgt een t-shirt: draag je steun"); primary CTA → Growfunding. Paired with the **riding-stays-free reassurance**.
4. **One-off (secondary)** — "Liever één keer?" → bank transfer / one-off gift (provisional, D-9).
5. **All tiers** — one line + "Bekijk alle tiers op Growfunding" link.
6. **Reassurance / movement scale** — lead on participation ("honderden gezinnen elke maand"), **not a backer count**. Pull live if shown; avoid stale stats.

## Skeleton (desktop)

```
┌──────────────────────────────────────────────────────┐
│ HERO   "Steun Kidical Mass"                            │
│        Iedereen fietst gratis mee.                     │
│        Jij zorgt dat dat zo blijft.                    │
├──────────────────────────────────────────────────────┤
│ WAT JE STEUN MOGELIJK MAAKT  (3 concrete punten)       │
│  • meer ritten in meer buurten                         │
│  • veilige begeleiding + opleiding                     │
│  • onafhankelijk van subsidies                         │
├──────────────────────────────────────────────────────┤
│ DE VRAAG (primair)               │  GERUSTSTELLING     │
│ ┌──────────────────────────────┐ │  Meefietsen blijft  │
│ │ Steun vanaf €3 per maand     │ │  altijd gratis.     │
│ │ Je krijgt een t-shirt:       │ │  Je steunt zodat    │
│ │ draag je steun.              │ │  het gratis kán     │
│ │ [ Steun maandelijks ↗ ]      │ │  blijven, voor      │
│ │   (opent Growfunding)        │ │  iedereen.          │
│ └──────────────────────────────┘ │                     │
│  Liever één keer? → eenmalige gift  (overschrijving; D-9)│
├──────────────────────────────────────────────────────┤
│ ALLE TIERS   "Meer geven? Bekijk alle tiers ↗"         │
│              (zakelijke tiers €20+ → Over/Partners)    │
├──────────────────────────────────────────────────────┤
│ MOVEMENT SCALE  "Elke maand rijden honderden gezinnen  │
│                  mee in heel België."   (geen teller)  │
└──────────────────────────────────────────────────────┘
```

Mobile: hero → funds → ask card → reassurance → one-off → tiers link → social proof (stacked).

### Contextual "Steun" block ([PAT-10](../40-patterns.md)) — one partial, two copy variants

```
HOME variant:
┌──────────────────────────────────────────────────────┐
│  Kidical Mass blijft gratis. Dankzij mensen zoals jij. │
│  Steun vanaf €3/maand en maak veilige straten mee      │
│  mogelijk, in elke buurt. Meefietsen blijft gratis.    │
│                     [ Steun Kidical Mass → /steun-ons ]│
└──────────────────────────────────────────────────────┘

EVENT-DETAIL (end) variant — the warmest moment:
┌──────────────────────────────────────────────────────┐
│  Fijn meegereden? Help dit blijven bestaan.            │
│  Met €3/maand zorg je dat er volgende maand weer        │
│  een rit is. Meefietsen blijft altijd gratis.          │
│                     [ Steun Kidical Mass → /steun-ons ]│
└──────────────────────────────────────────────────────┘
```

> **Copy rule (critique fix 2026-06-02):** **every** contextual block carries the **riding-stays-free** clause — the asks reach more first-timers than the page does, so the reassurance can't live only in the hero. And **no block leads with a coined term** ("Kidi Buddy"/"lid"); the act is "steun".

## Notes / open

- **One-off path = [D-9](../01-concerns.md)** — confirm with Leticia + capture the IBAN (Notion, not the public repo).
- **No backer count** (dropped 2026-06-02). If any live number is shown, use **participation scale** ("honderden gezinnen elke maand"), pulled live — never a hardcoded "X steunen" (small + stale-stats trap).
- Glossary: **Growfunding** = platform; **Spacefunding** = the recurring model; **Kidi Buddy** = entry tier. Public verb = **steun**. Use each correctly.
- **Surface deferred** (method stops at Skeleton). Copy companion (FR/NL) to follow in `steun-ons-content.md` when the page is built.
- **Code is not yet changed** (doc-only pass): the footer still renders "Word lid" → `/membership`; nav still shows login. Build work = terminology sweep + nav CTA + footer login move + the page + the contextual partial.

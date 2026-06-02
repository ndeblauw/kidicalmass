---
title: Membership / Spacefunding — page brief
tags: [design, skeleton]
sources: [wiki/design/10-scope, wiki/strategy/10-organisation-goals, https://growfunding.be/fr/projects/kidicalmassbelgique]
phase: design
updated: 2026-06-02
---

# Membership / Spacefunding — page brief

`/membership` · primary audience: families & supporters who've had a good ride experience. Serves the **#1 org goal** (recurring support → financial independence).

## Strategy

*Why this page exists:* turn goodwill into recurring income, quietly. The movement runs on volunteers with no paid staff beyond the coordination duo; recurring members are how KM becomes financially independent (preferred over grant dossiers). The page must convert without making the rest of the site feel transactional — it's the *destination* for the persistent footer CTA ([PAT-10](../40-patterns.md)), not an interstitial.

*User mental state:* "That was lovely / I believe in this — how do I support it?" Low friction, clear ask, no guilt.

## Scope

**In:**
- Explain what **Spacefunding** is (recurring monthly/annual support) and **why** (independence, what it funds).
- Lead with the **entry tier: €3/mo "Kidi Buddy" → t-shirt = your membership.**
- A single primary CTA **out to the Growfunding campaign** (the site does **not** process payments).
- A secondary **"see all tiers"** link (tiers live on Growfunding).
- Brief reassurance / social proof (e.g. current growfunder count, what support enables).

**Out (won't-have):**
- On-site payment or checkout.
- Reproducing/maintaining the full 6-tier ladder on-site (link out instead).
- One-off donations — **Growfunding offers recurring only**; don't invent a one-off path.
- A separate web store — **the t-shirt is the membership**, not merchandise.

**Cross-ref:** tiers **€20+ (Kidi Premium/Gold/World)** grant **logo + social/print placement** — i.e. high tiers behave like sponsors. Link those to [About/Partners](about.md); don't surface business-tier rewards as the family ask.

## Structure

1. **Hero** — what Spacefunding is, in one line + why it matters (independence). Warm, not corporate.
2. **The ask** — "Become a member from €3/mo" card: t-shirt, what it makes you (a co-financer of the movement). Primary CTA → Growfunding.
3. **What your support funds** — 2–3 concrete points (keeps it non-abstract; tone of voice).
4. **All tiers** — one line + "see all tiers on Growfunding" link.
5. **Reassurance** — current growfunders / movement scale; managed via Growfunding.

## Skeleton (desktop)

```
┌───────────────────────────────────────────────┐
│  Become part of what keeps Kidical Mass going   │
│  Spacefunding = monthly support. It's how we     │
│  stay independent and keep putting kids on bikes.│
├───────────────────────────────────────────────┤
│  ┌─────────────────────────┐                    │
│  │ Kidi Buddy · €3/month   │   What your support │
│  │ stickers + a t-shirt    │   funds:            │
│  │ → you're a co-financer  │   • more rides      │
│  │ [ Become a member ↗ ]   │   • training/safety │
│  │   (opens Growfunding)   │   • a city for kids │
│  └─────────────────────────┘                    │
│  Want to give more? See all tiers on Growfunding ↗│
├───────────────────────────────────────────────┤
│  Join 18+ growfunders already backing the movement│
└───────────────────────────────────────────────┘
```

Mobile: hero → ask card → funds → tiers link → reassurance (stacked).

## Notes / open

- Confirm live growfunder count is pulled or hardcoded-with-a-refresh-plan (avoid the old site's stale-stats trap).
- Glossary: **Growfunding** = platform; **Spacefunding** = the recurring model. Use both correctly.
- Copy companion (FR/NL) to follow in `membership-content.md` when the page is built.

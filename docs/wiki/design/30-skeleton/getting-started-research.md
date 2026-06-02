---
title: Getting Started — Research (verbatim)
tags: [content, research]
sources: [web, raw/website/help-je-n-ai-pas-de-vélo.md]
phase: design
updated: 2026-06-02
---

*Verbatim verification of the open questions in [getting-started.md](getting-started.md). Captured so findings are re-auditable. Synthesis at the bottom feeds the page docs + the NL view.*

---

## Q6 — Helmet policy (Belgium)

**Finding:** A bicycle helmet is **not legally required in Belgium**, for adults *or* children, on a normal bike or a standard pedal-assist e-bike (≤25 km/h). The only legal helmet requirement is for **speed pedelecs** (up to 45 km/h) — not relevant to a Kidical Mass ride. Helmets are strongly *recommended* by safety bodies; some schools/youth movements impose them for their own activities, but that is not law.

**Implication:** The FAQ line "aangeraden, niet verplicht" is **correct**. The content file's implementer note claiming helmets are "required by law for children under 12 in some regions" is **factually wrong** and must be removed — there is no such regional rule in Belgium.

Sources:
- [Fietsersbond — Fietshelm](https://fietsersbond.be/fietshelm/)
- [Belgium.be — Plichten van fietsers](https://www.belgium.be/nl/mobiliteit/fietsers_en_voetgangers/fietsers/plichten)
- [Cyclobility — Is een fietshelm verplicht in België?](https://www.cyclobility.be/en/blog/mandatory-bicycle-helmet-belgium)

---

## Q4 — Fietsbieb (price + coverage) — **CHANGED**

**Finding:** From **1 January 2026**, Fietsbieb membership is **€30/year** (or **€10/year** for those entitled to a *verhoogde tegemoetkoming*), plus a **€20 refundable deposit** (unchanged). First-time standard cost = €50 (€30 + €20 deposit); reduced first-time = €30 (€10 + €20 deposit). The bike can be swapped for a bigger size or another model during the year. From 1 Jan 2026, Fietsbieb runs **across all of Flanders and Brussels** with differentiated pricing.

**Implication:** The doc's "**€20/year** + €20 deposit" is **outdated** → update to **€30/year (€10 reduced) + €20 deposit**. The coverage label "🏙️ Brussels only" is **outdated** → Fietsbieb is now **Vlaanderen + Brussel** (not Brussels-only). This materially upgrades the card.

Sources:
- [Fietsbieb](https://fietsbieb.be/)
- [Kortenberg — Fietsbieb ook in 2026](https://www.kortenberg.be/nieuws/fietsbieb-kortenberg-ook-in-2026)
- [Visie — Fietsbieb voor kinderen in de prijzen](https://visie.net/artikel/fietsbieb-voor-kinderen-in-de-prijzen)

---

## Q1 — My Kids Bikes (still active?)

**Finding:** mykidsbikes.be is **live**. It is an evolving ("zéro souci") all-inclusive monthly subscription for children's bikes from the Austrian brand **Woom**; price is set by bike size, and the bike is swapped as the child grows. The `/abonnements/` page exists.

**Implication:** Keep the card. Confirmed brand is **Woom**; the doc's "Woom & BeMoov" — **BeMoov is unconfirmed** from the live site, so drop it or verify with the client.

Sources:
- [My Kids Bikes](https://www.mykidsbikes.be/)
- [My Kids Bikes — Nos vélos et abonnements](https://www.mykidsbikes.be/index.php/abonnements/)

---

## Q7 — Pro Velo "Families on Bike" (active in 2026?)

**Finding:** **Active in 2026.** Free guidance programme for Brussels families (funded by Breathe Cities), aimed at removing barriers to family cycling: learn to ride in traffic, choose a route, and **free family-bike testing** (cargo, longtail, trailer, child seat) for up to ~3 weeks. 2026 test locations include **Vorst, Sint-Gillis, Watermaal-Bosvoorde and Anderlecht**.

**Implication:** Keep the card. **Update the locations** — the doc's "Anderlecht + Saint-Gilles/Forest area" is partly stale; 2026 spreads wider. Safer to say "verschillende Brusselse gemeenten" and link to provelo.org rather than hard-coding communes.

Sources:
- [Pro Velo — Families on Bike (NL)](https://www.provelo.org/nl/families-on-bike/)
- [Pro Velo — Ontdek en test met Families on Bike](https://www.provelo.org/nl/families-on-bike-tests/)

---

## Loopz (offer + domain)

**Finding:** Kids' rental bikes **from €6/month**; the **KIDICALMASS** code gives the **first 2 months free** (exclusive Kidical Mass promo, confirmed on the KM raw page). Exact tier pricing is not published — Loopz directs to contact/shop. Canonical domain is **loopz.bike** (an optional repair pack add-on exists).

**Implication:** Keep the card. **Fix the domain** in the docs from `loopz.be` → **`loopz.bike`**. "vanaf €6/maand" + "KIDICALMASS = 2 maanden gratis" stand.

Sources:
- [Loopz — How it works](https://loopz.bike/en/how-it-works/)
- [Kidical Mass — Help? Je n'ai pas de vélo!](https://www.kidicalmass.be/help-je-n-ai-pas-de-v%C3%A9lo)

---

## Q5 — Kidical Mouse (availability) — **UNVERIFIABLE via web**

No public source describes the Kidical Mouse cargo-bike-at-the-start service or which rides it covers. **Flag for client** (Leticia/Cecilia): is it available at *every* Brussels ride or only specific ones? Card copy stays cautious ("aan de start van sommige ritten — check je afdeling") until confirmed.

---

## Q2 — Cyclo (purchase vs loan)

Unchanged from the existing decision: Cyclo sells **second-hand** bikes (a purchase, not a loan/subscription) and runs the **Duo Mechanics** workshop. Keep Cyclo as a brief "also" note under the no-bike cards, and as a card in "Other ways to cycle." Domain cyclo.be.

---

## Synthesis (feeds the docs + view)

| # | Question | Verdict | Action on docs/view |
|---|---|---|---|
| Q6 | Helmet law | **Resolved** — not mandatory | Keep "aangeraden, niet verplicht"; **delete** the false "required under 12" note |
| Q4 | Fietsbieb price/coverage | **Changed** | €30/jr (€10 verlaagd) + €20 waarborg; coverage **Vlaanderen + Brussel** |
| Q1 | My Kids Bikes alive | **Resolved** — live | Keep; brand **Woom** (drop unconfirmed BeMoov) |
| Q7 | Pro Velo 2026 | **Resolved** — active | Keep; soften locations to "verschillende Brusselse gemeenten" |
| — | Loopz domain | **Fixed** | loopz.be → **loopz.bike** |
| Q5 | Kidical Mouse | **Open — client** | Cautious copy; ask Leticia/Cecilia |
| Q2 | Cyclo | **Resolved** | Brief note + activity card |

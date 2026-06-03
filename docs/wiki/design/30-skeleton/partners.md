---
title: Partners & sponsors — recognition strip + Partners page
tags: [design, skeleton]
sources: [wiki/design/40-patterns.md, raw/website/index.md, raw/website/le-projet-het-project.md, "Wix PDF: Sponsor- en Partnerformules (cf0153_5294c80b…)", "Wix PDF: Sponsor- en Partnercharter (cf0153_cd730cb7…)"]
phase: design
updated: 2026-06-03
---

# Partners & sponsors — recognition strip + Partners page

Two surfaces, two jobs. The site-wide **recognition strip** ([PAT-5](../40-patterns.md)) does one quiet thing — credit those who make Kidical Mass possible — and routes to the **[Partners page](about.md#about--partners)** for everything else (categories, the "Ook ondersteund door" list, and becoming a partner). Reworked 2026-06-03 (Frederik): the former full-bleed band that carried *all* of this on *every* page was reduced.

## Why this changed

The old `<x-partners>` band was a full-bleed blue section rendered globally (in `layouts/site.blade.php`) on **every** page. It conflated three jobs:

1. **Recognition** — partner/funder logo grid + "Ook ondersteund door" list + the Brussel Mobiliteit "Met de steun van" logo.
2. **Acquisition** — "Ook partner worden? Sponsorformules · Partnercharter" (both `href="#"` dead links).
3. **Decoration** — a 420px kid-on-bike illustration.

Problems: it **duplicated** the dedicated `/about/partners` page, **bloated every page**, and **competed with the [PAT-10](../40-patterns.md) "Steun ons" ask** (the #1 org goal) wherever both landed (e.g. Home). PAT-5 was always scoped to *specific* pages linking to `/about/partners`, not a global band — the implementation had drifted.

### Correction (2026-06-03): Sponsorformules & Partnercharter **do exist**

An earlier note here claimed these two documents didn't exist (the homepage scrape only surfaced `mailto:` CTAs). **That was wrong** — Frederik supplied the live Wix PDF URLs; both are real, structured, bilingual (NL+FR) documents. Their content is captured in [§ Become-a-partner conversion flow](#become-a-partner-conversion-flow--plan-2026-06-03) below. The homepage simply linked email CTAs (`bike@kidicalmass.be` / `contact@kidicalmass.brussels?subject=partnership`) instead of the PDFs.

## Strategy

*Recognition strip — who & why:* every visitor, passively. Funders (esp. Brussel Mobiliteit) get visibility; visitors get a quiet legitimacy signal ("real institutions back this"). It must never shout, never compete with "Steun ons", never imply you must pay. **Funder-visibility obligation is satisfied by the homepage** (Frederik 2026-06-03); the strip running site-wide is a generous choice, not a contractual must.

*Partners page — who & why:* deliberate, evaluative visitors (potential partners, press, grant reviewers) who want the full picture. This is where acquisition and detail belong — see [about.md](about.md#about--partners).

## Scope

**Recognition strip (in):**
- A quiet "Mede mogelijk gemaakt door" label.
- The Brussel Mobiliteit "Met de steun van" logo (locale-aware: `bm-nl` / `bm-fr`).
- A muted row of **national** partner logos — `Partner` where `group_id IS NULL && visible && show_logo`. **National only**: chapter-local partners (`group_id` set) belong on their chapter page ([PAT-5](../40-patterns.md): "national vs local are different data"), not on a site-wide strip. This is what keeps the strip slim; the *institutional-only* refinement (vs movement-allies) awaits a category field ([D-11](../01-concerns.md)).
- One link → `/about/partners` ("Onze partners & sponsors →").

**Recognition strip (out — moved to the Partners page):**
- The "Partners & sponsors" H2 + the acquisition CTA + the dead Sponsorformules/Partnercharter links.
- The "Ook ondersteund door" supporters list (Clean Cities, Bruxelles Ville / Brussel Stad, Schaarbeek, spacefunders).
- Partner categories (institutional / allies / operational).
- The 420px illustration.

## Structure

```
Recognition strip (global, above the footer) — slim, one row:

┌──────────────────────────────────────────────────────────────┐
│ MEDE MOGELIJK GEMAAKT DOOR  [BM logo] [logo] [logo] …  Onze   │
│                                              partners & sponsors → │
└──────────────────────────────────────────────────────────────┘
        wraps to multiple lines on mobile; stays quiet/muted
```

The full Partners page skeleton (institutional / allies / operational + "Ook ondersteund door" + "Zelf partner of sponsor worden?" contact CTA) is specced in [about.md → About / Partners](about.md#about--partners).

## Become-a-partner conversion flow — plan (2026-06-03)

**Problem (Frederik):** the "Zelf partner worden?" CTA is a *black hole* — a B2B prospect must send a cold email before knowing the price, the benefit, or whether they qualify. That's friction at the pre-qualification moment, which kills exactly the low-intent civic supporters the €100 entry tier is for.

**Source docs (live Wix PDFs, captured verbatim — these can rot, so they live here):**

*Doc A — "Sponsor- en Partnerformules"* ([cf0153_5294…pdf](https://www.kidicalmass.be/_files/ugd/cf0153_5294c80b2c3b43e4b9b466d2ab3f30c2.pdf)) — the tier ladder, two tracks, €/year **excl. VAT**:

| vzw's | €/jr | krijgt | | bedrijven | €/jr | krijgt |
|---|---|---|---|---|---|---|
| Supporter | 100 | social-vermelding | | Friend | 500 | logo + 2 social |
| Partner | 250 | logo + 2 social | | Sponsor | 1.000 | logo + 4 social + logo op alle event-flyers |
| Community Partner | 500 | logo + 4 social + flyers van 1 event | | Main Partner | 2.500 | logo + 6 social + alle flyers & banners + stand/aanwezigheid (in overleg) |

Hook: *"zichtbaarheid bij gezinnen, buurtbewoners én beleidsmakers."* Conditions: excl. VAT · per charter · right to refuse · extra visibility negotiable.

*Doc B — "Sponsor- en Partnercharter"* ([cf0153_cd73…pdf](https://www.kidicalmass.be/_files/ugd/cf0153_cd730cb7a2174d7f9e5349dfe3b58034.pdf)) — values + terms. **Expects:** shared values; respect for KM's **non-political, non-commercial** independence; contributions **without strings**; no conflicting collaborations. **Offers:** visibility via channels; logo on activity material; event participation; positive image. **General:** formalised by agreement/mail; right to refuse; GDPR.

**Why surface this on-page (analysis):**
- The partner audience is **evaluative/B2B**, not emotional like the `/steun-ons` family ask. Transparency (price + benefit) is the conversion lever — it lets prospects **self-qualify and self-select a tier** before contacting.
- It resolves an IA split already half-decided in [`steun-ons.md`](steun-ons.md): business-grade tiers "behave like sponsors → link to About/Partners." So **`/steun-ons`** = families · recurring · Growfunding · €3/mo; **`/about/partners`** = orgs · annual · **invoiced agreement** (the "excl. VAT / per agreement" wording means *not* Growfunding) · €100–2.500/yr. Two tier systems, two audiences, two payment mechanics.
- The **charter doubles as a filter** (non-political, non-commercial, values-alignment, "we may refuse") — protects integrity *and* signals selectivity (being a chosen partner feels good).
- The flow still ends in contact (no on-site payment; invoiced), but a **pre-qualified, warm** one.

**Recommended flow:** hero → *who already backs us* (existing cards = social proof) → *waarom partner worden?* (benefit hook) → *hoe het werkt / formules* (summary of the two tracks) → *wat we van partners vragen* (charter essence + download) → *Interesse?* (routed form, email/phone fallback).

**Locked decisions (Frederik 2026-06-03):**
1. **Scope = placeholder pending Leticia.** These are a *KM Brussels* artifact (`contact@kidicalmass.brussels`, "KM Brussels"). Build the full structure, but treat exact prices/tier names as **provisional** until Leticia confirms they apply nationally. Couples to [D-11](../01-concerns.md) (national-scope pass).
2. **On-page depth = summary + downloadable PDF.** A short "why + how it works" summary on-page (two tracks, what you broadly get); the **full formules + charter as downloadable PDFs** (re-hosted off Wix). Keeps the maintenance-heavy price table out of templates and the national-pricing risk off the page until confirmed.
3. **Contact = routed form primary, email/phone fallback.** A short structured form (org · vzw/bedrijf · tier of interest · message) routed to the partnerships inbox ([PAT-6](../40-patterns.md)), with the direct email + phone (`0495 81 27 95`) as a secondary fallback (the docs list both).

**Built 2026-06-03 (Frederik: "build it").** Replaced the mailto `<x-about-cta>` on `/about/partners` with the full flow: *waarom partner worden* (benefit checklist) → *onze formules* (sky band, two tracks summarised, no on-page prices) → *wat we van partners vragen* (charter essence) → *Interesse?* (light-blue band, two-column: warm intro + email/phone fallback | routed form). The form is a new Livewire **`PartnerEnquiry`** (PAT-6, mirrors `ChapterVolunteerSignup`): name · email · organisation · type (vzw/bedrijf/overheid/andere) · formule-interest · message → `ContactForm` + `ContactFormSubmitted` to the comms inbox, honeypot. Both PDFs **re-hosted** at `public/downloads/kidical-mass-{sponsorformules,partnercharter}.pdf` (serve 200). Tests: `PartnerEnquiryTest` (submit/validation/honeypot) + a `PublicStructureTest` case (flow renders, PDFs linked, form present, 0 em-dashes). Suite 125 green, Pint clean. Surface = the About kit (sky/light-blue bands, white form card, yellow submit). Prices stay **off-page** (in the provisional PDF) pending the Leticia gate below.

**Polished 2026-06-03 (`/polish`, Frederik):** (1) **fewer bands** — dropped the formules + enquiry colour bands to **white sections**. (2) **Readability** — formules off the medium-blue band → white, legible. (3) **Button** — enquiry submit `<flux:button>` (yellow) → standard blue `.about-cta__btn--primary`. (4) **Imagery** — real **crowd-at-the-Cinquantenaire hero photo** + the real **partner logo wall** composite (`public/img/partners/partner-logos-2024.png`, the only cleared logo asset, D-11).

**"Who backs us" consolidated 2026-06-03 (Frederik, "anchors + wall"):** the critique's open question (4 institutional cards + 3 operational cards + logo wall = three heavy similar-weight blocks before the pitch) → merged into **one** section "Onze partners en bondgenoten": the 4 **institutional named cards** (depth/context — the credibility anchors) immediately followed by the **logo wall** ("En vele anderen…", breadth). The **operational/in-kind partners lost their dedicated cards** — they're a *family/resource* story (bike provision), not sponsor credibility, and overlapped the wall; they now live in the wall + a one-line pointer ("partners zoals Loopz en My Kids Bikes… Geen fiets? → find-a-bike"). Net: 3 blocks → 1, clear depth→breadth, faster to the pitch.

**Critiqued + polished/normalised 2026-06-03 (`/critique` → `/polish` `/normalize`):** verdict not-AI-slop; three finish defects fixed — (1) the rotated **"OVER ONS" hero badge overlapped the two-line H1** → added clearance (`.partners-page .about-hero__badge` margin-bottom 1.75rem); (2) **card radii were off-system** (1.25rem vs DESIGN.md's 1.5rem content-card standard) → normalised `.about-partner-card` + `.partner-enquiry__form` to **1.5rem**; (3) **the logo wall was illegible on mobile** (one wide strip shrunk to specks) → `min-width: 34rem` + `overflow-x: auto` so logos stay legible and scroll. Left as-is: faint dividers (10% ink = connected, intentional), the 7 text cards (genuine content), the reused Press checkmark list.

**Arranged 2026-06-03 (`/arrange`, Frederik):** the band assignment + rhythm were wrong. (1) **Operationeel was a light-blue band** → it read as *the most important* section, which it isn't → **white section**. (2) **The single light-blue accent band moved to the enquiry/CTA** at the end — the actual primary action (white form card pops on light-blue). (3) **The body felt like floating islands** (arbitrary whitespace, full-bleed band breaking the column width) → now **one connected column**: a `.partners-page` wrapper scopes consistent section padding + a **hairline divider** between every white body section, so they read as parts of one document. Net surface: blue hero → white body (6 sections, divider-separated) → one light-blue CTA band. Two full-bleed bands total (hero + CTA), both intentional.

**Open questions / dependencies:**
- **[client] National scope (Leticia):** do the Brussels tiers/prices apply to the national network? The PDFs are the *KM Brussels* versions (Brussels contact + branding) — **swap for confirmed national versions before relying on them**; on-page tier *names* are also provisional. Blocks publishing prices on-page. → [D-11](../01-concerns.md). The logo-wall composite is a **2024** snapshot — refresh when the partner set changes.
- **[back] Partnerships inbox:** `PartnerEnquiry` routes to the comms inbox with a `TODO` — point it at the decided `bike@` vs `partners@` address.
- **[asset] PDF re-hosting:** the two Wix PDFs must be re-hosted on the new site (e.g. `public/downloads/…`) before the Wix decommission, ideally the **confirmed national** versions.
- **[back] Form routing inbox:** which address — `bike@kidicalmass.be`, `contact@kidicalmass.brussels`, or a dedicated `partners@`? (Open in [about.md](about.md#about--partners).)
- **[content] Maintenance owner:** who updates tiers/prices when they change (coordination duo)? Summary-on-page + PDF keeps this light.

## `/critique` follow-ups — resolved 2026-06-03

1. **"Slim isn't enforced" (the strip showed every partner) — fixed.** Root cause: all 15 seeded partners carry a `group_id` (chapter-local), and the query showed them all. Scoped the strip to **national partners** (`whereNull('group_id')`) — principled per PAT-5, no schema change. Further *institutional-vs-ally* curation is parked on the category field ([D-11](../01-concerns.md)).
2. **Double "Steun Kidical Mass" (contextual callout + footer CTA) — kept by design.** Both are deliberate [PAT-10](../40-patterns.md) touchpoints (prominence *elevated* 2026-06-02; yellow support-pill consistency is intentional, P-04). Reversing either would undo a recent conscious decision. The recognition strip sitting *between* the home callout and the footer actually gives the two asks visual separation. Left as-is; flagged for Frederik if it still reads repetitive.
3. **Strip on `/steun-ons` — kept.** Site-wide was the explicit decision; recognition (legitimacy) serves a different job than the ask, so it's not redundant there. Not worth per-page suppression logic in the global shell. Confirm if you'd rather hide it on the support page.

## Notes / open

- **Built 2026-06-03** (Frederik): `resources/views/components/partners.blade.php` rewritten to the slim strip (`<aside class="partner-strip">`, national-scoped query), `lang/nl/partners.php` slimmed, `.partner-strip` CSS in `app.css` (old `.partners-*` band styles removed), `about/partners.blade.php` stub re-scoped to absorb the moved content. Test: `PublicStructureTest` asserts the strip renders site-wide, links to `about.partners`, and that acquisition/supporters copy no longer leaks onto every page.
- **Logos are the launch blocker ([D-11](../01-concerns.md)):** the strip reads the `Partner` model for logos, but every record is lorem with **no cleared logo asset**, so the strip renders only the hardcoded Brussel Mobiliteit logo until real national-partner records + logos land. This gap is now **site-wide visible** (every page), not just on the Partners page.
- **Become-a-partner = a real conversion flow, not a mailto.** The Sponsorformules + Partnercharter PDFs exist and drive an on-page summary + downloadable docs + routed form — see [§ Become-a-partner conversion flow](#become-a-partner-conversion-flow--plan-2026-06-03). Pending Leticia's national-scope confirmation before the prices go live.
- **Data gap:** the `partners` table has **no category field** — the Partners page categories (institutional / allies / operational) can't be data-driven yet. Flag to Nico ([about.md open questions](about.md#about--partners)).
- **Surface:** built on the brand band (kidical-blue), not a full re-skin — a light pass. FR copy follows when the FR layer lands.

---
title: Partners & sponsors — recognition strip + Partners page
tags: [design, skeleton]
sources: [wiki/design/40-patterns.md, raw/website/index.md, raw/website/le-projet-het-project.md]
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

### Investigation: Sponsorformules & Partnercharter do not exist

The two links Frederik lifted from the live Wix site were **aspirational placeholders**. The scrape ([`raw/website/index.md`](../../../raw/website/index.md)) shows the original site's partner/sponsor acquisition was **two `mailto:` CTAs**, not documents:

- *"Want to be a sponsor?"* → `bike@kidicalmass.be`
- *"Want to be our Partner/Sponsor? (local/regional)"* → `contact@kidicalmass.brussels?subject=partnership`

There is no Sponsorformules document and no Partnercharter (the only charter in the raw set is the **volunteer** ROI charter, unrelated). So we must **not** ship them as links. They become a **content dependency** on the coordination duo: if those documents get written, they live in the Partners page "become a partner" block. Until then, acquisition = a contact CTA, matching what the movement actually does today.

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

## `/critique` follow-ups — resolved 2026-06-03

1. **"Slim isn't enforced" (the strip showed every partner) — fixed.** Root cause: all 15 seeded partners carry a `group_id` (chapter-local), and the query showed them all. Scoped the strip to **national partners** (`whereNull('group_id')`) — principled per PAT-5, no schema change. Further *institutional-vs-ally* curation is parked on the category field ([D-11](../01-concerns.md)).
2. **Double "Steun Kidical Mass" (contextual callout + footer CTA) — kept by design.** Both are deliberate [PAT-10](../40-patterns.md) touchpoints (prominence *elevated* 2026-06-02; yellow support-pill consistency is intentional, P-04). Reversing either would undo a recent conscious decision. The recognition strip sitting *between* the home callout and the footer actually gives the two asks visual separation. Left as-is; flagged for Frederik if it still reads repetitive.
3. **Strip on `/steun-ons` — kept.** Site-wide was the explicit decision; recognition (legitimacy) serves a different job than the ask, so it's not redundant there. Not worth per-page suppression logic in the global shell. Confirm if you'd rather hide it on the support page.

## Notes / open

- **Built 2026-06-03** (Frederik): `resources/views/components/partners.blade.php` rewritten to the slim strip (`<aside class="partner-strip">`, national-scoped query), `lang/nl/partners.php` slimmed, `.partner-strip` CSS in `app.css` (old `.partners-*` band styles removed), `about/partners.blade.php` stub re-scoped to absorb the moved content. Test: `PublicStructureTest` asserts the strip renders site-wide, links to `about.partners`, and that acquisition/supporters copy no longer leaks onto every page.
- **Logos are the launch blocker ([D-11](../01-concerns.md)):** the strip reads the `Partner` model for logos, but every record is lorem with **no cleared logo asset**, so the strip renders only the hardcoded Brussel Mobiliteit logo until real national-partner records + logos land. This gap is now **site-wide visible** (every page), not just on the Partners page.
- **Content dependency (coördinatieduo):** do Sponsorformules / Partnercharter documents exist or should they be written? Until then, acquisition stays a `mailto:` contact CTA on the Partners page.
- **Data gap:** the `partners` table has **no category field** — the Partners page categories (institutional / allies / operational) can't be data-driven yet. Flag to Nico ([about.md open questions](about.md#about--partners)).
- **Surface:** built on the brand band (kidical-blue), not a full re-skin — a light pass. FR copy follows when the FR layer lands.

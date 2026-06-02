---
title: About section — core journey, jobs-to-be-done & section UX plan
tags: [design, skeleton, about, journey, jtbd]
sources: [30-skeleton/about.md, 30-skeleton/about-content.md, 10-scope.md, 20-structure.md]
phase: design
updated: 2026-06-03
---

# About — the journey that relies on this section

`about.md` + `about-content.md` already hold the per-page 5-plane specs and production
copy. This file adds the layer they were missing: **the consolidated user journey and
jobs-to-be-done that the whole About section exists to serve**, the cross-link map that
makes that journey actually walkable, and the open questions surfaced while building it
out (2026-06-03).

---

## Strategy — who relies on About, and for what job

**The defining insight: About is not a family-acquisition surface.** A parent deciding
whether to attend a ride does *not* read "Over ons → Missie" first — they go to
[Events](events-overview.md) and [Getting Started](getting-started.md). About serves a
different population: **deciders and deepeners** — people weighing whether to *commit
something* (money, time, a chapter, a press story, a partnership) and people already warm
who want to belong to something bigger. They arrive evaluative, not casual.

### The one core journey

> **"Should I invest in / commit to this movement?"**
> Someone arrives wanting to understand *what Kidical Mass is, what it stands for, how it
> works, and whether it's credible and "my people"* — then takes a next step that lives
> **off** the About section: start or join a chapter, help out, get in touch, donate, or
> publish a story.

About's job is **conversion of the already-interested into committed**, by supplying
*credibility + values + legibility*. Every leaf must therefore end by handing the visitor
forward — About pages are a corridor, not a destination.

### Jobs-to-be-done (the people walking this corridor)

| # | Who | When… I want… so I can… | Pages they lean on | Exit they need |
|---|-----|--------------------------|--------------------|----------------|
| **JTBD-1** | **Prospective chapter lead** | *When* unsafe streets in my town frustrate me and I find Kidical Mass, *I want* to see what the movement stands for and how a local group actually works, *so I can* decide to start one here and trust I won't be alone. | Mission · Vision · Organisation | Help out / Contact |
| **JTBD-2** | **Partner / institution** | *When* I weigh backing a citizen initiative, *I want* proof it's credible, aligned and real, *so I can* justify the partnership internally. | Mission (scale) · Vision (alignment) · Partners (proof) · Press | Partner worden |
| **JTBD-3** | **Funder / grant reviewer** | *When* I assess a grant, *I want* structured proof of impact, scope and that money is well-used, *so I can* score it. | Mission (stats) · Organisation (no paid staff) · Press | (feeds steun-ons) |
| **JTBD-4** | **Journalist** | *When* I write a quick piece, *I want* the story, the numbers and a contact, *so I can* publish fast and reach the right person. | Mission (quotable) · Vision (the why) · Press (contact) | mailto press |
| **JTBD-5** | **Proud / curious participant** | *When* I've ridden once and loved it, *I want* to grasp the bigger movement I'm now part of, *so I can* feel it matters and maybe do more. | Mission · Vision · News | Help out / Steun |
| **JTBD-6** | **Cautious parent** *(secondary)* | *When* I'm unsure this is safe or "for people like me", *I want* to see the values and who runs it, *so I can* trust it. | Mission (inclusivity) · Organisation (safety/who) | Getting started |

### Review of the journey — strengths & risks

- **Strength:** a clean story-first spine (why → what we stand for → how we work →
  proof) maps almost 1:1 onto the deciders' questions. The order is right.
- **Risk — institutional drift.** About is where org-speak creeps in. The deciders want
  credibility, but the brand's *edge* is that it's warm and citizen-led. Every page must
  carry both; stats are a celebration, not a KPI table. (ToV: "About" register = a notch
  more serious, still human.)
- **Risk — the corridor dead-ends.** Today every leaf is an `<x-stub>`: all six JTBD hit
  "Stub — geen inhoud" and stop. The whole journey is *currently broken*. Fixing that is
  the point of this build.
- **Risk — thin/fake proof.** The credibility leaves (Partners, Press) are the most
  content/data-dependent and currently the weakest: `partners` holds only seeded
  lorem-ipsum rows with no logos and no category field, and there is **no Press model at
  all**. Faking them would betray the very credibility these pages exist to build →
  handled honestly below (curated static Partners; contact-forward Press with an honest
  empty state), with the data work logged as open questions.

---

## Scope — confirmed (per-page specs unchanged)

The Must/Should/Out-of-scope for each leaf are settled in
[`about.md`](about.md) and the copy is in [`about-content.md`](about-content.md). This
build delivers, in NL, to the public-site DESIGN.md kit:

| P | Page | This build delivers |
|---|------|--------------------|
| P-14 | **Over ons** (hub) | Orientation hero + 6 nav cards + mini stat bar + forward CTA |
| P-15 | **Missie** | Movement intro · 3 axes (Start/Support/Spread) · stats · inclusivity → GS · parent quote · CTA |
| P-16 | **Visie** | Position statement · 4 demands · manifesto link · parent voices · CTA |
| P-17 | **Organisatie** | Intro · organigram · national-vs-local two columns · coördinatieduo · safety → GS · CTA |
| P-18 | **Nieuws** | NL surface pass on the article feed + honest empty state (social) |
| P-19 | **Pers** | Contact-forward intro + honest empty state (no fabricated coverage) |
| P-20 | **Partners** | Curated static two-category list + "partner worden" CTA |

---

## Structure — IA + the cross-link map (the new bit)

**Hierarchy** (unchanged): `Over ons` hub → 6 leaves, story-first order
**Missie → Visie → Organisatie → Nieuws → Pers → Partners**. Surfaced in the desktop
**dropdown**, the **footer** "Over ons" column, and the hub's **nav cards**.

**The cross-link map** — because deciders convert *off* About, each leaf must always offer
the next step. This is what turns the corridor into a journey:

```
                 ┌──────────── Over ons (hub) ────────────┐
                 │  cards → all 6 leaves · CTA → events/help-out
                 ▼
   Missie ──"geen fiets?"──▶ Getting started
     │  └─ axis "pleiten" ──▶ Visie
     ▼  CTA ──▶ Events · Help out
   Visie  ── manifest ──▶ (PDF)
     │  CTA ──▶ Events · Help out
     ▼
   Organisatie ── "veiligheid" ──▶ Getting started
     │  CTA ──▶ Help out · (start een afdeling) Help out
     ▼
   Nieuws · Pers · Partners ── each ──▶ Contact / mailto · Steun
```

Standing entry points into About from the rest of the site: header dropdown, footer
column, and the site-wide **partner-strip** (`→ /about/partners`). A home-page entry
point ("leer de beweging kennen") is recommended but **deferred** — the home page is still
the English placeholder undergoing its own NL rework, so an About link is best added there
once that page is rebuilt, to avoid a half-NL clash.

---

## Skeleton — wireframes settled; build deltas

Desktop + mobile ASCII wireframes per page live in [`about.md`](about.md). Build notes:

- **Reuse the kit, don't reinvent** (DESIGN.md): every page is a vertical stack of
  full-bleed colour bands built from `.activity-hero*` / `.activity-promises*` /
  `.activity-info-map*` + the `gs-*` / `ho-*` idioms (FAQ, green-check lists, closing
  yellow CTA). New CSS is confined to a small `about-*` namespace.
- **Palette per page** (kept coherent, adjacent bands distinct):
  hub `blue→white→sky→yellow` · missie `blue→white→sky→light-blue→white→yellow` ·
  visie `blue→white→sky→white→yellow` · organisatie `blue→white→sky→white→yellow` ·
  partners/pers `blue→white→light-blue→yellow` (calmer, "a notch more serious").
- **Emoji chips in the wireframes are placeholders** → built as red rounded-square chips
  with white Flux/Heroicons.

---

## Open questions & concerns (surfaced 2026-06-03 during build)

Content/asset/data dependencies — none block shipping the *structure*, all block calling a
page **Back 🟢 / OK**. Page-specific items also live in each leaf's "Open Questions" in
[`about.md`](about.md); the structural ones are promoted to the
[design concerns register](../01-concerns.md) as **`D-11`**.

1. **[data] Partners page has no real dataset.** `partners` table = faker/lorem rows, no
   logos, **no category column**. Page built from curated static NL copy (the 2 categories
   + named partners from `about-content.md`). To reach Back 🟢: real partner records +
   logos + an institutional/in-kind category, then bind the page to the model. *(→ D-11)*
2. **[data] Pers has no model.** No Press model/table exists; outlet URLs in the spec are
   unverified. Page ships contact-forward with an honest empty state — **no fabricated
   coverage**. To reach Back 🟢: a Press model (`outlet, headline, url, date, language,
   media_type, is_featured, is_archived, chapter_id`) + a curated, verified item list. *(→ D-11)*
3. **[content] Impact stats are stale + duplicated.** 150+/5 500+/120/16+ are 2024 figures
   shown on both hub and Missie. Coordination duo must refresh before launch; keep the two
   surfaces in sync (single source). Built as static NL copy for now.
4. **[content] Coördinatieduo not named.** Organisatie describes the role generically; real
   names + 2-line bios (+ optional photos) pending from the duo. No names invented.
5. **[content] Organigram.** Spec says "reuse the legacy SVG"; that asset isn't in-repo, so
   a lightweight semantic CSS organigram (3 levels) is built. Swap/restyle if the duo wants
   the original.
6. **[asset] Manifesto PDF link is a legacy Wix URL** (`kidicalmass.be/_files/ugd/…`) that
   dies when Wix is decommissioned — re-host on the new site. Couples to `D-7` redirect map.
7. **[content] Press/partner contact = `bike@kidicalmass.be`** used throughout; confirm
   this is the intended press + partnership address (a dedicated `pers@`/`partners@` may
   read cleaner).
8. **[strategy] National vs Brussels scope.** Stats, demands, partners and the duo were
   drafted Brussels-first; copy is written national, but the *facts* (esp. the all-Brussels
   partner list) need a Walloon/Flemish pass with Leticia so the page doesn't imply national
   institutional backing it lacks.
9. **[cross-cutting] Page metadata/OG** for these new pages inherits the site-wide stub head
   — tracked under existing **`D-10`**, not re-opened here.
</content>
</invoke>

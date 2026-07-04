---
title: About-section handoff — open work after the normalize pass
tags: [build, about, handoff]
sources: [multi-agent critique 2026-07-04, log.md 2026-07-04 entry]
phase: build
updated: 2026-07-04
---

# About-section handoff — open work after the normalize pass

**Context.** On 2026-07-04 the about section (P-14–P-20) went through a multi-agent
critique, a fixes wave, and a Frederik-approved normalize pass (decisions D1–D9 via a
throwaway `/design-choices` prototype), plus a news featured-first redesign. Commits
`f65bf8e` → `0d62658` + docs `72f6a31`; full detail in [`log.md`](../log.md) (2026-07-04
build entry). This page collects **everything the critiques surfaced that was deliberately
NOT done** — the backlog a next thread can pick up. Wire/UI across the section stay 🟠
until Frederik's critique of the new surface ([registry](../design/30-skeleton/00-page-registry.md)).

**In flight elsewhere — do not duplicate:** a parallel thread is running an
arrange/polish/distill pass on **Pers (P-19)** (press-archive rows restyled, uncommitted
at the time of writing). Check `git log`/working tree before touching press files.

## 1. Admin track (the "easier to update" goal) — ranked

| # | Item | Effort | Notes |
|---|------|--------|-------|
| 1 | ~~**`TeamMember` model + BlueAdmin resource**~~ **✅ done 2026-07-04** | M | Landed as planned (Teamleden admin, photo via Medialibrary, `x-person-card` row variant + initial-disc fallback, duo renders from DB). Duo photos + bios still pending client content (section 5). |
| 2 | **Formule single source** (partners) | S/M | Tier names + descriptions live in 3 places: `about/partners.blade.php`, `PartnerEnquiry::FORMULE_OPTIONS`, and the sponsorformules PDF. One enum/config array feeding page + form; tiers are provisional pending Leticia, so single-sourcing before the rename saves a triple edit. |
| 3 | **Logo wall from `Partner` records** (partners) | M | Static 1x PNG (`public/img/partners/partner-logos-2024.png`) defeats the admin-editable model; soft on retina, sideways-scroll strip on mobile. Blocked-ish: ~6 partners lack cleared logos (D-11). Interim: 2x export + wrap instead of scroll. |
| 4 | ~~**Quotes/voices table**~~ **✅ done 2026-07-04** | M | Landed as `Quote` + Citaten admin, fixed slots (`mission`/`vision-1`/`-2`), lang fallback per slot; mission quote now uses the quiet `--column` treatment. |
| 5 | ~~**Manifest PDF via Medialibrary + compress**~~ **✅ done 2026-07-04 (reduced)** | S | Frederik dropped the Medialibrary half — stays a static asset, replaced manually. Compressed 7.9→1.1 MB (ghostscript /ebook, 16 pp verified), size in the link text. |
| 6 | ~~**Contact single source**~~ **✅ done 2026-07-04** | S | `kidicalmass.contact` (email/phone/phone_e164) in config; press card, press empty state (`:email`), partner fallback and styleguide all read from it. |
| 7 | **Hub lang-key hygiene** | S | Hub is the only about page with copy fully in Blade (intro sentence, nav-card descriptions, closing CTA strings). Move to `about.php` keys per the file's own header convention. |

Explicitly **not** CMS-worthy (decided during critique): mission axes, vision demands,
organisation structure copy — strategy-stable, stay as lang strings.

## 2. Distill track (copy edits, per-page)

- **Organisation:** intro paragraphs 2–3 pre-narrate the two responsibility lists nearly
  verbatim → cut intro to two paragraphs; "coordination serves, doesn't steer" appears 3x
  (intro 2, intro 3, local list item) → keep once; split the 6-line `organisation_duo_body`.
- **Vision:** two lead-size statement paragraphs read as a bold wall and statement 1
  re-treads mission's origin story → trim + drop statement 2 to base size; the manifest
  card floats heading-less between bands → give it a short `x-section-heading` or fold it
  into the demands band as a coda.
- **Mission:** "Lees wat we vragen" appears twice in one scroll (axis-3 card link + closing
  CTA, `about.php:30` vs `:32`) → drop the in-card link.
- **Hub:** "Nieuws" card description restates its title; open design idea from critique —
  promote the intent pills into the hero panel and let 4 read-cards + closing CTA carry
  the rest.
- **Press:** collapse older archive years behind `<details>` (recent 2-3 open), shorten
  per-item dates (year heading already carries the year) — **likely being handled by the
  parallel pers thread**, verify first.
- **News detail:** consider a "Meer nieuws" prev/next before the closing CTA (reading an
  article then returning shows the same yellow band twice).

## 3. Polish track (visual leftovers from the critiques)

- **Mission:** type-size cliff between the ~28px intro and 16px "Iedereen is welkom" body;
  `pull-quote--large` (centred, 54rem-scale) squeezed into the story column; stat deck on
  mobile = four full-width colour slabs (consider `grid-cols-2`); `AboutStats` deck
  bookends red/red — reorder colours or extend the `stat-card` palette.
- **Organisation:** plainest page in the section — no colour band, no imagery below the
  hero; consider `about-band--sky` for "Wie wat doet" and a two-column duo composition
  (text + person cards) once TeamMember lands.
- **Partners:** 2 live partner cards float in a wide `auto-fit` grid (cap the grid);
  section `padding-block` tight vs the airy hero; fallback contact block is all-700-bold
  (weight only the lead-in); formule tracks blur together when stacked on mobile;
  "Geen fiets?" line is family-register on a B2B page.
- **News:** pagination is Laravel's default view, never styled or seen (fires at article
  13) — brand it before it surprises; 16:9 force-crop on main images is undocumented for
  admins.
- **Press:** hero has no illustration/photo (the copy promises a "fotomoment") — pass a
  `:photo` when a press-suitable shot exists. *(Parallel pers thread may cover this.)*
- **Hub (mobile):** 4 nav cards still stack as posters; the critique suggested compact
  rows (chip left, title right, ~72px) under 768px.
- **Vision:** `intro-text size="lead"` is used only here — document it as the deliberate
  manifesto variant in the component docblock, or drop to default.

## 4. Backend / Nico backlog

- **Article slug URLs** — public URLs are `/nl/about/news/1`; slug column +
  `getRouteKeyName()` (SEO + admin-influence).
- **`app(AboutStats::class)` service-location inside `mission.blade.php`** — inject via
  controller/view composer so the stats path is testable (hub call already gone).
- **Cover-image CMS field** for articles (registry P-18 `[asset]` gap).
- Cross-cutting **D-10** (head metadata/OG) still applies to every about page — tracked
  in [`design/01-concerns.md`](../design/01-concerns.md), not here.

## 5. Content gates (client, not build)

- Duo photos + per-person bios (P-17), Julienne + parent-quote NL translations (P-15/16),
  press 2022 dates + press address confirmation (P-19), ~6 partner logo clearances +
  national partner pass (P-20, D-11).

## Suggested thread-sized bundles

1. **"About admin pass"** — items 1.1 + 1.4 + 1.5 + 1.6 (models + BlueAdmin, one thread).
2. **"About distill + polish"** — sections 2 + 3 after Frederik's critique of the current
   surface (his critique may re-rank them).
3. **"Partners data finish"** — 1.2 + 1.3 + partners polish, once Leticia's tier rename
   and logo clearances land.

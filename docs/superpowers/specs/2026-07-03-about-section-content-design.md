# About-section content pass — design

**Date:** 2026-07-03 · **Approved by:** Frederik (brainstorm session, visual companion)
**Scope:** the Over ons section, P-14 → P-20. Last big section before site wrap-up.

## Goals

1. **Comprehensible content strategy** — kill the org-speak page names, one coherent
   reading path through the section.
2. **Credibility** — one source of truth for impact stats (today three pages tell
   three different numeric stories).
3. **CMS maintainability** — everything list-shaped is client-editable in BlueAdmin;
   story prose stays in Blade (deliberate); copy separated from markup via lang files.
4. **Fewer content types per page** — the Steun-ons simplification pattern applied to
   Missie, Visie, Organisatie and Pers: sections merged under one convincing title,
   bespoke one-off components replaced by the shared kit, one ask per page.

## Decisions (all Frederik's, in-session)

| Decision | Choice |
| --- | --- |
| IA direction | **A — rename in place**, six pages stay; merge-to-one-page stays a post-launch option |
| Who builds | **We build all six original items + restructure**; Nico reviews (BlueAdmin is his package) |
| Curated stats storage | **`volunteers` column on `year_stats`** (same Jaarcijfers screen as participants) |
| Partners binding depth | **Cards from DB + category field; static logo-wall PNG stays** this pass |
| News body editing | **Rich text (`rte`), trimmed toolbar if BlueAdmin allows** |
| Missie structure | **Variant A — Verhaallijn** (Steun-ons twin) |
| Visie structure | **Variant B — Stemmen bij de eisen** |
| Organisatie structure | **Variant A — Mensen en afspraken** (zero bespoke components) |
| Pers structure | **Variant B — Minimaal: contact + archief** |
| Closing CTAs | **Chain within the section**: Wat we doen → Wat we vragen → Hoe we werken → “Zo begin je” (existing exit). Pers gets no closing CTA. Hub closing (“Vind een rit”) unchanged. |

## Renames (labels only)

| Old | New | URL / route name |
| --- | --- | --- |
| Missie | **Wat we doen** | unchanged (`/about/mission`, `about.mission`) |
| Visie | **Wat we vragen** | unchanged |
| Organisatie | **Hoe we werken** | unchanged |

Touches: page `<title>`s, hero eyebrows, `lang/nl/nav.php`, hub nav-card titles
(cards need **new descriptions** — the old ones were promoted to titles), and
cross-link copy in running text (Missie→Visie link, Pers→Missie link). Copy per
the ToV guide, no em-dashes. Nieuws/Pers/Partners keep their names.

## Work items

### 1. Fix the curly-quote markup bug (do first, standalone)

`mission.blade.php:73` and `vision.blade.php:55–61` use smart quotes as Blade
attribute delimiters. Live effect: Visie's quote wrapper loses its
`about-voices` class; Missie prints literal `”`. Straight-quote them. (The
sections get rebuilt in items 4–5, but the fix is a 5-minute standalone win and
the restructure may land days later.)

### 2. One stats source

- Migration: nullable unsigned int `volunteers` on `year_stats`; field in the
  Jaarcijfers admin form (`x-ba-input`), `YearStatRequest` validation, BlueAdmin
  `YearStat` config columns.
- New `App\Support\AboutStats` with a `cards()` API shaped like
  `SupportStats::cards()` (`value`/`label`/`color`) so `x-stat-card` consumes it
  directly. Content: **gemeenten** = `Group::visible()->count()`,
  **fietsparades sinds 2020** = all-time published KIDICALMASS activities,
  **vrijwilligers** and **deelnemers in {year}** = latest `YearStat` row.
  Omission rule as in SupportStats: no honest value → no card.
- Consumers: the hub stat band (keeps its band layout, reads AboutStats) and the
  new Missie story deck (item 4). Both hardcoded stat blocks die. `SupportStats`
  and Steun-ons are untouched.
- Closes concern **D-13**.

### 3. Rename pass

As per the table above. Grep views *and tests* for old labels; rewrite, don't
delete, any test assertions that reference them.

### 4. Restructure Missie → “Wat we doen” (variant A, Steun-ons twin)

Hero → **story section**: one text column (current intro ¶s + “Iedereen is
welkom” merged, Julienne pull-quote inline in the column, getting-started link
kept) with the **stat deck** (`x-stat-card` × ≤4 via AboutStats) beside it →
**“Drie dingen die we doen”** (3 × `feature-card`, unchanged content; the third
card's link label becomes “Lees wat we vragen →”) → **closing CTA chained to
Wat we vragen**. Cut: the separate stats band, the separate welcome section, the
separate quote section, and the `support-callout` (the support ask lives on
Steun-ons). 7 → 4 content types.

### 5. Restructure Visie → “Wat we vragen” (variant B, Stemmen bij de eisen)

Hero → position statement tightened to 1–2 ¶ (keep “Dat is niet radicaal.”) →
**4 numbered demands with parent voices nested** under the demand they speak to
(Fatima under 1 · veilige infrastructuur, Camille under 2 · tragere straten —
final placement is an implementation judgement call), composed from existing
`numbered-item` + `pull-quote`, no new component → **manifesto as `info-card`**
(same component as the Pers contact card) linking the re-hosted PDF (item 9) →
**closing CTA chained to Hoe we werken**. “Word lid” disappears from this page
(deliberate; membership ask lives on Steun-ons). 5 → 5 types but all shared,
5 → 4 sections.

### 6. Restructure Organisatie → “Hoe we werken” (variant A, Mensen en afspraken)

Hero → intro (3 ¶, absorbs the “geen hoofdkantoor, geen betaald personeel” line
from the cut callout) → **“Wie wat doet”** as 2 × `titled-list-block`
(national / local, lists pruned to ~4 points each) → **duo section**:
2 × `person-card` + one paragraph that also carries safety & vorming (the duo
runs it) and the getting-started link → closing stays “Zo begin je”. Cut: the
`about-organigram` (intro prose already tells the three-tier story), the
`ho-deal` two-column block, the callout, the separate safety section.
7 → 4 content types, **zero bespoke components**. Note: `ho-deal` CSS belongs to
the help-out page — stop *using* it here, don't delete it; delete
`about-organigram` CSS only if nothing else uses it.

### 7. Restructure Pers (variant B, Minimaal)

Hero → **contact section**: one ¶ (the three-point offer list compressed into a
sentence) + achtergrond link line (“… lees Wat we doen →”) + `info-card`
perscontact → **archive** (existing dynamic year-grouped list + empty state) →
**no closing CTA** (it repeated the perscontact card). Cut: the hardcoded outlet
strip (the seeded archive shows the outlets itself). 6 → 3 content types.

### 8. Press archive import

The old site's press archive survives in `docs/raw/website/press.md`
(~20 entries, 2020–2025: RTBF, Vivacité, HLN, Bruzz, BX1, La DH, Het
Nieuwsblad, Politico). Build an idempotent **`PressArchiveSeeder`**
(`updateOrCreate` keyed on `url`): headline into `title_nl` as the display
title regardless of article language; `published_at` from the entry or
extracted from the URL where the scrape is vague (several URLs embed dates);
attach the 4 re-hosted persbericht PDFs (item 9) as `document` media — the
page already renders a per-article document link. Source PDFs live in the repo
(they were public on Wix; repo is public — fine). Client maintains the archive
in BlueAdmin afterwards.

### 9. Re-host the PDFs

Manifest + 4 persberichten, currently on `kidicalmass.be/_files/ugd/…` (dies at
Wix decommission). Fetch, commit under `public/downloads/` (manifest) and
`database/seeders/files/press/` (persberichten, copied into media storage by
the seeder), update the Visie info-card link. Closes **D-7**.

### 10. Partners: cards from DB

- `App\Enums\PartnerCategory` — Institutioneel / Bondgenoot / Operationeel — in
  the `ActivityType` style (`label()`, `getOptionsArray()`).
- Migration: nullable `category` on `partners`, **backfilling by name** for
  Brussel Mobiliteit, Brussel Stad, Gemeente Schaarbeek, Clean Cities Campaign
  → the page needs no static fallback path.
- Admin: `x-ba-select` in the partners form, `PartnerRequest` validation,
  BlueAdmin config columns.
- Page: partner cards render `visible` partners with category
  institutioneel/bondgenoot (institutioneel first), `name` + `description_nl`.
  Logo-wall PNG and the operationeel find-a-bike line stay as-is. Advances **D-11**.

### 11. News editorial controls

- Migration on `articles`: `is_published` (default false) + `published_at`
  (nullable datetime); **backfill existing rows to published,
  `published_at = created_at`**.
- Model: `#[Scope] published()` / `drafts()` — verbatim the Activity pattern.
- Controller: index `published()->orderByDesc('published_at')`; show
  `abort_unless($article->is_published, 404)`. Card + show dates switch to
  `published_at`.
- Admin form: publish toggle, date field, `rte` on both content textareas; the
  public renderer switches from `nl2br(e())` to `{!! !!}` inside the existing
  prose styles.
- Caveats (accepted): `rte` is documented as a boolean — if the TinyMCE toolbar
  isn't configurable per-field, take the default and flag trimming to Nico
  rather than patching his package. Unescaped HTML is acceptable because
  authors are admins only.

### 12. Copy → lang file

New `lang/nl/about.php` with `mission.*`, `vision.*`, `organisation.*`,
`press.*` key groups (the `support.php` precedent). All copy on the four
restructured pages goes through `__()`. The hub keeps inline copy this pass.

### 13. Pipeline & wiki pass (after build)

- Registry rows: P-15/16/17/19 Wire back to 🟠 (rebuilt, pending Frederik's
  critique); Back column: P-14/15 stats 🟢, P-18 🟢 (editorial controls),
  P-19 🟢 (archive seeded — also fix the stale “no Press model yet” gap note),
  P-20 🟠 (cards bound, wall static). Top-gaps cells + roll-up updated.
- `log.md` entry; `/build` dashboard verified (`BuildStatus::report()` clean).

## Cross-cutting

- **Branch/worktree:** all work on a feature branch in a git worktree (Nico
  commits to this checkout concurrently). Squash at `/wrap` into ~3 curated
  commits: content pass (1+3+4–7+12), stats source (2), CMS bindings (8–11).
- **Testing** (per `docs/testing-conventions.md`): AboutStats counting +
  omission rules; per restructured page assert `__()` keys, chained-CTA `href`s
  and `data-*` seams — never utility classes; partners category rendering;
  article draft/publish + ordering; existing assertions on removed structure
  get rewritten (no deletions without approval). `CssArchitectureTest` must
  stay green after CSS-partial removals. Known flake: `CalendarProximityTest`
  in full-suite runs.
- **Effort:** ~4–5 days total.

## Non-goals

Hub restructure (only its stat band + card labels change) · Direction B page
merge · CMS-editable story prose / page builder · dynamic logo wall · FR locale
· Nieuws page restructure · new content types beyond the `category` and
`volunteers` columns.

## Open content items (people, not code)

Duo photos + bios (Leticia & Cecilia) · NL translations of the parent quotes
(pending duo confirmation) · national-scope pass on stats, demands and the
partner list (Leticia) · press-address confirmation (`bike@kidicalmass.be`) ·
~6 partner logos not yet cleared.

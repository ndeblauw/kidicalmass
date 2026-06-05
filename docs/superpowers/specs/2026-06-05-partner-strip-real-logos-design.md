# Partner strip — real logos instead of stock photos

**Date:** 2026-06-05
**Status:** Approved, ready for implementation plan
**Owner:** Frederik (design), Claude (build)

## Problem

The site-wide recognition strip (`resources/views/components/partners.blade.php`,
"Mede mogelijk gemaakt door") loops over `Partner` records and renders whatever
lives in each partner's `logo` media collection. But `PartnerFactory::attachImage`
attaches a **random `picsum.photos` stock photo** as the logo, so the strip shows
a row of nature/city thumbnails instead of partner logos.

The real national partner list (names + URLs) already exists in
`DatabaseSeeder::partnerData()`. Only the logo image files are missing — Brussel
Mobiliteit (`public/img/sponsors/bm-{nl,fr}.avif`) is the sole real logo today.

## Goal

The strip shows real partner logos. No code path can render a stock photo as a
logo again. Missing logos degrade gracefully to a quiet text chip, not a gap or a
stock image. Logos are stored full-colour now; a monochrome treatment is a
deliberate later exploration and must not require rework to add.

## Non-goals

- Choosing/applying a monochrome treatment. Originals are stored full-colour; the
  strip displays them in colour. Monochrome is a separate future pass.
- Touching the `/about/partners` page logo wall (`partner-logos-2024.png`) beyond
  using it as a slicing source.
- Admin/Filament logo upload UX. Production logos are managed via the existing
  media library; this spec only fixes seed data + render + fallback.

## Design

### 1. Curated logo files (committed truth)

- New directory: `public/img/partners/logos/raw/{slug}.{svg|png}`
- `slug = Str::slug($partner->name)` → `pro-velo`, `cyclo`, `fietsersbond`,
  `gracq`, `clean-cities`, `heroes-for-zero`, `fietsbieb`, `bruzz`, `growfunding`,
  `my-kids-bikes`, `succulente`, `les-chercheurs-d-air`, `park-poetik`, …
- Full colour. Mirrors existing committed `public/img/partners/` and
  `public/img/sponsors/` convention.
- A future `public/img/partners/logos/mono/` sibling (or a CSS-filter pass) is
  where monochrome will live — out of scope here, but the `raw/` naming reserves
  the slot.

### 2. Acquisition pass (one-off, performed during build)

For each **national** partner (`group_id IS NULL`, `show_logo`, `visible`), try in
order:

1. **Clearbit logo API** by domain extracted from `url` (`logo.clearbit.com/{domain}`).
2. **Homepage scrape** for an SVG / `og:image` / header logo if Clearbit misses.
3. **Slice `partner-logos-2024.png`** for partners with no `url`
   (My Kids Bikes, Succulente, Les Chercheurs d'Air, Park Poetik) or where both
   fetches fail.

Trim whitespace and normalize to a consistent height. Keep SVG when available,
else PNG (prefer transparent background).

**Coverage report** (printed at the end of the pass): per partner, one of
`fetched` / `sliced` / `flagged-mediocre` / `failed`. Mediocre fetches are kept
and flagged (per Q2) so Frederik knows what to hand-swap. `failed` partners have
no file and will render the text chip.

### 3. Rewire the factory (kill stock photos)

`PartnerFactory::attachImage` no longer pulls `picsum.photos` for the `logo`
collection. New behaviour:

- Resolve `public/img/partners/logos/raw/{slug}.*`.
- If a file exists, attach it to the `logo` collection (`preservingOriginal`).
- If not, attach nothing.

No random-image fallback exists for logos, so a stock photo can never appear as a
logo again. (The generic image cache stays in use for other models that legitimately
want random imagery — only the partner-logo path changes.)

### 4. Blade: graceful fallback + de-dupe BM

`resources/views/components/partners.blade.php`:

- **Text-chip fallback:** when a partner is `show_logo` + `visible` but has no
  `logo` media URL, render `<span class="partner-strip__chip">{{ $partner->name }}</span>`
  instead of rendering nothing. Quiet, monochrome, matches the strip's calm tone.
- **De-dupe Brussel Mobiliteit:** BM is already rendered as the hardcoded
  locale-specific lead logo (`$bmLogo`). It is *also* a national partner record, so
  it currently loops a second time (today as a stock photo). Exclude it from the
  loop (skip when `Str::slug($partner->name) === 'brussel-mobiliteit'`) so it shows
  exactly once.

New CSS `.partner-strip__chip` in `resources/css/app.css` (appearance lives in CSS
per the public-site frontend rules — template carries structure only).

### 5. Tests

`tests/Feature/PartnerStripComponentTest.php` (Pest feature test):

- A partner with a `raw/{slug}` logo renders an `<img>` whose `src` points at that
  logo (not `picsum`).
- A logo-less `show_logo` partner renders `.partner-strip__chip` with its name, not
  an empty gap.
- The rendered strip never contains a `picsum.photos` URL.
- Brussel Mobiliteit appears exactly once.

## Risks / notes

- **Public repo:** `ndeblauw/kidicalmass` is public. The fetched logos are real
  partner brand marks the org legitimately displays (the partners page already
  commits a logo wall), so this is consistent with existing practice.
- **Shared working tree:** Nico commits concurrently in the same checkout. Stage
  only the files this work touches; never `git add -A`; do not push `main`.
- **Clearbit availability:** the logo API may be rate-limited or deprecated; the
  scrape + slice fallbacks cover misses, and the coverage report makes any gaps
  explicit rather than silent.

# About admin pass — design

**Date:** 2026-07-04
**Source:** [`docs/wiki/build/20-about-section-handoff.md`](../../wiki/build/20-about-section-handoff.md) section 1, items 1.1 + 1.4 + 1.5 + 1.6 (suggested bundle 1, "About admin pass").
**Goal:** make the about section easier to update by moving admin-editable content (team duo, parent quotes, contact details) out of Blade/lang hardcodes, and fix the oversized manifest PDF. Follows the existing Partner/YearStat BlueAdmin patterns.

## Phase 1 — design exploration (before building)

A throwaway `/design-choices` prototype page (same pattern as the normalize pass and
pers pass prototypes), rendered with the site's real tokens and CSS partials, showing:

**Person card** (in the organisation duo band context):

- **A. Portrait stack** — photo on top, name / role / bio below; two cards side by side
  in the existing band.
- **B. Horizontal row** — photo left, text right; cards stacked vertically, reading as
  "two people who run this" (anticipates the two-column duo composition floated in the
  handoff's polish track).
- Each direction shown **twice**: once with a placeholder photo, once with the
  photo-less **initial-letter disc** fallback, so the fallback state is designed, not
  inherited.

**Quote** (DB-driven, through `x-pull-quote`):

- **A. Baseline** — current `pull-quote--large` unchanged.
- **B. Quieter column treatment** — left-border / indented variant sized for the story
  column (addresses the polish-track note that the centred 54rem-scale quote is
  squeezed into mission's column).

Frederik critiques in the browser and picks per element; the winners are what phase 2
builds. The prototype file is deleted before the wrap commit (established convention).

## Phase 2 — build

### 1. `TeamMember` model + BlueAdmin resource (handoff 1.1)

- **Migration** `create_team_members_table`: `name` (string), `role` (string),
  `bio_nl` / `bio_fr` (nullable text — mirrors Partner's `description_nl`/`_fr`),
  `sort` (integer, default 0), `visible` (boolean, default true), timestamps.
- **Model**: implements `HasMedia`, single-file `photo` collection with a square
  `thumb` conversion (mirrors Partner's `logo` collection). Factory included.
- **Seeder**: rows for Leticia and Cecilia (name + role `Coördinatie` only; bios and
  photos are pending client content — handoff section 5).
- **`app/BlueAdmin/TeamMember.php`**: mirrors `app/BlueAdmin/Partner.php` —
  `$filepond = ['photo']`; index columns name, role, visible; auto-discovered, no
  registration step.
- **`organisation.blade.php`**: duo band renders
  `TeamMember::where('visible', true)->orderBy('sort')` passed from the controller
  (no `app()` service location in the view — same lesson as the AboutStats backend
  item in the handoff).
- **`x-person-card`**: gains a `bio` prop (short paragraph under the role) and the
  designed photo-less state (initial-letter disc in a brand token colour) per the
  phase-1 winner. New CSS goes in the person-card's role-based partial under
  `resources/css/components/`, never `app.css`.

### 2. `Quote` model, fixed slots (handoff 1.4)

- **Migration** `create_quotes_table`: `slot` (string, unique), `quote` (text),
  `attribution` (string), `visible` (boolean, default true), timestamps.
  Slots: `mission`, `vision-1`, `vision-2`. NL-only text — quotes are spoken NL
  content; no `_fr` columns (YAGNI, no testimonial-translation convention exists).
- **BlueAdmin resource** with slot visible in the index. **No seeder rows**: each page
  falls back to its current lang string when a slot has no visible row, so nothing
  changes visually until an admin enters a quote.
- **Lookup helper** (small support class à la `AboutStats`, e.g.
  `Quotes::forSlot('mission')`) injected via the controller so mission/vision
  templates stay dumb: `$quote?->quote ?? __('about.mission_quote')`, same for
  attribution.
- **Rendering** through `x-pull-quote` per the phase-1 winner (baseline `--large` or
  the quieter column variant).

### 3. Manifest PDF — compress + size in link (handoff 1.5, reduced scope)

Decision: **no Medialibrary/model** for the manifest (Frederik, 2026-07-04) — it stays
a static asset that is replaced manually when needed.

- Compress `public/downloads/kidical-mass-manifest.pdf` (currently 7.9 MB) with
  ghostscript, target **< 2 MB**; verify it still renders correctly.
- Vision page link text gains a hardcoded size label (e.g. "PDF, 1,8 MB") — no runtime
  `filesize()` for a static asset; the label changes only when the file does.

### 4. Contact single source (handoff 1.6)

- `config/kidicalmass.php` gains
  `'contact' => ['email' => 'bike@kidicalmass.be', 'phone' => '0495 81 27 95']`
  (same shape as the existing `social` block from the news pass).
- Replace the three hardcodes:
  - `resources/views/nl/about/press.blade.php:29` (email),
  - `resources/views/about/partners.blade.php:94–95` (email + phone),
  - `lang/nl/about.php:109` `press_empty_body` — gains an `:email` placeholder,
    filled at the call site from config.

## Tests (per `docs/testing-conventions.md`)

- **TeamMember**: one feature test — visible members render name/role on the
  organisation page, invisible ones don't (factory-driven).
- **Quote**: one feature test — a DB quote overrides the lang-string fallback for its
  slot; with no row the lang string renders.
- **Contact**: fold into existing press/partners assertions — pages show
  `config('kidicalmass.contact.email')` / `.phone` (assert via config, not literals).
- **Not tested**: PDF size label (copy), BlueAdmin CRUD (package behaviour),
  pull-quote styling (visual).

## Out of scope

- Formule single source + logo wall (handoff 1.2/1.3 — gated on Leticia / D-11,
  bundle 3).
- Hub lang-key hygiene (handoff 1.7 — not in this bundle).
- Distill/polish tracks (handoff sections 2–3 — gated on Frederik's critique), except
  where the quote variant B and duo composition happen to overlap, resolved by the
  phase-1 pick.
- Article slugs, AboutStats injection refactor, cover-image CMS field (handoff
  section 4 — Nico backlog).

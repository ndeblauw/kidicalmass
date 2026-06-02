---
title: Design — concerns register
tags: [design, concerns]
sources: [wiki/strategy/01-concerns, wiki/design]
phase: design
updated: 2026-06-03
---

# Design — concerns register

Open design-plane decisions. Stable IDs (`D-n`) never change. States: **Open** · **Partly** · **Closed**. Several graduated from the Strategy register when the Design phase opened — their lineage is noted.

## At a glance

| State | Count | IDs |
|---|---|---|
| Open | 1 | `D-10` |
| Partly | 4 | `D-1`, `D-3`, `D-7`, `D-9` |
| Closed | 2 | `D-2`, `D-4` |

**Conclusion gate:** **one fully-Open design concern: `D-10` (page metadata / SEO / social share previews — never addressed).** `D-2` (meetups public, chapter pages only) and `D-4` (tokens live + documented) are Closed; `D-7` (redirect map) is drafted with build fill-ins named; `D-1` (back-office detail, pending Alexandre), `D-3` (Grande KM, confirm with Leticia) and `D-9` (one-off support path, confirm with Leticia) carry decided directions with named remainders.

---

## Open

### `D-10` — Page metadata, SEO & social share previews — **Open** (flagged 2026-06-03)
- **Problem:** the public `<head>` ([`layouts/site.blade.php`](../../../resources/views/layouts/site.blade.php)) is a stub. Page titles are passed bare and unsuffixed (e.g. Help-out is just **"Meehelpen"**, with no `· Kidical Mass` pattern); the `<meta name="description">` is **hardcoded, English, and site-wide** (wrong language for the NL site, identical on every page); and there are **no Open Graph / Twitter Card tags, no canonical URL, and no `hreflang`/locale alternates** at all. Shared links (WhatsApp/Facebook/Instagram — on a ~75 %-mobile, heavily-shared audience) render with no preview title, copy, or image. This affects **every P-row**, so it is tracked here as a cross-cutting concern rather than per page.
- **Two sides:**
  - **Build mechanism** — a `<head>` metadata partial driven by per-page props (`$title`, `$description`, `$ogImage`, canonical) with a single title pattern; locale-aware `hreflang` alternates once the `/nl`·`/fr` middleware lands (couples to [`D-7`](#d-7--redirect-map-launch--drafted-2026-06-02)); favicon/touch-icon stack. Graduates to `build/01-concerns.md` when Build opens (coding days 2026-06-16/17).
  - **Design / content** — the title pattern, a per-page description written in voice ([`tone-of-voice.md`](../../tone-of-voice.md)), and an **OG-image strategy per page type** (events especially want their own hero/route image; a branded default for the rest).
- **Safe to:** keep shipping pages now; this is additive `<head>` work that doesn't block the surface pass. Do **not** call the site launch-ready until it's resolved — bare share previews are launch-visible.
- **Next step:** decide title pattern + default OG image with Frederik; spec the per-page-type description/OG plan; build the head partial when Build opens.

---

## Partly

### `D-1` — Private organiser back-office + attendance *(was strategy `S-3`)* — **validated as a design input**
- **Validated (Frederik 2026-06-02):** build a per-chapter volunteer back-office, in two layers:
  - **Before signing up:** a volunteer clearly sees *what to expect*.
  - **Once logged in:** the things now living in WhatsApp — how it works, documents to read, a video, when the meetups are, who leads the chapter and their role, what roles exist and what yours is / could be.
- **Attendance rule (decided):** "I'm coming" is **account-only, volunteers-only**, on **all** activity types (rides + meetups). **Display shows the hosts/organisers attending (a social nudge), not the full attendee list** (Leticia); the lead may see the full roster. Adds an **Attendance** relation ([content model](20-structure.md)).
- **Structural boundary (decided, Frederik 2026-06-02):** the back-office is a **separate branded frontend surface** (`/backstage/[postal-code]`, read-mostly), **not** the Filament `/admin` panel — rank-and-file volunteers (P4) never touch Filament. Accounts are **invite-only** (leads provision in `/admin`; no public Register). Post-login landing = [My activities](20-structure.md). These are locked in [Structure](20-structure.md).
- **Remainder:** the back-office *content* detail is real work — Frederik will seek clarity (and possibly the actual material) from the Alexandre/J3 interview before specing it. Content brief deferred until then.
- **Next step:** Alexandre (Schaerbeek, J3) interview.

### `D-3` — Grande Kidical Mass as a featured event *(was strategy `S-5`)*
- **Remainder:** migration normalises the annual flagship into Events as a *featured* event (no hand-built yearly page). Not explicitly confirmed with Leticia.
- **Safe to:** design against; confirm before retiring the yearly-page pattern.

### `D-9` — One-off support path *(funding)* — **decided direction, pending Leticia** (2026-06-02)
- **Context:** the support flow was reworked (term "steun" not "lid"; prominence elevated) — see [`10-scope.md` § Support](10-scope.md) and [`30-skeleton/steun-ons.md`](30-skeleton/steun-ons.md). Growfunding/Spacefunding is **recurring-only**; v1 had **dropped** a one-off path.
- **Decided direction (Frederik 2026-06-02):** **reinstate a discreet one-off option** on `/steun-ons`, secondary to the monthly lead — most likely the **BE72… bank transfer** that was dropped, or a one-off Growfunding gift if the platform allows.
- **Remainder:** confirm with **Leticia** that a one-off is wanted in v1, and pin the **mechanism + account/IBAN** (the live BE-number was never re-captured here per the public-repo guardrail — lives in Notion).
- **Safe to:** design the page with a secondary "liever één keer?" slot; do not publish an IBAN until confirmed.

### `D-7` — Redirect map *(launch)* — **drafted** (2026-06-02)
- **Resolved:** old Wix URLs → new routes documented in [`26-redirect-map.md`](26-redirect-map.md); all `301`. **Language rule decided:** redirects target neutral paths, a locale middleware resolves `/nl/`|`/fr/` (Accept-Language → cookie → geo, fallback NL).
- **Remainder (build, not design):** locale middleware + 301 config; fill the three Grande KM event `{slug}`s at seed; confirm the two combined-postal canonicals; preserve `/post/{slug}` slugs through blog migration; post-launch crawl of the critical set. Hand-off list in [`26-redirect-map.md` § Build hand-off](26-redirect-map.md).

---

## Closed

### `D-2` — Meetup visibility breadth *(was strategy `S-4`)* — **Closed** (Frederik 2026-06-02)
- **Decided & locked:** meetups (`meeting`/`workshop`/`other`) are **fully public** — visible to non-logged-in visitors, **all details** (incl. meeting point), **all groups (cross-group)** — as a traction/recruitment signal showing the movement's momentum. **Login gates attendance + the back-office, not viewing.** This **revises `D-8`** (which previously login-gated meetups) and is treated as **locked**: it diverges from Leticia's earlier "strong local communities" stance, accepted as the decided direction with no further client gate.
- **Where they surface (settled):** **chapter pages only** — each chapter page lists its own meetups/workshops publicly. **No national movement/aggregation view**, and **not** on the family ride calendar (`/events` stays rides-only, J1-focused). A logged-in volunteer's cross-group view lives in [My activities](30-skeleton/my-activities.md), not a public surface.
- **Decided:** the `Activity` viewing rule (no view-gate) + what login gates (attendance, back-office).
- **Remaining detail (not a standing concern):** the logged-in [My activities](30-skeleton/my-activities.md) default-municipality filter is a skeleton-level "to test", documented there.

### `D-4` — Surface plane / design tokens — **Closed** (2026-06-02)
- **Resolved:** the original premise ("no machine-readable tokens exist") was outdated — tokens are **live in [`resources/css/app.css`](../../../resources/css/app.css) `@theme`** (Tailwind v4), set in the merged `design/activity-page` redesign. **Source-of-truth decision:** `app.css` is canonical; **[`DESIGN.md`](../../../DESIGN.md)** (repo root) is the human-readable documentation — palette, typography (Poppins / Nunito Sans / Fredoka One), semantic tokens, and the signature design language (−3° tilts, red icon chips, full-bleed colour blocks). Direction/rationale stay in [`50-surface.md`](50-surface.md).
- **Palette status:** approved (the as-built redesign is the intended brand system), per Frederik 2026-06-02.
- **Not a blocker:** spacing/radius aren't formally tokenised (Tailwind defaults + ad-hoc component values); lift into `@theme` later if reuse grows — noted in `DESIGN.md`.

---

## Notes

- `D-5` (patterns not extracted) and `D-6` (page registry missing) — **resolved** in this pass: [`40-patterns.md`](40-patterns.md) and [`30-skeleton/00-page-registry.md`](30-skeleton/00-page-registry.md) now exist (first pass). Not tracked as standing concerns.
- Content-level notes (e.g. FR hero "kets" neutrality, date/month format) live in the relevant skeleton/content files, not here.

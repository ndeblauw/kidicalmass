---
title: Design — concerns register
tags: [design, concerns]
sources: [wiki/strategy/01-concerns, wiki/design]
phase: design
updated: 2026-06-05
---

# Design — concerns register

Open design-plane decisions. Stable IDs (`D-n`) never change. States: **Open** · **Partly** · **Closed**. Several graduated from the Strategy register when the Design phase opened — their lineage is noted.

## At a glance

| State | Count | IDs |
|---|---|---|
| Open | 3 | `D-10`, `D-11`, `D-12` |
| Partly | 4 | `D-1`, `D-3`, `D-7`, `D-9` |
| Closed | 2 | `D-2`, `D-4` |

**Conclusion gate:** **three fully-Open design concerns: `D-10` (page metadata / SEO / social share previews — never addressed), `D-11` (About credibility leaves have no real data — Partners + Press), and `D-12` (volunteer-enquiry follow-up / ownership — new, Alexandre/J3 2026-06-05).** `D-2` (meetups public, chapter pages only) and `D-4` (tokens live + documented) are Closed; `D-7` (redirect map) is drafted with build fill-ins named; `D-1` (**evidence gate closed by Alexandre/J3 2026-06-05** — back-office content brief now concrete, per-event attendance cut, replaced by the volunteer roster), `D-3` (Grande KM, confirm with Leticia) and `D-9` (one-off support path, confirm with Leticia) carry decided directions with named remainders.

---

## Open

### `D-10` — Page metadata, SEO & social share previews — **Open** (flagged 2026-06-03)
- **Problem:** the public `<head>` ([`layouts/site.blade.php`](../../../resources/views/layouts/site.blade.php)) is a stub. Page titles are passed bare and unsuffixed (e.g. Help-out is just **"Meehelpen"**, with no `· Kidical Mass` pattern); the `<meta name="description">` is **hardcoded, English, and site-wide** (wrong language for the NL site, identical on every page); and there are **no Open Graph / Twitter Card tags, no canonical URL, and no `hreflang`/locale alternates** at all. Shared links (WhatsApp/Facebook/Instagram — on a ~75 %-mobile, heavily-shared audience) render with no preview title, copy, or image. This affects **every P-row**, so it is tracked here as a cross-cutting concern rather than per page.
- **Two sides:**
  - **Build mechanism** — a `<head>` metadata partial driven by per-page props (`$title`, `$description`, `$ogImage`, canonical) with a single title pattern; locale-aware `hreflang` alternates once the `/nl`·`/fr` middleware lands (couples to [`D-7`](#d-7--redirect-map-launch--drafted-2026-06-02)); favicon/touch-icon stack. Graduates to `build/01-concerns.md` when Build opens (coding days 2026-06-16/17).
  - **Design / content** — the title pattern, a per-page description written in voice ([`tone-of-voice.md`](../../tone-of-voice.md)), and an **OG-image strategy per page type** (events especially want their own hero/route image; a branded default for the rest).
- **Safe to:** keep shipping pages now; this is additive `<head>` work that doesn't block the surface pass. Do **not** call the site launch-ready until it's resolved — bare share previews are launch-visible.
- **Next step:** decide title pattern + default OG image with Frederik; spec the per-page-type description/OG plan; build the head partial when Build opens.

### `D-11` — About credibility leaves have no real data *(Partners + Press)* — **Open** (flagged 2026-06-03)
- **Problem:** the two About leaves whose *whole job* is credibility/social proof have no usable data behind them. **Partners:** the `partners` table holds only faker/lorem-ipsum rows with **no cleared logo assets**, **no national records** (all 15 seeded rows carry a `group_id`, i.e. chapter-local), and **no institutional/in-kind category column**, so it can't drive a real Partners page. **This is now launch-visible site-wide:** the slim `partner-strip` ([PAT-5](40-patterns.md), every page above the footer) reads the model for **national** logos and currently finds none, so it renders only the hardcoded Brussel Mobiliteit logo — **we need real national-partner records + cleared logos to populate it.** **Press:** there is **no Press model/table at all**, and the outlet URLs in the spec ([`about-content.md`](30-skeleton/about-content.md)) are unverified. These pages exist to prove the movement is real — so faking them is self-defeating.
- **Decided handling (this build):** ship both honestly. **Partners** is built from **curated static NL copy** (the two categories + named partners from the spec) — looks real because the *names* are real, no lorem. **Press** ships **contact-forward** (hero + "journalisten, we praten graag" + `bike@kidicalmass.be`) with an **honest empty state** — no fabricated coverage. Structure is launch-ready; the data is not.
- **To reach Back 🟢:**
  - **Partners** — real partner records + cleared logos + a category field (institutional / movement-ally / in-kind), then bind `/about/partners` to the model; reconcile with the site-wide `partner-strip` (which already reads the model for logos). **Acquisition flow:** the Sponsorformules + Partnercharter PDFs exist (a *KM Brussels* tier ladder €100–2.500/yr + a charter) and become an on-page summary + downloadable docs + routed form ([`partners.md` § conversion flow](30-skeleton/partners.md#become-a-partner-conversion-flow--plan-2026-06-03)). **Before the prices go live:** Leticia confirms the tiers apply *nationally*, and the PDFs are re-hosted off Wix (ideally the confirmed national versions).
  - **Press** — a `Press` model (`outlet, headline, url, date, language, media_type, is_featured, is_archived, chapter_id`) + a curated, **verified** item list (priority: RTBF, BX1 video), incl. the 2020–2021 dead-link "archived" rule.
- **Couples to:** the all-Brussels partner list (national-scope pass with Leticia) and the `bike@kidicalmass.be` vs dedicated `pers@`/`partners@` question — both in [`about-journey.md` § Open questions](30-skeleton/about-journey.md).
- **Safe to:** ship the pages now; do **not** call them Back/OK until the data lands.
- **Next step:** confirm the real partner + press lists with the coordination duo; size the Press model for Build.

### `D-12` — Volunteer-enquiry follow-up / ownership — **Open** (flagged 2026-06-05, Alexandre/J3)
- **Problem:** the docs already route a per-chapter contact form to the local lead and let leads respond in-platform, but the **follow-up / ownership / tracking mechanism is unresolved.** When a routed enquiry arrives, who owns it — one responsible mailbox per chapter, or all leads with a **status/tracking** system so nothing falls through? This is the original `bike@` "email black hole" reframed at chapter level: routing to the right place does not by itself guarantee anyone answers.
- **Safe to:** ship the routed chapter form now (the routing is decided — J2); this concern is about what happens *after* submission.
- **Next step:** decide ownership model (single chapter mailbox vs. all-leads-with-status-tracking) with the coordination duo; size for Build. Tied to the `Volunteer enquiry` entity ([content model](20-structure.md)).

---

## Partly

### `D-1` — Private organiser back-office *(was strategy `S-3`)* — **evidence gate CLOSED (Alexandre/J3, 2026-06-05); content brief now concrete**
- **Evidence gate closed (Alexandre/J3, 2026-06-05):** this interview was the planned evidence gate — "the biggest item gating build." Outcome: the **back-office is confirmed IN for v1**, its content brief is now fillable, and its purpose is reframed. Mature chapters (Schaerbeek) publish self-sufficiently; the real jam is **material retrieval/distribution** (promo + posters, owned by Leticia; pink-vest docs lost in WhatsApp) and **hand-holding new/small chapters**. So the back-office = a **material library** whose **primary beneficiary is new/small chapters + organiser onboarding**, not the convenience of mature chapters (serves org-goal #5).
- **Content brief — a material library in three layers:**
  - **(1) Before signing up:** what to expect as a volunteer.
  - **(2) Once logged in (pink vest):** how it works, documents, a video, the meetup schedule, who leads the chapter + their role, what roles exist and what yours is / could be, plus a visible **growth path** toward deeper contribution (see Open Issue / `D-1` content note below).
  - **(3) NEW — new-chapter / organiser onboarding:** how to *start and run* a chapter (commune contact, route planning, promo/poster downloads) — distinct from the pink-vest onboarding set, for new local organisers.
- **Posters/promo are stored & distributed as downloads** in the library — the library does **not** generate them (poster/flyer auto-generation stays a confirmed scope cut).
- **Volunteer roster (replaces attendance as the social feature — Decision C):** a volunteer can **opt in** to show themselves **publicly** on their chapter page; the **full "who are the other volunteers in this chapter" roster** is visible **only to logged-in volunteers**. It is **per-chapter and standing, NOT per-event** — therefore **not** an `Attendance` relation. **Data model:** the existing **`group_user` pivot** (volunteer ↔ chapter) + an **opt-in public-visibility boolean**. **Login now gates the back-office *and* the roster view** (viewing rides/meetups stays fully public).
- **Per-event attendance ("who's coming") is CUT (Decision B):** all three volunteers (Alexandre, Jorge, Morgane) manage turnout via WhatsApp polls; none asked for it on the site. The previously-planned `Attendance` (volunteer ↔ activity) relation is **not built**. Login no longer gates attendance.
- **Light account, driver = material access (Decision F):** volunteers (pink vests) get a **light, mostly read-only, invite-only** account; its reason to exist is **reliable cross-device access to materials + the roster**, explicitly **not** RSVP. (A cookie-only approach is too fragile across laptop + phone — hence login.)
- **Multi-chapter affordance (Decision D):** a volunteer account is **not** constrained to a single chapter — `group_user` is already many-to-many, so the "a pink vest helps in more than one municipality" affordance exists at schema level; keep it. Surfacing multi-chapter membership in the **UI** is a **later call**, not v1-blocking.
- **Foolproof self-management, no HQ QC gate (Decision E):** local partners and local press are **chapter-admin editable without coordination-duo involvement** and **no approval gate** — design must be impossible to get wrong (reinforces `D3` and "Template over approval"; see [`00-design-plan.md`](00-design-plan.md)).
- **Structural boundary (unchanged, Frederik 2026-06-02):** the back-office is a **separate branded frontend surface** (`/backstage/[postal-code]`, read-mostly), **not** the Filament `/admin` panel — rank-and-file volunteers (P4) never touch Filament. Accounts are **invite-only** (leads provision in `/admin`; no public Register). Post-login landing = [My activities](20-structure.md). Locked in [Structure](20-structure.md).
- **Remainder (now small):** spec the three-layer content in detail for Build; surface the **volunteer growth-path** content (typical ways to contribute more, with the harder how-to — commune contact, route planning — explained there). Volunteer-enquiry follow-up/ownership is split out as [`D-12`](#d-12--volunteer-enquiry-follow-up--ownership--open-flagged-2026-06-05-alexandrej3).

### `D-3` — Grande Kidical Mass as a featured event *(was strategy `S-5`)*
- **Remainder:** migration normalises the annual flagship into Events as a *featured* event (no hand-built yearly page). Not explicitly confirmed with Leticia.
- **Operational coordination is OUT of v1 (Alexandre/J3, 2026-06-05 — Decision G):** Alexandre's biggest stress point is the cross-chapter coordination of the Grande KM, but that is an **org process across all chapters together, not a website job.** The site **features** the annual event; it does **not** orchestrate its organisation. Keep cross-chapter coordination out of v1 unless a specific, concrete website hook emerges.
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
- **Decided & locked:** meetups (`meeting`/`workshop`/`other`) are **fully public** — visible to non-logged-in visitors, **all details** (incl. meeting point), **all groups (cross-group)** — as a traction/recruitment signal showing the movement's momentum. **Login gates the back-office + volunteer roster, not viewing.** This **revises `D-8`** (which previously login-gated meetups) and is treated as **locked**: it diverges from Leticia's earlier "strong local communities" stance, accepted as the decided direction with no further client gate.
- **Where they surface (settled):** **chapter pages only** — each chapter page lists its own meetups/workshops publicly. **No national movement/aggregation view**, and **not** on the family ride calendar (`/events` stays rides-only, J1-focused). A logged-in volunteer's cross-group view lives in [My activities](30-skeleton/my-activities.md), not a public surface.
- **Decided:** the `Activity` viewing rule (no view-gate) + what login gates (back-office + volunteer roster — attendance cut, see `D-1`).
- **Remaining detail (not a standing concern):** the logged-in [My activities](30-skeleton/my-activities.md) default-municipality filter is a skeleton-level "to test", documented there.

### `D-4` — Surface plane / design tokens — **Closed** (2026-06-02)
- **Resolved:** the original premise ("no machine-readable tokens exist") was outdated — tokens are **live in [`resources/css/app.css`](../../../resources/css/app.css) `@theme`** (Tailwind v4), set in the merged `design/activity-page` redesign. **Source-of-truth decision:** `app.css` is canonical; **[`DESIGN.md`](../../../DESIGN.md)** (repo root) is the human-readable documentation — palette, typography (Poppins / Nunito Sans / Fredoka One), semantic tokens, and the signature design language (−3° tilts, red icon chips, full-bleed colour blocks). Direction/rationale stay in [`50-surface.md`](50-surface.md).
- **Palette status:** approved (the as-built redesign is the intended brand system), per Frederik 2026-06-02.
- **Not a blocker:** spacing/radius aren't formally tokenised (Tailwind defaults + ad-hoc component values); lift into `@theme` later if reuse grows — noted in `DESIGN.md`.

---

## Notes

- `D-5` (patterns not extracted) and `D-6` (page registry missing) — **resolved** in this pass: [`40-patterns.md`](40-patterns.md) and [`30-skeleton/00-page-registry.md`](30-skeleton/00-page-registry.md) now exist (first pass). Not tracked as standing concerns.
- Content-level notes (e.g. FR hero "kets" neutrality, date/month format) live in the relevant skeleton/content files, not here.

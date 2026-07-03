---
title: Design — concerns register
tags: [design, concerns]
sources: [wiki/strategy/01-concerns, wiki/design]
phase: design
updated: 2026-07-03
---

# Design — concerns register

Open design-plane decisions. Stable IDs (`D-n`) never change. States: **Open** · **Partly** · **Closed**. Several graduated from the Strategy register when the Design phase opened — their lineage is noted.

## At a glance

| State | Count | IDs |
|---|---|---|
| Open | 2 | `D-12`, `D-13` |
| Partly | 4 | `D-1`, `D-7`, `D-10`, `D-11` |
| Closed | 4 | `D-2`, `D-3`, `D-4`, `D-9` |

**Conclusion gate:** **two fully-Open design concerns: `D-12` (volunteer-enquiry follow-up / ownership — new, Alexandre/J3 2026-06-05) and `D-13` (About stats are hardcoded — 2026-06-08).** `D-2` (meetups public, chapter pages only), `D-3` (Grande KM = featured event — confirmed Frederik 2026-07-03, no further client gate), `D-4` (tokens live + documented) and `D-9` (one-off support path — cut, Frederik 2026-07-03) are Closed; `D-7` (redirect map) is drafted with build fill-ins named; `D-1` (**evidence gate closed by Alexandre/J3 2026-06-05** — back-office content brief now concrete, per-event attendance cut, replaced by the volunteer roster), `D-10` (head baseline shipped 2026-07-03; designed og-default card + hreflang + JSON-LD + sitemap remain) and `D-11` (Partners data layer live since 2026-06-25; content + Press remainders named) carry decided directions with named remainders.

---

## Open

### `D-13` — About stats are hardcoded — **Open** (flagged 2026-06-08)
- **Problem:** the three impact stats on `/about` (gemeenten, fietsparades, vrijwilligers) are static strings in the Blade template ([`about/index.blade.php`](../../../resources/views/about/index.blade.php), line ~112). They were updated to 20 / 200+ / 300+ on 2026-06-08 but will drift as the network grows.
- **Desired end state:** gemeenten = `Group::active()->count()` (or a scope), fietsparades = `Activity::past()->count()`, vrijwilligers = a configurable/CMS value (no reliable DB source yet — volunteers aren't modelled as a count).
- **Safe to:** leave static for now; update manually when the numbers shift.
- **Next step:** wire gemeenten + fietsparades to real counts once the data model stabilises (Build phase); decide how to source the vrijwilligers figure (config key, or a new `Volunteer` count model).

### `D-12` — Volunteer-enquiry follow-up / ownership — **Open** (flagged 2026-06-05, Alexandre/J3)
- **Problem:** the docs already route a per-chapter contact form to the local lead and let leads respond in-platform, but the **follow-up / ownership / tracking mechanism is unresolved.** When a routed enquiry arrives, who owns it — one responsible mailbox per chapter, or all leads with a **status/tracking** system so nothing falls through? This is the original `bike@` "email black hole" reframed at chapter level: routing to the right place does not by itself guarantee anyone answers.
- **Safe to:** ship the routed chapter form now (the routing is decided — J2); this concern is about what happens *after* submission.
- **Next step:** decide ownership model (single chapter mailbox vs. all-leads-with-status-tracking) with the coordination duo; size for Build. Tied to the `Volunteer enquiry` entity ([content model](20-structure.md)).

---

## Partly

### `D-10` — Page metadata, SEO & social share previews — **Partly** (flagged 2026-06-03; head baseline shipped 2026-07-03)
- **Done (2026-07-03):** the public `<head>` rebuilt as [`partials/site-head.blade.php`](../../../resources/views/partials/site-head.blade.php): title pattern (`{Page} · Kidical Mass België`), per-page NL descriptions ([`lang/nl/meta.php`](../../../lang/nl/meta.php)), canonical URL, OG/Twitter baseline, two-tier OG images (branded default + activity/article hero `og` conversion, 1200×630 jpg), favicons, theme-color, webmanifest.
- **Still open:** the designed `og-default.jpg` card (Frederik), `hreflang` alternates once the `/nl`·`/fr` middleware lands (couples to [`D-7`](#d-7--redirect-map-launch--drafted-2026-06-02)), JSON-LD, and the sitemap.
- **Safe to:** call the head baseline shipped; do **not** call SEO fully closed until the remainder lands.
- **Next step:** Frederik designs the og-default card; JSON-LD, sitemap and hreflang land with the wider audit follow-ups.

### `D-11` — About credibility leaves lack real data *(Partners + Press)* — **Partly** (flagged 2026-06-03; partner data layer live 2026-06-25)
- **Done (2026-06-25):** the partner **data layer is live** — real `partners` records (19, NL/FR), Spatie-media logos, admin resource with `show_logo`/`visible` toggles; the site-wide `partner-strip` ([PAT-5](40-patterns.md)) **binds to the model** and renders ~7 cleared national logos (Brussel Mobiliteit sits in the footer funder line by design, excluded from the strip).
- **Still open — Partners:** ~6 partners lack cleared logos (admin content entry); no category field (institutional / movement-ally / in-kind); `/about/partners` (P-20) is still a static `Route::view`, not bound to the model. **Acquisition flow:** the Sponsorformules + Partnercharter PDFs exist (a *KM Brussels* tier ladder €100–2.500/yr + a charter) and become an on-page summary + downloadable docs + routed form ([`partners.md` § conversion flow](30-skeleton/partners.md#become-a-partner-conversion-flow--plan-2026-06-03)). **Before the prices go live:** Leticia confirms the tiers apply *nationally*, and the PDFs are re-hosted off Wix (ideally the confirmed national versions).
- **Still open — Press:** there is **no Press model/table at all**; needs a `Press` model (`outlet, headline, url, date, language, media_type, is_featured, is_archived, chapter_id`) + a curated, **verified** item list (priority: RTBF, BX1 video), incl. the 2020–2021 dead-link "archived" rule. Until then the page ships **contact-forward** (hero + "journalisten, we praten graag" + `bike@kidicalmass.be`) with an honest empty state — no fabricated coverage.
- **Couples to:** the all-Brussels partner list (national-scope pass with Leticia) and the `bike@kidicalmass.be` vs dedicated `pers@`/`partners@` question — both in [`about-journey.md` § Open questions](30-skeleton/about-journey.md).
- **Safe to:** ship the pages now; do **not** call them Back/OK until the data lands.
- **Next step:** confirm the national partner + press lists with the coordination duo; bind `/about/partners` to the model; size the Press model for Build.

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
- **Structural boundary (unchanged, Frederik 2026-06-02):** the back-office is a **separate branded frontend surface** (`/backstage/[postal-code]`, read-mostly), **not** the `/admin` panel (BlueAdmin) — rank-and-file volunteers (P4) never touch `/admin`. Accounts are **invite-only** (leads provision in `/admin`; no public Register). Post-login landing = [My activities](20-structure.md). Locked in [Structure](20-structure.md).
- **Remainder (now small):** spec the three-layer content in detail for Build; surface the **volunteer growth-path** content (typical ways to contribute more, with the harder how-to — commune contact, route planning — explained there). Volunteer-enquiry follow-up/ownership is split out as [`D-12`](#d-12--volunteer-enquiry-follow-up--ownership--open-flagged-2026-06-05-alexandrej3).

### `D-7` — Redirect map *(launch)* — **drafted** (2026-06-02)
- **Resolved:** old Wix URLs → new routes documented in [`26-redirect-map.md`](26-redirect-map.md); all `301`. **Language rule decided:** redirects target neutral paths, a locale middleware resolves `/nl/`|`/fr/` (Accept-Language → cookie → geo, fallback NL).
- **Remainder (build, not design):** locale middleware + 301 config; fill the three Grande KM event `{slug}`s at seed; confirm the two combined-postal canonicals; preserve `/post/{slug}` slugs through blog migration; post-launch crawl of the critical set. Hand-off list in [`26-redirect-map.md` § Build hand-off](26-redirect-map.md).

---

## Closed

### `D-3` — Grande Kidical Mass as a featured event *(was strategy `S-5`)* — **Closed** (Frederik 2026-07-03)
- **Decided & locked:** migration normalises the annual flagship into Events as a **featured** event — same system, extra prominence, **no hand-built yearly page** (see [Structure](20-structure.md)). Confirmed by Frederik 2026-07-03; no further client gate — the yearly-page pattern is retired.
- **Operational coordination stays OUT of v1 (Alexandre/J3, 2026-06-05 — Decision G):** the cross-chapter coordination of the Grande KM is an **org process, not a website job**. The site **features** the annual event; it does **not** orchestrate its organisation. Revisit only if a specific, concrete website hook emerges.

### `D-9` — One-off support path *(funding)* — **Closed** (Frederik 2026-07-03: no one-off in v1)
- **Decided:** `/steun-ons` stays **monthly-only** (Growfunding); the provisionally-reinstated discreet one-off slot ("liever één keer?") is **cut** and the page ships as built — **no IBAN on-site**. This reverses the 2026-06-02 provisional direction and re-confirms the original v1 "recurring-only" cut.
- **If it ever returns:** mechanism + account details live in Notion (public-repo guardrail); reopening would need Leticia + a pinned mechanism (bank transfer or a one-off Growfunding gift).

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

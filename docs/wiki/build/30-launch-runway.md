---
title: Launch runway — pre-launch initiative board
tags: [build, launch, planning, seo, content, cutover]
sources:
  - ../design/30-skeleton/00-page-registry.md
  - ../design/01-concerns.md
  - ../strategy/01-concerns.md
  - ../design/25-content-migration.md
  - ../design/26-redirect-map.md
  - ../design/demo-journeys.md
  - codebase audit 2026-07-04 (routes, site-head partial, forms, lang/, public/, CI)
phase: build
updated: 2026-07-07
---

# Launch runway — pre-launch initiative board

Snapshot of where the NL rebuild stands (compiled 2026-07-04, updated 2026-07-07) and a weighed board of everything that typically happens between "the design is done" and "the client saw it live on kidicalmass.be". Visual version: [`30-launch-runway.html`](30-launch-runway.html).

**Verdict:** design is ~90% there. What separates the project from launch is not pages — it's **plumbing** (redirects, newsletter backend, hosting), **paperwork** (privacy, sign-offs) and **people** (client content, chapter-lead onboarding).

## State of play

| | Done / approved | In progress | Not started | N.v.t. / shape open |
|---|---|---|---|---|
| **Wire** | 20 | 1 | 0 | 1 |
| **UI** | 17 | 4 | 0 | 1 |
| **Back** | 17 | 4 | 0 | 1 |
| **CMS** (new 07-07) | 2 | 9 | 2 | 9 |
| **Client OK** | 0 | 0 | 21 | 1 |

22 registry pages (P-22 cut 07-04, P-08 cut 07-07 — the roze-hesje hub carries the volunteer layer) · avg content confidence **3.6/5** (recalibrated row-by-row in the 07-07 review session) · client sign-off **0/21** — the reveal is what starts that clock. **The critique queue is empty:** Frederik walked all 21 public rows through `/build/review` on 07-07. Only one page remains Wire-open — Event detail (P-03, redesign asked). The branded login view (P-07) built and live-verified the same day (Wire + Assets 🟢); its UI stays 🟠 pending Frederik's own `/build/review` critique. The new **CMS column** tracks whether CMS-driven content is loaded and correct; its nine 🟠 cells are the client content chase-list.

**Already banked (don't redo):** SEO/social head baseline (D-10 → Partly), branded error pages, 118 test files + CI, mobile pass (20 pages, zero overflow), admin role scoping, real data on chapters/press/stats/quotes/team/partners, **Wix redirect map built** (D-7 — every live-sitemap URL 301s, 07-07), demo runbook, **Privacy page (P-06 — Art-13 copy, config-driven cookie list, form notices, ContactForm retention pruning)**, **Contact page (P-05 — final a3a live, form wired to `ContactForm` + comms inbox)**, **accessibility pass** (skip link, landmarks, focus ring, form error wiring, contrast, reduced-motion, hit areas — 07-05), **performance pass** (photography srcset, partner conversions, CLS dims, cache rules, footerbunch avif — 07-05), **`/build/review` tool + first full review session** (07-07 — all 21 rows recalibrated by Frederik, feedback folded into the registry + this board).

**Open flanks (nothing exists yet):** newsletter forms drop addresses silently, no analytics / error monitoring / uptime, no hosting/DNS/cutover plan anywhere, FR locale unbuilt despite the v1 NL+FR strategy promise. (The critique-queue flank closed 07-07 — review session done; what remains of it is the bounded design-follow-up batch below.)

## The board

Impact ●●● = launch fails or embarrasses without it. Effort: S = a session, M = a thread or two, L = a real project.

### Lane 1 — Launch blockers (can't cut over without)

| Initiative | Why it bites | Impact | Effort | Owner |
|---|---|---|---|---|
| **Build the Wix redirect map** | Built 07-07 ([26-redirect-map.md](../design/26-redirect-map.md)): `routes/redirects.php` + `LegacyRedirectController` 301 every URL in the live sitemap (crawled 07-07; added `/bxltour2026` + `/1330`, missing from the design map). Postal pages resolve zip→chapter at request time; locale resolves per request off `SetLocale::SUPPORTED` (fallback nl); `LegacyRedirectTest` covers the set. Remaining: point the 3 Grande-KM pages + `/post/{slug}` at real detail pages once content migrates (both go to their index now); post-launch crawl (Lane 4). | ●●● | M | ~~Nico~~ done |
| **Newsletter backend** | Subscriber model + double opt-in + glue mailable. Change-computation + mail template already exist and are tested; the join is missing. 4+ live-looking forms drop addresses today. | ●●● | M | Nico |
| **Privacy page (P-06)** | Live: Art-13 copy, config-driven cookie list, form notices, retention pruning (12m/24m, daily `model:prune`). Remaining is client facts, not code — legal entity + processor names with the coördinatieduo, plus the analytics disclosure line once Fathom lands. | ● | S | Coördinatieduo + Frederik |
| **National contact page (P-05)** | Built 07-05 through three pick rounds; final a3a live (topic pills in form + sticky info-card sidebar), form wired to `ContactForm` + comms inbox (tests green, not verified live). Remaining: Frederik's sign-off, inbox ownership (D-12), `bike@` vs `contact@…brussels`. | ● | S | Frederik |
| **Hosting, deploy & cutover plan** | The wiki's one blank page: prod env, DNS, queue worker (opt-in mails **and media conversions** — without one, conversion jobs queue forever and pages silently fall back to raw originals, seen locally 2026-07-05), `media-library:regenerate`, SPF/DKIM, backups, and the open parallel-run-vs-hard-switch question from [25-content-migration.md](../design/25-content-migration.md). Web server must also: gzip/brotli text responses **including PHP-served JS** (`/flux/flux.min.js` 314→~90 KB, `/livewire-*/livewire.js`) and send `Cache-Control` on `/img` statics (~30 days; not fingerprinted) — Apache rules already live in `public/.htaccess`, nginx needs the equivalent. | ●●● | M | Nico + Frederik |

### Lane 2 — Make the reveal land (siblings of the SEO/social pass)

| Initiative | Why it bites | Impact | Effort | Owner |
|---|---|---|---|---|
| **The critique batch** | Done 07-07: `/build/review` shipped and Frederik walked all 21 rows in one sitting. Follow-ups became the four rows below. | ●●● | M | ~~Frederik~~ done |
| **Event detail redesign (P-03)** | The one page the review sent back to the drawing board (UX/Wire/UI → 🟠). Existing three-state lifecycle + share-band stay as the base; needs a fresh design pass (brief → alternatives → build), not a tweak. Handoff brief ready: [`activity-detail-redesign.md`](../design/30-skeleton/activity-detail-redesign.md). | ●● | M | Frederik |
| **About polish batch** | Built 07-07 in one thread: hub lightened (variant "geen dozen" picked from 3 live prototypes — exits as link row, browse path as chip list), organisation band now one white panel with a hairline seam, news tweaks live (chips overlay the photo, title "Nieuws uit de beweging", intro in the hero). Remaining: Frederik's `/build/review` re-check flips P-17/P-18 UI → 🟢 and confirms P-14. | ●● | S–M | ~~Frederik~~ built, re-review |
| **Branded login view (P-07)** | Built 07-07: geel-veld auth shell (photo collage, Set 1, three photos, `<x-photo-collage>`) + NL copy across login/forgot/reset/confirm/two-factor/verify, live-verified for all roles. Wire + Assets 🟢; UI 🟠 awaiting Frederik's `/build/review` critique. Handoff brief: [`login-view.md`](../design/30-skeleton/login-view.md). | ● | S | ~~Frederik~~ built, re-review |
| **Roze-hub surface pass (P-09)** | Wire approved 07-07; the hub still needs its KM-style surface pass. Real features wait on [#37](https://github.com/ndeblauw/kidicalmass/issues/37), styling doesn't. | ● | S–M | Frederik |
| **SEO tail (D-10)** | Designed `og-default` share card (check the file even exists), `sitemap.xml` + robots reference, JSON-LD events. hreflang parks with FR. | ●● | S | Frederik + Nico |
| **Analytics, cookieless** | Plausible/Fathom: no consent banner, simple privacy page, launch-week numbers for the client. | ●● | S | Nico |
| **Accessibility pass** | Done 07-05: audit + two fix passes site-wide (skip link, labelled landmarks, focus-visible, form error wiring, contrast token, heading order, reduced-motion, ≥44px hit areas), tested. Remaining: low tier + two decisions (location-picker focus model). | ● | S | Frederik |
| **Performance pass** | Largely done 2026-07-05: photography re-encoded (q70) with full `-768` srcset coverage, jpg strays → webp, partner-logo conversions repaired (png keeps transparency), `x-photo` emits width/height against CLS, `.htaccess` cache/compression rules. Footerbunch went avif the same day (parallel thread). Remaining: **YouTube autoplay hero** (~2 MB third-party load, the single heaviest item — pairs with the hero-MP4 content ask) and a Lighthouse round on a prod build (`npm run build`, no `public/hot`). | ●● | S | Frederik |
| **Steun stats check (P-04)** | Steun surface port landed (UI 🟢 07-07). Remaining `[research]`: verify the proof-deck stats really flow from `SupportStats`/`year_stats` and that Leticia can edit them in the admin — pairs with the admin-handoff screencasts. | ● | S | Frederik |

### Lane 3 — People & content (the human runway; starts earliest, finishes last)

| Initiative | Why it bites | Impact | Effort | Owner |
|---|---|---|---|---|
| **Leticia decision batch** | One session clears six blockers: S-1/S-2 sign-off, trekker consent [BLOKKEREND], one-off support + IBAN, national partner pass, D-12 enquiry ownership, press dates. Highest impact-per-hour on the board. | ●●● | S | Leticia + Frederik |
| **Content confidence push** | Avg now 3.6/5 (recalibrated 07-07). The chase list is no longer scattered: the registry's new **CMS column** holds it — nine 🟠 cells (partner logos, quote translations, duo photos + bios, news check + covers, press dates, steun stats 2026, per-event photos/Komoot) plus the hero MP4 under Assets. Message to the team: imported content (news, press) migrates automatically — they only check/complete what's 🟠. Turn the column into one client checklist for the Leticia session. | ●●● | M–L | Client + Frederik |
| **Chapter-lead onboarding** | Migration plan's own "biggest transition risk": ~13 Brussels chapter pages launch near-empty. Authoring guide + session + a decided stop-editing-Wix moment. | ●●● | M | Frederik + leads |
| **Admin handoff for the duo** | News/press/partners/quotes/team/stats are admin-editable; nobody but the builders knows. Screencasts > manual; doubles as reveal material. | ●● | S–M | Frederik |
| **Ops safety net** | Error monitoring (Flare fits the stack) + uptime ping + verified backup before real users arrive. | ●● | S | Nico |
| **Reveal runbook** | Adapt [demo-journeys.md](../design/demo-journeys.md) into a client-reveal script: real vs faux said up front, feedback captured into the OK column — the `/build/review` mode in build gives that capture a surface (stage bumps + review-inbox punch list). | ●● | S | Frederik |

### Lane 4 — After the reveal (named now to protect the launch from scope creep)

| Initiative | Why it bites | Impact | Effort | Owner |
|---|---|---|---|---|
| **FR locale: decide, then build** | Strategy promises NL+FR routed in v1; middleware, `lang/fr`, per-page copy, hreflang all unbuilt. Launch-blocking or announced fast-follow — decided, not drifted. Unblocks hreflang (legacy redirects already resolve locale off `SetLocale::SUPPORTED`). | ●●● | L | Frederik + Nico |
| **#37 backend bundle** | Per-chapter enquiry routing (waits on D-12), roze-hub real features, per-group intro/cover fields. Volunteer-facing, can trail public launch. | ●● | L | Nico |
| **Post-launch SEO ops** | Search Console + sitemap submit, crawl the redirect-critical set (301→200), watch the 404 log a month, media regenerate on prod. | ●● | S | Nico |
| **Volunteer growth path (My activities dropped)** | P-08 cut 2026-07-07 — the roze-hesje hub (P-09) carries the volunteer's logged-in surface. Remainder: D-1 material library depth + any cross-group view for multi-chapter volunteers. | ● | M–L | Both |

## If only five things happen this month

1. **Book the Leticia session** — six sign-offs in one afternoon; everything content-shaped queues behind it.
2. **Newsletter backend** — the Nico build that turns stage props into a real site (redirect routes landed 07-07).
3. **Write the cutover plan** — one wiki page: hosting, DNS, queue, mail, backups, Wix parallel-run yes/no.
4. **Clear the review follow-ups** — the 07-07 session shrank "critique" to four bounded design jobs (Event detail redesign, About polish batch, login view, roze-hub surface); each is a single thread.
5. **Make the FR call explicit** — launch-blocking or announced fast-follow, but written down.

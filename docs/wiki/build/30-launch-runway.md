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
| **Wire** | 10 | 10 | 1 | 1 |
| **UI** | 9 | 11 | 1 | 1 |
| **Back** | 8 | 8 | 1 | 5 |
| **Client OK** | 0 | 0 | 21 | 1 |

22 registry pages (P-22 cut 07-04, P-08 cut 07-07 — the roze-hesje hub carries the volunteer layer) · avg content confidence **2.8/5** (client material still the long pole) · client sign-off **0/21** — the reveal is what starts that clock. The "not started" column nearly emptied since 07-04: Contact and Privacy were built and moved into the orange critique queue; only the branded login view remains a stub.

**Already banked (don't redo):** SEO/social head baseline (D-10 → Partly), branded error pages, 118 test files + CI, mobile pass (20 pages, zero overflow), admin role scoping, real data on chapters/press/stats/quotes/team/partners, **Wix redirect map built** (D-7 — every live-sitemap URL 301s, 07-07), demo runbook, **Privacy page (P-06 — Art-13 copy, config-driven cookie list, form notices, ContactForm retention pruning)**, **Contact page (P-05 — final a3a live, form wired to `ContactForm` + comms inbox)**, **accessibility pass** (skip link, landmarks, focus ring, form error wiring, contrast, reduced-motion, hit areas — 07-05), **performance pass** (photography srcset, partner conversions, CLS dims, cache rules, footerbunch avif — 07-05).

**Open flanks (nothing exists yet):** newsletter forms drop addresses silently, no analytics / error monitoring / uptime, the 10-row critique queue all waits on Frederik (About ×7, roze-hub, Contact, Privacy — no code moves it), no hosting/DNS/cutover plan anywhere, FR locale unbuilt despite the v1 NL+FR strategy promise.

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
| **The critique batch** | Now 10 rows: About (P-14–P-20), roze-hub, Contact + Privacy all wait on Frederik's critique to go Wire/UI 🟢 — the largest block of 🟠. Tooling underway: `/build/review` split review mode (specced + planned 07-07, `RegistryWriter` built) walks every page and writes stage bumps + feedback notes into the registry. | ●●● | M | Frederik |
| **SEO tail (D-10)** | Designed `og-default` share card (check the file even exists), `sitemap.xml` + robots reference, JSON-LD events. hreflang parks with FR. | ●● | S | Frederik + Nico |
| **Analytics, cookieless** | Plausible/Fathom: no consent banner, simple privacy page, launch-week numbers for the client. | ●● | S | Nico |
| **Accessibility pass** | Done 07-05: audit + two fix passes site-wide (skip link, labelled landmarks, focus-visible, form error wiring, contrast token, heading order, reduced-motion, ≥44px hit areas), tested. Remaining: low tier + two decisions (location-picker focus model). | ● | S | Frederik |
| **Performance pass** | Largely done 2026-07-05: photography re-encoded (q70) with full `-768` srcset coverage, jpg strays → webp, partner-logo conversions repaired (png keeps transparency), `x-photo` emits width/height against CLS, `.htaccess` cache/compression rules. Footerbunch went avif the same day (parallel thread). Remaining: **YouTube autoplay hero** (~2 MB third-party load, the single heaviest item — pairs with the hero-MP4 content ask) and a Lighthouse round on a prod build (`npm run build`, no `public/hot`). | ●● | S | Frederik |
| **Board strays** | Steun surface port (UI 🟠) + branded login view (P-07, backend done). | ● | S | Frederik |

### Lane 3 — People & content (the human runway; starts earliest, finishes last)

| Initiative | Why it bites | Impact | Effort | Owner |
|---|---|---|---|---|
| **Leticia decision batch** | One session clears six blockers: S-1/S-2 sign-off, trekker consent [BLOKKEREND], one-off support + IBAN, national partner pass, D-12 enquiry ownership, press dates. Highest impact-per-hour on the board. | ●●● | S | Leticia + Frederik |
| **Content confidence push** | Avg 2.3/5: duo photos + bios, ~6 partner logos, per-event photos/Komoot, hero MP4, quote translations. Chase list exists per page in the registry Top gaps — turn it into one client checklist. | ●●● | M–L | Client + Frederik |
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
4. **Run the critique batch** — 10 orange Wire rows (About, roze-hub, Contact, Privacy) all wait on one person; `/build/review` makes it a single sitting.
5. **Make the FR call explicit** — launch-blocking or announced fast-follow, but written down.

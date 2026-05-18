# Wiki Log

## [2026-05-18] ux-planning | Internal meetups / activity types folded into scope

Gap found: the interview revealed Leticia's internal meetups + that Nico already built an `Activity` model (types `kidicalmass`/`meeting`/`workshop`/`other`, bilingual NL/FR, group-linked) with a separate `Article` model and a `group_user` pivot — none of which was in ux-planning (Events only ever covered public rides). Verified on `origin/main`; no visibility field exists yet. Applied 3 user-confirmed decisions: (1) non-ride activities visible to any logged-in account across all groups; (2) one Events system gated by type (public rides + account-visible meetups, chapter page + a "My activities" account view); (3) keep "group-volunteer account" distinct from "spacefunding paying member." Updated Scope (Core, new Activities + Accounts functional blocks, Content Requirements), Structure (sitemap + Key Structural Decisions), and added a build implication for Nico (needs a visibility rule) + a "validate with Leticia" open question (cross-group visibility is broader than her local-communities stance).

## [2026-05-18] ux-planning | Strategy & scope updated from Leticia interview

Applied four user-confirmed decisions + several confirmations from the interview. Strategy: added "bring money in" as the top organisational objective (recurring membership/spacefunding, site-wide); demoted "potential chapter leads" from primary to secondary audience and reframed growth toward participation in existing chapters; resolved the Facebook-vs-site open question (site is canonical, FB stays for reach/turnout); added a light-and-broad positioning guardrail; logged the private organiser back-office + "who's coming" as an explicit OPEN question (deferred to post-interview). Scope: v1 is bilingual NL+FR (English deferred); added a Membership/spacefunding page + global footer CTA; confirmed cuts (web store, photo-gallery system, poster/flyer generator, worked-out chapter-start flow); low-frequency per-chapter subscriptions; clearer volunteer onboarding. Structure: sitemap + language routing updated, `/membership` node added, Key Structural Decisions revised, Decision Flags reconciled (4 of 5 resolved; Grande Kidical still open).

## [2026-05-18] ingest | Leticia interview — structured transcript

Ran the Leticia site-strategy interview (2026-05-18). Synthesised the raw auto-transcription into a structured, theme-organised document at `docs/raw/interview-leticia-2026-05-18.md` (governance, scale, funding, audiences, events workflow, subscriptions, scope in/out, the contested organiser back-office, ecosystem/politics, next interviews, look & feel). Key new signal: funding/membership is a top org goal absent from Strategy; "potential chapter lead" as a primary audience is overstated (challenge is participants, not groups); Facebook stays as a turnout signal; trilingual → bilingual NL/FR for v1. Evaluation against `ux-planning.md` (strategy + scope) pending user confirmations.

## [2026-05-18] ux-planning | Content Migration Plan — page-by-page table + meeting visual

Completed the previously-stubbed Content Migration Plan in `ux-planning.md`. Added the full page-by-page mapping (every current Wix page → action per the Rewrite/Migrate/Merge/Absorb/Drop/Seed legend → new home), grouped into National & content, Events, Chapters, and News/Press/Store, sourced from `site-audit.md` and the new sitemap. Added a "Decision Flags for Leticia" subsection surfacing the five non-technical choices the new structure makes (web store, photo gallery, "no bike" demotion, Grande Kidical normalisation, Liège external). Produced a derived standalone meeting artifact at `docs/site-structure-comparison.html` — a self-contained side-by-side visual (current vs new sitemap, colour-coded actions, decision flags) for the site-strategy review with Leticia; kept separate from the wiki internals. Removed the redundant `docs/raw/current-live-site-kidicalmass-be.md` (superseded by the existing `docs/raw/website/` scrape + `site-audit.md`).

## [2026-04-13] image-map | Photography expanded — Downloads batch

Added 23 new photos from `~/Downloads/kidical-photos/`. Identified 4 duplicates: upgraded `ride-brussels-boulevard-crowd.jpg` (787→1960px wide) and `volunteer-pink-vest.jpg` (740→1200px tall) with higher-res originals; skipped `cf0153_6f0e0e6d` and `cf0153_0a3acc1b` as duplicates of `kidical-mass-16` and `kidical-mass-13`. Photography collection now 36 photos. Highlights: three 8688×5792 professional shots, Cinquantenaire crowd at 5472×3648, DJ-on-boombox-bike. Homepage and About hero slots upgraded from ⚠️ to ✅. Updated `image-map.md` and `image-map-new-site.md`.

## [2026-04-13] image-map | Asset catalogue + new site slot map

Visually assessed all 143 files in `docs/raw/website/assets/`. Applied exclusion criteria from spec (size, screenshots, iStock, social story formats, Wix UI elements, QR codes, third-party event assets). Copied 50 usable assets to `docs/raw/assets/` with clean kebab-case names: 11 illustrations, 13 photos, 11 event posters, 12 chapter posters, 3 logos. Wrote `image-map.md` (asset catalogue) and `image-map-new-site.md` (per-page slot map with gap priority list).

## [2026-04-13] ingest | Notion project — initial migration

Migrated 7 pages from the Notion project "Kidical Mass Belgium — Website Project" verbatim:
desk-research, service-design, key-decisions, tone-of-voice-notion, strategy-plan, site-audit, ux-planning (with 14 nested child pages).
Excluded: Meeting Notes (top-level template only), Look & feel examples.

## [2026-04-13] ingest | Existing codebase docs — initial migration

Migrated docs/ux/ (strategy, scope, structure, activity-detail sub-pages) and docs/tone-of-voice.md into the wiki verbatim.

## [2026-04-13] ux-planning | Full page-level UX specs — all remaining pages

Completed thorough UX planning for all remaining pages, matching the depth of the Activity Detail spec. Each page now has: deepened Strategy (psychological depth, user mental states), confirmed Scope, confirmed Structure, ASCII wireframes (desktop + mobile with annotations), and an Open Questions / Necessary Refinements section.

Pages completed:
- **Home** — hero structure, events strip, chapter map, stats, volunteer CTA, news, partners bar
- **Events Overview** — filter bar, date-grouped list (upcoming + past modes), Grande KM badge
- **Getting Started** — FAQ, "don't have a bike" section (added My Kids Bikes + Cyclo note from raw site), 3 structured "other activities"
- **Help Out** — 5 role cards, "what joining looks like" honest commitment section (from raw volunteer page), routed contact form, start-a-chapter CTA
- **Chapters Overview** — map + list, Brussels clustering, Flanders hidden, start CTA
- **Chapter Page Template** — events auto-populated, team + volunteer form merged, optional sections hidden
- **About / Mission** — NEW: full 5-plane spec from scratch. Strategy, scope, structure, wireframes
- **About Overview** — deepened strategy, 6 sub-section cards, stat bar, CTA
- **About / Organisation** — organigram, 3 levels, coordination duo named, safety/routes, open questions resolved
- **About / Vision** — 4 policy demands, Child Friendly City coalition, parent quotes, open questions resolved
- **About / News** — article feed, article detail page wireframe, open questions
- **About / Press** — featured items, full list, auto-aggregation, dead link strategy
- **About / Partners** — 3 categories, 6 confirmed partners, "become a partner" CTA

## [2026-04-13] content | Activity Detail — Content template

Created `ux/activity-detail-content.md`: full copy template for the `/events/[slug]` page. Covers all 8 sections (hero, practical strip, what to expect, chapter context, team + volunteer ask, partners, photo permission, meta). EN/FR/NL for all static copy; database-driven fields marked as variables. Includes default "what to expect" body, two event-specific theme examples (Spooky Edition, Safety First campaign), and final photo permission copy.

## [2026-04-13] write | UX content pages — all 7 sections
All 7 UX wireframe pages now have companion content pages with full EN copy and FR/NL inline notes.

## [2026-04-13] restructure | UX wiki reorganisation

Restructured UX content into cleaner buckets. ux-planning.md now holds site-level content (principles, strategy, scope, sitemap, migration plan). Extracted all 14 page specs from the raw Notion dump into 7 files in ux/: activity-detail.md, events-overview.md, home.md, help-out.md, getting-started.md, chapters.md (overview + template), about.md (all about/* pages). Removed old plane files (_strategy, _scope, _structure, _skeleton) and the activity-detail subfolder.

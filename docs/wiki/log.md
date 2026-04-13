# Wiki Log

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

## [2026-04-13] restructure | UX wiki reorganisation

Restructured UX content into cleaner buckets. ux-planning.md now holds site-level content (principles, strategy, scope, sitemap, migration plan). Extracted all 14 page specs from the raw Notion dump into 7 files in ux/: activity-detail.md, events-overview.md, home.md, help-out.md, getting-started.md, chapters.md (overview + template), about.md (all about/* pages). Removed old plane files (_strategy, _scope, _structure, _skeleton) and the activity-detail subfolder.

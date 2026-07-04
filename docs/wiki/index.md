# Wiki Index

Catalogue of every page. The wiki is being migrated to the **Cascade** structure (phases: Discovery → Strategy → Design → Build; see [CLAUDE.md](../../CLAUDE.md) → Documentation). Strategy is fully re-shelved; Design/Discovery content still lives in flat files pending a later pass.

## Cross-cutting

| Page | Summary | Phase |
|------|---------|-------|
| [Glossary](glossary.md) | Shared NL/FR vocabulary + don't-use terms | cross-cutting |
| [Tone of Voice](tone-of-voice.md) | 4 voice qualities, context table, bilingual dimension, reference phrases (the *voice* gate) | cross-cutting |
| [Log](log.md) | Append-only change timeline | cross-cutting |

## Discovery — kept in Notion

| Page | Summary | Phase |
|------|---------|-------|
| [Discovery plan & status](discovery/00-discovery-plan.md) | Pointer: discovery synthesis (desk research, service design, interviews) lives in Notion, deliberately out of git | discovery |
| [Site Audit](site-audit.md) | Deep audit of current Wix site — page-by-page issues, 7 cross-cutting problems (the one discovery doc kept in-repo) | discovery |

## Strategy

| Page | Summary | Phase |
|------|---------|-------|
| [Strategy Brief](strategy/00-strategy-brief.md) | The locked decisions (D1–D9) everything downstream rests on | strategy |
| [Concerns register](strategy/01-concerns.md) | Open/Partly/Closed strategy concerns with stable IDs (the keystone) | strategy |
| [Organisation Goals](strategy/10-organisation-goals.md) | Ranked org objectives; money-in is #1 | strategy |
| [Personas](strategy/20-personas.md) | 5 actors + anti-overlap contract; primary = families & volunteers | strategy |
| [Jobs-to-be-done](strategy/30-jobs-to-be-done.md) | Functional/emotional/social jobs per persona | strategy |
| [Value Proposition](strategy/40-value-proposition.md) | Overall + per-persona promises (drafts pending S-1/S-2) | strategy |
| [User Journeys](strategy/50-user-journeys.md) | J1 find a ride · J2 become a volunteer · J3 chapter lead publishes | strategy |
| [Key Decisions — evidence](strategy/90-key-decisions-evidence.md) | Proxy-interview evidence behind the brief (the brief is canonical) | strategy |

## Design (Garrett's planes)

| Page | Summary | Phase |
|------|---------|-------|
| [Design plan & status](design/00-design-plan.md) | Plane status + project UX principles | design |
| [Concerns register](design/01-concerns.md) | Open design decisions (D-1…D-12); incl. items graduated from Strategy | design |
| [Scope (plane 2)](design/10-scope.md) | MVP in / won't-have, functional specs, content requirements | design |
| [Structure (plane 3)](design/20-structure.md) | Navigation, sitemap, content-model table | design |
| [Journey Palette](design/journey-palette.md) | Shared colour language (4 persona/journey colours + tokens) reused by the sitemaps and A5 cards | design |
| [Demo journeys — arc & gaps](design/demo-journeys.md) | 3 demo journeys (J1 rider · J2 roze-hesje · J3 captain) as a tick-off arc; only open gaps stand out + one build-checklist. Print view: [`.html`](design/demo-journeys.html) | design |
| [Demo runbook](design/demo-runbook.md) | One-pager: who demos what (Frederik frontstage / Nico backstage) + where to frame "we bouwen dit nog" | design |
| [Sitemap — AS-IS](design/21-sitemap-as-is.md) | Visual sitemap of the current Wix site; 4 journeys drawn in signature colour, each breaking — client presentation | design |
| [Sitemap — TO-BE · Public](design/22-sitemap-to-be-public.md) | Visual sitemap of the new public site; nav-ordered, family + volunteer routes in signature colour — client presentation | design |
| [Sitemap — TO-BE · Private](design/23-sitemap-to-be-private.md) | Visual sitemap of the login-gated zone (logged-in volunteers + organiser back-office) — client presentation | design |
| [Content Migration Plan](design/25-content-migration.md) | Every Wix page → its new home (Rewrite/Migrate/Merge/Absorb/Drop/Seed) | design |
| [Redirect Map](design/26-redirect-map.md) | Old Wix URLs → new routes (301); locale-middleware language rule; launch-critical (closes D-7) | design |
| [Skeleton — page registry](design/30-skeleton/00-page-registry.md) | Every route + spec/content/build/lifecycle status | design |
| [Skeleton — per-page briefs](design/30-skeleton/) | Page specs + content companions (home, events, activity-detail, chapters, getting-started, help-out, about) + global-component briefs (steun-ons, partners) | design |
| [Patterns Library](design/40-patterns.md) | Shared UI patterns with stable IDs (PAT-1…PAT-17) | design |
| [Surface (plane 5)](design/50-surface.md) | Visual direction & rationale; tokens live in `app.css` `@theme`, documented in [`DESIGN.md`](../../DESIGN.md) (D-4 closed) | design |
| [Asset map — catalogue](design/60-asset-map.md) | 50 usable assets in `docs/raw/assets/` | design |
| [Asset map — per-page slots](design/61-asset-slots.md) | Slot map: have / candidate / missing | design |

## Build — not opened (YAGNI)

| Page | Summary | Phase |
|------|---------|-------|
| [Build plan & status](build/00-build-plan.md) | What's already on `main`; what the phase will need (DESIGN.md, redirect map) | build |
| [About-section handoff](build/20-about-section-handoff.md) | Open work after the 2026-07-04 normalize pass: admin track, distill/polish leftovers, Nico backlog, client gates, thread-sized bundles | build |

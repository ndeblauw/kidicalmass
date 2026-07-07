---
title: Handoff — Event detail redesign (P-03)
tags: [design, skeleton, build, handoff]
sources: [activity-detail.md, ux/activity-detail-content.md, 00-page-registry.md, ../../log.md]
phase: design
updated: 2026-07-07
---

# Handoff — Event detail redesign (P-03)

**Start a fresh thread with this brief.** In the 2026-07-07 `/build/review` session Frederik sent Event detail back to the drawing board — the only page that went *down*: UX/Wire/UI 🟢 → 🟠. His note, verbatim:

> "Oké, deze pagina moet ik nog ontwerpen. Dat is nog niet goed gedaan. Dat is de enige pagina die nog openstaat."

Read that as: the current page is *built*, not *designed*. It needs a real design pass (brainstorm → 2 directions → pick → build), not a tweak round.

## What exists (keep as the base)

- **View:** `resources/views/activities/show.blade.php` (routed `events/{activity}`, name `activities.show`). A `show-basic.blade.php` variant also exists (older/basic — check before touching).
- **Three-state lifecycle** (upcoming / just-past / recap) is date-driven off `begin_date` and real — Back 🟢, keep it. The states are the page's core idea; the redesign should make them *feel* different, not remove them.
- **Shared components in use:** `<x-share-band>`, agenda date-tile, `<x-support-callout>`, `<x-photo>`. Page CSS: `resources/css/pages/activity.css`; ride components in `resources/css/components/` (`ride-*.css`, `other-activity.css`).
- **Content template:** `docs/wiki/design/30-skeleton/activity-detail.md` (original brief) + the full copy template in the UX content pages (all 8 sections: hero, practical strip, what to expect, chapter context, team + volunteer ask, partners, photo permission, meta).
- **Cut, stays cut:** per-event "I'm coming" (D-1).

## Current pipeline row

`P-03 · UX 🟠 · Conf 4 · Wire 🟠 · Assets 🟢 · UI 🟠 · Back 🟢 · CMS 🟠 · OK 🔴`
CMS 🟠 = per-event verrijking (recap-foto's, Komoot-route, per-event partners PAT-5) is a *content* gap, not part of this redesign — design the slots, don't wait for the content.

## Constraints & taste

- Frederik's standing taste: **no boxy/dense layouts; hierarchy, breathing room; the day as the unit; no duplicated content.** Offer **two genuinely distinct directions** before building (his standing preference).
- Reference quality bar: P-02 calendar and P-11 chapter page v4 (both Frederik-approved) — the detail page should feel like their sibling.
- Styling architecture: tokens in `@theme`/`@layer base`, component appearance in the component blade, page CSS only in `resources/css/pages/activity.css` (never `app.css`). Raw `<h1>`–`<h6>`, `<time datetime>`, `<dl>` for practical metadata.
- Copy: NL, tone-of-voice guide (`docs/tone-of-voice.md`), event pages = warm and concrete. No em-dashes.
- Tests: `tests/Feature/BasicActivityPageTest.php` + activity tests exist — assert behaviour/`data-*` seams per `docs/testing-conventions.md`; a visual redesign must not need test rewrites unless seams change.

## Suggested flow

1. Brainstorm (superpowers): what does each lifecycle state need to *do*? (upcoming = convince & prepare; just-past = thanks & photos; recap = story & archive)
2. Two direction prototypes on the real view (behind nothing — this page is unlinked-safe to iterate on locally); Frederik picks.
3. Build the winner; bump Wire/UI via `/pipeline` or `/build/review` — **Wire/UI 🟢 only after Frederik's own critique pass.**

## Done when

- Frederik reviews the live page in `/build/review` (P-03) and sets UX/Wire/UI 🟢 himself.
- Registry row + runway "Event detail redesign" row updated; log entry appended.

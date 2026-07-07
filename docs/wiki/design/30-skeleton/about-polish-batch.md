---
title: Handoff — About polish batch (P-14 · P-17 · P-18)
tags: [design, skeleton, build, handoff]
sources: [about.md, about-journey.md, 00-page-registry.md, ../../log.md]
phase: design
updated: 2026-07-07
---

# Handoff — About polish batch (P-14 · P-17 · P-18)

**Start a fresh thread with this brief.** Three bounded follow-ups from the 2026-07-07 `/build/review` session, batchable in one thread because they share the about-frame and one review sitting. All three pages are otherwise approved — this is polish, not rebuild.

## 1 · About overview (P-14) — lighter look & feel, with alternatives

Frederik, verbatim:

> "Op deze pagina zou ik heel rap de look en feel nog een beetje vereenvoudigen. Het voelt wat zwaar; dat is het stuk van waar je naar op zoek bent en hoe dat dan verhoudt tot de sectie daaronder. Daar heb ik een paar designalternatieven voor nodig."

- The friction: the **intent pills ("waar ben je naar op zoek") vs the 4 nav-cards section below** — the two blocks compete; together they feel heavy.
- Deliverable: **a few (2–3) design alternatives** for that hero-to-cards relationship; Frederik picks. Think reduction: maybe the pills and cards merge, maybe one of them earns the page.
- Files: `resources/views/about/index.blade.php`, `resources/css/pages/about.css`, `resources/css/components/intent-card.css` + `nav-card.css`. Journey/JTBD context: [`about-journey.md`](about-journey.md).
- Row is UI 🟢 *with* this follow-up noted — so the bar is "approved page made lighter", don't regress what's approved.

## 2 · Organisation (P-17) — simplify the blue band

Frederik, verbatim:

> "Oei, deze pagina is nog wel raar, vooral die in de blauwe band. Dat moet even vereenvoudigd worden, die layout."

- Target: the **blue national-vs-local columns band**. Simplify the layout — fewer visual compartments, clearer two-sided story. UI is 🟠 on this row until this lands.
- Files: `resources/views/about/organisation.blade.php`, `resources/css/pages/about.css` (+ `person-card.css` for the duo cards — those are fine, don't touch).
- Content note: duo photos + bios are CMS 🟠 (team's job, Teamleden admin) — design must keep working with the initial-disc fallback.

## 3 · News (P-18) — three concrete tweaks

Frederik, verbatim:

> "Dat is nu de afdeling; dat staat daar raar. Dat zou beter boven op de afbeelding komen. Wat nu gebeurt, is dat de datum altijd verspringt, omdat dat een variabele breedte heeft. Daarnaast is de algemene titel een beetje raar. 'Wat we onderweg leren' is te abstract. Het mag een concretere titel zijn. Het is misschien ook wel raar dat de introtekst binnen de tweede blok staat. Ik zou denken dat die introtekst ook binnen de hero staat."

Three items, all small:
1. **Category label onto the image** (overlay), so the date stops jumping with variable label width.
2. **Concrete page title** — replace `'news_title' => 'Wat we onderweg leren'` in `lang/nl/about.php:92`. Candidates should pass the tone one-line test (joyful, local, concrete): think "Nieuws uit de stoet", "Nieuws van onderweg" — propose 2–3, Frederik picks.
3. **Intro text into the hero** (it now sits oddly in the second block).

- Files: `resources/views/articles/index.blade.php` (+ `x-article-feature` component), `resources/css/pages/article.css`, `lang/nl/about.php`.
- UI is 🟠 on this row until these land. CMS 🟠 (article check + covers) is the team's, not this thread's.

## Shared constraints

- Styling layers: appearance in component blades / `resources/css/components/*`, page layout in page CSS, tokens only — never raw hex/px, never `app.css`. Raw headings, `<time datetime>`, `aria-hidden` on decorative icons.
- Taste: lightweight, hierarchy, breathing room (standing preference). Copy NL, tone-of-voice guide, no em-dashes.
- Tests: assert `data-*` seams/behaviour only; check `ComponentExtractionTest`/`CssArchitectureTest` stay green. Title change: grep for tests asserting `__('about.news_title')` usage.
- Batch UI edits, then **one** screenshot pass (token discipline).

## Done when

Frederik re-reviews the three pages in `/build/review` and flips P-17/P-18 UI → 🟢 (P-14 already 🟢 — he confirms the lighter variant). Registry rows + runway "About polish batch" row updated; log entry appended.

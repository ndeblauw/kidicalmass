---
title: Chapter page (P-11) v4 — surface/UI build handoff
tags: [chapter, P-11, surface, handoff]
sources: [superpowers/specs/2026-06-23-chapter-page-v4-build-design.md, superpowers/plans/2026-06-23-chapter-page-v4.md, wiki/design/30-skeleton/chapters.md]
phase: design
updated: 2026-06-24
---

# Chapter page v4 — surface/UI build handoff

For a **fresh thread**. The v4 **structure** is built, reviewed, and merge-ready on `main`; what
remains is the **surface pass** — bringing the structurally-correct page to production visual quality,
plus a short list of deferred fixes. This is appearance work (colour, type, spacing, rhythm, motion),
which the structural build deliberately did not touch.

## Where things stand (do not re-do)

- The intent-driven v4 arc is live in `resources/views/groups/show.blade.php`, fed by
  `app/Http/Controllers/GroupController@show`, styled by `resources/css/pages/chapters.css`.
- Built via the [v4 plan](../plans/2026-06-23-chapter-page-v4.md); 8 tasks, all per-task reviewed +
  a final whole-branch review (merge-ready, no Critical/Important).
- **Tests green:** `php artisan test --compact --filter=Group` (118) and `--filter=CssArchitectureTest` (4); `npm run build` clean.
- **Visual starting point:** [`assets/2026-06-24-chapter-v4-built-structure.png`](assets/2026-06-24-chapter-v4-built-structure.png) (Schaarbeek, current built state). Locked greybox (the intent): [`assets/2026-06-23-chapter-v4-skeleton.png`](assets/2026-06-23-chapter-v4-skeleton.png).
- The arc, role/beat reasoning, and "evolves v3" notes: `docs/wiki/design/30-skeleton/chapters.md` (Critique v4).

The page renders correctly section-by-section; it just looks **greyboxed-into-real-components** in places
(especially §6). The job is to make each section look *designed*.

## Hard constraints (carry from the structural build)

- **KEEP VERBATIM — do not restructure or rewrite these, only restyle their container/new home:** the §5 gallery+lightbox (`chapter-gallery*` Alpine block), the team carousel internals (`chapter-team__carousel`/`__track`/`__card`), the Livewire signup form + its reveal. You may add/adjust CSS around them, but do not touch their markup/JS.
- **No invented numbers / no fake content beyond what's already flagged FAUX.** §2 stat cards stay `started_at` + counted rides only.
- **CSS only in `resources/css/pages/chapters.css`** (page partial) or `resources/css/components/*` if a unit becomes reusable — NEVER `app.css`. Tokens only — no raw hex/px. (Bare `white`/`rem` already appear throughout this file and are acceptable; only hex and `px` are banned by `CssArchitectureTest`.)
- **Headings raw `<h*>`**, never `flux:heading`. Decorative icons `aria-hidden`. Dates `<time datetime>`.
- **No em-dashes or en-dashes** in any copy (ToV). Copy currently on the page is DRAFT placeholder — refine it on this pass per `docs/tone-of-voice.md` (warm, local, family-first; "does this sound like someone who loves cycling with kids in their neighbourhood?").
- Full-bleed bands must not reintroduce horizontal scroll — match the `.chapter-head` technique (`width:100vw; margin-left:calc(50% - 50vw); overflow-x:clip`).
- Shared checkout (Nico commits to `main` concurrently): stage by explicit path, never `git add -A`; do not push `main`; run `vendor/bin/pint --dirty --format agent` before any PHP commit.

## The surface work, by section

Current CSS hooks are in `chapters.css` at the lines noted. Each section already has structural CSS; this
pass makes it *look* right. Verify each against the greybox intent and the role/beat it serves.

1. **§6 team carousel — the biggest gap (deferred from the final review).** The carousel was relocated
   from the yellow closing band to a mid-page white `<section class="chapter-body chapter-team">`, but it
   still leans on `.chapter-team-band` styling that no longer wraps it, so it renders plain. **Style the
   carousel for its new white mid-page home** (card surfaces, spacing, nav buttons, headline "Wij zwaaien
   je welkom aan de start"), and **rename `.chapter-team-band`** (`chapters.css:495,505`) — it now wraps
   only the seam illo + §7 help-mee, so the name is misleading; rename to e.g. `.chapter-closing-band`
   and update both the blade and the CSS.

2. **§2 De volgende parade — make the split read as the page's gravity.** `.chapter-parade__split` /
   `__proof` / `.chapter-stat*` (`chapters.css:375-410`). Left = the featured ride card + subscribe CTA;
   right = the two stat cards (`sinds {jaar}`, `N ritten`). This is the dominant section — give it the
   most visual weight. The stat cards are currently plain; design them as confident, warm proof.

3. **§3 alle parades strip — compact rhythm.** `.chapter-parades-strip*`. Should read as a tight,
   secondary list paired under §2 (cadence), clearly lighter than the featured parade.

4. **§4 "Ook in {gemeente}" sky band.** `.chapter-other--sky` / `__grid` / `__card` / `__type`
   (`chapters.css:935-975`). Full-bleed sky band of activity cards. **Also fix the type chip labels:**
   `{{ $activity->activity_type->label() }}` returns English ("Meeting", "Other") — add NL labels
   (Meeting→"Vergadering", Other→"Activiteit"; Workshop is fine). Either a locale-aware accessor on
   `App\Enums\ActivityType` or a chip-local mapping in the view.

5. **§5 colouring download — make it pretty.** `.chapter-colouring*` (`chapters.css:818-856`). Currently a
   thumbnail + link using a placeholder illustration (`img/illustrations/caterpillar-bike.svg`, FAUX). Frederik's
   ask: "if we keep it, it should look pretty, with a download preview." Polish the preview treatment; the
   real per-group asset is Nico's backend.

6. **§7 Help mee (yellow closing).** `.chapter-join*` + the new `chapter-join__tagline`. Keep it a lean
   ask (faces now live in §6). Make the closing band feel like a warm climax fused to the yellow footer.

7. **§8 affiches + "met dank aan".** `.chapter-extras` / `.chapter-partners` / `.chapter-downloads`. Quiet
   tail. Press is gone. Make the affiche thumbnails + sponsor list read as a calm footer zone, hide-if-empty.

8. **§1 hero + overall rhythm.** Confirm the mission-line hero, the blue→white→sky→white→yellow colour
   story, and the inter-section spacing read as one intentional page (not a stack of bands).

## Cleanup (fold into the pass)

- **Sweep dead CSS** now that markup is gone: `.chapter-press*`, `.chapter-agenda*`, `.chapter-optin*`
  in `chapters.css` have no remaining markup. (Keep `.chapter-next__card*` — still used by the §2
  empty-ride state.)
- **One cosmetic test nit** (optional): `GroupsTest` "chapter extras section hidden when no partners" keeps
  a now-vacuous `assertDontSee('In de pers')` — harmless; drop or comment it if you're in the file.

## Verify before claiming done

- `php artisan test --compact --filter=Group` + `--filter=CssArchitectureTest` green; `npm run build` clean; Pint clean.
- Full-page render on **Schaarbeek (filled)**, **Anderlecht (workshop, no ride → §2 empty state)**,
  **Brussel-Stad (meeting → §4 chip)**, and a **just-started group** (sparse, hide-if-empty zones gone).
  Confirm no horizontal scroll on any.
- Then it's **Frederik's own critique + refine pass** that gates Wire 🟢 (Claude's render/tone check tops
  at 🟠). Update P-11 in the page registry + a `log.md` entry via `/pipeline` once he signs off.

## Suggested approach

This is `impeccable` / `polish` / `arrange` / `typeset` / `colorize` territory, section by section, with
one full-page screenshot pass per batch of edits (don't loop fix→screenshot per tweak — token-heavy).
Start with §6 (the biggest visible gap) and §2 (the gravity), then work down.

## Out of scope (do NOT bundle here)

- **`PublicStructureTest` `/nl/help-out` em-dash failure** — pre-existing on `main`, unrelated to the chapter
  page (different files; `volunteer.blade.php` last touched by Nico's `596f9ff`). A real ToV bug, but its own
  ticket. Don't attribute it to this work or fix it inside the chapter surface pass.
- **Backend data deps for Nico** (still FAUX on the page, clearly commented): subscribe-CTA persistence, per-group
  `group_user.role` + photos, affiches/sponsors source, real colouring asset, ride-count if you'd rather not
  derive it. See the briefing's "data deps" section. These are not surface work.

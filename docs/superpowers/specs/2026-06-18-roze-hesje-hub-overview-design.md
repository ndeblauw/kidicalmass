---
title: Roze-hesje hub — Overview + shared chrome (build spec)
tags: [design, build, roze-hesjes, chapters]
sources:
  - docs/superpowers/specs/2026-06-18-roze-hesje-hub-split-design-handoff.md
  - docs/wiki/design/30-skeleton/chapters-roze-hesjes.md
  - "Downloads/design_handoff_roze_hesje_hub/README.md (surface handoff, Optie A)"
phase: design
updated: 2026-06-18
---

# Roze-hesje hub — Overview + shared chrome

## Purpose & scope

Split the single long `groups/roze-hesjes.blade.php` into a **hub Overview + 5 sub-pages**
that share a compact pink hero and a slim sub-nav. This pass delivers the **genuinely new
work**: the **Overview** page and the **shared chrome** (`<x-roze-hub>` layout: compact hero +
hub sub-nav). The 5 sub-pages are created as **real, navigable routes** and the existing page's
content is **mechanically migrated** into them (no visual redesign of the sub-pages in this
pass).

Visual direction is **Optie A — "Helder & rustig"** (one calm column, plain white list-cards,
two-tile *Voor de rit*). High-fidelity: match the surface handoff's values, pulling from
`@theme` tokens.

**Out of scope (deferred):** visual polish of the 5 sub-pages; exact per-type feed-card anatomy
(build the shell only); swapping the Agenda sub-page to the `RideCalendar` Livewire component;
all real backend (media library, draft-state, per-group WhatsApp/playlist URLs, the change-feed)
— these are faux/seeded (Nico, GitHub #37).

## Decisions locked in brainstorming

- **Scope:** chrome + Overview built fully; existing content migrated into 5 real sub-page routes.
- **Tokens:** mint genuinely-new design-language tokens; reuse existing by real name. (§ Tokens.)
- **isCaptain:** derived from the membership pivot `role === 'captain'` (no dev toggles).
- **Hero:** purpose-built inside `<x-roze-hub>`, NOT `<x-page-hero>` (which auto-adds a
  rounded-top panel we don't want directly above the sub-nav).
- **Hub chrome CSS:** self-contained `resources/css/components/roze-hub.css` partial (registered
  in `app.css`), not piled into `chrome.css`.
- **Card radius:** add `--radius-lg: 1.5rem`; leave existing `--radius-card: 2rem` untouched.

---

## Routing & controller

A dedicated **`RozeHesjeController`** holds the 6 actions (cohesive sub-area; cleaner than
extending `GroupController`). The existing `GroupController::rozeHesjes()` + `ridePreview()`
logic moves here. All routes stay inside the existing `{locale}` group in `routes/web.php` and
keep the `BackstageDemoAccess` middleware where the current route uses it.

| Route name | Path (after `/{locale}`) | Action | Page |
|---|---|---|---|
| `groups.roze-hesjes` *(kept)* | `chapters/{group}/roze-hesjes` | `overview` | Overzicht |
| `groups.roze-hesjes.aan-de-slag` | `…/roze-hesjes/aan-de-slag` | `aanDeSlag` | Aan de slag |
| `groups.roze-hesjes.agenda` | `…/roze-hesjes/agenda` | `agenda` | Agenda |
| `groups.roze-hesjes.fotos` | `…/roze-hesjes/fotos` | `fotos` | Foto's |
| `groups.roze-hesjes.groep` | `…/roze-hesjes/groep` | `groep` | De Groep |
| `groups.roze-hesjes.materiaal` | `…/roze-hesjes/materiaal` | `materiaal` | Materiaal |

The `groups.roze-hesjes` **name is preserved** so the site-nav `.roze-nav-btn` (active-state
match in `layouts/site/header.blade.php`) and any inbound links keep working.

**Shared chrome data** — a private helper, e.g.
`hubContext(Group $group): array`, runs in every action:

```
- abort_unless member (current abort_unless guard, lifted verbatim)
- $group->load(['users', 'children', 'parent'])
- $isCaptain  = membership pivot role === 'captain' for the current user
- $showWelcome = existing per-group cookie logic (ROZE_WELCOME_WEEKS = 2)
- returns [group, isCaptain, showWelcome]
```

Each action merges page-specific data on top (e.g. `agenda` adds `$activities`, `groep` adds
`$roster`, `overview` adds `$feed`). The `active` tab key is passed by each view to `<x-roze-hub>`.

`ridePreview()` stays as-is (still membership-gated; the feed's "draft changed" card deep-links
to `groups.ride-preview`).

---

## Shared chrome: `<x-roze-hub>`

A layout component used by all 6 views:

```blade
<x-roze-hub :group="$group" active="overzicht" :is-captain="$isCaptain" :show-welcome="$showWelcome">
    {{-- page body --}}
</x-roze-hub>
```

It renders `<x-layouts::site :title="…">` → compact pink hero → `<x-roze-subnav>` → a single
centered body column (`max-width: 760px` on desktop) wrapping `{{ $slot }}`.

### Compact pink hero (built into the component)

- Full-bleed `--color-kidical-red` band; flex row, space-between, vertically centred.
- Padding ≈ `1.0625rem 1.25rem` (phone) / `1.625rem 2.25rem` (desktop).
- **Chapter name** `<h1>`: `--font-heading` (Caprasimo), weight 800, `font-synthesis: none`,
  white, `letter-spacing: -0.01em`, line-height ~1.05; ≈ `1.34rem` phone / `2.3rem` desktop.
  Content `Kidical Mass {gemeente}`. **No photo, no eyebrow.**
- **Sun mark:** `public/img/logos/logo-icon.png`, `aria-hidden="true"`, ~38px phone / ~58px
  desktop, `object-fit: contain`.
- Full-bleed handled the same way other bands cancel the layout's `pt-28` (negative margin).

### Hub sub-nav: `<x-roze-subnav>`

Data-driven from an ordered tab list. Each tab: `{ key, label, href, active }`; Beheer is a
special trailing item.

**Tab order rules** (computed in the component or a small helper):

- Base order: `Overzicht · Agenda · Foto's · De Groep · Materiaal`.
- **Aan de slag floats:**
  - inside the welcome window (`showWelcome` true, non-captain) → **2nd** item.
  - after the window (established hesje) → **end**.
  - for captains → **second-to-last**, just before Beheer.
- **🔧 Beheer →** — **last, captains only**, visibly flagged as *leaving the hub* (wrench icon +
  label + external-arrow icon, neutral ink pill). Links out to Filament.

**Phone — tab strip:** horizontal flex, `gap: 0.4375rem`, `overflow-x: auto`, hidden scrollbar,
padding `0.6875rem 0.875rem`. Tabs: `--font-sans` 700, `0.85rem`, `padding: 0.375rem 0.8125rem`,
`border-radius: var(--radius-pill)`, `white-space: nowrap`, `flex: none`.
- Active: text `--color-kidical-red`, bg `color-mix(in oklab, var(--color-kidical-red), transparent 88%)`.
- Inactive: text `color-mix(in oklab, var(--color-kidical-ink), transparent 38%)`, transparent bg.

**Desktop — slim row:** `height: 3.5rem`, `gap: 1.75rem`, `--font-sans` 700 `0.95rem`,
hairline bottom border.
- Active: ink text + `border-bottom: 3px solid var(--color-kidical-yellow)`.
- Inactive: `color-mix(in oklab, var(--color-kidical-ink), transparent 42%)`; hover → red.
- Beheer: `margin-left: auto`, neutral pill
  (`color-mix(in oklab, var(--color-kidical-ink), transparent 92%)` bg, pill radius,
  `0.4375rem 0.9375rem`).

**Reduced motion:** transitions on `--ease-brand`, wrapped/short; respect
`prefers-reduced-motion`.

CSS lives in `resources/css/components/roze-hub.css` (hero + sub-nav), registered in `app.css`.

---

## Icon chip: `<x-icon-chip>`

Extract the chip currently baked inline in `resources/views/components/feature-card.blade.php`
into a shared `<x-icon-chip color size>` component, and refactor `feature-card` to consume it
(**zero visual change** — verify against an existing feature-card render).

- Rounded square `width = height = size`, `border-radius: var(--radius-chip)` (28%),
  `transform: rotate(-3deg)`, `box-shadow: var(--shadow-float)`, white icon centred.
- `color` ∈ `red | blue | orange | violet | ink | green | coral` → maps to the `--color-kidical-*`
  token (the body stays flat white; colour rotation across the feed is intentional).
- Icon passed as slot content (Lucide-style ~2px inline SVG, hand-placed as the repo does).

Used by: the *Voor de rit* tiles (size 36) and the feed cards (size 44).

---

## Overview page body (`groups/roze-hesjes/overzicht.blade.php`)

Single column. Phone body padding `1.25rem 1.125rem 1.5rem`, section gap `1.375rem`; desktop
`max-width: 760px`, section gap `1.5rem`.

1. **Welcome panel** (`@if ($showWelcome)`) — soft pink-tinted band
   (`color-mix(in oklab, var(--color-kidical-red), transparent 90%)`), heading + one line + a
   link to **Aan de slag**. Auto-hides after the window (cookie logic). Copy follows the desktop
   mock: "Welkom bij de roze hesjes van {gemeente}" / "Fijn dat je meerijdt. Begin bij Aan de
   slag om je weg te vinden. Dit bericht verdwijnt vanzelf na je eerste weken."
2. **Voor de rit** — small uppercase label (`--font-sans` 700, `0.7rem`, `letter-spacing: 0.1em`,
   `color-mix(... ink, transparent 45%)`), then a flex row of **two equal tiles** (`flex: 1`,
   `gap: 0.625rem`). Each tile is a link: white, hairline border, `border-radius: var(--radius-md)`,
   `padding: 0.625rem 0.75rem`, flex row `gap: 0.5625rem`, centred; `<x-icon-chip size=36>` +
   label (`--font-sans` 700 `0.92rem` ink). **Speech** (chip `orange`, megaphone) → startspeech
   material; **Playlist** (chip `violet`, music) → chapter playlist link. Hover:
   `translateY(-1px)` + `--shadow-hover`.
3. **Sinds je laatste bezoek** — `<h2>` (Caprasimo 800, `1.3rem` phone / `1.6rem` desktop) + a
   vertical list of `<x-roze-feed-card>` (`gap: 0.75rem`), newest first.

### `<x-roze-feed-card>` — the card shell (flexible, anatomy deferred)

Full-card `<a href>` to the exact deep-link target. White, `border-radius: var(--radius-lg)`,
`box-shadow: var(--shadow-float)`, `padding: 0.8125rem 0.9375rem` phone / `1rem 1.125rem` desktop,
flex row, centred, `gap: 0.8125rem`:

- **Leading:** `<x-icon-chip size=44>` (colour per type).
- **Middle** (`flex: 1; min-width: 0`):
  - "What" line: `--font-sans` 700, `0.97rem` phone / `1rem` desktop, `line-height: 1.3`, ink.
  - Meta line: `--font-sans` 400, `0.78rem`, `color-mix(... ink, transparent 45%)`,
    `margin-top: 0.1875rem`; pattern `{context} · <time datetime="ISO8601">{relative}</time>`.
- **Trailing:** chevron-right, 18px, `color-mix(... ink, transparent 62%)`.
- Hover: `translateY(-2px)` + `--shadow-hover` on `--ease-brand`.

Props kept minimal so per-type anatomy can grow later: `icon` (slot), `color`, `what`, `context`,
`timestamp` (ISO), `href`. CSS for tiles + feed shell → `resources/css/pages/chapters-roze-hesjes.css`
(or fold into the same file the migrated sub-page styles use).

### Faux feed (seeded in the controller)

Newest-first array; each item `[type, color, icon, what, context, timestamp, href]`. The feed is
the **dispatcher** — deep-links to the exact target, not a list:

| type | color / icon | what | context · time | href |
|---|---|---|---|---|
| photos | blue / image | `3 nieuwe foto's van de rit van zondag` | `Rit van zondag` · 2 d | that ride's gallery (`…/fotos` for now) |
| draft | orange / pencil | `De Halloweenrit krijgt vorm` | `Route gewijzigd` · 3 d | `groups.ride-preview` |
| member | red / user-plus | `Sara rijdt nu mee als roze hesje` | `Nieuw lid` · 5 d | `…/groep` |

Targets resolve to real routes where they exist; where the per-ride gallery/draft isn't wired
yet, link to the nearest real surface and leave a `// faux — Nico #37` note.

---

## Content migration into sub-pages (mechanical, no redesign)

Each sub-page view is wrapped in `<x-roze-hub>` and carries the existing markup, lightly adapted
to lose the old `.roze-head` hero (the hub hero replaces it):

| Sub-page view | Migrated from current `roze-hesjes.blade.php` sections |
|---|---|
| `aan-de-slag.blade.php` | Welkom + "Voor je eerste rit" (4 `<x-feature-card>`, safety video, 4-step `roze-steps`) + WhatsApp-doorgang |
| `agenda.blade.php` | "Op de agenda" (day-grouped `<x-ride-day>` + "Alle activiteiten" CTA + "In voorbereiding" draft block). *Keep current implementation; RideCalendar swap deferred.* |
| `fotos.blade.php` | "Foto's" gallery (faux grid, disabled upload) |
| `groep.blade.php` | "De roze hesjes" roster (`$roster`, role label, "nieuw" marker) |
| `materiaal.blade.php` | "Jouw materiaal" (faux `$materials`, publiek/besloten badges) + the **playlist** link (new requirement: a *link* lives under Materiaal) |

Existing `.roze-*` CSS in `resources/css/pages/chapters-roze-hesjes.css` is reused as-is for the
migrated sections; remove only the now-dead `.roze-head` rules (the hub hero supersedes them).
The old single-page route view is replaced by `overzicht.blade.php`.

---

## Tokens

Add to `@theme` in `resources/css/app.css` (final list for sign-off):

```css
--ease-brand: cubic-bezier(0.22, 1, 0.36, 1);
--shadow-float: 0 4px 20px rgba(0, 0, 0, 0.08);
--shadow-hover: 0 14px 30px -12px color-mix(in oklab, var(--color-kidical-ink), transparent 55%);
--radius-pill: 9999px;
--radius-md: 1rem;
--radius-lg: 1.5rem;
--color-kidical-hairline: color-mix(in oklab, var(--color-kidical-ink), transparent 88%);
```

**Reuse existing by real name** (do NOT add aliases): `--font-sans` (Nunito Sans; briefing's
"--font-body"), `--font-heading`, `--color-text-body`, `--color-kidical-*`, `--radius-chip`.

**Notes / flags:**
- `--radius-card: 2rem` is left untouched; cards here use the new `--radius-lg: 1.5rem` per the
  briefing. Easy to unify later if desired.
- `--shadow-nav` from the briefing is **not** added — it styled the site-nav pill, which we do
  not rebuild.
- If any further token gap surfaces during build, flag it rather than inventing a raw value
  (project rule). No raw hex/px in components or page templates beyond layout utilities.

---

## State & behaviour

- **`isCaptain`** — current user's membership pivot `role === 'captain'`. Drives Beheer in the
  sub-nav + the Aan-de-slag "second-to-last" ordering. (Demo seeder already creates a captain for
  Schaarbeek.)
- **`showWelcome`** — existing per-group cookie logic (`ROZE_WELCOME_WEEKS = 2`), unchanged.
  Drives both the Overview welcome panel and the Aan-de-slag "2nd item" ordering.
- **Landing logic** — first arrival lands on Aan de slag with the welcome shown once; thereafter
  the hesje lands on Overview. *(The redirect-on-first-visit behaviour can be a thin guard on the
  `overview` action; if it adds risk, defer it and document — the welcome panel + sub-nav still
  carry the intent. To confirm during planning.)*
- Hover/motion all on `--ease-brand`; honour `prefers-reduced-motion`.

## Accessibility

Decorative icons `aria-hidden="true"`; metadata as `<dl><dt><dd>` where it's key/value; dates as
`<time datetime="ISO8601">`; `<html lang>` already dynamic via the site layout; headings raw
`<h1>`–`<h6>` (never `flux:heading`). NL only, no em-dashes.

## Testing (Pest feature tests)

- **Access:** each of the 6 routes → 200 for a member, 403 for a guest and for a logged-in
  non-member.
- **Sub-nav ordering:** new hesje (welcome on) → Aan de slag 2nd; established (welcome off) →
  Aan de slag last; captain → Aan de slag second-to-last + Beheer last.
- **Captain affordance:** Beheer link present for captain, absent for non-captain.
- **Welcome:** panel present when `showWelcome`, absent otherwise (assert via cookie state).
- **Overview body:** renders the two *Voor de rit* tiles and the feed cards (assert "what" lines
  + that each card is an `<a>` to its href).
- **CSS architecture:** `CssArchitectureTest` stays green — `components/roze-hub.css` registered
  in `app.css`; no raw hex/px in `<x-roze-hub>`, `<x-roze-subnav>`, `<x-icon-chip>`,
  `<x-roze-feed-card>`, refactored `<x-feature-card>`.

Run targeted: `php artisan test --compact --filter=RozeHesje` (plus `--filter=CssArchitectureTest`).
Run `vendor/bin/pint --dirty --format agent` before finalising.

## Files touched

**New**
- `app/Http/Controllers/RozeHesjeController.php`
- `resources/views/components/roze-hub.blade.php`, `roze-subnav.blade.php`,
  `icon-chip.blade.php`, `roze-feed-card.blade.php`
- `resources/views/groups/roze-hesjes/overzicht.blade.php` + `aan-de-slag/agenda/fotos/groep/materiaal.blade.php`
- `resources/css/components/roze-hub.css`
- `tests/Feature/RozeHesjeHubTest.php`

**Modified**
- `routes/web.php` (6 routes; point `groups.roze-hesjes` at the new controller)
- `app/Http/Controllers/GroupController.php` (remove `rozeHesjes()`/`ridePreview()` once moved)
- `resources/views/components/feature-card.blade.php` (consume `<x-icon-chip>`)
- `resources/css/app.css` (new tokens + register `components/roze-hub.css`)
- `resources/css/pages/chapters-roze-hesjes.css` (drop `.roze-head`; add tile/feed-shell rules)

**Removed**
- `resources/views/groups/roze-hesjes.blade.php` (replaced by the `roze-hesjes/` view set)

## Out of scope / deferred (do not build here)

- Visual redesign of the 5 sub-pages (this pass only migrates their content under the chrome).
- Per-type feed-card anatomy (build the shell only).
- Swapping Agenda to the `RideCalendar` Livewire component.
- Real backend: media library/upload, Activity draft-state, per-group WhatsApp + playlist URLs,
  the change-feed (faux/seeded; Nico, GitHub #37).

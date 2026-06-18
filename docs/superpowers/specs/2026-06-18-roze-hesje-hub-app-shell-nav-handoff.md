---
title: Roze-hesje hub — App-shell navigation (build brief)
tags: [roze-hesjes, navigation, chrome, design-handoff]
sources: [critique 2026-06-18, /chapters/{group}/roze-hesjes]
phase: design
updated: 2026-06-18
---

# Roze-hesje hub — App-shell navigation (build brief)

A handoff for a fresh build thread. Decisions are settled; this is execution.
Build it on **Sonnet** — it's mechanical Blade/CSS work, not design reasoning.

## Problem (what's broken today)

`/chapters/{group}/roze-hesjes` renders the **full public marketing header**
(via `<x-layouts::site>` inside `resources/views/components/roze-hub.blade.php`)
**and then** its own red identity hero + tab strip on top. Result, in ~140px:

1. Site logo "Kidical Mass" (fixed)
2. Marketing nav — Kalender · Lokale groepen · Voor het eerst · Meehelpen · Over ons
3. Steun ons donation CTA
4. The "Schaarbeek" chapter pill (`header.blade.php:44`) — already a link into this hub
5. Account ⋮ menu
6. Red hero — giant "Kidical Mass Schaarbeek"
7. Hub subnav — Overzicht · Aan de slag · Agenda · Foto's · De Groep · Materiaal

Two stacked horizontal navs; the chapter name printed **three times**; two reds
(`--color-kidical-red` header + hero) merging into one slab; the red hero's
`margin-top: -7rem` slides it **under** the fixed logo, mashing the wordmark.

Root cause: the hub is a **logged-in member workspace** (`@auth`-only, welcome
banner that fades "na je eerste weken", captain/Beheer tab) wearing the public
marketing site's chrome.

> Built 2026-06-18 — see plan 2026-06-18-roze-hesje-hub-app-shell-nav.md.

> **Update 2026-06-18 (after commit `2bf1db5`):** a "minimal fix" landed that
> resolved the *collisions* but not the mode clash. Already done there:
> the `-7rem` pull-up is **removed** (#7 no longer overlaps the logo — the pink
> band now seats below the floating nav); the subnav shares the **47.5rem**
> content column; the 5 sub-pages share one `.roze-hub-title`. Still **unbuilt**
> from this brief: the marketing nav is still rendered (`roze-hub.blade.php`
> unchanged), the **red hero band remains**, and the chapter name still prints
> three times. The App-shell work below is what's left.

## Decision: App shell (one nav)

Inside the roze-hesjes routes, **drop the marketing nav entirely**. The hub gets
one slim top bar and the hub tabs become THE navigation.

```
┌──────────────────────────────────────────────┐
│ ✿ Kidical Mass     Schaarbeek · roze hesjes  ⋮ │  slim shell bar (sticky)
├──────────────────────────────────────────────┤
│ Overzicht  Aan de slag  Agenda  Foto's  Groep  │  THE nav (existing tabs)
└──────────────────────────────────────────────┘
        ↑ logo links back to public site (home)
```

### Settled details
- **Context label**: chapter leads, role qualifies — `Schaarbeek` (bold) ·
  `roze hesjes` (lighter/quieter). The logo carries the "Kidical Mass" brand, so
  never repeat it here. Name appears **exactly once**, in this bar.
- **Colour**: the shell bar is **NOT red**. White or a faint blue/ink tint —
  a member backstage should feel calmer than the marketing megaphone. Red stays
  an accent only. Keep the existing **yellow underline** for the active tab
  (`roze-subnav__tab--active`) — it already reads distinctly.
- **Sticky**: yes, slim. Flat at rest; add a hairline/subtle shadow only once
  scrolled. On tight mobile, let the identity line scroll away and keep the
  **tab strip** pinned.
- **Back to site**: the logo → `route('home')` is the primary exit; make it
  obviously clickable. Do NOT re-import the marketing nav to solve this. Add a
  quiet `← terug naar de site` affordance only if it tests as needed.
- **Steun ons**: **drop it** from the shell. A roze hesje is already the support;
  it's a public-conversion CTA. It stays one logo-click away on every public page.
- **Multi-chapter**: if `Auth::user()->groups()->where('invisible', false)` count
  > 1, the context label becomes a small **chapter switcher** dropdown (the
  marketing header loops `$myChapters` today — preserve that capability here).

## Files to touch

- `resources/views/components/roze-hub.blade.php` — stop rendering `roze-hub-hero`;
  render the new slim shell bar + `<x-roze-subnav>`. Still wraps the layout.
- **The site layout** (`resources/views/components/layouts/site.blade.php` or
  wherever `<x-layouts::site>` lives — confirm first) — add a way to **suppress
  the marketing header** for the hub. First implementation decision: either
  (a) a `:chrome="false"`/slot prop on the layout that hides
  `layouts/site/header`, or (b) give the hub a dedicated layout. Prefer (a) if
  the layout is small; it keeps footer/meta shared.
- `resources/css/components/roze-hub.css` — **delete** the whole `.roze-hub-hero*`
  block (the `-7rem` hack is already gone as of `2bf1db5`; the red band is what
  remains to remove); add the slim shell-bar styles. Keep `.roze-subnav*`
  (it's good) and the new `.roze-hub-title` + the **47.5rem** column the rework
  introduced — align the shell bar to that same column. Stays in `@layer
  components`, token-backed colours only (no raw hex/px — `CssArchitectureTest`).
- `resources/views/components/roze-subnav.blade.php` — no structural change; the
  `Beheer` external tab stays right-aligned (`margin-left:auto`).
- `app/Support/RozeHub/HubTabs.php` — unchanged (tabs are correct).
- `resources/views/layouts/site/header.blade.php` — the `@auth` chapter pill +
  roze-nav button logic may move into the shell switcher; leave the marketing
  header itself intact (other pages still use it).

## Out of scope
- Tab content (Overzicht/Agenda/etc.) — untouched.
- Marketing header on all non-hub pages — untouched.
- Backend/data wiring — the page's faux placeholders stay as-is.

## Acceptance
- `/chapters/{group}/roze-hesjes` shows **one** navigation (shell bar + tabs),
  chapter name **once**, no red-on-red, no logo overlap.
- Logo returns to home; no "Steun ons" / marketing links in the hub.
- Sticky bar; active tab keeps the yellow underline.
- Multi-chapter member sees a working chapter switcher.
- `php artisan test --filter=CssArchitectureTest` passes (partials registered,
  no raw hex/px in components).
- Verify live at `/build`-adjacent: load the page logged in as a chapter member
  on desktop + mobile widths; confirm sticky + scroll-away behaviour.

## Notes
- Build in a fresh thread (per the roze-hesjes project note). Commit this
  critique's decisions, `/clear`, drop to `/model sonnet`, then start from this
  brief.

---
title: Roze-hesje hub — design handoff (split into sub-pages)
tags: [design, handoff, chapters, roze-hesjes]
sources: [design/30-skeleton/chapters-roze-hesjes.md, design/prototype-chapter-pages.html]
phase: design
updated: 2026-06-18
---

# Design handoff — Roze-hesje hub, split into sub-pages

**For:** Claude Design (visual/Surface pass).
**From:** UX-planning thread, 2026-06-18 (Strategy → Skeleton resolved; Surface deliberately left open).
**Full UX rationale:** [`chapters-roze-hesjes.md` → "Derde iteratie"](../../wiki/design/30-skeleton/chapters-roze-hesjes.md#derde-iteratie-2026-06-18--splitsing-in-deelpaginas).

## What to produce

A proper visual design for a **logged-in, per-chapter "roze hesje" (pink-vest) hub**, now split from one long page into a **hub overview + 5 sub-pages**, each sharing a compact pink hero and a slim sub-nav. Deliver mobile-first; the established hesje uses this on a phone, often standing next to a bike.

Design **all six surfaces** (Overview + 5 sub-pages) plus the shared chrome (compact hero, sub-nav, mobile hamburger). The genuinely new design work is the **Overview** and the **shared hero + sub-nav**; the sub-pages mostly re-skin existing components.

## Who this is for (so the visuals carry the right feeling)

One user type, ~12 people per chapter. Two mental states:

- **New hesje** — anxious, asking *"what's expected of me, how does this work, what do I do?"* Lands on **Aan de slag**. Should feel reassuring and orienting, not bureaucratic.
- **Established hesje** — comes back for a **slightly social** reason: "what's new — who joined, what ride are the captains cooking up, did people post photos I can add to?" Lands on **Overview**. **Arrival feeling: "this is a vibrant community, we're doing this together."** Plus a practical pull: before a ride, grab the **speech** and **playlist** fast.
- **Captain** = same user (same reference needs) + a link out to their Filament admin.

The tone is a **warm shared room, not an app**. The split is along *mental states*, not feature-tabs — keep it feeling like a place, not a dashboard. Voice per [`docs/tone-of-voice.md`](../../tone-of-voice.md): joyful, warm, local, committed. **No em-dashes in any copy** (project rule — it's read as an AI tell).

## Information architecture

```
chapters/{group}/roze-hesjes   ← OVERVIEW (home for established hesje)
│   • "Wat is nieuw" feed = card-based navigation; each card deep-links to the EXACT thing
│
├── /aan-de-slag   Aan de slag   (home for NEW hesje; onboarding + safety video + WhatsApp link)
├── /agenda        Agenda        (public calendar component + status badges; upcoming)
├── /fotos         Foto's        (per-ride galleries; past, newest ride first)
├── /groep         De Groep      (volunteers/roster, "nieuw" marker, captain badge)
└── /materiaal     Materiaal     (files AND links: charter, speech, playlist, video)
```

**Navigation model = hybrid:**
- Every page carries a **slim header sub-nav**.
- The Overview *additionally* renders the feed as **navigation cards**.
- **Sub-nav order:** `Overzicht · Aan de slag · Agenda · Foto's · De Groep · Materiaal · [🔧 Beheer →]`
  - **Aan de slag floats:** 2nd item while the hesje is inside the welcome window; drops to the **end** afterwards. For captains it sits **second-to-last**, just before **Beheer**.
  - **🔧 Beheer →** = last item, **captains only**, visibly flagged as *leaving the hub* for Filament (external/page-change affordance).
  - **Phone:** sub-nav collapses into a **hamburger**. Cards are the focus; open the menu to navigate.

**Landing logic:** first arrival → **Aan de slag** with the pink welcome shown **once**; thereafter the hesje lands on **Overview**. Aan de slag stays reachable via the sub-nav forever.

**Feed = dispatcher, deep-links exactly:** a "draft changed" card opens *that ride's* draft preview (NOT the agenda list); a "photos added" card opens *that ride's* gallery; a "new member" card opens De Groep.

## Skeleton (resolved)

**Compact pink hero on every page** — chapter name only, distilled copy, **the small variant** of the existing hero. **No photo in the hero** (whether a photo belongs anywhere is a Surface call for you to make). The previous hero ("Kidical Mass {gemeente}" + round group photo + angled title) was too heavy; strip it right down.

### Overview — mobile (the centrepiece)
```
┌─────────────────────────────┐
│ ☰   Kidical Mass Mortsel  🎀 │  compact pink hero · chapter name only · hamburger
├─────────────────────────────┤
│ Wat is nieuw                │
│ ┌─────────────────────────┐ │
│ │ ▣  3 nieuwe foto's      │ │ → that ride's gallery
│ │    rit van zondag · 2d  │ │
│ └─────────────────────────┘ │
│ ┌─────────────────────────┐ │
│ │ ✎  Draft-rit gewijzigd  │ │ → that ride's draft preview (not agenda)
│ │    "Halloweenrit" · 3d  │ │
│ └─────────────────────────┘ │
│ ┌─────────────────────────┐ │
│ │ ☺  Nieuw lid: Sara · 5d │ │ → De Groep
│ └─────────────────────────┘ │
│   … older events            │
└─────────────────────────────┘
```

### Overview — desktop
```
┌────────────────────────────────────────────────────────────────┐
│  Kidical Mass Mortsel                                       🎀   │  compact pink hero
├────────────────────────────────────────────────────────────────┤
│ Overzicht · Aan de slag · Agenda · Foto's · De Groep · Materiaal │  slim sub-nav
│                                                  [🔧 Beheer →]   │  (captain-only, last)
├────────────────────────────────────────────────────────────────┤
│  Wat is nieuw                                                    │
│  ┌───────────────┐ ┌───────────────┐ ┌───────────────┐          │
│  │ ▣ 3 nieuwe    │ │ ✎ Draft-rit   │ │ ☺ Nieuw lid:  │          │  newest first,
│  │   foto's      │ │   gewijzigd   │ │   Sara        │          │  mixed types,
│  │   rit zondag  │ │   Halloween   │ │               │          │  each → exact target
│  └───────────────┘ └───────────────┘ └───────────────┘          │
└────────────────────────────────────────────────────────────────┘
```

Feed cards are **newest-first, chronological, mixed types**, rendered as proper cards (today they are bare text links — upgrade them). **Card anatomy is intentionally deferred** for content/data reasons (see below); design a clean, flexible card *shell* that can carry an icon/thumbnail + a one-line "what" + a timestamp + optionally "who," but do not over-specify per-type content yet.

### Sub-pages (all share compact hero + sub-nav)
- **Aan de slag** — welcome block (shown once), "hoe werkt het / wat verwacht men," safety video, "voor je eerste rit," WhatsApp link. Mostly relocated existing onboarding content.
- **Agenda** — reuse the **public calendar/agenda component** + **status badges**; upcoming rides; a ride opens its draft/detail.
- **Foto's** — per-ride galleries, newest ride first (past rides); a ride opens its gallery.
- **De Groep** — roster grid + soft "nieuw" marker (first ~2 weeks) + captain badge.
- **Materiaal** — list of files AND links: charter 📄, speech 📄, video ▶, playlist 🔗.

## Design system & constraints (must follow)

This is a Laravel + Tailwind v4 public site with a strict styling architecture. Honour it:

- **Tokens only.** Colour, type scale, radius, shadow come from `@theme` tokens in `resources/css/app.css`. **Never a raw hex or px** in a component. Pink = the existing `--color-kidical-red` (#E63A7B) token; do **not** mint a new pink.
- **Headings:** raw `<h1>`–`<h6>` only, never `flux:heading`. Type scale (size/weight/line-height) is defined once in `@layer base`; never set inline.
- **CSS lives in role-based partials**, never piled into `app.css`: reusable unit → `resources/css/components/<role>.css`; one-page-only → `resources/css/pages/<page>.css`. The existing roze styles are in `resources/css/pages/chapters-roze-hesjes.css` and chrome `.roze-nav-btn` in `resources/css/chrome.css`.
- **Reuse before building.** Existing components to lean on: the compact hero pattern (small variant of the current roze hero), the **public calendar/agenda component** (Agenda), `<x-cta-button>`, `<x-feature-card>`, the existing roster + materiaal-tile patterns from the current `groups/roze-hesjes.blade.php`.
- **Accessibility:** decorative icons `aria-hidden="true"`; metadata as `<dl><dt><dd>`; dates as `<time datetime="ISO8601">`; `<html lang>` dynamic.
- **NL only.** All copy in Dutch, per the voice guide.

**Visual reference for the current look:** [`design/prototype-chapter-pages.html`](../../wiki/design/prototype-chapter-pages.html) (right column = the roze page) and the live `resources/views/groups/roze-hesjes.blade.php`.

## Out of scope / deferred (do NOT design these yet)

- **Exact feed-card content per event type.** Today it's prose; v2 cards must be driven by **database fields**. We design the precise card anatomy *after* the sub-pages exist and the real data shape is known. Design only the card *shell*.
- **Surface decisions are yours to make**, but flag anything that needs a token that doesn't exist yet rather than inventing raw values.
- **Backend is faux for now** (Nico, [#37](https://github.com/ndeblauw/kidicalmass/issues/37)): group media library/photo upload, Activity draft-state, per-group WhatsApp URL, the change-feed, and "materiaal = files or links." Design as if real; the build mocks/seeds it.

## Backend notes captured for Nico (context, not design work)

- **Materiaal = files OR links.** Each material item needs a **type** (file vs URL). The playlist is a **link**, not an upload. Superadmin (Leticia) uploads global material; captains upload group-specific material.
- **Feed cards = structured DB records**, not prose, so dynamic "Wat is nieuw" cards can be built.

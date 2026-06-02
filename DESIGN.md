---
title: Design System — Kidical Mass Belgium
tags: [design, surface, tokens]
phase: design
updated: 2026-06-02
---

# Design System

Human-readable reference for the Kidical Mass visual system.

> **Source of truth:** [`resources/css/app.css`](resources/css/app.css) → the Tailwind v4 `@theme` block. That is where tokens live and where Tailwind reads them. **Edit tokens there, not here** — this file documents and explains them. Visual *direction & rationale* live in [`docs/wiki/design/50-surface.md`](docs/wiki/design/50-surface.md).

The system was set in the `design/activity-page` redesign (merged to `main`). It is the approved brand system, not a draft.

## Colour

Brand tokens — use as Tailwind classes (`bg-kidical-blue`, `text-kidical-ink`, `border-kidical-red`, …).

| Token | Hex | Role |
|---|---|---|
| `kidical-ink` | `#281a39` | Primary ink — body text & headings (dark purple-navy, **not** black) |
| `kidical-yellow` | `#f9d924` | **Accent** — link highlight, hero gradient, meta panels |
| `kidical-blue` | `#1d67cd` | Default `h1` colour; activity-hero ground |
| `kidical-red` | `#E63A7B` | Logo wordmark; icon chips (magenta-pink) |
| `kidical-green` | `#5CB85C` | Secondary |
| `kidical-orange` | `#F0803C` | Secondary; hero gradient end |
| `kidical-sky` | `#40c0f2` | Badges / location labels |
| `kidical-light-blue` | `#B7E7F0` | Tinted surface / secondary |
| `kidical-light-yellow` | `#FEF3D5` | Tinted surface (e.g. activity-meta) |
| `zinc-50` … `zinc-950` | greyscale | Neutrals (borders, muted text) |

**Semantic tokens** (derived — prefer these over raw brand colours for text/links):

| Token | Value | Use |
|---|---|---|
| `--color-text-body` | `ink` + 50% white (oklab) | Body copy |
| `--color-text-link` | `kidical-ink` | Link text |
| `--color-accent` | `kidical-yellow` | Accent fills |
| `--color-accent-foreground` | `neutral-900` | Text on accent |

Tints, hairline borders and overlays are built with `color-mix(in oklab, var(--color-kidical-ink), transparent <n>%)` rather than fixed greys — keeps everything in the brand's warm-dark family.

A minimal **dark mode** variant exists (`@custom-variant dark`); accent stays yellow.

## Typography

| Role | Family | Notes |
|---|---|---|
| Headings | **Poppins**, weight 800 | `font-heading`. Scale `h1` `text-6xl`/1.05 → `h6` `text-lg`/1.5; `h1` defaults to `kidical-blue` |
| Body | **Nunito Sans** | `font-sans`. Base `text-xl`, line-height 1.625 |
| Logo wordmark | **Fredoka One**, weight 900 | rounded, playful; rendered in `kidical-red` (`.logo-brand-text`) |

Headings are heavy (800) and tightly led; body runs generous (1.6+) for warmth and legibility.

## Design language (signature moves)

The system is **playful but intentional** — the visual counterpart of the [tone of voice](docs/wiki/tone-of-voice.md). Recurring devices, as built on the activity detail page:

- **−3° tilt** — the hero `h1`, the chapter/location badge, and the icon chips are rotated `-3deg`. The brand's defining "joyful, slightly off-grid" gesture; use sparingly and consistently.
- **Red rounded-square icon chips** — `kidical-red` square, `border-radius: 28%`, rotated `-3deg`, white icon inside (`.activity-info-item__icon-wrap`).
- **Full-bleed colour blocks** — `100vw` breakouts (`margin-left: calc(50% - 50vw)`): the blue activity hero, the yellow info panel + map two-column.
- **Circular hero photo** with illustration/daisy accents bleeding past its edge (`.activity-hero__photo` `border-radius: 50%`).
- **Yellow → orange gradient** for energetic hero bands (`.hero-section`, 135°).
- **Animated yellow underline** on links — grows left-to-right on hover (no underline in header/footer nav).
- **Entrance animations** — `cubic-bezier(0.22, 1, 0.36, 1)` ease-out, short staggered delays on hero elements.
- **Card radius** `0.75rem` (e.g. `.activity-meta`).

## Using tokens

- Reference brand colours as Tailwind utilities (`bg-kidical-*`, `text-kidical-*`, `border-kidical-*`) or the CSS vars (`var(--color-kidical-*)`). **Never hardcode hex** in views — add or adjust the token in `app.css @theme`.
- New shared UI should map to a [pattern](docs/wiki/design/40-patterns.md); patterns get their visuals from these tokens, not per-page values.

## Not yet tokenised

Spacing and radius use Tailwind defaults plus a few ad-hoc per-component values (`0.75rem` card radius, hero paddings via `calc(var(--spacing) * n)`). A formal spacing/radius scale can be lifted into `@theme` if reuse grows — not a launch blocker.

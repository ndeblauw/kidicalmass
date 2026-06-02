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
- **Card radius** — content cards `1.5rem` (`.activity-promises__item`); the flatter meta/info panels `0.75rem` (`.activity-meta`).

## Layout system (how a page is built)

Public pages are built as a **vertical stack of full-bleed colour bands**, each re-aligning its content to the site container. The *mechanic* below is the shared convention; the *colour and section order are not* — see the note on variation. The activity-detail page (`activities/show.blade.php` + its `.activity-*` CSS) is the canonical kit to build from.

**Full-bleed band mechanic.** A band breaks out of the `<main class="container mx-auto px-4 py-8">` wrapper to the viewport edge, then re-aligns its content with a nested `.container`:

```css
.band { width: 100vw; margin-left: calc(50% - 50vw); }   /* break out */
```
```blade
<section class="band">
    <div class="container mx-auto px-4"> … </div>          {{-- re-align --}}
</section>
```

- **First band** cancels the main's top padding to sit flush under the sticky header: `margin-top: calc(var(--spacing) * -8)`.
- **Last band** cancels the bottom padding to butt the partners section: `margin-bottom: calc(var(--spacing) * -8)`.
- A **contained** section (e.g. an FAQ) just flows in the container; cap its measure (`max-width: ~46rem`) and give it its own `padding-block`.

**Colour bands are a playful toolkit, not a fixed order.** This is the brand's joy — pages *should* vary and each can have its own colour story. There is **no prescribed sequence**. Grounds to compose from: `kidical-blue`, `kidical-yellow`, `kidical-sky`, `kidical-light-blue`, white, plus the yellow→orange gradient for energetic hero/CTA accents. The only soft guidance: give adjacent bands enough contrast to read as distinct (avoid two *near-identical* hues touching), and keep a page's palette coherent rather than using every colour at once. For reference, the activity-detail page happens to run blue → yellow → sky → white → partners → ink; treat that as one example, not a template — Getting Started, Help out, etc. each pick their own.

**Reuse the section components — do not reinvent.** Build new pages by reusing these classes; add a small page-specific modifier only for genuine deltas.

| Component | Classes | What it is |
|---|---|---|
| Poster hero | `.activity-hero*` | Solid-colour full-bleed; −3° headline; circular photo/illustration; daisy (`logo-icon`) bleeding past the edge; optional sky badge |
| Promises band | `.activity-promises*` | Colour band + big tilted H2 + illustration + **white tilted cards** holding red icon chips |
| Meta + map | `.activity-info-map*` | Two-column 50/50; yellow meta panel (red chips, `dl`) + map |
| Event card | `<x-event-card>` (PAT-1) | Compact ride card |

*Example: Getting Started (`getting-started.blade.php`) reuses `.activity-hero*` and `.activity-promises*` verbatim — its only new CSS is the FAQ accordion and a few hero tweaks.*

**Cards.** White, `border-radius: 1.5rem`, soft shadow, **slight alternating tilt** (`-1.5deg` / `+1deg`), sitting on a colour band.

**Icon chips — canonical.** Red rounded-square (`background: kidical-red; border-radius: 28%; transform: rotate(-3deg)`) with a **white Flux/Heroicon** inside (`.activity-*__icon-wrap` + `.activity-*__icon`). Emoji chips are a **wireframe placeholder only** — swap to red + Flux at surface.

**Motion.** Card reveals use an IntersectionObserver stagger (copy the script from `activities/show.blade.php`); hero elements use the `hero-*` / `fade-up` keyframes. Every animation must be added to the `prefers-reduced-motion` opt-out block in `app.css`.

**Appearance lives in `app.css`.** Templates carry **structure only** (`grid`, `flex`, `gap-*`, `p-*`, `m-*`, `max-w-*`); colour, type, shadow, radius go in semantic component classes — see the public-site rules in [`CLAUDE.md`](CLAUDE.md).

### New-page checklist

1. Compose a **band sequence + palette that fits this page's mood** — vary it from other pages; just keep adjacent bands distinct and the page coherent.
2. **Reuse** `.activity-hero*` / `.activity-promises*` / `.activity-info-map*` before writing any new CSS.
3. Full-bleed mechanic + `.container` alignment; first band `-8` top, last band `-8` bottom.
4. Red + Flux chips; white tilted cards; −3° on headline / badge / chips.
5. Appearance in `app.css`; structure in the Blade.
6. IntersectionObserver reveals + the reduced-motion opt-out.
7. Verify: `npm run build`, screenshot desktop + mobile, 0 em-dashes, tests green.

## Using tokens

- Reference brand colours as Tailwind utilities (`bg-kidical-*`, `text-kidical-*`, `border-kidical-*`) or the CSS vars (`var(--color-kidical-*)`). **Never hardcode hex** in views — add or adjust the token in `app.css @theme`.
- New shared UI should map to a [pattern](docs/wiki/design/40-patterns.md); patterns get their visuals from these tokens, not per-page values.

## Not yet tokenised

Spacing and radius use Tailwind defaults plus a few ad-hoc per-component values (`0.75rem` card radius, hero paddings via `calc(var(--spacing) * n)`). A formal spacing/radius scale can be lifted into `@theme` if reuse grows — not a launch blocker.

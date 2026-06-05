---
name: Kidical Mass Belgium
description: Group cycling rides for families — joyful, local, bold.
colors:
  ink: "#281a39"
  yellow: "#f9d924"
  blue: "#1d67cd"
  red: "#E63A7B"
  green: "#5CB85C"
  orange: "#F0803C"
  sky: "#40c0f2"
  light-blue: "#B7E7F0"
  light-yellow: "#FEF3D5"
typography:
  display:
    fontFamily: "'Caprasimo', 'Poppins', ui-sans-serif, sans-serif"
    fontSize: "clamp(3rem, 5vw, 4.5rem)"
    fontWeight: 800
    lineHeight: 1.0
    letterSpacing: "normal"
  headline:
    fontFamily: "'Caprasimo', 'Poppins', ui-sans-serif, sans-serif"
    fontSize: "clamp(2rem, 4vw, 2.5rem)"
    fontWeight: 800
    lineHeight: 1.15
  title:
    fontFamily: "'Caprasimo', 'Poppins', ui-sans-serif, sans-serif"
    fontSize: "1.875rem"
    fontWeight: 800
    lineHeight: 1.25
  body:
    fontFamily: "'Nunito Sans', ui-sans-serif, system-ui, sans-serif"
    fontSize: "1.25rem"
    fontWeight: 400
    lineHeight: 1.625
  label:
    fontFamily: "'Nunito Sans', ui-sans-serif, system-ui, sans-serif"
    fontSize: "0.875rem"
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: "0.06em"
rounded:
  chip: "28%"
  sm: "0.75rem"
  md: "1rem"
  lg: "1.5rem"
  xl: "2rem"
  pill: "9999px"
spacing:
  sm: "0.75rem"
  md: "1.5rem"
  lg: "2.5rem"
  xl: "4rem"
components:
  button-primary:
    backgroundColor: "{colors.yellow}"
    textColor: "{colors.ink}"
    rounded: "{rounded.pill}"
    padding: "0.75rem 1.5rem"
  button-primary-hover:
    backgroundColor: "#f7f5ef"
    textColor: "{colors.ink}"
    rounded: "{rounded.pill}"
    padding: "0.75rem 1.5rem"
  button-cta:
    backgroundColor: "{colors.blue}"
    textColor: "#ffffff"
    rounded: "0.875rem"
    padding: "0.95rem 1.875rem"
  button-cta-hover:
    backgroundColor: "#1856aa"
    textColor: "#ffffff"
    rounded: "0.875rem"
    padding: "0.95rem 1.875rem"
  button-ghost:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    rounded: "0.875rem"
    padding: "0.95rem 1.875rem"
  icon-chip:
    backgroundColor: "{colors.red}"
    textColor: "#ffffff"
    rounded: "{rounded.chip}"
    width: "3.5rem"
    height: "3.5rem"
  card:
    backgroundColor: "#ffffff"
    rounded: "{rounded.lg}"
    padding: "1.75rem"
  event-card:
    backgroundColor: "#ffffff"
    rounded: "{rounded.md}"
    padding: "1rem 1.25rem"
---

# Design System: Kidical Mass Belgium

## 1. Overview

**Creative North Star: "The Sunday Morning Carnival"**

Kidical Mass Belgium is serious advocacy wearing its most joyful outfit. The visual system is bold, chromatic, and unabashedly local. It feels like the best school-run day of the year: real families, real streets, a sense that anyone on the pavement can join. Every colour choice is a commitment — the blue says *here*, the yellow says *yes*, the ink says *real*. The system proves, page by page, that cycling with kids is fun and that the bar to joining is genuinely low.

The system is built as a vertical stack of full-bleed colour bands. Each page composes its own sequence from the brand palette; no two pages need to match. The components — hero panels, promise cards, icon chips, event cards — are the recurring grammar. The colour sequence is the poetry. Together they create a site that feels neighbourhood-specific and festive rather than institutional or generic.

The system explicitly rejects: the guilt-laced charity palette and donation-CTA-every-scroll format; the soulless events-platform grid (Eventbrite/Meetup aesthetic); municipal information architecture with bureaucratic density and grey-on-grey; protest-poster maximalism or confrontational tone. What remains is carnival warmth — specific, bold, and always more joyful than it is earnest.

**Key Characteristics:**
- Full-bleed colour bands compose each page's unique palette sequence.
- Hero headline at −3° tilt (h1 only, once per page) as the signature display gesture.
- Icon chips: rounded-square (28% radius), Berry Magenta fill, white icon — the site's most recognisable repeating motif.
- Summer Lemon as the single energy accent: CTAs, hover underlines, action bars on dark grounds.
- Entrance animations on `cubic-bezier(0.22, 1, 0.36, 1)` ease-out, always reduced-motion aware.
- Tilt is retiring as a card motif. Going forward: flat cards, tilt reserved for the hero h1 and icon chips.

## 2. Colors

The palette is four committed brand colours with a supporting cast of tinted surfaces. Deep Aubergine anchors; Summer Lemon energises; Belgian Cerulean grounds; Berry Magenta identifies.

### Primary
- **Deep Aubergine** (`#281a39`): Primary ink — all body text, headings on light backgrounds, the footer ground. A purple-navy that reads as near-black in body text but reveals warmth at display sizes. Never replaced with a neutral grey.

### Secondary
- **Summer Lemon** (`#f9d924`): The brand's energy colour. Primary CTA fills (the yellow pill), the fixed action bar on activity pages, footer CTAs, the animated hover underline on body links, hero gradient start. Used decisively, not decoratively.
- **Belgian Cerulean** (`#1d67cd`): Default h1 colour; full-bleed hero backgrounds (activity, getting-started, chapter, groups, index heroes). The trust register — solid, directional. Also the primary action button fill on yellow grounds.
- **Berry Magenta** (`#E63A7B`): Logo wordmark, icon chips throughout the site, FAQ toggle indicator, error/validation text. High chroma accent that carries the movement identity. Appears in concentrated doses.

### Tertiary
- **Tangerine Orange** (`#F0803C`): Hero gradient end (Summer Lemon → Tangerine on `.hero-section`), featured ride badges. Never fills full surfaces alone.
- **Field Green** (`#5CB85C`): Confirmation states, "what you get" bullet chips, success indicators. Calm and subordinate.
- **Kidical Sky** (`#40c0f2`): Location/chapter badge background, the activity-promises band, light accent surfaces.

### Neutral
- **Ice Blue** (`#B7E7F0`): Tinted surface — the getting-started card band, support callout backgrounds, find-a-group band. Calm without being cold.
- **Butter Yellow** (`#FEF3D5`): Tinted surface — the activity meta panel, testimonial cards, contextual yellow-ground panels that don't need Summer Lemon's full intensity.
- **Zinc scale** (`#fafafa` → `#0a0a0a`): Borders, dividers, muted text, hairlines. No brand warmth applied — pure neutral where needed.

### Named Rules
**The Yellow Rule.** Summer Lemon fills CTAs and active states. Butter Yellow fills quiet warm surfaces (meta panels, notes). They are not interchangeable. If the context needs to read as *action*, use Summer Lemon. If it needs to read as *warm background*, use Butter Yellow.

**The Band Palette Rule.** Pages are composed from brand grounds (blue, sky, light-blue, yellow, orange gradient, white). No page uses every colour. Adjacent bands must contrast visually — two near-identical hues touching is prohibited.

**The Ink Rule.** Deep Aubergine is the text colour everywhere. Never substitute a generic grey (`#333`, `#666`, `zinc-700`) in a context where the body ink or a brand-tinted `color-mix` would do the same job in the palette family.

## 3. Typography

**Display / Heading Font:** Caprasimo (fallback: Poppins, then system sans-serif)
**Body Font:** Nunito Sans (fallback: ui-sans-serif, system-ui)
**Logo Wordmark:** Fredoka One (fallback: Arial Rounded MT Bold; `color: Berry Magenta; font-weight: 900`)

**Character:** Caprasimo is a single-weight (effectively 800) rounded display face with a street-poster loudness. Set with `font-synthesis: none` on the `<body>` so the browser never fakes a bold for it. Nunito Sans is its warm, high-legibility companion: broad x-height, open apertures, generous spacing. Together they read as handmade-confident — Caprasimo draws attention, Nunito Sans earns trust.

### Hierarchy
- **Display** (Caprasimo 800, `clamp(3rem, 5vw, 4.5rem)`, line-height 1.0): Hero h1 — the single tilted headline per page. Colour: white on blue bands; Belgian Cerulean on white grounds.
- **Headline** (Caprasimo 800, `clamp(2rem, 4vw, 2.5rem)` / `text-4xl`, line-height 1.15): Section h2s — band or section title. Never tilted. Colour: Deep Aubergine on light bands; white or Summer Lemon on blue/dark bands.
- **Title** (Caprasimo 800, `1.875rem` / `text-3xl`, line-height 1.25): h3 card headings, sub-section labels, section titles within cards. Colour: Deep Aubergine.
- **Body** (Nunito Sans 400, `1.25rem` / `text-xl`, line-height 1.625): All flowing copy. Max measure ~48rem (`max-width`). Colour: text-body (`color-mix(in oklab, ink, white 50%)`).
- **Label** (Nunito Sans 600–700, `0.875rem` / `text-sm`, uppercase, letter-spacing 0.06–0.1em): Section eyebrows, `<dt>` terms, timestamps, category tags, nav-strip labels. Colour: zinc-500 or ink at 40–45% opacity.

### Named Rules
**The One-Weight Heading Rule.** All headings use Caprasimo at one effective weight (800). Scale and colour create the hierarchy — never weight variation.

**The Body Width Rule.** Flowing body text is capped at `max-width: ~48rem`. Prose columns never exceed 65ch.

**The Lead Rule.** Hero lead paragraphs (`.gs-hero__lead`, `.activity-hero__date`, etc.) use Nunito Sans 400 — not Caprasimo, not bold. They sit calmly under the Caprasimo title as a subtitle register.

## 4. Elevation

The system is **flat by default, structural on elevation**. Surfaces rest flat; shadows appear when an element floats above others, receives a hover state, or needs to stand apart from its ground. Brand-tinted shadows use `color-mix(in oklab, var(--color-kidical-ink), transparent N%)` rather than generic black RGBA to stay within the palette family.

### Shadow Vocabulary
- **Float** (`0 4px 20px rgba(0, 0, 0, 0.08)`): Resting content cards — the faint ambient lift that separates white cards from coloured band backgrounds.
- **Deep float** (`0 6px 30px rgba(0, 0, 0, 0.1)`): Stacking-card sequences — slightly more presence when cards overlap in a scroll-driven deck.
- **Nav pill** (`0 0.5rem 1.5rem -0.5rem color-mix(in oklab, var(--color-kidical-ink), transparent 70%)`): The floating navigation — brand-tinted, not generic grey.
- **Hover lift** (`0 6px 22px rgba(0, 0, 0, 0.10)` or `0 14px 30px -12px color-mix(…, transparent 55%)`): Communicates interactivity on card hover.
- **Portrait** (`0 18px 44px -18px color-mix(in oklab, var(--color-kidical-ink), transparent 45%)`): Tall sticky photo frames in scrollytelling sections — deep, tinted.

### Named Rules
**The Flat-By-Default Rule.** Every element starts with no shadow. A shadow is earned by: (1) the element floats over other content; (2) it carries a hover/active state; (3) it is a feature image or tall frame that needs visual separation. Ambient decorative shadows are prohibited.

## 5. Components

### Buttons
Two button shapes. Never mix them at the same call-to-action hierarchy level.

- **Primary CTA pill** (`border-radius: 9999px`): Summer Lemon fill, Deep Aubergine text, Caprasimo 700, `padding: 0.75rem 1.5rem`. Hover: near-white `oklch(97% 0.005 95)` + `translateY(-1px)`. Used for ride CTAs, footer join, nav support pill, support callouts.
- **Action button** (`border-radius: 0.875rem`): Belgian Cerulean fill, white text, Caprasimo 800, `padding: 0.95rem 1.875rem`. Hover: `color-mix(in oklab, blue, black 10%)` + `translateY(-2px)`. Used for secondary actions, page CTAs (Find a ride, Start here).
- **Ghost** (`border-radius: 0.875rem`): Transparent + `inset 0 0 0 2px` border at 40% ink opacity. Hover tightens border to full opacity + `translateY(-2px)`. Paired with an action button on CTA bands.
- **Transition** on all: `0.2s cubic-bezier(0.22, 1, 0.36, 1)` on background and transform.

### Icon Chips
The most recognisable micro-component. Berry Magenta rounded-square with white icon.

- **Shape:** `border-radius: 28%` — the specific value that reads as "rounded square" and matches the logo's geometry.
- **Colour:** Berry Magenta fill, white icon. Size varies by context: `3.5rem` (info lists), `4.25rem` (stacking expectation cards), `3.25rem` (about nav cards), `2.75rem` (demand cards).
- **Rotation:** `rotate(-3deg)` — intrinsic to the chip. The chip tilts, never the card it sits on.
- **Colour rotation in sequences:** Cards 1–6 can cycle through brand chip colours (red → cerulean → orange → ink → …), but the card body remains flat and white.

### Cards
White surfaces floating on coloured band grounds.

- **Corner style:** `1.5rem` (24px) — generous rounding, consistent with the rounded personality.
- **Background:** White.
- **Shadow:** Float (`0 4px 20px rgba(0, 0, 0, 0.08)`).
- **Border:** None by default.
- **Internal padding:** `1.75rem`.
- **Tilt (deprecated):** The alternating `rotate(-1.5deg)` / `rotate(1deg)` pattern was intentional on the activity-promises band. Going forward, new card components ship flat. Do not add card tilt to new pages.

### Event Card (PAT-1)
The agenda list unit. Where-first: location + title are the scan targets; time is a secondary detail.

- **Shape:** `border-radius: 1rem`, `border: 1px solid` at ~10% ink opacity.
- **Background:** White; featured variant uses Butter Yellow with Summer Lemon border.
- **Title:** Caprasimo 800, text-xl, Belgian Cerulean.
- **Location:** Nunito Sans 600, text-base, ink at 82% opacity, Berry Magenta pin icon.
- **Date/time:** Caprasimo 800, text-xs, uppercase, Berry Magenta (date); ink at 42% opacity (time).
- **Hover:** `translateY(-2px)`, box-shadow increases, border-color shifts toward cerulean.

### Navigation (Floating Pill)
The site's fixed header floats as a white pill above the blue hero.

- **Shape:** `border-radius: 1.25rem`.
- **Background:** White.
- **Shadow:** `0 0.5rem 1.5rem -0.5rem color-mix(in oklab, var(--color-kidical-ink), transparent 70%)`.
- **Height:** `4.5rem`. Top offset: `1rem` (so blue shows above it).
- **Nav links:** Caprasimo 700, text-base.
- **Support CTA:** Summer Lemon pill with Berry Magenta heart icon.

### Inputs
Used in the volunteer signup and chapter notify forms.

- **Style:** `border: 2px solid` at ~18% ink opacity; `border-radius: 0.75rem`; white background; Nunito Sans, text-lg, ink.
- **Focus:** `border-color: Berry Magenta` (volunteer form) or `outline: 2px solid Belgian Cerulean; outline-offset: 1px` (chapter notify).
- **Placeholder:** Ink at 65% opacity.
- **Error text:** Berry Magenta, text-sm, Nunito Sans 600.
- **Label:** Nunito Sans 700, text-sm, uppercase, letter-spacing 0.06em, ink at 30–45% opacity.

### Page Hero + Panel System
The unified interior layout for scanner and hub pages.

- **Hero:** Fixed, `background: Belgian Cerulean`, height `40rem` desktop / `30rem` mobile, `overflow: hidden`.
- **Panel:** White, `border-radius: 2rem 2rem 0 0`, `margin-top: -2rem` to overlap the hero, scrolls over it at `z-index: 10`.
- **Eyebrow:** Nunito Sans 400, Summer Lemon, `clamp(text-lg, 1.6vw, text-2xl)`.
- **Title:** Caprasimo 800, white, `clamp(text-4xl, 4.5vw, text-7xl)`, `max-width: 18ch`.
- **Illustration:** Absolutely positioned in the right half of the hero frame, bleeding below, `animation: hero-photo-in`.

## 6. Do's and Don'ts

### Do:
- **Do** use Summer Lemon for the primary ride CTA across all pages — it is the most recognisable interaction pixel on the site.
- **Do** compose each page as a unique sequence of colour bands. Vary from other pages; adjacent bands must contrast visually.
- **Do** use the full-bleed band mechanic (`width: 100vw; margin-left: calc(50% - 50vw)`) for all coloured sections.
- **Do** tilt only the hero h1 (−3°) and the icon chip (−3°) — these are the two intentional tilt gestures per page.
- **Do** keep body prose under `max-width: ~48rem` and 65ch.
- **Do** use icon chips (Berry Magenta, 28% radius, white icon) as the primary visual accent in info lists and card sequences.
- **Do** add every entrance animation to the `prefers-reduced-motion` opt-out block in `app.css`.
- **Do** use `color-mix(in oklab, …)` for brand-tinted shadows and muted text rather than fixed grey RGBA values.
- **Do** reference brand tokens as CSS custom properties or Tailwind utilities — never hardcode hex in templates.
- **Do** use real faces, local street names, and actual chapter data over stock imagery or generic copy.

### Don't:
- **Don't** treat the site as an NGO charity page — no guilt-laced stock-photo banner, no donation CTA every scroll. This is a community, not a cause.
- **Don't** build a generic events-platform grid (Eventbrite/Meetup aesthetic) — functional but soulless, no personality.
- **Don't** apply municipal information architecture — bureaucratic density, grey-on-grey, headings that feel like form labels.
- **Don't** use protest-poster maximalism or confrontational activist tone — joyful, not confrontational.
- **Don't** tilt cards or band/section headings (h2, h3, band titles). Card tilt is deprecated for new components. Tilt is reserved for the hero h1 and the icon chip.
- **Don't** add em dashes in copy — use commas, colons, semicolons, or periods instead.
- **Don't** use `border-left` or `border-right` greater than 1px as a coloured stripe accent on cards or callouts.
- **Don't** use `background-clip: text` with a gradient background (gradient text).
- **Don't** use glassmorphism or blur-card decorations.
- **Don't** substitute generic grey neutrals (`#333`, `zinc-700`) where a brand-tinted `color-mix(in oklab, var(--color-kidical-ink), …)` keeps the palette family warm.
- **Don't** use identical card grids. If multiple cards share the same size/icon/text structure, find a compositional variation (colour rotation, size contrast, layout shift) that breaks the sameness.

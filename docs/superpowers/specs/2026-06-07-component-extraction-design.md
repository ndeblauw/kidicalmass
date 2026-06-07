---
title: Component Extraction — 8 patterns + intro-text
phase: design
updated: 2026-06-07
---

# Component Extraction

Extract 8 recurring UI patterns from page templates into reusable Blade components, plus add a new `intro-text` component. Each component gets its own CSS partial under `resources/css/components/`.

## Components

### 1. `intro-text`
Lead paragraph block used near the top of almost every inner page.

**Props:** `size=base|lead`
**Slot:** paragraph(s)

- `base`: `clamp(1.7rem, 2.25vw, 2.25rem)`, `line-height: 1.55`, near-ink, `max-width: 60ch`
- `lead`: `clamp(1.875rem, 2.7vw, 3rem)`, `font-weight: 600`, `line-height: 1.4`, full ink

Source CSS: `.ho-intro` (help-out.css), `.about-intro` / `.about-intro--lead` (about.css) — both removed from page files after extraction.

---

### 2. `section-heading`
Semantic h2 (or configurable level) inside contained sections.

**Props:** `as=h2` (heading level)
**Slot:** heading text

Renders `<{{ $as }} class="section-heading">`. CSS: small `margin-bottom`, ink color (now default from base reset).

Source CSS: `.about-section__title` — removed from about.css.

---

### 3. `pull-quote`
A quoted voice with attribution. Two variants:

**Props:** `attribution` (string), `variant=large|card`
**Slot:** quote text (without quotes in markup — CSS adds them)

- `large`: centered, heading-font, `clamp(text-2xl, 3.5vw, text-4xl)`, `max-width: 54rem`, `text-align: center`
- `card`: tilted light-yellow card, `font-size: text-lg`, `font-weight: 600`, slight rotation

Source CSS: `.about-quote` / `.about-voice` — removed from about.css.

---

### 4. `numbered-item`
Numbered demand/point card with a rotated red number chip.

**Props:** `number` (int|string), `title` (string)
**Slot:** body paragraph

White card, `border-radius: 1.5rem`, subtle shadow, alternating slight rotation (odd/even via CSS). Red rotated chip for the number. Used inside an `<ol>` in page templates.

Source CSS: `.about-demand` / `.about-demand__num` — removed from about.css.

---

### 5. `person-card`
Named person tile with role, used for team listings.

**Props:** `name`, `role`, `photo=null`

Light-yellow card, heading-font name, small muted role label. Photo slot reserved but renders a placeholder-pattern when null (photo assets pending from coordination duo).

Source CSS: `.about-duo__person` — removed from about.css.

---

### 6. `agenda-item`
Icon + label/value row, used in `<dl>` lists (e.g., activity detail sidebar).

**Props:** `icon` (Heroicon name), `label` (dt text)
**Slot:** value (dd content)

Renders a `<div class="agenda-item">` with the rotated red icon chip and a `<dt>`/`<dd>` pair. The wrapping `<dl>` stays in the page template.

Source CSS: `.activity-info-item` — removed from activity.css.

---

### 7. `info-card`
Labeled content card, e.g. press contact. Light-yellow, slightly rotated.

**Props:** `label` (small caps eyebrow text)
**Slot:** arbitrary content (link, note, etc.)

Source CSS: `.about-contact-card` — removed from about.css.

---

### 8. `titled-list-block`
A large heading + bullet list block. Used in the volunteer "what you get / what we ask" section.

**Props:** `title`
**Slot:** `<li>` items

Renders `<div class="titled-list-block"><h3 class="titled-list-block__title">...</h3><ul>...</ul></div>`. The `ul` wraps the slot content.

Source CSS: `.ho-deal__subtitle` + `.ho-deal__list` — removed from help-out.css.

---

## Hero Title Size Bump

`page-hero__title` in `resources/css/components/page-hero.css`:
- Current: `clamp(var(--text-4xl), 4.5vw, var(--text-7xl))` = clamp(2.25rem, 4.5vw, 4.5rem)
- 50% bigger: `clamp(3.375rem, 6.75vw, 6.75rem)`

---

## CSS Architecture

- Each component → `resources/css/components/<name>.css`
- Import added to `app.css` after the existing component imports
- Corresponding page-scoped CSS blocks removed from `about.css`, `activity.css`, `help-out.css`
- Class names use the component name as root (`.intro-text`, `.section-heading`, `.pull-quote`, `.numbered-item`, `.person-card`, `.agenda-item`, `.info-card`, `.titled-list-block`)
- Pages updated to use `<x-component-name>` — existing BEM classes on page templates replaced

## Styleguide

All 8 components added to the "Componenten" section of `styleguide.blade.php` with demo instances. The "Nog te extraheren" list empties these 8 entries.

## Tests

`CssArchitectureTest` must pass after the new partials are registered. No new feature tests required (pure markup extraction, no logic).

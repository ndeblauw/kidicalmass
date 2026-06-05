---
title: Footer yellow zone redesign
tags: [design, footer, layout, cta, partners]
sources: [kidicalmass.pen#t0ukp "Footer Section"]
phase: design
updated: 2026-06-05
---

# Footer yellow zone redesign

## Problem

The current page bottom is three disconnected full-bleed bands: a sky-blue partner
strip, then a full-width dark footer, with a per-page floating `about-cta` card on
some pages. The reworked **Footer Section** in `kidicalmass.pen` (`t0ukp`) turns the
whole page bottom into a single continuous **yellow field**: a closing block, the
partner recognition, the footerbunch illustration, and an *inset, rounded-top* dark
footer card all sit on one shared yellow background.

The hard requirement that drives the structure: the block directly above the
illustration must be yellow, with no gap, so the illustration's yellow background
flows seamlessly out of whatever sits above it. No competing full-bleed band may
interrupt the yellow.

## Structure

One continuous `--color-kidical-yellow` field below the page content:

```
[ page content ............ in container, normal bg ]
[ closing block ........... PAGE-owned, full-bleed yellow, via <x-slot:closing> ]   ← optional
┌─ footer zone (yellow) ──────────────────────────────┐
│  [ partners ............. white card on yellow ]      │   ← only on showcase routes
│  [ illustration ......... footerbunch on yellow ]     │
│  [ dark footer + bottom bar ... inset, rounded-top ]  │
└──────────────────────────────────────────────────────┘
```

## Ownership

The closing block **belongs to the page**, not the footer. It is different on every
page — usually a large-typography CTA, sometimes something else — and the page writes
its markup and paints it yellow. The footer owns only the bottom three blocks
(partner card, illustration, dark card), which all share the yellow background.

Because the partner strip moves *into* the yellow footer zone (as a card), nothing
global sits between the page content and the footer anymore. The closing block is
exposed as a **named `closing` slot** positioned by the layout directly above the
footer zone. The slot exists purely to place the page's block below `main` and above
the footer in one full-width region — not to own its content.

## Seam guarantee

The page's closing block and the footer zone both use the **same
`--color-kidical-yellow` token** with **zero vertical gap** between them. Two adjacent
block elements, same background, no margin → they render as one uninterrupted yellow
field. No drift because both reference the single token.

## Components & files

1. **`resources/views/layouts/site.blade.php` (layout)**
   - Remove the standalone `<x-partners />` call (partners moves into the footer).
   - After `</main>`, render `{{ $closing ?? '' }}` as a full-width region. The page
     paints it yellow, so no container-breakout (`100vw` negative-margin) is needed —
     it already renders outside `main`'s container.
   - Then render the footer zone.

2. **`resources/views/layouts/site/footer.blade.php` (footer)**
   - Wrap contents in a yellow zone wrapper (`.site-footer-zone`, `bg-kidical-yellow`).
   - Inside, top → bottom:
     1. `<x-partners />` — now a card (still self-gates to showcase routes).
     2. The footerbunch illustration (`public/img/illustrations/footerbunch-yellow.png`,
        already present in the repo). Decorative: `alt=""`, `aria-hidden="true"`.
     3. The existing dark footer, restructured to an **inset, rounded-top card**:
        max-width centered on the yellow, `30px` top corners, columns + bottom bar
        unchanged in content.

3. **`resources/views/components/partners.blade.php` (partners)**
   - Restyle `partner-strip` from a sky-blue full-bleed band → a **white card on the
     yellow**. Same data, same showcase-route gating, same content. Appearance only.

4. **`resources/css/app.css`**
   - Add `.site-footer-zone`: yellow background + vertical padding.
   - Change `.site-footer` to the **inset rounded dark card** (dark bg, `max-width`
     centered, `border-radius: <token> <token> 0 0`). Its internal column BEM styles
     are kept as-is — scope-honest, no full rewrite.
   - Restyle the partners card (white surface, radius, shadow via tokens).
   - Per the project styling rules: the zone wrapper, the inset/rounded treatment, and
     the partners card are token-backed Tailwind utilities in the component markup
     (`bg-kidical-yellow`, `max-w-*`, token radius/shadow). The dark footer's existing
     column appearance stays in `app.css`. No raw hex/px — tokens only.

## Scope (this pass)

- Build the mechanism (closing slot + footer zone) and the restyle.
- Wire the **home page** closing slot as the worked example.
- Pages with **no** closing slot still render cleanly: page content meets the yellow
  zone at a normal section boundary; the illustration never looks cut off because the
  zone owns yellow above it.
- `about-cta` pages are left **untouched** (decide-later). They provide no closing
  slot for now; their existing floating card stays as is.

## Open details (non-blocking)

- **Top hairline.** The Pencil design has a 1px dark hairline at the very top of the
  yellow zone. With a page-owned yellow closing block, the page↔zone boundary moves
  up into page territory, so a zone-owned hairline would land mid-field. Treat it as
  page-side/optional or omit it.
- **Bottom-bar credits.** The current bottom bar carries a "website by Blue Pundit &
  Impact Studio" credit and the Brussel Mobiliteit funder line; the new bottom-bar
  design shows only copyright + utility links. Keep the credits unless decided
  otherwise.

## Testing (Pest)

- Footer renders site-wide (smoke test across representative pages, no JS errors).
- Closing slot renders its content when a page provides `<x-slot:closing>`.
- Partners card appears only on showcase routes; absent elsewhere.
- No regression on existing pages (home, about, activity, utility pages).

## Non-goals

- Reconciling/removing `about-cta` (separate decision).
- Rewriting the dark footer's column markup into Tailwind.
- Rolling the closing slot out to every page (only home this pass).

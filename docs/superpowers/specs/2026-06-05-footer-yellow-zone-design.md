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

0. **`resources/views/components/closing-cta.blade.php` (new)**
   - The page-owned closing block, built once as a reusable component matching the
     Pencil CTA: large heading + one simple button, on yellow.
   - API: `<x-closing-cta heading="…" :href="route(…)" label="…" />`. An optional
     `actions` slot overrides the default single button (for mailto buttons on
     press/partners). Token-backed Tailwind, full-bleed `bg-kidical-yellow`,
     big-typography heading (raw `<h2>`, never `flux:heading`).

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

5. **`resources/views/components/about-cta.blade.php` (remove)**
   - Delete the component view and migrate its 4 users (about index, mission, vision,
     organisation) to `<x-slot:closing><x-closing-cta …/></x-slot>`.
   - **Caveat:** the `.about-cta__btn*` CSS classes are reused by
     `resources/views/livewire/partner-enquiry.blade.php` (submit button). **Keep
     those button styles in `app.css`**; only the about-cta container/visual styles
     are removed. Do not break the enquiry form.

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

- Build the mechanism (closing slot + footer zone), the `closing-cta` component, and
  the restyle.
- **Wire the closing slot on all 17 content pages**, each with tailored copy (drafted
  below, Frederik to refine). The 4 about-cta pages migrate to the new CTA; about-cta
  is removed.
- **Stub pages get no closing CTA** — they render the yellow footer zone only.
- Any page without a closing slot still renders cleanly: page content meets the yellow
  zone at a normal section boundary; the illustration never looks cut off because the
  zone owns yellow above it.

### Per-page closing CTA copy (draft — refine)

| Page | Heading | Button → route |
|---|---|---|
| home | Klaar voor je eerste rit? | Vind een rit → `activities.index` |
| activities/index | Zelf een rit in je buurt? | Zo begin je → `getting-started` |
| activities/show | Nog niet zeker hoe het werkt? | Lees hoe je meerijdt → `getting-started` |
| groups/index | Geen groep in je buurt? | Start er een → `getting-started` |
| groups/show | Rij mee in je buurt | Word lid → `membership` |
| getting-started | Klaar om mee te rijden? | Vind een rit → `activities.index` |
| find-a-bike | Toch nog een vraag? | Neem contact op → `contact` |
| volunteer | Geef de straat terug aan kinderen | Word lid → `membership` |
| steun-ons | Steun Kidical Mass | Word lid → `membership` |
| articles/index | Zin gekregen om mee te rijden? | Vind een rit → `activities.index` |
| articles/show | Zin gekregen om mee te rijden? | Vind een rit → `activities.index` |
| about/index | Rij mee met de buurt | Vind een rit → `activities.index` |
| about/mission | Samen maken we straten veiliger | Vind een rit → `activities.index` |
| about/vision | Geloof je hierin? | Word lid → `membership` |
| about/organisation | Een afdeling starten of vervoegen? | Zo begin je → `getting-started` |
| about/partners | Partner worden? | Neem contact op → mailto/`partner-enquiry` |
| about/press | Pers? | Neem contact op → mailto/`contact` |

(about/partners and about/press use the `actions` slot for their mailto/enquiry button.)

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

- Rewriting the dark footer's column markup into Tailwind.
- A closing CTA on stub/placeholder pages.
- Final-form CTA copy — the table is a starting draft for Frederik to refine.

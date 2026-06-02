---
title: Design — Surface (plane 5) / visual direction
tags: [design, surface]
sources: [notion, wiki/tone-of-voice, docs/raw/assets]
phase: design
updated: 2026-06-01
---

# Design — Surface (plane 5) / visual direction

Plane 5: the visual layer. This is **direction only** — it feeds a future `DESIGN.md` token set, which doesn't exist yet ([open concern `D-4`](01-concerns.md)). Values below are **DRAFT / unverified**: exact hex, type, and spacing must be **extracted from the brand assets** (InDesign source, logos in [`60-asset-map.md`](60-asset-map.md)) before they become tokens.

## Direction (from "Look & feel examples", Notion)

The reference is the **Jango Jim** visual style: **dark blue + white + yellow**, playful, outspoken, joyful and intentional — the visual counterpart of the [tone of voice](../tone-of-voice.md) ("joyful but not frivolous"). Signature moves:

- **Bold, saturated primary colours** — dark blue ground, bright yellow as the energy/accent.
- **Playful typography**, especially in buttons and headlines — confident, not corporate.
- **Transparent-PNG character illustrations** (the bird-with-helmet, kid-on-bike set — see asset catalogue) used as friendly punctuation.
- **Bright, fun photography**, often with overlapping group shots and pops of yellow.
- **Bold text areas / large type** for statements.

Positioning guardrail (strategy D7): light and broad, mildly activist — never a hardcore-cyclist aesthetic.

## DRAFT token sketch (NOT yet normative — `D-4`)

```yaml
# PLACEHOLDER — extract real values from brand assets before use.
color:
  primary-blue:   "#TBD"   # dark blue ground
  accent-yellow:  "#TBD"   # bright energy/accent
  surface-white:  "#FFFFFF"
  ink:            "#TBD"   # body text on light
typography:
  display:  "TBD — playful display face (per Jango Jim refs)"
  body:     "TBD — legible humanist sans"
  scale:    "TBD"
radius:     "TBD (single radius preferred at low-fi stage)"
spacing:    "TBD (define a base unit)"
```

## How this becomes tokens

1. Pull exact colours from the logo files and brand sources ([`60-asset-map.md`](60-asset-map.md)).
2. Pick the two type families (display + body); define scale, weights.
3. Define base spacing unit + single radius.
4. Write them into a machine-readable `DESIGN.md` at repo root (Cascade Surface convention: tokens in frontmatter, rationale in body; code reads from tokens, never hardcodes).
5. Apply via [patterns](40-patterns.md) — patterns get their visuals from tokens, not per-page values.

## Known surface-level constraints

- **Bilingual:** every component must handle NL and FR text lengths (FR runs longer) — [structure](20-structure.md).
- **Mobile-first** for all components ([design principles](00-design-plan.md)).
- **Asset gaps** that affect surface: homepage hero video, 4 of 5 role illustrations, chapter team/ride photos — [`61-asset-slots.md`](61-asset-slots.md).

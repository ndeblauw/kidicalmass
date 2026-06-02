---
title: Design — Surface (plane 5) / visual direction
tags: [design, surface]
sources: [notion, wiki/tone-of-voice, docs/raw/assets]
phase: design
updated: 2026-06-02
---

# Design — Surface (plane 5) / visual direction

Plane 5: the visual layer. This page holds the **direction & rationale** (the *why*). The **tokens themselves are built and canonical** in [`resources/css/app.css`](../../../resources/css/app.css) `@theme`, documented in [`DESIGN.md`](../../../DESIGN.md) at repo root — set in the `design/activity-page` redesign (merged). `D-4` is closed; see [concerns](01-concerns.md).

## Direction (from "Look & feel examples", Notion)

The reference is the **Jango Jim** visual style: playful, outspoken, joyful and intentional — the visual counterpart of the [tone of voice](../tone-of-voice.md) ("joyful but not frivolous"). Signature moves:

- **Bold, saturated colours** — a **dark purple ink** (`#281a39`) ground for type, **bright yellow** (`#f9d924`) as the energy/accent, **blue** (`#1d67cd`) for headlines, with a wider playful secondary set (red/pink, green, orange, sky). Full palette in [`DESIGN.md`](../../../DESIGN.md).
- **Playful typography** — **Poppins** (heavy headings) + **Nunito Sans** (warm body) + **Fredoka One** (rounded logo wordmark). Confident, not corporate.
- **Playful −3° tilts** and **red rounded-square icon chips** as the brand's defining off-grid gesture.
- **Transparent-PNG character illustrations** (the bird-with-helmet, kid-on-bike set — see asset catalogue) used as friendly punctuation.
- **Bright, fun photography**, often circular-cropped with illustration accents bleeding past the edge; full-bleed colour blocks.
- **Bold text areas / large type** for statements.

Positioning guardrail (strategy D7): light and broad, mildly activist — never a hardcore-cyclist aesthetic.

## Tokens — built & canonical

The token set is **live in [`resources/css/app.css`](../../../resources/css/app.css) `@theme`** and documented in [`DESIGN.md`](../../../DESIGN.md) (palette, typography, the signature design-language moves, and what is *not yet* tokenised). It was established in the merged `design/activity-page` redesign and is the approved brand system — not a draft.

**Convention:** `app.css @theme` is the single source of truth; code references tokens as Tailwind utilities (`bg-kidical-*`) or CSS vars, never hardcoded hex. `DESIGN.md` is the human-readable mirror. Apply via [patterns](40-patterns.md) — patterns get their visuals from tokens, not per-page values.

## Known surface-level constraints

- **Bilingual:** every component must handle NL and FR text lengths (FR runs longer) — [structure](20-structure.md).
- **Mobile-first** for all components ([design principles](00-design-plan.md)).
- **Asset gaps** that affect surface: homepage hero video, 4 of 5 role illustrations, chapter team/ride photos — [`61-asset-slots.md`](61-asset-slots.md).

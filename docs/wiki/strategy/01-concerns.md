---
title: Strategy — concerns register
tags: [strategy, concerns]
sources: [wiki/design/10-scope, wiki/strategy/90-key-decisions-evidence, notion]
phase: strategy
updated: 2026-06-05
---

# Strategy — concerns register

The single authoritative answer to *"what's still open — can we conclude the Strategy phase?"* Stable IDs (`S-n`) never change; reference them anywhere. States: **Open** · **Partly** (remainder named) · **Closed** (resolution + pointer).

## At a glance

| State | Count | IDs |
|---|---|---|
| Open | 0 | — |
| Partly | 2 | `S-1`, `S-2` |
| Closed | 5 | `S-6`…`S-10` |
| Graduated → Design | 3 | `S-3`→`D-1`, `S-4`→`D-2`, `S-5`→`D-3` |

**Phase conclusion gate:** No strategy-plane concern is Open. `S-1`/`S-2` (Partly) are drafts safe to design against but must close before public copy ships. The three scope/design concerns that were carried here have **graduated to the [Design register](../design/01-concerns.md)** now that Design is open.

> **Interviews are signal, not decisions.** The two volunteer interviews (Jorge, Morgane) are complete, but their findings require Frederik's validation before they change strategy — they feed the design concerns, not these.

---

## Graduated to Design

These were tracked here while Design was closed; they are scope/design-plane decisions and are now canonical in [design/01-concerns.md](../design/01-concerns.md). Lineage kept for traceability.

- **`S-3` → `D-1`** — Private organiser back-office + "who's coming" attendance. **Resolved by the Alexandre (J3) interview, 2026-06-05:** back-office IN; per-event attendance CUT; replaced by a standing volunteer roster. Canonical detail in [`design/01-concerns.md` `D-1`](../design/01-concerns.md).
- **`S-4` → `D-2`** — Meetup visibility — **fully decided** (Frederik 2026-06-02): meetups public, full detail, surfaced on chapter pages only; `D-2` now **Closed**. See [`D-2`](../design/01-concerns.md).
- **`S-5` → `D-3`** — Grande Kidical Mass as a featured event (Partly).

---

## Partly

### `S-1` — Digital mission statement
- **Remainder:** working version **locked by Frederik (2026-06-02)** — the 4-job statement in [`40-value-proposition.md`](40-value-proposition.md). Only Leticia's sign-off remains.
- **Safe to:** design against it now. Close on Leticia confirm.

### `S-2` — Value proposition one-liner
- **Remainder:** hero + subhead **locked by Frederik (2026-06-02)** (joy/scale + inclusivity subhead). Only Leticia's sign-off remains. FR "kets" neutrality is a content note, not part of this concern.
- **Safe to:** wireframe and draft FR/NL now; close on Leticia confirm.

---

## Closed (audit trail)

### `S-6` — Facebook vs. site role — **Closed** (2026-05-18)
The site is canonical for ride detail; Facebook stays for reach + turnout signal. → [`00-strategy-brief.md`](00-strategy-brief.md) D2.

### `S-7` — Language scope — **Closed** (2026-05-18)
v1 = bilingual NL + FR, routed; English deferred. → D6.

### `S-8` — Audience ranking — **Closed** (2026-05-18)
Families + potential volunteers primary; potential chapter leads demoted to secondary. → D4, [`20-personas.md`](20-personas.md).

### `S-9` — Top organisational objective — **Closed** (2026-05-18)
"Bring money in" via recurring support is the #1 objective; site-wide CTA + dedicated page. → D5, [`10-organisation-goals.md`](10-organisation-goals.md). **Amended 2026-06-02 (Frederik):** the objective stands; its *expression* was reworked — framed as **"steun", not "lid"/membership**, and **prominence elevated** from quiet-footer-only to a nav CTA + contextual blocks + footer. Design-plane detail in [`design/10-scope.md` § Support](../design/10-scope.md); the reopened one-off path is [`D-9`](../design/01-concerns.md).

### `S-10` — Positioning register — **Closed** (2026-05-18)
Light and broad, mildly activist, never hardcore-cyclist. → D7.

---
title: Journey Palette — shared colour language
tags: [design, sitemap, presentation, tokens]
sources: [wiki/strategy/20-personas, wiki/strategy/50-user-journeys]
phase: design
updated: 2026-06-08
---

# Journey Palette — shared colour language

**One signature colour per journey persona.** These four colours are constant across the **A3 sitemaps** ([as-is](21-sitemap-as-is.md) · to-be [public](22-sitemap-to-be-public.md) / [private](23-sitemap-to-be-private.md)) **and the A5 journey cards**, so the two can be read side by side. This file is the single source of truth: copy the snippet blocks below, do not redefine colours ad hoc.

## The four routes

| Chip | Persona | Journey | Role | Line (edge) | Fill tint (legend/card) | Text |
|:--:|---|:--:|---|---|---|---|
| 🟢 | **P1 — first-timer family** | J1 | finds a ride, doesn't know how it works | `#16a34a` | `#dcfce7` | `#14532d` |
| 🔵 | **P2 — regular family** | J1 | finds a ride, wants a shareable page | `#2563eb` | `#dbeafe` | `#1e3a8a` |
| 🟠 | **P3 → P4 — would-be volunteer** | J2 | picks a role + chapter, gets a real reply | `#ea580c` | `#ffedd5` | `#7c2d12` |
| 🟣 | **P5 — chapter lead** | J3 | publishes a ride without emailing Brussels | `#7c3aed` | `#ede9fe` | `#4c1d95` |

**Deliberately uncoloured** (no route): P6 coordination duo, sponsors, press, spacefunding members. They appear as *destination pages* (admin, `/about/partners`, `/steun-ons`) but carry no journey line — keeps the map focused on the four card journeys. *(Adding a coordination/member route is a future decision.)*

## Neutral / structural tokens

| Use | Value |
|---|---|
| Page node fill / stroke / text | `#ffffff` / `#94a3b8` / `#0f172a` |
| Structural (grey) edge | `#cbd5e1`, width `1.5px` |
| Journey edge width | `2.5px` |
| Zone — public (bg/border) | `#f8fafc` / `#e2e8f0` |
| Zone — logged-in (bg/border) | `#fbfbfa` / `#e7e5e4` |
| Zone — back-office (bg/border) | `#f8f7f4` / `#e7e5e4` |
| External / dead-end (as-is) | fill `#f1f5f9`, border `#cbd5e1`; dead-end border `#dc2626` |

## Reuse snippet — Mermaid `classDef` (legend chips + zones)

```
classDef famFirst fill:#dcfce7,stroke:#16a34a,color:#14532d;
classDef famReg   fill:#dbeafe,stroke:#2563eb,color:#1e3a8a;
classDef vol      fill:#ffedd5,stroke:#ea580c,color:#7c2d12;
classDef lead     fill:#ede9fe,stroke:#7c3aed,color:#4c1d95;
classDef page     fill:#ffffff,stroke:#94a3b8,color:#0f172a;
```

## Reuse snippet — Mermaid `linkStyle` (the coloured routes)

Colour the journey edges (indices depend on the diagram; grey is the default):

```
linkStyle default stroke:#cbd5e1,stroke-width:1.5px;
%% P1 first-timer family
linkStyle <i…> stroke:#16a34a,stroke-width:2.5px;
%% P2 regular family
linkStyle <i…> stroke:#2563eb,stroke-width:2.5px;
%% P3→P4 volunteer
linkStyle <i…> stroke:#ea580c,stroke-width:2.5px;
%% P5 chapter lead
linkStyle <i…> stroke:#7c3aed,stroke-width:2.5px;
```

## Reuse for the A5 cards

Use the **fill tint** as the card's top border / header band and the **line** colour for the persona name, with the same chip emoji and the `Pn · Jn` tag. Keep page names identical to the sitemap node labels (e.g. *Event detail*, *Chapter page*, *Help out*) so a card and the map line up one-to-one.

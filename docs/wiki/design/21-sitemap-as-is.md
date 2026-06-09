---
title: Sitemap — AS-IS (current Wix site)
tags: [design, sitemap, presentation]
sources: [wiki/site-audit, wiki/design/25-content-migration, wiki/design/26-redirect-map, wiki/strategy/20-personas, wiki/design/journey-palette]
phase: design
updated: 2026-06-08
---

# Sitemap — AS-IS (current Wix site)

The structure of **kidicalmass.be as it is today**, with each persona's attempted route drawn in its [signature colour](journey-palette.md) — the same four colours as the to-be maps and the A5 cards. Compare against the to-be sitemaps: [Public](22-sitemap-to-be-public.md) · [Private](23-sitemap-to-be-private.md). Sourced from the [Site Audit](../site-audit.md) and the URL inventory in the [Redirect Map](26-redirect-map.md).

**The one-line story:** the website is not the entry point — **Facebook is**. Every coloured route bends out of the site: families bounce to Facebook and hit a dead end, would-be volunteers fall into the email black hole, and a chapter lead has *no on-site path at all* — they email Brussels and the duo hand-edit Wix. No logged-in zone, no self-publishing, no structured data.

## Legend

🟢 **P1 first-timer family** · 🔵 **P2 regular family** · 🟠 **P3→P4 would-be volunteer** · 🟣 **P5 chapter lead** — colour = *who is moving*; the number is that journey's step; **✗** marks where it dies today. Full token values: [journey-palette.md](journey-palette.md).

```mermaid
%%{init: {'layout': 'elk', 'flowchart': {'nodeSpacing': 50, 'rankSpacing': 75, 'curve': 'basis', 'htmlLabels': true}}}%%
flowchart TD
  classDef famFirst fill:#dcfce7,stroke:#16a34a,color:#14532d;
  classDef famReg   fill:#dbeafe,stroke:#2563eb,color:#1e3a8a;
  classDef vol      fill:#ffedd5,stroke:#ea580c,color:#7c2d12;
  classDef lead     fill:#ede9fe,stroke:#7c3aed,color:#4c1d95;
  classDef page     fill:#ffffff,stroke:#94a3b8,color:#0f172a;
  classDef ext      fill:#f1f5f9,stroke:#cbd5e1,color:#475569;
  classDef dead     fill:#fee2e2,stroke:#dc2626,color:#7f1d1d;

  subgraph LEGEND["Journey legend — colour = who is moving · ✗ = breaks today"]
    direction LR
    L1["🟢 P1 · first-timer family · J1"]:::famFirst
    L2["🔵 P2 · regular family · J1"]:::famReg
    L3["🟠 P3→P4 · would-be volunteer · J2"]:::vol
    L4["🟣 P5 · chapter lead · J3"]:::lead
  end

  subgraph SITE["kidicalmass.be (Wix) — FR + NL stacked on every page, no routing"]
    HOME["Home /<br/>hero links to Facebook"]:::page
    AG["Agenda /agenda<br/>hand-typed, every event → Facebook"]:::page
    VOL["Volunteer /volunteer<br/>one button: email bike@"]:::page
    OTHER["Other nav pages<br/>Mission · Organisation · Wallonie · Revendications<br/>Child Friendly City · News · Press · Downloads<br/>Grande Kidical Mass (yearly) · Gallery · Shop"]:::page
    HIDDEN["Hidden / orphaned<br/>13+ postal-code chapter pages /1000…/7000<br/>· /all-groups · /bruxelles — not in nav"]:::ext
  end

  subgraph OFFSITE["Off the website — where the real activity lives"]
    direction LR
    FB["Facebook<br/>event posts ARE the calendar"]:::ext
    FBEV["Facebook event<br/>time + meeting point live HERE"]:::ext
    MAIL["3 inboxes → Leticia + duo<br/>bike@ · cecilia@ · contact@kidicalmass.brussels"]:::ext
    WA["WhatsApp<br/>runs the community"]:::ext
    GF["Google Forms<br/>newsletter + Grande KM signup"]:::ext
    EXT["External crutches<br/>Google Docs (rules) · YouTube (safety)<br/>Wix CDN (PDFs) · kidicalmassliege.org"]:::ext
    LEADOFF["Chapter lead<br/>(no on-site path — works off-site)"]:::lead
  end

  DEAD["✗ Dead end for an on-site visitor<br/>no detail on-site · no account · no booking"]:::dead

  %% --- grey structural (indices 0-5) ---
  HOME --> OTHER
  HOME -.-> HIDDEN
  HOME -.-> GF
  HOME -.-> WA
  OTHER -.-> EXT
  VOL -.-> EXT

  %% --- 🟢 P1 first-timer family — breaks (6-8) ---
  HOME -->|"1"| AG
  AG -->|"2"| FBEV
  FBEV -->|"✗"| DEAD

  %% --- 🔵 P2 regular family — Facebook-only (9-11) ---
  HOME -->|"1"| FB
  FB -->|"2"| FBEV
  FBEV -->|"✗ no page to share"| DEAD

  %% --- 🟠 P3→P4 would-be volunteer — email black hole (12-14) ---
  HOME -->|"1"| VOL
  VOL -->|"2"| MAIL
  MAIL -->|"✗ black hole"| DEAD

  %% --- 🟣 P5 chapter lead — no on-site path (15-16) ---
  LEADOFF -->|"1 · email Brussels"| MAIL
  MAIL -->|"2 · duo hand-edits Wix"| AG

  %% --- invisible legend-order scaffold (17-19) ---
  L1 ~~~ L2
  L2 ~~~ L3
  L3 ~~~ L4

  linkStyle default stroke:#cbd5e1,stroke-width:1.5px;
  linkStyle 6,7,8 stroke:#16a34a,stroke-width:2.5px;
  linkStyle 9,10,11 stroke:#2563eb,stroke-width:2.5px;
  linkStyle 12,13,14 stroke:#ea580c,stroke-width:2.5px;
  linkStyle 15,16 stroke:#7c3aed,stroke-width:2.5px;
  linkStyle 17,18,19 stroke-width:0px,stroke:none;

  style SITE fill:#f8fafc,stroke:#e2e8f0;
  style OFFSITE fill:#f8f7f4,stroke:#e7e5e4;
  style LEGEND fill:#ffffff,stroke:#e2e8f0;
```

## Where each route breaks today

- **🟢 P1 first-timer family (J1):** Home → **Agenda** (a text-only date list, no times or locations) → bounces to **Facebook** → **dead end** for anyone without a Facebook account.
- **🔵 P2 regular family (J1):** knows the drill, jumps to **Facebook** for the next date — but there is **no standalone event page** to share; the site offers nothing to link to.
- **🟠 P3→P4 would-be volunteer (J2):** Home → **Volunteer** → one button, "email `bike@`". No form, no role, no chapter routing → the **email black hole**.
- **🟣 P5 chapter lead (J3):** has **no on-site path**. They email Brussels and wait; the coordination duo hand-edit the Wix agenda. The calendar is a single point of failure.

## The seven cross-cutting problems (from the audit)

1. **Bilingual structure is broken** — FR and NL stacked on every page; no toggle, no `/fr` `/nl` routes.
2. **Total Facebook dependency** — the entire event ecosystem lives on Facebook.
3. **Everything is manual** — no database-driven content anywhere.
4. **No chapter pages exist** — 13+ active local groups, not one has a real page.
5. **Contact fragmentation** — three inconsistent email addresses (`.be` vs `.brussels`).
6. **No structured volunteer onboarding** — read page → send email → wait.
7. **Stats and content go stale immediately** — hardcoded 2024 numbers shown in 2026.

## Uncertainty flags

- Only **Namur (`/5000`)** and **Mons (`/7000`)** were captured with real content in the scrape; the Brussels postal pages exist (direct links, in the redirect map) but their on-site content is unverified and assumed agenda-thin.
- The current Wix **navigation order** is not formally documented; the page grouping here is reconstructed from the audit and arranged for comparison, not a pixel-exact nav.

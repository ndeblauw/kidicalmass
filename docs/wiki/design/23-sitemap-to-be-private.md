---
title: Sitemap — TO-BE · Private zone (logged-in + back-office)
tags: [design, sitemap, presentation]
sources: [wiki/design/20-structure, wiki/strategy/50-user-journeys, wiki/strategy/20-personas, wiki/design/journey-palette]
phase: design
updated: 2026-06-08
---

# Sitemap — TO-BE · Private zone

The **login-gated** half of the new site: the logged-in volunteer zone and the organiser back-office. The public half is the previous page: [TO-BE · Public](22-sitemap-to-be-public.md). Colours follow the [journey palette](journey-palette.md).

**Story:** families never come here — they stay on the public site. Accounts are invite-only (no public register). Volunteers get the material that lives in WhatsApp today; chapter leads publish rides that surface on the public site automatically.

## Legend

🟠 **P3→P4 volunteer** continues here after Help out · 🟣 **P5 chapter lead** publishes here. Number = step. 🆕 = new.

```mermaid
%%{init: {'layout': 'elk', 'flowchart': {'nodeSpacing': 50, 'rankSpacing': 75, 'curve': 'basis', 'htmlLabels': true}}}%%
flowchart TD
  classDef page     fill:#ffffff,stroke:#94a3b8,color:#0f172a;
  classDef offpage  fill:#f5f5f4,stroke:#94a3b8,color:#475569,stroke-dasharray:4 3;
  classDef legend   fill:#fafafa,stroke:#ececec,color:#b0b0b0;

  FROMPUB["↩ arrives from the public site<br/>Help out → Chapter page"]:::offpage
  LOGIN["🔓 Log in<br/>invite-only · no public register"]:::page

  subgraph VOLZ["LOGGED-IN — volunteers"]
    direction LR
    MYA["🆕 My activities /my-activities"]:::page
    BACK["🆕 Chapter back-office /backstage/{postal}<br/>docs · video · roster · downloads"]:::page
  end

  subgraph ADM["BACK-OFFICE — organisers (Filament /admin)"]
    direction LR
    ADMINLEAD["🆕 Chapter lead — own chapter"]:::page
    ADMINPUB["🆕 Publish a ride"]:::page
    ADMINDUO["🆕 Coordination duo — all chapters"]:::page
  end

  PUBOUT["↗ appears on the public site<br/>Events + Chapter pages"]:::offpage
  LEG["🟠 would-be volunteer    🟣 chapter lead"]:::legend

  %% grey structural (0-1)
  FROMPUB -.-> LOGIN
  LOGIN --> ADMINDUO
  %% 🟠 volunteer (2-3)
  LOGIN -->|"1"| MYA
  MYA -->|"2"| BACK
  %% 🟣 chapter lead (4-6)
  LOGIN -->|"1"| ADMINLEAD
  ADMINLEAD -->|"2 · publish, no email to Brussels"| ADMINPUB
  ADMINPUB -->|"auto-appears publicly"| PUBOUT
  %% invisible legend placement (7)
  BACK ~~~ LEG

  linkStyle default stroke:#cbd5e1,stroke-width:1.5px;
  linkStyle 2,3 stroke:#ea580c,stroke-width:2.5px;
  linkStyle 4,5,6 stroke:#7c3aed,stroke-width:2.5px;
  linkStyle 7 stroke-width:0px,stroke:none;

  style VOLZ fill:#fbfbfa,stroke:#e7e5e4;
  style ADM fill:#f8f7f4,stroke:#e7e5e4;
```

## Reading the routes

- **🟠 P3→P4 volunteer:** arrives from Help out on the public site → **log in** → **My activities** (their chapters' rides + meetups) → **Chapter back-office** (docs, video, roster, downloads — the material library that lives in WhatsApp today).
- **🟣 P5 chapter lead:** **log in** → **admin** (own chapter) → **Publish a ride**. One publish, no email to Brussels — it appears automatically on the public **Events** calendar and the **Chapter page**. *(This is the reframe D1; still the journey most at risk — untested, Alexandre is the planned interview.)*

## Out of scope here

Per the [strategy brief](../strategy/00-strategy-brief.md) and [D-1](01-concerns.md): no per-event attendance / "who's coming", no membership gate (viewing is public; login gates this zone only — D9), no Grande Kidical Mass cross-chapter coordination. The coordination duo (P6) has full cross-chapter admin access but no dedicated journey route.

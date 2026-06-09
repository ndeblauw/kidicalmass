---
title: Sitemap — TO-BE · Public pages
tags: [design, sitemap, presentation]
sources: [wiki/design/20-structure, wiki/strategy/50-user-journeys, wiki/strategy/20-personas, wiki/design/journey-palette]
phase: design
updated: 2026-06-08
---

# Sitemap — TO-BE · Public pages

The **public** half of the new site — everything anyone can see without logging in. The login-gated half is on the next page: [TO-BE · Private zone](23-sitemap-to-be-private.md). Compare against [AS-IS](21-sitemap-as-is.md). Colours follow the [journey palette](journey-palette.md), shared with the A5 cards.

**Story:** the national site finally answers "when and where, near me" on-site. Top-level pages run in real nav order (Events leads). Footer/utility pages (Contact, Privacy, login link) are omitted here for clarity.

## Legend

🟢 **P1 first-timer family** · 🔵 **P2 regular family** · 🟠 **P3→P4 would-be volunteer** — colour = *who is moving*; the number is the step. 🟣 Chapter leads (P5) work in the [private zone](23-sitemap-to-be-private.md); the rides they publish appear automatically on **Events** and **Chapter pages**. 🆕 = new page.

```mermaid
%%{init: {'layout': 'elk', 'flowchart': {'nodeSpacing': 50, 'rankSpacing': 75, 'curve': 'basis', 'htmlLabels': true}}}%%
flowchart TD
  classDef page     fill:#ffffff,stroke:#94a3b8,color:#0f172a;
  classDef offpage  fill:#f5f5f4,stroke:#94a3b8,color:#475569,stroke-dasharray:4 3;
  classDef legend   fill:#fafafa,stroke:#ececec,color:#b0b0b0;

  subgraph PUB["PUBLIC — anyone, no login"]
    HOME["Home /"]:::page

    subgraph NAV["Main navigation — in nav order"]
      direction LR
      EV["Events /events<br/>national calendar"]:::page
      CH["Chapters /chapters"]:::page
      GS["Getting Started"]:::page
      HO["Help out"]:::page
      AB["About /about"]:::page
      STEUN["♥ Steun ons"]:::page
    end

    EVD["🆕 Event detail<br/>date · time · meeting point · distance"]:::page
    SUB["🆕 Subscribe · rides near me"]:::page
    CHP["🆕 Chapter page<br/>local schedule · volunteer form"]:::page
    ABCH["Mission · Vision · Organisation<br/>News · Press · Partners"]:::page
  end

  PRIVZONE["🔒 Volunteer + organiser zone<br/>log in — see the Private pages"]:::offpage
  LEG["🟢 first-timer family    🔵 regular family    🟠 would-be volunteer"]:::legend

  %% grey structural (0-4)
  HOME --> GS
  HOME --> AB
  HOME --> STEUN
  EV --> SUB
  AB --> ABCH
  %% 🟢 P1 first-timer family (5-6)
  HOME -->|"1"| EV
  EV -->|"2"| EVD
  %% 🔵 P2 regular family (7-9)
  HOME -->|"1"| CH
  CH -->|"2"| CHP
  CHP -->|"3"| EVD
  %% 🟠 P3→P4 would-be volunteer (10-12)
  HOME -->|"1"| HO
  HO -->|"2"| CHP
  CHP -->|"3 · log in →"| PRIVZONE
  %% invisible scaffold: nav order (13-17) + legend placement (18)
  EV ~~~ CH
  CH ~~~ GS
  GS ~~~ HO
  HO ~~~ AB
  AB ~~~ STEUN
  EVD ~~~ LEG

  linkStyle default stroke:#cbd5e1,stroke-width:1.5px;
  linkStyle 5,6 stroke:#16a34a,stroke-width:2.5px;
  linkStyle 7,8,9 stroke:#2563eb,stroke-width:2.5px;
  linkStyle 10,11,12 stroke:#ea580c,stroke-width:2.5px;
  linkStyle 13,14,15,16,17,18 stroke-width:0px,stroke:none;

  style PUB fill:#f8fafc,stroke:#e2e8f0;
  style NAV fill:#ffffff,stroke:#e2e8f0;
```

## Reading the routes

- **🟢 P1 first-timer family:** Home → **Events** (filter by town) → **Event detail** (date, time, meeting point, distance). No Facebook.
- **🔵 P2 regular family:** Home → **their Chapter** → **Chapter page** → **Event detail** — a shareable page that isn't Facebook.
- **🟠 P3→P4 would-be volunteer:** Home → **Help out** → **Chapter page** (routed form to the local lead), then **log in** to continue in the [private zone](23-sitemap-to-be-private.md).

## The hub

**Events** is the national calendar: every chapter's rides aggregate here automatically. Chapter leads never email Brussels — they publish in the private zone and the ride surfaces on Events and on their Chapter page. Facebook stays for reach and points *into* the site.

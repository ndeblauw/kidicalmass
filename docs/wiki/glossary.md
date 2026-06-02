---
title: Glossary
tags: [cross-cutting]
sources: [notion, wiki/strategy]
phase: cross-cutting
updated: 2026-06-02
---

# Glossary

Shared vocabulary across the wiki, the code/admin, and the live product. Use the **approved** term; avoid the **don't use** ones. Bilingual labels (NL / FR) are the public-facing strings — pick the chapter's own language at chapter level, both at national level.

| Concept | Approved term (EN, internal) | NL (public) | FR (public) | Don't use | Notes |
|---|---|---|---|---|---|
| A local municipal group | **chapter** | lokale groep | groupe local / antenne locale | "franchise", "branch" | A federated local team running rides in one municipality. ~45 exist. |
| The two national coordinators | **coordination duo** | coördinatieduo | duo de coordination | "the admins", "HQ" | Leticia Sere & Cecilia Pagola. |
| Escort volunteer in a hi-vis vest | **pink vest** | roze hesje | gilet rose | "marshal", "steward" | The signature volunteer role. |
| A public family bike parade | **ride** (activity type `kidicalmass`) | **fietsparade** (concept) · **fietstocht** / tocht (in motion) | **parade cycliste festive** (concept) · **balade** (in motion) · **cortège** (escorted) | NL "stoet" / "rit" / "optocht"; FR "événement" / "trajet"; EN "event" | Coordinator copy is consistent across every mission/about/stats page: **parade / fietsparade** *defines* the thing (mirrors the international movement); **balade / fietstocht** is the warm in-motion word; FR adds **cortège** for the escorted procession (no NL equivalent in use — "stoet" was never used and reads as carnaval/begrafenis). EN "ride" is the internal label for the `kidicalmass` type; "Activity" is the data model. Evidence: `docs/raw/website/{le-projet-het-project,index}.md`. |
| Internal gathering (publicly listed) | **meetup** (types `meeting` / `workshop` / `other`) | bijeenkomst | rencontre | "private event" | **Publicly visible** as a traction/recruitment signal (D-2, 2026-06-02). Login gates attendance + back-office, not viewing. |
| The annual flagship ride | **Grande Kidical Mass** | Grote Kidical Mass | Grande Kidical Mass | "the big one" | One featured ride per year (not a hand-built yearly page — see design scope). |
| Recurring individual membership | **spacefunding member** | spacefunding-lid | membre spacefunding | "Growthfunding" (misspelling) | **Resolved (live campaign 2026-06):** the platform is **Growfunding** ([growfunding.be](https://growfunding.be/fr/projects/kidicalmassbelgique)); **Spacefunding** is its recurring-support model ("Ceci est un projet Spacefunding"). Recurring only — monthly or annual. Entry tier **€3/mo "Kidi Buddy" → t-shirt = your membership**; 6 tiers total (€3–€500/mo); €20+ tiers add logo/social placement (sponsor-like, cross-ref Partners). The t-shirt **is** the membership (no separate store). Payment happens on Growfunding; the site links out. |
| A logged-in chapter volunteer | **group-volunteer account** (`group_user`) | — | — | "member" (collides with spacefunding) | **The only kind of site account — accounts are volunteers only** (families have none). A person belongs to a chapter. Gates **attendance** ("I'm coming") **and the back-office** (meetup *viewing* is public). **Distinct from a spacefunding member**, who pays externally via Growfunding and gets no site account — a person can be both; neither implies the other. |
| The data record behind every ride/meetup | **Activity** | — | — | "Event model" | One model, four types: `kidicalmass`, `meeting`, `workshop`, `other`. |
| Editorial posts | **News** (in About) | nieuws | actualités | "blog" | Lives under `/about/news`. |
| Volunteer page | **Help out** | Meehelpen | S'engager | "Volunteer" (page title) | Warmer than "Volunteer" — chosen for tone of voice. |
| First-timer onboarding page | **Getting Started** | — | — | — | Practical reassurance for families new to cycling with kids. |

## Voice / register

See [voice → tone-of-voice.md](tone-of-voice.md) for the register dial and pre-ship checklist. The glossary fixes *which words*; tone of voice fixes *how they sound*.

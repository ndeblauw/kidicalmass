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
| A local municipal group | **chapter** (internal EN coinage) | lokale groep | groupe local | "franchise", "branch", "antenne locale" | A federated local team running rides in one municipality. ~45 exist. **Public NL/FR are the duo's own words** — their org page uses the section headings **Lokale groepen** / **Groupes locaux** and body copy "lokale groepen" / "groupes locaux"; group *leads* = **lokale trekkers** / **porteurs locaux**. "chapter" is our internal label, never theirs; "antenne locale" has no source support — dropped. Evidence: `docs/raw/website/organisation.md` (parallel NL/FR + organigram). |
| The two national coordinators | **coordination duo** | coördinatieduo | duo de coordination | "the admins", "HQ" | Leticia Sere & Cecilia Pagola. |
| Escort volunteer in a hi-vis vest | **pink vest** | roze hesje | gilet rose | "marshal", "steward" | The signature volunteer role. |
| A public family bike parade | **ride** (activity type `kidicalmass`) | **fietsparade** (concept) · **fietstocht** / tocht (in motion) | **parade cycliste festive** (concept) · **balade** (in motion) · **cortège** (escorted) | NL "stoet" / "rit" / "optocht"; FR "événement" / "trajet"; EN "event" | Coordinator copy is consistent across every mission/about/stats page: **parade / fietsparade** *defines* the thing (mirrors the international movement); **balade / fietstocht** is the warm in-motion word; FR adds **cortège** for the escorted procession (no NL equivalent in use — "stoet" was never used and reads as carnaval/begrafenis). EN "ride" is the internal label for the `kidicalmass` type; "Activity" is the data model. Evidence: `docs/raw/website/{le-projet-het-project,index}.md`. |
| Internal gathering (publicly listed) | **meetup** (types `meeting` / `workshop` / `other`) | bijeenkomst | rencontre | "private event" | **Publicly visible** as a traction/recruitment signal (D-2, 2026-06-02). Login gates the back-office + volunteer roster, not viewing (per-event attendance cut — D-1, 2026-06-05). |
| The annual flagship ride | **Grande Kidical Mass** | Grote Kidical Mass | Grande Kidical Mass | "the big one" | One featured ride per year (not a hand-built yearly page — see design scope). |
| Recurring individual support | **spacefunding member** (internal) | **steun** / je steunt Kidical Mass | **soutien** / soutenir Kidical Mass | **"lid" / "word lid" / "lidmaatschap"**, "member", "membre", "Growthfunding" (misspelling) | **Terminology reworked 2026-06-02 (Frederik):** the public verb is **"steun", never "lid"** — everyone rides for free; you support so it *stays* free. "Spacefunding"/"Kidi Buddy" survive only as the *model / entry-tier* name, not the public verb. **Platform/model (live campaign 2026-06):** platform is **Growfunding** ([growfunding.be](https://growfunding.be/fr/projects/kidicalmassbelgique)); **Spacefunding** is its recurring-support model. Recurring is the lead (monthly/annual); a **discreet one-off path** is provisionally back in scope ([D-9](design/01-concerns.md)). Entry tier **€3/mo "Kidi Buddy" → t-shirt**, framed as a **visible token of support ("draag je steun")**, not a membership card; 6 tiers (€3–€500/mo); €20+ add logo/social placement (sponsor-like, cross-ref Partners). No separate store. Payment happens on Growfunding; the site links out. See [`design/30-skeleton/steun-ons.md`](design/30-skeleton/steun-ons.md). |
| A logged-in chapter volunteer | **group-volunteer account** (`group_user`) | — | — | "member" (collides with spacefunding) | **The only kind of site account — accounts are volunteers only** (families have none). A person belongs to **one or more** chapters (many-to-many). Gates **the back-office and the volunteer roster** (meetup *viewing* is public; per-event attendance cut — D-1, 2026-06-05). **Distinct from a spacefunding member**, who pays externally via Growfunding and gets no site account — a person can be both; neither implies the other. |
| The data record behind every ride/meetup | **Activity** | — | — | "Event model" | One model, four types: `kidicalmass`, `meeting`, `workshop`, `other`. |
| Editorial posts | **News** (in About) | nieuws | actualités | "blog" | Lives under `/about/news`. |
| Volunteer page | **Help out** | Meehelpen | S'engager | "Volunteer" (page title) | Warmer than "Volunteer" — chosen for tone of voice. |
| First-timer onboarding page | **Getting Started** | — | — | — | Practical reassurance for families new to cycling with kids. |

## Voice / register

See [voice → tone-of-voice.md](tone-of-voice.md) for the register dial and pre-ship checklist. The glossary fixes *which words*; tone of voice fixes *how they sound*.

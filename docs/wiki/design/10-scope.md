---
title: Design — Scope (plane 2)
tags: [design]
sources: [wiki/ux-planning, notion]
phase: design
updated: 2026-07-03
---

# Design — Scope (plane 2)

*What are we building? In / won't-have / shared vocabulary. Plane 2 of the design phase. Constrained by the [strategy brief](../strategy/00-strategy-brief.md); constrains [Structure](20-structure.md).*

### Core

1. **MVP core:** Activity database — one model covering public rides *and* internal meetings/workshops, **all publicly viewable** (calendar + detail pages + iCal) + Chapter pages (self-published, fixed template, list their own meetups) + Volunteer path (routed contact form) + Accounts (logged-in volunteers get the **back-office + volunteer roster**) + National pages (Home, About section, Getting Started)
2. **Backstage as constraint:** everything in scope must be maintainable by chapter leads without coordination duo involvement
3. **Genuine cuts (confirmed, interview 2026-05-18):** photo-gallery system, poster/flyer auto-generation, public web store — removed, not deferred. (Minimal private organiser back-office **IN scope, validated by Alexandre/J3 2026-06-05**; per-event attendance **CUT** — see [design concern D-1](01-concerns.md).)
4. **Content ownership by default:** coordination duo → national content; chapter leads → chapter content; any lead → news
5. **Migration is Nico's:** database seeders for existing content. Key pages rewritten using the ToV guide.

### Functional Specifications

**Activities — public rides + internal meetups (added/clarified — interview 2026-05-18)**
- One `Activity` model, four types: `kidicalmass` (public family ride), `meeting`, `workshop`, `other`. Bilingual (NL/FR), linked to one or more groups, has an organizer. *(Already built by Nico on `main` — the Articles/Activities split exists. Not yet built: a visibility field — see build implication.)*
- **Viewing rule (closed — D-2):** **all activity types are public** — `kidicalmass` rides and `meeting`/`workshop`/`other` meetups alike, **full detail**, cross-group. Meetups are public as a traction/recruitment signal. **Login gates the back-office + volunteer roster, not viewing.** **Where public meetups surface (settled): chapter pages only** — each chapter lists its own meetups; **no national movement view**, and **not** on the family ride calendar (`/events` stays rides-only). A logged-in volunteer's cross-group view lives in [My activities](30-skeleton/my-activities.md); its default-municipality filter is a to-test skeleton detail. See [D-2](01-concerns.md).
- Find rides by location; iCal export
- Per-region/per-chapter email subscriptions — low frequency (rides published ~Jan & Sept on a school-year rhythm; not an editorial newsletter). "Next ride near you" + nearby gemeentes + latest press/photos.
- Event detail pages with full practical info: date, time, meeting point, distance, duration, age range, cost, Komoot route link
- **Build implication for Nico:** viewing needs **no gate** (all types public). What needs a logged-in volunteer is the **back-office** and the **volunteer roster view**. **Do not build the `Attendance` (volunteer ↔ activity) relation** — replaced by an opt-in public-visibility flag on `group_user` (D-1). (The earlier "visibility column to hide meetups" need is also dropped.) Flag in build.

**Chapter pages**
- Self-published by chapter leads (no going through Brussels)
- Content per chapter: schedule, team members, **local volunteers (opt-in public visibility)**, local partners, press coverage, downloads
- Chapter leads manage their own content within design constraints — **local partners and local press are chapter-admin editable with foolproof uploads, no HQ quality-control gate** (Alexandre/J3, 2026-06-05; see [D-1](01-concerns.md) Decision E)

**Volunteer onboarding**
- Contact form per chapter, routed to the correct local lead (not the central mailbox)
- A clear "Zo word je vrijwilliger" page: explicit steps + what to expect
- Auto reminder/explainer email after sign-up ("here's how it works")
- Not a structured workflow/dashboard — that is an open question (see [design concern D-1](01-concerns.md))

**Contributor section**
- Covers both "I want to volunteer" and "I want to start a chapter"
- Chapter overview page includes a "start a chapter" CTA
- Full chapter-start intake process is static for MVP; structured workflow deferred

**News / Blog**
- Already built by Nico — included in MVP

**Support / funding — "Steun Kidical Mass" (top org objective; terminology + prominence reworked 2026-06-02)**
- Dedicated [`/steun-ons` page](30-skeleton/steun-ons.md) (was `/membership`) explaining **Spacefunding** (the recurring-support model) on the **Growfunding** platform.
- **Terminology (Frederik):** the public verb is **"steun" / "steun Kidical Mass"**, the giver **"steunt mee"** — **never "lid"/"member"**. Everyone rides for free; you support so it *stays* free. "Spacefunding" + "Kidi Buddy" survive only as the *name of the model / entry tier*, not the public verb. Retires "Word lid"/"Lid worden" sitewide.
- **Lead the ask with the plain act: "Steun vanaf €3 per maand"** — you're just *supporting*. The **t-shirt** is the **visible token ("draag je steun")**, a thank-you beneath the act; **"Kidi Buddy" does not front the card** (too specialised — Frederik 2026-06-02), it stays the tier name on Growfunding. 6 tiers total (€3–€500/mo); €20+ add logo/social placement (cross-ref Partners).
- **No backer count** on the page — reassure via **movement/participation scale** ("honderden gezinnen elke maand"), not "X mensen steunen" (small + stale-stats trap; Frederik 2026-06-02).
- **Recurring only** (monthly via Growfunding); the one-off path is **out of v1** — the provisional reinstatement was cut (Frederik 2026-07-03), no IBAN on-site. See [design concern `D-9`](01-concerns.md) (Closed).
- The site **links out** to Growfunding — it does **not** process payments. Offer a "see all tiers" link as the secondary path.
- **Prominence (elevated 2026-06-02):** a **"Steun" CTA button in the primary nav** (top-right, replacing login → login moves to the footer), **contextual "Steun" blocks** on **Home + end of event-detail** (the warm "just rode / believer" moments), **and** the persistent footer CTA. All asks route to `/steun-ons`. Supersedes the earlier quiet-footer-only call ([PAT-10](40-patterns.md); [org-goals trade-off](../strategy/10-organisation-goals.md)).
- The t-shirt is a **thank-you/token**, not merchandise — no separate web store. Prefer recurring over grant dossiers.

**Accounts — volunteers only (clarified 2026-06-02)**
- **A site account = a volunteer** (`group_user`, belongs to **one or more** chapters — `group_user` is many-to-many; already modelled). **Families have no account.** A **spacefunding member** pays externally via Growfunding and gets **no** site account — distinct concept; a person can be both, neither implies the other.
- Being a logged-in *volunteer* (not paying status) gates: **the back-office and the volunteer roster view** (meetup *viewing* is public — see Activities).
- A volunteer gets a personal **[My activities](30-skeleton/my-activities.md)** view: upcoming rides/meetups for **my chapter(s)** (the "attending" list is removed — attendance cut, D-1).

**Volunteer roster (validated 2026-06-05, Alexandre/J3 — replaces attendance as the social feature)**
- A standing **per-chapter** roster, **not** per-event. A volunteer can **opt in** to appear **publicly** on their chapter page; the **full roster of other volunteers in the chapter** is visible to **logged-in volunteers only**. Answers Morgane's "I don't know who else is in my group" gap.
- **Build implication for Nico:** backed by the existing **`group_user` pivot** + an **opt-in public-visibility boolean**. **Do not build an `Attendance` (volunteer ↔ activity) relation.** See [content model](20-structure.md).

**Private organiser back-office (validated — D-1; content brief concrete since Alexandre/J3 2026-06-05)**
- A per-chapter **material library** in three layers: (1) *before signing up* — what to expect as a volunteer; (2) *once logged in (pink vest)* — how it works, documents, a video, the meetup schedule, who leads the chapter + their role, what roles exist and what yours is/could be (the things now living in WhatsApp), plus a **growth path** toward deeper contribution; (3) **NEW — new-chapter / organiser onboarding** — how to start and run a chapter (commune contact, route planning, promo/poster downloads). **Primary beneficiary = new/small chapters + organiser onboarding.** Reached from the chapter page and [My activities](30-skeleton/my-activities.md).
- **Posters/promo are stored & distributed as downloads here — not generated** (poster auto-generation stays a confirmed cut).

**Bilingual routing**
- NL + FR — routed, not stacked (v1). English deferred to a later phase.

**Map of chapters**
- Shows national reach across Belgium
- Supports movement growth story for grants and new chapter leads

**Sponsor section**
- Basic display of active sponsors/partners
- Full tracking of sponsor obligations (tiers, logo placement, contract status) is deferred

### Content Requirements

**National pages:** Home, What is Kidical Mass / how it works, Events / calendar, Chapter overview + map, Getting Started, Help Out, News, Sponsors / partners, **Steun Kidical Mass / spacefunding** (`/steun-ons`). Mission/Vision/Organisation: shorten and audience-segment — keep internal-org detail off the public site (interview 2026-05-18).

**Chapter pages (per chapter):** Local schedule (public rides), Team, Local partners, Press coverage, Downloads, + the chapter's meetings/workshops (publicly visible)

**Event pages (per event):** All practical details a family needs to show up

### Out of Scope for MVP

- Photo-gallery system (confirmed cut, interview 2026-05-18 — a few inline photos per chapter only; rides go to social media)
- Public web store (confirmed cut — the t-shirt is the membership; buy via spacefunding)
- Poster / flyer auto-generation (confirmed cut — too much to build; layout stays in InDesign with interns)
- Worked-out "start a chapter" intake flow (static page only — challenge is participants, not new groups)
- Automated photo tagging (deferred)
- Full sponsor obligation tracking (deferred)
- **Per-event attendance / "who's coming" — confirmed cut** (Alexandre/J3, 2026-06-05): volunteers manage turnout via WhatsApp polls; no site signal needed. The minimal per-chapter **back-office is IN scope (validated)** and the social need is met by the standing **volunteer roster** (not attendance). See [design concern D-1](01-concerns.md) and the Volunteer roster / back-office sections above.

---

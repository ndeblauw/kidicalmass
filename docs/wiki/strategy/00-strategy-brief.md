---
title: Strategy Brief — Kidical Mass Belgium website
tags: [strategy, front-door]
sources: [notion, wiki/strategy/90-key-decisions-evidence, wiki/design/10-scope, wiki/site-audit]
phase: strategy
updated: 2026-06-01
---

# Strategy Brief

The small set of **locked decisions** everything downstream rests on. If one of these changes, the design and build cascade from it. Open items are not here — they live in [`01-concerns.md`](01-concerns.md).

> **The one-line brief (Leticia's own framing):**
> *"The coordination duo should be able to take two weeks' holiday and the site keeps running."*
> Not a prettier website, not more features — a platform where chapters run themselves.

## The reframe everything follows from

**The parade is the product. The website is infrastructure** to help people *find* a ride, *join* it, and *run* it. The site today fails at finding and running. (Source: service-design synthesis; strategie-plan, Notion.)

## Locked decisions

| # | Decision | Locked because | Date |
|---|---|---|---|
| **D1** | **Chapter autonomy is the core requirement.** Chapters self-publish their rides and manage their own page; nothing routes through Brussels. | The calendar is a single point of failure (60+ events/yr, all manual via the duo). Removing the bottleneck *is* the project. | 2026-04 |
| **D2** | **The site is canonical for ride detail; Facebook is distribution.** The site is the link Facebook points to. | Resolves the long-standing FB-vs-site question. FB stays for reach + as Leticia's turnout signal. | 2026-05-18 |
| **D3** | **Quality is guaranteed by templates, not by a person.** Strict design constraints + fixed templates replace Leticia's manual sign-off. | The key tension: brand consistency without the duo being the bottleneck. | 2026-05-18 |
| **D4** | **Primary audiences are families and potential volunteers.** Potential *chapter leads* are secondary. | The challenge is more participants in existing chapters (~15–20 already), not more groups. | 2026-05-18 |
| **D5** | **Bringing money in is the top organisational objective.** Recurring individual membership ("spacefunding") + persistent site-wide CTA. | Recurring individual donors > grant dossiers; lets KM hire and stay self-sufficient. | 2026-05-18 |
| **D6** | **v1 is bilingual NL + FR, routed (not stacked).** English deferred — same structure, added when review capacity allows. | *"Doe maar twee talen."* The bottleneck is review, not generation. | 2026-05-18 |
| **D7** | **Positioning stays light and broad — mildly activist, never hardcore-cyclist.** | Appeal to the mass, not a cycling niche; the ride makes the political point. | 2026-05-18 |
| **D8** | **One `Activity` model; all types publicly viewable.** Rides *and* meetups/workshops are visible to anyone — meetups are public too, as a traction/recruitment signal *(rev. 2026-06-02)*. Login gates **attendance + the back-office**, not viewing. | One system; public meetups show the movement's momentum. | 2026-05-18 |
| **D9** | **A group-volunteer account ≠ a spacefunding member.** A person can be both; neither implies the other. **Attendance + back-office** depend on being a logged-in volunteer, not on paying (viewing is public). | Keeps funding status and volunteer access cleanly separate. | 2026-05-18 |

## Confirmed scope cuts (won't-haves)

Photo-gallery system · public web store · poster/flyer auto-generation · worked-out "start a chapter" intake flow. *(These are decisions, not deferrals.)* Full detail: [design/10-scope.md § Out of Scope](../design/10-scope.md).

## Downstream artifacts

[Organisation goals](10-organisation-goals.md) · [Personas](20-personas.md) · [Jobs-to-be-done](30-jobs-to-be-done.md) · [Value proposition](40-value-proposition.md) · [User journeys](50-user-journeys.md) · register: [01-concerns.md](01-concerns.md).

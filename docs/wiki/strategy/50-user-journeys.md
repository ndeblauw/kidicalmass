---
title: User Journeys
tags: [strategy]
sources: [notion]
phase: strategy
updated: 2026-06-01
---

# User Journeys

Three journeys must work from day one. For each: the staged arc, the **make-or-break moment**, and how it fails today. These constrain the [scope](../design/10-scope.md) and [skeleton](../design/30-skeleton/00-page-registry.md).

## J1 — A family finds a ride *(P1/P2)*

```
Hears about KM → searches their municipality / opens Events
   → sees next date, time, meeting point, distance, age-suitability
   → decides to come
```
- **Make-or-break:** the moment the family looks for *when & where near me*. If the site answers it, they come; if not, they leave.
- **Today:** fails. Text-only date list, no times/locations → click through to Facebook → dead end for non-FB users.
- **Owns:** Home + Events + Event detail.

## J2 — Someone becomes a volunteer *(P3 → P4)*

```
Wants to help → picks a role (pink vest / organiser / photographer …)
   → picks a chapter → fills a form
   → routed to the local lead → gets a real reply
```
- **Make-or-break:** the hand-off — does a *real local person* actually respond? *(Post-onboarding friction is a design hypothesis fed by volunteer-interview signal — see [D-1](../design/01-concerns.md) — not asserted here.)*
- **Today:** fails. One button: "email bike@". No form, no role, no routing → the "email black hole".
- **Owns:** Help out + per-chapter contact form (routing) + a post-signup explainer email.

## J3 — A chapter lead publishes a ride *(P5)*

```
Logs in → creates an event
   → it appears automatically on the chapter page AND the national calendar
```
- **Make-or-break:** publishing **without emailing Brussels**. This is the entire reframe (D1) made concrete.
- **Today:** not possible. Send an email to Brussels and wait; the duo hand-edits Wix.
- **Owns:** admin (Filament) + chapter page + Activities → national calendar aggregation.
- **⚠️ Evidence gap:** two volunteer interviews are done (both pink-vests, not chapter leads), so J3 — self-publishing — is still **untested**; Alexandre (Schaerbeek) is the planned chapter-lead interview. See [`D-1`](../design/01-concerns.md). **The journey most at risk of being designed on assumption.**

## The current service map (for contrast)

A family today touches the site for ~one step (the date list) and that step is done poorly; Facebook does the heavy lifting, WhatsApp runs the community. The journeys above are the inversion of that. *(Full as-is map: service-design synthesis, Notion.)*

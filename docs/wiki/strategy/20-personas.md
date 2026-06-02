---
title: Personas / Audiences
tags: [strategy]
sources: [notion]
phase: strategy
updated: 2026-06-01
---

# Personas / Audiences

Six actors interact with the service. They have almost nothing in common except showing up on the same Sunday. Below, each is defined with an **anti-overlap contract** — the one thing that makes it distinct, so we never blur two of them in design.

> **Site priority (D4):** **Families** and **potential volunteers** are the primary *public-site* audiences. Chapter leads are served through the **logged-in/admin** experience, not the public site. The coordination duo is served through **admin**.

## Primary (public site)

### P1 — The Family, first-timer
*"Can my 5-year-old do it? When's the next one near me?"*
- **Needs (ranked):** next ride near me → date, time, meeting point; then *is it suitable for us?* (age, distance, pace); then *what if we don't have a bike?*
- **Make-or-break:** the site must answer the three questions they arrived with. Today it doesn't — they bounce to Facebook, or hit a dead end without it.
- **Anti-overlap:** *arrives not knowing how it works.* (vs P2 who does.)

### P2 — The Family, regular
*"We go every month. Schaerbeek is our chapter."*
- **Needs:** next date, any theme/special programme; a **shareable standalone event page** that isn't Facebook.
- **Anti-overlap:** *already knows how it works; low friction but Facebook-dependent.* (vs P1.)

### P3 — Potential volunteer
*"I'd like to help but I don't know how or where I fit."*
- **Needs:** a clear path from curiosity → pick a role → pick a chapter → contact, routed to the right local lead (not a central inbox).
- **Anti-overlap:** *wants to contribute but is not yet committed or trained.* (vs P4 who is active.)

## Secondary

### Sponsors / partners
- Get a dedicated section, not a deeply designed experience. (Logo visibility is a contractual obligation — see structural problem #5 in discovery.)

### Press
- Not a primary audience; no specific design beyond an `/about/press` section.

### Potential chapter leads
- **Demoted to secondary (D4).** The chapter map + growth story still serve them and grant applications, but recruiting new leads is not a primary site job. Start-a-chapter is a static page for MVP.

## Served elsewhere (not the public site)

### P4 — The pink vest (active volunteer)
*"I escort rides in Forest. I got trained last season."*
- Recruited by phone/WhatsApp at a ride, coordinates via WhatsApp, **barely uses the website** after first contact. *(Volunteer-interview signal — unvalidated — points to post-onboarding friction: role clarity, briefing, scattered WhatsApp info. This feeds design concern [D-1](../design/01-concerns.md); it is not a strategy claim.)*
- **Anti-overlap:** *already in; the site plays almost no role in their ongoing experience.* (vs P3.)

### P5 — The chapter lead (local organiser)
*"I run the Mons rides. I plan on Komoot, post on Facebook, show up."*
- 4–10 rides/yr per municipality; today completely dependent on Brussels to appear on the national site. Some chapters (Liège, Mons) built their own sites/domains to fill the vacuum.
- Served through the **logged-in/admin** experience. The platform must make a hosted chapter page more attractive than a separate domain.
- **Anti-overlap:** *publishes and runs a chapter; the only actor with CMS responsibility.*

### P6 — The coordination duo (Leticia & Cecilia)
*"We hold the whole thing together — comms, sponsors, training, website, press, WhatsApp, grants."*
- Two people doing a small comms team's work. **The site currently creates work rather than reducing it.** Served through **admin** with full cross-chapter access.
- **Anti-overlap:** *national owners; the platform's job is to give them their time back.*

## Why anti-overlap matters

P1↔P2 and P3↔P4 are the easy confusions. Designing the Events page for P2 (regulars who know the drill) would strand P1 (first-timers). Designing Help out for P4 (already-active) would strand P3 (the people we're trying to convert). Each design decision should name which actor it serves.

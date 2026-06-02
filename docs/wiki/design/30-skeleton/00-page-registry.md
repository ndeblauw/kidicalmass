---
title: Skeleton — page registry & build tracker
tags: [design, skeleton]
sources: [wiki/design/20-structure, wiki/design/30-skeleton]
phase: design
updated: 2026-06-01
---

# Skeleton — page registry & build tracker

Every page/route, its brief, and its honest pipeline status. **Lifecycle:** `stub` → `spec` (brief approved) → `built` (coded) → `live` (renders + wired, verified). Mark `live` **only when verified in the running app**, never just because it's coded.

> Build column is **not yet verified** — Nico has built data models (`Activity`, `Article`, `group_user`) but per-page render status hasn't been confirmed against the running app. Treat build/lifecycle as **provisional until checked with Nico** (use the app + `database-schema`).

| Route | Page | Spec | Content | Build | Lifecycle | Notes |
|---|---|---|---|---|---|---|
| `/` | Home | [home.md](home.md) | [home-content.md](home-content.md) | ❓ | spec | Hero wants video (asset gap). |
| `/events` | Events overview | [events-overview.md](events-overview.md) | [events-overview-content.md](events-overview-content.md) | ❓ | spec | Replaces `/agenda`; redirect-critical ([D-7](../01-concerns.md)). |
| `/events/[slug]` | Event detail | [activity-detail.md](activity-detail.md) | [activity-detail-content.md](activity-detail-content.md) | ❓ | spec | Komoot embed; per-event partners. |
| `/membership` | Membership / spacefunding | [membership.md](membership.md) | — | ❓ | spec | Links out to Growfunding; lead €3/mo entry. |
| `/contact` | Contact (national) | — | — | ❓ | **stub** | New front door → coordination duo; press/partnership/general buckets. |
| `/privacy` · `/cookies` | Legal / GDPR | — | — | ❓ | **stub** | Mandatory (enquiry form + email opt-in). Footer cluster. |
| `/login` | Volunteer login | — | — | ❓ | **stub** | Login only — no public Register (invite-only accounts). |
| `/my-activities` | My activities | [my-activities.md](my-activities.md) | — | ❓ | spec (provisional) | Volunteers only; post-login landing; depends on [D-1/D-2](../01-concerns.md). |
| `/backstage/[postal-code]` | Chapter back-office | — | — | ❓ | **stub** (provisional) | **Separate branded frontend surface, not Filament (decided)**; per-chapter materials + attendance. Content brief pending [D-1](../01-concerns.md)/Alexandre. |
| `/chapters` | Chapters overview | [chapters.md](chapters.md) | [chapters-content.md](chapters-content.md) | ❓ | spec | Map + list; PAT-8. |
| `/chapters/[postal-code]` | Chapter page (template) | [chapters.md](chapters.md) | [chapters-content.md](chapters-content.md) | ❓ | spec | Self-published; hide-if-empty sections (PAT-11); redirect-critical. |
| `/getting-started` | Getting Started | [getting-started.md](getting-started.md) | [getting-started-content.md](getting-started-content.md) | ❓ | spec | New page; no current-site equivalent. |
| `/help-out` | Help out | [help-out.md](help-out.md) | [help-out-content.md](help-out-content.md) | ❓ | spec | Routed form (PAT-6); 4/5 role illustrations missing. |
| `/about` | About overview | [about.md](about.md) | [about-content.md](about-content.md) | ❓ | spec | Sub-page nav cards (PAT-15). |
| `/about/mission` | Mission | [about.md](about.md) | [about-content.md](about-content.md) | ❓ | spec | New full spec; live stats (PAT-4). |
| `/about/vision` | Vision | [about.md](about.md) | [about-content.md](about-content.md) | ❓ | spec | Merge of what-we-want + revendications. |
| `/about/organisation` | Organisation | [about.md](about.md) | [about-content.md](about-content.md) | ❓ | spec | Accessible organigram. |
| `/about/news` (+ `/[slug]`) | News | [about.md](about.md) | [about-content.md](about-content.md) | ❓ | spec | `Article` model exists. |
| `/about/press` | Press | [about.md](about.md) | [about-content.md](about-content.md) | ❓ | spec | National + local (dual-homed). |
| `/about/partners` | Partners | [about.md](about.md) | [about-content.md](about-content.md) | ❓ | spec | Logo display (PAT-5). |
| `/admin` | Filament panel | — | — | partial | — | Coordination duo = all; chapter lead = own. Built atop existing models. |

## Summary

- **Specs:** all public routes now have briefs, including **Membership** and **My activities** (the latter provisional, pending `D-1`/`D-2`). The one remaining stub is the per-chapter **back-office** (provisional, pending `D-1`).
- **Build/lifecycle:** unverified across the board — needs a pass against the running app before anything is marked `live`.
- **Proposed build order** (from migration): Events → Chapters → Help out → Getting Started → About → Home. Validate with Nico ([25-content-migration.md](../25-content-migration.md)).

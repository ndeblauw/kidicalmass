---
title: Skeleton — page registry & build pipeline
tags: [design, skeleton, build]
sources: [wiki/design/20-structure, wiki/design/30-skeleton]
phase: design
updated: 2026-06-02
---

# Skeleton — page registry & build pipeline

Every page/route as one row, with its **honest pipeline status**. This table is the single source of truth; the read-only **`/build`** dashboard (non-prod, unlinked) parses it live. Update procedure + honesty gate live in [`CLAUDE.md`](../../../../CLAUDE.md) → "Updating the build pipeline" (or run `/pipeline`).

**Phases:** `UX` briefing · `Conf` content-confidence (1–5) · `Wire` wireframe (content + hierarchy, no style) · `Assets` media in · `UI` visual style · `Back` backend/CMS wired & verified · `OK` client sign-off.
**Status:** 🔴 niet begonnen · 🟠 bezig · 🟢 goed · ⚪ n.v.t. · ❓ te beslissen. `OK` is binary (🔴/🟢).

> **Initial seed (2026-06-02) — provisional.** Stages below are an honest first pass, not yet verified row-by-row with Nico against the running app. The six model-backed views (Home, Events, Event detail, Chapters, Chapter page, News) are **rebuilt + rebranded** → `Wire`/`UI` 🟠 (never 🟢 until Frederik's own critique + refine pass). `Back` 🟠 = models exist, wiring unverified live. `Conf` is seeded low where client material is still pending. Correct rows as reality is confirmed.

| ID | Page | Slug | Type | UX | Conf | Wire | Assets | UI | Back | OK | Top gaps |
|---|---|---|---|---|---|---|---|---|---|---|---|
| P-01 | **Home** | `/` | Conv | 🟢 | 3 | 🟠 | 🔴 | 🟠 | 🟠 | 🔴 | `[asset]` hero video/photo (gap); `[content]` live event strip + movement stats (PAT-4) from models. Rebuilt + rebranded — awaiting Frederik critique before Wire 🟢. |
| P-02 | **Events overview** | `/events` | Index | 🟢 | 3 | 🟠 | 🔴 | 🟠 | 🟠 | 🔴 | `[content]` replaces `/agenda`, redirect-critical (D-7); list + upcoming/past (PAT-12) from Activity model. Rebuilt + rebranded — awaiting critique. |
| P-03 | **Event detail** | `/events/[slug]` | Detail | 🟢 | 3 | 🟠 | 🔴 | 🟠 | 🟠 | 🔴 | `[asset]` Komoot embed; `[content]` per-event partners (PAT-5); "I'm coming" + hosts display (PAT-18, D-1). Rebuilt + rebranded — awaiting critique. |
| P-04 | **Membership** | `/membership` | Conv | 🟢 | 2 | 🔴 | 🔴 | 🔴 | ❓ | 🔴 | `[content]` Growfunding link-out + €3/mo lead entry. Brief only; route stubbed, view not built. |
| P-05 | **Contact (national)** | `/contact` | Utility | 🔴 | 1 | 🔴 | ⚪ | 🔴 | ❓ | 🔴 | `[strategy]` new national front door → coordination duo; press/partnership/general buckets. Route stubbed; no brief yet. |
| P-06 | **Legal / GDPR** | `/privacy` | Utility | 🔴 | 1 | 🔴 | ⚪ | 🔴 | ❓ | 🔴 | `[research]` `/privacy` + `/cookies` mandatory (enquiry form + email opt-in); footer cluster. Routes stubbed. |
| P-07 | **Volunteer login** | `/login` | Utility | 🔴 | 1 | 🔴 | ⚪ | 🔴 | 🟠 | 🔴 | `[client]` login-only — no public register (invite-only). Fortify route + backend exist; no branded view. |
| P-08 | **My activities** | `/my-activities` | App | 🟠 | 2 | 🔴 | ⚪ | 🔴 | ❓ | 🔴 | `[strategy]` volunteers-only post-login landing; depends on D-1/D-2 (attendance). Provisional spec; not routed. |
| P-09 | **Chapter back-office** | `/backstage/[postal-code]` | App | 🔴 | 1 | 🔴 | 🔴 | 🔴 | ❓ | 🔴 | `[client]` separate branded frontend (not Filament, decided); per-chapter materials + attendance. Brief pending D-1/Alexandre. |
| P-10 | **Chapters overview** | `/chapters` | Index | 🟢 | 3 | 🟠 | 🔴 | 🟠 | 🟠 | 🔴 | `[content]` Belgium map + list (PAT-8); "start a chapter" CTA. Rebuilt + rebranded — awaiting critique. |
| P-11 | **Chapter page** | `/chapters/[postal-code]` | Template | 🟢 | 3 | 🟠 | 🔴 | 🟠 | 🟠 | 🔴 | `[content]` self-published, hide-if-empty sections (PAT-11); redirect-critical. Rebuilt + rebranded — awaiting critique. |
| P-12 | **Getting Started** | `/getting-started` | Info | 🟢 | 3 | 🔴 | 🔴 | 🔴 | ⚪ | 🔴 | `[content]` new page, no current-site equivalent; practical FAQ (PAT-14). Brief + content drafted; route stubbed, view not built. |
| P-13 | **Help out** | `/help-out` | Conv | 🟢 | 3 | 🔴 | 🟠 | 🔴 | ❓ | 🔴 | `[asset]` 4/5 role illustrations missing (PAT-7); `[content]` routed contact form (PAT-6, J2 hand-off). Route renders thin volunteer signup, not the full specced page. |
| P-14 | **About overview** | `/about` | Hub | 🟢 | 2 | 🔴 | 🔴 | 🔴 | ⚪ | 🔴 | `[content]` sub-page nav cards (PAT-15). Brief drafted; route stubbed, view not built. |
| P-15 | **Mission** | `/about/mission` | Story | 🟢 | 3 | 🔴 | 🔴 | 🔴 | ❓ | 🔴 | `[content]` 3 axes + inclusivity; `[content]` live stats (PAT-4). Brief drafted; view not built. |
| P-16 | **Vision** | `/about/vision` | Story | 🟢 | 2 | 🔴 | 🔴 | 🔴 | ⚪ | 🔴 | `[content]` policy demands + Child-Friendly-City manifesto (merged). Brief drafted; view not built. |
| P-17 | **Organisation** | `/about/organisation` | Info | 🟢 | 2 | 🔴 | 🔴 | 🔴 | ⚪ | 🔴 | `[content]` accessible organigram; governance + coordination duo. Brief drafted; view not built. |
| P-18 | **News** | `/about/news` | Index | 🟢 | 2 | 🟠 | 🔴 | 🟠 | 🟠 | 🔴 | `[content]` Article model exists; feed + preview (PAT-17). Rebuilt + rebranded — awaiting critique. |
| P-19 | **Press** | `/about/press` | Info | 🟢 | 2 | 🔴 | 🔴 | 🔴 | ⚪ | 🔴 | `[content]` national + local (dual-homed). Brief drafted; view not built. |
| P-20 | **Partners** | `/about/partners` | Info | 🟢 | 2 | 🔴 | 🟠 | 🔴 | 🟠 | 🔴 | `[asset]` partner logos (PAT-5); national vs local data. Partner model + admin exist; view not built. |
| P-21 | **Admin (Filament)** | `/admin` | Admin | ⚪ | — | ⚪ | ⚪ | ⚪ | 🟢 | ⚪ | Filament panel — coordination duo = all, chapter lead = own. Runs atop existing models. Exempt from the public pipeline. |

## Roll-up

- **Pages:** 21 rows (`P-01`–`P-21`). **UX:** 16 🟢 + 1 🟠 (My activities) + 4 🔴 (stubs: Contact, Legal, Login, Back-office). **Avg content-confidence ≈ 2.3 / 5** — client material still pending across most pages.
- **Built + rebranded (Wire/UI 🟠):** Home, Events, Event detail, Chapters, Chapter page, News — the six model-backed views. **Wire 🟢 is gated on Frederik's critique** — no page is 🟢 yet.
- **Back:** 🟠 (in progress, unverified) on the six model-backed pages + Login (Fortify) + Partners; ❓ where need/shape is open (Membership, Contact, Legal, My activities, Back-office, Help-out, Mission); ⚪ on the static About leaves + Getting Started.
- **Routed but not built:** the static About leaves, Getting Started, Membership, Contact, Legal views are routed (`routes/web.php`, `{locale}` prefix) but the Blade views don't exist yet — rows stay Wire 🔴.
- **Proposed build order** (from migration): Events → Chapters → Help out → Getting Started → About → Home. Validate with Nico ([25-content-migration.md](../25-content-migration.md)).

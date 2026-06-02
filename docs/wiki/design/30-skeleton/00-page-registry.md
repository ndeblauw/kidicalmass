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
| P-04 | **Steun Kidical Mass** | `/steun-ons` | Conv | 🟢 | 2 | 🔴 | 🔴 | 🔴 | ❓ | 🔴 | `[content]` Growfunding link-out + €3/mo lead entry; nav CTA + contextual blocks (Home, event-detail) + footer all → here; one-off path pending Leticia (D-9). Brief reworked 2026-06-02 ("steun" not "lid"; prominence elevated). Route still stubbed at `/membership`; view not built. |
| P-05 | **Contact (national)** | `/contact` | Utility | 🔴 | 1 | 🔴 | ⚪ | 🔴 | ❓ | 🔴 | `[strategy]` new national front door → coordination duo; press/partnership/general buckets. Route stubbed; no brief yet. |
| P-06 | **Legal / GDPR** | `/privacy` | Utility | 🔴 | 1 | 🔴 | ⚪ | 🔴 | ❓ | 🔴 | `[research]` legal text pending (enquiry form + email opt-in). **Folded to one page 2026-06-02** — privacy + cookies are sections of `/privacy`; `/cookies` 301s here. View still a stub. |
| P-07 | **Volunteer login** | `/login` | Utility | 🔴 | 1 | 🔴 | ⚪ | 🔴 | 🟠 | 🔴 | `[client]` login-only — no public register (invite-only). Fortify route + backend exist; no branded view. |
| P-08 | **My activities** | `/my-activities` | App | 🟠 | 2 | 🔴 | ⚪ | 🔴 | ❓ | 🔴 | `[strategy]` volunteers-only post-login landing; depends on D-1/D-2 (attendance). Provisional spec; not routed. |
| P-09 | **Chapter back-office** | `/backstage/[postal-code]` | App | 🔴 | 1 | 🔴 | 🔴 | 🔴 | ❓ | 🔴 | `[client]` separate branded frontend (not Filament, decided); per-chapter materials + attendance. Brief pending D-1/Alexandre. |
| P-10 | **Chapters overview** | `/chapters` | Index | 🟢 | 3 | 🟠 | 🔴 | 🟠 | 🟠 | 🔴 | `[content]` Belgium map + list (PAT-8); "start a chapter" CTA. Rebuilt + rebranded — awaiting critique. |
| P-11 | **Chapter page** | `/chapters/[postal-code]` | Template | 🟢 | 3 | 🟠 | 🔴 | 🟠 | 🟠 | 🔴 | `[content]` self-published, hide-if-empty sections (PAT-11); redirect-critical. Rebuilt + rebranded. **J2 volunteer form built** 2026-06-02 (`ChapterVolunteerSignup`: name/email/roles/message → enquiry, `#aanmelden` + `?intent` welcome, next-ride confirmation). `[backend]` per-lead routing pending (no per-group lead email); page still **EN** (NL pass needed). Awaiting critique. |
| P-12 | **Getting Started** | `/getting-started` | Info | 🟢 | 4 | 🟠 | ⚪ | 🟠 | ⚪ | 🔴 | `[content]` NL wireframe built + **distilled** 2026-06-02 (Frederik critique): CTA lifted after FAQ; "Andere manieren" removed; **"Geen fiets?" folded to a FAQ link → Find a bike (P-22)**; safety FAQ + marshal card added from the volunteer ROI/Jorge interview. **Surface pass 2026-06-02 (Frederik-guided, aligned to the ride/show page):** blue full-bleed hero reusing `.activity-hero*` (daisy, circular illustration, sky badge, -3° white headline); "wat je mag verwachten" reuses `.activity-promises*` verbatim (sky band, white tilted cards, red Flux-icon chips); accordion FAQ; full-bleed yellow CTA band. Awaiting Frederik critique before Wire/UI 🟢. |
| P-13 | **Help out** | `/help-out` | Conv | 🟢 | 3 | 🟠 | 🟢 | 🟠 | ⚪ | 🔴 | NL orientation page, **Surface pass re-skinned to the ride/show kit 2026-06-02** (critique: too cool, off-system): reuses `.activity-hero*` (blue, real volunteer **photo**, daisy, "Doe mee" badge) + `.activity-promises*` for roles (yellow band, white tilted cards, **red Flux-icon chips** — emoji gone), a **real-photo joy band**, group-picker climax (light-blue band), quiet coda. Copy warmed up. J2 reframe intact (form on P-11; CTA = group picker → `?intent=volunteer#aanmelden`). Photos in `public/img/volunteers/`. Awaiting Frederik critique before Wire/UI 🟢. |
| P-14 | **About overview** | `/about` | Hub | 🟢 | 2 | 🔴 | 🔴 | 🔴 | ⚪ | 🔴 | `[content]` sub-page nav cards (PAT-15). Brief drafted; route stubbed, view not built. |
| P-15 | **Mission** | `/about/mission` | Story | 🟢 | 3 | 🔴 | 🔴 | 🔴 | ❓ | 🔴 | `[content]` 3 axes + inclusivity; `[content]` live stats (PAT-4). Brief drafted; view not built. |
| P-16 | **Vision** | `/about/vision` | Story | 🟢 | 2 | 🔴 | 🔴 | 🔴 | ⚪ | 🔴 | `[content]` policy demands + Child-Friendly-City manifesto (merged). Brief drafted; view not built. |
| P-17 | **Organisation** | `/about/organisation` | Info | 🟢 | 2 | 🔴 | 🔴 | 🔴 | ⚪ | 🔴 | `[content]` accessible organigram; governance + coordination duo. Brief drafted; view not built. |
| P-18 | **News** | `/about/news` | Index | 🟢 | 2 | 🟠 | 🔴 | 🟠 | 🟠 | 🔴 | `[content]` Article model exists; feed + preview (PAT-17). Rebuilt + rebranded — awaiting critique. |
| P-19 | **Press** | `/about/press` | Info | 🟢 | 2 | 🔴 | 🔴 | 🔴 | ⚪ | 🔴 | `[content]` national + local (dual-homed). Brief drafted; view not built. |
| P-20 | **Partners** | `/about/partners` | Info | 🟢 | 2 | 🔴 | 🟠 | 🔴 | 🟠 | 🔴 | `[asset]` partner logos (PAT-5); national vs local data. Partner model + admin exist; view not built. |
| P-21 | **Admin (Filament)** | `/admin` | Admin | ⚪ | — | ⚪ | ⚪ | ⚪ | 🟢 | ⚪ | Filament panel — coordination duo = all, chapter lead = own. Runs atop existing models. Exempt from the public pipeline. |
| P-22 | **Find a bike** | `/find-a-bike` | Info | 🟢 | 4 | 🟠 | ⚪ | 🔴 | ⚪ | 🔴 | `[content]` no-bike providers lifted off Getting Started 2026-06-02 (Kidical Mouse, Loopz, Fietsbieb, My Kids Bikes, Cyclo); facts verified. Standalone resource, reached from the Getting Started FAQ — **not in main nav**; reusable from event/chapter pages. `[client]` Kidical Mouse availability open. Awaiting UI pass. |

## Roll-up

- **Pages:** 22 rows (`P-01`–`P-22`). **UX:** 17 🟢 + 1 🟠 (My activities) + 4 🔴 (stubs: Contact, Legal, Login, Back-office). **Avg content-confidence ≈ 2.3 / 5** — client material still pending across most pages.
- **Built + rebranded (Wire/UI 🟠):** Home, Events, Event detail, Chapters, Chapter page, News — the six model-backed views. **Wire 🟢 is gated on Frederik's critique** — no page is 🟢 yet.
- **Surface pass done (Wire 🟠, UI 🟠):** **Getting Started** and **Help out** — both built on the ride/show kit (`.activity-hero*` / `.activity-promises*`, full-bleed colour bands, white tilted cards, red Flux-icon chips). Help out adds real volunteer photography (hero + joy band) and the group-picker climax. Wire/UI 🟢 still gated on Frederik's critique.
- **Built at wireframe fidelity (Wire 🟠, UI 🔴):** Find a bike (the no-bike resource offshoot) and **Help out** (J2 orientation reframe — motivates + routes to the chapter; the form lives on P-11) — NL structural views, content verified. Surface/UI pass still pending.
- **Back:** 🟠 (in progress, unverified) on the six model-backed pages + Login (Fortify) + Partners; ❓ where need/shape is open (Membership, Contact, Legal, My activities, Back-office, Mission); ⚪ on the static About leaves + Getting Started + Find a bike + Help out (now static — its routed form lives on the chapter page).
- **Routed but not built:** the static About leaves, Membership, Contact, Legal views are routed (`routes/web.php`, `{locale}` prefix) but the Blade views are still stubs — rows stay Wire 🔴. (Getting Started is now built.) Legal is now **one page** — `/cookies` 301s to `/privacy` (folded 2026-06-02).
- **Global shell (not a P-row):** the public **footer + partners block** are now NL-localised via `lang/nl/footer.php` + `lang/nl/partners.php` and the **fat-footer menu is rebuilt to mirror the IA** — *Ontdek* (main nav), *Over ons* (About sub-nav), persistent *Word lid* CTA → `/membership`, and a utilities bottom bar (Contact · Privacy & cookies · Inloggen). Dead `href="#"` links wired; social reduced to Instagram + Facebook (IG `kidicalmass.belgium`, FB `Kidicalmass.brussels`). **Planned rework (design 2026-06-02, not yet built):** relabel the footer CTA to **Steun Kidical Mass** → `/steun-ons`, add a **"Steun" nav CTA** top-right (replacing login → login stays footer-only), and **contextual "Steun" blocks** on Home + event-detail; "steun" replaces "lid" sitewide. See P-04 + [`10-scope.md` § Support](../10-scope.md).
- **Proposed build order** (from migration): Events → Chapters → Help out → Getting Started → About → Home. Validate with Nico ([25-content-migration.md](../25-content-migration.md)).

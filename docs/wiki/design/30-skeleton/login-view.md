---
title: Handoff — Branded login view (P-07)
tags: [design, skeleton, build, handoff]
sources: [00-page-registry.md, ../../log.md]
phase: design
updated: 2026-07-07
---

# Handoff — Branded login view (P-07)

**Start a fresh thread with this brief.** The self-contained "tof apart projectje" from the 2026-07-07 review. Frederik, verbatim:

> "Die login-pagina moet nog gestyled worden in de stijl van Kidical Mass. Dat is een tof apart projectje, dus dat is iets simpels dat nog moet gebeuren eigenlijk. En dat moet natuurlijk ook in het Nederlands staan."

## What exists

- **Backend is done and stays untouched:** Fortify auth with roles (superadmin/pinkvest/captain), `LocalGroupScope`, role-based `LoginResponse` redirect (post-login lands on the member's chapter page / roze-hub), dev login shortcuts (`login/as/{role}`, non-prod). Back 🟢, tested.
- **Views:** `resources/views/livewire/auth/login.blade.php` is the live one (Fortify/Livewire starter with Flux components — likely still default-styled and EN). Siblings in the same folder: `forgot-password`, `reset-password`, `confirm-password`, `two-factor-challenge`, `verify-email` — style at least login + forgot/reset for a coherent flow; `register` is disabled (invite-only, no public register).
- **Audience & register:** this is the *volunteer* door, not a public page — warm and welcoming ("welkom terug, roze hesje"?) but functional. NL only. Login is out of the public nav; people arrive via direct link/invite.

## Current pipeline row

`P-07 · UX 🟢 · Conf 4 · Wire 🟠 · Assets ❓ · UI 🟠 · Back 🟢 · CMS ⚪ · OK 🔴`

**Assets ❓ is a real open decision:** does the page get an illustration/photo (e.g. the mascot, a roze-hesje photo) or stays it typographic? Decide in the thread with Frederik — one AskUserQuestion, then set Assets 🟢 or ⚪.

## Constraints

- Flux form components are fine here (`flux:button`, inputs) — but headings raw `<h1>` per the frontend rules; auth pages still live on the public token set (`bg-kidical-*`, `rounded-card`, …).
- New CSS (if any beyond utilities in the blade) → `resources/css/pages/` partial, registered; never `app.css`.
- Copy: NL, tone-of-voice (warm, inclusive; this is the volunteers' door). Also translate validation-adjacent strings shown on these views. No em-dashes.
- Fortify skill: activate `fortify-development` when touching auth views/flows. Don't add features (no register, no socials) — YAGNI.
- Tests: `tests/Feature/Auth/` exists; a styling pass should not break them. If you add seams, `data-*` only. Keep the thin-Auth-tests backlog note in mind — don't expand coverage beyond behaviour.

## Suggested flow

1. Quick brainstorm: one direction is probably enough for this size (login is utility) — but show Frederik a render before calling it done.
2. Style login + password-flow siblings; NL copy pass.
3. Verify the full flow live (login as each demo role via `login/as/{role}`), one screenshot pass.
4. `/pipeline P-07 wire=good ui=wip` → then Frederik's own critique in `/build/review` flips Wire/UI 🟢 and resolves Assets ❓.

## Done when

Frederik logs in via the styled page, reviews it in `/build/review` (P-07), sets Wire/UI 🟢 and decides Assets. Registry + runway "Branded login view" row updated; log entry appended.

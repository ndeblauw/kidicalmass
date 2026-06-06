---
title: Pink-vest onboarding prototype — design / build brief
tags: [design, prototype, volunteer, onboarding]
sources: [wiki/strategy/50-user-journeys, wiki/design/01-concerns (D-1, D-12), notion/user-interviews, raw/website/volunteer-roi-charter]
phase: design
updated: 2026-06-06
---

# Pink-vest onboarding prototype

A clickable, branded prototype to show **Leticia (Mon 8 June, afternoon)** in broad
strokes how a pink-vest volunteer gets onboarded via the site / their account. Hi-fi,
NL, built in the real Laravel app so it "feels real." Example chapter: **Oudergem (1160)**
— Morgane's chapter, so the demo fixes exactly the gaps she described.

This is a **prototype for a meeting**, not production. Build lean; expect rework.

## Why (evidence)

The J2 reframe is already in the docs and is evidence-backed: the volunteer pain is
**not the intake form, it's everything after it** ("formulier → klaar voor je eerste rit").
The three volunteer interviews (Jorge, Morgane, Alexandre-prep) pin the concrete gaps:

- **Jorge:** the start-of-ride speech and "how to organise" know-how are lost in WhatsApp
  history; wants a findable, copy/paste-ready page.
- **Morgane:** role confusion (co-organiser vs pink vest); "who brings the vests?"; weak
  social onboarding ("don't know who's active, only know who's coming 2–3 days before");
  her partner "felt especially lost on first ride."
- Rests on **D-1** (back-office IN for v1 = a 3-layer material library + volunteer roster +
  light invite-only account at `/backstage/[postal]`) and the **ROI charter** (the
  operational know-how: shared vest bag, captain/sweeper, ~8–9 km/h, secure intersections).

## Evaluation — 5 findings carried to the meeting

1. **The hand-off is the weakest link, and it's pre-account** (who replies + who provisions
   the invite — **D-12 Open**). Raised verbally with Leticia, not a screen.
2. **"Ready for your first ride" is onboarding, not a library** — sequenced, not browsed.
   The hero screen. Highest value, most novel.
3. **Role confusion is unmodelled** — the welcome screen must *state your role*.
4. **Roster ≠ "who's coming Sunday"** (attendance cut, D-1 Decision B). Named to Leticia.
5. **Invite-only login is heavier than the WhatsApp add it replaces** — the library must be
   worth a login. A pressure-test, raised verbally.

## The demo: one clickable story (5 surfaces)

```
[existing chapter form — already built, show briefly]
   → 1. Invite email → 2. Set password → 3. FIRST-LOGIN WELCOME → 4. Backstage home → 5. Roster
```

**Priority for the Sat→Mon window:**
- **P1 (the concept):** #3 first-login welcome + #4 backstage home.
- **P2 (entry feels real):** #1 invite email + #2 set-password.
- **P3 (if time):** #5 roster.

Build P1+P2 solidly; P3 if the weekend allows. Stop and checkpoint between P-bands.

---

## Surfaces & NL copy

> Voice: joyful, warm, local, committed-not-preachy. **No em-dashes.** Demo names flagged
> `[demo]` — Frederik swaps. Using "Morgane" as the onboardee ties the demo to the interview;
> chapter lead name is a placeholder.

### 1 — Invite email *(system mailable, previewable route)*

- **Subject:** Welkom bij de roze hesjes van Oudergem
- **Preheader:** Activeer je account en maak je klaar voor je eerste rit.
- **Body:**
  - Dag Morgane,
  - Leuk dat je meefietst met Kidical Mass Oudergem. Je hoort er nu officieel bij als roze hesje.
  - We hebben een plek voor je gemaakt met alles wat je nodig hebt: hoe een rit verloopt, wat
    je rol is, en wie er in je team zit. Voortaan op één plek terug te vinden, op je gsm én je laptop.
  - **[ Activeer je account ]**
  - Tot op de volgende rit! Het team van Kidical Mass Oudergem
  - *(klein) Werkt de knop niet? Plak deze link in je browser.*

### 2 — Set password / activate *(reuse `layouts/auth` + reset-password pattern)*

- **Heading:** Stel je wachtwoord in
- Nog één stap, Morgane. Kies een wachtwoord en je bent binnen.
- Fields: Wachtwoord · Herhaal wachtwoord
- **[ Account activeren ]**
- *(reassurance) Een licht account, gewoon zodat je op elk toestel bij je materiaal kan.*
- On submit: log in the demo user → land on #3.

### 3 — First-login welcome (HERO): "Welkom, je bent klaar voor je eerste rit"

- **Header band (h1, tilted):** Welkom, Morgane 👋 · sub: Je bent een roze hesje bij Kidical Mass Oudergem.
- **Wat doet een roze hesje?**
  - Je rijdt mee naast de groep en houdt de kinderen samen.
  - Je brengt rust en een vrolijke aanwezigheid op de weg.
  - Goed zichtbaar zijn is genoeg: een fluo hesje en goeie energie.
  - Geen verkeersopleiding nodig. Dat leer je vanzelf, samen met het team.
- **Je eerste rit, stap voor stap** *(from ROI charter, warm)*
  1. Voor de start: de hesjes zitten in een gemeenschappelijke tas en worden ter plaatse
     uitgedeeld. Je hoeft zelf niks mee te brengen.
  2. Onderweg: vooraan rijdt een kapitein, achteraan een sluiter. Jij houdt mee de groep samen.
  3. Tempo: we rijden op kindertempo, ongeveer 8 à 9 km per uur. Rustig aan, het is geen koers.
  4. Aan de kruispunten: we zetten ze samen veilig af en sluiten daarna weer aan.
- **Wie leidt jouw afdeling:** [foto] [demo: Thomas], coördinator in Oudergem. Vragen? Bij hem
  kan je altijd terecht.
- **De startspeech** *(the Jorge fix):* Elke rit begint met een kort woordje: welkom, waarom we
  rijden, en de afspraken rond veiligheid. Hier staat ze, klaar om te gebruiken. **[ Kopieer de startspeech ]**
- **Onze afspraken** *(5 ROI principles, warm):* Veiligheid voorop · Iedereen welkom ·
  Vriendelijkheid (#kindnessisking) · Positieve actie, met de glimlach · Samen organiseren.
  **[ Lees onze afspraken (PDF) ]**
- **Volgende meetups:** Di 16 sep · Vrijwilligersmeetup. (+ next ride)
- **CTAs:** Bekijk je eerste rit → · Naar je materiaal →

### 4 — Backstage home / material library *(D-1 layer 2, the standing home)*

- **Route:** `/backstage/{group}` (auth). Header: Backstage, Oudergem.
- Intro: Alles voor de roze hesjes van Oudergem, op één plek.
- Grouped tiles: **Documenten** (Afsprakencharter PDF · Zo organiseer je een rit) ·
  **Video** (Veilig begeleiden — youtu.be/i9YQxJ-ChNM) · **Posters & promo** (downloads) ·
  **Startspeech** (copy-paste) · **Wat komt eraan** (next rides + meetups) ·
  **Jouw team →** (roster).

### 5 — Roster *(D-1 volunteer roster; logged-in only)*

- **Route:** `/backstage/{group}/team`. Header: De roze hesjes van Oudergem.
- Intro: Dit is je team. Zo weet je wie er mee rijdt.
- List: avatar + voornaam (+ optional role tag).
- Your own card: toggle **Toon mij op de publieke chapterpagina** (opt-in public — `group_user.is_public`).

---

## Build notes

- **Data (isolated, do not touch `GroupSeeder`):** a `PrototypeOnboardingSeeder` creates the
  Oudergem group (shortname `oudergem`, zip `1160`), a demo pink-vest user (Morgane), a demo
  lead (Thomas), a handful of roster members, `group_user` links, and a couple of upcoming
  activities/meetups.
- **Schema:** add `is_public` boolean (default false) to `group_user` — additive, matches the
  D-1 roster model. (Shared tree: additive migration only, no `-A` commits.)
- **Auth:** shallow. Set-password screen posts → logs in the demo user → `/backstage/oudergem/welkom`.
  Don't build real token provisioning for the demo.
- **Routes (prototype):** under the existing `auth` middleware group where logged-in; a public
  `GET /prototype/mail/invite` to render the email; the welcome at `/backstage/{group}/welkom`.
- **Reuse:** `layouts/site`, existing cards / icon-chips / buttons, CSS partials. New CSS (if any)
  → `resources/css/pages/backstage.css` per the partials rule; no raw hex/px in components.
- **Tests:** a light Pest smoke test that each prototype route renders 200 (authed where needed).
  Satisfies the test-enforcement rule without over-building a throwaway.

## Explicitly out (verbal-only for Leticia)

- Hand-off ownership / provisioning (D-12).
- "Who's coming Sunday" (attendance cut).
- Real invite-token auth, multi-chapter UI, new-chapter/organiser onboarding (D-1 layer 3).

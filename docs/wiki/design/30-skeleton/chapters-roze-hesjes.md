---
title: Roze-hesje-pagina — build-briefing
tags: [build, chapters, backstage, roze-hesjes]
sources: [design/30-skeleton/chapters.md, design/30-skeleton/chapters-research.md, design/playground-chapter-page.html, design/prototype-chapter-pages.html]
phase: design
updated: 2026-06-09
---

# Roze-hesje-pagina — build-briefing

*Compacte briefing om in een verse thread de **roze-hesje-pagina** te bouwen. Visuele referentie: [`prototype-chapter-pages.html`](../prototype-chapter-pages.html) (rechterkolom). Achtergrond: [`chapters.md`](chapters.md) (P-11) + [`chapters-research.md`](chapters-research.md).*

## Doel

Vervang de aparte **backstage** door een **roze-hesje-pagina** die in hetzelfde publieke framework leeft als de gewone lokale groep-pagina (`groups/show.blade.php`). Het is een **aparte route per chapter** met een **roze hero** die de ingelogde staat signaleert. Geen aparte branded shell meer; dezelfde layout-componenten, ander accent + andere inhoud.

## Beslist in deze thread

- **Aparte route, gedeeld framework.** De roze-hesje-pagina is een eigen pagina (eigen URL) die de layout-componenten van de publieke chapter-pagina hergebruikt — niet de huidige losse `layouts/backstage.blade.php`-shell.
- **Hero gestript + roze.** Zoals de publieke pagina: **ronde groepsfoto** (zelfde behandeling als `activity-hero__photo`, `border-radius:50%`) + angled titel **"Kidical Mass {gemeente}"**. Géén intro/ritteller/volgende-rit in de hero. Roze achtergrond i.p.v. blauw.
- **Pagina-structuur (roze):**
  1. Topnav (identiek aan publiek; zie nav-wissel hieronder)
  2. Roze hero — ronde foto + "Kidical Mass {gemeente}"
  3. Intro-tekst onder de hero (standaard "feestelijke fietsparade"-tekstje) — *open: identiek aan publiek of overslaan? zie open punten*
  4. **"Op de agenda in {gemeente}"** — volgende ritten (incl. meetups, publiek per D-2)
  5. **"De roze hesjes van {gemeente}"** — de **roster** (vervangt de kapiteins-sectie)
  6. **"Jouw materiaal"** — materiaalbibliotheek (vervangt de CTA-sectie)
  7. Historiek — heel onderaan, low-key
  8. Roze closing
- **Verschil t.o.v. publiek:** **géén kapiteins-sectie en géén "Help mee"-CTA**; in de plaats **roster + materiaal**.
- **Nav-wissel (de enige wijziging aan de publieke pagina):** als de bezoeker een **ingelogd roze hesje van dit chapter** is, toont de topnav een **roze chapter-knop `🎀 {gemeente}`** (naast de Steun-knop) die naar de roze-hesje-pagina linkt. Op de roze pagina is die knop actief. *Verder niets aan de publieke pagina wijzigen — die wordt in een andere thread opgekuist.*

## Inhoud-afbakening (publiek vs. roze-hesje-only)

Roze-hesje-only (alleen ingelogd, niet publiek):
- **Volledige roster + namen** (publiek alleen wie opt-in koos — `group_user.is_public`, D-1)
- **Interne materialen:** afsprakencharter, "zo organiseer je een rit", begeleidingsvideo, startspeech
- **Welkomstgids / onboarding** (eerste-rit-gids)

Mag publiek (dus niet exclusief): posters/flyers/downloads.

## Wat al bestaat (hergebruiken / migreren)

- **Huidige backstage** (inhoud is grotendeels klaar, alleen verkeerd ondergebracht):
  - `app/Http/Controllers/BackstageController.php` (`chapterData()`: group, volunteer, lead, roster, upcoming)
  - `resources/views/backstage/{home,welcome,team}.blade.php` — roster, materiaalbibliotheek (6 tegels), welkomstgids/onboarding, startspeech-copy
  - `layouts/backstage.blade.php` (te vervangen door publieke layout)
  - Routes `/backstage/{group:shortname}` + `/activeer/{group:shortname}` in `routes/web.php`
  - `BackstageDemoAccess` middleware (one-click demo login, non-prod) + `PrototypeOnboardingSeeder`
- **Publieke chapter-pagina:** `resources/views/groups/show.blade.php` + `resources/css/pages/chapters.css` — de layout-componenten om te delen.
- **Standaard componenten:** `<x-cta-button>` (Caprasimo-pill + rode disc; varianten yellow/blue/secondary/ghost), `<x-closing-cta>` (gele band), `<x-feature-card>`.
- **Ronde foto:** `activity-hero__photo` patroon in `resources/css/pages/activity.css`.

→ **Aanpak:** verhuis de backstage-inhoud (roster, materiaal, welkomstgids) naar een nieuwe view die de publieke layout gebruikt, op een nieuwe of hernoemde route. Beslis in de nieuwe thread of de oude backstage-routes/shell verdwijnen of redirecten.

## Datamodel (Nico / [#37](https://github.com/ndeblauw/kidicalmass/issues/37))

Bestaat nu niet, mag voor de build gemockt/geseed worden (zoals backstage al doet):
- `Group` media library — **vrije groepsgalerij** + ronde cover/groepsfoto (geen media library op Group vandaag)
- `group_user.role` (trekker / roze hesje / communicatie / foto / dj …) + `group_user.is_public`
- per-groep `intro`, lead-email
- opslag voor materialen/downloads (per groep, kapitein-bewerkbaar in Filament P-21)

## Open punten (beslis in de nieuwe thread)

1. **Route + naam.** Pad volgens bestaande conventie (publieke chapter = route `groups.show`, pad `/{locale}/chapters/{group}`). Voorstel: `groups.roze-hesjes`, pad `…/chapters/{group}/roze-hesjes`. Verzoen met de bestaande `/backstage/{group:shortname}`-routes.
2. **Intro op de roze pagina:** identiek aan publiek, of meteen door naar de agenda?
3. **Pers/downloads op de roze pagina:** ook tonen, of weglaten?
4. **Nav-knop-label:** enkel `🎀 {gemeente}`, of woord "roze hesje" erbij?
5. **Lot van de oude backstage** (welkom-onboarding als aparte pagina vs. als "welkomstgids"-blok/-link op de roze pagina).

## Regels & verificatie

- Publieke-site frontend-regels: rauwe `<h1>`–`<h6>` (nooit `flux:heading`), CSS in role-based partials (`resources/css/components|pages/…`, nooit in `app.css`), enkel tokens (geen rauwe hex/px in componenten), **geen em-dashes** in copy, tone-of-voice (`docs/tone-of-voice.md`).
- Het **oranje CTA-blok** uit het prototype is een *nieuw* standaardpatroon (bestaande `closing-cta` is geel + 1 button) — alleen relevant voor de publieke pagina, niet voor de roze pagina.
- Tests met Pest (auth-gating op chapter-lidmaatschap, roster-zichtbaarheid publiek vs. ingelogd, page rendert), `vendor/bin/pint --dirty` voor afronding.
- Na de bouw: pipeline-status (P-11 / backstage-rij) bijwerken in [`00-page-registry.md`](00-page-registry.md) + log-entry in [`log.md`](../../log.md).

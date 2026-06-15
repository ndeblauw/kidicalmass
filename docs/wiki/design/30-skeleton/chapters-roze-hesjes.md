---
title: Roze-hesje-pagina — build-briefing
tags: [build, chapters, backstage, roze-hesjes]
sources: [design/30-skeleton/chapters.md, design/30-skeleton/chapters-research.md, design/playground-chapter-page.html, design/prototype-chapter-pages.html]
phase: design
updated: 2026-06-15
---

# Roze-hesje-pagina — build-briefing

*Compacte briefing om in een verse thread de **roze-hesje-pagina** te bouwen. Visuele referentie: [`prototype-chapter-pages.html`](../prototype-chapter-pages.html) (rechterkolom). Achtergrond: [`chapters.md`](chapters.md) (P-11) + [`chapters-research.md`](chapters-research.md).*

> **Status:** de eerste versie (hieronder, "Beslist in deze thread") is **gebouwd en op `main`** — route, hero, agenda, roster, materiaal, welkomblok + onboarding, roze nav-knop. De **[tweede iteratie](#tweede-iteratie-2026-06-15--de-levende-hub)** herstructureert die pagina rond *levende staat*. Implementatieplan: [`2026-06-15-roze-hesje-living-hub.md`](../../../superpowers/plans/2026-06-15-roze-hesje-living-hub.md).

## Doel

Vervang de aparte **backstage** door een **roze-hesje-pagina** die in hetzelfde publieke framework leeft als de gewone lokale groep-pagina (`groups/show.blade.php`). Het is een **aparte route per chapter** met een **roze hero** die de ingelogde staat signaleert. Geen aparte branded shell meer; dezelfde layout-componenten, ander accent + andere inhoud.

## Tweede iteratie (2026-06-15) — de levende hub

De eerste versie zette alles op één lange pagina: hero, agenda, roster, onboarding, materiaal. Dat werkt als naslag, maar geeft een hesje geen reden om **terug te komen**. Deze iteratie herdenkt de pagina rond dat ene principe.

**Principe: toon wat beweegt, niet wat vastligt.** De terugkeerredenen zijn telkens dingen die *veranderd zijn sinds het vorige bezoek*: nieuwe foto's, een rit die vorm krijgt, iemand die net is bijgekomen. De statische inhoud (eerste rit, materiaal, roster) trekt niemand terug. Tweede laag: de pagina moet doen **wat de WhatsApp-groep slecht kan**. WhatsApp is het vluchtige gesprek; deze pagina is het **geheugen en de stand van zaken** — foto's die opstapelen, een statusvraag die blijft staan in plaats van weg te scrollen.

Daarmee keert de hiërarchie om: het levende komt bovenaan, het statische zakt naar naslag eronder.

**Onboardingpad door zichtbaarheid (de strategische winst).** Als een hesje het werk-in-uitvoering van de kapiteins kan *zien* (een draft-rit en waar die staat), begrijpt het dat werk en stapt het na verloop van tijd zelf in. Je rekruteert geen kapiteins met een knop; je laat ze de machine zien tot ze een hendel vastpakken. De ladder is **kijken → meedoen → kapitein**, en transparantie bouwt de eerste sport.

**Nieuwe / herziene structuur van de hub (boven → onder):**

1. Roze hero — ongewijzigd (state-signaal).
2. Welkomblok — ongewijzigd (tijdgebonden, ~2 weken).
3. **Wat is nieuw sinds vorige keer** — *nieuw.* Een rustige strook met de veranderingen sinds het laatste bezoek: nieuwe foto's, een nieuw hesje uitgelicht, een rit die een stap opschoof.
4. **Op de agenda** — uitgebreid: toont nu ook **draft-ritten**, duidelijk gemarkeerd als "nog niet vast", elk te openen als preview.
5. **Foto's** — *nieuw.* Gedeelde galerij + upload. De terugkeerreden bij uitstek (en het collectieve geheugen van het chapter).
6. **De roze hesjes** (roster) — naar naslag; met een zachte **"nieuw"-markering** op leden van de eerste ~2 weken.
7. **Voor je eerste rit** (onboarding) — naar naslag, ongewijzigd.
8. **Jouw materiaal** — naar naslag, ongewijzigd.
9. Historiek + roze closing — ongewijzigd.
10. **WhatsApp-doorgang** — *nieuw.* Een aparte link naar de chaptergroep. Bewust los van de pagina: gesprek en stand-van-zaken mogen elkaar niet proberen te zijn.

**Draft-preview (aparte sub-pagina per rit).** Een hesje kan een draft-rit openen en ziet de rit zoals ze nu is (datum/route voor zover bekend, duidelijk "nog niet vast"). Eén **lichtgewicht statusregel** zegt waar ze staat — *"wat moet er nu nog gebeuren"* (bv. "de communicatiekaart is nog niet ingevuld"), géén volledige checklist. Voor een hesje is dat puur een **venster** (read-only), nooit een opdracht; dezelfde regel is voor de kapitein wél de werkregel.

**Beslist in deze iteratie:**
- **Status verhuist van de hub naar de rit.** Geen voorbereidings-checklist op het overzicht; wel één vooruitkijkende statusregel op de draft-preview.
- **Hesjes zien, ze claimen niet.** Read-only op draftstatus; geen taken opnemen op de pagina (de ladder loopt via zien + later kapitein worden, niet via claim-knoppen).
- **Nieuwe mensen worden uitgelicht** — nadruk op nieuwe hesjes — zowel als gebeurtenis in "wat is nieuw" als met een zachte "nieuw"-markering in het roster gedurende de eerste ~2 weken (hergebruik het bestaande welkom-venster `ROZE_WELCOME_WEEKS`).
- **Toon moet warm blijven, niet als werk.** Statuszichtbaarheid leest als verwachting en erbij horen, niet als een takenbord. Bij ~12 mensen per chapter is dit een warme gedeelde kamer, geen applicatie — één levende hub, geen tab-app.

**Backend-afhankelijk (Nico / [#37](https://github.com/ndeblauw/kidicalmass/issues/37)), gefaket voor de build zoals de bestaande materiaal-tegels:**
- **Group media library** — foto-galerij + upload (bestaat nog niet op `Group`).
- **Activity draft-state** + een licht **status/volgende-stap-veld** ("wat moet er nog gebeuren").
- **`group_user` lid-sinds-timestamp** voor de "nieuw"-markering (of een expliciet veld).
- **Per-groep WhatsApp-URL.**
- **"Wat is nieuw"-feed** — vergt echte change-events (foto toegevoegd, lid bijgekomen, rit-status gewijzigd); voorlopig gefaket.

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

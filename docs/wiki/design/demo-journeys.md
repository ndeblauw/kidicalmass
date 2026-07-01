---
title: Demo journeys — arc & open gaps
tags: [design, journeys, demo, build]
sources: [wiki/design/journeys-frontstage-backstage.html, wiki/strategy/50-user-journeys.md, wiki/design/journey-palette.md, wiki/design/30-skeleton/00-page-registry.md]
phase: design
updated: 2026-06-25
---

# Demo journeys — arc & open gaps

Three personas, from **onbekend → in de bekend**. Together they form one circle:
**J3 publiceert een rit → J1 ontdekt die → J1 bekijkt de recap-foto's die J2 uploadde → J2 werd bevestigd door J3.**

Most of the arc is already built. So this overview reads as a **story you can tick off**, and only the
**open gaps stand out**. The full per-page status lives in the page registry
([`30-skeleton/00-page-registry.md`](30-skeleton/00-page-registry.md), `P-nn`) and the `/build` dashboard —
this page is the journey lens on top of it. Print view: [`demo-journeys.html`](demo-journeys.html). Who-demos-what:
[`demo-runbook.md`](demo-runbook.md).

**Legenda:** ✓ werkt & live · 🟡 deels (placeholder/faux/manueel) · 🔴 nog te bouwen · ⚪ beslissing nodig.
Stappen zonder badge werken gewoon — de aandacht gaat naar wat nog open is.

---

## Journey 1 · De eerste rijder *(groen)*

> Hoort erover → vindt een rit in de buurt → deelt → schrijft zich in → krijgt een mail → bekijkt foto's → steunt.

1. ✓ Landt op **Home**, ziet *"volgende rit bij jou"* (echte proximity). · `P-01`
2. ✓ **Vindt een rit in de buurt** op de kalender (afstandsbands). · `P-02`
3. ✓ Opent het **ritdetail** (route, GPX, datum, iCal). · `P-03`
4. ✓ **Deelt** met vrienden (WhatsApp · Facebook · mailto). · `P-03`
5. 🟡 **Schrijft zich in** voor updates — UI werkt, maar **persistentie ontbreekt**: geen subscriber-model, geen dubbele opt-in. · `P-24`
6. 🟡 **Krijgt een maandelijkse update-mail** — **de zware bouwstenen staan klaar**: `GetGroupChangesAction` (berekent per groep wat er veranderde — ritten, leden, artikels; getest) + een afgewerkte, gethematiseerde mailtemplate (`components/emails/notification`, via `KidicalMassMessage`). **Mist nog:** de lijm-mailable die de changes in de template giet + een trigger/schedule + ontvangers. · `P-24`
7. ✓ …en **bekijkt de foto's** (recap-galerij) — *dit zijn de foto's die J2 uploadt; hier sluit de lus.* · `P-03`
8. ✓ **Beslist te steunen** (`/steun-ons`, echte proof-stats, link-out Growfunding). · `P-04`

**⚠ Nog te bouwen:** de **nieuwsbrief-lijm** — subscriber-persistentie (5) + een mailable die `GetGroupChangesAction` in de bestaande template giet, met trigger/schedule (6). De rekenkern én de mailtemplate bestaan al, dus dit is kleiner dan het lijkt.
*Demo-tip:* de inschrijf-UI toont een optimistische bevestiging, en de mailtemplate is al productie-klaar (zie `VolunteerInvite` / `WelcomeNotification`) — beide toonbaar. (De "mail over de rit" is een **maandelijkse digest**, geen RSVP — dat lost `D-1` op.)

---

## Journey 2 · De kandidaat-roze-hesje *(oranje)*

> Reed al mee → bekijkt de lokale groep → contacteert de kapiteins → krijgt een uitnodiging →
> bezoekt de roze-hesjes-hub → leest getting-started → bereidt zich voor → deelt foto's.

1. ✓ Bekijkt de **lokale-groep-pagina** (echte agenda, laatste-rit-galerij). · `P-11`
2. 🟡 **Contacteert de kapiteins** (`?intent=volunteer`) — formulier werkt & slaat op, maar landt in de **centrale inbox**; **routing naar de kapitein nog niet gebouwd**. · `P-11`
3. 🟡 **Krijgt een uitnodiging** — mail bestaat, maar **geen automatische trigger**; user + groepskoppeling gebeuren **manueel in Filament**. · `P-07`
4. 🟡 Bezoekt de **roze-hesjes-hub** — gating + agenda echt; **change-feed en materiaal nog faux**. · `P-09`
5. ✓ Leest de **getting-started**. · `P-12`
6. 🟡 **Bereidt zich voor** op de eerste rit — content statisch; **materiaalbibliotheek nog faux**. · `P-09`
7. ✓ **Deelt foto's** van die rit (upload-tool, lid-gated) — *foto's verschijnen op de publieke recap (J1 #7); lus sluit.* · `P-09`

**⚠ Nog te bouwen:** **kapitein-routing** (2) en **automatische uitnodiging + "accepteer-aanvrager"** (3); roze-hub **feed + materiaal** (4, 6).
*Demo-tip:* de uitnodigingsmail is toonbaar via een prototype-route (`prototype.mail.invite`).

---

## Journey 3 · De kapitein *(paars)*

> Krijgt de aanmelding (J2) → bevestigt → plant een rit → WhatsApp "jaarplanning klaar" →
> ziet de concept-rides → verfijnt & publiceert → ze verschijnen automatisch op de groepspagina.

1. 🟡 **Krijgt de aanmelding** van de roze-hesje — komt in de **centrale inbox**, niet rechtstreeks bij de kapitein. · `P-21`
2. 🟡 **Bevestigt** het nieuwe lid — **manueel** (`User` + `group_user`-pivot); geen "accepteer"-actie of auto-uitnodiging. · `P-21`
3. ✓ **Plant een volgende rit** in Filament (gescopet op eigen groep). · `P-21`
4. ⚪ *Trigger:* **WhatsApp** "jaarplanning klaar" — bewust manueel, off-platform. · —
5. 🟡 **Ziet de concept-rides** — alleen een binaire `published`-vlag (niet-gepubliceerd = verborgen); **rijkere draft-lifecycle is faux**. · `P-21`
6. ✓ **Verfijnt** de draft-rides (volledige velden + completeness-widgets). · `P-21`
7. ✓ **Publiceert** (echte publish-actie). · `P-21`
8. ✓ Rit **verschijnt automatisch** op groepspagina + kalender — *hier ontdekt J1 #2 de rit; cirkel rond.* · `P-11`

**⚠ Nog te bouwen:** **aanmelding → kapitein** (1) en **bevestigen/uitnodigen** (2); een echte **concept/draft-lifecycle** voorbij de enkele vlag (5).
Het publiceren zelf (6–8) is echt en sterk — de kern van J3 werkt.

---

## De openstaande lijst (één build-checklist)

Alleen wat nog gebouwd of beslist moet worden:

| # | Gap | Raakt | P-nn / ref | Eigenaar |
|---|---|---|---|---|
| 1 | **Nieuwsbrief-persistentie** — subscriber-model + opslaan + dubbele opt-in | J1 #5 | `P-24` | Nico |
| 2 | **Update-mail-lijm** — mailable die `GetGroupChangesAction` → de bestaande mailtemplate giet, + schedule + ontvangers *(Action + template bestaan al & getest)* | J1 #6 | `P-24` | Nico |
| 3 | **Kapitein-routing** van aanmeldingen (nu centrale inbox) | J2 #2 · J3 #1 | `#37` | Nico |
| 4 | **Automatische uitnodiging + "accepteer-aanvrager"-actie** | J2 #3 · J3 #2 | `P-07` · `#37` | Nico |
| 5 | **Roze-hub: change-feed + materiaalbibliotheek** (nu faux) | J2 #4 · #6 | `P-09` · `#37` | Nico |
| 6 | **Concept/draft-lifecycle** voorbij de binaire `published`-vlag — ⚪ scope beslissen | J3 #5 | `P-21` · `#37` | beslissing → Nico |

**Demo-baar ondanks ontbrekende backend:** nieuwsbrief-UI (optimistische bevestiging) · uitnodigingsmail (`prototype.mail.invite`) · roze-hub (faux feed leest als echt).

---

## Onderhoud

Eén bron, drie views: deze tabel → render [`demo-journeys.html`](demo-journeys.html) opnieuw → check de runbook.
Houd status in lijn met `/build`; zie `CLAUDE.md` → "Updating the build pipeline".

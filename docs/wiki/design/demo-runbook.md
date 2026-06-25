---
title: Demo runbook — wie toont wat
tags: [design, journeys, demo]
sources: [wiki/design/demo-journeys.md]
phase: design
updated: 2026-06-25
---

# Demo runbook — wie toont wat

Voor de demo aan de vrijwilligers (woensdag) + de opname voor Leticia. Eén pagina: het verhaal, wie het toont,
en de plekken waar je het even moet kaderen. Detail per stap: [`demo-journeys.md`](demo-journeys.md).

**Het verhaal in één zin:** drie persona's die in elkaar haken — een rit die een kapitein publiceert wordt ontdekt
door een nieuwe rijder, die de foto's bekijkt die een roze-hesje uploadde, dat door diezelfde kapitein bevestigd werd.
**Sluit af met die cirkel.**

**Voorstel rolverdeling:** Frederik toont de **frontstage** (de publieke site), Nico toont de **backstage** (Filament).
Journey 2 wisselt af. *(⚪ samen finaliseren.)*

---

## Journey 1 · De eerste rijder — *frontstage*  → **Frederik**

| Beat | Toon | Kader / let op |
|---|---|---|
| Vindt een rit in de buurt | Home → kalender met afstandsbands | Werkt met echte locatie |
| Opent ritdetail & deelt | Ritpagina + share-knoppen | — |
| Schrijft zich in | Nieuwsbrief-formulier | **"Het opslaan bouwt Nico nog"** — UI toont een bevestiging, persistentie volgt |
| Krijgt mail & bekijkt foto's | Mailtemplate (toonbaar) + recap-galerij op de ritpagina | De **mail-bouwstenen staan klaar** (changes-Action + afgewerkte template); enkel de verzendlijm volgt nog. De **foto's zijn echt** — en het zijn die van J2 |
| Beslist te steunen | `/steun-ons` | Echte cijfers, link naar Growfunding |

---

## Journey 2 · De kandidaat-roze-hesje — *frontstage + één backstage-beat*  → **Frederik** (+ **Nico** voor de mail)

| Beat | Toon | Kader / let op |
|---|---|---|
| Bekijkt de lokale groep | Groepspagina | — |
| Contacteert de kapiteins | Aanmeldformulier (`?intent=volunteer`) | **"Belandt nu centraal; routing naar de kapitein bouwt Nico nog"** |
| Krijgt een uitnodiging | Uitnodigingsmail | **Nico** toont de mail via de prototype-route — automatische verzending volgt nog |
| Bezoekt de roze-hub & bereidt zich voor | Roze-hesjes-hub + getting-started | Feed en materiaal zijn nog placeholder |
| Deelt foto's | Foto-upload-tool | **De foto's verschijnen op de publieke recap uit Journey 1** — laat de lus zien |

---

## Journey 3 · De kapitein — *backstage*  → **Nico**

| Beat | Toon | Kader / let op |
|---|---|---|
| Krijgt & bevestigt de aanmelding | Filament-inbox + lid koppelen | Nu nog manueel; routing + "accepteer"-actie volgen |
| Plant een rit | Filament, gescopet op eigen groep | Werkt |
| WhatsApp "jaarplanning klaar" | — (vertel het, geen scherm) | Bewust manueel, off-platform |
| Verfijnt & publiceert | Concept-rit invullen → publiceren | Werkt; "concept" = nu een aan/uit-vlag |
| Rit verschijnt automatisch | Terug naar de publieke groepspagina | **Hier ontdekt de eerste rijder uit Journey 1 de rit — cirkel rond** |

---

## Drie plekken om eerlijk te kaderen ("dit bouwen we nog")

1. **Inschrijven op updates** (J1) — UI werkt; opslaan + de verzendlijm van de update-mail volgen (rekenkern + template bestaan al).
2. **Van aanmelding naar lid** (J2→J3) — vandaag manueel; routing + automatische uitnodiging volgen.
3. **Concept-rides** (J3) — nu een enkele publicatie-vlag; een echte draft-flow is nog een beslissing.

Alles daarbuiten is live en mag je tonen zoals het is.

---

## Openstaande beslissingen vóór de opname

- ⚪ **Rolverdeling** Frederik/Nico per journey bevestigen.
- ⚪ **Demo-account/groep** kiezen waarop de roze-hub + Filament getoond worden.

*(De "mail over de rit" is intussen helder: een maandelijkse digest op basis van `GetGroupChangesAction`, geen RSVP — geen beslissing meer nodig.)*

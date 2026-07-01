---
title: Group update-mail — content design & build
tags: [design, email, newsletter, handoff]
sources: [docs/superpowers/specs/2026-06-25-update-email-content-design-handoff.md, app/Actions/GetGroupChangesAction.php, app/Actions/GroupChangesResult.php, resources/views/components/emails/notification.blade.php, resources/views/emails/group-update.blade.php, routes/web.php, docs/tone-of-voice.md]
phase: design
updated: 2026-06-25
owner: Frederik
---

# Group update-mail — content design & build

Answers the content brief in [`2026-06-25-update-email-content-design-handoff.md`](2026-06-25-update-email-content-design-handoff.md):
the monthly "wat is er nieuw bij jouw groep"-mail. This is **Journey 1 #6** (de eerste rijder krijgt een
mail → bekijkt de foto's). The email shell (`<x-emails.notification>`) already existed; the data layer
needed a small extension, which is built and tested here along with the body, copy, and a previewable prototype.

## Decisions (the brief's open choices, resolved)

| # | Keuze | Beslissing |
|---|---|---|
| 1 | **Publiek vs. intern** | **Publieke digest.** Toont ritten/activiteiten, foto's, **nieuwe roze hesjes** (celebratory, voornamen) en nieuws. **Geen** kapiteins/geïnteresseerden, geen achternamen of contactgegevens. |
| 2 | **Wat is de verse maandelijkse waarde** | Niet de kalender (die wordt aan het begin van het jaar gemaakt, dus "nieuw aangemaakte ritten" is meestal leeg). Wél: **wat net gebeurde (recap + foto's)**, **wie erbij kwam (roze hesjes)**, en een **blik vooruit (komende 2-3 maanden, alle types)**. |
| 3 | **Drempel om te versturen** | **Niet versturen** als er nergens iets vers is. Komende activiteiten tellen **niet** mee voor die drempel (anders stuurt elke maand), recente ritten-met-foto's wel. Een stille groep valt weg uit de body. |
| 4 | **Detail per item** | Recap: titel + datum + plaats + tot 3 foto's. Komend: type-label + titel + datum + plaats + afstand. Alles **inline gelinkt** (recap → de rit-recap, komende activiteit → het detail). |
| 5 | **Activiteitstypes** | **Alle types**, incl. vergaderingen (een goede manier om nieuwe roze hesjes aan te trekken). Label via `ActivityType::labelNl()` (Fietsparade / Workshop / Vergadering / Activiteit). |
| 6 | **CTA-knop** | **Naar de kalender** (`activities.index`). Bewust groep-agnostisch: een abonnee kan meerdere groepen volgen. Foto's en losse activiteiten zijn via **inline links** in de body bereikbaar, dus de ene knop hoeft niet tussen foto's en kalender te kiezen. |
| 7 | **Thema** | **`blue`** (pastel). De rustige terugkerende digest, los van de gele onboarding-mails. |
| — | **Structuur** | **Merged**: één mail met een blok per gevolgde groep. Demo rendert één groep, **Schaarbeek**. |

## Onderwerp + preheader

Foto-geleid wanneer er een recap is, anders roze-hesje-geleid, anders nieuws:

| Situatie | Onderwerp (1 groep / merged) |
|---|---|
| recap-foto's aanwezig | `De foto's van de laatste rit in Schaarbeek staan online` / `De foto's van de laatste ritten staan online` |
| geen recap, wel roze hesjes | `Nieuwe roze hesjes in Schaarbeek` / `Nieuwe roze hesjes bij jouw groepen` |
| enkel nieuws | `Vers nieuws van Kidical Mass Schaarbeek` / `Vers nieuws van je Kidical Mass groepen` |

**Preheader:** `De foto's van de laatste rit, wie er nieuw is, en wat er binnenkort op de kalender staat.`

## Body-structuur (per groepsblok)

```
[eyebrow]  KIDICAL MASS · MAANDOVERZICHT
[h1]       Wat beweegt er in de buurt?

Hallo!
Hier is wat er deze maand gebeurde bij Kidical Mass Schaarbeek (of: de groepen die je volgt).
De foto's van de laatste rit, wie er nieuw bij is, en wat er binnenkort op de kalender staat.

── KIDICAL MASS SCHAARBEEK ──        ← groepskop, alleen bij >1 groep
📸  NET GEREDEN, IN BEELD
   Lenterit  ·  zaterdag 14 april  ·  Josaphatpark
   [foto] [foto] [foto]              ← tot 3 thumbnails, elk linkt naar de recap
   Bekijk alle foto's →

🗓️  BINNENKORT OP DE KALENDER
   Fietscheck-workshop               ← titel linkt naar het detail
   Workshop · zaterdag 3 mei, 14u · Josaphatpark
   Bekijk de activiteit →
   ... (max 5, dan "en nog X andere activiteiten →" → kalender)

🦺  NIEUWE ROZE HESJES
   Sofie, Mehmet en Lars trokken een roze hesje aan. Welkom in het team!

📰  IN HET NIEUWS
   "Een massa kets kleurt de Haachtsesteenweg"
   Eén zin teaser uit het artikel.

            [ Naar de kalender → ]   ← enige knop, blauw
```

### Mechaniek (in de Blade verwerkt)

- **Groepsblok** herhaalt per groep met verse content; een stille groep (`hasAny() === false`) verschijnt niet.
  Bij één groep valt de groepskop weg (onderwerp + intro noemen de groep al).
- **Recap:** `recentRidesWithPhotos`, tot 2 ritten, elk tot 3 thumbnails (`getFullUrl('thumb')`), foto's en
  titel linken naar `activities.show` (de recap-staat). Datum via `RideDate::full`, ISO `datetime` eronder.
- **Komend:** `upcomingActivities`, tot 5, type-label + titel (gelinkt) + datum + plaats + afstand. Meer dan
  5: "en nog {n} andere activiteiten" → kalender.
- **Roze hesjes:** `newPinkVests`, enkel voornamen (`Str::before(name, ' ')`), NL-opsomming met "en",
  enkelvoud/meervoud van "trok(ken)". In de merged variant noemt de zin de groep.
- **Nieuws:** `newArticles` (+ updated meegenomen), titel + één zin teaser uit `content_nl`. Geen per-artikel
  link tot er een publieke `articles.show` voor groeps-nieuws bevestigd is (er bestaat wel `about/news/{article}`,
  maar of groeps-`Article`s daar publiek leven moet Nico bevestigen).
- **Geen** kapiteins/geïnteresseerden, achternamen of contactgegevens.

## Wat is gebouwd

### Data-laag (uitgebreid + getest)
`GetGroupChangesAction` / `GroupChangesResult` kregen er twee velden bij:

- `recentRidesWithPhotos` — ritten met `begin_date` in het venster die een `gallery`-collectie hebben, nieuwste eerst.
- `upcomingActivities` — gepubliceerde activiteiten met `begin_date` tussen nu en de look-ahead-horizon
  (nieuwe constructor-param `upcomingUntil`, default `now()->addMonths(3)`), alle types, vroegste eerst.

`hasAny()` telt nu `recentRidesWithPhotos` mee, maar **bewust niet** `upcomingActivities` (zodat een mail enkel
vertrekt bij verse content). Tests: [`tests/Feature/Actions/GetGroupChangesActionTest.php`](../../../tests/Feature/Actions/GetGroupChangesActionTest.php).

### View (de slot-Blade)
[`resources/views/emails/group-update.blade.php`](../../../resources/views/emails/group-update.blade.php) —
ingevulde `<x-emails.notification>` die een `Collection<GroupChangesResult>` (`$changes`) verwacht.
Onderwerp/preheader/CTA worden bovenaan uit de data afgeleid, zodat het zelf-demonstrerend is.
Tests: [`tests/Feature/GroupUpdateEmailTest.php`](../../../tests/Feature/GroupUpdateEmailTest.php).

### Preview (niet-productie)
Route `prototype.mail.group-update` (`/prototype/mail/groep-update`) rendert de Schaarbeek-demo: echte
recap-ritten + komende activiteiten, met faux roze hesjes + één faux artikel zodat elk blok zichtbaar is.

## Copy-regels gevolgd

Nederlands, [`tone-of-voice.md`](../../tone-of-voice.md): joyful, warm, lokaal, geëngageerd. Geen em-dashes.
Datums leesbaar-NL via `RideDate`, ISO eronder. Emoji spaarzaam als sectie-ankers.

## Daarna (Nico, niet Frederik)

De **lijm**: een Mailable/Notification die per gevolgde groep `GetGroupChangesAction` draait, de resultaten
in `$changes` giet en `emails.group-update` rendert; een **schedule** (maandelijks); en **ontvangers**
(hangt aan de nog te bouwen subscriber-persistentie, J1 #5).

**Open voor Nico:**
- Een multi-groep abonnee krijgt **één merged mail** (de view ondersteunt het). Nico beslist of de Mailable
  per abonnee één mail met meerdere groepsblokken stuurt of per groep.
- Bevestig of groeps-`Article`s een publieke pagina hebben; zo ja, maak de nieuws-titels klikbaar.

**Status:** J1 #6 blijft 🟡 tot Nico de lijm + schedule + ontvangers wired. De content + data-laag zijn af.

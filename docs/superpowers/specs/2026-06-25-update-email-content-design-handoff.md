---
title: Design handoff — content for the monthly group update-mail
tags: [design, email, newsletter, handoff]
sources: [wiki/design/demo-journeys.md, app/Actions/GetGroupChangesAction.php, resources/views/components/emails/notification.blade.php, docs/tone-of-voice.md]
phase: design
updated: 2026-06-25
owner: Frederik
---

# Design handoff — content for the monthly group update-mail

**Your job (Frederik):** design the *content* of the monthly "wat is er nieuw bij jouw groep"-mail —
copy, structure, which changes we surface and how. The engineering is half-built: the data and the
email shell already exist. This is a **content/layout** task inside fixed building blocks, not a
visual-system task.

This is the missing schakel in **Journey 1 #6** (de eerste rijder krijgt een mail → bekijkt de foto's).
See [`demo-journeys.md`](../../wiki/design/demo-journeys.md).

---

## What already exists (design within this — don't redesign it)

### 1. The data — `GetGroupChangesAction` → `GroupChangesResult`
Computes, **per groep** over a tijdvenster (default: vorige maand → nu), alles wat veranderde.
Beschikbare velden in de mail:

| Veld | Inhoud |
|---|---|
| `startDate` · `endDate` | het tijdvenster van de digest |
| `group` | de groep (naam, gemeente, link) |
| `newActivities` | nieuwe ritten/activiteiten |
| `updatedActivities` | gewijzigde activiteiten (datum, route, …) |
| `newCaptains` · `newPinkVests` · `newInterested` | nieuwe leden, gesplitst per rol |
| `newArticles` · `updatedArticles` | nieuwe / gewijzigde pers- of nieuwsartikels |
| helpers | `hasAny()`, `summary()` (8 tellingen), `membersAddedCount()` |

Getest (`tests/Feature/Actions/GetGroupChangesActionTest.php`). Het **berekent enkel** — niets wordt verstuurd.

### 2. The email shell — `<x-emails.notification>`
`resources/views/components/emails/notification.blade.php`. Afgewerkt, in productie (invite/welcome).
Wat het je geeft (en wat je dus **niet** hoeft te ontwerpen):

- Wit afgerond kaartje op een gekleurde achtergrond, **logo automatisch** bovenaan.
- **Thema via `color`:** `blue` (default, pastel) · `yellow` · `pink` — zet achtergrond + knopkleur.
- **Eén CTA-knop** (`ctaUrl` + `ctaLabel`) + NL fallback-link-footer.
- `preheader` (verborgen inbox-voorbeeldtekst) + `subject`.
- De body is een vrije `{{ $slot }}` — **dit is jouw canvas.**

---

## What you design (the deliverable)

Lever dit aan als copy-doc of een ingevulde slot-Blade (`<x-emails.notification>`-voorbeeld), zodat Nico
het enkel nog aan de echte data hoeft te koppelen:

1. **Onderwerp (`subject`)** — sjabloon met variabelen, bv. `Nieuw bij {gemeente}: {n} ritten op komst`.
2. **Preheader** — één zin inbox-voorbeeld.
3. **Aanhef + intro** — warme openingsregel (tone-of-voice).
4. **Body-structuur per categorie** — hoe tonen we ritten, artikels, (en of) nieuwe leden? Volgorde,
   koppen, hoeveel detail per item (titel + datum + link? thumbnail?), opsomming vs. kaartjes.
5. **CTA** — wat is de knop? (bv. "Bekijk alle ritten" → groepspagina, of "Naar de kalender").
6. **Thema-keuze** — `blue` / `yellow` / `pink` voor deze mailsoort.
7. **Lege/rand-gevallen** (zie onder) — copy voor "weinig nieuws" en pluralisatie.

---

## Beslissingen die alleen jij kan maken

1. **Publiek vs. intern publiek.** `GetGroupChangesAction` berekent *alles*, óók nieuwe leden per rol.
   Maar de J1-ontvanger is een **publieke abonnee**, geen lid — die wil wellicht **ritten + nieuws**,
   niet "3 nieuwe roze hesjes". Bepaal: toont de publieke digest enkel ritten/artikels, en bewaren we
   de leden-info voor een **aparte kapitein-/interne digest**? (Zelfde Action, twee content-ontwerpen.)
   → *Dit is de belangrijkste keuze; ze bepaalt de rest van de structuur.*
2. **Drempel om te versturen.** Bij `hasAny() === false`: **niet versturen**, of een lichte "deze maand
   rustig, hou de kalender in 't oog"-mail? (Aanrader: niet versturen bij niks nieuws.)
3. **Eén rit vs. veel.** Copy + lay-out voor 1 item moeten even goed lezen als voor 8 (pluralisatie,
   "en nog 3 andere…").
4. **Hoeveel detail per rit.** Enkel titel + datum + link, of ook locatie/afstand/thumbnail? (Hoe rijker,
   hoe meer velden Nico moet doorgeven.)
5. **CTA-doel.** Eén knop — naar de groepspagina, de kalender, of de eerstvolgende rit?

---

## Copy-regels (niet onderhandelbaar)

- **Nederlands**, volg [`docs/tone-of-voice.md`](../../tone-of-voice.md): joyful, warm, lokaal, geëngageerd.
  De one-line test: klinkt het als iemand die graag met kinderen in de buurt fietst en jou erbij wil?
- **Geen em-dashes** (AI-tell). Gebruik komma's, haakjes of een nieuwe zin.
- Datums als leesbare NL-datum in de mail; de onderliggende `datetime` is ISO.
- Decoratieve emoji mag spaarzaam, in stijl met de site.

---

## Daarna (Nico, niet jij)

Zodra de content vastligt, wired Nico de **lijm**: een Mailable/Notification die `GroupChangesResult`
in jouw slot-ontwerp giet, een **trigger/schedule** (maandelijks), en **ontvangers** (hangt aan de nog te
bouwen subscriber-persistentie — J1 #5). Jouw ontwerp blokkeert daar niet op: content kan nu al af.

**Status na deze handoff:** J1 #6 blijft 🟡 tot (a) deze content af is en (b) Nico de lijm + schedule wired.

---

## Snelle referentie — voorbeeld-skelet

```blade
<x-emails.notification
    color="blue"
    subject="Nieuw bij {{ $group->gemeente }}: ..."
    preheader="..."
    :ctaUrl="route('groups.show', $group)"
    ctaLabel="Bekijk alle ritten"
>
    {{-- jouw body-ontwerp: intro + categorieblokken (ritten, artikels, ...) --}}
</x-emails.notification>
```

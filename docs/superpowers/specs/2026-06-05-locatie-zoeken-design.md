---
title: Locatie-zoeken op kalender en lokale groepen
tags: [design, spec, location, calendar, groups]
sources: [resources/views/livewire/ride-calendar.blade.php, app/Livewire/RideCalendar.php, resources/views/groups/index.blade.php, app/Models/Group.php, app/Models/Activity.php]
phase: design
updated: 2026-06-05
---

# Locatie-zoeken: kalender en lokale groepen

## Probleem

Beide pagina's laten je nu "je gemeente" kiezen, maar op een andere manier en met een
verkeerd werkwoord. De kalender **filtert op exact één gemeente**: kies je Jette, dan
verdwijnt Koekelberg, terwijl mensen net graag in buurgemeenten meefietsen. De lege
staat geeft het zelf toe ("Kies 'Alle gemeenten' voor fietstochten in de buurt"). De
groepenpagina heeft helemaal geen zoek, enkel een platte directory per regio met een
"kaart komt binnenkort"-belofte.

## Kernidee

**Eén locatie, twee journeys, sorteren in plaats van filteren.**

- **Eén gedeeld primitief.** De bezoeker zet één keer "Waar woon je?". Dat wordt onthouden
  en geldt site-breed (home, kalender, groepen). Niets wordt ooit weggefilterd; locatie
  *herordent* alleen.
- **Twee onafhankelijke signalen.**
  - *Locatie* (waar woon je) levert "in de buurt", op beide pagina's. Een berekening.
    Wijst nooit automatisch een groep toe.
  - *Lidmaatschap* (account + bewust lid worden) levert "jouw groep(en)". Een keuze van
    de gebruiker, kan meerdere groepen zijn, kan in een andere gemeente dan je woonplaats.
- **Geen kaart.** Valt buiten scope; de "binnenkort"-vermelding verdwijnt. De gesorteerde
  lijst is de werkende vind-tool.

## Beslissingen (vastgelegd tijdens brainstorm)

| Onderwerp | Keuze |
|---|---|
| Werkwoord | Sorteren + markeren, niet filteren |
| Invoer locatie | Postcode-veld met suggesties (basis) + "gebruik mijn locatie" geolocatie (snelkoppeling, valt terug op postcode) |
| Proximity-model | Echte afstand via postcode-coördinatentabel (haversine), niet numerieke postcode |
| Gedeeld vs per-pagina | Eén locatie, onthouden, beide pagina's lezen ze |
| Kalender-layout | Twee banden: "In de buurt" (op tijd) en "Verderaf" eronder |
| Groepen-layout | "In de buurt" band + bestaande regio-directory; ingelogd lid → "jouw groep(en)" bovenaan |
| Straal | Vast op 7 km, geen slider |
| Kaart | Geen, ook de vermelding weg |

## Architectuur — vier eenheden

### 1. Postcode → coördinaten (`postal_codes` seed + lookup)

Een opzoektabel met de Belgische postcodes en hun coördinaten, eenmalig geseed uit een
open dataset (Geopunt / bpost, ~1150 rijen).

- Migratie `postal_codes`: `zip` (string, geïndexeerd), `name`, `latitude` (decimal),
  `longitude` (decimal), `region` (Brussels Capital Region / Flanders / Wallonia).
- Seeder leest de dataset (CSV in `database/data/`) en vult de tabel.
- `Group.zip` en `Activity.postal_code` bestaan al; die joinen we op `postal_codes.zip`
  om coördinaten te krijgen.

**Interface:** `PostalCode::coordinatesFor(string $zip): ?array` (geeft `['lat','lng']`
of `null` bij onbekende postcode).

### 2. Proximity-service (`app/Support/Location/`)

Stateless rekenhulp. Geen kennis van Blade of Livewire.

- `distanceKm(array $from, array $to): float` — haversine.
- `partitionByRadius(Collection $items, array $origin, float $radiusKm, callable $zipOf): array`
  — geeft `['nearby' => ..., 'far' => ...]`, elk met de afstand erbij geannoteerd.
- Vaste straal uit config: `config('location.nearby_radius_km')`, default **7**. "Echt
  dichtbij": in Brussel je aangrenzende gemeenten (Jette → Schaarbeek ~6 km valt er net
  in), bij Vlaamse/Waalse dorpen de directe buren. Verder weg zakt naar "verderaf".
- Onbekende postcode of item zonder coördinaten → telt als "far" (nooit verbergen).

### 3. Gedeeld locatie-primitief (`LocationPicker` Livewire + cookie)

De bezoekerslocatie leeft in een **cookie** (`kcm_location`), zodat server-side rendering
(Livewire-kalender, Blade-groepen) ze zonder JS-rondje kan lezen. Inhoud: gekozen `zip`,
afgeleide `lat`/`lng`, en `name` voor weergave.

- `LocationPicker` (Livewire component, herbruikbaar):
  - Postcode-veld met autocomplete uit `postal_codes` (basis).
  - "Gebruik mijn locatie"-knop: JS-geolocatie → dichtstbijzijnde postcode opzoeken →
    in de cookie. Valt bij weigering/fout terug op het postcode-veld.
  - "Wijzig"-affordance als er al een locatie staat.
  - Schrijft de cookie en dispatcht een event zodat de pagina herrendert.
- **Ingelogde gebruiker:** als `users` later een `home_zip` krijgt, prefereert die boven de
  cookie. Nu nog niet nodig; cookie volstaat. (Buiten scope, wel voorzien.)
- **Resolver** `CurrentLocation::resolve(): ?array` — leest cookie (later: user), geeft
  `['zip','lat','lng','name']` of `null`.

### 4. De twee pagina's (consumenten)

**Kalender (`RideCalendar` Livewire):**

- De `gemeente`-select verdwijnt; de hero-control wordt de `LocationPicker`.
- Aankomende ritten: `partitionByRadius` → twee banden. **In de buurt** (≤ straal, op
  datum gesorteerd) met een streep, daaronder **Verderaf** (op datum). Afstandslabel per
  rit ("in jouw buurt", "3 km", "52 km").
- Voorbije ritten: ongewijzigd (geen banden).
- **Geen locatie gezet:** één lijst op datum zoals nu, met een rustige uitnodiging om je
  plek in te stellen. Nooit een poort.

**Lokale groepen (`groups/index`):**

- De `LocationPicker` in de hero.
- Boven de bestaande regio-directory: een **"In de buurt van {plaats}"** band — groepen
  binnen de straal, op afstand gesorteerd, als pills met afstandslabel.
- **Ingelogd én lid:** "Jouw groep(en)" bovenaan vastgepind (uit `auth()->user()->groups`),
  los van woonplaats. Anoniem of geen lidmaatschap: dit blok valt weg.
- De "kaart komt binnenkort"-note verdwijnt.
- **Geen locatie gezet:** enkel de regio-directory zoals nu + uitnodiging.
- Groep zonder `zip` → niet in de buurt-band, wel in de regio-directory.

## Edge cases

- **Locatie gezet, niets in de buurt:** "in de buurt"-band toont een vriendelijke lege
  staat; "verderaf" / volledige directory blijft staan.
- **Onbekende postcode** (niet in tabel): invoer wordt aanvaard maar levert geen
  coördinaten → terugval op "geen proximity" (toon alles) met een korte note.
- **Geolocatie geweigerd:** stil terugvallen op het postcode-veld.
- **Brussel vs rest:** opgevangen door de vaste straal, geen aparte logica.

## Copy

Alle interface-copy volgt `docs/tone-of-voice.md` (NL, warm en concreet, geen
em-dashes). Bandtitels: "In de buurt van {plaats}", "Verderaf". Lege buurt-staat en
de uitnodiging om je plek te zetten worden warm en uitnodigend geformuleerd, niet
technisch.

## Testen

- **Unit** — `distanceKm` (bekende afstanden), `partitionByRadius` (banden, annotatie,
  onbekende postcode → far).
- **Feature** — cookie zetten/lezen via `LocationPicker`; kalender toont twee banden bij
  gezette locatie; kalender toont één lijst zonder locatie; groepen tonen buurt-band;
  ingelogd lid ziet "jouw groep" bovenaan; anoniem niet; onbekende postcode valt terug.
- Volgens projectregel: elke wijziging programmatisch getest, `php artisan test --compact`
  met filter.

## Bewust buiten scope

- Kaart (in welke vorm dan ook).
- `users.home_zip` persistentie (cookie volstaat nu; wel voorzien in de resolver).
- Radius-slider voor de gebruiker (vaste straal volstaat; later eventueel).
- Meertaligheid van de postcodenamen (NL-only laag, conform huidige site).

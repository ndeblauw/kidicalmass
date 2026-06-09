---
title: Lokale groep-pagina — Research (as-is) + contentmodel
tags: [content, research, chapters]
sources: [raw/website/5000.md, raw/website/7000.md, raw/website/wallonie.md, raw/website/agenda.md, web]
phase: design
updated: 2026-06-09
---

# Lokale groep-pagina — Research (as-is) + contentmodel

*Hoe de huidige lokale groepen hun eigen pagina (`kidicalmass.be/<postcode>`) vandaag gebruiken, en wat dat leert voor het contentmodel van de nieuwe **[Chapter Page (P-11)](chapters.md)**. Voedt [`chapters-content.md`](chapters-content.md) PART 2.*

**Bron-caveat:** alleen `/5000` (Namur) en `/7000` (Mons) zitten in de [scrape](../../raw/website/5000.md); de overige ~12 pagina's zijn **live opgehaald op 2026-06-09** (sample, geen volledige inventaris). Dekking is bewust gemeten als observatie, niet als telling.

---

## 1. As-is: waar de pagina's leven

Lokale groepen leven vandaag op **drie verschillende plekken** — de eerste vaststelling:

1. **Brusselse gemeenten** → pagina op `kidicalmass.be/<postcode>` (bv. `/1030` Schaarbeek, `/1190` Vorst). Tweetalig FR/NL.
2. **Waalse chapters** → óók een postcode-pagina (`/5000` Namur, `/7000` Mons), maar FR-only en duidelijk autonomer.
3. **Volledig onafhankelijk** → Luik draait een **eigen domein** (`kidicalmassliege.org`), niet op de Wix-site.

**Dekking is partieel en inconsistent.** Verschillende groepen die actief op de agenda staan hebben **geen pagina** en vallen terug op een gedeeld Facebook-event: Ukkel, beide Woluwe's (1150/1200), Evere, Koekelberg, Ganshoren gaven 404. Home en agenda linken bovendien bijna alles naar **één gedeeld FB-event-ID** (`1669046660645535`) i.p.v. naar de eigen groeppagina. Het pad van bezoeker → specifieke groep is dus onbetrouwbaar.

## 2. As-is: anatomie van een typische pagina (de gedeelde ~80%)

Bijna elke Brusselse pagina is hetzelfde skelet met een paar velden gewisseld:

| Blok | Opmerking |
|---|---|
| Titel | `Kidical Mass <postcode> \| <Gemeente>` |
| "Wat is een Kidical Mass" intro | Boilerplate, bijna woordelijk overgenomen van de home |
| Praktische info | De 7 vaste bullets (vertrek / alle leeftijden / fiets geen loopfiets / 5–7 km traag / max 1u / gratis, geen inschrijving / muziek) — quasi letterlijk van de home gekopieerd |
| Datalijst | 1–3 komende ritten |
| Vrijwilligersoproep | "word coördinator / begeleider / DJ / partner" |
| **Lokale partners** | Het enige echt lokale veld (zie onder) |
| Contact-e-mail | Meestal het **centrale** `bike@kidicalmass.be` |
| Foto-toestemming | Standaard disclaimer |
| Socials + crowdfunding-QR | Overal hetzelfde |

→ Ruwweg **driekwart van elke pagina is gedupliceerde content.** Alleen **partners, data en startlocatie** dragen echt lokaal signaal.

## 3. As-is: sample-vergelijking

| Pagina | Data | Startlocatie | Contact | Historiek? | Downloads | Lokale kleur |
|---|---|---|---|---|---|---|
| 1000 Bxl-Stad | 1 | Sint-Katelijneplein (TBA) | centraal | – | flyer | Cyclo, Cyclonativa, Velodroom |
| 1030 Schaarbeek | 2 | **Vast**: Parc Josaphat | centraal | – | – | Helmet en Transition, Avello 1030, Grafik, Atelier 238 |
| 1040 Etterbeek | 1 | Roteert | centraal | – | – | Fietsbieb, Vélophil, MAHMA, Loopz |
| 1050 Elsene | 1 | TBA | centraal | – | flyer | + Gemeente & Brussel Mobiliteit als co-org |
| 1070 Anderlecht | **geen** ("nieuwe data 2026") | geen | centraal | – | sponsor + charter | Molenbeek à Vélo, Bike for Brussels — eigenlijk een stub |
| 1090 Jette | 1 | Roteert | + named PR (Cecilia) | – | sponsor + charter | Avello, Vélokanik, Staytion, Labolobo |
| 1120 NOH | 1 | Roteert | centraal | – | flyer | Vers'ailes, Amo Noh, Ride Your Future |
| 1160-1170 WB+Oudergem | 3 | **Vast**: Parc Seny | centraal | – | – | **Twee gemeenten op één pagina** |
| 1190 Vorst | 3 | Wisselt hoog/laag Vorst | groeps-mail | – | – | Cargobike-DJ, Roule Forest, Monkey Cycles |
| **5000 Namur** | 1 | Place du Théâtre | **lokaal** (Sindy) | partner-rijk | flyer | **Perssectie (RTBF), fotogalerij, editie-nummering, FR-only** |
| **7000 Mons** | 3 | Théâtre le Manège | **eigen subdomein** mons.bike | **Volledige "Historique"** | flyers | **Rijkste pagina**: named organisatoren, tombola/onthaal-verhaal, pers, aftermovie |
| **Luik** | eigen | Place Xavier Neujean | eigen mail | eigen origin-verhaal | – | **Eigen domein**, embedded kalender, hindernissenparcours + "freestyle moment" |

## 4. As-is: patronen & uitschieters

**Terugkerend**
- Praktische-info-blok is **van de home gekopieerd** op elke pagina — globale content vermomd als lokaal.
- **Centraal contact domineert**; alleen de autonome chapters (Namur, Mons, Luik) + een paar PR-getagde pagina's hebben een echte lokale mens.
- **Startlocatie** is het operationeel belangrijkste veld én het minst consistent: vast adres vs "roteert elke editie" vs "TBA".
- **Partnerlijst = de echte lokale identiteit** — altijd uniek, altijd aanwezig, nooit visueel behandeld (platte tekst, geen logo's).
- Tweetalig FR/NL in Brussel, FR-only in Wallonië.

**Uitschieters**
- **Mons (7000)** — leest als een echte lokale microsite: narratieve "Historique" met named organisatoren, deelnemersaantallen, tombola/onthaal-ritueel, pers, eigen subdomein. Wat een empowered chapter met de ruimte doet.
- **Namur (5000)** — eigen **perssectie** (twee RTBF-artikels), fotogalerij, "#édition #2"-nummering, named lokaal contact.
- **Luik** — volledig buiten het platform; eigen domein met features die geen Brusselse pagina heeft.
- **Anderlecht (1070)** — de **lege stub**: geen concrete datum/locatie, vrijwel alleen boilerplate + sponsor/charter-PDF's. Toont hoe een pagina eruitziet als niemand lokaal onderhoudt.
- **1160-1170** — twee gemeenten bewust **samengevoegd op één pagina**, breekt de "één-postcode-één-pagina"-aanname.

---

## 5. Synthese → contentmodel voor P-11

De observaties splitsen de pagina in twee soorten content. (Beslissingen Frederik, 2026-06-09.)

### A. Statische / template-items (gedeeld, geërfd — niet per groep overgetypt)
Dit is de gedupliceerde ~75% van vandaag. In het nieuwe model is dit **netwerk-content die automatisch op de groeppagina verschijnt**, niet door de kapitein onderhouden:
- "Wat is een Kidical Mass" intro
- Praktische-info-blok (de 7 bullets)
- Vrijwilligers-/word-coördinator-oproep (J2-form, al gebouwd op P-11)
- Foto-toestemming, socials, crowdfunding

→ Kill de duplicatie + de staleness: schrijf het één keer centraal, render het overal.

### B. Dynamische / per-groep items (de lokale identiteit)
Wat de kapitein/lokale groep wél vult en beheert:

| Item | Inhoud | Beheer |
|---|---|---|
| **Upcoming rides** | Komende ritten (datum + startlocatie) | Data, één bron, ook in globale kalender |
| **Startlocatie** | First-class veld: **vast adres** OF "zie event" — consistent patroon, niet langer TBA/roteert door elkaar | per groep |
| **Lokale partners** | Echte behandeling (logo's, links), niet komma-string | per groep |
| **Foto's / fotogalerij** | Veel foto's; sfeerband + galerij (cf. Namur) | per groep |
| **Historiek** | Narratief origin-/jaarverhaal (cf. Mons) | per groep |
| **Pers** | Lokale persknipsels gelinkt aan de groep | dual-level, zie §6 |
| **Downloads** | Posters, flyers, officiële documenten | dual-level, zie §6 |
| **Auto-statistieken** | **Aantal ritten dat al gereden is** (belangrijkste) — automatisch geteld, "iets wat hier gestart is" | afgeleid uit data, geen invoer |

### C. Pers & Downloads — nationaal beheerd, lokaal gelokaliseerd
Beide kennen **twee niveaus**:
- **Nationaal**: een algemene perssectie (Kidical Mass-brede persartikels) en algemene downloads. *(Wat precies op Kidical-Mass-niveau hoort = nog open — zie §7.)*
- **Lokaal**: elke groep kan **eigen pers opladen** (gelinkt aan die groep) en **eigen downloads** (lokale posters/flyers/officials, op groepsniveau).

### D. Bewerkbaarheid — kapiteins = lokale beheerders
**Kapiteins (chapter leads / lokale verantwoordelijken)** bewerken hun groep in **Filament admin** (P-21). Minstens bewerkbaar:
- Downloads (op/afladen)
- Historiek
- Foto's
- Partners

*(Coördinatieduo = alles; kapitein = enkel eigen groep. Sluit aan op het bestaande P-21 rolmodel.)*

---

## 6. Implicaties / open punten voor de design

1. **Twee tiers bestaan echt** en het model moet beide dragen: een *dunne template-gemeentepagina* (erft alle boilerplate; lokaal vult partners + data + startpunt) én een *rijker autonoom chapter* (eigen verhaal, historiek, contact, pers — Mons/Namur/Luik). Niet in één mal duwen.
2. **Auto-stat "aantal ritten"** vergt dat ritten als data per groep geteld worden (koppelt aan de Activity-/rides-bron).
3. **Elke actieve groep verdient minstens een auto-gegenereerde minimale pagina** zodat geen enkele degradeert naar een kale FB-link (~6 actieve groepen hebben vandaag geen pagina).
4. **Open — nationaal niveau:** wat staat er op pers/downloads op Kidicom-/netwerk-niveau (vs. puur lokaal)? Nog te bepalen.
5. **Hide-if-empty** blijft het patroon ([PAT-11](../40-patterns.md)): historiek/pers/galerij tonen enkel wanneer gevuld, zodat een just-started groep niet leeg oogt (cf. de designed empty state in [`chapters.md`](chapters.md)).

> Sluit aan op de backend-spec voor Nico in [`chapters.md` § P-11](chapters.md) ([#37](https://github.com/ndeblauw/kidicalmass/issues/37)): per-groep `intro`/cover/role/lead-email + single-group `Email subscription`. Dit dossier breidt die spec uit met **historiek, fotogalerij, per-groep pers, per-groep downloads, en de auto-rit-teller**, allemaal kapitein-bewerkbaar in Filament.

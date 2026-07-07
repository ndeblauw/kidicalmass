# Copy-briefing NL — feedbackdocument voor het coördinatieduo

**Datum:** 2026-07-07 · **Status:** approved (brainstorm-dialoog Frederik)

## Doel

Laetitia en Cecilia vroegen waarmee ze verder kunnen qua copywriting. Eén Google Doc
("Copywriting NL — kidicalmass.be", suggestie-modus) verzamelt alle statische NL-copy
van de publieke site zodat zij tekstfeedback kunnen geven die wij 1-op-1 in code
verwerken. CMS-content loopt via de admin, niet via het doc.

## Beslissingen

- **Site-toegang:** preview op `https://kidicalmass.on-forge.com/nl`; elke paginasectie linkt ernaar.
- **Structuur op slot:** copy-first. Dynamische componenten staan als één grijze regel
  (`▒ DYNAMISCH — … ▒`) zonder detail, zodat er geen structuurfeedback wordt uitgelokt.
  Structuurzorgen mogen als losse opmerking, maar we vragen er niet naar.
- **Scope:** alle publieke pagina's in één ronde (P-01–P-06, P-10–P-20, P-23, P-24)
  plus een sectie "Site-breed" (nav, footer, gedeelde componenten). Buiten scope:
  login, roze-hesjes-hub (niet publiek), FR/EN (na NL-lock), CMS-inhoud zelf.
  P-03 (rit-detail) gaat mee met een kanttekening: herontwerp gevraagd (07-07),
  kernteksten toch al nakijkbaar.
- **CMS-content:** via de admin; het doc bevat per pagina een to-do-blok, gevoed door
  de CMS-kolom (chase-list) + `[client]`-items uit het pagina-register.
- **Medium:** Google Doc, suggestie-modus; kort begeleidend mailtje van Frederik.
- **Briefing:** async, met leeswijzer-hoofdstuk in het doc: wat wel/niet, hoe
  suggestie-modus werkt, tone-of-voice-samenvatting (4 kwaliteiten + éénregel-test),
  twee sporen (doc vs admin), deadline: zij stellen zelf een haalbare datum voor.
- **Formaat per pagina:** leesscript in scrollvolgorde. Vet element-label
  (Kop/Subkop/Tekst/knop/link/veld), statische copy letterlijk uit Blade + `lang/nl`,
  dynamiek als grijze éénregel, gedeelde componenten éénmalig in "Site-breed" en elders
  als verwijzing, to-do-kader onderaan. Rit-detail: drie tijdsvarianten kort benoemd.
  Privacy gemarkeerd als juridische tekst.
- **Volgorde:** bezoekersreis: Site-breed → Home → Kalender → Rit-detail → Zo werkt een
  rit → Help mee → Lokale groepen → Groepspagina → Start een groep → Steun ons →
  Nieuwsbrief → Over ons (hub → missie → visie → organisatie → nieuws → pers →
  partners) → Contact → Privacy.

## Werkwijze

Copy wordt rechtstreeks uit Blade-templates + lang-files geëxtraheerd (bron = code),
per pagina, zodat elke suggestie terugvindbaar is. Verwerking na feedback: suggesties
→ code (één commit per pagina), to-do's → duo in admin, daarna Conf-scores omhoog en
OK-kolom in zicht.

## Open

- Hebben Laetitia en Cecilia werkende admin-logins? (voorwaarde voor de to-do's)
- Deadline: komt van het duo zelf.

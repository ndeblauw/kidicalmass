---
title: Events Overview — Content
tags: [content]
sources: [ux/events-overview.md]
updated: 2026-04-13
---

*Content companion to [events-overview.md](events-overview.md). All copy in English. FR/NL notes inline.*

---

## Page header

**H1**

| EN | FR | NL |
|---|---|---|
| Events | Événements | Ritten |

**Subtitle**

| EN | FR | NL |
|---|---|---|
| Find a ride near you | Trouvez une balade près de chez vous | Vind een rit bij jou in de buurt |

*FR note:* "balade" is warmer than "événement" or "trajet" — it carries the leisurely, neighbourhood feel.
*NL note:* "rit" is direct and casual; avoid "evenement" which sounds corporate.

---

## Filter bar

**Toggle labels**

| EN | FR | NL |
|---|---|---|
| Upcoming | À venir | Aankomend |
| Past | Passées | Voorbije |

*NL note:* "Aankomend" / "Voorbije" is slightly warmer than "Komende" / "Afgelopen" — shorter on mobile.

**Location dropdown label**

| EN | FR | NL |
|---|---|---|
| Location | Commune | Gemeente |

**Dropdown placeholder (unfiltered state)**

| EN | FR | NL |
|---|---|---|
| All locations | Toutes les communes | Alle gemeenten |

*Note:* Use the same placeholder text as the selected label when "all" is active — it doubles as the reset affordance.

---

## Date group headers (upcoming mode)

**Standard format**

Long-form (desktop): `[Day of week], [D] [Month]`
Short-form (mobile): `[Day abbrev.] [D] [Month]`

| EN (long) | FR (long) | NL (long) |
|---|---|---|
| Saturday, 19 April | Samedi 19 avril | Zaterdag 19 april |

| EN (short) | FR (short) | NL (short) |
|---|---|---|
| Sat 19 April | Sam. 19 avril | Za 19 april |

*Note:* Month names are lowercase in FR and NL. EN capitalises.

**Contextual labels — "Today" and "Tomorrow"**

Replace the date string with these labels when applicable. Keep the full date as a secondary/sub-label in smaller type for orientation.

| EN | FR | NL |
|---|---|---|
| Today | Aujourd'hui | Vandaag |
| Tomorrow | Demain | Morgen |

*Example (EN):* `Today · Saturday, 19 April`
*Example (FR):* `Aujourd'hui · samedi 19 avril`
*Example (NL):* `Vandaag · zaterdag 19 april`

**Grande Kidical Mass — date header badge**

The date group header for the Grande Kidical Mass weekend carries an additional label alongside the star icon.

| EN | FR | NL |
|---|---|---|
| ★ Featured | ★ À la une | ★ Uitgelicht |

*Full date header example (EN):* `Saturday, 10 May  ★ Featured`
*Full date header example (FR):* `Samedi 10 mai  ★ À la une`
*Full date header example (NL):* `Zaterdag 10 mei  ★ Uitgelicht`

---

## Event cards

### Standard card template

**Title**
`Kidical Mass [Municipality]`
For bilingual chapters (Brussels municipalities with both FR and NL names):
`Kidical Mass [FR Name] – [NL Name]`

*Examples:*
- Single name: `Kidical Mass Mons` — `Kidical Mass Namur`
- Bilingual: `Kidical Mass Ixelles – Elsene` — `Kidical Mass Forest – Vorst` — `Kidical Mass Evere – Haren`
- Brussels-wide: `Kidical Mass Bruxelles – Brussel`

*Note:* Use the officially bilingual form for Brussels municipalities. Walloon and Flemish chapters use their single regional name.

**Time**
`[HH:MM]`
*(Examples: `15:00` · `14:00` · `10:30`)*

**Municipality**
`[Municipality name]`
*(Same as the primary name in the card title — no translation needed here.)*

**Meeting point**
`[Place name / street name]`
Truncated to one line if necessary.
*(Examples: `Place Flagey` · `Place Colignon` · `Parvis St-Denis` · `Place du Trône`)*

**Full card example (EN):**
```
Kidical Mass Ixelles – Elsene     15:00
Ixelles · Place Flagey
```

**Full card example (FR):**
```
Kidical Mass Ixelles – Elsene     15:00
Ixelles · Place Flagey
```

**Full card example (NL):**
```
Kidical Mass Ixelles – Elsene     15:00
Elsene · Place Flagey
```

*NL note:* In NL context, use the NL municipality name (`Elsene`) rather than the FR form, but the event title retains both. Meeting point names stay in their local language.

---

### Grande Kidical Mass card variant

Same card structure. Differences:
- ★ icon prefix before the title
- Title is all-caps for visual distinction

**Title**

| EN | FR | NL |
|---|---|---|
| ★  GRANDE KIDICAL MASS 2026 | ★  GRANDE KIDICAL MASS 2026 | ★  GRANDE KIDICAL MASS 2026 |

*Note:* "Grande Kidical Mass" is a proper name — keep it as-is across all three languages. Do not translate "Grande".

**Full card example:**
```
★  GRANDE KIDICAL MASS 2026     15:00
Bruxelles · Place du Trône
```

---

### Past event cards

Same field structure as upcoming cards. Visual treatment: muted text, lighter border — signals archive, not action. No copy changes needed; the filter toggle ("Past") and the month-grouped layout communicate the context.

*Accessibility note:* Do not rely on colour alone. The date and the "Past" toggle state together communicate archive mode.

---

## Month group headers (past mode)

**Format:** `[Month] [Year]`

| EN | FR | NL |
|---|---|---|
| March 2026 | Mars 2026 | Maart 2026 |
| February 2026 | Février 2026 | Februari 2026 |
| October 2025 | Octobre 2025 | Oktober 2025 |

*Note:* Month names capitalised in EN. Lowercase in FR and NL.

---

## Empty states

### No upcoming events (season break or truly empty)

Shown when the upcoming list is empty and no location filter is active.

| Language | Copy |
|---|---|
| EN | No upcoming rides right now. The season runs from March to November — check back soon! |
| FR | Pas de balade prévue pour l'instant. La saison s'étend de mars à novembre — revenez bientôt ! |
| NL | Voorlopig geen ritten gepland. Het seizoen loopt van maart tot november — kom snel terug! |

*Tone note:* "check back soon" / "revenez bientôt" keeps the door open without manufactured urgency. The season range is the reassurance — it tells the family the movement is active and predictable.

---

### No results after location filter

Shown when a location filter is active and returns zero upcoming events.

| Language | Copy |
|---|---|
| EN | No upcoming rides in [Municipality]. Try 'All locations' to see rides nearby. |
| FR | Pas de balade à venir à [Commune]. Essayez « Toutes les communes » pour voir les balades proches. |
| NL | Geen aankomende ritten in [Gemeente]. Probeer « Alle gemeenten » voor ritten in de buurt. |

*Note:* `[Municipality]` / `[Commune]` / `[Gemeente]` is populated dynamically with the selected filter value (e.g. "Mons", "Schaerbeek", "Gent"). The quoted string `'All locations'` / `« Toutes les communes »` / `« Alle gemeenten »` should visually match the UI control label — use the same typography or inline code style.

---

## Meta

### Page title tag

| EN | FR | NL |
|---|---|---|
| Events — Kidical Mass Belgium | Événements — Kidical Mass Belgique | Ritten — Kidical Mass België |

*Note:* Short and descriptive. The brand name anchors it. No taglines in the title tag — keep it under 60 characters.

### Meta description

| Language | Copy |
|---|---|
| EN | Find a Kidical Mass bike ride near you. Family-friendly parades for children of all ages, across Belgium. |
| FR | Trouvez une balade Kidical Mass près de chez vous. Des cortèges vélo pour familles et enfants dans toute la Belgique. |
| NL | Vind een Kidical Mass rit bij jou in de buurt. Fietsparades voor families en kinderen door heel België. |

*Note:* Under 160 characters each. No exclamation marks in meta descriptions — they can read as spam in SERPs. "Family-friendly" / "voor families" does the reassurance work without over-promising.

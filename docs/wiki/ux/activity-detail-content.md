---
title: Activity Detail — Content
tags: [content]
sources: [ux/activity-detail.md]
updated: 2026-04-13
---

*Content companion to [activity-detail.md](activity-detail.md). Template copy for the `/events/[slug]` page. All copy in English with FR/NL notes. Database-driven fields shown as `[variables]`; static copy written out fully.*

---

## Hero — left panel

**Chapter name (above the title)**
`[Chapter name]`
e.g. "Kidical Mass Schaerbeek" or "Kidical Mass Mons"

**Page title**
`Kidical Mass [Postal code] — [Date]`
e.g. "Kidical Mass 1030 — 31 May"

> Title convention: postal code + date. Language-neutral. Never use the municipality name here — the postal code identifies the chapter clearly and follows existing Facebook convention.

**Day + time**
`[Day of week], [Date] · [Start time]`

EN: "Sunday, 31 May · 3:00 pm"
FR: "Dimanche 31 mai · 15h00"
NL: "Zondag 31 mei · 15u00"

**Meeting point**
`[Named landmark], [Street or neighbourhood]`

EN: "Starting from [Named landmark]"
FR: "Départ depuis [point de repère nommé]"
NL: "Vertrek aan [herkenbaar punt]"

> Always use a named landmark, never coordinates. A named place makes it feel like a neighbourhood event, not a logistics form.

**Action buttons**

| Element | EN | FR | NL |
|---|---|---|---|
| Calendar button | Add to calendar | Ajouter à l'agenda | Zet in agenda |
| Share button | Share | Partager | Delen |

---

## Hero — right panel (map)

Komoot route embed. No copy. The map speaks for itself: a coloured route through the neighbourhood, a start marker, the streets you'll cycle.

> The map carries the emotional argument. A route winding through a real neighbourhood is more persuasive than any sentence about safe cycling.

**Accessibility alt text for the map container:** "Route map for Kidical Mass [Municipality] on [Date]"
*(FR: "Carte du parcours pour Kidical Mass [Commune] le [Date]" | NL: "Routekaart voor Kidical Mass [Gemeente] op [Datum]")*

---

## Practical strip

One scannable line. Pipe-separated on desktop, stacked on mobile. Labels are static; values are database-driven.

**Full strip (EN):**
`[X] km · max [Y] min · Free · All ages · Music along the way · Children accompanied by an adult`

**Label translations:**

| Element | EN | FR | NL |
|---|---|---|---|
| Distance | `[X] km` | `[X] km` | `[X] km` |
| Duration | `max [Y] min` | `max [Y] min` | `max [Y] min` |
| Admission | Free | Gratuit | Gratis |
| Age | All ages | Tous les âges | Alle leeftijden |
| Music | Music along the way | Musique en route | Muziek onderweg | The music note icon (🎵) is a UI element, not part of the copy string — the icon is rendered by the component, not typed into the database field. |
| Guardian note | Children accompanied by an adult | Enfants accompagnés d'un adulte | Kinderen begeleid door een volwassene |

> The guardian note is always shown — it is both reassurance for parents and a legal responsibility signal. Keep it as a short label, not a paragraph.

---

## What to expect

### Default body (used when no event-specific notes are set)

**EN:**
We ride at the pace of the youngest. Expect music, new friends, and a different way of seeing your neighbourhood — together, one pedal stroke at a time.

**FR:**
On roule au rythme du plus petit. De la musique, de nouvelles rencontres, et un regard différent sur ton quartier — ensemble, pédale après pédale.

**NL:**
We rijden op het tempo van de jongste. Muziek, nieuwe vrienden, en je buurt van een andere kant — samen, stap voor stap.

> 2–3 lines max. Sensory first, context second. Never open with a policy statement — open with what you will see, hear, feel.

---

### Event-specific theme copy (optional override)

When the event has a name, campaign, or theme (e.g. "Spooky Edition", "Safety First", a partner collaboration), add a short line or two after the default body — or replace it entirely if the theme warrants it.

**Example — Spooky Edition (EN):**
This one's got costumes. Dress your bike, dress your kids, dress yourself. We ride at the pace of the youngest — ghosts included.

**FR:**
Déguisements bienvenus. Vélos décorés, enfants en costume, adultes aussi. On pédale ensemble, même les fantômes.

**NL:**
Kostuums welkom. Versier je fiets, kom verkleed, en rij mee — ook spoken rijden op het tempo van de jongste.

**Example — Safety First campaign (EN):**
This ride is part of the 2026 Safety First campaign — a push for safer school streets across Belgium. Come make some noise.

**FR:**
Cette balade fait partie de la campagne Safety First 2026 — pour des abords d'école plus sûrs partout en Belgique. Venez faire entendre votre voix.

**NL:**
Deze rit is onderdeel van de Safety First-campagne 2026 — voor veiligere schoolomgevingen in heel België. Kom mee.

> 2–3 lines max. Sensory first, context second. Never open with a policy statement — open with what you will see, hear, feel.

---

## Chapter context

**EN:**
This ride is part of [Chapter name]'s monthly series →

**FR:**
Cette balade fait partie de la série mensuelle de [Nom du chapitre] →

**NL:**
Deze rit is onderdeel van de maandelijkse reeks van [Naam hoofdstuk] →

> The arrow links to the chapter page. One sentence. No list of future dates here — those live on the chapter page and the events index.

---

## Organising team + volunteer ask

**Section heading:**

EN: "Organised by"
FR: "Organisé par"
NL: "Georganiseerd door"

**Team display:**
`[Name] · [Name] · [Name]`

Names only — no titles, no organisation labels. The list should feel like neighbours, not a board of directors.

**Volunteer ask (immediately below the team names):**

EN: Want to ride alongside them as a pink vest? [→](/help-out)
FR: Tu veux rouler avec eux comme gilet rose ? [→](/help-out)
NL: Wil je meerijden als roze hesje? [→](/help-out)

> The volunteer ask links to `/help-out`. The arrow is the link — keep the copy short, the warmth in the framing ("alongside them"), and the explanation of "pink vest" on the `/help-out` page rather than here.

---

## Local partners

**Section heading (shown only when partners exist):**

EN: "Partners"
FR: "Partenaires"
NL: "Partners"

Logos where available, names otherwise. No taglines, no descriptions. The section is hidden when no local partners are set for this event.

---

## Photo permission

**EN (final copy):**
Photos are taken during the ride. By taking part, you agree to the publication of these photos on our channels.

**FR:**
Des photos sont prises pendant la balade. En participant, vous acceptez la publication de ces photos sur nos canaux.

**NL:**
Tijdens de rit worden foto's gemaakt. Door deel te nemen ga je akkoord met publicatie op onze kanalen.

> Display at small text size, below the partners section. Visually quiet — this line must be present for legal reasons but should not compete for attention. No bold, no icon, no coloured background.

---

## Meta template

Used for `<title>`, `<meta name="description">`, and Open Graph tags (for WhatsApp previews and Google results).

**Page title:**
`Kidical Mass [Postal code] — [Day] [Date] | [Chapter name]`

e.g. "Kidical Mass 1030 — Sunday 31 May | Kidical Mass Schaerbeek"

**Meta description (EN default):**
Join us for a joyful family bike ride in [Municipality] on [Day] [Date]. Free, all ages, music along the way. Starting from [Named landmark].

**FR:**
Rejoignez-nous pour une balade à vélo en famille à [Commune] le [Jour] [Date]. Gratuit, tous les âges, avec de la musique. Départ depuis [point de repère].

**NL:**
Kom mee fietsen met het gezin in [Gemeente] op [Dag] [Datum]. Gratis, alle leeftijden, met muziek onderweg. Vertrek aan [herkenbaar punt].

**Open Graph image:** use a photo from a previous ride in this chapter if available; fall back to the Kidical Mass Belgium default event image.

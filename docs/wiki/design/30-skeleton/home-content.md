---
title: Home — Content
tags: [content]
sources: [ux/home.md]
phase: design
updated: 2026-04-13
---

*Content companion to [home.md](home.md). All copy in English. FR/NL equivalents inline per section.*

---

## Hero

### Headline

**EN:** Kids on bikes. Together.
**FR:** Des kets à vélo. Ensemble.
**NL:** Kinderen op de fiets. Samen.

> ⚠️ "Kets" is Brussels dialect — confirm with Leticia whether this is the intended national FR headline or a Brussels-specific variant.

*Mobile variant (line-break at "Together." / "Ensemble." / "Samen." on its own line) — same copy, same weight.*

### Subheadline

**EN:** Every month, hundreds of children ride through Belgian streets — safely, together, with music. Free for everyone.
**FR:** Chaque mois, des centaines d'enfants pédalent dans les rues belges — en toute sécurité, ensemble, avec de la musique. Gratuit pour tous.
**NL:** Elke maand rijden honderden kinderen door de Belgische straten — veilig, samen, met muziek. Gratis voor iedereen.

### Primary CTA

**EN:** Find a ride →
**FR:** Trouver une balade →
**NL:** Vind een fietstocht →

*Solid button. Leads to /events.*

### Secondary CTA

**EN:** New here? Start here →
**FR:** Première fois ? Commencez ici →
**NL:** Eerste keer? Begin hier →

*Text link, not a button. Leads to /getting-started.*

---

## Upcoming rides section

### Section heading

**EN:** Upcoming rides
**FR:** Prochaines balades
**NL:** Volgende fietstochten

### "See all" link label

**EN:** See all →
**FR:** Tout voir →
**NL:** Alles zien →

### Event card template

*Database-driven. Fields shown on each card:*

```
[Day abbrev] [DD Mon]         e.g. Sat 19 Apr
[HH:MM]                       e.g. 15:00
[Chapter name]                e.g. Evere–Haren
[Meeting point]               e.g. Place de la Mairie
```

*Card links to /events/[slug]. No additional copy on the card itself — the date, time, neighbourhood, and meeting point are the full message.*

*Date/month abbreviations must localise to the visitor's language (e.g. EN: "Sat 19 Apr", FR: "sam. 19 avr.", NL: "za 19 apr") — unless a locale-neutral numeric format like "19/04" is preferred; confirm the preferred format with Nico before implementing.*

### Off-season empty state

**EN:** No rides right now — the season runs from March to November.
**FR:** Pas de balades pour l'instant — la saison s'étend de mars à novembre.
**NL:** Momenteel geen fietstochten — het seizoen loopt van maart tot november.

*Single line. Replaces the 3-card strip entirely. No imagery, no "stay tuned" encouragement — just a honest, warm factual note.*

---

## Chapter map section

### Section heading

**EN:** Active across Belgium
**FR:** Actifs partout en Belgique
**NL:** Actief door heel België

### "See all chapters" link

**EN:** See all chapters →
**FR:** Voir tous les groupes →
**NL:** Alle groepen bekijken →

*Links to /chapters.*

### Map tooltip — generic chapter pin

*Shown on hover/tap of any standard pin.*

**EN:** [Chapter name] — tap to explore
**FR:** [Nom du groupe] — appuyez pour explorer
**NL:** [Naam groep] — tik om te ontdekken

### Map tooltip — Brussels cluster

*The Brussels cluster is a grouped pin representing multiple neighbourhood chapters (Schaerbeek, Forest/Vorst, Evere–Haren, Heembeek, etc.). On tap it expands.*

**EN:** Brussels — [N] neighbourhood chapters
**FR:** Bruxelles — [N] groupes de quartier
**NL:** Brussel — [N] buurtgroepen

### Liège pin label

*Liège links out to kidicalmassliege.org — external site. Pin is visually distinct (external link indicator).*

**EN:** Liège ↗
**FR:** Liège ↗
**NL:** Luik ↗

*No tooltip body copy needed — the pin label is sufficient. The map is impressionistic proof of scale, not a navigation directory.*

---

## Stats bar

### Format

Two stats separated by a centred dot (·). Dynamic — pulled from the database each season.

```
[N] active chapters  ·  [N] parades this season
```

**EN example:** [N] active chapters · [N] parades this season
**FR example:** [N] groupes actifs · [N] balades cette saison
**NL example:** [N] actieve groepen · [N] fietstochten dit seizoen

> ⚠️ These stats are database-driven and reflect the national movement. Confirm current national figures with Nico before launch. Do not use the "16 chapters / 60 parades" figures from the old website — those referred to Brussels only (2025).

### Separator

Mid-dot (·) — not a pipe (|), not a dash. Consistent with the visual register of the rest of the page.

### Implementation note

These stats are current-season only. They are deliberately different from the Mission page cumulative stats (150 parades, 5,500+ participants, 120 volunteers). Do not mix or combine them. Homepage stats = momentum signal. Mission stats = total impact.

---

## Volunteer CTA strip

*One line. A nudge, not a section. Appears between the stats bar and the news preview.*

**EN:** Want to help make rides happen? Help out →
**FR:** Envie d'aider à organiser des balades ? Donnez un coup de main →
**NL:** Wil je helpen om fietstochten te organiseren? Kom meehelpen →

*"Help out →" / "Donnez un coup de main →" / "Kom meehelpen →" links to /help-out.*

---

## News preview section

### Section heading

**EN:** News
**FR:** Actualités
**NL:** Nieuws

*No subheading. The cards carry the content.*

### Article card template

*Database-driven. Fields shown on each card:*

```
[Article title]
[DD Month YYYY] · [Excerpt — first 120 characters]
```

*Two cards side by side on desktop, stacked on mobile. Cards link to /about/news/[slug].*

### Empty-state rule

**The news section is hidden entirely when the feed is empty.** No "check back soon", no empty card shells, no placeholder text. If there are zero published articles, the section does not render. The page flows from the volunteer CTA strip directly to the partners bar.

*This is a hard rule — never show empty news cards.*

---

## Partners bar

### Heading

No heading. The logo strip stands alone. Logos are the content.

### The four institutional partners

In display order:
1. Bruxelles Mobilité
2. Clean Cities Campaign
3. Ville de Bruxelles
4. Commune de Schaerbeek

*All logos link to the respective partner's website (external, opens in new tab). The strip also carries a link to /about/partners.*

### "Our partners" link label

**EN:** Our partners →
**FR:** Nos partenaires →
**NL:** Onze partners →

*Appears as a text link at the end of the logo row (desktop) or below the logos (mobile). Links to /about/partners.*

### Implementation note

Operational and in-kind partners (Loopz, Kidical Mouse) do NOT appear in the homepage partners bar. They live on /about/partners and /getting-started only.

---

## Meta

### Page title tag

**EN:** Kidical Mass Belgium — Kids on bikes, together
**FR:** Kidical Mass Belgique — Des kets à vélo, ensemble
**NL:** Kidical Mass België — Kinderen op de fiets, samen

### Meta description

**EN:** Every month, hundreds of children ride through Belgian streets — safely, together, with music. Find a ride near you or start your own chapter.
**FR:** Chaque mois, des centaines d'enfants pédalent dans les rues belges — en sécurité, ensemble, avec de la musique. Trouvez une balade près de chez vous ou lancez votre propre groupe.
**NL:** Elke maand rijden honderden kinderen door de Belgische straten — veilig, samen, met muziek. Vind een fietstocht bij jou in de buurt of start je eigen groep.

---

## Tone of Voice check

**One-line test result:** Does this sound like someone who loves cycling with kids in their neighbourhood and wants you to come along? Yes — passed on all sections.

**Lines considered and reconsidered:**

- *"See all chapters →"* — first draft was *"Explore the map →"* but that felt like a tourism brochure. "See all chapters" is direct and honest about where you land.

- *"Want to help make rides happen?"* (volunteer strip) — this is flagged as a working example in home.md, and it survives the ToV test. It's direct, not preachy, makes the action feel tangible ("make rides happen" not "support our mission"). Kept.

- FR secondary CTA *"Première fois ? Commencez ici →"* — considered *"Nouveau ici ?"* but "Première fois" is warmer and more concrete — it names the experience of being new, not just the status.

- NL subheadline *"Gratis voor iedereen"* — considered *"Voor iedereen gratis"* for word order naturalness, but "Gratis voor iedereen" mirrors the FR and EN rhythm. Both are correct NL; chose the rhythm match.

- Off-season empty state: avoided "Stay tuned — the season runs from March to November" because "stay tuned" is corporate holding language. The bare factual version ("No rides right now — the season runs from March to November") is more honest and warmer for it.

- Stats bar: considered adding a third stat (e.g., participants) but that would duplicate the Mission page framing. Two stats = cleaner momentum signal. Held the line.

- Partners bar heading: considered "Supported by" but the logos speak for themselves and adding a heading risks making it feel like a thank-you note. No heading = cleaner, less institutional.

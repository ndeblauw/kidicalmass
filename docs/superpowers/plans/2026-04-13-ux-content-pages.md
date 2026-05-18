# UX Content Pages Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** For each of the 7 UX wireframe pages in `docs/wiki/ux/`, create a companion `-content.md` file with all actual copy written out, structured identically to the wireframe.

**Architecture:** Each content page mirrors the section structure of its wireframe counterpart — same headings, same section order — but with all placeholder labels, body text, CTAs, empty states, and microcopy filled in with real English copy. French and Dutch equivalents are noted inline where they differ meaningfully. Copy follows the Tone of Voice guide at `docs/wiki/tone-of-voice.md`. The one-line test: *does this sound like someone who loves cycling with kids in their neighbourhood and wants you to come along?*

**Tech Stack:** Markdown only. No code, no build steps.

---

## File Map

| Wireframe source | Content page to create |
|---|---|
| `docs/wiki/ux/home.md` | `docs/wiki/ux/home-content.md` |
| `docs/wiki/ux/events-overview.md` | `docs/wiki/ux/events-overview-content.md` |
| `docs/wiki/ux/getting-started.md` | `docs/wiki/ux/getting-started-content.md` |
| `docs/wiki/ux/help-out.md` | `docs/wiki/ux/help-out-content.md` |
| `docs/wiki/ux/chapters.md` | `docs/wiki/ux/chapters-content.md` |
| `docs/wiki/ux/about.md` | `docs/wiki/ux/about-content.md` |
| `docs/wiki/ux/activity-detail.md` | `docs/wiki/ux/activity-detail-content.md` |

After all content pages are written, update `docs/wiki/index.md` to register them.

---

## Reference material to read before starting each task

- **Wireframe page:** the corresponding `docs/wiki/ux/*.md` file
- **Tone of Voice:** `docs/wiki/tone-of-voice.md`
- **Raw site content:** `docs/raw/website/` — use as a source of confirmed facts, existing phrasing, and brand language
- **Key raw files per task:** listed in each task below

---

## Content file format

Every content file uses this frontmatter and structure:

```markdown
---
title: [Page Name] — Content
tags: [content]
sources: [ux/[page].md]
updated: 2026-04-13
---

*Content companion to [ux/page.md](page.md). All copy in English. FR/NL notes inline where relevant.*

---

## [Section heading from wireframe]

[Actual copy for this section]

---
```

---

## Task 1: home-content.md

**Files:**
- Read: `docs/wiki/ux/home.md`
- Read: `docs/wiki/tone-of-voice.md`
- Read: `docs/raw/website/index.md`
- Create: `docs/wiki/ux/home-content.md`

- [ ] **Step 1: Write the file**

Create `docs/wiki/ux/home-content.md` with the following content sections, all copy written out:

```markdown
---
title: Home — Content
tags: [content]
sources: [ux/home.md]
updated: 2026-04-13
---

*Content companion to [home.md](home.md). All copy in English. FR/NL notes inline.*

---

## Hero

**Headline:** Kids on bikes. Together.

**Subheadline:** Every month, hundreds of children ride through Belgian streets — safely, joyfully, with music. Free for everyone.

*FR: Chaque mois, des centaines d'enfants pédalent dans les rues belges — en sécurité, en fête, avec de la musique. Gratuit pour tout le monde.*
*NL: Elke maand rijden honderden kinderen door Belgische straten — veilig, feestelijk, met muziek. Gratis voor iedereen.*

**Primary CTA:** Find a ride →
*(FR: Trouver un ride → | NL: Vind een rit →)*

**Secondary CTA:** New here? Start here →
*(FR: Première fois ? Par ici → | NL: Eerste keer? Begin hier →)*

---

## Upcoming rides section

**Section heading:** Upcoming rides
**Section link:** See all →

**Event card example (database-driven — copy is template):**
- Date: [Day] [D] [Month]
- Time: [HH:MM]
- Name: Kidical Mass [Municipality]
- Meeting point: [Place name], [Municipality]

**Off-season empty state:**
No rides right now. The season runs from March to November — check back soon!
*(FR: Pas de ride en ce moment. La saison dure de mars à novembre — revenez bientôt !)*
*(NL: Geen ritten op dit moment. Het seizoen loopt van maart tot november — kom later terug!)*

---

## Chapter map section

**Section heading:** Active across Belgium
**Section link:** See all chapters →

**Map tooltip (per pin):** [Municipality name]

**Brussels cluster tooltip (on expand):** [Number] chapters in Brussels — tap to explore

**Liège pin:** Opens kidicalmassliege.org ↗

*(No body copy needed — the map is self-explanatory. Heading + link are the only text.)*

---

## Stats bar

**Stat 1:** [N] active chapters
**Stat 2:** [N] parades this season

*Both are database-driven. Separator: ·*

*Example: 16 active chapters · 60 parades this season*

*(FR: [N] groupes actifs · [N] parades cette saison)*
*(NL: [N] actieve groepen · [N] parades dit seizoen)*

---

## Volunteer CTA strip

**Copy:** Want to help make rides happen?
**Link:** Help out →

*(FR: Envie d'aider à organiser les rides ? | Participer →)*
*(NL: Wil je helpen om ritten te organiseren? | Meehelpen →)*

*One line. No more. This is a nudge, not a section.*

---

## News preview section

**Section heading:** News

**Article card (database-driven template):**
- Date: [D Month YYYY]
- Title: [Article title]
- Excerpt: [First 120 characters of body]

**Empty state:** Hidden entirely when no published articles exist. Never show an empty news section.

---

## Partners bar

**No heading.** Logo strip only.

**Partners shown:** Bruxelles Mobilité, Clean Cities Campaign, Ville de Bruxelles, Commune de Schaerbeek

**Link below strip:** → Our partners *(links to /about/partners)*
*(FR: → Nos partenaires | NL: → Onze partners)*

---

## Meta

**Page title tag:** Kidical Mass — Kids on bikes. Together.
**Meta description:** Monthly joyful bike parades for children and families across Belgium. Free, safe, open to all. Find a ride near you.
*(FR: Des parades cyclistes festives pour enfants et familles partout en Belgique. Gratuit, sécurisé, ouvert à tous.)*
*(NL: Maandelijkse feestelijke fietsparades voor kinderen en gezinnen in heel België. Gratis, veilig, voor iedereen.)*
```

- [ ] **Step 2: ToV check**

Read each piece of copy in the file. Apply the one-line test: *does this sound like someone who loves cycling with kids in their neighbourhood and wants you to come along?*

Confirm:
- "Kids on bikes. Together." — ✓ warm, direct, no jargon
- "Every month, hundreds of children ride through Belgian streets — safely, joyfully, with music." — ✓ sensory, specific, joyful
- "Want to help make rides happen?" — ✓ grounded, community language
- Volunteer strip: single line, nudge not pitch — ✓

If any line reads like a policy document or corporate newsletter: rewrite it.

- [ ] **Step 3: Commit**

```bash
git add docs/wiki/ux/home-content.md
git commit -m "docs: write copy for home page content"
```

---

## Task 2: events-overview-content.md

**Files:**
- Read: `docs/wiki/ux/events-overview.md`
- Read: `docs/wiki/tone-of-voice.md`
- Read: `docs/raw/website/agenda.md`
- Create: `docs/wiki/ux/events-overview-content.md`

- [ ] **Step 1: Write the file**

Create `docs/wiki/ux/events-overview-content.md`:

```markdown
---
title: Events Overview — Content
tags: [content]
sources: [ux/events-overview.md]
updated: 2026-04-13
---

*Content companion to [events-overview.md](events-overview.md). All copy in English. FR/NL notes inline.*

---

## Page header

**H1:** Events
**Subtitle:** Find a ride near you

*(FR: Événements | Trouvez un ride près de chez vous)*
*(NL: Evenementen | Vind een rit bij jou in de buurt)*

---

## Filter bar

**Toggle — option 1:** Upcoming  *(default selected)*
**Toggle — option 2:** Past

**Location dropdown label:** All locations
**Location dropdown placeholder:** Filter by municipality

*(FR: Toutes les communes | NL: Alle gemeenten)*

---

## Date group headers (upcoming mode)

**Standard format:** [Day of week] [D] [Month]
Examples: Saturday 19 April / Samedi 19 avril / Zaterdag 19 april

**Contextual labels (replace date when applicable):**
- Today → Today / Aujourd'hui / Vandaag
- Tomorrow → Tomorrow / Demain / Morgen

**Grande Kidical Mass date header badge:** ★ Featured / ★ À ne pas manquer / ★ Uitgelicht

---

## Event cards

**Standard card template:**
- Title: Kidical Mass [Municipality A] – [Municipality B]
  *(hyphenated bilingual names where applicable: "Forest – Vorst", "Ixelles – Elsene")*
- Time: [HH:MM]
- Municipality: [Municipality name]
- Meeting point: [Place name or street] *(truncated to one line if needed)*

**Grande Kidical Mass card:**
- Title prefix: ★ GRANDE KIDICAL MASS [YEAR]
- Same card structure, distinguished by the ★ prefix and the featured date header badge

**Past event cards:** Same structure, rendered at lower contrast to signal archive.

---

## Month group headers (past mode)

**Format:** [Month] [YYYY]
Example: March 2026 / Mars 2026 / Maart 2026

---

## Empty states

**No upcoming events (all locations):**
No upcoming rides right now. The season runs from March to November — check back soon!

*(FR: Pas de ride à venir pour le moment. La saison dure de mars à novembre — revenez bientôt !)*
*(NL: Geen aankomende ritten op dit moment. Het seizoen loopt van maart tot november — kom later terug!)*

**No upcoming events (filtered location):**
No upcoming rides in [Municipality]. Try "All locations" to see rides nearby.

*(FR: Pas de ride à venir à [Commune]. Essayez "Toutes les communes" pour voir les rides proches.)*
*(NL: Geen aankomende ritten in [Gemeente]. Probeer "Alle gemeenten" voor ritten in de buurt.)*

**No past events:**
*(Hidden — the past tab simply shows no content if no events exist.)*

---

## Meta

**Page title tag:** Events — Kidical Mass
**Meta description:** Upcoming and past Kidical Mass bike parades across Belgium. Filter by municipality and find your next ride.
*(FR: Rides à venir et passés en Belgique. Filtrez par commune et trouvez votre prochain ride.)*
*(NL: Komende en voorbije ritten in heel België. Filter op gemeente en vind je volgende rit.)*
```

- [ ] **Step 2: ToV check**

Check copy against the ToV table — Events pages register as "Warm, inviting, concrete — answer the practical questions joyfully."

- Empty state: "check back soon!" — ✓ warm but factual, not corporate
- Location filter copy: "Filter by municipality" — functional, fine
- "No upcoming rides in [Municipality]. Try 'All locations'..." — ✓ practical, not a dead-end

Confirm no urgency language, no insider jargon unexplained.

- [ ] **Step 3: Commit**

```bash
git add docs/wiki/ux/events-overview-content.md
git commit -m "docs: write copy for events overview content"
```

---

## Task 3: getting-started-content.md

**Files:**
- Read: `docs/wiki/ux/getting-started.md`
- Read: `docs/wiki/tone-of-voice.md`
- Read: `docs/raw/website/help-je-n-ai-pas-de-vélo.md` (if exists, else use info from wireframe)
- Read: `docs/raw/website/activités-vélo-fietsactiviteiten-kids.md` (if exists, else use info from wireframe)
- Create: `docs/wiki/ux/getting-started-content.md`

Note: use URL-encoded filenames. The actual filenames on disk are:
- `docs/raw/website/help-je-n-ai-pas-de-v-c3-a9lo.md`
- `docs/raw/website/activit-c3-a9s-v-c3-a9lo-fietsactiviteiten-kids.md`

- [ ] **Step 1: Read raw source files**

Read `docs/raw/website/help-je-n-ai-pas-de-v-c3-a9lo.md` and `docs/raw/website/activit-c3-a9s-v-c3-a9lo-fietsactiviteiten-kids.md` to extract confirmed facts about:
- Loopz: price, promo code, coverage
- Fietsbieb: price, deposit, commune list
- Kidical Mouse: availability
- My Kids Bikes: URL, bike brands
- ProVelo Families on Bike: location, cost
- Cyclo: what they offer
- Ride Your Future: location

- [ ] **Step 2: Write the file**

Create `docs/wiki/ux/getting-started-content.md`:

```markdown
---
title: Getting Started — Content
tags: [content]
sources: [ux/getting-started.md]
updated: 2026-04-13
---

*Content companion to [getting-started.md](getting-started.md). All copy in English. FR/NL notes inline.*

---

## Page header

**H1:** Getting Started
**Subtitle:** Come as you are. Here's what to expect.

*(FR: Premiers pas | Venez comme vous êtes. Voici ce qui vous attend.)*
*(NL: Aan de slag | Kom zoals je bent. Dit is wat je kan verwachten.)*

---

## What to expect at a ride

**Section heading:** What to expect at a ride
*(FR: Ce qui vous attend lors d'un ride | NL: Wat je kan verwachten bij een rit)*

**6 fact cards (icon · label · 1-line explanation):**

1. 🚲 · 5–7 km · At a slow pace, max 1 hour
2. 🎵 · Music all the way · There's always a sound system
3. 📍 · Fixed meeting point · Same spot every month per chapter
4. 🆓 · Free, no sign-up · Just show up at the listed time
5. 👶 · All ages welcome · From about 3 years old and up
6. 🦺 · Trained volunteers · Pink-vested guides keep the group together

*(FR versions of labels:)*
1. 5–7 km · À allure lente, max 1 heure
2. Musique tout du long · Il y a toujours un système de son
3. Point de départ fixe · Même endroit chaque mois par groupe
4. Gratuit, sans inscription · Venez simplement à l'heure indiquée
5. Tous âges bienvenus · Dès environ 3 ans
6. Bénévoles formés · Les gilets roses encadrent le groupe

*(NL versions:)*
1. 5–7 km · Aan een traag tempo, max 1 uur
2. Muziek onderweg · Er is altijd een geluidssysteem
3. Vaste afspraakplek · Elke maand dezelfde plek per groep
4. Gratis, geen inschrijving · Kom gewoon opdagen
5. Alle leeftijden welkom · Vanaf ongeveer 3 jaar
6. Opgeleide begeleiders · Roze hesjes houden de groep samen

---

## Common questions (FAQ)

**Section heading:** Common questions
*(FR: Questions fréquentes | NL: Veelgestelde vragen)*

**Q: Do I need to register?**
No. Just show up at the meeting point at the listed time. No ticket, no name on a list. The rides are open to everyone.

*(FR: Non. Rendez-vous simplement au point de départ à l'heure indiquée. Pas de billet, pas d'inscription.)*
*(NL: Nee. Kom gewoon opdagen op de afspraakplek op het aangegeven tijdstip. Geen ticket, geen inschrijving.)*

**Q: What age can children join?**
From about 3 years old and up. Children ride on their own bike (not balance bikes) or sit on a cargo bike or child seat. Adults are always responsible for their child's safety.

*(FR: Dès environ 3 ans. Les enfants roulent sur leur propre vélo (pas de draisiennes) ou voyagent sur un vélo cargo ou un siège enfant.)*
*(NL: Vanaf ongeveer 3 jaar. Kinderen rijden op hun eigen fiets (geen loopfiets) of zitten op een bakfiets of kinderzitje.)*

**Q: Do I need to be a confident cyclist?**
No. We ride at the pace of the youngest child. Many parents are cycling in traffic for the first time — you're not alone.

*(FR: Non. Nous roulons au rythme du plus jeune. Pour beaucoup de parents, c'est souvent la première fois qu'ils utilisent un vélo sur la route — vous n'êtes pas seul·e.)*
*(NL: Nee. We fietsen op het tempo van het jongste kind. Voor veel ouders is het vaak de eerste keer dat ze in het verkeer fietsen — je staat er niet alleen voor.)*

**Q: What if it rains?**
Rides happen in most weather. Check the Facebook event page or your local chapter page for cancellations — these are rare and only happen in extreme conditions.

*(FR: Les rides ont lieu par la plupart des temps. Consultez l'événement Facebook ou la page de votre groupe pour les annulations — rares et uniquement par conditions extrêmes.)*
*(NL: Ritten gaan door bij de meeste weersomstandigheden. Bekijk het Facebook-evenement of je lokale groepspagina voor annuleringen — zelden en alleen bij extreme omstandigheden.)*

**Q: What should we bring?**
Helmets are recommended but not mandatory. Water. That's it.

*(FR: Les casques sont recommandés mais pas obligatoires. De l'eau. C'est tout.)*
*(NL: Helmen zijn aanbevolen maar niet verplicht. Water. Dat is alles.)*

**Q: Is it really free?**
Yes. No registration, no entry fee, no cost — ever.

*(FR: Oui. Pas d'inscription, pas de frais d'entrée, jamais.)*
*(NL: Ja. Geen inschrijving, geen toegangsprijs, nooit.)*

---

## Don't have a bike?

**Section heading:** Don't have a bike?
**Section subheadline:** Not having a bike is not a reason to miss out.

*(FR: Pas de vélo ? | Pas de vélo, ce n'est pas une raison de rater l'événement.)*
*(NL: Geen fiets? | Geen fiets is geen reden om het te missen.)*

**4 resource cards:**

**Card 1: Loopz**
Bike subscription from €6/month via local partner shops.
Promo code **KIDICALMASS** = 2 months free.
Available nationally.
→ loopz.be

**Card 2: Fietsbieb / Vélothèque**
Borrow a child's bike (up to 12 years) for €20/year + €20 deposit.
Available in 10 Brussels communes: Anderlecht, Ixelles, Etterbeek, Jette, Laeken, Molenbeek, Neder-Over-Heembeek, Schaerbeek, Sint-Agatha-Berchem, Uccle.
→ fietsbieb.brussels

**Card 3: Kidical Mouse**
Cargo bike available at the ride start for families who need it.
Brussels rides.
→ Ask at your local chapter

**Card 4: My Kids Bikes**
Bike subscription service — Woom & BeMoov.
→ mykidsbikes.be

**Below cards (inline note):**
Also: Cyclo (Brussels) sells and repairs second-hand bikes. →
Local options may vary — check your chapter page for what's available near you. →

*(FR: Aussi : Cyclo (Bruxelles) vend et répare des vélos d'occasion. | Les options locales varient — consultez la page de votre groupe.)*
*(NL: Ook: Cyclo (Brussel) verkoopt en herstelt tweedehands fietsen. | Lokale opties variëren — bekijk de pagina van je groep.)*

---

## Other ways to cycle with your kids

**Section heading:** Other ways to cycle with your kids
**Section subheadline:** Kidical Mass isn't the only way to enjoy cycling with your kids in Belgium.

*(FR: D'autres façons de pédaler avec vos enfants | La Kidical Mass n'est pas la seule façon de profiter du vélo avec vos enfants en Belgique.)*
*(NL: Andere manieren om met je kinderen te fietsen | Kidical Mass is niet de enige manier om in België met je kinderen van fietsen te genieten.)*

**Card 1: Families on Bike — ProVelo**
Free coaching for Brussels families. Learn to ride in traffic, plan a route, test bikes. Sessions in Anderlecht and Saint-Gilles/Forest.
→ provelo.org

**Card 2: Duo mechanics — Cyclo**
Learn bike maintenance together with your child (age 8+). Practical, playful, about an hour.
→ cyclo.org

**Card 3: Pump Park — Ride Your Future**
110m pumptrack + kids track in Laeken (Brussels). Build balance and confidence. All levels welcome.
→ rideyourfuture.be

---

## Bottom CTA

**Heading:** Ready for your first ride?

**Primary button:** Find a ride near you →
*(FR: Trouver un ride près de chez moi → | NL: Vind een rit bij mij in de buurt →)*

**Secondary link:** Find your local chapter →
*(FR: Trouver mon groupe local → | NL: Vind mijn lokale groep →)*

---

## Meta

**Page title tag:** Getting Started — Kidical Mass
**Meta description:** First time at a Kidical Mass? Come as you are. Everything you need to know about attending a ride — and what to do if you don't have a bike.
*(FR: Première Kidical Mass ? Venez comme vous êtes. Tout ce qu'il faut savoir pour participer — et si vous n'avez pas de vélo.)*
*(NL: Eerste Kidical Mass? Kom zoals je bent. Alles wat je moet weten om mee te doen — ook als je geen fiets hebt.)*
```

- [ ] **Step 3: ToV check**

Apply one-line test to each section:
- "Come as you are. Here's what to expect." — ✓ concrete, warm, not a brochure
- FAQ answers — confirm they're factual and conversational, not policy-language
- "Not having a bike is not a reason to miss out." — ✓ addresses barrier directly
- "Kidical Mass isn't the only way..." — ✓ generous, not self-promotional
- "Ready for your first ride?" — ✓ warm but not urgent

Check that no answer sounds like "Experienced and novice cyclists are equally welcome" (ToV avoid list).

- [ ] **Step 4: Commit**

```bash
git add docs/wiki/ux/getting-started-content.md
git commit -m "docs: write copy for getting started content"
```

---

## Task 4: help-out-content.md

**Files:**
- Read: `docs/wiki/ux/help-out.md`
- Read: `docs/wiki/tone-of-voice.md`
- Read: `docs/raw/website/volunteer.md`
- Create: `docs/wiki/ux/help-out-content.md`

- [ ] **Step 1: Write the file**

Create `docs/wiki/ux/help-out-content.md`:

```markdown
---
title: Help Out — Content
tags: [content]
sources: [ux/help-out.md]
updated: 2026-04-13
---

*Content companion to [help-out.md](help-out.md). All copy in English. FR/NL notes inline.*

---

## Page header

**H1:** Help out
*(FR: S'engager | NL: Meehelpen)*

**Subtitle:** Join the people who make every ride happen.
*(FR: Rejoignez les personnes qui font vivre chaque ride. | NL: Sluit je aan bij de mensen die elke rit mogelijk maken.)*

---

## Pitch paragraph

Being a Kidical Mass volunteer means showing up for your neighbourhood. You'll be part of a team of parents, cyclists, and community builders who make every ride safe, joyful, and real. A few hours on a Saturday or Sunday — and much more back.

You don't need to be a cycling expert or an event professional. Just the desire to help, share, and keep the magic going.

→ Read the volunteer guidelines *(external Google Doc, opens in new tab)*

*(FR: Être bénévole Kidical Mass, c'est s'engager pour son quartier. Vous ferez partie d'une équipe de parents, cyclistes et bâtisseurs de communauté qui rendent chaque ride sûr, festif et réel. Quelques heures un samedi ou dimanche — et bien plus en retour. Pas besoin d'être un pro du vélo ou de l'événementiel. Juste l'envie d'aider, de partager et de faire vivre la magie Kidical.)*

*(NL: Vrijwilliger zijn bij Kidical Mass betekent er zijn voor je buurt. Je maakt deel uit van een team van ouders, fietsers en gemeenschapsmakers die elke rit veilig, feestelijk en echt maken. Een paar uur op zaterdag of zondag — en veel meer terug. Je hoeft geen fietsexpert of evenementprofessional te zijn. Gewoon de zin om te helpen, te delen en de Kidical-magie levend te houden.)*

---

## How you can help (role cards)

**Section heading:** How you can help
*(FR: Comment vous pouvez aider | NL: Hoe je kan helpen)*

**5 role cards:**

**🦺 Pink vest**
Ride alongside the group. You keep the children together and make sure everyone feels safe and seen. You're the friendly face on the road.

*(FR: Gilet rose | Roulez avec le groupe. Vous gardez les enfants ensemble et veillez à ce que tout le monde se sente en sécurité.)*
*(NL: Roze hesje | Rijd mee met de groep. Je houdt de kinderen samen en zorgt dat iedereen zich veilig voelt.)*

**🗓 Co-organiser**
Plan and prepare the ride — the route, the timing, the meeting point, the coordination with local partners. The backbone of every chapter.

*(FR: Co-organisateur·trice | Planifiez et préparez le ride — le parcours, le timing, le point de départ, la coordination.)*
*(NL: Co-organisator | Plan en bereid de rit voor — het parcours, de timing, de afspraakplek, de coördinatie.)*

**📢 Communicator**
Share the rides online and in your neighbourhood — social media, word of mouth, local networks. You bring new families in.

*(FR: Communicateur·trice | Partagez les rides en ligne et dans votre quartier.)*
*(NL: Communicator | Deel de ritten online en in je buurt.)*

**📸 Photographer**
Capture the best moments of every ride. Your photos and videos tell the story of the movement and bring new families along.

*(FR: Photographe | Capturez les plus beaux moments.)*
*(NL: Fotograaf | Leg de mooiste momenten vast.)*

**🎵 DJ**
Set the mood before, during, and after the ride. Music is half the vibe — and you're the one who brings it.

*(FR: DJ | Mettez l'ambiance avant, pendant et après le ride.)*
*(NL: DJ | Zorg voor de sfeer voor, tijdens en na de rit.)*

---

## What joining looks like

**Section heading:** What joining looks like
*(FR: Ce que ça implique | NL: Wat je kan verwachten)*

**You'll get:**
- Kidical Mass kit and support from day one
- Optional training (safety, route planning) — including a Safety First video guide
- 4 volunteer meetups a year — with good food included
- A community of parents, cyclists, and bike enthusiasts who actually become friends

**We ask:**
- Show up with enthusiasm and a positive attitude
- Follow our community guidelines *(link to Google Doc)*
- If you're part of a chapter team: send one representative to each annual meetup

*(FR — Vous bénéficiez :)*
- *Matériel Kidical Mass et soutien dès le premier jour*
- *Formation optionnelle (sécurité, planification de parcours)*
- *4 rencontres bénévoles par an — avec de la bonne nourriture*
- *Une communauté de parents et cyclistes qui deviennent de vrais amis*

*(FR — Nous attendons :)*
- *Enthousiasme et attitude positive*
- *Adhérer à notre règlement intérieur*
- *Un représentant par groupe aux rencontres annuelles*

*(NL — Je krijgt :)*
- *Kidical Mass materiaal en ondersteuning vanaf dag één*
- *Optionele opleiding (veiligheid, routeplanning)*
- *4 vrijwilligersmeetups per jaar — met lekker eten*
- *Een gemeenschap van ouders en fietsers die echte vrienden worden*

*(NL — We vragen :)*
- *Enthousiasme en een positieve houding*
- *Ons huishoudelijk reglement volgen*
- *Per groep een afgevaardigde naar de jaarlijkse meetups*

---

## Contact form

**Section heading:** I want to get involved
*(FR: Je veux m'engager | NL: Ik wil meedoen)*

**Section subheadline:** Fill in the form — your nearest chapter lead will be in touch.
*(FR: Remplissez le formulaire — le responsable du groupe le plus proche vous contactera.)*
*(NL: Vul het formulier in — de leider van je dichtstbijzijnde groep neemt contact op.)*

**Form fields:**
- Name *(required)*
- Email *(required)*
- Municipality *(required — dropdown, determines routing)*
- I'm interested in: *(checkboxes)* Pink vest · Co-organiser · Communicator · Photographer · DJ · Not sure yet
- Message *(optional — textarea)*

**Submit button:** I'm in →
*(FR: Je participe → | NL: Ik doe mee →)*

**Form confirmation message (shown inline after submit):**
Thanks! Your local Kidical Mass lead will be in touch soon. In the meantime, find your local chapter →

*(FR: Merci ! Le responsable local de Kidical Mass vous contactera bientôt. En attendant, trouvez votre groupe local →)*
*(NL: Bedankt! De lokale Kidical Mass-leider neemt binnenkort contact op. Vind intussen je lokale groep →)*

---

## Start a chapter section

**Section heading:** Don't see your city?
*(FR: Votre ville n'est pas encore sur la carte ? | NL: Jouw stad nog niet op de kaart?)*

**Body copy:**
New chapters keep joining. If your city isn't here yet, you could be the one to start it.

It takes a core team of 2–3 people, a meeting point, and a route idea. We handle the brand, the training, and the national visibility. You bring your neighbourhood.

*(FR: De nouveaux groupes rejoignent régulièrement. Si votre ville n'est pas encore là, vous pourriez être celui ou celle qui lance le mouvement. Il faut une équipe de 2-3 personnes, un point de départ et une idée de parcours. On s'occupe de la marque, de la formation et de la visibilité nationale. Vous apportez votre quartier.)*

*(NL: Er komen regelmatig nieuwe groepen bij. Als jouw stad er nog niet bij is, kun jij degene zijn die het opstart. Je hebt een team van 2-3 mensen nodig, een afspraakplek en een route-idee. Wij regelen het merk, de opleiding en de nationale zichtbaarheid. Jij brengt je buurt.)*

**Primary CTA:** Email the coordination team →
*(mailto:bike@kidicalmass.be)*

**Secondary link:** See which cities already have a chapter →
*(links to /chapters)*

---

## Meta

**Page title tag:** Help Out — Kidical Mass
**Meta description:** Volunteer with Kidical Mass. Choose your role — pink vest, co-organiser, communicator, photographer, or DJ. Your neighbourhood needs you.
*(FR: Bénévole avec Kidical Mass. Choisissez votre rôle et rejoignez l'aventure.)*
*(NL: Vrijwilliger bij Kidical Mass. Kies je rol en sluit je aan bij het avontuur.)*
```

- [ ] **Step 2: ToV check**

This page registers as "Energetic, honest about what's involved, make it feel doable" (ToV context table).

Check:
- Pitch paragraph: not a sales pitch — ✓ personal, honest
- "You don't need to be a cycling expert" — ✓ barrier removed directly
- Role cards: invitation language, not job descriptions — ✓ each starts with what you DO, not what you must be
- "I'm in →" — ✓ short, confident, personal
- "What we ask": honest, not heavy — ✓ three bullets, warmly worded

Red flags to fix: any role description that sounds intimidating ("ensure safety") → soften to "you make sure everyone feels safe and seen".

- [ ] **Step 3: Commit**

```bash
git add docs/wiki/ux/help-out-content.md
git commit -m "docs: write copy for help out content"
```

---

## Task 5: chapters-content.md

**Files:**
- Read: `docs/wiki/ux/chapters.md`
- Read: `docs/wiki/tone-of-voice.md`
- Read: `docs/raw/website/index.md` (for chapter list)
- Read: `docs/raw/website/organisation.md`
- Create: `docs/wiki/ux/chapters-content.md`

- [ ] **Step 1: Write the file**

Create `docs/wiki/ux/chapters-content.md`:

```markdown
---
title: Chapters — Content
tags: [content]
sources: [ux/chapters.md]
updated: 2026-04-13
---

*Content companion to [chapters.md](chapters.md). Covers both the Chapters Overview page and the Chapter Page Template. All copy in English. FR/NL notes inline.*

---

# PART 1: Chapters Overview

## Page header

**H1:** Chapters
*(FR: Groupes | NL: Groepen)*

**Subtitle:** [N] active groups across Belgium
*Database-driven. Example: 16 active groups across Belgium*

*(FR: [N] groupes actifs en Belgique | NL: [N] actieve groepen in België)*

---

## Map section

*(No body copy — the map is self-explanatory. Heading already in page header.)*

**Map tooltip (on hover/tap per pin):** [Municipality name]

**Brussels cluster tooltip:** [N] chapters — tap to explore
*(FR: [N] groupes — appuyez pour explorer | NL: [N] groepen — tik om te verkennen)*

**Liège pin tooltip:** Kidical Mass Liège ↗ *(external site)*

---

## Chapter list

**Region heading — Brussels:** Brussels
*(FR: Bruxelles | NL: Brussel)*

**Brussels chapters (confirmed from raw site, verify with Nico before build):**
- Anderlecht
- Berchem-Sainte-Agathe / Sint-Agatha-Berchem
- Bruxelles-Ville / Brussel-Stad
- Etterbeek
- Evere – Haren
- Forest – Vorst
- Ixelles – Elsene
- Jette
- Molenbeek
- Neder-Over-Heembeek
- Schaerbeek / Schaarbeek
- Watermael-Boitsfort – Watermaal-Bosvoorde & Auderghem – Oudergem
- Woluwe-Saint-Pierre & Woluwe-Saint-Lambert / Woluwe-Sint-Pieters & Woluwe-Sint-Lambrechts

**Region heading — Wallonia:** Wallonia
*(FR: Wallonie | NL: Wallonië)*

**Wallonia chapters:**
- Liège ↗ *(kidicalmassliege.org — external link with ↗ indicator)*
- Mons
- Namur

**Region heading — Flanders:** Flanders *(hidden until at least one active Flemish chapter)*
*(FR: Flandre | NL: Vlaanderen)*

---

## Start a chapter section

**Section heading:** Don't see your city?
*(FR: Votre ville n'est pas encore sur la carte ? | NL: Jouw stad nog niet op de kaart?)*

**Body copy:**
New chapters keep joining. If your city isn't on the map yet, you could be the one to start it. We'll support you every step of the way.

*(FR: De nouveaux groupes rejoignent régulièrement. Si votre ville n'est pas encore là, vous pourriez être celui ou celle qui lance le mouvement. Nous vous soutiendrons à chaque étape.)*

*(NL: Er komen regelmatig nieuwe groepen bij. Als jouw stad er nog niet op staat, kun jij degene zijn die het opstart. We ondersteunen je bij elke stap.)*

**Primary CTA:** Find out how →
*(links to /help-out#start-a-chapter)*
*(FR: Découvrez comment → | NL: Ontdek hoe →)*

**Secondary link:** Questions? Email the coordination team →
*(mailto:bike@kidicalmass.be)*
*(FR: Des questions ? Écrivez à l'équipe de coordination → | NL: Vragen? Schrijf de coördinatieteam →)*

---

## Meta (overview page)

**Page title tag:** Chapters — Kidical Mass
**Meta description:** Find your local Kidical Mass chapter across Belgium. [N] active groups in Brussels, Wallonia, and Flanders.
*(FR: Trouvez votre groupe Kidical Mass local en Belgique.)*
*(NL: Vind je lokale Kidical Mass-groep in heel België.)*

---

---

# PART 2: Chapter Page Template

*All copy below is the template for any chapter's individual page. [Municipality] = the chapter's name. [Postal code] = their code.*

---

## Chapter header

**H1:** [Municipality]
*(e.g. "Schaerbeek", "Forest – Vorst", "Woluwe-Saint-Pierre & Saint-Lambert")*

**Postal code (below H1):** [Postal code]
*(e.g. 1030, 1190, 1150–1200)*

**Brussels-only toggle:** NL | FR
*(visible only on Brussels chapter pages — language determines which language is primary on the page)*

**Breadcrumb:** ← All chapters
*(links to /chapters)*
*(FR: ← Tous les groupes | NL: ← Alle groepen)*

---

## Upcoming events section

**Section heading:** Upcoming rides in [Municipality]
*(FR: Prochains rides à [Commune] | NL: Aankomende ritten in [Gemeente])*

**Event card template:**
- Title: Kidical Mass [Municipality]
- Date: [D Month] · [HH:MM]
- Meeting point: [Place name]

**"Past rides" link below the list:** Past rides →
*(links to /events filtered to this chapter)*
*(FR: Rides passés → | NL: Vorige ritten →)*

**Empty state (no upcoming events):**
No upcoming rides in [Municipality] right now. Check /events for rides across Belgium.

*(FR: Pas de ride à venir à [Commune] pour le moment. Voir tous les rides →)*
*(NL: Geen aankomende ritten in [Gemeente] op dit moment. Bekijk alle ritten →)*

---

## Organised by section

**Section heading:** Organised by
*(FR: Organisé par | NL: Georganiseerd door)*

**Team member entry:** [Name] · [Role label]
*(Role labels match the 5 role card names from help-out.md)*

*(Section hidden entirely if no team members added in admin)*

**Volunteer pitch (immediately below team names):**
Want to help in [Municipality]?
We're always glad to have new people join the team. Get in touch — no experience needed.

*(FR: Envie d'aider à [Commune] ? Nous sommes toujours heureux d'accueillir de nouvelles personnes. Prenez contact — aucune expérience requise.)*
*(NL: Wil je helpen in [Gemeente]? We verwelkomen altijd nieuwe mensen. Neem contact op — geen ervaring nodig.)*

**Form fields:**
- Name *(required)*
- Email *(required)*
- Message *(optional)*

**Submit button:** Send →
*(FR: Envoyer → | NL: Verstuur →)*

**Form confirmation (inline after submit):**
Thanks! We'll be in touch soon.
*(FR: Merci ! Nous vous contacterons bientôt.)*
*(NL: Bedankt! We nemen binnenkort contact op.)*

**"More about volunteering" link:** More about volunteering →
*(links to /help-out)*
*(FR: En savoir plus sur le bénévolat → | NL: Meer over vrijwilligerswerk →)*

---

## Local partners section

**Section heading:** Local partners
*(FR: Partenaires locaux | NL: Lokale partners)*

*(Section hidden when empty. Each entry: logo + name + optional external link.)*

---

## Press coverage section

**Section heading:** Press coverage
*(FR: Couverture presse | NL: Persaandacht)*

*(Section hidden when empty. Each entry: Outlet · Headline · Date · ↗)*

---

## Downloads section

**Section heading:** Downloads
*(FR: Téléchargements | NL: Downloads)*

*(Section hidden when empty. Each entry: filename + format + download button.)*

**Download button label:** Download ↓
*(FR: Télécharger ↓ | NL: Downloaden ↓)*

---

## Meta (chapter pages)

**Page title tag:** Kidical Mass [Municipality] — Rides & info
*(FR: Kidical Mass [Commune] — Rides et infos)*
*(NL: Kidical Mass [Gemeente] — Ritten & info)*

**Meta description:** Find upcoming Kidical Mass rides in [Municipality]. Free, safe bike parades for children and families.
*(FR: Prochains rides Kidical Mass à [Commune]. Parades cyclistes festives pour enfants et familles.)*
*(NL: Aankomende Kidical Mass-ritten in [Gemeente]. Feestelijke, veilige fietsparades voor kinderen en gezinnen.)*
```

- [ ] **Step 2: ToV check**

Chapter pages register as "Local, personal, specific — this is your neighbourhood" in the ToV context table.

Check:
- Volunteer pitch on chapter page: "We're always glad to have new people join the team" — ✓ warm, personal
- "No experience needed" — ✓ dissolves hesitation barrier
- Form confirmation: "Thanks! We'll be in touch soon." — ✓ short, human, not automated-feeling

- [ ] **Step 3: Commit**

```bash
git add docs/wiki/ux/chapters-content.md
git commit -m "docs: write copy for chapters overview and chapter template content"
```

---

## Task 6: about-content.md

**Files:**
- Read: `docs/wiki/ux/about.md` *(read in two chunks: offset 0 limit 150, then offset 150 limit 150, to handle the large file)*
- Read: `docs/wiki/tone-of-voice.md`
- Read: `docs/raw/website/le-projet-het-project.md`
- Read: `docs/raw/website/organisation.md`
- Read: `docs/raw/website/what-we-want.md`
- Read: `docs/raw/website/press.md`
- Create: `docs/wiki/ux/about-content.md`

- [ ] **Step 1: Read about.md in chunks**

The file is large (>10k tokens). Read it in two passes:
- First: `offset: 0, limit: 150`
- Then: `offset: 150, limit: 150`

Extract the section structure for each About sub-page: Mission, Vision, Organisation, News, Press, Partners.

- [ ] **Step 2: Write the file**

Create `docs/wiki/ux/about-content.md` with all 6 About sub-pages' copy:

```markdown
---
title: About Section — Content
tags: [content]
sources: [ux/about.md]
updated: 2026-04-13
---

*Content companion to [about.md](about.md). Covers all About sub-pages: Mission, Vision, Organisation, News, Press, Partners. All copy in English. FR/NL notes inline.*

---

# ABOUT / MISSION

## Page header

**H1:** Mission
*(FR: Mission | NL: Missie)*

**Subtitle:** We organise joyful, safe bike parades for children and families across Belgium.
*(FR: Nous organisons des parades cyclistes festives et sécurisées pour les enfants et les familles partout en Belgique.)*
*(NL: We organiseren feestelijke, veilige fietsparades voor kinderen en gezinnen in heel België.)*

---

## Movement description

Kidical Mass Belgium is a national network of local groups that organise festive, safe bike parades at a child's pace — across Brussels, Wallonia, and Flanders.

Every month, volunteers from these communities plan a route through their neighbourhood, put on their pink vests, and ride alongside hundreds of children and families. It's a celebration first, and a demonstration second: children have the right to safe, welcoming streets.

The movement started in 2020 with a single group in Brussels. Today, there are [N] active groups across Belgium, having co-organised over 150 parades and welcomed more than 5,500 participants.

*(FR: Kidical Mass Belgium est un réseau national de groupes locaux qui organisent des parades cyclistes festives et sécurisées au rythme des enfants — à Bruxelles, en Wallonie et en Flandre.)*

*(NL: Kidical Mass Belgium is een nationaal netwerk van lokale groepen die feestelijke, veilige fietsparades op kindermaat organiseren — in Brussel, Wallonië en Vlaanderen.)*

---

## Three mission axes

**Section heading:** What we do
*(FR: Ce que nous faisons | NL: Wat we doen)*

**Axis 1: Start**
We help new communities start a local Kidical Mass. Every new chapter means more children on bikes in more streets, in more cities.

*(FR: Nous aidons les nouvelles communautés à lancer une Kidical Mass locale.)*
*(NL: We helpen nieuwe gemeenschappen een lokale Kidical Mass op te starten.)*

**Axis 2: Support**
We support existing chapters with training, materials, coordination tools, and national visibility. Local volunteers do the work — we make sure they don't do it alone.

*(FR: Nous soutenons les groupes existants avec formation, matériel et outils de coordination.)*
*(NL: We ondersteunen bestaande groepen met opleiding, materiaal en coördinatiemiddelen.)*

**Axis 3: Spread**
We advocate for child-friendly streets at city and regional level. We show what's possible — with every ride, in every neighbourhood.

*(FR: Nous plaidons pour des rues adaptées aux enfants au niveau local et régional.)*
*(NL: We pleiten voor kindvriendelijke straten op lokaal en regionaal niveau.)*

---

## Impact stats bar

**4 stats (manually maintained — not database-driven):**

- 150+ parades *(since 2020)*
- 5,500+ participants
- 120 active volunteers
- 16+ communities

*(FR: 150+ parades | 5 500+ participant·es | 120 bénévoles actif·ves | 16+ communautés)*
*(NL: 150+ parades | 5.500+ deelnemers | 120 actieve vrijwilligers | 16+ gemeenschappen)*

*Note: these are cumulative impact stats (since 2020), deliberately different from the homepage stats (current-season only).*

---

## Inclusivity section

**Section heading:** Open to everyone
*(FR: Ouvert à tout le monde | NL: Open voor iedereen)*

**Body copy:**
No bike? No problem. Never cycled in traffic? That's fine. Kidical Mass is for every family — regardless of cycling experience, fitness level, or equipment.

Our rides are slow by design: 5–7 km at the pace of the youngest child. And our volunteers — pink-vested people who ride alongside the group — are there to make sure everyone feels safe and welcome, from the very first metre.

*(FR: Pas de vélo ? Pas de problème. Jamais pédalé en circulation ? Aucun souci. La Kidical Mass est pour toutes les familles — quelle que soit l'expérience, la condition physique ou l'équipement.)*

*(NL: Geen fiets? Geen probleem. Nog nooit in het verkeer gefietst? Dat is prima. Kidical Mass is voor elk gezin — ongeacht fietsertaring, conditie of materiaal.)*

---

## Parent quote

*(Optional pull-quote — confirmed consented sources from what-we-want.md)*

**Quote 1:**
"What she loves about cycling, I think, is that freedom of being outside, having fresh air, going off on her own. She always wants to go far, explore something new."
— Julienne, mum of two (2 and 5 years old)

**Quote 2:**
"I have three children, including one still in a pushchair, but I only have two hands. I'm constantly afraid of cars. By the time we get home from school, I'm exhausted."
— Fatima, mum of three, Jette

*(These quotes are used to illustrate WHY the movement exists — they come from a 2021 RIEPP study cited in the manifesto. Use with citation.)*

---

## CTA block

**Section heading:** Ready to join?
*(FR: Prêt·e à rejoindre ? | NL: Klaar om mee te doen?)*

**Primary CTA:** Find a ride near you →
*(FR: Trouver un ride → | NL: Vind een rit →)*

**Secondary CTA:** Help make rides happen →
*(links to /help-out)*
*(FR: Aider à organiser les rides → | NL: Helpen ritten te organiseren →)*

---

## Meta (Mission)

**Page title tag:** Mission — Kidical Mass
**Meta description:** Kidical Mass Belgium organises joyful, safe bike parades for children and families. 150+ parades, 16+ communities, 5,500+ participants across Belgium.

---

---

# ABOUT / VISION

## Page header

**H1:** Vision
*(FR: Vision | NL: Visie)*

**Subtitle:** Streets that are safe, welcoming, and made for children too.
*(FR: Des rues sûres, accueillantes et faites aussi pour les enfants.)*
*(NL: Straten die veilig, uitnodigend en ook gemaakt zijn voor kinderen.)*

---

## Vision body

*This page presents Kidical Mass's political position — child-friendly streets as a rights issue, not just a lifestyle preference. Tone: confident and grounded, not preachy. State the position clearly; trust the reader.*

**Body copy:**
Children have the right to move freely and safely in their city. Today, that right is not guaranteed — traffic, pollution, and poorly designed public space push children indoors and off the streets.

Kidical Mass is part of a broader movement that says: this doesn't have to be this way. Streets can be designed differently. Cities can choose children.

We advocate for:
- Safe cycling infrastructure accessible to children and families
- Reduced traffic speed and volume in residential streets
- Public spaces that genuinely welcome children and families
- Children's participation in decisions about their own environment

We make this case not with anger, but with joy. Every ride through a city neighbourhood is a demonstration of what's possible: children cycling freely, parents relaxed, streets alive.

*(FR body equivalent — same content, translated not adapted)*

*(NL body equivalent — same content, translated not adapted)*

---

## Manifesto link

**CTA:** Read our full manifesto *(PDF download, opens in new tab)*
*(FR: Lire notre manifeste complet | NL: Lees ons volledige manifest)*

---

---

# ABOUT / ORGANISATION

## Page header

**H1:** Organisation
*(FR: Organisation | NL: Organisatie)*

**Subtitle:** How Kidical Mass Belgium works.
*(FR: Comment fonctionne Kidical Mass Belgium.)*
*(NL: Hoe Kidical Mass Belgium werkt.)*

---

## Org description

*This page explains the organisational structure: national coordination + local chapter autonomy. Tone: transparent, human, not bureaucratic.*

**Body copy:**
Kidical Mass Belgium is a citizens' movement, not an NGO. There's no headquarters, no paid staff, no membership fees.

What there is: a small national coordination team that supports local chapters across the country. Each chapter is organised by a team of local volunteers — parents, neighbours, cycling enthusiasts — who plan and run rides in their own municipality.

**National coordination:** Handles the brand, training, communications, partnerships, and grant administration. Keeps the network connected and visible.

**Local chapters:** Completely autonomous in their operational decisions — which routes, which meeting points, which local partners. They know their neighbourhood best.

This structure means Kidical Mass is genuinely local everywhere it exists — not a national campaign with local franchises.

*(FR and NL equivalents — same content)*

---

## Team section

*(If team member names and roles are available from Leticia/Nico, list them here. Otherwise use this placeholder template:)*

**Heading:** The coordination team

**Team member entry format:** [Name] · [Role / title]

*(Section hidden if no team data entered in admin)*

---

---

# ABOUT / NEWS

## Page header

**H1:** News
*(FR: Actualités | NL: Nieuws)*

**Subtitle:** Updates from the movement.
*(FR: Actualités du mouvement. | NL: Updates vanuit de beweging.)*

---

## News list

*Database-driven. No static copy needed. Template per article card:*

- Date: [D Month YYYY]
- Title: [Article title]
- Category tag: [optional]
- Excerpt: [First 150 characters]

**Empty state:**
Nothing here yet. Check back soon.
*(FR: Pas encore d'actualités. Revenez bientôt.)*
*(NL: Nog geen nieuws. Kom later terug.)*

---

---

# ABOUT / PRESS

## Page header

**H1:** Press
*(FR: Presse | NL: Pers)*

**Subtitle:** Kidical Mass in the media.
*(FR: Kidical Mass dans les médias. | NL: Kidical Mass in de media.)*

---

## Press intro

**Body copy (short):**
Want to cover Kidical Mass? We'd love to hear from you.
Contact: bike@kidicalmass.be

*(FR: Vous souhaitez couvrir Kidical Mass ? N'hésitez pas à nous contacter.)*
*(NL: Wil je Kidical Mass belichten in de media? Neem contact op.)*

---

## Press list

*Database-driven. Items auto-aggregated from chapter pages. Template per entry:*

- Outlet name · Headline · Date · ↗ link

**Empty state:**
*(Hidden — page only shown when press items exist)*

---

---

# ABOUT / PARTNERS

## Page header

**H1:** Partners
*(FR: Partenaires | NL: Partners)*

**Subtitle:** The organisations who make Kidical Mass possible.
*(FR: Les organisations qui rendent Kidical Mass possible.)*
*(NL: De organisaties die Kidical Mass mogelijk maken.)*

---

## Partners intro

**Body copy:**
Kidical Mass works with institutional partners, movement allies, and local supporters to make rides happen across Belgium.

*(FR: Kidical Mass travaille avec des partenaires institutionnels, des alliés du mouvement et des soutiens locaux pour organiser des rides partout en Belgique.)*
*(NL: Kidical Mass werkt samen met institutionele partners, bondgenoten van de beweging en lokale ondersteuners om ritten te organiseren in heel België.)*

---

## Partner categories

**Category 1: Institutional & movement partners**
*(These also appear on the homepage partners bar)*
- Bruxelles Mobilité / Brussel Mobiliteit
- Clean Cities Campaign
- Ville de Bruxelles / Stad Brussel
- Commune de Schaerbeek / Gemeente Schaarbeek

**Category 2: Operational & in-kind partners**
*(These do NOT appear on the homepage — only here and on /getting-started)*
- Loopz
- Kidical Mouse
- My Kids Bikes

**Partner entry template:**
[Logo] · [Name] · [1-line description] · [optional external link ↗]

---

## Become a partner

**Heading:** Want to support Kidical Mass?
*(FR: Vous souhaitez soutenir Kidical Mass ? | NL: Wil je Kidical Mass steunen?)*

**Body copy:**
We work with local and regional partners who share our belief in child-friendly streets. If you'd like to get involved, reach out.

*(FR equivalent | NL equivalent)*

**CTA:** Get in touch →
*(mailto:bike@kidicalmass.be)*
```

- [ ] **Step 3: ToV check**

About pages register as "Confident, grounded, community-first — not institutional" in the ToV context table. Partners register as "A notch more serious, still human — not corporate."

Check:
- Mission body: not a grant application, not an NGO pitch — ✓ starts with people and places
- Vision: position stated clearly without moralising — ✓
- Organisation: "no headquarters, no paid staff" — ✓ honest and warm about being a movement
- Partners intro: functional but human — ✓

- [ ] **Step 4: Commit**

```bash
git add docs/wiki/ux/about-content.md
git commit -m "docs: write copy for about section content (all 6 sub-pages)"
```

---

## Task 7: activity-detail-content.md

**Files:**
- Read: `docs/wiki/ux/activity-detail.md`
- Read: `docs/wiki/tone-of-voice.md`
- Read: `docs/raw/website/agenda.md`
- Create: `docs/wiki/ux/activity-detail-content.md`

- [ ] **Step 1: Write the file**

Create `docs/wiki/ux/activity-detail-content.md`:

```markdown
---
title: Activity Detail — Content
tags: [content]
sources: [ux/activity-detail.md]
updated: 2026-04-13
---

*Content companion to [activity-detail.md](activity-detail.md). Template copy for the /events/[slug] page. All copy in English with FR/NL notes. Where a field is database-driven, the template shows the format; where it's static copy, the exact text is given.*

---

## Hero — left panel

**Title format:** Kidical Mass [Postal code] — [Date]
*Example: Kidical Mass 1030 — 31 May*

**Chapter name (below title):** [Chapter full name]
*Example: Schaerbeek / Schaarbeek*

**Date line:** [Day of week] [D] [Month] · [HH:MM]
*Example: Saturday 31 May · 15:00*

**Meeting point:** [Named place], [Street or neighbourhood]
*Example: Place Colignon, Schaerbeek*

**Action buttons (in hero):**
- Add to calendar *(iCal export)*
  *(FR: Ajouter à l'agenda | NL: Toevoegen aan agenda)*
- Share *(WhatsApp + general link)*
  *(FR: Partager | NL: Delen)*

---

## Hero — right panel

**Map:** Komoot embed showing full route with start marker.
*(No static copy — embed only. Alt text for accessibility: "Route map for Kidical Mass [Chapter] on [Date]")*

---

## Practical strip

**Format:** [Distance] · [Duration] · [Admission] · [Age range] · [Music note] · [Guardian note]

**Static copy for each element:**
- Distance: 5–7 km
- Duration: max 1 hour / max 1 heure / max 1 uur
- Admission: Free / Gratuit / Gratis
- Age range: All ages / Tous âges / Alle leeftijden
- Music note: 🎵 Music along the way / Musique en cours de route / Muziek onderweg
- Guardian note: Children accompanied by adult / Enfants accompagnés d'un adulte / Kinderen begeleid door een volwassene

*Full strip example (EN): 5–7 km · max 1 hour · Free · All ages · 🎵 Music along the way · Children accompanied by adult*

---

## What to expect section

**Section heading:** What to expect
*(FR: Ce qui vous attend | NL: Wat je kan verwachten)*

**Default body copy (used when no event-specific notes):**
We ride at the pace of the youngest child — slow, joyful, together. Music plays the whole way. You'll discover your neighbourhood from a different angle, and your kids will make friends along the route.

*(FR: Nous roulons au rythme du plus jeune enfant — lentement, joyeusement, ensemble. La musique joue tout le long. Vous découvrirez votre quartier sous un autre angle, et vos enfants se feront des amis en route.)*

*(NL: We rijden op het tempo van het jongste kind — traag, feestelijk, samen. Er is muziek de hele weg. Je ontdekt je buurt vanuit een ander perspectief, en je kinderen maken vrienden onderweg.)*

**Optional: Theme / campaign note field**
*(If the event has a theme or campaign context, this block appears below the default copy:)*

*Example for a Safety First campaign event:*
This edition is part of our Safety First campaign — reminding our cities that children deserve safe streets, every day. Come ride with us.

*(FR: Cette édition fait partie de notre campagne Safety First.)*
*(NL: Deze editie maakt deel uit van onze Safety First-campagne.)*

---

## Chapter context section

**Static copy template:**
This ride is part of [Chapter name]'s monthly series →
Every month, a new ride through the neighbourhood. Come back for more.

*(FR: Ce ride fait partie de la série mensuelle de [Nom du groupe] →. Chaque mois, un nouveau parcours dans le quartier.)*

*(NL: Deze rit maakt deel uit van de maandelijkse reeks van [Naam groep] →. Elke maand een nieuwe route door de buurt.)*

*Link to chapter page: /chapters/[postal-code]*

---

## Organising team + volunteer ask section

**Heading:** Organised by
*(FR: Organisé par | NL: Georganiseerd door)*

**Team display:** [Name] [Name] [Name] *(local volunteers — first names only)*

**Volunteer ask (immediately below team names):**
Want to ride alongside them as a pink vest? →
*(links to /help-out)*

*(FR: Envie de les accompagner comme gilet rose ? →)*
*(NL: Wil je meefietsen als roze hesje? →)*

---

## Local partners section

**Heading:** Partners
*(FR: Partenaires | NL: Partners)*

*(Database-driven. Logo strip or name list. Section hidden if no local partners added for this event.)*

---

## Photo permission line

**Static copy:**
Photos may be taken during the ride and shared on our channels. By participating, you consent to publication.

*(FR: Des photos peuvent être prises pendant le ride et partagées sur nos canaux. En participant, vous consentez à leur publication.)*

*(NL: Er worden foto's gemaakt tijdens de rit en gedeeld op onze kanalen. Door deel te nemen, ga je akkoord met publicatie.)*

*Visual treatment: small text, low visual prominence. Legally present, not a design feature.*

---

## Meta

**Page title tag format:** Kidical Mass [Municipality] — [D Month YYYY]
*Example: Kidical Mass Schaerbeek — 31 May 2026*

**Meta description template:** Free bike parade in [Municipality] on [Date]. Children and families welcome. Meeting point: [Place name]. No registration needed.

*(FR: Parade cycliste gratuite à [Commune] le [Date]. Enfants et familles bienvenus. Point de départ : [Lieu]. Pas d'inscription requise.)*

*(NL: Gratis fietsparade in [Gemeente] op [Datum]. Kinderen en gezinnen welkom. Afspraakplek: [Plek]. Geen inschrijving nodig.)*
```

- [ ] **Step 2: ToV check**

Activity detail pages register as "Warm, inviting, concrete — answer the practical questions joyfully."

Check:
- Title format: "Kidical Mass 1030 — 31 May" — ✓ local, follows established convention
- "We ride at the pace of the youngest child — slow, joyful, together." — ✓ sensory, warm, passes one-line test
- "Want to ride alongside them as a pink vest?" — ✓ personal, invitation language
- Photo permission: legally present, not a scary legal disclaimer — ✓

Confirm no generic event listing language ("The event will take place at the designated location at the specified time").

- [ ] **Step 3: Commit**

```bash
git add docs/wiki/ux/activity-detail-content.md
git commit -m "docs: write copy for activity detail content"
```

---

## Task 8: Update wiki index

**Files:**
- Read: `docs/wiki/index.md`
- Edit: `docs/wiki/index.md`

- [ ] **Step 1: Add content pages to the index**

Read `docs/wiki/index.md`. Add a row for each new content page in the UX category. Use this format:

| [Page Name] — Content | Companion copy for [page] | UX |

Content pages to register:
1. Home — Content | `ux/home-content.md`
2. Events Overview — Content | `ux/events-overview-content.md`
3. Getting Started — Content | `ux/getting-started-content.md`
4. Help Out — Content | `ux/help-out-content.md`
5. Chapters — Content | `ux/chapters-content.md`
6. About Section — Content | `ux/about-content.md`
7. Activity Detail — Content | `ux/activity-detail-content.md`

- [ ] **Step 2: Append to log**

Append to `docs/wiki/log.md`:
```
## [2026-04-13] write | UX content pages (all 7 sections)
```

- [ ] **Step 3: Commit**

```bash
git add docs/wiki/index.md docs/wiki/log.md
git commit -m "docs: register UX content pages in wiki index"
```

---

## Self-Review

**Spec coverage check:**
- ✅ home-content.md — all sections from wireframe: hero, events strip, map, stats, volunteer strip, news, partners
- ✅ events-overview-content.md — page header, filter bar, date headers, event cards, empty states
- ✅ getting-started-content.md — page header, 6 fact cards, 6 FAQ items, 4 bike resources, 3 other activities, CTA
- ✅ help-out-content.md — page header, pitch, 5 role cards, what joining looks like, form, start-a-chapter
- ✅ chapters-content.md — overview page (map, list, start CTA) + chapter template (all sections)
- ✅ about-content.md — all 6 sub-pages: Mission, Vision, Organisation, News, Press, Partners
- ✅ activity-detail-content.md — all sections: hero, practical strip, what to expect, chapter context, team + volunteer, partners, photo permission
- ✅ wiki index updated

**Placeholder scan:** No "TBD", "TODO", or "similar to above" in any task. Each content block contains actual copy or an explicit template format with an example.

**Language consistency:** EN copy in all files. FR/NL notes inline as italicised blocks. Bilingual chapter names use the established `Municipality A – Municipality B` hyphenated format throughout.

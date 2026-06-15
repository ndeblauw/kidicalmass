---
title: Een lokale groep starten
tags: []
sources: [notion, raw/website/organisation.md, raw/website/volunteer-roi-charter.md, raw/website/volunteer.md, raw/website/index.md]
phase: design
updated: 2026-06-15
---

Status: 🟠 First version built (2026-06-15). Page URL: `/chapters/start-een-groep` (route `groups.start`, internal `groups.*` convention). Out of nav, reached contextually. NL only (FR/EN deferred — D6).

**Summary:** The canonical **"start a local group"** page. Until now starting a group was only a `mailto:bike@kidicalmass.be` coda on [Help out](help-out.md) + a CTA on [Chapters](chapters.md) — the "email black hole" of **[01-concerns.md](../01-concerns.md) D-12**. This page replaces both exits and owns the contact step. It is a deliberate upgrade of the logged MVP stance (*"start-a-chapter is a static page, recruiting demoted to secondary"*, [20-personas.md](../../strategy/20-personas.md) S-8/D4) — the page stays out of nav (still secondary) but is no longer a dead `mailto`.

## Strategy

**One primary user:** someone in a town/neighbourhood with *no* group yet, drawn to the idea but quietly daunted. *Not* the identity-anxious "I'm not a leader" type — so this is not cheerleading "anyone can do it!", it's *"here's the real deal, and here's how you're carried."*

**Three barriers, each mapped to a half of the page:**
- *"Te groot een klus voor mij"* → reframe the deal (jij brengt / wij dragen).
- *"Wie steunt mij eigenlijk?"* → make the backing concrete + visible, and kill the black box (warm form, "een echt mens antwoordt").
- *"Is er hier wel animo?"* → proof (60+ parades in 2024, het groeiende netwerk, "jouw stad kan de volgende zijn").

**Productive tension:** the coordination team wants **fewer, higher-intent leads**, so the page is *honest about the commitment* (which filters) while *warm about the support* (which reassures). The candor is the filter.

**Routing = both, as a comfort ladder** (← needs Leticia, see open questions): a low-stakes *"praat eerst met iemand die het al deed"* path **and** a *"ik ben er klaar voor"* path. Both run through one intake, so the team gets the intent signal Leticia wants no matter which the person picks — her worry is designed in, not worked around.

## Scope

- **One light intent form** (`StartGroupEnquiry` Livewire component): naam, e-mail, gemeente/postcode, *waarom/wat trekt je aan*, optional *heb je al mensen die mee willen?* (the high-intent signal), and a comfort-path radio. Honeypot + success state. Mirrors `PartnerEnquiry` / `ChapterVolunteerSignup`; persists to `ContactForm`, mails the central comms inbox tagged "Aanvraag nieuwe lokale groep".
- **Brokered peer path:** no trekker contact is ever exposed publicly. The "praat met iemand" choice tells the team to connect the person to a nearby trekker (matching can be manual at first, via the postcode).
- **Content** (most reused verbatim from the raw scrape): hero invitation · de deal (jij brengt / wij dragen) · wat het écht vraagt (the honest filter) · er is animo (proof) · je staat er niet alleen voor · the intent form.

**Out of scope (MVP):** a public trekker directory; automated trekker-matching (manual brokering is fine); FR/EN; a full onboarding dashboard (that's the logged-in roze-hesje / material-library territory — [chapters-roze-hesjes.md](chapters-roze-hesjes.md)).

## Structure

The page is the single canonical "start" destination. Three entry points funnel in:
- [Help out](help-out.md) coda "Nog geen lokale groep in je buurt?" — repointed from `mailto:` to `groups.start`. ✅ done.
- [Chapters](chapters.md) "Staat jouw stad er nog niet bij?" CTA + the nearby empty state ("Misschien start jij er een?"). ⬜ still point at `volunteer` / `mailto` — to repoint.

## Skeleton

```
┌────────────────────────────────────────────────────────────┐
│ HERO — "Breng Kidical Mass naar jouw buurt"                  │
│ subhead reframes the size · [ Ik wil starten ] → #start      │
├────────────────────────────────────────────────────────────┤
│ DE DEAL — "Je hoeft dit niet alleen te dragen"   ← barrier 1 │
│   Wat jij brengt            │   Wat wij dragen                │
├────────────────────────────────────────────────────────────┤
│ WAT HET ÉCHT VRAAGT — honest plain list (the filter)         │
├────────────────────────────────────────────────────────────┤
│ ER IS ANIMO — proof band (light-blue)            ← barrier 3 │
├────────────────────────────────────────────────────────────┤
│ JE STAAT ER NIET ALLEEN VOOR                     ← barrier 2 │
├────────────────────────────────────────────────────────────┤
│ INTENT-FORM — "Zin om te beginnen?"                          │
│   naam · e-mail · gemeente/postcode · waarom · team?         │
│   ( ) praat eerst met iemand die het al deed                 │
│   ( ) ik ben er klaar voor — neem contact op                 │
│   reassure: "Een echt mens leest dit en antwoordt je."       │
├────────────────────────────────────────────────────────────┤
│ CLOSING — "Benieuwd welke steden al meefietsen?" → /chapters │
└────────────────────────────────────────────────────────────┘
```

Build: `resources/views/groups/start.blade.php` · `app/Livewire/StartGroupEnquiry.php` (+ view) · `resources/css/pages/start-een-groep.css` · `GroupController@start`. Tests: `tests/Feature/StartGroupEnquiryTest.php`.

## Open questions (→ Leticia)

1. **Peer-path consent** — are trekkers willing to be brokered as mentors to new starters? (Brokered model never exposes their contact, but they say yes per match.)
2. **Intent signal** — confirm the form satisfies the worry: people express real intent *before* any trekker's time is spent. `[BLOKKEREND]` for the peer path.
3. **Response promise** — what honest "binnen X dagen" can the team commit to? The black-box fix only works if the number is true (currently the copy promises a personal reply, no number).
4. **Proof** — a short quote from a recent trekker, and confirm the "60+ parades / network" stats are current for 2026.
5. **Scope upgrade** — sign off on promoting start-a-group from "static MVP" to a real page + intent form.

## Still open (backend — Nico, GitHub #37 ecosystem)

- A dedicated `StartGroupEnquiry` model + a per-region routing rule once ownership (D-12) is decided. For now every enquiry lands in `ContactForm` and is mailed to the central comms inbox, tagged in the body.
- Optional: a postcode → nearby-trekker lookup if we later want to surface live proof or semi-automate the brokering (the `CurrentLocation` / `Proximity` infra already exists).

---
title: Contact (national) — page brief
tags: [design, skeleton]
sources:
  - docs/wiki/design/20-structure.md (national front door, Contact message entity)
  - docs/wiki/strategy/20-personas.md (secondary audiences)
  - docs/raw/website/organisation.md (coordination-duo copy)
  - config/kidicalmass.php (contact single-source)
phase: design
updated: 2026-07-05
---

# Contact (national) — page brief

`/contact` (P-05, route `contact`, footer utility). The national front door to the coordination duo for **press / partnership / general** enquiries. Serves the secondary audiences (press, sponsors, potential chapter leads); families and volunteers are served elsewhere and get redirected.

## Strategy

**Who arrives, in what mental state:**

- **A journalist on deadline.** Impatient, task-driven, scanning for an address or a name. Trust signal: a human answers, fast. Anything that smells like a black-hole form is a fail.
- **A potential partner or sponsor.** A notch more formal, evaluating whether this org is organised enough to work with. Wants a credible, low-friction way in, not a sales pitch.
- **Someone with "just a question".** A parent, a teacher, a commune employee. Slightly unsure whether their question is "allowed" here. Needs warmth and permission: every question is welcome.
- **A misrouted volunteer.** Arrives here because "contact" is the obvious word. The page must redirect them to Help out / their chapter *kindly*, without making them feel wrong.

**What good looks like:** the visitor knows within seconds that real people (the coordination duo) read this inbox, picks the right lane without thinking, and leaves either having sent a message or holding the direct address. The register is utility-warm: functional page, human voice, no conversion ask.

**Explicit non-goals:** no donation ask, no newsletter capture, no per-chapter contact details (chapters have no public contact fields; volunteering routes via Help out → chapter, per 20-structure.md).

## Scope

- **Contact form** → existing `ContactFormComponent` (Livewire): name, email, phone (optional), message, honeypot; stores a `ContactForm` row and mails the comms inbox. Re-skinned Dutch/public-brand (was unused English Flux markup). New: optional **topic** select (algemeen / pers / partnerschap), prefilled via `?onderwerp=`, stored as an "Onderwerp:" prefix in the message (no schema change).
- **Direct details** from `config/kidicalmass.php`: `bike@kidicalmass.be`, `0495 81 27 95` (tel: uses e164). Socials from `config('kidicalmass.social')` where the design wants them.
- **Volunteer redirect** to `route('volunteer')`; optional signposts to `groups.start` and `about.press`.
- **Copy:** inline NL for the prototype round (getting-started precedent); extract to `lang/nl/contact.php` when the winning variant lands. `meta.contact` added.

## Structure

Footer-linked utility page, outside main nav. One page, no children. Form submission stays Livewire (no POST route). Buckets are a routing aid, not separate pages.

## Skeleton — prototype rounds

**Round 1 (2026-07-05):** three directions — A "Drie deuren" (triage first), B "Zeg gewoon hallo" (email first), C "Meteen geregeld" (form forward). **Frederik picked A** but flagged it as overdesigned (huge components, too much vertical space); B and C deleted (git history has them).

**Round 2 (2026-07-05):** three lighter A-treatments next to the original — a1 "Compacte kaarten" (bordered tiles), a2 "Rijen" (divided link list), a3 "In het formulier" (buckets as radio pills at the top of the form, `ContactFormComponent :topic-pills="true"`). **Frederik picked a3** but wanted the trailing lines (liever mailen, meehelpen-redirect) arranged into sidebar blocks, the `/about/press` pattern; a/a1/a2 deleted.

**Round 3 (2026-07-05):** a3 plus three sidebar arrangements (a3a twee kaarten / a3b kaart+link / a3c lijnen), all on the press composition (`md:grid-cols-[1.5fr_1fr]`, sticky aside). **Frederik picked a3a.**

**Final (2026-07-05):** a3a folded into `contact.blade.php`; variants + picker deleted; copy extracted to `lang/nl/contact.php`. Polish/distill on Frederik's notes: hero lead cut (the pills label does the triage explanation), the pills fieldset got an explicit legend-gap fix (a `<legend>` sits outside the field's flex flow), the submit hugs its label (`align-self: flex-start`), and the form component's select mode was deleted — pills are the only topic UI. Page shape: compact bare hero → form (pills · naam · e-mail/telefoon · bericht → pill button) with sticky sidebar (info-cards: liever mailen · meehelpen-redirect).

### The triage-first arc (all round-2 variants)

The structure-plane buckets made literal: choose your lane, then write.

```
┌──────────────────────────────────────────────┐
│ HERO (compact, bare) "Waarvoor klop je aan?" │
├──────────────────────────────────────────────┤
│ [Algemene vraag] [Pers] [Partnerschap]       │  3 door cards → form with topic preselected
├──────────────────────────────────────────────┤
│ ~ Meehelpen? Dat loopt via je lokale groep ~ │  volunteer redirect band
├──────────────────────────────────────────────┤
│ FORM "Of schrijf ons meteen" + direct mailto │
└──────────────────────────────────────────────┘
```

(a1 renders the doors as tiles, a2 as rows, a3 folds them into the form as pills; the redirect band becomes an inline line in a1–a3.)

## Notes / open

- **Inbox ownership** (extends `D-12` to the national door): routing to the comms inbox does not guarantee an answer; prod needs `MAIL_COMMUNICATIONS_ADDRESS` set and an owner. `[client]`
- **Address split**: old site mixes `bike@kidicalmass.be` and `contact@kidicalmass.brussels`; this page single-sources the config value. Decide alias/redirect for the Brussels address. `[client]`
- **Topic storage**: message-prefix for now; promote to a `ContactForm` column if admin filtering is wanted. `[back]`
- **FR locale** copy when the FR layer lands. `[content]`

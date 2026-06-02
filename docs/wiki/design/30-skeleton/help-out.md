---
title: Help Out
tags: []
sources: [notion, raw/website/volunteer.md, raw/website/volunteer-roi-charter.md]
phase: design
updated: 2026-06-02
---

Status: ✅ Reframed (2026-06-02). Page URL: `/help-out` · `/nl/meehelpen` · `/fr/s-engager` ✅ *(EN deferred — D6)*

**Page title confirmed: "Help out" / "Meehelpen" / "S'engager"** — warmer than "Volunteer", fits ToV. *(Redirect from /volunteer and /contribute if those URLs were ever public)*

**Summary:** Help out is now a pure **orientation page** — it motivates and *routes*; it does **not** hold the contact form. This reconciles the page with the already-decided structure ([20-structure.md](../20-structure.md): *"the volunteer contact form lives on chapter pages"*) and with [chapters.md](chapters.md). The flow: **pitch → roles-as-invitation → what joining looks like → "Find your chapter →" (`/chapters`)**, with a calm secondary exit for "no chapter near me". The volunteer then contacts *from* their chapter page, so routing is implicit by context — no municipality dropdown.

> **Reframe note (2026-06-02):** the earlier version of this file put a routed contact form *on* Help out (with a municipality dropdown). That contradicted [`20-structure.md`](../20-structure.md) (form on chapter pages) and the chapter-page form in [`chapters.md`](chapters.md). The J2 UX pass resolved it in favour of **routing-via-chapter**: Help out orients, the chapter page converts. See [log 2026-06-02](../../log.md).

> **Built — the confident seam (2026-06-02).** The CTA is no longer a button to the family `/chapters` index. After a `/critique`, it became an **inline group picker**: tap your group → land straight on that chapter page's sign-up form (`/chapters/[id]?intent=volunteer#aanmelden`), where an `intent`-aware welcome leads with the form ("Je komt meehelpen — welkom"). No map detour, no typing. View `resources/views/volunteer.blade.php` (groups from `VolunteerController`); chapter form = `ChapterVolunteerSignup` Livewire component (distinct from the event-detail `VolunteerSignup`). `[backend]` it still emails the **central comms inbox**, tagged with the chapter name + chosen roles — true per-lead routing waits on a per-group lead email (open Q1).

---

## The J2 flow (what this page is one step of)

```
        ┌─────────────┐   "Find your chapter →"   ┌────────────┐
Home ──▶ │  Help out   │ ─────────────────────────▶│ /chapters  │
 vol CTA │(orientation)│                           │ (map+list) │
         └─────────────┘                           └─────┬──────┘
              │ secondary exit                           │ pick city
              ▼ "No chapter near you? Start one →"        ▼
        mailto / link-out                        ┌──────────────────┐
                                                 │ Chapter page     │
                                                 │  • upcoming rides │ ◀─ J1 data
                                                 │  • Organised by   │
                                                 │  • HELP OUT block │ ← form lives here
                                                 │    (role ✓ + msg) │   (see chapters.md)
                                                 └────────┬─────────┘
                                                          │ submit
                                                          ▼ inline confirmation:
                                                   warm + "come to our next ride ↑"
```

---

## Strategy

The conversion page for people who want to help. Replaces the `bike@` email black hole with a clear path from curiosity to a *routed, local* contact.

### The reframe (J2 UX pass, 2026-06-02)

A volunteer doesn't email a national org — they **walk into their neighbourhood's group**. Routing isn't a dropdown; it's *wayfinding through the chapter*. This does three things at once:

- **Reinforces locality** (core ToV: "local and grounded") — by the time they contact, they've *seen* their local chapter is real (its rides, its place on the map, its named team).
- **Makes routing implicit** — contacting *from* a chapter page sends the enquiry to that chapter by context, not by a form field someone has to get right.
- **Keeps the promise honest** — because the chapter page does the trust-building, the confirmation copy stays modest (*"someone from your local team will be in touch soon"*) without feeling like a black hole. We lean on **shown locality, not an over-promised reply speed** — which deliberately sidesteps the unvalidated reply-loop assumption in [D-1](../01-concerns.md).

### Who arrives and in what mental state

**Former or current ride participant — warm and inspired.** The most common path. They attended a ride, loved it, thought "I want to be part of this." They arrive already sold. Don't waste their energy with a heavy pitch — give them role clarity, commitment honesty, and a fast route to their chapter.

**Curious outsider — "I've heard about this, can I help?"** Arrived via a friend, social media, or the homepage volunteer CTA. Not a former rider; needs a bit of context before they can self-identify a role. The pitch handles this.

**Potential chapter lead — "There's no chapter near me."** A parent in a city without a Kidical Mass. May not know "start a chapter" is an option. Handled as a calm **secondary exit**, not a dead end, and deliberately *not* designed deeply here (anti-overlap: that edges into J3/chapter-lead territory).

### Key psychological insight

The raw volunteer page says it plainly: *"Tu n'as pas besoin d'être un pro du vélo ou de l'événementiel, juste l'envie d'aider."* Many potential volunteers hesitate because they think they're not qualified. The page's job is to dissolve that hesitation with warmth and specificity — **not** a requirements list.

At the same time, honesty about the commitment (meetups, guidelines) *attracts* the right people. Include it warmly.

### The post-submit limbo (new risk this reframe opens)

Because we deliberately don't promise a fast reply, **the silence after submitting is the new risk moment.** The flow must make the wait feel like *being welcomed in*, not *being ignored* — so the chapter-page confirmation surfaces the chapter's **next ride** ("come say hi this Sunday"). The hook reuses ride data already on the chapter page; no new dependency. (Confirmation lives on the chapter page — see [chapters.md](chapters.md).)

### Organisational objective

Replace the `bike@` email black hole with a structured, routed enquiry. Every submission reaches the right chapter by context. No central inbox.

---

## Scope

**Must have:**
- Short pitch ("why volunteer") — leads with the *"you don't need to be a pro"* barrier-dissolver
- Overview of the 5 volunteer roles as **invitation cards** (pink vest, co-organiser, communicator, photographer, DJ)
- "What you'll get / what we ask" — honest commitment, **distilled from the ROI principles** (see Guidelines below)
- **Primary CTA: "Find your chapter →"** → `/chapters` (the routing step)
- "Start a chapter" secondary exit for new cities (static for MVP; link-out)

**Should have:**
- Link to the volunteer guidelines (external Google Doc) — placed at the *commitment* moment, not as an entry gate

**Out of scope / moved:**
- ~~Contact form on this page~~ → **moved to the chapter page** ([chapters.md](chapters.md)); routing is by context
- ~~Municipality dropdown~~ → no longer needed (routing-via-chapter)
- Per-role signup (all roles go through the one chapter-page form; role is a checkbox there)
- Structured onboarding workflow / volunteer dashboard (deferred → back-office, [D-1](../01-concerns.md))
- Inline volunteer rules content (the ROI stays an external link for MVP)
- A chapter map or locator on this page (that's `/chapters`)

---

## Structure

Single orientation page, no sub-navigation, **no form**. Answers "why and how can I help?" then routes to "where" (`/chapters`).

**Section flow:**
1. Page header — "Help out" / "Meehelpen" / "S'engager"
2. Why volunteer — the pitch (barrier-dissolver first)
3. Roles overview (5 roles as invitation cards)
4. What joining looks like (honest get/ask — distilled ROI principles + guidelines link)
5. **Find your chapter** — the single primary action → `/chapters`
6. Start a chapter — calm secondary exit for new cities

**Key links out:**
- Primary CTA → `/chapters` (then the chapter page holds the routed form)
- "Start a chapter" → `mailto:bike@kidicalmass.be` (+ secondary link to `/chapters`)
- Volunteer guidelines → Google Doc (external, new tab) — see Guidelines

---

## Skeleton

**One primary action.** Everything on the page is orientation and motivation; the page's single job is to route a motivated person to their chapter. No competing form.

**Roles as orientation, not form fields.** The 5 role cards help people self-identify *before* they reach the chapter-page form (where the role becomes a checkbox). Role intent does **not** carry across the `/chapters` hop in MVP — the checkboxes start fresh on the chapter form. A `?role=` deep-link that pre-ticks the box is a noted future enhancement.

### Desktop

```
┌──────────────────────────────────────────────────────┐
│ NAV                                                  │
├──────────────────────────────────────────────────────┤
│  Help out                                            │
│  Join the people who make every ride happen.         │
├──────────────────────────────────────────────────────┤
│  [Pitch — 3–4 sentences. "You don't need to be a     │
│   cycling or events pro — just the urge to help."    │
│   Dissolves the qualified-enough barrier first.]     │
├──────────────────────────────────────────────────────┤
│  How you can help                                    │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐  │
│  │ 🦺 Pink vest  │ │ 🗓 Co-org    │ │ 📢 Comms      │  │
│  │ ride alongside│ │ plan & prep  │ │ spread the    │  │
│  │ keep it safe  │ │ the route    │ │ word          │  │
│  └──────────────┘ └──────────────┘ └──────────────┘  │
│  ┌──────────────┐ ┌──────────────┐                   │
│  │ 📸 Photo     │ │ 🎵 DJ        │  (invitations,    │
│  │ capture it   │ │ set the mood │   not job specs)  │
│  └──────────────┘ └──────────────┘                   │
├──────────────────────────────────────────────────────┤
│  What joining looks like                             │
│  You'll get:              We ask:                    │
│  · kit + support          · enthusiasm & a positive, │
│  · optional training        respectful attitude      │
│  · 4 meetups/year         · follow our guidelines →  │
│  · a real community         (kindness + safety)      │
│                           · one rep at the annual    │
│                             meetup (if on a team)    │
├──────────────────────────────────────────────────────┤
│  [distinct background — the single primary action]   │
│                                                      │
│   Ready? Find your local chapter.                    │
│   Every chapter has its own team — you'll reach      │
│   them directly, not a central inbox.                │
│                                                      │
│            [ Find your chapter → ]  (→ /chapters)    │
│                                                      │
│   No Kidical Mass in your city yet?                  │
│   You could start one → Email the team   [mailto]    │
├──────────────────────────────────────────────────────┤
│ FOOTER                                               │
└──────────────────────────────────────────────────────┘
```

### Mobile

```
┌──────────────────────┐
│ NAV (hamburger)      │
├──────────────────────┤
│  Help out            │
│  Join the people who │
│  make every ride     │
│  happen.             │
├──────────────────────┤
│  [Pitch — 3–4 lines] │
│  You don't need to   │
│  be a pro…           │
├──────────────────────┤
│  How you can help    │
│ ┌──────────────────┐ │
│ │ 🦺 Pink vest      │ │
│ │ ride alongside,  │ │
│ │ keep it safe     │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 🗓 Co-organiser  │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 📢 Comms          │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 📸 Photographer  │ │
│ └──────────────────┘ │
│ ┌──────────────────┐ │
│ │ 🎵 DJ             │ │
│ └──────────────────┘ │
├──────────────────────┤
│  What joining looks  │
│  like                │
│  You'll get: …       │
│  We ask: …           │
│  (follow guidelines→)│
├──────────────────────┤
│  [section break]     │
│  Ready? Find your    │
│  local chapter.      │
│  You'll reach them   │
│  directly.           │
│  [Find your chapter→]│
│                      │
│  No chapter yet?     │
│  Start one →         │
├──────────────────────┤
│ FOOTER               │
└──────────────────────┘
```

### Annotations

- **Header subtitle:** "Join the people who make every ride happen." Community-first, grounded in real scale (100+ active volunteers per the raw site).
- **Pitch:** 3–4 sentences. Leads with the barrier-dissolver (*"you don't need to be a pro"*), ends on a time/reward framing. No guidelines link here — that belongs at the commitment moment, below.
- **Role cards:** 5 cards, invitation language not job descriptions. Orientation only — they do **not** link into a form on this page; the role becomes a checkbox on the chapter-page form. Full per-card copy in [help-out-content.md](help-out-content.md).
- **"What joining looks like":** the honest get/ask. The "we ask" side is distilled from the ROI principles (kindness + safety + meetups) — warm, not legalese. The guidelines link sits here.
- **"Find your chapter →":** the single primary action, in a visually distinct band. The page has *one* job: motivate, then route. Copy makes the locality promise explicit ("reach them directly, not a central inbox").
- **Start a chapter:** calm secondary exit, visually quieter than the primary CTA. Email CTA (mailto) + secondary link to `/chapters`.

---

## Guidelines / ROI — repurposing the charter

The volunteer guidelines doc (FR *"Règlement d'Ordre Intérieur"* / NL *"Huishoudelijk reglement"*, captured at [`raw/website/volunteer-roi-charter.md`](../../../raw/website/volunteer-roi-charter.md)) is an **operational charter for organisers & escorts (P4/P5)** — not curiosity-stage material. Most of it (min. 4 escorts/ride, captain-sweeper roles, 8–9 km/h, the shared-vest bag, feedback-after-every-ride, "no political action without coordination") is **post-signup operational know-how** → belongs in the onboarding email / chapter back-office ([D-1](../01-concerns.md)), **not** this page.

**What this page repurposes — the 🧭 principles only:**

| ROI principle | Use here | Note |
|---|---|---|
| **Bienveillance — #kindnessisking** | "We ask" + pitch tone | Warmest line in the doc — pure ToV |
| **Inclusion** (all kids/families, any level) | Pitch | Dissolves the "am I good enough?" barrier at the source |
| **Action positive** (no anti-car rhetoric; modal shift at one's own pace; with a smile) | **Tone constraint for ALL copy in this flow** | This *is* the ToV "committed, not preachy" quality in their own words — it governs how every line is written, not just a bullet |
| **Sécurité avant tout** | Pink-vest card + "we ask" | One warm line; operational safety detail stays in onboarding |
| **Organisation partagée — co-coordination** | "what you'll get" (community) | Reinforces "you're joining a real local team" |

**Decisions:**
- **Rename it** — surface as "volunteer guidelines" / *"onze afspraken"*, never "ROI / Huishoudelijk reglement" (bureaucratic, off-ToV).
- **Link it in volunteer onboarding only, not on this public page** (Frederik 2026-06-02). The doc is confirmed public + kept, but Help out's "what we ask" just *names* the agreements (kindness + safety) without linking the formal charter. The link itself lives in the logged-in back-office / volunteer "getting started" ([D-1](../01-concerns.md), P-09). **Host as a PDF later**, replacing the Google Doc link.
- **Companion asset:** the "Safety First" video (`youtu.be/i9YQxJ-ChNM`, referenced on the raw volunteer page) is friendlier than the charter text for the safety/training angle — prefer it on the pink-vest card.

---

## Open Questions / Necessary Refinements

1. **Chapter→lead routing + fallback** *(build — [GitHub #37](https://github.com/ndeblauw/kidicalmass/issues/37))* — the `Volunteer enquiry` ([content model](../20-structure.md)) currently emails the central comms inbox tagged with the chapter; per-group lead routing (with a fallback when a chapter has no lead) is specced for Nico in #37. *(Lower-stakes than before: we deliberately promise "the team", no name or SLA, so the design doesn't hinge on this.)*
2. **Is the ROI safe to surface publicly?** ✅ Resolved (Frederik 2026-06-02): confirmed public + keep the doc, but **not linked on this public page** — only in volunteer onboarding / the logged-in back-office ([D-1](../01-concerns.md)). Host as a **PDF later**. Help out's "what we ask" now names the agreements (kindness + safety) without linking the charter.
3. **A short "good vibes" excerpt?** *(content)* — would the team like a one-screen charter in KM's voice (warm enough to inline) distinct from the full legal ROI (external source of truth)? That would be the ideal thing to inline in "We ask".
4. **"4 meetups/year"** *(client)* — confirm this applies to all Belgian chapters, not just Brussels.
5. **Confirmation + role checkboxes** — both now live on the chapter page; specced in [chapters.md](chapters.md).

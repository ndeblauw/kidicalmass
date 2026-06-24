---
title: Chapter page (P-11) — v4 structure rebuild · build briefing
tags: [chapter, P-11, skeleton, build-briefing]
sources: [wiki/design/30-skeleton/chapters.md, resources/views/groups/show.blade.php]
phase: design
updated: 2026-06-23
---

# Chapter page v4 — build briefing

Hand-off for a **fresh build thread**. The structure (skeleton) is **locked**; this thread did the
greybox prototype that locked it. Surface/styling is deliberately **open** — that's the build thread's
creative work, done in the real app on the CSS-partials architecture.

- **Canonical skeleton + reasoning:** [`docs/wiki/design/30-skeleton/chapters.md`](../../wiki/design/30-skeleton/chapters.md) → *Critique v4* + the *v4 locked* deltas appended there.
- **Visual reference (greybox, structure only):** [`assets/2026-06-23-chapter-v4-skeleton.png`](assets/2026-06-23-chapter-v4-skeleton.png) · live HTML: [`assets/2026-06-23-chapter-v4-skeleton.html`](assets/2026-06-23-chapter-v4-skeleton.html).
- **File being rebuilt:** `resources/views/groups/show.blade.php` (currently the v3 arc) + `resources/css/pages/chapters.css`.
- **Controller:** `app/Http/Controllers/GroupController@show` (provides `$group`, `$activities`, `$partners`, `$pressArticles`, `$latestRide`).

## Why this rebuild (one line)

The shipped v3 page is sequenced by **content type**, so it opens flat and the parade — the chapter's
product — is one undifferentiated row. v4 re-sequences by **reader intent** and makes the parade the
page's gravity. Full diagnosis + the four roles/beats are in `chapters.md` Critique v4.

## The locked arc

```
1  Hero                  — mission line only (warm what/why). NO micro-proof, NO next-parade line, NO press.
2  De volgende parade    — SPLIT-SCREEN. left = parade (details + Kom mee + built-in subscribe CTA);
                            right = social proof: TWO stat cards only — "sinds [jaar]" + "N ritten" (real data).
                            NO photo (the §5 gallery covers last-parade photos). NO map/route visual.   ★ dominant
3  Alle parades          — compact strip, paired directly under §2 (reads as one unit). "alle ritten (ook voorbije) →"
4  Ook in [gemeente]     — BIG "sky" band: workshops / filmavonden / repair as activity CARDS. Real presence, distinct from parades.
5  Zo ziet het eruit     — photo wall + lightbox — KEEP v3's EXACTLY (hard constraint) + colouring-page download WITH a preview thumbnail.
6  Wie zijn wij          — the EXISTING team carousel (kept exactly), RELOCATED here from the v3 closing band + the "wij zwaaien je welkom aan de start" welcome line, surfaced for everyone.
7  Help mee              — yellow band, LEANER recruitment ask (faces moved to §6), on-demand reveal (form kept as-is; ?intent=volunteer auto-opens).
8  Affiches + sponsors   — quiet tail: affiches/flyers (thumbnails, optional) + "met dank aan" sponsors (confirmed). NO press. hide-if-empty.
   closing               — "Deze groep is van jou." → back to /chapters.
```

Role/beat per section is tagged in the greybox rail (see the PNG). Colour story: blauw (hero) →
accent/wit (parade) → wit → **sky** (§4) → wit → geel (§7) → quiet tail → geel footer-naad.

## Build approach — two phases (Frederik)

Build incrementally, not in one shot. Each phase is reviewed before the next.

**Phase 1 — reordering pass.** Re-sequence the EXISTING sections of `show.blade.php` into the v4 arc,
moving working blocks as-is: gallery stays put structurally, **carousel relocates to §6**, the form +
its reveal move with §7, the typed agenda content feeds §2/§3/§4. No new layouts yet — just the new
ORDER with the kept components rendering real content end-to-end. Lowest risk; this is what we ship/review first.

**Phase 2 — tweaks (layered onto the reordered page, each reviewable on its own).** Hero trim (mission
line only); §2 split-screen + the two real stat cards; §3 strip pairing; §4 big sky band of activity
cards; §5 colouring preview; §7 leaner ask; §8 affiches + sponsors (press fully removed). The plan
enumerates these as discrete, independently-tunable tweaks.

## Deltas from the Critique-v4 *proposal* in chapters.md (this prototype changed these)

The earlier written v4 proposal differs from what we locked. Build the **locked** version:

1. **Hero is mission-line ONLY.** The proposal put a next-parade glance + micro-proof + a press trust line in the hero. **All three are cut** — they felt dry and slowed the move to the ride. Hero = name + one warm what/why line + the ride photo. Nothing else.
2. **Press is removed from the page entirely.** The proposal kept press as a one-line trust signal up top and a block at the bottom. **Neither remains.** Name-dropping outlets invites a click that leads people away. Press lives on the **channel-wide Press page** (live since 2023); that carries the trust signal for the whole movement.
3. **§2 is a split-screen, not a single card.** left = parade + Kom mee + subscribe CTA; right = **social proof: two stat cards ONLY** — **"sinds [jaar]"** (`groups.started_at`) + **"N ritten"** (count of past `kidicalmass` activities). Both **real / derivable — not faked.** NO attendance/"gezinnen" number (no honest source). NO photo on the right (the §5 gallery already owns last-parade photos — don't duplicate). NO map/route visual. The micro-proof cut from the dry hero **relocates here**, where it motivates.
4. **§4 "Ook in [gemeente]" is promoted.** The proposal had it "lighter weight". Locked: its **own BIG sky band** of activity cards — real presence, still clearly distinct from (and lighter than) the parade. Card approach is intentional ("fun"); revisit how it scales when a group has many.
5. **§8 has no press.** "Voor pers & delen" becomes **"Affiches + sponsors"**: optional affiche/flyer thumbnails + a confirmed "met dank aan" sponsors strip.

Everything else from Critique v4 holds: parade-as-gravity, faces surfaced mid-page, recruitment earned-and-late, the four roles/beats, hide-if-empty discipline, the designed just-started empty state.

## Evolves v3 — do NOT read as regression

- **Event tiering vs v3's unified typed agenda.** v3 fixed activity-type blindness with one day-grouped typed list (`x-ride-day`). v4 **re-splits** that into weighted zones: §2 featured next `kidicalmass` ride + §3 parades strip + §4 the non-ride activities. Keep v3's rules: never dress a workshop as a ride; honest empty state when no ride is scheduled (§2 left becomes "nog geen parade — laat je mailadres achter", §3/§4 still render). This is a deliberate sharpening of v3, agreed with Frederik.
- **"Mis geen rit"** moves from a standalone band into the **§2 left card** as the built-in subscribe CTA (peak intent).
- **Faces** move from "only in the closing band" to a **mid-page §6 beat** (orientation) AND keep the on-demand recruitment reveal at §7.

## Components / patterns to reuse (don't rebuild)

- **Activity rendering:** `x-ride-day` (date-rail lockup) for §3/§4 rows where it fits; the §2 featured parade is a bespoke split, not `x-ride-day`.
- **Photo wall + lightbox:** keep v3's `chapter-gallery` Alpine block in §5 verbatim — it's good.
- **Volunteer form:** `livewire:chapter-volunteer-signup` (`ChapterVolunteerSignup`) in §7, on-demand reveal pattern already in the v3 file (Alpine `x-data="{ open }"`, `?intent=volunteer`).
- **Buttons:** `x-cta-button` (variants blue/secondary).
- **Newsletter/subscribe:** reuse the existing `x-newsletter-optin` mechanics for the §2 subscribe CTA where sensible (it's a fake/client-side opt-in until Nico's subscription model — see below).
- **CSS:** new rules go in `resources/css/pages/chapters.css` (page-only) or `resources/css/components/*` if reusable — never `app.css`. Enforced by `CssArchitectureTest`. Tokens only (no raw hex/px in components).

## Data: real vs faked (this build)

Schema reality is unchanged from v3 (`groups`: name/shortname/zip/parent_id/started_at…; `users` no photo/role; `activities`: title_nl/fr, location, postal_code, distance, begin_date, **activity_type** enum). Build with existing data + graceful defaults; **do not migrate the shared schema** (Nico owns it).

- **Real now:** the typed activities split (`activity_type` → featured ride / parades strip / other-activities band); the §2 **stat cards** ("sinds [jaar]" from `started_at`, "N ritten" from a past-`kidicalmass` count — derive in the controller); the filtered "alle ritten (ook voorbije) →" deep-link (`route('activities.index', ['gemeente' => $group->id])`); the §5 photo wall + lightbox (`latestRide` gallery); §6 faces from the `group_user` pivot; the §7 on-demand reveal + Livewire form.
- **Faked (clearly commented, removable):** the §2 **subscribe CTA** (client-side "bedankt", no persistence — subscription model is Nico's); §6 **active volunteers + roles** (lead = real `trekker` from pivot + faux crew until a pivot `role` field exists); §8 **affiches** + **sponsors** (faux unless `$partners` is populated); §5 **colouring download** (faux file).
- **Never fabricate:** press (it's gone from the page anyway).

## Data deps to spec for Nico (carry forward)

- `activity_type`-aware **grouping** (separate `kidicalmass` from workshop/meeting/other into the §2/§3 vs §4 zones).
- §2 stat cards use **`started_at`** (real) + a **past-ride count** (derive in the controller now). Attendance/"gezinnen" is intentionally NOT shown (no honest source) — only add if real data appears later.
- **`group_user.role`** pivot (trekker / roze hesje / comms) + user photo for §6.
- Per-group **subscription model** for the §2 subscribe CTA (shared with the events-overview opt-in placeholder).
- Per-group **lead email** for J2 routing — [GitHub #37](https://github.com/ndeblauw/kidicalmass/issues/37).
- **Download categorisation** (family = colouring → §5; spreader = affiche/flyer → §8) + a thumbnail/preview for poster-type downloads.

## Empty / edge states (first-class)

- **No scheduled parade:** §2 left → "Nog geen parade gepland. Laat je mailadres achter…" (the slot stays the hero, as a promise); §3/§4 still render if they have content.
- **Just-started group** (no team, no rides): short warm page — hero → §2 empty promise → §6 "Help [stad] op gang" → hand-back. §3/§4/§5/§8 hide. (See the v2/v3 just-started mockup in chapters.md.)
- Every optional block is **hide-if-empty**; an empty §8 zone disappears entirely.

## Copy

Draft NL copy is in the greybox — treat it as **placeholder, not final**. ToV polish (per `docs/tone-of-voice.md`, no em-dashes) is its own pass. Judge structure against the greybox, not its wording.

## Verification (build thread)

- `GroupsTest` (+ type-label tests) must be updated to the v4 arc and stay green.
- Run `php artisan test --compact --filter=Group` + `--filter=CssArchitectureTest`; Pint `--dirty`; `npm run build`.
- Visually verify on **Schaarbeek (filled)**, **Anderlecht (workshop, no ride)**, **Brussel-Stad (meeting)**, plus a just-started group — full-page, all four.
- Update the P-11 registry row + roll-up + a `log.md` build entry per `/pipeline` when done.

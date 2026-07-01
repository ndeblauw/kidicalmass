---
title: Ride-page share band
tags: [design, share, activities, public-site]
sources: [resources/views/activities/show.blade.php, resources/css/pages/activity.css, docs/tone-of-voice.md]
phase: design
updated: 2026-06-24
---

# Ride-page share band

## Problem

The individual ride page (`/nl/events/{id}`, route `activities.show`) shares poorly. Sharing
today is a single `Deel` button buried in a fixed bottom action bar
(`activities/show.blade.php:351-371`). It calls `navigator.share` with a clipboard-copy
fallback. Three failures:

1. **Discoverability** — one ghost icon button, easy to miss; most visitors never share.
2. **Framing** — a bare button says "share this", never *why*. No emotional nudge.
3. **Channel fit** — relying on the native sheet buries WhatsApp, the #1 peer-to-peer
   channel for Belgian parents. No explicit channels.

## Goal

Reframe sharing as **inviting a specific gezin** and make the invitation visible and warm.
Primary intent: a parent sends this ride to a friend they think would enjoy it.

## Decisions

- **Placement:** one framed band, **bottom of page only** (no duplication). It sits after the
  support callout and before the closing CTA, the moment the reader is most convinced.
- **Channels:** WhatsApp (visual lead), Copy link, Email, Facebook.
- **Mobile:** explicit channel buttons everywhere. No `navigator.share` native sheet — the
  framed band is the point and the native sheet would undercut it. WhatsApp/email/Facebook
  deep links work cross-platform.
- **iCal "Bewaar in agenda":** removed entirely (along with the fixed bar). The ride page
  ends on the share invitation. The iCal *route* (`activities.ical`) stays; only the button
  is dropped.

## Design

### Component — `<x-share-band>`

Reusable Blade component at `resources/views/components/share-band.blade.php`.

**Props:**
- `:url` (string, required) — the canonical ride URL to share.
- `:title` (string, required) — ride title, used in pre-filled messages.
- `:date` (string, required) — human date, used in pre-filled messages.
- `heading` (string, optional, default below).
- `subline` (string, optional, default below).

**Appearance** is baked into the component as token-backed Tailwind utilities (per the styling
architecture in CLAUDE.md): cream/`kidical`-toned band, `rounded`, generous padding, the
heading in the brand display face, subline in muted body. No raw hex/px. No `app.css` entry;
no page CSS partial unless an effect genuinely needs one.

**Layout:** two zones inside the band — copy on the left (heading + subline), channels on the
right. Channels: a labelled pill button "Kopieer link" + three circular icon buttons
(WhatsApp, Facebook, email), matching the approved mockup. Wraps to stacked on narrow screens.

### Copy (tone-of-voice, no em-dashes)

- Heading: **"Ken je een gezin dat dit leuk zou vinden?"**
- Subline: **"Samen fietsen is leuker. Stuur deze rit door, dan staat de straat zondag nog
  voller met kinderen."**
- Copy-link label: **"Kopieer link"**, becomes **"Gekopieerd!"** for 2s on click.

Pre-filled WhatsApp / email message (NL, warm, no em-dash):

> "Zin om samen te fietsen? {title} op {date}, een vrolijke gezinsrit door autovrije straten.
> Rij je mee? {url}"

Email subject: **"Een leuke fietstocht voor jullie gezin"**.

### Channel mechanics

All channel buttons open in a new tab (`target="_blank" rel="noopener"`), except copy.

- **WhatsApp** → `https://wa.me/?text={urlencoded message+url}`
- **Facebook** → `https://www.facebook.com/sharer/sharer.php?u={urlencoded url}`
- **Email** → `mailto:?subject={subject}&body={urlencoded message+url}`
- **Copy link** → Alpine `navigator.clipboard.writeText(url)` then `copied = true`,
  reset after 2000ms (reuse the existing pattern from the old action bar).

### Accessibility

- Icons `aria-hidden="true"`; each icon button has a descriptive `aria-label`
  (e.g. "Deel via WhatsApp").
- Copy button announces state change; "Gekopieerd!" in an `aria-live="polite"` region.
- Band heading is a real `<h2>` (raw heading element, never `flux:heading`).

### Integration into `activities/show.blade.php`

1. Remove the fixed `.activity-actions-bar` block (`show.blade.php:351-371`).
2. Place `<x-share-band :url="..." :title="$activity->title_nl" :date="..." />`
   after the `<x-support-callout>` (line 272) and before the `<x-slot:closing>`.
3. Build the canonical URL with `route('activities.show', $activity)` (absolute).
4. Format the date with the same helper/format the hero `<time>` uses.

### Cleanup

- Delete `.activity-actions-bar`, `.activity-actions-bar__copied`, and
  `.activity-actions-bar [data-flux-button]` rules from `resources/css/pages/activity.css`
  (lines ~234-266).
- Remove the `body:has(.activity-actions-bar) .site-footer { padding-bottom: ... }` rule from
  `resources/css/chrome.css:1-3` (no longer any fixed bar to clear).
- Remove the unused `.activity-hero__actions` rules from `activity.css:228-233` (dead code).

## Testing

Feature test on `activities.show` (`tests/Feature/`):

- The band renders and contains the heading text.
- WhatsApp link href contains `wa.me/` and the URL-encoded ride URL.
- Facebook link href contains `sharer.php?u=` and the ride URL.
- Email link href starts with `mailto:` and contains the subject + URL.
- The copy-link button renders with its `data` / Alpine hook.
- The old fixed action bar / "Bewaar in agenda" button is gone (assert absent).

## Out of scope

- Open Graph link-preview tuning (separate concern; covered by the `open-graph` skill if
  revisited later).
- Native share sheet.
- Hero-level share row.
- Share analytics / counts.

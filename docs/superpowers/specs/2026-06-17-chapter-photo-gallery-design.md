# Chapter photo gallery (P-11)

**Date:** 2026-06-17
**Page:** P-11 — chapter / local-group detail (`groups.show`, URL `/nl/chapters/{group}`)
**Status:** design approved, pending implementation

## Goal

Show a chapter's photos on its public detail page, sourced from the chapter's own
`gallery` media collection. The page currently shows a single hardcoded full-bleed
fallback cover photo and no gallery.

## Context

- `App\Models\Group` ("chapter" in the IA) implements `HasMedia` and already
  registers a multi-file `gallery` collection plus a single-file `main`, with
  `thumb` (150×150) and `card` (400×300) conversions. The data plumbing exists.
- Nico's "Photo upload by pinkvests" feature (commit `8051936`) attaches uploaded
  photos to **Activities**, not Groups. As a result `$group->getMedia('gallery')`
  is empty for every chapter today. Aggregating from activities was considered and
  **rejected**: this design reads the Group's own gallery collection directly.
- Public view: `resources/views/groups/show.blade.php` (blue → white → yellow band
  rhythm). Section 2 is the full-bleed cover (`.chapter-photo`); section 3 is the
  agenda; section 4 is the local-extras tail.
- Controller: `App\Http\Controllers\GroupController@show`.
- Page styles: `resources/css/pages/chapters.css` (already imported by `app.css`).
- The article page (`articles/show.blade.php`) has the only gallery precedent — a
  plain inline Tailwind grid. We are NOT following that; chapter styles use bespoke
  semantic classes in the page partial.

## Decisions (locked with the user)

1. **Source:** the Group's own `gallery` collection — `$group->getMedia('gallery')`.
2. **Populating:** a dev-only artisan command seeds sample photos; no change to
   Nico's upload flow or `DatabaseSeeder`.
3. **Placement:** cover photo + gallery band below.
4. **Treatment:** editorial / varied (non-uniform) layout with an Alpine lightbox.

## Design

### 1. Data & controller

- `GroupController@show` eager-loads the group's media so the page issues one media
  query: add `'media'` to the existing `$group->load(...)` call.
- In the view, resolve once:
  - `$galleryPhotos = $group->getMedia('gallery');`
  - `$coverPhoto = $galleryPhotos->first();` — the cover.
  - `$galleryRest = $galleryPhotos->slice(1);` — everything after the cover, shown
    in the gallery band. (Excluding the cover prevents a duplicate photo.)

### 2. Cover (section 2)

- When `$coverPhoto` exists, the `.chapter-photo` figure renders it
  (`$coverPhoto->getUrl()`, with a sensible large conversion if one fits; full image
  is fine for a full-bleed hero) with `alt` from the media name.
- When there are **no** gallery photos, the existing hardcoded fallback art stays
  exactly as today. No empty state, no placeholder churn.

### 3. Gallery band "In beeld" (new, section 3b)

- A new white `chapter-body` `<section class="chapter-gallery">` inserted between the
  agenda section (`</section>` ~line 117) and the local-extras tail (~line 119).
- Heading: `<h2>In beeld</h2>` (raw heading per frontend rules — never `flux:heading`).
  Voice check: warm, concrete, local — passes.
- **Renders only when `$galleryRest` has ≥1 item** (i.e. 2+ total gallery photos).
  With 0–1 gallery photos the band is absent entirely.
- **Editorial layout:** a 12-column CSS grid with an index-driven span pattern so
  tiles are deliberately non-uniform (generous gaps, varied sizes/aspect ratios — not
  a boxy uniform grid). Concrete pattern: a repeating rhythm keyed off the loop index
  (`$loop->index`) — e.g. roughly every 4th tile spans 2 columns / taller — defined in
  CSS via `:nth-child` or an index-derived modifier class. Tiles use the `card`
  (400×300) conversion; `object-fit: cover`.
- Each tile is a `<button type="button">` that opens the lightbox; decorative chrome
  is `aria-hidden`, but the `<img>` carries a real `alt`.

### 4. Lightbox (Alpine, self-contained)

- A single Alpine component scoped to the gallery section holds `open` (bool) and
  `current` (index into `$galleryRest`).
- Tile click sets `current` and `open = true`. Overlay shows the **full-size** image
  (`$media->getUrl()`), a caption from the media name, and prev/next controls.
- Dismiss: Esc, click on the backdrop, and a close button. Arrow keys (←/→) navigate.
- Accessibility: overlay is `role="dialog"` `aria-modal="true"`, focus moves into it
  on open and returns to the triggering tile on close, body scroll locked while open.
- Respects `prefers-reduced-motion` for any open/close transition (per `effects.css`
  conventions). Kept modest — no external lightbox library.

### 5. Styling

- All new CSS in `resources/css/pages/chapters.css`, inside `@layer components {}`,
  in a new numbered block (e.g. "3b · GROUP GALLERY") placed near the `.chapter-photo`
  rules. Classes: `.chapter-gallery`, `.chapter-gallery__grid`,
  `.chapter-gallery__tile`, `.chapter-gallery__img`, `.chapter-gallery__lightbox`,
  `.chapter-gallery__lightbox-*` as needed.
- Tokens only — no raw hex/px (enforced by `CssArchitectureTest`). No new `app.css`
  import (chapters.css is already imported). Composition utilities (grid/gap/aspect)
  may live in the Blade per the styling-layers rule; appearance stays in the partial.

### 6. Dev-only seeding

- New artisan command, e.g. `app/Console/Commands/SeedGroupGalleryCommand.php`,
  signature `dev:seed-group-gallery`.
- Guards: aborts unless `app()->environment('local')` (non-production only).
- Idempotent: clears the target groups' `gallery` collection first, then attaches a
  handful of sample images, so re-running is safe.
- Targets a small set of groups **including group id 3** (the test page).
- Source images: the already-scraped per-chapter photos committed to the repo
  (commit `3f603a0`); falls back to any available `public/img/photography/*` asset.
  The command copies image files into the `gallery` collection via
  `$group->addMedia(...)->preservingOriginal()->toMediaCollection('gallery')`.
- Not wired into `DatabaseSeeder`; does not touch Nico's seed flow.

### 7. Tests

`tests/Feature/ChapterGalleryTest.php` (Pest):

- Group with ≥2 gallery photos → page renders the `In beeld` band and the correct
  number of tiles (total − 1 for the cover); cover figure shows the first photo.
- Group with exactly 1 gallery photo → cover swaps to it, **no** gallery band.
- Group with 0 gallery photos → fallback cover stays, no gallery band.
- `dev:seed-group-gallery` attaches media to the target group's `gallery` collection
  and is idempotent (running twice does not duplicate).

Use the `Group` factory + a fake media file (`UploadedFile::fake()->image()` /
medialibrary test helpers). Run with `php artisan test --compact --filter=ChapterGallery`.

## Out of scope

- Changing where uploads attach (still Activities — Nico's call).
- Backfilling existing activity photos onto groups.
- Any reusable cross-page `<x-gallery>` component (YAGNI; revisit if a second page
  needs one).
- Per-group "cover" content field in the CMS.

## Pipeline note

On completion, this advances P-11 on the page registry (Wire/Assets/Back as honestly
applicable) and gets a `log.md` build entry — handled via `/pipeline`.

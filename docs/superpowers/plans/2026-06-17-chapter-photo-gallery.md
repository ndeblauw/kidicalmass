# Chapter Photo Gallery Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Show a chapter's photos on its public detail page (`/nl/chapters/{group}`), sourced from the Group's own `gallery` media collection, with a cover photo and an editorial gallery band + lightbox.

**Architecture:** The `Group` model already implements `HasMedia` with `gallery`/`main` collections and `thumb`/`card` conversions. We read `$group->getMedia('gallery')` directly — the first photo becomes the section-2 cover (replacing the hardcoded fallback only when photos exist), the rest render in a new white "In beeld" band between the agenda and the local-extras tail. A dev-only artisan command seeds sample photos so the page is visible locally. No change to Nico's upload flow (uploads still target Activities).

**Tech Stack:** Laravel 12, PHP 8.4, spatie/laravel-medialibrary ^11.17, Blade, Alpine (bundled via Livewire 4 — inline `x-data`, no extra plugins), Tailwind v4 + CSS partials, Pest 4.

## Global Constraints

- **Headings:** raw `<h1>`–`<h6>` only — never `flux:heading`.
- **Styling layers:** appearance utilities/values live in the CSS partial (`resources/css/pages/chapters.css`), composition utilities (grid/flex/gap/aspect/object) may live in Blade. No raw hex/px in the page view; CSS partial uses tokens (`var(--spacing)`, `var(--radius-card)`, `var(--color-kidical-ink)`, `var(--text-3xl)`) and `rem`, consistent with the rest of `chapters.css`.
- **Copy / voice:** NL only; follow `docs/tone-of-voice.md`. **No em-dashes in site copy.** Gallery heading is exactly `In beeld`.
- **Alpine convention:** inline `x-data` object literals; pass server data with `@js(...)`. No Alpine focus/trap plugin is installed — do not use `x-trap`.
- **Media:** disk is `media` (public). Conversions available: `thumb` (150×150), `card` (400×300). Tiles use `card`; cover + lightbox use the original (`getUrl()`).
- **Dev command guard:** the seeder must refuse to run in production (`app()->isProduction()`); it is never wired into `DatabaseSeeder`.
- **Tests:** every change is covered; run `php artisan test --compact --filter=...`. Run `vendor/bin/pint --dirty --format agent` before each commit.

---

### Task 1: Dev-only gallery seeder command

Lets us attach sample photos to a group's `gallery` so the page is visible locally and so later tasks can be verified by eye.

**Files:**
- Create: `app/Console/Commands/SeedGroupGalleryCommand.php`
- Test: `tests/Feature/SeedGroupGalleryCommandTest.php`

**Interfaces:**
- Produces: artisan command `dev:seed-group-gallery` with options `--group=*` (one or more group ids; defaults to the curated `[3]`) and `--count=` (photos per group, default 6). Attaches images from `public/img/photography` (top level + one sub-dir) to each group's `gallery` collection. Idempotent (clears the collection first). Returns `Command::FAILURE` in production.

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\Group;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('media');
});

it('attaches the requested number of gallery photos to a group', function () {
    $group = Group::factory()->create();

    $this->artisan('dev:seed-group-gallery', ['--group' => [$group->id], '--count' => 3])
        ->assertSuccessful();

    expect($group->fresh()->getMedia('gallery'))->toHaveCount(3);
});

it('is idempotent — re-running does not duplicate photos', function () {
    $group = Group::factory()->create();

    $this->artisan('dev:seed-group-gallery', ['--group' => [$group->id], '--count' => 3])->assertSuccessful();
    $this->artisan('dev:seed-group-gallery', ['--group' => [$group->id], '--count' => 3])->assertSuccessful();

    expect($group->fresh()->getMedia('gallery'))->toHaveCount(3);
});

it('refuses to run in production', function () {
    app()->detectEnvironment(fn () => 'production');
    $group = Group::factory()->create();

    $this->artisan('dev:seed-group-gallery', ['--group' => [$group->id]])->assertFailed();

    expect($group->fresh()->getMedia('gallery'))->toHaveCount(0);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=SeedGroupGalleryCommand`
Expected: FAIL — command `dev:seed-group-gallery` is not defined.

- [ ] **Step 3: Write the command**

```php
<?php

namespace App\Console\Commands;

use App\Models\Group;
use Illuminate\Console\Command;

class SeedGroupGalleryCommand extends Command
{
    protected $signature = 'dev:seed-group-gallery
        {--group=* : Group ids to seed (defaults to a curated set)}
        {--count=6 : Photos to attach per group}';

    protected $description = 'Attach sample photos to groups\' gallery collection (non-production only).';

    /**
     * Groups that get a sample gallery by default. Includes id 3 (the local test page).
     *
     * @var list<int>
     */
    private const DEFAULT_GROUP_IDS = [3];

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('Refusing to seed gallery photos in production.');

            return self::FAILURE;
        }

        $sources = $this->sampleImagePaths();

        if ($sources === []) {
            $this->error('No sample images found under public/img/photography.');

            return self::FAILURE;
        }

        $ids = $this->option('group') ?: self::DEFAULT_GROUP_IDS;
        $count = max(1, (int) $this->option('count'));

        foreach ($ids as $id) {
            $group = Group::find($id);

            if (! $group) {
                $this->warn("Group {$id} not found, skipping.");

                continue;
            }

            $group->clearMediaCollection('gallery');

            for ($i = 0; $i < $count; $i++) {
                $path = $sources[$i % count($sources)];

                $group->addMedia($path)
                    ->preservingOriginal()
                    ->usingName(pathinfo($path, PATHINFO_FILENAME))
                    ->toMediaCollection('gallery');
            }

            $this->info("Seeded {$count} gallery photos onto group {$id} ({$group->name}).");
        }

        return self::SUCCESS;
    }

    /**
     * Sample images: every jpg/png at the top level of img/photography and one sub-dir deep.
     *
     * @return list<string>
     */
    private function sampleImagePaths(): array
    {
        $base = public_path('img/photography');

        return collect(glob("{$base}/*.{jpg,jpeg,png}", GLOB_BRACE) ?: [])
            ->merge(glob("{$base}/*/*.{jpg,jpeg,png}", GLOB_BRACE) ?: [])
            ->values()
            ->all();
    }
}
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --compact --filter=SeedGroupGalleryCommand`
Expected: PASS (3 passing). If a test errors on image conversions, ensure GD is available; the assertions only count `gallery` media rows.

- [ ] **Step 5: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Console/Commands/SeedGroupGalleryCommand.php tests/Feature/SeedGroupGalleryCommandTest.php
git commit -m "feat(chapter-gallery): dev command to seed group gallery photos

Why: make the chapter gallery visible locally without changing Nico's
activity-scoped upload flow.

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 2: Cover swap + "In beeld" gallery band + lightbox markup

Reads the group's gallery, swaps the cover to the first photo, and renders the rest as an editorial band with an inline-Alpine lightbox. This is the feature's core and is fully server-rendered (so testable).

**Files:**
- Modify: `app/Http/Controllers/GroupController.php:93` (eager-load media)
- Modify: `resources/views/groups/show.blade.php` (@php block ~line 17–65; cover figure lines 77–83; insert new band after `</section>` at line 116)
- Test: `tests/Feature/ChapterGalleryTest.php`

**Interfaces:**
- Consumes: `dev:seed-group-gallery` from Task 1 (for local visual verification only; the test attaches its own media).
- Produces: in the view, `$galleryPhotos = $group->getMedia('gallery')`, `$coverPhoto = $galleryPhotos->first()`, `$galleryRest = $galleryPhotos->slice(1)->values()`. CSS hooks for Task 3: `.chapter-gallery`, `.chapter-gallery__grid`, `.chapter-gallery__cell`, `.chapter-gallery__tile`, `.chapter-gallery__img`, `.chapter-gallery__lightbox`, `.chapter-gallery__lb-figure`, `.chapter-gallery__lb-img`, `.chapter-gallery__lb-close`, `.chapter-gallery__lb-nav` (+ `--prev`/`--next`), and root class `is-lightbox-open`.

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\Group;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('media');
});

function attachGalleryPhotos(Group $group, int $n): void
{
    for ($i = 0; $i < $n; $i++) {
        $group->addMedia(UploadedFile::fake()->image("photo-{$i}.jpg", 800, 600)->getRealPath())
            ->preservingOriginal()
            ->usingName("photo-{$i}")
            ->toMediaCollection('gallery');
    }
}

function showChapter(Group $group)
{
    return test()->get(route('groups.show', ['locale' => 'nl', 'group' => $group->id]));
}

it('renders the gallery band with one tile per non-cover photo', function () {
    $group = Group::factory()->create();
    attachGalleryPhotos($group, 3); // 1 cover + 2 in the band

    $response = showChapter($group)->assertOk();

    $response->assertSee('chapter-gallery__grid', false);
    expect(substr_count($response->getContent(), 'chapter-gallery__tile'))->toBe(2);
});

it('uses the first gallery photo as the cover instead of the fallback', function () {
    $group = Group::factory()->create();
    attachGalleryPhotos($group, 2);

    showChapter($group)
        ->assertOk()
        ->assertDontSee('ride-cinquantenaire-crowd.jpg'); // fallback art is gone
});

it('keeps the fallback cover and omits the band when there are no photos', function () {
    $group = Group::factory()->create();

    showChapter($group)
        ->assertOk()
        ->assertSee('ride-cinquantenaire-crowd.jpg')   // fallback stays
        ->assertDontSee('chapter-gallery__grid', false); // no band
});

it('omits the band when there is only a cover photo', function () {
    $group = Group::factory()->create();
    attachGalleryPhotos($group, 1);

    showChapter($group)
        ->assertOk()
        ->assertDontSee('chapter-gallery__grid', false)
        ->assertDontSee('ride-cinquantenaire-crowd.jpg'); // cover still swapped
});
```

> If `route('groups.show', ...)` rejects the `locale` key, mirror the URL construction used in `tests/Feature/PublicPagesRenderTest.php` (same locale-prefixed route group).

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=ChapterGallery`
Expected: FAIL — `chapter-gallery__grid` not found / fallback still present.

- [ ] **Step 3: Eager-load media in the controller**

In `app/Http/Controllers/GroupController.php`, change line 93 from:

```php
        $group->load(['parent', 'children', 'users'])->loadCount(['articles', 'activities']);
```

to:

```php
        $group->load(['parent', 'children', 'users', 'media'])->loadCount(['articles', 'activities']);
```

- [ ] **Step 4: Resolve the photos in the view @php block**

In `resources/views/groups/show.blade.php`, inside the `@php` block (after `$allActivitiesUrl = ...` at line 64, before `@endphp` at line 65), add:

```php
        // Chapter gallery — read the group's own `gallery` collection. The first photo
        // is the cover (section 2); the rest fill the "In beeld" band. Uploads still
        // attach to activities (Nico), so this is empty until photos land on the group;
        // `php artisan dev:seed-group-gallery` populates it locally.
        $galleryPhotos = $group->getMedia('gallery');
        $coverPhoto = $galleryPhotos->first();
        $galleryRest = $galleryPhotos->slice(1)->values();
```

- [ ] **Step 5: Swap the cover figure (section 2)**

Replace the figure at lines 77–83:

```blade
    <figure class="chapter-photo">
        <img
            src="{{ asset('img/photography/ride-cinquantenaire-crowd.jpg') }}"
            alt="Een grote groep gezinnen fietst samen door de straat tijdens een Kidical Mass in {{ $gemeente }}"
            class="chapter-photo__img"
        >
    </figure>
```

with:

```blade
    <figure class="chapter-photo">
        @if ($coverPhoto)
            <img
                src="{{ $coverPhoto->getUrl() }}"
                alt="Foto van een Kidical Mass in {{ $gemeente }}"
                class="chapter-photo__img"
            >
        @else
            <img
                src="{{ asset('img/photography/ride-cinquantenaire-crowd.jpg') }}"
                alt="Een grote groep gezinnen fietst samen door de straat tijdens een Kidical Mass in {{ $gemeente }}"
                class="chapter-photo__img"
            >
        @endif
    </figure>
```

- [ ] **Step 6: Insert the "In beeld" band + lightbox**

In `resources/views/groups/show.blade.php`, immediately after the agenda `</section>` (line 116) and before the `{{-- 4 · LOCAL EXTRAS ... --}}` comment (line 118), insert:

```blade
    {{-- 3b · IN BEELD — the group's gallery, editorial varied tiles + an inline lightbox.
         Renders only when there is more than the cover photo. Structure only; appearance
         in resources/css/pages/chapters.css. --}}
    @if ($galleryRest->isNotEmpty())
        <section
            class="chapter-body chapter-gallery"
            x-data="{
                photos: @js($galleryRest->map(fn ($m) => ['url' => $m->getUrl(), 'name' => $m->name])->values()),
                isOpen: false,
                index: 0,
                open(i) { this.index = i; this.isOpen = true; this.$nextTick(() => this.$refs.closeBtn?.focus()); },
                close() { this.isOpen = false; },
                next() { this.index = (this.index + 1) % this.photos.length; },
                prev() { this.index = (this.index - 1 + this.photos.length) % this.photos.length; },
            }"
            x-effect="document.documentElement.classList.toggle('is-lightbox-open', isOpen)"
            @keydown.escape.window="close()"
            @keydown.arrow-right.window="isOpen && next()"
            @keydown.arrow-left.window="isOpen && prev()"
        >
            <h2 class="chapter-section__title">In beeld</h2>

            <ul class="chapter-gallery__grid">
                @foreach ($galleryRest as $media)
                    <li class="chapter-gallery__cell">
                        <button
                            type="button"
                            class="chapter-gallery__tile"
                            @click="open({{ $loop->index }})"
                            aria-label="Bekijk foto {{ $loop->iteration }} groter"
                        >
                            <img
                                src="{{ $media->getUrl('card') }}"
                                alt="Foto uit {{ $gemeente }}"
                                loading="lazy"
                                class="chapter-gallery__img"
                            >
                        </button>
                    </li>
                @endforeach
            </ul>

            <div
                class="chapter-gallery__lightbox"
                x-show="isOpen"
                x-cloak
                @click.self="close()"
                role="dialog"
                aria-modal="true"
                aria-label="Foto groter bekeken"
            >
                <button type="button" class="chapter-gallery__lb-close" x-ref="closeBtn" @click="close()" aria-label="Sluiten">&times;</button>
                <button type="button" class="chapter-gallery__lb-nav chapter-gallery__lb-nav--prev" @click="prev()" aria-label="Vorige foto">&lsaquo;</button>
                <figure class="chapter-gallery__lb-figure">
                    <img :src="photos[index]?.url" :alt="photos[index]?.name" class="chapter-gallery__lb-img">
                </figure>
                <button type="button" class="chapter-gallery__lb-nav chapter-gallery__lb-nav--next" @click="next()" aria-label="Volgende foto">&rsaquo;</button>
            </div>
        </section>
    @endif
```

- [ ] **Step 7: Run tests to verify they pass**

Run: `php artisan test --compact --filter=ChapterGallery`
Expected: PASS (4 passing).

- [ ] **Step 8: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/GroupController.php resources/views/groups/show.blade.php tests/Feature/ChapterGalleryTest.php
git commit -m "feat(chapter-gallery): cover swap + In beeld gallery band with lightbox

- Read group's gallery collection; first photo becomes the cover
- Editorial gallery band renders the rest; inline-Alpine lightbox
- Eager-load media to keep the page to one media query

Why: surface chapter photos on the public detail page.

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

### Task 3: Editorial gallery + lightbox styles

Gives the band its non-uniform, breathing layout and styles the lightbox. CSS is verified structurally by the architecture test and visually by a screenshot pass.

**Files:**
- Modify: `resources/css/pages/chapters.css` (add a "3b · GROUP GALLERY" block after the `.chapter-photo__img` rules ~line 56)

**Interfaces:**
- Consumes: the class hooks emitted by Task 2 (`.chapter-gallery*`, root `is-lightbox-open`).

- [ ] **Step 1: Add the gallery + lightbox CSS**

In `resources/css/pages/chapters.css`, inside the existing `@layer components { ... }`, after the `.chapter-photo__img { ... }` rule (line 56), add:

```css
    /* 3b · GROUP GALLERY ("In beeld") — deliberately non-uniform tiles so the wall
       breathes instead of reading as a boxy grid. A 12-col grid on wide viewports
       where a recurring tile spans wider/taller. */
    .chapter-gallery__grid {
        list-style: none;
        margin: calc(var(--spacing) * 8) 0 0;
        padding: 0;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: calc(var(--spacing) * 3);
    }
    @media (min-width: 48rem) {
        .chapter-gallery__grid {
            grid-template-columns: repeat(12, 1fr);
            grid-auto-flow: dense;
            gap: calc(var(--spacing) * 4);
        }
        .chapter-gallery__cell { grid-column: span 4; }
        .chapter-gallery__cell:nth-child(5n + 1) { grid-column: span 6; }
        .chapter-gallery__cell:nth-child(7n + 3) { grid-row: span 2; }
    }
    .chapter-gallery__tile {
        display: block;
        width: 100%;
        height: 100%;
        padding: 0;
        border: 0;
        background: none;
        cursor: pointer;
        overflow: hidden;
        border-radius: var(--radius-card);
        aspect-ratio: 4 / 3;
    }
    .chapter-gallery__cell:nth-child(7n + 3) .chapter-gallery__tile { aspect-ratio: auto; }
    .chapter-gallery__img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .chapter-gallery__tile:hover .chapter-gallery__img,
    .chapter-gallery__tile:focus-visible .chapter-gallery__img { transform: scale(1.04); }

    /* Lightbox overlay */
    .chapter-gallery__lightbox {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: calc(var(--spacing) * 2);
        padding: calc(var(--spacing) * 4);
        background-color: color-mix(in srgb, var(--color-kidical-ink) 92%, transparent);
    }
    .chapter-gallery__lb-figure {
        margin: 0;
        max-width: min(90vw, 60rem);
        max-height: 86vh;
    }
    .chapter-gallery__lb-img {
        width: 100%;
        height: 100%;
        max-height: 86vh;
        object-fit: contain;
        border-radius: var(--radius-card);
        display: block;
    }
    .chapter-gallery__lb-close,
    .chapter-gallery__lb-nav {
        background: none;
        border: 0;
        color: white;
        cursor: pointer;
        font-size: var(--text-3xl);
        line-height: 1;
        padding: calc(var(--spacing) * 2);
    }
    .chapter-gallery__lb-close {
        position: absolute;
        top: calc(var(--spacing) * 3);
        right: calc(var(--spacing) * 4);
    }
    @media (prefers-reduced-motion: reduce) {
        .chapter-gallery__img { transition: none; }
    }
```

And at the very end of the file, after the closing `}` of `@layer components`, add the root scroll-lock rule (it targets `html`, so it lives outside `.chapter-*` but stays in this page partial):

```css
@layer components {
    /* Body scroll lock while the gallery lightbox is open. */
    html.is-lightbox-open { overflow: hidden; }
}
```

- [ ] **Step 2: Verify the CSS architecture test still passes**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS (chapters.css is already imported; no new partial to register; raw-value rule applies to components only).

- [ ] **Step 3: Build and screenshot-verify (one pass)**

```bash
npm run build
php artisan dev:seed-group-gallery --group=3 --count=7
```

Then take a single Playwright screenshot pass (HTTPS self-signed, `.cjs`, `ignoreHTTPSErrors: true`) of `https://kidicalmass.test/nl/chapters/3` at desktop (1440) and mobile (390) widths, plus one with the lightbox open (click a `.chapter-gallery__tile`). Confirm: cover shows a real photo, the band reads as varied (not a uniform grid), tiles open the lightbox, Esc / backdrop / arrows work. Adjust the `nth-child` rhythm or `aspect-ratio` only if it reads boxy.

- [ ] **Step 4: Run the full affected suite**

Run: `php artisan test --compact --filter='ChapterGallery|SeedGroupGallery|CssArchitecture'`
Expected: PASS.

- [ ] **Step 5: Format and commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/css/pages/chapters.css
git commit -m "style(chapter-gallery): editorial varied tiles + lightbox styling

Why: give the In beeld band breathing, non-uniform rhythm and a quiet
full-size overlay.

Co-Authored-By: Claude Opus 4.8 (1M context) <noreply@anthropic.com>"
```

---

## Post-implementation

- **Pipeline:** run `/pipeline` for P-11 — bump `Wire`/`Assets`/`Back` honestly (Wire 🟢 only after Frederik's own critique pass), update Top gaps + Roll-up, append a `log.md` build entry.
- **Wrap:** at `/wrap`, squash this worktree's commits into one curated commit per the global git policy, guarding against Nico's commits, before any merge to `main`.

## Self-review notes

- **Spec coverage:** §1 data/controller → Task 2 (steps 3–4); §2 cover → Task 2 (step 5); §3 band → Task 2 (step 6) + Task 3 (step 1); §4 lightbox → Task 2 (step 6) markup + Task 3 styling, with the honest limitation that no focus-trap plugin exists (focus moves into the dialog on open via `$refs.closeBtn`, scroll is locked, but focus is not strictly trapped); §5 styling → Task 3; §6 seeder → Task 1; §7 tests → Tasks 1 & 2.
- **Production guard:** spec said "local only"; implemented as "not production" (`app()->isProduction()`) so it is testable under the `testing` env while still blocking production — equivalent intent.
- **No placeholders:** every code/step is concrete.

# About Admin Pass Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the about section admin-editable (team duo, parent quotes, contact details) and fix the 7.9 MB manifest PDF, per `docs/superpowers/specs/2026-07-04-about-admin-pass-design.md`.

**Architecture:** Two new small models (`TeamMember`, `Quote`) following the existing Partner/YearStat BlueAdmin pattern (model + config class + empty controller + `Route::resource` + menu entry). Public pages read them via route-closure data passing with lang-string fallbacks. A throwaway `/design-choices` prototype (Task 1) lets Frederik pick the person-card and quote treatments before the visual work in Tasks 6/8 lands.

**Tech Stack:** Laravel 13, BlueAdmin (`ndeblauw/blue-admin`), Spatie Medialibrary, Pest 4, Tailwind v4 CSS partials.

## Global Constraints

- Run `vendor/bin/pint --dirty --format agent` after every PHP change, before committing.
- Stage by explicit path; never `git add -A` (shared checkout with Nico).
- No raw hex/px in `.blade.php` components — token-backed utilities/CSS vars only (enforced by `CssArchitectureTest`).
- New CSS goes in `resources/css/components/*.css` partials, never `app.css`.
- Public-site copy follows `docs/tone-of-voice.md`; **no em-dashes** anywhere in copy.
- Tests assert behaviour and `__('key')` lang keys, never Tailwind utilities or literal long copy (see `docs/testing-conventions.md`).
- Repo is public: no internal/sensitive content in commits.
- **Human gate:** Task 1 ends with Frederik picking variants at `/design-choices`. Tasks 2, 3, 4, 5, 7 do not depend on the picks and can proceed while waiting. Tasks 6 and 8 need the picks for their visual steps.

---

### Task 1: `/design-choices` prototype (person-card + quote variants)

**Files:**
- Create: `resources/views/design-choices.blade.php`
- Modify: `routes/web.php` (non-prod block at the bottom, next to `/styleguide`)

**Interfaces:**
- Produces: Frederik's picks — `person-card: A|B` and `quote: A|B` — which Task 6 and Task 8 consume. The variant CSS written here is the source the winning styles get promoted from.

- [ ] **Step 1: Add the non-prod route**

In `routes/web.php`, inside the existing `if (! app()->isProduction()) {` block (after the `/styleguide` route):

```php
    // Internal design-choices prototype — person-card + quote variants for the about admin pass.
    Route::view('/design-choices', 'design-choices')->name('design.choices');
```

- [ ] **Step 2: Create the prototype page**

Create `resources/views/design-choices.blade.php`. Two decision blocks, real tokens, prototype-only CSS inline in a `<style>` block (throwaway file, not subject to the partials rule). Radio + localStorage scaffolding conventions come from the previous prototype — restore it for reference with:

```bash
git show 3fc0e8e:resources/views/design-choices.blade.php > /tmp/old-design-choices.blade.php
```

Reuse its page shell (layout component, decision-section structure, radio persistence, copyable summary). Replace its nine decisions with these two:

**Decision 1 — person card** (render inside a copy of the organisation duo band: `<x-section-heading>Het coördinatieduo</x-section-heading>` + the `about-duo` list context). Show four cards, labelled:

- **A1. Portrait stack, photo:** current `.person-card` markup + a photo on top (use an existing seeded image, e.g. `asset('img/photography/ride-cinquantenaire-crowd.jpg')`, square-cropped via the existing `person-card__photo` styles) + name, role, and a 2-sentence placeholder bio paragraph.
- **A2. Portrait stack, no photo:** same, photo replaced by the initial-letter disc.
- **B1. Horizontal row, photo:** photo left, text (name/role/bio) right, the two cards stacked vertically instead of side by side.
- **B2. Horizontal row, no photo:** disc left, text right.

Placeholder bio text (NL, tone-of-voice register): `Leticia fietst elke week met haar twee kinderen door Brussel. Ze coördineert de vormingen en is het eerste aanspreekpunt voor nieuwe groepen.`

Prototype CSS for the new pieces (in the page's `<style>` block; the winner moves to `person-card.css` in Task 6):

```css
.person-card__disc {
    display: grid;
    place-items: center;
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    background: var(--color-kidical-red);
    color: var(--color-white);
    font-family: var(--font-heading);
    font-weight: 800;
    font-size: var(--text-2xl);
    margin-bottom: 0.5rem;
}
.person-card__bio {
    margin: 0.5rem 0 0;
    font-size: var(--text-sm);
    line-height: 1.5;
    color: color-mix(in oklab, var(--color-kidical-ink), transparent 25%);
}
/* Variant B: row layout */
.person-card--row {
    flex-direction: row;
    align-items: flex-start;
    gap: 1.25rem;
    padding: 1.25rem 1.5rem;
}
.person-card--row .person-card__photo,
.person-card--row .person-card__disc {
    flex-shrink: 0;
    margin-bottom: 0;
}
```

For variant B the card's inner text needs a wrapper `<div class="person-card__text">` holding name/role/bio (plain flex column, no extra CSS needed beyond `display:flex; flex-direction:column; gap:0.1rem`).

**Decision 2 — quote** (render inside a fake story column, `max-w-prose`, with a paragraph of body text above and below so the squeeze is visible). Two variants using the real `x-pull-quote` component and `about.mission_quote` copy:

- **A. Baseline:** `<x-pull-quote :attribution="__('about.mission_quote_attribution')">{{ __('about.mission_quote') }}</x-pull-quote>` (current `--large`).
- **B. Quieter column treatment:** same component with `variant="column"`, styled in the prototype `<style>` block:

```css
.pull-quote--column {
    margin-block: calc(var(--spacing) * 10);
    padding-inline-start: 1.5rem;
    border-inline-start: 4px solid var(--color-kidical-red);
}
.pull-quote--column blockquote { margin: 0; }
.pull-quote--column blockquote p {
    font-family: var(--font-heading);
    font-weight: 700;
    font-size: var(--text-xl);
    line-height: 1.35;
    color: var(--color-kidical-ink);
    margin: 0;
}
.pull-quote--column figcaption {
    margin-top: 0.75rem;
    font-size: var(--text-sm);
    font-weight: 700;
    color: color-mix(in oklab, var(--color-kidical-ink), transparent 40%);
}
```

- [ ] **Step 3: Verify it renders**

Run: `php artisan test --compact --filter=CssArchitectureTest` (must stay green — prototype is not in `components/`), then load the page once with a Playwright screenshot (`scripts/screenshot.cjs` if present, else the standard `.cjs` pattern) against `https://kidicalmass.test/design-choices`. Confirm all 4 person cards + 2 quote variants render with tokens applied.

- [ ] **Step 4: Commit**

```bash
git add resources/views/design-choices.blade.php routes/web.php
git commit -m "chore(design): /design-choices prototype for the about admin pass

- person-card portrait-stack vs horizontal-row, each with photo and
  initial-disc fallback states, plus bio line
- pull-quote baseline --large vs quieter --column treatment

Why: Frederik picks from rendered variants; throwaway page, deleted
after the pass lands.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

- [ ] **Step 5: HUMAN GATE — ask Frederik to open `/design-choices` and pick person-card A/B and quote A/B.** Record the picks; Tasks 6 and 8 reference them. Continue with Tasks 2–5 and 7 while waiting.

---

### Task 2: Contact single source

**Files:**
- Modify: `config/kidicalmass.php`
- Modify: `resources/views/about/press.blade.php` (mailto card, ~line 30)
- Modify: `resources/views/about/partners.blade.php:94-97` (fallback block)
- Modify: `lang/nl/about.php:109` (`press_empty_body`)
- Test: `tests/Feature/PublicStructureTest.php` (extend existing press + partners tests)

**Interfaces:**
- Produces: `config('kidicalmass.contact.email')`, `config('kidicalmass.contact.phone')` (display format), `config('kidicalmass.contact.phone_e164')` (tel: href). Later tasks and future pages read contact data only from here.

- [ ] **Step 1: Extend the existing press/partners tests with failing assertions**

Locate the press and partners tests in `tests/Feature/PublicStructureTest.php` (grep for `about/press` and `about/partners`). Add to the press test:

```php
        ->assertSee(config('kidicalmass.contact.email'))
```

Add to the partners test:

```php
        ->assertSee(config('kidicalmass.contact.email'))
        ->assertSee(config('kidicalmass.contact.phone'))
```

- [ ] **Step 2: Run to verify they fail**

Run: `php artisan test --compact --filter=PublicStructureTest`
Expected: FAIL — `config('kidicalmass.contact.email')` is null, `assertSee(null)` errors (or the literal is missing). Either failure mode confirms the config key doesn't exist yet.

- [ ] **Step 3: Add the config block**

In `config/kidicalmass.php`, after the `social` block:

```php
    /*
    |--------------------------------------------------------------------------
    | Contact details
    |--------------------------------------------------------------------------
    | One source for the public contact email + phone (press card, partner
    | enquiry fallback, press empty state). phone is the display format,
    | phone_e164 feeds tel: hrefs.
    */

    'contact' => [
        'email' => 'bike@kidicalmass.be',
        'phone' => '0495 81 27 95',
        'phone_e164' => '+32495812795',
    ],
```

- [ ] **Step 4: Replace the three hardcodes**

`resources/views/about/press.blade.php` — the contact card link becomes:

```blade
                    <a href="mailto:{{ config('kidicalmass.contact.email') }}" class="info-card__link">{{ config('kidicalmass.contact.email') }}</a>
```

Same file, the empty state call gains the placeholder parameter:

```blade
                        {{ __('about.press_empty_body', ['email' => config('kidicalmass.contact.email')]) }}
```

`resources/views/about/partners.blade.php:95-97` — the fallback block becomes:

```blade
                <p class="partner-enquiry__fallback">Liever rechtstreeks?<br>
                    <a href="mailto:{{ config('kidicalmass.contact.email') }}" class="more-link">{{ config('kidicalmass.contact.email') }}</a><br>
                    <a href="tel:{{ config('kidicalmass.contact.phone_e164') }}" class="more-link">{{ config('kidicalmass.contact.phone') }}</a>
                </p>
```

`lang/nl/about.php:109` — replace the literal email with `:email`:

```php
    'press_empty_body' => 'Kidical Mass kwam de afgelopen jaren in heel wat kranten, radio en tv. We brengen die berichtgeving binnenkort samen op één plek. Schreef je over Kidical Mass en wil je dat je artikel hier verschijnt? Laat het ons weten via :email.',
```

- [ ] **Step 5: Run tests, verify pass**

Run: `php artisan test --compact --filter=PublicStructureTest`
Expected: PASS (all tests in the file).

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add config/kidicalmass.php resources/views/about/press.blade.php resources/views/about/partners.blade.php lang/nl/about.php tests/Feature/PublicStructureTest.php
git commit -m "feat(about): single-source contact details in config

- kidicalmass.contact email/phone/phone_e164, same shape as social
- press card, press empty state (:email placeholder) and partner
  enquiry fallback read from config

Why: handoff item 1.6 — three scattered hardcodes for one address.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 3: Manifest PDF — compress + size in link

**Files:**
- Modify: `public/downloads/kidical-mass-manifest.pdf` (binary replace)
- Modify: `lang/nl/about.php:55` (`vision_manifest_link`)

**Interfaces:**
- Consumes: nothing. Produces: nothing code-facing (static asset + copy only).

- [ ] **Step 1: Compress with ghostscript** (available at `/opt/homebrew/bin/gs`)

```bash
gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.5 -dPDFSETTINGS=/ebook \
   -dNOPAUSE -dQUIET -dBATCH \
   -sOutputFile="$SCRATCHPAD/manifest-compressed.pdf" \
   public/downloads/kidical-mass-manifest.pdf
ls -lh "$SCRATCHPAD/manifest-compressed.pdf"
```

(`$SCRATCHPAD` = the session scratchpad directory.) Expected: well under 2 MB. If still over 2 MB, retry with `-dPDFSETTINGS=/screen` and visually re-check quality is acceptable for a text manifesto.

- [ ] **Step 2: Verify integrity, then replace**

Compare page counts (must match):

```bash
mdls -name kMDItemNumberOfPages public/downloads/kidical-mass-manifest.pdf
mdls -name kMDItemNumberOfPages "$SCRATCHPAD/manifest-compressed.pdf"
```

Open the compressed file (`open "$SCRATCHPAD/manifest-compressed.pdf"`) and spot-check a text page and an image page. Then:

```bash
cp "$SCRATCHPAD/manifest-compressed.pdf" public/downloads/kidical-mass-manifest.pdf
```

- [ ] **Step 3: Put the size in the link text**

In `lang/nl/about.php`, update `vision_manifest_link` with the real size from Step 1, Dutch decimal comma, rounded to one decimal (example for a 1.6 MB result):

```php
    'vision_manifest_link' => 'Download het manifest (PDF, 1,6 MB)',
```

- [ ] **Step 4: Verify page still green**

Run: `php artisan test --compact --filter=PublicStructureTest`
Expected: PASS (vision test asserts lang keys, not literals).

- [ ] **Step 5: Commit**

```bash
git add public/downloads/kidical-mass-manifest.pdf lang/nl/about.php
git commit -m "fix(about): compress manifest PDF and state its size in the link

- 7.9 MB -> <2 MB via ghostscript /ebook, page count verified
- link copy names the size so visitors know what they tap

Why: handoff item 1.5 (reduced scope: static asset stays, no CMS).

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 4: TeamMember model, migration, factory, seeder

**Files:**
- Create: `database/migrations/<timestamp>_create_team_members_table.php`
- Create: `app/Models/TeamMember.php`
- Create: `database/factories/TeamMemberFactory.php`
- Modify: `database/seeders/DatabaseSeeder.php`

**Interfaces:**
- Produces: `App\Models\TeamMember` — columns `name`, `role`, `bio_nl`, `bio_fr`, `sort`, `visible`; Medialibrary collection `photo` (singleFile) with conversion `thumb`; `TeamMember::factory()`. Task 5 and Task 6 consume these exact names.

- [ ] **Step 1: Scaffold**

```bash
php artisan make:model TeamMember -mf --no-interaction
```

- [ ] **Step 2: Write the migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            // Bios are pending client content; nullable so the duo can exist
            // as name + role until the texts arrive.
            $table->text('bio_nl')->nullable();
            $table->text('bio_fr')->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};
```

- [ ] **Step 3: Write the model** (mirrors `app/Models/Partner.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * A member of the national coordination team, shown on "Hoe we werken".
 * Curated in the admin; photo and bios arrive from the duo themselves.
 */
#[Unguarded]
class TeamMember extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected function casts(): array
    {
        return [
            'sort' => 'integer',
            'visible' => 'boolean',
        ];
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(300)
            ->height(300)
            ->sharpen(10);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('photo')
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->registerMediaConversions($media);
            });
    }
}
```

- [ ] **Step 4: Write the factory**

```php
<?php

namespace Database\Factories;

use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamMember>
 */
class TeamMemberFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->firstName(),
            'role' => 'Coördinatie',
            'bio_nl' => fake()->sentences(2, true),
            'bio_fr' => null,
            'sort' => 0,
            'visible' => true,
        ];
    }
}
```

- [ ] **Step 5: Seed the duo**

In `database/seeders/DatabaseSeeder.php`: add `use App\Models\TeamMember;` to the imports, add `$this->seedTeamMembers();` in `run()` directly after `$this->seedYearStats();`, and add the method (next to `seedYearStats()`):

```php
    /**
     * The national coordination duo shown on "Hoe we werken". Bios and photos
     * are pending client content, so name + role is deliberately all there is.
     */
    private function seedTeamMembers(): void
    {
        $this->task('Seeding team members', function () {
            TeamMember::updateOrCreate(['name' => 'Leticia'], ['role' => 'Coördinatie', 'sort' => 1]);
            TeamMember::updateOrCreate(['name' => 'Cecilia'], ['role' => 'Coördinatie', 'sort' => 2]);
        });
    }
```

- [ ] **Step 6: Migrate and verify**

```bash
php artisan migrate --no-interaction
php artisan tinker --execute 'App\Models\TeamMember::updateOrCreate(["name" => "Leticia"], ["role" => "Coördinatie", "sort" => 1]); echo App\Models\TeamMember::count();'
```

Expected: migration runs; count prints `1`. (Full re-seed not needed; local DB is shared with Nico's data.)

- [ ] **Step 7: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add database/migrations/*_create_team_members_table.php app/Models/TeamMember.php database/factories/TeamMemberFactory.php database/seeders/DatabaseSeeder.php
git commit -m "feat(about): TeamMember model for the coordination duo

- name/role/bio_nl+fr/sort/visible, photo via Medialibrary (singleFile,
  300px thumb), factory + seeded duo rows

Why: handoff item 1.1 — duo content becomes admin-editable.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 5: TeamMember BlueAdmin resource

**Files:**
- Create: `app/BlueAdmin/TeamMember.php`
- Create: `app/Http/Controllers/Admin/TeamMemberController.php`
- Modify: `routes/web.php` (admin group, next to `Route::resource('partners', ...)`)
- Modify: `config/blue-admin.php` (menu)

**Interfaces:**
- Consumes: `App\Models\TeamMember` (Task 4).
- Produces: admin CRUD at `/admin/teammembers`.

- [ ] **Step 1: Config class** — `app/BlueAdmin/TeamMember.php` (mirrors `app/BlueAdmin/Partner.php`):

```php
<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class TeamMember extends BlueAdminModel
{
    public $CLASS = \App\Models\TeamMember::class;

    public $name_to_use = 'Teamleden';

    public $title_field = 'name';

    public $indexTableColumns = ['name', 'role', 'visible'];

    public $attributesToShow = ['name', 'role', 'bio_nl', 'bio_fr', 'sort', 'visible'];

    public $filepond = ['photo'];

    public $index_load = ['media'];

    public $show_load = ['media'];
}
```

- [ ] **Step 2: Empty controller** — `app/Http/Controllers/Admin/TeamMemberController.php` (mirrors `YearStatController`):

```php
<?php

namespace App\Http\Controllers\Admin;

use Ndeblauw\BlueAdmin\Http\Controllers\AdminController;

class TeamMemberController extends AdminController
{
    //
}
```

- [ ] **Step 3: Route + menu**

`routes/web.php`: add `use App\Http\Controllers\Admin\TeamMemberController;` to the imports, and inside the `Route::middleware(['admin'])->prefix('admin')` group, after `Route::resource('partners', PartnerController::class);`:

```php
    Route::resource('teammembers', TeamMemberController::class);
```

`config/blue-admin.php`: in the `menu` array's `General` section, after the Partners entry:

```php
        [
            'title' => 'Teamleden',
            'color' => 'violet',
            'link' => 'admin/teammembers',
            'icon' => 'fa-people-group',
        ],
```

- [ ] **Step 4: Verify in the admin**

Run: `php artisan route:list --path=admin/teammembers` — expect the 7 resource routes. Then log in to `/admin` locally (Playwright script or ask Frederik) and confirm: Teamleden appears in the menu, the index lists Leticia + Cecilia, and editing a row (e.g. adding a bio) saves. This is the "verified live" bar the pipeline's Back column requires.

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/BlueAdmin/TeamMember.php app/Http/Controllers/Admin/TeamMemberController.php routes/web.php config/blue-admin.php
git commit -m "feat(admin): Teamleden BlueAdmin resource

Why: handoff item 1.1 — duo editable at /admin/teammembers, mirrors
the Partner resource incl. filepond photo upload.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 6: Organisation page renders the duo from the DB *(needs the Task 1 person-card pick)*

**Files:**
- Modify: `routes/web.php:90` (organisation route)
- Modify: `resources/views/about/organisation.blade.php:46-50`
- Modify: `resources/views/components/person-card.blade.php`
- Modify: `resources/css/components/person-card.css`
- Test: `tests/Feature/PublicStructureTest.php`

**Interfaces:**
- Consumes: `App\Models\TeamMember` (Task 4); Frederik's person-card pick (Task 1).
- Produces: `x-person-card` props `name`, `role`, `photo` (url|null), `bio` (string|null) — final component API.

- [ ] **Step 1: Write the failing test** — in `tests/Feature/PublicStructureTest.php`, after the existing `Hoe we werken` test:

```php
it('renders the coordination duo from the database, hiding invisible members', function () {
    \App\Models\TeamMember::factory()->create(['name' => 'Zichtbaar Teamlid', 'bio_nl' => 'Fietst elke week met de kinderen.', 'visible' => true]);
    \App\Models\TeamMember::factory()->create(['name' => 'Verborgen Teamlid', 'visible' => false]);

    get('/nl/about/organisation')
        ->assertOk()
        ->assertSee('Zichtbaar Teamlid')
        ->assertSee('Fietst elke week met de kinderen.')
        ->assertDontSee('Verborgen Teamlid');
});
```

- [ ] **Step 2: Run to verify it fails**

Run: `php artisan test --compact --filter="coordination duo"`
Expected: FAIL — page still shows the hardcoded Leticia/Cecilia chips, not the factory names.

- [ ] **Step 3: Convert the route** — in `routes/web.php`, replace `Route::view('about/organisation', 'about.organisation')->name('about.organisation');` with (add `use App\Models\TeamMember;` to imports):

```php
        Route::get('about/organisation', fn () => view('about.organisation', [
            'teamMembers' => TeamMember::query()->where('visible', true)->orderBy('sort')->get(),
        ]))->name('about.organisation');
```

- [ ] **Step 4: Update the view** — in `resources/views/about/organisation.blade.php`, replace lines 46-50 (comment + hardcoded `<ul>`) with:

```blade
        @if ($teamMembers->isNotEmpty())
            <ul class="about-duo" role="list">
                @foreach ($teamMembers as $member)
                    <li>
                        <x-person-card
                            :name="$member->name"
                            :role="$member->role"
                            :bio="$member->bio_nl"
                            :photo="$member->getFirstMediaUrl('photo', 'thumb') ?: null" />
                    </li>
                @endforeach
            </ul>
        @endif
```

- [ ] **Step 5: Update the component per the Task 1 pick.**

If **A (portrait stack)** won, `resources/views/components/person-card.blade.php` becomes:

```blade
@props(['name', 'role', 'photo' => null, 'bio' => null])

<div {{ $attributes->merge(['class' => 'person-card']) }}>
    @if ($photo)
        <img src="{{ $photo }}" alt="{{ $name }}" class="person-card__photo" loading="lazy">
    @else
        <span class="person-card__disc" aria-hidden="true">{{ mb_substr($name, 0, 1) }}</span>
    @endif
    <span class="person-card__name">{{ $name }}</span>
    <span class="person-card__role">{{ $role }}</span>
    @if ($bio)
        <p class="person-card__bio">{{ $bio }}</p>
    @endif
</div>
```

If **B (horizontal row)** won, use this instead (photo/disc left, a `person-card__text` wrapper right):

```blade
@props(['name', 'role', 'photo' => null, 'bio' => null])

<div {{ $attributes->merge(['class' => 'person-card person-card--row']) }}>
    @if ($photo)
        <img src="{{ $photo }}" alt="{{ $name }}" class="person-card__photo" loading="lazy">
    @else
        <span class="person-card__disc" aria-hidden="true">{{ mb_substr($name, 0, 1) }}</span>
    @endif
    <div class="person-card__text">
        <span class="person-card__name">{{ $name }}</span>
        <span class="person-card__role">{{ $role }}</span>
        @if ($bio)
            <p class="person-card__bio">{{ $bio }}</p>
        @endif
    </div>
</div>
```

- [ ] **Step 6: Promote the winning CSS** from the Task 1 prototype `<style>` block into `resources/css/components/person-card.css` (inside the existing `@layer components`): always `.person-card__disc` + `.person-card__bio`; plus `.person-card--row`/`.person-card__text` rules only if B won. If B won, the duo `<ul>` composition in the organisation template may need `flex-col` instead of the current row layout — adjust the `about-duo` composition there (check `resources/css/pages/about.css` for `.about-duo`).

- [ ] **Step 7: Run tests + build + visual check**

```bash
php artisan test --compact --filter=PublicStructureTest
npm run build
```

Expected: PASS + clean build. One Playwright screenshot of `/nl/about/organisation` to verify the duo band (seeded rows have no bio/photo, so this shows the disc state live).

- [ ] **Step 8: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add routes/web.php resources/views/about/organisation.blade.php resources/views/components/person-card.blade.php resources/css/components/person-card.css tests/Feature/PublicStructureTest.php resources/css/pages/about.css
git commit -m "feat(about): coordination duo rendered from TeamMember records

- organisation route passes visible members ordered by sort
- person-card gains bio prop + initial-letter disc fallback state

Why: handoff item 1.1 — page follows the admin, placeholder chips gone.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

*(Drop `resources/css/pages/about.css` from the `git add` if it wasn't touched.)*

---

### Task 7: Quote model + BlueAdmin resource

**Files:**
- Create: `database/migrations/<timestamp>_create_quotes_table.php`
- Create: `app/Models/Quote.php`
- Create: `database/factories/QuoteFactory.php`
- Create: `app/BlueAdmin/Quote.php`
- Create: `app/Http/Controllers/Admin/QuoteController.php`
- Modify: `routes/web.php`, `config/blue-admin.php`

**Interfaces:**
- Produces: `App\Models\Quote` — columns `slot` (unique string), `quote`, `attribution`, `visible`; `Quote::factory()`; admin CRUD at `/admin/quotes`. Task 8 consumes the model.

- [ ] **Step 1: Scaffold**

```bash
php artisan make:model Quote -mf --no-interaction
```

- [ ] **Step 2: Migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            // Fixed page slot the quote renders in: mission, vision-1, vision-2.
            // No row for a slot means the page falls back to its lang string.
            $table->string('slot')->unique();
            $table->text('quote');
            $table->string('attribution');
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
```

- [ ] **Step 3: Model**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * An admin-curated parent quote for a fixed page slot (mission, vision-1,
 * vision-2). Pages fall back to their lang string when a slot is empty,
 * so this table can stay empty without any visual change.
 */
#[Unguarded]
class Quote extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'visible' => 'boolean',
        ];
    }
}
```

- [ ] **Step 4: Factory**

```php
<?php

namespace Database\Factories;

use App\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quote>
 */
class QuoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slot' => fake()->unique()->slug(2),
            'quote' => fake()->sentence(12),
            'attribution' => fake()->firstName().', mama van twee kinderen',
            'visible' => true,
        ];
    }
}
```

- [ ] **Step 5: BlueAdmin resource + controller + route + menu**

`app/BlueAdmin/Quote.php`:

```php
<?php

namespace App\BlueAdmin;

use Ndeblauw\BlueAdmin\Models\BlueAdminModel;

class Quote extends BlueAdminModel
{
    public $CLASS = \App\Models\Quote::class;

    public $name_to_use = 'Citaten';

    public $title_field = 'attribution';

    public $indexTableColumns = ['slot', 'attribution', 'visible'];

    public $attributesToShow = ['slot', 'quote', 'attribution', 'visible'];
}
```

`app/Http/Controllers/Admin/QuoteController.php`:

```php
<?php

namespace App\Http\Controllers\Admin;

use Ndeblauw\BlueAdmin\Http\Controllers\AdminController;

class QuoteController extends AdminController
{
    //
}
```

`routes/web.php` (admin group, after the teammembers resource; add the controller import):

```php
    Route::resource('quotes', QuoteController::class);
```

`config/blue-admin.php` menu, after the Teamleden entry:

```php
        [
            'title' => 'Citaten',
            'color' => 'violet',
            'link' => 'admin/quotes',
            'icon' => 'fa-quote-left',
        ],
```

- [ ] **Step 6: Migrate + verify**

```bash
php artisan migrate --no-interaction
php artisan route:list --path=admin/quotes
```

Expected: table created, 7 resource routes listed. Confirm `/admin/quotes` loads and a row with slot `mission` can be created and edited (same live-verification bar as Task 5; delete the trial row or keep it deliberately).

- [ ] **Step 7: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add database/migrations/*_create_quotes_table.php app/Models/Quote.php database/factories/QuoteFactory.php app/BlueAdmin/Quote.php app/Http/Controllers/Admin/QuoteController.php routes/web.php config/blue-admin.php
git commit -m "feat(admin): Quote model with fixed page slots + Citaten resource

- slot (unique) / quote / attribution / visible, editable at /admin/quotes
- slots: mission, vision-1, vision-2; empty slot = lang-string fallback

Why: handoff item 1.4 — rotating testimonial content without deploys.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

---

### Task 8: Pages prefer DB quotes over lang strings *(quote variant needs the Task 1 pick)*

**Files:**
- Create: `app/Support/Quotes.php`
- Modify: `routes/web.php:88-89` (mission + vision routes)
- Modify: `resources/views/about/mission.blade.php:30-32`
- Modify: `resources/views/about/vision.blade.php:36-47`
- Modify: `resources/css/components/pull-quote.css` (only if variant B won)
- Test: `tests/Feature/PublicStructureTest.php`

**Interfaces:**
- Consumes: `App\Models\Quote` (Task 7); Frederik's quote pick (Task 1).
- Produces: `App\Support\Quotes::forSlot(string $slot): ?Quote`; view variables `$missionQuote`, `$visionQuote1`, `$visionQuote2` (each `?Quote`).

- [ ] **Step 1: Write the failing test** — in `tests/Feature/PublicStructureTest.php`, near the mission test:

```php
it('prefers an admin-entered quote over the lang fallback', function () {
    get('/nl/about/mission')->assertSee(__('about.mission_quote_attribution'));

    \App\Models\Quote::factory()->create([
        'slot' => 'mission',
        'quote' => 'Fietsen samen voelt als een klein feest.',
        'attribution' => 'Testouder, Gent',
    ]);

    get('/nl/about/mission')
        ->assertSee('Fietsen samen voelt als een klein feest.')
        ->assertSee('Testouder, Gent')
        ->assertDontSee(__('about.mission_quote_attribution'));
});
```

- [ ] **Step 2: Run to verify it fails**

Run: `php artisan test --compact --filter="admin-entered quote"`
Expected: FAIL on the second `get()` — the page still renders the lang string, not the DB quote.

- [ ] **Step 3: Support class** — `app/Support/Quotes.php`:

```php
<?php

namespace App\Support;

use App\Models\Quote;

/**
 * Admin-curated pull-quotes for fixed page slots (mission, vision-1,
 * vision-2). A page asks for its slot; null means the caller renders its
 * lang-string fallback, so an empty quotes table changes nothing visually.
 */
class Quotes
{
    public function forSlot(string $slot): ?Quote
    {
        return Quote::query()
            ->where('slot', $slot)
            ->where('visible', true)
            ->first();
    }
}
```

- [ ] **Step 4: Convert the routes** — in `routes/web.php` replace the mission and vision `Route::view` lines with (add `use App\Support\Quotes;` to imports):

```php
        Route::get('about/mission', fn (Quotes $quotes) => view('about.mission', [
            'missionQuote' => $quotes->forSlot('mission'),
        ]))->name('about.mission');
        Route::get('about/vision', fn (Quotes $quotes) => view('about.vision', [
            'visionQuote1' => $quotes->forSlot('vision-1'),
            'visionQuote2' => $quotes->forSlot('vision-2'),
        ]))->name('about.vision');
```

(The `app(AboutStats::class)` call inside `mission.blade.php` stays — its injection refactor is Nico-backlog, out of scope.)

- [ ] **Step 5: Wire the views**

`resources/views/about/mission.blade.php:30-32` becomes:

```blade
            <x-pull-quote :attribution="$missionQuote?->attribution ?? __('about.mission_quote_attribution')">
                {{ $missionQuote?->quote ?? __('about.mission_quote') }}
            </x-pull-quote>
```

`resources/views/about/vision.blade.php` — the Fatima quote (lines 36-38) becomes:

```blade
                    <x-pull-quote variant="card" :attribution="$visionQuote1?->attribution ?? __('about.vision_quote_fatima_attribution')">
                        {{ $visionQuote1?->quote ?? __('about.vision_quote_fatima') }}
                    </x-pull-quote>
```

and the Camille quote (lines 44-46):

```blade
                    <x-pull-quote variant="card" :attribution="$visionQuote2?->attribution ?? __('about.vision_quote_camille_attribution')">
                        {{ $visionQuote2?->quote ?? __('about.vision_quote_camille') }}
                    </x-pull-quote>
```

- [ ] **Step 6: Apply the Task 1 quote pick.** If **A (baseline)** won: nothing more. If **B (quieter column)** won: add the `.pull-quote--column` block from the Task 1 prototype into `resources/css/components/pull-quote.css` (same `@layer components`), and set `variant="column"` on the mission pull-quote in Step 5 (the vision `card` variants stay as they are).

- [ ] **Step 7: Run tests + build**

```bash
php artisan test --compact --filter=PublicStructureTest
npm run build
```

Expected: PASS + clean build (build only needed if B won).

- [ ] **Step 8: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Support/Quotes.php routes/web.php resources/views/about/mission.blade.php resources/views/about/vision.blade.php tests/Feature/PublicStructureTest.php resources/css/components/pull-quote.css
git commit -m "feat(about): mission and vision quotes read from the quotes table

- Quotes::forSlot support class, lang strings as fallback per slot
- mission + vision routes pass their slot quotes to the views

Why: handoff item 1.4 — swap a parent voice without touching code.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

*(Drop `pull-quote.css` from the `git add` if variant A won.)*

---

### Task 9: Cleanup, full verification, pipeline

**Files:**
- Delete: `resources/views/design-choices.blade.php`
- Modify: `routes/web.php` (remove the prototype route)
- Modify: `docs/wiki/design/30-skeleton/00-page-registry.md`, `docs/wiki/log.md`, `docs/wiki/build/20-about-section-handoff.md`

- [ ] **Step 1: Delete the prototype** — remove `resources/views/design-choices.blade.php` and its route from the non-prod block in `routes/web.php`.

- [ ] **Step 2: Full test run**

Run: `php artisan test --compact`
Expected: green (known exception: `CalendarProximityTest` can flake order-dependently in the full suite — rerun it in isolation before treating it as a regression).

- [ ] **Step 3: Update the handoff + pipeline.** Mark items 1.1, 1.4, 1.5, 1.6 done in `docs/wiki/build/20-about-section-handoff.md` (strike or annotate, matching its style). Then run the `/pipeline` skill for the affected rows (P-15 mission, P-16 vision, P-17 organisation, P-19 press, P-20 partners): Back advances where data is wired *and was verified live in the admin* (Tasks 5/7 Step verification); Wire/UI stay 🟠 pending Frederik's critique. Append the `## [YYYY-MM-DD] build | …` entry to `docs/wiki/log.md`.

- [ ] **Step 4: Commit cleanup + docs**

```bash
git add resources/views/design-choices.blade.php routes/web.php docs/wiki/build/20-about-section-handoff.md docs/wiki/design/30-skeleton/00-page-registry.md docs/wiki/log.md
git commit -m "chore(about): drop design-choices prototype, log the admin pass

Why: throwaway prototype served its purpose; registry + handoff reflect
the landed admin track.

Co-Authored-By: Claude Fable 5 <noreply@anthropic.com>"
```

- [ ] **Step 5: Offer Frederik the `/wrap` squash** — this thread's commits collapse to one curated commit before anything ships (guard for Nico's interleaved commits first: `git log <upstream>..HEAD --format='%an'`).

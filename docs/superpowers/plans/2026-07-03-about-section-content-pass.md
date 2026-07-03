# About-Section Content Pass Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Execute the approved spec `docs/superpowers/specs/2026-07-03-about-section-content-design.md`: rename + restructure the About story pages onto the Steun-ons pattern, unify impact stats behind one DB-backed source, bind Partners cards to the CMS, add news editorial controls, and import the real press archive.

**Architecture:** Laravel 13 + Livewire/Blade public site served by Herd at `https://kidicalmass.test`; admin is `ndeblauw/blue-admin` (plain controllers under `/admin`, Blade form partials in `resources/views/admin/*/_form.blade.php`, config classes in `app/BlueAdmin/`). Public About pages are Blade views under `resources/views/about/`; copy moves into `lang/nl/about.php`. Stats come from a new `App\Support\AboutStats` (sibling of `App\Support\SupportStats`).

**Tech Stack:** PHP 8.4, Pest 4, spatie/laravel-medialibrary, Tailwind v4 (tokens in `resources/css/app.css`, role-based partials under `resources/css/`).

## Global Constraints

- Work happens on a feature branch **in a git worktree** (Nico commits to the main checkout concurrently). Create it at execution start via `superpowers:using-git-worktrees`. To run tests/serve inside the worktree: real `composer install`, `npm ci && npm run build`, `herd link` (see memory: worktree verification recipe).
- Route **names and URLs never change** (`about.mission`, `about.vision`, `about.organisation`, `about.press`, `articles.index`). Only visible labels change.
- All public copy: Dutch, per `docs/tone-of-voice.md`. **No em-dashes** in site copy. Quoted source headlines (press titles) stay verbatim.
- Styling: composition-only utilities in page templates (`grid`, `gap-*`, `max-w-*` …); **no appearance utilities or raw hex/px in templates**; component appearance lives in the component blade or a registered CSS partial (`tests/Feature/CssArchitectureTest.php` enforces).
- Tests: assert behaviour and copy via `__('key')`, `route()` and semantic/BEM hooks; **never Tailwind utility classes**. Do **not delete** tests; rewrite assertions that the restructure invalidates.
- After any PHP edit: `vendor/bin/pint --dirty --format agent`. All artisan commands get `--no-interaction`.
- Commit after every task (small checkpoints). Squash into ~3 curated commits happens later at `/wrap`, not in this plan.
- Full-suite runs: `php artisan test --compact`. Known pre-existing flake: `CalendarProximityTest` (order-dependent; passes in isolation) — not a regression signal.

---

### Task 1: Fix the curly-quote markup bug

**Files:**
- Modify: `resources/views/about/mission.blade.php:73`
- Modify: `resources/views/about/vision.blade.php:55-61`

**Interfaces:** none (pure markup repair; these sections are rebuilt in Tasks 6-7, but this fix ships now because the rebuild may land days later).

- [ ] **Step 1: Replace the smart quotes in mission.blade.php**

Line 73 currently reads (note the `”` characters):

```blade
    <x-pull-quote attribution=”Julienne, mama van twee kinderen (2 en 5 jaar)”>
```

Replace with:

```blade
    <x-pull-quote attribution="Julienne, mama van twee kinderen (2 en 5 jaar)">
```

- [ ] **Step 2: Replace the smart quotes in vision.blade.php**

Lines 55-61: `class=”about-voices”`, `variant=”card”` (×2) and `attribution=”…”` (×2) all use `”`. After the fix the block reads:

```blade
        <div class="about-voices">
            <x-pull-quote variant="card" attribution="Camille, mama van twee kinderen, Sint-Gillis">
                “Ik heb het gevoel dat ik de hele tijd de levenslust van mijn kinderen afrem.”
            </x-pull-quote>
            <x-pull-quote variant="card" attribution="Fatima, mama van drie kinderen, Jette">
                “Ik ben constant bang voor de auto's, de trams. Tegen dat we thuis zijn van school, ben ik uitgeput.”
            </x-pull-quote>
        </div>
```

(The `“…”` inside the quote *slots* are content, not attribute delimiters — leave them.)

- [ ] **Step 3: Verify in the browser HTML**

Run: `curl -sk https://kidicalmass.test/nl/about/vision | grep -c 'class="about-voices"'`
Expected: `1`
Run: `curl -sk https://kidicalmass.test/nl/about/mission | grep -c '”>'`
Expected: `0`

- [ ] **Step 4: Run the page smokes**

Run: `php artisan test --compact --filter=PublicStructureTest`
Expected: PASS (all)

- [ ] **Step 5: Commit**

```bash
git add resources/views/about/mission.blade.php resources/views/about/vision.blade.php
git commit -m "fix(about): straight quotes in Blade attributes on mission + vision"
```

---

### Task 2: Re-host the Wix-hosted PDFs

**Files:**
- Create: `public/downloads/kidical-mass-manifest.pdf`
- Create: `database/seeders/files/press/2024-10-07-persbericht-grote-kidical-mass-nl.pdf`
- Create: `database/seeders/files/press/2024-10-07-communique-grande-kidical-mass-fr.pdf`
- Create: `database/seeders/files/press/2024-02-20-persbericht-start-seizoen-nl.pdf`
- Create: `database/seeders/files/press/2024-02-20-communique-debut-saison-fr.pdf`
- Modify: `resources/views/about/vision.blade.php:51` (manifest link — temporary until Task 7 rebuilds the view; keeps the page correct in the meantime)

**Interfaces:**
- Produces: the five committed PDF files at the exact paths above. Task 7 links `asset('downloads/kidical-mass-manifest.pdf')`; Task 12 attaches the two NL press PDFs as media.

- [ ] **Step 1: Download the five PDFs from the legacy Wix URLs**

```bash
mkdir -p public/downloads database/seeders/files/press
curl -sfL -o public/downloads/kidical-mass-manifest.pdf \
  'https://www.kidicalmass.be/_files/ugd/cf0153_2b074cb919ea46698c1732a2f55b26eb.pdf'
curl -sfL -o database/seeders/files/press/2024-10-07-communique-grande-kidical-mass-fr.pdf \
  'https://www.kidicalmass.be/_files/ugd/cf0153_93a3decd79b74d1595233c7f93464dc3.pdf'
curl -sfL -o database/seeders/files/press/2024-10-07-persbericht-grote-kidical-mass-nl.pdf \
  'https://www.kidicalmass.be/_files/ugd/cf0153_72d2f4caec3f461cb95a5526d0a4730e.pdf'
curl -sfL -o database/seeders/files/press/2024-02-20-persbericht-start-seizoen-nl.pdf \
  'https://www.kidicalmass.be/_files/ugd/cf0153_a54cf90c103a4a66a1ba730048dcc473.pdf'
curl -sfL -o database/seeders/files/press/2024-02-20-communique-debut-saison-fr.pdf \
  'https://www.kidicalmass.be/_files/ugd/cf0153_83ec298a98d74fd08d835a0843f0bed9.pdf'
file public/downloads/kidical-mass-manifest.pdf database/seeders/files/press/*.pdf
```

Expected: every line reports `PDF document`. If any URL 404s (Wix may already have pruned), STOP and report — do not commit an HTML error page as a `.pdf`.

- [ ] **Step 2: Point the Visie manifest link at the local copy**

In `resources/views/about/vision.blade.php:50-51` replace the legacy comment + link:

```blade
        <p class="about-section__link"><a href="{{ asset('downloads/kidical-mass-manifest.pdf') }}" target="_blank" rel="noopener noreferrer">Download het manifest (PDF) →</a></p>
```

(Also delete the `{{-- NB: legacy Wix-gehoste PDF … --}}` comment above it.)

- [ ] **Step 3: Verify the local file serves**

Run: `curl -sk -o /dev/null -w '%{http_code}' https://kidicalmass.test/downloads/kidical-mass-manifest.pdf`
Expected: `200`

- [ ] **Step 4: Commit**

```bash
git add public/downloads database/seeders/files/press resources/views/about/vision.blade.php
git commit -m "feat(about): re-host manifest + persbericht PDFs off Wix (closes D-7)"
```

---

### Task 3: `volunteers` on year_stats (Jaarcijfers)

**Files:**
- Create: `database/migrations/<timestamp>_add_volunteers_to_year_stats_table.php`
- Modify: `resources/views/admin/yearstats/_form.blade.php`
- Modify: `app/Http/Requests/YearStatRequest.php`
- Modify: `app/BlueAdmin/YearStat.php`

**Interfaces:**
- Produces: nullable unsigned-int column `year_stats.volunteers`; `App\Models\YearStat` rows expose `->volunteers`. Task 4's `AboutStats` reads it.

- [ ] **Step 1: Create the migration**

Run: `php artisan make:migration add_volunteers_to_year_stats_table --no-interaction`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('year_stats', function (Blueprint $table) {
            $table->unsignedInteger('volunteers')->nullable()->after('participants');
        });
    }

    public function down(): void
    {
        Schema::table('year_stats', function (Blueprint $table) {
            $table->dropColumn('volunteers');
        });
    }
};
```

Run: `php artisan migrate --no-interaction`
Expected: `DONE`

- [ ] **Step 2: Admin form + validation + columns**

`resources/views/admin/yearstats/_form.blade.php` — append:

```blade
<x-ba-text type="number" name="volunteers" label="Vrijwilligers" comment="Aantal actieve vrijwilligers dat jaar. Wordt op de Over ons-pagina's getoond." />
```

`app/Http/Requests/YearStatRequest.php` — add to `rules()`:

```php
            'volunteers' => ['nullable', 'integer', 'min:0'],
```

`app/BlueAdmin/YearStat.php` — extend both column lists:

```php
    public $indexTableColumns = ['year', 'participants', 'volunteers'];

    public $attributesToShow = ['year', 'participants', 'volunteers'];
```

- [ ] **Step 3: Pint + quick round-trip check**

Run: `vendor/bin/pint --dirty --format agent`
Run: `php artisan tinker --execute '$y = \App\Models\YearStat::query()->updateOrCreate(["year" => 2025], ["volunteers" => 120]); echo $y->fresh()->volunteers;'`
Expected: `120` (this also gives the local DB a curated volunteers figure).

- [ ] **Step 4: Commit**

```bash
git add database/migrations resources/views/admin/yearstats/_form.blade.php app/Http/Requests/YearStatRequest.php app/BlueAdmin/YearStat.php
git commit -m "feat(admin): curated volunteers figure on Jaarcijfers"
```

---

### Task 4: `AboutStats` — one source of truth

**Files:**
- Create: `app/Support/AboutStats.php`
- Create: `lang/nl/about.php` (stat labels only; page copy arrives in Tasks 6-9)
- Test: `tests/Feature/AboutStatsTest.php`

**Interfaces:**
- Consumes: `year_stats.volunteers` (Task 3); existing `Group::visible()` scope, `Activity` `published()` scope + `ActivityType::KIDICALMASS`.
- Produces: `App\Support\AboutStats::cards(): array<int, array{value: string, label: string, color: string}>` — same card shape as `SupportStats::cards()`, consumable by `<x-stat-card>`. Lang keys `about.stat_groups`, `about.stat_rides`, `about.stat_volunteers`, `about.stat_participants` (`:year`).

- [ ] **Step 1: Create the lang file with the stat labels**

`lang/nl/about.php`:

```php
<?php

// Over ons — copy for the About section (P-14 → P-20). Stats labels here feed
// App\Support\AboutStats; the values are live counts + the curated Jaarcijfers
// row. Page copy groups (mission/vision/organisation/press) follow the
// support.php precedent: words live here, structure lives in the Blade views.
return [
    'stat_groups' => 'lokale groepen in heel België',
    'stat_rides' => 'fietsparades sinds 2020',
    'stat_volunteers' => 'actieve vrijwilligers',
    'stat_participants' => 'deelnemers in :year',
];
```

- [ ] **Step 2: Write the failing test**

`tests/Feature/AboutStatsTest.php`:

```php
<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\YearStat;
use App\Support\AboutStats;

it('counts groups and all-time published parades live', function () {
    Group::factory()->count(2)->create(['visible' => true]);
    Activity::factory()->count(3)->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'is_published' => true,
    ]);
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'is_published' => false,
    ]);

    $labels = collect(app(AboutStats::class)->cards())->pluck('value', 'label');

    expect($labels[__('about.stat_groups')])->toBe('2')
        ->and($labels[__('about.stat_rides')])->toBe('3');
});

it('reads volunteers and participants from the latest curated year and formats them per locale', function () {
    app()->setLocale('nl');
    YearStat::create(['year' => 2024, 'participants' => 9999, 'volunteers' => 1]);
    YearStat::create(['year' => 2025, 'participants' => 5500, 'volunteers' => 120]);

    $cards = collect(app(AboutStats::class)->cards());

    expect($cards->firstWhere('label', __('about.stat_volunteers'))['value'])->toBe('120')
        ->and($cards->firstWhere('label', __('about.stat_participants', ['year' => 2025]))['value'])->toBe('5.500');
});

it('omits any metric without an honest value', function () {
    // No rides, no year stats: only the groups card remains.
    $cards = app(AboutStats::class)->cards();

    expect($cards)->toHaveCount(1)
        ->and($cards[0]['label'])->toBe(__('about.stat_groups'));
});
```

Note: check `GroupFactory` for the visibility flag first (`grep -n visible database/factories/GroupFactory.php`); if the column is named differently (e.g. a `hidden` flag or a `visible()` factory state), adjust the two `Group::factory()` lines to whatever makes `Group::visible()->count()` return 2 — mirror how `tests/Feature/SupportStatsTest.php` builds visible groups.

- [ ] **Step 3: Run it, expect failure**

Run: `php artisan test --compact --filter=AboutStatsTest`
Expected: FAIL — `Class "App\Support\AboutStats" not found`

- [ ] **Step 4: Implement `App\Support\AboutStats`**

`app/Support/AboutStats.php`:

```php
<?php

namespace App\Support;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\YearStat;
use Illuminate\Support\Number;

/**
 * The About-section impact deck: one source of truth for the numbers on the
 * About hub and the "Wat we doen" page. Counts what the database knows (visible
 * groups, all-time published parades) and reads what only humans know
 * (participants, volunteers) from the latest curated {@see YearStat} row.
 * A metric without an honest value yields no card, mirroring {@see SupportStats}.
 */
class AboutStats
{
    /**
     * @return array<int, array{value: string, label: string, color: string}>
     */
    public function cards(): array
    {
        $cards = [[
            'value' => $this->format(Group::visible()->count()),
            'label' => __('about.stat_groups'),
            'color' => 'red',
        ]];

        $rides = Activity::query()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->published()
            ->count();

        if ($rides > 0) {
            $cards[] = [
                'value' => $this->format($rides),
                'label' => __('about.stat_rides'),
                'color' => 'green',
            ];
        }

        $latest = YearStat::query()->orderByDesc('year')->first();

        if ($latest?->volunteers) {
            $cards[] = [
                'value' => $this->format($latest->volunteers),
                'label' => __('about.stat_volunteers'),
                'color' => 'blue',
            ];
        }

        if ($latest?->participants) {
            $cards[] = [
                'value' => $this->format($latest->participants),
                'label' => __('about.stat_participants', ['year' => $latest->year]),
                'color' => 'red',
            ];
        }

        return $cards;
    }

    /** Localised number formatting: 5500 -> "5.500" under nl. */
    private function format(int $value): string
    {
        return Number::format($value, locale: app()->getLocale());
    }
}
```

(`x-stat-card` supports `blue | green | red` only — the fourth card reuses red.)

- [ ] **Step 5: Run tests, expect pass, pint, commit**

Run: `vendor/bin/pint --dirty --format agent && php artisan test --compact --filter=AboutStatsTest`
Expected: PASS (3 tests)

```bash
git add app/Support/AboutStats.php lang/nl/about.php tests/Feature/AboutStatsTest.php
git commit -m "feat(about): AboutStats single source for section impact numbers (closes D-13)"
```

---

### Task 5: Hub — rename labels, new card descriptions, live stat band

**Files:**
- Modify: `lang/nl/nav.php`
- Modify: `resources/views/about/index.blade.php`
- Modify: `tests/Feature/PublicStructureTest.php:33-49` (hub test)

**Interfaces:**
- Consumes: `AboutStats::cards()` (Task 4).
- Produces: nav labels `nav.mission = 'Wat we doen'`, `nav.vision = 'Wat we vragen'`, `nav.organisation = 'Hoe we werken'` — Tasks 6-9 reuse these for page titles/eyebrows via `__('nav.…')`.

- [ ] **Step 1: Update `lang/nl/nav.php`**

```php
    'mission' => 'Wat we doen',
    'vision' => 'Wat we vragen',
    'organisation' => 'Hoe we werken',
```

(Other keys unchanged.)

- [ ] **Step 2: Hub nav cards — new titles + fresh descriptions**

In `resources/views/about/index.blade.php` replace the three story-page cards' `__title`/`__desc` contents (the old descriptions were promoted to titles; ToV: concrete, warm, no em-dashes):

```blade
                    <h2 class="about-nav-card__title">{{ __('nav.mission') }}</h2>
                    <p class="about-nav-card__desc">Fietsparades, lokale groepen en de weg naar veilige straten.</p>
```

```blade
                    <h2 class="about-nav-card__title">{{ __('nav.vision') }}</h2>
                    <p class="about-nav-card__desc">Vier duidelijke vragen aan steden en gemeenten.</p>
```

```blade
                    <h2 class="about-nav-card__title">{{ __('nav.organisation') }}</h2>
                    <p class="about-nav-card__desc">Lokaal geworteld, licht gecoördineerd, gedragen door vrijwilligers.</p>
```

- [ ] **Step 3: Bind the hub stat band to AboutStats**

Replace the hardcoded `<ul class="about-stats__grid about-stats__grid--three">…</ul>` block (index.blade.php:111-116, including the `TODO [concern]` comment) with:

```blade
            <ul class="about-stats__grid" role="list" data-stats-source="about-stats">
                @foreach (app(\App\Support\AboutStats::class)->cards() as $card)
                    <li class="about-stat"><span class="about-stat__num">{{ $card['value'] }}</span><span class="about-stat__label">{{ $card['label'] }}</span></li>
                @endforeach
            </ul>
```

(The `--three` modifier goes: the band now holds up to 4 stats like Missie's did. `data-stats-source` is the test seam.)

- [ ] **Step 4: Update the hub test**

In `tests/Feature/PublicStructureTest.php`, extend the hub test (`it('renders the About hub…')`) — keep all existing route assertions, replace nothing else, and add at the end:

```php
        ->assertSee(__('nav.mission'))
        ->assertSee(__('nav.vision'))
        ->assertSee(__('nav.organisation'))
        ->assertSee('data-stats-source="about-stats"', escape: false);
```

- [ ] **Step 5: Run, pint, commit**

Run: `vendor/bin/pint --dirty --format agent && php artisan test --compact --filter=PublicStructureTest`
Expected: PASS

```bash
git add lang/nl/nav.php resources/views/about/index.blade.php tests/Feature/PublicStructureTest.php
git commit -m "feat(about): plain-language nav labels + live hub stat band"
```

---

### Task 6: Rebuild Missie → “Wat we doen” (variant A, Steun-ons twin)

**Files:**
- Modify: `lang/nl/about.php` (add `mission_*` keys)
- Rewrite: `resources/views/about/mission.blade.php`
- Modify: `tests/Feature/PublicStructureTest.php:51-64` (mission test)

**Interfaces:**
- Consumes: `AboutStats::cards()` (Task 4), `__('nav.mission')` (Task 5), existing components `x-page-hero`, `x-intro-text`, `x-stat-card`, `x-feature-card`, `x-pull-quote`, `x-closing-cta`.
- Produces: the page structure the PartnersPlacementTest strip test relies on is unchanged (strip injects by route name via the footer).

- [ ] **Step 1: Add the mission copy group to `lang/nl/about.php`**

Append inside the returned array (flat keys, `support.php` style):

```php
    // Wat we doen (P-15). Structure: story column (intro + welkom + quote) with
    // the stat deck beside it, then the three axes, then a chained closing CTA.
    'mission_title' => 'Veilige straten, voor elk kind.',
    'mission_intro_1' => 'Kidical Mass Belgium is een nationaal netwerk van lokale groepen die feestelijke, veilige en kindvriendelijke fietsparades organiseren in heel België. We begonnen in 2020 in Brussel en groeien nog elk jaar, in Brussel, Wallonië en Vlaanderen.',
    'mission_intro_2' => 'Elke fietsparade heeft muziek onderweg. We rijden op het tempo van het jongste kind, op zorgvuldig gekozen routes, begeleid door getrainde vrijwilligers in opvallende roze hesjes. Kidical Mass is een manier om samen je buurt te ontdekken, nieuwe mensen te leren kennen en zelfvertrouwen op de fiets te winnen. Voor de kinderen, en vaak ook voor de ouders.',
    'mission_welcome_title' => 'Iedereen is welkom',
    'mission_welcome_body' => 'Je hoeft geen ervaren fietser te zijn. Nog nooit in het verkeer gefietst? Dat geeft niets: voor veel ouders is een rit de eerste keer op de baan, en onze begeleiders zorgen dat niemand er alleen voor staat. Je hoeft geen fiets te hebben en je hoeft niet uit de buurt te komen. Kidical Mass is gemaakt om de volledige diversiteit van elke gemeente te weerspiegelen, en om elke drempel weg te nemen die een gezin kan tegenhouden.',
    'mission_welcome_link' => 'Geen fiets of nog nooit meegereden? Voor het eerst mee →',
    'mission_quote' => '“Wat hij zo leuk vindt aan fietsen, denk ik, is die vrijheid om buiten te zijn, lucht te hebben, er alleen op uit te trekken. Hij wil altijd ver gaan, iets nieuws ontdekken.”',
    'mission_quote_attribution' => 'Julienne, mama van twee kinderen (2 en 5 jaar)',
    'mission_axes_title' => 'Drie dingen die we doen',
    'mission_axis1_title' => 'Gemeenschappen helpen starten',
    'mission_axis1_body' => 'Elke Kidical Mass begint met een handvol mensen die iets beters willen voor hun buurt. We helpen nieuwe groepen een lokale fietsparade op te starten, van de eerste vergadering tot de eerste rit.',
    'mission_axis2_title' => 'Bestaande groepen ondersteunen',
    'mission_axis2_body' => 'Lokale groepen staan er niet alleen voor. We bieden vorming, coördinatiemiddelen, materiaal en nationale zichtbaarheid, zodat elke groep zich kan richten op wat telt: mensen samenbrengen.',
    'mission_axis3_title' => 'Pleiten voor kindvriendelijke straten',
    'mission_axis3_body' => 'Vrolijke fietsparades zijn een begin, geen eindpunt. We werken samen met steden en regio\'s voor veiligere infrastructuur, trager verkeer en straten die kinderen en gezinnen echt verwelkomen.',
    'mission_axis3_link' => 'Lees wat we vragen →',
    'mission_closing_heading' => 'Feest op wielen, met een duidelijke vraag',
    'mission_closing_label' => 'Lees wat we vragen',
```

- [ ] **Step 2: Rewrite the mission test (failing first)**

Replace the body of `it('renders the Mission leaf …')` in `tests/Feature/PublicStructureTest.php` (rename its description too):

```php
it('renders Wat we doen as one story with live stats, welcome fold-in and a chained CTA', function () {
    get('/nl/about/mission')
        ->assertOk()
        ->assertSee(__('nav.mission'))
        ->assertSee(__('about.mission_axes_title'))
        ->assertSee(__('about.mission_welcome_title'))
        ->assertSee(__('about.mission_quote_attribution'))
        // Live stats deck replaces the hardcoded band.
        ->assertSee('data-stats-source="about-stats"', escape: false)
        ->assertDontSee('150+')
        // The corridor hands the visitor forward: welcome link + chained closing.
        ->assertSee(route('getting-started'), escape: false)
        ->assertSee(route('about.vision'), escape: false)
        ->assertSee(__('about.mission_closing_heading'))
        // The peak-intent Steun ask moved to Steun-ons; no membership exit here.
        ->assertDontSee('Al onze ritten zijn gratis');
});
```

Run: `php artisan test --compact --filter=PublicStructureTest`
Expected: FAIL on the new assertions (old view still renders).

- [ ] **Step 3: Rewrite the view**

`resources/views/about/mission.blade.php` — full replacement:

```blade
{{--
    Over ons / Wat we doen — /about/mission (P-15)
    Restructured 2026-07 to the Steun-ons pattern (spec: 2026-07-03-about-section-
    content-design.md, variant A): one story column (intro + welkom + quote) with
    the live stat deck beside it, the three axes, and a closing CTA chained to
    Wat we vragen. Copy: lang/nl/about.php (mission_*). Structure only.
--}}
<x-layouts::site :title="__('nav.mission')">

    <x-page-hero
        :eyebrow="__('nav.mission')"
        :title="__('about.mission_title')"
        illustration="img/illustrations/rider-with-flag.svg">

    {{-- STORY — intro, welkom and the parent voice as ONE column; the live
         AboutStats deck sits beside it (Steun-ons stramien). --}}
    <section class="grid gap-10 lg:grid-cols-[1fr_20rem] lg:gap-14">
        <div class="max-w-prose">
            <x-intro-text>
                <p>{{ __('about.mission_intro_1') }}</p>
                <p>{{ __('about.mission_intro_2') }}</p>
            </x-intro-text>

            <section class="about-section">
                <x-section-heading>{{ __('about.mission_welcome_title') }}</x-section-heading>
                <p>{{ __('about.mission_welcome_body') }}</p>
                <p class="about-section__link"><a href="{{ route('getting-started') }}">{{ __('about.mission_welcome_link') }}</a></p>
            </section>

            <x-pull-quote :attribution="__('about.mission_quote_attribution')">
                {{ __('about.mission_quote') }}
            </x-pull-quote>
        </div>

        <div class="grid content-start gap-4" role="list" data-stats-source="about-stats">
            @foreach (app(\App\Support\AboutStats::class)->cards() as $card)
                <x-stat-card role="listitem" :value="$card['value']" :label="$card['label']" :color="$card['color']" />
            @endforeach
        </div>
    </section>

    {{-- DRIE DINGEN DIE WE DOEN — unchanged axes on the sky band --}}
    <section class="about-band about-band--sky">
        <div class="container mx-auto px-4">
            <h2 class="about-band__title">{{ __('about.mission_axes_title') }}</h2>
            <ul class="about-card-grid" role="list">
                <li>
                    <x-feature-card icon="rocket-launch" color="red" :title="__('about.mission_axis1_title')">
                        {{ __('about.mission_axis1_body') }}
                    </x-feature-card>
                </li>
                <li>
                    <x-feature-card icon="lifebuoy" color="red" :title="__('about.mission_axis2_title')">
                        {{ __('about.mission_axis2_body') }}
                    </x-feature-card>
                </li>
                <li>
                    <x-feature-card icon="megaphone" color="red" :title="__('about.mission_axis3_title')">
                        {{ __('about.mission_axis3_body') }} <a href="{{ route('about.vision') }}">{{ __('about.mission_axis3_link') }}</a>
                    </x-feature-card>
                </li>
            </ul>
        </div>
    </section>

    @push('scripts')
    <x-about-reveal selector=".about-band .about-card-grid > li" />
    @endpush

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta :heading="__('about.mission_closing_heading')"
            :href="route('about.vision')" :label="__('about.mission_closing_label')" />
    </x-slot:closing>

</x-layouts::site>
```

- [ ] **Step 4: Run tests, verify visually, pint, commit**

Run: `vendor/bin/pint --dirty --format agent && php artisan test --compact --filter='PublicStructureTest|ClosingCtaTest|PartnersPlacementTest|AboutStatsTest'`
Expected: PASS
Run: `curl -sk -o /dev/null -w '%{http_code}' https://kidicalmass.test/nl/about/mission`
Expected: `200`

```bash
git add lang/nl/about.php resources/views/about/mission.blade.php tests/Feature/PublicStructureTest.php
git commit -m "feat(about): rebuild Wat we doen as story + live stat deck (variant A)"
```

---

### Task 7: Rebuild Visie → “Wat we vragen” (variant B, voices under the demands)

**Files:**
- Modify: `lang/nl/about.php` (add `vision_*` keys)
- Rewrite: `resources/views/about/vision.blade.php`
- Modify: `tests/Feature/PublicStructureTest.php:66-75` (vision test)

**Interfaces:**
- Consumes: `__('nav.vision')` (Task 5), manifest PDF at `public/downloads/kidical-mass-manifest.pdf` (Task 2), components `x-numbered-item`, `x-pull-quote` (variant `card`), `x-info-card`, `x-closing-cta`.

- [ ] **Step 1: Add the vision copy group to `lang/nl/about.php`**

```php
    // Wat we vragen (P-16). Structure: tightened statement, 4 demands with
    // parent voices nested under the demand they speak to, manifest info-card,
    // closing chained to Hoe we werken. Register: committed, not preachy (ToV).
    'vision_title' => 'Een stad op kindermaat.',
    'vision_statement_1' => 'Kidical Mass begon als een fietsparade en werd een beweging. We geloven dat elk kind in België zich veilig en met vertrouwen door zijn stad moet kunnen bewegen. Dat straten ontworpen horen te zijn voor de mensen die er wonen, niet alleen voor de auto\'s die er passeren. En dat kinderen mee mogen beslissen over hoe hun buurt eruitziet.',
    'vision_statement_2' => 'Dat is niet radicaal. Het is wat de meeste ouders willen, het is wat onderzoek bevestigt, en het is waar we naartoe werken: één rit, één gemeenteraad, één beleidsgesprek tegelijk.',
    'vision_demands_title' => 'Wat we vragen',
    'vision_demand1_title' => 'Veilige fietsinfrastructuur voor kinderen en gezinnen',
    'vision_demand1_body' => 'Aparte fietspaden die kinderen echt kunnen gebruiken: gescheiden van het verkeer, goed onderhouden en aaneengesloten. Gebouwd voor de kleinste fietsers, niet alleen voor de snelste.',
    'vision_demand2_title' => 'Tragere, rustigere woonstraten',
    'vision_demand2_body' => 'Minder snel en minder druk verkeer in de straten waar kinderen wonen en spelen. Twintig is genoeg, en handhaving telt evenveel als borden.',
    'vision_demand3_title' => 'Openbare ruimte die kinderen en gezinnen echt verwelkomt',
    'vision_demand3_body' => 'Parken, pleinen en straten waar kinderen kind kunnen zijn: luidruchtig, nieuwsgierig, in beweging. Ruimte die werkt voor kinderwagens en bakfietsen, niet alleen voor auto\'s en gehaaste volwassenen.',
    'vision_demand4_title' => 'De stem van kinderen in beslissingen over hun omgeving',
    'vision_demand4_body' => 'Kinderen zijn experts van hun eigen buurt. Ze verdienen echte inspraak, geen symbolisch gebaar, wanneer steden straten, parken en openbare ruimte plannen.',
    'vision_quote_fatima' => '“Ik ben constant bang voor de auto\'s, de trams. Tegen dat we thuis zijn van school, ben ik uitgeput.”',
    'vision_quote_fatima_attribution' => 'Fatima, mama van drie kinderen, Jette',
    'vision_quote_camille' => '“Ik heb het gevoel dat ik de hele tijd de levenslust van mijn kinderen afrem.”',
    'vision_quote_camille_attribution' => 'Camille, mama van twee kinderen, Sint-Gillis',
    'vision_manifest_label' => 'Het manifest',
    'vision_manifest_body' => 'Onze volledige visie op papier, mee ondertekend door een coalitie van Belgische verenigingen. Lees het en deel het.',
    'vision_manifest_link' => 'Download het manifest (PDF)',
    'vision_closing_heading' => 'Wie maakt dit waar?',
    'vision_closing_label' => 'Ontdek hoe we werken',
```

- [ ] **Step 2: Rewrite the vision test (failing first)**

Replace `it('renders the Vision leaf …')`:

```php
it('renders Wat we vragen with voiced demands, a manifest card and a chained CTA', function () {
    get('/nl/about/vision')
        ->assertOk()
        ->assertSee(__('about.vision_demands_title'))
        ->assertSee(__('about.vision_demand1_title'))
        // Parent voices nest under the demand they speak to.
        ->assertSee(__('about.vision_quote_fatima_attribution'))
        ->assertSee(__('about.vision_quote_camille_attribution'))
        // The manifest is a self-hosted download, not a Wix URL.
        ->assertSee('downloads/kidical-mass-manifest.pdf', escape: false)
        ->assertDontSee('_files/ugd', escape: false)
        // Chain: Wat we vragen → Hoe we werken.
        ->assertSee(route('about.organisation'), escape: false)
        ->assertSee(__('about.vision_closing_heading'));
});
```

Run: `php artisan test --compact --filter=PublicStructureTest`
Expected: FAIL on the new assertions.

- [ ] **Step 3: Rewrite the view**

`resources/views/about/vision.blade.php` — full replacement:

```blade
{{--
    Over ons / Wat we vragen — /about/vision (P-16)
    Restructured 2026-07 (spec: 2026-07-03-about-section-content-design.md,
    variant B): tightened statement, four demands with the parent voices nested
    under the demand they speak to, the manifest as an info-card, closing CTA
    chained to Hoe we werken. Copy: lang/nl/about.php (vision_*). Structure only.
--}}
<x-layouts::site :title="__('nav.vision')">

    <x-page-hero
        :eyebrow="__('nav.vision')"
        :title="__('about.vision_title')"
        illustration="img/illustrations/zone-30-sign.svg">

    {{-- POSITIESTATEMENT --}}
    <x-intro-text size="lead">
        <p>{{ __('about.vision_statement_1') }}</p>
        <p>{{ __('about.vision_statement_2') }}</p>
    </x-intro-text>

    {{-- VIER EISEN — parent voices nested under the demand they speak to --}}
    <section class="about-band about-band--light-blue">
        <div class="container mx-auto px-4">
            <h2 class="about-band__title">{{ __('about.vision_demands_title') }}</h2>
            <ol class="about-demand-grid">
                <x-numbered-item number="1" :title="__('about.vision_demand1_title')">
                    {{ __('about.vision_demand1_body') }}
                    <x-pull-quote variant="card" :attribution="__('about.vision_quote_fatima_attribution')">
                        {{ __('about.vision_quote_fatima') }}
                    </x-pull-quote>
                </x-numbered-item>
                <x-numbered-item number="2" :title="__('about.vision_demand2_title')">
                    {{ __('about.vision_demand2_body') }}
                    <x-pull-quote variant="card" :attribution="__('about.vision_quote_camille_attribution')">
                        {{ __('about.vision_quote_camille') }}
                    </x-pull-quote>
                </x-numbered-item>
                <x-numbered-item number="3" :title="__('about.vision_demand3_title')">
                    {{ __('about.vision_demand3_body') }}
                </x-numbered-item>
                <x-numbered-item number="4" :title="__('about.vision_demand4_title')">
                    {{ __('about.vision_demand4_body') }}
                </x-numbered-item>
            </ol>
        </div>
    </section>

    {{-- MANIFEST — same info-card component as the Pers contact card --}}
    <section class="about-section">
        <x-info-card :label="__('about.vision_manifest_label')">
            <p>{{ __('about.vision_manifest_body') }}</p>
            <a href="{{ asset('downloads/kidical-mass-manifest.pdf') }}" target="_blank" rel="noopener noreferrer" class="info-card__link">{{ __('about.vision_manifest_link') }}</a>
        </x-info-card>
    </section>

    @push('scripts')
    <x-about-reveal selector=".about-demand" :transform="true" />
    @endpush

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta :heading="__('about.vision_closing_heading')"
            :href="route('about.organisation')" :label="__('about.vision_closing_label')" />
    </x-slot:closing>

</x-layouts::site>
```

Check `resources/views/components/numbered-item.blade.php` first: if its body renders inside a `<p>` (nesting a `figure` inside `p` is invalid HTML), move each nested `x-pull-quote` to directly after the body slot content and verify the rendered HTML with `curl -sk https://kidicalmass.test/nl/about/vision | grep -c 'pull-quote--card'` → expected `2`.

- [ ] **Step 4: Run tests, pint, commit**

Run: `vendor/bin/pint --dirty --format agent && php artisan test --compact --filter='PublicStructureTest|ClosingCtaTest'`
Expected: PASS

```bash
git add lang/nl/about.php resources/views/about/vision.blade.php tests/Feature/PublicStructureTest.php
git commit -m "feat(about): rebuild Wat we vragen with voiced demands + manifest card (variant B)"
```

---

### Task 8: Rebuild Organisatie → “Hoe we werken” (variant A, zero bespoke)

**Files:**
- Modify: `lang/nl/about.php` (add `organisation_*` keys)
- Rewrite: `resources/views/about/organisation.blade.php`
- Modify: `resources/css/pages/about.css` (delete `about-organigram` rules if present)
- Modify: `tests/Feature/PublicStructureTest.php:77-87` (organisation test)

**Interfaces:**
- Consumes: `__('nav.organisation')` (Task 5), components `x-titled-list-block` (from Steun-ons), `x-person-card`, `x-closing-cta`.
- Note: `ho-deal` CSS belongs to the help-out page — this task stops **using** it here but must NOT delete those rules. The `about-organigram` rules are deleted only after `grep -rn 'about-organigram' resources/` shows this view was the sole consumer.

- [ ] **Step 1: Add the organisation copy group to `lang/nl/about.php`**

Deliberate copy change: the old callout claimed "geen betaald personeel". The Steun-ons rework explicitly dropped that claim (the team aims to be paid fairly; see `lang/nl/support.php` header comment). The fold-in keeps "geen hoofdkantoor" and drops the paid-staff claim.

```php
    // Hoe we werken (P-17). Structure: intro (absorbs the no-HQ line), two
    // titled lists (nationaal | lokaal), the duo (safety folded into their
    // text), closing to getting-started. No bespoke components (variant A).
    'organisation_title' => 'Buren die de straat op trekken.',
    'organisation_intro_1' => 'Kidical Mass Belgium is zo opgebouwd dat het overal echt lokaal blijft. Geen nationale campagne met lokale filialen, maar een netwerk van groepen die elk hun eigen buurt kennen.',
    'organisation_intro_2' => 'Op nationaal niveau doet een klein coördinatieteam wat alleen op dat niveau kan: het merk bewaken, vorming ontwikkelen, communicatie en partnerschappen coördineren en subsidies aanvragen voor het hele netwerk. Het team schept de voorwaarden waarin lokale groepen kunnen groeien. Het stuurt ze niet aan.',
    'organisation_intro_3' => 'Op lokaal niveau is elke afdeling autonoom. Lokale trekkers kiezen hun eigen routes, verzamelpunten en partners. Zij kennen hun buurt beter dan wie ook. Die autonomie is bewust, en niet onderhandelbaar.',
    'organisation_intro_4' => 'Er is geen hoofdkantoor: Kidical Mass draait op vrijwilligers, gedragen door mensen zoals jij.',
    'organisation_who_title' => 'Wie wat doet',
    'organisation_national_title' => 'Nationale coördinatie',
    'organisation_national' => [
        'Bewaakt het merk en de identiteit van Kidical Mass Belgium',
        'Ontwikkelt vorming en onboarding voor nieuwe trekkers',
        'Coördineert nationale communicatie, website en pers',
        'Beheert partnerschappen en dient subsidieaanvragen in voor het hele netwerk',
    ],
    'organisation_local_title' => 'Lokale afdelingen',
    'organisation_local' => [
        'Organiseren hun eigen fietsparades, met eigen routes en verzamelpunten',
        'Werven en begeleiden lokale vrijwilligers',
        'Bouwen banden met lokale partners en de gemeente',
        'Zíjn de beweging. De coördinatie bestaat om hen te steunen, niet andersom.',
    ],
    'organisation_duo_title' => 'Het coördinatieduo',
    'organisation_duo_body' => 'Leticia en Cecilia vormen samen het coördinatieduo. Zij zijn het centrale aanspreekpunt voor lokale groepen en vrijwilligers: ze organiseren vorming voor veilige begeleiding, lossen dagelijkse vragen op en bewaken de basiskwaliteit en veiligheid van elke rit. Alle afdelingen werken daarvoor met dezelfde veiligheidsafspraken en routerichtlijnen: elke route loopt langs parken, speelpleinen en veilige infrastructuur, en waar nodig stemmen organisatoren de route vooraf af met de lokale politie.',
    'organisation_duo_link' => 'Hoe een rit praktisch verloopt: Voor het eerst mee →',
    'organisation_closing_heading' => 'Een afdeling starten of vervoegen?',
    'organisation_closing_label' => 'Zo begin je',
```

- [ ] **Step 2: Rewrite the organisation test (failing first)**

Replace `it('renders the Organisation leaf …')`:

```php
it('renders Hoe we werken with the two who-does-what lists and the duo carrying safety', function () {
    get('/nl/about/organisation')
        ->assertOk()
        ->assertSee(__('about.organisation_who_title'))
        ->assertSee(__('about.organisation_national_title'))
        ->assertSee(__('about.organisation_local_title'))
        ->assertSee('Leticia')
        ->assertSee('Cecilia')
        // Safety folded into the duo's text, not a separate section.
        ->assertSee(__('about.organisation_duo_title'))
        ->assertSee(route('getting-started'), escape: false)
        // The paid-staff claim is gone on purpose (mirrors the Steun-ons copy decision).
        ->assertDontSee('geen betaald personeel')
        ->assertSee(__('about.organisation_closing_heading'));
});
```

Run: `php artisan test --compact --filter=PublicStructureTest`
Expected: FAIL on the new assertions.

- [ ] **Step 3: Rewrite the view**

`resources/views/about/organisation.blade.php` — full replacement:

```blade
{{--
    Over ons / Hoe we werken — /about/organisation (P-17)
    Restructured 2026-07 (spec: 2026-07-03-about-section-content-design.md,
    variant A): intro carries the three-tier story (organigram cut), the
    ho-deal columns became two shared titled-list-blocks, safety lives in the
    duo's text, the callout folded into the intro. Copy: lang/nl/about.php
    (organisation_*). Structure only; zero page-specific components.
--}}
<x-layouts::site :title="__('nav.organisation')">

    <x-page-hero
        :eyebrow="__('nav.organisation')"
        :title="__('about.organisation_title')"
        illustration="img/illustrations/heart-30-sign.svg">

    {{-- HOE WE GEORGANISEERD ZIJN — the intro tells the whole three-tier story --}}
    <x-intro-text>
        <p>{{ __('about.organisation_intro_1') }}</p>
        <p>{{ __('about.organisation_intro_2') }}</p>
        <p>{{ __('about.organisation_intro_3') }}</p>
        <p>{{ __('about.organisation_intro_4') }}</p>
    </x-intro-text>

    {{-- WIE WAT DOET — two shared titled-list-blocks (Steun-ons component) --}}
    <section class="about-section about-section--wide">
        <x-section-heading>{{ __('about.organisation_who_title') }}</x-section-heading>
        <div class="grid gap-8 md:grid-cols-2">
            <x-titled-list-block :title="__('about.organisation_national_title')" level="h3">
                @foreach (__('about.organisation_national') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </x-titled-list-block>
            <x-titled-list-block :title="__('about.organisation_local_title')" level="h3">
                @foreach (__('about.organisation_local') as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </x-titled-list-block>
        </div>
    </section>

    {{-- HET COÖRDINATIEDUO — carries safety & vorming (they run it) --}}
    <section class="about-section">
        <x-section-heading>{{ __('about.organisation_duo_title') }}</x-section-heading>
        <p>{{ __('about.organisation_duo_body') }}</p>
        <p class="about-section__link"><a href="{{ route('getting-started') }}">{{ __('about.organisation_duo_link') }}</a></p>
        {{-- Foto's + persoonlijke bio's nog aan te leveren door het duo. --}}
        <ul class="about-duo" role="list">
            <li><x-person-card name="Leticia" role="Coördinatie" /></li>
            <li><x-person-card name="Cecilia" role="Coördinatie" /></li>
        </ul>
    </section>

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta :heading="__('about.organisation_closing_heading')"
            :href="route('getting-started')" :label="__('about.organisation_closing_label')" />
    </x-slot:closing>

</x-layouts::site>
```

- [ ] **Step 4: Delete the orphaned organigram CSS**

Run: `grep -rn 'about-organigram' resources/`
If the only hits are in `resources/css/pages/about.css`, delete that rule block from the file. Do NOT touch `ho-deal` rules (help-out page owns them).

- [ ] **Step 5: Run tests (incl. CSS architecture), pint, commit**

Run: `vendor/bin/pint --dirty --format agent && php artisan test --compact --filter='PublicStructureTest|CssArchitectureTest|PartnersPlacementTest'`
Expected: PASS

```bash
git add lang/nl/about.php resources/views/about/organisation.blade.php resources/css/pages/about.css tests/Feature/PublicStructureTest.php
git commit -m "feat(about): rebuild Hoe we werken on shared components only (variant A)"
```

---

### Task 9: Rebuild Pers (variant B, minimal: contact + archive)

**Files:**
- Modify: `lang/nl/about.php` (add `press_*` keys)
- Rewrite: `resources/views/about/press.blade.php`
- Modify: `resources/css/pages/about.css` (delete `about-press__outlets` rules if orphaned)
- Modify: `tests/Feature/PublicStructureTest.php:101-107` (press test)
- Modify: `tests/Feature/ClosingCtaTest.php:26` (remove the `about.press` dataset row)

**Interfaces:**
- Consumes: the existing route closure in `routes/web.php:92-100` that passes `$articlesByYear` (unchanged), `x-info-card`.
- Produces: archive markup consumed as-is by Task 12's seeded data.

- [ ] **Step 1: Add the press copy group to `lang/nl/about.php`**

```php
    // Pers (P-19). Structure: one contact section (offer folded into a sentence,
    // background link inline) + the year-grouped archive. No outlet strip (the
    // archive shows the outlets), no closing CTA (the page IS the contact).
    'press_title' => 'Het verhaal van de beweging.',
    'press_contact_title' => 'Journalisten, we praten graag',
    'press_contact_body' => 'We brengen je in contact met lokale trekkers en gezinnen, delen cijfers en achtergrond bij de beweging, en regelen een fotomoment bij een volgende fietsparade.',
    'press_background_link' => 'Achtergrond en cijfers: lees wat we doen →',
    'press_contact_label' => 'Perscontact',
    'press_contact_note' => 'We antwoorden zo snel als vrijwilligers dat kunnen.',
    'press_empty_title' => 'We bouwen aan een persoverzicht',
    'press_empty_body' => 'Kidical Mass kwam de afgelopen jaren in heel wat kranten, radio en tv. We brengen die berichtgeving binnenkort samen op één plek. Schreef je over Kidical Mass en wil je dat je artikel hier verschijnt? Laat het ons weten via bike@kidicalmass.be.',
    'press_document_label' => 'Artikel',
```

- [ ] **Step 2: Rewrite the press test (failing first) + drop the ClosingCta row**

Replace `it('renders the Press leaf …')` in PublicStructureTest:

```php
it('renders Pers as contact plus archive, without outlet strip or closing CTA', function () {
    get('/nl/about/press')
        ->assertOk()
        ->assertSee(__('about.press_contact_title'))
        ->assertSee('bike@kidicalmass.be')
        // Background link folded into the contact section.
        ->assertSee(route('about.mission'), escape: false)
        // The hardcoded outlet strip is gone; the archive carries the outlets.
        ->assertDontSee('Eerder verschenen in')
        // No closing CTA: the page IS the contact.
        ->assertDontSee('Vragen van de pers?');
});
```

In `tests/Feature/ClosingCtaTest.php`, delete this dataset row (approved rewrite, not a test deletion):

```php
    ['about.press', 'Vragen van de pers?'],
```

Run: `php artisan test --compact --filter='PublicStructureTest|ClosingCtaTest'`
Expected: PublicStructureTest press test FAILS (old view); ClosingCtaTest PASSES.

- [ ] **Step 3: Rewrite the view**

`resources/views/about/press.blade.php` — full replacement:

```blade
{{--
    Over ons / Pers — /about/press (P-19)
    Restructured 2026-07 (spec: 2026-07-03-about-section-content-design.md,
    variant B): one contact section + the year-grouped PressArticle archive.
    Outlet strip and closing CTA cut (the archive shows the outlets; the page
    IS the contact). Copy: lang/nl/about.php (press_*). Structure only.
--}}
<x-layouts::site :title="__('nav.press')">

    <x-page-hero
        :eyebrow="__('nav.press')"
        :title="__('about.press_title')">

    {{-- CONTACT — one section: pitch, background link, perscontact card --}}
    <section class="about-section about-section--wide">
        <div class="about-press">
            <div class="about-press__intro">
                <x-section-heading>{{ __('about.press_contact_title') }}</x-section-heading>
                <p>{{ __('about.press_contact_body') }}</p>
                <p class="about-section__link"><a href="{{ route('about.mission') }}">{{ __('about.press_background_link') }}</a></p>
            </div>
            <x-info-card :label="__('about.press_contact_label')">
                <a href="mailto:bike@kidicalmass.be" class="info-card__link">bike@kidicalmass.be</a>
                <p class="info-card__note">{{ __('about.press_contact_note') }}</p>
            </x-info-card>
        </div>
    </section>

    {{-- PERSOVERZICHT — year-grouped archive (PressArticle, admin-maintained) --}}
    <section class="about-band about-band--light-blue">
        <div class="container mx-auto px-4">
            @if ($articlesByYear->isNotEmpty())
                @foreach ($articlesByYear as $year => $articles)
                    <h2 class="about-press__year">{{ $year }}</h2>
                    <ul class="about-press__list" role="list">
                        @foreach ($articles as $article)
                            <li class="about-press__item">
                                <span class="about-press__item-outlet">{{ $article->outlet }}</span>
                                <span class="about-press__item-date">— {{ $article->published_at->isoFormat('D MMMM YYYY') }}</span>
                                @if ($article->url)
                                    <a href="{{ $article->url }}" target="_blank" rel="noopener noreferrer" class="about-press__item-title">
                                @else
                                    <span class="about-press__item-title">
                                @endif
                                    {{ $article->title }}
                                @if ($article->url)
                                    </a>
                                @else
                                    </span>
                                @endif
                                @if ($article->getFirstMedia('document'))
                                    <a href="{{ $article->getFirstMediaUrl('document') }}" target="_blank" class="about-press__item-document" rel="noopener noreferrer">
                                        <svg class="about-press__item-document-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                        </svg>
                                        {{ __('about.press_document_label') }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            @else
                <div class="about-empty">
                    <h2 class="about-empty__title">{{ __('about.press_empty_title') }}</h2>
                    <p>{{ __('about.press_empty_body') }}</p>
                </div>
            @endif
        </div>
    </section>

    </x-page-hero>

</x-layouts::site>
```

(Note: the em-dash-looking `—` in the date row is pre-existing rendered punctuation between metadata, kept for visual continuity with the current archive list. The `<x-slot:closing>` is intentionally absent.)

- [ ] **Step 4: Delete orphaned outlet-strip CSS**

Run: `grep -rn 'about-press__outlets' resources/`
If the only hits are in `resources/css/pages/about.css`, delete that rule block.

- [ ] **Step 5: Run tests, pint, commit**

Run: `vendor/bin/pint --dirty --format agent && php artisan test --compact --filter='PublicStructureTest|ClosingCtaTest|CssArchitectureTest'`
Expected: PASS

```bash
git add lang/nl/about.php resources/views/about/press.blade.php resources/css/pages/about.css tests/Feature/PublicStructureTest.php tests/Feature/ClosingCtaTest.php
git commit -m "feat(about): rebuild Pers as contact + archive only (variant B)"
```

---

### Task 10: Partners — category field + cards from the database

**Files:**
- Create: `app/Enums/PartnerCategory.php`
- Create: `database/migrations/<timestamp>_add_category_to_partners_table.php`
- Modify: `app/Models/Partner.php` (cast)
- Modify: `app/Http/Requests/PartnerRequest.php`
- Modify: `resources/views/admin/partners/_form.blade.php`
- Modify: `app/BlueAdmin/Partner.php`
- Modify: `routes/web.php:101` (the `about/partners` route)
- Modify: `resources/views/about/partners.blade.php:26-43` (cards block only)
- Test: `tests/Feature/PartnersPageTest.php` (new)
- Modify: `tests/Feature/PublicStructureTest.php:89-99` (partners test)

**Interfaces:**
- Produces: `App\Enums\PartnerCategory` (string-backed: `institutioneel | bondgenoot | operationeel`, methods `label(): string`, `getOptionsArray(): array`); nullable `partners.category` column cast to the enum. The partners page renders national (`group_id` null), `visible` partners with category institutioneel/bondgenoot.

- [ ] **Step 1: Create the enum**

`app/Enums/PartnerCategory.php` (mirror `ActivityType`'s shape):

```php
<?php

namespace App\Enums;

enum PartnerCategory: string
{
    case INSTITUTIONEEL = 'institutioneel';
    case BONDGENOOT = 'bondgenoot';
    case OPERATIONEEL = 'operationeel';

    public function label(): string
    {
        return match ($this) {
            self::INSTITUTIONEEL => 'Institutioneel',
            self::BONDGENOOT => 'Bondgenoot',
            self::OPERATIONEEL => 'Operationeel',
        };
    }

    /**
     * Get an array of options for use in forms and filters
     */
    public static function getOptionsArray(): array
    {
        return collect(self::cases())->mapWithKeys(fn ($case) => [$case->value => $case->label()])->toArray();
    }
}
```

- [ ] **Step 2: Migration with backfill-by-name**

Run: `php artisan make:migration add_category_to_partners_table --no-interaction`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description_fr');
        });

        // Backfill the four partners the page previously named in static copy,
        // so the bound cards section is never empty on existing databases.
        DB::table('partners')->where('name', 'like', '%Brussel Mobiliteit%')->update(['category' => 'institutioneel']);
        DB::table('partners')->where('name', 'like', '%Brussel Stad%')->update(['category' => 'institutioneel']);
        DB::table('partners')->where('name', 'like', '%Schaarbeek%')->update(['category' => 'institutioneel']);
        DB::table('partners')->where('name', 'like', '%Clean Cities%')->update(['category' => 'bondgenoot']);
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
```

Run: `php artisan migrate --no-interaction` → `DONE`

- [ ] **Step 3: Model cast, validation, admin form, admin columns**

`app/Models/Partner.php` — add to the `casts()` array:

```php
            'category' => \App\Enums\PartnerCategory::class,
```

`app/Http/Requests/PartnerRequest.php` — add:

```php
            'category' => ['nullable', \Illuminate\Validation\Rule::enum(\App\Enums\PartnerCategory::class)],
```

`resources/views/admin/partners/_form.blade.php` — insert after the `description_fr` textarea:

```blade
<x-ba-select name="category" label="Categorie" :options="\App\Enums\PartnerCategory::getOptionsArray()" allow-null-option comment="Institutioneel en Bondgenoot verschijnen als kaart op /about/partners." />
```

`app/BlueAdmin/Partner.php`:

```php
    public $indexTableColumns = ['name', 'category', 'visible'];

    public $attributesToShow = ['name', 'url', 'description_nl', 'description_fr', 'category', 'group_id', 'show_logo', 'visible'];
```

- [ ] **Step 4: Write the failing page test**

`tests/Feature/PartnersPageTest.php`:

```php
<?php

use App\Enums\PartnerCategory;
use App\Models\Partner;

use function Pest\Laravel\get;

it('renders categorised national partners as cards from the database', function () {
    Partner::factory()->create([
        'name' => 'Testgewest Mobiliteit',
        'description_nl' => 'Gewestelijke testpartner.',
        'category' => PartnerCategory::INSTITUTIONEEL,
        'visible' => true,
        'group_id' => null,
    ]);
    Partner::factory()->create([
        'name' => 'Onzichtbare Partner',
        'category' => PartnerCategory::INSTITUTIONEEL,
        'visible' => false,
        'group_id' => null,
    ]);
    Partner::factory()->create([
        'name' => 'Ongecategoriseerde Partner',
        'category' => null,
        'visible' => true,
        'group_id' => null,
    ]);

    get('/nl/about/partners')
        ->assertOk()
        ->assertSee('Testgewest Mobiliteit')
        ->assertSee('Gewestelijke testpartner.')
        ->assertDontSee('Onzichtbare Partner')
        ->assertDontSee('Ongecategoriseerde Partner');
});

it('orders institutioneel before bondgenoot', function () {
    Partner::factory()->create(['name' => 'Alliantie A', 'category' => PartnerCategory::BONDGENOOT, 'visible' => true, 'group_id' => null]);
    Partner::factory()->create(['name' => 'Zetel Z', 'category' => PartnerCategory::INSTITUTIONEEL, 'visible' => true, 'group_id' => null]);

    get('/nl/about/partners')->assertOk()->assertSeeInOrder(['Zetel Z', 'Alliantie A']);
});
```

Check `database/factories/PartnerFactory.php` first — if `group_id`/`visible` defaults differ, set them explicitly as above.

Run: `php artisan test --compact --filter=PartnersPageTest`
Expected: FAIL (page is a static `Route::view`; names not present).

- [ ] **Step 5: Bind the route + the cards block**

`routes/web.php` — replace `Route::view('about/partners', 'about.partners')->name('about.partners');` with:

```php
        Route::get('about/partners', function () {
            $categoryOrder = [
                \App\Enums\PartnerCategory::INSTITUTIONEEL->value,
                \App\Enums\PartnerCategory::BONDGENOOT->value,
            ];

            $partners = \App\Models\Partner::query()
                ->whereNull('group_id')
                ->where('visible', true)
                ->whereIn('category', $categoryOrder)
                ->get()
                ->sortBy([
                    fn ($a, $b) => array_search($a->category->value, $categoryOrder) <=> array_search($b->category->value, $categoryOrder),
                    fn ($a, $b) => strcmp($a->name, $b->name),
                ]);

            return view('about.partners', ['partners' => $partners]);
        })->name('about.partners');
```

`resources/views/about/partners.blade.php` — replace ONLY the hardcoded `<ul class="about-partner-grid">…</ul>` (the four static `<li class="about-partner-card">` entries) with:

```blade
        <ul class="about-partner-grid" role="list">
            @foreach ($partners as $partner)
                <li class="about-partner-card" data-partner-category="{{ $partner->category->value }}">
                    <strong>{{ $partner->name }}</strong>
                    @if ($partner->description_nl)
                        <p>{{ $partner->description_nl }}</p>
                    @endif
                </li>
            @endforeach
        </ul>
```

Everything else on the page (logo wall PNG, formules, charter, enquiry band) stays untouched.

- [ ] **Step 6: Reconcile the PublicStructureTest partners test**

The existing test asserts the static names. On a test database the backfill matches nothing (factory data), so rewrite `it('renders the Partners leaf …')` to assert the page furniture instead of seeded names:

```php
it('renders the Partners leaf with the logo wall, find-a-bike pointer and enquiry contact', function () {
    get('/nl/about/partners')
        ->assertOk()
        ->assertSee('Onze partners en bondgenoten')
        ->assertSee('En vele anderen die Kidical Mass mee mogelijk maken')
        ->assertSee('Loopz')
        ->assertSee('bike@kidicalmass.be')
        ->assertSee(route('find-a-bike'), escape: false);
});
```

- [ ] **Step 7: Run, verify live, pint, commit**

Run: `vendor/bin/pint --dirty --format agent && php artisan test --compact --filter='PartnersPageTest|PublicStructureTest|PartnerEnquiryTest'`
Expected: PASS
Run: `curl -sk https://kidicalmass.test/nl/about/partners | grep -c 'data-partner-category'`
Expected: `4` (the backfilled real partners on the dev DB).

```bash
git add app/Enums/PartnerCategory.php database/migrations app/Models/Partner.php app/Http/Requests/PartnerRequest.php resources/views/admin/partners/_form.blade.php app/BlueAdmin/Partner.php routes/web.php resources/views/about/partners.blade.php tests/Feature/PartnersPageTest.php tests/Feature/PublicStructureTest.php
git commit -m "feat(partners): bind about/partners cards to the partners table (advances D-11)"
```

---

### Task 11: News editorial controls (draft state, publish date, rich text)

**Files:**
- Create: `database/migrations/<timestamp>_add_publishing_to_articles_table.php`
- Modify: `app/Models/Article.php` (casts, scopes, content accessor)
- Modify: `database/factories/ArticleFactory.php`
- Modify: `app/Http/Controllers/ArticleController.php`
- Modify: `app/Http/Requests/ArticleRequest.php`
- Modify: `resources/views/admin/articles/_form.blade.php`
- Modify: `app/BlueAdmin/Article.php`
- Modify: `resources/views/components/article-card.blade.php:31`
- Modify: `resources/views/articles/show.blade.php`
- Test: `tests/Feature/ArticlePublishingTest.php` (new)

**Interfaces:**
- Consumes: the `#[Scope]` attribute pattern from `app/Models/Activity.php:45-54`.
- Produces: `articles.is_published` (bool, default false) + `articles.published_at` (nullable datetime); `Article::published()` / `Article::drafts()` scopes; `$article->content_html` accessor returning `Illuminate\Support\HtmlString`.

- [ ] **Step 1: Migration with backfill**

Run: `php artisan make:migration add_publishing_to_articles_table --no-interaction`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('content_fr');
            $table->dateTime('published_at')->nullable()->after('is_published');
        });

        // Everything that exists today is live; keep it live and dated.
        DB::table('articles')->update(['is_published' => true]);
        DB::table('articles')->update(['published_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['is_published', 'published_at']);
        });
    }
};
```

Run: `php artisan migrate --no-interaction` → `DONE`

- [ ] **Step 2: Write the failing tests**

`tests/Feature/ArticlePublishingTest.php`:

```php
<?php

use App\Models\Article;

use function Pest\Laravel\get;

it('hides drafts from the news feed and 404s their detail page', function () {
    $draft = Article::factory()->draft()->create(['title_nl' => 'Geheime kladversie']);
    $live = Article::factory()->create(['title_nl' => 'Nieuwe groep in Gent']);

    get('/nl/about/news')
        ->assertOk()
        ->assertSee('Nieuwe groep in Gent')
        ->assertDontSee('Geheime kladversie');

    get(route('articles.show', $draft))->assertNotFound();
    get(route('articles.show', $live))->assertOk();
});

it('orders the feed by publish date, newest first', function () {
    Article::factory()->create(['title_nl' => 'Ouder bericht', 'published_at' => now()->subDays(10), 'created_at' => now()]);
    Article::factory()->create(['title_nl' => 'Verser bericht', 'published_at' => now()->subDay(), 'created_at' => now()->subMonth()]);

    get('/nl/about/news')->assertOk()->assertSeeInOrder(['Verser bericht', 'Ouder bericht']);
});

it('renders rich-text content as HTML and legacy plain text with line breaks', function () {
    $rich = Article::factory()->create(['content_nl' => '<p>Een <strong>rijk</strong> bericht.</p>']);
    $plain = Article::factory()->create(['content_nl' => "Regel een.\nRegel twee."]);

    get(route('articles.show', $rich))->assertOk()->assertSee('<strong>rijk</strong>', escape: false);
    get(route('articles.show', $plain))->assertOk()->assertSee("Regel een.<br />\nRegel twee.", escape: false);
});
```

Run: `php artisan test --compact --filter=ArticlePublishingTest`
Expected: FAIL — `Call to undefined method … draft()`.

- [ ] **Step 3: Model + factory**

`app/Models/Article.php` — add casts + scopes + accessor (imports: `Illuminate\Database\Eloquent\Attributes\Scope`, `Illuminate\Database\Eloquent\Builder`, `Illuminate\Support\HtmlString`):

```php
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    #[Scope]
    protected function drafts(Builder $query): void
    {
        $query->where('is_published', false);
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * Body HTML for the public page: rich-text (TinyMCE) content renders as-is,
     * legacy plain-text content keeps its escaped nl2br rendering.
     */
    protected function getContentHtmlAttribute(): HtmlString
    {
        $content = (string) $this->content_nl;

        return str_contains($content, '<p')
            ? new HtmlString($content)
            : new HtmlString(nl2br(e($content)));
    }
```

`database/factories/ArticleFactory.php` — add to `definition()`:

```php
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-1 year'),
```

and add the state method:

```php
    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false, 'published_at' => null]);
    }
```

- [ ] **Step 4: Controller, views, admin**

`app/Http/Controllers/ArticleController.php`:

```php
    public function index(string $locale): View
    {
        $articles = Article::with(['author', 'groups'])
            ->published()
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('articles.index', compact('articles'));
    }

    public function show(string $locale, Article $article): View
    {
        abort_unless($article->is_published, 404);

        $article->load(['author', 'groups']);

        return view('articles.show', compact('article'));
    }
```

`resources/views/articles/show.blade.php` — two changes:

```blade
                <time datetime="{{ $article->published_at->format('Y-m-d') }}">{{ $article->published_at->format('j F Y') }}</time>
```

```blade
        <div class="text-lg leading-relaxed text-kidical-ink">
            {!! $article->content_html !!}
        </div>
```

`resources/views/components/article-card.blade.php:31`:

```blade
            <time datetime="{{ $article->published_at->format('Y-m-d') }}">{{ $article->published_at->format('j M Y') }}</time>
```

`resources/views/admin/articles/_form.blade.php` — switch both textareas to rich text and add the publish controls before the divider:

```blade
<x-ba-textarea name="content_nl" label="Content (NL)" rows="5" rte />
<x-ba-textarea name="content_fr" label="Content (FR)" rows="5" rte />
```

```blade
<x-ba-boolean name="is_published" label="Gepubliceerd" comment="Uit = kladversie, onzichtbaar op de site." />
<x-ba-datepicker name="published_at" label="Publicatiedatum" only-date comment="Bepaalt de volgorde in de nieuwsfeed." />
```

`app/Http/Requests/ArticleRequest.php` — add:

```php
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
```

`app/BlueAdmin/Article.php`:

```php
    public $indexTableColumns = ['title_nl', 'is_published', 'published_at'];

    public $attributesToShow = ['title_nl', 'title_fr', 'is_published', 'published_at', 'author_id', 'created_at'];
```

If the `rte` flag needs TinyMCE config that BlueAdmin does not expose per-field (check `config/blue-admin.php` and the `blue-admin-backend` skill), take the package default toolbar and note "toolbar trim" as a follow-up for Nico — do NOT patch the package.

- [ ] **Step 5: Run the full news-related set, pint, commit**

Run: `vendor/bin/pint --dirty --format agent && php artisan test --compact --filter='ArticlePublishingTest|PublicPagesTest|PublicStructureTest|BlueAdminSmokeTest|NewsletterOptinTest'`
Expected: PASS. If `PublicPagesTest` or others create articles via the factory and asserted visibility, the factory's `is_published => true` default keeps them green.

```bash
git add database/migrations app/Models/Article.php database/factories/ArticleFactory.php app/Http/Controllers/ArticleController.php app/Http/Requests/ArticleRequest.php resources/views/admin/articles/_form.blade.php app/BlueAdmin/Article.php resources/views/articles/show.blade.php resources/views/components/article-card.blade.php tests/Feature/ArticlePublishingTest.php
git commit -m "feat(news): draft state, publish date and rich-text body for articles"
```

---

### Task 12: Press archive import (seeder from the Wix scrape)

**Files:**
- Create: `database/seeders/PressArchiveSeeder.php`
- Test: `tests/Feature/PressArchiveSeederTest.php` (new)

**Interfaces:**
- Consumes: `App\Models\PressArticle` (fields `title_nl`, `outlet`, `url`, `published_at`; media collection `document`, singleFile); the PDFs from Task 2 in `database/seeders/files/press/`.
- Source of truth for entries: `docs/raw/website/press.md` (Wix scrape). Strip `fbclid`/`utm`/`referrer` junk from URLs.

- [ ] **Step 1: Write the failing seeder test**

`tests/Feature/PressArchiveSeederTest.php`:

```php
<?php

use App\Models\PressArticle;
use Database\Seeders\PressArchiveSeeder;

it('seeds the historic press archive once, idempotently', function () {
    $this->seed(PressArchiveSeeder::class);
    $count = PressArticle::count();

    expect($count)->toBeGreaterThanOrEqual(18);

    // Running twice must not duplicate.
    $this->seed(PressArchiveSeeder::class);
    expect(PressArticle::count())->toBe($count);

    // Spot-check one entry per era.
    expect(PressArticle::where('outlet', 'RTBF')->whereYear('published_at', 2025)->exists())->toBeTrue()
        ->and(PressArticle::where('outlet', 'Het Nieuwsblad')->whereYear('published_at', 2020)->exists())->toBeTrue()
        ->and(PressArticle::where('outlet', 'Persbericht')->count())->toBe(2);
});
```

Run: `php artisan test --compact --filter=PressArchiveSeederTest`
Expected: FAIL — seeder class not found.

- [ ] **Step 2: Write the seeder**

`database/seeders/PressArchiveSeeder.php`. Entries transcribed from `docs/raw/website/press.md`; URLs cleaned of tracking params; three date-less 2022 entries carry an approximate date (flagged in the array — the client corrects them in BlueAdmin if needed).

```php
<?php

namespace Database\Seeders;

use App\Models\PressArticle;
use Illuminate\Database\Seeder;

/**
 * Imports the historic press archive scraped from the old Wix site
 * (docs/raw/website/press.md). Idempotent: keyed on url (or title for the
 * two persberichten without one). The two NL persbericht PDFs attach as
 * `document` media. Dates marked "approx" below were not in the scrape and
 * are estimated from context or the article URL; correct them in the admin
 * if better information surfaces.
 */
class PressArchiveSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->entries() as $entry) {
            $article = PressArticle::updateOrCreate(
                $entry['url'] ? ['url' => $entry['url']] : ['title_nl' => $entry['title']],
                [
                    'title_nl' => $entry['title'],
                    'outlet' => $entry['outlet'],
                    'url' => $entry['url'],
                    'published_at' => $entry['published_at'],
                ],
            );

            if (($entry['document'] ?? null) && $article->getFirstMedia('document') === null) {
                $path = database_path('seeders/files/press/'.$entry['document']);

                if (is_file($path)) {
                    $article->addMedia($path)->preservingOriginal()->toMediaCollection('document');
                }
            }
        }
    }

    /**
     * @return array<int, array{outlet: string, title: string, url: ?string, published_at: string, document?: string}>
     */
    private function entries(): array
    {
        return [
            // 2025
            ['outlet' => 'RTBF', 'title' => '“Kidical mass” : des enfants dans la rue pour demander plus de sécurité lors de leurs déplacements à vélo', 'url' => 'https://www.rtbf.be/article/kidical-mass-des-enfants-dans-la-rue-pour-demander-plus-de-securite-lors-de-leurs-deplacements-a-velo-11565713', 'published_at' => '2025-06-21'],
            ['outlet' => 'RTBF', 'title' => 'Morning Rush et Kidical Mass : le vélo à l\'honneur à Namur ce samedi', 'url' => 'https://www.rtbf.be/article/morning-rush-et-kidicall-mass-le-velo-a-l-honneur-a-namur-ce-samedi-11562950', 'published_at' => '2025-05-13'],
            ['outlet' => 'Het Laatste Nieuws', 'title' => 'Vijf jaar Kidical Mass: feest en fietsprotest voor een kindvriendelijke stad', 'url' => 'https://www.hln.be/brussel/vijf-jaar-kidical-mass-feest-en-fietsprotest-voor-een-kindvriendelijke-stad~a4a43002/', 'published_at' => '2025-05-05'],
            ['outlet' => 'Bruzz', 'title' => 'Kidical Mass bepleit fietsvriendelijk Brussel voor jongeren (video)', 'url' => 'https://www.bruzz.be/actua/veiligheid/kidical-mass-bepleit-fietsvriendelijk-brussel-voor-jongeren-2025-05-04', 'published_at' => '2025-05-04'],
            ['outlet' => 'BX1', 'title' => 'Le Tram : la mobilité des jeunes (video)', 'url' => 'https://bx1.be/emission/le-tram-la-mobilite-des-jeunes/', 'published_at' => '2025-02-21'],

            // 2024
            ['outlet' => 'BX1', 'title' => 'Kidical Mass : un millier d\'enfants et parents paradent à vélo', 'url' => 'https://bx1.be/categories/mobilite/kidical-mass-un-millier-denfants-et-parents-paradent-a-velo/', 'published_at' => '2024-10-14'],
            ['outlet' => 'Persbericht', 'title' => 'Kidical Mass mobiliseert bijna 1.150 ouders en kinderen om een fiets- en kindvriendelijkere stad te eisen', 'url' => null, 'published_at' => '2024-10-07', 'document' => '2024-10-07-persbericht-grote-kidical-mass-nl.pdf'],
            ['outlet' => 'Bruzz', 'title' => '“Kidical Mass lokt duizend deelnemers: \'Door dit soort initiatieven zien we ook beterschap\'”', 'url' => 'https://www.bruzz.be/actua/mobiliteit/grote-opkomst-voor-jaarlijkse-grote-kidical-mass-2024-10-06', 'published_at' => '2024-10-06'],
            ['outlet' => 'BX1', 'title' => 'Bruxelles Vit : Kidical Mass (radio)', 'url' => 'https://bx1.be/radio-emission/bruxelles-vit-kidical-mass-03-10-2024/', 'published_at' => '2024-10-03'],
            ['outlet' => 'Het Laatste Nieuws', 'title' => '“Dit jaar organiseren we 60 fietstochten doorheen Brussel”', 'url' => 'https://www.hln.be/brussel/leticia-37-bouwt-aan-een-fietscultuur-in-brussel-met-kidical-mass-dit-jaar-organiseren-we-60-fietstochten-doorheen-brussel~a2dc9830/', 'published_at' => '2024-03-05'],
            ['outlet' => 'Persbericht', 'title' => 'Persbericht start seizoen 2024', 'url' => null, 'published_at' => '2024-02-20', 'document' => '2024-02-20-persbericht-start-seizoen-nl.pdf'],

            // 2023
            ['outlet' => 'Bruzz', 'title' => 'Fietsambassadeur Leticia Sere bij Melina: \'Er is nood aan conflictvrije kruispunten\'', 'url' => 'https://www.bruzz.be/videoreeks/melina/video-leticia-sere-bij-melina-er-nood-aan-conflictvrije-kruispunten', 'published_at' => '2023-12-07'],
            ['outlet' => 'Politico', 'title' => 'Living Cities: Turning Helsinki\'s empty offices into homes (vermelding)', 'url' => 'https://www.politico.eu/newsletter/global-policy-lab/living-cities-turning-helsinkis-empty-offices-into-homes/', 'published_at' => '2023-11-02'],
            ['outlet' => 'La Dernière Heure', 'title' => 'Près d\'un millier de participants à la "Kidical Mass" organisée à Bruxelles ce dimanche', 'url' => 'https://www.dhnet.be/regions/bruxelles/bruxelles-mobilite/2023/09/11/pres-dun-millier-de-participants-a-la-kidical-mass-organisee-a-bruxelles-ce-dimanche-YIPY45NFIVEBDES67UI2FJTXQI/', 'published_at' => '2023-09-11'],
            ['outlet' => 'Het Nieuwsblad', 'title' => 'Kidical Mass lokt bijna 1.000 deelnemers', 'url' => 'https://www.nieuwsblad.be/cnt/dmf20230911_93580288', 'published_at' => '2023-09-11'],
            ['outlet' => 'Het Laatste Nieuws', 'title' => 'Driejarig bestaan Kidical Mass gevierd met tocht door Brussel', 'url' => 'https://www.hln.be/brussel/driejarig-bestaan-kidical-mass-gevierd-met-tocht-door-brussel~a0badfe0/', 'published_at' => '2023-09-10'],
            ['outlet' => 'BX1', 'title' => 'Kidical Mass : plus de sécurité pour les enfants à vélo', 'url' => 'https://bx1.be/categories/news/kidical-mass-plus-de-securite-pour-les-enfants-a-velo/', 'published_at' => '2023-09-10'],
            ['outlet' => 'Bruzz', 'title' => 'Leticia Sere (Kidical Mass): \'Liever Schaarbike dan Carbeek\'', 'url' => 'https://www.bruzz.be/mobiliteit/leticia-sere-kidical-mass-kinderen-kunnen-zoveel-bijdragen-aan-verkeersveiligheid-2023', 'published_at' => '2023-09-07'],

            // 2022 (scrape had no dates; approx from context/URL)
            ['outlet' => 'La Dernière Heure', 'title' => 'Une "grande Kidical Mass" ce dimanche à Bruxelles au départ du Grand-Hospice', 'url' => 'https://www.dhnet.be/regions/bruxelles/bruxelles-mobilite/2022/09/09/une-grande-kidical-mass-ce-dimanche-a-bruxelles-au-depart-du-grand-hospice-DM7FZ2QGXFBKFKAROND3OCU2JQ/', 'published_at' => '2022-09-09'],
            ['outlet' => 'BX1', 'title' => 'Kidical Mass : la manifestation à vélo s\'adapte aux enfants', 'url' => 'https://bx1.be/categories/news/kidical-mass-la-manifesttion-a-velo-sadapte-aux-enfants/', 'published_at' => '2022-09-09'], // approx
            ['outlet' => 'Het Laatste Nieuws', 'title' => 'Kidical Mass in Elsene: “Willen kinderen leren fietsen op laagdrempelige manier”', 'url' => 'https://www.hln.be/brussel/kidical-mass-in-elsene-willen-kinderen-leren-fietsen-op-laagdrempelige-manier~a0cd3db6/', 'published_at' => '2022-05-01'], // approx

            // 2020
            ['outlet' => 'Het Nieuwsblad', 'title' => 'Eerste Kidical Mass is schot in de roos', 'url' => 'https://www.nieuwsblad.be/cnt/dmf20200701_93495053', 'published_at' => '2020-07-01'],
            ['outlet' => 'Bruzz', 'title' => 'Kidical Mass: \'Fietsen moet vanzelfsprekend worden\' (video)', 'url' => 'https://www.bruzz.be/videoreeks/vrijdag-26-juni-2020/video-kidical-mass-fietsen-moet-vanzelfsprekend-worden', 'published_at' => '2020-06-26'],
        ];
    }
}
```

- [ ] **Step 3: Run the test, expect pass**

Run: `vendor/bin/pint --dirty --format agent && php artisan test --compact --filter=PressArchiveSeederTest`
Expected: PASS.

- [ ] **Step 4: Seed the dev database and verify the live page**

Run: `php artisan db:seed --class=PressArchiveSeeder --no-interaction`
Run: `curl -sk https://kidicalmass.test/nl/about/press | grep -oE 'about-press__year">[0-9]{4}' | sort -u`
Expected: five year headings (2020, 2022, 2023, 2024, 2025) and no empty state.

- [ ] **Step 5: Commit**

```bash
git add database/seeders/PressArchiveSeeder.php tests/Feature/PressArchiveSeederTest.php
git commit -m "feat(press): import the historic press archive from the Wix scrape"
```

---

### Task 13: Full verification + pipeline/wiki bookkeeping

**Files:**
- Modify: `docs/wiki/design/30-skeleton/00-page-registry.md` (P-14, P-15, P-16, P-17, P-18, P-19, P-20 rows + Top gaps + Roll-up)
- Modify: `docs/wiki/log.md` (new entry)

**Interfaces:** consumes everything above; run this task LAST.

- [ ] **Step 1: Full test suite**

Run: `php artisan test --compact`
Expected: green (the known `CalendarProximityTest` order-dependence flake aside — if it fails, rerun it in isolation: `php artisan test --compact --filter=CalendarProximityTest` → PASS confirms it's the flake, not a regression).

- [ ] **Step 2: Frontend build + eyeball pass**

Run: `npm run build`
Then load `/nl/about`, `/nl/about/mission`, `/nl/about/vision`, `/nl/about/organisation`, `/nl/about/press`, `/nl/about/partners`, `/nl/about/news` — every page 200, stats identical on hub and Wat we doen, chain CTAs route correctly, archive shows five year groups.

- [ ] **Step 3: Update the page registry (follow the CLAUDE.md "one update touches four things" rule)**

- P-14: `Back` → 🟢 (stat bar live via AboutStats); gaps: delete the "stat bar duplicates Missie's stats" gap, add "stats live (AboutStats)".
- P-15: `Wire` → 🟠 with note "restructured to story+deck 2026-07, Frederik critique pending"; `Back` → 🟢 (live stats); gaps: delete "stats static + 2024-stale"; keep "Julienne NL translation pending duo".
- P-16: `Wire` → 🟠 (restructured, critique pending); gaps: delete the manifesto-Wix gap (D-7 closed); keep parent-quote translations note.
- P-17: `Wire` → 🟠 (restructured, critique pending); gaps: keep duo photos/bios; delete the organigram note.
- P-18: `Back` → 🟢 (draft state + publish date + rich text live).
- P-19: `Wire` → 🟠 (restructured, critique pending); `Back` → 🟢; gaps: DELETE the stale "no Press model yet" note, add "archive live: PressArticle + 20-item import `[content]` client corrects approx 2022 dates"; keep "confirm press address".
- P-20: `Back` → 🟠 (cards bound to partners table; logo wall still static); gaps: update D-11 note to "category field live, cards bound; logos + national pass still open".
- Keep all 12 columns per row intact; update the Roll-up prose counts to match.

- [ ] **Step 4: Append the log entry**

`docs/wiki/log.md`:

```markdown
## [2026-07-03] build | About-section content pass

Renamed the story pages to plain language (Wat we doen / Wat we vragen / Hoe we
werken; URLs unchanged), restructured all four content pages onto the Steun-ons
pattern (fewer content types, copy in lang/nl/about.php), unified impact stats
behind App\Support\AboutStats + a curated volunteers figure on Jaarcijfers
(closes D-13), chained the closing CTAs through the section, bound the Partners
cards to the partners table via a new category field (advances D-11), added
draft state + publish date + rich text to news articles, imported the 20-item
historic press archive from the Wix scrape, and re-hosted the manifest +
persbericht PDFs off Wix (closes D-7). Spec:
docs/superpowers/specs/2026-07-03-about-section-content-design.md.
```

- [ ] **Step 5: Verify the dashboard parses, commit**

Run: `php artisan tinker --execute '$r = app(\App\Support\Build\BuildStatus::class)->report(); echo count($r["warnings"] ?? []), " warnings";'`
Expected: `0 warnings`

```bash
git add docs/wiki/design/30-skeleton/00-page-registry.md docs/wiki/log.md
git commit -m "docs(pipeline): about-section content pass status updates"
```

---

## Self-review notes (already applied)

- Spec coverage: items 1-13 of the spec map to Tasks 1-13 (spec item 3 "rename" is split across Tasks 5-8 since each rebuilt page carries its own title; spec item 12 "lang file" is distributed across Tasks 4, 6, 7, 8, 9).
- The Missie stats deck and hub band share `data-stats-source="about-stats"` as their test seam — assert the seam, not markup.
- Type consistency: `AboutStats::cards()` shape matches `SupportStats::cards()` and `<x-stat-card>` props (`value`, `label`, `color` ∈ blue|green|red). `PartnerCategory` matches `ActivityType`'s API (`label()`, `getOptionsArray()`).
- Out of scope, discovered during prep, to report to Nico (do NOT fix in this plan): `app/Models/Scopes/LocalGroupScope.php:17` contains a stray `$builder->ray()` debug call that runs on every scoped query.

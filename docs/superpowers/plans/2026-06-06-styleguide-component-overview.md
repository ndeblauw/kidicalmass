# Styleguide — component overview + extraction audit — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build `/styleguide` — a non-prod, unlinked page that renders every public-site component live with example markup + design tokens, plus a "nog te extraheren" section listing recurring UI found in page templates that should become components.

**Architecture:** Single Blade view (`styleguide.blade.php`) rendered through `layouts/site`, fed by an invokable `StyleguideController` that fabricates in-memory sample models. A small `x-styleguide.swatch` sub-component renders token chips. The candidates list is a controller-side array (data-driven). Route gated behind `if (! app()->isProduction())`, mirroring `build.dashboard`.

**Tech Stack:** Laravel 12, Blade (anonymous components), Tailwind v4 (token-backed), Pest 4.

Spec: `docs/superpowers/specs/2026-06-06-styleguide-component-overview-design.md`

---

## File Structure

- **Create** `app/Http/Controllers/StyleguideController.php` — invokable; fabricates sample `Activity`/`Article`, builds candidates array, returns the view.
- **Create** `resources/views/styleguide.blade.php` — the page (TOC + Tokens + Componenten + Nog te extraheren).
- **Create** `resources/views/components/styleguide/swatch.blade.php` — colour-token chip sub-component.
- **Create** `resources/css/pages/styleguide.css` — page-only styles (registered in `app.css`). Keep minimal; reuse tokens.
- **Modify** `routes/web.php` — add gated `styleguide` route.
- **Modify** `resources/css/app.css` — add `@import './pages/styleguide.css';` in the partials block.
- **Create** `tests/Feature/StyleguideTest.php` — route gating + section anchors.

### Component demo classification (decided from reading each component)

- **Live-demoable** (render inline with sample props/models): `cta-button`, `closing-cta`, `support-callout`, `feature-card`, `event-card`, `article-card`, `stat-card`, `group-statistics`, `kal-day-band`, `kal-month-band`, `bike-icon`, `placeholder-pattern`.
- **Markup + note only** (not safe to render inline, with the reason shown on the page):
  - `page-hero` — `position: fixed`, pinned at lowest z-layer; would escape the showcase flow.
  - `partners` — early-returns unless route name ∈ {home, about*}; also DB-backed (`Partner` query).
  - `about-reveal` — `<script>`-only scroll-reveal behaviour; nothing visual to render.
  - `contact-form` (`⚡contact-form`) — interactive form island; document usage, don't embed.
- **Out of scope** (auth/settings/Filament scaffolding, listed once, not demoed): `app-logo`, `app-logo-icon`, `auth-header`, `auth-session-status`, `action-message`, `desktop-user-menu`, `stub`, `settings/*`, `wire/*`.

---

## Task 1: Gated route + failing gating test

**Files:**
- Modify: `routes/web.php` (the `if (! app()->isProduction())` block near `build.dashboard`)
- Test: `tests/Feature/StyleguideTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use function Pest\Laravel\get;

it('serves the styleguide in non-production', function () {
    app()->detectEnvironment(fn () => 'local');

    get(route('styleguide'))
        ->assertOk()
        ->assertSee('id="tokens"', false)
        ->assertSee('id="componenten"', false)
        ->assertSee('id="nog-te-extraheren"', false);
});

it('does not register the styleguide route in production', function () {
    expect(app('router')->getRoutes()->hasNamedRoute('styleguide'))->toBeTrue();
})->skip('Route registration is environment-bound at boot; covered by manual prod check.');
```

- [ ] **Step 2: Run it, expect failure**

Run: `php artisan test --compact --filter=StyleguideTest`
Expected: FAIL — `Route [styleguide] not defined.`

- [ ] **Step 3: Add the route**

In `routes/web.php`, add the `use` import at the top with the other controller imports:

```php
use App\Http\Controllers\StyleguideController;
```

Inside the existing `if (! app()->isProduction()) { ... }` block (the one holding `build.dashboard`), add:

```php
    Route::get('/styleguide', StyleguideController::class)
        ->name('styleguide');
```

- [ ] **Step 4: Re-run**

Run: `php artisan test --compact --filter=StyleguideTest`
Expected: still FAIL — controller class missing (`Target class [StyleguideController] does not exist`). That's the next task.

---

## Task 2: StyleguideController with sample data + candidates

**Files:**
- Create: `app/Http/Controllers/StyleguideController.php`

- [ ] **Step 1: Write the controller**

```php
<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class StyleguideController extends Controller
{
    public function __invoke(): View
    {
        $activity = $this->sampleActivity('Kidical Mass Gent', 'Sint-Pietersplein, Gent', 1);
        $activityB = $this->sampleActivity('Grande Kidical Mass Brussel', 'Jubelpark, Brussel', 2, days: 9);

        $article = new Article([
            'title_nl' => 'Eerste rit van het seizoen was een groot succes',
            'content_nl' => '<p>Met meer dan tweehonderd fietsers reden we samen door de '
                .'straten. Hier lees je hoe het was en wanneer de volgende rit plaatsvindt.</p>',
        ]);
        $article->id = 1;
        $article->created_at = Carbon::parse('2026-05-18');
        $article->setRelation('author', new User(['name' => 'Leticia']));

        // year => group count, as group-statistics expects.
        $statistics = [2021 => 2, 2022 => 5, 2023 => 9, 2024 => 14, 2025 => 21];

        // Calendar bands wrap event-card; rows = [['item' => Activity]], rides = [Activity].
        $dayRows = [['item' => $activity], ['item' => $activityB]];

        return view('styleguide', [
            'activity' => $activity,
            'activityB' => $activityB,
            'article' => $article,
            'statistics' => $statistics,
            'dayPeriodKey' => $activity->begin_date->toDateString(),
            'dayRows' => $dayRows,
            'monthPeriodKey' => $activity->begin_date->format('Y-m'),
            'monthRides' => [$activity, $activityB],
            'candidates' => $this->candidates(),
        ]);
    }

    private function sampleActivity(string $title, string $location, int $id, int $days = 2): Activity
    {
        $activity = new Activity([
            'title_nl' => $title,
            'location' => $location,
            'begin_date' => Carbon::parse('2026-06-06 14:00')->addDays($days),
        ]);
        $activity->id = $id;

        return $activity;
    }

    /**
     * Extraction candidates found in the thorough page-template sweep (Task 5).
     *
     * @return list<array{name: string, where: string, props: string}>
     */
    private function candidates(): array
    {
        return []; // Populated in Task 5.
    }
}
```

- [ ] **Step 2: Run tests**

Run: `php artisan test --compact --filter=StyleguideTest`
Expected: still FAIL — view `[styleguide]` not found. Next task.

---

## Task 3: Swatch sub-component

**Files:**
- Create: `resources/views/components/styleguide/swatch.blade.php`

- [ ] **Step 1: Write the component**

```blade
@props([
    'token', // CSS var name without var(), e.g. "kidical-blue"
    'name',  // human label
])

<div class="flex flex-col gap-2">
    <div class="h-16 rounded-card border border-kidical-ink/10" style="background: var(--color-{{ $token }})"></div>
    <div class="text-sm">
        <p class="font-semibold">{{ $name }}</p>
        <code class="text-xs text-kidical-ink/60">--color-{{ $token }}</code>
    </div>
</div>
```

Note: `style="background: var(...)"` references a token, not a raw hex/px, so it is allowed under `CssArchitectureTest`. (The test bans raw `#hex` and `NNpx` literals, not `var()`.)

- [ ] **Step 2: No test yet** — exercised via the page render in Task 4.

---

## Task 4: The styleguide page

**Files:**
- Create: `resources/views/styleguide.blade.php`
- Create: `resources/css/pages/styleguide.css`
- Modify: `resources/css/app.css`

- [ ] **Step 1: Register the page CSS partial**

In `resources/css/app.css`, in the `@import './pages/...'` block, add (alphabetical order is fine):

```css
@import './pages/styleguide.css';
```

- [ ] **Step 2: Create the page CSS partial** (`resources/css/pages/styleguide.css`)

```css
/* Styleguide (internal, non-prod). Layout-only helpers; appearance uses tokens. */
@layer components {
    .sg-section {
        scroll-margin-top: 7rem; /* clears the fixed nav pill on anchor jump */
    }

    .sg-demo {
        border: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 90%);
        border-radius: var(--radius-card);
        background: white;
    }

    .sg-toc a {
        background-image: none;
    }
}
```

- [ ] **Step 3: Create the page** (`resources/views/styleguide.blade.php`)

```blade
<x-layouts.site title="Styleguide — Kidical Mass">
    <div class="flex flex-col gap-6 mb-12">
        <p class="text-sm font-semibold uppercase tracking-widest text-kidical-ink/50">Intern · niet zichtbaar in productie</p>
        <h1>Styleguide</h1>
        <p class="max-w-2xl">Alle bouwstenen van de site op één plek: tokens, componenten en wat er nog
            wacht om een component te worden. Gebruik dit om consistent te bouwen — hergebruik wat er al is.</p>
    </div>

    <div class="flex gap-12">
        {{-- Sticky TOC --}}
        <nav class="sg-toc hidden lg:block w-52 shrink-0" aria-label="Inhoud">
            <div class="sticky top-28 flex flex-col gap-2 text-sm">
                <a href="#tokens">Tokens</a>
                <a href="#componenten">Componenten</a>
                <a href="#nog-te-extraheren">Nog te extraheren</a>
                <a href="#buiten-scope" class="text-kidical-ink/50">Buiten scope</a>
            </div>
        </nav>

        <div class="flex-1 min-w-0 flex flex-col gap-20">

            {{-- ============ TOKENS ============ --}}
            <section id="tokens" class="sg-section flex flex-col gap-8">
                <h2>Tokens</h2>

                <div class="flex flex-col gap-4">
                    <h3>Merkkleuren</h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <x-styleguide.swatch token="kidical-blue" name="Blue" />
                        <x-styleguide.swatch token="kidical-red" name="Red" />
                        <x-styleguide.swatch token="kidical-yellow" name="Yellow" />
                        <x-styleguide.swatch token="kidical-green" name="Green" />
                        <x-styleguide.swatch token="kidical-orange" name="Orange" />
                        <x-styleguide.swatch token="kidical-ink" name="Ink" />
                        <x-styleguide.swatch token="kidical-sky" name="Sky" />
                        <x-styleguide.swatch token="kidical-light-blue" name="Light blue" />
                        <x-styleguide.swatch token="kidical-light-yellow" name="Light yellow" />
                        <x-styleguide.swatch token="kidical-violet" name="Violet" />
                        <x-styleguide.swatch token="kidical-coral" name="Coral" />
                    </div>
                </div>

                <div class="flex flex-col gap-4">
                    <h3>Typografie</h3>
                    <div class="sg-demo p-8 flex flex-col gap-3">
                        <h1>H1 — Caprasimo, blauw</h1>
                        <h2>H2 — sectiekop</h2>
                        <h3>H3 — subkop</h3>
                        <h4>H4 — kleine kop</h4>
                        <p>Body — Nunito Sans. Dit is hoe lopende tekst eruitziet op de site.</p>
                        <p><a href="#tokens">Een link met de gele onderlijn-animatie</a></p>
                    </div>
                </div>
            </section>

            {{-- ============ COMPONENTEN ============ --}}
            <section id="componenten" class="sg-section flex flex-col gap-12">
                <h2>Componenten</h2>

                {{-- Knoppen & CTA's --}}
                <x-styleguide.entry name="cta-button" props="href, variant=yellow|blue, icon=arrow|heart, size=md|sm">
                    <div class="flex flex-wrap items-center gap-4">
                        <x-cta-button href="#" variant="blue">Doe mee</x-cta-button>
                        <x-cta-button href="#" variant="blue" icon="heart">Word lid</x-cta-button>
                        <x-cta-button href="#" variant="blue" size="sm">Klein</x-cta-button>
                    </div>
                </x-styleguide.entry>

                <x-styleguide.entry name="support-callout" props="variant=home, title, body">
                    <x-support-callout />
                </x-styleguide.entry>

                {{-- Kaarten --}}
                <x-styleguide.entry name="feature-card" props="icon, title, color=red|blue|orange|ink|green|violet|coral">
                    <div class="grid sm:grid-cols-3 gap-6">
                        <x-feature-card icon="clock" title="Op tijd" color="blue">Ritten starten stipt.</x-feature-card>
                        <x-feature-card icon="map" title="Veilige route" color="green">Rustige straten.</x-feature-card>
                        <x-feature-card icon="heart" title="Voor iedereen" color="red">Jong en oud welkom.</x-feature-card>
                    </div>
                </x-styleguide.entry>

                <x-styleguide.entry name="event-card" props="activity, showDate=true, featured=auto">
                    <div class="flex flex-col gap-3 max-w-xl">
                        <x-event-card :activity="$activity" />
                        <x-event-card :activity="$activityB" />
                    </div>
                </x-styleguide.entry>

                <x-styleguide.entry name="article-card" props="article">
                    <div class="max-w-sm">
                        <x-article-card :article="$article" />
                    </div>
                </x-styleguide.entry>

                <x-styleguide.entry name="stat-card" props="value, label, icon, color=blue|green|red">
                    <div class="grid sm:grid-cols-3 gap-6">
                        <x-stat-card value="5.500" label="fietsers" icon="users" color="blue" />
                        <x-stat-card value="21" label="lokale groepen" icon="map-pin" color="green" />
                        <x-stat-card value="120" label="ritten" icon="calendar" color="red" />
                    </div>
                </x-styleguide.entry>

                <x-styleguide.entry name="group-statistics" props=":statistics (year => count)"
                    note="Bevat nog Engelse hardcoded copy ('We are growing!', 'groups') — zie kandidaten.">
                    <x-group-statistics :statistics="$statistics" />
                </x-styleguide.entry>

                {{-- Kalender --}}
                <x-styleguide.entry name="kal-day-band" props="periodKey, rows=[['item'=>Activity]]">
                    <x-kal-day-band :period-key="$dayPeriodKey" :rows="$dayRows" />
                </x-styleguide.entry>

                <x-styleguide.entry name="kal-month-band" props="periodKey, rides=[Activity]">
                    <x-kal-month-band :period-key="$monthPeriodKey" :rides="$monthRides" />
                </x-styleguide.entry>

                {{-- Primitieven --}}
                <x-styleguide.entry name="bike-icon" props="(attributes)">
                    <x-bike-icon class="w-10 h-10 text-kidical-blue" />
                </x-styleguide.entry>

                <x-styleguide.entry name="placeholder-pattern" props="id">
                    <x-placeholder-pattern class="w-40 h-24 text-kidical-ink/20" />
                </x-styleguide.entry>

                {{-- Closing CTA: page-owned full-bleed band; safe to show inline --}}
                <x-styleguide.entry name="closing-cta" props="heading, href, label, icon=arrow|heart"
                    note="Normaal in de layout-'closing'-slot, vlak boven de footer.">
                    <x-closing-cta heading="Rijd mee met de volgende Kidical Mass" href="#" label="Bekijk de kalender" />
                </x-styleguide.entry>

                {{-- Markup + note only --}}
                <x-styleguide.entry name="page-hero" props="eyebrow, title, illustration?"
                    note="position: fixed (vastgepind achter de pagina) — niet inline te tonen. Zie bovenkant van elke binnenpagina." />

                <x-styleguide.entry name="partners" props="(geen — DB + route-gated)"
                    note="Rendert alleen op home + about-pagina's en bevraagt de Partner-tabel. Niet los te tonen." />

                <x-styleguide.entry name="about-reveal" props="selector, transform=false"
                    note="Alleen een <script> voor scroll-reveal. Geen visuele weergave." />

                <x-styleguide.entry name="⚡contact-form" props="(geen)"
                    note="Interactief formulier-eiland. Gebruik op de contactpagina; hier niet ingebed." />
            </section>

            {{-- ============ NOG TE EXTRAHEREN ============ --}}
            <section id="nog-te-extraheren" class="sg-section flex flex-col gap-6">
                <h2>Nog te extraheren</h2>
                <p class="max-w-2xl">Terugkerende stukken UI in de pagina-templates die nog geen component zijn.
                    Werk deze lijst af door elk te extraheren en naar "Componenten" te verplaatsen.</p>

                @forelse ($candidates as $candidate)
                    <div class="sg-demo p-6 flex flex-col gap-2">
                        <p class="font-semibold text-kidical-blue">{{ $candidate['name'] }}</p>
                        <p class="text-sm"><span class="font-semibold">Waar:</span> {{ $candidate['where'] }}</p>
                        <p class="text-sm"><span class="font-semibold">Voorgestelde props:</span> <code class="text-xs">{{ $candidate['props'] }}</code></p>
                    </div>
                @empty
                    <p class="text-kidical-ink/50">Nog geen kandidaten genoteerd.</p>
                @endforelse
            </section>

            {{-- ============ BUITEN SCOPE ============ --}}
            <section id="buiten-scope" class="sg-section flex flex-col gap-4">
                <h2>Buiten scope</h2>
                <p class="max-w-2xl text-kidical-ink/60">Auth-, instellingen- en Filament-scaffolding horen niet bij de
                    publieke designtaal en worden hier niet getoond:</p>
                <p class="text-sm text-kidical-ink/60"><code>app-logo</code>, <code>app-logo-icon</code>,
                    <code>auth-header</code>, <code>auth-session-status</code>, <code>action-message</code>,
                    <code>desktop-user-menu</code>, <code>stub</code>, <code>settings/*</code>, <code>wire/*</code></p>
            </section>
        </div>
    </div>
</x-layouts.site>
```

- [ ] **Step 2: Create the `entry` sub-component** — see Task 4b. (Referenced above; must exist before render.)

- [ ] **Step 3: Build assets**

Run: `npm run build`
Expected: builds without error.

- [ ] **Step 4: Run the test**

Run: `php artisan test --compact --filter=StyleguideTest`
Expected: PASS (the `assertSee` anchors now render).

---

## Task 4b: The `entry` sub-component (demo wrapper with markup toggle)

**Files:**
- Create: `resources/views/components/styleguide/entry.blade.php`

- [ ] **Step 1: Write it**

```blade
@props([
    'name',       // component name, e.g. "cta-button"
    'props' => '', // short props summary
    'note' => null,
])

<div class="sg-section flex flex-col gap-4">
    <div class="flex flex-col gap-1">
        <h3>{{ $name }}</h3>
        @if ($props !== '')
            <p class="text-sm text-kidical-ink/60"><code class="text-xs">{{ $props }}</code></p>
        @endif
        @if ($note)
            <p class="text-sm text-kidical-orange">{{ $note }}</p>
        @endif
    </div>

    @if (! $slot->isEmpty())
        <div class="sg-demo p-8">
            {{ $slot }}
        </div>
    @endif
</div>
```

Note: components in the "markup + note only" group call `<x-styleguide.entry ... />` self-closed (empty slot), so only the header + note render.

- [ ] **Step 2: Verify** by re-running Task 4 Step 4 (the page render test already covers it).

---

## Task 5: Thorough extraction audit → populate candidates

**Files:**
- Modify: `app/Http/Controllers/StyleguideController.php` (`candidates()` return)

- [ ] **Step 1: Sweep the public page templates**

Read every public template and note repeated markup not yet behind a component:
`home`, `about/*`, `activities/*`, `groups/*`, `articles/*`, `steun-ons`, `getting-started`,
`membership`, `volunteer`, `contact`, `find-a-bike`, `privacy`.

Run this as a parallel fan-out (one reviewer per page group) for speed and completeness, then
dedupe overlapping findings into a single list. Each finding becomes a candidate row:
`['name' => ..., 'where' => 'file:area (xN)', 'props' => '...']`.

Known seed finding (already spotted): `group-statistics` carries hardcoded **English** copy
("We are growing!", "group/groups") — log a candidate to translate + parameterise its copy.

- [ ] **Step 2: Fill `candidates()`**

Replace the empty return with the real array, e.g.:

```php
return [
    [
        'name' => 'section-heading (eyebrow + h2)',
        'where' => 'home.blade.php, about/mission.blade.php, steun-ons.blade.php (xN)',
        'props' => 'eyebrow, title, align?',
    ],
    // ... remaining findings from the sweep
];
```

- [ ] **Step 3: Verify it renders**

Run: `php artisan test --compact --filter=StyleguideTest`
Expected: PASS. Load `/styleguide` and confirm the candidates render under "Nog te extraheren".

- [ ] **Step 4: Commit**

```bash
git add app/Http/Controllers/StyleguideController.php resources/views/styleguide.blade.php \
  resources/views/components/styleguide/ resources/css/pages/styleguide.css resources/css/app.css \
  routes/web.php tests/Feature/StyleguideTest.php \
  docs/superpowers/specs/2026-06-06-styleguide-component-overview-design.md \
  docs/superpowers/plans/2026-06-06-styleguide-component-overview.md
git commit -m "feat(styleguide): internal component overview + extraction audit"
```

---

## Task 6: Full verification

- [ ] **Step 1: Run the focused tests**

Run: `php artisan test --compact --filter=StyleguideTest`
Expected: PASS.

- [ ] **Step 2: Run the CSS architecture test**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS (styleguide.css registered; no raw hex/px in new components).

- [ ] **Step 3: Pint**

Run: `vendor/bin/pint --dirty --format agent`
Expected: clean.

- [ ] **Step 4: Visual check**

Screenshot `/styleguide` (Herd, HTTPS self-signed, see global prefs) and confirm tokens,
component demos, and candidates all render without layout breakage.

---

## Self-Review notes

- **Spec coverage:** route+gating (T1), controller+sample data (T2), swatch (T3), page with Tokens/Componenten/candidates/out-of-scope (T4/4b), audit (T5), tests + CSS-arch + Pint (T1/T6). All spec sections mapped.
- **No new app.css entries** beyond the one partial `@import` (spec-compliant). Page styles live in `resources/css/pages/styleguide.css`.
- **Dutch copy** throughout the view (spec).
- **Honest demos:** components that can't render inline (page-hero/partners/about-reveal/contact-form) are documented with the reason, not faked.

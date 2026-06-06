# Home (P-01) UX Rebuild — Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the stale English wireframe homepage with the NL "emotional pitch wrapping a dispatcher spine" design from the spec — video hero, one location-aware next-ride, three dispatcher routes, a quiet support beat, closing CTA.

**Architecture:** A new testable `NextRideFinder` support class computes the single soonest ride (preferring within-radius, falling back to soonest-anywhere) from the `kcm_location` cookie, reusing the existing `Proximity` + `CurrentLocation` + `PostalCode` machinery. `HomeController` becomes thin: resolve location → ask the finder → pass four flags to a rewritten `home.blade.php`. The view reuses existing islands/components (`<livewire:location-picker>`, `<x-event-card>`, `<x-support-callout>`, `<x-closing-cta>`, `<x-cta-button>`); only the hero markup + minimal home CSS are new.

**Tech Stack:** Laravel 12, Livewire 4, Blade, Tailwind v4 (`app.css` tokens), Pest 4. Reference spec: `docs/superpowers/specs/2026-06-06-home-ux-design.md`.

---

## File structure

- **Create** `app/Support/Location/NextRideFinder.php` — pure logic: location → next ride + distance + flags. One responsibility, unit-tested.
- **Create** `tests/Unit/Location/NextRideFinderTest.php` — unit tests for the finder.
- **Modify** `app/Http/Controllers/HomeController.php` — drop the news/groups/upcoming-list queries; call the finder.
- **Modify** `resources/views/home.blade.php` — full rewrite to the 5-section skeleton.
- **Modify** `resources/css/app.css` — replace the `.home-hero` block; add `.home-nextride` placement helpers.
- **Modify** `tests/Feature/PublicStructureTest.php` — update the stale "New here?" assertion to NL.
- **Create** `tests/Feature/HomePageTest.php` — feature tests for the four next-ride states + hero + dispatcher.

Route names used (do not rename — IA paths, legacy route names per project convention): `activities.index`, `getting-started`, `volunteer` (Help out), `groups.index`, `membership` (→ /steun-ons).

---

## Task 1: `NextRideFinder` support class

**Files:**
- Create: `app/Support/Location/NextRideFinder.php`
- Test: `tests/Unit/Location/NextRideFinderTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;
use App\Support\Location\NextRideFinder;

beforeEach(function () {
    PostalCode::insert([
        ['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

it('reports no upcoming rides when the calendar is empty', function () {
    $result = NextRideFinder::find(null);

    expect($result['has_upcoming'])->toBeFalse()
        ->and($result['ride'])->toBeNull();
});

it('returns no ride (picker state) when upcoming rides exist but no location is set', function () {
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'postal_code' => '1090',
        'begin_date' => now()->addDays(3),
    ]);

    $result = NextRideFinder::find(null);

    expect($result['has_upcoming'])->toBeTrue()
        ->and($result['ride'])->toBeNull();
});

it('picks the soonest ride within the radius and marks it not far', function () {
    $near = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Jette',
        'postal_code' => '1090',
        'begin_date' => now()->addDays(5),
    ]);
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Gent',
        'postal_code' => '9000',
        'begin_date' => now()->addDays(2),
    ]);

    $result = NextRideFinder::find(['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette']);

    expect($result['ride']->is($near))->toBeTrue()
        ->and($result['is_far'])->toBeFalse()
        ->and($result['distance_km'])->toBe(0.0);
});

it('falls back to the soonest ride anywhere and marks it far when nothing is in range', function () {
    $far = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Gent',
        'postal_code' => '9000',
        'begin_date' => now()->addDays(4),
    ]);

    $result = NextRideFinder::find(['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette']);

    expect($result['ride']->is($far))->toBeTrue()
        ->and($result['is_far'])->toBeTrue()
        ->and($result['distance_km'])->toBeGreaterThan(7.0);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=NextRideFinder`
Expected: FAIL — `Class "App\Support\Location\NextRideFinder" not found`.

- [ ] **Step 3: Write minimal implementation**

```php
<?php

namespace App\Support\Location;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;

class NextRideFinder
{
    /**
     * Resolve the single ride to feature on the homepage's "De volgende rit bij jou".
     * Prefers the soonest ride within the nearby radius; otherwise the soonest ride
     * anywhere (flagged far). Returns ride=null when no location is set (picker state)
     * or when there are no upcoming rides at all (off-season).
     *
     * @param  array{zip: string, lat: float, lng: float, name: string}|null  $location
     * @return array{ride: Activity|null, distance_km: float|null, is_far: bool, has_upcoming: bool}
     */
    public static function find(?array $location): array
    {
        $upcoming = Activity::query()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->where('begin_date', '>=', now()->startOfDay())
            ->orderBy('begin_date')
            ->with('groups')
            ->get();

        if ($upcoming->isEmpty()) {
            return ['ride' => null, 'distance_km' => null, 'is_far' => false, 'has_upcoming' => false];
        }

        if (! $location) {
            return ['ride' => null, 'distance_km' => null, 'is_far' => false, 'has_upcoming' => true];
        }

        $coordsByZip = PostalCode::whereIn('zip', $upcoming->pluck('postal_code')->filter()->unique())
            ->get()->keyBy('zip');

        $partition = Proximity::partitionByRadius(
            $upcoming,
            ['lat' => $location['lat'], 'lng' => $location['lng']],
            (float) config('location.nearby_radius_km'),
            function (Activity $activity) use ($coordsByZip) {
                $pc = $activity->postal_code ? $coordsByZip->get($activity->postal_code) : null;

                return $pc ? ['lat' => $pc->latitude, 'lng' => $pc->longitude] : null;
            },
        );

        $chosen = $partition['nearby']->first() ?? $partition['far']->first();

        return [
            'ride' => $chosen['item'],
            'distance_km' => $chosen['distance_km'],
            'is_far' => $partition['nearby']->isEmpty(),
            'has_upcoming' => true,
        ];
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=NextRideFinder`
Expected: PASS (4 passed).

- [ ] **Step 5: Commit**

```bash
git add app/Support/Location/NextRideFinder.php tests/Unit/Location/NextRideFinderTest.php
git commit -m "feat(home): add NextRideFinder for the homepage next-ride section"
```

---

## Task 2: Thin out `HomeController`

**Files:**
- Modify: `app/Http/Controllers/HomeController.php`

- [ ] **Step 1: Replace the controller body**

Replace the entire file with:

```php
<?php

namespace App\Http\Controllers;

use App\Support\Location\CurrentLocation;
use App\Support\Location\NextRideFinder;
use Illuminate\View\View;

class HomeController extends Controller
{
    /** @param string $locale Supplied by the {locale} route prefix (set via SetLocale middleware); kept first for route-model binding order. */
    public function __invoke(string $locale): View
    {
        $location = CurrentLocation::resolve();
        $next = NextRideFinder::find($location);

        return view('home', [
            'hasLocation' => $location !== null,
            'nextRide' => $next['ride'],
            'nextRideDistanceKm' => $next['distance_km'],
            'nextRideIsFar' => $next['is_far'],
            'hasUpcoming' => $next['has_upcoming'],
        ]);
    }
}
```

- [ ] **Step 2: Run the home route smoke test**

Run: `php artisan test --compact --filter=PublicStructureTest`
Expected: the "New here?" test FAILS (still asserts old English copy — fixed in Task 4); all other home assertions still pass (support callout, partner strip). This confirms the controller still renders `home`.

- [ ] **Step 3: Commit**

```bash
git add app/Http/Controllers/HomeController.php
git commit -m "refactor(home): drop duplicated news/groups/list queries from HomeController"
```

---

## Task 3: Rewrite the home view (hero + dispatcher + support + closing)

**Files:**
- Modify: `resources/views/home.blade.php`

This task writes the full new view. The next-ride states are covered by tests in Task 4; the hero/dispatcher are covered here.

- [ ] **Step 1: Replace the entire view**

```blade
<x-layouts::site title="Kidical Mass Belgium">
    {{-- ① HERO — video-led emotional pitch. Joy is the argument ("is het de moeite waard?"). --}}
    <section class="home-hero">
        <div class="home-hero__video" aria-hidden="true">
            <iframe
                src="https://www.youtube.com/embed/VXiIgU9vI-4?autoplay=1&mute=1&loop=1&playlist=VXiIgU9vI-4&controls=0&showinfo=0&modestbranding=1&playsinline=1&rel=0"
                title="" tabindex="-1" frameborder="0"
                allow="autoplay; encrypted-media; picture-in-picture"
            ></iframe>
        </div>

        <div class="home-hero__inner">
            <h1 class="home-hero__title">Het leukste uur op de fiets, door autovrije straten.</h1>
            <p class="home-hero__lead">
                Een vrolijke gezinsfietstocht door autovrije straten, bij jou in de buurt.
                Samen laten we zien dat de straat ook van kinderen is.
            </p>
            <div class="home-hero__actions">
                <x-cta-button :href="route('activities.index')" class="link-plain">Vind een rit in de buurt</x-cta-button>
                <a href="{{ route('getting-started') }}" class="home-hero__secondary link-plain">Nieuw hier? Zo werkt het →</a>
            </div>
        </div>
    </section>

    <div class="space-y-20">
        {{-- ② DE VOLGENDE RIT BIJ JOU — one location-aware ride (proof + utility). --}}
        <section class="home-nextride space-y-6 scroll-mt-24" id="volgende-rit">
            <div class="flex items-baseline justify-between gap-4">
                <h2 class="text-kidical-ink">De volgende rit bij jou</h2>
                <a href="{{ route('activities.index') }}" class="shrink-0 font-bold text-kidical-blue hover:underline">Bekijk alle ritten →</a>
            </div>

            @if (! $hasUpcoming)
                <p class="text-kidical-ink/70">
                    Het fietsseizoen loopt van maart tot november.
                    <a href="{{ route('getting-started') }}" class="font-bold text-kidical-blue hover:underline">Ontdek hoe een rit werkt →</a>
                </p>
            @elseif (! $hasLocation)
                <livewire:location-picker />
            @else
                @if ($nextRideIsFar)
                    <p class="text-kidical-ink/70">Geen rit vlakbij op dit moment. De eerstvolgende iets verderaf:</p>
                @endif

                <div class="event-list">
                    <x-event-card :activity="$nextRide" />
                </div>

                @if ($nextRideDistanceKm !== null)
                    <p class="home-nextride__distance text-sm font-semibold text-kidical-ink/60">
                        {{ str_replace('.', ',', (string) $nextRideDistanceKm) }} km van jou
                    </p>
                @endif

                <livewire:location-picker />
            @endif
        </section>

        {{-- ③ DISPATCHER — three equal routes. Home is a crossroads, not a content dump. --}}
        <section class="home-routes grid gap-5 sm:grid-cols-3">
            <a href="{{ route('getting-started') }}" class="home-route link-plain">
                <span class="home-route__title">Nieuw hier?</span>
                <span class="home-route__desc">Zo werkt een Kidical Mass rit.</span>
            </a>
            <a href="{{ route('volunteer') }}" class="home-route link-plain">
                <span class="home-route__title">Help mee</span>
                <span class="home-route__desc">Word vrijwilliger bij een rit.</span>
            </a>
            <a href="{{ route('groups.index') }}" class="home-route link-plain">
                <span class="home-route__title">Vind je lokale groep</span>
                <span class="home-route__desc">Ontdek de groep bij jou in de buurt.</span>
            </a>
        </section>

        {{-- ④ Quiet support beat (reuses the tested home callout). --}}
        <x-support-callout variant="home" />
    </div>

    <x-slot:closing>
        <x-closing-cta heading="Klaar voor je eerste rit?"
            :href="route('activities.index')" label="Vind een rit" />
    </x-slot:closing>
</x-layouts::site>
```

- [ ] **Step 2: Build assets so the new markup renders**

Run: `npm run build`
Expected: builds without error (Vite manifest updated).

- [ ] **Step 3: Commit**

```bash
git add resources/views/home.blade.php
git commit -m "feat(home): rebuild homepage view (NL video hero, dispatcher, next-ride states)"
```

---

## Task 4: Feature tests for the four next-ride states + hero + fix stale assertion

**Files:**
- Create: `tests/Feature/HomePageTest.php`
- Modify: `tests/Feature/PublicStructureTest.php:160-165`

- [ ] **Step 1: Write the feature tests**

Create `tests/Feature/HomePageTest.php`:

```php
<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;

use function Pest\Laravel\get;
use function Pest\Laravel\withCookie;

beforeEach(function () {
    PostalCode::insert([
        ['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

it('renders the NL video hero and drops the old English copy', function () {
    get('/nl')
        ->assertOk()
        ->assertSee('Het leukste uur op de fiets, door autovrije straten.')
        ->assertSee('de straat ook van kinderen is')
        ->assertSee('youtube.com/embed/VXiIgU9vI-4', escape: false)
        ->assertDontSee('Kids on bikes')
        ->assertDontSee('—');
});

it('shows the three dispatcher routes pointing at the right pages', function () {
    get('/nl')
        ->assertOk()
        ->assertSee('Nieuw hier?')
        ->assertSee('Help mee')
        ->assertSee('Vind je lokale groep')
        ->assertSee(route('getting-started'), escape: false)
        ->assertSee(route('volunteer'), escape: false)
        ->assertSee(route('groups.index'), escape: false);
});

it('shows the off-season message when there are no upcoming rides', function () {
    get('/nl')
        ->assertOk()
        ->assertSee('Het fietsseizoen loopt van maart tot november.');
});

it('shows the location picker in the next-ride section when no location is set', function () {
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'postal_code' => '1090',
        'begin_date' => now()->addDays(3),
    ]);

    get('/nl')
        ->assertOk()
        ->assertSee('De volgende rit bij jou')
        ->assertSee('Waar wil je fietsen?')
        ->assertDontSee('km van jou');
});

it('shows the nearest upcoming ride with a distance label when a location is set', function () {
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Jette',
        'location' => 'Josaphatpark',
        'postal_code' => '1090',
        'begin_date' => now()->addDays(3),
    ]);

    withCookie('kcm_location', json_encode(['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette']))
        ->get('/nl')
        ->assertOk()
        ->assertSee('Jette')
        ->assertSee('km van jou')
        ->assertSee('Je fietst rond');
});

it('flags a far fallback ride when nothing is in range', function () {
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Gent',
        'postal_code' => '9000',
        'begin_date' => now()->addDays(3),
    ]);

    withCookie('kcm_location', json_encode(['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette']))
        ->get('/nl')
        ->assertOk()
        ->assertSee('iets verderaf')
        ->assertSee('Gent');
});
```

- [ ] **Step 2: Fix the stale assertion in `PublicStructureTest`**

Replace lines 160-165 (the `it('routes the home "New here?" entry link to Getting Started', ...)` test) with:

```php
it('routes the home "New here?" entry link to Getting Started', function () {
    get('/nl')
        ->assertOk()
        ->assertSee(route('getting-started'), escape: false)
        ->assertSee('Nieuw hier? Zo werkt het', escape: false);
});
```

- [ ] **Step 3: Run the home tests**

Run: `php artisan test --compact --filter="HomePageTest|PublicStructureTest"`
Expected: PASS (all home tests green, including the updated "New here?" assertion).

- [ ] **Step 4: Commit**

```bash
git add tests/Feature/HomePageTest.php tests/Feature/PublicStructureTest.php
git commit -m "test(home): cover hero, dispatcher, and the four next-ride states"
```

---

## Task 5: Home CSS — video hero cover + section placement

**Files:**
- Modify: `resources/css/app.css`

Wire-fidelity styling so the hero video actually covers and the layout reads. Full Surface polish (motion, exact type scale) is a later pass per the spec.

- [ ] **Step 1: Locate the existing `.home-hero` rule**

Run: `grep -n "home-hero" resources/css/app.css`
Expected: one or more existing `.home-hero` declarations (from the old hero). Note the line range of that block.

- [ ] **Step 2: Replace the existing `.home-hero` block with the new home styles**

Delete the existing `.home-hero { ... }` block found in Step 1 and insert this in its place:

```css
/* Home — video hero (cover the section, overlay the pitch) */
.home-hero {
    position: relative;
    isolation: isolate;
    overflow: hidden;
    min-height: 70vh;
    display: flex;
    align-items: flex-end;
    padding: 2.5rem 1rem 3rem;
    background: var(--color-kidical-blue);
}

.home-hero__video {
    position: absolute;
    inset: 0;
    z-index: -2;
}

.home-hero__video iframe {
    /* Cover: a 16:9 iframe scaled to fill any viewport, centred. */
    position: absolute;
    top: 50%;
    left: 50%;
    width: 177.78vh; /* 16/9 of viewport height */
    height: 56.25vw; /* 9/16 of viewport width */
    min-width: 100%;
    min-height: 100%;
    transform: translate(-50%, -50%);
    pointer-events: none;
}

.home-hero::after {
    /* Legibility scrim so white copy holds over any frame. */
    content: "";
    position: absolute;
    inset: 0;
    z-index: -1;
    background: linear-gradient(to top, rgb(0 0 0 / 0.55), rgb(0 0 0 / 0.15) 55%, rgb(0 0 0 / 0.35));
}

.home-hero__inner {
    max-width: 48rem;
    margin-inline: auto;
    color: #fff;
    text-align: center;
}

.home-hero__lead {
    margin-top: 1rem;
    font-size: 1.25rem;
    line-height: 1.5;
    color: rgb(255 255 255 / 0.92);
}

.home-hero__actions {
    margin-top: 1.5rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    gap: 0.75rem 1.5rem;
}

.home-hero__secondary {
    font-weight: 700;
    color: #fff;
}

.home-hero__secondary:hover {
    text-decoration: underline;
}

/* Home — dispatcher routes */
.home-route {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    padding: 1.5rem;
    border-radius: var(--radius-card, 0.75rem);
    background: #fff;
    box-shadow: var(--shadow-card, 0 1px 2px rgb(0 0 0 / 0.06));
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.home-route:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgb(0 0 0 / 0.08);
}

.home-route__title {
    font-weight: 800;
    color: var(--color-kidical-blue);
}

.home-route__desc {
    font-size: 0.9rem;
    color: var(--color-kidical-ink);
    opacity: 0.7;
}
```

> Note: if `--radius-card`, `--shadow-card`, or the colour tokens are named differently in this file, use the existing token names (grep `@theme` in `app.css`). Never hardcode a hex outside the scrim/overlay rgba above.

- [ ] **Step 3: Build and verify the home page renders live**

Run: `npm run build`
Then load `https://kidicalmass.test/nl` in the browser (Herd). Expected: video fills the hero behind the white headline; below it the next-ride section (picker by default), three route cards, support callout, closing CTA. No console errors (`browser-logs`).

- [ ] **Step 4: Commit**

```bash
git add resources/css/app.css
git commit -m "style(home): video-cover hero + dispatcher route cards (wire fidelity)"
```

---

## Task 6: Full suite + format + pipeline note

**Files:**
- Modify: `docs/wiki/design/30-skeleton/00-page-registry.md` (P-01 row + roll-up)
- Modify: `docs/wiki/log.md`

- [ ] **Step 1: Run the full suite**

Run: `php artisan test --compact`
Expected: all green (the new home tests + the rest of the suite). Fix any regressions before continuing.

- [ ] **Step 2: Format**

Run: `vendor/bin/pint --dirty --format agent`
Expected: clean (formats `NextRideFinder.php`, `HomeController.php`, the new test files).

- [ ] **Step 3: Update the page registry P-01 row**

In `docs/wiki/design/30-skeleton/00-page-registry.md`, update the P-01 row: set `Wire` and `UI` to 🟠 (built on the new structure, render-verified; not 🟢 until Frederik's own critique), and rewrite the Top-gaps cell to note: NL video-hero + location-aware next-ride + dispatcher built on the UX spec (`docs/superpowers/specs/2026-06-06-home-ux-design.md`); remaining `[asset]` self-hosted MP4 export of the hero video (YouTube embed used for now); Frederik critique pending before Wire 🟢. Remove the "UX brief incomplete" gap (now closed). Keep all 12 columns intact. Update the roll-up prose so Home is no longer listed under "UX brief incomplete".

- [ ] **Step 4: Append a log entry**

Add to `docs/wiki/log.md`:

```markdown
## [2026-06-06] build | Home (P-01) rebuilt on the UX spec
NL video hero (YouTube VXiIgU9vI-4), location-aware "De volgende rit bij jou"
(NextRideFinder over Proximity/CurrentLocation), three dispatcher routes, quiet
support beat, closing CTA. Duplicated news/stats/map cut. UX brief gap closed.
Wire/UI 🟠 (Frederik critique pending). Spec: docs/superpowers/specs/2026-06-06-home-ux-design.md.
```

- [ ] **Step 5: Commit**

```bash
git add docs/wiki/design/30-skeleton/00-page-registry.md docs/wiki/log.md
git commit -m "docs(home): mark P-01 rebuilt (Wire/UI orange) + log entry"
```

---

## Notes for the implementer

- **Do not `git add -A`.** This is a shared working tree (Nico commits concurrently). Stage only the exact files listed in each commit. Do not push.
- **YouTube autoplay caveat:** the embed autoplays muted; some browsers still block it. The poster/fallback polish and a possible self-hosted MP4 are deferred to the Surface pass (tracked in the registry gap). The headline/lead are real DOM text, so tests and SEO do not depend on the video playing.
- **Reuse, don't rebuild:** `<livewire:location-picker>` already handles the cookie + redirect; the page just reacts to the cookie server-side via `CurrentLocation`. `<x-event-card>` leads with the town and shows location + time. `<x-support-callout variant="home" />` is the existing, tested support beat.
- **`distance` prop on `<x-event-card>`** is currently unused by that component, so the distance is rendered separately in the home view (`.home-nextride__distance`). Do not wire it through the card unless you also update the calendar usage.
```

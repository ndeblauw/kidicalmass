# Lokale groepen — List + Map Finder Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the Lokale groepen pill directory with a Booking-style list + map finder — a synced list (left) and Leaflet map (right) under a region selector + the shared location picker.

**Architecture:** `GroupController@index` builds a markers view-model (group → zip → lat/lng + region) and region counts. The Blade view renders the list as real server-side links (works without JS) plus a `@json` markers island. A `@push('scripts')` block initialises Leaflet (CartoDB tiles, like `activities/show`) and wires region-filtering and list↔map hover/click sync client-side. The existing Livewire `location-picker` keeps owning postcode search + geolocation (with reload); the map only *reacts* to the server-resolved location on load.

**Tech Stack:** Laravel 12, Blade, Livewire 4 (picker only), Leaflet 1.9 via CDN, CartoDB light tiles, vanilla JS island, Pest.

---

## File Structure

- **Modify** `app/Http/Controllers/GroupController.php` — hoist `$coordsByZip`; add `mapMarkers()` + `$regionCounts` + `$regionLabels`; pass to view.
- **Rewrite** `resources/views/groups/index.blade.php` — control bar (region buttons + `<livewire:location-picker>`), split (server-rendered link list + map shell with markers island), `@push('scripts')` Leaflet + sync.
- **Modify** `resources/css/pages/local-groups.css` — remove index-only directory/scale/find rules (KEEP `.grp-pill`, used by `groups/show`); add finder styles.
- **Create** `tests/Feature/GroupsFinderTest.php` — controller view-model + rendered output.
- **Modify** `tests/Feature/GroupsFilterBarTest.php` — assert the finder/picker render in the new structure.

Region keys → NL labels and marker colours (used everywhere):
- `Brussels Capital Region` → `Brussel` → `--color-kidical-blue`
- `Wallonia` → `Wallonië` → `--color-kidical-orange`
- `Flanders` → `Vlaanderen` → `--color-kidical-green`

---

### Task 1: Controller view-model (markers + region counts)

**Files:**
- Modify: `app/Http/Controllers/GroupController.php` (the `index` method, ~lines 17-49)
- Test: `tests/Feature/GroupsFinderTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/GroupsFinderTest.php`:

```php
<?php

use App\Models\Group;
use App\Models\PostalCode;

test('index passes map markers with resolved coordinates and region counts', function () {
    $belgium = Group::factory()->create(['name' => 'Belgium', 'invisible' => true]);
    $flanders = Group::factory()->withParent($belgium)->create(['name' => 'Flanders', 'invisible' => true]);
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.05, 'longitude' => 3.7167]);
    $gent = Group::factory()->withParent($flanders)->create([
        'name' => 'Gent', 'shortname' => 'gent', 'zip' => '9000', 'invisible' => false,
    ]);

    $response = $this->get(route('groups.index'));

    $response->assertOk();

    $markers = $response->viewData('markers');
    $marker = collect($markers)->firstWhere('slug', 'gent');

    expect($marker)->not->toBeNull();
    expect($marker['name'])->toBe('Gent');
    expect($marker['region'])->toBe('Flanders');
    expect($marker['regionLabel'])->toBe('Vlaanderen');
    expect($marker['lat'])->toBe(51.05);
    expect($marker['lng'])->toBe(3.7167);
    expect($marker['url'])->toContain('/chapters/gent');

    expect($response->viewData('regionCounts')['Flanders'])->toBe(1);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=GroupsFinderTest`
Expected: FAIL — `viewData('markers')` is null (key not passed yet).

- [ ] **Step 3: Implement the view-model**

In `app/Http/Controllers/GroupController.php`, replace the body of `index()` with:

```php
    public function index(string $locale): View
    {
        $groups = Group::visible()
            ->with(['parent', 'children'])
            ->withCount(['articles', 'activities'])
            ->get();

        $activityCount = Activity::whereYear('begin_date', now()->year)->count();

        $coordsByZip = PostalCode::whereIn('zip', $groups->pluck('zip')->filter()->unique())
            ->get()->keyBy('zip');

        $location = CurrentLocation::resolve();
        $nearby = collect();

        if ($location) {
            $partition = Proximity::partitionByRadius(
                $groups,
                ['lat' => $location['lat'], 'lng' => $location['lng']],
                (float) config('location.nearby_radius_km'),
                fn ($group) => $group->zip && $coordsByZip->has($group->zip)
                    ? ['lat' => $coordsByZip[$group->zip]->latitude, 'lng' => $coordsByZip[$group->zip]->longitude]
                    : null,
            );

            $nearby = $partition['nearby'];
        }

        $myGroups = auth()->check()
            ? auth()->user()->groups()->where('invisible', false)->get()
            : collect();

        $regionLabels = [
            'Brussels Capital Region' => 'Brussel',
            'Wallonia' => 'Wallonië',
            'Flanders' => 'Vlaanderen',
        ];

        $markers = $this->mapMarkers($groups, $coordsByZip, $regionLabels);

        $regionCounts = $groups
            ->groupBy(fn (Group $group) => $group->parent?->name)
            ->map->count();

        return view('groups.index', compact(
            'groups', 'activityCount', 'location', 'nearby', 'myGroups',
            'markers', 'regionCounts', 'regionLabels',
        ));
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Group>  $groups
     * @param  \Illuminate\Support\Collection<string, PostalCode>  $coordsByZip
     * @param  array<string, string>  $regionLabels
     * @return list<array{name: string, slug: string, url: string, region: ?string, regionLabel: ?string, zip: ?string, lat: ?float, lng: ?float}>
     */
    private function mapMarkers(
        \Illuminate\Support\Collection $groups,
        \Illuminate\Support\Collection $coordsByZip,
        array $regionLabels,
    ): array {
        return $groups->map(function (Group $group) use ($coordsByZip, $regionLabels): array {
            $postalCode = $group->zip ? $coordsByZip->get($group->zip) : null;
            $region = $group->parent?->name;

            return [
                'name' => $group->name,
                'slug' => $group->shortname,
                'url' => route('groups.show', $group),
                'region' => $region,
                'regionLabel' => $region ? ($regionLabels[$region] ?? $region) : null,
                'zip' => $group->zip,
                'lat' => $postalCode?->latitude,
                'lng' => $postalCode?->longitude,
            ];
        })->values()->all();
    }
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=GroupsFinderTest`
Expected: PASS.

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Http/Controllers/GroupController.php tests/Feature/GroupsFinderTest.php
git commit -m "feat(groups): build map-markers view-model for the finder"
```

---

### Task 2: Blade view — control bar, split list, markers island

**Files:**
- Modify: `resources/views/groups/index.blade.php` (full rewrite of the panel content)
- Test: `tests/Feature/GroupsFinderTest.php`

- [ ] **Step 1: Write the failing test**

Append to `tests/Feature/GroupsFinderTest.php`:

```php
test('index renders the finder: region buttons, picker, link list and markers island', function () {
    $belgium = Group::factory()->create(['name' => 'Belgium', 'invisible' => true]);
    $flanders = Group::factory()->withParent($belgium)->create(['name' => 'Flanders', 'invisible' => true]);
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.05, 'longitude' => 3.7167]);
    Group::factory()->withParent($flanders)->create([
        'name' => 'Gent', 'shortname' => 'gent', 'zip' => '9000', 'invisible' => false,
    ]);

    $response = $this->get(route('groups.index'));

    $response->assertOk();
    $response->assertSee('grp-region-btn', false);            // region selector
    $response->assertSee('data-region="Flanders"', false);    // a region button
    $response->assertSee('Vlaanderen', false);                // NL label
    $response->assertSee('location-picker', false);           // shared picker still present
    $response->assertSee('grp-map', false);                   // map shell
    $response->assertSee('data-markers', false);              // markers island
    // List card is a real link to the group page (works without JS)
    $response->assertSee(route('groups.show', Group::where('shortname', 'gent')->first()), false);
    $response->assertSee('grp-card__name', false);
    $response->assertDontSee('grp-directory', false);         // old layout gone
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=GroupsFinderTest`
Expected: FAIL — `grp-region-btn` / `grp-map` not found (old view still renders).

- [ ] **Step 3: Rewrite the view**

Replace the entire contents of `resources/views/groups/index.blade.php` with:

```blade
{{--
    Lokale groepen (P-10) — list + map finder.
    Server-rendered link list (works without JS) + a markers island that the
    @push('scripts') block turns into a synced Leaflet map. The shared Livewire
    location-picker owns postcode search + geolocation; the map reacts to the
    resolved location on load. Spec: docs/superpowers/specs/2026-06-15-lokale-groepen-list-map-finder-design.md
--}}
<x-layouts::site title="Lokale groepen">

    <x-page-hero
        eyebrow="Lokale groepen"
        title="Jouw buurt fietst al, rij mee."
        illustration="img/illustrations/longtail-with-kid.svg">

        <x-intro-text>
            <p>In elke gemeente trekken buren samen de straat op voor veilig fietsen met kinderen. Eén beweging, lokaal geworteld en het hele jaar door actief in jouw buurt.</p>
        </x-intro-text>

        @if ($groups->isNotEmpty())
            @php
                $regionOrder = ['Brussels Capital Region', 'Wallonia', 'Flanders'];
                $mineIds = $myGroups->pluck('id');
                $orderedGroups = $groups
                    ->sortBy('name')
                    ->sortByDesc(fn ($group) => $mineIds->contains($group->id) ? 1 : 0)
                    ->values();
            @endphp

            <div class="grp-finder" data-group-finder data-location='@json($location)'>
                <div class="grp-finder__controls">
                    <div class="grp-regions">
                        <button type="button" class="grp-region-btn is-active" data-region="all">
                            Heel België <span class="grp-region-btn__count">{{ $groups->count() }}</span>
                        </button>
                        @foreach ($regionOrder as $regionKey)
                            @php $count = $regionCounts[$regionKey] ?? 0; @endphp
                            @if ($count > 0)
                                <button type="button" class="grp-region-btn" data-region="{{ $regionKey }}">
                                    <span class="grp-region-btn__dot" aria-hidden="true"></span>
                                    {{ $regionLabels[$regionKey] ?? $regionKey }}
                                    <span class="grp-region-btn__count">{{ $count }}</span>
                                </button>
                            @endif
                        @endforeach
                    </div>
                    <div class="grp-finder__picker">
                        <livewire:location-picker :compact="true" />
                    </div>
                </div>

                <div class="grp-finder__split">
                    <div class="grp-results">
                        <p class="grp-results__count" data-count>{{ $groups->count() }} {{ $groups->count() === 1 ? 'groep' : 'groepen' }}</p>
                        <ul class="grp-results__list" data-list>
                            @foreach ($orderedGroups as $group)
                                <li class="grp-card {{ $mineIds->contains($group->id) ? 'grp-card--mine' : '' }}"
                                    data-slug="{{ $group->shortname }}"
                                    data-region="{{ $group->parent?->name }}">
                                    <a href="{{ route('groups.show', $group) }}" class="grp-card__link link-plain">
                                        <span class="grp-card__dot" aria-hidden="true"></span>
                                        <span class="grp-card__main">
                                            <span class="grp-card__name">{{ $group->name }}@if ($mineIds->contains($group->id))<span class="grp-card__tag">· jouw groep</span>@endif</span>
                                            <span class="grp-card__zip">{{ $group->zip }}</span>
                                        </span>
                                        <span class="grp-card__dist" data-dist></span>
                                        <span class="grp-card__go" aria-hidden="true">→</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="grp-map-shell">
                        <p class="grp-map__status" data-status>Heel België</p>
                        <div id="grp-map" class="grp-map" data-markers='@json($markers)'></div>
                    </div>
                </div>
            </div>
        @else
            <p class="kal-empty mt-10">Er zijn nog geen lokale groepen om te tonen.</p>
        @endif

    </x-page-hero>

    <x-slot:closing>
        <x-closing-cta heading="Staat jouw stad er nog niet bij?"
            :href="route('volunteer')" label="Zo begin je" />
    </x-slot:closing>

    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9/dist/leaflet.js"></script>
        {{-- Sync logic (region filter + list/map) is added in Task 4, right here. --}}
    @endpush

</x-layouts::site>
```

> Note: this mirrors the `activities/show` map pattern exactly (CDN Leaflet in `@push('scripts')`). The sync `<script>` is added inline in this same block in Task 4. Until Task 4 is done the map div stays empty — the server-rendered link list still works.

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=GroupsFinderTest`
Expected: PASS (both tests).

- [ ] **Step 5: Commit**

```bash
git add resources/views/groups/index.blade.php tests/Feature/GroupsFinderTest.php
git commit -m "feat(groups): render list+map finder view (server-rendered link list + markers island)"
```

---

### Task 3: CSS — remove dead directory rules, add finder styles

**Files:**
- Modify: `resources/css/pages/local-groups.css`

- [ ] **Step 1: Confirm `.grp-pill` is still needed elsewhere**

Run: `grep -rl "grp-pill" resources/views`
Expected: `resources/views/groups/show.blade.php` — so `.grp-pill` MUST be kept.

- [ ] **Step 2: Replace the file contents**

Replace the entire contents of `resources/css/pages/local-groups.css` with:

```css
@layer components {
    /* ---------- Lokale groepen — list + map finder (P-10) ---------- */
    .grp-finder {
        margin-top: clamp(1.5rem, 3vw, 2.25rem);
    }

    .grp-finder__controls {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.9rem 1.5rem;
        margin-bottom: 1.1rem;
    }
    .grp-regions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .grp-region-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        border: 2px solid color-mix(in oklab, var(--color-kidical-ink), transparent 86%);
        background: white;
        border-radius: 999px;
        padding: 0.5rem 1rem;
        font-family: var(--font-sans);
        font-weight: 800;
        font-size: var(--text-sm);
        color: var(--color-kidical-ink);
        transition: border-color 0.15s, background 0.15s, color 0.15s, transform 0.15s;
    }
    .grp-region-btn:hover {
        transform: translateY(-1px);
        border-color: var(--color-kidical-ink);
    }
    .grp-region-btn.is-active {
        background: var(--color-kidical-ink);
        color: white;
        border-color: var(--color-kidical-ink);
    }
    .grp-region-btn__count {
        font-weight: 700;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 40%);
    }
    .grp-region-btn.is-active .grp-region-btn__count {
        color: rgb(255 255 255 / 0.7);
    }
    .grp-region-btn__dot {
        width: 0.7rem;
        height: 0.7rem;
        border-radius: 999px;
        flex: none;
    }

    /* Region colours — shared by selector dots and list-card dots. */
    .grp-region-btn[data-region="Brussels Capital Region"] .grp-region-btn__dot,
    .grp-card[data-region="Brussels Capital Region"] .grp-card__dot {
        background: var(--color-kidical-blue);
    }
    .grp-region-btn[data-region="Wallonia"] .grp-region-btn__dot,
    .grp-card[data-region="Wallonia"] .grp-card__dot {
        background: var(--color-kidical-orange);
    }
    .grp-region-btn[data-region="Flanders"] .grp-region-btn__dot,
    .grp-card[data-region="Flanders"] .grp-card__dot {
        background: var(--color-kidical-green);
    }

    .grp-finder__picker {
        flex: 1 1 20rem;
        max-width: 28rem;
        min-width: 15rem;
    }

    /* Split: list left, map right (Booking-style). Single column on small screens. */
    .grp-finder__split {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.25rem;
    }
    @media (min-width: 48rem) {
        .grp-finder__split {
            grid-template-columns: minmax(18rem, 0.82fr) 1fr;
            height: clamp(34rem, 74vh, 48rem);
        }
    }

    .grp-results {
        overflow-y: auto;
        padding-right: 0.4rem;
        scrollbar-width: thin;
    }
    @media (max-width: 47.99rem) {
        .grp-results { overflow: visible; }
    }
    .grp-results__count {
        font-weight: 800;
        font-size: var(--text-sm);
        margin: 0.1rem 0 0.8rem;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 25%);
    }
    .grp-results__list {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .grp-card {
        margin-bottom: 0.6rem;
    }
    .grp-card.is-hidden {
        display: none;
    }
    .grp-card__link {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        width: 100%;
        background: white;
        border: 2px solid color-mix(in oklab, var(--color-kidical-ink), transparent 90%);
        border-radius: 14px;
        padding: 0.85rem 1rem;
        transition: border-color 0.15s, box-shadow 0.15s, transform 0.12s;
    }
    .grp-card__link:hover {
        border-color: color-mix(in oklab, var(--color-kidical-ink), transparent 70%);
        box-shadow: 0 8px 22px -12px rgb(40 26 57 / 0.4);
        transform: translateY(-1px);
    }
    .grp-card.is-active .grp-card__link {
        border-color: var(--color-kidical-blue);
        box-shadow: 0 10px 26px -12px color-mix(in oklab, var(--color-kidical-blue), transparent 40%);
    }
    .grp-card--mine .grp-card__link {
        border-color: color-mix(in oklab, var(--color-kidical-blue), transparent 55%);
    }
    .grp-card__dot {
        width: 1.65rem;
        height: 1.65rem;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        border: 2px solid white;
        box-shadow: 0 2px 6px rgb(40 26 57 / 0.3);
        flex: none;
    }
    .grp-card__main {
        flex: 1 1 auto;
        min-width: 0;
    }
    .grp-card__name {
        display: block;
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: var(--text-lg);
        line-height: 1.1;
        color: var(--color-kidical-ink);
    }
    .grp-card__tag {
        font-family: var(--font-sans);
        font-size: var(--text-xs);
        font-weight: 700;
        color: var(--color-kidical-blue);
        margin-left: 0.4rem;
    }
    .grp-card__zip {
        display: block;
        font-size: var(--text-xs);
        font-weight: 700;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 42%);
        margin-top: 0.15rem;
    }
    .grp-card__dist {
        font-weight: 800;
        color: var(--color-kidical-blue);
        font-size: var(--text-xs);
        white-space: nowrap;
    }
    .grp-card__go {
        font-weight: 800;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 55%);
        font-size: var(--text-lg);
    }

    /* Map */
    .grp-map-shell {
        position: relative;
        border-radius: 1.25rem;
        overflow: hidden;
        box-shadow: 0 24px 60px -28px rgb(40 26 57 / 0.45);
        border: 1px solid color-mix(in oklab, var(--color-kidical-ink), transparent 90%);
    }
    @media (max-width: 47.99rem) {
        .grp-map-shell { height: 22.5rem; }
    }
    .grp-map {
        width: 100%;
        height: 100%;
        min-height: 22.5rem;
        background: var(--color-kidical-light-blue);
    }
    .grp-map__status {
        position: absolute;
        left: 1rem;
        top: 1rem;
        z-index: 500;
        margin: 0;
        background: rgb(255 255 255 / 0.94);
        border-radius: 999px;
        padding: 0.45rem 0.95rem;
        font-weight: 800;
        font-size: var(--text-sm);
        box-shadow: 0 6px 18px rgb(40 26 57 / 0.18);
    }

    /* Leaflet divIcon pins */
    .grp-pin {
        display: block;
        width: 26px;
        height: 26px;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        border: 2.5px solid white;
        box-shadow: 0 3px 8px rgb(40 26 57 / 0.4);
        transition: opacity 0.25s, transform 0.15s;
    }
    .grp-pin--me {
        background: var(--color-kidical-ink) !important;
        width: 20px;
        height: 20px;
    }
    .grp-pin.is-dim { opacity: 0.22; }
    .grp-pin.is-hot { transform: rotate(-45deg) scale(1.4); z-index: 1000 !important; }
    .grp-popup__region {
        font-weight: 600;
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 30%);
    }

    /* ---------- Kept: region pill (used by groups/show.blade.php) ---------- */
    .grp-region__title {
        font-family: var(--font-heading);
        font-weight: 700;
        font-size: var(--text-base);
        color: color-mix(in oklab, var(--color-kidical-ink), transparent 45%);
        margin-bottom: 0.9rem;
    }
    .grp-pill {
        display: inline-block;
        border-radius: 999px;
        border: 2px solid color-mix(in oklab, var(--color-kidical-ink), transparent 88%);
        background: white;
        padding: 0.55rem 1.15rem;
        font-family: var(--font-heading);
        font-weight: 700;
        font-size: var(--text-base);
        color: var(--color-kidical-blue);
        box-shadow: 0 2px 10px rgb(0 0 0 / 0.05);
        transition: transform 0.18s cubic-bezier(0.22, 1, 0.36, 1), border-color 0.18s, color 0.18s;
    }
    .grp-pill:hover {
        transform: translateY(-2px);
        border-color: var(--color-kidical-red);
        box-shadow: 0 4px 16px rgb(0 0 0 / 0.09);
    }
}
```

- [ ] **Step 3: Run the CSS architecture test**

Run: `php artisan test --compact --filter=CssArchitectureTest`
Expected: PASS (partial already registered in `app.css`; no hex/px in Blade components).

- [ ] **Step 4: Commit**

```bash
git add resources/css/pages/local-groups.css
git commit -m "style(groups): finder styles; drop dead directory rules, keep grp-pill for show page"
```

---

### Task 4: Sync script — Leaflet init + region filter + list↔map sync

Inline `<script>` in the view's `@push('scripts')` block (matches `activities/show`: CDN global `L`, no new file, no npm dependency, no Vite config).

**Files:**
- Modify: `resources/views/groups/index.blade.php` (the `@push('scripts')` block)

- [ ] **Step 1: Add the inline script**

In `resources/views/groups/index.blade.php`, replace the `{{-- Sync logic … --}}` comment inside `@push('scripts')` with this `<script>` (it uses the global `L` provided by the CDN `<script>` above it):

```blade
        <script>
        (function () {
            function haversineKm(aLat, aLng, bLat, bLng) {
    const R = 6371;
    const dLat = ((bLat - aLat) * Math.PI) / 180;
    const dLng = ((bLng - aLng) * Math.PI) / 180;
    const s =
        Math.sin(dLat / 2) ** 2 +
        Math.cos((aLat * Math.PI) / 180) * Math.cos((bLat * Math.PI) / 180) * Math.sin(dLng / 2) ** 2;
    return 2 * R * Math.asin(Math.sqrt(s));
}

function initFinder() {
    const root = document.querySelector('[data-group-finder]');
    const mapEl = document.getElementById('grp-map');
    if (!root || !mapEl || typeof L === 'undefined') {
        return;
    }

    const markers = JSON.parse(mapEl.dataset.markers || '[]').filter((m) => m.lat != null && m.lng != null);
    let location = null;
    try {
        location = JSON.parse(root.dataset.location || 'null');
    } catch (e) {
        location = null;
    }

    const styles = getComputedStyle(document.documentElement);
    const token = (name, fallback) => styles.getPropertyValue(name).trim() || fallback;
    const regionColor = {
        'Brussels Capital Region': token('--color-kidical-blue', '#1d67cd'),
        Wallonia: token('--color-kidical-orange', '#F0803C'),
        Flanders: token('--color-kidical-green', '#5CB85C'),
    };
    const fallbackColor = token('--color-kidical-red', '#E63A7B');

    const map = L.map(mapEl, { zoomControl: true, scrollWheelZoom: false });
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19,
    }).addTo(map);

    const bySlug = {};
    markers.forEach((m) => {
        const color = regionColor[m.region] || fallbackColor;
        const icon = L.divIcon({
            className: '',
            html: `<span class="grp-pin" data-slug="${m.slug}" style="background:${color}"></span>`,
            iconSize: [26, 26],
            iconAnchor: [13, 26],
            popupAnchor: [0, -24],
        });
        const marker = L.marker([m.lat, m.lng], { icon }).addTo(map);
        marker.bindPopup(
            `<strong>${m.name}</strong><br><span class="grp-popup__region">${m.regionLabel ?? ''}</span><br><a href="${m.url}">Bekijk groep →</a>`,
        );
        marker.on('click', () => focusCard(m.slug));
        bySlug[m.slug] = { marker, data: m };
    });

    const allBounds = markers.length ? L.latLngBounds(markers.map((m) => [m.lat, m.lng])) : null;
    const fitAll = () => allBounds && map.fitBounds(allBounds, { padding: [40, 40] });
    map.whenReady(() => map.invalidateSize());

    const cards = Array.from(root.querySelectorAll('.grp-card'));
    const listEl = root.querySelector('[data-list]');
    const countEl = root.querySelector('[data-count]');
    const statusEl = root.querySelector('[data-status]');
    const pinEl = (slug) => mapEl.querySelector(`.grp-pin[data-slug="${slug}"]`);

    function focusCard(slug) {
        const card = cards.find((c) => c.dataset.slug === slug);
        if (!card) return;
        cards.forEach((c) => c.classList.toggle('is-active', c === card));
        card.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    cards.forEach((card) => {
        const slug = card.dataset.slug;
        card.addEventListener('mouseenter', () => pinEl(slug)?.classList.add('is-hot'));
        card.addEventListener('mouseleave', () => pinEl(slug)?.classList.remove('is-hot'));
    });

    // Region filter
    const regionButtons = Array.from(root.querySelectorAll('.grp-region-btn'));
    function setRegion(region) {
        regionButtons.forEach((b) => b.classList.toggle('is-active', b.dataset.region === region));
        let shown = 0;
        cards.forEach((card) => {
            const inRegion = region === 'all' || card.dataset.region === region;
            card.classList.toggle('is-hidden', !inRegion);
            if (inRegion) shown += 1;
            pinEl(card.dataset.slug)?.classList.toggle('is-dim', !inRegion);
        });
        countEl.textContent = `${shown} ${shown === 1 ? 'groep' : 'groepen'}`;
        if (region === 'all') {
            fitAll();
            statusEl.textContent = 'Heel België';
        } else {
            const pts = markers.filter((m) => m.region === region).map((m) => [m.lat, m.lng]);
            if (pts.length) map.fitBounds(L.latLngBounds(pts), { padding: [55, 55] });
            const label = markers.find((m) => m.region === region)?.regionLabel || region;
            statusEl.textContent = label;
        }
    }
    regionButtons.forEach((b) => b.addEventListener('click', () => setRegion(b.dataset.region)));

    // React to the server-resolved location: distance-sort the list + zoom nearest
    if (location && location.lat != null && location.lng != null && markers.length) {
        const meIcon = L.divIcon({
            className: '',
            html: '<span class="grp-pin grp-pin--me"></span>',
            iconSize: [20, 20],
            iconAnchor: [10, 20],
        });
        L.marker([location.lat, location.lng], { icon: meIcon }).addTo(map).bindPopup('<strong>Jij bent hier</strong>');

        const dist = {};
        markers.forEach((m) => {
            dist[m.slug] = haversineKm(location.lat, location.lng, m.lat, m.lng);
        });
        cards.forEach((card) => {
            const d = dist[card.dataset.slug];
            const el = card.querySelector('[data-dist]');
            if (el && d != null) el.textContent = `~${Math.round(d)} km`;
        });
        cards
            .slice()
            .sort((a, b) => (dist[a.dataset.slug] ?? 1e9) - (dist[b.dataset.slug] ?? 1e9))
            .forEach((c) => listEl.appendChild(c));

        const nearest = markers
            .slice()
            .sort((a, b) => dist[a.slug] - dist[b.slug])
            .slice(0, 5);
        map.fitBounds(L.latLngBounds([[location.lat, location.lng], ...nearest.map((m) => [m.lat, m.lng])]), {
            padding: [55, 55],
        });
        statusEl.textContent = 'Dichtst bij jou';
    } else {
        fitAll();
    }
}

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initFinder);
            } else {
                initFinder();
            }
        })();
        </script>
```

> `initFinder` reads the global `L` from the CDN `<script>` rendered above it (synchronous, so `L` is ready). The `typeof L === 'undefined'` guard is a belt-and-braces no-op fallback. (Indentation inside the IIFE is cosmetic — Blade serves it verbatim.)

- [ ] **Step 2: Build the CSS bundle**

Task 3 changed a CSS partial, so rebuild (the inline script is served with the view — no JS build needed):

Run: `npm run build`
Expected: build succeeds.

- [ ] **Step 3: Manual verification**

Screenshot `https://kidicalmass.test/nl/chapters` at 1440px and 600px (Playwright, `ignoreHTTPSErrors: true`, global `playwright` via `NODE_PATH`). Confirm:
- region buttons + picker on top; list left, map right with coloured pins;
- clicking a region zooms + filters + updates the count;
- hovering a card pops its pin; clicking a pin scrolls/highlights the card.

Use `mcp__laravel-boost__browser-logs` to confirm no JS console errors.

- [ ] **Step 4: Commit**

```bash
git add resources/views/groups/index.blade.php
git commit -m "feat(groups): inline Leaflet finder script — region filter + list/map sync"
```

---

### Task 5: Update legacy test + full regression

**Files:**
- Modify: `tests/Feature/GroupsFilterBarTest.php`

- [ ] **Step 1: Rewrite the assertions for the new structure**

Replace the contents of `tests/Feature/GroupsFilterBarTest.php` with:

```php
<?php

use App\Models\Group;

test('groups index shows the location picker in the finder control bar, without radius tabs', function () {
    Group::factory()->create(['invisible' => false]);

    $response = $this->get(route('groups.index'));

    $response->assertOk();
    $response->assertSee('location-picker', false);       // shared picker, now in the control bar
    $response->assertSee('grp-region-btn', false);        // region selector present
    $response->assertDontSee('filter-bar', false);        // no full-bleed panel-top bar
    $response->assertDontSee('grp-hero__locate', false);
    $response->assertDontSee('filter-bar__tab', false);   // radius tabs are agenda-only
});
```

- [ ] **Step 2: Run the related suite**

Run: `php artisan test --compact --filter='GroupsFinder|GroupsFilterBar|LocationPickerCompact|PublicPages|CssArchitecture'`
Expected: all PASS.

- [ ] **Step 3: Commit**

```bash
git add tests/Feature/GroupsFilterBarTest.php
git commit -m "test(groups): assert finder control bar replaces the full-bleed filter bar"
```

---

### Task 6: Final verification + pipeline

- [ ] **Step 1: Full test run**

Run: `php artisan test --compact`
Expected: green (note: `CalendarProximityTest` is a known order-dependent flake — re-run it in isolation if it fails).

- [ ] **Step 2: Pint**

Run: `vendor/bin/pint --dirty --format agent`

- [ ] **Step 3: Pipeline update (separate, guided)**

Run the `/pipeline` skill for `P-10`: bump Wire/UI/Back as honestly warranted, refresh Top gaps ("list+map finder live") and the roll-up, and append a `build` entry to `docs/wiki/log.md`. Do not bump Wire to 🟢 until Frederik has done his own critique pass.

---

## Notes / deviations from the prototype

- **Card click navigates** to the group page (the card is a real link) rather than flying the map — this keeps progressive enhancement honest and matches Booking (clicking a result opens it). Hover handles the pin highlight; pin-click handles the reverse sync. If Frederik wants click-to-fly instead, intercept the card click in the inline finder script and call `marker.openPopup()` + `flyTo`.
- **No in-finder search/geo button** — the Livewire `location-picker` owns both (with reload); the map reacts to the resolved `$location` on load. This is intentional per the spec (shared cookie location).
- **Region filter is client-only** and resets on reload (e.g. after setting a location). Acceptable for v1.

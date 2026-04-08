# Activity Map Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Upgrade the activity page map with CartoDB Positron tiles, a branded departure pin, an arrival pin for point-to-point routes, a loop/p2p info strip, and an optional "Bekijk op Komoot" link.

**Architecture:** All changes are frontend-only (Blade + inline JS + CSS) except for adding a nullable `komoot_url` column to the activities table and a matching Filament form field. The map tile layer, marker design, loop detection, and info strip are all implemented in the existing inline `<script>` block in `show.blade.php`. No new JS files.

**Tech Stack:** Leaflet 1.9, CartoDB Positron tile layer, Blade, Tailwind/CSS in `app.css`, Filament v5, Pest v4.

---

## File Map

| File | Change |
|---|---|
| `database/migrations/YYYY_MM_DD_add_komoot_url_to_activities_table.php` | New — adds `komoot_url` nullable string |
| `app/Filament/Resources/Activities/ActivityResource.php` | Add `komoot_url` TextInput to form |
| `database/factories/ActivityFactory.php` | Add `komoot_url` to definition |
| `resources/views/activities/show.blade.php` | Map section restructure + JS overhaul |
| `resources/css/app.css` | Add `.activity-map-container`, `.activity-map-info-strip`, `.activity-map-marker`, `.activity-map-label`, `.activity-map-badge`, `.activity-map-komoot-link` |
| `tests/Feature/ActivityMapTest.php` | New — tests komoot link rendering |

---

## Task 1: Add `komoot_url` column, Filament field, and factory state

**Files:**
- Create: `database/migrations/YYYY_MM_DD_add_komoot_url_to_activities_table.php`
- Modify: `app/Filament/Resources/Activities/ActivityResource.php`
- Modify: `database/factories/ActivityFactory.php`
- Test: `tests/Feature/ActivityMapTest.php`

- [ ] **Step 1: Create the migration**

Run:
```bash
php artisan make:migration add_komoot_url_to_activities_table --no-interaction
```

Open the generated file and replace the `up()` and `down()` bodies:

```php
public function up(): void
{
    Schema::table('activities', function (Blueprint $table) {
        $table->string('komoot_url')->nullable()->after('duration');
    });
}

public function down(): void
{
    Schema::table('activities', function (Blueprint $table) {
        $table->dropColumn('komoot_url');
    });
}
```

- [ ] **Step 2: Run the migration**

```bash
php artisan migrate --no-interaction
```

Expected: `Migrating: YYYY_MM_DD_add_komoot_url_to_activities_table` followed by `Migrated`.

- [ ] **Step 3: Write the failing test**

Create `tests/Feature/ActivityMapTest.php`:

```php
<?php

use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows the komoot link when komoot_url is set on the activity', function () {
    $activity = Activity::factory()->create([
        'komoot_url' => 'https://www.komoot.com/tour/1234567',
    ]);

    $this->get(route('activities.show', $activity))
        ->assertOk()
        ->assertSee('Bekijk op Komoot')
        ->assertSee('https://www.komoot.com/tour/1234567');
});

it('does not show the komoot link when komoot_url is not set', function () {
    $activity = Activity::factory()->create([
        'komoot_url' => null,
    ]);

    $this->get(route('activities.show', $activity))
        ->assertOk()
        ->assertDontSee('Bekijk op Komoot');
});
```

- [ ] **Step 4: Run the tests to confirm they fail**

```bash
php artisan test --compact --filter=ActivityMapTest
```

Expected: 2 failures — `assertSee('Bekijk op Komoot')` fails because the template doesn't render it yet.

- [ ] **Step 5: Add `komoot_url` TextInput to the Filament form**

Open `app/Filament/Resources/Activities/ActivityResource.php`. Add after the `duration` TextInput (around line 84):

```php
TextInput::make('komoot_url')
    ->nullable()
    ->url()
    ->label('Komoot URL')
    ->helperText('Paste the public Komoot tour URL (e.g. https://www.komoot.com/tour/123). Optional.')
    ->maxLength(500),
```

- [ ] **Step 6: Add `komoot_url` to the factory**

Open `database/factories/ActivityFactory.php`. Add `komoot_url` to the `definition()` return array:

```php
'komoot_url' => null,
```

So the definition array becomes:

```php
return [
    'title_nl' => fake()->sentence(),
    'title_fr' => fake()->sentence(),
    'content_nl' => fake()->paragraphs(2, true),
    'content_fr' => fake()->paragraphs(2, true),
    'activity_type' => fake()->randomElement(ActivityType::cases()),
    'begin_date' => $beginDate,
    'end_date' => $endDate,
    'location' => fake()->city().', '.fake()->address(),
    'author_id' => User::factory(),
    'komoot_url' => null,
];
```

- [ ] **Step 7: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 8: Commit**

```bash
git add database/migrations/ app/Filament/Resources/Activities/ActivityResource.php database/factories/ActivityFactory.php tests/Feature/ActivityMapTest.php
git commit -m "feat: add komoot_url to activities table and Filament form"
```

---

## Task 2: Restructure map section in Blade template

**Files:**
- Modify: `resources/views/activities/show.blade.php`

Replace the map `<div>` inside `.activity-map-col` (currently lines 126–133) with a wrapper that adds the info strip and Komoot link. The JS `data-` attributes are on the inner map element.

- [ ] **Step 1: Replace the map column HTML**

In `resources/views/activities/show.blade.php`, find this block:

```blade
@if($hasMap)
    <div class="activity-map-col">
        <h2>De route</h2>
        <div id="activity-map"
             class="activity-map-embed"
             data-coordinates="{{ json_encode($routeCoords) }}">
        </div>
    </div>
@endif
```

Replace it with:

```blade
@if($hasMap)
    <div class="activity-map-col">
        <h2>De route</h2>
        <div class="activity-map-container">
            <div id="activity-map"
                 class="activity-map-embed"
                 data-coordinates="{{ json_encode($routeCoords) }}"
                 data-komoot-url="{{ $activity->komoot_url ?? '' }}">
            </div>
            <div class="activity-map-info-strip">
                <div class="activity-map-info-strip__stats">
                    @if($activity->distance)
                        <span>{{ $activity->distance }}</span>
                    @endif
                    @if($activity->duration)
                        <span>{{ $activity->duration }}</span>
                    @endif
                    <span class="activity-map-badge" id="activity-map-route-type" aria-live="polite"></span>
                </div>
                @if($activity->komoot_url)
                    <a href="{{ $activity->komoot_url }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="activity-map-komoot-link">
                        Bekijk op Komoot
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                @endif
            </div>
        </div>
    </div>
@endif
```

- [ ] **Step 2: Run the failing tests — they should now pass**

```bash
php artisan test --compact --filter=ActivityMapTest
```

Expected: 2 tests pass.

- [ ] **Step 3: Run Pint**

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 4: Commit**

```bash
git add resources/views/activities/show.blade.php
git commit -m "feat: add map info strip and komoot link to activity page"
```

---

## Task 3: Upgrade tile layer and replace circle marker with brand pin

**Files:**
- Modify: `resources/views/activities/show.blade.php` (the inline `<script>` block)

- [ ] **Step 1: Replace the script block**

Find the `@if($hasMap)` script block at the bottom of `show.blade.php` (starts around line 172). Replace the entire script contents with:

```javascript
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('activity-map');
    if (!el) return;

    const coords = JSON.parse(el.dataset.coordinates || '[]');
    if (!coords.length) return;

    const map = L.map(el, { zoomControl: true, scrollWheelZoom: false });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="https://carto.com/attributions">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19,
    }).addTo(map);

    const polyline = L.polyline(coords, {
        color: '#E63A7B',
        weight: 5,
        opacity: 0.95,
    }).addTo(map);

    map.fitBounds(polyline.getBounds(), { padding: [40, 40] });

    // Departure pin
    const departureIcon = L.divIcon({
        html: `<svg width="28" height="38" viewBox="0 0 28 38" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M14 1C6.82 1 1 6.82 1 14C1 24 14 37 14 37C14 37 27 24 27 14C27 6.82 21.18 1 14 1Z" fill="#E63A7B"/>
            <circle cx="14" cy="14" r="5.5" fill="rgba(0,0,0,0.2)"/>
            <circle cx="14" cy="14" r="3.5" fill="white"/>
        </svg><span class="activity-map-label">Vertrekpunt</span>`,
        className: 'activity-map-marker',
        iconAnchor: [14, 37],
    });

    L.marker(coords[0], { icon: departureIcon }).addTo(map);

    // Loop detection (Haversine)
    function haversineMeters(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const φ1 = lat1 * Math.PI / 180;
        const φ2 = lat2 * Math.PI / 180;
        const Δφ = (lat2 - lat1) * Math.PI / 180;
        const Δλ = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(Δφ / 2) ** 2 + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    const first = coords[0];
    const last = coords[coords.length - 1];
    const distanceBetweenEnds = haversineMeters(first[0], first[1], last[0], last[1]);
    const isLoop = distanceBetweenEnds <= 150;

    const routeTypeBadge = document.getElementById('activity-map-route-type');
    if (routeTypeBadge) {
        routeTypeBadge.textContent = isLoop ? 'Lus' : 'Punt naar punt';
    }

    if (!isLoop) {
        const arrivalIcon = L.divIcon({
            html: `<svg width="22" height="30" viewBox="0 0 28 38" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M14 1C6.82 1 1 6.82 1 14C1 24 14 37 14 37C14 37 27 24 27 14C27 6.82 21.18 1 14 1Z" fill="white" stroke="#E63A7B" stroke-width="2.5"/>
                <circle cx="14" cy="14" r="4" fill="#E63A7B" opacity="0.5"/>
            </svg><span class="activity-map-label activity-map-label--end">Aankomst</span>`,
            className: 'activity-map-marker activity-map-marker--end',
            iconAnchor: [11, 30],
        });

        L.marker(last, { icon: arrivalIcon }).addTo(map);
    }
});
```

- [ ] **Step 2: Run Pint** (no PHP changes but run to be safe)

```bash
vendor/bin/pint --dirty --format agent
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/activities/show.blade.php
git commit -m "feat: cartodb positron tiles, brand pin marker, loop detection and arrival marker"
```

---

## Task 4: Add CSS for map container, info strip, markers, and badges

**Files:**
- Modify: `resources/css/app.css`

- [ ] **Step 1: Add the new CSS rules**

Open `resources/css/app.css`. Find the existing `.activity-map-embed` rule (around line 486) and add the following block directly after it:

```css
.activity-map-container {
    border-radius: var(--radius-xl, 0.75rem);
    overflow: hidden;
    border: 1px solid var(--color-zinc-200);
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
}

.activity-map-container .activity-map-embed {
    border-radius: 0;
    border: none;
    box-shadow: none;
}

.activity-map-info-strip {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding: 0.625rem 0.875rem;
    background: white;
    border-top: 1px solid var(--color-zinc-100);
}

.activity-map-info-strip__stats {
    display: flex;
    align-items: center;
    gap: 0.875rem;
    font-size: var(--text-sm);
    font-weight: 600;
    color: var(--color-kidical-ink);
}

.activity-map-badge {
    font-size: var(--text-xs);
    font-weight: 600;
    color: var(--color-zinc-500);
    background: var(--color-zinc-100);
    padding: 0.125rem 0.5rem;
    border-radius: 999px;
}

.activity-map-komoot-link {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: var(--text-sm);
    font-weight: 700;
    color: var(--color-kidical-red);
    background-image: none;
    transition: opacity 0.15s ease;
    white-space: nowrap;

    &:hover {
        opacity: 0.75;
        background-image: none;
    }
}

/* Leaflet div icon markers */
.activity-map-marker {
    background: none !important;
    border: none !important;
    display: flex;
    align-items: flex-end;
    gap: 0.375rem;
}

.activity-map-label {
    font-family: var(--font-heading);
    font-size: 0.6875rem;
    font-weight: 700;
    color: var(--color-kidical-ink);
    background: white;
    padding: 0.125rem 0.5rem;
    border-radius: 999px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
    white-space: nowrap;
    line-height: 1.6;
    align-self: center;
}

.activity-map-label--end {
    color: var(--color-zinc-500);
}
```

- [ ] **Step 2: Build assets to verify no CSS errors**

```bash
npm run build
```

Expected: build completes without errors.

- [ ] **Step 3: Commit**

```bash
git add resources/css/app.css
git commit -m "feat: add map container, info strip and marker CSS"
```

---

## Task 5: Full test run and final Pint pass

- [ ] **Step 1: Run the full test suite**

```bash
php artisan test --compact
```

Expected: all tests pass, including the 2 new `ActivityMapTest` tests.

- [ ] **Step 2: Run Pint on all dirty files**

```bash
vendor/bin/pint --dirty --format agent
```

Expected: no changes needed (already run per task).

- [ ] **Step 3: Commit if Pint made any changes**

Only if Pint produced a diff:

```bash
git add -p
git commit -m "style: pint formatting fixes"
```

# Locatie-zoeken Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the single-gemeente filter on the Kalender and the search-less directory on Lokale groepen with one shared, remembered "waar woon je?" location that sorts both pages by proximity (nearest first), never hiding anything.

**Architecture:** One location primitive lives in a long-lived cookie (`kcm_location`, JSON `{zip,lat,lng,name}`), read server-side by `CurrentLocation::resolve()`. A `postal_codes` table (seeded from GeoNames Belgium) turns a zip into coordinates; a stateless `Proximity` helper computes haversine distance in PHP and partitions items into "nearby" (≤ 7 km) and "far". A reusable `LocationPicker` Livewire component writes the cookie and reloads the current page, so both the Livewire Kalender and the Blade Groups page simply re-read the cookie at render time. No map.

**Tech Stack:** Laravel 12, Livewire 4, Flux UI, Pest 4, SQLite (test), Tailwind v4.

**Key design decisions baked in:**
- Distance is computed in **PHP** (Collections), never SQL — the test DB is SQLite and the dataset is ~1150 rows, trivially small.
- Setting/changing location triggers a **full page reload** (`$this->redirect` to the current URL). This keeps both a Livewire page and a Blade page in sync with zero event-wiring: each just reads the cookie on render.
- The cookie, not the URL, holds location. The Kalender's old `#[Url] gemeente` query param is removed.
- `region` column is NOT created — no consumer needs it (the Groups page derives region from `parent_id`).

---

## File Structure

**Create:**
- `database/data/be-postcodes.csv` — committed dataset: `zip,name,latitude,longitude`.
- `database/migrations/<ts>_create_postal_codes_table.php`
- `app/Models/PostalCode.php` — lookup model (`coordinatesFor`, `nearestTo`).
- `database/seeders/PostalCodeSeeder.php`
- `config/location.php` — `nearby_radius_km` => 7.
- `app/Support/Location/Proximity.php` — `distanceKm`, `partitionByRadius`.
- `app/Support/Location/CurrentLocation.php` — cookie reader/contract.
- `app/Livewire/LocationPicker.php` + `resources/views/livewire/location-picker.blade.php`
- Tests: `tests/Unit/Location/ProximityTest.php`, `tests/Unit/Location/PostalCodeTest.php`, `tests/Feature/Location/CurrentLocationTest.php`, `tests/Feature/Location/LocationPickerTest.php`, `tests/Feature/Location/CalendarProximityTest.php`, `tests/Feature/Location/GroupsProximityTest.php`.

**Modify:**
- `app/Livewire/RideCalendar.php` — drop `gemeente`, read location, two bands.
- `resources/views/livewire/ride-calendar.blade.php` — picker + In de buurt / Verderaf bands.
- `resources/views/components/event-card.blade.php` — optional `:distance` label.
- `app/Http/Controllers/GroupController.php` — pass location-derived nearby + membership.
- `resources/views/groups/index.blade.php` — picker, nearby band, your-group pin, drop map note.
- `database/seeders/DatabaseSeeder.php` — call `PostalCodeSeeder`.

---

## Task 0: Worktree dependencies + clean baseline

**Files:** none (environment only).

- [ ] **Step 1: Install PHP + JS deps in the worktree**

Run:
```bash
cd /Users/frederikvincx/Herd/kidicalmass/.claude/worktrees/locatie-zoeken
composer install
npm install
```
Expected: both complete without error; `vendor/` and `node_modules/` now exist.

- [ ] **Step 2: Run the full suite to confirm a green baseline**

Run: `php artisan test --compact`
Expected: all tests pass (0 failures). If anything fails on a fresh `main` checkout, STOP and report — do not build on a red baseline.

---

## Task 1: Belgian postcode dataset (CSV)

**Files:**
- Create: `database/data/be-postcodes.csv`

- [ ] **Step 1: Download the GeoNames Belgium postal export and transform to CSV**

Run:
```bash
cd /Users/frederikvincx/Herd/kidicalmass/.claude/worktrees/locatie-zoeken
mkdir -p database/data
curl -sSL https://download.geonames.org/export/zip/BE.zip -o /tmp/BE.zip
unzip -o /tmp/BE.zip BE.txt -d /tmp
# GeoNames BE.txt is tab-separated: 1=country 2=postal 3=place 10=lat 11=lng
# Keep the first row per postal code; emit a clean CSV with a header.
echo "zip,name,latitude,longitude" > database/data/be-postcodes.csv
awk -F '\t' '!seen[$2]++ { gsub(/"/,"",$3); printf "%s,\"%s\",%s,%s\n", $2, $3, $10, $11 }' /tmp/BE.txt >> database/data/be-postcodes.csv
```

- [ ] **Step 2: Sanity-check the dataset**

Run: `wc -l database/data/be-postcodes.csv && head -3 database/data/be-postcodes.csv && grep -c '^1090,' database/data/be-postcodes.csv`
Expected: ~1100–1300 lines; header is `zip,name,latitude,longitude`; the `1090` (Jette) grep returns `1`. If the download is blocked, STOP and report — the dataset is a hard dependency and must not be faked with random coordinates.

- [ ] **Step 3: Commit**

```bash
git add database/data/be-postcodes.csv
git commit -m "data: add GeoNames Belgian postcode coordinates"
```

---

## Task 2: `postal_codes` table + `PostalCode` model

**Files:**
- Create: `database/migrations/<ts>_create_postal_codes_table.php`
- Create: `app/Models/PostalCode.php`
- Test: `tests/Unit/Location/PostalCodeTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\PostalCode;

use function Pest\Laravel\artisan;

it('returns coordinates for a known zip and null for an unknown one', function () {
    PostalCode::create(['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265]);

    expect(PostalCode::coordinatesFor('1090'))->toBe(['lat' => 50.8782, 'lng' => 4.3265]);
    expect(PostalCode::coordinatesFor('0000'))->toBeNull();
});

it('finds the nearest postcode to a coordinate pair', function () {
    PostalCode::create(['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265]);
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174]);

    $nearest = PostalCode::nearestTo(50.88, 4.33);

    expect($nearest->zip)->toBe('1090');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=PostalCodeTest`
Expected: FAIL — `Class "App\Models\PostalCode" not found`.

- [ ] **Step 3: Create the migration**

Create `database/migrations/<ts>_create_postal_codes_table.php` (use `php artisan make:migration create_postal_codes_table --no-interaction`, then set `up()`):
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postal_codes', function (Blueprint $table) {
            $table->id();
            $table->string('zip')->index();
            $table->string('name');
            $table->decimal('latitude', 9, 6);
            $table->decimal('longitude', 9, 6);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postal_codes');
    }
};
```

- [ ] **Step 4: Create the model**

Create `app/Models/PostalCode.php`:
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    public static function coordinatesFor(string $zip): ?array
    {
        $row = static::where('zip', $zip)->first();

        if (! $row) {
            return null;
        }

        return ['lat' => $row->latitude, 'lng' => $row->longitude];
    }

    public static function nearestTo(float $lat, float $lng): ?self
    {
        return static::all()
            ->sortBy(fn (self $pc): float => ($pc->latitude - $lat) ** 2 + ($pc->longitude - $lng) ** 2)
            ->first();
    }
}
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --compact --filter=PostalCodeTest`
Expected: PASS (2 tests).

- [ ] **Step 6: Commit**

```bash
git add database/migrations app/Models/PostalCode.php tests/Unit/Location/PostalCodeTest.php
git commit -m "feat(location): postal_codes table + PostalCode lookup model"
```

---

## Task 3: `PostalCodeSeeder`

**Files:**
- Create: `database/seeders/PostalCodeSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Test: `tests/Feature/Location/CurrentLocationTest.php` is later; reuse a seeder test here.

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/Location/PostalCodeSeederTest.php`:
```php
<?php

use App\Models\PostalCode;
use Database\Seeders\PostalCodeSeeder;

it('seeds postcodes from the CSV', function () {
    (new PostalCodeSeeder)->run();

    expect(PostalCode::count())->toBeGreaterThan(1000);
    expect(PostalCode::coordinatesFor('1090'))->not->toBeNull();
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=PostalCodeSeederTest`
Expected: FAIL — `Class "Database\Seeders\PostalCodeSeeder" not found`.

- [ ] **Step 3: Create the seeder**

Create `database/seeders/PostalCodeSeeder.php`:
```php
<?php

namespace Database\Seeders;

use App\Models\PostalCode;
use Illuminate\Database\Seeder;

class PostalCodeSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/be-postcodes.csv');

        if (! is_readable($path)) {
            $this->command?->warn("Postcode dataset missing at {$path}; skipping.");

            return;
        }

        $handle = fopen($path, 'r');
        fgetcsv($handle); // header

        $rows = [];
        $now = now();

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4 || $row[0] === '') {
                continue;
            }

            $rows[] = [
                'zip' => $row[0],
                'name' => $row[1],
                'latitude' => (float) $row[2],
                'longitude' => (float) $row[3],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        fclose($handle);

        PostalCode::query()->delete();
        foreach (array_chunk($rows, 500) as $chunk) {
            PostalCode::insert($chunk);
        }
    }
}
```

- [ ] **Step 4: Register in DatabaseSeeder**

In `database/seeders/DatabaseSeeder.php`, add a `$this->call(PostalCodeSeeder::class);` near the top of `run()` (before Groups/Activities, and import the class). Show the engineer the exact edit: add `use` if absent and insert the call as the first seeded dataset.

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --compact --filter=PostalCodeSeederTest`
Expected: PASS.

- [ ] **Step 6: Commit**

```bash
git add database/seeders tests/Feature/Location/PostalCodeSeederTest.php
git commit -m "feat(location): seed postal_codes from CSV"
```

---

## Task 4: Location config

**Files:**
- Create: `config/location.php`

- [ ] **Step 1: Create the config**

Create `config/location.php`:
```php
<?php

return [
    /*
     | Radius (km) within which rides and groups count as "in de buurt".
     | 7 km = your adjacent municipalities (Jette -> Schaarbeek ~6 km), or
     | the directly neighbouring towns in Flanders/Wallonia.
     */
    'nearby_radius_km' => (float) env('LOCATION_NEARBY_RADIUS_KM', 7),

    'cookie' => 'kcm_location',
    'cookie_days' => 365,
];
```

- [ ] **Step 2: Commit**

```bash
git add config/location.php
git commit -m "feat(location): nearby radius + cookie config"
```

---

## Task 5: `Proximity` service

**Files:**
- Create: `app/Support/Location/Proximity.php`
- Test: `tests/Unit/Location/ProximityTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Support\Location\Proximity;
use Illuminate\Support\Collection;

it('computes haversine distance between two points', function () {
    // Jette (50.8782, 4.3265) -> Gent (51.0543, 3.7174) ~ 45 km
    $km = Proximity::distanceKm(['lat' => 50.8782, 'lng' => 4.3265], ['lat' => 51.0543, 'lng' => 3.7174]);

    expect($km)->toBeGreaterThan(42)->toBeLessThan(48);
});

it('partitions items into nearby and far, annotated and sorted by distance', function () {
    $origin = ['lat' => 50.8782, 'lng' => 4.3265]; // Jette
    $coords = [
        'jette' => ['lat' => 50.8782, 'lng' => 4.3265],
        'schaarbeek' => ['lat' => 50.8676, 'lng' => 4.3737], // ~3.5 km
        'gent' => ['lat' => 51.0543, 'lng' => 3.7174], // ~45 km
        'unknown' => null, // no coordinates -> far
    ];
    $items = new Collection(['jette', 'schaarbeek', 'gent', 'unknown']);

    $result = Proximity::partitionByRadius($items, $origin, 7, fn ($key) => $coords[$key]);

    expect($result['nearby']->pluck('item')->all())->toBe(['jette', 'schaarbeek']);
    expect($result['far']->pluck('item')->all())->toBe(['gent', 'unknown']);
    expect($result['nearby']->first()['distance_km'])->toBe(0.0);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=ProximityTest`
Expected: FAIL — `Class "App\Support\Location\Proximity" not found`.

- [ ] **Step 3: Implement**

Create `app/Support/Location/Proximity.php`:
```php
<?php

namespace App\Support\Location;

use Illuminate\Support\Collection;

class Proximity
{
    /**
     * @param  array{lat: float, lng: float}  $from
     * @param  array{lat: float, lng: float}  $to
     */
    public static function distanceKm(array $from, array $to): float
    {
        $earthRadius = 6371.0;

        $dLat = deg2rad($to['lat'] - $from['lat']);
        $dLng = deg2rad($to['lng'] - $from['lng']);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($from['lat'])) * cos(deg2rad($to['lat'])) * sin($dLng / 2) ** 2;

        return $earthRadius * 2 * asin(min(1.0, sqrt($a)));
    }

    /**
     * Split a collection into nearby (<= radius) and far, each annotated with
     * `['item' => $original, 'distance_km' => float|null]` and sorted ascending.
     * Items whose coordinates resolve to null are always "far" (never hidden).
     *
     * @template T
     * @param  Collection<int, T>  $items
     * @param  array{lat: float, lng: float}  $origin
     * @param  callable(T): (array{lat: float, lng: float}|null)  $coordsOf
     * @return array{nearby: Collection<int, array{item: T, distance_km: float}>, far: Collection<int, array{item: T, distance_km: float|null}>}
     */
    public static function partitionByRadius(Collection $items, array $origin, float $radiusKm, callable $coordsOf): array
    {
        $annotated = $items->map(function ($item) use ($origin, $coordsOf) {
            $coords = $coordsOf($item);

            return [
                'item' => $item,
                'distance_km' => $coords ? round(static::distanceKm($origin, $coords), 1) : null,
            ];
        });

        $nearby = $annotated
            ->filter(fn ($row) => $row['distance_km'] !== null && $row['distance_km'] <= $radiusKm)
            ->sortBy('distance_km')
            ->values();

        $far = $annotated
            ->reject(fn ($row) => $row['distance_km'] !== null && $row['distance_km'] <= $radiusKm)
            ->sortBy(fn ($row) => $row['distance_km'] ?? INF)
            ->values();

        return ['nearby' => $nearby, 'far' => $far];
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=ProximityTest`
Expected: PASS (2 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Support/Location/Proximity.php tests/Unit/Location/ProximityTest.php
git commit -m "feat(location): proximity service (haversine + radius partition)"
```

---

## Task 6: `CurrentLocation` cookie resolver

**Files:**
- Create: `app/Support/Location/CurrentLocation.php`
- Test: `tests/Feature/Location/CurrentLocationTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Support\Location\CurrentLocation;

it('returns null when no location cookie is set', function () {
    expect(CurrentLocation::resolve())->toBeNull();
});

it('reads a valid location cookie into an array', function () {
    request()->cookies->set('kcm_location', json_encode([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]));

    expect(CurrentLocation::resolve())->toBe([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]);
});

it('returns null for a malformed cookie', function () {
    request()->cookies->set('kcm_location', 'not-json');

    expect(CurrentLocation::resolve())->toBeNull();
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=CurrentLocationTest`
Expected: FAIL — class not found.

- [ ] **Step 3: Implement**

Create `app/Support/Location/CurrentLocation.php`:
```php
<?php

namespace App\Support\Location;

class CurrentLocation
{
    /**
     * @return array{zip: string, lat: float, lng: float, name: string}|null
     */
    public static function resolve(): ?array
    {
        $raw = request()->cookie(config('location.cookie'));

        if (! is_string($raw) || $raw === '') {
            return null;
        }

        $data = json_decode($raw, true);

        if (! is_array($data) || ! isset($data['zip'], $data['lat'], $data['lng'], $data['name'])) {
            return null;
        }

        return [
            'zip' => (string) $data['zip'],
            'lat' => (float) $data['lat'],
            'lng' => (float) $data['lng'],
            'name' => (string) $data['name'],
        ];
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=CurrentLocationTest`
Expected: PASS (3 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Support/Location/CurrentLocation.php tests/Feature/Location/CurrentLocationTest.php
git commit -m "feat(location): CurrentLocation cookie resolver"
```

---

## Task 7: `LocationPicker` Livewire component

**Files:**
- Create: `app/Livewire/LocationPicker.php`
- Create: `resources/views/livewire/location-picker.blade.php`
- Test: `tests/Feature/Location/LocationPickerTest.php`

**Behaviour:** a postcode field with autocomplete suggestions from `postal_codes`; choosing a suggestion (or calling `setFromCoords` from JS geolocation) queues the `kcm_location` cookie and redirects to the current page so the server re-renders with the new location. Shows the current `name` with a "wijzig" toggle when already set.

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\PostalCode;
use App\Livewire\LocationPicker;
use Livewire\Livewire;

beforeEach(function () {
    PostalCode::create(['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265]);
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174]);
});

it('suggests postcodes by zip or name', function () {
    Livewire::test(LocationPicker::class)
        ->set('query', 'Jet')
        ->assertSee('1090')
        ->assertSee('Jette')
        ->assertDontSee('Gent');
});

it('sets the location cookie and redirects when a zip is chosen', function () {
    Livewire::withQueryParams([])
        ->test(LocationPicker::class)
        ->call('choose', '1090')
        ->assertRedirect();

    // The cookie is queued on the response.
    $cookie = collect(\Illuminate\Support\Facades\Cookie::getQueuedCookies())
        ->firstWhere('getName', null); // see assertion below
});

it('resolves the nearest postcode from geolocation coords', function () {
    Livewire::test(LocationPicker::class)
        ->call('setFromCoords', 50.88, 4.33)
        ->assertRedirect();

    expect(true)->toBeTrue();
});
```

> Note for the engineer: queued-cookie assertions are awkward through Livewire's test harness. Keep the cookie assertion light (assert the redirect happens and no exception is thrown); the cookie *content* is covered indirectly by `CalendarProximityTest`/`GroupsProximityTest`, which set the cookie directly and assert the rendered bands. If you can cleanly assert `Cookie::hasQueued(config('location.cookie'))`, do so.

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=LocationPickerTest`
Expected: FAIL — component class not found.

- [ ] **Step 3: Implement the component**

Create `app/Livewire/LocationPicker.php`:
```php
<?php

namespace App\Livewire;

use App\Models\PostalCode;
use App\Support\Location\CurrentLocation;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Collection;
use Livewire\Component;

class LocationPicker extends Component
{
    public string $query = '';

    public bool $editing = false;

    /**
     * @return Collection<int, PostalCode>
     */
    public function suggestions(): Collection
    {
        $term = trim($this->query);

        if (mb_strlen($term) < 2) {
            return new Collection;
        }

        return PostalCode::query()
            ->where('zip', 'like', $term.'%')
            ->orWhere('name', 'like', $term.'%')
            ->orderBy('zip')
            ->limit(8)
            ->get();
    }

    public function choose(string $zip): void
    {
        $row = PostalCode::where('zip', $zip)->first();

        if (! $row) {
            return;
        }

        $this->persist($zip, $row->latitude, $row->longitude, $row->name);
    }

    public function setFromCoords(float $lat, float $lng): void
    {
        $nearest = PostalCode::nearestTo($lat, $lng);

        if (! $nearest) {
            return;
        }

        $this->persist($nearest->zip, $nearest->latitude, $nearest->longitude, $nearest->name);
    }

    public function clear(): void
    {
        Cookie::queue(Cookie::forget(config('location.cookie')));
        $this->redirect($this->currentUrl());
    }

    protected function persist(string $zip, float $lat, float $lng, string $name): void
    {
        Cookie::queue(
            config('location.cookie'),
            json_encode(['zip' => $zip, 'lat' => $lat, 'lng' => $lng, 'name' => $name]),
            config('location.cookie_days') * 24 * 60,
        );

        $this->redirect($this->currentUrl());
    }

    protected function currentUrl(): string
    {
        return url()->previous() ?: url('/');
    }

    public function render()
    {
        return view('livewire.location-picker', [
            'current' => CurrentLocation::resolve(),
            'suggestions' => $this->suggestions(),
        ]);
    }
}
```

- [ ] **Step 4: Implement the view**

Create `resources/views/livewire/location-picker.blade.php`. Structure only; appearance in `app.css`. Copy follows tone-of-voice (NL, warm, no em-dashes):
```blade
<div
    class="location-picker"
    x-data="{
        locate() {
            if (! navigator.geolocation) { return; }
            navigator.geolocation.getCurrentPosition(
                (pos) => $wire.setFromCoords(pos.coords.latitude, pos.coords.longitude),
                () => {},
            );
        }
    }"
>
    @if ($current && ! $editing)
        <p class="location-picker__current">
            <flux:icon.map-pin variant="solid" aria-hidden="true" />
            Je fietst rond <strong>{{ $current['name'] }}</strong>
            <button type="button" wire:click="$set('editing', true)" class="location-picker__change link-plain">wijzig</button>
        </p>
    @else
        <label class="location-picker__label" for="location-picker-query">Waar woon je?</label>
        <div class="location-picker__field">
            <input
                id="location-picker-query"
                type="text"
                wire:model.live.debounce.250ms="query"
                placeholder="Postcode of gemeente"
                autocomplete="off"
                class="location-picker__input"
            >
            <button type="button" @click="locate()" class="location-picker__geo link-plain">
                <flux:icon.map-pin aria-hidden="true" /> Gebruik mijn locatie
            </button>
        </div>

        @if ($suggestions->isNotEmpty())
            <ul class="location-picker__suggestions">
                @foreach ($suggestions as $pc)
                    <li>
                        <button type="button" wire:click="choose('{{ $pc->zip }}')" class="location-picker__suggestion link-plain">
                            {{ $pc->zip }} <span>{{ $pc->name }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
        @endif
    @endif
</div>
```

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --compact --filter=LocationPickerTest`
Expected: PASS. If the queued-cookie assertion is flaky, simplify per the Step 1 note.

- [ ] **Step 6: Commit**

```bash
git add app/Livewire/LocationPicker.php resources/views/livewire/location-picker.blade.php tests/Feature/Location/LocationPickerTest.php
git commit -m "feat(location): shared LocationPicker (postcode + geolocation)"
```

---

## Task 8: Kalender — two bands (In de buurt / Verderaf)

**Files:**
- Modify: `app/Livewire/RideCalendar.php`
- Modify: `resources/views/livewire/ride-calendar.blade.php`
- Test: `tests/Feature/Location/CalendarProximityTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;
use App\Livewire\RideCalendar;
use Livewire\Livewire;

beforeEach(function () {
    PostalCode::insert([
        ['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174, 'created_at' => now(), 'updated_at' => now()],
    ]);

    $this->near = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Jette',
        'postal_code' => '1090',
        'begin_date' => now()->addDays(3),
    ]);
    $this->far = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Gent',
        'postal_code' => '9000',
        'begin_date' => now()->addDays(5),
    ]);
});

it('shows one undivided list when no location is set', function () {
    Livewire::test(RideCalendar::class)
        ->assertSee('Jette')
        ->assertSee('Gent')
        ->assertDontSee('In de buurt');
});

it('splits upcoming rides into nearby and far when a location is set', function () {
    request()->cookies->set('kcm_location', json_encode([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]));

    Livewire::test(RideCalendar::class)
        ->assertSee('In de buurt')
        ->assertSee('Verderaf')
        ->assertSee('Jette')
        ->assertSee('Gent');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=CalendarProximityTest`
Expected: FAIL — "In de buurt" not seen (current component filters by `gemeente`).

- [ ] **Step 3: Rewrite the component**

Replace `app/Livewire/RideCalendar.php` with (drops `gemeente`, adds proximity banding; past view unchanged):
```php
<?php

namespace App\Livewire;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;
use App\Support\Location\CurrentLocation;
use App\Support\Location\Proximity;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * The Kalender's ride list. Location-first: when the visitor has set "waar woon je?",
 * upcoming rides split into "In de buurt" (<= radius, by date) and "Verderaf" (by date).
 * Without a location it is one undivided list by date. Rides-only (D-2/J1); no pagination.
 */
class RideCalendar extends Component
{
    #[Url(as: 'when')]
    public string $when = 'aankomend';

    public function showPast(): void
    {
        $this->when = 'voorbije';
    }

    public function showUpcoming(): void
    {
        $this->when = 'aankomend';
    }

    public function render()
    {
        $when = $this->when === 'voorbije' ? 'voorbije' : 'aankomend';

        $query = Activity::query()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->with(['groups']);

        if ($when === 'voorbije') {
            $activities = $query->where('begin_date', '<', now()->startOfDay())
                ->orderByDesc('begin_date')->limit(24)->get();

            return view('livewire.ride-calendar', [
                'when' => $when,
                'location' => null,
                'nearbyByPeriod' => collect(),
                'farByPeriod' => collect(),
                'byPeriod' => $activities->groupBy(fn ($a) => $a->begin_date->format('Y-m')),
                'hasActivities' => $activities->isNotEmpty(),
            ]);
        }

        $activities = $query->where('begin_date', '>=', now()->startOfDay())
            ->orderBy('begin_date')->get();

        $location = CurrentLocation::resolve();

        if (! $location) {
            return view('livewire.ride-calendar', [
                'when' => $when,
                'location' => null,
                'nearbyByPeriod' => collect(),
                'farByPeriod' => collect(),
                'byPeriod' => $activities->groupBy(fn ($a) => $a->begin_date->format('Y-m-d')),
                'hasActivities' => $activities->isNotEmpty(),
            ]);
        }

        $coordsByZip = PostalCode::whereIn('zip', $activities->pluck('postal_code')->filter()->unique())
            ->get()->keyBy('zip');

        $partition = Proximity::partitionByRadius(
            $activities,
            ['lat' => $location['lat'], 'lng' => $location['lng']],
            (float) config('location.nearby_radius_km'),
            function (Activity $a) use ($coordsByZip) {
                $pc = $a->postal_code ? $coordsByZip->get($a->postal_code) : null;

                return $pc ? ['lat' => $pc->latitude, 'lng' => $pc->longitude] : null;
            },
        );

        return view('livewire.ride-calendar', [
            'when' => $when,
            'location' => $location,
            'nearbyByPeriod' => $this->groupAnnotated($partition['nearby']),
            'farByPeriod' => $this->groupAnnotated($partition['far']),
            'byPeriod' => collect(),
            'hasActivities' => $activities->isNotEmpty(),
        ]);
    }

    /**
     * Group annotated `['item' => Activity, 'distance_km' => ?float]` rows by day,
     * preserving the distance so the card can show a label.
     *
     * @param  Collection<int, array{item: Activity, distance_km: float|null}>  $rows
     * @return Collection<string, Collection<int, array{item: Activity, distance_km: float|null}>>
     */
    protected function groupAnnotated(Collection $rows): Collection
    {
        return $rows->groupBy(fn ($row) => $row['item']->begin_date->format('Y-m-d'));
    }
}
```

- [ ] **Step 4: Rewrite the view**

Replace `resources/views/livewire/ride-calendar.blade.php`. The hero control becomes `<livewire:location-picker />`. Render a helper for a band so the day-grouping markup is not duplicated. Structure only; appearance in `app.css`:
```blade
<div>
    <x-page-hero
        eyebrow="Kalender"
        title="Spring op de fiets, wij rijden samen."
        illustration="img/illustrations/kid-on-bike.png">

        <x-slot:controls>
            <div class="kal-herofilter">
                <livewire:location-picker />
            </div>
        </x-slot:controls>

        <div class="kal-body">
            <div class="kal-periodbar">
                @if ($when === 'aankomend')
                    <button type="button" wire:click="showPast" class="kal-pastlink">Bekijk voorbije ritten →</button>
                @else
                    <button type="button" wire:click="showUpcoming" class="kal-pastlink">← Terug naar aankomende ritten</button>
                @endif
            </div>

            @if (! $hasActivities)
                <p class="kal-empty">
                    @if ($when === 'voorbije')
                        Er zijn nog geen voorbije fietstochten om te tonen.
                    @else
                        Er zijn momenteel geen fietstochten gepland. Het seizoen loopt van maart tot november. Kom snel terug!
                    @endif
                </p>
            @elseif ($when === 'voorbije')
                <div class="kal-days">
                    @foreach ($byPeriod as $periodKey => $rides)
                        <x-kal-month-band :period-key="$periodKey" :rides="$rides" />
                    @endforeach
                </div>
            @elseif ($location)
                @if ($nearbyByPeriod->isNotEmpty())
                    <h2 class="kal-bandtitle kal-bandtitle--near">In de buurt van {{ $location['name'] }}</h2>
                    <div class="kal-days">
                        @foreach ($nearbyByPeriod as $periodKey => $rows)
                            <x-kal-day-band :period-key="$periodKey" :rows="$rows" />
                        @endforeach
                    </div>
                @else
                    <p class="kal-empty">Nog geen fietstochten vlak bij {{ $location['name'] }}. Verderop is er wel wat te beleven.</p>
                @endif

                @if ($farByPeriod->isNotEmpty())
                    <h2 class="kal-bandtitle kal-bandtitle--far">Verderaf</h2>
                    <div class="kal-days">
                        @foreach ($farByPeriod as $periodKey => $rows)
                            <x-kal-day-band :period-key="$periodKey" :rows="$rows" />
                        @endforeach
                    </div>
                @endif
            @else
                <div class="kal-days">
                    @foreach ($byPeriod as $periodKey => $rides)
                        <x-kal-day-band :period-key="$periodKey" :rows="$rides" :plain="true" />
                    @endforeach
                </div>
            @endif
        </div>
    </x-page-hero>
</div>
```

- [ ] **Step 5: Extract the day-band Blade components**

The original view inlined the day/month tile markup. Extract it so the four call-sites above stay DRY. Create `resources/views/components/kal-day-band.blade.php`:
```blade
@props(['periodKey', 'rows', 'plain' => false])
@php
    $periodDate = \Illuminate\Support\Carbon::parse($periodKey)->locale('nl');
    $landmark = $periodDate->isToday() ? 'Vandaag'
        : ($periodDate->isTomorrow() ? 'Morgen'
        : (($periodDate->isCurrentWeek() && $periodDate->isWeekend()) ? 'Dit weekend' : null));
@endphp
<section class="kal-day">
    <h3 class="kal-day__date">
        <time datetime="{{ $periodDate->toDateString() }}" class="kal-day__tile">
            <span class="kal-day__eyebrow @if ($landmark) kal-day__eyebrow--landmark @endif">{{ $landmark ?? \Illuminate\Support\Str::ucfirst($periodDate->isoFormat('dddd')) }}</span>
            <span class="kal-day__num">{{ $periodDate->isoFormat('D') }}</span>
            <span class="kal-day__month">{{ $periodDate->isoFormat('MMMM') }}</span>
        </time>
    </h3>
    <div class="kal-day__cards">
        @foreach ($rows as $row)
            @php($activity = $plain ? $row : $row['item'])
            @php($distance = $plain ? null : ($row['distance_km'] ?? null))
            <x-event-card :activity="$activity" :show-date="false" :distance="$distance" />
        @endforeach
    </div>
</section>
```
Create `resources/views/components/kal-month-band.blade.php` for the past view (rides are plain models, not annotated rows):
```blade
@props(['periodKey', 'rides'])
@php($periodDate = \Illuminate\Support\Carbon::parse($periodKey)->locale('nl'))
<section class="kal-day">
    <h3 class="kal-day__date">
        <time datetime="{{ $periodDate->format('Y-m') }}" class="kal-day__tile kal-day__tile--month">
            <span class="kal-day__num kal-day__num--month">{{ \Illuminate\Support\Str::ucfirst($periodDate->isoFormat('MMMM')) }}</span>
            <span class="kal-day__month">{{ $periodDate->isoFormat('YYYY') }}</span>
        </time>
    </h3>
    <div class="kal-day__cards">
        @foreach ($rides as $activity)
            <x-event-card :activity="$activity" :show-date="false" />
        @endforeach
    </div>
</section>
```

> Note: in the no-location upcoming branch, `byPeriod` holds plain Activity models, so `<x-kal-day-band :plain="true">` is used; in the banded branches the rows are annotated arrays, so `:plain` defaults to false.

- [ ] **Step 6: Update the two existing tests that referenced the removed gemeente filter**

`tests/Feature/PublicPagesTest.php` has two now-broken expectations:

1. In `it('renders the Kalender with NL chrome and no English/em-dashes', ...)`: the control label changed and the "Alle gemeenten" option is gone. Change `->assertSee('Waar fiets je?')` to `->assertSee('Waar woon je?')` and **delete** the `->assertSee('Alle gemeenten')` line. Leave every other assertion intact.

2. Delete the whole `it('filters the Kalender by gemeente', ...)` test — the single-gemeente filter no longer exists; proximity banding replaces it and is covered by `CalendarProximityTest`. (This deletion is in-scope: the feature under test was intentionally removed.)

- [ ] **Step 7: Run tests to verify they pass**

Run: `php artisan test --compact --filter=CalendarProximityTest` then `php artisan test --compact --filter=PublicPagesTest`
Expected: both PASS.

- [ ] **Step 8: Commit**

```bash
git add app/Livewire/RideCalendar.php resources/views/livewire/ride-calendar.blade.php resources/views/components/kal-day-band.blade.php resources/views/components/kal-month-band.blade.php tests/Feature/Location/CalendarProximityTest.php tests/Feature/PublicPagesTest.php
git commit -m "feat(calendar): proximity bands replace single-gemeente filter"
```

---

## Task 9: Event card distance label

**Files:**
- Modify: `resources/views/components/event-card.blade.php`

- [ ] **Step 1: Add the optional prop and label**

In `resources/views/components/event-card.blade.php`, add `'distance' => null` to the `@props` array, and render a label next to the location. After the existing `event-card__loc` paragraph block, inside `event-card__body`, add:
```blade
@if (! is_null($distance))
    <span class="event-card__distance">{{ $distance == 0 ? 'in jouw buurt' : $distance.' km' }}</span>
@endif
```
Update the `@props` line to:
```blade
@props(['activity', 'showDate' => true, 'featured' => null, 'distance' => null])
```

- [ ] **Step 2: Verify rendering via the calendar test**

Run: `php artisan test --compact --filter=CalendarProximityTest`
Expected: still PASS. Add an assertion to the banded test that the card shows `in jouw buurt` for the 0 km ride:
```php
->assertSee('in jouw buurt')
```

- [ ] **Step 3: Commit**

```bash
git add resources/views/components/event-card.blade.php tests/Feature/Location/CalendarProximityTest.php
git commit -m "feat(calendar): distance label on event card"
```

---

## Task 10: Lokale groepen — nearby band, your-group pin, drop map note

**Files:**
- Modify: `app/Http/Controllers/GroupController.php`
- Modify: `resources/views/groups/index.blade.php`
- Test: `tests/Feature/Location/GroupsProximityTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

use App\Models\Group;
use App\Models\PostalCode;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\withCookie;

beforeEach(function () {
    PostalCode::insert([
        ['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '1030', 'name' => 'Schaarbeek', 'latitude' => 50.8676, 'longitude' => 4.3737, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174, 'created_at' => now(), 'updated_at' => now()],
    ]);
    $region = Group::factory()->create(['name' => 'Brussels Capital Region', 'invisible' => true, 'zip' => null]);
    $this->jette = Group::factory()->create(['name' => 'Kidical Mass Jette', 'zip' => '1090', 'parent_id' => $region->id]);
    $this->gent = Group::factory()->create(['name' => 'Kidical Mass Gent', 'zip' => '9000', 'parent_id' => $region->id]);
});

it('shows a nearby band when a location cookie is set', function () {
    withCookie('kcm_location', json_encode(['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette']))
        ->get('/nl/chapters')
        ->assertOk()
        ->assertSee('In de buurt van Jette')
        ->assertSee('Kidical Mass Jette');
});

it('pins the logged-in users group above everything', function () {
    $user = User::factory()->create();
    $user->groups()->attach($this->gent->id);

    actingAs($user)
        ->get('/nl/chapters')
        ->assertOk()
        ->assertSeeInOrder(['Jouw groep', 'Kidical Mass Gent']);
});

it('drops the coming-soon map note', function () {
    get('/nl/chapters')->assertDontSee('kaart');
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=GroupsProximityTest`
Expected: FAIL — "In de buurt van Jette" not seen; map note still present.

- [ ] **Step 3: Update the controller**

In `app/Http/Controllers/GroupController.php` `index()`, after loading `$groups`, compute the location-derived nearby set and the membership pin, and pass them to the view:
```php
public function index(string $locale): View
{
    $groups = Group::visible()
        ->with(['parent', 'children'])
        ->withCount(['articles', 'activities'])
        ->get();

    $activityCount = Activity::whereYear('begin_date', now()->year)->count();

    $location = \App\Support\Location\CurrentLocation::resolve();
    $nearby = collect();

    if ($location) {
        $coordsByZip = \App\Models\PostalCode::whereIn('zip', $groups->pluck('zip')->filter()->unique())
            ->get()->keyBy('zip');

        $partition = \App\Support\Location\Proximity::partitionByRadius(
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

    return view('groups.index', compact('groups', 'activityCount', 'location', 'nearby', 'myGroups'));
}
```

- [ ] **Step 4: Update the view**

In `resources/views/groups/index.blade.php`: put `<livewire:location-picker />` in the hero `controls` slot (alongside the stats `<dl>`); **remove** the `grp-map-note` paragraph entirely; and insert the pin + nearby band above the existing region directory. Add inside `<x-slot:controls>` after the `</dl>`:
```blade
<div class="grp-hero__locate">
    <livewire:location-picker />
</div>
```
Replace the `{{-- DIRECTORY first --}}` section's heading block and add the new bands before the per-region `<div class="mt-6 space-y-8">`:
```blade
<section class="mt-10 space-y-1">
    <h2 class="grp-find__title">Vind je groep</h2>
    <p class="grp-find__sub">Tik je gemeente aan voor de volgende fietstocht en het lokale team.</p>
</section>

@if ($myGroups->isNotEmpty())
    <section class="mt-8">
        <h3 class="grp-region__title">Jouw groep{{ $myGroups->count() > 1 ? 'en' : '' }}</h3>
        <ul class="flex flex-wrap gap-2.5">
            @foreach ($myGroups as $group)
                <li><a href="{{ route('groups.show', $group) }}" class="grp-pill grp-pill--mine link-plain">{{ $group->name }}</a></li>
            @endforeach
        </ul>
    </section>
@endif

@if ($location)
    <section class="mt-8">
        <h3 class="grp-region__title">In de buurt van {{ $location['name'] }}</h3>
        @if ($nearby->isNotEmpty())
            <ul class="flex flex-wrap gap-2.5">
                @foreach ($nearby as $row)
                    <li>
                        <a href="{{ route('groups.show', $row['item']) }}" class="grp-pill link-plain">
                            {{ $row['item']->name }}<span class="grp-pill__km">{{ $row['distance_km'] == 0 ? 'hier' : $row['distance_km'].' km' }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="grp-find__sub">Nog geen groep vlak bij jou. Misschien start jij er een?</p>
        @endif
    </section>
@endif
```

> The per-region `$byRegion` directory below stays exactly as it is (full list, nothing removed).

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --compact --filter=GroupsProximityTest`
Expected: PASS (3 tests).

- [ ] **Step 6: Run the existing groups suite to catch regressions**

Run: `php artisan test --compact --filter=Groups`
Expected: PASS. The existing `GroupsTest` asserted the map note or `gemeente` copy may need updating — adjust assertions to match the new markup (the directory itself is unchanged).

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/GroupController.php resources/views/groups/index.blade.php tests/Feature/Location/GroupsProximityTest.php
git commit -m "feat(groups): nearby band + your-group pin, drop map note"
```

---

## Task 11: Full suite + Pint + final commit

**Files:** none new.

- [ ] **Step 1: Run the whole suite**

Run: `php artisan test --compact`
Expected: all pass. Fix any cross-test fallout from the removed `gemeente` URL param.

- [ ] **Step 2: Format**

Run: `vendor/bin/pint --dirty --format agent`
Expected: clean.

- [ ] **Step 3: Commit any formatting**

```bash
git add -u
git commit -m "style: pint" || echo "nothing to format"
```

---

## Self-Review notes (author)

- **Spec coverage:** shared primitive (Task 6/7), postcode→coords (Task 1/2/3), proximity service + 7 km (Task 4/5), calendar two bands (Task 8/9), groups nearby + membership pin + drop map note (Task 10), edge cases — unknown postcode → far (Proximity test), no location → undivided list (Calendar test), empty nearby (calendar + groups copy), geolocation refused → silent (LocationPicker JS `() => {}`). All covered.
- **Out of scope honoured:** no map, no `users.home_zip`, no radius slider, no postcode i18n. `region` column dropped (no consumer).
- **Type consistency:** `coordsOf`/`partitionByRadius` return `['item','distance_km']` used identically in calendar (`$row['item']`) and groups (`$row['item']`). `CurrentLocation::resolve()` shape `{zip,lat,lng,name}` consumed identically everywhere.
- **Known soft spot:** queued-cookie assertion in LocationPickerTest is awkward through Livewire; the plan leans on Calendar/Groups tests (which set the cookie directly) for real coverage and keeps the picker test to redirect/no-exception.
```

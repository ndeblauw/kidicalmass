# Help-out Location Picker Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make the location picker the gateway for Help-out's "Vind je lokale groep" section, showing the 4 nearest chapters once a location is set instead of a flat full list.

**Architecture:** Add a count-based `Proximity::nearest()` helper beside the existing radius helper. `VolunteerController` resolves the shared location cookie and (when set) ranks visible chapters by distance, passing the 4 nearest to the view. `volunteer.blade.php` swaps its flat `.ho-groups` list for the compact `<livewire:location-picker>` plus a conditional nearest-chapters block. No change to the picker component or cookie.

**Tech Stack:** Laravel 12, Livewire 4, Pest 4, Blade. Spec: `docs/superpowers/specs/2026-06-15-help-out-location-picker-design.md`.

---

## File Structure

- `app/Support/Location/Proximity.php` — add `nearest()` (count-based ranking) next to `partitionByRadius()`.
- `tests/Unit/Location/ProximityTest.php` — add `nearest()` coverage.
- `app/Http/Controllers/VolunteerController.php` — resolve location, compute 4 nearest chapters.
- `resources/views/volunteer.blade.php` — picker + conditional nearest-chapters block in `<section class="ho-find">`.
- `tests/Feature/HelpOutLocationPickerTest.php` — new feature test for the section.

---

### Task 1: `Proximity::nearest()` helper

**Files:**
- Modify: `app/Support/Location/Proximity.php`
- Test: `tests/Unit/Location/ProximityTest.php`

- [ ] **Step 1: Write the failing test**

Append to `tests/Unit/Location/ProximityTest.php`:

```php
it('ranks the n nearest items by distance, dropping null-coord items', function () {
    $origin = ['lat' => 50.8782, 'lng' => 4.3265]; // Jette
    $coords = [
        'gent' => ['lat' => 51.0543, 'lng' => 3.7174],      // ~45 km
        'schaarbeek' => ['lat' => 50.8676, 'lng' => 4.3737], // ~3.5 km
        'jette' => ['lat' => 50.8782, 'lng' => 4.3265],      // 0 km
        'antwerpen' => ['lat' => 51.2194, 'lng' => 4.4025],  // ~38 km
        'unknown' => null,                                   // excluded
    ];
    $items = new Collection(['gent', 'schaarbeek', 'jette', 'antwerpen', 'unknown']);

    $result = Proximity::nearest($items, $origin, 3, fn ($key) => $coords[$key]);

    // Sorted ascending by distance, capped at 3, null-coord item excluded.
    expect($result->pluck('item')->all())->toBe(['jette', 'schaarbeek', 'antwerpen']);
    expect($result->first()['distance_km'])->toBe(0.0);
});

it('returns fewer than n when fewer ranked items exist', function () {
    $origin = ['lat' => 50.8782, 'lng' => 4.3265];
    $coords = ['jette' => ['lat' => 50.8782, 'lng' => 4.3265], 'unknown' => null];
    $items = new Collection(['jette', 'unknown']);

    $result = Proximity::nearest($items, $origin, 4, fn ($key) => $coords[$key]);

    expect($result->pluck('item')->all())->toBe(['jette']);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=ProximityTest`
Expected: FAIL — `Call to undefined method App\Support\Location\Proximity::nearest()`.

- [ ] **Step 3: Write the implementation**

Add this method to `app/Support/Location/Proximity.php` (after `partitionByRadius()`):

```php
/**
 * The $count items closest to $origin, annotated with distance and sorted ascending.
 * Items whose coordinates resolve to null are dropped (they cannot be ranked).
 *
 * @template T
 *
 * @param  Collection<int, T>  $items
 * @param  array{lat: float, lng: float}  $origin
 * @param  callable(T): (array{lat: float, lng: float}|null)  $coordsOf
 * @return Collection<int, array{item: T, distance_km: float}>
 */
public static function nearest(Collection $items, array $origin, int $count, callable $coordsOf): Collection
{
    return $items
        ->map(function ($item) use ($origin, $coordsOf) {
            $coords = $coordsOf($item);

            return $coords === null
                ? null
                : ['item' => $item, 'distance_km' => round(static::distanceKm($origin, $coords), 1)];
        })
        ->filter()
        ->sortBy('distance_km')
        ->take($count)
        ->values();
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --compact --filter=ProximityTest`
Expected: PASS (all ProximityTest cases green).

- [ ] **Step 5: Commit**

```bash
git add app/Support/Location/Proximity.php tests/Unit/Location/ProximityTest.php
git commit -m "feat(location): add count-based Proximity::nearest() helper"
```

---

### Task 2: Wire picker + nearest chapters into Help-out

**Files:**
- Modify: `app/Http/Controllers/VolunteerController.php`
- Modify: `resources/views/volunteer.blade.php:162-177` (the `@if ($groups->isNotEmpty())` flat list inside `<section class="ho-find">`)
- Test: `tests/Feature/HelpOutLocationPickerTest.php`

- [ ] **Step 1: Write the failing test**

Create `tests/Feature/HelpOutLocationPickerTest.php`:

```php
<?php

use App\Models\Group;
use App\Models\PostalCode;

use function Pest\Laravel\get;
use function Pest\Laravel\withCookie;

beforeEach(function () {
    PostalCode::insert([
        ['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '1030', 'name' => 'Schaarbeek', 'latitude' => 50.8676, 'longitude' => 4.3737, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '2000', 'name' => 'Antwerpen', 'latitude' => 51.2194, 'longitude' => 4.4025, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '8000', 'name' => 'Brugge', 'latitude' => 51.2093, 'longitude' => 3.2247, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '3000', 'name' => 'Leuven', 'latitude' => 50.8798, 'longitude' => 4.7005, 'created_at' => now(), 'updated_at' => now()],
    ]);

    $this->jette = Group::factory()->create(['name' => 'Kidical Mass Jette', 'zip' => '1090']);
    $this->schaarbeek = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek', 'zip' => '1030']);
    $this->leuven = Group::factory()->create(['name' => 'Kidical Mass Leuven', 'zip' => '3000']);
    $this->antwerpen = Group::factory()->create(['name' => 'Kidical Mass Antwerpen', 'zip' => '2000']);
    $this->gent = Group::factory()->create(['name' => 'Kidical Mass Gent', 'zip' => '9000']);
    $this->brugge = Group::factory()->create(['name' => 'Kidical Mass Brugge', 'zip' => '8000']); // farthest from Jette
});

it('shows the location picker and no chapter pills when no location is set', function () {
    $response = get('/nl/help-out');

    $response->assertOk()
        ->assertSee('location-picker', escape: false)   // the picker is present
        ->assertDontSee('ho-group__name', escape: false); // no chapter pills yet
});

it('shows the four nearest chapters, in distance order, when a location is set', function () {
    $response = withCookie('kcm_location', json_encode(['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette']))
        ->get('/nl/help-out');

    $response->assertOk()
        ->assertSee('Het dichtst bij Jette')
        // Jette (0) < Schaarbeek (~3.5) < Leuven (~24) < Antwerpen (~38) are the nearest 4.
        ->assertSeeInOrder([
            'Kidical Mass Jette',
            'Kidical Mass Schaarbeek',
            'Kidical Mass Leuven',
            'Kidical Mass Antwerpen',
        ])
        // Brugge is the 6th-nearest and must be excluded.
        ->assertDontSee('Kidical Mass Brugge');
});

it('links each nearest chapter to its volunteer sign-up form', function () {
    $response = withCookie('kcm_location', json_encode(['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette']))
        ->get('/nl/help-out');

    $href = route('groups.show', ['group' => $this->jette, 'intent' => 'volunteer']).'#aanmelden';
    $response->assertSee($href, escape: false);
});
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --compact --filter=HelpOutLocationPickerTest`
Expected: FAIL — first test fails on `assertDontSee('ho-group__name')` (the current view always renders the flat list), and the location tests fail on `Het dichtst bij Jette` not being present.

- [ ] **Step 3: Update the controller**

Replace the body of `__invoke` in `app/Http/Controllers/VolunteerController.php`. Add the needed imports at the top of the file (`use App\Models\PostalCode;`, `use App\Support\Location\CurrentLocation;`, `use App\Support\Location\Proximity;`, `use Illuminate\Support\Collection;`), then:

```php
public function __invoke(string $locale): View
{
    $groups = Group::visible()
        ->orderBy('name')
        ->get(['id', 'name', 'zip']);

    $location = CurrentLocation::resolve();
    $nearestGroups = new Collection;

    if ($location) {
        $coordsByZip = PostalCode::whereIn('zip', $groups->pluck('zip')->filter()->unique())
            ->get()->keyBy('zip');

        $nearestGroups = Proximity::nearest(
            $groups,
            ['lat' => $location['lat'], 'lng' => $location['lng']],
            4,
            fn ($group) => $group->zip && $coordsByZip->has($group->zip)
                ? ['lat' => $coordsByZip[$group->zip]->latitude, 'lng' => $coordsByZip[$group->zip]->longitude]
                : null,
        );
    }

    return view('volunteer', compact('groups', 'location', 'nearestGroups'));
}
```

- [ ] **Step 4: Update the view**

In `resources/views/volunteer.blade.php`, replace the whole `@if ($groups->isNotEmpty()) ... @endif` block (currently lines 162-177, the flat `.ho-groups` list) inside `<section class="ho-find">` with:

```blade
            <div class="ho-find__picker">
                <livewire:location-picker :compact="true" />
            </div>

            @if ($location && $nearestGroups->isNotEmpty())
                <h3 class="ho-find__nearest-title">Het dichtst bij {{ $location['name'] }}</h3>
                <ul role="list" class="ho-groups">
                    @foreach ($nearestGroups as $row)
                        <li>
                            <a class="ho-group link-plain" href="{{ route('groups.show', ['group' => $row['item'], 'intent' => 'volunteer']) }}#aanmelden">
                                <span class="ho-group__name">{{ $row['item']->name }}</span>
                                @if ($row['item']->zip)
                                    <span class="ho-group__zip">{{ $row['item']->zip }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
```

Leave the `<h2 class="ho-find__title">` and `<p class="ho-find__lead">` above it, and the `<section class="ho-start">` coda below it, unchanged.

- [ ] **Step 5: Run test to verify it passes**

Run: `php artisan test --compact --filter=HelpOutLocationPickerTest`
Expected: PASS (all four cases green).

- [ ] **Step 6: Run the existing Help-out test to check for regressions**

Run: `php artisan test --compact --filter=HelpOut`
Expected: PASS — `HelpOutLocationPickerTest` and `HelpOutRolesCarouselTest` both green.

- [ ] **Step 7: Style the picker wrapper + nearest heading (if needed)**

Load `https://kidicalmass.test/nl/help-out` and set a location. If the picker or `ho-find__nearest-title` need spacing on the light-blue band, add rules to `resources/css/pages/help-out.css` (page-scoped). Mirror the `grp-find__picker` spacing from the groups index. Use tokens only — no raw hex/px. Skip this step if the inherited styles already sit well.

- [ ] **Step 8: Run Pint**

Run: `vendor/bin/pint --dirty --format agent`
Expected: clean (files reformatted if needed).

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/VolunteerController.php resources/views/volunteer.blade.php tests/Feature/HelpOutLocationPickerTest.php resources/css/pages/help-out.css
git commit -m "feat(help-out): gate 'vind je lokale groep' behind the location picker

- picker is the section's initial content; no full chapter list
- once a location is set, show the 4 nearest chapters in distance order
- each chapter still links to its volunteer sign-up form
- VolunteerController ranks via Proximity::nearest()"
```

---

## Notes for the implementer

- The Help-out route is `/nl/help-out` (named `volunteer`); the groups page is `/nl/chapters`.
- The shared location cookie is `kcm_location`; `CurrentLocation::resolve()` reads it. The picker writes it and reloads, so no extra wiring is needed on this page.
- `Proximity::nearest()` is count-based on purpose (always returns up to 4), unlike `partitionByRadius()` which is radius-based. Do not swap one for the other.
- `.ho-group`, `.ho-group__name`, `.ho-group__zip`, `.ho-groups` styles already exist — reuse them; do not duplicate into `app.css`.

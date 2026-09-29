<?php

// The pure geometry behind <x-belgium-map>: projection, outline, Brussels
// collapse and greedy label placement. No database, no framework.

use App\Enums\Region;
use App\Support\Map\BelgiumMap;

/** A 4° x 1° box around Belgium's latitude: easy corners to reason about. */
function boxMap(): BelgiumMap
{
    return new BelgiumMap([[2.0, 51.0], [6.0, 51.0], [6.0, 50.0], [2.0, 50.0]]);
}

/** @return array{name: string, region: ?Region, lat: ?float, lng: ?float} */
function mapGroup(string $name, ?Region $region, ?float $lat = null, ?float $lng = null): array
{
    return ['name' => $name, 'region' => $region, 'lat' => $lat, 'lng' => $lng];
}

it('projects lon/lat into the viewBox with longitude scaled by cos(50.5°)', function () {
    $map = boxMap();
    $padding = BelgiumMap::PADDING;
    $scale = (BelgiumMap::WIDTH - 2 * $padding) / (4 * cos(deg2rad(50.5)));

    expect($map->project(2.0, 51.0))->toBe(['x' => (float) $padding, 'y' => (float) $padding])
        ->and($map->project(6.0, 50.0))->toBe([
            'x' => (float) (BelgiumMap::WIDTH - $padding),
            'y' => round($padding + $scale, 1),
        ])
        ->and($map->height())->toBe((int) ceil($scale + 2 * $padding));
});

it('draws the outline with the same projection as the markers', function () {
    $map = boxMap();
    $bottom = $map->project(6.0, 50.0)['y'];

    expect($map->outlinePath())->toBe("M10 10L590 10L590 {$bottom}L10 {$bottom}Z");
});

it('collapses Brussels groups into one counted bubble and draws the rest as dots', function () {
    $result = boxMap()->compose([
        mapGroup('Elsene', Region::BRUSSELS),                        // no coordinates needed
        mapGroup('Jette', Region::BRUSSELS, 50.87, 4.33),
        mapGroup('Gent', Region::FLANDERS, 50.5, 3.0),
        mapGroup('Nergens', Region::WALLONIA),                        // no centroid: no dot
        mapGroup('Los', null, 50.5, 5.0),                            // no region: fallback colour
        mapGroup('Verdwaald', Region::FLANDERS, 0.0, 0.0),           // bad centroid far outside: skipped
    ], 'Brussel');

    expect($result['bubble'])->toMatchArray(['name' => 'Brussel', 'count' => 2, 'colorToken' => '--color-kidical-blue'])
        ->and(array_column($result['dots'], 'name'))->toBe(['Gent', 'Los'])
        ->and(array_column($result['dots'], 'colorToken'))->toBe(['--color-kidical-green', Region::FALLBACK_COLOR_TOKEN])
        // Counts describe the drawing (the map's accessible name): undrawn groups are left out.
        ->and($result['counts'])->toBe(['brussels' => 2, 'flanders' => 1, 'other' => 1]);
});

it('has no bubble when there are no Brussels groups', function () {
    expect(boxMap()->compose([mapGroup('Gent', Region::FLANDERS, 50.5, 3.0)], 'Brussel')['bubble'])->toBeNull();
});

it('places labels right, then left, above, below, and drops one that fits nowhere', function () {
    // Five groups on the very same spot: each next label must dodge the ones already placed.
    // One-letter names keep the above/below boxes clear of the side labels' padding.
    $groups = array_map(fn (string $name) => mapGroup($name, Region::WALLONIA, 50.5, 4.0), ['A', 'B', 'C', 'D', 'E']);

    $labels = array_column(boxMap()->compose($groups, 'Brussel')['dots'], 'label', 'name');

    expect(array_map(fn (?array $label) => $label['anchor'] ?? null, $labels))->toBe([
        'A' => 'start',   // right
        'B' => 'end',     // left
        'C' => 'middle',  // above
        'D' => 'middle',  // below
        'E' => null,      // dropped, the dot stays
    ])->and($labels['C']['y'])->toBeLessThan($labels['D']['y']);
});

it('moves a label to the left when the right side would leave the viewBox', function () {
    $dot = boxMap()->compose([mapGroup('Oostgrens', Region::WALLONIA, 50.5, 5.99)], 'Brussel')['dots'][0];

    expect($dot['label']['anchor'])->toBe('end')
        ->and($dot['label']['x'])->toBeLessThan($dot['x']);
});

it('keeps a dot label clear of the Brussels bubble', function () {
    // A dot just east of the bubble: its natural left-hand spot would sit on the bubble.
    $centre = BelgiumMap::BRUSSELS_CENTRE;
    $result = boxMap()->compose([
        ...array_fill(0, 16, mapGroup('X', Region::BRUSSELS)),
        mapGroup('Buur', Region::FLANDERS, $centre['lat'], $centre['lng'] + 0.2),
    ], 'Brussel');

    $bubble = $result['bubble'];
    $label = $result['dots'][0]['label'];

    expect($label['anchor'])->toBe('start')
        ->and($label['x'])->toBeGreaterThan($bubble['x'] + $bubble['r']);
});

it('never lets two nearby labels touch: every box keeps its padding', function () {
    // Two dots stacked about one line apart: unpadded, both right-hand labels
    // would fit with barely a unit between them and read as one two-line label.
    $dots = boxMap()->compose([
        mapGroup('Terhulpen', Region::WALLONIA, 50.72, 4.30),
        mapGroup('Tubeke', Region::WALLONIA, 50.65, 4.30),
    ], 'Brussel')['dots'];

    $boxes = collect($dots)->filter(fn (array $dot) => $dot['label'] !== null)->map(function (array $dot) {
        $width = mb_strlen($dot['name']) * BelgiumMap::FONT_SIZE * 0.58;
        $x1 = match ($dot['label']['anchor']) {
            'start' => $dot['label']['x'],
            'end' => $dot['label']['x'] - $width,
            default => $dot['label']['x'] - $width / 2,
        };

        return [$x1, $dot['label']['y'] - 0.8 * BelgiumMap::FONT_SIZE, $x1 + $width, $dot['label']['y'] + 0.25 * BelgiumMap::FONT_SIZE];
    })->values();

    expect($boxes)->toHaveCount(2);
    foreach ($boxes as $i => $a) {
        foreach ($boxes->slice($i + 1) as $b) {
            $gap = max($b[0] - $a[2], $a[0] - $b[2], $b[1] - $a[3], $a[1] - $b[3]);
            expect($gap)->toBeGreaterThanOrEqual(4.0);
        }
    }
});

it('keeps the Brussels label when neighbouring groups surround the bubble', function () {
    $result = boxMap()->compose([
        mapGroup('Elsene', Region::BRUSSELS),
        mapGroup('Dilbeek', Region::FLANDERS, 50.85, 4.26),
        mapGroup('Zaventem', Region::FLANDERS, 50.88, 4.47),
        mapGroup('Vilvoorde', Region::FLANDERS, 50.93, 4.43),
        mapGroup('Halle', Region::FLANDERS, 50.73, 4.24),
    ], 'Brussel');

    expect($result['bubble']['label'])->not->toBeNull();
});

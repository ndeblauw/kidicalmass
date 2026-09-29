<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;
use App\Support\Location\NextRideFinder;

use function Pest\Laravel\withCookie;

test('finder returns a grouped preview of upcoming rides', function () {
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDays(3),
    ]);
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDays(10),
    ]);

    $result = NextRideFinder::find(null);

    expect($result['has_upcoming'])->toBeTrue()
        ->and($result['ride'])->toBeNull()
        ->and($result['upcoming_preview'])->toBeArray()
        ->and($result['upcoming_preview'])->not->toBeEmpty();

    $firstDay = $result['upcoming_preview'][0];
    expect($firstDay['rows'][0]['item'])->toBeInstanceOf(Activity::class);
});

test('preview is empty when there are no upcoming rides', function () {
    expect(NextRideFinder::find(null)['upcoming_preview'])->toBe([]);
});

test('homepage lists far-away rides nearest first, each with its distance', function () {
    PostalCode::insert([
        ['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '3000', 'name' => 'Leuven', 'latitude' => 50.8798, 'longitude' => 4.7005, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '6700', 'name' => 'Arlon', 'latitude' => 49.6833, 'longitude' => 5.8167, 'created_at' => now(), 'updated_at' => now()],
    ]);

    // Soonest is farthest, so a date-ordered list would read Arlon, Gent, Leuven.
    foreach (['6700' => 2, '9000' => 4, '3000' => 6] as $zip => $days) {
        Activity::factory()->create([
            'activity_type' => ActivityType::KIDICALMASS,
            'title_nl' => "Parade {$zip}",
            'postal_code' => $zip,
            'begin_date' => now()->addDays($days),
        ]);
    }

    $jette = ['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette'];

    $rows = collect(NextRideFinder::find($jette)['upcoming_preview'])->pluck('rows')->flatten(1);

    expect($rows->map(fn (array $row): string => $row['item']->postal_code)->all())->toBe(['3000', '9000', '6700'])
        ->and($rows->pluck('distance_km')->every(fn ($km): bool => $km > 5))->toBeTrue();

    $response = withCookie('kcm_location', json_encode($jette))->get('/nl')->assertOk();

    $response->assertSee(__('home.next_rides.far_away'))
        ->assertSeeInOrder(['Parade 3000', 'Parade 9000', 'Parade 6700']);

    expect(substr_count($response->getContent(), 'data-distance-km='))->toBe(3);
});

test('far-away rides stay nearest first when two of them share a day', function () {
    PostalCode::insert([
        ['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '3000', 'name' => 'Leuven', 'latitude' => 50.8798, 'longitude' => 4.7005, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '6700', 'name' => 'Arlon', 'latitude' => 49.6833, 'longitude' => 5.8167, 'created_at' => now(), 'updated_at' => now()],
    ]);

    // Leuven and Arlon ride on the same day; Gent, in between by distance, rides earlier.
    foreach (['3000' => 5, '9000' => 2, '6700' => 5] as $zip => $days) {
        Activity::factory()->create([
            'activity_type' => ActivityType::KIDICALMASS,
            'title_nl' => "Parade {$zip}",
            'postal_code' => $zip,
            'begin_date' => now()->addDays($days),
        ]);
    }

    $jette = ['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette'];

    $rows = collect(NextRideFinder::find($jette)['upcoming_preview'])->pluck('rows')->flatten(1);

    expect($rows->map(fn (array $row): string => $row['item']->postal_code)->all())->toBe(['3000', '9000', '6700']);

    withCookie('kcm_location', json_encode($jette))->get('/nl')->assertOk()
        ->assertSeeInOrder(['Parade 3000', 'Parade 9000', 'Parade 6700']);
});

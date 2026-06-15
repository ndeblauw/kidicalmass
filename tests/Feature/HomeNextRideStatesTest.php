<?php

use App\Enums\ActivityType;
use App\Models\Activity;

beforeEach(function () {
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDays(5),
    ]);
});

test('no-location state shows generic heading, rides, picker and the agenda link', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Volgende ritten');
    $response->assertDontSee('De volgende rit bij jou');
    $response->assertSee('location-picker', false);
    $response->assertSee('Alle ritten in de agenda');
});

test('located state shows the personal heading and the compact picker', function () {
    $cookie = [config('location.cookie') => json_encode([
        'zip' => '9000', 'lat' => 51.05, 'lng' => 3.72, 'name' => 'Gent',
    ])];

    $response = $this->withCookies($cookie)->get(route('home'));

    $response->assertOk();
    $response->assertSee('De volgende rit bij jou');
    $response->assertSee('location-picker--compact', false);
});

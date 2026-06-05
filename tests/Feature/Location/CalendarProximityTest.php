<?php

use App\Enums\ActivityType;
use App\Livewire\RideCalendar;
use App\Models\Activity;
use App\Models\PostalCode;
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
    Livewire::withCookie('kcm_location', json_encode([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]));

    Livewire::test(RideCalendar::class)
        ->assertSee('In de buurt')
        ->assertSee('Verderaf')
        ->assertSee('Jette')
        ->assertSee('Gent');
});

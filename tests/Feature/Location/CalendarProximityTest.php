<?php

use App\Enums\ActivityType;
use App\Livewire\RideCalendar;
use App\Models\Activity;
use App\Models\PostalCode;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    PostalCode::insert([
        ['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174, 'created_at' => now(), 'updated_at' => now()],
    ]);

    $this->author = User::factory()->create();

    // ~0 km from Jette (same zip)
    $this->near = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Jette',
        'postal_code' => '1090',
        'begin_date' => now()->addDays(3),
        'author_id' => $this->author->id,
    ]);

    // ~54 km from Jette
    $this->far = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Gent',
        'postal_code' => '9000',
        'begin_date' => now()->addDays(5),
        'author_id' => $this->author->id,
    ]);
});

it('shows all rides unfiltered when no location is set', function () {
    Livewire::test(RideCalendar::class)
        ->assertSee('Jette')
        ->assertSee('Gent')
        ->assertDontSee('In de buurt')
        ->assertDontSee('Verderaf');
});

it('shows only nearby rides when location set and radius is dichtbij', function () {
    Livewire::withCookie('kcm_location', json_encode([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]));

    Livewire::test(RideCalendar::class, ['radius' => 'dichtbij'])
        ->assertSee('Jette')
        ->assertDontSee('Gent')
        ->assertDontSee('In de buurt')
        ->assertDontSee('Verderaf');
});

it('shows rides within 30km when radius is regio', function () {
    // Brussel is ~4 km from Jette — within 30km regio but check Gent (~54km) is excluded
    PostalCode::insert([
        ['zip' => '1000', 'name' => 'Brussel', 'latitude' => 50.8503, 'longitude' => 4.3517, 'created_at' => now(), 'updated_at' => now()],
    ]);
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Brussel',
        'postal_code' => '1000',
        'begin_date' => now()->addDays(4),
        'author_id' => $this->author->id,
    ]);

    Livewire::withCookie('kcm_location', json_encode([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]));

    Livewire::test(RideCalendar::class, ['radius' => 'regio'])
        ->assertSee('Jette')
        ->assertSee('Brussel')
        ->assertDontSee('Gent');
});

it('labels the radius tabs with abstract bands, not raw distances', function () {
    Livewire::withCookie('kcm_location', json_encode([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]));

    Livewire::test(RideCalendar::class, ['radius' => 'dichtbij'])
        ->assertSee('Dichtbij')
        ->assertSee('In de regio')
        ->assertSee('Heel België')
        ->assertDontSee('5 km')
        ->assertDontSee('30 km');
});

it('shows all rides when radius is belgie', function () {
    Livewire::withCookie('kcm_location', json_encode([
        'zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette',
    ]));

    Livewire::test(RideCalendar::class, ['radius' => 'belgie'])
        ->assertSee('Jette')
        ->assertSee('Gent');
});

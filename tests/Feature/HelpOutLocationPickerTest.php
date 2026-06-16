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
        ->assertSee('location-picker', escape: false)      // the picker is present
        ->assertDontSee('ho-find__nearest', escape: false) // no chapter links yet
        ->assertDontSee('Het dichtst bij');                // and no nearest-chapters heading
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
        // Gent (5th) and Brugge (6th) fall outside the top 4 and must be excluded.
        ->assertDontSee('Kidical Mass Gent')
        ->assertDontSee('Kidical Mass Brugge');
});

it('links each nearest chapter to its volunteer sign-up form', function () {
    $response = withCookie('kcm_location', json_encode(['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette']))
        ->get('/nl/help-out');

    $href = route('groups.show', ['group' => $this->jette, 'intent' => 'volunteer']).'#aanmelden';
    $response->assertSee($href, escape: false);
});

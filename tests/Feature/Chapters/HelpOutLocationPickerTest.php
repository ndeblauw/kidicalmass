<?php

use App\Models\Group;
use App\Models\PostalCode;

use function Pest\Laravel\withCookie;

beforeEach(function () {
    PostalCode::insert([
        ['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '1030', 'name' => 'Schaarbeek', 'latitude' => 50.8676, 'longitude' => 4.3737, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '1050', 'name' => 'Elsene', 'latitude' => 50.8333, 'longitude' => 4.3667, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '1180', 'name' => 'Ukkel', 'latitude' => 50.8000, 'longitude' => 4.3333, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '3000', 'name' => 'Leuven', 'latitude' => 50.8798, 'longitude' => 4.7005, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '6700', 'name' => 'Arlon', 'latitude' => 49.6833, 'longitude' => 5.8167, 'created_at' => now(), 'updated_at' => now()],
    ]);

    $this->jette = Group::factory()->create(['name_nl' => 'Kidical Mass Jette', 'name_fr' => 'Kidical Mass Jette FR', 'zip' => '1090']);
    Group::factory()->create(['name_nl' => 'Kidical Mass Schaarbeek', 'name_fr' => 'Kidical Mass Schaerbeek', 'zip' => '1030']);
    Group::factory()->create(['name_nl' => 'Kidical Mass Elsene', 'name_fr' => 'Kidical Mass Ixelles', 'zip' => '1050']);
    Group::factory()->create(['name_nl' => 'Kidical Mass Ukkel', 'name_fr' => null, 'zip' => '1180']); // no French name entered yet
    Group::factory()->create(['name_nl' => 'Kidical Mass Leuven', 'name_fr' => 'Kidical Mass Louvain', 'zip' => '3000']); // ~26 km from Jette, 5th nearest
    Group::factory()->create(['name_nl' => 'Kidical Mass Gent', 'name_fr' => 'Kidical Mass Gand', 'zip' => '9000']); // ~50 km, outside the regio radius
});

function locatedAt(string $zip, float $lat, float $lng, string $name): array
{
    return ['kcm_location', json_encode(['zip' => $zip, 'lat' => $lat, 'lng' => $lng, 'name' => $name])];
}

it('shows the four nearest chapters, in distance order, when a location is set', function () {
    withCookie(...locatedAt('1090', 50.8782, 4.3265, 'Jette'))
        ->get('/nl/help-out')
        ->assertOk()
        ->assertSeeInOrder([
            'Kidical Mass Jette',
            'Kidical Mass Schaarbeek',
            'Kidical Mass Elsene',
            'Kidical Mass Ukkel',
        ])
        ->assertDontSee('Kidical Mass Leuven')
        ->assertDontSee('Kidical Mass Gent');
});

it('shows the chapter names in the page language on /fr, falling back to the Dutch name', function () {
    withCookie(...locatedAt('1090', 50.8782, 4.3265, 'Jette'))
        ->get('/fr/donner-un-coup-de-main')
        ->assertOk()
        ->assertSeeInOrder(['Kidical Mass Jette FR', 'Kidical Mass Schaerbeek', 'Kidical Mass Ixelles', 'Kidical Mass Ukkel']);
});

it('links each nearest chapter to its volunteer sign-up form', function () {
    $href = route('groups.show', ['group' => $this->jette, 'intent' => 'volunteer']).'#aanmelden';

    withCookie(...locatedAt('1090', 50.8782, 4.3265, 'Jette'))
        ->get('/nl/help-out')
        ->assertSee($href, escape: false);
});

it('leaves out chapters beyond the regio radius', function () {
    // From Gent every Brussels chapter is 40+ km away: only Gent itself is in range.
    withCookie(...locatedAt('9000', 51.0543, 3.7174, 'Gent'))
        ->get('/nl/help-out')
        ->assertSee('Kidical Mass Gent')
        ->assertDontSee('Kidical Mass Jette');
});

it('points to starting a group when no chapter is within the regio radius', function () {
    withCookie(...locatedAt('6700', 49.6833, 5.8167, 'Arlon'))
        ->get('/nl/help-out')
        ->assertOk()
        ->assertDontSee(__('volunteer.find.nearest_title', ['name' => 'Arlon']))
        ->assertSee(__('volunteer.find.none_nearby', ['radius' => 30, 'name' => 'Arlon']))
        ->assertSee(localized_route('groups.start'), escape: false);
});

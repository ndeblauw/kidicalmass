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

it('hands the resolved location to the finder map when a cookie is set', function () {
    withCookie('kcm_location', json_encode(['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette']))
        ->get('/nl/chapters')
        ->assertOk()
        ->assertSee('data-location', false)   // location handed to the client map island
        ->assertSee('Jette')                  // surfaced (picker + island JSON)
        ->assertSee('Kidical Mass Jette');    // the group is listed
});

it('pins the logged-in users group above the rest of the list', function () {
    $user = User::factory()->create();
    // "Jette" sorts after "Gent" alphabetically — pinning must override that order.
    $user->groups()->attach($this->jette->id);

    actingAs($user)
        ->get('/nl/chapters')
        ->assertOk()
        ->assertSee('jouw groep')                                        // pinned card tag
        ->assertSeeInOrder(['Kidical Mass Jette', 'Kidical Mass Gent']); // mine pinned first
});

it('drops the coming-soon map note', function () {
    get('/nl/chapters')->assertDontSee('kaart');
});

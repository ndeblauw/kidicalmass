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

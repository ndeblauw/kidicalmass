<?php

use App\Models\PostalCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('returns coordinates for a known zip and null for an unknown one', function () {
    PostalCode::create(['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265]);

    expect(PostalCode::coordinatesFor('1090'))->toBe(['lat' => 50.8782, 'lng' => 4.3265]);
    expect(PostalCode::coordinatesFor('0000'))->toBeNull();
});

it('finds the nearest postcode to a coordinate pair', function () {
    PostalCode::create(['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265]);
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174]);

    $nearest = PostalCode::nearestTo(50.88, 4.33);

    expect($nearest->zip)->toBe('1090');
});

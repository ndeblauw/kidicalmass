<?php

use App\Models\PostalCode;
use Database\Seeders\PostalCodeSeeder;

it('seeds postcodes from the CSV', function () {
    (new PostalCodeSeeder)->run();

    expect(PostalCode::count())->toBeGreaterThan(1000);
    expect(PostalCode::coordinatesFor('1090'))->not->toBeNull();
});

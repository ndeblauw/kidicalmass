<?php

use App\Models\PostalCode;
use Database\Seeders\PostalCodeSeeder;

it('seeds postcodes from the CSV', function () {
    (new PostalCodeSeeder)->run();

    expect(PostalCode::count())->toBeGreaterThan(1000);
    expect(PostalCode::coordinatesFor('1090'))->not->toBeNull();
});

it('seeds every locality and both municipality names, so big places are findable in either language', function () {
    (new PostalCodeSeeder)->run();

    expect(PostalCode::search('Brussel')->first()->zip)->toBe('1000')
        ->and(PostalCode::search('Schaarbeek')->pluck('zip'))->toContain('1030')
        ->and(PostalCode::search('Elsene')->pluck('zip'))->toContain('1050')
        ->and(PostalCode::search('Laken')->pluck('zip'))->toContain('1020')
        ->and(PostalCode::search('Luik')->pluck('zip'))->toContain('4000')
        ->and(PostalCode::search('Liège')->pluck('zip'))->toContain('4000');

    $liege = PostalCode::where('zip', '4000')->sole();

    app()->setLocale('nl');
    expect($liege->localizedName())->toBe('Luik');

    app()->setLocale('fr');
    expect($liege->localizedName())->toBe('Liège');
});

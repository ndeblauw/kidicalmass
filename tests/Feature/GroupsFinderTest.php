<?php

use App\Models\Group;
use App\Models\PostalCode;

test('index passes map markers with resolved coordinates and region counts', function () {
    $belgium = Group::factory()->create(['name' => 'Belgium', 'invisible' => true]);
    $flanders = Group::factory()->withParent($belgium)->create(['name' => 'Flanders', 'invisible' => true]);
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.05, 'longitude' => 3.7167]);
    $gent = Group::factory()->withParent($flanders)->create([
        'name' => 'Gent', 'shortname' => 'gent', 'zip' => '9000', 'invisible' => false,
    ]);

    $response = $this->get(route('groups.index'));

    $response->assertOk();

    $markers = $response->viewData('markers');
    $marker = collect($markers)->firstWhere('slug', 'gent');

    expect($marker)->not->toBeNull();
    expect($marker['name'])->toBe('Gent');
    expect($marker['region'])->toBe('Flanders');
    expect($marker['regionLabel'])->toBe('Vlaanderen');
    expect($marker['lat'])->toBe(51.05);
    expect($marker['lng'])->toBe(3.7167);
    expect($marker['url'])->toContain('/chapters/'.$gent->id); // groups.show binds by id

    expect($response->viewData('regionCounts')['Flanders'])->toBe(1);
});

test('index renders the finder: region buttons, picker, link list and markers island', function () {
    $belgium = Group::factory()->create(['name' => 'Belgium', 'invisible' => true]);
    $flanders = Group::factory()->withParent($belgium)->create(['name' => 'Flanders', 'invisible' => true]);
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.05, 'longitude' => 3.7167]);
    Group::factory()->withParent($flanders)->create([
        'name' => 'Gent', 'shortname' => 'gent', 'zip' => '9000', 'invisible' => false,
    ]);

    $response = $this->get(route('groups.index'));

    $response->assertOk();
    $response->assertSee('grp-region-btn', false);            // region selector
    $response->assertSee('data-region="Flanders"', false);    // a region button
    $response->assertSee('Vlaanderen', false);                // NL label
    $response->assertSee('location-picker', false);           // shared picker still present
    $response->assertSee('grp-map', false);                   // map shell
    $response->assertSee('data-markers', false);              // markers island
    $response->assertSee(route('groups.show', Group::where('shortname', 'gent')->first()), false);
    $response->assertSee('grp-card__name', false);
    $response->assertDontSee('grp-directory', false);         // old layout gone
});

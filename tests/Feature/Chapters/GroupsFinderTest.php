<?php

use App\Models\Group;
use App\Models\PostalCode;

beforeEach(function (): void {
    $belgium = Group::factory()->create(['name_nl' => 'Belgium', 'name_fr' => 'Belgique', 'invisible' => true]);
    $this->flanders = Group::factory()->withParent($belgium)->create([
        'shortname' => 'flanders', 'name_nl' => 'Flanders', 'name_fr' => 'Flandre', 'invisible' => true,
    ]);
    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.05, 'longitude' => 3.7167]);
    $this->gent = Group::factory()->withParent($this->flanders)->create([
        'name_nl' => 'Gent', 'name_fr' => 'Gand', 'shortname' => 'gent', 'zip' => '9000', 'invisible' => false,
    ]);
});

test('index passes map markers with resolved coordinates and region counts', function () {
    $response = $this->get(route('groups.index'));

    $response->assertOk();

    $marker = collect($response->viewData('markers'))->firstWhere('slug', 'gent');

    expect($marker)->not->toBeNull();
    expect($marker['name'])->toBe('Gent');
    expect($marker['region'])->toBe('Flanders');
    expect($marker['regionLabel'])->toBe('Vlaanderen');
    expect($marker['colorToken'])->toBe('--color-kidical-green');
    expect($marker['lat'])->toBe(51.05);
    expect($marker['lng'])->toBe(3.7167);
    expect($marker['url'])->toBe(localized_route('groups.show', ['group' => $this->gent]));

    expect($response->viewData('regionCounts')['Flanders'])->toBe(1);
});

test('on /fr the region key stays locale-independent so filters and map colours still match', function () {
    $response = $this->get('/fr/groupes-locaux');

    $response->assertOk()
        ->assertSee('data-region="Flanders"', false)
        ->assertSee('Flandre');

    $marker = collect($response->viewData('markers'))->firstWhere('slug', 'gent');

    expect($marker['name'])->toBe('Gand');
    expect($marker['region'])->toBe('Flanders');
    expect($marker['url'])->toContain('/fr/groupes-locaux/');
    expect($response->viewData('regionCounts')['Flanders'])->toBe(1);
});

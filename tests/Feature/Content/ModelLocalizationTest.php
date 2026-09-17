<?php

use App\Models\Activity;
use App\Models\Group;
use App\Support\FrenchNames;

it('serves an activity title and location in the active locale', function () {
    $activity = Activity::factory()->create([
        'title_nl' => 'Zomertocht',
        'title_fr' => 'Balade d\'été',
        'location_nl' => 'Jubelpark, Brussel',
        'location_fr' => 'Parc du Cinquantenaire, Bruxelles',
    ]);

    app()->setLocale('nl');
    expect($activity->title)->toBe('Zomertocht')
        ->and($activity->location)->toBe('Jubelpark, Brussel');

    app()->setLocale('fr');
    expect($activity->title)->toBe('Balade d\'été')
        ->and($activity->location)->toBe('Parc du Cinquantenaire, Bruxelles');
});

it('falls back to French when the active locale has no content', function () {
    $activity = Activity::factory()->create([
        'title_nl' => '',
        'title_fr' => 'Balade de printemps',
        'content_nl' => '',
        'content_fr' => 'Le contenu français.',
    ]);

    app()->setLocale('nl');
    expect($activity->title)->toBe('Balade de printemps')
        ->and($activity->content)->toBe('Le contenu français.');
});

it('stays empty on French when a record has no French content', function () {
    $activity = Activity::factory()->create([
        'title_nl' => 'Alleen Nederlands',
        'title_fr' => '',
        'content_nl' => 'Inhoud.',
        'content_fr' => '',
    ]);

    app()->setLocale('fr');
    expect($activity->title)->toBe('')
        ->and($activity->content)->toBe('');
});

it('keeps a record in one language instead of mixing fields', function () {
    $activity = Activity::factory()->create([
        'title_nl' => 'Alleen titel NL',
        'title_fr' => '',
        'content_nl' => '',
        'content_fr' => 'Inhoud alleen in het Frans.',
    ]);

    app()->setLocale('nl');
    expect($activity->title)->toBe('Alleen titel NL')
        ->and($activity->content)->toBe('');
});

it('serves a group name in the active locale', function () {
    $group = Group::factory()->create(['name_nl' => 'Bergen', 'name_fr' => 'Mons']);

    app()->setLocale('nl');
    expect($group->name)->toBe('Bergen');

    app()->setLocale('fr');
    expect($group->name)->toBe('Mons');
});

it('falls back to the Dutch name for the public label when French is missing', function () {
    $group = Group::factory()->create(['name_nl' => 'Namen', 'name_fr' => null]);

    app()->setLocale('fr');
    expect($group->publicLabel())->toBe('Namen');

    app()->setLocale('nl');
    expect($group->publicLabel())->toBe('Namen');
});

it('elides de to d apostrophe before a vowel and slugifies French names', function () {
    expect(FrenchNames::preposition('Etterbeek'))->toBe("d'Etterbeek")
        ->and(FrenchNames::preposition('Uccle'))->toBe("d'Uccle")
        ->and(FrenchNames::preposition('Mons'))->toBe('de Mons');

    expect(FrenchNames::slug('Sint-Gillis'))->toBe('sint-gillis')
        ->and(FrenchNames::slug('Saint-Gilles'))->toBe('saint-gilles');
});

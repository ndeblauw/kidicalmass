<?php

use App\Models\Group;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function (): void {
    $this->withoutVite();
});

function localizedChapter(): Group
{
    return Group::factory()->create([
        'shortname' => 'mons',
        'name_nl' => 'Kidical Mass Mons',
        'name_fr' => 'Kidical Mass Mons',
        'invisible' => false,
    ]);
}

function localizedMember(Group $group): User
{
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    return $member;
}

it('serves every hub page in French', function (string $route, string $expected) {
    $group = localizedChapter();
    $member = localizedMember($group);

    actingAs($member)
        ->get(route($route, ['locale' => 'fr', 'group' => $group]))
        ->assertOk()
        ->assertSee($expected);
})->with([
    ['fr.groups.roze-hesjes', 'Aperçu'],
    ['fr.groups.roze-hesjes.aan-de-slag', 'Avant votre première parade'],
    ['fr.groups.roze-hesjes.agenda', 'Déjà fixé'],
    ['fr.groups.roze-hesjes.fotos', 'Photos de'],
    ['fr.groups.roze-hesjes.groep', 'Les gilets roses de'],
    ['fr.groups.roze-hesjes.materiaal', 'Votre matériel'],
]);

it('serves the ride preview in French', function () {
    $group = localizedChapter();
    $member = localizedMember($group);

    actingAs($member)
        ->get(route('fr.groups.ride-preview', ['locale' => 'fr', 'group' => $group]))
        ->assertOk()
        ->assertSee('Pas encore fixé');
});

it('keeps the Dutch hub in Dutch', function () {
    $group = localizedChapter();
    $member = localizedMember($group);

    actingAs($member)
        ->get(route('groups.roze-hesjes', ['locale' => 'nl', 'group' => $group]))
        ->assertOk()
        ->assertSee('Overzicht')
        ->assertDontSee('Aperçu');
});

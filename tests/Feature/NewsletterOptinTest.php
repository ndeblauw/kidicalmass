<?php

use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Blade;

use function Pest\Laravel\get;

test('guest sees the opt-in form with benefits and submit button', function () {
    $html = Blade::render('<x-newsletter-optin />');

    expect($html)
        ->toContain('Blijf op de hoogte')
        ->toContain('De nieuwste ritten, elke maand als eerste')
        ->toContain('jouw lokale groep')
        ->toContain('Eén rustige mail, makkelijk uit te schrijven')
        ->toContain('Je e-mailadres')
        ->toContain('type="email"')
        ->toContain('Ja, hou me op de hoogte');
});

test('group prop localises the lokale-groep benefit with the gemeente name', function () {
    $group = Group::create([
        'shortname' => 'sb',
        'name' => 'Kidical Mass Schaarbeek',
        'invisible' => false,
        'started_at' => now(),
    ]);

    $html = Blade::render('<x-newsletter-optin :group="$group" />', ['group' => $group]);

    expect($html)
        ->toContain('Het laatste nieuws uit Schaarbeek')
        ->not->toContain('jouw lokale groep');
});

test('authenticated visitor sees a manage-preferences panel and no email form', function () {
    $this->actingAs(User::factory()->create());

    $html = Blade::render('<x-newsletter-optin />');

    expect($html)
        ->toContain('Beheer voorkeuren')
        ->toContain(route('settings'))
        ->not->toContain('type="email"')
        ->not->toContain('Ja, hou me op de hoogte');
});

test('the calendar page shows the opt-in in the sidebar and not the old card', function () {
    get(route('activities.index'))
        ->assertOk()
        ->assertSee('Blijf op de hoogte')
        ->assertSee('Ja, hou me op de hoogte')
        ->assertDontSee('Mis geen rit')
        ->assertDontSee('Schrijf je in');
});

test('chapter page always shows the localised opt-in, with and without a ride', function () {
    $author = User::factory()->create();
    $group = Group::create(['shortname' => 'sb', 'name' => 'Kidical Mass Schaarbeek', 'zip' => '1030', 'invisible' => false, 'started_at' => now()]);

    // Without a ride
    get(route('groups.show', $group))
        ->assertOk()
        ->assertSee('Nog geen fietstocht gepland')
        ->assertSee('Blijf op de hoogte')
        ->assertSee('Het laatste nieuws uit Schaarbeek');

    // With a ride
    Activity::create([
        'title_nl' => 'Kidical Mass Schaarbeek', 'title_fr' => 'x',
        'content_nl' => 'x', 'content_fr' => 'x',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addWeek(), 'duration_minutes' => 60,
        'location' => 'Place Colignon', 'author_id' => $author->id,
    ])->groups()->attach($group);

    get(route('groups.show', $group))
        ->assertOk()
        ->assertDontSee('Nog geen fietstocht gepland')
        ->assertSee('Blijf op de hoogte')
        ->assertSee('Het laatste nieuws uit Schaarbeek');
});

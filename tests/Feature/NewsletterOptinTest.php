<?php

use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Blade;

use function Pest\Laravel\get;

test('guest sees the teaser with a link to the signup page', function () {
    $html = Blade::render('<x-newsletter-optin />');

    expect($html)
        ->toContain('Mis geen rit')
        ->toContain('Eén mail per maand met de ritten bij jou in de buurt.')
        ->toContain('Schrijf je in')
        ->toContain('nieuwsbrief')
        ->not->toContain('href="#"');
});

test('group prop localises the teaser with the gemeente name', function () {
    $group = Group::create([
        'shortname' => 'sb',
        'name' => 'Kidical Mass Schaarbeek',
        'invisible' => false,
        'started_at' => now(),
    ]);

    $html = Blade::render('<x-newsletter-optin :group="$group" />', ['group' => $group]);

    expect($html)->toContain('Eén mail per maand met de ritten en het nieuws uit Schaarbeek.');
});

test('authenticated visitor sees a manage-preferences panel and no signup CTA', function () {
    $this->actingAs(User::factory()->create());

    $html = Blade::render('<x-newsletter-optin />');

    expect($html)
        ->toContain('Beheer voorkeuren')
        ->toContain(route('settings'))
        ->not->toContain('Schrijf je in');
});

test('the calendar page shows the opt-in teaser in the sidebar', function () {
    get('/nl/events')
        ->assertOk()
        ->assertSee('Mis geen rit');
});

test('chapter page shows the localised opt-in teaser, with and without a ride', function () {
    $author = User::factory()->create();
    $group = Group::create([
        'shortname' => 'sb',
        'name' => 'Kidical Mass Schaarbeek',
        'zip' => '1030',
        'invisible' => false,
        'started_at' => now(),
    ]);

    get(route('groups.show', ['locale' => 'nl', 'group' => $group]))
        ->assertOk()
        ->assertSee('Nog geen fietstocht gepland')
        ->assertSee('Mis geen rit')
        ->assertSee('uit Schaarbeek', escape: false);

    Activity::create([
        'title_nl' => 'Kidical Mass Schaarbeek', 'title_fr' => 'x',
        'content_nl' => 'x', 'content_fr' => 'x',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addWeek(), 'duration_minutes' => 60,
        'location' => 'Place Colignon', 'author_id' => $author->id,
    ])->groups()->attach($group);

    // v4: with a ride, the opt-in card is replaced by a decoupled subscribe line beneath
    // the next-ride card, linking straight to the newsletter sign-up (never competing
    // with the card). The localised "Mis geen rit" teaser is reserved for the empty state.
    get(route('groups.show', ['locale' => 'nl', 'group' => $group]))
        ->assertOk()
        ->assertDontSee('Nog geen fietstocht gepland')
        ->assertSee('Kan je er niet bij deze keer?')
        ->assertSee(route('newsletter.show', ['locale' => 'nl']), escape: false);
});

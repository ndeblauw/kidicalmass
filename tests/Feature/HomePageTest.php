<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;

use function Pest\Laravel\get;
use function Pest\Laravel\withCookie;

beforeEach(function () {
    PostalCode::insert([
        ['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174, 'created_at' => now(), 'updated_at' => now()],
    ]);
});

it('renders the NL video hero and drops the old English copy', function () {
    get('/nl')
        ->assertOk()
        ->assertSee('Het leukste uur op de fiets, door autovrije straten.')
        ->assertSee('de straat ook van kinderen is')
        ->assertSee('youtube.com/embed/VXiIgU9vI-4', escape: false)
        ->assertDontSee('Kids on bikes')
        ->assertDontSee('—');
});

it('shows the three dispatcher routes pointing at the right pages', function () {
    get('/nl')
        ->assertOk()
        ->assertSee('Nieuw hier?')
        ->assertSee('Help mee')
        ->assertSee('Vind je lokale groep')
        ->assertSee(route('getting-started'), escape: false)
        ->assertSee(route('volunteer'), escape: false)
        ->assertSee(route('groups.index'), escape: false);
});

it('shows the off-season message when there are no upcoming rides', function () {
    get('/nl')
        ->assertOk()
        ->assertSee('Het fietsseizoen loopt van maart tot november.');
});

it('shows the location picker in the next-ride section when no location is set', function () {
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'postal_code' => '1090',
        'begin_date' => now()->addDays(3),
    ]);

    get('/nl')
        ->assertOk()
        ->assertSee('De volgende rit bij jou')
        ->assertSee('Waar wil je fietsen?')
        ->assertDontSee('km van jou');
});

it('shows the nearest upcoming ride using the date-rail lockup when a location is set', function () {
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Jette',
        'location' => 'Josaphatpark',
        'postal_code' => '1090',
        'begin_date' => now()->addDays(3),
    ]);

    withCookie('kcm_location', json_encode(['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette']))
        ->get('/nl')
        ->assertOk()
        ->assertSee('Jette')
        ->assertDontSee('km van jou')
        ->assertSee('Je fietst rond');
});

it('flags a far fallback ride when nothing is in range', function () {
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Gent',
        'postal_code' => '9000',
        'begin_date' => now()->addDays(3),
    ]);

    withCookie('kcm_location', json_encode(['zip' => '1090', 'lat' => 50.8782, 'lng' => 4.3265, 'name' => 'Jette']))
        ->get('/nl')
        ->assertOk()
        ->assertSee('iets verderaf')
        ->assertSee('Gent');
});

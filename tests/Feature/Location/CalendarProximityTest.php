<?php

use App\Enums\ActivityType;
use App\Livewire\RideCalendar;
use App\Models\Activity;
use App\Models\PostalCode;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    PostalCode::insert([
        ['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265, 'created_at' => now(), 'updated_at' => now()],
        ['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.0543, 'longitude' => 3.7174, 'created_at' => now(), 'updated_at' => now()],
    ]);

    $this->author = User::factory()->create();

    // ~0 km from Jette (same zip)
    $this->near = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Jette',
        'postal_code' => '1090',
        'begin_date' => now()->addDays(3),
        'author_id' => $this->author->id,
    ]);

    // ~54 km from Jette
    $this->far = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Gent',
        'postal_code' => '9000',
        'begin_date' => now()->addDays(5),
        'author_id' => $this->author->id,
    ]);
});

function setLocationCookie(string $zip, float $lat, float $lng, string $name): void
{
    Livewire::withCookie('kcm_location', json_encode(['zip' => $zip, 'lat' => $lat, 'lng' => $lng, 'name' => $name]));
}

it('shows all rides unfiltered when no location is set', function () {
    Livewire::test(RideCalendar::class)
        ->assertSee('Jette')
        ->assertSee('Gent')
        ->assertDontSeeHtml('data-proximity-section');
});

it('groups rides nearest first, keeping unresolvable postcodes in the far section', function () {
    PostalCode::insert([
        ['zip' => '1800', 'name' => 'Vilvoorde', 'latitude' => 50.9281, 'longitude' => 4.4250, 'created_at' => now(), 'updated_at' => now()],
    ]);
    // ~9 km from Jette: region band. Dated before Jette, so section order beats date order.
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Vilvoorde',
        'postal_code' => '1800',
        'begin_date' => now()->addDays(1),
        'author_id' => $this->author->id,
    ]);
    // Postcode without a postal_codes row: cannot be ranked, so it counts as far.
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'title_nl' => 'Kidical Mass Nergensdorp',
        'postal_code' => '9999',
        'begin_date' => now()->addDays(2),
        'author_id' => $this->author->id,
    ]);

    setLocationCookie('1090', 50.8782, 4.3265, 'Jette');

    Livewire::test(RideCalendar::class)
        ->assertSeeHtmlInOrder([
            'data-proximity-section="nearby"', 'Jette',
            'data-proximity-section="region"', 'Vilvoorde',
            'data-proximity-section="far"', 'Nergensdorp', 'Gent',
        ])
        ->assertSee(__('calendar.proximity.nearby.title'))
        ->assertSeeHtml('data-distance-km')
        ->assertDontSeeHtml('data-proximity-empty-lead')
        ->assertSee(trans_choice('calendar.found', 4));
});

it('skips an empty band after a filled one', function () {
    setLocationCookie('1090', 50.8782, 4.3265, 'Jette');

    Livewire::test(RideCalendar::class)
        ->assertSeeHtml('data-proximity-section="nearby"')
        ->assertSeeHtml('data-proximity-section="far"')
        ->assertDontSeeHtml('data-proximity-section="region"')
        ->assertDontSeeHtml('data-proximity-empty-lead');
});

it('collapses empty leading bands into one quiet line', function () {
    PostalCode::insert([
        ['zip' => '8000', 'name' => 'Brugge', 'latitude' => 51.2093, 'longitude' => 3.2247, 'created_at' => now(), 'updated_at' => now()],
    ]);
    setLocationCookie('8000', 51.2093, 3.2247, 'Brugge');

    Livewire::test(RideCalendar::class)
        ->assertSeeHtml('data-proximity-empty-lead')
        ->assertSee(__('calendar.proximity.none_within', ['km' => '30', 'place' => 'Brugge']))
        ->assertDontSeeHtml('data-proximity-section="nearby"')
        ->assertDontSeeHtml('data-proximity-section="region"')
        ->assertSeeHtml('data-proximity-section="far"')
        ->assertSee('Jette')
        ->assertSee('Gent')
        ->assertSee(trans_choice('calendar.found', 2));
});

it('takes the band radii and their copy from config', function () {
    config(['location.nearby_radius_km' => 6, 'location.regio_radius_km' => 60]);
    setLocationCookie('1090', 50.8782, 4.3265, 'Jette');

    // Gent (~54 km) is far with the default 30 km region, but region with 60 km.
    Livewire::test(RideCalendar::class)
        ->assertSeeHtmlInOrder(['data-proximity-section="nearby"', 'Jette', 'data-proximity-section="region"', 'Gent'])
        ->assertDontSeeHtml('data-proximity-section="far"')
        ->assertSee(__('calendar.proximity.nearby.sub', ['km' => '6']))
        ->assertSee(__('calendar.proximity.region.sub', ['km' => '60']));
});

it('ignores a legacy radius query param', function () {
    setLocationCookie('1090', 50.8782, 4.3265, 'Jette');

    Livewire::withQueryParams(['radius' => 'dichtbij'])
        ->test(RideCalendar::class)
        ->assertSee('Jette')
        ->assertSee('Gent');
});

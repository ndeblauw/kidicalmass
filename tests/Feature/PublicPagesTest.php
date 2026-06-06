<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;

use function Pest\Laravel\get;

/**
 * Covers the public content pages after the Surface pass: each renders its real
 * data, and the hand-designed activity detail page keeps its branded markup.
 */
beforeEach(function () {
    $this->group = Group::factory()->create(['name' => 'Kidical Mass Testville']);

    $this->activity = Activity::factory()->create([
        'title_nl' => 'Surface Test Ride',
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addWeek(),
    ]);
    $this->activity->groups()->attach($this->group);

    $this->article = Article::factory()->create(['title_nl' => 'Surface Test Article']);
    $this->article->groups()->attach($this->group);
});

it('renders the home page with real data', function () {
    get('/nl')
        ->assertOk()
        ->assertSee('Surface Test Ride')
        ->assertSee('Kidical Mass Testville')
        ->assertSee('Surface Test Article')
        ->assertSee('Find a ride', escape: false);
});

it('renders the activities index with the event listed', function () {
    get('/nl/events')
        ->assertOk()
        ->assertSee('Surface Test Ride');
});

it('renders the Kalender with NL chrome and no English/em-dashes', function () {
    get('/nl/events')
        ->assertOk()
        ->assertSee('Kalender')
        ->assertSee('Spring op de fiets, wij rijden samen.')
        ->assertSee('Waar wil je fietsen?')           // location-first filter is the primary control
        ->assertSee('Bekijk voorbije ritten')          // period demoted to a link
        ->assertSee('Mis geen fietstocht')
        ->assertDontSee('Activities')
        ->assertDontSee('Find a ride near you')
        ->assertDontSee('—');
});

it('leads event cards with the town, dropping the "Kidical Mass" prefix', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Schaarbeek',
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addWeek(),
    ]);
    $ride->groups()->attach($this->group);

    $grande = Activity::factory()->create([
        'title_nl' => 'Grande Kidical Mass 2026',
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addWeek(),
    ]);
    $grande->groups()->attach($this->group);

    get('/nl/events')
        ->assertOk()
        ->assertSee('Schaarbeek')
        ->assertDontSee('Kidical Mass Schaarbeek') // prefix dropped on the card headline
        ->assertSee('Grande Kidical Mass 2026');   // one-off keeps its full name
});

it('shows the whole upcoming run without pagination', function () {
    // 14 upcoming rides — the old paginate(12) would have hidden the last two.
    for ($i = 1; $i <= 14; $i++) {
        Activity::factory()->create([
            'title_nl' => "Kidical Mass Stad{$i}",
            'activity_type' => ActivityType::KIDICALMASS,
            'begin_date' => now()->addDays($i),
        ])->groups()->attach($this->group);
    }

    get('/nl/events')
        ->assertOk()
        ->assertSee('Stad13')
        ->assertSee('Stad14')
        ->assertDontSee('Volgende', escape: false); // no pagination control
});

it('labels imminent days with a relative landmark', function () {
    Activity::factory()->create([
        'title_nl' => 'Kidical Mass Morgenstad',
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDay()->setTime(15, 0),
    ])->groups()->attach($this->group);

    get('/nl/events')
        ->assertOk()
        ->assertSee('Morgen');
});

it('shows only rides on the Kalender, not meetups', function () {
    $meetup = Activity::factory()->create([
        'title_nl' => 'Vrijwilligersvergadering',
        'activity_type' => ActivityType::MEETING,
        'begin_date' => now()->addWeek(),
    ]);
    $meetup->groups()->attach($this->group);

    get('/nl/events')
        ->assertOk()
        ->assertSee('Surface Test Ride')
        ->assertDontSee('Vrijwilligersvergadering');
});

it('splits the Kalender into upcoming (default) and past via the when toggle', function () {
    $pastRide = Activity::factory()->create([
        'title_nl' => 'Voorbije Testrit',
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->subWeek(),
    ]);
    $pastRide->groups()->attach($this->group);

    // Default (upcoming) hides the past ride, shows the future one.
    get('/nl/events')
        ->assertOk()
        ->assertSee('Surface Test Ride')
        ->assertDontSee('Voorbije Testrit');

    // Past toggle flips it.
    get('/nl/events?when=voorbije')
        ->assertOk()
        ->assertSee('Voorbije Testrit')
        ->assertDontSee('Surface Test Ride');
});

it('renders the articles index with the article listed', function () {
    get('/nl/about/news')
        ->assertOk()
        ->assertSee('Surface Test Article');
});

it('renders the groups index with the group listed', function () {
    get('/nl/chapters')
        ->assertOk()
        ->assertSee('Kidical Mass Testville');
});

it('renders Lokale groepen with NL chrome, no count badges and no em-dashes', function () {
    get('/nl/chapters')
        ->assertOk()
        ->assertSee('Lokale groepen')
        ->assertSee('Jouw buurt fietst al, rij mee.')
        ->assertSee('Vind je groep')
        // Movement counter moved out of the hero into the foot of the white panel.
        ->assertSee('activiteiten dit jaar')
        ->assertSee('Staat jouw stad er nog niet bij?')
        ->assertSee(route('volunteer'), escape: false)
        // The card-grid count badges from the drifted build are gone.
        ->assertDontSee('activities')
        ->assertDontSee('articles')
        ->assertDontSee('Part of:')
        ->assertDontSee('Groups')
        ->assertDontSee('—');
});

it('groups chapters by region under NL region headers', function () {
    // Region = the invisible parent group (mirrors the seeded Belgium → region → chapter tree).
    $brussels = Group::factory()->create(['name' => 'Brussels Capital Region', 'invisible' => true]);
    $schaarbeek = Group::factory()->withParent($brussels)->create(['name' => 'Schaarbeek']);

    get('/nl/chapters')
        ->assertOk()
        ->assertSee('Brussel')
        ->assertSee('Schaarbeek')
        ->assertSeeInOrder(['Brussel', 'Schaarbeek']);
});

it('renders the article detail with its content', function () {
    get(route('articles.show', $this->article))
        ->assertOk()
        ->assertSee('Surface Test Article');
});

it('renders the group detail with its upcoming ride', function () {
    get(route('groups.show', $this->group))
        ->assertOk()
        ->assertSee('Kidical Mass Testville')
        ->assertSee('Surface Test Ride');
});

it('keeps the activity detail page branded', function () {
    get(route('activities.show', $this->activity))
        ->assertOk()
        ->assertSee('Surface Test Ride')
        ->assertSee('activity-hero', escape: false);
});

it('shows the support callout at the end of an event detail page', function () {
    get(route('activities.show', $this->activity))
        ->assertOk()
        ->assertSee('Fijn meegereden')
        ->assertSee(route('membership'), escape: false);
});

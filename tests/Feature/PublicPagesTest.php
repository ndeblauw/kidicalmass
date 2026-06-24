<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use App\Models\PostalCode;
use App\Models\User;

use function Pest\Laravel\get;
use function Pest\Laravel\withCookie;

/**
 * Covers the public content pages after the Surface pass: each renders its real
 * data, and the hand-designed activity detail page keeps its branded markup.
 */
beforeEach(function () {
    $this->group = Group::factory()->create(['name' => 'Kidical Mass Testville']);

    $this->author = User::factory()->create();

    $this->activity = Activity::factory()->create([
        'title_nl' => 'Surface Test Ride',
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addWeek(),
        'author_id' => $this->author->id,
    ]);
    $this->activity->groups()->attach($this->group);

    $this->article = Article::factory()->create(['title_nl' => 'Surface Test Article', 'author_id' => $this->author->id]);
    $this->article->groups()->attach($this->group);
});

it('renders the home page with the next ride when a location is set', function () {
    PostalCode::insert([
        ['zip' => '1030', 'name' => 'Schaarbeek', 'latitude' => 50.8669, 'longitude' => 4.3733, 'created_at' => now(), 'updated_at' => now()],
    ]);
    $this->activity->update(['postal_code' => '1030']);

    withCookie('kcm_location', json_encode(['zip' => '1030', 'lat' => 50.8669, 'lng' => 4.3733, 'name' => 'Schaarbeek']))
        ->get('/nl')
        ->assertOk()
        ->assertSee('De volgende ritten bij jou')
        ->assertSee('Surface Test Ride')
        ->assertSee('Je fietst rond')
        ->assertDontSee('Find a ride', escape: false);
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
        ->assertSee('Mis geen rit')
        ->assertDontSee('Activities')
        ->assertDontSee('Find a ride near you')
        ->assertDontSee('—');
});

it('leads event cards with the town, dropping the "Kidical Mass" prefix', function () {
    $ride = Activity::factory()->create([
        'title_nl' => 'Kidical Mass Schaarbeek',
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addWeek(),
        'author_id' => $this->author->id,
    ]);
    $ride->groups()->attach($this->group);

    $grande = Activity::factory()->create([
        'title_nl' => 'Grande Kidical Mass 2026',
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addWeek(),
        'author_id' => $this->author->id,
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
    $author = User::factory()->create();

    Activity::factory()
        ->count(14)
        ->sequence(fn ($seq) => [
            'title_nl' => 'Kidical Mass Stad'.($seq->index + 1),
            'begin_date' => now()->addDays($seq->index + 1),
        ])
        ->create(['activity_type' => ActivityType::KIDICALMASS, 'author_id' => $author->id])
        ->each(fn (Activity $a) => $a->groups()->attach($this->group));

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
        'author_id' => $this->author->id,
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
        'author_id' => $this->author->id,
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
        'author_id' => $this->author->id,
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
        // The list+map finder: default region selector + the closing recruit CTA.
        ->assertSee('Heel België')
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
    // The v4 chapter page leads with the <x-next-ride> feature card, which surfaces the
    // ride as "Volgende fietsparade" + its date (not the title — that's only on the ride page).
    get(route('groups.show', $this->group))
        ->assertOk()
        ->assertSee('Kidical Mass Testville')
        ->assertSee('Volgende fietsparade')
        ->assertSee(route('activities.show', $this->activity), escape: false);
});

it('keeps the activity detail page branded', function () {
    get(route('activities.show', $this->activity))
        ->assertOk()
        ->assertSee('Surface Test Ride')
        ->assertSee('activity-head', escape: false);
});

it('shows the support callout at the end of a past event detail page', function () {
    $ride = Activity::factory()->past()->create();

    get(route('activities.show', $ride))
        ->assertOk()
        ->assertSee('Steun de volgende rit')
        ->assertSee(route('membership'), escape: false);
});

it('links the chapter gallery to the full ride recap', function () {
    $group = Group::factory()->create();
    $ride = Activity::factory()->past()->withGallery(3)->create();
    $ride->groups()->attach($group);

    $this->get(route('groups.show', ['locale' => 'nl', 'group' => $group]))
        ->assertSee(route('activities.show', ['locale' => 'nl', 'activity' => $ride]), escape: false)
        ->assertSee('Bekijk de hele rit');
});

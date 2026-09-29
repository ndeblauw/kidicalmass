<?php

// Home beat 4 "Eén beweging": the movement figures, the static map of local
// groups and the way on to Over ons.

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\PostalCode;
use App\Models\User;
use App\Models\YearStat;
use App\Support\Map\LocalGroupsMap;
use App\Support\MovementStats;

use function Pest\Laravel\get;

it('shows the movement figures, the map and a link to Over ons', function () {
    // The figures themselves are covered in MovementStatsTest; this checks the wiring.
    $this->withoutVite();

    Group::factory()->create(['invisible' => false]);
    YearStat::factory()->create(['year' => 2024, 'participants' => 5500]);
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => '2024-05-01 14:00',
        'is_published' => true,
        'author_id' => User::factory(),
    ]);

    get('/nl')
        ->assertOk()
        ->assertSee('data-home-movement', false)
        ->assertSee('data-belgium-map', false)
        ->assertSeeInOrder(['data-stat="groups"', 'data-stat="rides"', 'data-stat="participants"'], false)
        ->assertSee('href="'.localized_route('about').'"', false)
        ->assertSeeText(__('home.routes.movement.cta'));
});

it('shows admin edits to groups, rides and year figures on the next render', function () {
    $this->withoutVite();

    PostalCode::create(['zip' => '9000', 'name' => 'Gent', 'latitude' => 51.05, 'longitude' => 3.7167]);
    Group::factory()->create(['invisible' => false, 'zip' => '9000']);
    $yearStat = YearStat::factory()->create(['year' => 2024, 'participants' => 5500]);
    $ride = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => '2024-05-01 14:00',
        'is_published' => true,
        'author_id' => User::factory(),
    ]);

    $stats = fn (): array => collect(app(MovementStats::class)->items())->pluck('value', 'key')->all();
    $mapLabel = fn (): string => app(LocalGroupsMap::class)->data()['label'];

    get('/nl')->assertOk()->assertSee('data-stat="rides"', false);
    $labelBefore = $mapLabel();
    expect($stats())->toBe(['groups' => '1', 'rides' => '1', 'participants' => '5.500']);

    Group::factory()->create(['invisible' => false, 'zip' => '9000']);
    $yearStat->update(['participants' => 6000]);
    $ride->update(['is_published' => false]);

    get('/nl')->assertOk()->assertDontSee('data-stat="rides"', false);
    expect($stats())->toBe(['groups' => '2', 'participants' => '6.000'])
        ->and($mapLabel())->not->toBe($labelBefore);
});

<?php

// Home beat 4 "Eén beweging": the movement figures, the static map of local
// groups and the way on to Over ons.

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use App\Models\YearStat;

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

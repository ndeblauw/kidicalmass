<?php

use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Carbon\CarbonInterface;
use Database\Seeders\ChapterRideGallerySeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('media');
});

/**
 * A past Kidical Mass ride attached to $group.
 */
function pastRide(Group $group, CarbonInterface $when): Activity
{
    $ride = Activity::create([
        'title_nl' => 'Rit',
        'title_fr' => 'Rit',
        'content_nl' => 'x',
        'content_fr' => 'x',
        'activity_type' => 'kidicalmass',
        'begin_date' => $when,
        'duration_minutes' => 60,
        'location' => 'Place Colignon',
        'author_id' => User::factory()->create()->id,
    ]);
    $ride->groups()->attach($group);

    return $ride;
}

it('attaches the requested number of photos to the chapter\'s latest ride', function () {
    $group = Group::factory()->create();
    $ride = pastRide($group, now()->subWeek());

    $seeded = (new ChapterRideGallerySeeder)->seedLatestRide($group, 3);

    expect($seeded)->toBeTrue();
    expect($ride->refresh()->getMedia('gallery'))->toHaveCount(3);
});

it('targets the most recent past ride, not an older one', function () {
    $group = Group::factory()->create();
    $older = pastRide($group, now()->subMonths(2));
    $latest = pastRide($group, now()->subWeek());

    (new ChapterRideGallerySeeder)->seedLatestRide($group, 2);

    expect($latest->refresh()->getMedia('gallery'))->toHaveCount(2);
    expect($older->refresh()->getMedia('gallery'))->toHaveCount(0);
});

it('is idempotent — reseeding replaces rather than piles up', function () {
    $group = Group::factory()->create();
    $ride = pastRide($group, now()->subWeek());

    $seeder = new ChapterRideGallerySeeder;
    $seeder->seedLatestRide($group, 3);
    $seeder->seedLatestRide($group, 3);

    expect($ride->refresh()->getMedia('gallery'))->toHaveCount(3);
});

it('skips a chapter with no past ride', function () {
    $group = Group::factory()->create();

    $seeded = (new ChapterRideGallerySeeder)->seedLatestRide($group, 3);

    expect($seeded)->toBeFalse();
    expect($group->fresh()->getMedia('gallery'))->toHaveCount(0);
});

it('seeds the mapped count onto each listed chapter when run', function () {
    $counts = ['schaarbeek' => 8, 'elsene' => 5, 'namen' => 3];

    $rides = collect($counts)->mapWithKeys(function (int $count, string $shortname) {
        $group = Group::factory()->create(['shortname' => $shortname]);

        return [$shortname => pastRide($group, now()->subWeek())];
    });

    (new ChapterRideGallerySeeder)->run();

    foreach ($counts as $shortname => $count) {
        expect($rides[$shortname]->refresh()->getMedia('gallery'))->toHaveCount($count);
    }
});

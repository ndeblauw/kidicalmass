<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use App\Support\RideDate;
use Illuminate\Http\UploadedFile;

use function Pest\Laravel\actingAs;

function delightHubUrl(Group $group): string
{
    return route('groups.roze-hesjes', ['locale' => 'nl', 'group' => $group]);
}

function establishedMember(Group $group, string $name = 'Lien Govaerts'): User
{
    $member = User::factory()->create(['name' => $name]);
    $group->users()->attach($member, ['role' => 'pinkvest']);
    // Pivot created outside the welcome window so the member card is not "new".
    $group->users()->updateExistingPivot($member->id, [
        'created_at' => now()->subMonths(3),
        'updated_at' => now()->subMonths(3),
    ]);

    return $member;
}

test('a new hesje is greeted with the welcome moment on first visit', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create(['name' => 'Lien Govaerts']);
    $group->users()->attach($member, ['role' => 'pinkvest']);

    actingAs($member)->get(delightHubUrl($group))
        ->assertSee('data-moment="welcome"', escape: false)
        ->assertSee('Welkom bij de hesjes, Lien.')
        ->assertSee('Fijn dat je meerijdt.');
});

test('an established hesje gets the default greeting when nothing is happening', function () {
    $group = Group::factory()->create(['name' => 'Kidical Mass Schaarbeek']);
    $member = establishedMember($group);

    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertSee('data-moment="default"', escape: false)
        ->assertSee('Dag Lien.')
        ->assertSee('Dit is wat er leeft in Schaarbeek.');
});

test('a ride within a week makes the pre-ride moment with its weekday', function () {
    $group = Group::factory()->create();
    $member = establishedMember($group);
    $ride = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDays(3),
        'is_published' => true,
    ]);
    $ride->groups()->attach($group);

    $weekday = ucfirst(RideDate::weekday($ride->begin_date));

    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertSee('data-moment="pre-ride"', escape: false)
        ->assertSee("{$weekday} rijden we.");
});

function recapRideForGroup(Group $group, int $daysAgo, bool $withPhoto = true): Activity
{
    $ride = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->subDays($daysAgo),
        'is_published' => true,
    ]);
    $ride->groups()->attach($group);

    if ($withPhoto) {
        $ride->addMedia(UploadedFile::fake()->image('rit.jpg', 40, 30))->toMediaCollection('gallery');
    }

    return $ride;
}

test('a fresh ride with photos leads the overview as the recap moment', function () {
    $group = Group::factory()->create();
    $member = establishedMember($group);
    $ride = recapRideForGroup($group, daysAgo: 4);

    $weekday = RideDate::weekday($ride->begin_date);

    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertSee('data-moment="recap"', escape: false)
        ->assertSee("Dat was 'm.")
        ->assertSee("1 foto's van de rit van {$weekday} staan in het album");
});

test('the recap steps aside after the window or without photos', function () {
    $group = Group::factory()->create();
    $member = establishedMember($group);
    recapRideForGroup($group, daysAgo: 6);                    // too old
    recapRideForGroup($group, daysAgo: 2, withPhoto: false);  // fresh but photo-less

    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertDontSee("Dat was 'm.")
        ->assertDontSee('data-moment="recap"', escape: false);
});

test('the personal welcome outranks the recap', function () {
    $group = Group::factory()->create();
    $member = User::factory()->create(['name' => 'Sara Janssens']);
    $group->users()->attach($member, ['role' => 'pinkvest']);
    recapRideForGroup($group, daysAgo: 2);

    // No cookie: the welcome window is open, so Sara's first Monday greets HER, not the album.
    actingAs($member)->get(delightHubUrl($group))
        ->assertSee('data-moment="welcome"', escape: false)
        ->assertSee('Welkom bij de hesjes, Sara.')
        ->assertDontSee("Dat was 'm.");
});

test('the next-ride card counts down the nights to a nearby ride', function () {
    $group = Group::factory()->create();
    $member = establishedMember($group);
    $ride = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDays(3)->setTime(14, 0),
        'is_published' => true,
    ]);
    $ride->groups()->attach($group);

    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertSee('Nog 3 nachtjes slapen.');
});

test('a meeting gets no countdown line', function () {
    $group = Group::factory()->create();
    $member = establishedMember($group);
    $meeting = Activity::factory()->create([
        'activity_type' => ActivityType::MEETING,
        'begin_date' => now()->addDays(2)->setTime(19, 30),
        'is_published' => true,
    ]);
    $meeting->groups()->attach($group);

    actingAs($member)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertDontSee('nachtjes slapen')
        ->assertDontSee('roze-next__count', escape: false);
});

test('a new member gets a celebrating feed card with a hello nudge when a ride is coming', function () {
    $group = Group::factory()->create();
    $viewer = establishedMember($group);
    $newbie = User::factory()->create(['name' => 'Sara Janssens']);
    $group->users()->attach($newbie, ['role' => 'pinkvest']);
    $ride = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDays(4),
        'is_published' => true,
    ]);
    $ride->groups()->attach($group);

    $weekday = RideDate::weekday($ride->begin_date);

    actingAs($viewer)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertSee('data-celebrate', escape: false)
        ->assertSee("Sara Janssens rijdt nu mee als roze hesje. Zeg {$weekday} zeker hallo.");
});

test('without an upcoming ride the member card celebrates without the hello nudge', function () {
    $group = Group::factory()->create();
    $viewer = establishedMember($group);
    $newbie = User::factory()->create(['name' => 'Sara Janssens']);
    $group->users()->attach($newbie, ['role' => 'pinkvest']);

    actingAs($viewer)
        ->withUnencryptedCookie('roze_welcome_'.$group->id, now()->subWeeks(3)->toIso8601String())
        ->get(delightHubUrl($group))
        ->assertSee('data-celebrate', escape: false)
        ->assertSee('Sara Janssens rijdt nu mee als roze hesje')
        ->assertDontSee('zeker hallo');
});

<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

/** @return array<int, string> the 6 hub route names */
function hubRoutes(): array
{
    return [
        'groups.roze-hesjes',
        'groups.roze-hesjes.aan-de-slag',
        'groups.roze-hesjes.agenda',
        'groups.roze-hesjes.fotos',
        'groups.roze-hesjes.groep',
        'groups.roze-hesjes.materiaal',
    ];
}

function hubUrl(string $name, Group $group): string
{
    return route($name, ['locale' => 'nl', 'group' => $group]);
}

function rozeChapter(): Group
{
    return Group::create([
        'shortname' => 'mons',
        'name' => 'Kidical Mass Mons',
        'zip' => '7000',
        'invisible' => false,
        'started_at' => now(),
    ]);
}

test('a member can open every hub page', function (string $name) {
    $group = rozeChapter();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)->get(hubUrl($name, $group))->assertOk();
})->with(hubRoutes());

test('a logged-in non-member is forbidden from every hub page', function (string $name) {
    $group = rozeChapter();

    actingAs(User::factory()->create())->get(hubUrl($name, $group))->assertForbidden();
})->with(hubRoutes());

test('a guest with no demo volunteer is sent to the activate screen', function () {
    $group = rozeChapter();

    get(hubUrl('groups.roze-hesjes', $group))
        ->assertRedirect(route('backstage.activate', $group));
});

test('the De Groep page shows the full roster, not just the public opt-ins', function () {
    $group = rozeChapter();
    $lead = User::factory()->create(['name' => 'Violette Dupont']);
    $hidden = User::factory()->create(['name' => 'Karim Benali']);
    $group->users()->attach($lead, ['is_public' => true]);
    $group->users()->attach($hidden, ['is_public' => false]);

    actingAs($lead)
        ->get(hubUrl('groups.roze-hesjes.groep', $group))
        ->assertOk()
        ->assertSee('Violette')
        ->assertSee('Karim Benali'); // a non-public member is still visible to fellow hesjes
});

test('the agenda renders drafts and confirmed rides, linking each to its destination', function () {
    $group = rozeChapter();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    $confirmed = Activity::factory()->create([
        'is_published' => true,
        'title_nl' => 'Vastgelegde rit',
        'begin_date' => now()->addWeeks(2)->setTime(14, 0),
    ]);
    $confirmed->groups()->attach($group);

    $draft = Activity::factory()->create([
        'is_published' => false,
        'title_nl' => 'Rit in wording',
        'begin_date' => now()->addWeeks(3)->setTime(14, 0),
    ]);
    $draft->groups()->attach($group);

    actingAs($member)->get(hubUrl('groups.roze-hesjes.agenda', $group))
        ->assertOk()
        ->assertSee('Vastgelegde rit')
        ->assertSee('Rit in wording')
        // The draft links to the live preview; the confirmed ride to its public page.
        ->assertSee(route('groups.ride-preview', [$group, 'ride' => $draft->id]), escape: false)
        ->assertSee(route('activities.show', $confirmed), escape: false);
});

test('the hub sets the welcome-window cookie on first visit', function () {
    $group = rozeChapter();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    actingAs($member)
        ->get(hubUrl('groups.roze-hesjes', $group))
        ->assertOk()
        ->assertCookie('roze_welcome_'.$group->id);
});

test('the ride preview is membership-gated', function () {
    $group = rozeChapter();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);
    $outsider = User::factory()->create();

    actingAs($member)
        ->get(route('groups.ride-preview', ['locale' => 'nl', 'group' => $group]))
        ->assertOk();

    actingAs($outsider)
        ->get(route('groups.ride-preview', ['locale' => 'nl', 'group' => $group]))
        ->assertForbidden();
});

test('the agenda shows only the chapter\'s own upcoming published rides and drafts', function () {
    $group = rozeChapter();
    $other = Group::factory()->create();
    $member = User::factory()->create();
    $group->users()->attach($member, ['role' => null]);

    $ownConfirmed = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'is_published' => true,
        'title_nl' => 'Eigen rit',
        'begin_date' => now()->addWeek(),
    ]);
    $ownConfirmed->groups()->attach($group);

    $otherConfirmed = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'is_published' => true,
        'title_nl' => 'Andere rit',
        'begin_date' => now()->addWeek(),
    ]);
    $otherConfirmed->groups()->attach($other);

    $pastOwn = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'is_published' => true,
        'title_nl' => 'Voorbije rit',
        'begin_date' => now()->subWeek(),
    ]);
    $pastOwn->groups()->attach($group);

    actingAs($member)->get(hubUrl('groups.roze-hesjes.agenda', $group))
        ->assertOk()
        ->assertSee('Eigen rit')
        ->assertDontSee('Andere rit')
        ->assertDontSee('Voorbije rit');
});
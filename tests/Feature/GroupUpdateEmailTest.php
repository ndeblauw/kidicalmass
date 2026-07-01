<?php

use App\Actions\GroupChangesResult;
use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

/**
 * Build a GroupChangesResult for a group, defaulting every collection to empty so
 * each test only sets the blocks it cares about.
 *
 * @param  array<string, Collection>  $overrides
 */
function groupChanges(Group $group, array $overrides = []): GroupChangesResult
{
    $empty = collect();

    return new GroupChangesResult(
        startDate: now()->subMonth(),
        endDate: now(),
        group: $group,
        newActivities: $overrides['newActivities'] ?? $empty,
        updatedActivities: $overrides['updatedActivities'] ?? $empty,
        newCaptains: $overrides['newCaptains'] ?? $empty,
        newPinkVests: $overrides['newPinkVests'] ?? $empty,
        newInterested: $overrides['newInterested'] ?? $empty,
        newArticles: $overrides['newArticles'] ?? $empty,
        updatedArticles: $overrides['updatedArticles'] ?? $empty,
        recentRidesWithPhotos: $overrides['recentRidesWithPhotos'] ?? $empty,
        upcomingActivities: $overrides['upcomingActivities'] ?? $empty,
    );
}

function renderUpdateMail(iterable $changes): string
{
    return view('emails.group-update', ['changes' => collect($changes)])->render();
}

beforeEach(function () {
    // The view links to route('activities.index'), which needs the {locale} default
    // the SetLocale middleware normally supplies on a real request.
    URL::defaults(['locale' => app()->getLocale()]);
});

it('renders every block for a single group with full activity', function () {
    Storage::fake('media');

    $group = Group::factory()->create(['name' => 'Schaarbeek']);

    $recap = Activity::factory()->create(['title_nl' => 'Lenterit', 'begin_date' => now()->subDays(5)]);
    $recap->addMedia(UploadedFile::fake()->image('foto.jpg', 40, 30))->toMediaCollection('gallery');

    $upcoming = Activity::factory()->create([
        'title_nl' => 'Fietscheck-workshop',
        'begin_date' => now()->addWeeks(2),
        'location' => 'Josaphatpark',
        'activity_type' => 'workshop',
        'is_published' => true,
    ]);

    $pinkVests = collect(['Sofie Maes', 'Mehmet Yilmaz', 'Lars De Smet'])
        ->map(fn ($name) => (new User)->forceFill(['name' => $name]));

    $article = (new Article)->forceFill(['title_nl' => 'Kets kleuren de straat', 'content_nl' => 'De buurt liep uit voor de rit.']);

    $html = renderUpdateMail([groupChanges($group, [
        'recentRidesWithPhotos' => collect([$recap]),
        'upcomingActivities' => collect([$upcoming]),
        'newPinkVests' => $pinkVests,
        'newArticles' => collect([$article]),
    ])]);

    expect($html)
        ->toContain('laatste rit in Schaarbeek staan online')  // photo-led subject (apostrophe is HTML-escaped in <title>)
        ->toContain('Net gereden, in beeld')
        ->toContain('Lenterit')
        ->toContain("Bekijk alle foto's")
        ->toContain('Binnenkort op de kalender')
        ->toContain('Fietscheck-workshop')
        ->toContain('Workshop')                                                  // type label (labelNl)
        ->toContain('Josaphatpark')
        ->toContain('Nieuwe roze hesjes')
        ->toContain('Sofie, Mehmet en Lars trokken een roze hesje aan')          // first names, NL join
        ->toContain('In het nieuws')
        ->toContain('Kets kleuren de straat')
        ->toContain('Naar de kalender');                                          // single CTA
});

it('uses a singular verb for a single new pink vest', function () {
    $group = Group::factory()->create(['name' => 'Schaarbeek']);
    $pinkVest = collect([(new User)->forceFill(['name' => 'Sofie Maes'])]);

    $html = renderUpdateMail([groupChanges($group, ['newPinkVests' => $pinkVest])]);

    expect($html)->toContain('Sofie trok een roze hesje aan');
});

it('shows a group heading per group in a merged mail', function () {
    $gent = Group::factory()->create(['name' => 'Gent']);
    $brugge = Group::factory()->create(['name' => 'Brugge']);

    $pinkVest = fn () => collect([(new User)->forceFill(['name' => 'Test Persoon'])]);

    $html = renderUpdateMail([
        groupChanges($gent, ['newPinkVests' => $pinkVest()]),
        groupChanges($brugge, ['newPinkVests' => $pinkVest()]),
    ]);

    expect($html)
        ->toContain('Kidical Mass Gent')
        ->toContain('Kidical Mass Brugge')
        ->toContain('roze hesje aan in Gent')                 // group named in the merged variant
        ->toContain('Nieuwe roze hesjes bij jouw groepen');   // pink-vest-led subject (no recap photos)
});

it('drops groups with nothing fresh from the body', function () {
    $active = Group::factory()->create(['name' => 'Schaarbeek']);
    $quiet = Group::factory()->create(['name' => 'Stiltegroep']);

    $html = renderUpdateMail([
        groupChanges($active, ['newPinkVests' => collect([(new User)->forceFill(['name' => 'Sofie Maes'])])]),
        groupChanges($quiet),  // hasAny() === false
    ]);

    expect($html)
        ->toContain('Sofie')
        ->not->toContain('Stiltegroep');
});

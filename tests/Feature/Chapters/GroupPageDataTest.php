<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Ndeblauw\BlueAdmin\Models\Filepond;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileUnacceptableForCollection;

use function Pest\Laravel\get;

beforeEach(function (): void {
    $this->region = Group::factory()->create(['name_nl' => 'Brussels Capital Region', 'name_fr' => 'Bruxelles', 'invisible' => true]);
    $this->group = Group::factory()->withParent($this->region)->create([
        'shortname' => 'schaarbeek',
        'name_nl' => 'Schaarbeek',
        'name_fr' => 'Schaerbeek',
    ]);
});

it('shows the French municipality name on the French page', function () {
    get('/fr/groupes-locaux/'.$this->group->id)
        ->assertOk()
        ->assertSee('Schaerbeek');
});

it('serves the per-group intro in the active locale, or nothing when empty', function () {
    expect($this->group->intro)->toBeNull();

    $this->group->update(['intro_nl' => 'Een eigen zin.', 'intro_fr' => 'Une phrase à nous.']);

    expect($this->group->fresh()->intro)->toBe('Een eigen zin.');

    app()->setLocale('fr');
    expect($this->group->fresh()->intro)->toBe('Une phrase à nous.');
});

it('builds the gallery only from the group\'s own rides, not its parent region', function () {
    $regionRide = Activity::factory()->past(10)->withGallery()->create(['activity_type' => ActivityType::KIDICALMASS]);
    $regionRide->groups()->attach($this->region);

    get(route('groups.show', $this->group))
        ->assertOk()
        ->assertViewHas('latestRide', null);

    $ownRide = Activity::factory()->past(20)->withGallery()->create(['activity_type' => ActivityType::KIDICALMASS]);
    $ownRide->groups()->attach($this->group);

    get(route('groups.show', $this->group))
        ->assertViewHas('latestRide', fn ($ride) => $ride->is($ownRide));
});

it('tells the view when the next ride is borrowed from a parent group', function () {
    $regionRide = Activity::factory()->create(['activity_type' => ActivityType::KIDICALMASS, 'begin_date' => now()->addWeek()]);
    $regionRide->groups()->attach($this->region);

    get(route('groups.show', $this->group))
        ->assertViewHas('nextRideIsOwn', false)
        ->assertViewHas('nextRideArea', 'heel Brussel');

    $ownRide = Activity::factory()->create(['activity_type' => ActivityType::KIDICALMASS, 'begin_date' => now()->addDay()]);
    $ownRide->groups()->attach($this->group);

    get(route('groups.show', $this->group))
        ->assertViewHas('nextRideIsOwn', true)
        ->assertViewHas('nextRideArea', null);
});

it('accepts PDFs and images in the downloads collection but rejects other files', function () {
    Storage::fake('public');

    $media = $this->group->addMediaFromString('%PDF-1.4 flyer')
        ->usingFileName('flyer.pdf')
        ->toMediaCollection('downloads');

    expect($this->group->getMedia('downloads'))->toHaveCount(1)
        ->and($media->file_name)->toBe('flyer.pdf');

    expect(fn () => $this->group->addMediaFromString('plain text')
        ->usingFileName('notes.txt')
        ->toMediaCollection('downloads'))->toThrow(FileUnacceptableForCollection::class);
});

it('offers the downloads upload and intro fields in the admin group form', function () {
    $admin = User::factory()->create(['superadmin' => true]);

    $this->actingAs($admin)
        ->get(route('admin.groups.edit', $this->group))
        ->assertOk()
        ->assertSee('name="intro_nl"', escape: false)
        ->assertSee('downloads');
});

it('replaces the shared hero lead with the group intro, and falls back when empty', function () {
    $lead = __('groups.show.hero_lead', ['name' => 'Schaarbeek']);

    get(route('groups.show', $this->group))->assertOk()->assertSee($lead, escape: false);

    $this->group->update(['intro_nl' => 'Onze eigen openingszin.']);

    get(route('groups.show', $this->group))
        ->assertSee('Onze eigen openingszin.')
        ->assertDontSee($lead, escape: false);
});

it('marks a borrowed next ride, and not an own one', function () {
    $regionRide = Activity::factory()->create(['activity_type' => ActivityType::KIDICALMASS, 'begin_date' => now()->addWeek()]);
    $regionRide->groups()->attach($this->region);

    get(route('groups.show', $this->group))
        ->assertSee('data-ride-origin="parent"', escape: false)
        ->assertSee(__('groups.show.borrowed_heading'))
        ->assertSee('heel Brussel.')
        ->assertDontSee('Brussels Capital Region');

    $ownRide = Activity::factory()->create(['activity_type' => ActivityType::KIDICALMASS, 'begin_date' => now()->addDay()]);
    $ownRide->groups()->attach($this->group);

    get(route('groups.show', $this->group))
        ->assertSee('data-ride-origin="own"', escape: false)
        ->assertDontSee('data-ride-origin="parent"', escape: false);
});

it('lists downloads only when the group has files', function () {
    Storage::fake('public');

    get(route('groups.show', $this->group))->assertDontSee('data-chapter-downloads', escape: false);

    $this->group->addMediaFromString('%PDF-1.4 flyer')->usingFileName('flyer.pdf')->toMediaCollection('downloads');

    get(route('groups.show', $this->group))
        ->assertSee('data-chapter-downloads', escape: false)
        ->assertSee('flyer');
});

it('counts a ride linked to both the group and its region as the group\'s own', function () {
    $ride = Activity::factory()->create(['activity_type' => ActivityType::KIDICALMASS, 'begin_date' => now()->addWeek()]);
    $ride->groups()->attach([$this->region->id, $this->group->id]);

    get(route('groups.show', $this->group))
        ->assertViewHas('nextRideIsOwn', true)
        ->assertDontSee('data-ride-origin="parent"', escape: false);
});

it('turns a disallowed download type into a form error instead of an upload crash', function () {
    Storage::fake('local');
    $path = Storage::disk('local')->path('filepond/abc/notes.txt');
    @mkdir(dirname($path), 0777, true);
    file_put_contents($path, 'plain text');
    $serverId = app(Filepond::class)->getServerIdFromPath($path);

    $admin = User::factory()->create(['superadmin' => true]);

    $this->actingAs($admin)
        ->put(route('admin.groups.update', $this->group), [
            'shortname' => $this->group->shortname,
            'name_nl' => 'Schaarbeek',
            'started_at' => '2020-01-01',
            'downloads' => [$serverId],
        ])
        ->assertSessionHasErrors('downloads.0');
});

it('hides partners without a French name on the French page, heading included', function () {
    $this->group->partners()->create(['name_nl' => 'Pro Velo', 'name_fr' => null, 'visible' => true]);

    get('/nl/chapters/'.$this->group->id)->assertSee('Pro Velo')->assertSee(__('groups.show.thanks'));

    app()->setLocale('fr');
    get('/fr/groupes-locaux/'.$this->group->id)
        ->assertOk()
        ->assertDontSee('Pro Velo')
        ->assertDontSee('chapter-partners', false);
});

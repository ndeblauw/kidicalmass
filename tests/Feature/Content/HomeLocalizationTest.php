<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;
use Illuminate\Support\Facades\Lang;

use function Pest\Laravel\get;

it('renders the localized Dutch hero title and provides it in both home locales', function () {
    $this->withoutVite();

    $dutchTitle = __('home.hero.title', [], 'nl');
    $frenchTitle = __('home.hero.title', [], 'fr');

    expect($dutchTitle)->not->toBe('home.hero.title');
    expect($frenchTitle)->not->toBe('home.hero.title');

    $localizedTitle = 'Localized Home Hero';

    Lang::addLines(['home.hero.title' => $localizedTitle], 'nl');

    get('/nl')
        ->assertOk()
        ->assertSeeText($localizedTitle);
});

it('renders shared location picker copy on the Home page without a selected location', function () {
    $this->withoutVite();

    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDay(),
    ]);

    $localizedPrompt = 'Localized Home Location Prompt';

    Lang::addLines(['common.location.prompt' => $localizedPrompt], 'nl');

    get('/nl')
        ->assertSeeText($localizedPrompt);
});

it('renders shared location picker copy on the Home page with a selected location', function () {
    $this->withoutVite();

    PostalCode::create(['zip' => '1090', 'name' => 'Jette', 'latitude' => 50.8782, 'longitude' => 4.3265]);
    Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
        'begin_date' => now()->addDay(),
        'postal_code' => '1090',
    ]);

    Lang::addLines(['common.location.current' => 'Localized Home Location'], 'nl');

    $this->withCookie(config('location.cookie'), json_encode([
        'zip' => '1090',
        'lat' => 50.8782,
        'lng' => 4.3265,
        'name' => 'Jette',
    ]))
        ->get('/nl')
        ->assertSeeText('Localized Home Location Jette');
});

it('renders French Home calls to action at their French destinations', function () {
    $this->withoutVite();

    get('/fr')
        ->assertSee('/fr/premiere-fois', escape: false)
        ->assertSee('/fr/groupes-locaux', escape: false)
        ->assertSee('/fr/donner-un-coup-de-main', escape: false)
        ->assertSee('/fr/newsletter', escape: false);
});

<?php

use App\Enums\PartnerCategory;
use App\Models\Group;
use App\Models\Partner;

use function Pest\Laravel\get;

it('renders categorised national partners as cards from the database', function () {
    Partner::factory()->create([
        'name' => 'Testgewest Mobiliteit',
        'description_nl' => 'Gewestelijke testpartner.',
        'category' => PartnerCategory::INSTITUTIONEEL,
        'visible' => true,
        'group_id' => null,
    ]);
    Partner::factory()->create([
        'name' => 'Onzichtbare Partner',
        'category' => PartnerCategory::INSTITUTIONEEL,
        'visible' => false,
        'group_id' => null,
    ]);
    Partner::factory()->create([
        'name' => 'Ongecategoriseerde Partner',
        'category' => null,
        'visible' => true,
        'group_id' => null,
    ]);

    get('/nl/about/partners')
        ->assertOk()
        ->assertSee('Testgewest Mobiliteit')
        ->assertSee('Gewestelijke testpartner.')
        ->assertDontSee('Onzichtbare Partner')
        ->assertDontSee('Ongecategoriseerde Partner');
});

it('orders institutioneel before bondgenoot', function () {
    Partner::factory()->create(['name' => 'Alliantie A', 'category' => PartnerCategory::BONDGENOOT, 'visible' => true, 'group_id' => null]);
    Partner::factory()->create(['name' => 'Zetel Z', 'category' => PartnerCategory::INSTITUTIONEEL, 'visible' => true, 'group_id' => null]);

    get('/nl/about/partners')->assertOk()->assertSeeInOrder(['Zetel Z', 'Alliantie A']);
});

it('excludes chapter-scoped partners from the national cards', function () {
    $group = Group::factory()->create();

    Partner::factory()->create([
        'name' => 'Fietsbieb Wijkhaven',
        'category' => PartnerCategory::INSTITUTIONEEL,
        'visible' => true,
        'group_id' => $group->id,
    ]);

    get('/nl/about/partners')
        ->assertOk()
        ->assertDontSee('Fietsbieb Wijkhaven');
});

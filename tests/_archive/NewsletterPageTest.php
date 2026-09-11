<?php

use App\Models\Group;
use App\Models\PostalCode;

use function Pest\Laravel\get;
use function Pest\Laravel\withCookie;

it('welcomes the subscriber and offers next steps on the confirmed page', function () {
    get('/nl/nieuwsbrief/bevestigd')
        ->assertOk()
        ->assertSee('Je bent erbij')
        ->assertSee('Bekijk de kalender')
        ->assertSee('Vind je groep');
});

it('shows the signup page with hero, form and reassurance', function () {
    get('/nl/nieuwsbrief')
        ->assertOk()
        ->assertSee('Elke maand de nieuwste ritten in je bus')
        ->assertSee('Je e-mailadres')
        ->assertSee('Geen spam, uitschrijven met één klik')
        ->assertSee('Ritten bij jou in de buurt kiezen')
        ->assertDontSee('Bekijk alle groepen');
});

it('reflects the saved location and shows nearby chapters immediately', function () {
    PostalCode::insert([
        ['zip' => '1030', 'name' => 'Schaarbeek', 'latitude' => 50.8669, 'longitude' => 4.3733, 'created_at' => now(), 'updated_at' => now()],
    ]);

    Group::create([
        'shortname' => 'sb',
        'name' => 'Kidical Mass Schaarbeek',
        'zip' => '1030',
        'invisible' => false,
        'started_at' => now(),
    ]);

    withCookie('kcm_location', json_encode([
        'zip' => '1030', 'lat' => 50.8669, 'lng' => 4.3733, 'name' => 'Schaarbeek',
    ]))
        ->get('/nl/nieuwsbrief')
        ->assertOk()
        ->assertSee('Schaarbeek')
        ->assertSee('We sturen je standaard de ritten van deze groepen')
        // the lone chapter is already shown, so the "Heel België" opt-in is suppressed
        ->assertDontSee('Heel België')
        // chip shows the bare gemeente, not the full "Kidical Mass …" name
        ->assertDontSee('Kidical Mass Schaarbeek');
});

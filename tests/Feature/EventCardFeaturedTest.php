<?php

use App\Models\Activity;
use App\Models\User;

use function Pest\Laravel\get;

it('features the Grande Kidical Mass inline with an Uitgelicht badge', function () {
    $author = User::factory()->create();

    Activity::create([
        'title_nl' => 'Grande Grote Kidical Mass 2026', 'title_fr' => 'x',
        'content_nl' => 'x', 'content_fr' => 'x',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addWeek(), 'duration_minutes' => 60,
        'location' => 'Jubelpark, Brussel', 'author_id' => $author->id,
    ]);

    Activity::create([
        'title_nl' => 'Kidical Mass Schaarbeek', 'title_fr' => 'x',
        'content_nl' => 'x', 'content_fr' => 'x',
        'activity_type' => 'kidicalmass',
        'begin_date' => now()->addDays(2), 'duration_minutes' => 60,
        'location' => 'Place Colignon', 'author_id' => $author->id,
    ]);

    $response = get('/nl/events')
        ->assertOk()
        ->assertSee('Grande Grote Kidical Mass 2026')
        ->assertSee('Schaarbeek')
        // The flagship is featured inline (PAT-13/D-3), in its chronological slot.
        ->assertSee('Uitgelicht');

    // The badge appears exactly once — only the Grande, never a normal ride.
    expect(substr_count($response->getContent(), 'Uitgelicht'))->toBe(1);
});

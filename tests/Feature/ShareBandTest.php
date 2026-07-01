<?php

use App\Enums\ActivityType;
use App\Models\Activity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;

uses(RefreshDatabase::class);

it('renders the share band with framed copy and all four channels', function () {
    $url = 'https://kidicalmass.test/nl/events/17';

    $html = Blade::render(
        '<x-share-band :url="$url" title="Kidical Mass Gent" date="zondag 28 juni" />',
        ['url' => $url]
    );

    $encodedUrl = rawurlencode($url);

    expect($html)
        ->toContain('Ken je een gezin dat dit leuk zou vinden?')
        ->toContain('Kopieer link')
        ->toContain('wa.me/?text=')
        ->toContain('facebook.com/sharer/sharer.php?u='.rawurlencode($url))
        ->toContain('mailto:?subject=')
        ->toContain($encodedUrl) // the ride URL is encoded into the WhatsApp message
        ->toContain('aria-label="Deel via WhatsApp"')
        ->toContain('aria-label="Deel op Facebook"')
        ->toContain('aria-label="Deel via e-mail"');
});

it('lets callers override the heading and subline', function () {
    $html = Blade::render(
        '<x-share-band :url="$url" title="T" date="d" heading="Anders" subline="Ook anders" />',
        ['url' => 'https://example.test/x']
    );

    expect($html)->toContain('Anders')->toContain('Ook anders');
});

it('shows the in-context share panel on the ride page and no longer shows the old action bar', function () {
    $activity = Activity::factory()->create([
        'activity_type' => ActivityType::KIDICALMASS,
    ]);

    $response = $this->get(route('activities.show', $activity));

    $response->assertOk()
        ->assertSee('activity-share', false)             // share now lives beside the facts, not in a full-width band
        ->assertSee('wa.me/?text=', false)
        ->assertDontSee('Ken je een gezin dat dit leuk zou vinden?') // the old full-width band is gone
        ->assertDontSee('activity-actions-bar')
        ->assertDontSee('Bewaar in agenda');
});

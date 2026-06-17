<?php

use App\Models\Activity;
use App\Models\Group;

// tests/Feature/PublicPagesRenderTest.php
it('renders public pages without server errors', function (string $uri) {
    $this->get($uri)->assertOk();
})->with([
    '/nl', '/nl/events', '/nl/chapters', '/nl/getting-started',
    '/nl/steun-ons', '/nl/help-out', '/nl/find-a-bike', '/nl/about',
    '/nl/about/press', '/nl/contact', '/nl/privacy',
]);

it('renders the event detail page', function () {
    $activity = Activity::factory()->create();

    $this->get(route('activities.show', ['locale' => 'nl', 'activity' => $activity]))
        ->assertOk();
});

it('renders the chapter detail page', function () {
    $group = Group::factory()->create();

    $this->get(route('groups.show', ['locale' => 'nl', 'group' => $group]))
        ->assertOk();
});

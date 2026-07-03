<?php

test('static page titles carry the brand suffix', function () {
    $this->get('/nl/events')
        ->assertOk()
        ->assertSee('<title>Kalender · Kidical Mass België</title>', false);
});

test('home title stands alone without the suffix', function () {
    $this->get('/nl')
        ->assertOk()
        ->assertSee('<title>'.__('meta.home_title').'</title>', false)
        ->assertDontSee('· Kidical Mass België</title>', false);
});

test('every page has a description, canonical and OG baseline', function (string $route) {
    $this->get($route)
        ->assertOk()
        ->assertSee('<link rel="canonical" href="'.url($route).'">', false)
        ->assertSee('property="og:url" content="'.url($route).'"', false)
        ->assertSee('property="og:site_name" content="Kidical Mass België"', false)
        ->assertSee('property="og:locale" content="nl_BE"', false)
        ->assertSee('name="twitter:card" content="summary_large_image"', false)
        ->assertSee('property="og:image" content="'.asset('img/og-default.jpg').'"', false)
        ->assertSee('name="description" content="', false);
})->with('public routes');

test('the head links favicons, manifest and theme-color', function () {
    $this->get('/nl')
        ->assertOk()
        ->assertSee('<link rel="icon" href="/favicon.svg" type="image/svg+xml">', false)
        ->assertSee('<link rel="apple-touch-icon" href="/apple-touch-icon.png">', false)
        ->assertSee('<link rel="manifest" href="/site.webmanifest">', false)
        ->assertSee('<meta name="theme-color" content="#1d67cd">', false);
});

use App\Models\Activity;
use App\Models\Group;

test('the calendar page uses its own meta description', function () {
    $this->get('/nl/events')
        ->assertOk()
        ->assertSee('name="description" content="'.e(__('meta.calendar')).'"', false);
});

test('an activity page derives description and share image from its content', function () {
    $activity = Activity::factory()->withMedia()->create(['content_nl' => '<p>Een vrolijke rit door de stad.</p>']);

    $this->get("/nl/events/{$activity->id}")
        ->assertOk()
        ->assertSee('name="description" content="Een vrolijke rit door de stad."', false)
        ->assertSee('-og.jpg', false);
});

test('an activity without a photo falls back to the branded share image', function () {
    $activity = Activity::factory()->create();

    $this->get("/nl/events/{$activity->id}")
        ->assertOk()
        ->assertSee('property="og:image" content="'.asset('img/og-default.jpg').'"', false);
});

test('a chapter page templates its description from the group name', function () {
    $group = Group::factory()->create(['name' => 'Gent']);

    $this->get("/nl/chapters/{$group->id}")
        ->assertOk()
        ->assertSee('name="description" content="'.e(__('meta.chapter', ['name' => 'Gent'])).'"', false);
});

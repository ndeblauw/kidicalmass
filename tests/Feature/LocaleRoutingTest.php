<?php

use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;

use function Pest\Laravel\get;

it('redirects the bare root to the nl prefix', function () {
    get('/')->assertRedirect('/nl');
});

it('serves the home page under /nl with a nl lang attribute', function () {
    get('/nl')
        ->assertOk()
        ->assertSee('lang="nl"', escape: false);
});

it('404s an unsupported locale', function () {
    get('/fr')->assertNotFound();
});

it('serves the renamed IA paths for existing pages', function () {
    $group = Group::factory()->create();
    $activity = Activity::factory()->create(['begin_date' => now()->addWeek()]);
    $activity->groups()->attach($group);
    Article::factory()->create();

    get('/nl/events')->assertOk();
    get('/nl/chapters')->assertOk();
    get('/nl/about/news')->assertOk();
    get('/nl/help-out')->assertOk();
});

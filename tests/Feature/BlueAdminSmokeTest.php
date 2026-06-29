<?php

use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use App\Models\Partner;
use App\Models\PressArticle;
use App\Models\User;
use App\Models\YearStat;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    User::query()->delete();
    Group::query()->delete();

    $this->admin = User::factory()->create(['superadmin' => true]);
});

it('renders the blue-admin dashboard', function () {
    actingAs($this->admin)
        ->get('/admin')
        ->assertOk();
});

it('renders the blue-admin index of every migrated resource', function (string $path) {
    actingAs($this->admin)
        ->get($path)
        ->assertOk();
})->with([
    'year stats' => '/admin/yearstats',
    'contact forms' => '/admin/contactforms',
    'users' => '/admin/users',
    'partners' => '/admin/partners',
    'groups' => '/admin/groups',
    'articles' => '/admin/articles',
    'press articles' => '/admin/pressarticles',
    'activities' => '/admin/activities',
]);

it('renders the blue-admin create form for resources that support creation', function (string $path) {
    actingAs($this->admin)
        ->get($path)
        ->assertOk();
})->with([
    'year stats' => '/admin/yearstats/create',
    'users' => '/admin/users/create',
    'partners' => '/admin/partners/create',
    'groups' => '/admin/groups/create',
    'articles' => '/admin/articles/create',
    'press articles' => '/admin/pressarticles/create',
    'activities' => '/admin/activities/create',
]);

it('renders the blue-admin edit form for a sample record of every editable resource', function (string $path, callable $factory) {
    $record = $factory();

    actingAs($this->admin)
        ->get($path.'/'.$record->getKey().'/edit')
        ->assertOk();
})->with([
    'year stats' => ['/admin/yearstats', fn () => YearStat::create(['year' => 2025, 'participants' => 1000])],
    'partners' => ['/admin/partners', fn () => Partner::factory()->create()],
    'groups' => ['/admin/groups', fn () => Group::create(['shortname' => 'demo', 'name' => 'Demo', 'started_at' => now()])],
    'activities' => ['/admin/activities', fn () => Activity::factory()->create()],
    'articles' => ['/admin/articles', fn () => Article::factory()->create()],
    'press articles' => ['/admin/pressarticles', fn () => PressArticle::factory()->create()],
    'users' => ['/admin/users', fn () => User::factory()->create()],
]);

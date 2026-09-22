<?php

use App\Models\Group;

use function Pest\Laravel\get;

beforeEach(function (): void {
    $this->withoutVite();
});

it('links to the French twin of the current page', function () {
    get('/nl/events')->assertSee('/fr/agenda', escape: false);
});

it('links back to the Dutch twin from a French page', function () {
    get('/fr/agenda')->assertSee('/nl/events', escape: false);
});

it('keeps the bound model when switching locale on a detail page', function () {
    $group = Group::factory()->create();

    get('/nl/chapters/'.$group->getRouteKey())
        ->assertSee('/fr/groupes-locaux/'.$group->getRouteKey(), escape: false);
});

it('offers no English link and marks EN as coming soon', function () {
    get('/nl')
        ->assertSee('aria-disabled="true"', escape: false)
        ->assertSee(__('nav.coming_soon', [], 'nl'))
        ->assertDontSee('href="'.url('/en').'"', escape: false);
});

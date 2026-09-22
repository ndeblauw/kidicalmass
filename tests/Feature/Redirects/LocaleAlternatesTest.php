<?php

it('maps a Dutch route name to its French twin', function () {
    $route = app('router')->getRoutes()->getByName('activities.index');

    expect(alternate_locale_url('fr', $route))->toEndWith('/fr/agenda');
});

it('maps a French route name back to its Dutch twin', function () {
    $route = app('router')->getRoutes()->getByName('fr.activities.index');

    expect(alternate_locale_url('nl', $route))->toEndWith('/nl/events');
});

it('returns null when the page has no route in the target locale', function () {
    $route = app('router')->getRoutes()->getByName('groups.ride-preview');

    expect(alternate_locale_url('fr', $route))->toBeNull()
        ->and(alternate_locale_url('en', $route))->toBeNull();
});

<?php

it('serves the build dashboard in non-production', function () {
    expect(app()->isProduction())->toBeFalse();

    $this->get('/build')
        ->assertOk()
        ->assertSee('build status')
        ->assertSee('id="P-01"', false)
        ->assertSee('<abbr', false);
});

it('links to the review mode', function () {
    $this->get('/build')->assertSee(route('build.review'));
});

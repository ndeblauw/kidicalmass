<?php

it('serves the build dashboard in non-production', function () {
    expect(app()->isProduction())->toBeFalse();

    $this->get('/build')
        ->assertOk()
        ->assertSee('build status')
        ->assertSee('id="P-01"', false)
        ->assertSee('<abbr', false);
});

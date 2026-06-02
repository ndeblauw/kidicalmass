<?php

use App\Support\Build\DriftChecker;
use App\Support\Build\Stage;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->checker = new DriftChecker(stubLineThreshold: 12, stubMarkers: ['[placeholder']);
});

function page(array $overrides = []): array
{
    return array_merge([
        'id' => 'P-99',
        'slug' => '/nope',
        'stages' => ['ux' => Stage::NotStarted, 'wireframe' => Stage::NotStarted],
        'briefPath' => null,
        'viewPath' => null,
        'routeUri' => null,
    ], $overrides);
}

it('flags a page whose UX is declared but the brief file is missing', function () {
    $findings = $this->checker->check([
        page([
            'stages' => ['ux' => Stage::Good, 'wireframe' => Stage::NotStarted],
            'briefPath' => 'docs/wiki/design/30-skeleton/does-not-exist.md',
        ]),
    ], []);

    expect($findings)->toHaveCount(1)
        ->and($findings[0]['id'])->toBe('P-99')
        ->and($findings[0]['message'])->toContain('UX');
});

it('finds nothing for a fully not-started page', function () {
    $findings = $this->checker->check([
        page(['briefPath' => 'whatever.md']),
    ], []);

    expect($findings)->toBe([]);
});

it('flags a wireframed page whose route is not registered', function () {
    $findings = $this->checker->check([
        page(['stages' => ['ux' => Stage::NotStarted, 'wireframe' => Stage::Good], 'routeUri' => 'totally/unrouted']),
    ], []);

    expect($findings)->toHaveCount(1)
        ->and($findings[0]['message'])->toContain('route');
});

it('does not flag a wireframed page whose route exists', function () {
    // `build` is registered in routes/web.php (non-production).
    $findings = $this->checker->check([
        page(['stages' => ['ux' => Stage::NotStarted, 'wireframe' => Stage::Good], 'routeUri' => 'build']),
    ], []);

    expect($findings)->toBe([]);
});

it('flags a pattern claiming a missing partial', function () {
    $findings = $this->checker->check([], [
        ['id' => 'PAT-99', 'partialPath' => 'resources/views/partials/nope.blade.php'],
    ]);

    expect($findings)->toHaveCount(1)
        ->and($findings[0]['id'])->toBe('PAT-99');
});

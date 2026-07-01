<?php

use App\Support\Build\BuildStatus;
use App\Support\Build\Stage;
use Tests\TestCase;

uses(TestCase::class);

describe('report assembled from fixture sources', function () {
    beforeEach(function () {
        config()->set('build.sources', [
            'skeleton' => 'tests/Fixtures/build/page-registry.md',
            'concerns' => 'tests/Fixtures/build/concerns.md',
            'patterns' => 'tests/Fixtures/build/patterns.md',
        ]);
        config()->set('build.briefs', []);
        config()->set('build.pages', []);

        $this->report = app(BuildStatus::class)->report();
    });

    it('parses pages with stages, confidence and overview counts', function () {
        $pages = collect($this->report['pages']);
        expect($pages)->toHaveCount(2);

        $home = $pages->firstWhere('id', 'P-01');
        expect($home['name'])->toBe('Home')
            ->and($home['slug'])->toBe('/')
            ->and($home['confidence'])->toBe(3);

        // Assert the actual stage per column, not just "is a Stage" — otherwise a
        // wrong column index (🟢/⚪/🟠/🔴 read off-by-one) parses silently.
        expect($home['stages'])->toMatchArray([
            'ux' => Stage::Good,            // 🟢
            'wireframe' => Stage::Good,     // 🟢
            'assets' => Stage::NotApplicable, // ⚪
            'ui' => Stage::InProgress,      // 🟠
            'back' => Stage::NotStarted,    // 🔴
            'ok' => Stage::Good,            // 🟢
        ]);

        expect($this->report['overview']['pagesTotal'])->toBe(2)
            ->and($this->report['overview']['avgConfidence'])->toBe(2.5);

        expect($this->report['warnings'])->toBe([]);
    });

    it('parses patterns, concerns with status, and builds an id map', function () {
        expect(collect($this->report['patterns'])->pluck('id')->all())
            ->toContain('PAT-1', 'PAT-2');

        $concerns = collect($this->report['concerns']);
        expect($concerns->pluck('id')->all())->toContain('D-1', 'D-2')
            ->and($concerns->firstWhere('id', 'D-1')['status'])->toBe('partly')
            ->and($concerns->firstWhere('id', 'D-2')['status'])->toBe('open');

        expect($this->report['idMap'])->toHaveKey('D-1')
            ->and($this->report['idMap']['D-1'])->toBeString();
    });

    it('reports per-source freshness', function () {
        expect($this->report['sources'])->toHaveKey('skeleton')
            ->and($this->report['sources']['skeleton']['file'])->toBe('page-registry.md')
            ->and($this->report['sources']['skeleton']['ago'])->toBeString();
    });
});

it('linkifies id tokens and escapes other text', function () {
    $html = BuildStatus::linkify('see D-1 & PAT-8 & <b>x</b>', ['D-1' => 'Back-office', 'PAT-8' => 'Chapter map']);
    expect($html)->toContain('href="#D-1"')
        ->toContain('href="#PAT-8"')
        ->toContain('title="Back-office"')
        ->toContain('&lt;b&gt;'); // non-token text escaped
});

it('plainifies markdown and summarizes to the first sentence', function () {
    expect(BuildStatus::plainify('See [Glossary](../glossary.md) and **bold** and *italic* and `code`.'))
        ->toBe('See Glossary and bold and italic and code.');

    expect(BuildStatus::summarize('Avg page content-confidence = 2.3 / 5. A long trailing sentence dropped from the row.', 140))
        ->toBe('Avg page content-confidence = 2.3 / 5.');
});

// The /build dashboard parses these live docs at runtime, and the CLAUDE.md
// pipeline workflow makes "warnings empty" a documented manual check. This guard
// catches a registry edit that breaks the table format — without pinning exact
// counts or IDs, which legitimately change as the project evolves.
it('still parses the real wiki sources cleanly for the /build dashboard', function () {
    $report = app(BuildStatus::class)->report();

    expect($report['warnings'])->toBe([])
        ->and($report['overview']['pagesTotal'])->toBeGreaterThan(0)
        ->and($report['sources'])->toHaveKey('skeleton');
});

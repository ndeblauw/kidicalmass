<?php

use App\Support\Build\BuildStatus;
use App\Support\Build\Stage;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->report = app(BuildStatus::class)->report();
});

it('parses all 24 pages with stages and confidence', function () {
    $pages = collect($this->report['pages']);
    expect($pages)->toHaveCount(24);

    $home = $pages->firstWhere('id', 'P-01');
    expect($home['name'])->toContain('Home')
        ->and($home['slug'])->toBe('/')
        ->and($home['stages']['ux'])->toBeInstanceOf(Stage::class)
        ->and($home['confidence'])->toBe(3);
});

it('parses patterns and concerns and builds an id map', function () {
    expect(collect($this->report['patterns'])->pluck('id')->all())->toContain('PAT-8')
        ->and(collect($this->report['concerns'])->pluck('id')->all())->toContain('D-1')
        ->and($this->report['idMap'])->toHaveKey('D-1')
        ->and($this->report['idMap']['D-1'])->toBeString();
});

it('computes overview counts', function () {
    expect($this->report['overview']['pagesTotal'])->toBe(24)
        ->and($this->report['overview']['avgConfidence'])->toBeGreaterThan(0);
});

it('reports no parse warnings against the real source files', function () {
    expect($this->report['warnings'])->toBe([]);
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

it('reports per-source freshness', function () {
    expect($this->report['sources'])->toHaveKey('skeleton')
        ->and($this->report['sources']['skeleton']['file'])->toBe('00-page-registry.md')
        ->and($this->report['sources']['skeleton']['ago'])->toBeString();
});

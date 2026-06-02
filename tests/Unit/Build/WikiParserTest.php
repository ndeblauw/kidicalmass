<?php

use App\Support\Build\WikiParser;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    $this->parser = new WikiParser;
});

it('extracts page-registry rows from the registry', function () {
    $md = $this->parser->read('docs/wiki/design/30-skeleton/00-page-registry.md');
    $rows = $this->parser->extractRows($md, '/^P-\d+$/');

    expect(count($rows))->toBeGreaterThanOrEqual(21);

    $p01 = collect($rows)->first(fn ($r) => $r[0] === 'P-01');
    expect($p01[1])->toContain('Home')
        ->and($p01)->toHaveCount(12); // ID,Page,Slug,Type,UX,Conf,Wire,Assets,UI,Back,OK,Gaps
});

it('extracts bold PAT- pattern rows', function () {
    $md = $this->parser->read('docs/wiki/design/40-patterns.md');
    $rows = $this->parser->extractRows($md, '/^PAT-\d+$/');

    $ids = collect($rows)->map(fn ($r) => trim($r[0], '* `'))->all();
    expect($ids)->toContain('PAT-8')
        ->and(count($rows))->toBeGreaterThanOrEqual(18);
});

it('extracts concern D- headings and the status summary table', function () {
    $md = $this->parser->read('docs/wiki/design/01-concerns.md');

    $headings = $this->parser->extractHeadings($md, '/^#{2,4}\s*`?(?<id>D-\d+)`?\s*[—\-–]\s*(?<title>.+)$/u');
    expect(collect($headings)->pluck('id')->all())->toContain('D-1');

    $summary = $this->parser->extractRows($md, '/^(Open|Partly|Closed)$/i');
    expect($summary)->not->toBeEmpty();
});

it('extracts the sitemap fenced block from the structure doc', function () {
    $md = $this->parser->read('docs/wiki/design/20-structure.md');
    $tree = $this->parser->extractFencedBlock($md, 'Sitemap');

    expect($tree)->toContain('Home')
        ->and($tree)->toContain('Chapters');
});

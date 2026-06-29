<?php

use App\Support\Build\WikiParser;

// Pure parser logic, exercised on known inline markdown rather than the live
// wiki docs — the extraction must work on any well-formed markdown, not just
// today's registry. The "real docs still parse" guard lives in BuildStatusTest.

beforeEach(function () {
    $this->parser = new WikiParser;
});

it('extracts table rows whose first cell matches the id regex', function () {
    $md = <<<'MD'
        | ID | Page | Slug |
        |----|------|------|
        | P-01 | Home | `/` |
        | P-02 | Kalender | `/events` |
        | note | not a page row | |
        MD;

    $rows = $this->parser->extractRows($md, '/^P-\d+$/');

    expect($rows)->toHaveCount(2)
        ->and($rows[0][0])->toBe('P-01')
        ->and($rows[0][1])->toBe('Home')
        ->and($rows[0])->toHaveCount(3); // ID, Page, Slug
});

it('strips bold and backticks from the id cell before matching', function () {
    $md = <<<'MD'
        | **PAT-1** | Chapter map | A map |
        | `D-1` | Back-office | A concern |
        | regular text | ignored | row |
        MD;

    expect($this->parser->extractRows($md, '/^PAT-\d+$/'))->toHaveCount(1)
        ->and($this->parser->extractRows($md, '/^D-\d+$/'))->toHaveCount(1);
});

it('ignores non-table lines and empty input', function () {
    $md = "Just a paragraph.\n\n## A heading\n\nNo pipes here.";

    expect($this->parser->extractRows($md, '/^P-\d+$/'))->toBe([])
        ->and($this->parser->extractRows('', '/^P-\d+$/'))->toBe([]);
});

it('extracts id and title from heading-based registers', function () {
    $md = <<<'MD'
        # Concerns

        ### `D-1` — Back-office scope

        Body.

        ### `D-2` — Sponsor tracking

        Body.
        MD;

    $headings = $this->parser->extractHeadings(
        $md,
        '/^#{2,4}\s*`?(?<id>D-\d+)`?\s*[—\-–]\s*(?<title>.+)$/u'
    );

    expect($headings)->toHaveCount(2)
        ->and($headings[0])->toBe(['id' => 'D-1', 'title' => 'Back-office scope'])
        ->and($headings[1]['id'])->toBe('D-2');
});

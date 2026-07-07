<?php

use App\Support\Build\RegistryWriter;
use Illuminate\Support\Facades\File;

/**
 * Fixture registry: 2 rows, realistic 12-column shape, plus a malformed row.
 * Written to a temp path so the real wiki file is never touched.
 */
function fakeRegistry(): string
{
    return <<<'MD'
# Skeleton — page registry & build pipeline

| ID | Page | Slug | Type | UX | Conf | Wire | Assets | UI | Back | OK | Top gaps |
|---|---|---|---|---|---|---|---|---|---|---|---|
| P-01 | **Home** | `/` | Conv | 🟢 | 3 | 🟢 | 🟠 | 🟢 | 🟠 | 🔴 | NL video hero, spec: [x](y.md). |
| P-05 | **Contact** | `/contact` | Utility | 🟢 | 2 | 🟠 | ⚪ | 🟠 | 🟠 | 🔴 | Brief live. |
| P-99 | **Broken** | `/broken` | Foo | 🟢 | 2 | 🟠 |

## Roll-up

- prose that must never change
MD;
}

beforeEach(function () {
    $this->registryPath = 'tests/tmp/registry-'.uniqid().'.md';
    $this->inboxPath = 'tests/tmp/review-inbox.md';
    $this->logPath = 'tests/tmp/log.md';
    File::ensureDirectoryExists(base_path('tests/tmp'));
    File::put(base_path($this->registryPath), fakeRegistry());
    File::put(base_path($this->logPath), "# Wiki Log\n\n## [2026-07-01] build | ouder\n\nOude entry.\n");
    config()->set('build.sources.skeleton', $this->registryPath);
    config()->set('build.review.inbox', $this->inboxPath);
    config()->set('build.review.log', $this->logPath);
});

afterEach(function () {
    File::deleteDirectory(base_path('tests/tmp'));
});

it('replaces only the targeted stage cells and leaves every other byte alone', function () {
    app(RegistryWriter::class)->updateStages('P-05', ['wireframe' => '🟢', 'ui' => '🟢', 'conf' => '3']);

    $after = File::get(base_path($this->registryPath));
    $expected = str_replace(
        '| P-05 | **Contact** | `/contact` | Utility | 🟢 | 2 | 🟠 | ⚪ | 🟠 | 🟠 | 🔴 | Brief live. |',
        '| P-05 | **Contact** | `/contact` | Utility | 🟢 | 3 | 🟢 | ⚪ | 🟢 | 🟠 | 🔴 | Brief live. |',
        fakeRegistry()
    );
    expect($after)->toBe($expected);
});

it('refuses a row with an unexpected column count and writes nothing', function () {
    expect(fn () => app(RegistryWriter::class)->updateStages('P-99', ['wireframe' => '🟢']))
        ->toThrow(RuntimeException::class, 'kolomstructuur');
    expect(File::get(base_path($this->registryPath)))->toBe(fakeRegistry());
});

it('refuses an unknown page id', function () {
    expect(fn () => app(RegistryWriter::class)->updateStages('P-42', ['ui' => '🟢']))
        ->toThrow(RuntimeException::class, 'P-42');
});

it('refuses an unknown column key', function () {
    expect(fn () => app(RegistryWriter::class)->updateStages('P-01', ['gaps' => 'x']))
        ->toThrow(RuntimeException::class, 'gaps');
});

it('never writes in production', function () {
    $this->app['env'] = 'production';
    expect(fn () => app(RegistryWriter::class)->updateStages('P-01', ['ui' => '🟢']))
        ->toThrow(RuntimeException::class);
    $this->app['env'] = 'testing';
});

it('creates the inbox on first note and groups same-day notes per page heading', function () {
    $writer = app(RegistryWriter::class);
    $writer->appendFeedback('P-05', 'Contact', 'hero te druk');
    $writer->appendFeedback('P-05', 'Contact', 'pills wrappen raar op mobiel');

    $inbox = File::get(base_path($this->inboxPath));
    $heading = '## ['.now()->format('Y-m-d').'] P-05 Contact';
    expect(substr_count($inbox, $heading))->toBe(1)
        ->and($inbox)->toContain('- hero te druk')
        ->and($inbox)->toContain('- pills wrappen raar op mobiel');
});

it('files a note under its own page heading even when another page was reviewed in between', function () {
    $writer = app(RegistryWriter::class);
    $writer->appendFeedback('P-05', 'Contact', 'eerste notitie');
    $writer->appendFeedback('P-06', 'Legal', 'privacy notitie');
    $writer->appendFeedback('P-05', 'Contact', 'tweede notitie');

    $inbox = File::get(base_path($this->inboxPath));
    $p05 = strpos($inbox, '] P-05 Contact');
    $p06 = strpos($inbox, '] P-06 Legal');
    $second = strpos($inbox, '- tweede notitie');
    expect($second)->toBeGreaterThan($p05)->toBeLessThan($p06);
});

it('keeps a multi-line note inside one bullet and defuses heading-like lines', function () {
    $writer = app(RegistryWriter::class);
    $writer->appendFeedback('P-05', 'Contact', "regel een\n## geen echte heading\nregel drie");
    $writer->appendFeedback('P-05', 'Contact', 'tweede notitie');

    $inbox = File::get(base_path($this->inboxPath));
    expect($inbox)->toContain("- regel een\n  ## geen echte heading\n  regel drie")
        ->and(substr_count($inbox, '## ['.now()->format('Y-m-d').'] P-05 Contact'))->toBe(1)
        ->and(strpos($inbox, '- tweede notitie'))->toBeGreaterThan(strpos($inbox, '- regel een'));
});

it('inserts one review-session log heading per day right after the title, bullets beneath it', function () {
    $writer = app(RegistryWriter::class);
    $writer->appendLog('**P-05 Contact**: Wire 🟠→🟢');
    $writer->appendLog('**P-01 Home**: Back 🟠→🟢');

    $log = File::get(base_path($this->logPath));
    $heading = '## ['.now()->format('Y-m-d').'] build | review-sessie (/build/review)';
    expect(substr_count($log, $heading))->toBe(1)
        ->and(strpos($log, $heading))->toBeLessThan(strpos($log, '## [2026-07-01]'))
        ->and(strpos($log, '- **P-01 Home**'))->toBeLessThan(strpos($log, '## [2026-07-01]'))
        ->and($log)->toContain('- **P-05 Contact**: Wire 🟠→🟢')
        ->and($log)->toContain($heading."\n\n- **P-05 Contact**: Wire 🟠→🟢");
});

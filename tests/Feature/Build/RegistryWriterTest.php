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
    File::ensureDirectoryExists(base_path('tests/tmp'));
    File::put(base_path($this->registryPath), fakeRegistry());
    config()->set('build.sources.skeleton', $this->registryPath);
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

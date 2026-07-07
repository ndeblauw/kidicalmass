# Build Review Mode (`/build/review`) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** A non-prod split review mode: live page in an iframe, sidebar to bump P-nn registry statuses and drop feedback notes, writing straight into the wiki markdown.

**Architecture:** A `RegistryWriter` support class does all file writes (registry row cells, `review-inbox.md`, `log.md`) with a column-count guard and a production guard. A full-page Livewire component `BuildReview` renders the split layout and walks the registry rows; prev/next are plain links. Spec: `docs/superpowers/specs/2026-07-07-build-review-mode-design.md`.

**Tech Stack:** Laravel 13, Livewire 4 (class in `app/Livewire/` + view in `resources/views/livewire/`), Pest 4 feature tests, Tailwind 4.

## Global Constraints

- Registry SSOT: `docs/wiki/design/30-skeleton/00-page-registry.md`; 12-column rows (`ID·Page·Slug·Type·UX·Conf·Wire·Assets·UI·Back·OK·Top gaps`). **Never touch the Top gaps cell (col 11) or the Roll-up prose.**
- Stage emoji come from `App\Support\Build\Stage` — no second emoji list; cycle order 🔴→🟠→🟢→⚪→❓ (`OK` only 🔴/🟢).
- All writes refuse to run when `app()->environment('production')`; the route registers only inside the existing `if (! app()->isProduction())` block in `routes/web.php`.
- Tests: assert behaviour + `data-*` seams, never Tailwind utilities (see `docs/testing-conventions.md`). Reuse fixtures, not the real wiki files.
- Interface copy is NL, no em-dashes.
- Run `vendor/bin/pint --dirty --format agent` after PHP edits. Stage commits by explicit path (shared checkout with Nico).
- BuildStatus stage keys are `ux, wireframe, assets, ui, back, ok` + `confidence` — reuse these keys everywhere (registry column "Wire" = key `wireframe`).

---

### Task 1: `RegistryWriter::updateStages`

**Files:**
- Create: `app/Support/Build/RegistryWriter.php`
- Test: `tests/Feature/Build/RegistryWriterTest.php`

**Interfaces:**
- Consumes: `config('build.sources.skeleton')` (existing), `Illuminate\Support\Facades\File`.
- Produces: `RegistryWriter::updateStages(string $pageId, array $cells): void` where `$cells` keys ∈ `ux|conf|wireframe|assets|ui|back|ok` and values are the literal cell strings (`'🟢'`, `'3'`). Throws `RuntimeException` on: production env, unknown page, unknown key, malformed column count (file untouched in every failure case).

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/Build/RegistryWriterTest.php`:

```php
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
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --compact --filter=RegistryWriterTest`
Expected: FAIL — `Class "App\Support\Build\RegistryWriter" not found`.

- [ ] **Step 3: Implement `RegistryWriter` (updateStages only)**

Create `app/Support/Build/RegistryWriter.php`:

```php
<?php

namespace App\Support\Build;

use Illuminate\Support\Facades\File;
use RuntimeException;

/**
 * The only code path that WRITES to the wiki markdown. Cell-level edits on the
 * page-registry table plus append-only notes (review-inbox, log). Top-gaps
 * cells and Roll-up prose are curated by hand and never touched here.
 */
class RegistryWriter
{
    /** Registry column index per editable cell, matching BuildStatus::parsePages. */
    private const COLUMNS = [
        'ux' => 4,
        'conf' => 5,
        'wireframe' => 6,
        'assets' => 7,
        'ui' => 8,
        'back' => 9,
        'ok' => 10,
    ];

    /** explode('|') parts of a well-formed 12-column row: leading '' + 12 cells + trailing ''. */
    private const EXPECTED_PARTS = 14;

    /** @param array<string, string> $cells column key => literal cell content ('🟢', '3') */
    public function updateStages(string $pageId, array $cells): void
    {
        $this->guardEnvironment();

        $path = base_path(config('build.sources.skeleton'));
        $lines = explode("\n", File::get($path));

        foreach ($lines as $i => $line) {
            if (! preg_match('/^\|\s*'.preg_quote($pageId, '/').'\s*\|/u', $line)) {
                continue;
            }

            $parts = explode('|', $line);
            if (count($parts) !== self::EXPECTED_PARTS) {
                throw new RuntimeException("Rij {$pageId} heeft een onverwachte kolomstructuur, niets weggeschreven.");
            }

            foreach ($cells as $key => $value) {
                $column = self::COLUMNS[$key]
                    ?? throw new RuntimeException("Onbekende registerkolom: {$key}");
                $parts[$column + 1] = ' '.trim($value).' ';
            }

            $lines[$i] = implode('|', $parts);
            File::put($path, implode("\n", $lines));

            return;
        }

        throw new RuntimeException("Rij {$pageId} niet gevonden in het register.");
    }

    private function guardEnvironment(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('RegistryWriter schrijft nooit in productie.');
        }
    }
}
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --compact --filter=RegistryWriterTest`
Expected: PASS (5 tests).

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Support/Build/RegistryWriter.php tests/Feature/Build/RegistryWriterTest.php
git commit -m "feat(build): RegistryWriter cell-level registry edits with guards"
```

---

### Task 2: `RegistryWriter` appends — review inbox + log.md

**Files:**
- Modify: `app/Support/Build/RegistryWriter.php`
- Modify: `config/build.php` (add `review` block)
- Test: `tests/Feature/Build/RegistryWriterTest.php` (extend)

**Interfaces:**
- Consumes: `config('build.review.inbox')`, `config('build.review.log')` (added here).
- Produces:
  - `appendFeedback(string $pageId, string $pageName, string $note): void` — bullet under a `## [Y-m-d] P-nn Name` heading in the inbox file (file created on first use).
  - `appendLog(string $line): void` — bullet under one `## [Y-m-d] build | review-sessie (/build/review)` heading per day, inserted right after the `# Wiki Log` title (newest-first convention).

- [ ] **Step 1: Add the `review` config block**

In `config/build.php`, after the `'sources'` array, add:

```php
    // Review mode (/build/review). Inbox = raw punch list written by the tool;
    // a later /pipeline pass folds it into Top gaps + Roll-up.
    'review' => [
        'inbox' => 'docs/wiki/design/30-skeleton/review-inbox.md',
        'log' => 'docs/wiki/log.md',
        // Static preview-URL overrides per registry ID (null = geen preview).
        // Template slugs (P-03 event, P-09/P-11 chapter) resolve dynamically
        // in BuildReview::previewUrl().
        'urls' => [
            'P-07' => '/login',
            'P-21' => null,
        ],
    ],
```

- [ ] **Step 2: Write the failing tests**

Append to `tests/Feature/Build/RegistryWriterTest.php` (inside the file, after the existing tests; the `beforeEach` gains two config lines — update it to):

```php
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
```

New tests:

```php
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

it('inserts one review-session log heading per day right after the title, bullets beneath it', function () {
    $writer = app(RegistryWriter::class);
    $writer->appendLog('**P-05 Contact**: Wire 🟠→🟢');
    $writer->appendLog('**P-01 Home**: Back 🟠→🟢');

    $log = File::get(base_path($this->logPath));
    $heading = '## ['.now()->format('Y-m-d').'] build | review-sessie (/build/review)';
    expect(substr_count($log, $heading))->toBe(1)
        ->and(strpos($log, $heading))->toBeLessThan(strpos($log, '## [2026-07-01]'))
        ->and(strpos($log, '- **P-01 Home**'))->toBeLessThan(strpos($log, '## [2026-07-01]'))
        ->and($log)->toContain('- **P-05 Contact**: Wire 🟠→🟢');
});
```

- [ ] **Step 3: Run tests to verify the new ones fail**

Run: `php artisan test --compact --filter=RegistryWriterTest`
Expected: 5 pass, 3 FAIL with `Call to undefined method ... appendFeedback()`.

- [ ] **Step 4: Implement the appends**

Add to `RegistryWriter`:

```php
    public function appendFeedback(string $pageId, string $pageName, string $note): void
    {
        $this->guardEnvironment();

        $path = base_path(config('build.review.inbox'));
        $content = File::exists($path)
            ? File::get($path)
            : "# Review-inbox\n\nRuwe feedback uit `/build/review`. Punchlist, geen wiki-prose: afgewerkte items verwijderen, daarna Top gaps + Roll-up bijwerken via `/pipeline`.\n";

        $heading = sprintf('## [%s] %s %s', now()->format('Y-m-d'), $pageId, $pageName);
        File::put($path, $this->insertUnderHeading($content, $heading, '- '.trim($note)));
    }

    public function appendLog(string $line): void
    {
        $this->guardEnvironment();

        $path = base_path(config('build.review.log'));
        $content = File::get($path);
        $heading = sprintf('## [%s] build | review-sessie (/build/review)', now()->format('Y-m-d'));

        if (! str_contains($content, $heading)) {
            // log.md is newest-first: today's session heading goes right after the title.
            $content = preg_replace('/^# Wiki Log\n/u', "# Wiki Log\n\n{$heading}\n", $content, 1);
        }

        File::put($path, $this->insertUnderHeading($content, $heading, '- '.trim($line)));
    }

    /** Appends $bullet at the end of $heading's block (before the next `## `), creating the heading at EOF if absent. */
    private function insertUnderHeading(string $content, string $heading, string $bullet): string
    {
        if (! str_contains($content, $heading)) {
            return rtrim($content)."\n\n".$heading."\n\n".$bullet."\n";
        }

        $blockStart = strpos($content, $heading) + strlen($heading);
        $nextHeading = strpos($content, "\n## ", $blockStart);

        if ($nextHeading === false) {
            return rtrim($content)."\n".$bullet."\n";
        }

        $block = rtrim(substr($content, $blockStart, $nextHeading - $blockStart));

        return substr($content, 0, $blockStart).$block."\n".$bullet."\n".substr($content, $nextHeading);
    }
```

- [ ] **Step 5: Run tests to verify they pass**

Run: `php artisan test --compact --filter=RegistryWriterTest`
Expected: PASS (8 tests).

- [ ] **Step 6: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Support/Build/RegistryWriter.php config/build.php tests/Feature/Build/RegistryWriterTest.php
git commit -m "feat(build): review-inbox + log.md appends on RegistryWriter"
```

---

### Task 3: `BuildReview` component — route, page walk, preview URL

**Files:**
- Create: `app/Livewire/BuildReview.php`
- Create: `resources/views/layouts/build.blade.php`
- Create: `resources/views/livewire/build-review.blade.php` (minimal here; fleshed out in Task 5)
- Modify: `routes/web.php` (non-prod block, after `build.dashboard`)
- Test: `tests/Feature/Build/BuildReviewTest.php`

**Interfaces:**
- Consumes: `BuildStatus::report()['pages']` (arrays with `id`, `name`, `slug`, `stages` (Stage enums keyed `ux|wireframe|assets|ui|back|ok`), `confidence`), `Stage::emoji()`, `config('build.review.urls')`.
- Produces: route `build.review` (`/build/review/{pageId?}`); Livewire props `pageId`, `stages` (array<string,string> emoji), `confidence` (string), `feedback` (string); actions `cycle(string $key)` and `save(bool $next = true)` (save implemented in Task 4 — stub here).

- [ ] **Step 1: Write the failing tests**

Create `tests/Feature/Build/BuildReviewTest.php`. It reuses the fixture idea from Task 1 — extract `fakeRegistry()` usage by duplicating a slimmer fixture here (Pest file-scoped functions don't cross files; keep each file self-contained):

```php
<?php

use App\Livewire\BuildReview;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;

function reviewRegistry(): string
{
    return <<<'MD'
| ID | Page | Slug | Type | UX | Conf | Wire | Assets | UI | Back | OK | Top gaps |
|---|---|---|---|---|---|---|---|---|---|---|---|
| P-01 | **Home** | `/` | Conv | 🟢 | 3 | 🟢 | 🟠 | 🟢 | 🟠 | 🔴 | gap a |
| P-05 | **Contact** | `/contact` | Utility | 🟢 | 2 | 🟠 | ⚪ | 🟠 | 🟠 | 🔴 | gap b |
| P-21 | **Admin** | `/admin` | Admin | ⚪ | — | ⚪ | ⚪ | ⚪ | 🟢 | ⚪ | exempt |
MD;
}

beforeEach(function () {
    $this->registryPath = 'tests/tmp/registry-'.uniqid().'.md';
    File::ensureDirectoryExists(base_path('tests/tmp'));
    File::put(base_path($this->registryPath), reviewRegistry());
    config()->set('build.sources.skeleton', $this->registryPath);
    config()->set('build.review.inbox', 'tests/tmp/review-inbox.md');
    config()->set('build.review.log', 'tests/tmp/log.md');
    File::put(base_path('tests/tmp/log.md'), "# Wiki Log\n");
});

afterEach(function () {
    File::deleteDirectory(base_path('tests/tmp'));
});

it('renders the first registry page when no id is given', function () {
    $this->get('/build/review')
        ->assertOk()
        ->assertSee('data-review-page="P-01"', false);
});

it('shows the requested page with its current stages and a same-origin preview', function () {
    $this->get('/build/review/P-05')
        ->assertOk()
        ->assertSee('data-review-page="P-05"', false)
        ->assertSee('data-preview-url="'.url('/nl/contact').'"', false);
});

it('shows a no-preview placeholder for pages without a preview url', function () {
    $this->get('/build/review/P-21')
        ->assertOk()
        ->assertSee('data-preview-missing', false);
});

it('404s on an unknown page id', function () {
    $this->get('/build/review/P-42')->assertNotFound();
});

it('cycles a stage through the emoji sequence', function () {
    Livewire::test(BuildReview::class, ['pageId' => 'P-05'])
        ->assertSet('stages.wireframe', '🟠')
        ->call('cycle', 'wireframe')
        ->assertSet('stages.wireframe', '🟢')
        ->call('cycle', 'ok')
        ->assertSet('stages.ok', '🟢')
        ->call('cycle', 'ok')
        ->assertSet('stages.ok', '🔴');
});

it('links prev and next in registry order', function () {
    $this->get('/build/review/P-05')
        ->assertSee(route('build.review', 'P-01'))
        ->assertSee(route('build.review', 'P-21'));
});
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --compact --filter=BuildReviewTest`
Expected: FAIL — route not defined / class not found.

- [ ] **Step 3: Register the route**

In `routes/web.php`, inside the existing `if (! app()->isProduction())` block, directly after the `build.dashboard` route:

```php
    // Split review mode — walk the P-nn rows, bump statuses, drop feedback.
    Route::get('/build/review/{pageId?}', BuildReview::class)
        ->name('build.review');
```

Add the import at the top: `use App\Livewire\BuildReview;`

- [ ] **Step 4: Create the layout**

Create `resources/views/layouts/build.blade.php`:

```blade
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Build review</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-neutral-100">
    {{ $slot }}
</body>
</html>
```

- [ ] **Step 5: Implement the component**

Create `app/Livewire/BuildReview.php`:

```php
<?php

namespace App\Livewire;

use App\Models\Activity;
use App\Models\Group;
use App\Support\Build\BuildStatus;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.build')]
class BuildReview extends Component
{
    private const CYCLE = ['🔴', '🟠', '🟢', '⚪', '❓'];

    private const OK_CYCLE = ['🔴', '🟢'];

    public string $pageId;

    /** @var array<string, string> stage key => emoji, editable copy */
    public array $stages = [];

    /** @var array<string, string> stage key => emoji, as read from the registry */
    public array $original = [];

    public string $confidence = '';

    public string $originalConfidence = '';

    public string $feedback = '';

    public function mount(?string $pageId = null): void
    {
        $pages = $this->pages();
        $page = $pageId === null
            ? $pages[0]
            : collect($pages)->firstWhere('id', $pageId);
        abort_unless((bool) $page, 404);

        $this->pageId = $page['id'];
        foreach ($page['stages'] as $key => $stage) {
            $this->stages[$key] = $stage->emoji();
        }
        $this->original = $this->stages;
        $this->confidence = $page['confidence'] > 0 ? (string) $page['confidence'] : '';
        $this->originalConfidence = $this->confidence;
    }

    public function cycle(string $key): void
    {
        $cycle = $key === 'ok' ? self::OK_CYCLE : self::CYCLE;
        $at = array_search($this->stages[$key], $cycle, true);
        $this->stages[$key] = $cycle[($at === false ? 0 : $at + 1) % count($cycle)];
    }

    public function save(bool $next = true): void
    {
        // Task 4.
    }

    public function render()
    {
        $pages = $this->pages();
        $index = collect($pages)->search(fn ($p) => $p['id'] === $this->pageId);
        $page = $pages[$index];

        return view('livewire.build-review', [
            'page' => $page,
            'index' => $index,
            'total' => count($pages),
            'prev' => $pages[$index - 1]['id'] ?? null,
            'next' => $pages[$index + 1]['id'] ?? null,
            'previewUrl' => $this->previewUrl($page),
            'inboxPending' => file_exists(base_path(config('build.review.inbox'))),
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    private function pages(): array
    {
        return app(BuildStatus::class)->report()['pages'];
    }

    /** Representative live URL for the row, null when nothing sensible renders. */
    private function previewUrl(array $page): ?string
    {
        $overrides = config('build.review.urls', []);
        if (array_key_exists($page['id'], $overrides)) {
            return $overrides[$page['id']];
        }

        return match ($page['id']) {
            'P-03' => ($activity = Activity::published()->where('begin_date', '>=', now())->orderBy('begin_date')->first()
                    ?? Activity::published()->orderByDesc('begin_date')->first())
                ? route('activities.show', ['locale' => 'nl', 'activity' => $activity])
                : null,
            'P-09' => ($group = Group::query()->first())
                ? route('groups.roze-hesjes', ['locale' => 'nl', 'group' => $group])
                : null,
            'P-11' => ($group = Group::query()->first())
                ? route('groups.show', ['locale' => 'nl', 'group' => $group])
                : null,
            default => str_contains($page['slug'], '[')
                ? null
                : url('/nl'.rtrim($page['slug'], '/')),
        };
    }
}
```

- [ ] **Step 6: Create the minimal view (layout comes in Task 5)**

Create `resources/views/livewire/build-review.blade.php`:

```blade
<div data-review-page="{{ $page['id'] }}" @if ($previewUrl) data-preview-url="{{ $previewUrl }}" @endif>
    <header>
        <a href="{{ route('build.dashboard') }}">← dashboard</a>
        <h1>{{ $page['id'] }} · {{ $page['name'] }} <small>({{ $index + 1 }}/{{ $total }})</small></h1>
        @if ($prev) <a href="{{ route('build.review', $prev) }}">← vorige</a> @endif
        @if ($next) <a href="{{ route('build.review', $next) }}">volgende →</a> @endif
    </header>

    @if ($previewUrl)
        <iframe src="{{ $previewUrl }}" title="{{ $page['name'] }}"></iframe>
    @else
        <p data-preview-missing>Geen live preview voor deze rij.</p>
    @endif

    <section>
        @foreach (['ux' => 'UX', 'wireframe' => 'Wire', 'assets' => 'Assets', 'ui' => 'UI', 'back' => 'Back', 'ok' => 'OK'] as $key => $label)
            <button type="button" wire:click="cycle('{{ $key }}')" data-stage="{{ $key }}">
                {{ $label }} {{ $stages[$key] }}
            </button>
        @endforeach
        <label>Conf <input type="number" min="1" max="5" wire:model="confidence" data-stage="conf"></label>
        <textarea wire:model="feedback" placeholder="Feedback voor deze pagina" data-review-feedback></textarea>
        <button type="button" wire:click="save(false)">Bewaar</button>
        <button type="button" wire:click="save(true)">Bewaar en volgende</button>
    </section>
</div>
```

- [ ] **Step 7: Run tests to verify they pass**

Run: `php artisan test --compact --filter=BuildReviewTest`
Expected: PASS (6 tests).

- [ ] **Step 8: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/BuildReview.php resources/views/layouts/build.blade.php resources/views/livewire/build-review.blade.php routes/web.php tests/Feature/Build/BuildReviewTest.php
git commit -m "feat(build): /build/review route + BuildReview page walk"
```

---

### Task 4: Save action — registry write, inbox note, log line

**Files:**
- Modify: `app/Livewire/BuildReview.php` (implement `save`)
- Test: `tests/Feature/Build/BuildReviewTest.php` (extend)

**Interfaces:**
- Consumes: `RegistryWriter::updateStages/appendFeedback/appendLog` (Tasks 1–2), `Stage` keys as in Task 3.
- Produces: `save(bool $next = true)` — writes only *changed* cells; feedback note only when non-blank; one log line per save summarising the diff; redirects to `build.review` of the next page (`$next && next exists`) or the same page otherwise.

- [ ] **Step 1: Write the failing tests**

Append to `tests/Feature/Build/BuildReviewTest.php`:

```php
it('saves changed cells to the registry, files the note, logs the diff, and moves on', function () {
    Livewire::test(BuildReview::class, ['pageId' => 'P-05'])
        ->call('cycle', 'wireframe')   // 🟠 → 🟢
        ->set('confidence', '3')
        ->set('feedback', 'hero te druk')
        ->call('save', true)
        ->assertRedirect(route('build.review', 'P-21'));

    $registry = File::get(base_path($this->registryPath));
    expect($registry)
        ->toContain('| P-05 | **Contact** | `/contact` | Utility | 🟢 | 3 | 🟢 | ⚪ | 🟠 | 🟠 | 🔴 | gap b |')
        ->toContain('| P-01 | **Home** | `/` | Conv | 🟢 | 3 | 🟢 | 🟠 | 🟢 | 🟠 | 🔴 | gap a |');

    expect(File::get(base_path('tests/tmp/review-inbox.md')))
        ->toContain('P-05 Contact')->toContain('- hero te druk');

    expect(File::get(base_path('tests/tmp/log.md')))
        ->toContain('review-sessie')->toContain('P-05')->toContain('Wire 🟠→🟢');
});

it('writes nothing to the registry when nothing changed, but still files feedback', function () {
    Livewire::test(BuildReview::class, ['pageId' => 'P-01'])
        ->set('feedback', 'alleen een notitie')
        ->call('save', false)
        ->assertRedirect(route('build.review', 'P-01'));

    expect(File::get(base_path($this->registryPath)))->toBe(reviewRegistry());
    expect(File::get(base_path('tests/tmp/review-inbox.md')))->toContain('- alleen een notitie');
});

// mount() needs a valid row, so corrupt the file only AFTER mount:
it('surfaces a writer error instead of crashing', function () {
    $component = Livewire::test(BuildReview::class, ['pageId' => 'P-05'])
        ->call('cycle', 'ui');

    File::put(base_path($this->registryPath), "| ID |\n|---|\n| P-05 | broken |\n");

    $component->call('save', true)->assertHasErrors('save');

    expect(File::exists(base_path('tests/tmp/review-inbox.md')))->toBeFalse();
});
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --compact --filter=BuildReviewTest`
Expected: the three new tests FAIL (save is a no-op stub).

- [ ] **Step 3: Implement `save`**

Replace the stub in `app/Livewire/BuildReview.php` (add `use App\Support\Build\RegistryWriter;` and `use RuntimeException;`):

```php
    public function save(bool $next = true): void
    {
        $writer = app(RegistryWriter::class);
        $labels = ['ux' => 'UX', 'conf' => 'Conf', 'wireframe' => 'Wire', 'assets' => 'Assets', 'ui' => 'UI', 'back' => 'Back', 'ok' => 'OK'];

        $changed = collect($this->stages)
            ->filter(fn ($emoji, $key) => $emoji !== $this->original[$key])
            ->all();
        if ($this->confidence !== $this->originalConfidence && in_array($this->confidence, ['1', '2', '3', '4', '5'], true)) {
            $changed['conf'] = $this->confidence;
        }

        $pages = $this->pages();
        $index = collect($pages)->search(fn ($p) => $p['id'] === $this->pageId);
        $page = $pages[$index];

        try {
            if ($changed !== []) {
                $writer->updateStages($this->pageId, $changed);
            }
            if (trim($this->feedback) !== '') {
                $writer->appendFeedback($this->pageId, $page['name'], $this->feedback);
            }
            if ($changed !== [] || trim($this->feedback) !== '') {
                $writer->appendLog($this->logLine($page, $changed, $labels));
            }
        } catch (RuntimeException $e) {
            $this->addError('save', $e->getMessage());

            return;
        }

        $target = $next ? ($pages[$index + 1]['id'] ?? $this->pageId) : $this->pageId;
        $this->redirect(route('build.review', $target));
    }

    /** @param array<string, string> $changed */
    private function logLine(array $page, array $changed, array $labels): string
    {
        $diffs = collect($changed)->map(fn ($to, $key) => $labels[$key].' '
            .($key === 'conf' ? $this->originalConfidence : $this->original[$key])
            .'→'.$to)->implode(', ');
        $parts = array_filter([
            $diffs ?: null,
            trim($this->feedback) !== '' ? 'feedbacknotitie in review-inbox' : null,
        ]);

        return sprintf('**%s %s**: %s', $page['id'], $page['name'], implode('; ', $parts));
    }
```

- [ ] **Step 4: Run tests to verify they pass**

Run: `php artisan test --compact --filter=BuildReviewTest`
Expected: PASS (9 tests). Also run `php artisan test --compact --filter=RegistryWriterTest` — still 8 passing.

- [ ] **Step 5: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add app/Livewire/BuildReview.php tests/Feature/Build/BuildReviewTest.php
git commit -m "feat(build): review save writes registry row, inbox note, log line"
```

---

### Task 5: Split layout, width toggle, reconcile hint, dashboard link

**Files:**
- Modify: `resources/views/livewire/build-review.blade.php` (full layout)
- Modify: `resources/views/build/dashboard.blade.php` (header link to review mode)
- Test: `tests/Feature/Build/BuildReviewTest.php` (extend) and `tests/Feature/BuildDashboardTest.php` (one assertion)

This is an internal tool, so Tailwind utilities may live in the template (the public-site styling layers don't apply to `/build`), but tests still only assert `data-*` seams.

- [ ] **Step 1: Write the failing tests**

Append to `tests/Feature/Build/BuildReviewTest.php`:

```php
it('shows the reconcile hint once the inbox has content', function () {
    $this->get('/build/review/P-01')->assertDontSee('data-reconcile-hint', false);

    File::put(base_path('tests/tmp/review-inbox.md'), "# Review-inbox\n\n- iets");

    $this->get('/build/review/P-01')->assertSee('data-reconcile-hint', false);
});
```

Add to `tests/Feature/BuildDashboardTest.php` (match the file's existing style):

```php
it('links to the review mode', function () {
    $this->get('/build')->assertSee(route('build.review'));
});
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --compact --filter="BuildReviewTest|BuildDashboardTest"`
Expected: the two new tests FAIL.

- [ ] **Step 3: Build the full view**

Replace `resources/views/livewire/build-review.blade.php` with the split layout. Structure (keep the existing `data-*` seams exactly as in Task 3 — `data-review-page`, `data-preview-url`, `data-preview-missing`, `data-stage`, `data-review-feedback` — tests depend on them):

```blade
<div class="flex h-screen" data-review-page="{{ $page['id'] }}" @if ($previewUrl) data-preview-url="{{ $previewUrl }}" @endif
     x-data="{ mobile: false }">

    {{-- Preview pane --}}
    <main class="flex-1 flex items-stretch justify-center bg-neutral-200 overflow-hidden">
        @if ($previewUrl)
            <div class="h-full transition-all" :class="mobile ? 'w-[390px]' : 'w-full'">
                <iframe src="{{ $previewUrl }}" title="{{ $page['name'] }}" class="w-full h-full border-0 bg-white"></iframe>
            </div>
        @else
            <p class="self-center text-neutral-500" data-preview-missing>Geen live preview voor deze rij.</p>
        @endif
    </main>

    {{-- Sidebar --}}
    <aside class="w-80 shrink-0 flex flex-col gap-4 p-4 bg-white border-l border-neutral-200 overflow-y-auto">
        <header class="flex flex-col gap-1">
            <a href="{{ route('build.dashboard') }}" class="text-xs text-neutral-500">← dashboard</a>
            <h1 class="font-bold">{{ $page['id'] }} · {{ $page['name'] }}</h1>
            <div class="flex items-center justify-between text-sm">
                <span class="text-neutral-500">{{ $index + 1 }}/{{ $total }} · <code>{{ $page['slug'] }}</code></span>
                <span class="flex gap-2">
                    @if ($prev)<a href="{{ route('build.review', $prev) }}">←</a>@endif
                    @if ($next)<a href="{{ route('build.review', $next) }}">→</a>@endif
                </span>
            </div>
            @if ($previewUrl)
                <div class="flex gap-2 text-xs">
                    <button type="button" @click="mobile = false" :class="! mobile && 'font-bold'">desktop</button>
                    <button type="button" @click="mobile = true" :class="mobile && 'font-bold'">mobiel 390px</button>
                    <a href="{{ $previewUrl }}" target="_blank" class="ml-auto">open in tab ↗</a>
                </div>
            @endif
        </header>

        <section class="grid grid-cols-2 gap-2">
            @foreach (['ux' => 'UX', 'wireframe' => 'Wire', 'assets' => 'Assets', 'ui' => 'UI', 'back' => 'Back', 'ok' => 'OK'] as $key => $label)
                <button type="button" wire:click="cycle('{{ $key }}')" data-stage="{{ $key }}"
                        class="flex items-center justify-between rounded border border-neutral-200 px-3 py-2 text-sm hover:bg-neutral-50">
                    <span>{{ $label }}</span><span>{{ $stages[$key] }}</span>
                </button>
            @endforeach
            <label class="col-span-2 flex items-center justify-between rounded border border-neutral-200 px-3 py-2 text-sm">
                Conf (1–5)
                <input type="number" min="1" max="5" wire:model="confidence" data-stage="conf" class="w-14 text-right">
            </label>
        </section>

        <section class="flex flex-col gap-2">
            <textarea wire:model="feedback" rows="6" data-review-feedback
                      class="rounded border border-neutral-200 p-2 text-sm"
                      placeholder="Feedback voor deze pagina (komt in review-inbox.md)"></textarea>
            @error('save')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            <div class="flex gap-2">
                <button type="button" wire:click="save(false)" class="rounded border border-neutral-300 px-3 py-2 text-sm">Bewaar</button>
                <button type="button" wire:click="save(true)" class="flex-1 rounded bg-neutral-900 px-3 py-2 text-sm text-white">Bewaar en volgende</button>
            </div>
        </section>

        @if ($inboxPending)
            <p class="mt-auto rounded bg-amber-50 p-2 text-xs text-amber-800" data-reconcile-hint>
                Er staat feedback in <code>review-inbox.md</code>. Top gaps en Roll-up lopen achter: verwerk na de sessie via <code>/pipeline</code>.
            </p>
        @endif
    </aside>
</div>
```

- [ ] **Step 4: Link the dashboard to review mode**

In `resources/views/build/dashboard.blade.php`, find the page header (the element containing the dashboard title) and add next to it:

```blade
<a href="{{ route('build.review') }}">Review-modus →</a>
```

Match the surrounding markup/classes of whatever nav or header links already exist there; read the file first and place it where the source-freshness links live.

- [ ] **Step 5: Run the tests**

Run: `php artisan test --compact --filter="BuildReviewTest|BuildDashboardTest|RegistryWriterTest"`
Expected: all PASS.

- [ ] **Step 6: Verify by hand (the tool itself)**

Load `https://kidicalmass.test/build/review` in a browser (or `curl -sk https://kidicalmass.test/build/review | grep data-review-page`). Walk two pages, bump a throwaway stage, type a note, save, then `git diff docs/wiki` to see exactly the row change + inbox + log — then `git checkout -- docs/wiki` to discard the smoke-test writes.

- [ ] **Step 7: Pint + commit**

```bash
vendor/bin/pint --dirty --format agent
git add resources/views/livewire/build-review.blade.php resources/views/build/dashboard.blade.php tests/Feature/Build/BuildReviewTest.php tests/Feature/BuildDashboardTest.php
git commit -m "feat(build): review split layout, width toggle, reconcile hint"
```

---

## Self-review checklist (done at plan time)

- Spec coverage: iframe + toggle (T5), sidebar all stages + conf + ok binary (T3/T5), prev/next registry order (T3), representative URLs + no-preview (T3 + config in T2), registry write with guard (T1), inbox (T2), log (T2), reconcile hint (T5), non-prod route + env guard (T1/T3), dashboard link (T5).
- No placeholders; all code shown.
- Key/type consistency: stage keys `ux|conf|wireframe|assets|ui|back|ok` shared by `RegistryWriter::COLUMNS`, `BuildReview::$stages`, and views.

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

it('refuses a feedback-only save when the row has vanished from the registry', function () {
    $component = Livewire::test(BuildReview::class, ['pageId' => 'P-05'])
        ->set('feedback', 'notitie zonder statuswijziging');

    File::put(base_path($this->registryPath), "| ID |\n|---|\n| P-05 | broken |\n");

    $component->call('save', true)->assertHasErrors('save');

    expect(File::exists(base_path('tests/tmp/review-inbox.md')))->toBeFalse();
});

it('shows a missing-row warning instead of a phantom page when the row vanishes mid-session', function () {
    $component = Livewire::test(BuildReview::class, ['pageId' => 'P-05']);

    File::put(base_path($this->registryPath), "| ID |\n|---|\n| P-05 | broken |\n");

    $component->call('cycle', 'ui')->assertSee('data-review-missing-row', false);
});

it('surfaces the writer exception when the row parses but fails the stricter write guard', function () {
    $component = Livewire::test(BuildReview::class, ['pageId' => 'P-05'])
        ->call('cycle', 'ui');

    // 13 columns: survives BuildStatus::parsePages (>=12 cells) but explode('|')
    // yields 15 parts, so RegistryWriter::updateStages refuses inside save()'s try/catch.
    File::put(base_path($this->registryPath), <<<'MD'
| ID | Page | Slug | Type | UX | Conf | Wire | Assets | UI | Back | OK | Top gaps | Extra |
|---|---|---|---|---|---|---|---|---|---|---|---|---|
| P-05 | **Contact** | `/contact` | Utility | 🟢 | 2 | 🟠 | ⚪ | 🟠 | 🟠 | 🔴 | gap b | extra |
MD);

    $component->call('save', true)->assertHasErrors([
        'save' => fn ($rules, $messages) => collect($messages)->contains(fn ($m) => str_contains($m, 'kolomstructuur')),
    ]);

    expect(File::exists(base_path('tests/tmp/review-inbox.md')))->toBeFalse()
        ->and(File::get(base_path('tests/tmp/log.md')))->not->toContain('P-05');
});

it('shows the reconcile hint once the inbox has content', function () {
    $this->get('/build/review/P-01')->assertDontSee('data-reconcile-hint', false);

    File::put(base_path('tests/tmp/review-inbox.md'), "# Review-inbox\n\n- iets");

    $this->get('/build/review/P-01')->assertSee('data-reconcile-hint', false);
});

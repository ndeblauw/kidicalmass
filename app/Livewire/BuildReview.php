<?php

namespace App\Livewire;

use App\Models\Activity;
use App\Models\Group;
use App\Support\Build\BuildStatus;
use App\Support\Build\RegistryWriter;
use Livewire\Attributes\Layout;
use Livewire\Component;
use RuntimeException;

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
        $page = $this->currentPage($pages, $index);

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

    public function render()
    {
        $pages = $this->pages();
        $index = collect($pages)->search(fn ($p) => $p['id'] === $this->pageId);
        $page = $this->currentPage($pages, $index);

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

    /**
     * A malformed row can vanish from parsePages() entirely (BuildStatus drops
     * rows with an unexpected column count) mid-session, after a writer error
     * corrupts the registry the request just read. Fall back to a minimal
     * stand-in so lookups never crash the page, letting the writer's own
     * RuntimeException be what surfaces to the user instead.
     *
     * @param  array<int, array<string, mixed>>  $pages
     * @return array<string, mixed>
     */
    private function currentPage(array $pages, int|false $index): array
    {
        return $index !== false
            ? $pages[$index]
            : ['id' => $this->pageId, 'name' => $this->pageId, 'slug' => ''];
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

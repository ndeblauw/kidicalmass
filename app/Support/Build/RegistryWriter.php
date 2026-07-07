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
            $trimmed = rtrim($content);
            $separator = str_ends_with($trimmed, $heading) ? "\n\n" : "\n";

            return $trimmed.$separator.$bullet."\n";
        }

        $block = rtrim(substr($content, $blockStart, $nextHeading - $blockStart));
        $separator = $block === '' ? "\n\n" : "\n";

        return substr($content, 0, $blockStart).$block.$separator.$bullet."\n".substr($content, $nextHeading);
    }

    private function guardEnvironment(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('RegistryWriter schrijft nooit in productie.');
        }
    }
}

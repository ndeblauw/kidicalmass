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

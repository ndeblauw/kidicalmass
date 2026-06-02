<?php

namespace App\Support\Build;

use Illuminate\Support\Facades\File;

class WikiParser
{
    public function read(string $relativePath): string
    {
        $full = base_path($relativePath);

        return File::exists($full) ? File::get($full) : '';
    }

    /**
     * Every markdown table row whose first cell matches $idRegex (after stripping
     * bold `**` and backticks, so `**PAT-1**` / `` `D-1` `` both match).
     * Returns rows as arrays of trimmed cell strings.
     *
     * @return array<int, array<int, string>>
     */
    public function extractRows(string $markdown, string $idRegex): array
    {
        $rows = [];

        foreach (preg_split('/\R/u', $markdown) as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] !== '|') {
                continue;
            }
            $cells = array_map('trim', explode('|', trim($line, '|')));
            $first = trim($cells[0] ?? '', '* `');
            if (preg_match($idRegex, $first)) {
                $rows[] = $cells;
            }
        }

        return $rows;
    }

    /**
     * Every markdown heading matching $headingRegex, returned as [id, title].
     * Used for heading-based registers (kidicalmass concerns live as
     * `### \`D-1\` — title …` headings, not table rows).
     *
     * @return array<int, array{id:string, title:string}>
     */
    public function extractHeadings(string $markdown, string $headingRegex): array
    {
        $out = [];

        foreach (preg_split('/\R/u', $markdown) as $line) {
            if (preg_match($headingRegex, trim($line), $m)) {
                $out[] = ['id' => $m['id'], 'title' => trim($m['title'] ?? '')];
            }
        }

        return $out;
    }

    /** First fenced ``` block after a heading line containing $afterHeading. */
    public function extractFencedBlock(string $markdown, string $afterHeading): ?string
    {
        $past = false;
        $inFence = false;
        $buf = [];

        foreach (preg_split('/\R/u', $markdown) as $line) {
            $trim = trim($line);
            if (! $past) {
                if (str_starts_with($trim, '#') && str_contains($line, $afterHeading)) {
                    $past = true;
                }

                continue;
            }
            if (! $inFence && str_starts_with($trim, '```')) {
                $inFence = true;

                continue;
            }
            if ($inFence && str_starts_with($trim, '```')) {
                return implode("\n", $buf);
            }
            if ($inFence) {
                $buf[] = $line;
            }
        }

        return null;
    }
}

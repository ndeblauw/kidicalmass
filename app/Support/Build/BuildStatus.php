<?php

namespace App\Support\Build;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BuildStatus
{
    public function __construct(private WikiParser $parser) {}

    public function report(): array
    {
        $cfg = config('build');
        $warnings = [];

        $skeleton = $this->parser->read($cfg['sources']['skeleton']);
        $concernsMd = $this->parser->read($cfg['sources']['concerns']);
        $patternsMd = $this->parser->read($cfg['sources']['patterns']);

        $pages = $this->parsePages($skeleton, $cfg, $warnings);
        $patterns = $this->parsePatterns($patternsMd, $warnings);
        $concerns = $this->parseConcerns($concernsMd, $warnings);

        $checker = new DriftChecker($cfg['stub_line_threshold'], $cfg['stub_markers']);
        $driftInput = array_map(fn ($p) => [
            'id' => $p['id'], 'slug' => $p['slug'], 'stages' => $p['stages'],
            'briefPath' => $p['briefPath'], 'viewPath' => $p['viewPath'], 'routeUri' => $p['routeUri'],
        ], $pages);
        $driftPatterns = array_map(fn ($p) => ['id' => $p['id'], 'partialPath' => $p['partialPath']], $patterns);
        $drift = $checker->check($driftInput, $driftPatterns);

        $byId = [];
        foreach ($drift as $d) {
            $byId[$d['id']][] = $d['message'];
        }
        foreach ($pages as &$p) {
            $p['drift'] = $byId[$p['id']] ?? [];
        }
        unset($p);
        foreach ($patterns as &$pat) {
            $pat['drift'] = $byId[$pat['id']] ?? [];
        }
        unset($pat);

        Carbon::setLocale('nl');

        return [
            'generatedAt' => Carbon::now()->format('d-m-Y H:i'),
            'warnings' => $warnings,
            'overview' => $this->overview($pages, $patterns, $concerns),
            'drift' => $drift,
            'pages' => $pages,
            'patterns' => $patterns,
            'concerns' => $concerns,
            'idMap' => $this->buildIdMap($pages, $patterns, $concerns),
            'sources' => $this->freshness($cfg),
        ];
    }

    /**
     * Last-modified date + relative age per parsed wiki source. Informational —
     * lets the reader see at a glance which source is the oldest (most drift-prone).
     */
    private function freshness(array $cfg): array
    {
        $out = [];
        foreach (['skeleton', 'concerns', 'patterns'] as $key) {
            $full = base_path($cfg['sources'][$key]);
            if (! file_exists($full)) {
                continue;
            }
            $when = Carbon::createFromTimestamp(filemtime($full));
            $out[$key] = [
                'file' => basename($cfg['sources'][$key]),
                'date' => $when->translatedFormat('j M Y'),
                'ago' => $when->diffForHumans(),
            ];
        }

        return $out;
    }

    private function parsePages(string $md, array $cfg, array &$warnings): array
    {
        $rows = $this->parser->extractRows($md, '/^P-\d+$/');
        if ($rows === []) {
            $warnings[] = 'Geen page-registry rijen gevonden in 30-skeleton/00-page-registry.md.';
        }
        $pages = [];
        foreach ($rows as $c) {
            if (count($c) < 13) {
                continue;
            }
            // Columns: 0 ID · 1 Page · 2 Slug · 3 Type · 4 UX · 5 Conf · 6 Wire
            //          · 7 Assets · 8 UI · 9 Back · 10 CMS · 11 OK · 12 Top gaps
            $id = $c[0];
            $slug = trim($c[2], '` ');
            $built = $cfg['pages'][$slug] ?? null;
            $pages[] = [
                'id' => $id,
                'name' => trim(str_replace('**', '', $c[1])),
                'slug' => $slug,
                'type' => $c[3],
                'stages' => [
                    'ux' => Stage::fromEmoji($c[4]),
                    'wireframe' => Stage::fromEmoji($c[6]),
                    'assets' => Stage::fromEmoji($c[7]),
                    'ui' => Stage::fromEmoji($c[8]),
                    'back' => Stage::fromEmoji($c[9]),
                    'cms' => Stage::fromEmoji($c[10]),
                    'ok' => Stage::fromEmoji($c[11]),
                ],
                'confidence' => (int) (Str::match('/\d/', $c[5]) ?: 0),
                'gaps' => $c[12],
                'briefPath' => $cfg['briefs'][$id] ?? null,
                'viewPath' => $built['view'] ?? null,
                'routeUri' => $built['route'] ?? null,
                'drift' => [],
            ];
        }

        return $pages;
    }

    private function parsePatterns(string $md, array &$warnings): array
    {
        $rows = $this->parser->extractRows($md, '/^PAT-\d+$/');
        if ($rows === []) {
            $warnings[] = 'Geen pattern-rijen gevonden in 40-patterns.md.';
        }
        $patterns = [];
        foreach ($rows as $c) {
            if (count($c) < 5) {
                continue;
            }
            // Columns: 0 ID · 1 Pattern · 2 What it is · 3 Used on · 4 Notes / source
            $notes = $c[4];
            $patterns[] = [
                'id' => trim($c[0], '* `'),
                'name' => trim(str_replace('**', '', $c[1])),
                'what' => $c[2],
                'usedOn' => $c[3],
                'notes' => $notes,
                'partialPath' => $this->partialPath($notes),
                'drift' => [],
            ];
        }

        return $patterns;
    }

    private function partialPath(string $notes): ?string
    {
        if (preg_match('/`?(resources\/views\/[a-z0-9\-\/]+\.blade\.php)`?/', $notes, $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * Concerns live as `### \`D-n\` — title …` headings; their state lives in the
     * "At a glance" summary table (State → IDs). Combine the two.
     */
    private function parseConcerns(string $md, array &$warnings): array
    {
        $statusOf = $this->concernStatusMap($md);
        $headings = $this->parser->extractHeadings($md, '/^#{2,4}\s*`?(?<id>D-\d+)`?\s*[—\-–]\s*(?<title>.+)$/u');

        $titles = [];
        foreach ($headings as $h) {
            $titles[$h['id']] = self::summarize($h['title'], 90);
        }

        $ids = array_unique(array_merge(array_keys($statusOf), array_keys($titles)));
        if ($ids === []) {
            $warnings[] = 'Geen concern-IDs gevonden in 01-concerns.md.';
        }

        $concerns = [];
        foreach ($ids as $id) {
            $concerns[] = [
                'id' => $id,
                'title' => $titles[$id] ?? $id,
                'status' => $statusOf[$id] ?? 'open',
            ];
        }

        usort($concerns, fn ($a, $b) => strnatcmp($a['id'], $b['id']));

        return $concerns;
    }

    /**
     * Reads the "At a glance" table (rows: State | Count | IDs) into id → status.
     *
     * @return array<string,string>
     */
    private function concernStatusMap(string $md): array
    {
        $map = [];
        $rows = $this->parser->extractRows($md, '/^(Open|Partly|Closed)$/i');
        foreach ($rows as $c) {
            $status = strtolower(trim($c[0]));
            $idsCell = $c[2] ?? '';
            if (preg_match_all('/D-\d+/', $idsCell, $m)) {
                foreach ($m[0] as $id) {
                    $map[$id] = $status;
                }
            }
        }

        return $map;
    }

    private function buildIdMap(array $pages, array $patterns, array $concerns): array
    {
        $map = [];
        foreach ($pages as $p) {
            $map[$p['id']] = "{$p['name']} ({$p['slug']})";
        }
        foreach ($patterns as $p) {
            $map[$p['id']] = $p['name'];
        }
        foreach ($concerns as $c) {
            $map[$c['id']] = Str::limit($c['title'], 80);
        }

        return $map;
    }

    private function overview(array $pages, array $patterns, array $concerns): array
    {
        $confs = array_filter(array_column($pages, 'confidence'));
        $atDraft = collect($pages)->filter(fn ($p) => $p['stages']['ux'] !== Stage::NotStarted)->count();
        $byStatus = collect($concerns)->countBy('status');

        return [
            'pagesAtDraft' => $atDraft,
            'pagesTotal' => count($pages),
            'avgConfidence' => $confs ? round(array_sum($confs) / count($confs), 1) : 0,
            'patternsTotal' => count($patterns),
            'concernsOpen' => $byStatus['open'] ?? 0,
            'concernsPartly' => $byStatus['partly'] ?? 0,
            'concernsClosed' => $byStatus['closed'] ?? 0,
        ];
    }

    public static function linkify(string $text, array $idMap): string
    {
        $escaped = htmlspecialchars($text, ENT_QUOTES);

        return preg_replace_callback('/\b(PAT-\d+|P-\d+|D-\d+)\b/', function ($m) use ($idMap) {
            $id = $m[1];
            $title = htmlspecialchars($idMap[$id] ?? $id, ENT_QUOTES);

            return "<a href=\"#{$id}\" class=\"tok\"><abbr title=\"{$title}\">{$id}</abbr></a>";
        }, $escaped);
    }

    /** Strip markdown noise (links→label, bold, code, stray brackets) to plain prose. */
    public static function plainify(string $md): string
    {
        $s = preg_replace('/\[([^\]]+)\]\([^)]*\)/', '$1', $md); // [label](url) → label
        $s = str_replace(['**', '*', '`', '[', ']'], '', $s);    // bold · italic · code · stray refs
        $s = preg_replace('/\s+/', ' ', $s);

        return trim($s);
    }

    /** Plain-text lead for a register row: first sentence, else truncated to $max. */
    public static function summarize(string $md, int $max = 150): string
    {
        $t = self::plainify($md);
        $first = Str::before($t, '. ');
        if ($first !== $t && mb_strlen($first) >= 16 && mb_strlen($first) <= $max) {
            return $first.'.';
        }

        return Str::limit($t, $max);
    }
}

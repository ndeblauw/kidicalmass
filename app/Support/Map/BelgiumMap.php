<?php

namespace App\Support\Map;

use App\Enums\Region;
use App\Support\ImpactFigures;
use InvalidArgumentException;

/**
 * Pure geometry for the static map of Belgium: projects lon/lat into SVG
 * viewBox units, draws the country outline, turns local groups into dots (and
 * all Brussels-Capital groups into one counted bubble) and places their labels.
 * No database, no framework state: everything it needs comes in as arguments,
 * so it is unit-testable and its output is plain arrays.
 *
 * Projection: equirectangular, with longitude scaled by cos(50.5°) (Belgium's
 * mid-latitude) so distances look right, fitted to a fixed-width viewBox. The
 * same {@see project()} places the outline and every marker, so they can't
 * drift apart. A marker that projects outside the viewBox (a bad centroid row,
 * swapped lat/lng) is skipped rather than painted over the page.
 *
 * Label placement is a deterministic greedy pass. The Brussels bubble's label
 * goes first and gets extra candidate spots (diagonals, then one step farther
 * out), so a ring of neighbouring groups can push it aside but not drop it.
 * Dot labels follow by name and try right, left, above, below. A spot is taken
 * when it stays inside the viewBox and, grown by {@see LABEL_PADDING} on every
 * side, clears every dot, the bubble and the labels already placed. A label
 * that fits nowhere is dropped (the dot stays). Text boxes use the font's
 * ascent/descent and a width estimated from the character count, calibrated on
 * the rendered Nunito Sans semibold labels.
 *
 * @phpstan-type MapLabel array{x: float, y: float, anchor: string}
 * @phpstan-type MapDot array{name: string, region: ?string, colorToken: string, x: float, y: float, r: float, label: ?MapLabel}
 * @phpstan-type MapBubble array{name: string, count: int, colorToken: string, x: float, y: float, r: float, label: ?MapLabel}
 * @phpstan-type BelgiumMapData array{width: int, height: int, fontSize: int, outline: string, dots: list<MapDot>, bubble: ?MapBubble, counts: array<string, int>}
 */
final class BelgiumMap
{
    /** viewBox width in SVG units; the height follows from the outline's aspect ratio. */
    public const WIDTH = 600;

    public const PADDING = 10;

    public const DOT_RADIUS = 5;

    public const FONT_SIZE = 16;

    /** Centre of the Brussels-Capital Region, where the Brussels bubble sits. */
    public const BRUSSELS_CENTRE = ['lat' => 50.8427, 'lng' => 4.3667];

    private const REFERENCE_LATITUDE = 50.5;

    /** Average glyph advance as a fraction of the font size (place names, Nunito Sans semibold). */
    private const CHAR_WIDTH = 0.58;

    /** Glyph extent above and below the baseline, as a fraction of the font size. */
    private const ASCENT = 0.8;

    private const DESCENT = 0.25;

    /** Space between a marker's edge and its own label. */
    private const LABEL_GAP = 5;

    /** Clear space kept around every label box when testing for collisions (0.3em). */
    private const LABEL_PADDING = 0.3 * self::FONT_SIZE;

    private readonly float $lngFactor;

    private readonly float $minLng;

    private readonly float $maxLat;

    private readonly float $scale;

    private readonly int $height;

    /**
     * @param  list<array{0: float, 1: float}>  $outline  closed-or-open ring of [lng, lat] pairs
     */
    public function __construct(private readonly array $outline)
    {
        if (count($outline) < 3) {
            throw new InvalidArgumentException('The outline needs at least three points.');
        }

        $lngs = array_column($outline, 0);
        $lats = array_column($outline, 1);

        $this->lngFactor = cos(deg2rad(self::REFERENCE_LATITUDE));
        $this->minLng = min($lngs);
        $this->maxLat = max($lats);
        $this->scale = (self::WIDTH - 2 * self::PADDING) / ((max($lngs) - $this->minLng) * $this->lngFactor);
        $this->height = (int) ceil((max($lats) - min($lats)) * $this->scale + 2 * self::PADDING);
    }

    /** Load the outline shipped in database/data (see its "source"/"license" keys). */
    public static function fromFile(string $path): self
    {
        $data = json_decode((string) file_get_contents($path), true, flags: JSON_THROW_ON_ERROR);

        return new self($data['coordinates']);
    }

    public function height(): int
    {
        return $this->height;
    }

    /**
     * @return array{x: float, y: float} viewBox units, rounded to 0.1
     */
    public function project(float $lng, float $lat): array
    {
        return [
            'x' => round(self::PADDING + ($lng - $this->minLng) * $this->lngFactor * $this->scale, 1),
            'y' => round(self::PADDING + ($this->maxLat - $lat) * $this->scale, 1),
        ];
    }

    /** SVG path data for the country outline ("M x y L x y … Z"). */
    public function outlinePath(): string
    {
        $points = array_map(function (array $point): string {
            ['x' => $x, 'y' => $y] = $this->project($point[0], $point[1]);

            return $this->number($x).' '.$this->number($y);
        }, $this->outline);

        return 'M'.implode('L', $points).'Z';
    }

    /**
     * Compose the full map. Brussels-Capital groups collapse into one bubble
     * (no coordinates needed); every other group with coordinates inside the
     * viewBox becomes a dot.
     *
     * `counts` describes the drawing, which is what the map's accessible name
     * reads out: the bubble's count under "brussels", then the dots actually
     * drawn per region slug ("other" for a group without a region). A group
     * without a usable centroid is not on the map, so it is not in `counts`
     * either; the Home stat row ({@see ImpactFigures}) is where
     * every visible group is counted.
     *
     * @param  list<array{name: string, region: ?Region, lat: ?float, lng: ?float}>  $groups
     * @return BelgiumMapData
     */
    public function compose(array $groups, string $brusselsLabel): array
    {
        $brusselsCount = count(array_filter($groups, fn (array $group): bool => $group['region'] === Region::BRUSSELS));
        $bubble = $brusselsCount > 0 ? $this->bubble($brusselsCount, $brusselsLabel) : null;

        $dots = collect($groups)
            ->filter(fn (array $group): bool => $group['region'] !== Region::BRUSSELS
                && $group['lat'] !== null && $group['lng'] !== null)
            ->map(fn (array $group): array => [
                'name' => $group['name'],
                'region' => $group['region']?->slug(),
                'colorToken' => Region::colorTokenFor($group['region']),
                ...$this->project($group['lng'], $group['lat']),
                'r' => (float) self::DOT_RADIUS,
                'label' => null,
            ])
            ->filter(fn (array $dot): bool => $this->insideViewBox($this->circleBox($dot)))
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->all();

        $counts = $brusselsCount > 0 ? [Region::BRUSSELS->slug() => $brusselsCount] : [];
        foreach ($dots as $dot) {
            $key = $dot['region'] ?? 'other';
            $counts[$key] = ($counts[$key] ?? 0) + 1;
        }

        $this->placeLabels($dots, $bubble);

        return [
            'width' => self::WIDTH,
            'height' => $this->height,
            'fontSize' => self::FONT_SIZE,
            'outline' => $this->outlinePath(),
            'dots' => $dots,
            'bubble' => $bubble,
            'counts' => $counts,
        ];
    }

    /**
     * @return MapBubble
     */
    private function bubble(int $count, string $label): array
    {
        return [
            'name' => $label,
            'count' => $count,
            'colorToken' => Region::BRUSSELS->colorToken(),
            ...$this->project(self::BRUSSELS_CENTRE['lng'], self::BRUSSELS_CENTRE['lat']),
            // Grows gently with the count so 3 and 30 groups still read differently.
            'r' => round(self::DOT_RADIUS * 2 + 2 * sqrt($count), 1),
            'label' => null,
        ];
    }

    /**
     * Greedy, deterministic label placement (see class docblock). Mutates the
     * `label` key of the bubble and each dot.
     *
     * @param  list<array<string, mixed>>  $dots
     * @param  array<string, mixed>|null  $bubble
     */
    private function placeLabels(array &$dots, ?array &$bubble): void
    {
        /** @var list<array{0: float, 1: float, 2: float, 3: float}> $obstacles [x1, y1, x2, y2] */
        $obstacles = array_map(fn (array $dot): array => $this->circleBox($dot), $dots);
        if ($bubble !== null) {
            $obstacles[] = $this->circleBox($bubble);
            $bubble['label'] = $this->placeLabel($bubble, $obstacles, extended: true);
        }

        foreach ($dots as $index => $dot) {
            $dots[$index]['label'] = $this->placeLabel($dot, $obstacles);
        }
    }

    /**
     * @param  array<string, mixed>  $marker
     * @param  list<array{0: float, 1: float, 2: float, 3: float}>  $obstacles  placed label boxes are appended
     * @param  bool  $extended  also try the diagonals and a ring one step farther out (the bubble's label)
     * @return array{x: float, y: float, anchor: string}|null
     */
    private function placeLabel(array $marker, array &$obstacles, bool $extended = false): ?array
    {
        $fontSize = (float) self::FONT_SIZE;
        $width = mb_strlen($marker['name']) * $fontSize * self::CHAR_WIDTH;
        ['x' => $cx, 'y' => $cy] = $marker;

        $candidates = [];
        foreach ($extended ? [0.0, 1.0] : [0.0] as $ring) {
            $offset = $marker['r'] + self::LABEL_GAP + $ring * $fontSize;
            $diagonal = $offset * M_SQRT1_2;
            // [text x, text y (baseline), text-anchor]
            $candidates[] = [$cx + $offset, $cy + 0.35 * $fontSize, 'start'];
            $candidates[] = [$cx - $offset, $cy + 0.35 * $fontSize, 'end'];
            $candidates[] = [$cx, $cy - $offset - self::DESCENT * $fontSize, 'middle'];
            $candidates[] = [$cx, $cy + $offset + self::ASCENT * $fontSize, 'middle'];
            if ($extended) {
                $candidates[] = [$cx + $diagonal, $cy - $diagonal - self::DESCENT * $fontSize, 'start'];
                $candidates[] = [$cx - $diagonal, $cy - $diagonal - self::DESCENT * $fontSize, 'end'];
                $candidates[] = [$cx + $diagonal, $cy + $diagonal + self::ASCENT * $fontSize, 'start'];
                $candidates[] = [$cx - $diagonal, $cy + $diagonal + self::ASCENT * $fontSize, 'end'];
            }
        }

        foreach ($candidates as [$textX, $baseline, $anchor]) {
            $x1 = match ($anchor) {
                'start' => $textX,
                'end' => $textX - $width,
                default => $textX - $width / 2,
            };
            $box = [$x1, $baseline - self::ASCENT * $fontSize, $x1 + $width, $baseline + self::DESCENT * $fontSize];

            if ($this->insideViewBox($box) && ! $this->collides($this->grow($box, self::LABEL_PADDING), $obstacles)) {
                $obstacles[] = $box;

                return ['x' => round($textX, 1), 'y' => round($baseline, 1), 'anchor' => $anchor];
            }
        }

        return null;
    }

    /**
     * @param  array{0: float, 1: float, 2: float, 3: float}  $box
     * @return array{0: float, 1: float, 2: float, 3: float}
     */
    private function grow(array $box, float $by): array
    {
        return [$box[0] - $by, $box[1] - $by, $box[2] + $by, $box[3] + $by];
    }

    /**
     * @param  array<string, mixed>  $marker
     * @return array{0: float, 1: float, 2: float, 3: float}
     */
    private function circleBox(array $marker): array
    {
        return [$marker['x'] - $marker['r'], $marker['y'] - $marker['r'], $marker['x'] + $marker['r'], $marker['y'] + $marker['r']];
    }

    /** @param array{0: float, 1: float, 2: float, 3: float} $box */
    private function insideViewBox(array $box): bool
    {
        return $box[0] >= 0 && $box[1] >= 0 && $box[2] <= self::WIDTH && $box[3] <= $this->height;
    }

    /**
     * @param  array{0: float, 1: float, 2: float, 3: float}  $box
     * @param  list<array{0: float, 1: float, 2: float, 3: float}>  $obstacles
     */
    private function collides(array $box, array $obstacles): bool
    {
        foreach ($obstacles as $other) {
            if ($box[0] < $other[2] && $box[2] > $other[0] && $box[1] < $other[3] && $box[3] > $other[1]) {
                return true;
            }
        }

        return false;
    }

    /** "12.0" -> "12", "12.5" -> "12.5": the shortest faithful form for path data. */
    private function number(float $value): string
    {
        return rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.');
    }
}

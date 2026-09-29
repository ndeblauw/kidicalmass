<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Unguarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * One row per Belgian postcode: a coordinate, the main municipality in Dutch and
 * French, and `search_names`, a normalised `|name|name|` list of every locality
 * and municipality name that should find it. Data and sources: database/data.
 */
#[Unguarded]
class PostalCode extends Model
{
    /** Escape character for LIKE patterns; not special in MySQL or SQLite strings. */
    private const LIKE_ESCAPE = '!';

    protected static function booted(): void
    {
        static::saving(function (self $postalCode): void {
            if (blank($postalCode->search_names)) {
                $postalCode->search_names = static::searchNamesFor([$postalCode->name, $postalCode->name_nl, $postalCode->name_fr]);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    /**
     * Centroid rows for a set of zips in one query, keyed by zip. A zip with
     * several localities (4000 Liège) resolves to one row, so every map that
     * places groups (Lokale groepen, <x-belgium-map>) lands them on the same spot.
     *
     * @param  iterable<int, string|null>  $zips
     * @return Collection<string, self>
     */
    public static function centroidsByZip(iterable $zips): Collection
    {
        $zips = collect($zips)->filter()->unique()->values();

        if ($zips->isEmpty()) {
            return collect();
        }

        return static::whereIn('zip', $zips)->orderBy('id')->get()->keyBy('zip');
    }

    /**
     * @return array{lat: float, lng: float}|null
     */
    public static function coordinatesFor(string $zip): ?array
    {
        $row = static::where('zip', $zip)->first();

        if (! $row) {
            return null;
        }

        return ['lat' => $row->latitude, 'lng' => $row->longitude];
    }

    public static function nearestTo(float $lat, float $lng): ?self
    {
        return static::all()
            ->sortBy(fn (self $pc): float => ($pc->latitude - $lat) ** 2 + ($pc->longitude - $lng) ** 2)
            ->first();
    }

    /**
     * The postcode's main municipality in the current locale (Brussel/Bruxelles,
     * Luik/Liège), falling back to the stored place name.
     */
    public function localizedName(): string
    {
        $name = app()->getLocale() === 'fr' ? $this->name_fr : $this->name_nl;

        return filled($name) ? $name : $this->name;
    }

    /**
     * Postcodes whose zip starts with the term or that have a locality or
     * municipality name (in any language) starting with it, ignoring case and
     * accents. Postcodes whose main municipality matches come first.
     *
     * @return Collection<int, self>
     */
    public static function search(string $term, int $limit = 8): Collection
    {
        $needle = static::normalizeName($term);

        if ($needle === '') {
            return new Collection;
        }

        $escaped = str_replace(
            [self::LIKE_ESCAPE, '%', '_'],
            [self::LIKE_ESCAPE.self::LIKE_ESCAPE, self::LIKE_ESCAPE.'%', self::LIKE_ESCAPE.'_'],
            $needle,
        );
        $escapeClause = "escape '".self::LIKE_ESCAPE."'";

        $municipalityMatches = fn (self $postalCode): bool => collect([$postalCode->name_nl, $postalCode->name_fr, $postalCode->name])
            ->filter()
            ->contains(fn (string $name): bool => str_starts_with(static::normalizeName($name), $needle));

        return static::query()
            ->whereRaw("zip like ? {$escapeClause}", [$escaped.'%'])
            ->orWhereRaw("search_names like ? {$escapeClause}", ['%|'.$escaped.'%'])
            ->orderBy('zip')
            ->limit(50)
            ->get()
            ->sortBy(fn (self $postalCode): array => [$municipalityMatches($postalCode) ? 0 : 1, $postalCode->zip])
            ->take($limit)
            ->values();
    }

    /**
     * @param  iterable<int, string|null>  $names
     */
    public static function searchNamesFor(iterable $names): string
    {
        $normalized = collect($names)
            ->filter()
            ->map(fn (string $name): string => static::normalizeName($name))
            ->filter()
            ->unique();

        return '|'.$normalized->implode('|').'|';
    }

    /**
     * Lowercase ASCII form used on both sides of a search, so "Liège", "LIEGE" and
     * "liege" are the same name.
     */
    public static function normalizeName(string $name): string
    {
        return Str::lower(trim(Str::ascii($name)));
    }
}

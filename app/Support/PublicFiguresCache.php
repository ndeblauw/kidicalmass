<?php

namespace App\Support;

use App\Http\Middleware\SetLocale;
use App\Models\Activity;
use App\Models\Group;
use App\Models\PostalCode;
use App\Models\YearStat;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Caches the per-locale public figures Home draws from the database on every
 * hit (the movement stat row and the Belgium map), so a warm Home costs one
 * cache read per block instead of a handful of aggregate queries.
 *
 * Freshness: every Eloquent save or delete of a model the figures read
 * ({@see self::SOURCES}) flushes all entries, so an admin edit shows on the
 * next render. Writes that bypass model events (raw DB::table() updates, bulk
 * inserts in seeders) are caught by the TTL, which is a backstop, not the
 * freshness mechanism. The TTL also rolls over the reference-year fallback in
 * {@see ImpactFigures::referenceYear()} at New Year.
 *
 * Entries hold plain arrays only (cache.serializable_classes is false).
 */
class PublicFiguresCache
{
    /** Backstop lifetime for writes that bypass model events. */
    public const TTL_SECONDS = 3600;

    /** Every cached figure, so a flush can reach all of them. */
    public const MOVEMENT_STATS = 'movement-stats';

    public const BELGIUM_MAP = 'belgium-map';

    /** @var list<string> */
    private const NAMES = [self::MOVEMENT_STATS, self::BELGIUM_MAP];

    /**
     * Models whose changes can alter a cached figure.
     *
     * @var list<class-string<Model>>
     */
    public const SOURCES = [Group::class, Activity::class, YearStat::class, PostalCode::class];

    /**
     * The cached value for the active locale, rebuilt when missing or when it
     * was stored for another `$version` (e.g. the mtime of a static input file,
     * so replacing that file needs no flush).
     *
     * @template TValue of array
     *
     * @param  Closure(): TValue  $build
     * @return TValue
     */
    public static function remember(string $name, Closure $build, int|string $version = 0): array
    {
        $key = self::key($name, app()->getLocale());
        $entry = Cache::get($key);

        if (is_array($entry) && ($entry['version'] ?? null) === $version) {
            return $entry['value'];
        }

        $value = $build();
        Cache::put($key, ['version' => $version, 'value' => $value], self::TTL_SECONDS);

        return $value;
    }

    /** Drop every cached figure in every locale. */
    public static function flush(): void
    {
        foreach (self::NAMES as $name) {
            foreach (SetLocale::SUPPORTED as $locale) {
                Cache::forget(self::key($name, $locale));
            }
        }
    }

    /** Flush on every save or delete of a source model. */
    public static function flushOnSourceChanges(): void
    {
        foreach (self::SOURCES as $model) {
            $model::saved(fn () => self::flush());
            $model::deleted(fn () => self::flush());
        }
    }

    private static function key(string $name, string $locale): string
    {
        return "public-figures:{$name}:{$locale}";
    }
}

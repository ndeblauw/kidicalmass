<?php

namespace App\Support\Map;

use App\Enums\Region;
use App\Models\Group;
use App\Models\PostalCode;
use Illuminate\Support\Arr;

/**
 * Feeds the live list of visible local groups into {@see BelgiumMap} for
 * <x-belgium-map>.
 *
 * Freshness strategy: nothing is stored, so there is nothing to invalidate.
 * Every render reads the small input set (visible groups, their region parent,
 * postcode centroids: three queries, constant in the number of groups) and
 * composes the map in about 0.2 ms. Any change that alters the picture (a group
 * added, deleted, hidden, renamed, moved to another zip, re-parented, a
 * centroid corrected) shows on the next request, whether it came through
 * Eloquent, a query-builder update, a seeder or raw SQL.
 *
 * Why no cache: the three input queries are needed either way to know whether
 * a cached copy is still valid. A cheaper count + max(updated_at) fingerprint
 * would miss changes that do not touch a group's timestamp: raw DB::table()
 * writes, postal_codes centroid edits and renames of a region parent. Caching
 * would then only save the 0.2 ms composition, while the app's default cache
 * store is `database`, so a cache hit costs one more query than it saves. If
 * composition ever gets expensive, key a Cache::remember() on a hash of
 * {@see groups()} plus the locale and a version number for the geometry; the
 * output is plain arrays and therefore cache-safe (cache.serializable_classes
 * is false).
 *
 * @phpstan-import-type BelgiumMapData from BelgiumMap
 */
class LocalGroupsMap
{
    public const OUTLINE_PATH = 'data/belgium-outline.json';

    /**
     * The composed map for the active locale: {@see BelgiumMap::compose()} plus
     * `label`, the localised accessible name ("Kaart van België: 16 lokale
     * groepen in Brussel, 6 in Wallonië en 4 in Vlaanderen").
     *
     * @return BelgiumMapData&array{label: string}
     */
    public function data(): array
    {
        $map = BelgiumMap::fromFile(database_path(self::OUTLINE_PATH))
            ->compose($this->groups(), Region::BRUSSELS->label());

        return [...$map, 'label' => $this->label($map['counts'])];
    }

    /**
     * @param  array<string, int>  $counts  groups drawn per region slug (see BelgiumMap::compose())
     */
    private function label(array $counts): string
    {
        $parts = [];
        foreach ([...array_map(fn (Region $region): string => $region->slug(), Region::cases()), 'other'] as $where) {
            $count = $counts[$where] ?? 0;
            if ($count === 0) {
                continue;
            }

            $replace = ['count' => $count, 'where' => __('components.belgium_map.where.'.$where)];
            $parts[] = $parts === []
                ? trans_choice('components.belgium_map.first', $count, $replace)
                : __('components.belgium_map.next', $replace);
        }

        return __('components.belgium_map.label', [
            'parts' => Arr::join($parts, ', ', ' '.__('components.belgium_map.and').' '),
        ]);
    }

    /**
     * Visible groups as map input: localised name, region and postcode centroid.
     * Same sources as the interactive map on Lokale groepen (parent region,
     * {@see PostalCode::centroidsByZip()}).
     *
     * @return list<array{name: string, region: ?Region, lat: ?float, lng: ?float}>
     */
    private function groups(): array
    {
        $groups = Group::visible()
            ->with('parent:id,name_nl')
            ->orderBy('id')
            ->get(['id', 'parent_id', 'name_nl', 'name_fr', 'shortname', 'zip']);

        $centroids = PostalCode::centroidsByZip($groups->pluck('zip'));

        return $groups->map(function (Group $group) use ($centroids): array {
            $centroid = $group->zip ? $centroids->get($group->zip) : null;

            return [
                'name' => $group->publicLabel(),
                'region' => Region::forGroup($group),
                'lat' => $centroid?->latitude,
                'lng' => $centroid?->longitude,
            ];
        })->all();
    }
}

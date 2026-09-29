<?php

namespace App\Support\Map;

use App\Enums\Region;
use App\Models\Group;
use App\Models\PostalCode;
use App\Support\PublicFiguresCache;
use Illuminate\Support\Arr;

/**
 * Feeds the live list of visible local groups into {@see BelgiumMap} for
 * <x-belgium-map>.
 *
 * Freshness strategy: the composed map (plain arrays) is cached per locale in
 * {@see PublicFiguresCache}, which is flushed on every Eloquent save or delete
 * of a group or postal code, so an admin adding, hiding, renaming, moving or
 * re-parenting a group shows on the next render. Writes that bypass model
 * events (raw DB::table() updates, seeder bulk inserts) show once the cache's
 * backstop TTL runs out. The static outline file is part of the cache version
 * (its mtime), so replacing it rebuilds the map without a flush.
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
        $outline = database_path(self::OUTLINE_PATH);

        return PublicFiguresCache::remember(
            PublicFiguresCache::BELGIUM_MAP,
            fn (): array => $this->compose($outline),
            version: (int) filemtime($outline),
        );
    }

    /** @return BelgiumMapData&array{label: string} */
    private function compose(string $outline): array
    {
        $map = BelgiumMap::fromFile($outline)->compose($this->groups(), Region::BRUSSELS->label());

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

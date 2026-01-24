<?php

namespace Database\Factories\Concerns;

trait AttachesMediaFromCache
{
    protected static array $mediaCache = [];

    /**
     * @param  callable(int):array  $downloader
     */
    protected function attachMediaFromCache(
        mixed $model,
        string $collection,
        callable $downloader,
        int $downloadCount,
        int $galleryMax = 0,
        ?string $cacheKey = null
    ): void {
        $cacheKey ??= $collection;

        if (empty(static::$mediaCache[$cacheKey] ?? [])) {
            static::$mediaCache[$cacheKey] = $downloader($downloadCount);
        }

        $pool = static::$mediaCache[$cacheKey] ?? [];

        if (empty($pool)) {
            return;
        }

        $mainImage = $pool[array_rand($pool)];

        try {
            $model->addMedia($mainImage)->preservingOriginal()->toMediaCollection($collection);
        } catch (\Exception $e) {
        }

        if ($galleryMax <= 0) {
            return;
        }

        $galleryCount = rand(0, $galleryMax);

        for ($i = 0; $i < $galleryCount; $i++) {
            $galleryImage = $pool[array_rand($pool)];

            try {
                $model->addMedia($galleryImage)->preservingOriginal()->toMediaCollection($collection);
            } catch (\Exception $e) {
            }
        }
    }
}

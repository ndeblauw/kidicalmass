<?php

namespace Database\Factories\Concerns;

use Illuminate\Database\Eloquent\Model;

trait AttachesMediaFromCache
{
    protected static array $mediaCache = [];

    protected function primeMediaCache(string $cacheKey, callable $loader): void
    {
        if (empty(static::$mediaCache[$cacheKey] ?? [])) {
            static::$mediaCache[$cacheKey] = $loader();
        }
    }

    protected function attachSingleMedia(Model $model, string $collection, string $cacheKey = 'media'): void
    {
        $pool = static::$mediaCache[$cacheKey] ?? [];

        if (empty($pool)) {
            return;
        }

        $mediaPath = $pool[array_rand($pool)];

        try {
            $model->addMedia($mediaPath)->preservingOriginal()->toMediaCollection($collection);
        } catch (\Exception $e) {
        }
    }

    protected function attachMultipleMedia(
        Model $model,
        string $collection,
        int $min = 0,
        int $max = 5,
        string $cacheKey = 'media'
    ): void {
        $pool = static::$mediaCache[$cacheKey] ?? [];

        if (empty($pool)) {
            return;
        }

        $count = $max < $min ? 0 : rand($min, $max);

        for ($i = 0; $i < $count; $i++) {
            $mediaPath = $pool[array_rand($pool)];

            try {
                $model->addMedia($mediaPath)->preservingOriginal()->toMediaCollection($collection);
            } catch (\Exception $e) {
            }
        }
    }
}

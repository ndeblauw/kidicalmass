<?php

namespace Database\Factories\Concerns;

use Illuminate\Database\Eloquent\Model;

trait AttachesMediaFromCache
{
    protected static array $mediaCache = [];

    /**
     * @param  callable(int):array  $downloader
     */
    protected function attachMediaFromCache(
        Model $model,
        string $collection,
        callable $downloader,
        int $downloadCount,
        int $galleryMax = 0
    ): void {
        if (empty(static::$mediaCache[$collection] ?? [])) {
            static::$mediaCache[$collection] = $downloader($downloadCount);
        }

        $pool = static::$mediaCache[$collection] ?? [];

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

    protected function attachMultipleMediaFor(
        Model $model,
        string $collection,
        callable $downloader,
        int $downloadCount,
        int $maxAdditional = 0
    ): void {
        $this->attachMediaFromCache(
            $model,
            $collection,
            $downloader,
            $downloadCount,
            $maxAdditional
        );
    }

    protected function attachSingleMediaFor(
        Model $model,
        string $collection,
        callable $downloader,
        int $downloadCount
    ): void {
        $this->attachMediaFromCache(
            $model,
            $collection,
            $downloader,
            $downloadCount,
            0
        );
    }
}

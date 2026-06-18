<?php

namespace App\Console\Commands;

use App\Models\Group;
use Illuminate\Console\Command;

class SeedGroupGalleryCommand extends Command
{
    protected $signature = 'dev:seed-group-gallery
        {--group=* : Group ids to seed (defaults to a curated set)}
        {--count=6 : Photos to attach per group}
        {--source=* : Explicit image paths in order, cover first (absolute or relative to base_path); defaults to globbing public/img/photography}';

    protected $description = 'Attach sample photos to groups\' gallery collection (non-production only).';

    /**
     * Groups that get a sample gallery by default. Includes id 3 (the local test page).
     *
     * @var list<int>
     */
    private const DEFAULT_GROUP_IDS = [3];

    public function handle(): int
    {
        if (app()->isProduction()) {
            $this->error('Refusing to seed gallery photos in production.');

            return self::FAILURE;
        }

        $sources = $this->resolveSourcePaths();

        if ($sources === []) {
            $this->error('No source images found (checked --source paths and public/img/photography).');

            return self::FAILURE;
        }

        $ids = $this->option('group') ?: self::DEFAULT_GROUP_IDS;
        $count = max(1, (int) $this->option('count'));

        foreach ($ids as $id) {
            $group = Group::find($id);

            if (! $group) {
                $this->warn("Group {$id} not found, skipping.");

                continue;
            }

            $group->clearMediaCollection('gallery');

            for ($i = 0; $i < $count; $i++) {
                $path = $sources[$i % count($sources)];

                $group->addMedia($path)
                    ->preservingOriginal()
                    ->usingName(pathinfo($path, PATHINFO_FILENAME))
                    ->toMediaCollection('gallery');
            }

            $this->info("Seeded {$count} gallery photos onto group {$id} ({$group->name}).");
        }

        return self::SUCCESS;
    }

    /**
     * Resolve the images to attach: explicit --source paths (in order, cover first)
     * when given, otherwise the globbed sample set.
     *
     * @return list<string>
     */
    private function resolveSourcePaths(): array
    {
        $explicit = $this->option('source');

        if ($explicit === []) {
            return $this->sampleImagePaths();
        }

        return collect($explicit)
            ->map(fn (string $path): string => is_file($path) ? $path : base_path($path))
            ->filter(function (string $path): bool {
                if (is_file($path)) {
                    return true;
                }

                $this->warn("Source image not found, skipping: {$path}");

                return false;
            })
            ->values()
            ->all();
    }

    /**
     * Sample images: every jpg/png at the top level of img/photography and one sub-dir deep.
     *
     * @return list<string>
     */
    private function sampleImagePaths(): array
    {
        $base = public_path('img/photography');

        return collect(glob("{$base}/*.{jpg,jpeg,png}", GLOB_BRACE) ?: [])
            ->merge(glob("{$base}/*/*.{jpg,jpeg,png}", GLOB_BRACE) ?: [])
            ->values()
            ->all();
    }
}

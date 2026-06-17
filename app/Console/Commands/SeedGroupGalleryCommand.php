<?php

namespace App\Console\Commands;

use App\Models\Group;
use Illuminate\Console\Command;

class SeedGroupGalleryCommand extends Command
{
    protected $signature = 'dev:seed-group-gallery
        {--group=* : Group ids to seed (defaults to a curated set)}
        {--count=6 : Photos to attach per group}';

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

        $sources = $this->sampleImagePaths();

        if ($sources === []) {
            $this->error('No sample images found under public/img/photography.');

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

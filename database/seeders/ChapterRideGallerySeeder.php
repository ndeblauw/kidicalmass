<?php

namespace Database\Seeders;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use Illuminate\Database\Seeder;

/**
 * Dresses each listed chapter's most recent past ride with sample photos, so the
 * "In beeld" wall on the chapter page (P-11, groups/show.blade.php) shows a real-
 * looking gallery. The chapter page reads the latest ride that has a `gallery`
 * collection (see GroupController::show), so this is what fills that band.
 *
 * Single owner of ride galleries among the seeders: ChapterShowcaseSeeder no
 * longer touches them. Re-runnable and non-production only — each ride's gallery
 * is cleared before reseeding, so `db:seed` twice is a no-op rather than a pile-up.
 *
 * Counts vary per chapter on purpose, to exercise the wall's cover + four-tile cap
 * and the "bekijk alle foto's" lightbox at different sizes. Add a chapter by
 * dropping another `shortname => count` into the map below.
 */
class ChapterRideGallerySeeder extends Seeder
{
    /**
     * Chapters to dress, by shortname, and how many photos each latest ride gets.
     *
     * @var array<string, int>
     */
    private const CHAPTER_PHOTO_COUNTS = [
        'schaarbeek' => 8,
        'elsene' => 5,
        'namen' => 3,
    ];

    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->warn('ChapterRideGallerySeeder: refusing to run in production.');

            return;
        }

        // Slide the window into the source set per chapter so the three pages don't
        // open with the same cover photo.
        $offset = 0;

        foreach (self::CHAPTER_PHOTO_COUNTS as $shortname => $count) {
            $group = Group::where('shortname', $shortname)->first();

            if (! $group) {
                $this->command?->warn("ChapterRideGallerySeeder: chapter '{$shortname}' not found, skipping.");

                continue;
            }

            $this->seedLatestRide($group, $count, $offset);
            $offset += $count;
        }
    }

    /**
     * Attach $count sample photos to $group's most recent past ride's `gallery`
     * collection, starting $offset photos into the (varied-orientation) source set.
     * Idempotent: clears that ride's gallery first. Returns false when there is no
     * past ride or no source photos.
     */
    public function seedLatestRide(Group $group, int $count, int $offset = 0): bool
    {
        $ride = $this->latestRide($group);

        if (! $ride) {
            $this->command?->warn("ChapterRideGallerySeeder: no past ride for '{$group->name}', skipping.");

            return false;
        }

        // Run conversions in-process (no queue worker during seeding) and give GD
        // room for the full-size photography — mirrors SeedGroupGalleryCommand.
        config(['media-library.queue_conversions_by_default' => false]);
        ini_set('memory_limit', '512M');

        $sources = $this->samplePhotoPaths();

        if ($sources === []) {
            $this->command?->warn('ChapterRideGallerySeeder: no sample photos found, skipping.');

            return false;
        }

        $ride->clearMediaCollection('gallery');

        for ($i = 0; $i < $count; $i++) {
            $path = $sources[($offset + $i) % count($sources)];

            $ride->addMedia($path)
                ->preservingOriginal()
                ->usingName(pathinfo($path, PATHINFO_FILENAME))
                ->toMediaCollection('gallery');
        }

        $this->command?->info("Seeded {$count} ride photos onto '{$group->name}'.");

        return true;
    }

    /**
     * The chapter's most recent past Kidical Mass ride, the one the chapter page
     * highlights — matches the ordering in GroupController::show.
     */
    private function latestRide(Group $group): ?Activity
    {
        return Activity::query()
            ->whereHas('groups', fn ($query) => $query->whereKey($group->id))
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->where('begin_date', '<', now())
            ->orderByDesc('begin_date')
            ->first();
    }

    /**
     * Sample photos in deliberately mixed aspect ratios and orientations: every
     * web image at the top level of img/photography plus one sub-dir deep. Widened
     * to .webp (the bulk of the set) beyond SeedGroupGalleryCommand's jpg/png glob.
     *
     * @return list<string>
     */
    private function samplePhotoPaths(): array
    {
        $base = public_path('img/photography');

        return collect(glob("{$base}/*.{webp,jpg,jpeg,png}", GLOB_BRACE) ?: [])
            ->merge(glob("{$base}/*/*.{webp,jpg,jpeg,png,avif}", GLOB_BRACE) ?: [])
            ->values()
            ->all();
    }
}

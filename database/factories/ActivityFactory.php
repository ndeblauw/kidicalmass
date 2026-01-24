<?php

namespace Database\Factories;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Factories\Concerns\AttachesMediaFromCache;
use Database\Seeders\MediaSeeder;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
    use AttachesMediaFromCache;

    protected $model = Activity::class;

    public function definition(): array
    {
        $beginDate = \Carbon\Carbon::parse(fake()->dateTimeBetween('now', '+1 year'));
        $endDate = $beginDate->copy()->addDays(random_int(1, 2));

        return [
            'title_nl' => fake()->sentence(),
            'title_fr' => fake()->sentence(),
            'content_nl' => fake()->paragraphs(2, true),
            'content_fr' => fake()->paragraphs(2, true),
            'activity_type' => fake()->randomElement(ActivityType::cases()),
            'begin_date' => $beginDate,
            'end_date' => $endDate,
            'location' => fake()->city().', '.fake()->address(),
            'author_id' => User::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Activity $activity) {
            $this->attachImages($activity);
        });
    }

    protected function attachImages(Activity $activity): void
    {
        $downloader = fn (int $count) => MediaSeeder::ensureImages($count);

        $this->attachSingleMediaFor($activity, 'main', $downloader, 5);
        $this->attachMultipleMediaFor($activity, 'gallery', $downloader, 5, 3);
    }
}

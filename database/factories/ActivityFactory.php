<?php

namespace Database\Factories;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\Concerns\AttachesMediaFromCache;
use Database\Seeders\MediaSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    use AttachesMediaFromCache;

    protected $model = Activity::class;

    public function definition(): array
    {
        $beginDate = Carbon::parse(fake()->dateTimeBetween('now', '+1 year'));
        $durationMinutes = fake()->randomElement([60, 90, 120, 180]);

        return [
            'title_nl' => fake()->sentence(),
            'title_fr' => fake()->sentence(),
            'content_nl' => fake()->paragraphs(2, true),
            'content_fr' => fake()->paragraphs(2, true),
            'activity_type' => fake()->randomElement(ActivityType::cases()),
            'begin_date' => $beginDate,
            'location' => fake()->city().', '.fake()->address(),
            'commute_link' => fake()->randomElement([
                'https://www.komoot.com/plan/tour/123456',
                'https://ridewithgps.com/routes/12345',
                null,
            ]),
            'duration_minutes' => $durationMinutes,
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
        $this->primeMediaCache('images', fn () => MediaSeeder::ensureImages(5));

        $this->attachSingleMedia($activity, 'main', 'images');
        $this->attachMultipleMedia($activity, 'gallery', 0, 3, 'images');
    }
}

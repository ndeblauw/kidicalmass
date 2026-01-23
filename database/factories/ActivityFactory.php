<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Activity>
 */
class ActivityFactory extends Factory
{
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
            'begin_date' => $beginDate,
            'end_date' => $endDate,
            'location' => fake()->city().', '.fake()->address(),
            'author_id' => User::factory(),
        ];
    }
}

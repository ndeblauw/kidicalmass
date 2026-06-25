<?php

namespace Database\Factories;

use App\Models\YearStat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<YearStat>
 */
class YearStatFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'year' => fake()->unique()->numberBetween(2020, 2030),
            'participants' => fake()->numberBetween(500, 9000),
        ];
    }
}

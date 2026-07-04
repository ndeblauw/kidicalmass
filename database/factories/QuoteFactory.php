<?php

namespace Database\Factories;

use App\Models\Quote;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Quote>
 */
class QuoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'slot' => fake()->unique()->slug(2),
            'quote' => fake()->sentence(12),
            'attribution' => fake()->firstName().', mama van twee kinderen',
            'visible' => true,
        ];
    }
}

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
            'quote_nl' => fake()->sentence(12),
            'quote_fr' => fake()->sentence(12),
            'attribution_nl' => fake()->firstName().', mama van twee kinderen',
            'attribution_fr' => fake()->firstName().', maman de deux enfants',
            'visible' => true,
        ];
    }
}

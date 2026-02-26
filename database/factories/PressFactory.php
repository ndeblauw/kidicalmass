<?php

namespace Database\Factories;

use App\Enums\PressType;
use App\Models\Press;
use Database\Factories\Concerns\AttachesMediaFromCache;
use Database\Seeders\MediaSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Press>
 */
class PressFactory extends Factory
{
    use AttachesMediaFromCache;

    protected $model = Press::class;

    public function definition(): array
    {
        $outlets = [
            'De Standaard', 'Het Laatste Nieuws', 'De Morgen', 'De Tijd',
            'Le Soir', 'La Libre Belgique', 'La DH', 'L\'Echo',
            'VRT Nieuws', 'RTBF', 'Knack', 'Humo',
            'Apache', 'MO Magazine', 'Bruzz', 'Gondola',
        ];

        return [
            'title' => fake()->sentence(),
            'publication_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'outlet' => $outlets[array_rand($outlets)],
            'media_type' => fake()->randomElement(PressType::cases())->value,
            'url' => fake()->boolean(80) ? fake()->url() : null,
            'description' => fake()->paragraphs(2, true),
            'visible' => fake()->boolean(90),
            'highlighted' => fake()->boolean(20),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Press $press) {
            $this->attachImage($press);
        });
    }

    protected function attachImage(Press $press): void
    {
        $this->primeMediaCache('images', fn () => MediaSeeder::ensureImages(5));

        $this->attachSingleMedia($press, 'attachment', 'images');
    }
}

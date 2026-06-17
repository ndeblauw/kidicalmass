<?php

namespace Database\Factories;

use App\Models\PressArticle;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PressArticle>
 */
class PressArticleFactory extends Factory
{
    protected $model = PressArticle::class;

    protected array $outlets = [
        'RTBF', 'BX1', 'BRUZZ', 'La DH', 'HLN', 'Het Nieuwsblad',
        'Politico', 'Vivacité', 'La Libre', 'Le Soir', 'De Standaard',
        'De Morgen', 'VRT NWS', 'Sudinfo', 'L\'Écho',
    ];

    public function definition(): array
    {
        return [
            'title_nl' => fake()->sentence(),
            'title_fr' => fake()->sentence(),
            'outlet' => $this->outlets[array_rand($this->outlets)],
            'url' => fake()->url(),
            'published_at' => fake()->dateTimeThisYear(),
            'author_id' => User::factory(),
        ];
    }
}

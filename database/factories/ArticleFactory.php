<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Database\Factories\Concerns\AttachesMediaFromCache;
use Database\Seeders\MediaSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    use AttachesMediaFromCache;

    public function definition(): array
    {
        return [
            'title_nl' => fake()->sentence(),
            'title_fr' => fake()->sentence(),
            'content_nl' => fake()->paragraphs(6, true),
            'content_fr' => fake()->paragraphs(6, true),
            'author_id' => User::factory(),
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-1 year'),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['is_published' => false, 'published_at' => null]);
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Article $article) {
            if (app()->environment('testing')) {
                return;
            }

            $this->attachImages($article);
        });
    }

    protected function attachImages(Article $article): void
    {
        $this->primeMediaCache('images', fn () => MediaSeeder::ensureImages(5));

        $this->attachSingleMedia($article, 'main', 'images');
        $this->attachMultipleMedia($article, 'gallery', 0, 3, 'images');
    }
}

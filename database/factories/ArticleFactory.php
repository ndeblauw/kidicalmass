<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Factories\Concerns\AttachesMediaFromCache;
use Database\Seeders\MediaSeeder;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    use AttachesMediaFromCache;

    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'title_nl' => fake()->sentence(),
            'title_fr' => fake()->sentence(),
            'content_nl' => fake()->paragraphs(6, true),
            'content_fr' => fake()->paragraphs(6, true),
            'author_id' => User::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Article $article) {
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

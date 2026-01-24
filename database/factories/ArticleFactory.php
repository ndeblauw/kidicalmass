<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Seeders\MediaSeeder;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    protected static $imageCache = [];

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
        if (empty(static::$imageCache)) {
            static::$imageCache = MediaSeeder::ensureImages(5);
        }

        if (empty(static::$imageCache)) {
            return;
        }

        $mainImage = static::$imageCache[array_rand(static::$imageCache)];
        try {
            $article->addMedia($mainImage)->preservingOriginal()->toMediaCollection('main');
        } catch (\Exception $e) {
        }

        $galleryCount = rand(0, 3);
        for ($i = 0; $i < $galleryCount; $i++) {
            $galleryImage = static::$imageCache[array_rand(static::$imageCache)];
            try {
                $article->addMedia($galleryImage)->toMediaCollection('gallery');
            } catch (\Exception $e) {
            }
        }
    }
}

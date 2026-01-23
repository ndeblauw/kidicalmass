<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

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
        // Download and cache images if not already cached
        if (empty(static::$imageCache)) {
            $this->downloadAndCacheImages();
        }

        // Attach a random main image
        if (! empty(static::$imageCache)) {
            $mainImage = static::$imageCache[array_rand(static::$imageCache)];
            $article->addMedia($mainImage)->toMediaCollection('main');

            // Randomly attach 0-3 gallery images
            $galleryCount = rand(0, 3);
            for ($i = 0; $i < $galleryCount; $i++) {
                $galleryImage = static::$imageCache[array_rand(static::$imageCache)];
                $article->addMedia($galleryImage)->toMediaCollection('gallery');
            }
        }
    }

    protected function downloadAndCacheImages(): void
    {
        $tempDir = storage_path('app/temp-images');

        // Check if we already have cached images
        if (File::exists($tempDir) && count(File::files($tempDir)) >= 20) {
            static::$imageCache = File::files($tempDir);

            return;
        }

        // Create temp directory
        File::ensureDirectoryExists($tempDir);

        // Download 20 images from Unsplash
        for ($i = 1; $i <= 20; $i++) {
            $imagePath = $tempDir.'/image-'.$i.'.jpg';

            if (! File::exists($imagePath)) {
                try {
                    // Use Unsplash API with random cycling/family related images
                    $response = Http::timeout(10)->get('https://source.unsplash.com/800x600/?cycling,family,bike');
                    if ($response->successful()) {
                        File::put($imagePath, $response->body());
                        static::$imageCache[] = $imagePath;
                    }
                } catch (\Exception $e) {
                    // If download fails, skip this image
                    continue;
                }
            } else {
                static::$imageCache[] = $imagePath;
            }
        }
    }

    public static function cleanupTempImages(): void
    {
        $tempDir = storage_path('app/temp-images');
        if (File::exists($tempDir)) {
            File::deleteDirectory($tempDir);
        }
    }
}

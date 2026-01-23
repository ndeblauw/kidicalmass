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

    protected static $imagesDownloaded = false;

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
        // Only download images once per seeding run
        if (! static::$imagesDownloaded) {
            $this->downloadAndCacheImages();
            static::$imagesDownloaded = true;
        }

        // Attach a random main image
        if (! empty(static::$imageCache)) {
            $mainImage = static::$imageCache[array_rand(static::$imageCache)];
            try {
                $article->addMedia($mainImage)->toMediaCollection('main');
            } catch (\Exception $e) {
                // Silently skip if image attachment fails
            }

            // Randomly attach 0-3 gallery images
            $galleryCount = rand(0, 3);
            for ($i = 0; $i < $galleryCount; $i++) {
                $galleryImage = static::$imageCache[array_rand(static::$imageCache)];
                try {
                    $article->addMedia($galleryImage)->toMediaCollection('gallery');
                } catch (\Exception $e) {
                    // Silently skip if image attachment fails
                }
            }
        }
    }

    protected function downloadAndCacheImages(): void
    {
        $tempDir = storage_path('app/temp-images');

        // Check if we already have cached images
        if (File::exists($tempDir)) {
            $files = File::files($tempDir);
            if (count($files) >= 20) {
                static::$imageCache = $files;

                return;
            }
        }

        // Create temp directory
        File::ensureDirectoryExists($tempDir);

        // Download 20 images from Unsplash
        $topics = ['cycling', 'bike', 'family', 'kids', 'outdoor'];
        for ($i = 1; $i <= 20; $i++) {
            $imagePath = $tempDir.'/image-'.$i.'.jpg';

            if (! File::exists($imagePath)) {
                try {
                    $topic = $topics[array_rand($topics)];
                    // Use Unsplash source with specific size and random seed
                    $url = "https://source.unsplash.com/800x600/?{$topic}&sig={$i}";
                    $response = Http::timeout(15)->get($url);

                    if ($response->successful() && strlen($response->body()) > 1000) {
                        File::put($imagePath, $response->body());
                        static::$imageCache[] = $imagePath;
                        // Small delay to avoid rate limiting
                        usleep(500000); // 0.5 seconds
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

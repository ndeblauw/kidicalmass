<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        static::ensureImages(40);
    }

    public static function ensureImages(int $count): array
    {
        return static::downloadBatch(
            $count,
            storage_path('app/temp-images'),
            'image',
            fn (int $index): string => "https://picsum.photos/800/600?random={$index}"
        );
    }

    public static function cleanup(): void
    {
        // File::deleteDirectory(storage_path('app/temp-images'));
    }

    private static function downloadBatch(int $count, string $directory, string $prefix, callable $urlResolver): array
    {
        File::ensureDirectoryExists($directory);

        $files = collect(File::files($directory))->map(fn ($file) => $file->getPathname())->values()->all();

        if (count($files) >= $count) {
            return array_values($files);
        }

        while (count($files) < $count) {
            $index = count($files) + 1;
            $path = $directory."/{$prefix}-{$index}.jpg";

            if (! File::exists($path)) {
                try {
                    $url = $urlResolver($index);
                    /** @var \Illuminate\Http\Client\Response $response */
                    $response = Http::timeout(15)->get($url);

                    if ($response->successful() && strlen($response->body()) > 1000) {
                        File::put($path, $response->body());
                        $files[] = $path;
                        usleep(100000);
                    }
                } catch (\Exception $e) {
                    continue;
                }
            } else {
                $files[] = $path;
            }
        }

        return $files;
    }
}

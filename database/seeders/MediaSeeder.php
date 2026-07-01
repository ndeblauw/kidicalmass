<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        static::ensureImages(40);
    }

    public static function ensureImages(int $count): array
    {
        $directory = storage_path('app/temp-images');

        File::ensureDirectoryExists($directory);

        $files = collect(File::files($directory))->map(fn ($file) => $file->getPathname())->values()->all();

        if (count($files) >= $count) {
            return array_values($files);
        }

        while (count($files) < $count) {
            $index = count($files) + 1;
            $path = $directory."/image-{$index}.jpg";

            if (! File::exists($path)) {
                static::generateImage($path);
                $files[] = $path;
            } else {
                $files[] = $path;
            }
        }

        return $files;
    }

    public static function cleanup(): void
    {
        // File::deleteDirectory(storage_path('app/temp-images'));
    }

    private static function generateImage(string $path): void
    {
        $width = 800;
        $height = 600;

        $img = imagecreatetruecolor($width, $height);

        $bg = imagecolorallocate($img, mt_rand(0, 200), mt_rand(0, 200), mt_rand(0, 200));
        imagefill($img, 0, 0, $bg);

        $textColor = imagecolorallocate($img, 255, 255, 255);
        $text = pathinfo($path, PATHINFO_FILENAME);
        $fontSize = 5;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $x = (int) (($width - $textWidth) / 2);
        $y = (int) (($height - imagefontheight($fontSize)) / 2);
        imagestring($img, $fontSize, $x, $y, $text, $textColor);

        // Add some decorative rectangles
        $accent = imagecolorallocate($img, 255, 255, 255);
        for ($i = 0; $i < 5; $i++) {
            $rx = mt_rand(0, $width - 100);
            $ry = mt_rand(0, $height - 100);
            $rw = mt_rand(20, 80);
            $rh = mt_rand(20, 80);
            imagefilledrectangle($img, $rx, $ry, $rx + $rw, $ry + $rh, $accent);
        }

        imagejpeg($img, $path, 30);
        imagedestroy($img);
    }
}

<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    protected static $logoCache = [];

    protected static $logosDownloaded = false;

    public function definition(): array
    {
        $companyNames = [
            'Cyclo', 'Farm', 'MonkeyDonkey', 'GRC', 'Ride', 'REM Brussel', 
            'Citizens Action', 'Heroes for Zero', 'Kids Beschik', 'Ketje',
            'Pro Velo', 'My Kids Bikes', 'Velokanik', 'Fiets FEB', 
            'EUCyclo', 'Velophil', 'Angel of Care', 'Gracy',
            'Fietsersbond', 'Bike4Brussels', 'Brussels Mobiliteit'
        ];

        $name = $companyNames[array_rand($companyNames)] ?? fake()->company();

        return [
            'name' => $name,
            'url' => fake()->url(),
            'description_nl' => fake()->paragraphs(2, true),
            'description_fr' => fake()->paragraphs(2, true),
            'show_logo' => fake()->boolean(90), // 90% show logo
            'visible' => fake()->boolean(95), // 95% visible
            'group_id' => Group::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Partner $partner) {
            $this->attachLogo($partner);
        });
    }

    protected function attachLogo(Partner $partner): void
    {
        // Only download logos once per seeding run
        if (! static::$logosDownloaded) {
            $this->downloadAndCacheLogos();
            static::$logosDownloaded = true;
        }

        // Attach a random logo
        if (! empty(static::$logoCache)) {
            $logo = static::$logoCache[array_rand(static::$logoCache)];
            try {
                $partner->addMedia($logo)->toMediaCollection('logo');
            } catch (\Exception $e) {
                // Silently skip if logo attachment fails
            }
        }
    }

    protected function downloadAndCacheLogos(): void
    {
        $tempDir = storage_path('app/temp-logos');

        // Check if we already have cached logos
        if (File::exists($tempDir)) {
            $files = File::files($tempDir);
            if (count($files) >= 20) {
                static::$logoCache = $files;

                return;
            }
        }

        // Create temp directory
        File::ensureDirectoryExists($tempDir);

        // Download 20 logo images
        for ($i = 1; $i <= 20; $i++) {
            $logoPath = $tempDir.'/logo-'.$i.'.jpg';

            if (! File::exists($logoPath)) {
                try {
                    // Use a smaller size for logos
                    $url = "https://picsum.photos/400/200?random=logo-{$i}";
                    $response = Http::timeout(15)->get($url);

                    if ($response->successful() && strlen($response->body()) > 1000) {
                        File::put($logoPath, $response->body());
                        static::$logoCache[] = $logoPath;
                        // Small delay to avoid rate limiting
                        usleep(500000); // 0.5 seconds
                    }
                } catch (\Exception $e) {
                    // If download fails, skip this logo
                    continue;
                }
            } else {
                static::$logoCache[] = $logoPath;
            }
        }
    }

    public static function cleanupTempLogos(): void
    {
        $tempDir = storage_path('app/temp-logos');
        if (File::exists($tempDir)) {
            File::deleteDirectory($tempDir);
        }
    }
}

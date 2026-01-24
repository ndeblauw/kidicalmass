<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Database\Factories\Concerns\AttachesMediaFromCache;
use Database\Seeders\MediaSeeder;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partner>
 */
class PartnerFactory extends Factory
{
    use AttachesMediaFromCache;

    protected $model = Partner::class;

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
        $this->attachMediaFromCache(
            $partner,
            'logo',
            fn (int $count) => MediaSeeder::ensureImages($count),
            5,
            0,
            'logos'
        );
    }
}

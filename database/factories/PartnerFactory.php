<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\Partner;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * @extends Factory<Partner>
 */
class PartnerFactory extends Factory
{
    protected $model = Partner::class;

    public function definition(): array
    {
        $companyNames = [
            'Cyclo', 'Farm', 'MonkeyDonkey', 'GRC', 'Ride', 'REM Brussel',
            'Citizens Action', 'Heroes for Zero', 'Kids Beschik', 'Ketje',
            'Pro Velo', 'My Kids Bikes', 'Velokanik', 'Fiets FEB',
            'EUCyclo', 'Velophil', 'Angel of Care', 'Gracy',
            'Fietsersbond', 'Bike4Brussels', 'Brussels Mobiliteit',
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
        return $this->afterCreating(function (Partner $partner): void {
            $this->attachLogo($partner);
        });
    }

    /**
     * Attach the curated logo file matching the partner's slug, if one exists.
     * No file -> no logo media (the strip renders a name chip instead), so a
     * stock photo can never be attached as a logo.
     */
    protected function attachLogo(Partner $partner): void
    {
        $slug = Str::slug($partner->name);
        $matches = File::glob(public_path("img/partners/logos/raw/{$slug}.*"));

        if (empty($matches)) {
            return;
        }

        try {
            $partner->addMedia($matches[0])->preservingOriginal()->toMediaCollection('logo');
        } catch (\Exception $e) {
        }
    }
}

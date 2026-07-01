<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

/**
 * Seeds the real Kidical Mass Belgium chapter directory.
 *
 * Hierarchy: Belgium (root) → region → local chapter. The root and the three
 * regions are invisible; only the local chapters surface in the public directory.
 *
 * NOTE: the region NAMES must stay 'Brussels Capital Region' / 'Wallonia' /
 * 'Flanders' verbatim. The chapters index (resources/views/groups/index.blade.php)
 * groups by these exact strings and maps them to their Dutch labels there.
 * Chapter names use the Dutch exonym since the public site is Dutch-only.
 */
class GroupSeeder extends Seeder
{
    /**
     * @var array<string, array{name: string, started: int, chapters: array<int, array{shortname: string, name: string, zip: string, started: int}>}>
     */
    private array $regions = [
        'brussels-capital-region' => [
            'name' => 'Brussels Capital Region',
            'started' => 2020,
            'chapters' => [
                ['shortname' => 'schaarbeek', 'name' => 'Schaarbeek', 'zip' => '1030', 'started' => 2020],
                ['shortname' => 'elsene', 'name' => 'Elsene', 'zip' => '1050', 'started' => 2020],
                ['shortname' => 'brussel-stad', 'name' => 'Brussel Stad', 'zip' => '1000', 'started' => 2020],
                ['shortname' => 'vorst', 'name' => 'Vorst', 'zip' => '1190', 'started' => 2021],
                ['shortname' => 'anderlecht', 'name' => 'Anderlecht', 'zip' => '1070', 'started' => 2021],
                // ['shortname' => 'molenbeek', 'name' => 'Molenbeek', 'zip' => '1080', 'started' => 2021],
                ['shortname' => 'jette', 'name' => 'Jette', 'zip' => '1090', 'started' => 2022],
                ['shortname' => 'etterbeek', 'name' => 'Etterbeek', 'zip' => '1040', 'started' => 2022],
                ['shortname' => 'ukkel', 'name' => 'Ukkel', 'zip' => '1180', 'started' => 2022],
                ['shortname' => 'sint-gillis', 'name' => 'Sint-Gillis', 'zip' => '1060', 'started' => 2022],
                ['shortname' => 'watermaal-bosvoorde', 'name' => 'Watermaal-Bosvoorde', 'zip' => '1170', 'started' => 2023],
                ['shortname' => 'woluwe', 'name' => 'Woluwe', 'zip' => '1200', 'started' => 2023],
                ['shortname' => 'laken', 'name' => 'Laken', 'zip' => '1020', 'started' => 2023],
                ['shortname' => 'koekelberg', 'name' => 'Koekelberg', 'zip' => '1081', 'started' => 2023],
                ['shortname' => 'evere-haren', 'name' => 'Evere-Haren', 'zip' => '1140', 'started' => 2024],
                ['shortname' => 'neder-over-heembeek', 'name' => 'Neder-Over-Heembeek', 'zip' => '1120', 'started' => 2024],
            ],
        ],
        'wallonia' => [
            'name' => 'Wallonia',
            'started' => 2022,
            'chapters' => [
                ['shortname' => 'luik', 'name' => 'Luik', 'zip' => '4000', 'started' => 2022],
                ['shortname' => 'tubeke', 'name' => 'Tubeke', 'zip' => '1480', 'started' => 2023],
                ['shortname' => 'terhulpen', 'name' => 'Terhulpen', 'zip' => '1310', 'started' => 2023],
                ['shortname' => 'namen', 'name' => 'Namen', 'zip' => '5000', 'started' => 2024],
                ['shortname' => 'moeskroen', 'name' => 'Moeskroen', 'zip' => '7700', 'started' => 2024],
                ['shortname' => 'bergen', 'name' => 'Bergen', 'zip' => '7000', 'started' => 2025],
            ],
        ],
        'flanders' => [
            'name' => 'Flanders',
            'started' => 2024,
            'chapters' => [
                ['shortname' => 'gent', 'name' => 'Gent', 'zip' => '9000', 'started' => 2024],
                ['shortname' => 'antwerpen', 'name' => 'Antwerpen', 'zip' => '2000', 'started' => 2024],
                ['shortname' => 'leuven', 'name' => 'Leuven', 'zip' => '3000', 'started' => 2025],
                ['shortname' => 'brugge', 'name' => 'Brugge', 'zip' => '8000', 'started' => 2025],
            ],
        ],
    ];

    public function run(): void
    {
        $belgium = Group::create([
            'shortname' => 'belgium',
            'name' => 'Belgium',
            'zip' => null,
            'invisible' => true,
            'started_at' => '2020-01-01',
        ]);

        foreach ($this->regions as $shortname => $region) {
            $regionGroup = Group::create([
                'shortname' => $shortname,
                'name' => $region['name'],
                'zip' => null,
                'parent_id' => $belgium->id,
                'invisible' => true,
                'started_at' => $region['started'].'-01-01',
            ]);

            foreach ($region['chapters'] as $chapter) {
                Group::create([
                    'shortname' => $chapter['shortname'],
                    'name' => $chapter['name'],
                    'zip' => $chapter['zip'],
                    'parent_id' => $regionGroup->id,
                    'started_at' => $chapter['started'].'-03-01',
                ]);
            }
        }
    }
}

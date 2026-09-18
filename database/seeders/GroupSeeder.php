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
     * @var array<string, array{name: string, name_fr: string, started: int, chapters: array<int, array{shortname: string, name: string, name_fr: string, zip: string, started: int}>}>
     */
    private array $regions = [
        'brussels-capital-region' => [
            'name' => 'Brussels Capital Region',
            'name_fr' => 'Région de Bruxelles-Capitale',
            'started' => 2020,
            'chapters' => [
                ['shortname' => 'schaarbeek', 'name' => 'Schaarbeek', 'name_fr' => 'Schaerbeek', 'zip' => '1030', 'started' => 2020],
                ['shortname' => 'elsene', 'name' => 'Elsene', 'name_fr' => 'Ixelles', 'zip' => '1050', 'started' => 2020],
                ['shortname' => 'brussel-stad', 'name' => 'Brussel Stad', 'name_fr' => 'Bruxelles-Ville', 'zip' => '1000', 'started' => 2020],
                ['shortname' => 'vorst', 'name' => 'Vorst', 'name_fr' => 'Forest', 'zip' => '1190', 'started' => 2021],
                ['shortname' => 'anderlecht', 'name' => 'Anderlecht', 'name_fr' => 'Anderlecht', 'zip' => '1070', 'started' => 2021],
                // ['shortname' => 'molenbeek', 'name' => 'Molenbeek', 'name_fr' => 'Molenbeek', 'zip' => '1080', 'started' => 2021],
                ['shortname' => 'jette', 'name' => 'Jette', 'name_fr' => 'Jette', 'zip' => '1090', 'started' => 2022],
                ['shortname' => 'etterbeek', 'name' => 'Etterbeek', 'name_fr' => 'Etterbeek', 'zip' => '1040', 'started' => 2022],
                ['shortname' => 'ukkel', 'name' => 'Ukkel', 'name_fr' => 'Uccle', 'zip' => '1180', 'started' => 2022],
                ['shortname' => 'sint-gillis', 'name' => 'Sint-Gillis', 'name_fr' => 'Saint-Gilles', 'zip' => '1060', 'started' => 2022],
                ['shortname' => 'watermaal-bosvoorde', 'name' => 'Watermaal-Bosvoorde', 'name_fr' => 'Watermael-Boitsfort', 'zip' => '1170', 'started' => 2023],
                ['shortname' => 'woluwe', 'name' => 'Woluwe', 'name_fr' => 'Woluwe', 'zip' => '1200', 'started' => 2023],
                ['shortname' => 'laken', 'name' => 'Laken', 'name_fr' => 'Laeken', 'zip' => '1020', 'started' => 2023],
                ['shortname' => 'koekelberg', 'name' => 'Koekelberg', 'name_fr' => 'Koekelberg', 'zip' => '1081', 'started' => 2023],
                ['shortname' => 'evere-haren', 'name' => 'Evere-Haren', 'name_fr' => 'Evere-Haren', 'zip' => '1140', 'started' => 2024],
                ['shortname' => 'neder-over-heembeek', 'name' => 'Neder-Over-Heembeek', 'name_fr' => 'Neder-Over-Heembeek', 'zip' => '1120', 'started' => 2024],
            ],
        ],
        'wallonia' => [
            'name' => 'Wallonia',
            'name_fr' => 'Wallonie',
            'started' => 2022,
            'chapters' => [
                ['shortname' => 'luik', 'name' => 'Luik', 'name_fr' => 'Liège', 'zip' => '4000', 'started' => 2022],
                ['shortname' => 'tubeke', 'name' => 'Tubeke', 'name_fr' => 'Tubize', 'zip' => '1480', 'started' => 2023],
                ['shortname' => 'terhulpen', 'name' => 'Terhulpen', 'name_fr' => 'La Hulpe', 'zip' => '1310', 'started' => 2023],
                ['shortname' => 'namen', 'name' => 'Namen', 'name_fr' => 'Namur', 'zip' => '5000', 'started' => 2024],
                ['shortname' => 'moeskroen', 'name' => 'Moeskroen', 'name_fr' => 'Mouscron', 'zip' => '7700', 'started' => 2024],
                ['shortname' => 'bergen', 'name' => 'Bergen', 'name_fr' => 'Mons', 'zip' => '7000', 'started' => 2025],
            ],
        ],
        'flanders' => [
            'name' => 'Flanders',
            'name_fr' => 'Flandre',
            'started' => 2024,
            'chapters' => [
                ['shortname' => 'gent', 'name' => 'Gent', 'name_fr' => 'Gand', 'zip' => '9000', 'started' => 2024],
                ['shortname' => 'antwerpen', 'name' => 'Antwerpen', 'name_fr' => 'Anvers', 'zip' => '2000', 'started' => 2024],
                ['shortname' => 'leuven', 'name' => 'Leuven', 'name_fr' => 'Louvain', 'zip' => '3000', 'started' => 2025],
                ['shortname' => 'brugge', 'name' => 'Brugge', 'name_fr' => 'Bruges', 'zip' => '8000', 'started' => 2025],
            ],
        ],
    ];

    public function run(): void
    {
        $belgium = Group::create([
            'shortname' => 'belgium',
            'name_nl' => 'Belgium',
            'name_fr' => 'Belgique',
            'zip' => null,
            'invisible' => true,
            'started_at' => '2020-01-01',
        ]);

        foreach ($this->regions as $shortname => $region) {
            $regionGroup = Group::create([
                'shortname' => $shortname,
                'name_nl' => $region['name'],
                'name_fr' => $region['name_fr'],
                'zip' => null,
                'parent_id' => $belgium->id,
                'invisible' => true,
                'started_at' => $region['started'].'-01-01',
            ]);

            foreach ($region['chapters'] as $chapter) {
                Group::create([
                    'shortname' => $chapter['shortname'],
                    'name_nl' => $chapter['name'],
                    'name_fr' => $chapter['name_fr'],
                    'zip' => $chapter['zip'],
                    'parent_id' => $regionGroup->id,
                    'started_at' => $chapter['started'].'-03-01',
                ]);
            }
        }
    }
}

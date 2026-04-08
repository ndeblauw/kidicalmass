<?php

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    private $allGroups;

    private $mainGroups;

    public function run(): void
    {
        $this->createGroupHierarchy();
    }

    private function createGroupHierarchy(): void
    {
        $belgium = Group::create([
            'shortname' => 'belgium',
            'name' => 'Belgium',
            'zip' => null,
            'invisible' => true,
            'started_at' => '2021-01-01',
            'ended_at' => null,
        ]);

        $regions = [
            ['shortname' => 'flanders', 'name' => 'Flanders', 'zip' => null],
            ['shortname' => 'brussels-capital-region', 'name' => 'Brussels Capital Region', 'zip' => null],
            ['shortname' => 'wallonia', 'name' => 'Wallonia', 'zip' => null],
        ];

        $regionGroups = [];
        foreach ($regions as $region) {
            $regionGroups[$region['shortname']] = Group::create([
                'shortname' => $region['shortname'],
                'name' => $region['name'],
                'zip' => $region['zip'],
                'parent_id' => $belgium->id,
                'invisible' => true,
                'started_at' => '2021-01-01',
                'ended_at' => null,
            ]);
        }

        $subgroupData = [
            'flanders' => [
                ['shortname' => 'mechelen', 'name' => 'Mechelen', 'zip' => '2800'],
            ],
            'brussels-capital-region' => [
                ['shortname' => 'bruxelles-ville-brussel-stad', 'name' => 'Bruxelles Ville - Brussel Stad', 'zip' => '1000'],
                ['shortname' => 'anderlecht', 'name' => 'Anderlecht', 'zip' => '1070'],
                ['shortname' => 'schaerbeek-schaarbeek', 'name' => 'Schaerbeek - Schaarbeek', 'zip' => '1030'],
                ['shortname' => 'forest-vorst', 'name' => 'Forest - Vorst', 'zip' => '1190'],
                ['shortname' => 'watermael-boitsfort-auderghem', 'name' => 'Watermael-Boitsfort - Watermaal-Bosvoorde & Auderghem - Oudergem', 'zip' => '1170'],
                ['shortname' => 'woluwe-saint-pierre-woluwe-saint-lambert', 'name' => 'Woluwe-Saint-Pierre - Woluwe-Saint-Lambert / Woluwe-Sint-Pieters - Woluwe-Sint-Lambrechts', 'zip' => '1150'],
                ['shortname' => 'evere-haren', 'name' => 'Evere - Haren', 'zip' => '1140'],
                ['shortname' => 'ixelles-elsene', 'name' => 'Ixelles - Elsene', 'zip' => '1050'],
                ['shortname' => 'neder-over-heembeek', 'name' => 'Neder-Over-Heembeek', 'zip' => '1120'],
                ['shortname' => 'etterbeek', 'name' => 'Etterbeek', 'zip' => '1040'],
                ['shortname' => 'laeken-laken', 'name' => 'Laeken - Laken', 'zip' => '1020'],
                ['shortname' => 'koekelberg-berchem-ganshoren', 'name' => 'Koekelberg / Berchem / Ganshoren', 'zip' => '1080'],
                ['shortname' => 'uccle-ukkel', 'name' => 'Uccle - Ukkel', 'zip' => '1180'],
                ['shortname' => 'jette', 'name' => 'Jette', 'zip' => '1090'],
            ],
            'wallonia' => [
                ['shortname' => 'mons', 'name' => 'Mons', 'zip' => '7000'],
                ['shortname' => 'namur', 'name' => 'Namur', 'zip' => '5000'],
            ],
        ];

        $this->allGroups = collect([$belgium]);
        $this->mainGroups = collect($regionGroups);

        foreach ($regionGroups as $regionShortname => $regionGroup) {
            foreach ($subgroupData[$regionShortname] as $subgroup) {
                $group = Group::create([
                    'shortname' => $subgroup['shortname'],
                    'name' => $subgroup['name'],
                    'zip' => $subgroup['zip'],
                    'parent_id' => $regionGroup->id,
                    'started_at' => '2021-01-01',
                    'ended_at' => null,
                ]);
                $this->allGroups->push($group);
            }
        }

        $this->allGroups = $this->allGroups->merge($this->mainGroups);
    }

    public function getAllGroups(): \Illuminate\Support\Collection
    {
        return $this->allGroups ?? collect();
    }

    public function getMainGroups(): \Illuminate\Support\Collection
    {
        return $this->mainGroups ?? collect();
    }

    private function info(string $message): void
    {
        $this->command?->info($message);
    }
}

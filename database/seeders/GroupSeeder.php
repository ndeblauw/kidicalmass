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
            'started_at' => $this->randomStartDate(),
            'ended_at' => $this->randomEndDate(),
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
                'started_at' => $this->randomStartDate(),
                'ended_at' => $this->randomEndDate(),
            ]);
        }

        $subgroupData = [
            'flanders' => [
                ['shortname' => 'antwerp', 'name' => 'Antwerp', 'zip' => '2000'],
                ['shortname' => 'gent', 'name' => 'Gent', 'zip' => '9000'],
                ['shortname' => 'leuven', 'name' => 'Leuven', 'zip' => '3000'],
                ['shortname' => 'hasselt', 'name' => 'Hasselt', 'zip' => '3500'],
                ['shortname' => 'brugge', 'name' => 'Brugge', 'zip' => '8000'],
            ],
            'brussels-capital-region' => [
                ['shortname' => 'brussels', 'name' => 'Brussels', 'zip' => '1000'],
                ['shortname' => 'ixelles', 'name' => 'Ixelles', 'zip' => '1050'],
                ['shortname' => 'anderlecht', 'name' => 'Anderlecht', 'zip' => '1070'],
                ['shortname' => 'schaerbeek', 'name' => 'Schaerbeek', 'zip' => '1030'],
                ['shortname' => 'sint-joost-ten-node', 'name' => 'Sint-Joost-ten-Node', 'zip' => '1210'],
                ['shortname' => 'molenbeek', 'name' => 'Molenbeek', 'zip' => '1080'],
            ],
            'wallonia' => [
                ['shortname' => 'mons', 'name' => 'Mons', 'zip' => '7000'],
                ['shortname' => 'liege', 'name' => 'Liège', 'zip' => '4000'],
                ['shortname' => 'arlon', 'name' => 'Arlon', 'zip' => '6700'],
                ['shortname' => 'namur', 'name' => 'Namur', 'zip' => '5000'],
                ['shortname' => 'nivelles', 'name' => 'Nivelles', 'zip' => '1300'],
            ],
        ];

        $this->allGroups = collect([$belgium]);
        $this->mainGroups = collect($regionGroups);

        foreach ($regionGroups as $regionShortname => $regionGroup) {
            $availableSubgroups = $subgroupData[$regionShortname];
            $subgroupCount = rand(1, 5);
            $selectedSubgroups = collect($availableSubgroups)->random($subgroupCount);

            foreach ($selectedSubgroups as $subgroup) {
                $group = Group::create([
                    'shortname' => $subgroup['shortname'],
                    'name' => $subgroup['name'],
                    'zip' => $subgroup['zip'],
                    'parent_id' => $regionGroup->id,
                    'started_at' => $this->randomStartDate(),
                    'ended_at' => $this->randomEndDate(),
                ]);
                $this->allGroups->push($group);
            }
        }

        $this->allGroups = $this->allGroups->merge($this->mainGroups);
    }

    private function randomStartDate(): ?string
    {
        $startDate = now()->subYears(rand(0, now()->year - 2021));

        return $startDate->format('Y-m-d');
    }

    private function randomEndDate(): ?string
    {
        if (rand(1, 100) <= 5) {
            return now()->subDays(rand(1, 365))->format('Y-m-d');
        }

        return null;
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

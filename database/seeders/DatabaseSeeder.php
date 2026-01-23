<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Article;
use App\Models\Group;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    private $allGroups;
    private $mainGroups;

    public function run(): void
    {
        $this->createGroupHierarchy();
        $this->seedUsers();
        $this->seedArticles();
        $this->seedActivities();
    }

    private function createGroupHierarchy(): void
    {
        $belgium = Group::create([
            'shortname' => 'belgium',
            'name' => 'Belgium',
            'zip' => null,
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
                ]);
                $this->allGroups->push($group);
            }
        }

        $this->allGroups = $this->allGroups->merge($this->mainGroups);
    }

    private function seedUsers(): void
    {
        $this->allGroups->each(function (Group $group) {
            $userCount = rand(1, 3);
            User::factory($userCount)->create()->each(function (User $user) use ($group) {
                $user->groups()->attach($group->id);
            });
        });
    }

    private function seedArticles(): void
    {
        $this->mainGroups->each(function (Group $group) {
            $articleCount = rand(3, 8);
            Article::factory($articleCount)->create()->each(function (Article $article) use ($group) {
                $article->groups()->attach($group->id);
            });
        });

        $subgroups = $this->allGroups->diff($this->mainGroups);
        $selectedSubgroups = $subgroups->random(min(3, $subgroups->count()));

        $selectedSubgroups->each(function (Group $group) {
            $articleCount = rand(1, 4);
            Article::factory($articleCount)->create()->each(function (Article $article) use ($group) {
                $article->groups()->attach($group->id);
            });
        });
    }

    private function seedActivities(): void
    {
        $this->mainGroups->each(function (Group $group) {
            $activityCount = rand(2, 6);
            Activity::factory($activityCount)->create()->each(function (Activity $activity) use ($group) {
                $activity->groups()->attach($group->id);
            });
        });

        $subgroups = $this->allGroups->diff($this->mainGroups);
        $selectedSubgroups = $subgroups->random(min(2, $subgroups->count()));

        $selectedSubgroups->each(function (Group $group) {
            $activityCount = rand(1, 3);
            Activity::factory($activityCount)->create()->each(function (Activity $activity) use ($group) {
                $activity->groups()->attach($group->id);
            });
        });
    }
}

<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Article;
use App\Models\ContactForm;
use App\Models\Group;
use App\Models\Partner;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    private $allGroups;

    private $mainGroups;

    public function run(): void
    {
        // Seed groups first
        $groupSeeder = new GroupSeeder;
        $groupSeeder->run();
        $this->allGroups = $groupSeeder->getAllGroups();
        $this->mainGroups = $groupSeeder->getMainGroups();

        $this->seedUsers();
        $this->seedArticles();
        $this->seedActivities();
        $this->seedContactForms();
        $this->seedPartners();

        // Clean up temporary images
        \Database\Factories\ArticleFactory::cleanupTempImages();
        \Database\Factories\ActivityFactory::cleanupTempImages();
    }

    private function seedUsers(): void
    {
        User::create([
            'name' => 'Nico Deblauwe',
            'email' => 'nico@deblauwe.be',
            'password' => '$2y$12$caY7UhzzouF4BRc7rxg1eOndYSP1VhBWrgU6UxZ9cN7QhIel6DKHa',
        ]);

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

    private function seedContactForms(): void
    {
        ContactForm::factory(10)->create();
    }

    private function seedPartners()
    {
        // Create partners
        $groups = Group::inRandomOrder()->take(5)->get();
        foreach (range(1, 15) as $i) {
            Partner::factory()->create([
                'group_id' => $groups->random()->id,
            ]);
        }
    }
}

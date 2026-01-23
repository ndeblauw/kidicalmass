<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create some test users
        $user1 = \App\Models\User::firstOrCreate(
            ['email' => 'author1@example.com'],
            [
                'name' => 'Test Author 1',
                'password' => bcrypt('password'),
            ]
        );

        $user2 = \App\Models\User::firstOrCreate(
            ['email' => 'author2@example.com'],
            [
                'name' => 'Test Author 2',
                'password' => bcrypt('password'),
            ]
        );

        // Create some groups
        $groupA = \App\Models\Group::firstOrCreate(
            ['shortname' => 'group-a'],
            ['name' => 'Group A']
        );

        $groupB = \App\Models\Group::firstOrCreate(
            ['shortname' => 'group-b'],
            ['name' => 'Group B']
        );

        $groupC = \App\Models\Group::firstOrCreate(
            ['shortname' => 'group-c'],
            [
                'name' => 'Group C',
                'parent_id' => $groupA->id,
            ]
        );

        // Create some articles
        $article1 = \App\Models\Article::create([
            'title_nl' => 'Eerste artikel',
            'title_fr' => 'Premier article',
            'content_nl' => 'Dit is de inhoud van het eerste artikel in het Nederlands.',
            'content_fr' => 'Ceci est le contenu du premier article en français.',
            'author_id' => $user1->id,
        ]);
        $article1->groups()->attach([$groupA->id, $groupB->id]);

        $article2 = \App\Models\Article::create([
            'title_nl' => 'Tweede artikel',
            'title_fr' => 'Deuxième article',
            'content_nl' => 'Dit is de inhoud van het tweede artikel in het Nederlands.',
            'content_fr' => 'Ceci est le contenu du deuxième article en français.',
            'author_id' => $user2->id,
        ]);
        $article2->groups()->attach([$groupB->id]);

        // Create some activities
        $activity1 = \App\Models\Activity::create([
            'title_nl' => 'Eerste activiteit',
            'title_fr' => 'Première activité',
            'content_nl' => 'Beschrijving van de eerste activiteit.',
            'content_fr' => 'Description de la première activité.',
            'begin_date' => now()->addDays(7),
            'end_date' => now()->addDays(7)->addHours(3),
            'location' => 'Brussels',
            'author_id' => $user1->id,
        ]);
        $activity1->groups()->attach([$groupA->id]);

        $activity2 = \App\Models\Activity::create([
            'title_nl' => 'Tweede activiteit',
            'title_fr' => 'Deuxième activité',
            'content_nl' => 'Beschrijving van de tweede activiteit.',
            'content_fr' => 'Description de la deuxième activité.',
            'begin_date' => now()->addDays(14),
            'end_date' => now()->addDays(14)->addHours(2),
            'location' => 'Antwerp',
            'author_id' => $user2->id,
        ]);
        $activity2->groups()->attach([$groupB->id, $groupC->id]);

        $activity3 = \App\Models\Activity::create([
            'title_nl' => 'Afgelopen activiteit',
            'title_fr' => 'Activité passée',
            'content_nl' => 'Dit is een activiteit in het verleden.',
            'content_fr' => 'Ceci est une activité dans le passé.',
            'begin_date' => now()->subDays(7),
            'end_date' => now()->subDays(7)->addHours(2),
            'location' => 'Ghent',
            'author_id' => $user1->id,
        ]);
        $activity3->groups()->attach([$groupA->id, $groupC->id]);
    }
}

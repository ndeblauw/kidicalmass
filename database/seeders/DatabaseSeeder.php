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
use Symfony\Component\Console\Terminal;

class DatabaseSeeder extends Seeder
{
    private $allGroups;

    private $mainGroups;

    public function run(): void
    {
        $this->command->info('Starting database seeding...');

        // Seed groups first
        $groupSeeder = new GroupSeeder;
        $groupSeeder->run();
        $this->allGroups = $groupSeeder->getAllGroups();
        $this->mainGroups = $groupSeeder->getMainGroups();

        // $this->seedUsers();
        // $this->seedArticles();
        // $this->seedActivities();
        $this->seedContactForms();
        $this->seedPartners();

        $this->task('Cleanup temporary images', function () {
            \Database\Factories\ArticleFactory::cleanupTempImages();
            \Database\Factories\ActivityFactory::cleanupTempImages();
        });
    }

    private function seedUsers(): void
    {
        $this->command->line('Seeding users...');

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

        $this->command->line('<info>✓</info> Users seeded successfully');
    }

    private function seedArticles(int $nr = 40): void
    {
        $this->command->line('Seeding articles...');

        $bar = $this->command->getOutput()->createProgressBar($nr);
        $bar->start();

        foreach (range(1, $nr) as $i) {
            $groups = ($i <= 15)
                ? [$this->mainGroups->random()->id]
                : ($subgroups = $this->allGroups->diff($this->mainGroups))->random(min(3, $subgroups->count()))->pluck('id')->toArray();

            Article::factory()->create()->groups()->attach($groups);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();

        $this->command->line('<info>✓</info> Articles seeded successfully');
    }

    private function seedActivities(int $nr = 30): void
    {
        $this->command->line('Seeding activities...');

        $bar = $this->command->getOutput()->createProgressBar($nr);
        $bar->start();

        foreach (range(1, $nr) as $i) {
            $groups = ($i <= 15)
                ? [$this->mainGroups->random()->id]
                : ($subgroups = $this->allGroups->diff($this->mainGroups))->random(min(3, $subgroups->count()))->pluck('id')->toArray();

            Activity::factory()->create()->groups()->attach($groups);
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();

        $this->command->line('<info>✓</info> Activities seeded successfully');
    }

    private function seedContactForms(): void
    {
        $this->task('Seeding contact forms', function () {
            ContactForm::factory(10)->create();
        });
    }

    private function seedPartners()
    {
        $this->task('Seeding partners', function () {

            // Create partners
            $groups = Group::inRandomOrder()->take(5)->get();
            foreach (range(1, 15) as $i) {
                Partner::factory()->create([
                    'group_id' => $groups->random()->id,
                ]);
            }
        });
    }

    protected function task(string $label, callable $callback): void
    {
        $output = $this->command->getOutput();

        $terminalWidth = (new Terminal)->getWidth() - 2 ?: 80;

        $minDots = 3;

        $labelLen = mb_strlen($label);
        $statusLen = 6;

        $dots = max($minDots, $terminalWidth - $labelLen - $statusLen) - 2;

        $output->write('  '.$label);
        $output->write(str_repeat('.', $dots));

        try {
            $callback();
            $output->writeln('. <info>DONE</info>');
        } catch (\Throwable $e) {
            $output->writeln(' <error>ERROR</error>');
            throw $e;
        }

    }
}

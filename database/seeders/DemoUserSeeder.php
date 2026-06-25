<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $schaarbeek = Group::firstOrCreate(
            ['shortname' => 'schaarbeek'],
            ['name' => 'Schaarbeek', 'started_at' => now()],
        );

        $user = User::updateOrCreate(
            ['email' => 'user@kidi.be'],
            ['name' => 'Wim Aerts', 'password' => bcrypt('password'), 'email_verified_at' => now(), 'superadmin' => false],
        );
        $user->groups()->syncWithoutDetaching([$schaarbeek->id => ['role' => null]]);

        $pinkvest = User::updateOrCreate(
            ['email' => 'pinkvest@kidi.be'],
            ['name' => 'Lien Govaerts', 'password' => bcrypt('password'), 'email_verified_at' => now(), 'superadmin' => false],
        );
        $pinkvest->groups()->syncWithoutDetaching([$schaarbeek->id => ['role' => 'pinkvest']]);

        $captain = User::updateOrCreate(
            ['email' => 'captain@kidi.be'],
            ['name' => 'Joris De Smet', 'password' => bcrypt('password'), 'email_verified_at' => now(), 'superadmin' => false],
        );
        $captain->groups()->syncWithoutDetaching([$schaarbeek->id => ['role' => 'captain']]);

        User::updateOrCreate(
            ['email' => 'admin@kidi.be'],
            ['name' => 'Admin User', 'password' => bcrypt('password'), 'email_verified_at' => now(), 'superadmin' => true],
        );
    }
}

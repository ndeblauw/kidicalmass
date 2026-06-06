<?php

namespace Database\Seeders;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Demo data for the pink-vest onboarding prototype (Mon 8 June, Leticia).
 *
 * Self-contained: creates the Oudergem (1160) chapter — which is NOT in the canonical
 * GroupSeeder directory — plus a new pink-vest volunteer (Morgane), a chapter lead
 * (Thomas), a small roster, and a couple of upcoming activities. Idempotent-ish:
 * uses updateOrCreate / syncWithoutDetaching so re-running won't duplicate.
 *
 * See docs/superpowers/specs/2026-06-06-pink-vest-onboarding-prototype-design.md
 */
class PrototypeOnboardingSeeder extends Seeder
{
    public function run(): void
    {
        $region = Group::where('name', 'Brussels Capital Region')->first();

        $oudergem = Group::updateOrCreate(
            ['shortname' => 'oudergem'],
            [
                'name' => 'Oudergem',
                'zip' => '1160',
                'parent_id' => $region?->id,
                'started_at' => '2023-01-01',
                'invisible' => false,
            ],
        );

        // The new pink vest we onboard in the demo.
        $morgane = User::updateOrCreate(
            ['email' => 'morgane@example.test'],
            [
                'name' => 'Morgane Vurpas',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        // The chapter lead (placeholder name).
        $thomas = User::updateOrCreate(
            ['email' => 'thomas@example.test'],
            [
                'name' => 'Thomas Maes',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        // A handful of fellow volunteers for the roster.
        $roster = collect([
            'Lien De Smet',
            'Karim Benali',
            'Sophie Janssens',
            'Pieter Vermeulen',
            'Amina Haddad',
        ])->map(fn (string $name, int $i) => User::updateOrCreate(
            ['email' => 'vol'.$i.'@example.test'],
            ['name' => $name, 'password' => Hash::make('password'), 'email_verified_at' => now()],
        ));

        // Attach everyone to Oudergem. is_public mirrors the roster opt-in (D-1, Decision C):
        // a few volunteers chose to show themselves publicly; Morgane has not yet.
        $oudergem->users()->syncWithoutDetaching([
            $thomas->id => ['is_public' => true],
            $morgane->id => ['is_public' => false],
        ]);
        foreach ($roster as $i => $user) {
            $oudergem->users()->syncWithoutDetaching([
                $user->id => ['is_public' => $i % 2 === 0],
            ]);
        }

        // Upcoming activities for the chapter: the next family ride + a volunteer meetup.
        $nextRide = Activity::updateOrCreate(
            ['title_nl' => 'Kidical Mass Oudergem — septemberrit', 'author_id' => $thomas->id],
            [
                'title_fr' => 'Kidical Mass Auderghem — balade de septembre',
                'content_nl' => 'Onze maandelijkse fietstocht door Oudergem. Rustig tempo, kindvriendelijke route, en veel goesting. Roze hesjes verzamelen tien minuten voor de start.',
                'content_fr' => 'Notre balade mensuelle à Auderghem.',
                'activity_type' => ActivityType::KIDICALMASS,
                'begin_date' => Carbon::parse('next sunday')->setTime(14, 30),
                'location' => 'Plein Pinoy, 1160 Oudergem',
                'postal_code' => '1160',
                'distance' => '5 km',
                'duration_minutes' => 90,
            ],
        );

        $meetup = Activity::updateOrCreate(
            ['title_nl' => 'Vrijwilligersmeetup Oudergem', 'author_id' => $thomas->id],
            [
                'title_fr' => 'Rencontre des bénévoles Auderghem',
                'content_nl' => 'We bespreken de najaarsritten en delen een hapje en een drankje. Welkom voor alle hesjes.',
                'content_fr' => 'On prépare les balades d’automne.',
                'activity_type' => ActivityType::MEETING,
                'begin_date' => Carbon::parse('next sunday')->addDays(2)->setTime(20, 0),
                'location' => 'Café Oud Auderghem, 1160 Oudergem',
                'postal_code' => '1160',
                'duration_minutes' => 90,
            ],
        );

        $oudergem->activities()->syncWithoutDetaching([$nextRide->id, $meetup->id]);
    }
}

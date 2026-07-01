<?php

namespace Database\Seeders;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Fills the roze-hesje hub of the demo chapter (Schaarbeek) so no sub-page lands empty:
 *
 *  - galleries on every past ride, so Foto's has several rides to switch between;
 *  - a couple of draft (unpublished) upcoming rides, so Agenda leads with work-in-progress;
 *  - a roster timeline where one member is genuinely "new", so De Groep + the feed have a
 *    real fresh face instead of marking everyone new.
 *
 * Non-production and idempotent: re-runnable without piling up. Runs last in DatabaseSeeder,
 * after ChapterRideGallerySeeder has dressed each chapter's latest ride.
 */
class RozeHesjeDemoSeeder extends Seeder
{
    private const CHAPTER = 'schaarbeek';

    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->warn('RozeHesjeDemoSeeder: refusing to run in production.');

            return;
        }

        $group = Group::where('shortname', self::CHAPTER)->first();

        if (! $group) {
            $this->command?->warn('RozeHesjeDemoSeeder: chapter "'.self::CHAPTER.'" not found, skipping.');

            return;
        }

        $this->seedPastRideGalleries($group);
        $this->seedDraftRides($group);
        $this->seedRosterTimeline($group);
    }

    /**
     * Give every past ride a gallery so the Foto's dropdown has more than one album. The
     * latest ride is already dressed by ChapterRideGallerySeeder, so leave galleries that
     * already exist untouched (keeps both seeders idempotent and non-overlapping).
     */
    private function seedPastRideGalleries(Group $group): void
    {
        $gallery = new ChapterRideGallerySeeder;

        $pastRides = Activity::query()
            ->whereHas('groups', fn ($query) => $query->whereKey($group->id))
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->where('begin_date', '<', now())
            ->orderByDesc('begin_date')
            ->get();

        // Older rides get fuller / leaner albums on purpose, to exercise the wall's
        // cover + tile caps at different sizes as you page back through the season.
        $counts = [12, 6, 9, 5];
        $offset = 16;

        foreach ($pastRides as $index => $ride) {
            if ($ride->hasGallery()) {
                continue;
            }

            $count = $counts[$index] ?? 6;
            $gallery->seedRide($ride, $count, $offset);
            $offset += $count;
        }
    }

    /**
     * Two rides the captains are still preparing (published = 0). These never reach the
     * public agenda, but the hub shows them under "In voorbereiding" so a hesje can watch
     * a ride take shape — the onboarding-by-visibility ladder (kijken → meedoen → kapitein).
     */
    private function seedDraftRides(Group $group): void
    {
        $author = $group->users()->wherePivot('role', 'captain')->first()
            ?? $group->users()->first();

        $drafts = [
            [
                'title_nl' => 'Zomeravondrit langs het Josaphatpark',
                'title_fr' => 'Balade du soir le long du parc Josaphat',
                'begin_date' => now()->addDays(18)->setTime(18, 30),
                'location' => 'Josaphatpark, Schaarbeek',
            ],
            [
                'title_nl' => 'Halloween-tochtje met lichtjes',
                'title_fr' => 'Petite virée d\'Halloween avec lumières',
                'begin_date' => now()->addMonths(2)->setTime(17, 0),
                'location' => 'Gemeenteplein, Schaarbeek',
            ],
        ];

        foreach ($drafts as $draft) {
            $activity = Activity::updateOrCreate(
                ['title_nl' => $draft['title_nl']],
                [
                    'title_fr' => $draft['title_fr'],
                    'content_nl' => 'De route ligt grotendeels vast. Datum en communicatie worden nog afgewerkt voor de rit publiek gaat.',
                    'content_fr' => 'L\'itinéraire est presque fixé. La date et la communication sont encore en préparation avant l\'annonce publique.',
                    'activity_type' => ActivityType::KIDICALMASS,
                    'begin_date' => $draft['begin_date'],
                    'location' => $draft['location'],
                    'author_id' => $author?->id,
                    'published' => false,
                ],
            );

            $activity->groups()->syncWithoutDetaching([$group->id]);
        }
    }

    /**
     * Stretch the roster across the season so the "Nieuw" marker means something: everyone
     * who was already seeded is backdated well past the welcome window, and one extra hesje
     * (Sara) joins five days ago — the fresh face the overview feed points to.
     */
    private function seedRosterTimeline(Group $group): void
    {
        DB::table('group_user')
            ->where('group_id', $group->id)
            ->update([
                'created_at' => now()->subMonths(5),
                'updated_at' => now()->subMonths(5),
            ]);

        $sara = User::updateOrCreate(
            ['email' => 'sara@kidi.be'],
            [
                'name' => 'Sara Janssens',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'superadmin' => false,
            ],
        );

        $sara->groups()->syncWithoutDetaching([$group->id => ['role' => 'pinkvest']]);

        DB::table('group_user')
            ->where('group_id', $group->id)
            ->where('user_id', $sara->id)
            ->update([
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ]);
    }
}

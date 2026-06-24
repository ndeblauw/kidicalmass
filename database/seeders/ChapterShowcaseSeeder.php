<?php

namespace Database\Seeders;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\Partner;
use App\Models\PressArticle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

/**
 * Dresses a handful of real chapters into deliberate content variations so the
 * chapter page (P-11, groups/show.blade.php) can be eyeballed in every state
 * without clicking around the admin. Non-production only.
 *
 * Runs AFTER DatabaseSeeder's groups/activities/partners, and only enriches —
 * it never undoes the base demo data. Re-runnable: chapter-scoped partners and
 * press links are reset for the chapters it owns, so `db:seed` twice is a no-op
 * rather than a pile-up.
 *
 * Variation map (visit /nl/lokale-groepen/<slug>):
 *
 *   schaarbeek   EVERYTHING — a hero cover, a ride + workshop + meeting on the
 *                agenda, 4 friends (text links, some with a url, some without) and
 *                3 press items (with PDF + link, link only, plain). The "In beeld"
 *                wall (latest ride's photos) is seeded by ChapterRideGallerySeeder.
 *   namen        Press only — has rides from the base seed, no partners, no
 *                gallery: exercises the single-column extras layout.
 *   bergen       Text-fallback partner — Avello (show_logo on, no logo file) from
 *                the base seed renders as a name, plus an upcoming ride.
 *   anderlecht   Workshop, no ride — the warm "nog geen fietstocht" empty-ride
 *                note above a correctly typed workshop. No extras. (base seed)
 *   koekelberg   Bare — no agenda, no extras, no team: the full empty state.
 *                (left untouched on purpose)
 */
class ChapterShowcaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command?->warn('ChapterShowcaseSeeder: refusing to run in production.');

            return;
        }

        $this->showcaseSchaarbeek();
        $this->pressOnlyNamen();
    }

    /**
     * The everything-on chapter: gallery, a full agenda (ride + workshop + meeting),
     * partners in all four render shapes, and press in all three.
     */
    private function showcaseSchaarbeek(): void
    {
        $group = $this->chapter('schaarbeek');

        if (! $group) {
            return;
        }

        // Hero cover — the group's own identity photo: a posed group shot from a
        // Schaarbeek ride (cover first), via the tested command (it clears the
        // collection first, so this stays idempotent). Only the first gallery photo
        // surfaces, as the hero cover; the rest round out the collection. The "In
        // beeld" wall (the latest ride's photos) is owned by ChapterRideGallerySeeder.
        $coverFirst = [
            'ride-group-photo-bandstand.webp',        // hero cover: the group posed together at the kiosk
            'ride-group-celebration-station.webp',
            'ride-brussels-boulevard-crowd.webp',
            'ride-crowd-intersection.webp',
            'ride-park-crowd-cheering-namur.webp',
            'ride-brussels-street-crowd-portrait.webp',
        ];

        Artisan::call('dev:seed-group-gallery', [
            '--group' => [$group->id],
            '--source' => array_map(
                fn (string $file): string => public_path("img/photography/{$file}"),
                $coverFirst,
            ),
            '--count' => count($coverFirst),
        ]);

        // A second real trekker so the team carousel shows more than one face.
        $cohost = User::firstOrCreate(
            ['email' => 'sofie@kidicalmass.be'],
            ['name' => 'Sofie Maes', 'password' => bcrypt('password')],
        );
        $group->users()->syncWithoutDetaching([$cohost->id]);

        // Agenda: a workshop and a meeting alongside the rides the base seed already
        // attached, so all three activity types show on one page.
        $this->ensureActivity($group, [
            'title_nl' => 'Fietscheck & sleutelworkshop Schaarbeek',
            'title_fr' => 'Atelier vélo & petites réparations Schaerbeek',
            'type' => ActivityType::WORKSHOP,
            'begin_date' => now()->addWeeks(1)->setTime(10, 0),
            'location' => 'Cyclo werkplaats, Schaarbeek',
            'postal_code' => '1030',
            'duration' => 120,
        ]);
        $this->ensureActivity($group, [
            'title_nl' => 'Vrijwilligersmeeting Schaarbeek',
            'title_fr' => 'Réunion des bénévoles Schaerbeek',
            'type' => ActivityType::MEETING,
            'begin_date' => now()->addWeeks(2)->setTime(19, 30),
            'location' => 'Maison des Citoyens, Schaarbeek',
            'postal_code' => '1030',
            'duration' => 90,
        ]);

        // Give the chapter's next ride a real GPX route so the next-ride card (§2) draws
        // an actual map (parity with the ride detail page) instead of the faux placeholder.
        $this->attachRouteToNextRide($group, 50.8676, 4.3735); // Place Colignon, Schaarbeek

        // Friends — text links (the chapter page never renders logos). Most carry a
        // url; "Buurtcomité Josaphat" has none, so it renders as plain text.
        $this->resetChapterPartners($group);
        $this->partner($group, 'Cyclo', 'https://www.cyclo.org',
            'Fietspuntwerking om de hoek voor herstellingen en onderhoud.');
        $this->partner($group, 'Pro Velo', 'https://www.provelo.org',
            'Fietslessen en begeleide ritten voor jong en oud in de buurt.');
        $this->partner($group, 'Fietsbieb Schaarbeek', 'https://www.fietsbieb.be',
            'Uitleendienst waar kinderen een fiets op maat lenen tegen een kleine bijdrage.');
        $this->partner($group, 'Buurtcomité Josaphat', null,
            'Bewonersgroep die mee de straten kleurt op de dag van de rit.');

        // Press — with PDF + link, link only, plain.
        $withPdf = $this->press($group, [
            'title_nl' => 'Schaarbeek fietst massaal mee met Kidical Mass',
            'title_fr' => 'Schaerbeek roule en masse avec Kidical Mass',
            'outlet' => 'BRUZZ',
            'url' => 'https://www.bruzz.be/mobiliteit/kidical-mass-schaarbeek',
            'published_at' => now()->subWeeks(3),
        ]);
        $this->attachDocument($withPdf, 'kidical-mass-sponsorformules.pdf');

        $this->press($group, [
            'title_nl' => 'Roze hesjes veroveren het Colignonplein',
            'title_fr' => 'Les gilets roses prennent la place Colignon',
            'outlet' => 'Het Nieuwsblad',
            'url' => 'https://www.nieuwsblad.be/schaarbeek-kidical-mass',
            'published_at' => now()->subMonths(4),
        ]);

        $this->press($group, [
            'title_nl' => 'Een wijk leert samen fietsen',
            'title_fr' => 'Un quartier apprend à rouler ensemble',
            'outlet' => 'BX1',
            'url' => null,
            'published_at' => now()->subMonths(9),
        ]);
    }

    /**
     * A chapter that has rides but only press as extras — the single-column
     * "In de pers" layout with no partners beside it.
     */
    private function pressOnlyNamen(): void
    {
        $group = $this->chapter('namen');

        if (! $group) {
            return;
        }

        $this->press($group, [
            'title_nl' => 'Namen rijdt met het gezin door het centrum',
            'title_fr' => 'Namur roule en famille à travers le centre',
            'outlet' => 'Vivacité',
            'url' => 'https://www.rtbf.be/vivacite/namur-kidical-mass',
            'published_at' => now()->subWeeks(6),
        ]);

        $this->press($group, [
            'title_nl' => 'De parade groeit jaar na jaar in Namen',
            'title_fr' => 'La parade grandit année après année à Namur',
            'outlet' => 'Le Soir',
            'url' => null,
            'published_at' => now()->subMonths(7),
        ]);
    }

    private function chapter(string $shortname): ?Group
    {
        $group = Group::where('shortname', $shortname)->first();

        if (! $group) {
            $this->command?->warn("ChapterShowcaseSeeder: chapter '{$shortname}' not found, skipping.");
        }

        return $group;
    }

    /**
     * Remove the chapter-scoped partners this seeder owns so a re-run replaces
     * rather than duplicates them. National (groupless) partners are untouched.
     */
    private function resetChapterPartners(Group $group): void
    {
        Partner::where('group_id', $group->id)->get()->each->delete();
    }

    private function partner(Group $group, string $name, ?string $url, string $descriptionNl): void
    {
        Partner::factory()->create([
            'group_id' => $group->id,
            'name' => $name,
            'url' => $url,
            'description_nl' => $descriptionNl,
            'description_fr' => $descriptionNl,
            'show_logo' => false,
            'visible' => true,
        ]);
    }

    /**
     * @param  array{title_nl: string, title_fr: string, outlet: string, url: ?string, published_at: Carbon}  $data
     */
    private function press(Group $group, array $data): PressArticle
    {
        $article = PressArticle::firstOrCreate(
            ['title_nl' => $data['title_nl']],
            [
                'title_fr' => $data['title_fr'],
                'outlet' => $data['outlet'],
                'url' => $data['url'],
                'published_at' => $data['published_at'],
            ],
        );

        $article->groups()->syncWithoutDetaching([$group->id]);

        return $article;
    }

    /**
     * Attach a sample PDF from public/downloads as the press article's scan,
     * if the file exists and one is not already attached.
     */
    private function attachDocument(PressArticle $article, string $filename): void
    {
        if ($article->getFirstMedia('document')) {
            return;
        }

        $path = public_path("downloads/{$filename}");

        if (! is_file($path)) {
            $this->command?->warn("ChapterShowcaseSeeder: document '{$filename}' not found, skipping PDF.");

            return;
        }

        $article->addMedia($path)->preservingOriginal()->toMediaCollection('document');
    }

    /**
     * Create an agenda activity for the chapter unless one with the same title
     * already exists (keeps re-runs from piling up rides).
     *
     * @param  array{title_nl: string, title_fr: string, type: ActivityType, begin_date: Carbon, location: string, postal_code: string, duration: int}  $data
     */
    private function ensureActivity(Group $group, array $data): Activity
    {
        $existing = Activity::where('title_nl', $data['title_nl'])->first();

        if ($existing) {
            return $existing;
        }

        $author = $group->users()->first() ?? User::query()->first();

        $activity = Activity::factory()->create([
            'title_nl' => $data['title_nl'],
            'title_fr' => $data['title_fr'],
            'content_nl' => 'Een lokale activiteit van Kidical Mass '.$group->name.'.',
            'content_fr' => 'Une activité locale de Kidical Mass '.$group->name.'.',
            'activity_type' => $data['type'],
            'begin_date' => $data['begin_date'],
            'location' => $data['location'],
            'postal_code' => $data['postal_code'],
            'duration_minutes' => $data['duration'],
            'komoot_url' => null,
            'author_id' => $author?->id,
        ]);

        $activity->groups()->attach($group->id);

        return $activity;
    }

    /**
     * Attach a GPX route to the chapter's soonest upcoming Kidical Mass ride (the one
     * the next-ride card features), so its map renders. Reuses the base-seed ride when
     * present; creates a headline ride otherwise (e.g. an isolated test).
     */
    private function attachRouteToNextRide(Group $group, float $lat, float $lng): void
    {
        $nextRide = Activity::query()
            ->whereHas('groups', fn ($query) => $query->where('groups.id', $group->id))
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date')
            ->first();

        $nextRide ??= $this->ensureActivity($group, [
            'title_nl' => 'Kidical Mass Schaarbeek',
            'title_fr' => 'Kidical Mass Schaerbeek',
            'type' => ActivityType::KIDICALMASS,
            'begin_date' => now()->addDays(4)->setTime(14, 0),
            'location' => 'Gemeenteplein Colignon, Schaarbeek',
            'postal_code' => '1030',
            'duration' => 90,
        ]);

        $this->attachRouteGpx($nextRide, $lat, $lng);
    }

    /**
     * Attach a generated GPX route to the ride's single-file 'gpx' collection, unless
     * one is already present (idempotent).
     */
    private function attachRouteGpx(Activity $activity, float $lat, float $lng): void
    {
        if ($activity->getFirstMedia('gpx')) {
            return;
        }

        $activity->addMediaFromString($this->routeGpxNear($lat, $lng))
            ->usingFileName('route.gpx')
            ->usingName('route')
            ->toMediaCollection('gpx');
    }

    /**
     * Build a GPX track tracing a believable ~1.5 km loop around (lat, lng) — enough
     * points for a convincing route line and a departure pin on the map.
     */
    private function routeGpxNear(float $lat, float $lng): string
    {
        $points = [];
        $steps = 32;

        for ($i = 0; $i <= $steps; $i++) {
            $angle = ($i / $steps) * 2 * M_PI;
            $pointLat = $lat + (0.009 * sin($angle)) + (0.0018 * sin(3 * $angle));
            $pointLng = $lng + (0.013 * cos($angle)) + (0.0026 * cos(2 * $angle));
            $points[] = sprintf('<trkpt lat="%.5f" lon="%.5f"/>', $pointLat, $pointLng);
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<gpx version="1.1" xmlns="http://www.topografix.com/GPX/1/1"><trk><trkseg>'
            .implode('', $points)
            .'</trkseg></trk></gpx>';
    }
}

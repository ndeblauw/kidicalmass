<?php

namespace Database\Seeders;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Article;
use App\Models\ContactForm;
use App\Models\Group;
use App\Models\Partner;
use App\Models\User;
use App\Models\YearStat;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Symfony\Component\Console\Terminal;

class DatabaseSeeder extends Seeder
{
    /** Chapter shortname => group id. */
    private array $groupIds = [];

    /** Coordinator/author users, rotated across content. */
    private Collection $authors;

    /** Shared sample GPX track, generated once and reused for every ride. */
    private ?string $sampleGpx = null;

    public function run(): void
    {
        $this->call(PostalCodeSeeder::class);

        $this->call(MediaSeeder::class);

        $this->call(GroupSeeder::class);
        $this->groupIds = Group::pluck('id', 'shortname')->all();

        $this->seedUsers();
        $this->seedActivities();
        $this->seedHistoricalRides();
        $this->seedYearStats();
        $this->seedArticles();
        $this->seedPartners();
        $this->seedContactForms();

        $this->call(DemoUserSeeder::class);

        // Dress specific chapters into deliberate content variations (press,
        // chapter-scoped partners, hero cover, full agenda) for eyeballing P-11.
        $this->call(ChapterShowcaseSeeder::class);

        // Fill the "In beeld" wall: attach sample photos to three chapters' latest
        // rides, in varied counts and orientations.
        $this->call(ChapterRideGallerySeeder::class);

        $this->command->newLine();
        $this->task('Cleanup temporary media (if requested)', function () {
            MediaSeeder::cleanup();
        });
    }

    private function seedUsers(): void
    {
        $this->task('Seeding users', function () {
            User::create([
                'name' => 'Nico Deblauwe',
                'email' => 'nico@deblauwe.be',
                'password' => '$2y$12$caY7UhzzouF4BRc7rxg1eOndYSP1VhBWrgU6UxZ9cN7QhIel6DKHa',
            ]);

            $coordinators = [
                ['name' => 'Leticia Sere', 'email' => 'leticia@kidicalmass.be', 'chapter' => 'brussel-stad'],
                ['name' => 'Cecilia Vanderhaegen', 'email' => 'cecilia@kidicalmass.be', 'chapter' => 'schaarbeek'],
                ['name' => 'Sindy Kinard', 'email' => 'sindy@kidicalmass.be', 'chapter' => 'namen'],
                ['name' => 'Sébastien Lefèvre', 'email' => 'sebastien@kidicalmass.be', 'chapter' => 'bergen'],
                ['name' => 'Marieke Peeters', 'email' => 'marieke@kidicalmass.be', 'chapter' => 'gent'],
            ];

            $this->authors = collect($coordinators)->map(function (array $coordinator) {
                $user = User::create([
                    'name' => $coordinator['name'],
                    'email' => $coordinator['email'],
                    'password' => bcrypt('password'),
                ]);

                if ($groupId = $this->groupIds[$coordinator['chapter']] ?? null) {
                    $user->groups()->attach($groupId);
                }

                return $user;
            });
        });
    }

    private function seedActivities(): void
    {
        $this->task('Seeding activities', function () {
            foreach ($this->activityData() as $index => $data) {
                $beginDate = $this->rideDate($data['week'], $data['time'], $data['day_offset'] ?? 0);

                $activity = Activity::factory()->create([
                    'title_nl' => $data['title_nl'],
                    'title_fr' => $data['title_fr'],
                    'content_nl' => $data['content_nl'],
                    'content_fr' => $data['content_fr'],
                    'activity_type' => $data['type'] ?? ActivityType::KIDICALMASS,
                    'begin_date' => $beginDate,
                    'location' => $data['location'],
                    'postal_code' => $data['postal_code'],
                    'distance' => $data['distance'] ?? null,
                    'duration_minutes' => $data['duration'] ?? 60,
                    'commute_link' => $data['commute_link'] ?? null,
                    'komoot_url' => null,
                    'author_id' => $this->authors[$index % $this->authors->count()]->id,
                ]);

                $groupShortnames = (array) $data['groups'];
                $activity->groups()->attach(
                    collect($groupShortnames)->map(fn ($name) => $this->groupIds[$name])->all()
                );

                if (! empty($data['photo'])) {
                    $this->attachRealPhoto($activity, 'main');
                }

                $this->attachSampleGpx($activity);
            }
        });
    }

    /**
     * A believable 2025 ride archive so the "Steun ons" proof deck can count a
     * real "ritten in 2025" figure (App\Support\SupportStats). Lightweight on
     * purpose: plain creates, no media or GPX, spread across local chapters and
     * the calendar year. These also populate the calendar's "voorbije" tab.
     */
    private function seedHistoricalRides(): void
    {
        $this->task('Seeding 2025 ride archive', function () {
            $blurbNl = 'Een rustige, feestelijke fietstocht op kindermaat door de buurt. We reden traag, met muziek voorop, langs veilige straten en pleintjes.';
            $blurbFr = "Une parade à vélo joyeuse et tranquille, à hauteur d'enfant, dans le quartier. On a roulé lentement, en musique, le long de rues sûres.";

            $names = Group::pluck('name', 'shortname');
            $shortnames = array_keys($this->groupIds);
            $postals = ['1000', '1030', '1050', '1070', '1080', '1090', '1180', '1200', '5000', '7000', '9000', '2000'];

            $start = Carbon::create(2025, 1, 11, 14, 0);

            // 62 rides -> matches the "meer dan 60 parades" story, all within 2025.
            foreach (range(0, 61) as $i) {
                $shortname = $shortnames[$i % count($shortnames)];
                $name = $names[$shortname] ?? 'Kidical Mass';

                $activity = Activity::create([
                    'title_nl' => 'Kidical Mass '.$name,
                    'title_fr' => 'Kidical Mass '.$name,
                    'content_nl' => $blurbNl,
                    'content_fr' => $blurbFr,
                    'activity_type' => ActivityType::KIDICALMASS,
                    'begin_date' => (clone $start)->addDays($i * 5),
                    'location' => $name,
                    'postal_code' => $postals[$i % count($postals)],
                    'duration_minutes' => 60,
                    'published' => true,
                    'author_id' => $this->authors[$i % $this->authors->count()]->id,
                ]);

                $activity->groups()->attach($this->groupIds[$shortname]);
            }
        });
    }

    /**
     * The curated per-year impact figure the admin edits (year_stats). There is
     * no attendance tracking to derive this from, so the participant count is
     * seeded by hand to match the current copy.
     */
    private function seedYearStats(): void
    {
        $this->task('Seeding year stats', function () {
            YearStat::updateOrCreate(['year' => 2025], ['participants' => 5500]);
        });
    }

    private function seedArticles(): void
    {
        $this->task('Seeding articles', function () {
            foreach ($this->articleData() as $index => $data) {
                $article = Article::factory()->create([
                    'title_nl' => $data['title_nl'],
                    'title_fr' => $data['title_fr'],
                    'content_nl' => $data['content_nl'],
                    'content_fr' => $data['content_fr'],
                    'author_id' => $this->authors[$index % $this->authors->count()]->id,
                ]);

                $article->groups()->attach(
                    collect((array) $data['groups'])->map(fn ($name) => $this->groupIds[$name])->all()
                );

                if (! empty($data['photo'])) {
                    $this->attachRealPhoto($article, 'main');
                }

                // Back-date so the news list reads like a real archive.
                $publishedAt = now()->subDays($data['days_ago']);
                DB::table('articles')->where('id', $article->id)->update([
                    'created_at' => $publishedAt,
                    'updated_at' => $publishedAt,
                ]);
            }
        });
    }

    private function seedPartners(): void
    {
        $this->task('Seeding partners', function () {
            foreach ($this->partnerData() as $data) {
                Partner::factory()->create([
                    'name' => $data['name'],
                    'url' => $data['url'],
                    'description_nl' => $data['description_nl'],
                    'description_fr' => $data['description_fr'],
                    'show_logo' => true,
                    'visible' => true,
                    'group_id' => isset($data['chapter']) ? ($this->groupIds[$data['chapter']] ?? null) : null,
                ]);
            }
        });
    }

    private function seedContactForms(): void
    {
        $this->task('Seeding contact forms', function () {
            $messages = [
                ['name' => 'Ann Verstraeten', 'email' => 'ann.verstraeten@example.com', 'phone' => '0478 12 34 56', 'message' => 'Dag, ik woon in Deurne en zou graag een Kidical Mass-groep opstarten in onze buurt. Hoe begin ik daaraan?', 'page_url' => '/nl/help-mee'],
                ['name' => 'Karim Haddad', 'email' => 'karim.haddad@example.com', 'phone' => null, 'message' => 'Mijn dochter (6) wil heel graag meefietsen. Moeten we vooraf inschrijven of komen we gewoon af?', 'page_url' => '/nl/events'],
                ['name' => 'Lien De Smet', 'email' => 'lien.desmet@example.com', 'phone' => '0496 88 77 66', 'message' => 'Onze school wil meedoen met een rit langs de schoolomgeving. Kunnen we daarvoor samenwerken?', 'page_url' => '/nl/chapters/schaarbeek'],
                ['name' => 'Tom Claes', 'email' => 'tom.claes@example.com', 'phone' => null, 'message' => 'Ik wil graag meehelpen als begeleider in het roze hesje. Waar schrijf ik me in?', 'page_url' => '/nl/help-mee'],
                ['name' => 'Fatima Ouali', 'email' => 'fatima.ouali@example.com', 'phone' => '0471 22 33 44', 'message' => 'Is er een rit in Molenbeek deze maand? Ik vind de datum niet meteen terug op de site.', 'page_url' => '/nl/chapters/molenbeek'],
                ['name' => 'Pieter Janssens', 'email' => 'pieter.janssens@example.com', 'phone' => null, 'message' => 'Wij zijn een fietswinkel en zouden jullie graag sponsoren. Met wie kan ik daarover praten?', 'page_url' => '/nl/about/partners'],
            ];

            foreach ($messages as $message) {
                ContactForm::create($message + ['honeypot' => null]);
            }
        });
    }

    /**
     * Builds a Sunday (or offset day) at the given week relative to this week.
     * Negative weeks land in the past, populating the "voorbije" calendar tab.
     */
    private function rideDate(int $weekOffset, string $time, int $dayOffset = 0): Carbon
    {
        return Carbon::now()
            ->startOfWeek(Carbon::SUNDAY)
            ->addWeeks($weekOffset)
            ->addDays($dayOffset)
            ->setTimeFromTimeString($time);
    }

    private function attachRealPhoto(HasMedia $model, string $collection): void
    {
        $path = public_path('img/photography/ride-cinquantenaire-crowd.jpg');

        if (! is_file($path)) {
            return;
        }

        $model->clearMediaCollection($collection);
        $model->addMedia($path)->preservingOriginal()->toMediaCollection($collection);
    }

    /**
     * Give every ride a GPX route so its detail page renders a route map. Only actual
     * rides get one (a meeting or workshop has no route). Skipped in the testing
     * environment (mirrors ActivityFactory) and when one already exists.
     */
    private function attachSampleGpx(Activity $activity): void
    {
        if (app()->environment('testing')) {
            return;
        }

        if ($activity->activity_type !== ActivityType::KIDICALMASS) {
            return;
        }

        if ($activity->getFirstMedia('gpx')) {
            return;
        }

        $activity->addMediaFromString($this->sampleRouteGpx())
            ->usingFileName('route.gpx')
            ->usingName('route')
            ->toMediaCollection('gpx');
    }

    /**
     * A believable ~1.5 km loop through central Brussels, reused for every seeded ride.
     * Enough points for a convincing route line and a departure pin. Built once, cached.
     */
    private function sampleRouteGpx(): string
    {
        if ($this->sampleGpx !== null) {
            return $this->sampleGpx;
        }

        $lat = 50.8467;
        $lng = 4.3499;
        $steps = 32;
        $points = [];

        for ($i = 0; $i <= $steps; $i++) {
            $angle = ($i / $steps) * 2 * M_PI;
            $pointLat = $lat + (0.009 * sin($angle)) + (0.0018 * sin(3 * $angle));
            $pointLng = $lng + (0.013 * cos($angle)) + (0.0026 * cos(2 * $angle));
            $points[] = sprintf('<trkpt lat="%.5f" lon="%.5f"/>', $pointLat, $pointLng);
        }

        return $this->sampleGpx = '<?xml version="1.0" encoding="UTF-8"?>'
            .'<gpx version="1.1" xmlns="http://www.topografix.com/GPX/1/1"><trk><trkseg>'
            .implode('', $points)
            .'</trkseg></trk></gpx>';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function activityData(): array
    {
        $ride = 'Een rustige, feestelijke fietstocht op kindermaat. We rijden traag, met muziek voorop, langs veilige straten, parken en pleintjes. Van loopfietsje tot bakfiets: iedereen is welkom. Geen inschrijving nodig, kom gewoon af. We zijn na een uurtje terug.';
        $rideFr = "Une parade à vélo joyeuse et tranquille, à hauteur d'enfant. On roule lentement, en musique, le long de rues sûres, de parcs et de placettes. De la draisienne au vélo cargo : tout le monde est le bienvenu. Sans inscription, venez comme vous êtes. On est de retour après une petite heure.";

        return [
            // --- Voorbije ritten (lente 2026) ---
            ['groups' => 'brussels-capital-region', 'week' => -3, 'time' => '15:00', 'type' => ActivityType::KIDICALMASS, 'photo' => true,
                'title_nl' => 'Grande Grote Kidical Mass: lente-editie', 'title_fr' => 'Grande Kidical Mass : édition de printemps',
                'content_nl' => 'De grote lenteparade vertrekt vanaf het Troonplein. Lokale groepen uit heel Brussel fietsen samen de stad door, met fanfare, bakfietsen vol kinderen en een zee van roze hesjes. Een feestelijke vraag om straten op maat van kinderen.',
                'content_fr' => "La grande parade de printemps s'élance de la place du Trône. Les groupes locaux de toute la région roulent ensemble à travers la ville, fanfare en tête. Une fête et une demande : des rues à hauteur d'enfant.",
                'location' => 'Troonplein, Brussel', 'postal_code' => '1000', 'distance' => '6 km', 'duration' => 120],
            ['groups' => 'schaarbeek', 'week' => 0, 'time' => '14:00', 'title_nl' => 'Kidical Mass Schaarbeek', 'title_fr' => 'Kidical Mass Schaerbeek',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Gemeenteplein Colignon, Schaarbeek', 'postal_code' => '1030', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'elsene', 'week' => -2, 'time' => '15:00', 'title_nl' => 'Kidical Mass Elsene', 'title_fr' => 'Kidical Mass Ixelles',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Ter Kamerenbos (kiosk), Elsene', 'postal_code' => '1050', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'namen', 'week' => -1, 'time' => '11:00', 'title_nl' => 'Kidical Mass Namen', 'title_fr' => 'Kidical Mass Namur',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Place du Théâtre, Namen', 'postal_code' => '5000', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'bergen', 'week' => -2, 'day_offset' => -1, 'time' => '14:00', 'title_nl' => 'Kidical Mass Bergen', 'title_fr' => 'Kidical Mass Mons',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Théâtre le Manège, Bergen', 'postal_code' => '7000', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'anderlecht', 'week' => -1, 'time' => '14:30', 'title_nl' => 'Kidical Mass Anderlecht', 'title_fr' => 'Kidical Mass Anderlecht',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Sint-Guidoplein, Anderlecht', 'postal_code' => '1070', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'jette', 'week' => -2, 'time' => '14:00', 'title_nl' => 'Kidical Mass Jette', 'title_fr' => 'Kidical Mass Jette',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Koning Boudewijnpark, Jette', 'postal_code' => '1090', 'distance' => '5 km', 'duration' => 60],

            // --- Aankomende ritten (juni 2026) ---
            ['groups' => 'elsene', 'week' => 1, 'time' => '15:00', 'title_nl' => 'Kidical Mass Elsene', 'title_fr' => 'Kidical Mass Ixelles',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Ter Kamerenbos (kiosk), Elsene', 'postal_code' => '1050', 'distance' => '5 km', 'duration' => 60,
                'commute_link' => 'https://www.komoot.com/tour/123456789'],
            ['groups' => 'ukkel', 'week' => 1, 'time' => '14:30', 'title_nl' => 'Kidical Mass Ukkel', 'title_fr' => 'Kidical Mass Uccle',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Wolvendaelpark, Ukkel', 'postal_code' => '1180', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'laken', 'week' => 1, 'time' => '14:00', 'title_nl' => 'Kidical Mass Laken', 'title_fr' => 'Kidical Mass Laeken',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Ossegempark, Laken', 'postal_code' => '1020', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'woluwe', 'week' => 2, 'time' => '15:00', 'title_nl' => 'Kidical Mass Woluwe', 'title_fr' => 'Kidical Mass Woluwe',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Park van Woluwe', 'postal_code' => '1200', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'namen', 'week' => 2, 'time' => '11:00', 'title_nl' => 'Kidical Mass Namen', 'title_fr' => 'Kidical Mass Namur',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Place du Théâtre, Namen', 'postal_code' => '5000', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'etterbeek', 'week' => 2, 'time' => '14:00', 'title_nl' => 'Kidical Mass Etterbeek', 'title_fr' => 'Kidical Mass Etterbeek',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Jubelpark (ingang Etterbeek)', 'postal_code' => '1040', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'gent', 'week' => 3, 'time' => '14:00', 'title_nl' => 'Kidical Mass Gent', 'title_fr' => 'Kidical Mass Gand',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Citadelpark, Gent', 'postal_code' => '9000', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'brussel-stad', 'week' => 3, 'time' => '15:00', 'title_nl' => 'Kidical Mass Brussel-Stad', 'title_fr' => 'Kidical Mass Bruxelles-Ville',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Begijnhofplein, Brussel', 'postal_code' => '1000', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'molenbeek', 'week' => 3, 'time' => '14:30', 'title_nl' => 'Kidical Mass Molenbeek', 'title_fr' => 'Kidical Mass Molenbeek',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Karreveldpark, Molenbeek', 'postal_code' => '1080', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'schaarbeek', 'week' => 4, 'time' => '14:00', 'title_nl' => 'Kidical Mass Schaarbeek', 'title_fr' => 'Kidical Mass Schaerbeek',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Gemeenteplein Colignon, Schaarbeek', 'postal_code' => '1030', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'antwerpen', 'week' => 4, 'time' => '14:00', 'title_nl' => 'Kidical Mass Antwerpen', 'title_fr' => 'Kidical Mass Anvers',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Park Spoor Noord, Antwerpen', 'postal_code' => '2000', 'distance' => '5 km', 'duration' => 60],
            ['groups' => 'bergen', 'week' => 4, 'day_offset' => -1, 'time' => '14:00', 'title_nl' => 'Kidical Mass Bergen', 'title_fr' => 'Kidical Mass Mons',
                'content_nl' => $ride, 'content_fr' => $rideFr, 'location' => 'Théâtre le Manège, Bergen', 'postal_code' => '7000', 'distance' => '5 km', 'duration' => 60],

            // --- Najaar & speciale edities ---
            ['groups' => 'brussels-capital-region', 'week' => 18, 'time' => '15:00', 'title_nl' => 'Grande Grote Kidical Mass: najaarseditie', 'title_fr' => "Grande Kidical Mass : édition d'automne",
                'content_nl' => 'De grote najaarsparade in het kader van het internationale actieweekend Streets for Kids. Heel Brussel fietst samen voor kindvriendelijke straten. Met animatie, muziek en een feest op de aankomst.',
                'content_fr' => "La grande parade d'automne, dans le cadre du week-end d'action international Streets for Kids. Animations, musique et fête à l'arrivée.",
                'location' => 'Jubelpark, Brussel', 'postal_code' => '1000', 'distance' => '6 km', 'duration' => 120],
            ['groups' => 'schaarbeek', 'week' => 20, 'time' => '18:00', 'title_nl' => 'Bright Light Parade Schaarbeek', 'title_fr' => 'Bright Light Parade Schaerbeek',
                'content_nl' => 'Een avondrit vol lichtjes. Versier je fiets met lampjes en slingers en rijd mee terwijl het donker wordt. Magisch voor groot en klein.',
                'content_fr' => 'Une parade du soir pleine de lumières. Décore ton vélo de guirlandes lumineuses et roule avec nous à la tombée de la nuit.',
                'location' => 'Josaphatpark, Schaarbeek', 'postal_code' => '1030', 'distance' => '4 km', 'duration' => 60],
            ['groups' => 'schaarbeek', 'week' => 21, 'time' => '15:00', 'title_nl' => 'Spooky Edition Schaarbeek', 'title_fr' => 'Spooky Edition Schaerbeek',
                'content_nl' => 'De griezelrit van het seizoen. Verkleed je mooiste spook of monster en kom mee fietsen. Snoep gegarandeerd.',
                'content_fr' => 'La parade frissons de la saison. Déguise-toi en fantôme ou en monstre et viens rouler avec nous. Bonbons garantis.',
                'location' => 'Josaphatpark, Schaarbeek', 'postal_code' => '1030', 'distance' => '4 km', 'duration' => 60],

            // --- Meetings & workshops (chapterpagina's, niet in de kalender) ---
            ['groups' => 'brussel-stad', 'week' => 1, 'time' => '19:30', 'type' => ActivityType::MEETING,
                'title_nl' => 'Vrijwilligersmeeting', 'title_fr' => 'Réunion des bénévoles',
                'content_nl' => 'Vier keer per jaar komen we samen met alle vrijwilligers om ervaringen te delen en het volgende seizoen voor te bereiden. Nieuwe gezichten zijn van harte welkom.',
                'content_fr' => 'Quatre fois par an, tous les bénévoles se réunissent pour partager et préparer la saison suivante. Les nouveaux visages sont les bienvenus.',
                'location' => 'Mundo-B, Edinburgstraat 26, Elsene', 'postal_code' => '1050', 'duration' => 120],
            ['groups' => 'anderlecht', 'week' => 2, 'time' => '10:00', 'type' => ActivityType::WORKSHOP,
                'title_nl' => 'Fietscheck & sleutelworkshop', 'title_fr' => 'Atelier vélo & petites réparations',
                'content_nl' => 'Breng je fiets langs voor een gratis veiligheidscheck. We helpen je remmen, banden en verlichting na te kijken en leren je zelf kleine herstellingen doen.',
                'content_fr' => "Amène ton vélo pour un contrôle de sécurité gratuit. On vérifie freins, pneus et éclairage et on t'apprend les petites réparations.",
                'location' => 'Cyclo werkplaats, Anderlecht', 'postal_code' => '1070', 'duration' => 180],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function articleData(): array
    {
        return [
            ['groups' => 'brussels-capital-region', 'days_ago' => 25, 'photo' => true,
                'title_nl' => 'Grande Kidical Mass brengt bijna 1.150 mensen op de been',
                'title_fr' => 'La Grande Kidical Mass rassemble près de 1 150 personnes',
                'content_nl' => "Wat een dag. Onder een stralende zon vertrokken bijna 1.150 ouders en kinderen vanaf het Troonplein voor onze grote lenteparade. Lokale groepen uit heel Brussel reden samen, met fanfare voorop en bakfietsen vol kleuters.\n\nDe boodschap was duidelijk en vrolijk tegelijk: geef kinderen veilige straten, dan fietsen ze graag. We danken alle vrijwilligers in het roze hesje die de rit veilig hielden, en iedereen die kwam meefietsen.\n\nTot de volgende rit in je eigen gemeente.",
                'content_fr' => 'Quelle journée. Sous un soleil radieux, près de 1 150 parents et enfants se sont élancés de la place du Trône pour notre grande parade de printemps. Merci à tous les bénévoles en gilet rose et à tous ceux qui sont venus rouler.'],
            ['groups' => 'namen', 'days_ago' => 32,
                'title_nl' => 'Namen fietst mee: de tweede editie was een schot in de roos',
                'title_fr' => 'Namur roule avec nous : une belle deuxième édition',
                'content_nl' => "De Kidical Mass in Namen bestaat nog niet lang, maar de tweede editie zat meteen vol. Vanaf de Place du Théâtre reden tientallen gezinnen in een rustig tempo door het centrum.\n\nDat een groep in een jaar tijd zo groeit, toont hoeveel ouders snakken naar veilige fietsstraten, ook buiten Brussel. Bedankt aan het lokale team en aan onze partners ter plaatse.",
                'content_fr' => "La Kidical Mass de Namur est jeune, mais la deuxième édition a fait le plein. Depuis la place du Théâtre, des dizaines de familles ont traversé le centre à allure tranquille. Merci à l'équipe locale et à nos partenaires."],
            ['groups' => 'bergen', 'days_ago' => 45,
                'title_nl' => 'Eerste Kidical Mass in Bergen: 120 deelnemers op de openingsrit',
                'title_fr' => 'Première Kidical Mass à Mons : 120 participants',
                'content_nl' => "Bergen heeft er een nieuwe traditie bij. Voor de allereerste rit kwamen meteen zo'n 120 mensen opdagen aan het Théâtre le Manège. Het lokale team, met Violette, Babas, Sébastien en Thibault, plant dit jaar drie ritten.\n\nWil je meehelpen of gewoon meefietsen? Hou de agenda in de gaten, iedereen is welkom.",
                'content_fr' => "Mons tient une nouvelle tradition. Pour la toute première parade, environ 120 personnes se sont retrouvées au Théâtre le Manège. L'équipe locale prévoit trois sorties cette année. Bienvenue à toutes et tous."],
            ['groups' => 'brussels-capital-region', 'days_ago' => 95,
                'title_nl' => 'Het seizoen is gestart: meer dan 60 fietsparades dit jaar',
                'title_fr' => 'La saison démarre : plus de 60 parades cette année',
                'content_nl' => "Het fietsseizoen is officieel begonnen. Dit jaar staan er meer dan 60 parades op de kalender, verspreid over alle lokale groepen. Van een rustige buurtrit tot de grote lente- en najaarsparade: er is voor elk gezin wel iets.\n\nKies je gemeente, prik een datum en kom meefietsen. Geen inschrijving nodig, gewoon komen.",
                'content_fr' => 'La saison cycliste est officiellement lancée. Plus de 60 parades sont au programme cette année, réparties entre tous les groupes locaux. Choisis ta commune, note une date et viens rouler.'],
            ['groups' => 'brussels-capital-region', 'days_ago' => 60,
                'title_nl' => 'Rijd mee in een roze hesje: word begeleider',
                'title_fr' => 'Roule en gilet rose : deviens accompagnateur',
                'content_nl' => "Onze ritten draaien op vrijwilligers. De begeleiders in het roze hesje houden de groep samen, kruisen veilig de straat over en zorgen voor goede sfeer. Je hoeft geen fietsexpert te zijn, een halve dag per maand kan al veel betekenen.\n\nWe geven een korte vorming zodat je je meteen op je gemak voelt. Zin om mee te doen? Laat iets weten via het contactformulier.",
                'content_fr' => "Nos parades reposent sur les bénévoles. Les accompagnateurs en gilet rose gardent le groupe ensemble et assurent la bonne ambiance. Pas besoin d'être un expert : une demi-journée par mois suffit. Une courte formation est prévue."],
            ['groups' => 'brussels-capital-region', 'days_ago' => 120,
                'title_nl' => 'Van één groep naar meer dan twintig: hoe Kidical Mass groeide',
                'title_fr' => "D'un groupe à plus de vingt : la croissance de Kidical Mass",
                'content_nl' => "We begonnen in 2020 met één groep tijdens de coronaperiode. Vandaag zijn er meer dan twintig lokale groepen in heel het land, met honderden vrijwilligers en duizenden deelnemers per jaar.\n\nDie groei is geen toeval. Steeds meer ouders willen dat hun kinderen veilig met de fiets naar school en naar de speeltuin kunnen. Elke nieuwe groep begint klein, bij iemand die zijn buurt graag ziet.",
                'content_fr' => "Nous avons commencé en 2020 avec un seul groupe, en pleine période covid. Aujourd'hui, plus de vingt groupes locaux existent dans tout le pays. Chaque nouveau groupe commence petit, avec quelqu'un qui aime son quartier."],
            ['groups' => 'brussels-capital-region', 'days_ago' => 150,
                'title_nl' => 'Vijf jaar Kidical Mass: feest en fietsprotest voor een kindvriendelijke stad',
                'title_fr' => 'Cinq ans de Kidical Mass : fête et plaidoyer pour une ville à hauteur d’enfant',
                'content_nl' => "Vijf jaar geleden reed de eerste Kidical Mass uit. Dat vierden we met een extra grote parade, samen met het Irisfeest in het Park van Brussel.\n\nEr was livemuziek, een fanfare, fiets-dj's en zelfs een reuzentaart. Maar achter het feest zit een blijvende vraag: straten die veilig genoeg zijn voor het jongste kind. Op naar de volgende vijf jaar.",
                'content_fr' => "Il y a cinq ans roulait la toute première Kidical Mass. Nous l'avons fêté avec une parade XXL, en marge de la Fête de l'Iris. Derrière la fête, une demande qui reste : des rues sûres pour le plus jeune des enfants."],
            ['groups' => 'brussels-capital-region', 'days_ago' => 165,
                'title_nl' => 'Steun onze werking via Growfunding',
                'title_fr' => 'Soutiens notre action via Growfunding',
                'content_nl' => "Kidical Mass draait op vrijwilligers en kleine bijdragen. Met hesjes, materiaal en verzekering houden we elke rit veilig. Daarvoor lopen we elk jaar een buurtcrowdfunding via Growfunding.\n\nElke bijdrage, groot of klein, helpt een groep verder. Bedankt aan iedereen die ons al steunde.",
                'content_fr' => 'Kidical Mass fonctionne grâce aux bénévoles et aux petits dons. Gilets, matériel et assurance gardent chaque parade en sécurité. Chaque contribution, petite ou grande, fait avancer un groupe. Merci.'],
            ['groups' => 'brussels-capital-region', 'days_ago' => 210,
                'title_nl' => 'Streets for Kids: samen met heel Europa de straat op',
                'title_fr' => 'Streets for Kids : dans la rue avec toute l’Europe',
                'content_nl' => "Kidical Mass is deel van een internationale beweging. Twee keer per jaar, in de lente en het najaar, fietsen steden in heel Europa samen tijdens het actieweekend Streets for Kids.\n\nDe vraag is overal dezelfde: tragere straten, veilige schoolomgevingen en meer plaats voor wie te voet of met de fiets gaat. Fijn om te weten dat we niet alleen rijden.",
                'content_fr' => "Kidical Mass fait partie d'un mouvement international. Deux fois par an, des villes de toute l'Europe roulent ensemble lors du week-end Streets for Kids. Partout la même demande : des rues plus lentes et des abords d'école sûrs."],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function partnerData(): array
    {
        return [
            ['name' => 'Brussel Mobiliteit', 'url' => 'https://mobilite-mobiliteit.brussels', 'chapter' => null,
                'description_nl' => 'Het gewestelijke mobiliteitsagentschap dat onze parades ondersteunt en mee bouwt aan veilige fietsroutes.',
                'description_fr' => 'L’agence régionale de la mobilité qui soutient nos parades et construit des itinéraires cyclables sûrs.'],
            ['name' => 'Pro Velo', 'url' => 'https://www.provelo.org',
                'description_nl' => 'Vzw die fietslessen, begeleide ritten en fietseducatie organiseert in heel Brussel.',
                'description_fr' => 'Asbl qui organise cours de vélo, balades guidées et éducation cyclable à Bruxelles.'],
            ['name' => 'Cyclo', 'url' => 'https://www.cyclo.org',
                'description_nl' => 'Brusselse fietspuntwerking voor herstellingen, onderhoud en fietsdelen.',
                'description_fr' => 'Réseau de points vélo bruxellois pour réparations, entretien et vélo partagé.'],
            ['name' => 'Fietsersbond', 'url' => 'https://www.fietsersbond.be',
                'description_nl' => 'De ledenvereniging die opkomt voor iedereen die zich met de fiets verplaatst.',
                'description_fr' => 'L’association de membres qui défend toutes celles et ceux qui roulent à vélo.'],
            ['name' => 'GRACQ', 'url' => 'https://www.gracq.org',
                'description_nl' => 'Franstalige fietsersvereniging die ijvert voor veiliger fietsen in Brussel en Wallonië.',
                'description_fr' => 'L’association des cyclistes quotidiens qui milite pour un vélo plus sûr.'],
            ['name' => 'Clean Cities', 'url' => 'https://cleancitiescampaign.org',
                'description_nl' => 'Europese campagne voor autoluwe, leefbare steden en partner van Streets for Kids.',
                'description_fr' => 'Campagne européenne pour des villes apaisées et partenaire de Streets for Kids.'],
            ['name' => 'Heroes for Zero', 'url' => 'https://www.heroesforzero.be',
                'description_nl' => 'Burgerbeweging die ijvert voor nul verkeersdoden en veilige straten voor kinderen.',
                'description_fr' => 'Mouvement citoyen qui milite pour zéro mort sur les routes et des rues sûres.'],
            ['name' => 'Fietsbieb', 'url' => 'https://www.fietsbieb.be',
                'description_nl' => 'Uitleendienst waar kinderen tegen een kleine bijdrage een fiets op maat lenen.',
                'description_fr' => 'Bibliothèque de vélos où les enfants empruntent un vélo à leur taille.'],
            ['name' => 'My Kids Bikes', 'url' => null,
                'description_nl' => 'Tweedehands kinderfietsen, zodat elk kind op een goed passende fiets rijdt.',
                'description_fr' => 'Vélos d’enfants d’occasion, pour que chacun roule sur un vélo adapté.'],
            ['name' => 'Succulente', 'url' => null,
                'description_nl' => 'Vegan bakkerij die onze grote parades op een reuzentaart trakteert.',
                'description_fr' => 'Boulangerie vegan qui régale nos grandes parades d’un gâteau géant.'],
            ['name' => 'Les Chercheurs d’Air', 'url' => null,
                'description_nl' => 'Vereniging die de luchtkwaliteit rond scholen meet en aankaart.',
                'description_fr' => 'Association qui mesure et dénonce la qualité de l’air autour des écoles.'],
            ['name' => 'Park Poetik', 'url' => null,
                'description_nl' => 'Buurtcollectief dat onze feesten kleurt met cuistax en animatie.',
                'description_fr' => 'Collectif de quartier qui anime nos fêtes avec cuistax et animations.'],
            ['name' => 'BRUZZ', 'url' => 'https://www.bruzz.be',
                'description_nl' => 'Brussels stadsmedium dat onze acties op de voet volgt.',
                'description_fr' => 'Le média bruxellois qui suit nos actions de près.'],
            ['name' => 'Growfunding', 'url' => 'https://growfunding.be/nl/projects/kidicalmassbelgique',
                'description_nl' => 'Buurtcrowdfundingplatform waarop je onze werking financieel kan steunen.',
                'description_fr' => 'Plateforme de crowdfunding de quartier pour soutenir notre action.'],
            ['name' => 'Avello', 'url' => null, 'chapter' => 'bergen',
                'description_nl' => 'Fietswinkel en werkplaats die onze Waalse groepen mee op weg helpt.',
                'description_fr' => 'Magasin et atelier vélo qui épaule nos groupes wallons.'],
        ];
    }

    protected function task(string $label, callable $callback): void
    {
        $output = $this->command->getOutput();

        $terminalWidth = min(150, (new Terminal)->getWidth()) - 3 ?: 80;

        $minDots = 3;

        $labelLen = mb_strlen($label);
        $statusLen = 6;

        $dots = max($minDots, $terminalWidth - $labelLen - $statusLen) - 2;

        $output->write('  '.$label.' ');
        $output->write('<fg=gray>');
        $output->write(str_repeat('.', $dots));

        try {
            $callback();
            $output->writeln('. </><fg=green>DONE</fg=green>');
        } catch (\Throwable $e) {
            $output->writeln(' </><error>ERROR</error>');
            throw $e;
        }
    }
}

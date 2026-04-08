<?php

namespace Database\Seeders;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use RuntimeException;

class ActivitySeeder extends Seeder
{
    private const DEFAULT_EVENT_TIME = '14:00';
    private const AGENDA_YEAR = 2025;

    public function run(): void
    {
        $author = User::query()->first();
        if (! $author) {
            throw new RuntimeException('ActivitySeeder requires at least one existing user.');
        }

        foreach ($this->agendaActivities() as $activityData) {
            $beginDate = $this->parseDate($activityData['date'], $activityData['time'] ?? self::DEFAULT_EVENT_TIME);
            $endDate = $beginDate->addHours(2);

            $activity = Activity::query()->updateOrCreate(
                [
                    'title_nl' => $activityData['title_nl'],
                ],
                [
                    'title_fr' => $activityData['title_fr'],
                    'content_nl' => $activityData['content_nl'],
                    'content_fr' => $activityData['content_fr'],
                    'activity_type' => $activityData['activity_type'],
                    'begin_date' => $beginDate,
                    'end_date' => $endDate,
                    'location' => $activityData['location'],
                    'author_id' => $author->id,
                ]
            );

            if (! empty($activityData['groups'])) {
                $groupIds = Group::query()
                    ->whereIn('shortname', $activityData['groups'])
                    ->pluck('id');

                $activity->groups()->sync($groupIds);
            }
        }
    }

    private function parseDate(string $dayAndMonth, string $time): CarbonImmutable
    {
        [$day, $month] = array_map('intval', explode('/', $dayAndMonth));

        return CarbonImmutable::create(self::AGENDA_YEAR, $month, $day, ...array_map('intval', explode(':', $time)));
    }

    private function agendaActivities(): array
    {
        return [
            ['date' => '07/03', 'time' => '19:00', 'title_nl' => 'MEETUP Lancement de Saison @ Growfunding', 'title_fr' => 'MEETUP Lancement de Saison @ Growfunding', 'content_nl' => 'Start van het Kidical Mass-seizoen.', 'content_fr' => 'Lancement de la saison Kidical Mass.', 'activity_type' => ActivityType::MEETING, 'location' => 'Growfunding, Brussel', 'groups' => []],
            ['date' => '15/03', 'time' => '13:30', 'title_nl' => 'Kidical Mass Mons', 'title_fr' => 'Kidical Mass Mons', 'content_nl' => 'Kidical Mass Mons (Wallonië).', 'content_fr' => 'Kidical Mass Mons (Wallonie).', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Mons', 'groups' => ['mons']],
            ['date' => '22/03', 'title_nl' => 'Kidical Mass Woluwe-Saint-Pierre & Woluwe-Saint-Lambert', 'title_fr' => 'Kidical Mass Woluwe-Saint-Pierre & Woluwe-Saint-Lambert', 'content_nl' => 'Kidical Mass in Woluwe-Saint-Pierre en Woluwe-Saint-Lambert.', 'content_fr' => 'Kidical Mass à Woluwe-Saint-Pierre et Woluwe-Saint-Lambert.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Woluwe-Saint-Pierre / Woluwe-Saint-Lambert', 'groups' => ['woluwe-saint-pierre-woluwe-saint-lambert']],
            ['date' => '22/03', 'title_nl' => 'Kidical Mass Schaerbeek - Schaarbeek', 'title_fr' => 'Kidical Mass Schaerbeek - Schaarbeek', 'content_nl' => 'Kidical Mass in Schaerbeek.', 'content_fr' => 'Kidical Mass à Schaerbeek.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Schaerbeek', 'groups' => ['schaerbeek-schaarbeek']],
            ['date' => '26/03', 'title_nl' => 'Verkeersveiligheid Congres @ Mechelen', 'title_fr' => 'Congrès Sécurité Routière @ Malines', 'content_nl' => 'Congres over verkeersveiligheid.', 'content_fr' => 'Congrès sur la sécurité routière.', 'activity_type' => ActivityType::WORKSHOP, 'location' => 'Mechelen', 'groups' => ['mechelen']],
            ['date' => '29/03', 'title_nl' => 'Kidical Mass Forest - Vorst', 'title_fr' => 'Kidical Mass Forest - Vorst', 'content_nl' => 'Kidical Mass in Forest - Vorst.', 'content_fr' => 'Kidical Mass à Forest - Vorst.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Forest - Vorst', 'groups' => ['forest-vorst']],
            ['date' => '29/03', 'title_nl' => 'Kidical Mass Watermael-Boitsfort & Auderghem', 'title_fr' => 'Kidical Mass Watermael-Boitsfort & Auderghem', 'content_nl' => 'Kidical Mass in Watermael-Boitsfort en Auderghem.', 'content_fr' => 'Kidical Mass à Watermael-Boitsfort et Auderghem.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Watermael-Boitsfort / Auderghem', 'groups' => ['watermael-boitsfort-auderghem']],
            ['date' => '19/04', 'title_nl' => 'Kidical Mass Evere - Haren', 'title_fr' => 'Kidical Mass Evere - Haren', 'content_nl' => 'Kidical Mass in Evere en Haren.', 'content_fr' => 'Kidical Mass à Evere et Haren.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Evere - Haren', 'groups' => ['evere-haren']],
            ['date' => '19/04', 'title_nl' => 'Kidical Mass Ixelles - Elsene', 'title_fr' => 'Kidical Mass Ixelles - Elsene', 'content_nl' => 'Kidical Mass in Ixelles - Elsene.', 'content_fr' => 'Kidical Mass à Ixelles - Elsene.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Ixelles - Elsene', 'groups' => ['ixelles-elsene']],
            ['date' => '26/04', 'title_nl' => 'Kidical Mass Forest - Vorst (Avril)', 'title_fr' => 'Kidical Mass Forest - Vorst (Avril)', 'content_nl' => 'Kidical Mass in Forest - Vorst.', 'content_fr' => 'Kidical Mass à Forest - Vorst.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Forest - Vorst', 'groups' => ['forest-vorst']],
            ['date' => '26/04', 'title_nl' => 'Kidical Mass Woluwe-Saint-Pierre - Woluwe-Saint-Lambert (Avril)', 'title_fr' => 'Kidical Mass Woluwe-Saint-Pierre - Woluwe-Saint-Lambert (Avril)', 'content_nl' => 'Kidical Mass in Woluwe-Saint-Pierre en Woluwe-Saint-Lambert.', 'content_fr' => 'Kidical Mass à Woluwe-Saint-Pierre et Woluwe-Saint-Lambert.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Woluwe-Saint-Pierre / Woluwe-Saint-Lambert', 'groups' => ['woluwe-saint-pierre-woluwe-saint-lambert']],
            ['date' => '03/05', 'title_nl' => 'Kidical Mass Neder-Over-Heembeek', 'title_fr' => 'Kidical Mass Neder-Over-Heembeek', 'content_nl' => 'Kidical Mass in Neder-Over-Heembeek.', 'content_fr' => 'Kidical Mass à Neder-Over-Heembeek.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Neder-Over-Heembeek', 'groups' => ['neder-over-heembeek']],
            ['date' => '10/05', 'time' => '15:00', 'title_nl' => 'GRANDE GROTE Kidical Mass (Inter)Nationaal', 'title_fr' => 'GRANDE GROTE Kidical Mass (Inter)National', 'content_nl' => 'Grote internationale Kidical Mass op Place Troon.', 'content_fr' => 'Grande Kidical Mass internationale à la Place du Trône.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Place Troon, Brussel', 'groups' => ['bruxelles-ville-brussel-stad']],
            ['date' => '17/05', 'title_nl' => 'Kidical Mass Mons (Mai)', 'title_fr' => 'Kidical Mass Mons (Mai)', 'content_nl' => 'Kidical Mass Mons (Wallonië).', 'content_fr' => 'Kidical Mass Mons (Wallonie).', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Mons', 'groups' => ['mons']],
            ['date' => '24/05', 'title_nl' => 'Kidical Mass Woluwe-Saint-Pierre & Woluwe-Saint-Lambert (Mai)', 'title_fr' => 'Kidical Mass Woluwe-Saint-Pierre & Woluwe-Saint-Lambert (Mai)', 'content_nl' => 'Kidical Mass in Woluwe-Saint-Pierre en Woluwe-Saint-Lambert.', 'content_fr' => 'Kidical Mass à Woluwe-Saint-Pierre et Woluwe-Saint-Lambert.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Woluwe-Saint-Pierre / Woluwe-Saint-Lambert', 'groups' => ['woluwe-saint-pierre-woluwe-saint-lambert']],
            ['date' => '24/05', 'title_nl' => 'Kidical Mass Bruxelles Ville - Brussel Stad', 'title_fr' => 'Kidical Mass Bruxelles Ville - Brussel Stad', 'content_nl' => 'Kidical Mass in Brussel Stad.', 'content_fr' => 'Kidical Mass à Bruxelles-Ville.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Bruxelles Ville - Brussel Stad', 'groups' => ['bruxelles-ville-brussel-stad']],
            ['date' => '31/05', 'title_nl' => 'Kidical Mass Namur - Namen', 'title_fr' => 'Kidical Mass Namur - Namen', 'content_nl' => 'Kidical Mass in Namur.', 'content_fr' => 'Kidical Mass à Namur.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Namur', 'groups' => ['namur']],
            ['date' => '31/05', 'title_nl' => 'Kidical Mass Schaerbeek - Schaarbeek (Mai)', 'title_fr' => 'Kidical Mass Schaerbeek - Schaarbeek (Mai)', 'content_nl' => 'Kidical Mass in Schaerbeek.', 'content_fr' => 'Kidical Mass à Schaerbeek.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Schaerbeek', 'groups' => ['schaerbeek-schaarbeek']],
            ['date' => '31/05', 'title_nl' => 'Kidical Mass Watermael-Boitsfort - Auderghem (Mai)', 'title_fr' => 'Kidical Mass Watermael-Boitsfort - Auderghem (Mai)', 'content_nl' => 'Kidical Mass in Watermael-Boitsfort en Auderghem.', 'content_fr' => 'Kidical Mass à Watermael-Boitsfort et Auderghem.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Watermael-Boitsfort / Auderghem', 'groups' => ['watermael-boitsfort-auderghem']],
            ['date' => '07/06', 'title_nl' => 'Kidical Mass Forest - Vorst (Juin)', 'title_fr' => 'Kidical Mass Forest - Vorst (Juin)', 'content_nl' => 'Kidical Mass in Forest - Vorst.', 'content_fr' => 'Kidical Mass à Forest - Vorst.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Forest - Vorst', 'groups' => ['forest-vorst']],
            ['date' => '07/06', 'title_nl' => 'Kidical Mass Etterbeek', 'title_fr' => 'Kidical Mass Etterbeek', 'content_nl' => 'Kidical Mass in Etterbeek.', 'content_fr' => 'Kidical Mass à Etterbeek.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Etterbeek', 'groups' => ['etterbeek']],
            ['date' => '14/06', 'title_nl' => 'Kidical Mass Laeken - Laken & BXL TOUR', 'title_fr' => 'Kidical Mass Laeken - Laken & BXL TOUR', 'content_nl' => 'Kidical Mass met BXL TOUR.', 'content_fr' => 'Kidical Mass avec BXL TOUR.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Laeken - Laken', 'groups' => ['laeken-laken']],
            ['date' => '14/06', 'title_nl' => 'Kidical Mass Koekelberg / Berchem / Ganshoren', 'title_fr' => 'Kidical Mass Koekelberg / Berchem / Ganshoren', 'content_nl' => 'Kidical Mass in Koekelberg, Berchem en Ganshoren.', 'content_fr' => 'Kidical Mass à Koekelberg, Berchem et Ganshoren.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Koekelberg / Berchem / Ganshoren', 'groups' => ['koekelberg-berchem-ganshoren']],
            ['date' => '21/06', 'title_nl' => 'Kidical Mass Uccle - Ukkel', 'title_fr' => 'Kidical Mass Uccle - Ukkel', 'content_nl' => 'Kidical Mass in Uccle - Ukkel.', 'content_fr' => 'Kidical Mass à Uccle - Ukkel.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Uccle - Ukkel', 'groups' => ['uccle-ukkel']],
            ['date' => '21/06', 'title_nl' => 'Kidical Mass Watermael-Boitsfort - Auderghem (Juin)', 'title_fr' => 'Kidical Mass Watermael-Boitsfort - Auderghem (Juin)', 'content_nl' => 'Kidical Mass in Watermael-Boitsfort en Auderghem.', 'content_fr' => 'Kidical Mass à Watermael-Boitsfort et Auderghem.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Watermael-Boitsfort / Auderghem', 'groups' => ['watermael-boitsfort-auderghem']],
            ['date' => '28/06', 'title_nl' => 'Kidical Mass Schaerbeek - Schaarbeek (Juin)', 'title_fr' => 'Kidical Mass Schaerbeek - Schaarbeek (Juin)', 'content_nl' => 'Kidical Mass in Schaerbeek.', 'content_fr' => 'Kidical Mass à Schaerbeek.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Schaerbeek', 'groups' => ['schaerbeek-schaarbeek']],
            ['date' => '28/06', 'title_nl' => 'Kidical Mass Woluwe-Saint-Pierre - Woluwe-Saint-Lambert (Juin)', 'title_fr' => 'Kidical Mass Woluwe-Saint-Pierre - Woluwe-Saint-Lambert (Juin)', 'content_nl' => 'Kidical Mass in Woluwe-Saint-Pierre en Woluwe-Saint-Lambert.', 'content_fr' => 'Kidical Mass à Woluwe-Saint-Pierre et Woluwe-Saint-Lambert.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Woluwe-Saint-Pierre / Woluwe-Saint-Lambert', 'groups' => ['woluwe-saint-pierre-woluwe-saint-lambert']],
            ['date' => '30/08', 'title_nl' => 'Kidical Mass Schaerbeek - Schaarbeek (Août)', 'title_fr' => 'Kidical Mass Schaerbeek - Schaarbeek (Août)', 'content_nl' => 'Kidical Mass in Schaerbeek.', 'content_fr' => 'Kidical Mass à Schaerbeek.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Schaerbeek', 'groups' => ['schaerbeek-schaarbeek']],
            ['date' => '30/08', 'title_nl' => 'Kidical Mass Watermael-Boitsfort - Auderghem (Août)', 'title_fr' => 'Kidical Mass Watermael-Boitsfort - Auderghem (Août)', 'content_nl' => 'Kidical Mass in Watermael-Boitsfort en Auderghem.', 'content_fr' => 'Kidical Mass à Watermael-Boitsfort et Auderghem.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Watermael-Boitsfort / Auderghem', 'groups' => ['watermael-boitsfort-auderghem']],
            ['date' => '06/09', 'title_nl' => 'Kidical Mass Jette', 'title_fr' => 'Kidical Mass Jette', 'content_nl' => 'Kidical Mass in Jette.', 'content_fr' => 'Kidical Mass à Jette.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Jette', 'groups' => ['jette']],
            ['date' => '06/09', 'title_nl' => 'Kidical Mass Uccle - Ukkel (Septembre)', 'title_fr' => 'Kidical Mass Uccle - Ukkel (Septembre)', 'content_nl' => 'Kidical Mass in Uccle - Ukkel.', 'content_fr' => 'Kidical Mass à Uccle - Ukkel.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Uccle - Ukkel', 'groups' => ['uccle-ukkel']],
            ['date' => '13/09', 'title_nl' => 'Kidical Mass Bruxelles Ville - Brussel Stad (Septembre)', 'title_fr' => 'Kidical Mass Bruxelles Ville - Brussel Stad (Septembre)', 'content_nl' => 'Kidical Mass in Brussel Stad.', 'content_fr' => 'Kidical Mass à Bruxelles-Ville.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Bruxelles Ville - Brussel Stad', 'groups' => ['bruxelles-ville-brussel-stad']],
            ['date' => '13/09', 'title_nl' => 'Kidical Mass Forest - Vorst (Septembre)', 'title_fr' => 'Kidical Mass Forest - Vorst (Septembre)', 'content_nl' => 'Kidical Mass in Forest - Vorst.', 'content_fr' => 'Kidical Mass à Forest - Vorst.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Forest - Vorst', 'groups' => ['forest-vorst']],
            ['date' => '20/09', 'title_nl' => 'Journée Sans Voitures - Autoloze Zondag', 'title_fr' => 'Journée Sans Voitures - Autoloze Zondag', 'content_nl' => 'Autovrije dag (geen Kidical Mass).', 'content_fr' => 'Journée sans voitures (pas de Kidical Mass).', 'activity_type' => ActivityType::OTHER, 'location' => 'Brussel', 'groups' => ['bruxelles-ville-brussel-stad']],
            ['date' => '27/09', 'title_nl' => 'Kidical Mass Schaerbeek - Evere / Haren', 'title_fr' => 'Kidical Mass Schaerbeek - Evere / Haren', 'content_nl' => 'Kidical Mass in Schaerbeek en Evere/Haren.', 'content_fr' => 'Kidical Mass à Schaerbeek et Evere/Haren.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Schaerbeek / Evere / Haren', 'groups' => ['schaerbeek-schaarbeek', 'evere-haren']],
            ['date' => '27/09', 'title_nl' => 'Kidical Mass Woluwe-Saint-Pierre - Woluwe-Saint-Lambert (Septembre)', 'title_fr' => 'Kidical Mass Woluwe-Saint-Pierre - Woluwe-Saint-Lambert (Septembre)', 'content_nl' => 'Kidical Mass in Woluwe-Saint-Pierre en Woluwe-Saint-Lambert.', 'content_fr' => 'Kidical Mass à Woluwe-Saint-Pierre et Woluwe-Saint-Lambert.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Woluwe-Saint-Pierre / Woluwe-Saint-Lambert', 'groups' => ['woluwe-saint-pierre-woluwe-saint-lambert']],
            ['date' => '04/10', 'title_nl' => 'Kidical Mass Anderlecht', 'title_fr' => 'Kidical Mass Anderlecht', 'content_nl' => 'Kidical Mass in Anderlecht.', 'content_fr' => 'Kidical Mass à Anderlecht.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Anderlecht', 'groups' => ['anderlecht']],
            ['date' => '04/10', 'title_nl' => 'Kidical Mass Watermael-Boitsfort - Auderghem (Octobre)', 'title_fr' => 'Kidical Mass Watermael-Boitsfort - Auderghem (Octobre)', 'content_nl' => 'Kidical Mass in Watermael-Boitsfort en Auderghem.', 'content_fr' => 'Kidical Mass à Watermael-Boitsfort et Auderghem.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Watermael-Boitsfort / Auderghem', 'groups' => ['watermael-boitsfort-auderghem']],
            ['date' => '11/10', 'title_nl' => 'Kidical Mass Koekelberg / Berchem / Ganshoren (Octobre)', 'title_fr' => 'Kidical Mass Koekelberg / Berchem / Ganshoren (Octobre)', 'content_nl' => 'Kidical Mass in Koekelberg, Berchem en Ganshoren.', 'content_fr' => 'Kidical Mass à Koekelberg, Berchem et Ganshoren.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Koekelberg / Berchem / Ganshoren', 'groups' => ['koekelberg-berchem-ganshoren']],
            ['date' => '17/10', 'title_nl' => 'Bright Light Parade - Parade des Lumières - Lichtparade', 'title_fr' => 'Bright Light Parade - Parade des Lumières - Lichtparade', 'content_nl' => 'Lichtparade als seizoensevenement.', 'content_fr' => 'Parade des lumières comme événement de saison.', 'activity_type' => ActivityType::OTHER, 'location' => 'Brussel', 'groups' => ['bruxelles-ville-brussel-stad']],
            ['date' => '25/10', 'title_nl' => 'Kidical Mass Woluwe-Saint-Pierre - Woluwe-Saint-Lambert (Octobre)', 'title_fr' => 'Kidical Mass Woluwe-Saint-Pierre - Woluwe-Saint-Lambert (Octobre)', 'content_nl' => 'Kidical Mass in Woluwe-Saint-Pierre en Woluwe-Saint-Lambert.', 'content_fr' => 'Kidical Mass à Woluwe-Saint-Pierre et Woluwe-Saint-Lambert.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Woluwe-Saint-Pierre / Woluwe-Saint-Lambert', 'groups' => ['woluwe-saint-pierre-woluwe-saint-lambert']],
            ['date' => '31/10', 'title_nl' => 'Kidical Mass Schaerbeek SPOOKY EDITION', 'title_fr' => 'Kidical Mass Schaerbeek SPOOKY EDITION', 'content_nl' => 'Spooky editie van Kidical Mass in Schaerbeek.', 'content_fr' => 'Édition spooky de Kidical Mass à Schaerbeek.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Schaerbeek', 'groups' => ['schaerbeek-schaarbeek']],
            ['date' => '08/11', 'title_nl' => 'Kidical Mass Forest - Vorst (Novembre)', 'title_fr' => 'Kidical Mass Forest - Vorst (Novembre)', 'content_nl' => 'Kidical Mass in Forest - Vorst.', 'content_fr' => 'Kidical Mass à Forest - Vorst.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Forest - Vorst', 'groups' => ['forest-vorst']],
            ['date' => '11/11', 'title_nl' => 'Kidical Mass Watermael-Boitsfort - Auderghem (Novembre)', 'title_fr' => 'Kidical Mass Watermael-Boitsfort - Auderghem (Novembre)', 'content_nl' => 'Kidical Mass in Watermael-Boitsfort en Auderghem.', 'content_fr' => 'Kidical Mass à Watermael-Boitsfort et Auderghem.', 'activity_type' => ActivityType::KIDICALMASS, 'location' => 'Watermael-Boitsfort / Auderghem', 'groups' => ['watermael-boitsfort-auderghem']],
            ['date' => '15/11', 'title_nl' => 'Fête fin de saison - Einde seizoensfeest', 'title_fr' => 'Fête fin de saison - Einde seizoensfeest', 'content_nl' => 'Afsluitfeest van het Kidical-seizoen.', 'content_fr' => 'Fête de fin de saison Kidical.', 'activity_type' => ActivityType::MEETING, 'location' => 'Brussel', 'groups' => ['bruxelles-ville-brussel-stad']],
        ];
    }
}

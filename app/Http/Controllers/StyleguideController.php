<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class StyleguideController extends Controller
{
    public function __invoke(): View
    {
        $activity = $this->sampleActivity('Kidical Mass Gent', 'Sint-Pietersplein, Gent', 1);
        $activityB = $this->sampleActivity('Grande Kidical Mass Brussel', 'Jubelpark, Brussel', 2, days: 9);
        $workshop = $this->sampleActivity('Fietsherstel workshop', 'Wijkcentrum, Gent', 3, days: 5, type: ActivityType::WORKSHOP);
        $meeting = $this->sampleActivity('Teamvergadering', 'Online', 4, days: 7, type: ActivityType::MEETING);

        $article = new Article([
            'title_nl' => 'Eerste rit van het seizoen was een groot succes',
            'content_nl' => '<p>Met meer dan tweehonderd fietsers reden we samen door de '
                .'straten. Hier lees je hoe het was en wanneer de volgende rit plaatsvindt.</p>',
        ]);
        $article->id = 1;
        $article->created_at = Carbon::parse('2026-05-18');
        $article->setRelation('author', new User(['name' => 'Leticia']));

        // year => group count, as group-statistics expects.
        $statistics = [2021 => 2, 2022 => 5, 2023 => 9, 2024 => 14, 2025 => 21];

        return view('styleguide', [
            'activity' => $activity,
            'activityB' => $activityB,
            'workshop' => $workshop,
            'meeting' => $meeting,
            'article' => $article,
            'statistics' => $statistics,
            'dayPeriodKey' => $activity->begin_date->toDateString(),
            'dayRows' => [['item' => $activity], ['item' => $activityB]],
            'monthPeriodKey' => $activity->begin_date->format('Y-m'),
            'monthRides' => [$activity, $activityB],
            'candidates' => $this->candidates(),
        ]);
    }

    private function sampleActivity(string $title, string $location, int $id, int $days = 2, ActivityType $type = ActivityType::KIDICALMASS): Activity
    {
        $activity = new Activity([
            'title_nl' => $title,
            'location' => $location,
            'begin_date' => Carbon::parse('2026-06-06 14:00')->addDays($days),
            'activity_type' => $type,
        ]);
        $activity->id = $id;

        return $activity;
    }

    /**
     * Extraction candidates found in the thorough page-template sweep.
     *
     * @return list<array{name: string, where: string, props: string}>
     */
    private function candidates(): array
    {
        return [
            [
                'name' => 'meta-row',
                'where' => 'activities/show — icoon + label + waarde als dt/dd (5×: start, locatie, afstand, duur, deelname)',
                'props' => 'icon, label, value (html), :if?',
            ],
            [
                'name' => 'icon-text-item',
                'where' => 'activities/show (promises, 4×) + about value-bladeren — icoon + titel + body in <li>; bijna-tweeling van feature-card (overweeg een variant i.p.v. nieuw component)',
                'props' => 'icon, title, slot',
            ],
            [
                'name' => 'email-opt-in',
                'where' => 'groups/show (2×) + activities/index — e-mailveld + verzendknop + succesbericht (Alpine toggle)',
                'props' => 'title, subtitle?, placeholder, submitLabel, successMessage, inputId',
            ],
            [
                'name' => 'group-pill',
                'where' => 'groups/index (3 secties) + articles/show — gelinkte groep-badge, soms met afstand-suffix',
                'props' => 'href, label, variant=default|mine, distance?',
            ],
            [
                'name' => 'empty-state',
                'where' => 'groups/show ("nog geen rit") + articles/index — gecentreerde kop + tekst + optionele acties',
                'props' => 'title, message, actions?',
            ],
            [
                'name' => 'media-gallery',
                'where' => 'articles/show — kop + grid van aspect-ratio afbeeldingen met hover-zoom (lagere prioriteit)',
                'props' => 'title, items, aspect=4/3|16/9, columns?',
            ],
            [
                'name' => '[content] group-statistics vertalen',
                'where' => 'components/group-statistics — bevat hardcoded Engelse copy ("We are growing!", "group/groups")',
                'props' => 'gebruik __() lang-keys + parameteriseer de titel',
            ],
        ];
    }
}

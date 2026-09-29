<?php

namespace App\Livewire;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;
use App\Support\Location\CurrentLocation;
use App\Support\Location\Proximity;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

class RideCalendar extends Component
{
    #[Url(as: 'when', history: true)]
    public string $when = 'aankomend';

    public function showPast(): void
    {
        $this->when = 'voorbije';
    }

    public function showUpcoming(): void
    {
        $this->when = 'aankomend';
    }

    public function render(): View
    {
        $when = $this->when === 'voorbije' ? 'voorbije' : 'aankomend';

        $query = Activity::query()
            ->published()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->with(['groups']);

        if ($when === 'voorbije') {
            $activities = $query->where('begin_date', '<', now()->startOfDay())
                ->orderByDesc('begin_date')->limit(24)->get();

            return view('livewire.ride-calendar', [
                'when' => $when,
                'location' => null,
                'byPeriod' => $activities->groupBy(fn ($a) => $a->begin_date->format('Y-m')),
                'sections' => null,
                'emptyLead' => null,
                'hasActivities' => $activities->isNotEmpty(),
                'rideCount' => $activities->count(),
            ]);
        }

        $activities = $query->where('begin_date', '>=', now()->startOfDay())
            ->orderBy('begin_date')->get();

        $location = CurrentLocation::resolve();

        // Without a location: one plain chronological list (rows carry a null distance).
        if (! $location) {
            $rows = $activities->map(fn ($a) => ['item' => $a, 'distance_km' => null]);

            return view('livewire.ride-calendar', [
                'when' => $when,
                'location' => null,
                'byPeriod' => $rows->groupBy(fn ($r) => $r['item']->begin_date->format('Y-m-d')),
                'sections' => null,
                'emptyLead' => null,
                'hasActivities' => $activities->isNotEmpty(),
                'rideCount' => $activities->count(),
            ]);
        }

        [$sections, $emptyLead] = $this->proximitySections($activities, $location);

        return view('livewire.ride-calendar', [
            'when' => $when,
            'location' => $location,
            'byPeriod' => null,
            'sections' => $sections,
            'emptyLead' => $emptyLead,
            'hasActivities' => $activities->isNotEmpty(),
            'rideCount' => $activities->count(),
        ]);
    }

    /**
     * Group upcoming rides into distance bands, nearest first. Nothing is hidden:
     * rides beyond the region radius or with an unknown postcode land in `far`.
     * Leading empty bands collapse into one lead line naming the widest empty radius.
     *
     * @param  Collection<int, Activity>  $activities  sorted by date
     * @param  array{zip: string, lat: float, lng: float, name: string}  $location
     * @return array{0: list<array{band: string, radius_km: float|null, byDay: Collection<string, Collection<int, array{item: Activity, distance_km: float|null}>>}>, 1: array{km: float, place: string}|null}
     */
    protected function proximitySections(Collection $activities, array $location): array
    {
        $coordsByZip = PostalCode::whereIn('zip', $activities->pluck('postal_code')->filter()->unique())
            ->get()->keyBy('zip');

        $bounds = [
            'nearby' => (float) config('location.nearby_radius_km'),
            'region' => (float) config('location.regio_radius_km'),
        ];

        $partitions = Proximity::partitionByBands(
            $activities,
            ['lat' => $location['lat'], 'lng' => $location['lng']],
            $bounds,
            function (Activity $activity) use ($coordsByZip) {
                $postalCode = $activity->postal_code ? $coordsByZip->get($activity->postal_code) : null;

                return $postalCode ? ['lat' => $postalCode->latitude, 'lng' => $postalCode->longitude] : null;
            },
        );

        $sections = [];
        $emptyLead = null;

        foreach ($partitions as $band => $rows) {
            if ($rows->isEmpty()) {
                // Only empty bands *before* the first filled one feed the lead line.
                if ($sections === [] && isset($bounds[$band])) {
                    $emptyLead = ['km' => $bounds[$band], 'place' => $location['name']];
                }

                continue;
            }

            $sections[] = [
                'band' => $band,
                'radius_km' => $bounds[$band] ?? null,
                'byDay' => $rows->groupBy(fn ($row) => $row['item']->begin_date->format('Y-m-d')),
            ];
        }

        // No rides at all: the regular empty state takes over, no lead line.
        if ($sections === []) {
            $emptyLead = null;
        }

        return [$sections, $emptyLead];
    }
}

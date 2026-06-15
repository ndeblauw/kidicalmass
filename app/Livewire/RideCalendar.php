<?php

namespace App\Livewire;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;
use App\Support\Location\CurrentLocation;
use App\Support\Location\Proximity;
use Livewire\Attributes\Url;
use Livewire\Component;

class RideCalendar extends Component
{
    #[Url(as: 'when')]
    public string $when = 'aankomend';

    /** URL-bound radius tab: dichtbij | regio | belgie */
    #[Url(as: 'radius')]
    public string $radius = 'dichtbij';

    public function showPast(): void
    {
        $this->when = 'voorbije';
    }

    public function showUpcoming(): void
    {
        $this->when = 'aankomend';
    }

    public function setRadius(string $value): void
    {
        if (in_array($value, ['dichtbij', 'regio', 'belgie'], true)) {
            $this->radius = $value;
        }
    }

    public function render()
    {
        $when = $this->when === 'voorbije' ? 'voorbije' : 'aankomend';

        $query = Activity::published()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->with(['groups']);

        if ($when === 'voorbije') {
            $activities = $query->where('begin_date', '<', now()->startOfDay())
                ->orderByDesc('begin_date')->limit(24)->get();

            return view('livewire.ride-calendar', [
                'when' => $when,
                'location' => null,
                'radius' => $this->radius,
                'byPeriod' => $activities->groupBy(fn ($a) => $a->begin_date->format('Y-m')),
                'hasActivities' => $activities->isNotEmpty(),
                'isEmpty' => $activities->isEmpty(),
            ]);
        }

        $activities = $query->where('begin_date', '>=', now()->startOfDay())
            ->orderBy('begin_date')->get();

        $location = CurrentLocation::resolve();

        // When no location is set, show all rides unfiltered (annotated with null distance).
        if (! $location) {
            $rows = $activities->map(fn ($a) => ['item' => $a, 'distance_km' => null]);

            return view('livewire.ride-calendar', [
                'when' => $when,
                'location' => null,
                'radius' => $this->radius,
                'byPeriod' => $rows->groupBy(fn ($r) => $r['item']->begin_date->format('Y-m-d')),
                'hasActivities' => $activities->isNotEmpty(),
                'isEmpty' => false,
            ]);
        }

        // Resolve postal-code coordinates for every unique zip in the result set.
        $coordsByZip = PostalCode::whereIn('zip', $activities->pluck('postal_code')->filter()->unique())
            ->get()->keyBy('zip');

        $origin = ['lat' => $location['lat'], 'lng' => $location['lng']];

        // Annotate every activity with its distance from the user's location.
        $annotated = $activities->map(function ($activity) use ($origin, $coordsByZip) {
            $pc = $activity->postal_code ? $coordsByZip->get($activity->postal_code) : null;
            $coords = $pc ? ['lat' => $pc->latitude, 'lng' => $pc->longitude] : null;

            return [
                'item' => $activity,
                'distance_km' => $coords ? round(Proximity::distanceKm($origin, $coords), 1) : null,
            ];
        });

        // Filter by active radius. 'belgie' shows everything.
        if ($this->radius !== 'belgie') {
            $radiusKm = $this->radius === 'regio'
                ? (float) config('location.regio_radius_km')
                : (float) config('location.nearby_radius_km');

            $annotated = $annotated->filter(
                fn ($row) => $row['distance_km'] === null || $row['distance_km'] <= $radiusKm
            );
        }

        $byPeriod = $annotated->values()->groupBy(fn ($r) => $r['item']->begin_date->format('Y-m-d'));

        return view('livewire.ride-calendar', [
            'when' => $when,
            'location' => $location,
            'radius' => $this->radius,
            'byPeriod' => $byPeriod,
            'hasActivities' => $activities->isNotEmpty(),
            'isEmpty' => $byPeriod->isEmpty() && $activities->isNotEmpty(),
        ]);
    }
}

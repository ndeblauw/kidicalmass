<?php

namespace App\Livewire;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\PostalCode;
use App\Support\Location\CurrentLocation;
use App\Support\Location\Proximity;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * The Kalender's ride list. Location-first: when the visitor has set "waar woon je?",
 * upcoming rides split into "In de buurt" (<= radius, by date) and "Verderaf" (by date).
 * Without a location it is one undivided list by date. Rides-only (D-2/J1); no pagination.
 */
class RideCalendar extends Component
{
    #[Url(as: 'when')]
    public string $when = 'aankomend';

    public function showPast(): void
    {
        $this->when = 'voorbije';
    }

    public function showUpcoming(): void
    {
        $this->when = 'aankomend';
    }

    public function render()
    {
        $when = $this->when === 'voorbije' ? 'voorbije' : 'aankomend';

        $query = Activity::query()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->with(['groups']);

        if ($when === 'voorbije') {
            $activities = $query->where('begin_date', '<', now()->startOfDay())
                ->orderByDesc('begin_date')->limit(24)->get();

            return view('livewire.ride-calendar', [
                'when' => $when,
                'location' => null,
                'nearbyByPeriod' => collect(),
                'farByPeriod' => collect(),
                'byPeriod' => $activities->groupBy(fn ($a) => $a->begin_date->format('Y-m')),
                'hasActivities' => $activities->isNotEmpty(),
            ]);
        }

        $activities = $query->where('begin_date', '>=', now()->startOfDay())
            ->orderBy('begin_date')->get();

        $location = CurrentLocation::resolve();

        if (! $location) {
            return view('livewire.ride-calendar', [
                'when' => $when,
                'location' => null,
                'nearbyByPeriod' => collect(),
                'farByPeriod' => collect(),
                'byPeriod' => $activities->groupBy(fn ($a) => $a->begin_date->format('Y-m-d')),
                'hasActivities' => $activities->isNotEmpty(),
            ]);
        }

        $coordsByZip = PostalCode::whereIn('zip', $activities->pluck('postal_code')->filter()->unique())
            ->get()->keyBy('zip');

        $partition = Proximity::partitionByRadius(
            $activities,
            ['lat' => $location['lat'], 'lng' => $location['lng']],
            (float) config('location.nearby_radius_km'),
            function (Activity $a) use ($coordsByZip) {
                $pc = $a->postal_code ? $coordsByZip->get($a->postal_code) : null;

                return $pc ? ['lat' => $pc->latitude, 'lng' => $pc->longitude] : null;
            },
        );

        return view('livewire.ride-calendar', [
            'when' => $when,
            'location' => $location,
            'nearbyByPeriod' => $this->groupAnnotated($partition['nearby']),
            'farByPeriod' => $this->groupAnnotated($partition['far']),
            'byPeriod' => collect(),
            'hasActivities' => $activities->isNotEmpty(),
        ]);
    }

    /**
     * Group annotated `['item' => Activity, 'distance_km' => ?float]` rows by day,
     * preserving the distance so the card can show a label.
     *
     * @param  Collection<int, array{item: Activity, distance_km: float|null}>  $rows
     * @return Collection<string, Collection<int, array{item: Activity, distance_km: float|null}>>
     */
    protected function groupAnnotated(Collection $rows): Collection
    {
        return $rows->groupBy(fn ($row) => $row['item']->begin_date->format('Y-m-d'));
    }
}

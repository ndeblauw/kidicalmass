<?php

namespace App\Livewire;

use App\Enums\ActivityType;
use App\Models\Activity;
use App\Models\Group;
use Illuminate\Support\Collection;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * The Kalender's filterable ride list. Location-first: the gemeente is the primary
 * control (Flux searchable select), the period is a demoted "voorbije ritten" link.
 * Rides-only (D-2/J1); a calendar flows so there's no pagination.
 */
class RideCalendar extends Component
{
    #[Url(as: 'gemeente')]
    public ?int $gemeente = null;

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

    /**
     * Visible chapters for the gemeente picker — ordered by postal code,
     * labelled "1083 – Ganshoren" so the search matches zip or name.
     *
     * @return Collection<int, Group>
     */
    public function gemeenten(): Collection
    {
        return Group::visible()
            ->orderBy('zip')
            ->orderBy('name')
            ->get(['id', 'name', 'zip']);
    }

    public function render()
    {
        $when = $this->when === 'voorbije' ? 'voorbije' : 'aankomend';

        $query = Activity::query()
            ->where('activity_type', ActivityType::KIDICALMASS)
            ->with(['groups']);

        if ($this->gemeente) {
            $query->whereHas('groups', fn ($groups) => $groups->where('groups.id', $this->gemeente));
        }

        if ($when === 'voorbije') {
            $query->where('begin_date', '<', now()->startOfDay())->orderByDesc('begin_date')->limit(24);
        } else {
            $query->where('begin_date', '>=', now()->startOfDay())->orderBy('begin_date');
        }

        $activities = $query->get();
        $gemeenten = $this->gemeenten();

        return view('livewire.ride-calendar', [
            'when' => $when,
            'gemeenten' => $gemeenten,
            'selectedGemeente' => $this->gemeente ? optional($gemeenten->firstWhere('id', $this->gemeente))->name : null,
            'byPeriod' => $activities->groupBy(
                fn ($activity) => $activity->begin_date->format($when === 'voorbije' ? 'Y-m' : 'Y-m-d')
            ),
            'headerFormat' => $when === 'voorbije' ? 'MMMM YYYY' : 'dddd D MMMM',
            'hasActivities' => $activities->isNotEmpty(),
        ]);
    }
}

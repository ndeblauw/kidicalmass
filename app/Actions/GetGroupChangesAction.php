<?php

namespace App\Actions;

use App\Models\Group;
use App\Models\User;
use Carbon\CarbonInterface;

class GetGroupChangesAction
{
    public function __construct(
        private Group $group,
        private ?CarbonInterface $startDate = null,
        private ?CarbonInterface $endDate = null,
        private ?CarbonInterface $upcomingUntil = null,
    ) {
        $this->startDate ??= now()->subMonth();
        $this->endDate ??= now();
        $this->upcomingUntil ??= now()->addMonths(3);
    }

    public function execute(): GroupChangesResult
    {
        $newActivities = $this->group->activities()
            ->whereBetween('activities.created_at', [$this->startDate, $this->endDate])
            ->get();

        $updatedActivities = $this->group->activities()
            ->whereBetween('activities.updated_at', [$this->startDate, $this->endDate])
            ->whereColumn('activities.updated_at', '!=', 'activities.created_at')
            ->get();

        $newMembers = $this->group->users()
            ->wherePivotBetween('created_at', [$this->startDate, $this->endDate])
            ->get();

        $newCaptains = $newMembers->filter(fn (User $u) => $u->pivot->role === 'captain')->values();
        $newPinkVests = $newMembers->filter(fn (User $u) => $u->pivot->role === 'pinkvest')->values();
        $newInterested = $newMembers->filter(fn (User $u) => is_null($u->pivot->role))->values();

        $newArticles = $this->group->articles()
            ->whereBetween('articles.created_at', [$this->startDate, $this->endDate])
            ->get();

        $updatedArticles = $this->group->articles()
            ->whereBetween('articles.updated_at', [$this->startDate, $this->endDate])
            ->whereColumn('articles.updated_at', '!=', 'articles.created_at')
            ->get();

        // Rides that already happened inside the window and have a recap gallery,
        // newest first. These are the "in beeld" block, the fresh monthly value.
        $recentRidesWithPhotos = $this->group->activities()
            ->whereBetween('activities.begin_date', [$this->startDate, $this->endDate])
            ->whereHas('media', fn ($query) => $query->where('collection_name', 'gallery'))
            ->orderByDesc('activities.begin_date')
            ->get();

        // A glance ahead: published activities of any type still to come, soonest
        // first. The calendar is built at year-start, so this is not "what changed"
        // but "what is coming up" within the look-ahead horizon.
        $upcomingActivities = $this->group->activities()
            ->where('activities.published', true)
            ->where('activities.begin_date', '>', $this->endDate)
            ->where('activities.begin_date', '<=', $this->upcomingUntil)
            ->orderBy('activities.begin_date')
            ->get();

        return new GroupChangesResult(
            startDate: $this->startDate,
            endDate: $this->endDate,
            group: $this->group,
            newActivities: $newActivities,
            updatedActivities: $updatedActivities,
            newCaptains: $newCaptains,
            newPinkVests: $newPinkVests,
            newInterested: $newInterested,
            newArticles: $newArticles,
            updatedArticles: $updatedArticles,
            recentRidesWithPhotos: $recentRidesWithPhotos,
            upcomingActivities: $upcomingActivities,
        );
    }
}

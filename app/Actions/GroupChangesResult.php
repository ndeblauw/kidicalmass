<?php

namespace App\Actions;

use App\Models\Group;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class GroupChangesResult
{
    public function __construct(
        public readonly CarbonInterface $startDate,
        public readonly CarbonInterface $endDate,
        public readonly Group $group,
        public readonly Collection $newActivities,
        public readonly Collection $updatedActivities,
        public readonly Collection $newCaptains,
        public readonly Collection $newPinkVests,
        public readonly Collection $newInterested,
        public readonly Collection $newArticles,
        public readonly Collection $updatedArticles,
    ) {}

    public function hasAny(): bool
    {
        return $this->newActivities->isNotEmpty()
            || $this->updatedActivities->isNotEmpty()
            || $this->newCaptains->isNotEmpty()
            || $this->newPinkVests->isNotEmpty()
            || $this->newInterested->isNotEmpty()
            || $this->newArticles->isNotEmpty()
            || $this->updatedArticles->isNotEmpty();
    }

    public function summary(): array
    {
        return [
            'activities_added' => $this->newActivities->count(),
            'activities_updated' => $this->updatedActivities->count(),
            'members_added' => $this->membersAddedCount(),
            'captains_added' => $this->newCaptains->count(),
            'pinkvests_added' => $this->newPinkVests->count(),
            'interested_added' => $this->newInterested->count(),
            'articles_added' => $this->newArticles->count(),
            'articles_updated' => $this->updatedArticles->count(),
        ];
    }

    public function membersAddedCount(): int
    {
        return $this->newCaptains->count()
            + $this->newPinkVests->count()
            + $this->newInterested->count();
    }
}

<?php

namespace App\View\Components;

use App\Models\Group;
use Illuminate\View\Component;

class GroupStatistics extends Component
{
    public array $statistics;

    public function __construct()
    {
        $this->statistics = $this->calculateStatistics();
    }

    private function calculateStatistics(): array
    {
        // Get all groups with started_at dates
        $groups = Group::query()
            ->whereNotNull('started_at')
            ->orderBy('started_at')
            ->get();

        // Group by year and count
        $stats = $groups->groupBy(function ($group) {
            return $group->started_at->format('Y');
        })->map->count();

        // Calculate cumulative totals
        $cumulative = [];
        $total = 0;
        foreach ($stats as $year => $count) {
            $total += $count;
            $cumulative[$year] = $total;
        }

        return $cumulative;
    }

    public function render()
    {
        return view('components.group-statistics');
    }
}

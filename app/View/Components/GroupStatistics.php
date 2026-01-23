<?php

namespace App\View\Components;

use App\Models\Group;
use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class GroupStatistics extends Component
{
    public array $statistics;

    public function __construct()
    {
        $this->statistics = $this->calculateStatistics();
    }

    private function calculateStatistics(): array
    {
        // Get all groups with created_at dates
        $groups = Group::query()
            ->whereNotNull('created_at')
            ->orderBy('created_at')
            ->get();

        // Group by year and count
        $stats = $groups->groupBy(function ($group) {
            return $group->created_at->format('Y');
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

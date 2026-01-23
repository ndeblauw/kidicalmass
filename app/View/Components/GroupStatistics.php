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
        $stats = Group::query()
            ->whereNotNull('created_at')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->pluck('count', 'year')
            ->toArray();

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

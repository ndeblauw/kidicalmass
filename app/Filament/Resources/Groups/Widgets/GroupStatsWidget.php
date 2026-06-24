<?php

namespace App\Filament\Resources\Groups\Widgets;

use App\Models\Group;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;

class GroupStatsWidget extends BaseWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        /** @var Group $group */
        $group = $this->record;

        $captains = $group->users()->wherePivot('role', 'captain')->count();
        $pinkVests = $group->users()->wherePivot('role', 'pinkvest')->count();
        $interested = $group->users()->whereNull('role')->count();
        $totalMembers = $group->users()->count();

        $upcomingActivities = $group->activities()->where('begin_date', '>=', now())->count();
        $pastActivities = $group->activities()->whereRaw('DATE_ADD(begin_date, INTERVAL duration_minutes MINUTE) < NOW()')->count();
        $unpublished = $group->activities()->where('published', false)->count();

        return [
            Stat::make('Pink Vests', $pinkVests)
                ->description('Active volunteer escorts')
                ->color('pink')
                ->chart([$captains, $pinkVests, $interested]),
            Stat::make('Captains', $captains)
                ->description('Chapter leaders')
                ->color('warning'),
            Stat::make('Interested', $interested)
                ->description('Members without a role yet')
                ->color('gray'),
            Stat::make('Total Members', $totalMembers)
                ->description('All group members')
                ->color('success'),
            Stat::make('Upcoming Activities', $upcomingActivities)
                ->description('Scheduled rides & events')
                ->color('info'),
            Stat::make('Past Activities', $pastActivities)
                ->description('Completed rides & events')
                ->color('gray'),
            Stat::make('Unpublished', $unpublished)
                ->description('Needing review & publishing')
                ->color($unpublished > 0 ? 'danger' : 'success'),
        ];
    }
}

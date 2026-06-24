<?php

namespace App\Filament\Resources\Groups\Widgets;

use App\Filament\Resources\Activities\ActivityResource;
use App\Models\Activity;
use App\Models\Group;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UpcomingIncompleteActivitiesWidget extends BaseWidget
{
    public ?Model $record = null;

    protected function getTableDescription(): string
    {
        return 'Upcoming activities missing important information such as a route, location, or organizer. Complete these details before the event.';
    }

    protected function getTableQuery(): Builder
    {
        /** @var Group $group */
        $group = $this->record;

        return Activity::with(['organizer', 'media'])
            ->whereHas('groups', fn (Builder $q) => $q->whereKey($group))
            ->where('begin_date', '>=', now())
            ->orderBy('begin_date');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('title_nl')
                ->label('Activity')
                ->searchable(),
            TextColumn::make('begin_date')
                ->dateTime()
                ->sortable()
                ->label('Date'),
            TextColumn::make('missing_fields')
                ->label('Missing')
                ->state(fn (Activity $record): string => implode(', ', $record->missingFields()) ?: 'None')
                ->badge()
                ->color(fn (string $state): string => $state === 'None' ? 'success' : 'danger'),
            TextColumn::make('location')
                ->label('Location')
                ->searchable(),
            TextColumn::make('organizer.name')
                ->label('Organizer'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('edit')
                ->label('Edit')
                ->icon('heroicon-o-pencil')
                ->url(ActivityResource::getUrl('index')),
        ];
    }
}

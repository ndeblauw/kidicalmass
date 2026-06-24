<?php

namespace App\Filament\Resources\Groups\Widgets;

use App\Filament\Resources\Activities\ActivityResource;
use App\Models\Activity;
use App\Models\Group;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class UnpublishedActivitiesWidget extends BaseWidget
{
    public ?Model $record = null;

    protected function getTableDescription(): string
    {
        return 'Activities below are still in draft. Complete the missing fields and publish them when ready.';
    }

    protected function getTableQuery(): Builder
    {
        /** @var Group $group */
        $group = $this->record;

        return Activity::with(['author', 'media', 'pressArticles'])
            ->whereHas('groups', fn (Builder $q) => $q->whereKey($group))
            ->where('published', false)
            ->orderByDesc('begin_date');
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('title_nl')
                ->label('Title')
                ->searchable(),
            TextColumn::make('begin_date')
                ->dateTime()
                ->sortable()
                ->label('Date'),
            TextColumn::make('missing_fields')
                ->label('Needs')
                ->state(fn (Activity $record): string => implode(', ', $record->missingFields()) ?: 'None')
                ->badge()
                ->color(fn (string $state): string => $state === 'None' ? 'success' : 'danger'),
            IconColumn::make('has_main_image')
                ->label('Image')
                ->boolean()
                ->state(fn (Activity $record): bool => $record->hasMainImage()),
            IconColumn::make('has_route')
                ->label('Route')
                ->boolean()
                ->state(fn (Activity $record): bool => $record->hasRoute()),
            TextColumn::make('author.name')
                ->label('Author'),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('edit')
                ->label('Edit')
                ->icon('heroicon-o-pencil')
                ->url(ActivityResource::getUrl('index')),
            Action::make('publish')
                ->label('Publish')
                ->icon('heroicon-o-eye')
                ->color('success')
                ->action(function (Activity $record): void {
                    $record->update(['published' => true]);
                    Notification::make()->success()->title('Activity published')->send();
                }),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Action::make('create_activity')
                ->label('Create Activity')
                ->icon('heroicon-o-plus')
                ->url(ActivityResource::getUrl('index').'?create=1'),
        ];
    }
}

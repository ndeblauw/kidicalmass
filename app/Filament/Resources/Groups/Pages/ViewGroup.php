<?php

namespace App\Filament\Resources\Groups\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use App\Filament\Resources\Groups\GroupResource;
use App\Filament\Resources\Groups\Widgets\GroupStatsWidget;
use App\Filament\Resources\Groups\Widgets\RecentActivitiesFollowUpWidget;
use App\Filament\Resources\Groups\Widgets\UnpublishedActivitiesWidget;
use App\Filament\Resources\Groups\Widgets\UpcomingIncompleteActivitiesWidget;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewGroup extends ViewRecord
{
    protected static string $resource = GroupResource::class;

    protected static ?string $title = 'Captain Dashboard';

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Group Overview')
                    ->columns(4)
                    ->schema([
                        TextEntry::make('name')
                            ->extraAttributes(['class' => 'text-xl font-bold'])
                            ->columnSpan(2),
                        TextEntry::make('zip')
                            ->label('Postal Code'),
                        TextEntry::make('shortname')
                            ->label('Short Name'),
                        TextEntry::make('parent.name')
                            ->label('Parent Group')
                            ->default('—'),
                        TextEntry::make('started_at')
                            ->label('Started')
                            ->date(),
                        TextEntry::make('ended_at')
                            ->label('Ended')
                            ->date()
                            ->placeholder('Active'),
                    ]),
            ]);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            GroupStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            UnpublishedActivitiesWidget::class,
            RecentActivitiesFollowUpWidget::class,
            UpcomingIncompleteActivitiesWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manage_groups')
                ->label('Manage Groups')
                ->url(GroupResource::getUrl('index')),
            Action::make('manage_activities')
                ->label('Manage Activities')
                ->url(ActivityResource::getUrl('index')),
        ];
    }
}

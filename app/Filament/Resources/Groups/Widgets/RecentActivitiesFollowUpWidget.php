<?php

namespace App\Filament\Resources\Groups\Widgets;

use App\Filament\Resources\Activities\ActivityResource;
use App\Models\Activity;
use App\Models\Group;
use App\Models\PressArticle;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RecentActivitiesFollowUpWidget extends BaseWidget
{
    public ?Model $record = null;

    protected function getTableDescription(): string
    {
        return 'Recent rides that need photos or press coverage. Add media and link press articles to keep your chapter\'s history complete.';
    }

    protected function getTableQuery(): Builder
    {
        /** @var Group $group */
        $group = $this->record;

        $sixtyDaysAgo = now()->subDays(60);

        return Activity::with(['media', 'pressArticles'])
            ->whereHas('groups', fn (Builder $q) => $q->whereKey($group))
            ->whereRaw('DATE_ADD(begin_date, INTERVAL COALESCE(duration_minutes, 0) MINUTE) < NOW()')
            ->where('begin_date', '>=', $sixtyDaysAgo)
            ->orderByDesc('begin_date');
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
            IconColumn::make('has_main_image')
                ->label('Photos')
                ->boolean()
                ->state(fn (Activity $record): bool => $record->hasMainImage() || $record->hasGallery()),
            IconColumn::make('has_press_coverage')
                ->label('Press')
                ->boolean()
                ->state(fn (Activity $record): bool => $record->hasPressCoverage()),
            IconColumn::make('has_gallery')
                ->label('Gallery')
                ->boolean()
                ->state(fn (Activity $record): bool => $record->hasGallery()),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('add_photos')
                ->label('Add Photos')
                ->icon('heroicon-o-camera')
                ->url(ActivityResource::getUrl('index')),
            Action::make('add_press_coverage')
                ->label('Add Press Article')
                ->icon('heroicon-o-newspaper')
                ->form([
                    TextInput::make('title_nl')
                        ->label('Title (NL)')
                        ->requiredWithout('title_fr')
                        ->maxLength(255),
                    TextInput::make('title_fr')
                        ->label('Title (FR)')
                        ->requiredWithout('title_nl')
                        ->maxLength(255),
                    TextInput::make('outlet')
                        ->label('Outlet')
                        ->required()
                        ->maxLength(255)
                        ->helperText('News outlet name (RTBF, BRUZZ, HLN, …)'),
                    TextInput::make('url')
                        ->label('Article URL')
                        ->url()
                        ->maxLength(500),
                    DateTimePicker::make('published_at')
                        ->label('Published At')
                        ->default(now()),
                ])
                ->action(function (array $data, Activity $record): void {
                    $pressArticle = PressArticle::create([
                        'title_nl' => $data['title_nl'] ?? '',
                        'title_fr' => $data['title_fr'] ?? '',
                        'outlet' => $data['outlet'],
                        'url' => $data['url'] ?? null,
                        'published_at' => $data['published_at'] ?? now(),
                    ]);

                    $record->pressArticles()->attach($pressArticle);

                    Notification::make()->success()->title('Press article linked to activity')->send();
                }),
            Action::make('edit')
                ->label('Edit')
                ->icon('heroicon-o-pencil')
                ->url(ActivityResource::getUrl('index')),
        ];
    }
}

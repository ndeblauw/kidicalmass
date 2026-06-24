<?php

namespace App\Filament\Resources\Activities;

use App\Enums\ActivityType;
use App\Filament\Resources\Activities\Pages\ManageActivities;
use App\Models\Activity;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Content')
                    ->columnSpanFull()
                    ->description('Provide content in at least one language (NL or FR)')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title_nl')
                            ->requiredWithout('title_fr')
                            ->maxLength(255)
                            ->label('Title (NL)'),
                        TextInput::make('title_fr')
                            ->requiredWithout('title_nl')
                            ->maxLength(255)
                            ->label('Title (FR)'),
                        Textarea::make('content_nl')
                            ->requiredWithout('content_fr')
                            ->rows(5)
                            ->columnSpanFull()
                            ->label('Content (NL)'),
                        Textarea::make('content_fr')
                            ->requiredWithout('content_nl')
                            ->rows(5)
                            ->columnSpanFull()
                            ->label('Content (FR)'),
                    ]),
                Section::make('Activity Details')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('activity_type')
                            ->required()
                            ->options(ActivityType::getOptionsArray())
                            ->default(ActivityType::KIDICALMASS->value)
                            ->label('Activity Type'),
                        DateTimePicker::make('begin_date')
                            ->required()
                            ->label('Begin Date'),
                        TextInput::make('location')
                            ->required()
                            ->maxLength(255)
                            ->label('Location')
                            ->helperText('For Critical Mass: enter the starting address'),
                        TextInput::make('postal_code')
                            ->nullable()
                            ->maxLength(10)
                            ->label('Postal Code')
                            ->helperText('e.g. 1000 — used in the display title'),
                        TextInput::make('distance')
                            ->nullable()
                            ->maxLength(50)
                            ->label('Distance')
                            ->helperText('e.g. 5–7 km'),
                        TextInput::make('duration_minutes')
                            ->label('Duration (minutes)')
                            ->helperText('Duration of the activity in minutes')
                            ->numeric()
                            ->minValue(1),
                    ]),
                Section::make('Route Information')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('commute_link')
                            ->label('Commute Route Link')
                            ->helperText('URL to visualize the route (e.g., Komoot, RideWithGPS)')
                            ->url()
                            ->maxLength(500),
                        TextInput::make('komoot_url')
                            ->nullable()
                            ->url()
                            ->label('Komoot URL')
                            ->helperText('Paste the public Komoot tour URL (e.g. https://www.komoot.com/tour/123). Optional.')
                            ->maxLength(500),
                        SpatieMediaLibraryFileUpload::make('gpx')
                            ->columnSpanFull()
                            ->label('Route (GPX file)')
                            ->disk('media')
                            ->collection('gpx')
                            ->acceptedFileTypes(['application/gpx+xml', 'application/xml', 'text/xml'])
                            ->maxSize(15360)
                            ->helperText('Export GPX from Komoot (or any route planner) and upload here.'),
                    ]),
                Section::make('Organisation')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('author_id')
                            ->label('Author')
                            ->relationship('author', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('groups')
                            ->relationship('groups', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->afterStateUpdated(fn ($state, $set) => $set('organizer_id', null)),
                        Select::make('organizer_id')
                            ->label('Organizer')
                            ->options(fn (Get $get): array => self::getOrganizerOptions($get))
                            ->searchable()
                            ->preload()
                            ->helperText('Leave empty to automatically assign from the responsible group or author.'),
                    ]),
                Section::make('Images')
                    ->columnSpanFull()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('main')
                            ->label('Main Image')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '4:3',
                                '16:9',
                            ])
                            ->maxSize(15360)
                            ->disk('media')
                            ->collection('main')
                            ->helperText('This image will be used in the card preview on the activities index page.'),
                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->label('Additional Images')
                            ->image()
                            ->multiple()
                            ->maxSize(15360)
                            ->disk('media')
                            ->collection('gallery')
                            ->helperText('These images will only appear on the activity detail page.'),
                    ]),
                Section::make('Visibility')
                    ->columnSpanFull()
                    ->schema([
                        Checkbox::make('published')
                            ->label('Published')
                            ->helperText('Unpublished activities are hidden from the public site.'),
                    ]),
            ]);
    }

    public static function getOrganizerOptions(Get $get): array
    {
        $groupIds = $get('groups') ?? [];

        if (empty($groupIds)) {
            return User::query()
                ->orderBy('name')
                ->pluck('name', 'id')
                ->toArray();
        }

        return User::whereHas('groups', fn ($query) => $query->whereIn('groups.id', $groupIds))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('main')
                    ->label('Image')
                    ->disk('media')
                    ->collection('main')
                    ->conversion('thumb')
                    ->size(60),
                TextColumn::make('title_nl')
                    ->searchable()
                    ->sortable()
                    ->label('Title (NL)'),
                TextColumn::make('title_fr')
                    ->searchable()
                    ->sortable()
                    ->label('Title (FR)')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('activity_type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => $state->badgeColor())
                    ->sortable()
                    ->label('Type'),
                TextColumn::make('begin_date')
                    ->dateTime()
                    ->sortable()
                    ->label('Begin Date'),
                TextColumn::make('end_date')
                    ->label('End Date')
                    ->getStateUsing(fn ($record) => $record->end_date?->format('Y-m-d H:i:s'))
                    ->sortable(),
                TextColumn::make('location')
                    ->searchable()
                    ->sortable()
                    ->label('Location'),
                TextColumn::make('author.name')
                    ->searchable()
                    ->sortable()
                    ->label('Author'),
                TextColumn::make('organizer.name')
                    ->searchable()
                    ->sortable()
                    ->label('Organizer')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('groups.name')
                    ->badge()
                    ->separator(',')
                    ->label('Groups'),
                TextColumn::make('commute_link')
                    ->label('Route')
                    ->url(fn ($record) => $record->commute_link)
                    ->openUrlInNewTab()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('published')
                    ->boolean()
                    ->sortable()
                    ->label('Published'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('published')
                    ->options([
                        1 => 'Published',
                        0 => 'Unpublished',
                    ])
                    ->label('Published'),
                SelectFilter::make('activity_type')
                    ->options(ActivityType::getOptionsArray())
                    ->label('Activity Type'),
                SelectFilter::make('author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('groups')
                    ->relationship('groups', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('upcoming')
                    ->query(fn (Builder $query): Builder => $query->where('begin_date', '>=', now()))
                    ->label('Upcoming Activities'),
                Filter::make('past')
                    ->query(fn (Builder $query): Builder => $query->whereRaw('DATE_ADD(begin_date, INTERVAL duration_minutes MINUTE) < NOW()'))
                    ->label('Past Activities'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('publish')
                    ->label('Publish')
                    ->icon(Heroicon::OutlinedEye)
                    ->visible(fn (Activity $record): bool => ! $record->published)
                    ->action(function (Activity $record): void {
                        $record->update(['published' => true]);
                        Notification::make()->success()->title('Activity published')->send();
                    }),
                Action::make('unpublish')
                    ->label('Unpublish')
                    ->icon(Heroicon::OutlinedEyeSlash)
                    ->color('warning')
                    ->visible(fn (Activity $record): bool => $record->published)
                    ->action(function (Activity $record): void {
                        $record->update(['published' => false]);
                        Notification::make()->success()->title('Activity unpublished')->send();
                    }),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('begin_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageActivities::route('/'),
        ];
    }
}

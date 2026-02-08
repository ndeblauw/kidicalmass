<?php

namespace App\Filament\Resources\Activities;

use App\Enums\ActivityType;
use App\Filament\Resources\Activities\Pages\ManageActivities;
use App\Models\Activity;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
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
                TextInput::make('title_nl')
                    ->required()
                    ->maxLength(255)
                    ->label('Title (NL)'),
                TextInput::make('title_fr')
                    ->required()
                    ->maxLength(255)
                    ->label('Title (FR)'),
                Textarea::make('content_nl')
                    ->required()
                    ->rows(5)
                    ->label('Content (NL)'),
                Textarea::make('content_fr')
                    ->required()
                    ->rows(5)
                    ->label('Content (FR)'),
                Select::make('activity_type')
                    ->required()
                    ->options(ActivityType::getOptionsArray())
                    ->default(ActivityType::KIDICALMASS->value)
                    ->label('Activity Type'),
                DateTimePicker::make('begin_date')
                    ->required()
                    ->label('Begin Date'),
                DateTimePicker::make('end_date')
                    ->required()
                    ->label('End Date')
                    ->after('begin_date'),
                TextInput::make('location')
                    ->required()
                    ->maxLength(255)
                    ->label('Location'),
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
                    ->searchable(),
                SpatieMediaLibraryFileUpload::make('main')
                    ->label('Main Image')
                    ->image()
                    ->imageEditor()
                    ->imageEditorAspectRatios([
                        '4:3',
                        '16:9',
                    ])
                    ->maxSize(5120)
                    ->disk('media')
                    ->collection('main')
                    ->helperText('This image will be used in the card preview on the activities index page.'),
                SpatieMediaLibraryFileUpload::make('gallery')
                    ->label('Additional Images')
                    ->image()
                    ->multiple()
                    ->maxSize(5120)
                    ->disk('media')
                    ->collection('gallery')
                    ->helperText('These images will only appear on the activity detail page.'),
            ]);
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
                    ->dateTime()
                    ->sortable()
                    ->label('End Date'),
                TextColumn::make('location')
                    ->searchable()
                    ->sortable()
                    ->label('Location'),
                TextColumn::make('author.name')
                    ->searchable()
                    ->sortable()
                    ->label('Author'),
                TextColumn::make('groups.name')
                    ->badge()
                    ->separator(',')
                    ->label('Groups'),
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
                    ->query(fn (Builder $query): Builder => $query->where('end_date', '<', now()))
                    ->label('Past Activities'),
            ])
            ->recordActions([
                EditAction::make(),
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

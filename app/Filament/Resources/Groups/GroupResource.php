<?php

namespace App\Filament\Resources\Groups;

use App\Filament\Resources\Groups\Pages\ManageGroups;
use App\Filament\Resources\Groups\Pages\ViewGroup;
use App\Models\Group;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GroupResource extends Resource
{
    protected static ?string $model = Group::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('shortname')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label('Short Name'),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Name'),
                TextInput::make('zip')
                    ->maxLength(255)
                    ->label('Postal Code'),
                Select::make('parent_id')
                    ->label('Parent Group')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload(),
                Checkbox::make('invisible')
                    ->label('Invisible')
                    ->helperText('Hide this group from the public groups index page'),
                DatePicker::make('started_at')
                    ->label('Started At')
                    ->required()
                    ->default(now()),
                DatePicker::make('ended_at')
                    ->label('Ended At')
                    ->after('started_at'),
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
                            ->collection('main'),
                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->label('Additional Images')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '4:3',
                                '16:9',
                            ])
                            ->multiple()
                            ->reorderable()
                            ->maxSize(15360)
                            ->disk('media')
                            ->collection('gallery'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                SpatieMediaLibraryImageColumn::make('main')
                    ->label('Image')
                    ->disk('media')
                    ->collection('main')
                    ->conversion('thumb')
                    ->size(60),
                TextColumn::make('shortname')
                    ->searchable()
                    ->sortable()
                    ->label('Short Name'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Name'),
                TextColumn::make('zip')
                    ->searchable()
                    ->sortable()
                    ->label('Postal Code'),
                TextColumn::make('parent.name')
                    ->searchable()
                    ->sortable()
                    ->label('Parent Group'),
                IconColumn::make('invisible')
                    ->boolean()
                    ->sortable()
                    ->label('Invisible'),
                TextColumn::make('started_at')
                    ->date()
                    ->sortable()
                    ->label('Started At'),
                TextColumn::make('ended_at')
                    ->date()
                    ->sortable()
                    ->label('Ended At'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('dashboard')
                    ->label('Dashboard')
                    ->icon('heroicon-o-chart-bar')
                    ->url(fn (Group $record): string => GroupResource::getUrl('view', ['record' => $record])),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('started_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGroups::route('/'),
            'view' => ViewGroup::route('/{record}'),
        ];
    }
}

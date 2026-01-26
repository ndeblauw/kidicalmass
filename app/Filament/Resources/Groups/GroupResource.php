<?php

namespace App\Filament\Resources\Groups;

use App\Filament\Resources\Groups\Pages\ManageGroups;
use App\Models\Group;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
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
                DatePicker::make('started_at')
                    ->label('Started At')
                    ->required()
                    ->default(now()),
                DatePicker::make('ended_at')
                    ->label('Ended At')
                    ->after('started_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
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
        ];
    }
}

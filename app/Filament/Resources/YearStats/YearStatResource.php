<?php

namespace App\Filament\Resources\YearStats;

use App\Filament\Resources\YearStats\Pages\ManageYearStats;
use App\Models\YearStat;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class YearStatResource extends Resource
{
    protected static ?string $model = YearStat::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Jaarcijfers';

    protected static ?string $modelLabel = 'jaarcijfer';

    protected static ?string $pluralModelLabel = 'jaarcijfers';

    protected static ?string $recordTitleAttribute = 'year';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('year')
                    ->label('Jaar')
                    ->numeric()
                    ->required()
                    ->minValue(2020)
                    ->maxValue(2100)
                    ->unique(ignoreRecord: true)
                    ->helperText('Het kalenderjaar waarop dit cijfer slaat.'),
                TextInput::make('participants')
                    ->label('Deelnemers')
                    ->numeric()
                    ->minValue(0)
                    ->helperText('Aantal kinderen en ouders dat dat jaar meefietste. Handmatig in te vullen; toont op de steunpagina.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('year', 'desc')
            ->columns([
                TextColumn::make('year')
                    ->label('Jaar')
                    ->sortable(),
                TextColumn::make('participants')
                    ->label('Deelnemers')
                    ->numeric(locale: 'nl')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label('Bijgewerkt')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageYearStats::route('/'),
        ];
    }
}

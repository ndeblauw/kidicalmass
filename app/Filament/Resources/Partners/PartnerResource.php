<?php

namespace App\Filament\Resources\Partners;

use App\Filament\Resources\Partners\Pages\ManagePartners;
use App\Models\Partner;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label('Name'),
                TextInput::make('url')
                    ->url()
                    ->maxLength(255)
                    ->label('Website URL')
                    ->placeholder('https://example.com'),
                Textarea::make('description_nl')
                    ->rows(3)
                    ->label('Description (NL)')
                    ->columnSpanFull(),
                Textarea::make('description_fr')
                    ->rows(3)
                    ->label('Description (FR)')
                    ->columnSpanFull(),
                Select::make('group_id')
                    ->label('Group')
                    ->relationship('group', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Toggle::make('show_logo')
                    ->label('Show Logo')
                    ->default(true)
                    ->inline(false),
                Toggle::make('visible')
                    ->label('Visible')
                    ->default(true)
                    ->inline(false),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->imageEditor()
                    ->maxSize(5120)
                    ->disk('media')
                    ->collection('logo')
                    ->helperText('Upload the partner/sponsor logo. Recommended size: 400x200px.')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('media')
                    ->collection('logo')
                    ->conversion('thumb')
                    ->size(60),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Name'),
                TextColumn::make('url')
                    ->searchable()
                    ->limit(30)
                    ->label('URL')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('group.name')
                    ->searchable()
                    ->sortable()
                    ->label('Group'),
                ToggleColumn::make('show_logo')
                    ->label('Show Logo'),
                ToggleColumn::make('visible')
                    ->label('Visible'),
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
                SelectFilter::make('group')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('visible')
                    ->options([
                        '1' => 'Visible',
                        '0' => 'Hidden',
                    ]),
                SelectFilter::make('show_logo')
                    ->options([
                        '1' => 'Show Logo',
                        '0' => 'Hide Logo',
                    ]),
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
            'index' => ManagePartners::route('/'),
        ];
    }
}

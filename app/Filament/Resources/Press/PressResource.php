<?php

namespace App\Filament\Resources\Press;

use App\Enums\PressType;
use App\Filament\Resources\Press\Pages\ManagePress;
use App\Models\Press;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
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
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PressResource extends Resource
{
    protected static ?string $model = Press::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Title'),
                DatePicker::make('publication_date')
                    ->required()
                    ->label('Publication Date'),
                TextInput::make('outlet')
                    ->required()
                    ->maxLength(255)
                    ->label('Media Outlet'),
                Select::make('media_type')
                    ->required()
                    ->options(PressType::getOptionsArray())
                    ->default(PressType::ONLINE->value)
                    ->label('Media Type'),
                TextInput::make('url')
                    ->url()
                    ->maxLength(255)
                    ->nullable()
                    ->label('URL'),
                Textarea::make('description')
                    ->rows(5)
                    ->nullable()
                    ->label('Description'),
                Toggle::make('visible')
                    ->default(true)
                    ->label('Visible'),
                Toggle::make('highlighted')
                    ->default(false)
                    ->label('Highlighted'),
                Select::make('groups')
                    ->relationship('groups', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                SpatieMediaLibraryFileUpload::make('attachment')
                    ->label('Attachment / Image')
                    ->image()
                    ->imageEditor()
                    ->maxSize(5120)
                    ->disk('media')
                    ->collection('attachment')
                    ->helperText('An image or document preview for this press item.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('publication_date', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('attachment')
                    ->label('Image')
                    ->disk('media')
                    ->collection('attachment')
                    ->conversion('thumb')
                    ->size(60),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->label('Title'),
                TextColumn::make('outlet')
                    ->searchable()
                    ->sortable()
                    ->label('Outlet'),
                TextColumn::make('media_type')
                    ->badge()
                    ->formatStateUsing(fn (PressType $state) => $state->label())
                    ->sortable()
                    ->label('Type'),
                TextColumn::make('publication_date')
                    ->date()
                    ->sortable()
                    ->label('Publication Date'),
                ToggleColumn::make('visible')
                    ->label('Visible'),
                ToggleColumn::make('highlighted')
                    ->label('Highlighted'),
                TextColumn::make('groups.name')
                    ->badge()
                    ->separator(',')
                    ->label('Groups')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('media_type')
                    ->options(PressType::getOptionsArray())
                    ->label('Media Type'),
                SelectFilter::make('groups')
                    ->relationship('groups', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('visible')
                    ->label('Visible'),
                TernaryFilter::make('highlighted')
                    ->label('Highlighted'),
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
            'index' => ManagePress::route('/'),
        ];
    }
}

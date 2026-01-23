<?php

namespace App\Filament\Resources\Articles;

use App\Filament\Resources\Articles\Pages\ManageArticles;
use App\Models\Article;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

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
                    ->helperText('This image will be used in the card preview on the articles index page.'),
                SpatieMediaLibraryFileUpload::make('gallery')
                    ->label('Additional Images')
                    ->image()
                    ->multiple()
                    ->maxSize(5120)
                    ->disk('media')
                    ->collection('gallery')
                    ->helperText('These images will only appear on the article detail page.'),
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
                SelectFilter::make('author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('groups')
                    ->relationship('groups', 'name')
                    ->searchable()
                    ->preload(),
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
            'index' => ManageArticles::route('/'),
        ];
    }
}

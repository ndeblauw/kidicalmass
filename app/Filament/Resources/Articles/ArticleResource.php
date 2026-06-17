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
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
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
                Section::make('Organisation')
                    ->columnSpanFull()
                    ->columns(2)
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
                            ->searchable(),
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
                            ->helperText('This image will be used in the card preview on the articles index page.'),
                        SpatieMediaLibraryFileUpload::make('gallery')
                            ->label('Additional Images')
                            ->image()
                            ->multiple()
                            ->maxSize(15360)
                            ->disk('media')
                            ->collection('gallery')
                            ->helperText('These images will only appear on the article detail page.'),
                    ]),
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

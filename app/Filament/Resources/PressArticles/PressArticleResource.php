<?php

namespace App\Filament\Resources\PressArticles;

use App\Filament\Resources\PressArticles\Pages\ManagePressArticles;
use App\Models\PressArticle;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PressArticleResource extends Resource
{
    protected static ?string $model = PressArticle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

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
                TextInput::make('outlet')
                    ->required()
                    ->maxLength(255)
                    ->label('Outlet')
                    ->helperText('News outlet name (RTBF, BRUZZ, HLN, …)'),
                TextInput::make('url')
                    ->url()
                    ->maxLength(500)
                    ->label('Article URL')
                    ->helperText('Link to the original article or video'),
                DateTimePicker::make('published_at')
                    ->label('Published At'),
                Select::make('author_id')
                    ->label('Author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
                Section::make('Linked records')
                    ->description('Link this press article to activities, site articles, or groups')
                    ->schema([
                        Select::make('activities')
                            ->multiple()
                            ->relationship('activities', 'title_nl')
                            ->preload()
                            ->searchable()
                            ->label('Activities'),
                        Select::make('articles')
                            ->multiple()
                            ->relationship('articles', 'title_nl')
                            ->preload()
                            ->searchable()
                            ->label('Site Articles'),
                        Select::make('groups')
                            ->multiple()
                            ->relationship('groups', 'name')
                            ->preload()
                            ->searchable()
                            ->label('Groups'),
                    ])
                    ->columns(1),
                SpatieMediaLibraryFileUpload::make('document')
                    ->label('Article scan / PDF')
                    ->acceptedFileTypes(['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'])
                    ->maxSize(15360)
                    ->disk('media')
                    ->collection('document')
                    ->helperText('Upload a PDF scan or image of the press article as it appeared.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title_nl')
                    ->searchable()
                    ->sortable()
                    ->label('Title (NL)'),
                TextColumn::make('outlet')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->label('Outlet'),
                TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Published'),
                TextColumn::make('activities_count')
                    ->counts('activities')
                    ->label('Activities')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('articles_count')
                    ->counts('articles')
                    ->label('Articles')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('groups_count')
                    ->counts('groups')
                    ->label('Groups')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('author.name')
                    ->searchable()
                    ->sortable()
                    ->label('Author')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePressArticles::route('/'),
        ];
    }
}

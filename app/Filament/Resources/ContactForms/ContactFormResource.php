<?php

namespace App\Filament\Resources\ContactForms;

use App\Filament\Resources\ContactForms\Pages\ManageContactForms;
use App\Models\ContactForm;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactFormResource extends Resource
{
    protected static ?string $model = ContactForm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Contact Submissions';

    protected static ?string $modelLabel = 'Contact Submission';

    protected static ?string $pluralModelLabel = 'Contact Submissions';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                TextInput::make('phone')
                    ->tel()
                    ->maxLength(255)
                    ->disabled(),
                Textarea::make('message')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull()
                    ->disabled(),
                TextInput::make('page_url')
                    ->label('Page URL')
                    ->url()
                    ->required()
                    ->maxLength(255)
                    ->disabled(),
                TextInput::make('created_at')
                    ->label('Submitted At')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('phone')
                    ->searchable()
                    ->placeholder('Not provided')
                    ->toggleable(),
                TextColumn::make('message')
                    ->limit(50)
                    ->searchable()
                    ->wrap(),
                TextColumn::make('page_url')
                    ->label('Page')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
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
            'index' => ManageContactForms::route('/'),
        ];
    }
}

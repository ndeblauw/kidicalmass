<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->minLength(8)
                    ->dehydrated(fn ($state) => filled($state))
                    ->confirmed(),
                TextInput::make('password_confirmation')
                    ->password()
                    ->required(fn (string $context): bool => $context === 'create')
                    ->dehydrated(false),
                Select::make('groups')
                    ->relationship('groups', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->label('Groups'),
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
                    ->searchable()
                    ->sortable(),
                TextColumn::make('groups.name')
                    ->badge()
                    ->searchable()
                    ->sortable()
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
                Filter::make('verified')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('email_verified_at'))
                    ->label('Verified Only'),
                Filter::make('unverified')
                    ->query(fn (Builder $query): Builder => $query->whereNull('email_verified_at'))
                    ->label('Unverified Only'),
            ])
            ->recordActions([
                Action::make('impersonate')
                    ->label('Login As')
                    ->icon(Heroicon::OutlinedUser)
                    ->color('warning')
                    ->visible(fn (User $record): bool => Auth::id() !== $record->id)
                    ->requiresConfirmation()
                    ->modalDescription(fn (User $record): string => "You are about to log in as {$record->name}. You will be able to perform actions as this user.")
                    ->url(fn (User $record): string => '#')
                    ->action(function (User $record) {
                        // Use the controller method to handle impersonation
                        $controller = app(\App\Http\Controllers\ImpersonateController::class);

                        return $controller->start(request(), $record);
                    }),
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
            'index' => ManageUsers::route('/'),
        ];
    }
}

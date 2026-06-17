<?php

namespace App\Filament\Resources\PressArticles\Pages;

use App\Filament\Resources\PressArticles\PressArticleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePressArticles extends ManageRecords
{
    protected static string $resource = PressArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

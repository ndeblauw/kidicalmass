<?php

namespace App\Filament\Resources\YearStats\Pages;

use App\Filament\Resources\YearStats\YearStatResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageYearStats extends ManageRecords
{
    protected static string $resource = YearStatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

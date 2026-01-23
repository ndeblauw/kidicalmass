<?php

namespace App\Filament\Resources\ContactForms\Pages;

use App\Filament\Resources\ContactForms\ContactFormResource;
use Filament\Resources\Pages\ManageRecords;

class ManageContactForms extends ManageRecords
{
    protected static string $resource = ContactFormResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Contact forms are submitted through the public form only
        ];
    }
}

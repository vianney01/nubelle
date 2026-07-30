<?php

namespace App\Filament\Resources\Avis\Pages;

use App\Filament\Resources\Avis\AvisResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAvis extends ManageRecords
{
    protected static string $resource = AvisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

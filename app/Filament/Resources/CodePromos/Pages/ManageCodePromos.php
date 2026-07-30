<?php

namespace App\Filament\Resources\CodePromos\Pages;

use App\Filament\Resources\CodePromos\CodePromoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCodePromos extends ManageRecords
{
    protected static string $resource = CodePromoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

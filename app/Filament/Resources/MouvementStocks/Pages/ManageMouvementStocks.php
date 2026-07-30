<?php

namespace App\Filament\Resources\MouvementStocks\Pages;

use App\Filament\Resources\MouvementStocks\MouvementStockResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMouvementStocks extends ManageRecords
{
    protected static string $resource = MouvementStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

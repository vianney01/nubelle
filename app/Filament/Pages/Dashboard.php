<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use UnitEnum;

class Dashboard extends BaseDashboard
{
    protected static string|UnitEnum|null $navigationGroup = 'Dashboard';

    protected static ?string $navigationLabel = "Vue d'ensemble";

    public function getColumns(): int|array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 3,
        ];
    }
}

<?php

namespace App\Filament\Resources\Commandes\Pages;

use App\Filament\Resources\Commandes\CommandeResource;
use App\Models\Commande;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCommandes extends ListRecords
{
    protected static string $resource = CommandeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * Onglets de filtrage par statut, avec compteur, au-dessus du tableau.
     */
    public function getTabs(): array
    {
        $comptes = Commande::query()
            ->selectRaw('statut, COUNT(*) as total')
            ->groupBy('statut')
            ->pluck('total', 'statut');

        $couleurs = [
            'en_attente' => 'gray',
            'en_preparation' => 'info',
            'expediee' => 'warning',
            'livree' => 'success',
            'annulee' => 'danger',
        ];

        $onglets = [
            'toutes' => Tab::make('Toutes')
                ->badge($comptes->sum()),
        ];

        foreach (Commande::STATUTS_LABELS as $statut => $label) {
            $n = $comptes[$statut] ?? 0;

            $onglets[$statut] = Tab::make($label)
                ->badge($n ?: null)
                ->badgeColor($couleurs[$statut] ?? 'gray')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('statut', $statut));
        }

        return $onglets;
    }
}

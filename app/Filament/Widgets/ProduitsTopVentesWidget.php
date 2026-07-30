<?php

namespace App\Filament\Widgets;

use App\Models\LigneCommande;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Model;

class ProduitsTopVentesWidget extends TableWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected function getTableHeading(): string
    {
        return 'Produits les plus vendus';
    }

    /**
     * La requête agrège les lignes de commande par produit (GROUP BY + SUM),
     * donc chaque ligne résultante n'a pas d'« id » de ligne_commande exploitable.
     * On identifie chaque ligne par son produit_id, unique dans ce résultat groupé.
     */
    public function getTableRecordKey(Model|array $record): string
    {
        if (is_array($record)) {
            return (string) $record['produit_id'];
        }

        return (string) $record->produit_id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LigneCommande::query()
                    ->selectRaw('produit_id, SUM(quantite) as total_quantite, SUM(quantite * prix_unitaire) as total_ca')
                    ->groupBy('produit_id')
                    ->orderByDesc('total_quantite')
                    ->limit(5)
                    ->with('produit')
            )
            ->paginated(false)
            ->columns([
                ImageColumn::make('produit.image_principale')->label('')->circular(),
                TextColumn::make('produit.nom')->label('Produit'),
                TextColumn::make('total_quantite')->label('Vendus')->badge()->color('success'),
                TextColumn::make('total_ca')->label('CA généré')->money('XOF', divideBy: 1),
            ]);
    }
}

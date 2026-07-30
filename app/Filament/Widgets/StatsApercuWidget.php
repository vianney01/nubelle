<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Commande;
use App\Models\Produit;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsApercuWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $chiffreAffaires = Commande::query()
            ->where('statut', '!=', 'annulee')
            ->sum('total');

        $chiffreAffairesMoisDernier = Commande::query()
            ->where('statut', '!=', 'annulee')
            ->whereBetween('created_at', [Carbon::now()->subDays(60), Carbon::now()->subDays(30)])
            ->sum('total');

        $chiffreAffaires30j = Commande::query()
            ->where('statut', '!=', 'annulee')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum('total');

        $evolution = $chiffreAffairesMoisDernier > 0
            ? round((($chiffreAffaires30j - $chiffreAffairesMoisDernier) / $chiffreAffairesMoisDernier) * 100)
            : null;

        return [
            Stat::make('Chiffre d\'affaires', number_format((float) $chiffreAffaires, 0, ',', ' ').' FCFA')
                ->description($evolution === null ? '30 derniers jours' : ($evolution >= 0 ? "+{$evolution}% vs mois précédent" : "{$evolution}% vs mois précédent"))
                ->descriptionIcon($evolution === null || $evolution >= 0 ? Heroicon::ArrowTrendingUp : Heroicon::ArrowTrendingDown)
                ->color($evolution === null || $evolution >= 0 ? 'success' : 'danger')
                ->icon(Heroicon::OutlinedCurrencyDollar),

            Stat::make('Commandes', (string) Commande::query()->count())
                ->description(Commande::query()->where('statut', 'en_attente')->count().' en attente')
                ->descriptionIcon(Heroicon::OutlinedClock)
                ->color('warning')
                ->icon(Heroicon::OutlinedShoppingCart),

            Stat::make('Clients', (string) Client::query()->count())
                ->description('Clients enregistrés')
                ->descriptionIcon(Heroicon::OutlinedUsers)
                ->color('primary')
                ->icon(Heroicon::OutlinedUserGroup),

            Stat::make('Alertes stock', (string) Produit::query()->where('stock', '<=', 10)->count())
                ->description(Produit::query()->where('stock', '<=', 0)->count().' en rupture totale')
                ->descriptionIcon(Heroicon::OutlinedExclamationTriangle)
                ->color('danger')
                ->icon(Heroicon::OutlinedArchiveBox),
        ];
    }
}

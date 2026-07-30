<?php

namespace App\Filament\Widgets;

use App\Models\Commande;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class VentesChartWidget extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $heading = "Évolution du chiffre d'affaires";

    protected int|string|array $columnSpan = 2;

    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return [
            '7' => '7 derniers jours',
            '30' => '30 derniers jours',
            '90' => '90 derniers jours',
        ];
    }

    protected function getData(): array
    {
        $jours = (int) $this->filter;

        $ventes = Commande::query()
            ->where('statut', '!=', 'annulee')
            ->where('created_at', '>=', Carbon::now()->subDays($jours)->startOfDay())
            ->selectRaw('DATE(created_at) as jour, SUM(total) as total')
            ->groupBy('jour')
            ->orderBy('jour')
            ->pluck('total', 'jour');

        $labels = [];
        $valeurs = [];

        for ($i = $jours - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $cle = $date->format('Y-m-d');

            $labels[] = $date->translatedFormat('d M');
            $valeurs[] = (float) ($ventes[$cle] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Chiffre d\'affaires (FCFA)',
                    'data' => $valeurs,
                    'borderColor' => '#AA4C00',
                    'backgroundColor' => 'rgba(170, 76, 0, 0.1)',
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}

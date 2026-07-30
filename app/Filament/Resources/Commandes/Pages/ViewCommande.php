<?php

namespace App\Filament\Resources\Commandes\Pages;

use App\Filament\Resources\Commandes\CommandeResource;
use App\Models\Commande;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewCommande extends ViewRecord
{
    protected static string $resource = CommandeResource::class;

    public function getHeading(): string
    {
        return "Commande {$this->getRecord()->numero}";
    }

    public function getSubheading(): ?string
    {
        /** @var Commande $commande */
        $commande = $this->getRecord();
        $client = $commande->client ? trim("{$commande->client->prenom} {$commande->client->nom}") : 'Client supprimé';

        return $client.' · '.$commande->created_at->translatedFormat('d F Y à H:i');
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->actionConfirmerPaiement(),
            ActionGroup::make([
                $this->actionMarquerStatut('en_preparation', 'preparation', 'Marquer en préparation', 'heroicon-o-cube'),
                $this->actionMarquerStatut('expediee', 'expedition', 'Marquer comme expédiée', 'heroicon-o-truck'),
                $this->actionMarquerStatut('livree', 'livraison', 'Marquer comme livrée', 'heroicon-o-check-badge'),
                $this->actionChangerStatut(),
                $this->actionAnnuler(),
            ])
                ->label('Traiter la commande')
                ->icon('heroicon-o-bolt')
                ->button(),
            $this->actionAjouterNote(),
            $this->actionFacture(),
            $this->actionContacterClient(),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    private function actionAjouterNote(): Action
    {
        return Action::make('ajouter_note')
            ->label('Ajouter une note')
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->color('gray')
            ->schema([
                Textarea::make('commentaire')->label('Note interne')->rows(3)->required(),
            ])
            ->action(function (array $data) {
                /** @var Commande $commande */
                $commande = $this->getRecord();
                $commande->journaliser('note', $commande->statut, $data['commentaire']);

                Notification::make()->title('Note ajoutée.')->success()->send();
            });
    }

    /**
     * Enregistre un changement de statut logistique + une entrée de timeline.
     */
    private function appliquerStatut(string $statut, string $typeHistorique, ?string $commentaire = null): void
    {
        /** @var Commande $commande */
        $commande = $this->getRecord();
        $commande->update(['statut' => $statut]);
        $commande->journaliser($typeHistorique, $statut, $commentaire);

        Notification::make()
            ->title('Commande mise à jour : '.$commande->statutLabel())
            ->success()
            ->send();
    }

    private function actionMarquerStatut(string $statut, string $type, string $label, string $icone): Action
    {
        return Action::make('statut_'.$statut)
            ->label($label)
            ->icon($icone)
            ->requiresConfirmation()
            ->visible(fn (Commande $record) => $record->statut !== $statut && $record->statut !== 'annulee')
            ->action(fn () => $this->appliquerStatut($statut, $type));
    }

    private function actionChangerStatut(): Action
    {
        return Action::make('changer_statut')
            ->label('Changer le statut…')
            ->icon('heroicon-o-arrow-path')
            ->schema([
                Select::make('statut')
                    ->label('Nouveau statut')
                    ->options(Commande::STATUTS_LABELS)
                    ->required(),
                Textarea::make('commentaire')->label('Commentaire (optionnel)')->rows(2),
            ])
            ->fillForm(fn (Commande $record) => ['statut' => $record->statut])
            ->action(fn (array $data) => $this->appliquerStatut($data['statut'], 'statut', $data['commentaire'] ?? null));
    }

    private function actionAnnuler(): Action
    {
        return Action::make('annuler')
            ->label('Annuler la commande')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->visible(fn (Commande $record) => $record->statut !== 'annulee')
            ->schema([
                Textarea::make('commentaire')->label('Motif de l\'annulation')->rows(2),
            ])
            ->action(fn (array $data) => $this->appliquerStatut('annulee', 'annulation', $data['commentaire'] ?? null));
    }

    private function actionConfirmerPaiement(): Action
    {
        return Action::make('confirmer_paiement')
            ->label('Confirmer le paiement')
            ->icon('heroicon-o-banknotes')
            ->color('success')
            ->visible(fn (Commande $record) => $record->statut_paiement !== 'paye')
            ->schema([
                TextInput::make('reference_paiement')
                    ->label('Référence de transaction (optionnel)')
                    ->maxLength(100),
            ])
            ->fillForm(fn (Commande $record) => ['reference_paiement' => $record->reference_paiement])
            ->action(function (array $data) {
                /** @var Commande $commande */
                $commande = $this->getRecord();
                $commande->update([
                    'statut_paiement' => 'paye',
                    'reference_paiement' => $data['reference_paiement'] ?? null,
                ]);
                $commande->journaliser(
                    'paiement',
                    $commande->statut,
                    'Paiement confirmé'.($data['reference_paiement'] ? ' — réf. '.$data['reference_paiement'] : '').'.',
                );

                Notification::make()->title('Paiement confirmé.')->success()->send();
            });
    }

    private function actionFacture(): Action
    {
        return Action::make('facture')
            ->label('Facture / Imprimer')
            ->icon('heroicon-o-printer')
            ->color('gray')
            ->url(fn (Commande $record) => route('admin.commande.facture', $record))
            ->openUrlInNewTab();
    }

    private function actionContacterClient(): Action
    {
        return Action::make('contacter_client')
            ->label('Contacter le client')
            ->icon('heroicon-o-envelope')
            ->color('gray')
            ->visible(fn (Commande $record) => (bool) $record->client?->email)
            ->url(fn (Commande $record) => 'mailto:'.$record->client->email
                .'?subject='.rawurlencode('Votre commande '.$record->numero.' — NUBELLE Cosmetics'))
            ->openUrlInNewTab();
    }
}

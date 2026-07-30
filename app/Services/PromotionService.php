<?php

namespace App\Services;

use App\Models\Client;
use App\Models\CodePromo;
use App\Models\Commande;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Point unique de vérité pour tous les calculs de promotion du parcours
 * d'achat (panier, checkout, enregistrement de commande). Aucune logique de
 * remise ne doit vivre ailleurs : les contrôleurs se contentent d'appeler
 * evaluer() et de lire le résultat.
 *
 * Deux natures de promotions sont gérées ensemble :
 *  - le code promo manuel saisi par le client ;
 *  - les remises membres automatiques (sans code), déclenchées par le profil
 *    client (nouveau / déjà client) déduit de l'e-mail de commande.
 *
 * La priorité (priorite) et le cumul (cumulable) arbitrent les conflits :
 * la promotion la plus prioritaire s'applique toujours ; les suivantes ne
 * s'ajoutent que si toutes les promotions retenues sont cumulables.
 */
class PromotionService
{
    /**
     * Construit le contexte client à partir de l'e-mail de commande (et,
     * si disponible, d'un identifiant client en session). « Nouveau » = aucune
     * commande passée ; « membre » = au moins une commande existante.
     *
     * @return array{email: ?string, client_id: ?int, client: ?Client, est_membre: bool, est_nouveau: bool}
     */
    public function contexteDepuisEmail(?string $email, ?int $clientId = null): array
    {
        $client = null;

        if ($clientId) {
            $client = Client::find($clientId);
        }
        if (! $client && $email) {
            $client = Client::where('email', $email)->first();
        }

        $nbCommandes = $client ? $client->commandes()->count() : 0;

        return [
            'email' => $email ?: $client?->email,
            'client_id' => $client?->id,
            'client' => $client,
            'est_membre' => $nbCommandes > 0,
            'est_nouveau' => $nbCommandes === 0,
        ];
    }

    /**
     * Évalue l'ensemble des promotions applicables à un panier et renvoie le
     * détail chiffré complet (sous-total, réductions, livraison, total).
     *
     * @param  Collection<int, array{produit: \App\Models\Produit, quantite: int}>  $lignes
     * @param  array{email?: ?string, client_id?: ?int, est_membre?: bool, est_nouveau?: bool}  $contexte
     */
    public function evaluer(Collection $lignes, ?string $code, array $contexte = [], float $fraisLivraisonBase = 0.0): array
    {
        $sousTotal = round((float) $lignes->sum(
            fn (array $l) => (float) $l['produit']->prix * (int) $l['quantite']
        ), 2);

        $candidats = [];
        $codePromo = null;
        $erreurCode = null;

        // 1. Code manuel saisi par le client.
        if (filled($code)) {
            $promo = CodePromo::query()
                ->manuels()
                ->with(['produits:id', 'categories:id', 'clients:id'])
                ->where('code', trim($code))
                ->first();

            if (! $promo) {
                $erreurCode = 'Code promo introuvable.';
            } elseif ($raison = $this->raisonInvalidite($promo, $sousTotal, $contexte, $lignes)) {
                $erreurCode = $raison;
            } else {
                $codePromo = $promo;
                $candidats[] = ['promo' => $promo, 'source' => 'code'];
            }
        }

        // 2. Remises membres automatiques (sans code).
        $automatiques = CodePromo::query()
            ->automatiques()
            ->actifs()
            ->with(['produits:id', 'categories:id', 'clients:id'])
            ->get();

        foreach ($automatiques as $promo) {
            if ($this->raisonInvalidite($promo, $sousTotal, $contexte, $lignes) === null) {
                $candidats[] = ['promo' => $promo, 'source' => 'membre'];
            }
        }

        // 3. Arbitrage priorité + cumul.
        $candidats = $this->trierParPriorite($candidats, $lignes);
        $appliques = $this->selectionnerSelonCumul($candidats);

        // 4. Chiffrage (plafonné au sous-total, dans l'ordre de priorité).
        $reductionCode = 0.0;
        $reductionMembre = 0.0;
        $livraisonGratuite = false;
        $restant = $sousTotal;
        $promosAppliquees = [];

        foreach ($appliques as $c) {
            $montant = min($this->montantReduction($c['promo'], $lignes), $restant);
            $restant = round($restant - $montant, 2);

            if ($c['source'] === 'code') {
                $reductionCode = round($reductionCode + $montant, 2);
            } else {
                $reductionMembre = round($reductionMembre + $montant, 2);
            }

            if ($c['promo']->livraison_gratuite) {
                $livraisonGratuite = true;
            }

            $promosAppliquees[] = ['promo' => $c['promo'], 'source' => $c['source'], 'montant' => $montant];
        }

        $reductionTotale = round($reductionCode + $reductionMembre, 2);
        $fraisLivraison = $livraisonGratuite ? 0.0 : round($fraisLivraisonBase, 2);
        $total = round($sousTotal - $reductionTotale + $fraisLivraison, 2);

        return [
            'sous_total' => $sousTotal,
            'code_promo' => $codePromo,
            'code_saisi' => $code,
            'erreur_code' => $erreurCode,
            'reduction_code' => $reductionCode,
            'reduction_membre' => $reductionMembre,
            'reduction_totale' => $reductionTotale,
            'reduction_type' => $this->typeReduction($reductionCode, $reductionMembre, $codePromo),
            'promos_appliquees' => $promosAppliquees,
            'livraison_gratuite' => $livraisonGratuite,
            'frais_livraison' => $fraisLivraison,
            'total' => max(0.0, $total),
        ];
    }

    /**
     * Renvoie la raison d'invalidité d'une promotion pour ce panier/contexte,
     * ou null si elle est parfaitement applicable.
     */
    public function raisonInvalidite(CodePromo $promo, float $sousTotal, array $contexte, Collection $lignes): ?string
    {
        if (! $promo->actif) {
            return "Ce code n'est plus actif.";
        }

        $aujourdhui = Carbon::today();
        if ($promo->date_debut && $aujourdhui->lt($promo->date_debut)) {
            return "Ce code n'est pas encore valable.";
        }
        if ($promo->date_fin && $aujourdhui->gt($promo->date_fin)) {
            return 'Ce code a expiré.';
        }

        if ($promo->montant_min !== null && $sousTotal < (float) $promo->montant_min) {
            return 'Panier minimum de '.number_format((float) $promo->montant_min, 0, ',', ' ').' FCFA requis.';
        }

        switch ($promo->restriction_client) {
            case CodePromo::RESTRICTION_INSCRITS:
                if (empty($contexte['est_membre'])) {
                    return 'Réservé à nos clients déjà inscrits.';
                }
                break;
            case CodePromo::RESTRICTION_NOUVEAUX:
                if (empty($contexte['est_nouveau'])) {
                    return 'Réservé aux nouveaux clients.';
                }
                break;
            case CodePromo::RESTRICTION_SELECTION:
                $clientId = $contexte['client_id'] ?? null;
                if (! $clientId || ! $promo->clients->pluck('id')->contains($clientId)) {
                    return 'Ce code ne vous est pas destiné.';
                }
                break;
        }

        if ($this->aDesRestrictionsProduits($promo) && $this->baseEligible($promo, $lignes) <= 0) {
            return 'Aucun produit éligible à ce code dans votre panier.';
        }

        if ($promo->max_utilisations !== null && $this->nombreUtilisations($promo) >= $promo->max_utilisations) {
            return "Ce code a atteint sa limite d'utilisation.";
        }

        if ($promo->max_utilisations_client !== null
            && ! empty($contexte['email'])
            && $this->nombreUtilisationsClient($promo, $contexte['email']) >= $promo->max_utilisations_client) {
            return 'Vous avez déjà utilisé ce code le nombre de fois autorisé.';
        }

        return null;
    }

    /**
     * Montant de réduction d'une promotion sur la base éligible du panier.
     */
    public function montantReduction(CodePromo $promo, Collection $lignes): float
    {
        $base = $this->baseEligible($promo, $lignes);

        if ($base <= 0) {
            return 0.0;
        }

        return $promo->estPourcentage()
            ? round($base * (float) $promo->valeur / 100, 2)
            : round(min((float) $promo->valeur, $base), 2);
    }

    /**
     * Sous-total des lignes éligibles à la promotion. Sans restriction produit
     * ni catégorie, toute la valeur du panier est éligible.
     */
    public function baseEligible(CodePromo $promo, Collection $lignes): float
    {
        if (! $this->aDesRestrictionsProduits($promo)) {
            return round((float) $lignes->sum(
                fn (array $l) => (float) $l['produit']->prix * (int) $l['quantite']
            ), 2);
        }

        $produitsIds = $promo->produits->pluck('id');
        $categoriesIds = $promo->categories->pluck('id');

        return round((float) $lignes
            ->filter(function (array $l) use ($produitsIds, $categoriesIds) {
                $produit = $l['produit'];

                return $produitsIds->contains($produit->id)
                    || $categoriesIds->contains($produit->categorie_id);
            })
            ->sum(fn (array $l) => (float) $l['produit']->prix * (int) $l['quantite']), 2);
    }

    public function nombreUtilisations(CodePromo $promo): int
    {
        return Commande::where('code_promo_id', $promo->id)->count();
    }

    public function nombreUtilisationsClient(CodePromo $promo, string $email): int
    {
        return Commande::where('code_promo_id', $promo->id)
            ->whereHas('client', fn ($q) => $q->where('email', $email))
            ->count();
    }

    private function aDesRestrictionsProduits(CodePromo $promo): bool
    {
        return $promo->produits->isNotEmpty() || $promo->categories->isNotEmpty();
    }

    /**
     * Tri décroissant par priorité puis par montant de réduction, pour que la
     * promotion la plus avantageuse/prioritaire soit toujours retenue en tête.
     */
    private function trierParPriorite(array $candidats, Collection $lignes): array
    {
        usort($candidats, function (array $a, array $b) use ($lignes) {
            $prio = $b['promo']->priorite <=> $a['promo']->priorite;
            if ($prio !== 0) {
                return $prio;
            }

            return $this->montantReduction($b['promo'], $lignes)
                <=> $this->montantReduction($a['promo'], $lignes);
        });

        return $candidats;
    }

    /**
     * Applique la règle de cumul : la première promotion (la plus prioritaire)
     * est toujours retenue ; les suivantes ne s'ajoutent que si toutes les
     * promotions déjà retenues ET la candidate sont cumulables.
     */
    private function selectionnerSelonCumul(array $candidats): array
    {
        $appliques = [];

        foreach ($candidats as $c) {
            if (empty($appliques)) {
                $appliques[] = $c;
                continue;
            }

            $toutesCumulables = $c['promo']->cumulable
                && collect($appliques)->every(fn (array $a) => $a['promo']->cumulable);

            if ($toutesCumulables) {
                $appliques[] = $c;
            }
        }

        return $appliques;
    }

    private function typeReduction(float $reductionCode, float $reductionMembre, ?CodePromo $codePromo): ?string
    {
        if ($reductionCode > 0 && $reductionMembre > 0) {
            return 'mixte';
        }
        if ($reductionCode > 0) {
            return $codePromo?->type_reduction;
        }
        if ($reductionMembre > 0) {
            return 'membre';
        }

        return null;
    }
}

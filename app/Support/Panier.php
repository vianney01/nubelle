<?php

namespace App\Support;

use App\Models\Produit;
use Illuminate\Support\Collection;

/**
 * Panier d'achat basé sur la session (aucune authentification client requise).
 * Le contenu stocké est volontairement minimal : [produit_id => quantite].
 * Les produits sont toujours re-lus depuis la base au moment de l'affichage
 * (lignes()) afin de refléter le prix, le stock et la disponibilité réels.
 */
class Panier
{
    private const CLE_SESSION = 'panier';

    private const CLE_CODE_PROMO = 'code_promo';

    /**
     * Contenu brut de la session : [produit_id => quantite].
     *
     * @return array<int, int>
     */
    public static function contenu(): array
    {
        return session(self::CLE_SESSION, []);
    }

    public static function estVide(): bool
    {
        return empty(self::contenu());
    }

    /**
     * Nombre total d'articles (badge du header) — lecture pure de la
     * session, sans requête, pour rester bon marché sur chaque page.
     */
    public static function nombreArticles(): int
    {
        return array_sum(self::contenu());
    }

    /**
     * Ajoute un produit au panier en respectant le stock disponible.
     *
     * @return array{succes: bool, message: string}
     */
    public static function ajouter(Produit $produit, int $quantite = 1): array
    {
        if (! $produit->actif || $produit->stock <= 0) {
            return ['succes' => false, 'message' => "« {$produit->nom} » n'est plus disponible."];
        }

        $panier = self::contenu();
        $dejaEnPanier = $panier[$produit->id] ?? 0;
        $quantiteVoulue = $dejaEnPanier + max(1, $quantite);
        $quantiteFinale = min($quantiteVoulue, $produit->stock);

        if ($quantiteFinale <= $dejaEnPanier) {
            return [
                'succes' => false,
                'message' => "Stock maximum déjà atteint pour « {$produit->nom} » ({$produit->stock} disponible(s)).",
            ];
        }

        $panier[$produit->id] = $quantiteFinale;
        session([self::CLE_SESSION => $panier]);

        return [
            'succes' => true,
            'message' => $quantiteFinale < $quantiteVoulue
                ? "Quantité ajustée au stock disponible pour « {$produit->nom} »."
                : "« {$produit->nom} » a été ajouté au panier.",
        ];
    }

    /**
     * Fixe la quantité d'une ligne (0 ou moins = suppression), plafonnée au stock.
     *
     * @return array{succes: bool, quantite: int, ajuste: bool}
     */
    public static function modifierQuantite(Produit $produit, int $quantite): array
    {
        $panier = self::contenu();

        if ($quantite <= 0) {
            unset($panier[$produit->id]);
            session([self::CLE_SESSION => $panier]);

            return ['succes' => true, 'quantite' => 0, 'ajuste' => false];
        }

        $quantiteFinale = max(0, min($quantite, $produit->stock));

        if ($quantiteFinale <= 0) {
            unset($panier[$produit->id]);
            session([self::CLE_SESSION => $panier]);

            return ['succes' => true, 'quantite' => 0, 'ajuste' => true];
        }

        $panier[$produit->id] = $quantiteFinale;
        session([self::CLE_SESSION => $panier]);

        return ['succes' => true, 'quantite' => $quantiteFinale, 'ajuste' => $quantiteFinale < $quantite];
    }

    public static function supprimer(int $produitId): void
    {
        $panier = self::contenu();
        unset($panier[$produitId]);
        session([self::CLE_SESSION => $panier]);
    }

    public static function vider(): void
    {
        session()->forget(self::CLE_SESSION);
        session()->forget(self::CLE_CODE_PROMO);
    }

    /**
     * Code promo actuellement saisi par le client (null si aucun).
     */
    public static function codePromo(): ?string
    {
        return session(self::CLE_CODE_PROMO);
    }

    public static function definirCodePromo(string $code): void
    {
        session([self::CLE_CODE_PROMO => trim($code)]);
    }

    public static function retirerCodePromo(): void
    {
        session()->forget(self::CLE_CODE_PROMO);
    }

    /**
     * Lignes hydratées depuis la base (produit + quantité). Retire
     * silencieusement les produits supprimés/désactivés et plafonne toute
     * quantité qui dépasserait le stock actuel, en resynchronisant la
     * session le cas échéant (état vide plutôt que données fictives).
     *
     * @return Collection<int, array{produit: Produit, quantite: int}>
     */
    public static function lignes(): Collection
    {
        $panier = self::contenu();

        if (empty($panier)) {
            return collect();
        }

        $produits = Produit::query()
            ->actifs()
            ->avecAvis()
            ->with('categorie')
            ->whereIn('id', array_keys($panier))
            ->get()
            ->keyBy('id');

        $modifie = false;
        $lignes = collect();

        foreach ($panier as $produitId => $quantite) {
            $produit = $produits->get($produitId);

            if (! $produit) {
                unset($panier[$produitId]);
                $modifie = true;

                continue;
            }

            $quantiteAjustee = min($quantite, $produit->stock);

            if ($quantiteAjustee <= 0) {
                unset($panier[$produitId]);
                $modifie = true;

                continue;
            }

            if ($quantiteAjustee !== $quantite) {
                $panier[$produitId] = $quantiteAjustee;
                $modifie = true;
            }

            $lignes->push(['produit' => $produit, 'quantite' => $quantiteAjustee]);
        }

        if ($modifie) {
            session([self::CLE_SESSION => $panier]);
        }

        return $lignes;
    }

    public static function sousTotal(): float
    {
        return (float) self::lignes()->sum(fn (array $l) => $l['produit']->prix * $l['quantite']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Produit;
use App\Support\CatalogueDemo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ProduitController extends Controller
{
    /**
     * Catalogue : recherche, filtres, tri, pagination — entièrement piloté
     * par la base de données.
     */
    public function index(Request $request)
    {
        $produits = $this->requeteCatalogue($request)
            ->paginate(8)
            ->withQueryString();

        return view('produits.index', [
            'produits' => $produits,
            'categories' => Categorie::query()->orderBy('nom')->get(),
            'q' => trim((string) $request->query('q', '')),
            'tri' => $request->query('tri', 'pertinence'),
            'categorieActive' => $request->query('categorie'),
            'filtreActif' => $request->query('filtre'),
        ]);
    }

    /**
     * Fiche produit détaillée.
     */
    public function show(string $slug)
    {
        $produit = Produit::query()
            ->actifs()
            ->avecAvis()
            ->with('categorie')
            ->where('slug', $slug)
            ->firstOrFail();

        $similaires = Produit::query()
            ->actifs()
            ->avecAvis()
            ->where('categorie_id', $produit->categorie_id)
            ->where('id', '!=', $produit->id)
            ->take(4)
            ->get();

        $avisProduit = $produit->avis()
            ->where('visible', true)
            ->with('client')
            ->latest()
            ->get();

        return view('produits.show', [
            'produit' => $produit,
            'similaires' => $similaires,
            'avisProduit' => $avisProduit,
        ]);
    }

    /**
     * Page dédiée à une catégorie (bannière, description, produits filtrables).
     */
    public function categorie(string $slug, Request $request)
    {
        $categorie = Categorie::query()->where('slug', $slug)->firstOrFail();

        $produits = $this->requeteCatalogue($request, $categorie->id)
            ->paginate(8)
            ->withQueryString();

        return view('produits.categorie', [
            'categorie' => $categorie,
            'produits' => $produits,
            'tri' => $request->query('tri', 'pertinence'),
            'q' => trim((string) $request->query('q', '')),
        ]);
    }

    /**
     * Page marque. NUBELLE est une boutique mono-marque : les produits sont
     * chargés depuis la base ; seul le bloc d'identité (logo, bandeau, texte
     * de présentation) reste statique, faute de table « marques » dédiée.
     */
    public function marque(string $slug)
    {
        $marque = CatalogueDemo::marque();
        abort_unless($slug === $marque['slug'], 404);

        return view('produits.marque', [
            'marque' => $marque,
            'produits' => Produit::query()
                ->actifs()
                ->avecAvis()
                ->with('categorie')
                ->latest()
                ->get(),
        ]);
    }

    /**
     * Résultats de recherche (page complète, paginée).
     */
    public function recherche(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $resultats = $q !== ''
            ? $this->requeteRecherche($q)
                ->avecAvis()
                ->latest()
                ->paginate(12)
                ->withQueryString()
            : null;

        return view('produits.recherche', [
            'q' => $q,
            'resultats' => $resultats,
        ]);
    }

    /**
     * Suggestions de recherche en direct (autocomplete du header), au format JSON.
     */
    public function suggestions(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json([]);
        }

        $produits = $this->requeteRecherche($q)
            ->with('categorie')
            ->take(6)
            ->get();

        return response()->json($produits->map(fn (Produit $p) => [
            'nom' => $p->nom,
            'categorie' => $p->categorie->nom ?? null,
            'prixFormate' => number_format((float) $p->prix, 0, ',', ' ').' FCFA',
            'image' => $p->image,
            'url' => url('/produit/'.$p->slug),
        ])->values());
    }

    /**
     * Requête de recherche partagée entre la page de résultats et les
     * suggestions du header : nom, description et catégorie associée.
     * `whereLike` (insensible à la casse) combiné à la collation
     * utf8mb4_unicode_ci de la base tolère aussi les accents.
     */
    private function requeteRecherche(string $q): Builder
    {
        return Produit::query()
            ->actifs()
            ->where(function ($requete) use ($q) {
                $requete->whereLike('nom', "%{$q}%")
                    ->orWhereLike('description', "%{$q}%")
                    ->orWhereHas('categorie', fn ($cat) => $cat->whereLike('nom', "%{$q}%"));
            });
    }

    /**
     * Construit la requête catalogue (produits actifs, note moyenne préchargée)
     * en appliquant recherche, filtres rapides, prix, disponibilité et tri.
     * Partagée entre le catalogue global et les pages catégorie ; lorsqu'un
     * $categorieId est fourni, le filtre de catégorie de l'URL est ignoré.
     */
    private function requeteCatalogue(Request $request, ?int $categorieId = null): Builder
    {
        $query = Produit::query()->actifs()->avecAvis()->with('categorie');

        if ($categorieId !== null) {
            $query->where('categorie_id', $categorieId);
        } elseif ($slug = $request->query('categorie')) {
            $query->whereHas('categorie', fn (Builder $c) => $c->where('slug', $slug));
        }

        $q = trim((string) $request->query('q', ''));
        if ($q !== '') {
            $query->where(fn (Builder $sub) => $sub
                ->whereLike('nom', "%{$q}%")
                ->orWhereLike('description', "%{$q}%"));
        }

        $filtresBooleens = [
            'nouveaute' => 'nouveaute',
            'best_seller' => 'best_seller',
            'stock_limite' => 'stock_limite',
        ];
        $filtre = $request->query('filtre');
        if ($filtre === 'promotions') {
            $query->whereNotNull('ancien_prix');
        } elseif ($filtre && isset($filtresBooleens[$filtre])) {
            $query->where($filtresBooleens[$filtre], true);
        }

        if ($request->filled('prix_min')) {
            $query->where('prix', '>=', (int) $request->query('prix_min'));
        }
        if ($request->filled('prix_max')) {
            $query->where('prix', '<=', (int) $request->query('prix_max'));
        }
        if ($request->query('disponibilite') === 'en_stock') {
            $query->where('stock', '>', 0);
        }

        return match ($request->query('tri', 'pertinence')) {
            'prix_asc' => $query->orderBy('prix'),
            'prix_desc' => $query->orderByDesc('prix'),
            'nom' => $query->orderBy('nom'),
            'nouveaute' => $query->orderByDesc('nouveaute')->latest(),
            default => $query->latest(),
        };
    }
}

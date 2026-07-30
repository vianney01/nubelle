<?php

namespace App\Support;

/**
 * Source de données de démonstration pour le front-office.
 *
 * Centralise le catalogue afin que tous les contrôleurs (accueil, catalogue,
 * fiche produit, catégorie, marque, recherche…) partagent exactement la même
 * donnée. Conçue pour être remplacée par des requêtes Eloquent le jour où
 * les tables produits/catégories seront créées, sans changer les contrôleurs.
 */
class CatalogueDemo
{
    public static function categories(): array
    {
        return [
            ['slug' => 'visage',  'nom' => 'Visage',  'image' => 'visage.jpg', 'description' => "Sérums, crèmes et soins ciblés pour révéler l'éclat naturel de votre peau."],
            ['slug' => 'corps',   'nom' => 'Corps',   'image' => 'corp.jpg',   'description' => 'Huiles, laits et gommages pour une peau nourrie et sublimée au quotidien.'],
            ['slug' => 'soins',   'nom' => 'Soins',   'image' => 'tete.jpg',   'description' => 'Rituels de soins intensifs pensés pour chaque type de peau.'],
            ['slug' => 'besoins', 'nom' => 'Besoins', 'image' => 'pied.jpg',   'description' => 'Nos indispensables beauté : parfums, soins capillaires et essentiels.'],
        ];
    }

    public static function marque(): array
    {
        return [
            'slug' => 'nubelle',
            'nom' => 'Nubelle Cosmetics',
            'logo' => 'logo.png',
            'image_bandeau' => 'accueil.jpg',
            'description' => "Nubelle Cosmetics est née de la passion d'une femme ivoirienne déterminée à sublimer la beauté naturelle de toutes les peaux. À travers des produits de qualité, naturels et adaptés, notre fondatrice souhaite offrir à chaque femme la confiance et l'éclat qu'elle mérite.",
        ];
    }

    public static function produits(): array
    {
        return [
            [
                'slug' => 'serum-eclat', 'nom' => 'Sérum éclat', 'categorie' => 'visage',
                'image' => 'produit.jpeg', 'images' => ['produit.jpeg', 'produit3.jpeg', 'Produit2.jpeg'],
                'description' => 'Hydrate & illumine le teint', 'badge' => 'nouveau',
                'description_longue' => "Ce sérum concentré en actifs éclaircissants et hydratants redonne à votre peau sa luminosité naturelle dès les premières applications. Sa texture légère pénètre instantanément sans laisser de film gras.",
                'composition' => "Eau, Glycerin, Niacinamide, Extrait de vitamine C, Acide hyaluronique, Extrait de karité, Parfum naturel.",
                'conseils' => "Appliquer matin et soir sur peau propre, avant la crème hydratante. Éviter le contour des yeux. Usage externe uniquement.",
                'prix' => 12000, 'ancien_prix' => null, 'stock' => 18,
                'etoiles' => 5, 'avis' => 100, 'cta' => 'Ajouter au panier',
                'nouveaute' => true, 'best_seller' => false, 'promotion' => false, 'stock_limite' => false,
            ],
            [
                'slug' => 'pack-eclat', 'nom' => 'Pack éclat', 'categorie' => 'visage',
                'image' => 'Produit2.jpeg', 'images' => ['Produit2.jpeg', 'produit.jpeg', 'produit4.jpeg'],
                'description' => 'Nettoie + nourrit + protège', 'badge' => 'meilleur vendeur',
                'description_longue' => "Un trio pensé pour une routine complète : nettoyant doux, sérum éclat et crème protectrice, pour une peau visiblement plus nette et repulpée.",
                'composition' => "Eau, huiles végétales, extraits botaniques, glycérine, vitamine E.",
                'conseils' => "Utiliser les trois produits dans l'ordre : nettoyant, sérum, crème. Idéal en routine du matin.",
                'prix' => 18000, 'ancien_prix' => null, 'stock' => 9,
                'etoiles' => 4, 'avis' => 200, 'cta' => 'Voir le pack',
                'nouveaute' => false, 'best_seller' => true, 'promotion' => false, 'stock_limite' => false,
            ],
            [
                'slug' => 'creme-apaisante', 'nom' => 'Crème apaisante', 'categorie' => 'visage',
                'image' => 'produit3.jpeg', 'images' => ['produit3.jpeg', 'produit.jpeg'],
                'description' => 'Apaise rougeurs & tiraillements', 'badge' => 'rupture',
                'description_longue' => "Formulée pour les peaux sensibles, cette crème apaise instantanément les tiraillements et rougeurs grâce à son complexe végétal doux.",
                'composition' => "Eau, beurre de karité, aloe vera, camomille, allantoïne.",
                'conseils' => "Appliquer en fine couche sur les zones sensibles, matin et soir.",
                'prix' => 15000, 'ancien_prix' => null, 'stock' => 0,
                'etoiles' => 5, 'avis' => 150, 'cta' => 'Prévenez-moi',
                'nouveaute' => false, 'best_seller' => false, 'promotion' => false, 'stock_limite' => false,
            ],
            [
                'slug' => 'baume-levres-caramel', 'nom' => 'Baume lèvres caramel', 'categorie' => 'visage',
                'image' => 'produit4.jpeg', 'images' => ['produit4.jpeg', 'produit3.jpeg'],
                'description' => 'Hydrate et adoucit', 'badge' => '+3 nuances',
                'description_longue' => "Un baume gourmand à la texture fondante qui nourrit intensément les lèvres tout en leur apportant une jolie teinte caramel.",
                'composition' => "Beurre de karité, cire d'abeille, huile de coco, vitamine E, arôme caramel.",
                'conseils' => "Appliquer généreusement sur les lèvres, renouveler dans la journée selon besoin.",
                'nuances' => ['blanc', 'beige', 'brun'],
                'prix' => 9000, 'ancien_prix' => null, 'stock' => 25,
                'etoiles' => 4, 'avis' => 80, 'cta' => 'Ajouter au panier',
                'nouveaute' => false, 'best_seller' => false, 'promotion' => false, 'stock_limite' => false,
            ],
            [
                'slug' => 'huile-corps-nourrissante', 'nom' => 'Huile corps nourrissante', 'categorie' => 'corps',
                'image' => 'produit3.jpeg', 'images' => ['produit3.jpeg', 'produit4.jpeg'],
                'description' => 'Nourrit et sublime la peau', 'badge' => 'promo',
                'description_longue' => "Un mélange d'huiles précieuses qui pénètre rapidement pour nourrir intensément la peau et lui offrir un fini satiné, non gras.",
                'composition' => "Huile de coco, huile d'argan, huile de macadamia, vitamine E.",
                'conseils' => "Appliquer sur peau humide après la douche, masser jusqu'à absorption complète.",
                'prix' => 14000, 'ancien_prix' => 19000, 'stock' => 14,
                'etoiles' => 5, 'avis' => 64, 'cta' => 'Ajouter au panier',
                'nouveaute' => false, 'best_seller' => false, 'promotion' => true, 'stock_limite' => false,
            ],
            [
                'slug' => 'gommage-corps-douceur', 'nom' => 'Gommage corps douceur', 'categorie' => 'corps',
                'image' => 'produit4.jpeg', 'images' => ['produit4.jpeg', 'produit.jpeg'],
                'description' => 'Exfolie en douceur', 'badge' => null,
                'description_longue' => "Un gommage fin au sucre de canne et huiles végétales qui élimine les cellules mortes sans agresser la peau.",
                'composition' => "Sucre de canne, huile d'amande douce, huile de coco.",
                'conseils' => "1 à 2 fois par semaine, masser en mouvements circulaires puis rincer.",
                'prix' => 11000, 'ancien_prix' => null, 'stock' => 20,
                'etoiles' => 4, 'avis' => 37, 'cta' => 'Ajouter au panier',
                'nouveaute' => false, 'best_seller' => false, 'promotion' => false, 'stock_limite' => false,
            ],
            [
                'slug' => 'lait-corps-hydratant', 'nom' => 'Lait corps hydratant', 'categorie' => 'corps',
                'image' => 'produit.jpeg', 'images' => ['produit.jpeg', 'Produit2.jpeg'],
                'description' => 'Hydratation 48h', 'badge' => 'nouveau',
                'description_longue' => "Un lait corps à absorption rapide qui hydrate intensément pendant 48h grâce à son complexe hydratant longue durée.",
                'composition' => "Eau, beurre de karité, glycérine, panthénol, parfum naturel.",
                'conseils' => "Appliquer quotidiennement sur l'ensemble du corps, en particulier après la douche.",
                'prix' => 13000, 'ancien_prix' => null, 'stock' => 30,
                'etoiles' => 5, 'avis' => 52, 'cta' => 'Ajouter au panier',
                'nouveaute' => true, 'best_seller' => false, 'promotion' => false, 'stock_limite' => false,
            ],
            [
                'slug' => 'serum-anti-age', 'nom' => 'Sérum anti-âge intense', 'categorie' => 'soins',
                'image' => 'Produit2.jpeg', 'images' => ['Produit2.jpeg', 'produit3.jpeg'],
                'description' => 'Repulpe et lisse les traits', 'badge' => 'meilleur vendeur',
                'description_longue' => "Un soin concentré en peptides et acide hyaluronique qui agit sur les signes visibles de l'âge pour une peau visiblement repulpée.",
                'composition' => "Acide hyaluronique, peptides, extrait de rose, vitamine E.",
                'conseils' => "Appliquer le soir sur peau propre, en évitant le contour des yeux.",
                'prix' => 22000, 'ancien_prix' => 27000, 'stock' => 6,
                'etoiles' => 5, 'avis' => 91, 'cta' => 'Ajouter au panier',
                'nouveaute' => false, 'best_seller' => true, 'promotion' => true, 'stock_limite' => true,
            ],
            [
                'slug' => 'masque-argile-purifiant', 'nom' => 'Masque argile purifiant', 'categorie' => 'soins',
                'image' => 'produit3.jpeg', 'images' => ['produit3.jpeg', 'produit.jpeg'],
                'description' => 'Purifie et resserre les pores', 'badge' => null,
                'description_longue' => "Une argile verte purifiante qui absorbe l'excès de sébum et resserre visiblement les pores pour un teint net.",
                'composition' => "Argile verte, eau florale de romarin, extrait d'aloe vera.",
                'conseils' => "Appliquer en couche fine, laisser poser 10 minutes puis rincer à l'eau tiède. 1 fois par semaine.",
                'prix' => 10000, 'ancien_prix' => null, 'stock' => 40,
                'etoiles' => 4, 'avis' => 45, 'cta' => 'Ajouter au panier',
                'nouveaute' => false, 'best_seller' => false, 'promotion' => false, 'stock_limite' => false,
            ],
            [
                'slug' => 'huile-cheveux-brillance', 'nom' => 'Huile cheveux brillance', 'categorie' => 'besoins',
                'image' => 'produit4.jpeg', 'images' => ['produit4.jpeg', 'produit3.jpeg'],
                'description' => 'Nourrit et fait briller', 'badge' => 'nouveau',
                'description_longue' => "Un soin capillaire sans rinçage qui nourrit les longueurs et pointes tout en apportant une brillance miroir.",
                'composition' => "Huile d'argan, huile de ricin, huile de jojoba, vitamine E.",
                'conseils' => "Appliquer quelques gouttes sur cheveux humides ou secs, en évitant les racines.",
                'prix' => 16000, 'ancien_prix' => null, 'stock' => 12,
                'etoiles' => 5, 'avis' => 28, 'cta' => 'Ajouter au panier',
                'nouveaute' => true, 'best_seller' => false, 'promotion' => false, 'stock_limite' => false,
            ],
            [
                'slug' => 'parfum-signature-nubelle', 'nom' => 'Parfum signature Nubelle', 'categorie' => 'besoins',
                'image' => 'parfum.png', 'images' => ['parfum.png'],
                'description' => 'Une fragrance florale et boisée', 'badge' => 'meilleur vendeur',
                'description_longue' => "Notre parfum signature marie des notes florales délicates à un fond boisé chaleureux, pour une signature olfactive unique et durable.",
                'composition' => "Alcool denat., parfum, eau, colorant.",
                'conseils' => "Vaporiser sur les points de pulsation : poignets, cou, derrière les oreilles.",
                'prix' => 25000, 'ancien_prix' => null, 'stock' => 8,
                'etoiles' => 5, 'avis' => 73, 'cta' => 'Ajouter au panier',
                'nouveaute' => false, 'best_seller' => true, 'promotion' => false, 'stock_limite' => false,
            ],
        ];
    }

    public static function produitParSlug(string $slug): ?array
    {
        foreach (self::produits() as $produit) {
            if ($produit['slug'] === $slug) {
                return $produit;
            }
        }

        return null;
    }

    public static function produitsSimilaires(array $produit, int $limite = 4): array
    {
        return collect(self::produits())
            ->where('slug', '!=', $produit['slug'])
            ->where('categorie', $produit['categorie'])
            ->take($limite)
            ->values()
            ->all();
    }

    /**
     * Contenu de démonstration pour panier/checkout (pas de session panier
     * réelle pour l'instant — uniquement la couche de présentation).
     */
    public static function panierDemo(): array
    {
        $produits = self::produits();

        return [
            ['produit' => $produits[0], 'quantite' => 1],
            ['produit' => $produits[3], 'quantite' => 2],
        ];
    }

    public static function avis(): array
    {
        return [
            ['note' => 5, 'auteur' => 'Kouamé Bertrand, 25 ans', 'texte' => 'Les produits Nubelle sont incroyables, je suis ravi de mon achat.', 'image' => 'produit3.jpeg'],
            ['note' => 4, 'auteur' => 'Rachel, 22 ans',          'texte' => 'Très satisfaite de ma crème hydratante, merci pour votre réactivité.', 'image' => 'produit.jpeg'],
            ['note' => 3, 'auteur' => 'Guy, 32 ans',             'texte' => 'Merci pour la livraison rapide depuis Bouaké, je commanderai encore.', 'image' => 'Produit2.jpeg'],
        ];
    }

    public static function faqsGroupees(): array
    {
        return [
            'Commandes & paiement' => [
                ['q' => 'Comment passer une commande ?', 'r' => "Ajoutez vos produits au panier puis suivez les étapes du tunnel de commande : adresse, livraison, paiement et récapitulatif."],
                ['q' => 'Quels moyens de paiement acceptez-vous ?', 'r' => 'Carte bancaire (Visa, Mastercard), Mobile Money et paiement à la livraison selon votre zone.'],
                ['q' => 'Puis-je utiliser un code promo ?', 'r' => "Oui, le champ code promo est disponible dans votre panier avant de passer au paiement."],
            ],
            'Livraison' => [
                ['q' => 'Quel est le prix de la livraison ?', 'r' => "Livraison pour Abidjan à partir de 500 FCFA, pour l'intérieur de la Côte d'Ivoire à partir de 1500 FCFA. L'Afrique et l'Europe à partir de 5000 FCFA."],
                ['q' => 'Quels sont vos délais de livraison ?', 'r' => 'Généralement entre 24h et 72h selon la zone. Un numéro de suivi vous sera communiqué.'],
                ['q' => 'Livrez-vous en Afrique et en Europe ?', 'r' => "Oui, nous livrons dans toute la Côte d'Ivoire, en Afrique et en Europe via nos partenaires logistiques."],
            ],
            'Retours & remboursements' => [
                ['q' => 'Puis-je retourner un produit ?', 'r' => "Oui, sous 14 jours après réception, produit non ouvert et dans son emballage d'origine."],
                ['q' => 'Combien de temps pour être remboursé ?', 'r' => 'Le remboursement est effectué sous 5 à 7 jours ouvrés après réception du retour.'],
            ],
            'Produits' => [
                ['q' => 'Vos produits sont-ils naturels ?', 'r' => "La majorité de nos formules privilégient des ingrédients naturels et des extraits botaniques, précisés sur chaque fiche produit."],
                ['q' => 'Comment choisir le bon produit pour ma peau ?', 'r' => "Chaque fiche produit détaille les bienfaits et le type de peau conseillé. N'hésitez pas à nous contacter pour un conseil personnalisé."],
            ],
        ];
    }
}

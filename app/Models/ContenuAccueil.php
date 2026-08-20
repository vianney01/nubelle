<?php

namespace App\Models;

use App\Support\Whatsapp;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Contenus éditables de la page d'accueil (enregistrement unique).
 *
 * Regroupe les textes, images et boutons des sections Hero, « Pourquoi choisir
 * Nubelle » (dont le bloc image + bouton « mise en avant » attenant) et
 * « À propos ». Les valeurs par défaut reprennent exactement le contenu qui
 * était codé en dur, afin que l'affichage reste identique tant qu'aucune
 * modification n'est faite dans le back-office.
 */
class ContenuAccueil extends Model
{
    protected $table = 'contenu_accueil';

    protected $fillable = [
        'hero_image', 'hero_sous_titre', 'hero_titre', 'hero_bouton_texte', 'hero_bouton_lien',
        'pourquoi_image', 'pourquoi_eyebrow', 'pourquoi_titre', 'pourquoi_bouton_texte', 'pourquoi_bouton_lien',
        'apropos_image', 'apropos_sous_titre', 'apropos_titre', 'apropos_texte', 'apropos_bouton_texte', 'apropos_bouton_lien',
        'popup_actif', 'popup_image', 'popup_badge', 'popup_titre', 'popup_sous_titre',
        'popup_code_promo_id', 'popup_bouton_texte', 'popup_bouton_lien', 'popup_cible',
        'tiktok_url', 'facebook_url', 'instagram_url', 'whatsapp_numero',
        'reseaux_eyebrow', 'reseaux_titre', 'reseaux_images',
    ];

    protected $casts = [
        'popup_actif' => 'boolean',
        'reseaux_images' => 'array',
    ];

    /**
     * Contenu par défaut = ce qui était codé en dur dans home.blade.php.
     * Les images restent nulles : le front retombe alors sur les fichiers
     * d'origine (public/images), et l'aperçu Filament n'affiche pas de vignette
     * cassée tant qu'aucune image n'a été uploadée.
     */
    public const DEFAUTS = [
        'hero_sous_titre' => 'Nubelle Cosmetics',
        'hero_titre' => 'Une peau naturellement neuve',
        'hero_bouton_texte' => 'Découvrir la boutique',
        'hero_bouton_lien' => '/produits',

        'pourquoi_eyebrow' => 'Notre engagement',
        'pourquoi_titre' => 'Pourquoi choisir Nubelle',
        'pourquoi_bouton_texte' => 'Voir plus',
        'pourquoi_bouton_lien' => '/produits',

        'apropos_sous_titre' => 'Notre histoire',
        'apropos_titre' => 'À propos de Nubelle',
        'apropos_texte' => "Nubelle Cosmetics est née de la passion d'une femme ivoirienne déterminée à sublimer la beauté naturelle de toutes les peaux. À travers des produits de qualité, naturels et adaptés, notre fondatrice souhaite offrir à chaque femme la confiance et l'éclat qu'elle mérite. Chaque soin est conçu avec amour, précision et un profond respect de la peau.",
        'apropos_bouton_texte' => 'En savoir plus →',
        'apropos_bouton_lien' => '/a-propos',

        'popup_actif' => false,
        'popup_badge' => 'Offre de bienvenue',
        'popup_titre' => 'Bienvenue chez Nubelle',
        'popup_sous_titre' => 'Profitez d’un avantage sur votre première commande.',
        'popup_bouton_texte' => 'Créer mon compte',
        'popup_bouton_lien' => '/connexion',
        'popup_cible' => 'non_connectes',

        'tiktok_url' => 'https://www.tiktok.com/@nubellecosmetics',
        'facebook_url' => 'https://www.facebook.com/nubellecosmetics',
        'instagram_url' => 'https://www.instagram.com/nubellecosmetics',
        'whatsapp_numero' => '+2250700000000',

        'reseaux_eyebrow' => '@nubellecosmetics',
        'reseaux_titre' => 'Suivez-nous sur nos réseaux sociaux',
    ];

    /**
     * Enregistrement unique des contenus (créé avec les valeurs par défaut
     * s'il n'existe pas encore).
     */
    public static function instance(): self
    {
        return static::query()->firstOr(fn () => static::create(self::DEFAUTS));
    }

    protected function heroImageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->urlImage($this->hero_image));
    }

    protected function pourquoiImageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->urlImage($this->pourquoi_image));
    }

    protected function aproposImageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->urlImage($this->apropos_image));
    }

    protected function popupImageUrl(): Attribute
    {
        return Attribute::get(fn () => $this->urlImage($this->popup_image));
    }

    /**
     * Numéro WhatsApp prêt pour un lien https://wa.me/… (chiffres sans « + »),
     * ou null si aucun numéro valide n'est configuré (le bouton est alors masqué).
     */
    protected function whatsappLien(): Attribute
    {
        return Attribute::get(fn () => Whatsapp::pourLienWa(Whatsapp::normaliser($this->whatsapp_numero)));
    }

    /**
     * Numéro formaté pour l'affichage (ex : « +225 07 00 00 00 00 »).
     */
    protected function whatsappAffichage(): Attribute
    {
        return Attribute::get(function () {
            $normalise = Whatsapp::normaliser($this->whatsapp_numero);
            if ($normalise === null) {
                return $this->whatsapp_numero;
            }

            return '+225 '.implode(' ', str_split(substr($normalise, 4), 2));
        });
    }

    /**
     * URLs affichables des images du bloc « Suivez-nous » (mur de photos de la
     * page d'accueil). Chemins uploadés via Filament ou anciens fichiers de
     * démonstration, résolus par urlImage().
     *
     * @return list<string>
     */
    protected function reseauxImagesUrls(): Attribute
    {
        return Attribute::get(function () {
            $urls = [];

            foreach ((array) ($this->reseaux_images ?? []) as $chemin) {
                // Ignore un fichier uploadé désormais absent du disque (évite une
                // image cassée si le fichier a disparu ou n'a pas été déployé).
                if (filled($chemin) && str_contains((string) $chemin, '/')
                    && ! Storage::disk('public')->exists($chemin)) {
                    continue;
                }
                if ($url = $this->urlImage($chemin)) {
                    $urls[] = $url;
                }
            }

            return array_values($urls);
        });
    }

    /**
     * Code promo de bienvenue affiché dans la pop-up (référence au module
     * Promotions — jamais dupliqué).
     */
    public function codePromoPopup(): BelongsTo
    {
        return $this->belongsTo(CodePromo::class, 'popup_code_promo_id');
    }

    /**
     * Détermine si la pop-up doit être affichée au visiteur courant, selon
     * l'audience ciblée. « clientId » = identifiant du client en session
     * (posé après une première commande) ; « aCommande » = ce client a déjà
     * au moins une commande. Après une première commande, les cibles
     * d'acquisition ne s'appliquent plus → la pop-up disparaît d'elle-même.
     */
    public function popupVisiblePour(?int $clientId, bool $aCommande): bool
    {
        if (! $this->popup_actif) {
            return false;
        }

        return match ($this->popup_cible) {
            'non_connectes' => $clientId === null,
            'nouveaux_inscrits' => $clientId !== null && ! $aCommande,
            'jamais_commande' => $clientId === null || ! $aCommande,
            default => true,
        };
    }

    /**
     * Résout l'URL d'une image : fichier uploadé via Filament (disque public,
     * chemin avec « / ») servi en statique depuis public/uploads (/uploads/…) ;
     * ancien nom de fichier de démonstration servi depuis public/images/.
     */
    private function urlImage(?string $valeur): ?string
    {
        if (blank($valeur)) {
            return null;
        }

        return str_contains($valeur, '/')
            ? Storage::disk('public')->url($valeur)
            : asset('images/'.$valeur);
    }
}

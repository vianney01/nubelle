<?php

namespace App\Providers;

use App\Models\ContenuAccueil;
use App\Support\Panier;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Nombre d'articles + aperçu du panier, disponibles sur toutes les
        // pages du front-office (badge du header + mini-panier) sans que
        // chaque contrôleur ait à les transmettre manuellement.
        View::composer('layouts.app', function ($view) {
            $view->with('nbPanier', Panier::nombreArticles());
            $view->with('panierApercu', Panier::nombreArticles() > 0 ? Panier::lignes() : collect());

            // Liens réseaux sociaux (footer + menu latéral), gérés depuis le
            // back-office. TikTok en premier ; les liens vides sont retirés.
            $contenu = ContenuAccueil::instance();
            $view->with('reseaux', array_filter([
                'tiktok' => $contenu->tiktok_url,
                'facebook' => $contenu->facebook_url,
                'instagram' => $contenu->instagram_url,
            ]));

            // Numéro WhatsApp de la boutique (bouton flottant, menu, contact).
            $view->with('whatsappLien', $contenu->whatsapp_lien);
            $view->with('whatsappTel', $contenu->whatsapp_numero);
            $view->with('whatsappAffichage', $contenu->whatsapp_affichage);
        });

        // La page Contact utilise le numéro WhatsApp dans son propre contenu
        // (bloc coordonnées), rendu avant le layout : on le lui partage aussi.
        View::composer('pages.contact', function ($view) {
            $contenu = ContenuAccueil::instance();
            $view->with('whatsappLien', $contenu->whatsapp_lien);
            $view->with('whatsappTel', $contenu->whatsapp_numero);
            $view->with('whatsappAffichage', $contenu->whatsapp_affichage);
        });
    }
}

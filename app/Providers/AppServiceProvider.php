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
        });
    }
}

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Adresse de notification des commandes
    |--------------------------------------------------------------------------
    |
    | Adresse e-mail qui reçoit une notification à chaque nouvelle commande
    | passée depuis la boutique. Définir NUBELLE_ADMIN_EMAIL dans le .env ;
    | à défaut, l'adresse d'expédition (MAIL_FROM_ADDRESS) est utilisée.
    |
    */

    'admin_email' => env('NUBELLE_ADMIN_EMAIL', env('MAIL_FROM_ADDRESS')),

];

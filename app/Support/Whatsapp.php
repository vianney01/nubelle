<?php

namespace App\Support;

/**
 * Normalisation des numéros WhatsApp ivoiriens.
 *
 * Accepte les formats saisis couramment et les ramène à une forme canonique
 * internationale « +225 » suivie du numéro local à 10 chiffres :
 *
 *   0556400246      -> +2250556400246
 *   2250556400246   -> +2250556400246
 *   +2250556400246  -> +2250556400246
 *   +225 05 56 40 02 46 -> +2250556400246   (espaces/séparateurs ignorés)
 */
class Whatsapp
{
    /**
     * Renvoie le numéro normalisé (+225XXXXXXXXXX) ou null si le format n'est
     * pas reconnu.
     */
    public static function normaliser(?string $valeur): ?string
    {
        if (blank($valeur)) {
            return null;
        }

        // Ne garder que les chiffres (supprime +, espaces, points, tirets…).
        $chiffres = preg_replace('/\D+/', '', $valeur);

        if (str_starts_with($chiffres, '225') && strlen($chiffres) === 13) {
            $local = substr($chiffres, 3);
        } elseif (strlen($chiffres) === 10 && str_starts_with($chiffres, '0')) {
            $local = $chiffres;
        } else {
            return null;
        }

        // Le numéro local ivoirien fait 10 chiffres et commence par 0.
        if (strlen($local) !== 10 || $local[0] !== '0') {
            return null;
        }

        return '+225'.$local;
    }

    /**
     * Numéro au format international sans le « + », utilisable dans un lien
     * https://wa.me/… (ex : 2250556400246).
     */
    public static function pourLienWa(?string $normalise): ?string
    {
        return $normalise ? ltrim($normalise, '+') : null;
    }
}

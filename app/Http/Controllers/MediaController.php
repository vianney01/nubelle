<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    /**
     * Sert les fichiers uploadés (disque "public") directement depuis le
     * stockage, sans dépendre du lien symbolique public/storage — sur
     * Windows, `php artisan storage:link` crée une Junction NTFS que le
     * serveur de développement PHP intégré sert mal (403 Forbidden).
     */
    public function show(string $path): StreamedResponse
    {
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Categorie extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [
        'nom',
        'slug',
        'description',
        'image',
    ];

    public function produits(): HasMany
    {
        return $this->hasMany(Produit::class);
    }

    /**
     * URL utilisable de l'image de catégorie — même logique que Produit::image :
     * les fichiers uploadés depuis Filament (ex: "categories/abc.png") sont
     * servis via MediaController, les anciens noms de démonstration (ex:
     * "visage.jpg") depuis public/images/. Renvoie null si aucune image.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::get(function () {
            if (blank($this->image)) {
                return null;
            }

            return str_contains($this->image, '/')
                ? Storage::disk('public')->url($this->image)
                : asset('images/'.$this->image);
        });
    }
}

<?php

/**
 * MODEL COUNTRY - VERSION CORRIGÉE
 * 
 * Chemin : app/Models/Country.php
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    // Les champs qu'on peut remplir
    protected $fillable = [
        'name_en',
        'is_active',
    ];

    // Relation avec les produits
    public function products()
    {
        return $this->hasMany(Product::class, 'organization_country_id');
    }
}



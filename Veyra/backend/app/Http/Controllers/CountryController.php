<?php

/**
 * CONTROLLER COUNTRY - VERSION SIMPLE ET FIABLE
 * 
 * Chemin : app/Http/Controllers/Api/CountryController.php
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Country;

class CountryController extends Controller
{
    /**
     * Liste de tous les pays actifs
     */
    public function index()
    {
        $countries = Country::where('is_active', true)
            ->orderBy('name_en')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $countries
        ]);
    }

    /**
     * Voir un pays spécifique
     */
    public function show($id)
    {
        $country = Country::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $country
        ]);
    }
}
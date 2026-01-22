<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductTypeController extends Controller
{
    /**
     * GET /api/categories
     * Récupérer toutes les catégories avec leurs sous-catégories
     */
    public function getCategories()
    {
        $categories = Category::with('subcategories')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * GET /api/products/{productId}/type
     * Récupérer les données du volet 2 pour un produit
     */
    public function getProductType($productId)
    {
        $product = Product::with('category', 'subcategory')->findOrFail($productId);

        return response()->json([
            'success' => true,
            'data' => [
                'category' => $product->category,
                'subcategory' => $product->subcategory,
                'status' => $product->volet_2_status,
                'completed' => $product->volet_2_completed,
            ],
        ]);
    }

    /**
     * POST /api/products/{productId}/type/save-progress
     * Bouton ORANGE : sauvegarde partielle
     * - Volet 1 doit être complété
     * - Catégorie obligatoire
     * - Sous-catégorie optionnelle
     */
    public function saveProgress(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->volet_1_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Volet 1 must be completed before accessing Volet 2',
            ], 403);
        }

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => ['nullable', 'exists:subcategories,id'],
        ]);

        // Si une sous-catégorie est donnée, vérifier qu’elle appartient bien à la catégorie choisie
        if (!empty($validated['subcategory_id'])) {
            $ok = \App\Models\Subcategory::where('id', $validated['subcategory_id'])
                ->where('category_id', $validated['category_id'])
                ->exists();

            if (!$ok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected subcategory does not belong to the selected category',
                ], 422);
            }
        }

        // Sauvegarde partielle => statut ORANGE, completed = false
        $product->update([
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'] ?? null,
            'volet_2_status' => 'orange',
            'volet_2_completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Progress saved successfully',
            'status' => 'orange',
            'data' => $product->load('category', 'subcategory'),
        ]);
    }

    /**
     * POST /api/products/{productId}/type/validate-step
     * Bouton VERT : valider et passer au volet 3
     * - Volet 1 doit être complété
     * - Catégorie + sous-catégorie obligatoires
     * - Sous-catégorie doit appartenir à la catégorie
     */
    public function validateStep(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->volet_1_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Volet 1 must be completed before completing Volet 2',
            ], 403);
        }

        $validated = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'subcategory_id' => [
                'required',
                Rule::exists('subcategories', 'id')->where(fn ($q) => $q->where('category_id', $request->input('category_id'))),
            ],
        ]);

        $product->update([
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'],
            'volet_2_status' => 'green',
            'volet_2_completed' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Step 2 completed successfully',
            'status' => 'green',
            'next_step' => 3,
            'data' => $product->load('category', 'subcategory'),
        ]);
    }
}

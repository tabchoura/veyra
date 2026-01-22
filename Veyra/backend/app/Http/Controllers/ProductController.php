<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Règles VOLET 1 (Initialisation du produit)
     * CDC : image obligatoire (JPG/PNG/WEBP), poids positif, description max 3000, etc.
     */
    private function volet1Rules(bool $imageRequired = true): array
    {
        return [
            'product_image' => ($imageRequired ? 'required' : 'sometimes') . '|file|mimes:jpg,jpeg,png,webp|max:5120',
            'product_name' => 'required|string|max:255',
            'weight' => 'required|numeric|min:0', // valeur positive réelle
            'batch_serial' => 'nullable|string|max:100',
            'prodcom_code' => 'nullable|string|max:50',
            'declaring_organization' => 'required|string|max:255',
            'organization_country_id' => 'required|exists:countries,id',
            'organization_address' => 'nullable|string',
            // CDC dit "numérique entier" mais on le garde en string digits (pratique pour codes postaux avec 0)
            'postal_code' => 'nullable|regex:/^\d+$/|max:20',
            'item_description' => 'required|string|max:3000',
        ];
    }

    /**
     * Générer un code article automatiquement (Index automatique)
     * On utilise ULID => unique + évite les collisions.
     */
    private function generateItemCode(): string
    {
        do {
            $code = 'ITEM-' . Str::upper((string) Str::ulid());
        } while (Product::withTrashed()->where('item_code', $code)->exists());

        return $code;
    }

    /**
     * GET /api/products
     * Liste des produits de l'utilisateur connecté
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    /**
     * GET /api/products/{id}
     * Détails d'un produit (utile Postman)
     */
    public function show(Request $request, $id)
    {
        $product = Product::where('user_id', $request->user()->id)->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product,
        ]);
    }

    /**
     * POST /api/products
     * Création du produit (VOLET 1) -> Sauvegarde progression => ORANGE
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->volet1Rules(true));

        return DB::transaction(function () use ($request, $validated) {

            $path = $request->file('product_image')->store('products', 'public');

            $product = Product::create([
                'item_code' => $this->generateItemCode(), // lecture seule côté front
                // creation_datetime géré par DB (useCurrent)
                'product_image' => $path,
                'product_name' => $validated['product_name'],
                'weight' => $validated['weight'],
                'batch_serial' => $validated['batch_serial'] ?? null,
                'prodcom_code' => $validated['prodcom_code'] ?? null,
                'declaring_organization' => $validated['declaring_organization'],
                'organization_country_id' => $validated['organization_country_id'],
                'organization_address' => $validated['organization_address'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'item_description' => $validated['item_description'],

                // Save progress => ORANGE (en cours)
                'volet_1_status' => 'orange',
                'volet_1_completed' => false,

                // Les autres volets restent grey par défaut (migration), mais ok de les laisser.
                'user_id' => $request->user()->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Volet 1 saved (progress).',
                'status' => 'orange',
                'data' => $product,
            ], 201);
        });
    }

    /**
     * POST /api/products/save-progress
     * Mise à jour VOLET 1 (produit déjà créé) -> ORANGE
     * Body : product_id + champs volet 1 (+ image optionnelle)
     */
    public function saveProgress(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $product = Product::where('user_id', $request->user()->id)->findOrFail($request->product_id);

        // Image non obligatoire si déjà existante
        $validated = $request->validate($this->volet1Rules(false));

        return DB::transaction(function () use ($request, $product, $validated) {

            $updates = [
                'product_name' => $validated['product_name'],
                'weight' => $validated['weight'],
                'batch_serial' => $validated['batch_serial'] ?? null,
                'prodcom_code' => $validated['prodcom_code'] ?? null,
                'declaring_organization' => $validated['declaring_organization'],
                'organization_country_id' => $validated['organization_country_id'],
                'organization_address' => $validated['organization_address'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'item_description' => $validated['item_description'],

                // Sauvegarde partielle => ORANGE
                'volet_1_status' => 'orange',
                'volet_1_completed' => false,
            ];

            // Si nouvelle image uploadée : remplacer + supprimer l'ancienne
            if ($request->hasFile('product_image')) {
                $newPath = $request->file('product_image')->store('products', 'public');

                if (!empty($product->product_image)) {
                    Storage::disk('public')->delete($product->product_image);
                }

                $updates['product_image'] = $newPath;
            }

            $product->update($updates);

            return response()->json([
                'success' => true,
                'message' => 'Progress saved successfully',
                'status' => 'orange',
                'data' => $product,
            ]);
        });
    }

    /**
     * POST /api/products/{id}/complete-volet1
     * Étape suivante (VOLET 1 -> VOLET 2) => VERT + completed = true
     */
    public function completeVolet1(Request $request, $id)
    {
        $product = Product::where('user_id', $request->user()->id)->findOrFail($id);

        // Vérifier champs obligatoires (CDC)
        $missing = [];
        $requiredFields = [
            'item_code',
            'creation_datetime',
            'product_image',
            'product_name',
            'weight',
            'declaring_organization',
            'organization_country_id',
            'item_description',
        ];

        foreach ($requiredFields as $field) {
            if (empty($product->{$field})) {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            return response()->json([
                'success' => false,
                'message' => 'All required fields must be filled before going to next step',
                'missing' => $missing,
            ], 422);
        }

        $product->update([
            'volet_1_status' => 'green',
            'volet_1_completed' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Step 1 completed successfully',
            'status' => 'green',
            'next_step' => 2,
            'data' => $product,
        ]);
    }

    /**
     * DELETE /api/products/{id}
     * (Optionnel) supprimer un produit
     */
    public function destroy(Request $request, $id)
    {
        $product = Product::where('user_id', $request->user()->id)->findOrFail($id);

        // supprimer image
        if (!empty($product->product_image)) {
            Storage::disk('public')->delete($product->product_image);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
        ]);
    }
}

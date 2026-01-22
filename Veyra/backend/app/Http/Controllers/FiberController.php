<?php

namespace App\Http\Controllers;

use App\Models\Fiber;
use App\Models\Material;
use App\Models\Country;
use App\Models\Product;
use Illuminate\Http\Request;

class FiberController extends Controller
{
    /**
     * GET /api/products/{productId}/fibers
     * Liste des fibres (Volet 3)
     */
    public function index($productId)
    {
        $product = Product::findOrFail($productId);

        // Volet 2 doit être terminé avant Volet 3
        if (!$product->volet_2_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Volet 2 must be completed before accessing Volet 3',
            ], 403);
        }

        $fibers = Fiber::where('product_id', $productId)
            ->with(['fiber', 'originCountry'])
            ->orderBy('id', 'asc')
            ->get();

        $total = (float) $fibers->sum('percentage');

        return response()->json([
            'success' => true,
            'data' => $fibers,
            'total_percentage' => $total,
            'is_complete' => $total == 100.0,
            'status' => $product->volet_3_status,
            'completed' => $product->volet_3_completed,
        ]);
    }

    /**
     * POST /api/products/{productId}/fibers
     * Ajouter une fibre
     */
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->volet_2_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Volet 2 must be completed before adding fibers',
            ], 403);
        }

        $validated = $request->validate([
            'fiber_id' => 'required|exists:materials,id',
            'percentage' => 'required|numeric|min:0|max:100',
            'origin_country_id' => 'required|exists:countries,id',

            'transaction_date' => 'nullable|date',

            'has_certification' => 'nullable|boolean',
            'certificate_number' => 'required_if:has_certification,1|nullable|string|max:255',
            'validity_date' => 'required_if:has_certification,1|nullable|date',
            'transaction_reference' => 'nullable|string|max:255', // non obligatoire

            'has_client_audit' => 'nullable|boolean',
            'audit_comments' => 'required_if:has_client_audit,1|nullable|string',
        ]);

        // total ≤ 100 (sinon bloquer)
        $currentTotal = (float) Fiber::where('product_id', $productId)->sum('percentage');
        $attempt = (float) $validated['percentage'];

        if ($currentTotal + $attempt > 100.0) {
            return response()->json([
                'success' => false,
                'message' => 'Total cannot exceed 100%',
                'current_total' => $currentTotal,
                'attempted_addition' => $attempt,
            ], 422);
        }

        $fiber = Fiber::create([
            'product_id' => $productId,
            'fiber_id' => $validated['fiber_id'],
            'percentage' => $validated['percentage'],
            'origin_country_id' => $validated['origin_country_id'],
            'transaction_date' => $validated['transaction_date'] ?? null,

            'has_certification' => (bool)($validated['has_certification'] ?? false),
            'certificate_number' => ($validated['has_certification'] ?? false) ? ($validated['certificate_number'] ?? null) : null,
            'validity_date' => ($validated['has_certification'] ?? false) ? ($validated['validity_date'] ?? null) : null,
            'transaction_reference' => $validated['transaction_reference'] ?? null,

            'has_client_audit' => (bool)($validated['has_client_audit'] ?? false),
            'audit_comments' => ($validated['has_client_audit'] ?? false) ? ($validated['audit_comments'] ?? null) : null,
        ]);

        // statut volet 3 = orange (en cours) dès qu'on touche au volet
        $product->update([
            'volet_3_status' => 'orange',
            'volet_3_completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fiber added successfully',
            'data' => $fiber->load(['fiber', 'originCountry']),
        ], 201);
    }

    /**
     * PUT /api/products/{productId}/fibers/{fiberId}
     * Modifier une fibre
     */
    public function update(Request $request, $productId, $fiberId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->volet_2_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Volet 2 must be completed before modifying fibers',
            ], 403);
        }

        $fiber = Fiber::where('product_id', $productId)->findOrFail($fiberId);

        $validated = $request->validate([
            'fiber_id' => 'sometimes|exists:materials,id',
            'percentage' => 'sometimes|numeric|min:0|max:100',
            'origin_country_id' => 'sometimes|exists:countries,id',

            'transaction_date' => 'nullable|date',

            'has_certification' => 'nullable|boolean',
            'certificate_number' => 'required_if:has_certification,1|nullable|string|max:255',
            'validity_date' => 'required_if:has_certification,1|nullable|date',
            'transaction_reference' => 'nullable|string|max:255',

            'has_client_audit' => 'nullable|boolean',
            'audit_comments' => 'required_if:has_client_audit,1|nullable|string',
        ]);

        // si on change le pourcentage => vérifier total ≤ 100
        if (array_key_exists('percentage', $validated)) {
            $otherTotal = (float) Fiber::where('product_id', $productId)
                ->where('id', '!=', $fiberId)
                ->sum('percentage');

            $newValue = (float) $validated['percentage'];

            if ($otherTotal + $newValue > 100.0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total cannot exceed 100%',
                    'current_total' => $otherTotal,
                    'attempted_value' => $newValue,
                ], 422);
            }
        }

        // Normalisation: si certification/audit = false => on nettoie les champs liés
        if (array_key_exists('has_certification', $validated) && !$validated['has_certification']) {
            $validated['certificate_number'] = null;
            $validated['validity_date'] = null;
            // transaction_reference reste possible, mais tu peux aussi la null si tu veux:
            // $validated['transaction_reference'] = null;
        }
        if (array_key_exists('has_client_audit', $validated) && !$validated['has_client_audit']) {
            $validated['audit_comments'] = null;
        }

        $fiber->update($validated);

        $product->update([
            'volet_3_status' => 'orange',
            'volet_3_completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fiber updated successfully',
            'data' => $fiber->load(['fiber', 'originCountry']),
        ]);
    }

    /**
     * DELETE /api/products/{productId}/fibers/{fiberId}
     * Supprimer une fibre (min 1 fibre obligatoire)
     */
    public function destroy($productId, $fiberId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->volet_2_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Volet 2 must be completed before deleting fibers',
            ], 403);
        }

        $count = Fiber::where('product_id', $productId)->count();
        if ($count <= 1) {
            return response()->json([
                'success' => false,
                'message' => 'At least one fiber is required',
            ], 422);
        }

        $fiber = Fiber::where('product_id', $productId)->findOrFail($fiberId);
        $fiber->delete();

        $product->update([
            'volet_3_status' => 'orange',
            'volet_3_completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fiber deleted successfully',
        ]);
    }

    /**
     * POST /api/products/{productId}/fibers/save-progress
     * Sauvegarder progression (orange)
     */
    public function saveProgress($productId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->volet_2_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Volet 2 must be completed before saving Volet 3 progress',
            ], 403);
        }

        $fibers = Fiber::where('product_id', $productId)->get();

        // min 1 fibre obligatoire
        if ($fibers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'At least one fiber is required to save progress',
            ], 422);
        }

        $product->update([
            'volet_3_status' => 'orange',
            'volet_3_completed' => false,
        ]);

        $total = (float) $fibers->sum('percentage');

        return response()->json([
            'success' => true,
            'message' => 'Progress saved successfully',
            'status' => 'orange',
            'data' => [
                'total_percentage' => $total,
                'is_complete' => $total == 100.0,
                'fibers_count' => $fibers->count(),
            ],
        ]);
    }

    /**
     * POST /api/products/{productId}/fibers/validate-step
     * Valider Volet 3 (vert) => total = 100% + min 1 fibre
     */
    public function validateStep($productId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->volet_2_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Volet 2 must be completed before completing Volet 3',
            ], 403);
        }

        $fibers = Fiber::where('product_id', $productId)->get();

        if ($fibers->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'At least one fiber is required',
            ], 422);
        }

        $total = (float) $fibers->sum('percentage');
        if ($total != 100.0) {
            return response()->json([
                'success' => false,
                'message' => "Total must be 100% (current: {$total}%)",
            ], 422);
        }

        $product->update([
            'volet_3_status' => 'green',
            'volet_3_completed' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Step 3 completed successfully',
            'status' => 'green',
            'next_step' => 4,
            'data' => $product->load('category', 'subcategory'),
        ]);
    }

    /**
     * GET /api/materials
     * Liste des matériaux (fibres)
     */
    public function getMaterials()
    {
        $materials = Material::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $materials,
        ]);
    }

    /**
     * GET /api/countries
     * Liste des pays
     */
    public function getCountries()
    {
        $countries = Country::orderBy('name')->get();

        return response()->json([
            'success' => true,
            'data' => $countries,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Fabric;
use App\Models\Product;
use Illuminate\Http\Request;

class FabricController extends Controller
{
    /**
     * GET /api/products/{productId}/fabrics
     */
    public function index($productId)
    {
        $product = Product::findOrFail($productId);

        // (optionnel) si tu veux bloquer tant que volet 4 pas terminé:
        // if (!$product->volet_4_completed) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Volet 4 must be completed before accessing Volet 5',
        //     ], 403);
        // }

        $fabrics = Fabric::where('product_id', $productId)
            ->orderBy('id', 'asc')
            ->get();

        $total = (float) $fabrics->sum('percentage');

        return response()->json([
            'success' => true,
            'data' => $fabrics,
            'total_percentage' => $total,
            'is_complete' => abs($total - 100.0) <= 0.01,
            'status' => $product->volet_5_status,
            'completed' => $product->volet_5_completed,
        ]);
    }

    /**
     * POST /api/products/{productId}/fabrics
     */
    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        // (optionnel) blocage volet 4:
        // if (!$product->volet_4_completed) { ... }

        $validated = $request->validate([
'producing_country_id' => 'required|exists:countries,id',
            'fabric_type_id' => 'required|exists:fabric_types,id',

            'percentage' => 'required|numeric|min:0|max:100',
            'production_date' => 'nullable|date',

            'producing_organization' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:30',

            'has_dyeing' => 'nullable|boolean',
            'dyeing_type' => 'nullable|string|max:255',

            'has_finishing' => 'nullable|boolean',
            'finishing_type' => 'nullable|string|max:255',

            'has_certification' => 'nullable|boolean',
            'certificate_number' => 'required_if:has_certification,1|nullable|string|max:255',
            'validity_date' => 'required_if:has_certification,1|nullable|date',
            'transaction_reference' => 'nullable|string|max:255',

            'has_client_audit' => 'nullable|boolean',
            'audit_comments' => 'required_if:has_client_audit,1|nullable|string',

            'renewable_energy_percentage' => 'required|numeric|min:0|max:100',
            'recycled_water_percentage' => 'required|numeric|min:0|max:100',

            'zdhc_supply_to_zero' => 'nullable|boolean',
            'zdhc_get_zd' => 'nullable|boolean',
        ]);

        $currentTotal = (float) Fabric::where('product_id', $productId)->sum('percentage');
        $attempt = (float) $validated['percentage'];

        if ($currentTotal + $attempt > 100.0001) {
            return response()->json([
                'success' => false,
                'message' => 'Total cannot exceed 100%',
                'current_total' => round($currentTotal, 2),
                'attempted_addition' => $attempt,
            ], 422);
        }

        $fabric = Fabric::create([
            'product_id' => $productId,
            ...$validated,
            'has_dyeing' => (bool)($validated['has_dyeing'] ?? false),
            'has_finishing' => (bool)($validated['has_finishing'] ?? false),
            'has_certification' => (bool)($validated['has_certification'] ?? false),
            'has_client_audit' => (bool)($validated['has_client_audit'] ?? false),
            'zdhc_supply_to_zero' => (bool)($validated['zdhc_supply_to_zero'] ?? false),
            'zdhc_get_zd' => (bool)($validated['zdhc_get_zd'] ?? false),
        ]);

        $product->update([
            'volet_5_status' => 'orange',
            'volet_5_completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fabric added successfully',
            'data' => $fabric,
        ], 201);
    }

    /**
     * PUT /api/products/{productId}/fabrics/{fabricId}
     */
    public function update(Request $request, $productId, $fabricId)
    {
        $product = Product::findOrFail($productId);
        $fabric = Fabric::where('product_id', $productId)->findOrFail($fabricId);

        $validated = $request->validate([
'producing_country_id' => 'sometimes|required|exists:countries,id',
            'fabric_type_id' => 'sometimes|exists:fabric_types,id',

            'percentage' => 'sometimes|numeric|min:0|max:100',
            'production_date' => 'nullable|date',

            'producing_organization' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:30',

            'has_dyeing' => 'nullable|boolean',
            'dyeing_type' => 'nullable|string|max:255',

            'has_finishing' => 'nullable|boolean',
            'finishing_type' => 'nullable|string|max:255',

            'has_certification' => 'nullable|boolean',
            'certificate_number' => 'required_if:has_certification,1|nullable|string|max:255',
            'validity_date' => 'required_if:has_certification,1|nullable|date',
            'transaction_reference' => 'nullable|string|max:255',

            'has_client_audit' => 'nullable|boolean',
            'audit_comments' => 'required_if:has_client_audit,1|nullable|string',

            'renewable_energy_percentage' => 'sometimes|numeric|min:0|max:100',
            'recycled_water_percentage' => 'sometimes|numeric|min:0|max:100',

            'zdhc_supply_to_zero' => 'nullable|boolean',
            'zdhc_get_zd' => 'nullable|boolean',
        ]);

        if (array_key_exists('percentage', $validated)) {
            $otherTotal = (float) Fabric::where('product_id', $productId)
                ->where('id', '!=', $fabricId)
                ->sum('percentage');

            $newValue = (float) $validated['percentage'];

            if ($otherTotal + $newValue > 100.0001) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total cannot exceed 100%',
                    'current_total' => round($otherTotal, 2),
                    'new_total' => round($otherTotal + $newValue, 2),
                ], 422);
            }
        }

        // nettoyage si toggles off
        if (array_key_exists('has_certification', $validated) && !$validated['has_certification']) {
            $validated['certificate_number'] = null;
            $validated['validity_date'] = null;
        }
        if (array_key_exists('has_client_audit', $validated) && !$validated['has_client_audit']) {
            $validated['audit_comments'] = null;
        }

        $fabric->update($validated);

        $product->update([
            'volet_5_status' => 'orange',
            'volet_5_completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fabric updated successfully',
            'data' => $fabric,
        ]);
    }

    /**
     * DELETE /api/products/{productId}/fabrics/{fabricId}
     */
    public function destroy($productId, $fabricId)
    {
        $product = Product::findOrFail($productId);

        $fabric = Fabric::where('product_id', $productId)->findOrFail($fabricId);
        $fabric->delete();

        $product->update([
            'volet_5_status' => 'orange',
            'volet_5_completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fabric deleted',
        ]);
    }

    /**
     * POST /api/products/{productId}/fabrics/save-progress
     */
    public function saveProgress($productId)
    {
        $product = Product::findOrFail($productId);

        $fabrics = Fabric::where('product_id', $productId)->get();

        if ($fabrics->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'At least one fabric is required to save progress',
            ], 422);
        }

        $product->update([
            'volet_5_status' => 'orange',
            'volet_5_completed' => false,
        ]);

        $total = (float) $fabrics->sum('percentage');

        return response()->json([
            'success' => true,
            'message' => 'Progress saved successfully',
            'status' => 'orange',
            'data' => [
                'total_percentage' => $total,
                'is_complete' => abs($total - 100.0) <= 0.01,
                'fabrics_count' => $fabrics->count(),
            ],
        ]);
    }

    /**
     * POST /api/products/{productId}/fabrics/validate-step
     */
    public function validateStep($productId)
    {
        $product = Product::findOrFail($productId);

        $fabrics = Fabric::where('product_id', $productId)->get();

        if ($fabrics->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'At least one fabric is required',
            ], 422);
        }

        $total = (float) $fabrics->sum('percentage');

        if (abs($total - 100.0) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => "Total must be 100% (current: " . round($total, 2) . "%)",
            ], 422);
        }

        $product->update([
            'volet_5_status' => 'green',
            'volet_5_completed' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Volet 5 (Fabric) validated',
            'status' => 'green',
            'next_step' => 6,
        ]);
    }
}

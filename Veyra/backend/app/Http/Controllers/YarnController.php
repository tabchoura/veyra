<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Yarn;
use Illuminate\Http\Request;

class YarnController extends Controller
{
    public function index($productId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->volet_3_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Volet 3 must be completed before accessing Volet 4',
            ], 403);
        }

        $yarns = Yarn::where('product_id', $productId)
            ->with('producingCountry')
            ->orderBy('id', 'asc')
            ->get();

        $total = (float) $yarns->sum('percentage');

        return response()->json([
            'success' => true,
            'data' => $yarns,
            'total_percentage' => $total,
            'is_complete' => $total === 100.0,
            'status' => $product->volet_4_status,
            'completed' => $product->volet_4_completed,
        ]);
    }

    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->volet_3_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Volet 3 must be completed first',
            ], 403);
        }

        $validated = $request->validate([
            'producing_country_id' => 'required|exists:countries,id',
            'percentage' => 'required|numeric|min:0|max:100',
            'renewable_energy_percentage' => 'required|numeric|min:0|max:100',
            'recycled_water_percentage' => 'required|numeric|min:0|max:100',

            'producing_organization' => 'nullable|string',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string',
            'yarn_type' => 'nullable|string',
            'production_date' => 'nullable|date',

            'has_certification' => 'nullable|boolean',
            'certificate_number' => 'required_if:has_certification,1|nullable|string',
            'validity_date' => 'required_if:has_certification,1|nullable|date',

            'has_client_audit' => 'nullable|boolean',
            'audit_comments' => 'required_if:has_client_audit,1|nullable|string',

            'yarn_type_id' => 'nullable|exists:yarn_types,id',
        ]);

        $currentTotal = (float) Yarn::where('product_id', $productId)->sum('percentage');
        if ($currentTotal + (float)$validated['percentage'] > 100.0) {
            return response()->json([
                'success' => false,
                'message' => 'Total percentage cannot exceed 100%',
                'current_total' => $currentTotal,
                'attempted_addition' => (float)$validated['percentage'],
            ], 422);
        }

        // Normalisation (comme tes autres volets)
        if (!($validated['has_certification'] ?? false)) {
            $validated['certificate_number'] = null;
            $validated['validity_date'] = null;
        }
        if (!($validated['has_client_audit'] ?? false)) {
            $validated['audit_comments'] = null;
        }

        $yarn = Yarn::create(array_merge($validated, [
            'product_id' => $productId,
            'yarn_type_id' => $validated['yarn_type_id'] ?? null,
        ]));

        $product->update([
            'volet_4_status' => 'orange',
            'volet_4_completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Yarn added successfully',
            'data' => $yarn->load('producingCountry'),
        ], 201);
    }

    /**
     * POST /api/products/{productId}/yarns/save-progress
     * Sauvegarder progression (orange)
     */
    public function saveProgress($productId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->volet_3_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Volet 3 must be completed before saving Volet 4 progress',
            ], 403);
        }

        $yarns = Yarn::where('product_id', $productId)->get();

        // min 1 yarn obligatoire (comme fibers)
        if ($yarns->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'At least one yarn is required to save progress',
            ], 422);
        }

        $product->update([
            'volet_4_status' => 'orange',
            'volet_4_completed' => false,
        ]);

        $total = (float) $yarns->sum('percentage');

        return response()->json([
            'success' => true,
            'message' => 'Progress saved successfully',
            'status' => 'orange',
            'data' => [
                'total_percentage' => $total,
                'is_complete' => $total == 100.0,
                'yarns_count' => $yarns->count(),
            ],
        ]);
    }

    public function validateStep($productId)
    {
        $product = Product::findOrFail($productId);

        if (!$product->volet_3_completed) {
            return response()->json([
                'success' => false,
                'message' => 'Volet 3 must be completed before completing Volet 4',
            ], 403);
        }

        $yarns = Yarn::where('product_id', $productId)->get();
        if ($yarns->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'At least one yarn is required',
            ], 422);
        }

        $total = (float) $yarns->sum('percentage');

        if ($total != 100.0) {
            return response()->json([
                'success' => false,
                'message' => "Total must be 100% (current: {$total}%)",
            ], 422);
        }

        $product->update([
            'volet_4_status' => 'green',
            'volet_4_completed' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Volet 4 completed successfully',
            'next_step' => 5,
        ]);
    }
}

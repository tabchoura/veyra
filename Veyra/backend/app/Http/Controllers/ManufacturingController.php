<?php

namespace App\Http\Controllers;

use App\Models\Manufacturing;
use App\Models\Product;
use Illuminate\Http\Request;

class ManufacturingController extends Controller
{
    public function index($productId)
    {
        $product = Product::findOrFail($productId);

        // (optionnel) protection si tu veux forcer l'ordre des volets
        // if (!$product->volet_5_completed) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Volet 5 must be completed before accessing Volet 6',
        //     ], 403);
        // }

        $items = Manufacturing::where('product_id', $productId)
            ->with(['finishingMethod', 'colouringMethod', 'finishTreatment'])
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items,
            'status' => $product->step_6_status ?? null,
            'completed' => $product->step_6_completed ?? false,
        ]);
    }

    public function store(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        // (optionnel) protection si tu veux forcer l'ordre des volets
        // if (!$product->volet_5_completed) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Volet 5 must be completed before saving Volet 6',
        //     ], 403);
        // }

        $validated = $request->validate([
            'producing_country_id' => 'required|exists:countries,id',

            'producing_organization' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:50',
            'production_date' => 'nullable|date',

            'has_certification' => 'required|boolean',
            'certificate_number' => 'nullable|string|max:255|required_if:has_certification,1',
            'validity_date' => 'nullable|date|required_if:has_certification,1',
            'transaction_reference' => 'nullable|string|max:255',

            'has_client_audit' => 'required|boolean',
            'audit_comments' => 'nullable|string|required_if:has_client_audit,1',

            'special_effects' => 'required|boolean',

            // ✅ FK dropdowns
            'finishing_method_id' => 'required|exists:finishing_methods,id',
            'colouring_method_id' => 'required|exists:colouring_methods,id',
            'finish_treatment_id' => 'required|exists:finish_treatments,id',

            'comments' => 'nullable|string',

            'renewable_energy_percentage' => 'required|numeric|min:0|max:100',
            'recycled_water_percentage' => 'required|numeric|min:0|max:100',

            'zdhc_supply_to_zero' => 'required|boolean',
            'zdhc_get_zd' => 'required|boolean',
        ]);

        // Normalisation (comme Yarn)
        if (!$validated['has_certification']) {
            $validated['certificate_number'] = null;
            $validated['validity_date'] = null;
        }
        if (!$validated['has_client_audit']) {
            $validated['audit_comments'] = null;
        }

        $manufacturing = Manufacturing::create([
            'product_id' => $productId,
            ...$validated,
        ]);

        // statut orange dès qu'on ajoute/modifie
        $product->update([
            'step_6_status' => 'orange',
            'step_6_completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Manufacturing added successfully',
            'data' => $manufacturing->load(['finishingMethod', 'colouringMethod', 'finishTreatment']),
        ], 201);
    }

    public function update(Request $request, $productId, $manufacturingId)
    {
        $product = Product::findOrFail($productId);

        $manufacturing = Manufacturing::where('product_id', $productId)
            ->findOrFail($manufacturingId);

        $validated = $request->validate([
            'producing_country_id' => 'sometimes|exists:countries,id',

            'producing_organization' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:50',
            'production_date' => 'nullable|date',

            'has_certification' => 'sometimes|boolean',
            'certificate_number' => 'nullable|string|max:255',
            'validity_date' => 'nullable|date',
            'transaction_reference' => 'nullable|string|max:255',

            'has_client_audit' => 'sometimes|boolean',
            'audit_comments' => 'nullable|string',

            'special_effects' => 'sometimes|boolean',

            // ✅ FK dropdowns
            'finishing_method_id' => 'sometimes|exists:finishing_methods,id',
            'colouring_method_id' => 'sometimes|exists:colouring_methods,id',
            'finish_treatment_id' => 'sometimes|exists:finish_treatments,id',

            'comments' => 'nullable|string',

            'renewable_energy_percentage' => 'nullable|numeric|min:0|max:100',
            'recycled_water_percentage' => 'nullable|numeric|min:0|max:100',

            'zdhc_supply_to_zero' => 'sometimes|boolean',
            'zdhc_get_zd' => 'sometimes|boolean',
        ]);

        // Normalisation en tenant compte de l'existant (si champs non fournis)
        $hasCert = array_key_exists('has_certification', $validated)
            ? (bool)$validated['has_certification']
            : (bool)$manufacturing->has_certification;

        $hasAudit = array_key_exists('has_client_audit', $validated)
            ? (bool)$validated['has_client_audit']
            : (bool)$manufacturing->has_client_audit;

        if (!$hasCert) {
            $validated['certificate_number'] = null;
            $validated['validity_date'] = null;
        } else {
            // si il active certification et n'envoie pas les champs -> on force required côté back
            // (sinon tu risques d'avoir certif=true avec champs vides)
            if (array_key_exists('has_certification', $validated) && $validated['has_certification']) {
                if (!$request->filled('certificate_number') && !$manufacturing->certificate_number) {
                    return response()->json([
                        'success' => false,
                        'message' => 'certificate_number is required when has_certification is true'
                    ], 422);
                }
                if (!$request->filled('validity_date') && !$manufacturing->validity_date) {
                    return response()->json([
                        'success' => false,
                        'message' => 'validity_date is required when has_certification is true'
                    ], 422);
                }
            }
        }

        if (!$hasAudit) {
            $validated['audit_comments'] = null;
        } else {
            if (array_key_exists('has_client_audit', $validated) && $validated['has_client_audit']) {
                if (!$request->filled('audit_comments') && !$manufacturing->audit_comments) {
                    return response()->json([
                        'success' => false,
                        'message' => 'audit_comments is required when has_client_audit is true'
                    ], 422);
                }
            }
        }

        $manufacturing->update($validated);

        $product->update([
            'step_6_status' => 'orange',
            'step_6_completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Manufacturing updated successfully',
            'data' => $manufacturing->load(['finishingMethod', 'colouringMethod', 'finishTreatment']),
        ]);
    }

    public function destroy($productId, $manufacturingId)
    {
        $product = Product::findOrFail($productId);

        $manufacturing = Manufacturing::where('product_id', $productId)
            ->findOrFail($manufacturingId);

        $manufacturing->delete();

        $product->update([
            'step_6_status' => 'orange',
            'step_6_completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Manufacturing record deleted',
        ]);
    }

    /**
     * POST /api/products/{productId}/manufacturings/save-progress
     */
    public function saveProgress($productId)
    {
        $product = Product::findOrFail($productId);

        $count = Manufacturing::where('product_id', $productId)->count();
        if ($count < 1) {
            return response()->json([
                'success' => false,
                'message' => 'At least 1 manufacturing entry is required to save progress'
            ], 422);
        }

        $product->update([
            'step_6_status' => 'orange',
            'step_6_completed' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Step 6 progress saved',
            'status' => 'orange',
            'count' => $count,
        ]);
    }

    /**
     * POST /api/products/{productId}/manufacturings/validate-step
     */
    public function validateStep($productId)
    {
        $product = Product::findOrFail($productId);

        $count = Manufacturing::where('product_id', $productId)->count();
        if ($count < 1) {
            return response()->json([
                'success' => false,
                'message' => 'At least 1 manufacturing entry is required'
            ], 422);
        }

        $product->update([
            'step_6_status' => 'green',
            'step_6_completed' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Step 6 (Manufacturing) validated',
            'next_step' => 7,
        ]);
    }
}

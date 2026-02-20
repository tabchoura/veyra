<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductUsage;
use Illuminate\Http\Request;

class UsageController extends Controller
{
    // GET /products/{productId}/usage
    public function show($productId)
    {
        $usage = ProductUsage::where('product_id', $productId)->first();

        return response()->json([
            'success' => true,
            'data' => $usage
        ]);
    }

    // POST /products/{productId}/usage  (UPSERT: create or update)
    public function upsert(Request $request, $productId)
    {
        $data = $request->validate([
            'brand' => 'nullable|string|max:255',

            // Required
            'delivery_country_id' => 'required|exists:countries,id',
            'delivery_date' => 'required|date',

            // Care (optional but present)
            'washing_temperature' => 'nullable|integer|min:0|max:120',
            'hand_wash' => 'nullable|boolean',
            'machine_wash' => 'nullable|boolean',
            'dry_clean' => 'nullable|boolean',
            'bleach' => 'nullable|boolean',
            'dry_shade' => 'nullable|boolean',
            'tumble_dry' => 'nullable|boolean',
            'ironing' => 'nullable|boolean',

            // Repairable
            'is_repairable' => 'required|boolean',
            'repair_comment' => 'nullable|string',
        ]);

        // Conditional rule: if repairable => comment required
        if (($data['is_repairable'] ?? false) && empty($data['repair_comment'])) {
            return response()->json([
                'success' => false,
                'message' => 'repair_comment is required when is_repairable is true'
            ], 422);
        }

        // normalize missing booleans to false (frontend may not send unchecked)
        $data = array_merge([
            'hand_wash' => false,
            'machine_wash' => false,
            'dry_clean' => false,
            'bleach' => false,
            'dry_shade' => false,
            'tumble_dry' => false,
            'ironing' => false,
        ], $data);

        $usage = ProductUsage::updateOrCreate(
            ['product_id' => $productId],
            $data
        );

        // progress => orange
        Product::where('id', $productId)->update([
            'volet_8_status' => 'orange',
            'volet_8_completed' => false
        ]);

        return response()->json([
            'success' => true,
            'data' => $usage
        ]);
    }

    // POST /products/{productId}/usage/save-progress
    public function saveProgress($productId)
    {
        Product::where('id', $productId)->update([
            'volet_8_status' => 'orange'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Volet 8 progress saved'
        ]);
    }

    // POST /products/{productId}/usage/validate-step
    public function validateStep($productId)
    {
        $usage = ProductUsage::where('product_id', $productId)->first();

        if (!$usage) {
            return response()->json([
                'success' => false,
                'message' => 'Usage is required (delivery country/date)'
            ], 400);
        }

        // required fields check
        if (!$usage->delivery_country_id || !$usage->delivery_date) {
            return response()->json([
                'success' => false,
                'message' => 'Delivery country and delivery date are required'
            ], 400);
        }

        // conditional check
        if ($usage->is_repairable && !$usage->repair_comment) {
            return response()->json([
                'success' => false,
                'message' => 'repair_comment is required when is_repairable is true'
            ], 400);
        }

        Product::where('id', $productId)->update([
            'volet_8_status' => 'green',
            'volet_8_completed' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Volet 8 (Usage) validated'
        ]);
    }
}

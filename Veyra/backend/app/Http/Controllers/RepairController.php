<?php

namespace App\Http\Controllers;

use App\Models\ProductUsage;
use App\Models\ProductRepair;
use App\Models\Product;
use App\Models\RepairAction;
use Illuminate\Http\Request;

class RepairController extends Controller
{
    // GET /products/{productId}/usage/repairs
    public function index($productId)
    {
        $usage = ProductUsage::where('product_id', $productId)->first();

        if (!$usage) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        $repairs = ProductRepair::where('product_usage_id', $usage->id)
            ->orderBy('repair_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $repairs
        ]);
    }

    // POST /products/{productId}/usage/repairs
    public function store(Request $request, $productId)
    {
        $usage = ProductUsage::firstOrCreate(
            ['product_id' => $productId],
            [
                // minimal defaults (you can keep null until user saves usage properly)
                'delivery_country_id' => 1, // ⚠️ replace later; better create usage first in UI
                'delivery_date' => now()->toDateString(),
                'is_repairable' => false,
            ]
        );

        $data = $request->validate([
            'repair_date' => 'required|date',
            'repair_action_id' => 'required|exists:repair_actions,id',
            'country_id' => 'required|exists:countries,id',
            'organization' => 'nullable|string|max:255',
            'other_text' => 'nullable|string|max:255',
        ]);

        // If action == "Other" => other_text recommended (CDC says not mandatory, so we don't force)
        $action = RepairAction::find($data['repair_action_id']);
        if ($action && strtolower(trim($action->name)) === 'other' && empty($data['other_text'])) {
            // Not mandatory per CDC; keep as warning-like message if you want
        }

        $repair = ProductRepair::create([
            'product_usage_id' => $usage->id,
            ...$data
        ]);

        Product::where('id', $productId)->update([
            'volet_8_status' => 'orange',
            'volet_8_completed' => false
        ]);

        return response()->json([
            'success' => true,
            'data' => $repair
        ], 201);
    }

    // PUT /products/{productId}/usage/repairs/{repairId}
    public function update(Request $request, $productId, $repairId)
    {
        $usage = ProductUsage::where('product_id', $productId)->firstOrFail();

        $repair = ProductRepair::where('product_usage_id', $usage->id)->findOrFail($repairId);

        $data = $request->validate([
            'repair_date' => 'sometimes|date',
            'repair_action_id' => 'sometimes|exists:repair_actions,id',
            'country_id' => 'sometimes|exists:countries,id',
            'organization' => 'nullable|string|max:255',
            'other_text' => 'nullable|string|max:255',
        ]);

        $repair->update($data);

        Product::where('id', $productId)->update([
            'volet_8_status' => 'orange',
            'volet_8_completed' => false
        ]);

        return response()->json([
            'success' => true,
            'data' => $repair->fresh()
        ]);
    }

    // DELETE /products/{productId}/usage/repairs/{repairId}
    public function destroy($productId, $repairId)
    {
        $usage = ProductUsage::where('product_id', $productId)->firstOrFail();

        $repair = ProductRepair::where('product_usage_id', $usage->id)->findOrFail($repairId);
        $repair->delete();

        Product::where('id', $productId)->update([
            'volet_8_status' => 'orange',
            'volet_8_completed' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Repair deleted'
        ]);
    }
}

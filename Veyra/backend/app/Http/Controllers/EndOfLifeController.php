<?php

namespace App\Http\Controllers;

use App\Models\EndOfLife;
use App\Models\Product;
use Illuminate\Http\Request;

class EndOfLifeController extends Controller
{
    // GET /products/{productId}/end-of-life
    public function show($productId)
    {
        $eol = EndOfLife::where('product_id', $productId)->first();

        return response()->json([
            'success' => true,
            'data' => $eol
        ]);
    }

    // POST /products/{productId}/end-of-life  (UPSERT)
    public function storeOrUpdate(Request $request, $productId)
    {
        $data = $request->validate([
            // required (CDC)
            'is_recoverable' => 'required|boolean',
            'comment' => 'nullable|string',

            'end_of_life_date' => 'required|date',
            'end_of_life_country_id' => 'required|exists:countries,id',

            // options (CDC)
            'reuse' => 'required|boolean',
            'recycling' => 'required|boolean',
            'incineration' => 'required|boolean',
            'composting' => 'required|boolean',
            'landfill' => 'required|boolean',

            // recycling block
            'recycling_country_id' => 'nullable|exists:countries,id|required_if:recycling,1',
            'recycling_method' => 'nullable|string|max:255|required_if:recycling,1',
            'recycling_valued_product' => 'nullable|string|max:255',
            'recycling_organization' => 'nullable|string|max:255',

            // incineration block
            'incineration_country_id' => 'nullable|exists:countries,id|required_if:incineration,1',
            'incineration_method' => 'nullable|string|max:255|required_if:incineration,1',
            'incineration_valued_product' => 'nullable|string|max:255',
            'incineration_organization' => 'nullable|string|max:255',

            // composting block
            'composting_country_id' => 'nullable|exists:countries,id|required_if:composting,1',
            'composting_method' => 'nullable|string|max:255|required_if:composting,1',
            'composting_valued_product' => 'nullable|string|max:255',
            'composting_organization' => 'nullable|string|max:255',

            // landfill block
            'landfill_country_id' => 'nullable|exists:countries,id|required_if:landfill,1',
            'landfill_method' => 'nullable|string|max:255|required_if:landfill,1',
            'landfill_valued_product' => 'nullable|string|max:255',
            'landfill_organization' => 'nullable|string|max:255',
        ]);

        // Create or Update
        $eol = EndOfLife::updateOrCreate(
            ['product_id' => $productId],
            $data
        );

        // Save progress => orange
        Product::where('id', $productId)->update([
            'volet_9_status' => 'orange',
            'volet_9_completed' => false
        ]);

        return response()->json([
            'success' => true,
            'data' => $eol
        ]);
    }

    // POST /products/{productId}/end-of-life/validate-step
    public function validateStep($productId)
    {
        $eol = EndOfLife::where('product_id', $productId)->first();

        if (!$eol) {
            return response()->json([
                'success' => false,
                'message' => 'Volet 9 data is required before validation'
            ], 400);
        }

        // Base required checks
        if ($eol->is_recoverable === null) {
            return response()->json(['success' => false, 'message' => 'is_recoverable is required'], 400);
        }

        if (!$eol->end_of_life_date || !$eol->end_of_life_country_id) {
            return response()->json([
                'success' => false,
                'message' => 'end_of_life_date and end_of_life_country_id are required'
            ], 400);
        }

        // Conditional checks helper
        $checkBlock = function (bool $enabled, $countryId, $method, string $label) {
            if (!$enabled) return null;

            if (!$countryId) return "$label: country is required";
            if (!$method) return "$label: method is required";

            return null;
        };

        // Recycling
        if ($msg = $checkBlock((bool)$eol->recycling, $eol->recycling_country_id, $eol->recycling_method, 'Recycling')) {
            return response()->json(['success' => false, 'message' => $msg], 400);
        }

        // Incineration
        if ($msg = $checkBlock((bool)$eol->incineration, $eol->incineration_country_id, $eol->incineration_method, 'Incineration')) {
            return response()->json(['success' => false, 'message' => $msg], 400);
        }

        // Composting
        if ($msg = $checkBlock((bool)$eol->composting, $eol->composting_country_id, $eol->composting_method, 'Composting')) {
            return response()->json(['success' => false, 'message' => $msg], 400);
        }

        // Landfill
        if ($msg = $checkBlock((bool)$eol->landfill, $eol->landfill_country_id, $eol->landfill_method, 'Landfill')) {
            return response()->json(['success' => false, 'message' => $msg], 400);
        }

        // Valid => green
        Product::where('id', $productId)->update([
            'volet_9_status' => 'green',
            'volet_9_completed' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Volet 9 (End of Life) validated'
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Accessory;
use App\Models\AccessoryType;
use App\Models\Product;
use Illuminate\Http\Request;

class AccessoryController extends Controller
{
    // GET /products/{productId}/accessories
    public function index($productId)
    {
        return response()->json([
            'success' => true,
            'data' => Accessory::where('product_id', $productId)->get()
        ]);
    }

    // POST /products/{productId}/accessories
    public function store(Request $request, $productId)
    {
        $data = $this->validateAccessory($request);

        // Rule: if type is "Other" => accessory_type_other required
        $this->validateOtherRule($data);

        $accessory = Accessory::create([
            'product_id' => $productId,
            ...$data,
        ]);

        // Save progress => orange
        Product::where('id', $productId)->update([
            'volet_7_status' => 'orange',
            'volet_7_completed' => false
        ]);

        return response()->json([
            'success' => true,
            'data' => $accessory
        ], 201);
    }

    // PUT /products/{productId}/accessories/{accessoryId}
    public function update(Request $request, $productId, $accessoryId)
    {
        $accessory = Accessory::where('product_id', $productId)->findOrFail($accessoryId);

        // partial update
        $data = $this->validateAccessory($request, true);

        // merge to evaluate "Other" rule properly
        $merged = array_merge($accessory->toArray(), $data);
        $this->validateOtherRule($merged);

        $accessory->update($data);

        // If it was validated before, go back to orange
        Product::where('id', $productId)->update([
            'volet_7_status' => 'orange',
            'volet_7_completed' => false
        ]);

        return response()->json([
            'success' => true,
            'data' => $accessory->fresh()
        ]);
    }

    // DELETE /products/{productId}/accessories/{accessoryId}
    public function destroy($productId, $accessoryId)
    {
        $accessory = Accessory::where('product_id', $productId)->findOrFail($accessoryId);
        $accessory->delete();

        // Usually goes back to orange (because requirements may no longer hold)
        Product::where('id', $productId)->update([
            'volet_7_status' => 'orange',
            'volet_7_completed' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Accessory deleted'
        ]);
    }

    // POST /products/{productId}/accessories/save-progress
    public function saveProgress($productId)
    {
        Product::where('id', $productId)->update([
            'volet_7_status' => 'orange'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Volet 7 progress saved'
        ]);
    }

    // POST /products/{productId}/accessories/validate-step
    public function validateStep($productId)
    {
        $count = Accessory::where('product_id', $productId)->count();

        if ($count < 1) {
            return response()->json([
                'success' => false,
                'message' => 'At least 1 accessory is required'
            ], 400);
        }

        // You can also enforce that each accessory respects conditional rules:
        $invalid = Accessory::where('product_id', $productId)
            ->get()
            ->first(function ($a) {
                // certif required fields if has_certification
                if ($a->has_certification) {
                    if (!$a->certificate_number || !$a->validity_date) {
                        return true;
                    }
                }
                // audit comment required if has_client_audit
                if ($a->has_client_audit && !$a->audit_comments) {
                    return true;
                }
                // energy/water basic sanity
                if ($a->renewable_energy_percentage < 0 || $a->renewable_energy_percentage > 100) return true;
                if ($a->recycled_water_percentage < 0 || $a->recycled_water_percentage > 100) return true;

                // Other rule (type name == Other)
                // We need to check type name from accessory_types:
                $type = AccessoryType::find($a->accessory_type_id);
                if ($type && strtolower(trim($type->name)) === 'other') {
                    if (!$a->accessory_type_other) return true;
                }
                return false;
            });

        if ($invalid) {
            return response()->json([
                'success' => false,
                'message' => 'Some accessories have incomplete conditional fields (certification/audit/other).'
            ], 400);
        }

        Product::where('id', $productId)->update([
            'volet_7_status' => 'green',
            'volet_7_completed' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Volet 7 (Accessories) validated'
        ]);
    }

    /**
     * Shared validation rules
     * $partial = true for PATCH/PUT partial updates
     */
    private function validateAccessory(Request $request, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';

        return $request->validate([
            'producing_country_id' => [$required, 'exists:countries,id'],
            'accessory_type_id' => [$required, 'exists:accessory_types,id'],
            'weight' => [$required, 'numeric', 'min:0'],

            'producing_organization' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'production_date' => ['nullable', 'date'],

            // "Other" will be checked in validateOtherRule()
            'accessory_type_other' => ['nullable', 'string', 'max:255'],

            // Conditional blocks
            'has_certification' => [$required, 'boolean'],
            'certificate_number' => ['nullable', 'string', 'max:255', 'required_if:has_certification,1'],
            'validity_date' => ['nullable', 'date', 'required_if:has_certification,1'],
            'transaction_reference' => ['nullable', 'string', 'max:255'],

            'has_client_audit' => [$required, 'boolean'],
            'audit_comments' => ['nullable', 'string', 'required_if:has_client_audit,1'],

            // Environment
            'renewable_energy_percentage' => [$required, 'numeric', 'min:0', 'max:100'],
            'recycled_water_percentage' => [$required, 'numeric', 'min:0', 'max:100'],
        ]);
    }

    private function validateOtherRule(array $data): void
    {
        if (!isset($data['accessory_type_id'])) return;

        $type = AccessoryType::find($data['accessory_type_id']);
        if (!$type) return;

        if (strtolower(trim($type->name)) === 'other') {
            if (empty($data['accessory_type_other'])) {
                abort(response()->json([
                    'success' => false,
                    'message' => 'accessory_type_other is required when accessory type is Other'
                ], 422));
            }
        }
    }
}

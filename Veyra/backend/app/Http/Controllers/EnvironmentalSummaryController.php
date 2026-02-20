<?php

namespace App\Http\Controllers;

use App\Models\Passport;
use Illuminate\Http\Request;

class EnvironmentalSummaryController extends Controller
{
    private function getPassport(int $productId, int $userId): Passport
    {
        return Passport::firstOrCreate(
            ['product_id' => $productId],
            [
                'created_by' => $userId,
                'with_qr' => false,
                'status' => 'draft'
            ]
        );
    }

    // ==============================
    // GET SUMMARY
    // ==============================
    public function show(Request $request, $productId)
    {
        $passport = $this->getPassport($productId, $request->user()->id);

        return response()->json([
            'data' => $passport->environmental_summary ?? []
        ]);
    }

    // ==============================
    // SAVE PROGRESS
    // ==============================
    public function saveProgress(Request $request, $productId)
    {
        $request->validate([
            'bawearScore' => 'nullable|numeric|min:0|max:100',
            'bawearProvided' => 'boolean',
            'avgRenewableEnergyPct' => 'nullable|numeric|min:0|max:100',
            'avgRecycledWaterPct' => 'nullable|numeric|min:0|max:100',
            'certifications' => 'nullable|array',
        ]);

        $passport = $this->getPassport($productId, $request->user()->id);

        $env = $passport->environmental_summary ?? [];

        $env['bawearScore'] = $request->bawearScore;
        $env['bawearProvided'] = $request->boolean('bawearProvided');
        $env['avgRenewableEnergyPct'] = $request->avgRenewableEnergyPct ?? 0;
        $env['avgRecycledWaterPct'] = $request->avgRecycledWaterPct ?? 0;
        $env['certifications'] = $request->certifications ?? [];
        $env['updated_at'] = now();

        $passport->environmental_summary = $env;
        $passport->status = 'draft';
        $passport->save();

        return response()->json([
            'message' => 'Environmental summary saved ✅',
            'data' => $passport->environmental_summary
        ]);
    }

    // ==============================
    // GENERATE PASSPORT
    // ==============================
    public function generate(Request $request, $productId)
    {
        $passport = $this->getPassport($productId, $request->user()->id);

        $snapshot = [
            'product_id' => $productId,
            'generated_at' => now(),
            'environmental_summary' => $passport->environmental_summary,
        ];

        $passport->is_generated = true;
        $passport->generated_at = now();
        $passport->payload_snapshot = $snapshot;
        $passport->status = 'final';
        $passport->save();

        return response()->json([
            'message' => 'Passport generated successfully 🎉',
            'data' => [
                'generated_at' => $passport->generated_at
            ]
        ]);
    }
}

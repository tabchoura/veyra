<?php

namespace App\Http\Controllers;

use App\Models\Passport;
use Illuminate\Http\Request;

class PassportProgressController extends Controller
{
    public function save(Request $request, $productId)
    {
        $request->validate([
            'step' => 'required|integer|min:1|max:13',
        ]);

        $passport = Passport::firstOrCreate(
            ['product_id' => $productId],
            [
                'created_by' => $request->user()->id,
                'with_qr' => false,
                'status' => 'draft',
                'total_steps' => 13,
                'completed_steps' => 0,
            ]
        );

        // ✅ important : ne jamais diminuer le progress
        $passport->completed_steps = max((int) $passport->completed_steps, (int) $request->step);
        $passport->status = 'draft';
        $passport->save();

        return response()->json([
            'message' => 'Progress saved',
            'completed_steps' => $passport->completed_steps,
            'total_steps' => $passport->total_steps,
        ]);
    }
}

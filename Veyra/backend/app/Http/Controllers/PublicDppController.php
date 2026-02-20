<?php

namespace App\Http\Controllers;

use App\Models\Passport;
use Illuminate\Http\Request;

class PublicDppController extends Controller
{
    public function show(Request $request, string $token)
    {
        $passport = Passport::where('public_token', $token)->firstOrFail();

        if ($passport->status !== 'published') {
            return response()->json(['message' => 'Not published'], 404);
        }

        // public uniquement (version simple)
        if ($passport->access_level !== 'public') {
            return response()->json(['message' => 'Access denied'], 403);
        }

        return response()->json([
            'data' => [
                'productId' => $passport->product_id,
                'publishedAt' => $passport->published_at,
                // plus tard: renvoyer les volets/sections du passeport
            ]
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Passport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PassportGenerationController extends Controller
{
    private function getPassport(int $productId, int $userId): Passport
    {
        return Passport::firstOrCreate(
            ['product_id' => $productId],
            [
                'created_by' => $userId,
                'with_qr' => false,
                'status' => 'draft',
                'access_level' => 'internal',
            ]
        );
    }

    public function show(Request $request, int $productId)
    {
        $passport = $this->getPassport($productId, $request->user()->id);

        return response()->json([
            'data' => [
                'accessLevel' => $passport->access_level,
                'withQr' => (bool) $passport->with_qr,
                'authorizedEmails' => $passport->partner_emails ?? [],
                'authorizedUserIds' => [],
                'authorizedPartnerIds' => [],
                // ✅ CORRECTION
                'isGenerated' => (bool) $passport->is_generated,
                'generatedAt' => $passport->published_at ?? $passport->generated_at,
                'publicUrl' => $passport->public_token
                    ? url("/api/public/dpp/{$passport->public_token}")
                    : null,
            ]
        ]);
    }

    public function saveProgress(Request $request, int $productId)
    {
        $passport = $this->getPassport($productId, $request->user()->id);

        $data = $request->validate([
            'accessLevel' => 'required|in:internal,partner,public',
            'withQr' => 'required|boolean',
            'authorizedEmails' => 'nullable|array',
            'authorizedEmails.*' => 'email', // ✅ évite dns/rfc si ça te fait bloquer localement
            'authorizedUserIds' => 'nullable|array',
            'authorizedPartnerIds' => 'nullable|array',
        ]);

        if ($data['accessLevel'] === 'partner') {
            $emails = array_values(array_unique(array_filter(array_map('trim', $data['authorizedEmails'] ?? []))));
            if (count($emails) < 1) {
                return response()->json([
                    'message' => 'Partner Access requires at least one partner email.'
                ], 422);
            }
            $passport->partner_emails = $emails;
        } else {
            $passport->partner_emails = [];
        }

        $passport->access_level = $data['accessLevel'];
        $passport->with_qr = (bool) $data['withQr'];

        // ✅ garder enum draft/final (pas published)
        $passport->status = 'draft';

        $passport->save();

        return response()->json([
            'message' => 'Saved',
            'data' => [
                'accessLevel' => $passport->access_level,
                'withQr' => (bool) $passport->with_qr,
                'authorizedEmails' => $passport->partner_emails ?? [],
                'isGenerated' => (bool) $passport->is_generated,
                'generatedAt' => $passport->published_at ?? $passport->generated_at,
                'publicUrl' => $passport->public_token
                    ? url("/api/public/dpp/{$passport->public_token}")
                    : null,
            ],
        ]);
    }

    public function publish(Request $request, int $productId)
    {
        $passport = $this->getPassport($productId, $request->user()->id);

        // règles avant publish
        if ($passport->access_level === 'partner'
            && (empty($passport->partner_emails) || count($passport->partner_emails) < 1)
        ) {
            return response()->json([
                'message' => 'Partner Access requires at least one partner email.'
            ], 422);
        }

        // token public
        if (!$passport->public_token) {
            $passport->public_token = Str::random(48);
        }

        // ✅ IMPORTANT: status enum draft/final فقط
        $passport->status = 'final';

        // publish info
        $passport->is_generated = true;
        $passport->generated_at = $passport->generated_at ?? now();
        $passport->published_at = now();

        $passport->save();

        return response()->json([
            'message' => 'Passport published',
            'data' => [
                'publicUrl' => url("/api/public/dpp/{$passport->public_token}"),
                'generatedAt' => $passport->published_at,
                'withQr' => (bool) $passport->with_qr,
            ],
        ]);
    }
}

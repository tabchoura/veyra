<?php

namespace App\Http\Controllers;

use App\Http\Requests\PassportUpsertRequest;
use App\Models\Passport;
use Illuminate\Support\Facades\DB;

class PassportController extends Controller
{
    /**
     * GET /products/{productId}/passport
     * Retourne le passeport + accès (users/emails/partners)
     */
    public function show($productId)
    {
        $passport = Passport::with([
            'authorizedUsers:id,name,email',
            'authorizedEmails:id,passport_id,email',
            'authorizedPartners:id,name'
        ])->where('product_id', $productId)->first();

        return response()->json([
            'passport' => $passport
        ]);
    }

    /**
     * POST /products/{productId}/passport/save-progress
     * Sauvegarde partielle (status=draft) + menu orange (à brancher sur ton système)
     */
    public function saveProgress(PassportUpsertRequest $request, $productId)
    {
        return DB::transaction(function () use ($request, $productId) {

            $passport = Passport::firstOrCreate(
                ['product_id' => $productId],
                ['created_by' => $request->user()->id]
            );

            $passport->update([
                'with_qr' => $request->boolean('with_qr'),
                'status'  => 'draft',
            ]);

            // 1) Utilisateurs autorisés (multi select)
            $passport->authorizedUsers()->sync($request->input('user_ids', []));

            // 2) Emails autorisés
            $emails = collect($request->input('emails', []))
                ->map(fn ($e) => mb_strtolower(trim($e)))
                ->filter()
                ->unique()
                ->values();

            $passport->authorizedEmails()->delete();
            foreach ($emails as $email) {
                $passport->authorizedEmails()->create(['email' => $email]);
            }

            // 3) Partenaires autorisés (si access_level = partner)
            $accessLevel = $request->input('access_level'); // internal|partner|public
            if ($accessLevel === 'partner') {
                $passport->authorizedPartners()->sync($request->input('partner_ids', []));
            } else {
                $passport->authorizedPartners()->sync([]);
            }

            // ✅ TODO: Brancher ta progression ici (menu orange volet 13)
            // Exemple générique:
            // ProductProgress::updateOrCreate(['product_id' => $productId], ['volet13_status' => 'orange']);

            return response()->json([
                'message' => 'Passport saved (draft)',
                'passport' => $passport->load([
                    'authorizedUsers:id,name,email',
                    'authorizedEmails:id,passport_id,email',
                    'authorizedPartners:id,name'
                ])
            ]);
        });
    }

    /**
     * POST /products/{productId}/passport/validate-step
     * Sauvegarde finale (status=final) + menu vert + variable volet 9 = 1 (à brancher)
     */
    public function validateStep(PassportUpsertRequest $request, $productId)
    {
        return DB::transaction(function () use ($request, $productId) {

            $passport = Passport::firstOrCreate(
                ['product_id' => $productId],
                ['created_by' => $request->user()->id]
            );

            $passport->update([
                'with_qr' => $request->boolean('with_qr'),
                'status'  => 'final',
            ]);

            $passport->authorizedUsers()->sync($request->input('user_ids', []));

            $emails = collect($request->input('emails', []))
                ->map(fn ($e) => mb_strtolower(trim($e)))
                ->filter()
                ->unique()
                ->values();

            $passport->authorizedEmails()->delete();
            foreach ($emails as $email) {
                $passport->authorizedEmails()->create(['email' => $email]);
            }

            $accessLevel = $request->input('access_level'); // internal|partner|public
            if ($accessLevel === 'partner') {
                $passport->authorizedPartners()->sync($request->input('partner_ids', []));
            } else {
                $passport->authorizedPartners()->sync([]);
            }

            // ✅ TODO: Brancher ta progression ici (menu vert volet 13 + passage volet 14)
            // ✅ TODO: Mettre "variable volet 9 = 1" comme indiqué dans le CDC :contentReference[oaicite:2]{index=2}
            // Exemple générique:
            // ProductProgress::updateOrCreate(['product_id' => $productId], [
            //   'volet13_status' => 'green',
            //   'current_volet' => 14,
            //   'volet9_done' => 1
            // ]);

            return response()->json([
                'message' => 'Passport validated',
                'passport' => $passport->load([
                    'authorizedUsers:id,name,email',
                    'authorizedEmails:id,passport_id,email',
                    'authorizedPartners:id,name'
                ]),
                'next' => [
                    'go_to_volet' => 14
                ]
            ]);
        });
    }
}

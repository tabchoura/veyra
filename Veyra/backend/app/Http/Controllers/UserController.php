<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Vérifie si l'utilisateur connecté est SUPER_USER
     */
    private function ensureIsAdmin(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->user_type !== 'SUPER_USER') {
            abort(403, 'Accès réservé à l\'administrateur.');
        }
    }

    /**
     * GET /api/admin/users
     * Liste des clients inscrits
     */


    
 public function index(){
 $users = User::whereIn('user_type', ['USER', 'SUPER_USER'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $users,
        ]);
    }         

//    public function index(Request $request)
// {
//     $this->ensureIsAdmin($request);

//     $users = User::where('user_type', 'USER')
//         ->orderBy('created_at', 'desc')
//         ->get();

//     return response()->json([
//         'data' => $users,
//     ]);
// }

    /**
     * PATCH /api/admin/users/{user}/approve
     */
    public function approve(Request $request, User $user)
    {
        $this->ensureIsAdmin($request);

        if ($user->user_type !== 'USER') {
            return response()->json([
                'message' => 'Action non autorisée sur cet utilisateur.',
            ], 403);
        }

        $user->status = 'approved';
        $user->email_verified = true; // si tu veux valider aussi son email
        $user->save();

        return response()->json([
            'message' => 'Utilisateur approuvé avec succès.',
            'user' => $user,
        ]);
    }

    /**
     * PATCH /api/admin/users/{user}/reject
     */
    public function reject(Request $request, User $user)
    {
        $this->ensureIsAdmin($request);

        if ($user->user_type !== 'USER') {
            return response()->json([
                'message' => 'Action non autorisée sur cet utilisateur.',
            ], 403);
        }

        $user->status = 'rejected';
        $user->save();

        return response()->json([
            'message' => 'Utilisateur refusé avec succès.',
            'user' => $user,
        ]);
    }
}


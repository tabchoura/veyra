<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    /**
     * Vérifie si l'utilisateur connecté est ADMIN
     */
    private function ensureIsAdmin(Request $request)
    {
        $user = $request->user();

        if (!$user || $user->user_type !== 'ADMIN') {
            abort(403, 'Accès réservé à l\'administrateur.');
        }
    }

    /**
     * GET /api/admin/users
     * Liste des clients inscrits
     */
    public function index(Request $request)
    {
        $this->ensureIsAdmin($request);

        $users = User::whereIn('user_type', ['USER', 'SUPER_USER'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $users,
        ]);
    }

    /**
     * PATCH /api/admin/users/{user}/approve
     */
    public function approve(Request $request, User $user)
    {
        $this->ensureIsAdmin($request);

        // On peut approuver USER et SUPER_USER, mais pas ADMIN
        if ($user->user_type === 'ADMIN') {
            return response()->json([
                'message' => 'Action non autorisée sur cet utilisateur.',
            ], 403);
        }

        $user->status = 'approved';
        $user->email_verified = true;
        $user->save();

        // ✅ Envoyer l'email d'approbation
        $this->sendApprovalEmail($user);

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

        // On peut rejeter USER et SUPER_USER, mais pas ADMIN
        if ($user->user_type === 'ADMIN') {
            return response()->json([
                'message' => 'Action non autorisée sur cet utilisateur.',
            ], 403);
        }

        $user->status = 'rejected';
        $user->save();

        // ✅ Envoyer l'email de refus
        $this->sendRejectionEmail($user);

        return response()->json([
            'message' => 'Utilisateur refusé avec succès.',
            'user' => $user,
        ]);
    }

    /**
     * Envoyer email d'approbation
     */
    private function sendApprovalEmail(User $user)
    {
        $subject = $user->language === 'fr' 
            ? 'Votre compte Veyra a été approuvé'
            : 'Your Veyra account has been approved';

        $message = $user->language === 'fr'
            ? "Bonjour {$user->first_name},\n\n"
              . "Bonne nouvelle ! Votre compte Veyra a été approuvé par notre équipe.\n\n"
              . "Vous pouvez maintenant vous connecter et accéder à tous nos services.\n\n"
              . "Connectez-vous sur : " . url('/login') . "\n\n"
              . "Cordialement,\n"
              . "L'équipe Veyra"
            : "Hello {$user->first_name},\n\n"
              . "Good news! Your Veyra account has been approved by our team.\n\n"
              . "You can now log in and access all our services.\n\n"
              . "Log in at: " . url('/login') . "\n\n"
              . "Best regards,\n"
              . "The Veyra Team";

        try {
            Mail::raw($message, function ($mail) use ($user, $subject) {
                $mail->to($user->email)
                     ->subject($subject);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send approval email: ' . $e->getMessage());
        }
    }

    /**
     * Envoyer email de refus
     */
    private function sendRejectionEmail(User $user)
    {
        $subject = $user->language === 'fr' 
            ? 'Votre demande d\'inscription sur Veyra'
            : 'Your registration request on Veyra';

        $message = $user->language === 'fr'
            ? "Bonjour {$user->first_name},\n\n"
              . "Nous vous remercions pour votre intérêt pour Veyra.\n\n"
              . "Malheureusement, nous ne pouvons pas donner suite à votre demande d'inscription pour le moment.\n\n"
              . "Si vous avez des questions, n'hésitez pas à nous contacter.\n\n"
              . "Cordialement,\n"
              . "L'équipe Veyra"
            : "Hello {$user->first_name},\n\n"
              . "Thank you for your interest in Veyra.\n\n"
              . "Unfortunately, we cannot proceed with your registration request at this time.\n\n"
              . "If you have any questions, please feel free to contact us.\n\n"
              . "Best regards,\n"
              . "The Veyra Team";

        try {
            Mail::raw($message, function ($mail) use ($user, $subject) {
                $mail->to($user->email)
                     ->subject($subject);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send rejection email: ' . $e->getMessage());
        }
    }
  

}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;
use App\Mail\ResetPasswordMail;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        // Validation
        $validated = $request->validate([
            // Company info
            'company_name' => 'required|string|max:255',
            'tva_number' => 'required|string|max:255',
            
            // Logo (optionnel)
            'logo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            
            // Contact person
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'function'   => 'nullable|string|max:255',
            
            // Address
            'address1'    => 'required|string|max:255',
            'address2'    => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:50',
            'country'     => 'required|string|max:255',
            
            // Business
            'sector'       => 'required|string|max:255',
            'sector_other' => 'nullable|string|max:255|required_if:sector,Autres',
            
            // Partners
            'partner'       => 'nullable|string|max:255',
            'partner_other' => 'nullable|string|max:255|required_if:partner,Autre',
            
            // Preferences
            'language' => 'required|in:fr,en',
            
            // Auth
            'email' => 'required|email|unique:users,email|max:255',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ], [
            'company_name.required' => 'Le nom de la société est obligatoire.',
            'tva_number.required'   => 'Le numéro de TVA est obligatoire.',
            'first_name.required'   => 'Le prénom est obligatoire.',
            'last_name.required'    => 'Le nom est obligatoire.',
            'address1.required'     => 'L\'adresse est obligatoire.',
            'postal_code.required'  => 'Le code postal est obligatoire.',
            'country.required'      => 'Le pays est obligatoire.',
            'sector.required'       => 'Le secteur d\'activité est obligatoire.',
            'sector_other.required_if'  => 'Veuillez préciser votre secteur.',
            'partner_other.required_if' => 'Veuillez préciser votre partenaire.',
            'language.required'     => 'La langue est obligatoire.',
            'language.in'           => 'La langue doit être fr ou en.',
            'email.required'        => 'L\'email est obligatoire.',
            'email.email'           => 'L\'email doit être valide.',
            'email.unique'          => 'Cet email est déjà utilisé.',
            'password.required'     => 'Le mot de passe est obligatoire.',
            'password.confirmed'    => 'Les mots de passe ne correspondent pas.',
            'logo.image'            => 'Le fichier doit être une image.',
            'logo.mimes'            => 'L\'image doit être au format jpeg, jpg ou png.',
            'logo.max'              => 'L\'image ne doit pas dépasser 2MB.',
        ]);

        // Handle logo upload
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        // Generate email verification token
        $verificationToken = Str::random(64);

        // Create user
        $user = User::create([
            // Auth
            'email'                 => $validated['email'],
            'password'              => Hash::make($validated['password']),
            'email_verified'        => false,
            'email_verification_token' => $verificationToken,
            
            // Company
            'company_name' => $validated['company_name'],
            'logo_url'     => $logoPath,
            'tva_number'   => $validated['tva_number'],
            
            // Contact
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'function'   => $validated['function'] ?? null,
            
            // Address
            'address1'    => $validated['address1'],
            'address2'    => $validated['address2'] ?? null,
            'postal_code' => $validated['postal_code'],
            'country'     => $validated['country'],
            
            // Business
            'sector'       => $validated['sector'],
            'sector_other' => $validated['sector_other'] ?? null,
            
            // Partners
            'partner'       => $validated['partner'] ?? null,
            'partner_other' => $validated['partner_other'] ?? null,
            
            // Preferences
            'language' => $validated['language'],
            
            // Tracking
            'ip_signup' => $request->ip(),
            'signup_at' => now(),
            
            // Defaults
            'user_type'  => 'SUPER_USER',
            'dpp_access' => 0,
            'quota_dpp'  => 0,
        ]);

        // Send verification email
        $this->sendVerificationEmail($user, $verificationToken);

        // Generate token
        $token = $user->createToken('veyra-token')->plainTextToken;

        return response()->json([
            'message' => 'Compte créé avec succès. Veuillez vérifier votre email.',
            'user' => [
                'id'             => $user->id,
                'email'          => $user->email,
                'company_name'   => $user->company_name,
                'first_name'     => $user->first_name,
                'last_name'      => $user->last_name,
                'email_verified' => $user->email_verified,
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Login Admin
     */
    public function loginadmin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'L\'email est obligatoire.',
            'email.email'       => 'L\'email doit être valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        // Find user
        $user = User::where('email', $credentials['email'])->first();

        // Check if user exists
        if (!$user) {
            return response()->json([
                'message' => 'Identifiants invalides.'
            ], 401);
        }

        // Check password
        if (!Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Identifiants invalides.'
            ], 401);
        }

        // Vérifier que c'est bien un ADMIN
        if ($user->user_type !== 'ADMIN') {
            return response()->json([
                'message' => 'Accès réservé aux administrateurs.',
            ], 403);
        }

        // Create new token
        $token = $user->createToken('veyra-admin-token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'user' => [
                'id'           => $user->id,
                'email'        => $user->email,
                'first_name'   => $user->first_name,
                'last_name'    => $user->last_name,
                'user_type'    => $user->user_type,
                'dpp_access'   => $user->dpp_access,
                'quota_dpp'    => $user->quota_dpp,
                'language'     => $user->language,
            ],
            'token' => $token,
        ], 200);
    }

    /**
     * Login User (non-admin)
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'L\'email est obligatoire.',
            'email.email'       => 'L\'email doit être valide.',
            'password.required' => 'Le mot de passe est obligatoire.',
        ]);

        // Find user
        $user = User::where('email', $credentials['email'])->first();

        // Check credentials
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Identifiants invalides.'
            ], 401);
        }

        // Vérifier si c'est un ADMIN
        if ($user->user_type === 'ADMIN') {
            return response()->json([
                'message' => 'Les administrateurs ne peuvent pas se connecter ici. Veuillez utiliser l\'espace d\'administration.',
                'error_type' => 'admin_restricted'
            ], 403);
        }

        // Vérifier si le compte est approuvé
        if ($user->status !== 'approved') {
            return response()->json([
                'message' => 'Vous n\'avez pas accès. Votre compte n\'est pas encore validé.',
                'error_type' => 'not_approved',
                'status' => $user->status
            ], 403);
        }

        // Vérifier l'email
        if (!$user->email_verified) {
            return response()->json([
                'message' => 'Veuillez vérifier votre email avant de vous connecter.',
                'error_type' => 'email_not_verified'
            ], 403);
        }

        // Créer le token
        $token = $user->createToken('veyra-token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie.',
            'user' => [
                'id'           => $user->id,
                'email'        => $user->email,
                'first_name'   => $user->first_name,
                'last_name'    => $user->last_name,
                'company_name' => $user->company_name,
                'logo_url'     => $user->logo_url ? asset('storage/' . $user->logo_url) : null,
                'user_type'    => $user->user_type,
                'status'       => $user->status,
                'dpp_access'   => $user->dpp_access,
                'quota_dpp'    => $user->quota_dpp,
                'language'     => $user->language,
            ],
            'token' => $token,
        ], 200);
    }

    /**
     * Get current user info
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id'           => $user->id,
                'email'        => $user->email,
                'first_name'   => $user->first_name,
                'last_name'    => $user->last_name,
                'company_name' => $user->company_name,
                'logo_url'     => $user->logo_url ? asset('storage/' . $user->logo_url) : null,
                'tva_number'   => $user->tva_number,
                'function'     => $user->function,
                'address1'     => $user->address1,
                'address2'     => $user->address2,
                'postal_code'  => $user->postal_code,
                'country'      => $user->country,
                'sector'       => $user->sector,
                'sector_other' => $user->sector_other,
                'partner'      => $user->partner,
                'partner_other'=> $user->partner_other,
                'language'     => $user->language,
                'user_type'    => $user->user_type,
                'dpp_access'   => $user->dpp_access,
                'quota_dpp'    => $user->quota_dpp,
                'email_verified' => $user->email_verified,
                'created_at'     => $user->created_at,
            ]
        ], 200);
    }

    /**
     * Verify email
     */
    public function verifyEmail(Request $request, $token)
    {
        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Token de vérification invalide.'
            ], 400);
        }

        if ($user->email_verified) {
            return response()->json([
                'message' => 'Email déjà vérifié.'
            ], 200);
        }

        $user->update([
            'email_verified'           => true,
            'email_verification_token' => null,
        ]);

        return response()->json([
            'message' => 'Email vérifié avec succès. Vous pouvez maintenant vous connecter.'
        ], 200);
    }

    /**
     * Resend verification email
     */
    public function resendVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user->email_verified) {
            return response()->json([
                'message' => 'Email déjà vérifié.'
            ], 200);
        }

        // Generate new token
        $verificationToken = Str::random(64);
        $user->update([
            'email_verification_token' => $verificationToken,
        ]);

        // Send email
        $this->sendVerificationEmail($user, $verificationToken);

        return response()->json([
            'message' => 'Email de vérification renvoyé.'
        ], 200);
    }

    /**
     * Send verification email
     */
    private function sendVerificationEmail(User $user, string $token)
    {
        $subject = $user->language === 'fr' 
            ? 'Confirmation de votre inscription sur Veyra'
            : 'Confirmation of your registration on Veyra';

        $message = $user->language === 'fr'
            ? "Bonjour {$user->first_name},\n\n"
              . "Merci de vous être inscrit sur Veyra.\n\n"
              . "Votre demande est en cours de validation par notre équipe. "
              . "Veuillez attendre un e-mail de retour qui vous indiquera si votre compte "
              . "a été accepté ou refusé.\n\n"
              . "Cordialement,\n"
              . "L'équipe Veyra"
            : "Hello {$user->first_name},\n\n"
              . "Thank you for signing up on Veyra.\n\n"
              . "Your registration is currently under review by our team. "
              . "Please wait for a follow-up email that will inform you whether "
              . "your account has been approved or rejected.\n\n"
              . "Best regards,\n"
              . "The Veyra Team";

        try {
            Mail::raw($message, function ($mail) use ($user, $subject) {
                $mail->to($user->email)
                     ->subject($subject);
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email: ' . $e->getMessage());
        }
    }

    /**
     * Forgot password
     */
    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Aucun compte associé à cet email'
            ], 404);
        }

        // Générer un token
        $token = Str::random(64);
        
        // Supprimer les anciens tokens
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        
        // Sauvegarder le nouveau token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        // Envoyer l'email
        try {
            Mail::to($user->email)->send(new ResetPasswordMail($token, $user));
            
            return response()->json([
                'message' => 'Email de réinitialisation envoyé'
            ], 200);
            
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email: ' . $e->getMessage());
            
            return response()->json([
                'message' => 'Erreur lors de l\'envoi de l\'email'
            ], 500);
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Récupérer le reset
        $reset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$reset) {
            return response()->json([
                'message' => 'Token invalide ou expiré.'
            ], 400);
        }

        // Vérifier le token
        if (!Hash::check($request->token, $reset->token)) {
            return response()->json([
                'message' => 'Token incorrect.'
            ], 400);
        }

        // Vérifier expiration : 60 minutes
        if (Carbon::parse($reset->created_at)->addMinutes(60)->isPast()) {
            return response()->json([
                'message' => 'Le lien a expiré.'
            ], 400);
        }

        // Mettre à jour le mot de passe
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Supprimer le token utilisé
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'message' => 'Mot de passe réinitialisé avec succès.'
        ], 200);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.'
        ], 200);
    }
}
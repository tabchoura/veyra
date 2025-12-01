<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;
    public $user;
    public $resetUrl;

    public function __construct($token, User $user)
    {
        $this->token = $token;
        $this->user = $user;

        // Générer l'URL pour la réinitialisation
        $this->resetUrl = env('FRONTEND_URL') 
            . '/reset-password?token=' . $this->token 
            . '&email=' . urlencode($this->user->email);
    }

    public function build()
    {
        return $this->subject('Réinitialisation de votre mot de passe - Veyra')
                    ->view('emails.reset-password') // Vue HTML
                    ->text('emails.reset-password') // Version texte (optionnelle)
                    ->with([
                        'user'     => $this->user,
                        'resetUrl' => $this->resetUrl,
                    ]);
    }
}
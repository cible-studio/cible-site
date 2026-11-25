<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Code de connexion à l'espace admin (double authentification, 2026-11-25).
 *
 * Envoyé uniquement à l'adresse admin configurée (CIBLE_ADMIN_EMAIL), jamais
 * à une adresse saisie dans le formulaire. Le code n'est pas dans l'objet :
 * il s'afficherait en clair dans les notifications d'un téléphone verrouillé.
 * Envoi synchrone : l'administrateur attend ce mail pour se connecter.
 */
class AdminCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $code,
        public int $minutes,
        public string $quand,
        public string $ip,
    ) {}

    public function build()
    {
        return $this->subject('Votre code de connexion — administration du site CIBLE')
                    ->view('emails.admin.code')
                    ->text('emails.admin.code-texte');
    }
}

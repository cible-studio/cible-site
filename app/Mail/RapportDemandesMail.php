<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Récapitulatif mensuel des demandes (2026-11-25) — chiffres seulement,
 * aucune donnée personnelle. Contient un lien vers le rapport de l'admin,
 * donc l'adresse confidentielle de l'administration : décision du
 * 2026-11-25, l'accès reste protégé par le mot de passe et le code e-mail.
 */
class RapportDemandesMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $libelle,
        public array $stats,
        public int $totalAvant,
        public string $libelleAvant,
        public string $lien,
    ) {}

    public function build()
    {
        return $this->subject("Demandes reçues sur le site CIBLE — {$this->libelle}")
                    ->view('emails.admin.rapport');
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Récapitulatif mensuel des demandes (2026-11-25) — chiffres seulement,
 * aucune donnée personnelle, et pas l'adresse secrète de l'administration.
 */
class RapportDemandesMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $libelle,
        public array $stats,
        public int $totalAvant,
        public string $libelleAvant,
    ) {}

    public function build()
    {
        return $this->subject("Demandes reçues sur le site CIBLE — {$this->libelle}")
                    ->view('emails.admin.rapport');
    }
}

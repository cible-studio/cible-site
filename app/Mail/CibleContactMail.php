<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Mail de demande reçue via /contact (mini-brief marketing).
 * Envoyé à commercial@cible-ci.com (config mail.cible_devis_to).
 *
 * Les documents éventuellement déposés par le visiteur sont attachés
 * depuis leur emplacement temporaire d'upload. L'envoi est synchrone
 * (pas de queue) : les fichiers temporaires existent donc encore au
 * moment du build, et disparaissent à la fin de la requête — rien
 * n'est conservé sur le serveur.
 */
class CibleContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    /** @var array<int, array{label: string, file: \Illuminate\Http\UploadedFile}> */
    public array $fichiers;

    public function __construct(array $data, array $fichiers = [])
    {
        $this->data     = $data;
        $this->fichiers = $fichiers;
    }

    public function build()
    {
        $subj = 'Nouvelle demande — ' . ($this->data['entreprise'] ?? '?') . ' (' . ($this->data['nom'] ?? '?') . ')';

        $mail = $this->subject($subj)
                     ->view('emails.cible.devis', ['d' => $this->data])
                     ->replyTo($this->data['email'] ?? config('mail.from.address'));

        foreach ($this->fichiers as $doc) {
            $mail->attach($doc['file']->getRealPath(), [
                'as'   => $doc['label'] . ' — ' . $doc['file']->getClientOriginalName(),
                'mime' => $doc['file']->getMimeType(),
            ]);
        }

        return $mail;
    }
}

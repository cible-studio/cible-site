<?php

namespace App\Console\Commands;

use App\Mail\RapportDemandesMail;
use App\Support\Demandes;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Récapitulatif mensuel des demandes du formulaire de contact (2026-11-25).
 *
 * Lancé le 1er de chaque mois par une tâche planifiée Coolify :
 *     php artisan cible:rapport-demandes          (mois précédent)
 *     php artisan cible:rapport-demandes --mois=2026-11 --a=moi@cible-ci.com   (test)
 *
 * Le mail ne contient que des chiffres : le détail des demandes (données
 * personnelles) reste dans l'administration, protégée par le code e-mail.
 */
class RapportDemandes extends Command
{
    protected $signature = 'cible:rapport-demandes
                            {--mois= : Mois à résumer (AAAA-MM) ; par défaut le mois précédent}
                            {--a= : Destinataire(s) pour un essai, à la place de CIBLE_RAPPORT_TO}';

    protected $description = 'Envoie le récapitulatif mensuel des demandes du formulaire de contact';

    public function handle(): int
    {
        Demandes::purger();

        $mois = (string) ($this->option('mois') ?: now()->subMonthNoOverflow()->format('Y-m'));
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $mois)) {
            $this->error("Mois invalide : $mois (format attendu AAAA-MM).");
            return self::FAILURE;
        }

        $destinataires = $this->option('a')
            ? array_values(array_filter(array_map('trim', explode(',', (string) $this->option('a')))))
            : config('cible.rapport_to');

        if (empty($destinataires)) {
            $this->error('Aucun destinataire : renseigner CIBLE_RAPPORT_TO (adresses séparées par des virgules).');
            return self::FAILURE;
        }

        [$debut, $fin, $libelle] = Demandes::periodeMois($mois);
        $stats = Demandes::statistiques($debut, $fin);

        $avant = substr(\Illuminate\Support\Carbon::createFromFormat('Y-m-d', "$mois-01")->subMonthNoOverflow()->format('Y-m'), 0, 7);
        [$debutAvant, $finAvant] = Demandes::periodeMois($avant);
        $totalAvant = Demandes::statistiques($debutAvant, $finAvant)['total'];

        Mail::to($destinataires)->send(new RapportDemandesMail($libelle, $stats, $totalAvant, Demandes::libelleMois($avant)));

        $this->info("Récapitulatif « $libelle » envoyé à " . implode(', ', $destinataires) . " : {$stats['total']} demande(s).");

        return self::SUCCESS;
    }
}

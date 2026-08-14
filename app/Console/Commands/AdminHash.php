<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Génère le hash bcrypt du mot de passe administrateur.
 *
 * Évite d'avoir à passer par tinker et, surtout, évite que le mot de passe
 * en clair se retrouve dans un fichier de configuration ou dans un
 * historique de commandes : la saisie est masquée.
 */
class AdminHash extends Command
{
    protected $signature = 'cible:admin-hash';

    protected $description = "Génère le hash du mot de passe de l'espace admin";

    public function handle(): int
    {
        $motDePasse = $this->secret('Mot de passe administrateur (saisie masquée)');

        if (!is_string($motDePasse) || mb_strlen($motDePasse) < 12) {
            $this->error('Mot de passe trop court : 12 caractères minimum.');
            $this->line("L'espace admin est exposé publiquement, un mot de passe faible");
            $this->line('est la première chose qu'."'".'un robot testera.');

            return self::FAILURE;
        }

        if ($motDePasse !== $this->secret('Confirmer le mot de passe')) {
            $this->error('Les deux saisies diffèrent.');

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('À coller dans les variables d\'environnement (Coolify) :');
        $this->newLine();
        $this->line('CIBLE_ADMIN_EMAIL=votre@email.com');
        $this->line('CIBLE_ADMIN_HASH=' . Hash::make($motDePasse));
        $this->newLine();
        $this->comment('Le hash contient des $ : pensez aux guillemets si votre');
        $this->comment('interface les interprète.');

        return self::SUCCESS;
    }
}

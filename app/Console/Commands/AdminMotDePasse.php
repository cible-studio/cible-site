<?php

namespace App\Console\Commands;

use App\Support\AdminAuth;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Définit le mot de passe administrateur directement sur le volume
 * persistant, sans passer par les variables d'environnement.
 *
 * Raison d'être : l'interface de configuration a abîmé la variable à
 * plusieurs reprises — « $ » interprétés comme des variables shell, espaces
 * parasites, valeur tronquée — et chaque tentative demandait un
 * redéploiement pour être testée. Ici, le hash est écrit tel quel par PHP :
 * rien ne peut le transformer en chemin, et l'effet est immédiat.
 *
 * Le fichier vit hors de public/, n'est jamais servi par le serveur web et
 * n'est pas versionné.
 */
class AdminMotDePasse extends Command
{
    protected $signature = 'cible:admin-motdepasse';

    protected $description = "Définit le mot de passe admin sur le volume persistant (sans variable d'environnement)";

    public function handle(): int
    {
        if (blank(config('cible.admin.email'))) {
            $this->error('CIBLE_ADMIN_EMAIL doit être renseigné.');
            $this->line("Cette variable-là ne contient aucun caractère spécial, elle passe sans problème.");

            return self::FAILURE;
        }

        $this->line('Compte : <fg=yellow>' . config('cible.admin.email') . '</>');
        $this->newLine();

        $motDePasse = $this->secret('Nouveau mot de passe (saisie masquée)');

        if (!is_string($motDePasse) || mb_strlen($motDePasse) < 12) {
            $this->error('Mot de passe trop court : 12 caractères minimum.');

            return self::FAILURE;
        }

        if ($motDePasse !== $this->secret('Confirmer')) {
            $this->error('Les deux saisies diffèrent.');

            return self::FAILURE;
        }

        if (!AdminAuth::enregistrerHash(Hash::make($motDePasse))) {
            $this->error("Écriture impossible sur le volume persistant.");
            $this->line('Vérifiez qu\'un volume est bien monté sur /app/storage/app/contenu');
            $this->line('dans Coolify, et qu\'il est accessible en écriture.');

            return self::FAILURE;
        }

        // Contrôle immédiat par le vrai chemin d'authentification : sans lui,
        // on annoncerait un succès sans savoir si la connexion fonctionnera.
        $d = AdminAuth::diagnostic((string) config('cible.admin.email'), $motDePasse);

        $this->newLine();

        if ($d['passe_ok'] && $d['email_ok']) {
            $this->info('✓ Mot de passe enregistré et vérifié.');
            $this->line('  Source retenue : ' . $d['source']);
            $this->newLine();
            $this->comment('Vous pouvez vous connecter immédiatement, sans redéployer.');
            $this->comment('CIBLE_ADMIN_HASH peut rester vide ou erroné : le fichier prime');
            $this->comment("dès lors que la variable n'est pas exploitable.");

            return self::SUCCESS;
        }

        $this->error('Enregistré, mais la vérification échoue — signalez-le.');

        return self::FAILURE;
    }
}

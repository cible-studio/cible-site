<?php

namespace App\Console\Commands;

use App\Support\AdminAuth;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

/**
 * Génère — ou vérifie — le hash du mot de passe administrateur.
 *
 * Le mode vérification existe parce que le diagnostic à distance est
 * pénible : un hash abîmé par l'interface de configuration produit une
 * erreur générique, et rien ne distingue « mauvais mot de passe » de
 * « valeur d'environnement corrompue ». Cette commande tranche depuis le
 * terminal du conteneur, sans divulguer le hash.
 */
class AdminHash extends Command
{
    protected $signature = 'cible:admin-hash {--verifier : Contrôle la valeur actuellement configurée}';

    protected $description = "Génère ou vérifie le hash du mot de passe de l'espace admin";

    public function handle(): int
    {
        return $this->option('verifier') ? $this->verifier() : $this->generer();
    }

    private function verifier(): int
    {
        $email = (string) config('cible.admin.email');
        $brut  = (string) config('cible.admin.hash');

        $this->newLine();
        $this->line('CIBLE_ADMIN_EMAIL : ' . ($email !== '' ? $email : '<fg=red>absent</>'));
        $this->line('CIBLE_ADMIN_HASH  : ' . ($brut !== '' ? strlen($brut) . ' caractères' : '<fg=red>absent</>'));

        if ($brut === '' || $email === '') {
            $this->newLine();
            $this->error("L'espace admin est désactivé : /admin répond 404 tant que les deux variables ne sont pas renseignées.");

            return self::FAILURE;
        }

        if ($brut !== trim($brut)) {
            $this->warn('⚠ La valeur contient un espace ou un retour à la ligne en trop — absorbé, mais à corriger.');
        }

        // On teste le chemin réel de l'authentification plutôt qu'une
        // réimplémentation : c'est le seul contrôle qui prouve quelque chose.
        $motDePasse = $this->secret('Mot de passe à tester (saisie masquée)');

        $d = AdminAuth::diagnostic($email, (string) $motDePasse);

        $this->newLine();
        $this->line('Hash exploitable   : ' . ($d['hash_exploitable'] ? '<fg=green>oui</>' : '<fg=red>NON</>'));
        $this->line('Source du hash     : ' . $d['source']);
        $this->line('Email correspond   : ' . ($d['email_ok'] ? '<fg=green>oui</>' : '<fg=red>NON</>'));
        $this->line('Mot de passe       : ' . ($d['passe_ok'] ? '<fg=green>correct</>' : '<fg=red>INCORRECT</>'));
        $this->newLine();

        if ($d['hash_exploitable'] && $d['email_ok'] && $d['passe_ok']) {
            $this->info('✓ La connexion doit fonctionner.');

            return self::SUCCESS;
        }

        // Chaque cause a sa correction : on la nomme au lieu de laisser
        // deviner, c'est tout l'intérêt d'un diagnostic en ligne de commande.
        if (!$d['hash_exploitable']) {
            $this->error('→ CIBLE_ADMIN_HASH est inexploitable.');
            $this->line('  « $ » avalés par l\'interface, valeur tronquée, ou guillemets inclus.');
            $this->line('  Solution la plus sûre, qui contourne la variable :');
            $this->line('      <fg=yellow>php artisan cible:admin-motdepasse</>');
        } elseif (!$d['email_ok']) {
            $this->error('→ L\'email saisi ne correspond pas à CIBLE_ADMIN_EMAIL.');
            $this->line('  Attendu : ' . $email);
        } else {
            $this->error('→ Le hash est valide, mais ce mot de passe ne correspond pas.');
            $this->line('  Pour en définir un nouveau :');
            $this->line('      <fg=yellow>php artisan cible:admin-motdepasse</>');
        }

        return self::FAILURE;
    }

    private function generer(): int
    {
        $motDePasse = $this->secret('Mot de passe administrateur (saisie masquée)');

        if (!is_string($motDePasse) || mb_strlen($motDePasse) < 12) {
            $this->error('Mot de passe trop court : 12 caractères minimum.');
            $this->line("L'espace admin est exposé publiquement, un mot de passe faible");
            $this->line('est la première chose qu\'un robot testera.');

            return self::FAILURE;
        }

        if ($motDePasse !== $this->secret('Confirmer le mot de passe')) {
            $this->error('Les deux saisies diffèrent.');

            return self::FAILURE;
        }

        $hash = Hash::make($motDePasse);

        $this->newLine();
        $this->info('À coller dans les variables d\'environnement (Coolify) :');
        $this->newLine();
        $this->line('CIBLE_ADMIN_EMAIL=votre@email.com');
        $this->line('CIBLE_ADMIN_HASH=' . base64_encode($hash));
        $this->newLine();

        // Le hash bcrypt brut contient des « $ » que beaucoup d'interfaces
        // interprètent comme des variables shell et avalent silencieusement.
        // La version base64 n'en contient aucun : c'est la forme à privilégier.
        $this->comment('Cette valeur est encodée en base64 : elle ne contient aucun « $ »,');
        $this->comment('donc rien que votre interface puisse interpréter de travers.');
        $this->comment('Collez-la SANS espace ni retour à la ligne autour.');
        $this->newLine();
        $this->line('<fg=gray>Contrôle après redémarrage : php artisan cible:admin-hash --verifier</>');

        return self::SUCCESS;
    }
}

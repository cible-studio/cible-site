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

        if (AdminAuth::tenter($email, (string) $motDePasse)) {
            $this->newLine();
            $this->info('✓ Hash valide et mot de passe correct : la connexion doit fonctionner.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->error('✗ Échec.');
        $this->line('Deux causes possibles :');
        $this->line('  · le mot de passe saisi ne correspond pas ;');
        $this->line('  · CIBLE_ADMIN_HASH est abîmé — « $ » avalés par l\'interface,');
        $this->line('    valeur tronquée, ou espace parasite.');
        $this->newLine();
        $this->comment('Regénérez avec « php artisan cible:admin-hash » et collez la');
        $this->comment('valeur base64, qui ne contient aucun caractère interprétable.');

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

<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


/**
 * Authentification de l'espace admin — un seul compte, aucune table.
 *
 * Le site n'a pas de base relationnelle et n'en a pas besoin ici : un
 * unique administrateur, dont l'identifiant et le hash du mot de passe
 * vivent dans les variables d'environnement. Cela supprime d'un coup la
 * table `users`, l'inscription, la réinitialisation de mot de passe et la
 * vérification d'email — soit quatre surfaces d'attaque en moins sur un
 * site public qui n'avait aucun login jusqu'ici.
 *
 * Générer le hash : php artisan cible:admin-hash
 */
class AdminAuth
{
    private const CLE_SESSION = 'cible_admin';

    public static function configure(): bool
    {
        // L'email vient toujours de l'environnement ; le hash peut venir de
        // l'environnement OU du fichier posé sur le volume persistant.
        return filled(config('cible.admin.email'))
            && (filled(config('cible.admin.hash')) || self::hashDepuisFichier() !== null);
    }

    /**
     * Hash bcrypt exploitable, ou null.
     *
     * Beaucoup d'interfaces de configuration — Coolify en fait partie —
     * interprètent les `$` d'un hash bcrypt (`$2y$12$…`) comme des variables
     * shell et les avalent. Le hash arrive alors tronqué, et Hash::check ne
     * renvoie pas false : il LÈVE une RuntimeException, donc une erreur 500
     * à la connexion. C'est exactement ce qui s'est produit en production.
     *
     * Deux parades : on accepte le hash encodé en base64 (aucun `$`, donc
     * rien à interpréter), et on refuse proprement un hash illisible au lieu
     * de laisser remonter l'exception.
     */
    /**
     * Un bcrypt exploitable fait exactement 60 caractères et est reconnu
     * comme tel par PHP.
     *
     * Le test du seul préfixe « $2y$ » ne suffit pas : un hash tronqué le
     * conserve, passe la vérification, puis fait lever Hash::check — donc
     * une 500. Vérifié : un hash coupé, ou simplement suivi d'un espace,
     * garde son préfixe mais n'est plus un bcrypt. Un espace parasite en
     * fin de valeur est le cas le plus courant d'un copier-coller dans une
     * interface de configuration.
     */
    private static function estBcrypt(string $h): bool
    {
        return strlen($h) === 60 && password_get_info($h)['algoName'] === 'bcrypt';
    }

    /**
     * Fichier de mot de passe sur le volume persistant.
     *
     * Voie de secours quand l'interface de configuration abîme la variable
     * d'environnement — ce qui s'est produit à répétition ici. Le fichier
     * vit hors de public/, n'est jamais servi par le serveur web, et n'est
     * pas dans git. Écrit par « php artisan cible:admin-motdepasse ».
     */
    private const FICHIER_HASH = 'admin-hash.txt';

    private static function hashDepuisFichier(): ?string
    {
        try {
            if (!Storage::disk('contenu')->exists(self::FICHIER_HASH)) {
                return null;
            }

            $h = trim(Storage::disk('contenu')->get(self::FICHIER_HASH));

            return self::estBcrypt($h) ? $h : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function enregistrerHash(string $hash): bool
    {
        try {
            Storage::disk('contenu')->put(self::FICHIER_HASH, $hash);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    private static function hashValide(): ?string
    {
        // trim : un espace ou un retour à la ligne collé par mégarde suffit
        // à invalider le hash, autant l'absorber silencieusement.
        $brut = trim((string) config('cible.admin.hash'));

        if (self::estBcrypt($brut)) {
            return $brut;
        }

        $decode = base64_decode($brut, true);
        if (is_string($decode) && self::estBcrypt(trim($decode))) {
            return trim($decode);
        }

        // Repli sur le volume : la variable d'environnement est inexploitable.
        if ($fichier = self::hashDepuisFichier()) {
            return $fichier;
        }

        Journal::erreur('cible.admin.hash_invalide', [
            'longueur_recue' => strlen($brut),
            'longueur_attendue' => '60 (bcrypt brut) ou 80 (base64)',
            'indice' => "CIBLE_ADMIN_HASH n'est pas un hash bcrypt exploitable. "
                      . "Causes fréquentes : les « \$ » avalés par l'interface, ou un "
                      . "espace collé en fin de valeur. Utilisez la version encodée en "
                      . "base64 de « php artisan cible:admin-hash », puis vérifiez avec "
                      . "« php artisan cible:admin-hash --verifier ».",
        ]);

        return null;
    }

    public static function tenter(string $email, string $motDePasse): bool
    {
        if (!self::configure()) {
            return false;
        }

        $hash = self::hashValide();

        // hash_equals : comparaison à temps constant, pour ne pas laisser
        // fuir l'identifiant valide par la durée de la réponse.
        $emailOk = hash_equals(
            mb_strtolower((string) config('cible.admin.email')),
            mb_strtolower(trim($email))
        );

        $passeOk = $hash !== null && Hash::check($motDePasse, $hash);

        // Les deux vérifications sont exécutées quoi qu'il arrive, pour que
        // la durée ne dépende pas de l'endroit où ça a échoué.
        return $emailOk && $passeOk;
    }

    /**
     * Détail des vérifications — réservé à la commande de diagnostic.
     *
     * L'écran de connexion ne renvoie qu'un message unique : dire lequel des
     * deux éléments est faux confirmerait l'identifiant à un attaquant. En
     * ligne de commande, l'accès au conteneur est déjà acquis — la
     * distinction ne coûte donc rien et fait gagner un temps considérable.
     *
     * @return array{hash_exploitable:bool, source:string, email_ok:bool, passe_ok:bool}
     */
    public static function diagnostic(string $email, string $motDePasse): array
    {
        $brut = trim((string) config('cible.admin.hash'));
        $hash = self::hashValide();

        $source = 'aucune';
        if ($hash !== null) {
            if (self::estBcrypt($brut)) {
                $source = 'variable d\'environnement (brute)';
            } elseif (self::estBcrypt(trim((string) base64_decode($brut, true)))) {
                $source = 'variable d\'environnement (base64)';
            } else {
                $source = 'fichier du volume persistant';
            }
        }

        return [
            'hash_exploitable' => $hash !== null,
            'source'           => $source,
            'email_ok'         => hash_equals(
                mb_strtolower((string) config('cible.admin.email')),
                mb_strtolower(trim($email))
            ),
            'passe_ok'         => $hash !== null && Hash::check($motDePasse, $hash),
        ];
    }

    public static function connecter(Request $request): void
    {
        // Régénération : sans elle, un identifiant de session obtenu avant
        // connexion resterait valide après (fixation de session).
        $request->session()->regenerate();
        $request->session()->put(self::CLE_SESSION, true);
    }

    public static function deconnecter(Request $request): void
    {
        $request->session()->forget(self::CLE_SESSION);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public static function connecte(Request $request): bool
    {
        return (bool) $request->session()->get(self::CLE_SESSION, false);
    }

    public static function journaliser(string $evenement, Request $request, array $extra = []): void
    {
        Journal::avertir("cible.admin.$evenement", [
            'ip' => $request->ip(),
            'ua' => substr((string) $request->userAgent(), 0, 160),
        ] + $extra);
    }
}

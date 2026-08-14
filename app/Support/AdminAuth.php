<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
        return filled(config('cible.admin.email')) && filled(config('cible.admin.hash'));
    }

    public static function tenter(string $email, string $motDePasse): bool
    {
        if (!self::configure()) {
            return false;
        }

        // hash_equals : comparaison à temps constant, pour ne pas laisser
        // fuir l'identifiant valide par la durée de la réponse.
        $emailOk = hash_equals(
            mb_strtolower(config('cible.admin.email')),
            mb_strtolower(trim($email))
        );

        $passeOk = Hash::check($motDePasse, config('cible.admin.hash'));

        // Les deux vérifications sont exécutées quoi qu'il arrive, pour que
        // la durée ne dépende pas de l'endroit où ça a échoué.
        return $emailOk && $passeOk;
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
        Log::warning("cible.admin.$evenement", [
            'ip' => $request->ip(),
            'ua' => substr((string) $request->userAgent(), 0, 160),
        ] + $extra);
    }
}

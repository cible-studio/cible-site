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
    private static function hashValide(): ?string
    {
        $brut = (string) config('cible.admin.hash');

        if (str_starts_with($brut, '$2y$') || str_starts_with($brut, '$2a$')) {
            return $brut;
        }

        $decode = base64_decode($brut, true);
        if (is_string($decode) && (str_starts_with($decode, '$2y$') || str_starts_with($decode, '$2a$'))) {
            return $decode;
        }

        Log::error('cible.admin.hash_invalide', [
            'indice' => "CIBLE_ADMIN_HASH n'est pas un hash bcrypt exploitable. "
                      . "Si votre interface interprète les « \$ », utilisez la version "
                      . "encodée en base64 fournie par « php artisan cible:admin-hash ».",
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

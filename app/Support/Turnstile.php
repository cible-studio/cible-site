<?php

namespace App\Support;

use Illuminate\Support\Facades\Http;

/**
 * Cloudflare Turnstile — vérification anti-robot du formulaire /contact.
 *
 * Conçu pour être inactif tant que les clés ne sont pas renseignées :
 * sans TURNSTILE_SITE_KEY / TURNSTILE_SECRET_KEY, le widget n'est pas rendu
 * et la vérification laisse passer. Le site continue donc de fonctionner
 * exactement comme avant en développement, et la mise en service se fait
 * uniquement en ajoutant deux variables d'environnement dans Coolify —
 * sans redéploiement de code.
 *
 * Turnstile complète l'analyse maison (App\Support\AntiSpam), il ne la
 * remplace pas : le premier arrête les robots à l'entrée, la seconde
 * rattrape ce qui passe malgré tout.
 */
class Turnstile
{
    private const ENDPOINT = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public static function actif(): bool
    {
        return filled(config('services.turnstile.site_key'))
            && filled(config('services.turnstile.secret_key'));
    }

    public static function siteKey(): ?string
    {
        return config('services.turnstile.site_key');
    }

    /**
     * @return bool true si le jeton est valide, ou si Turnstile est inactif.
     */
    public static function verifier(?string $jeton, ?string $ip = null): bool
    {
        if (!self::actif()) {
            return true;
        }

        if (!is_string($jeton) || $jeton === '') {
            return false;
        }

        try {
            $reponse = Http::asForm()
                ->timeout(5)
                ->post(self::ENDPOINT, array_filter([
                    'secret'   => config('services.turnstile.secret_key'),
                    'response' => $jeton,
                    'remoteip' => $ip,
                ]));

            $ok = (bool) ($reponse->json('success') ?? false);

            if (!$ok) {
                Journal::avertir('cible.turnstile.echec', [
                    'codes' => $reponse->json('error-codes') ?? [],
                    'ip'    => $ip,
                ]);
            }

            return $ok;
        } catch (\Throwable $e) {
            // Cloudflare injoignable : on laisse passer plutôt que de bloquer
            // un vrai prospect. L'analyse maison reste en place, et l'incident
            // est tracé pour ne pas passer inaperçu.
            Journal::erreur('cible.turnstile.indisponible', ['error' => $e->getMessage()]);

            return true;
        }
    }
}

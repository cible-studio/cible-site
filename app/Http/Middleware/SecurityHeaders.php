<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * En-têtes de sécurité appliqués à toutes les réponses HTML.
 *
 * Le site n'a ni compte ni session authentifiée : l'enjeu n'est pas le vol
 * de compte, mais la protection du visiteur et de la marque — empêcher
 * qu'on encadre le site dans une page tierce pour du clickjacking, qu'un
 * script injecté exfiltre les données du formulaire, ou qu'un navigateur
 * devine un type de contenu et exécute ce qu'il ne devrait pas.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Origines externes réellement utilisées par le site. Toute nouvelle
        // dépendance devra être ajoutée ici, sinon le navigateur la bloquera
        // — c'est précisément l'intérêt.
        $polices  = 'https://fonts.googleapis.com https://fonts.gstatic.com';
        $leaflet  = 'https://unpkg.com';
        $tuiles   = 'https://*.basemaps.cartocdn.com';
        $ga       = 'https://www.googletagmanager.com https://www.google-analytics.com https://*.google-analytics.com';
        // Turnstile : script + iframe du défi. Autorisé même quand les clés
        // ne sont pas configurées — la CSP décrit ce qui est permis, pas ce
        // qui est chargé, et cela évite un oubli le jour de l'activation.
        $turnstile = 'https://challenges.cloudflare.com';

        $csp = implode('; ', [
            "default-src 'self'",
            "base-uri 'self'",                    // interdit de détourner les URL relatives
            "object-src 'none'",                  // ni Flash ni plugin
            "frame-ancestors 'none'",             // anti-clickjacking (remplace X-Frame-Options)
            "form-action 'self'",                 // le formulaire ne peut poster que chez nous
            "img-src 'self' data: {$tuiles} {$leaflet}",
            // 'unsafe-inline' est nécessaire : le site est construit sur des
            // styles et scripts en ligne. La protection reste réelle — aucun
            // script d'une origine non listée ne peut s'exécuter, et le
            // contenu affiché est intégralement échappé côté Blade.
            "style-src 'self' 'unsafe-inline' {$polices} {$leaflet}",
            "font-src 'self' data: {$polices}",
            "script-src 'self' 'unsafe-inline' {$leaflet} {$ga} {$turnstile}",
            // Turnstile présente son défi dans une iframe.
            "frame-src {$turnstile}",
            "connect-src 'self' {$ga} {$tuiles} {$turnstile}",
            'upgrade-insecure-requests',
        ]);

        $entetes = [
            'Content-Security-Policy'   => $csp,
            'X-Content-Type-Options'    => 'nosniff',
            'X-Frame-Options'           => 'DENY',
            'Referrer-Policy'           => 'strict-origin-when-cross-origin',
            // Le site n'a besoin d'aucune de ces API : on les coupe.
            'Permissions-Policy'        => 'camera=(), microphone=(), geolocation=(), payment=(), usb=(), interest-cohort=()',
            'Cross-Origin-Opener-Policy'=> 'same-origin',
        ];

        // HSTS seulement en HTTPS : envoyé en clair, il n'a aucun sens et
        // bloquerait le développement local en http.
        if ($request->secure()) {
            $entetes['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        foreach ($entetes as $cle => $valeur) {
            $response->headers->set($cle, $valeur);
        }

        // Signature serveur : n'apporte rien au visiteur, renseigne un
        // attaquant sur la version exacte à cibler.
        $response->headers->remove('X-Powered-By');

        return $response;
    }
}

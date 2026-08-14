<?php

namespace App\Http\Middleware;

use App\Support\AdminAuth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garde de l'espace admin.
 *
 * Deux comportements volontairement distincts :
 *
 *  - admin NON configuré (variables d'environnement absentes) → 404.
 *    L'espace n'existe pas, point. Un scanner qui tombe sur /admin ne peut
 *    même pas déduire qu'il y a quelque chose à attaquer.
 *  - admin configuré mais visiteur non connecté → redirection vers le
 *    formulaire de connexion.
 */
class AdminProtege
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(AdminAuth::configure(), 404);

        if (!AdminAuth::connecte($request)) {
            return redirect()->route('admin.login');
        }

        $reponse = $next($request);

        // L'admin ne doit jamais être indexé ni mis en cache par un
        // intermédiaire : il affiche des données de gestion.
        $reponse->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive');
        $reponse->headers->set('Cache-Control', 'no-store, private');

        return $reponse;
    }
}

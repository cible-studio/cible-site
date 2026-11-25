<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Coolify place un reverse proxy (Traefik) devant le conteneur : il
        // termine le TLS et transmet une requête en HTTP simple. Sans cette
        // ligne, $request->isSecure() est faux et Laravel génère TOUTES ses
        // URL en http:// alors que la page est servie en https:// — les
        // navigateurs bloquaient alors le favicon et le logo (contenu mixte),
        // et le <link rel="canonical"> annonçait une URL non sécurisée à
        // Google.
        //
        // Corrige aussi $request->ip() : sans proxy de confiance, le
        // formulaire /contact journalisait l'IP du proxy et non celle du
        // visiteur, rendant le rate-limit contournable... et surtout commun
        // à tous les visiteurs.
        //
        // `at: '*'` est le réglage attendu ici : l'IP du proxy est allouée
        // dynamiquement sur le réseau Docker. Cela suppose que le conteneur
        // ne soit joignable QUE via le proxy — c'est le cas sous Coolify.
        $middleware->trustProxies(at: '*');

        // En-têtes de sécurité sur toutes les réponses web (CSP,
        // anti-clickjacking, anti-sniffing…). Cf. SecurityHeaders.
        $middleware->web(append: [
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // Limite d'essais atteinte (2026-11-25) : au lieu de la page brute
        // « 429 Too Many Requests » en anglais, la personne revient sur sa
        // page avec un message en français et le délai exact. La limite
        // elle-même ne change pas. Ailleurs : vue errors/429.
        // La connexion admin et le renvoi de code ne passent plus par ici :
        // leurs limites sont dans AdminController (formulaire grisé).
        $exceptions->render(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, Request $request) {
            $secondes = (int) ($e->getHeaders()['Retry-After'] ?? 600);
            $minutes  = max(1, (int) ceil($secondes / 60));
            $duree    = $minutes . ' minute' . ($minutes > 1 ? 's' : '');

            switch ($request->route()?->getName()) {
                case 'admin.verifier':
                    return redirect()->route('admin.verification')->withErrors([
                        'code' => "Trop d'essais. Réessayez dans $duree.",
                    ]);

                case 'devis.submit':
                    // Saisie conservée (sauf fichiers et jetons) : le prospect
                    // ne retape pas son brief.
                    $tel = \App\Support\Contenu::get('coordonnees.tel_mobile');
                    return back()
                        ->withInput($request->except(['_token', '_ts', 'cf-turnstile-response',
                            'doc_brief', 'doc_logo', 'doc_charte', 'doc_cahier']))
                        ->with('devis_error', "Vous avez envoyé plusieurs demandes en peu de temps. Merci de réessayer dans $duree, ou appelez-nous au $tel.");
            }

            return null;   // rendu par défaut : resources/views/errors/429.blade.php
        });
    })->create();
